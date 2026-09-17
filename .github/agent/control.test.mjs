import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';
import { validPath, safeFile, validateFiles, branchName, agentSession } from './control.mjs';

test('protected files, traversal and unexpected formats are rejected', () => {
  for (const p of ['.env', 'frontend/.env.local', '../app/A.php', '/app/A.php',
    'app/../../.env', 'app\\A.php', 'app//A.php', 'app/AGENTS.md', '.github/workflows/x.yml',
    'composer.json', 'frontend/package.json', 'phpunit.xml', 'config/database.php',
    'docs/private.key', 'app/node_modules/x.js', 'app/.env.backup', 'app/credentials.json',
    'public/games/index.js', 'app/A.php:secret']) assert.equal(validPath(p, true), false, p);
  for (const p of ['app/Http/Controllers/A.php', 'frontend/src/App.tsx', 'tests/Unit/A.php',
    'resources/views/home.blade.php', 'docs/new.md']) assert.equal(validPath(p, true), true, p);
});
test('proposal limits, duplicates and likely credentials are enforced', () => {
  assert.throws(() => validateFiles([{ path: 'app/A.php', content: 'a'.repeat(60001) }]));
  assert.throws(() => validateFiles([{ path: 'app/A.php', content: 'x' }, { path: 'app/a.php', content: 'x' }]));
  assert.throws(() => validateFiles([{ path: 'app/A.php', content: '-----BEGIN PRIVATE KEY-----' }]));
  assert.throws(() => validateFiles(Array.from({ length: 21 }, (_, i) => ({ path: `app/A${i}.php`, content: 'x' }))));
  assert.throws(() => validateFiles(Array.from({ length: 10 }, (_, i) => ({ path: `app/A${i}.php`, content: 'x'.repeat(50000) }))));
});
test('branch slug is deterministic and cannot inject shell/ref syntax', () => {
  assert.equal(branchName(42, 'Añade modo oscuro; $(curl evil)'), 'agent/issue-42-anade-modo-oscuro-curl-evil');
  assert.equal(branchName(1, '✨'), 'agent/issue-1-task');
  assert.throws(() => branchName(-1, 'x'));
});
test('filesystem symlinks are rejected', { skip: process.platform === 'win32' }, () => {
  const dir = fs.mkdtempSync(path.join(os.tmpdir(), 'agent-test-'));
  try {
    fs.symlinkSync(os.tmpdir(), path.join(dir, 'app'));
    assert.throws(() => safeFile(dir, 'app/secret.php'));
  } finally { fs.rmSync(dir, { recursive: true, force: true }); }
});
const passed = [{ command: 'php artisan test', status: 'passed' }];
test('exhausted model budget still validates the last write', async () => {
  let checks = 0, writes = 0;
  const r = await agentSession({ maxCalls: 1, prompt: 'test',
    ask: async () => ({ action: 'write', files: [{ path: 'app/A.php', content: 'ok' }] }),
    read() {}, write() { writes++; }, check() { checks++; return passed; } });
  assert.equal(writes, 1); assert.equal(checks, 1); assert.equal(r.report, passed);
});
test('quota exhaustion stops without retries and validates pending edits', async () => {
  let calls = 0, checks = 0;
  const r = await agentSession({ prompt: 'test', ask: async () => {
    if (++calls === 1) return { action: 'write', files: [] };
    throw new Error('Gemini HTTP 429');
  }, read() {}, write() {}, check() { checks++; return passed; } });
  assert.equal(calls, 2); assert.equal(checks, 1); assert.match(r.stop, /429/);
});
test('finish cannot skip checks', async () => {
  let checks = 0;
  await agentSession({ prompt: 'test', ask: async () => ({ action: 'finish' }),
    read() {}, write() {}, check() { checks++; return passed; } });
  assert.equal(checks, 1);
});
test('three validation rounds maximum and no edits afterwards', async () => {
  let calls = 0, checks = 0, writes = 0;
  const r = await agentSession({ prompt: 'test', ask: async () => (++calls % 2)
    ? { action: 'write', files: [] } : { action: 'check' },
  read() {}, write() { writes++; }, check() { checks++; return [{ command: 'build', status: 'failed' }]; } });
  assert.equal(checks, 3); assert.equal(writes, 3); assert.equal(calls, 6); assert.equal(r.rounds, 3);
});
test('failed checks are returned for repair and final report reflects repaired code', async () => {
  const actions = [{ action: 'write', files: [] }, { action: 'check' },
    { action: 'write', files: [] }, { action: 'finish' }];
  let checks = 0;
  const r = await agentSession({ prompt: 'test', ask: async history => {
    if (actions.length === 2) assert.match(JSON.stringify(history), /failed/);
    return actions.shift();
  }, read() {}, write() {}, check() { return ++checks === 1 ? [{ command: 'build', status: 'failed' }] : passed; } });
  assert.equal(checks, 2); assert.equal(r.report, passed);
});
