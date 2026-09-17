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
    fs.symlinkSync(path.join(dir, 'missing'), path.join(dir, 'docs'));
    assert.throws(() => safeFile(dir, 'docs/secret.md'));
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

test('publishing rechecks authorization, freezes Issue and never publishes protected paths', async () => {
  const temp = fs.mkdtempSync(path.join(os.tmpdir(), 'agent-api-test-'));
  const originalEnv = { ...process.env }, originalFetch = globalThis.fetch;
  const calls = [];
  const issue = { number: 12, title: 'Fix Snake', body: 'Task', state: 'open',
    updated_at: '2026-09-17T00:00:00Z', labels: [{ name: 'agent' }] };
  let permission = 'read';
  try {
    Object.assign(process.env, { RUNNER_TEMP: temp, GH_TOKEN: 'fake-test-token',
      GITHUB_REPOSITORY: 'test/microgames', GITHUB_ACTOR: 'maintainer', GITHUB_TRIGGERING_ACTOR: 'maintainer',
      ISSUE_NUMBER: '12', EXPECTED_BASE_SHA: 'a'.repeat(40), GITHUB_EVENT_PATH: path.join(temp, 'event.json'),
      GITHUB_STEP_SUMMARY: path.join(temp, 'summary.md'), GITHUB_SERVER_URL: 'https://github.com', GITHUB_RUN_ID: '1' });
    fs.writeFileSync(process.env.GITHUB_EVENT_PATH, JSON.stringify({ repository: { default_branch: 'main' } }));
    const dir = path.join(temp, 'microgames-agent'); fs.mkdirSync(dir);
    const proposal = { number: 12, title: issue.title, updated_at: issue.updated_at,
      repository: 'test/microgames', base: 'a'.repeat(40), baseBranch: 'main',
      branch: 'agent/issue-12-fix-snake', files: [{ path: 'app/AgentExample.php', content: '<?php // example' }], results: passed };
    const save = () => fs.writeFileSync(path.join(dir, 'proposal.json'), JSON.stringify(proposal));
    save();
    globalThis.fetch = async (url, options) => {
      const endpoint = new URL(url).pathname.replace('/repos/test/microgames', '');
      const body = options.body ? JSON.parse(options.body) : undefined;
      calls.push({ endpoint, method: options.method, body });
      let data;
      if (endpoint.includes('/collaborators/')) data = { permission };
      else if (endpoint === '/issues/12') data = issue;
      else if (endpoint.includes('/git/matching-refs/')) data = [];
      else if (endpoint === `/git/commits/${'a'.repeat(40)}`) data = { tree: { sha: 'base-tree' } };
      else if (endpoint === '/git/trees') data = { sha: 'tree' };
      else if (endpoint === '/git/commits') data = { sha: 'commit' };
      else if (endpoint === '/git/refs') data = {};
      else if (endpoint === '/pulls') data = { html_url: 'https://github.com/test/microgames/pull/1' };
      else throw new Error(`Unexpected API ${endpoint}`);
      return { ok: true, status: 200, json: async () => data };
    };
    const { publish } = await import(`./control.mjs?integration=${Date.now()}`);
    await assert.rejects(publish(), /Only maintainers/);
    assert.equal(calls.filter(c => c.method === 'POST').length, 0);
    permission = 'write';
    proposal.files[0].path = '.env'; save();
    await assert.rejects(publish(), /Invalid or duplicate path/);
    assert.equal(calls.filter(c => c.method === 'POST').length, 0);
    proposal.files[0].path = 'app/AgentExample.php'; proposal.updated_at = 'old'; save();
    await assert.rejects(publish(), /Issue or proposal changed/);
    proposal.updated_at = issue.updated_at; save();
    await publish();
    assert.deepEqual(calls.filter(c => c.method === 'POST').map(c => c.endpoint),
      ['/git/trees', '/git/commits', '/git/refs', '/pulls']);
    assert.equal(calls.find(c => c.endpoint === '/git/refs').body.ref, 'refs/heads/agent/issue-12-fix-snake');
    assert.equal(calls.find(c => c.endpoint === '/pulls').body.draft, true);
    assert.equal(calls.find(c => c.endpoint === '/pulls').body.base, 'main');
    assert.equal(calls.some(c => c.endpoint.includes('merge')), false);
  } finally {
    globalThis.fetch = originalFetch;
    for (const key of Object.keys(process.env)) if (!(key in originalEnv)) delete process.env[key];
    Object.assign(process.env, originalEnv);
    fs.rmSync(temp, { recursive: true, force: true });
  }
});
