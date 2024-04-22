@props([
    'livewire' => null,
])

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ __('filament-panels::layout.direction') ?? 'ltr' }}"
    @class([
        'fi min-h-screen',
        'dark' => filament()->hasDarkModeForced(),
    ])
>
    <head>
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::HEAD_START, scopes: $livewire->getRenderHookScopes()) }}

        <meta charset="utf-8" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        @if ($favicon = filament()->getFavicon())
            <link rel="icon" href="{{ $favicon }}" />
        @endif

        <title>
            {{ filled($title = strip_tags(($livewire ?? null)?->getTitle() ?? '')) ? "{$title} - " : null }}
            {{ strip_tags(filament()->getBrandName()) }}
        </title>

        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            custom: {
                                50: 'rgba(var(--c-50), <alpha-value>)',
                                100: 'rgba(var(--c-100), <alpha-value>)',
                                200: 'rgba(var(--c-200), <alpha-value>)',
                                300: 'rgba(var(--c-300), <alpha-value>)',
                                400: 'rgba(var(--c-400), <alpha-value>)',
                                500: 'rgba(var(--c-500), <alpha-value>)',
                                600: 'rgba(var(--c-600), <alpha-value>)',
                                700: 'rgba(var(--c-700), <alpha-value>)',
                                800: 'rgba(var(--c-800), <alpha-value>)',
                                900: 'rgba(var(--c-900), <alpha-value>)',
                                950: 'rgba(var(--c-950), <alpha-value>)',
                            },
                            danger: {
                                50: 'rgba(var(--danger-50), <alpha-value>)',
                                100: 'rgba(var(--danger-100), <alpha-value>)',
                                200: 'rgba(var(--danger-200), <alpha-value>)',
                                300: 'rgba(var(--danger-300), <alpha-value>)',
                                400: 'rgba(var(--danger-400), <alpha-value>)',
                                500: 'rgba(var(--danger-500), <alpha-value>)',
                                600: 'rgba(var(--danger-600), <alpha-value>)',
                                700: 'rgba(var(--danger-700), <alpha-value>)',
                                800: 'rgba(var(--danger-800), <alpha-value>)',
                                900: 'rgba(var(--danger-900), <alpha-value>)',
                                950: 'rgba(var(--danger-950), <alpha-value>)',
                            },
                            gray: {
                                50: 'rgba(var(--gray-50), <alpha-value>)',
                                100: 'rgba(var(--gray-100), <alpha-value>)',
                                200: 'rgba(var(--gray-200), <alpha-value>)',
                                300: 'rgba(var(--gray-300), <alpha-value>)',
                                400: 'rgba(var(--gray-400), <alpha-value>)',
                                500: 'rgba(var(--gray-500), <alpha-value>)',
                                600: 'rgba(var(--gray-600), <alpha-value>)',
                                700: 'rgba(var(--gray-700), <alpha-value>)',
                                800: 'rgba(var(--gray-800), <alpha-value>)',
                                900: 'rgba(var(--gray-900), <alpha-value>)',
                                950: 'rgba(var(--gray-950), <alpha-value>)',
                            },
                            info: {
                                50: 'rgba(var(--info-50), <alpha-value>)',
                                100: 'rgba(var(--info-100), <alpha-value>)',
                                200: 'rgba(var(--info-200), <alpha-value>)',
                                300: 'rgba(var(--info-300), <alpha-value>)',
                                400: 'rgba(var(--info-400), <alpha-value>)',
                                500: 'rgba(var(--info-500), <alpha-value>)',
                                600: 'rgba(var(--info-600), <alpha-value>)',
                                700: 'rgba(var(--info-700), <alpha-value>)',
                                800: 'rgba(var(--info-800), <alpha-value>)',
                                900: 'rgba(var(--info-900), <alpha-value>)',
                                950: 'rgba(var(--info-950), <alpha-value>)',
                            },
                            primary: {
                                50: 'rgba(var(--primary-50), <alpha-value>)',
                                100: 'rgba(var(--primary-100), <alpha-value>)',
                                200: 'rgba(var(--primary-200), <alpha-value>)',
                                300: 'rgba(var(--primary-300), <alpha-value>)',
                                400: 'rgba(var(--primary-400), <alpha-value>)',
                                500: 'rgba(var(--primary-500), <alpha-value>)',
                                600: 'rgba(var(--primary-600), <alpha-value>)',
                                700: 'rgba(var(--primary-700), <alpha-value>)',
                                800: 'rgba(var(--primary-800), <alpha-value>)',
                                900: 'rgba(var(--primary-900), <alpha-value>)',
                                950: 'rgba(var(--primary-950), <alpha-value>)',
                            },
                            success: {
                                50: 'rgba(var(--success-50), <alpha-value>)',
                                100: 'rgba(var(--success-100), <alpha-value>)',
                                200: 'rgba(var(--success-200), <alpha-value>)',
                                300: 'rgba(var(--success-300), <alpha-value>)',
                                400: 'rgba(var(--success-400), <alpha-value>)',
                                500: 'rgba(var(--success-500), <alpha-value>)',
                                600: 'rgba(var(--success-600), <alpha-value>)',
                                700: 'rgba(var(--success-700), <alpha-value>)',
                                800: 'rgba(var(--success-800), <alpha-value>)',
                                900: 'rgba(var(--success-900), <alpha-value>)',
                                950: 'rgba(var(--success-950), <alpha-value>)',
                            },
                            warning: {
                                50: 'rgba(var(--warning-50), <alpha-value>)',
                                100: 'rgba(var(--warning-100), <alpha-value>)',
                                200: 'rgba(var(--warning-200), <alpha-value>)',
                                300: 'rgba(var(--warning-300), <alpha-value>)',
                                400: 'rgba(var(--warning-400), <alpha-value>)',
                                500: 'rgba(var(--warning-500), <alpha-value>)',
                                600: 'rgba(var(--warning-600), <alpha-value>)',
                                700: 'rgba(var(--warning-700), <alpha-value>)',
                                800: 'rgba(var(--warning-800), <alpha-value>)',
                                900: 'rgba(var(--warning-900), <alpha-value>)',
                                950: 'rgba(var(--warning-950), <alpha-value>)',
                            },
                        },
                    },
                },
            }
        </script>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::STYLES_BEFORE, scopes: $livewire->getRenderHookScopes()) }}

        <style>
            [x-cloak=''],
            [x-cloak='x-cloak'],
            [x-cloak='1'] {
                display: none !important;
            }

            @media (max-width: 1023px) {
                [x-cloak='-lg'] {
                    display: none !important;
                }
            }

            @media (min-width: 1024px) {
                [x-cloak='lg'] {
                    display: none !important;
                }
            }
        </style>

        @filamentStyles

        {{ filament()->getTheme()->getHtml() }}
        {{ filament()->getFontHtml() }}

        <style>
            :root {
                --font-family: '{!! filament()->getFontFamily() !!}';
                --sidebar-width: {{ filament()->getSidebarWidth() }};
                --collapsed-sidebar-width: {{ filament()->getCollapsedSidebarWidth() }};
                --default-theme-mode: {{ filament()->getDefaultThemeMode()->value }};
            }
            body {
                font-family: var(--font-family) !important;;
            }
        </style>

        @stack('styles')

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::STYLES_AFTER, scopes: $livewire->getRenderHookScopes()) }}

        @if (! filament()->hasDarkMode())
            <script>
                localStorage.setItem('theme', 'light')
            </script>
        @elseif (filament()->hasDarkModeForced())
            <script>
                localStorage.setItem('theme', 'dark')
            </script>
        @else
            <script>
                const theme = localStorage.getItem('theme') ?? @js(filament()->getDefaultThemeMode()->value)

                if (
                    theme === 'dark' ||
                    (theme === 'system' &&
                        window.matchMedia('(prefers-color-scheme: dark)')
                            .matches)
                ) {
                    document.documentElement.classList.add('dark')
                }
            </script>
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::HEAD_END, scopes: $livewire->getRenderHookScopes()) }}
    </head>

    <body
        {{ $attributes
                ->merge(($livewire ?? null)?->getExtraBodyAttributes() ?? [], escape: false)
                ->class([
                    'fi-body',
                    'fi-panel-' . filament()->getId(),
                    'min-h-screen bg-gray-50 font-normal text-gray-950 antialiased dark:bg-gray-950 dark:text-white',
                ]) }}
    >
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::BODY_START, scopes: $livewire->getRenderHookScopes()) }}

        {{ $slot }}

        @livewire(Filament\Livewire\Notifications::class)

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SCRIPTS_BEFORE, scopes: $livewire->getRenderHookScopes()) }}

        @filamentScripts(withCore: true)

        @if (config('filament.broadcasting.echo'))
            <script data-navigate-once>
                window.Echo = new window.EchoFactory(@js(config('filament.broadcasting.echo')))

                window.dispatchEvent(new CustomEvent('EchoLoaded'))
            </script>
        @endif

        @stack('scripts')

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SCRIPTS_AFTER, scopes: $livewire->getRenderHookScopes()) }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::BODY_END, scopes: $livewire->getRenderHookScopes()) }}
    </body>
</html>
