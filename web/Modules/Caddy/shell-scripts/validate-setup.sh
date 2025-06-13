#!/bin/bash

# Caddy Module Validation Test Script
# This script validates the complete Caddy module setup

set -euo pipefail

# Configuration
PHYRE_ROOT="/usr/local/phyre/web"
CADDY_MODULE_PATH="$PHYRE_ROOT/Modules/Caddy"
TEST_LOG="/tmp/caddy-validation-test.log"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Counters
TESTS_PASSED=0
TESTS_FAILED=0
TESTS_TOTAL=0

# Logging function
log() {
    local level=$1
    shift
    local message="$*"
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    
    case $level in
        "PASS")
            echo -e "${GREEN}[PASS]${NC} $message"
            ((TESTS_PASSED++))
            ;;
        "FAIL")
            echo -e "${RED}[FAIL]${NC} $message"
            ((TESTS_FAILED++))
            ;;
        "INFO")
            echo -e "${BLUE}[INFO]${NC} $message"
            ;;
        "WARN")
            echo -e "${YELLOW}[WARN]${NC} $message"
            ;;
    esac
    
    ((TESTS_TOTAL++))
    echo "[$timestamp] [$level] $message" >> "$TEST_LOG"
}

# Test function
test_condition() {
    local description="$1"
    local condition="$2"
    
    if eval "$condition"; then
        log "PASS" "$description"
        return 0
    else
        log "FAIL" "$description"
        return 1
    fi
}

# Module structure tests
test_module_structure() {
    echo -e "\n${BLUE}=== Testing Module Structure ===${NC}"
    
    test_condition "Module directory exists" "[[ -d '$CADDY_MODULE_PATH' ]]"
    test_condition "module.json exists" "[[ -f '$CADDY_MODULE_PATH/module.json' ]]"
    test_condition "Config file exists" "[[ -f '$CADDY_MODULE_PATH/config/config.php' ]]"
    test_condition "Service provider exists" "[[ -f '$CADDY_MODULE_PATH/App/Providers/CaddyServiceProvider.php' ]]"
    test_condition "Route service provider exists" "[[ -f '$CADDY_MODULE_PATH/App/Providers/RouteServiceProvider.php' ]]"
}

# Job classes tests
test_job_classes() {
    echo -e "\n${BLUE}=== Testing Job Classes ===${NC}"
    
    test_condition "CaddyBuild job exists" "[[ -f '$CADDY_MODULE_PATH/App/Jobs/CaddyBuild.php' ]]"
    
    # Test job class syntax
    if php -l "$CADDY_MODULE_PATH/App/Jobs/CaddyBuild.php" >/dev/null 2>&1; then
        log "PASS" "CaddyBuild job syntax is valid"
    else
        log "FAIL" "CaddyBuild job has syntax errors"
    fi
}

# Filament pages tests
test_filament_pages() {
    echo -e "\n${BLUE}=== Testing Filament Pages ===${NC}"
    
    local pages=(
        "CaddyDashboard.php"
        "CaddySettings.php"
        "CaddyLogs.php"
        "CaddyManagement.php"
    )
    
    for page in "${pages[@]}"; do
        test_condition "$page exists" "[[ -f '$CADDY_MODULE_PATH/App/Filament/Pages/$page' ]]"
        
        # Test PHP syntax
        if php -l "$CADDY_MODULE_PATH/App/Filament/Pages/$page" >/dev/null 2>&1; then
            log "PASS" "$page syntax is valid"
        else
            log "FAIL" "$page has syntax errors"
        fi
    done
    
    test_condition "Caddy cluster exists" "[[ -f '$CADDY_MODULE_PATH/App/Filament/Clusters/Caddy.php' ]]"
}

