@extends('layout.app')

@section('title', 'Novo Pedido de Férias')
@section('page-title', 'Novo Pedido de Férias / Licença')
@section('page-subtitle', 'Submeter pedido de férias ou licença para aprovação')

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

    <form method="POST" action="/rh/leaves" class="glass-card rounded-2xl p-6 space-y-6">
        <div>
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Dados do Pedido</h3>
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
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Tipo <span class="text-red-400">*</span></label>
                    <select name="leave_type" required class="form-input">
                        <option value="">— Selecione —</option>
                        <option value="ferias">Férias anuais (22 dias úteis)</option>
                        <option value="licenca_maternidade">Licença de Maternidade</option>
                        <option value="licenca_paternidade">Licença de Paternidade</option>
                        <option value="licenca_doenca">Licença por Doença</option>
                        <option value="licenca_remunerada">Licença Remunerada</option>
                        <option value="licenca_nao_remunerada">Licença não Remunerada</option>
                        <option value="falta_justificada">Falta Justificada</option>
                    </select>
                    <p class="text-[11px] mt-1.5" style="color: var(--text-muted)">No 1.º ano de serviço o direito é proporcional (2 dias/mês completo, mín. 6); após 1 ano são 22 dias úteis/ano.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Data de início <span class="text-red-400">*</span></label>
                    <input type="date" name="start_date" required class="form-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Data de fim <span class="text-red-400">*</span></label>
                    <input type="date" name="end_date" required class="form-input">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Motivo / Observações</label>
                    <textarea name="reason" class="form-input" rows="3" placeholder="Justificativa ou observações"></textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t" style="border-color: var(--border-color)">
            <button type="submit" class="btn-primary">Submeter Pedido</button>
            <a href="/rh/leaves" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection