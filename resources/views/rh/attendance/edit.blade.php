@extends('layout.app')

@section('title', 'Editar Registo de Ponto')
@section('page-title', 'Editar Registo de Ponto')
@section('page-subtitle', 'Alterar dados de frequência')

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

    <form method="POST" action="/rh/attendance/{{ $record->id }}/update" class="glass-card rounded-2xl p-6 space-y-6">
        <div>
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Dados de Frequência</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Funcionário <span class="text-red-400">*</span></label>
                    <select name="employee_id" required class="form-input">
                        <option value="">— Selecione —</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $record->employee_id == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Data <span class="text-red-400">*</span></label>
                    <input type="date" name="date" required class="form-input" value="{{ $record->date->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Status <span class="text-red-400">*</span></label>
                    <select name="status" required class="form-input">
                        <option value="">— Selecione —</option>
                        <option value="presente" {{ $record->status == 'presente' ? 'selected' : '' }}>Presente</option>
                        <option value="atrasado" {{ $record->status == 'atrasado' ? 'selected' : '' }}>Atrasado</option>
                        <option value="falta" {{ $record->status == 'falta' ? 'selected' : '' }}>Falta</option>
                        <option value="justificado" {{ $record->status == 'justificado' ? 'selected' : '' }}>Justificado</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Entrada (Hora)</label>
                    <input type="time" name="check_in" class="form-input" value="{{ $record->check_in ?? '' }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Saída (Hora)</label>
                    <input type="time" name="check_out" class="form-input" value="{{ $record->check_out ?? '' }}">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Observações</label>
                    <textarea name="observations" class="form-input" rows="3" placeholder="Justificativa ou observações">{{ $record->observations }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t" style="border-color: var(--border-color)">
            <button type="submit" class="btn-primary">Atualizar</button>
            <a href="/rh/attendance" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
