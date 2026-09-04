<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Empresa;
use eftec\bladeone\BladeOne;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory as Validator;

class CompanyController
{
    public function __construct(
        protected BladeOne $blade,
        protected Validator $validator
    ) {}

    public function index(): \Illuminate\Http\Response
    {
        $companies = Empresa::all();

        $html = $this->blade->run('companies.index', [
            'companies' => $companies,
            'success'   => $_SESSION['flash_success'] ?? null,
            'error'     => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        return response($html);
    }

    public function create(): \Illuminate\Http\Response
    {
        $html = $this->blade->run('companies.create', [
            'error' => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->all();

        $validation = $this->validator->make($data, [
            'nome'              => 'required|min:2|max:255',
            'nif'               => 'required|max:30|unique:empresas,nif',
            'morada'            => 'nullable|max:255',
            'codigo_postal'     => 'nullable|max:20',
            'cidade'            => 'nullable|max:100',
            'pais'              => 'nullable|max:100',
            'regime_iva'        => 'nullable|max:50',
            'cae'               => 'nullable|max:20',
            'data_constituicao' => 'nullable|date',
            'status'            => 'nullable|in:ativo,inactivo',
        ], [
            'nome.required' => 'O nome da empresa é obrigatório.',
            'nif.required'  => 'O NIF é obrigatório.',
            'nif.unique'    => 'Este NIF já está registado.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/companies/create');
        }

        $data = array_map(fn ($v) => $v === '' ? null : $v, $data);

        try {
            Empresa::create($data);
            $_SESSION['flash_success'] = 'Empresa criada com sucesso!';
            return redirect('/companies');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/companies/create');
        }
    }

    public function edit(int $id): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    {
        $company = Empresa::find($id);

        if (!$company) {
            $_SESSION['flash_error'] = 'Empresa não encontrada.';
            return redirect('/companies');
        }

        $html = $this->blade->run('companies.edit', [
            'company' => $company,
            'error'   => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);

        return response($html);
    }

    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $company = Empresa::find($id);

        if (!$company) {
            $_SESSION['flash_error'] = 'Empresa não encontrada.';
            return redirect('/companies');
        }

        $data = $request->all();

        $validation = $this->validator->make($data, [
            'nome'              => 'required|min:2|max:255',
            'nif'               => 'required|max:30|unique:empresas,nif,' . $id,
            'morada'            => 'nullable|max:255',
            'codigo_postal'     => 'nullable|max:20',
            'cidade'            => 'nullable|max:100',
            'pais'              => 'nullable|max:100',
            'regime_iva'        => 'nullable|max:50',
            'cae'               => 'nullable|max:20',
            'data_constituicao' => 'nullable|date',
            'status'            => 'nullable|in:ativo,inactivo',
        ], [
            'nome.required' => 'O nome da empresa é obrigatório.',
            'nif.required'  => 'O NIF é obrigatório.',
            'nif.unique'    => 'Este NIF já está registado.',
        ]);

        if ($validation->fails()) {
            $_SESSION['flash_error'] = $validation->errors()->first();
            return redirect('/companies/' . $id . '/edit');
        }

        $data = array_map(fn ($v) => $v === '' ? null : $v, $data);

        try {
            $company->update($data);
            $_SESSION['flash_success'] = 'Empresa atualizada com sucesso!';
            return redirect('/companies');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            return redirect('/companies/' . $id . '/edit');
        }
    }

    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        $company = Empresa::find($id);

        if (!$company) {
            $_SESSION['flash_error'] = 'Empresa não encontrada.';
            return redirect('/companies');
        }

        try {
            $company->delete();
            $_SESSION['flash_success'] = 'Empresa removida com sucesso!';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        return redirect('/companies');
    }

    public function switch(Request $request): \Illuminate\Http\RedirectResponse
    {
        $rawEmpresaId = $request->input('empresa_id') ?? $_POST['empresa_id'] ?? 0;
        /** @var int $empresaId */
        $empresaId = is_numeric($rawEmpresaId) ? (int) $rawEmpresaId : 0;

        if ($empresaId > 0) {
            /** @var \App\Core\Session $session */
            $session = session();
            $session->empresaId($empresaId);
            $_SESSION['flash_success'] = 'Empresa alterada com sucesso!';
        }

        return back();
    }
}
