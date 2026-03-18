@props([
    'chartId',
    'chartOptions',
    'contentHeight',
    'pollingInterval',
    'loadingIndicator',
    'deferLoading',
    'readyToLoad',
    'darkMode',
    'extraJsOptions',
])
@php
    $chartType = $chartOptions['chart']['type'] ?? null;
@endphp

<div {!! $deferLoading ? ' wire:init="loadWidget" ' : '' !!} class="flex items-center justify-center filament-apex-charts-chart"
    style="{{ $contentHeight ? 'height: ' . $contentHeight . 'px;' : '' }}">
    @if ($readyToLoad)
        <div id="chart"></div>
        <div x-ignore x-load
            x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('apexcharts') }}"
            x-data="apexcharts({
                options: @js($chartOptions),
                chartId: '#{{ $chartId }}',
                theme: {{ $darkMode ? "document.querySelector('html').matches('.dark') ? 'dark' : 'light'" : "'light'" }},
                extraJsOptions: {{ $extraJsOptions ?? '{}' }},
            })">
        </div>
        <div wire:ignore class="w-full filament-apex-charts-chart-container">

            @if ($chartType === 'bar')
                <div style="max-height: 415px; overflow-y: auto; overflow-x: auto;">
                    <div class="filament-apex-charts-chart-object"
                        x-ref="{{ $chartId }}"
                        id="{{ $chartId }}">
                    </div>
                </div>
            @else
                <div class="filament-apex-charts-chart-object"
                    x-ref="{{ $chartId }}"
                    id="{{ $chartId }}">
                </div>
            @endif

            <div {!! $pollingInterval ? 'wire:poll.' . $pollingInterval . '="updateOptions"' : '' !!} x-data="{}" x-init="$watch('dropdownOpen', value => $wire.dropdownOpen = value)">
            </div>

        </div>
    @else
        <div class="filament-apex-charts-chart-loading-indicator m-auto">
            @if ($loadingIndicator)
                {!! $loadingIndicator !!}
            @else
                <x-filament::loading-indicator class="h-7 w-7 text-gray-500 dark:text-gray-400" wire:loading.delay />
            @endif
        </div>
    @endif
</div>
