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

    public function index()
    {
        $empresaId = 1;
        $accounts = $this->accountService->getFullChart($empresaId);

        return $this->blade->run('accounting.accounts.index', [
            'accounts' => $accounts
        ]);
    }

    public function create()
    {
        $empresaId = 1;
        $parentAccounts = $this->accountService->getAccountsByEmpresa($empresaId);

        return $this->blade->run('accounting.accounts.create', [
            'parentAccounts' => $parentAccounts
        ]);
    }

    public function store(Request $request)
    {
        $this->accountService->createAccount(1, $request->all());

        header('Location: /accounting/accounts');
        exit;
    }

    public function edit($id)
    {
        $account = $this->accountService->getAccountById((int)$id);
        $empresaId = 1;
        $parentAccounts = $this->accountService->getAccountsByEmpresa($empresaId);

        return $this->blade->run('accounting.accounts.edit', [
            'account' => $account,
            'parentAccounts' => $parentAccounts
        ]);
    }

    public function update($id, Request $request)
    {
        $this->accountService->updateAccount((int)$id, $request->all());

        header('Location: /accounting/accounts');
        exit;
    }

    public function destroy($id)
    {
        $this->accountService->deleteAccount((int)$id);

        header('Location: /accounting/accounts');
        exit;
    }
}
