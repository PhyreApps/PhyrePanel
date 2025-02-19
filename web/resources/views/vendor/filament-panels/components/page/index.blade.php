@props([
    'fullHeight' => false,
])

@php
    use Filament\Pages\SubNavigationPosition;

    $subNavigation = $this->getCachedSubNavigation();
    $subNavigationPosition = $this->getSubNavigationPosition();
    $widgetData = $this->getWidgetData();
@endphp

<div
    {{
        $attributes->class([
            'fi-page',
            'h-full' => $fullHeight,
        ])
    }}
>
    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_START, scopes: $this->getRenderHookScopes()) }}

    <section
        @class([
            'flex flex-col gap-y-8 py-8',
            'h-full' => $fullHeight,
        ])
    >
        @if ($header = $this->getHeader())
            {{ $header }}
        @elseif ($heading = $this->getHeading())
            @php
                $subheading = $this->getSubheading();
            @endphp

            <x-filament-panels::header
                :actions="$this->getCachedHeaderActions()"
                :breadcrumbs="filament()->hasBreadcrumbs() ? $this->getBreadcrumbs() : []"
                :heading="$heading"
                :subheading="$subheading"
            >
                @if ($heading instanceof \Illuminate\Contracts\Support\Htmlable)
                    <x-slot name="heading">
                        {{ $heading }}
                    </x-slot>
                @endif

                @if ($subheading instanceof \Illuminate\Contracts\Support\Htmlable)
                    <x-slot name="subheading">
                        {{ $subheading }}
                    </x-slot>
                @endif
            </x-filament-panels::header>
        @endif

        <div
            @class([
                'flex flex-col gap-8' => $subNavigation,
                match ($subNavigationPosition) {
                    SubNavigationPosition::Start, SubNavigationPosition::End => 'md:flex-row',
                    default => null,
                } => $subNavigation,
                'h-full' => $fullHeight,
            ])
        >
            @if ($subNavigation)
                <x-filament-panels::page.sub-navigation.select
                    :navigation="$subNavigation"
                />

                @if ($subNavigationPosition === SubNavigationPosition::Start)
                    <x-filament-panels::page.sub-navigation.sidebar
                        :navigation="$subNavigation"
                    />
                @endif

                @if ($subNavigationPosition === SubNavigationPosition::Top)
                    <x-filament-panels::page.sub-navigation.tabs
                        :navigation="$subNavigation"
                    />
                @endif
            @endif

            <div
                @class([
                    'grid flex-1 auto-cols-fr gap-y-8',
                    'h-full' => $fullHeight,
                ])
            >
                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_HEADER_WIDGETS_BEFORE, scopes: $this->getRenderHookScopes()) }}

                @if ($headerWidgets = $this->getVisibleHeaderWidgets())
                    <x-filament-widgets::widgets
                        :columns="$this->getHeaderWidgetsColumns()"
                        :data="$widgetData"
                        :widgets="$headerWidgets"
                        class="fi-page-header-widgets"
                    />
                @endif

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_HEADER_WIDGETS_AFTER, scopes: $this->getRenderHookScopes()) }}

                {{ $slot }}

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_FOOTER_WIDGETS_BEFORE, scopes: $this->getRenderHookScopes()) }}

                @if ($footerWidgets = $this->getVisibleFooterWidgets())
                    <x-filament-widgets::widgets
                        :columns="$this->getFooterWidgetsColumns()"
                        :data="$widgetData"
                        :widgets="$footerWidgets"
                        class="fi-page-footer-widgets"
                    />
                @endif

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_FOOTER_WIDGETS_AFTER, scopes: $this->getRenderHookScopes()) }}
            </div>

            @if ($subNavigation && $subNavigationPosition === SubNavigationPosition::End)
                <x-filament-panels::page.sub-navigation.sidebar
                    :navigation="$subNavigation"
                />
            @endif
        </div>

        @if ($footer = $this->getFooter())
            {{ $footer }}
        @endif
    </section>

    @if (! ($this instanceof \Filament\Tables\Contracts\HasTable))
        <x-filament-actions::modals />
    @elseif ($this->isTableLoaded() && filled($this->defaultTableAction))
        <div
            wire:init="mountTableAction(@js($this->defaultTableAction), @if (filled($this->defaultTableActionRecord)) @js($this->defaultTableActionRecord) @else {{ 'null' }} @endif @if (filled($this->defaultTableActionArguments)) , @js($this->defaultTableActionArguments) @endif)"
        ></div>
    @endif

    @if (filled($this->defaultAction))
        <div
            wire:init="mountAction(@js($this->defaultAction) @if (filled($this->defaultActionArguments)) , @js($this->defaultActionArguments) @endif)"
        ></div>
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_END, scopes: $this->getRenderHookScopes()) }}

    <x-filament-panels::unsaved-action-changes-alert />

    <div class="text-sm dark:text-white/40 text-center mb-4">
        <div style="width: 700px; margin: 0 auto;">
            PhyrePanel is currently in beta, so some features may not work as expected.
            <br />
            If you'd like to support development and help resolve issues, consider <a href="https://www.buymeacoffee.com/phyre">buying a coffee. ☕ </a>
        </div>

       @php
            $primaryColor = 'FFDD00';
            try {
            if (setting('general.brand_primary_color')) {
                $primaryColor = setting('general.brand_primary_color');
                $primaryColor = str_replace('#', '', $primaryColor);
            }
           } catch (\Exception $e) {
                //
            }
       @endphp

        <a href="https://www.buymeacoffee.com/phyre" style="display: inline-block;margin: 15px 0px;">
            <img src="https://img.buymeacoffee.com/button-api/?text=Support Phyre Panel&emoji=&slug=phyre&button_colour={{$primaryColor}}&font_colour=000000&font_family=Cookie&outline_colour=000000&coffee_colour=ffffff" alt="Support Phyre Panel" style="height: 30px !important; ">
        </a>
        <br />
        <div>
            © 2023-2024 <a href="#" class="hover:underline"> PHYRE - Web Hosting Panel ™</a>. All Rights Reserved.
            <br />
            <br />
        </div>
    </div>
</div>
