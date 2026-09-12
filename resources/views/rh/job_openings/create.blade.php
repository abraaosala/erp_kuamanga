@extends('layout.app')

@section('title', 'Nova Vaga')
@section('page-title', 'Recrutamento')
@section('page-subtitle', 'Criar nova vaga de emprego')

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

    <form method="POST" action="/rh/job-openings" class="glass-card rounded-2xl p-6 space-y-6">
        <div>
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Dados da Vaga</h3>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Título <span class="text-red-400">*</span></label>
                    <input type="text" name="title" required maxlength="150" class="form-input" placeholder="Ex: Assistente Contabilístico">
                </div>
                <div class="grid grid-cols-2 gap-4" x-data="{ departmentId: null }">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Departamento</label>
                        <select name="department_id" class="form-input no-select2" @change="departmentId = $event.target.value ? parseInt($event.target.value) : null">
                            <option value="">—</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Cargo</label>
                        <select name="position_id" class="form-input no-select2">
                            <option value="">—</option>
                            @foreach($positions as $position)
                            <option value="{{ $position->id }}" x-show="!departmentId || {{ $position->department_id ?: 'null' }} === departmentId">{{ $position->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Descrição</label>
                    <textarea name="description" class="form-input" rows="4" placeholder="Responsabilidades e contexto da função"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Requisitos</label>
                    <textarea name="requirements" class="form-input" rows="3" placeholder="Formação, experiência, competências"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Nº de Vagas</label>
                        <input type="number" name="openings_count" min="1" max="999" value="1" class="form-input">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Localização</label>
                        <input type="text" name="location" maxlength="150" class="form-input" placeholder="Ex: Luanda">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Salário Mínimo (Kz)</label>
                        <input type="number" name="salary_range_min" min="0" step="0.01" class="form-input" placeholder="Ex: 150000">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Salário Máximo (Kz)</label>
                        <input type="number" name="salary_range_max" min="0" step="0.01" class="form-input" placeholder="Ex: 250000">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Data de Encerramento</label>
                        <input type="date" name="closes_at" class="form-input">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Status</label>
                        <select name="status" class="form-input">
                            @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ $status === 'aberta' ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t" style="border-color: var(--border-color)">
            <button type="submit" class="btn-primary">Salvar</button>
            <a href="/rh/job-openings" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection