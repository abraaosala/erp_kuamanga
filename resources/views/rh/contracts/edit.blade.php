@extends('layout.app')

@section('title', 'Editar Contrato')
@section('page-title', 'Editar Contrato')
@section('page-subtitle', 'Alterar dados do contrato')

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

    <form method="POST" action="/rh/contracts/{{ $contract->id }}/update" class="glass-card rounded-2xl p-6 space-y-6">
        <div>
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Dados do Contrato</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Funcionário <span class="text-red-400">*</span></label>
                    <select name="employee_id" required class="form-input">
                        <option value="">— Selecione —</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $contract->employee_id == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Tipo de Contrato <span class="text-red-400">*</span></label>
                    <select name="tipo_contrato" required class="form-input">
                        <option value="">— Selecione —</option>
                        <option value="indeterminado" {{ $contract->tipo_contrato == 'indeterminado' ? 'selected' : '' }}>Sem Termo (Efectivo)</option>
                        <option value="determinado" {{ $contract->tipo_contrato == 'determinado' ? 'selected' : '' }}>Prazo Determinado</option>
                        <option value="estagio" {{ $contract->tipo_contrato == 'estagio' ? 'selected' : '' }}>Estágio Profissional</option>
                        <option value="prestacao_servicos" {{ $contract->tipo_contrato == 'prestacao_servicos' ? 'selected' : '' }}>Prestação de Serviços</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Data de Início <span class="text-red-400">*</span></label>
                    <input type="date" name="data_inicio" required class="form-input" value="{{ $contract->data_inicio->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Data de Fim</label>
                    <input type="date" name="data_fim" class="form-input" value="{{ $contract->data_fim ? $contract->data_fim->format('Y-m-d') : '' }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Salário Base (AOA)</label>
                    <input type="number" step="0.01" min="0" name="salario_base" class="form-input" value="{{ $contract->salario_base }}" placeholder="0,00">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Carga Horária</label>
                    <input type="text" name="carga_horaria" class="form-input" value="{{ $contract->carga_horaria }}" placeholder="Ex: 40h/semana">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Status</label>
                    <select name="status" class="form-input">
                        <option value="active" {{ $contract->status === 'active' ? 'selected' : '' }}>Activo</option>
                        <option value="encerrado" {{ $contract->status === 'encerrado' ? 'selected' : '' }}>Encerrado</option>
                        <option value="rescindido" {{ $contract->status === 'rescindido' ? 'selected' : '' }}>Rescindido</option>
                        <option value="renovado" {{ $contract->status === 'renovado' ? 'selected' : '' }}>Renovado</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Observações</label>
                    <textarea name="observacoes" class="form-input" rows="3" placeholder="Observações relevantes sobre o contrato">{{ $contract->observacoes }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t" style="border-color: var(--border-color)">
            <button type="submit" class="btn-primary">Atualizar</button>
            <a href="/rh/contracts" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
