<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Accounting;

use App\Services\Contracts\AccountServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\Request;

class AccountController
{
    public function __construct(
        protected AccountServiceInterface $accountService,
        protected BladeOne $blade
    ) {}

    public function index(): string
    {
        $empresaId = 1;
        $accounts = $this->accountService->getFullChart($empresaId);

        return $this->blade->run('accounting.accounts.index', [
            'accounts' => $accounts
        ]);
    }

    public function create(): string
    {
        $empresaId = 1;
        $parentAccounts = $this->accountService->getAccountsByEmpresa($empresaId);

        return $this->blade->run('accounting.accounts.create', [
            'parentAccounts' => $parentAccounts
        ]);
    }

    public function store(Request $request): never
    {
        $this->accountService->createAccount(1, $request->all());

        header('Location: /accounting/accounts');
        exit;
    }

    public function edit(int|string $id): string
    {
        $account = $this->accountService->getAccountById((int)$id);
        $empresaId = 1;
        $parentAccounts = $this->accountService->getAccountsByEmpresa($empresaId);

        return $this->blade->run('accounting.accounts.edit', [
            'account' => $account,
            'parentAccounts' => $parentAccounts
        ]);
    }

    public function update(int|string $id, Request $request): never
    {
        $this->accountService->updateAccount((int)$id, $request->all());

        header('Location: /accounting/accounts');
        exit;
    }

    public function destroy(int|string $id): never
    {
        $this->accountService->deleteAccount((int)$id);

        header('Location: /accounting/accounts');
        exit;
    }
}
