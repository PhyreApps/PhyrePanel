<x-filament-panels::page>

    <div>
        <link rel="stylesheet" href="node_modules/@xterm/xterm/css/xterm.css" />
        <script src="node_modules/@xterm/xterm/lib/xterm.js"></script>


        <div id="js-web-terminal"></div>
        <script>

            const terminal = new Terminal();
            terminal.open(document.getElementById('js-web-terminal'));
            //terminal.resize(160, 30);

            const socket = new WebSocket(`ws://49.13.166.115:3311`);
            socket.addEventListener('open', (_) => {
                terminal.onData((data) => socket.send(data));
                socket.addEventListener('message', (evt) => terminal.write(evt.data));
            });
            socket.addEventListener('error', (_) => {
                terminal.reset();
                terminal.writeln('Connection error.');
            });
            socket.addEventListener('close', (evt) => {
                if (evt.wasClean) {
                    terminal.reset();
                    terminal.writeln(evt.reason ?? 'Connection closed.');
                }
            });
        </script>

    </div>

</x-filament-panels::page>
