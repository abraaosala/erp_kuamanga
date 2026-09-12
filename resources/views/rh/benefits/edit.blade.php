@extends('layout.app')

@section('title', 'Editar Benefício')
@section('page-title', 'Editar Benefício')
@section('page-subtitle', $benefit->name)

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

    <form method="POST" action="/rh/benefits/{{ $benefit->id }}/update" class="glass-card rounded-2xl p-6 space-y-6">
        <div>
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Dados do Benefício</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{ departmentId: {{ $benefit->department_id ?: 'null' }} }">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Nome <span class="text-red-400">*</span></label>
                    <input type="text" name="name" required maxlength="120" value="{{ $benefit->name }}" class="form-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Categoria</label>
                    <select name="category" class="form-input">
                        <option value="">— Sem categoria —</option>
                        @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ $benefit->category === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Estado <span class="text-red-400">*</span></label>
                    <select name="status" required class="form-input">
                        <option value="active" {{ $benefit->status === 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ $benefit->status === 'inactive' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Descrição</label>
                    <textarea name="description" class="form-input" rows="3" placeholder="Detalhes do benefício">{{ $benefit->description }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Restrito ao cargo</label>
                    <select name="position_id" class="form-input no-select2">
                        <option value="">— Todos os cargos —</option>
                        @foreach($positions as $position)
                        <option value="{{ $position->id }}" x-show="!departmentId || {{ $position->department_id ?: 'null' }} === departmentId" {{ (int) $benefit->position_id === (int) $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Restrito ao departamento</label>
                    <select name="department_id" class="form-input no-select2" @change="departmentId = $event.target.value ? parseInt($event.target.value) : null">
                        <option value="">— Todos os departamentos —</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ (int) $benefit->department_id === (int) $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Antiguidade mínima (meses)</label>
                    <input type="number" name="min_tenure_months" min="0" value="{{ (int) $benefit->min_tenure_months }}" class="form-input">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t" style="border-color: var(--border-color)">
            <button type="submit" class="btn-primary">Guardar Alterações</button>
            <a href="/rh/benefits/{{ $benefit->id }}" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection