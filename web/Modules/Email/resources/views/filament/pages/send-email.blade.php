<x-filament-panels::page>

    <div>
        <form wire:submit="send">

            <div class="mb-4">
                {{ $this->form }}
            </div>

            <x-filament::button
                type="submit"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Send</span>
                <span wire:loading>Sending...</span>
            </x-filament::button>
        </form>

        <x-filament-actions::modals />
    </div>

</x-filament-panels::page>
