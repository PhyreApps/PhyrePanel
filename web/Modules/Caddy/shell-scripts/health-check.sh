#!/bin/bash

# Caddy Health Check Script
# This script performs comprehensive health checks for the Caddy module

set -euo pipefail

# Configuration
CADDY_CONFIG="/etc/caddy/Caddyfile"
CADDY_LOG_DIR="/var/log/caddy"
CADDY_BINARY="/usr/bin/caddy"
HEALTH_CHECK_LOG="/var/log/caddy-health-check.log"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Logging function
log() {
    local level=$1
    shift
    local message="$*"
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    
    case $level in
        "INFO")
            echo -e "${GREEN}[INFO]${NC} $message"
            ;;
        "WARN")
            echo -e "${YELLOW}[WARN]${NC} $message"
            ;;
        "ERROR")
            echo -e "${RED}[ERROR]${NC} $message"
            ;;
        "DEBUG")
            echo -e "${BLUE}[DEBUG]${NC} $message"
            ;;
    esac
    
    # Also log to file
    echo "[$timestamp] [$level] $message" >> "$HEALTH_CHECK_LOG"
}

# Check if script is run as root or with sudo
check_permissions() {
    if [[ $EUID -ne 0 ]]; then
        log "WARN" "This script should be run as root for complete health checks"
        return 1
    fi
    return 0
}

# Check if Caddy service is running
check_service_status() {
    log "INFO" "Checking Caddy service status..."
    
    if systemctl is-active --quiet caddy; then
        log "INFO" "✓ Caddy service is running"
        
        # Get additional service info
        local pid=$(systemctl show caddy --property=MainPID --value)
        local uptime=$(systemctl show caddy --property=ActiveEnterTimestamp --value)
        log "INFO" "  PID: $pid"
        log "INFO" "  Started: $uptime"
        return 0
    else
        log "ERROR" "✗ Caddy service is not running"
        return 1
    fi
}

# Check Caddy binary
check_binary() {
    log "INFO" "Checking Caddy binary..."
    
    if [[ -x "$CADDY_BINARY" ]]; then
        local version=$($CADDY_BINARY version 2>/dev/null | head -n1)
        log "INFO" "✓ Caddy binary is executable"
        log "INFO" "  Version: $version"
        return 0
    else
        log "ERROR" "✗ Caddy binary not found or not executable at $CADDY_BINARY"
        return 1
    fi
}

# Check configuration file
check_configuration() {
    log "INFO" "Checking Caddy configuration..."
    
    if [[ -f "$CADDY_CONFIG" ]]; then
        log "INFO" "✓ Configuration file exists at $CADDY_CONFIG"
        
        # Check if readable
        if [[ -r "$CADDY_CONFIG" ]]; then
            log "INFO" "✓ Configuration file is readable"
        else
            log "ERROR" "✗ Configuration file is not readable"
            return 1
        fi
        
        # Validate syntax
        if [[ -x "$CADDY_BINARY" ]]; then
            if $CADDY_BINARY validate --config "$CADDY_CONFIG" >/dev/null 2>&1; then
                log "INFO" "✓ Configuration syntax is valid"
            else
                log "ERROR" "✗ Configuration syntax validation failed"
                $CADDY_BINARY validate --config "$CADDY_CONFIG" 2>&1 | while read -r line; do
                    log "ERROR" "  $line"
                done
                return 1
            fi
        fi
        
        return 0
    else
        log "ERROR" "✗ Configuration file not found at $CADDY_CONFIG"
        return 1
    fi
}

# Check log directory
check_log_directory() {
    log "INFO" "Checking log directory..."
    
    if [[ -d "$CADDY_LOG_DIR" ]]; then
        log "INFO" "✓ Log directory exists at $CADDY_LOG_DIR"
        
        # Check if writable
        if [[ -w "$CADDY_LOG_DIR" ]]; then
            log "INFO" "✓ Log directory is writable"
        else
            log "ERROR" "✗ Log directory is not writable"
            return 1
        fi
        
        # Check log files
        local log_count=$(find "$CADDY_LOG_DIR" -name "*.log" | wc -l)
        log "INFO" "  Found $log_count log files"
        
        return 0
    else
        log "ERROR" "✗ Log directory not found at $CADDY_LOG_DIR"
        return 1
    fi
}

