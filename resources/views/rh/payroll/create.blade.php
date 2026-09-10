@extends('layout.app')

@section('title', 'Novo Processamento')
@section('page-title', 'Processar Folha Salarial')
@section('page-subtitle', 'Gerar folha a partir dos contratos ativos, banco de horas e assiduidade')

@section('content')
<div class="max-w-2xl mx-auto">
    @if(!empty($error))
    <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $error }}
    </div>
    @endif

    @if(empty($hasEligibleEmployees))
    <div class="mb-5 flex items-start gap-3 px-4 py-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 text-sm">
        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="font-semibold">Sem funcionários elegíveis</p>
            <p class="mt-0.5 opacity-90">Não existem contratos activos com salário base nesta empresa. Cadastre funcionários e contratos activos em «RH › Funcionários» e «RH › Contratos» antes de processar a folha.</p>
        </div>
    </div>
    @endif

    <form method="POST" action="/rh/payroll" class="glass-card rounded-2xl p-6 space-y-6">
        <div>
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Período de Processamento</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Data de Início <span class="text-red-400">*</span></label>
                    <input type="date" name="period_start" required class="form-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Data de Fim <span class="text-red-400">*</span></label>
                    <input type="date" name="period_end" required class="form-input">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Descrição</label>
                    <input type="text" name="description" class="form-input" placeholder="Ex: Folha de Setembro 2026">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t" style="border-color: var(--border-color)">
            <button type="submit" class="btn-primary" @if(empty($hasEligibleEmployees)) disabled style="opacity:.5;cursor:not-allowed" @endif>Processar Folha</button>
            <a href="/rh/payroll" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection