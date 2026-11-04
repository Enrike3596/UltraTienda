import { rm } from 'node:fs/promises';
import { spawn, spawnSync } from 'node:child_process';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const projectRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const hotFile = path.join(projectRoot, 'public', 'hot');
const devServerUrl = 'http://127.0.0.1:5173';

const isDevServerListening = async () => {
    try {
        await fetch(devServerUrl, { signal: AbortSignal.timeout(1500) });

        return true;
    } catch {
        return false;
    }
};

if (await isDevServerListening()) {
    console.error(
        `\n[laravel] Ya hay un servidor Vite escuchando en ${devServerUrl}.\n` +
            '[laravel] Puede ser una instancia anterior de este proyecto o el servidor de otro proyecto.\n' +
            '[laravel] Cierra ese proceso y vuelve a ejecutar "pnpm run dev".\n',
    );
    process.exit(1);
}

await rm(hotFile, { force: true });

const vite = spawn(process.platform === 'win32' ? 'vite.cmd' : 'vite', process.argv.slice(2), {
    cwd: projectRoot,
    stdio: 'inherit',
    shell: process.platform === 'win32',
});

let shuttingDown = false;

const removeHotFile = async () => {
    await rm(hotFile, { force: true });
};

const killViteProcessTree = (signal) => {
    if (vite.killed) {
        return;
    }

    if (process.platform === 'win32') {
        spawnSync('taskkill', ['/pid', String(vite.pid), '/T', '/F'], { stdio: 'ignore' });
        return;
    }

    vite.kill(signal);
};

const shutdown = async (signal) => {
    if (shuttingDown) {
        return;
    }

    shuttingDown = true;
    await removeHotFile();
    killViteProcessTree(signal);
};

process.once('SIGINT', () => shutdown('SIGINT'));
process.once('SIGTERM', () => shutdown('SIGTERM'));
process.once('SIGHUP', () => shutdown('SIGHUP'));

vite.once('exit', async (code, signal) => {
    await removeHotFile();
    process.exitCode = code ?? (signal ? 1 : 0);
});
