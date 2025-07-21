@php
    $config = config('caddy');
    $healthResults = $this->getHealthResults();
@endphp

<x-filament::page>
    <div class="space-y-6">
        {{-- Health Overview --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">System Health Overview</h2>
                <div class="flex items-center space-x-2">
                    @if($healthResults['overall']['status'] === 'healthy')
                        <div class="flex items-center text-green-600">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">Healthy</span>
                        </div>
                    @elseif($healthResults['overall']['status'] === 'warning')
                        <div class="flex items-center text-yellow-600">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">Warning</span>
                        </div>
                    @else
                        <div class="flex items-center text-red-600">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">Critical</span>
                        </div>
                    @endif
                    <button 
                        wire:click="refreshHealth"
                        class="ml-4 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>

            {{-- Health Score --}}
            <div class="text-center mb-6">
                <div class="text-4xl font-bold mb-2 {{ $healthResults['overall']['status'] === 'healthy' ? 'text-green-600' : ($healthResults['overall']['status'] === 'warning' ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ $healthResults['overall']['score'] }}%
                </div>
                <p class="text-gray-600 dark:text-gray-400">Overall Health Score</p>
            </div>

            {{-- Quick Summary --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $healthResults['summary']['healthy'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Healthy</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $healthResults['summary']['warning'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Warning</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $healthResults['summary']['critical'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Critical</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-600">{{ $healthResults['summary']['total'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Checks</div>
                </div>
            </div>
        </div>

        {{-- Health Check Details --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Service Health --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Service Health</h3>
                </div>
                <div class="p-6 space-y-4">
                    @foreach($healthResults['checks']['service'] as $check)
                        <div class="flex items-center justify-between p-3 rounded-lg {{ $check['status'] === 'healthy' ? 'bg-green-50 dark:bg-green-900/20' : ($check['status'] === 'warning' ? 'bg-yellow-50 dark:bg-yellow-900/20' : 'bg-red-50 dark:bg-red-900/20') }}">
                            <div class="flex items-center">
                                @if($check['status'] === 'healthy')
                                    <svg class="w-5 h-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif($check['status'] === 'warning')
                                    <svg class="w-5 h-5 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $check['name'] }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $check['message'] }}</div>
                                </div>
                            </div>
                            <div class="text-sm font-medium {{ $check['status'] === 'healthy' ? 'text-green-600' : ($check['status'] === 'warning' ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ ucfirst($check['status']) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Configuration Health --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Configuration Health</h3>
                </div>
                <div class="p-6 space-y-4">
                    @foreach($healthResults['checks']['configuration'] as $check)
                        <div class="flex items-center justify-between p-3 rounded-lg {{ $check['status'] === 'healthy' ? 'bg-green-50 dark:bg-green-900/20' : ($check['status'] === 'warning' ? 'bg-yellow-50 dark:bg-yellow-900/20' : 'bg-red-50 dark:bg-red-900/20') }}">
                            <div class="flex items-center">
                                @if($check['status'] === 'healthy')
                                    <svg class="w-5 h-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif($check['status'] === 'warning')
                                    <svg class="w-5 h-5 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $check['name'] }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $check['message'] }}</div>
                                </div>
                            </div>
                            <div class="text-sm font-medium {{ $check['status'] === 'healthy' ? 'text-green-600' : ($check['status'] === 'warning' ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ ucfirst($check['status']) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Network Health --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Network Health</h3>
                </div>
                <div class="p-6 space-y-4">
                    @foreach($healthResults['checks']['network'] as $check)
                        <div class="flex items-center justify-between p-3 rounded-lg {{ $check['status'] === 'healthy' ? 'bg-green-50 dark:bg-green-900/20' : ($check['status'] === 'warning' ? 'bg-yellow-50 dark:bg-yellow-900/20' : 'bg-red-50 dark:bg-red-900/20') }}">
                            <div class="flex items-center">
                                @if($check['status'] === 'healthy')
                                    <svg class="w-5 h-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif($check['status'] === 'warning')
                                    <svg class="w-5 h-5 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $check['name'] }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $check['message'] }}</div>
                                </div>
                            </div>
                            <div class="text-sm font-medium {{ $check['status'] === 'healthy' ? 'text-green-600' : ($check['status'] === 'warning' ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ ucfirst($check['status']) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- System Health --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">System Health</h3>
                </div>
                <div class="p-6 space-y-4">
                    @foreach($healthResults['checks']['system'] as $check)
                        <div class="flex items-center justify-between p-3 rounded-lg {{ $check['status'] === 'healthy' ? 'bg-green-50 dark:bg-green-900/20' : ($check['status'] === 'warning' ? 'bg-yellow-50 dark:bg-yellow-900/20' : 'bg-red-50 dark:bg-red-900/20') }}">
                            <div class="flex items-center">
                                @if($check['status'] === 'healthy')
                                    <svg class="w-5 h-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif($check['status'] === 'warning')
                                    <svg class="w-5 h-5 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $check['name'] }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $check['message'] }}</div>
                                    @if(isset($check['details']))
                                        <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                            @if(is_array($check['details']))
                                                {{ json_encode($check['details'], JSON_PRETTY_PRINT) }}
                                            @else
                                                {{ $check['details'] }}
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-sm font-medium {{ $check['status'] === 'healthy' ? 'text-green-600' : ($check['status'] === 'warning' ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ ucfirst($check['status']) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Recommended Actions --}}
        @if(!empty($healthResults['recommendations']))
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recommended Actions</h3>
                <div class="space-y-3">
                    @foreach($healthResults['recommendations'] as $recommendation)
                        <div class="flex items-start p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                            <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <div class="font-medium text-blue-900 dark:text-blue-100">{{ $recommendation['title'] }}</div>
                                <div class="text-sm text-blue-700 dark:text-blue-300 mt-1">{{ $recommendation['description'] }}</div>
                                @if(isset($recommendation['action']))
                                    <button 
                                        wire:click="{{ $recommendation['action'] }}"
                                        class="mt-2 px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition-colors">
                                        {{ $recommendation['action_label'] ?? 'Take Action' }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Health History --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Health History</h3>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <p>Last health check: {{ $healthResults['timestamp'] }}</p>
                <p>Check frequency: Every 5 minutes</p>
                <p>Next scheduled check: {{ now()->addMinutes(5)->format('Y-m-d H:i:s') }}</p>
            </div>
            
            {{-- Simple history chart placeholder --}}
            <div class="mt-4 h-32 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                <div class="text-center text-gray-500 dark:text-gray-400">
                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="text-sm">Health metrics chart would go here</p>
                    <p class="text-xs">Future enhancement</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if (session()->has('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif
</x-filament::page>
