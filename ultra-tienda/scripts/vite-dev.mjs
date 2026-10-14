import { rm } from 'node:fs/promises';
import { spawn } from 'node:child_process';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const projectRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const hotFile = path.join(projectRoot, 'public', 'hot');

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

const shutdown = async (signal) => {
    if (shuttingDown) {
        return;
    }

    shuttingDown = true;
    await removeHotFile();

    if (!vite.killed) {
        vite.kill(signal);
    }
};

process.once('SIGINT', () => shutdown('SIGINT'));
process.once('SIGTERM', () => shutdown('SIGTERM'));

vite.once('exit', async (code, signal) => {
    await removeHotFile();
    process.exitCode = code ?? (signal ? 1 : 0);
});
