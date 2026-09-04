@extends('layout.app')

@section('title', 'Editar Funcionário')
@section('page-title', 'Editar Funcionário')
@section('page-subtitle', 'Alterar dados do colaborador')

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

    @if(!empty($success))
    <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $success }}
    </div>
    @endif

    <form method="POST" action="/rh/employees/{{ $employee->id }}/update" class="glass-card rounded-2xl p-6 space-y-6" x-data="{ submitting: false }" @submit="submitting = true">
        <div>
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Dados do Funcionário</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Nome completo <span class="text-red-400">*</span></label>
                    <div class="field">
                        <i data-lucide="user" class="field-icon"></i>
                        <input type="text" name="name" required minlength="2" maxlength="150" class="form-input" value="{{ $employee->name }}" placeholder="Nome do funcionário">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Email</label>
                    <div class="field">
                        <i data-lucide="mail" class="field-icon"></i>
                        <input type="email" name="email" class="form-input" value="{{ $employee->email }}" placeholder="email@exemplo.com">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Telefone</label>
                    <div class="field">
                        <i data-lucide="phone" class="field-icon"></i>
                        <input type="tel" name="phone" data-mask="phone" class="form-input" value="{{ $employee->phone }}" placeholder="+244 900 000 000" maxlength="17">
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
                                <option value="{{ $dept->id }}" {{ $employee->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
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
                                <option value="{{ $pos->id }}" {{ $employee->position_id == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Data de Admissão</label>
                    <div class="field">
                        <i data-lucide="calendar" class="field-icon"></i>
                        <input type="date" name="hire_date" class="form-input" value="{{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '' }}">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Status</label>
                    <div class="field">
                        <i data-lucide="toggle-right" class="field-icon"></i>
                        <select name="status" class="form-input no-select2">
                            <option value="active" {{ $employee->status === 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ $employee->status === 'inactive' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">BI</label>
                    <div class="field">
                        <i data-lucide="id-card" class="field-icon"></i>
                        <input type="text" name="bi" data-mask="bi" class="form-input" value="{{ $employee->bi }}" placeholder="000000000AB000" maxlength="14">
                    </div>
                    <p class="field-hint">9 dígitos + 2 letras + 3 dígitos</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">INSS</label>
                    <div class="field">
                        <i data-lucide="hash" class="field-icon"></i>
                        <input type="text" name="inss" data-mask="numeric" class="form-input" value="{{ $employee->inss }}" placeholder="Número do INSS" maxlength="12">
                    </div>
                    <p class="field-hint">Apenas números</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t" style="border-color: var(--border-color)">
            <button type="submit" class="btn-primary" :disabled="submitting">
                <span x-show="submitting">A guardar...</span>
                <span x-show="!submitting">Atualizar</span>
            </button>
            <a href="/rh/employees" class="btn-secondary">Cancelar</a>
        </div>
    </form>

    <form method="POST" action="/rh/employees/{{ $employee->id }}/documents" enctype="multipart/form-data" class="glass-card rounded-2xl p-6 mt-6" x-data="{ rows: [{ type: 'bi' }] }">
        <div>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold" style="color: var(--text-main)">Enviar documentos</h3>
                    <p class="field-hint">PDF, JPG ou PNG — máx. 2MB cada</p>
                </div>
                <button type="button" @click="rows.push({ type: 'bi' })" class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200" style="color: var(--accent); background-color: var(--accent-soft); border: 1px solid var(--accent)">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Adicionar
                </button>
            </div>

            @php
                $docTypes = [
                    'bi'          => 'BI',
                    'inss'        => 'INSS',
                    'contract'    => 'Contrato',
                    'medical'     => 'Atestado médico',
                    'certificate' => 'Certificado',
                    'cv'          => 'CV',
                    'photo'       => 'Foto',
                ];
                $grouped = [];
                if (!empty($documents)) {
                    foreach ($documents as $doc) {
                        $grouped[$doc->document_type][] = $doc;
                    }
                }
            @endphp

            <template x-for="(row, index) in rows" :key="index">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-40 flex-shrink-0">
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Tipo</label>
                        <select :name="'docs[' + index + '][document_type]'" x-model="row.type" class="form-input no-select2">
                            @foreach($docTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
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

            <button type="submit" class="btn-primary mt-1">Enviar documentos</button>
        </div>
    </form>

    @if(!empty($grouped))
    <div class="glass-card rounded-2xl p-6 mt-6">
        <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Documentos enviados</h3>
        <div class="space-y-3">
            @foreach($grouped as $type => $docs)
                <div>
                    <h4 class="text-xs font-semibold mb-2" style="color: var(--text-muted)">{{ $docTypes[$type] ?? ucfirst($type) }}</h4>
                    <div class="space-y-2">
                        @foreach($docs as $doc)
                        <div class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg bg-black/20 border" style="border-color: var(--border-color)">
                            <div class="flex items-center gap-2 min-w-0">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-muted)">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <div class="min-w-0">
                                    <p class="text-sm truncate" style="color: var(--text-main)">{{ $doc->file_name }}</p>
                                    @if(!empty($doc->document_number))
                                        <p class="text-xs" style="color: var(--text-muted)">{{ $doc->document_number }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <a href="/rh/employees/{{ $employee->id }}/documents/{{ $doc->id }}/download" class="btn-secondary px-3 py-1.5 text-xs">Download</a>
                                <form method="POST" action="/rh/employees/{{ $employee->id }}/documents/{{ $doc->id }}/delete" onsubmit="return confirm('Remover este documento?')">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-400 text-xs hover:bg-red-500/20 transition">Remover</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
