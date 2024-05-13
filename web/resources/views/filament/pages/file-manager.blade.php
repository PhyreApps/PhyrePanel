<x-filament-panels::page
    @class([
        'fi-resource-manage-files-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
    ])
>

    <div>
        {{ $this->table }}
    </div>

</x-filament-panels::page>
