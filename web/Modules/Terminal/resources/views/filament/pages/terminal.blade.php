<x-filament-panels::page>

    <div>

        <script>
            window.terminal = {};
            window.terminal.sessionId = '{{ $sessionId }}';
        </script>

        @vite('resources/js/web-terminal.js')

        <div class="bg-black/5 dark:bg-white/5 rounded p-4">
            <div id="js-web-terminal"></div>
        </div>

    </div>

</x-filament-panels::page>
