<?php

declare(strict_types=1);

namespace App\Http\Controllers\Modules\Accounting;

use App\Services\Contracts\AccountServiceInterface;
use eftec\bladeone\BladeOne;
use Illuminate\Http\Request;

class JournalController
{
    protected AccountServiceInterface $accountService;
    protected BladeOne $blade;

    public function __construct(AccountServiceInterface $accountService, BladeOne $blade)
    {
        $this->accountService = $accountService;
        $this->blade = $blade;
    }

    public function index(Request $request)
    {
        $empresaId = (int)($_SESSION['empresa_id'] ?? 1);
        $entries = $this->accountService->getJournalEntries($empresaId);
        
        $html = $this->blade->run('accounting.journal.index', [
            'entries' => $entries,
            'title'   => 'Lançamentos Diários',
        ]);
        return response($html);
    }

    public function create(Request $request)
    {
        $empresaId = (int)($_SESSION['empresa_id'] ?? 1);
        $accounts = $this->accountService->getAccountsByEmpresa($empresaId);
        
        $html = $this->blade->run('accounting.journal.create', [
            'accounts' => $accounts,
            'title'    => 'Novo Lançamento',
        ]);
        return response($html);
    }

    public function store(Request $request)
    {
        $empresaId = (int)($_SESSION['empresa_id'] ?? 1);
        

    
        // Ensure we capture items correctly from either Request or $_POST
        $data = $request->all();
        if (empty($data) || !isset($data['items'])) {
            $data = $_POST;
        }

        try {
            // Validation and logic happens in Service
            $this->accountService->createJournalEntry($empresaId, $data);
            session()->flash('success', "Lançamento registrado com sucesso!");
            return redirect('/accounting/journal');
        } catch (\Exception $e) {
            session()->flash('error', "Erro ao registrar lançamento: " . $e->getMessage());
            return redirect('/accounting/journal/create');
        }
    }
}
