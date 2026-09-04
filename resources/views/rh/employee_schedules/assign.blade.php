@extends('layout.app')

@section('title', 'Vincular Funcionário')
@section('page-title', 'Vincular Funcionário à Escala')
@section('page-subtitle', $schedule->name)

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

    <form method="POST" action="/rh/schedules/{{ $schedule->id }}/employees" class="glass-card rounded-2xl p-6 space-y-6">
        <div>
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Dados do Vínculo</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Funcionário <span class="text-red-400">*</span></label>
                    <select name="employee_id" required class="form-input">
                        <option value="">Selecione um funcionário</option>
                        @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}{{ $employee->department ? ' — ' . $employee->department->name : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Data de Início</label>
                    <input type="date" name="start_date" class="form-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Data de Fim</label>
                    <input type="date" name="end_date" class="form-input">
                </div>
                <div class="md:col-span-2">
                    <label class="flex items-center gap-2 text-xs" style="color: var(--text-muted)">
                        <input type="checkbox" name="is_default" value="1" class="rounded accent-violet-500">
                        Definir como escala principal deste funcionário
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t" style="border-color: var(--border-color)">
            <button type="submit" class="btn-primary">Salvar</button>
            <a href="/rh/schedules/{{ $schedule->id }}/employees" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
