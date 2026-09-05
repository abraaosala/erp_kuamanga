@extends('layout.app')

@section('title', 'Novo Funcionário')
@section('page-title', 'Novo Funcionário')
@section('page-subtitle', 'Cadastrar novo colaborador')

@section('content')
<div class="max-w-3xl mx-auto">
    @if(!empty($error))
    <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $error }}
    </div>
    @endif

    <form method="POST" action="/rh/employees" enctype="multipart/form-data" class="glass-card rounded-2xl p-6 space-y-6" x-data="{ submitting: false }" @submit="submitting = true">
        <div x-data="{ photoUrl: '' }">
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Foto do Funcionário</h3>
            <div class="flex items-center gap-5">
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-3xl font-bold text-white flex-shrink-0 overflow-hidden">
                    <template x-if="photoUrl">
                        <img :src="photoUrl" alt="Foto do funcionário" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!photoUrl">
                        <span><i data-lucide="user" class="w-10 h-10"></i></span>
                    </template>
                </div>
                <div class="space-y-2">
                    <label class="btn-secondary px-4 py-2 rounded-xl text-sm font-semibold cursor-pointer inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Escolher foto
                        <input type="file" name="photo" accept=".jpg,.jpeg,.png" class="hidden" @change="const f = $event.target.files[0]; if (f) photoUrl = URL.createObjectURL(f)">
                    </label>
                    <p class="field-hint">JPG ou PNG — máx. 2MB</p>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Dados do Funcionário</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Nome completo <span class="text-red-400">*</span></label>
                    <div class="field">
                        <i data-lucide="user" class="field-icon"></i>
                        <input type="text" name="name" required minlength="2" maxlength="150" class="form-input" placeholder="Nome do funcionário">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Email</label>
                    <div class="field">
                        <i data-lucide="mail" class="field-icon"></i>
                        <input type="email" name="email" class="form-input" placeholder="email@exemplo.com">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Telefone</label>
                    <div class="field">
                        <i data-lucide="phone" class="field-icon"></i>
                        <input type="tel" name="phone" data-mask="phone" class="form-input" placeholder="+244 900 000 000" maxlength="17">
                    </div>
                    <p class="field-hint">Formato: +244 9XX XXX XXX</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Departamento</label>
                    <div class="field">
                        <i data-lucide="building-2" class="field-icon"></i>
                        <select name="department_id" class="form-input no-select2">
                            <option value="">— Selecione —</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Cargo</label>
                    <div class="field">
                        <i data-lucide="briefcase" class="field-icon"></i>
                        <select name="position_id" class="form-input no-select2">
                            <option value="">— Selecione —</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Data de Admissão</label>
                    <div class="field">
                        <i data-lucide="calendar" class="field-icon"></i>
                        <input type="date" name="hire_date" class="form-input">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">BI</label>
                    <div class="field">
                        <i data-lucide="id-card" class="field-icon"></i>
                        <input type="text" name="bi" data-mask="bi" class="form-input" placeholder="000000000AB000" maxlength="14">
                    </div>
                    <p class="field-hint">9 dígitos + 2 letras + 3 dígitos</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">INSS</label>
                    <div class="field">
                        <i data-lucide="hash" class="field-icon"></i>
                        <input type="text" name="inss" data-mask="numeric" class="form-input" placeholder="Número do INSS" maxlength="12">
                    </div>
                    <p class="field-hint">Apenas números</p>
                </div>
            </div>
        </div>

        <div class="border-t pt-5" style="border-color: var(--border-color)" x-data="{ rows: [{ type: 'bi' }] }">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold" style="color: var(--text-main)">Documentos (opcional)</h3>
                    <p class="field-hint">PDF, JPG ou PNG — máx. 2MB cada</p>
                </div>
                <button type="button" @click="rows.push({ type: 'bi' })" class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200" style="color: var(--accent); background-color: var(--accent-soft); border: 1px solid var(--accent)">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Adicionar
                </button>
            </div>

            <template x-for="(row, index) in rows" :key="index">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-40 flex-shrink-0">
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Tipo</label>
                        <select :name="'docs[' + index + '][document_type]'" x-model="row.type" class="form-input no-select2">
                            <option value="bi">BI</option>
                            <option value="inss">INSS</option>
                            <option value="contract">Contrato</option>
                            <option value="medical">Atestado médico</option>
                            <option value="certificate">Certificado</option>
                            <option value="cv">CV</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Ficheiro</label>
                        <input type="file" :name="'document_files[' + index + ']'" class="form-input" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <button type="button" @click="rows.splice(index, 1)" x-show="rows.length > 1" class="mt-7 flex-shrink-0 p-2 rounded-lg transition-all duration-200" style="color: #ef4444" title="Remover">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t" style="border-color: var(--border-color)">
            <button type="submit" class="btn-primary" :disabled="submitting">
                <span x-show="submitting">A guardar...</span>
                <span x-show="!submitting">Salvar</span>
            </button>
            <a href="/rh/employees" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
