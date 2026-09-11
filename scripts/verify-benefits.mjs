#!/usr/bin/env node
// Oracle de verificação do submódulo RH "Benefícios" (gate-ledger).
// Editável apenas pelo dono da leaf; usado pelos gates em GATES.md.
import { execFile } from 'node:child_process';
import { existsSync, readFileSync, readdirSync } from 'node:fs';
import { join } from 'node:path';

const ROOT = process.cwd();
const PASS_MARK = 'verify-benefits: PASS';

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
  const res = await run('php', ['console', 'test', 'Benefit']);
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
  const entry = readdirSync(join(ROOT, dir)).find((f) => f.includes('create_benefit_tables'));
  if (!entry) return false;
  const text = file(`${dir}/${entry}`);
  return (
    text.includes("table('benefits'") &&
    text.includes("table('employee_benefits'") &&
    text.includes("addColumn('min_tenure_months'") &&
    text.includes("addColumn('category'") &&
    text.includes("addIndex(['employee_id', 'benefit_id'], ['unique' => true])") &&
    text.includes("addForeignKey('benefit_id', 'benefits'") &&
    file('tests/TestCase.php').includes("create('benefits'") &&
    file('tests/TestCase.php').includes("create('employee_benefits'")
  );
}

function wiring() {
  const provider = file('app/Providers/Modules/Rh/RhServiceProvider.php');
  return (
    provider.includes('BenefitRepositoryInterface::class, BenefitRepository::class') &&
    provider.includes('BenefitServiceInterface::class, BenefitService::class') &&
    exist('app/Repositories/Contracts/BenefitRepositoryInterface.php') &&
    exist('app/Repositories/Modules/Rh/BenefitRepository.php') &&
    exist('app/Services/Contracts/BenefitServiceInterface.php') &&
    exist('app/Services/Modules/Rh/BenefitService.php') &&
    exist('app/Services/Modules/Rh/BenefitPolicy.php')
  );
}

function views() {
  return (
    haveControllerMethods() &&
    hasAll(file('routes/rh.php'), [
      "BenefitController",
      "'/benefits'",
      "'/benefits/{id}'",
      "'/benefits/{id}/edit'",
      "'/benefits/{id}/employees'",
    ]) &&
    file('resources/views/layout/app.blade.php').includes('/rh/benefits') &&
    exist('resources/views/rh/benefits/index.blade.php') &&
    exist('resources/views/rh/benefits/create.blade.php') &&
    exist('resources/views/rh/benefits/edit.blade.php') &&
    exist('resources/views/rh/benefits/show.blade.php')
  );
}

function haveControllerMethods() {
  const controllerFile = 'app/Http/Controllers/Modules/Rh/BenefitController.php';
  if (!exist(controllerFile)) return false;
  const text = file(controllerFile);
  return ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy', 'assign', 'unassign'].every((m) =>
    new RegExp(`function ${m}\\(`).test(text)
  );
}

function docs() {
  const text = file('docs/modules/rh.md');
  const start = text.indexOf('### 12. Benefícios');
  if (start === -1) return false;
  const afterSection = text.slice(start);
  const end = afterSection.search(/\n### (?=\d)/);
  const section = end === -1 ? afterSection : afterSection.slice(0, end);
  return (
    !section.includes('- [ ]') &&
    section.includes('- [x]') &&
    section.includes('BenefitTest.php') &&
    section.includes('elegibilidade') &&
    hasAll(text, ['13 controllers', '13 repos', '13 services', '15 models', '16 migrations'])
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
  console.error(`uso: node scripts/verify-benefits.mjs <${Object.keys(GATES).join('|')}>`);
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