<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-6">
        <!-- System Logs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    System Logs
                </h3>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-md p-4">
                    <pre class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap font-mono max-h-96 overflow-y-auto">{{ $logs }}</pre>
                </div>
            </div>
        </div>

        <!-- Access Logs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Access Logs
                </h3>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-md p-4">
                    <pre class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap font-mono max-h-96 overflow-y-auto">{{ $accessLogs }}</pre>
                </div>
            </div>
        </div>

        <!-- Error Logs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Error Logs
                </h3>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-md p-4">
                    <pre class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap font-mono max-h-96 overflow-y-auto">{{ $errorLogs }}</pre>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
