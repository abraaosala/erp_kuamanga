@extends('layout.app')

@section('title', 'Nova Escala')
@section('page-title', 'Nova Escala de Trabalho')
@section('page-subtitle', 'Registar um turno/horário de trabalho')

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

    <form method="POST" action="/rh/schedules" class="glass-card rounded-2xl p-6 space-y-6">
        <div>
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Dados da Escala</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Nome <span class="text-red-400">*</span></label>
                    <input type="text" name="name" required class="form-input" placeholder="Ex: Turno Normal, Turno Noturno">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Hora de Entrada</label>
                    <input type="time" name="check_in_time" class="form-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Hora de Saída</label>
                    <input type="time" name="check_out_time" class="form-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Pausa (minutos)</label>
                    <input type="number" name="break_minutes" value="0" min="0" max="600" class="form-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Status</label>
                    <select name="status" class="form-input">
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Dias da Semana</label>
                    <div class="flex flex-wrap gap-3">
                        @php
                            $days = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
                        @endphp
                        @foreach($days as $i => $day)
                        <label class="flex items-center gap-2 text-xs" style="color: var(--text-muted)">
                            <input type="checkbox" name="days_of_week[]" value="{{ $i + 1 }}" class="rounded accent-violet-500">
                            {{ $day }}
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t" style="border-color: var(--border-color)">
            <button type="submit" class="btn-primary">Salvar</button>
            <a href="/rh/schedules" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
