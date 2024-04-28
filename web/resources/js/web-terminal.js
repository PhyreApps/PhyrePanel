import '@xterm/xterm/css/xterm.css';
import { Terminal } from '@xterm/xterm';
import { WebglAddon } from '@xterm/addon-webgl';
import { CanvasAddon } from '@xterm/addon-canvas';
import { FitAddon } from '@xterm/addon-fit';

let terminalElement = document.getElementById('js-web-terminal');
if (terminalElement !== null) {

    const terminal = new Terminal({
        allowTransparency: true,
        theme: {
            background: 'rgba(22,22,23,0)',
            foreground: '#cccccc',
            selectionBackground: '#399ef440',
            black: '#666666',
            blue: '#399ef4',
            brightBlack: '#666666',
            brightBlue: '#399ef4',
            brightCyan: '#21c5c7',
            brightGreen: '#4eb071',
            brightMagenta: '#b168df',
            brightRed: '#da6771',
            brightWhite: '#efefef',
            brightYellow: '#fff099',
            cyan: '#21c5c7',
            green: '#4eb071',
            magenta: '#b168df',
            red: '#da6771',
            white: '#efefef',
            yellow: '#fff099'
        }
    });

    if (typeof WebGL2RenderingContext !== 'undefined') {
        terminal.loadAddon(new WebglAddon());
    } else {
        terminal.loadAddon(new CanvasAddon());
    }

    const fitAddon = new FitAddon();
    terminal.loadAddon(fitAddon);

    terminal.open(terminalElement);

    fitAddon.fit();


    const socket = new WebSocket(`ws://${window.location.host}/_shell/?sessionId=${window.terminal.sessionId}`);
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
}
