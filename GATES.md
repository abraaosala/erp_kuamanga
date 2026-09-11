# Gates: rh benefits (beneficios)

OWNS: database/migrations/20260910120000_create_benefit_tables.php, app/Models/Benefit.php, app/Models/Employee.php, app/Services/Modules/Rh/BenefitPolicy.php, app/Services/Modules/Rh/BenefitService.php, app/Services/Contracts/BenefitServiceInterface.php, app/Repositories/Modules/Rh/BenefitRepository.php, app/Repositories/Contracts/BenefitRepositoryInterface.php, app/Http/Controllers/Modules/Rh/BenefitController.php, resources/views/rh/benefits/**, tests/Modules/Rh/BenefitTest.php, scripts/verify-benefits.mjs, app/Providers/Modules/Rh/RhServiceProvider.php, routes/rh.php, resources/views/layout/app.blade.php, tests/TestCase.php, docs/modules/rh.md

Scope: implementar o submódulo RH Benefícios (catálogo de benefícios, elegibilidade por cargo/departamento/antiguidade, atribuição por funcionário, controller+views, rotas, testes, docs) seguindo os padrões do projecto.

- [x] G0: este ledger declara outcomes que podem falhar
  CHECK: node C:\Users\abraa\.agents\skills\unlazy\scripts\gate-lint.mjs GATES.md
  EXPECT: LINT OK
  EVIDENCE: automatic-evidence=v1; definition-sha256=34ee768b59c9b273864781b17fbc3fddc8e29d62825b4bb08dfc67096075ed30; exit=0; EXPECT=matched; output-sha256=07de1a3fdbb119780824944843dcfc7da4b1349f87411f5d2df255b3caa7c614; output-bytes=150; shell=C:\WINDOWS\system32\cmd.exe; cwd=D:\Tecnologia\Full\www\erp; path=711c702822d5/35 entries

- [x] G1: a migration (e o schema inline de teste) definem benefits e employee_benefits com elegibilidade e atribuição
  CHECK: node scripts/verify-benefits.mjs migration
  EXPECT: verify-benefits: PASS (migration)
  EVIDENCE: automatic-evidence=v1; definition-sha256=49feee2535d09917dff1aee46707621cce29861a76741b13b0d8b1c5cd10b6e2; exit=0; EXPECT=matched; output-sha256=534011a90f4604d3bb11f04a4dda6315486ca4afbbaecf21c6af7f8e681168c8; output-bytes=34; shell=C:\WINDOWS\system32\cmd.exe; cwd=D:\Tecnologia\Full\www\erp; path=711c702822d5/35 entries

- [x] G2: regras de elegibilidade e atribuição passam nos novos testes
  CHECK: node scripts/verify-benefits.mjs leaf-tests
  EXPECT: verify-benefits: PASS (leaf-tests)
  EVIDENCE: automatic-evidence=v1; definition-sha256=9c9214324e4432407e3595c35f1b9bd66c9e9c36e32dcbf0dd6627e99a454a0c; exit=0; EXPECT=matched; output-sha256=516b56722ded0552b2f8c53b0de989a3a6e89e3676aaac9b2e8669407ae78c77; output-bytes=35; shell=C:\WINDOWS\system32\cmd.exe; cwd=D:\Tecnologia\Full\www\erp; path=711c702822d5/35 entries

- [x] G3: a suite completa continua verde (regressão)
  CHECK: node scripts/verify-benefits.mjs suite
  EXPECT: verify-benefits: PASS (suite)
  EVIDENCE: automatic-evidence=v1; definition-sha256=ea6a2600ee864efdb648db03bcc57970e59bc78a94a4a32b1b17256be7892b03; exit=0; EXPECT=matched; output-sha256=c102719ca9408d1cc3a9af2498677db43078ae89a54b9fbfe6fb9387a8b5b665; output-bytes=30; shell=C:\WINDOWS\system32\cmd.exe; cwd=D:\Tecnologia\Full\www\erp; path=711c702822d5/35 entries

- [x] G4: PHPStan nível 9 continua limpo
  CHECK: node scripts/verify-benefits.mjs phpstan
  EXPECT: verify-benefits: PASS (phpstan)
  EVIDENCE: automatic-evidence=v1; definition-sha256=73fdd3d265c163e041e781d3b1d58b162b33612a9418a78c747d2dbddeac6186; exit=0; EXPECT=matched; output-sha256=3fe79afac40b03ebfd67af435bb5b1cd2af014c23b1348dd4c569041559f53bb; output-bytes=32; shell=C:\WINDOWS\system32\cmd.exe; cwd=D:\Tecnologia\Full\www\erp; path=711c702822d5/35 entries

- [x] G5: o submódulo está exposto em rotas, controller, views e sidebar
  CHECK: node scripts/verify-benefits.mjs views
  EXPECT: verify-benefits: PASS (views)
  EVIDENCE: automatic-evidence=v1; definition-sha256=0e77610b73860fedc12dcfeac79adc923b4b53a9152aa312a9c77bd2034a3a07; exit=0; EXPECT=matched; output-sha256=f998244e71b80b65ac16802b6a11a7d2b9943b6ea932d0d8c8694610f149763a; output-bytes=30; shell=C:\WINDOWS\system32\cmd.exe; cwd=D:\Tecnologia\Full\www\erp; path=711c702822d5/35 entries

- [x] G6: wiring completo (bindings no provider, interfaces, impls e BenefitPolicy presente)
  CHECK: node scripts/verify-benefits.mjs wiring
  EXPECT: verify-benefits: PASS (wiring)
  EVIDENCE: automatic-evidence=v1; definition-sha256=ea72f5f7f71c5bdb9bdf554682968209f0ae7641937ef3defb7b779b3fe7b007; exit=0; EXPECT=matched; output-sha256=310bcb32a97479eddc95b0c2cb29196165f086a32662c38e8cbee31157020ff9; output-bytes=31; shell=C:\WINDOWS\system32\cmd.exe; cwd=D:\Tecnologia\Full\www\erp; path=711c702822d5/35 entries

- [x] G7: docs/modules/rh.md actualizado (secção 12 fechada, testes e totais)
  CHECK: node scripts/verify-benefits.mjs docs
  EXPECT: verify-benefits: PASS (docs)
  EVIDENCE: automatic-evidence=v1; definition-sha256=ea89d3fb01a957c010e46940f69619c9b00f43470c1a3f0b2b4095067ea05014; exit=0; EXPECT=matched; output-sha256=979f59a3d232e0b8c4e43c0737affd587b28aebdd5ed258491ba2f31174acb70; output-bytes=29; shell=C:\WINDOWS\system32\cmd.exe; cwd=D:\Tecnologia\Full\www\erp; path=711c702822d5/35 entries

- [ ] G8: revisão visual das páginas (index, create/edit, show com atribuição) no browser
  EVIDENCE: pending