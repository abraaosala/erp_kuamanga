@extends('layout.app')

@section('title', 'Novo Lançamento')
@section('page-title', 'Novo Lançamento de Horas')
@section('page-subtitle', 'Registar horas no banco de horas do colaborador')

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

    <form method="POST" action="/rh/hour-bank" class="glass-card rounded-2xl p-6 space-y-6">
        <div>
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Dados do Lançamento</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Funcionário <span class="text-red-400">*</span></label>
                    <select name="employee_id" required class="form-input">
                        <option value="">— Selecione —</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Data <span class="text-red-400">*</span></label>
                    <input type="date" name="date" required class="form-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Horas <span class="text-red-400">*</span></label>
                    <input type="number" name="hours" step="0.25" required class="form-input" placeholder="Ex: 2.50 (use + ou −)">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Tipo <span class="text-red-400">*</span></label>
                    <select name="type" required class="form-input">
                        <option value="">— Selecione —</option>
                        <option value="horas_extra">Horas Extras</option>
                        <option value="compensacao">Compensação</option>
                        <option value="ajuste">Ajuste</option>
                        <option value="saldo_inicial">Saldo Inicial</option>
                    </select>
                    <p class="text-[11px] mt-1.5" style="color: var(--text-muted)">Use valores positivos (+) para crédito e negativos (−) para débito no campo horas.</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Observações</label>
                    <textarea name="observations" class="form-input" rows="3" placeholder="Justificativa ou observações"></textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t" style="border-color: var(--border-color)">
            <button type="submit" class="btn-primary">Salvar</button>
            <a href="/rh/hour-bank" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
