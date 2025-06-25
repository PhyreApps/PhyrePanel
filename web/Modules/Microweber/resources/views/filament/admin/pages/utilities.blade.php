<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <x-heroicon-o-globe-americas class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Installations</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalInstallations }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Installations List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Microweber Installations</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Manage and run commands on individual installations
                </p>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($installations as $installation)
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                            <x-heroicon-o-globe-americas class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $installation->domain->domain ?? 'Unknown Domain' }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                            {{ $installation->installation_path }}
                                        </p>
                                        <div class="flex items-center space-x-4 mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $installation->installation_type ?? 'microweber' }}
                                            </span>
                                            @if($installation->app_version)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    v{{ $installation->app_version }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <div class="flex items-center space-x-2">
                            <!-- Clear Cache Button -->
                            <button
                                wire:click="clearCacheForInstallation({{ $installation->id }})"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                title="Clear Cache"
                            >
                                <x-heroicon-o-trash class="w-4 h-4 mr-1" />
                                Clear Cache
                            </button>

                            <!-- Composer Dump Button -->
                            <button
                                wire:click="composerDumpForInstallation({{ $installation->id }})"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                title="Composer Dump"
                            >
                                <x-heroicon-o-code-bracket class="w-4 h-4 mr-1" />
                                Composer Dump
                            </button>
                        </div>

                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <x-heroicon-o-globe-americas class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No installations found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            No Microweber installations are currently available.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Command History Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Usage Information</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Information about using the utilities
                </p>
            </div>

            <div class="px-6 py-4">
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-information-circle class="w-5 h-5 text-blue-500" />
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Clear Cache</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Runs <code class="bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded text-xs">php artisan cache:clear</code>
                                to clear application cache for better performance.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-information-circle class="w-5 h-5 text-blue-500" />
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Composer Dump</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Runs <code class="bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded text-xs">composer dump-autoload</code>
                                to regenerate the autoloader files.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-yellow-500" />
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Background Processing</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                All commands are executed in the background as queue jobs.
                                Check the logs for execution details.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