# Check ports
check_ports() {
    log "INFO" "Checking port availability..."
    
    # Check if Caddy is listening on expected ports
    local http_port=$(ss -tlnp | grep ":80 " | grep caddy || true)
    local https_port=$(ss -tlnp | grep ":443 " | grep caddy || true)
    
    if [[ -n "$http_port" ]]; then
        log "INFO" "✓ Caddy is listening on port 80"
    else
        log "WARN" "⚠ Caddy is not listening on port 80"
    fi
    
    if [[ -n "$https_port" ]]; then
        log "INFO" "✓ Caddy is listening on port 443"
    else
        log "WARN" "⚠ Caddy is not listening on port 443"
    fi
    
    # Check for Apache on expected proxy port
    local apache_port=$(ss -tlnp | grep ":8080 " | grep -E "(apache|httpd)" || true)
    if [[ -n "$apache_port" ]]; then
        log "INFO" "✓ Apache is listening on proxy port 8080"
    else
        log "WARN" "⚠ Apache is not listening on proxy port 8080"
    fi
}

# Check SSL certificates
check_ssl_certificates() {
    log "INFO" "Checking SSL certificates..."
    
    local cert_dir="/var/lib/caddy/.local/share/caddy/certificates"
    
    if [[ -d "$cert_dir" ]]; then
        local cert_count=$(find "$cert_dir" -name "*.crt" | wc -l)
        log "INFO" "✓ Certificate storage directory exists"
        log "INFO" "  Found $cert_count SSL certificates"
        
        # Check for expiring certificates (within 30 days)
        local expiring=0
        while IFS= read -r -d '' cert; do
            if [[ -f "$cert" ]]; then
                local expiry=$(openssl x509 -in "$cert" -noout -enddate 2>/dev/null | cut -d= -f2)
                if [[ -n "$expiry" ]]; then
                    local expiry_epoch=$(date -d "$expiry" +%s 2>/dev/null || echo 0)
                    local current_epoch=$(date +%s)
                    local days_until_expiry=$(( (expiry_epoch - current_epoch) / 86400 ))
                    
                    if [[ $days_until_expiry -lt 30 ]]; then
                        log "WARN" "⚠ Certificate expires in $days_until_expiry days: $cert"
                        ((expiring++))
                    fi
                fi
            fi
        done < <(find "$cert_dir" -name "*.crt" -print0)
        
        if [[ $expiring -eq 0 ]]; then
            log "INFO" "✓ No certificates expiring within 30 days"
        fi
        
    else
        log "WARN" "⚠ Certificate storage directory not found at $cert_dir"
    fi
}

# Check disk space
check_disk_space() {
    log "INFO" "Checking disk space..."
    
    # Check space for log directory
    local log_usage=$(df "$CADDY_LOG_DIR" | awk 'NR==2 {print $5}' | sed 's/%//')
    if [[ $log_usage -gt 90 ]]; then
        log "ERROR" "✗ Log directory disk usage is critical: ${log_usage}%"
        return 1
    elif [[ $log_usage -gt 80 ]]; then
        log "WARN" "⚠ Log directory disk usage is high: ${log_usage}%"
    else
        log "INFO" "✓ Log directory disk usage is normal: ${log_usage}%"
    fi
    
    # Check space for certificate directory
    local cert_dir="/var/lib/caddy"
    if [[ -d "$cert_dir" ]]; then
        local cert_usage=$(df "$cert_dir" | awk 'NR==2 {print $5}' | sed 's/%//')
        if [[ $cert_usage -gt 90 ]]; then
            log "ERROR" "✗ Certificate directory disk usage is critical: ${cert_usage}%"
            return 1
        elif [[ $cert_usage -gt 80 ]]; then
            log "WARN" "⚠ Certificate directory disk usage is high: ${cert_usage}%"
        else
            log "INFO" "✓ Certificate directory disk usage is normal: ${cert_usage}%"
        fi
    fi
}

