import { rm } from 'node:fs/promises';
import { spawn } from 'node:child_process';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const projectRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const hotFile = path.join(projectRoot, 'public', 'hot');

// Laravel prioriza public/hot sobre el manifest de public/build. Si queda un hot
// de una sesión anterior de "pnpm run dev", la aplicación compilada seguiría
// apuntando al servidor Vite y fallaría con ERR_CONNECTION_REFUSED.
await rm(hotFile, { force: true });

const build = spawn(process.platform === 'win32' ? 'vite.cmd' : 'vite', ['build', ...process.argv.slice(2)], {
    cwd: projectRoot,
    stdio: 'inherit',
    shell: process.platform === 'win32',
});

build.once('exit', async (code, signal) => {
    await rm(hotFile, { force: true });
    process.exitCode = code ?? (signal ? 1 : 0);
});
