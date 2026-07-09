@extends('layout.app')

@section('title', 'Nova Empresa')
@section('page-title', 'Nova Empresa')
@section('page-subtitle', 'Registar novo cliente')

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

    <form method="POST" action="/companies" class="glass-card rounded-2xl p-6 space-y-6">
        <div>
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Dados da Empresa</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Nome <span class="text-red-400">*</span></label>
                    <input type="text" name="nome" required class="form-input" placeholder="Razão Social">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">NIF <span class="text-red-400">*</span></label>
                    <input type="text" name="nif" required class="form-input" placeholder="500000000">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Regime IVA</label>
                    <select name="regime_iva" class="form-input">
                        <option value="">— Selecione —</option>
                        <option value="Geral">Geral</option>
                        <option value="Simplificado">Simplificado</option>
                        <option value="Isento">Isento</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Morada</label>
                    <input type="text" name="morada" class="form-input" placeholder="Rua, número, bairro">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Código Postal</label>
                    <input type="text" name="codigo_postal" class="form-input" placeholder="">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Cidade</label>
                    <input type="text" name="cidade" class="form-input" placeholder="Luanda">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">País</label>
                    <input type="text" name="pais" class="form-input" placeholder="Angola">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">CAE</label>
                    <input type="text" name="cae" class="form-input" placeholder="00000">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Data de Constituição</label>
                    <input type="date" name="data_constituicao" class="form-input">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t" style="border-color: var(--border-color)">
            <button type="submit" class="btn-primary">Salvar</button>
            <a href="/companies" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