# Test HTTP/HTTPS connectivity
test_connectivity() {
    log "INFO" "Testing connectivity..."
    
    # Test local HTTP connection
    if curl -s -f -H "Host: localhost" http://localhost/ >/dev/null 2>&1; then
        log "INFO" "✓ HTTP connectivity test passed"
    else
        log "WARN" "⚠ HTTP connectivity test failed"
    fi
    
    # Test local HTTPS connection (if available)
    if curl -s -f -k -H "Host: localhost" https://localhost/ >/dev/null 2>&1; then
        log "INFO" "✓ HTTPS connectivity test passed"
    else
        log "WARN" "⚠ HTTPS connectivity test failed"
    fi
}

# Check recent errors in logs
check_recent_errors() {
    log "INFO" "Checking for recent errors..."
    
    # Check systemd journal for errors in last hour
    local error_count=$(journalctl -u caddy --since="1 hour ago" --no-pager -q | grep -i error | wc -l)
    
    if [[ $error_count -eq 0 ]]; then
        log "INFO" "✓ No errors found in last hour"
    else
        log "WARN" "⚠ Found $error_count errors in last hour"
        log "INFO" "Recent errors:"
        journalctl -u caddy --since="1 hour ago" --no-pager -q | grep -i error | tail -5 | while read -r line; do
            log "WARN" "  $line"
        done
    fi
}

# Performance check
check_performance() {
    log "INFO" "Checking performance metrics..."
    
    if systemctl is-active --quiet caddy; then
        local pid=$(systemctl show caddy --property=MainPID --value)
        if [[ -n "$pid" && "$pid" != "0" ]]; then
            # Get memory usage
            local memory=$(ps -p "$pid" -o rss= 2>/dev/null | awk '{print $1/1024}' | cut -d. -f1)
            log "INFO" "  Memory usage: ${memory} MB"
            
            # Get CPU usage (approximate)
            local cpu=$(ps -p "$pid" -o %cpu= 2>/dev/null | tr -d ' ')
            log "INFO" "  CPU usage: ${cpu}%"
            
            # Check file descriptors
            local fd_count=$(ls /proc/"$pid"/fd 2>/dev/null | wc -l)
            log "INFO" "  Open file descriptors: $fd_count"
        fi
    fi
}

# Main health check function
main() {
    local exit_code=0
    local checks_passed=0
    local checks_failed=0
    
    log "INFO" "Starting Caddy health check..."
    log "INFO" "Timestamp: $(date)"
    log "INFO" "=================================================="
    
    # Array of check functions
    local checks=(
        "check_service_status"
        "check_binary"
        "check_configuration"
        "check_log_directory"
        "check_ports"
        "check_ssl_certificates"
        "check_disk_space"
        "test_connectivity"
        "check_recent_errors"
        "check_performance"
    )
    
    # Run all checks
    for check in "${checks[@]}"; do
        if $check; then
            ((checks_passed++))
        else
            ((checks_failed++))
            exit_code=1
        fi
        echo
    done
    
    # Summary
    log "INFO" "=================================================="
    log "INFO" "Health check completed"
    log "INFO" "Checks passed: $checks_passed"
    log "INFO" "Checks failed: $checks_failed"
    
    if [[ $exit_code -eq 0 ]]; then
        log "INFO" "✓ Overall health status: HEALTHY"
    else
        log "ERROR" "✗ Overall health status: UNHEALTHY"
    fi
    
    return $exit_code
}

# Script options
case "${1:-check}" in
    "check")
        main
        ;;
    "quick")
        log "INFO" "Running quick health check..."
        check_service_status && check_configuration
        ;;
    "service")
        check_service_status
        ;;
    "config")
        check_configuration
        ;;
    "certs")
        check_ssl_certificates
        ;;
    "help"|"-h"|"--help")
        echo "Caddy Health Check Script"
        echo
        echo "Usage: $0 [command]"
        echo
        echo "Commands:"
        echo "  check   - Run complete health check (default)"
        echo "  quick   - Run quick health check (service + config)"
        echo "  service - Check service status only"
        echo "  config  - Check configuration only"
        echo "  certs   - Check SSL certificates only"
        echo "  help    - Show this help message"
        ;;
    *)
        log "ERROR" "Unknown command: $1"
        log "INFO" "Use '$0 help' for usage information"
        exit 1
        ;;
esac
