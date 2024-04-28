<x-filament-panels::page>

    <div>

        <script>
            window.terminal = {};
            window.terminal.sessionId = '{{ $sessionId }}';
        </script>

        @vite('resources/js/web-terminal.js')

        <div id="js-web-terminal"></div>

    </div>

</x-filament-panels::page>