# Console commands tests
test_console_commands() {
    echo -e "\n${BLUE}=== Testing Console Commands ===${NC}"
    
    local commands=(
        "CaddyRebuild.php"
        "CaddyStatus.php"
    )
    
    for command in "${commands[@]}"; do
        test_condition "$command exists" "[[ -f '$CADDY_MODULE_PATH/App/Console/$command' ]]"
        
        # Test PHP syntax
        if php -l "$CADDY_MODULE_PATH/App/Console/$command" >/dev/null 2>&1; then
            log "PASS" "$command syntax is valid"
        else
            log "FAIL" "$command has syntax errors"
        fi
    done
}

# Service class tests
test_service_classes() {
    echo -e "\n${BLUE}=== Testing Service Classes ===${NC}"
    
    test_condition "CaddyService exists" "[[ -f '$CADDY_MODULE_PATH/App/Services/CaddyService.php' ]]"
    
    # Test service class syntax
    if php -l "$CADDY_MODULE_PATH/App/Services/CaddyService.php" >/dev/null 2>&1; then
        log "PASS" "CaddyService syntax is valid"
    else
        log "FAIL" "CaddyService has syntax errors"
    fi
}

# Event listeners tests
test_event_listeners() {
    echo -e "\n${BLUE}=== Testing Event Listeners ===${NC}"
    
    test_condition "DomainEventListener exists" "[[ -f '$CADDY_MODULE_PATH/App/Listeners/DomainEventListener.php' ]]"
    
    # Test listener syntax
    if php -l "$CADDY_MODULE_PATH/App/Listeners/DomainEventListener.php" >/dev/null 2>&1; then
        log "PASS" "DomainEventListener syntax is valid"
    else
        log "FAIL" "DomainEventListener has syntax errors"
    fi
}

# View templates tests
test_view_templates() {
    echo -e "\n${BLUE}=== Testing View Templates ===${NC}"
    
    local views=(
        "caddyfile-build.blade.php"
        "filament/pages/caddy-dashboard.blade.php"
        "filament/pages/caddy-logs.blade.php"
        "filament/pages/caddy-management.blade.php"
    )
    
    for view in "${views[@]}"; do
        test_condition "$view exists" "[[ -f '$CADDY_MODULE_PATH/resources/views/$view' ]]"
    done
}

# Shell scripts tests
test_shell_scripts() {
    echo -e "\n${BLUE}=== Testing Shell Scripts ===${NC}"
    
    local scripts=(
        "install-caddy.sh"
        "health-check.sh"
    )
    
    for script in "${scripts[@]}"; do
        test_condition "$script exists" "[[ -f '$CADDY_MODULE_PATH/shell-scripts/$script' ]]"
        test_condition "$script is executable" "[[ -x '$CADDY_MODULE_PATH/shell-scripts/$script' ]]"
    done
}

# Configuration tests
test_configuration() {
    echo -e "\n${BLUE}=== Testing Configuration ===${NC}"
    
    # Test config file syntax
    if php -l "$CADDY_MODULE_PATH/config/config.php" >/dev/null 2>&1; then
        log "PASS" "Configuration file syntax is valid"
    else
        log "FAIL" "Configuration file has syntax errors"
    fi
    
    # Test if config can be loaded
    if php -r "include '$CADDY_MODULE_PATH/config/config.php';" >/dev/null 2>&1; then
        log "PASS" "Configuration file can be loaded"
    else
        log "FAIL" "Configuration file cannot be loaded"
    fi
}

# Module registration tests
test_module_registration() {
    echo -e "\n${BLUE}=== Testing Module Registration ===${NC}"
    
    # Check if module is listed in modules_statuses.json
    if [[ -f "$PHYRE_ROOT/modules_statuses.json" ]]; then
        if grep -q '"Caddy"' "$PHYRE_ROOT/modules_statuses.json"; then
            log "PASS" "Module is registered in modules_statuses.json"
        else
            log "FAIL" "Module is not registered in modules_statuses.json"
        fi
    else
        log "WARN" "modules_statuses.json not found"
    fi
}

# Documentation tests
test_documentation() {
    echo -e "\n${BLUE}=== Testing Documentation ===${NC}"
    
    test_condition "README.md exists" "[[ -f '$CADDY_MODULE_PATH/README.md' ]]"
    
    # Check README content
    if [[ -f "$CADDY_MODULE_PATH/README.md" ]]; then
        if grep -q "Caddy Module" "$CADDY_MODULE_PATH/README.md"; then
            log "PASS" "README.md contains proper content"
        else
            log "FAIL" "README.md missing proper content"
        fi
    fi
}

