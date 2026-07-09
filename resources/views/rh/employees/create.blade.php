@extends('layout.app')

@section('title', 'Novo Funcionário')
@section('page-title', 'Novo Funcionário')
@section('page-subtitle', 'Cadastrar novo colaborador')

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

    <form method="POST" action="/rh/employees" class="glass-card rounded-2xl p-6 space-y-6">
        <div>
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Dados do Funcionário</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Nome completo <span class="text-red-400">*</span></label>
                    <input type="text" name="name" required class="form-input" placeholder="Nome do funcionário">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Email</label>
                    <input type="email" name="email" class="form-input" placeholder="email@exemplo.com">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Telefone</label>
                    <input type="text" name="phone" class="form-input" placeholder="+244 900 000 000">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Departamento</label>
                    <select name="department_id" class="form-input">
                        <option value="">— Selecione —</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Cargo</label>
                    <select name="position_id" class="form-input">
                        <option value="">— Selecione —</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Data de Admissão</label>
                    <input type="date" name="hire_date" class="form-input">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t" style="border-color: var(--border-color)">
            <button type="submit" class="btn-primary">Salvar</button>
            <a href="/rh/employees" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
