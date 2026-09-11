#!/usr/bin/env node
// Oracle de verificação do submódulo RH "Férias e Licenças" (gate-ledger).
// Editável apenas pelo dono da leaf; usado pelos gates em GATES.md.
import { execFile } from 'node:child_process';
import { existsSync, readFileSync, readdirSync } from 'node:fs';
import { join } from 'node:path';

const ROOT = process.cwd();
const PASS_MARK = 'verify-leaves: PASS';

function stripAnsi(input) {
  return input.replace(/\u001b\[[0-9;]*m/g, '');
}

function run(cmd, args) {
  return new Promise((resolve) => {
    execFile(cmd, args, { cwd: ROOT, maxBuffer: 8 * 1024 * 1024 }, (error, stdout, stderr) => {
      resolve({ code: error ? (error.code ?? 1) : 0, out: `${stdout}${stderr}` });
    });
  });
}

function hasAll(text, needles) {
  return needles.every((n) => text.includes(n));
}

function file(path) {
  return readFileSync(join(ROOT, path), 'utf8');
}

function exist(path) {
  return existsSync(join(ROOT, path));
}

async function leafTests() {
  const res = await run('php', ['console', 'test', 'Leave']);
  const out = stripAnsi(res.out);
  return res.code === 0 && out.includes('Tests:') && out.includes('passed') && !/failed|Errors:/.test(out);
}

async function suite() {
  const res = await run('php', ['console', 'test']);
  const out = stripAnsi(res.out);
  return res.code === 0 && out.includes('Tests:') && out.includes('passed') && !/failed|Errors:/.test(out);
}

async function phpstan() {
  const res = await run('php', ['vendor/bin/phpstan', 'analyse', '--no-progress', '--memory-limit=1G']);
  return res.code === 0 && stripAnsi(res.out).includes('[OK] No errors');
}

function migration() {
  const dir = 'database/migrations';
  const entry = readdirSync(join(ROOT, dir)).find((f) => f.includes('create_leave_tables'));
  if (!entry) return false;
  const text = file(`${dir}/${entry}`);
  return (
    text.includes("table('leave_requests'") &&
    text.includes("table('leaves'") &&
    text.includes("addForeignKey('employee_id', 'employees'") &&
    text.includes("addForeignKey('leave_request_id', 'leave_requests'") &&
    text.includes("addColumn('days'") &&
    text.includes("addColumn('status'") &&
    file('tests/TestCase.php').includes("create('leave_requests'") &&
    file('tests/TestCase.php').includes("create('leaves'")
  );
}

function wiring() {
  const provider = file('app/Providers/Modules/Rh/RhServiceProvider.php');
  return (
    provider.includes('LeaveRepositoryInterface::class, LeaveRepository::class') &&
    provider.includes('LeaveServiceInterface::class, LeaveService::class') &&
    exist('app/Repositories/Contracts/LeaveRepositoryInterface.php') &&
    exist('app/Repositories/Modules/Rh/LeaveRepository.php') &&
    exist('app/Services/Contracts/LeaveServiceInterface.php') &&
    exist('app/Services/Modules/Rh/LeaveService.php') &&
    exist('app/Services/Modules/Rh/LeavePolicy.php')
  );
}

function views() {
  return (
    haveControllerMethods() &&
    hasAll(file('routes/rh.php'), [
      "LeaveController",
      "'/leaves'",
      "'/leaves/{id}/approve'",
      "'/leaves/{id}/reject'",
      "'/leaves/{id}/cancel'",
    ]) &&
    file('resources/views/layout/app.blade.php').includes('/rh/leaves') &&
    exist('resources/views/rh/leaves/index.blade.php') &&
    exist('resources/views/rh/leaves/create.blade.php')
  );
}

function haveControllerMethods() {
  const controllerFile = 'app/Http/Controllers/Modules/Rh/LeaveController.php';
  if (!exist(controllerFile)) return false;
  const text = file(controllerFile);
  return ['index', 'create', 'store', 'approve', 'reject', 'cancel', 'destroy'].every((m) =>
    new RegExp(`function ${m}\\(`).test(text)
  );
}

function docs() {
  const text = file('docs/modules/rh.md');
  const start = text.indexOf('### 11. Férias e Licenças');
  if (start === -1) return false;
  const afterSection = text.slice(start);
  const end = afterSection.search(/\n### (?=\d)/);
  const section = end === -1 ? afterSection : afterSection.slice(0, end);
  return (
    !section.includes('- [ ]') &&
    section.includes('- [x]') &&
    section.includes('LeaveTest.php') &&
    section.includes('dias por antiguidade')
  );
}

const GATES = {
  migration: async () => migration(),
  'leaf-tests': async () => leafTests(),
  suite: async () => suite(),
  phpstan: async () => phpstan(),
  views: async () => views(),
  wiring: async () => wiring(),
  docs: async () => docs(),
};

const requested = process.argv[2];
if (!requested || !(requested in GATES)) {
  console.error(`uso: node scripts/verify-leaves.mjs <${Object.keys(GATES).join('|')}>`);
  process.exit(2);
}

GATES[requested]()
  .then((ok) => {
    if (ok) {
      console.log(`${PASS_MARK} (${requested})`);
      process.exit(0);
    }
    console.error(`${PASS_MARK.replace('PASS', 'FAIL')} (${requested})`);
    process.exit(1);
  })
  .catch(() => process.exit(1));