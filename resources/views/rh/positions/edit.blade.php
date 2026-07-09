@extends('layout.app')

@section('title', 'Editar Cargo')
@section('page-title', 'Editar Cargo')
@section('page-subtitle', 'Alterar dados do cargo')

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

    <form method="POST" action="/rh/positions/{{ $position->id }}/update" class="glass-card rounded-2xl p-6 space-y-6">
        <div>
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Dados do Cargo</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Nome <span class="text-red-400">*</span></label>
                    <input type="text" name="name" required class="form-input" value="{{ $position->name }}" placeholder="Ex: Contabilista Sénior">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Descrição</label>
                    <textarea name="description" class="form-input" rows="3" placeholder="Descrição das responsabilidades">{{ $position->description }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Departamento</label>
                    <select name="department_id" class="form-input">
                        <option value="">— Selecione —</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $position->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Status</label>
                    <select name="status" class="form-input">
                        <option value="active" {{ $position->status === 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ $position->status === 'inactive' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Salário Mínimo (AOA)</label>
                    <input type="number" step="0.01" min="0" name="salary_range_min" class="form-input" value="{{ $position->salary_range_min }}" placeholder="0,00">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Salário Máximo (AOA)</label>
                    <input type="number" step="0.01" min="0" name="salary_range_max" class="form-input" value="{{ $position->salary_range_max }}" placeholder="0,00">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t" style="border-color: var(--border-color)">
            <button type="submit" class="btn-primary">Atualizar</button>
            <a href="/rh/positions" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
