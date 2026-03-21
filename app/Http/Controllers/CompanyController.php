<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanyController
{
    protected \eftec\bladeone\BladeOne $blade;

    public function __construct(\eftec\bladeone\BladeOne $blade)
    {
        $this->blade = $blade;
    }

    public function index()
    {
        $companies = \App\Models\Empresa::all();
        
        $html = $this->blade->run('companies.index', [
            'companies' => $companies,
            'title'     => 'Gerir Empresas',
        ]);
        return response($html);
    }

    public function switch(Request $request)
    {
        $empresaId = $request->input('empresa_id') ?? $_POST['empresa_id'] ?? 0;
        $empresaId = (int)$empresaId;
        
        if ($empresaId > 0) {
            session()->empresaId($empresaId);
            session()->flash('success', "Empresa alterada com sucesso!");
        }

        return back();
    }
}
