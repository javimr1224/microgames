// This file is mounted read-only from the trusted checkout, outside /app.
import { spawnSync } from 'node:child_process';
import { existsSync, readFileSync } from 'node:fs';

const results = [];
function run(command, args, cwd = '/app') {
  const p = spawnSync(command, args, {
    cwd, encoding: 'utf8', timeout: 180_000, maxBuffer: 2 * 1024 * 1024,
    env: { ...process.env, CI: 'true' },
  });
  const result = { command: [command, ...args].join(' '), cwd,
    status: p.status === 0 && !p.error ? 'passed' : 'failed',
    output: `${p.stdout ?? ''}\n${p.stderr ?? ''}\n${p.error?.code ?? ''}`.slice(-10000) };
  results.push(result);
}
if (existsSync('/app/artisan')) {
  run('php', ['artisan', 'package:discover', '--ansi']);
  // Builds precede the Laravel suite because HTTP tests can need the Vite manifest.
}
for (const cwd of ['/app', '/app/frontend']) {
  if (!existsSync(`${cwd}/package.json`)) continue;
  const { scripts = {} } = JSON.parse(readFileSync(`${cwd}/package.json`, 'utf8'));
  for (const name of ['build', 'lint', 'test']) {
    if (scripts[name]) run('npm', name === 'test' ? ['run', name, '--', '--run'] : ['run', name], cwd);
    else results.push({ command: `npm run ${name}`, cwd, status: 'unavailable' });
  }
}
if (existsSync('/app/artisan')) run('php', ['artisan', 'test']);
else results.push({ command: 'php artisan test', cwd: '/app', status: 'unavailable' });
process.stdout.write(JSON.stringify(results));
process.exitCode = results.some(r => r.status === 'failed') ? 1 : 0;
