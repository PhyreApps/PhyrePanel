<x-filament-panels::page>

    <form wire:submit="save">

        <div class="mb-4">
            {{ $this->form }}
        </div>

        <x-filament::button
            type="submit"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove>Save</span>
            <span wire:loading>Saving...</span>
        </x-filament::button>
    </form>

</x-filament-panels::page>
