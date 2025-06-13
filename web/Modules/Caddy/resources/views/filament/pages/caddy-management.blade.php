<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Caddyfile Editor -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Caddyfile Configuration
                    </h3>
                    <div class="flex space-x-2">
                        <button 
                            wire:click="loadCaddyfile"
                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Refresh
                        </button>
                        <button 
                            wire:click="saveCaddyfile"
                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Save & Reload
                        </button>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Current Caddyfile Content
                        </label>
                        <textarea 
                            wire:model="caddyfileContent"
                            rows="20"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-mono"
                            style="tab-size: 4;"
                        ></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Quick Actions
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <button 
                        onclick="window.location.reload()"
                        class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh Page
                    </button>
                    
                    <button 
                        onclick="navigator.clipboard.writeText(document.querySelector('textarea').value)"
                        class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Copy Config
                    </button>
                    
                    <button 
                        onclick="document.querySelector('textarea').value = ''"
                        class="flex items-center justify-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Clear
                    </button>
                    
                    <button 
                        onclick="window.open('/admin/caddy/logs', '_blank')"
                        class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        View Logs
                    </button>
                </div>
            </div>
        </div>

        <!-- Configuration Tips -->
        <div class="bg-blue-50 dark:bg-blue-900 rounded-lg shadow">
            <div class="p-6">
                <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-4">
                    Configuration Tips
                </h3>
                <div class="text-sm text-blue-800 dark:text-blue-200 space-y-2">
                    <p><strong>SSL Termination:</strong> Caddy automatically handles SSL certificates via Let's Encrypt</p>
                    <p><strong>Proxy Headers:</strong> X-Forwarded-For, X-Real-IP headers are automatically added</p>
                    <p><strong>Security:</strong> HSTS, CSRF protection headers are included by default</p>
                    <p><strong>Compression:</strong> Gzip compression is enabled for better performance</p>
                    <p><strong>Health Checks:</strong> Backend health monitoring is configured</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