# Laravel integration tests
test_laravel_integration() {
    echo -e "\n${BLUE}=== Testing Laravel Integration ===${NC}"
    
    # Test if we can run artisan commands (requires being in Laravel directory)
    cd "$PHYRE_ROOT"
    
    # Check if Caddy commands are registered
    if php artisan list | grep -q "caddy:"; then
        log "PASS" "Caddy commands are registered with Artisan"
    else
        log "FAIL" "Caddy commands are not registered with Artisan"
    fi
    
    # Test command availability
    if php artisan caddy:status --help >/dev/null 2>&1; then
        log "PASS" "caddy:status command is available"
    else
        log "FAIL" "caddy:status command is not available"
    fi
    
    if php artisan caddy:rebuild --help >/dev/null 2>&1; then
        log "PASS" "caddy:rebuild command is available"
    else
        log "FAIL" "caddy:rebuild command is not available"
    fi
}

# System requirements tests
test_system_requirements() {
    echo -e "\n${BLUE}=== Testing System Requirements ===${NC}"
    
    # Check PHP version
    local php_version=$(php -r "echo PHP_VERSION;")
    if [[ $(echo "$php_version" | cut -d. -f1) -ge 8 ]] && [[ $(echo "$php_version" | cut -d. -f2) -ge 1 ]]; then
        log "PASS" "PHP version is compatible ($php_version)"
    else
        log "FAIL" "PHP version is not compatible ($php_version), requires 8.1+"
    fi
    
    # Check if running on supported OS
    if [[ -f /etc/os-release ]]; then
        local os_name=$(grep "^NAME=" /etc/os-release | cut -d'"' -f2)
        log "INFO" "Operating System: $os_name"
        
        if [[ "$os_name" == *"Ubuntu"* ]] || [[ "$os_name" == *"Debian"* ]] || [[ "$os_name" == *"CentOS"* ]] || [[ "$os_name" == *"Rocky"* ]]; then
            log "PASS" "Operating system is supported"
        else
            log "WARN" "Operating system may not be fully supported"
        fi
    fi
    
    # Check required PHP extensions
    local required_extensions=("json" "openssl" "curl" "mbstring")
    for ext in "${required_extensions[@]}"; do
        if php -m | grep -q "^$ext$"; then
            log "PASS" "PHP extension '$ext' is available"
        else
            log "FAIL" "PHP extension '$ext' is missing"
        fi
    done
}

# Test results summary
show_summary() {
    echo -e "\n${BLUE}=== Test Summary ===${NC}"
    echo "Total tests run: $TESTS_TOTAL"
    echo -e "Tests passed: ${GREEN}$TESTS_PASSED${NC}"
    echo -e "Tests failed: ${RED}$TESTS_FAILED${NC}"
    
    if [[ $TESTS_FAILED -eq 0 ]]; then
        echo -e "\n${GREEN}✓ All tests passed! The Caddy module is properly configured.${NC}"
        return 0
    else
        echo -e "\n${RED}✗ Some tests failed. Please review the issues above.${NC}"
        return 1
    fi
}

# Main function
main() {
    echo -e "${BLUE}Caddy Module Validation Test${NC}"
    echo "==============================="
    echo "Starting validation at $(date)"
    echo "Log file: $TEST_LOG"
    
    # Initialize log file
    echo "Caddy Module Validation Test - $(date)" > "$TEST_LOG"
    
    # Run all test suites
    test_module_structure
    test_job_classes
    test_filament_pages
    test_console_commands
    test_service_classes
    test_event_listeners
    test_view_templates
    test_shell_scripts
    test_configuration
    test_module_registration
    test_documentation
    test_laravel_integration
    test_system_requirements
    
    # Show summary
    show_summary
}

# Run main function
main "$@"
