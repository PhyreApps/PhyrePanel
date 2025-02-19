<x-filament-panels::page>


    <div>
        @if (session()->has('message'))
            <div class="p-4 mb-4 text-sm rounded-lg bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                {{ session('message') }}
            </div>
        @endif

        <div class="repair-tool-wrapper max-w[15rem] flex flex-col gap-4 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <!-- Repair Tool Action 1 -->
                <div wire:click="runRepair" class="repair-tool-action flex items-center space-x-3 p-4  border-2 border-primary-500 dark:border-primary-700 rounded-lg cursor-pointer transition-all hover:border-primary-600 dark:hover:border-primary-800 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed" wire:loading.attr="disabled">
                    <svg xmlns="http://www.w3.org/2000/svg" width="50%" viewBox="0 0 512 512" class="h-8 w-8">
                        <path fill="currentColor" d="m241.406 21l-15.22 34.75a182 182 0 0 0-23.467 2.97l-23.282-30.064l-25.094 8.532l-.125 38.25c-10.63 5.464-20.817 12.07-30.44 19.78L88.313 79.25L70.156 98.563L88.312 133a180.6 180.6 0 0 0-15.218 26.094l-38.938 1.062l-7.906 25.28l31.438 23.158c-1.505 9.38-2.24 18.858-2.282 28.344L20.5 254.625l3.656 26.25l38.313 7.5a182 182 0 0 0 8.5 23.5L45.72 343.22l14.093 22.436l39.25-9.187a185 185 0 0 0 7.718 8.53a187 187 0 0 0 17.72 16.125l-7.625 39.313l22.938 13.25l29.968-26.094a179.4 179.4 0 0 0 26.407 8.312l9.782 38.406l26.405 2.157l15.875-36.22c10.97-.66 21.904-2.3 32.656-4.938l25.22 29.22l24.593-9.844l-.72-14.813l-57.406-43.53c-16.712 4.225-34.042 5.356-51.063 3.436c-31.754-3.58-62.27-17.92-86.218-42.686c-54.738-56.614-53.173-146.67 3.438-201.406c27.42-26.513 62.69-39.963 98-40.344c37.59-.406 75.214 13.996 103.438 43.187c45.935 47.512 52.196 118.985 19.562 173.095l31.97 24.25a181 181 0 0 0 10.75-19.375l38.655-1.063l7.906-25.28l-31.217-23a183 183 0 0 0 2.28-28.594l34.688-17.625l-3.655-26.25l-38.28-7.5a182 182 0 0 0-12.75-32.125l22.81-31.594l-15.25-21.657l-37.56 10.906c-.472-.5-.93-1.007-1.408-1.5a185 185 0 0 0-18.937-17.064l7.188-37.125L334 43.78l-28.5 24.814c-9.226-3.713-18.702-6.603-28.313-8.75l-9.343-36.688zM183.25 174.5c-10.344.118-20.597 2.658-30 7.28l45.22 34.314c13.676 10.376 17.555 30.095 7.06 43.937c-10.498 13.85-30.656 15.932-44.53 5.408l-45.188-34.282c-4.627 24.793 4.135 51.063 25.594 67.344c19.245 14.597 43.944 17.33 65.22 9.688l4.78-1.72l4.03 3.063l135.19 102.564l4.03 3.062l-.344 5.063c-1.637 22.55 7.59 45.61 26.844 60.217c21.46 16.28 49.145 17.63 71.78 6.5l-45.186-34.28c-13.874-10.526-17.282-30.506-6.78-44.344c10.5-13.84 30.537-15.405 44.217-5.032l45.188 34.283c4.616-24.784-4.11-51.067-25.563-67.344c-19.313-14.658-43.817-17.562-64.968-10.033l-4.75 1.688l-4.03-3.063l-135.19-102.562l-4.03-3.063l.344-5.03c1.55-22.387-7.85-45.194-27.157-59.845c-12.544-9.516-27.222-13.978-41.78-13.812zm43.563 90.25l163.875 124.344L379.406 404L215.5 279.625z"/>
                    </svg>
                    <div>
                        Run Repair
                    </div>

                    <!-- Loading Spinner -->
                    <div wire:loading wire:target="runRepair" class="ml-2 spinner-border animate-spin h-5 w-5 border-t-2 dark:border-white border-primary-700 rounded-full"></div>
                </div>

                <!-- Repair Tool Action 2 -->
                <div wire:click="runDomainRepair" class="repair-tool-action flex items-center space-x-3 p-4 border-2 border-primary-500 dark:border-primary-700 rounded-lg cursor-pointer transition-all hover:border-primary-600 dark:hover:border-primary-800 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed" wire:loading.attr="disabled">
                    <svg xmlns="http://www.w3.org/2000/svg" width="50%" viewBox="0 0 24 24" class="h-8 w-8">
                        <path fill="currentColor" d="M2 14a1 1 0 1 1-2 0a1 1 0 0 1 2 0" opacity="0.5"/>
                        <path fill="currentColor" d="M6.719 10.262a1.73 1.73 0 0 0-2.458 0a1.757 1.757 0 0 0 0 2.476a1.73 1.73 0 0 0 2.458 0a.75.75 0 0 1 1.062 1.059a3.23 3.23 0 0 1-4.583 0a3.257 3.257 0 0 1 0-4.594a3.23 3.23 0 0 1 4.583 0a.75.75 0 0 1-1.062 1.059M8.5 11.5a3.25 3.25 0 1 1 6.5 0a3.25 3.25 0 0 1-6.5 0m3.25-1.75a1.75 1.75 0 1 0 0 3.5a1.75 1.75 0 0 0 0-3.5M18 8.25c-1.395 0-2.5 1.15-2.5 2.536V14a.75.75 0 0 0 1.5 0v-3.214c0-.587.462-1.036 1-1.036s1 .45 1 1.036V14a.75.75 0 0 0 1.5 0v-3.214c0-.587.462-1.036 1-1.036s1 .45 1 1.036V14a.75.75 0 0 0 1.5 0v-3.214C24 9.4 22.895 8.25 21.5 8.25c-.686 0-1.301.278-1.75.725A2.47 2.47 0 0 0 18 8.25"/>
                    </svg>
                    <div>
                        Run Domain Repair
                    </div>

                    <!-- Loading Spinner -->
                    <div wire:loading wire:target="runDomainRepair" class="ml-2 spinner-border animate-spin h-5 w-5 border-t-2 dark:border-white border-primary-700 rounded-full"></div>
                </div>

                <!-- Repair Tool Action 3 -->
                <div wire:click="runRenewSSL" class="repair-tool-action flex items-center space-x-3 p-4 border-2 border-primary-500 dark:border-primary-700 rounded-lg cursor-pointer transition-all hover:border-primary-600 dark:hover:border-primary-800 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed" wire:loading.attr="disabled">
                    <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg">
                        <title>letsencrypt</title>
                        <path d="M15.986 21.652c0.004-0 0.009-0 0.014-0 0.883 0 1.599 0.716 1.599 1.599 0 0.594-0.323 1.112-0.804 1.387l-0.008 0.004v1.556c-0.003 0.432-0.354 0.781-0.787 0.781s-0.784-0.349-0.787-0.781v-1.556c-0.488-0.28-0.812-0.798-0.812-1.391 0-0.878 0.708-1.591 1.584-1.598h0.001zM2.915 13.187c-0.573 0.044-1.021 0.52-1.021 1.1s0.448 1.056 1.017 1.1l0.004 0h3.747c0.573-0.044 1.021-0.52 1.021-1.1s-0.448-1.056-1.017-1.1l-0.004-0zM25.214 13.184c-0.608 0.002-1.1 0.495-1.1 1.103 0 0.609 0.494 1.103 1.103 1.103 0.030 0 0.059-0.001 0.088-0.003l-0.004 0h3.782c0.573-0.044 1.021-0.52 1.021-1.1s-0.448-1.056-1.017-1.1l-0.004-0h-3.782q-0.044-0.003-0.088-0.003zM15.991 12.555c0.003 0 0.006 0 0.009 0 1.485 0 2.689 1.204 2.689 2.689 0 0 0 0 0 0v0 1.859h-5.379v-1.859c0 0 0-0 0-0 0-1.482 1.199-2.684 2.68-2.689h0.001zM15.975 8.939c-3.472 0.014-6.281 2.831-6.281 6.305v0 1.859h-1.458c-0.665 0.002-1.203 0.54-1.206 1.205v11.483c0.002 0.665 0.541 1.203 1.205 1.205h15.528c0.665-0.002 1.203-0.54 1.205-1.205v-11.485c-0.003-0.664-0.541-1.2-1.205-1.203h-1.46v-1.859c-0-3.482-2.823-6.305-6.305-6.305-0.008 0-0.017 0-0.025 0h0.001zM6.403 4.906c-0 0-0 0-0 0-0.609 0-1.103 0.494-1.103 1.103 0 0.313 0.13 0.596 0.34 0.797l0 0 2.962 2.437c0.188 0.156 0.431 0.25 0.696 0.25 0.002 0 0.003 0 0.004 0h-0v-0.002c0 0 0 0 0 0 0.608 0 1.1-0.493 1.1-1.1 0-0.341-0.155-0.646-0.399-0.848l-0.002-0.001-2.964-2.435c-0.177-0.126-0.397-0.201-0.635-0.201h-0zM25.617 4.889c-0.246 0.001-0.472 0.083-0.654 0.22l0.003-0.002-2.967 2.434c-0.247 0.203-0.402 0.509-0.402 0.851 0 0.608 0.493 1.101 1.101 1.101 0.266 0 0.51-0.094 0.701-0.252l-0.002 0.002 2.963-2.438c0.223-0.202 0.363-0.493 0.363-0.817 0-0.608-0.493-1.1-1.1-1.1-0.002 0-0.004 0-0.006 0h0zM15.989 1.004c-0.576 0.006-1.046 0.452-1.089 1.017l-0 0.004v3.775c0.004 0.605 0.495 1.094 1.1 1.094 0.604 0 1.095-0.487 1.1-1.090v-3.779c-0.044-0.573-0.52-1.021-1.1-1.021-0.004 0-0.007 0-0.011 0h0.001z"></path>
                    </svg>
                    <div>
                        Run Renew SSL
                    </div>

                    <!-- Loading Spinner -->
                    <div wire:loading wire:target="runRenewSSL" class="ml-2 spinner-border animate-spin h-5 w-5 border-t-2 dark:border-white border-primary-700 rounded-full"></div>
                </div>


            </div>



    </div>

</x-filament-panels::page>
