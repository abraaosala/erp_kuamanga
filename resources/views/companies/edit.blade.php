@extends('layout.app')

@section('title', 'Editar Empresa')
@section('page-title', 'Editar Empresa')
@section('page-subtitle', 'Alterar dados do cliente')

@section('content')
<div class="max-w-2xl">
    @if(!empty($error))
    <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $error }}
    </div>
    @endif

    <form method="POST" action="/companies/{{ $company->id }}/update" class="glass-card rounded-2xl p-6 space-y-6">
        <div>
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Dados da Empresa</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Nome <span class="text-red-400">*</span></label>
                    <input type="text" name="nome" required class="form-input" value="{{ $company->nome }}" placeholder="Razão Social">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">NIF <span class="text-red-400">*</span></label>
                    <input type="text" name="nif" required class="form-input" value="{{ $company->nif }}" placeholder="500000000">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Regime IVA</label>
                    <select name="regime_iva" class="form-input">
                        <option value="">— Selecione —</option>
                        <option value="Geral" {{ $company->regime_iva == 'Geral' ? 'selected' : '' }}>Geral</option>
                        <option value="Simplificado" {{ $company->regime_iva == 'Simplificado' ? 'selected' : '' }}>Simplificado</option>
                        <option value="Isento" {{ $company->regime_iva == 'Isento' ? 'selected' : '' }}>Isento</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Morada</label>
                    <input type="text" name="morada" class="form-input" value="{{ $company->morada }}" placeholder="Rua, número, bairro">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Código Postal</label>
                    <input type="text" name="codigo_postal" class="form-input" value="{{ $company->codigo_postal }}" placeholder="">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Cidade</label>
                    <input type="text" name="cidade" class="form-input" value="{{ $company->cidade }}" placeholder="Luanda">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">País</label>
                    <input type="text" name="pais" class="form-input" value="{{ $company->pais }}" placeholder="Angola">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">CAE</label>
                    <input type="text" name="cae" class="form-input" value="{{ $company->cae }}" placeholder="00000">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Data de Constituição</label>
                    <input type="date" name="data_constituicao" class="form-input" value="{{ $company->data_constituicao ? $company->data_constituicao->format('Y-m-d') : '' }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Status</label>
                    <select name="status" class="form-input">
                        <option value="ativo" {{ $company->status == 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactivo" {{ $company->status == 'inactivo' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t" style="border-color: var(--border-color)">
            <button type="submit" class="btn-primary">Atualizar</button>
            <a href="/companies" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
