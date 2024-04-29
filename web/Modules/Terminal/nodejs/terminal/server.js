#!/usr/bin/env node

import { execSync } from 'node:child_process';
import { readFileSync } from 'node:fs';
import { spawn } from 'node-pty';
import { WebSocketServer } from 'ws';

const sessionName = 'phyre_panel_session';
const hostname = execSync('hostname', { silent: true }).toString().trim();

const systemIPs = [];
const terminalConfig = JSON.parse(readFileSync("/usr/local/phyre/web/storage/app/terminal/config.json").toString());
if (terminalConfig.serverIps) {
    for (const ip of terminalConfig.serverIps) {
        systemIPs.push(ip);
    }
}

const config = {
    WEB_TERMINAL_PORT: 8449,
    BACKEND_PORT: 8443,
};

const wss = new WebSocketServer({
    port: parseInt(config.WEB_TERMINAL_PORT, 10),
    verifyClient: async (info, cb) => {

        if (!info.req.headers.cookie.includes(sessionName)) {
            cb(false, 401, 'Unauthorized');
            console.error('Unauthorized connection attempt');
            return;
        }

        const origin = info.origin || info.req.headers.origin;
        let matches = origin === `https://${hostname}:${config.BACKEND_PORT}`;

//        console.log(`Origin: ${origin}`);

        if (!matches) {
            for (const ip of systemIPs) {
                if (origin === `https://${ip}:${config.BACKEND_PORT}`) {
                    matches = true;
                    break;
                }
                if (origin === `http://${ip}:${config.BACKEND_PORT}`) {
                    matches = true;
                    break;
                }
            }
        }

        if (matches) {
            cb(true);
            console.log(`Accepted connection from ${info.req.headers['x-real-ip']} to ${origin}`);
            return;
        }
       console.error(`Forbidden connection attempt from ${info.req.headers['x-real-ip']} to ${origin}`);
       cb(false, 403, 'Forbidden');
    },
});

wss.on('listening', () => {
    console.log(`Listening on port ${config.WEB_TERMINAL_PORT}`);
});

wss.on('connection', (ws, req) => {

    console.log('New connection');

    wss.clients.add(ws);

    const remoteIP = req.headers['x-real-ip'] || req.socket.remoteAddress;

    // Check if session is valid
    let sessionID = null;
    try {
        sessionID = req.url.split('?sessionId=')[1];
    } catch (e) {
        console.error(`Invalid session ID, refusing connection`);
        ws.close(1000, 'Your session has expired.');
        return false;
    }

    console.log(`New connection from ${remoteIP} (${sessionID})`);

    const file = readFileSync(`/usr/local/phyre/web/storage/app/terminal/sessions/${sessionID}`);
    if (!file) {
        console.error(`Invalid session ID ${sessionID}, refusing connection`);
        ws.close(1000, 'Your session has expired.');
        return;
    }
    const fileContent = file.toString();
    const sessionContent = JSON.parse(fileContent);
    if (sessionContent.sessionId !== sessionID) {
        console.error(`Invalid session ID ${sessionID}, refusing connection`);
        ws.close(1000, 'Your session has expired.');
        return;
    }
    if (!sessionContent.user) {
        console.error(`Invalid session ID ${sessionID}, refusing connection`);
        ws.close(1000, 'Your session has expired.');
        return;
    }

    // Get username
   //  const login = session.split('user|s:')[1].split('"')[1];
   //  const impersonating = session.split('look|s:')[1].split('"')[1];
   // const username = impersonating.length > 0 ? impersonating : login;

    const username = sessionContent.user;

    // Get user info
    const passwd = readFileSync('/etc/passwd').toString();
    const userline = passwd.split('\n').find((line) => line.startsWith(`${username}:`));
    if (!userline) {
        console.error(`User ${username} not found, refusing connection`);
        ws.close(1000, 'You are not allowed to access this server.');
        return;
    }
    const [, , uid, gid, , homedir, shell] = userline.split(':');

    if (shell.endsWith('nologin')) {
        console.error(`User ${username} has no shell, refusing connection`);
        ws.close(1000, 'You have no shell access.');
        return;
    }

    // Spawn shell as logged in user
    const pty = spawn(shell, [], {
        name: 'xterm-color',
        uid: parseInt(uid, 10),
        gid: parseInt(gid, 10),
        cwd: homedir,
        env: {
            SHELL: shell,
            TERM: 'xterm-color',
            USER: username,
            HOME: homedir,
            PWD: homedir,
            PHYRE: process.env.PHYRE,
        },
    });
    console.log(`New pty (${pty.pid}): ${shell} as ${username} (${uid}:${gid}) in ${homedir}`);

    // Send/receive data from websocket/pty
    pty.on('data', (data) => ws.send(data));
    ws.on('message', (data) => pty.write(data));

    // Ensure pty is killed when websocket is closed and vice versa
    pty.on('exit', () => {
        console.log(`Ended pty (${pty.pid})`);
        if (ws.OPEN) {
            ws.close();
        }
    });
    ws.on('close', () => {
        console.log(`Ended connection from ${remoteIP} (${sessionID})`);
        pty.kill();
        wss.clients.delete(ws);
    });
});
