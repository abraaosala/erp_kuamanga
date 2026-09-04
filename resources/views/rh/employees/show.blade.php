@extends('layout.app')

@section('title', $employee->name . ' | Perfil')
@section('page-title', $employee->name)
@section('page-subtitle', 'Perfil do funcionário')

@section('header-actions')
    <a href="/rh/employees/{{ $employee->id }}/edit" class="btn-secondary px-3 py-1.5 text-xs">Editar</a>
@endsection

@section('content')
<div class="space-y-5" x-data="{ open: false, previewUrl: '', previewName: '' }">
    @if(!empty($error))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $error }}
    </div>
    @endif

    @if(!empty($success))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $success }}
    </div>
    @endif

    <div class="glass-card rounded-2xl p-6">
        <div class="flex items-center gap-5">
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-2xl font-bold text-white flex-shrink-0">
                {{ strtoupper(substr($employee->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <h3 class="text-xl font-bold truncate" style="color: var(--text-main)">{{ $employee->name }}</h3>
                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                    @if($employee->position)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold" style="background-color: var(--accent-soft); color: var(--accent)">
                            <i data-lucide="briefcase" class="w-3 h-3"></i>
                            {{ $employee->position->name }}
                        </span>
                    @endif
                    @if($employee->department)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold" style="background-color: var(--accent-soft); color: var(--accent)">
                            <i data-lucide="building-2" class="w-3 h-3"></i>
                            {{ $employee->department->name }}
                        </span>
                    @endif
                    @if($employee->status === 'active')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Ativo
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-gray-500/10 text-gray-500 border border-gray-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                        Inativo
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass-card rounded-2xl p-5">
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider mb-2" style="color: var(--text-muted)">
                <i data-lucide="clock" class="w-4 h-4"></i> Banho de horas
            </div>
            <p class="text-2xl font-bold" style="color: {{ $balance < 0 ? '#ef4444' : 'var(--accent)' }}">
                {{ number_format($balance, 2) }} h
            </p>
        </div>
        <div class="glass-card rounded-2xl p-5">
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider mb-2" style="color: var(--text-muted)">
                <i data-lucide="file-text" class="w-4 h-4"></i> Contratos
            </div>
            <p class="text-2xl font-bold" style="color: var(--text-main)">{{ $contracts->count() }}</p>
        </div>
        <div class="glass-card rounded-2xl p-5">
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider mb-2" style="color: var(--text-muted)">
                <i data-lucide="paperclip" class="w-4 h-4"></i> Documentos
            </div>
            <p class="text-2xl font-bold" style="color: var(--text-main)">{{ $documents->count() }}</p>
        </div>
        <div class="glass-card rounded-2xl p-5">
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider mb-2" style="color: var(--text-muted)">
                <i data-lucide="calendar-clock" class="w-4 h-4"></i> Escalas
            </div>
            <p class="text-2xl font-bold" style="color: var(--text-main)">{{ $schedules->count() }}</p>
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Dados pessoais</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-muted)">Email</p>
                <p class="text-sm mt-0.5" style="color: var(--text-main)">{{ $employee->email ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-muted)">Telefone</p>
                <p class="text-sm mt-0.5" style="color: var(--text-main)">{{ $employee->phone ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-muted)">BI</p>
                <p class="text-sm mt-0.5" style="color: var(--text-main)">{{ $employee->bi ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-muted)">INSS</p>
                <p class="text-sm mt-0.5" style="color: var(--text-main)">{{ $employee->inss ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-muted)">Data de admissão</p>
                <p class="text-sm mt-0.5" style="color: var(--text-main)">{{ $employee->hire_date ? $employee->hire_date->format('d/m/Y') : '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-muted)">Cargo</p>
                <p class="text-sm mt-0.5" style="color: var(--text-main)">{{ $employee->position->name ?? '—' }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold" style="color: var(--text-main)">Contratos</h3>
                <a href="/rh/contracts/create?employee_id={{ $employee->id }}" class="text-xs font-semibold" style="color: var(--accent)">+ Novo</a>
            </div>
            @forelse($contracts as $contract)
            <div class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg bg-black/20 border mb-2" style="border-color: var(--border-color)">
                <div class="min-w-0">
                    <p class="text-sm font-medium" style="color: var(--text-main)">{{ $contract->tipo_contrato ?? 'Sem tipo' }}</p>
                    <p class="text-xs" style="color: var(--text-muted)">
                        {{ $contract->data_inicio ? $contract->data_inicio->format('d/m/Y') : '—' }}
                        @if($contract->data_fim)
                            → {{ $contract->data_fim->format('d/m/Y') }}
                        @endif
                    </p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-sm font-semibold" style="color: var(--text-main)">{{ $contract->salario_base ? number_format((float) $contract->salario_base, 2, ',', ' ') . ' Kz' : '—' }}</p>
                    @if($contract->status === 'active')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-600">Ativo</span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-gray-500/10 text-gray-500">{{ $contract->status }}</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <i data-lucide="file-text" class="w-8 h-8 mx-auto mb-2" style="color: var(--border-color)"></i>
                <p class="text-sm" style="color: var(--text-muted)">Sem contratos registados</p>
            </div>
            @endforelse
        </div>

        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold" style="color: var(--text-main)">Escalas de trabalho</h3>
                <a href="/rh/schedules" class="text-xs font-semibold" style="color: var(--accent)">Ver todas</a>
            </div>
            @forelse($schedules as $schedule)
            <div class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg bg-black/20 border mb-2" style="border-color: var(--border-color)">
                <div class="min-w-0">
                    <p class="text-sm font-medium" style="color: var(--text-main)">{{ $schedule->name }}</p>
                    <p class="text-xs" style="color: var(--text-muted)">
                        {{ $schedule->check_in_time ? substr($schedule->check_in_time, 0, 5) : '—' }} → {{ $schedule->check_out_time ? substr($schedule->check_out_time, 0, 5) : '—' }}
                        @if($schedule->break_minutes)
                            <span class="ml-1">· pausa {{ $schedule->break_minutes }}min</span>
                        @endif
                    </p>
                </div>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase" style="background-color: var(--accent-soft); color: var(--accent)">{{ $schedule->days_of_week ?? '—' }}</span>
            </div>
            @empty
            <div class="text-center py-8">
                <i data-lucide="calendar-clock" class="w-8 h-8 mx-auto mb-2" style="color: var(--border-color)"></i>
                <p class="text-sm" style="color: var(--text-muted)">Sem escalas atribuídas</p>
            </div>
            @endforelse
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold" style="color: var(--text-main)">Documentos</h3>
            <div class="text-xs font-semibold" style="color: var(--accent)">{{ $documents->count() }} ficheiro(s)</div>
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
            foreach ($documents as $doc) {
                $grouped[$doc->document_type][] = $doc;
            }
        @endphp

        @if(!empty($grouped))
        <div class="space-y-3">
            @foreach($grouped as $type => $docs)
                <div>
                    <h4 class="text-xs font-semibold mb-2" style="color: var(--text-muted)">{{ $docTypes[$type] ?? ucfirst($type) }}</h4>
                    <div class="space-y-2">
                        @foreach($docs as $doc)
                        <div class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg bg-black/20 border" style="border-color: var(--border-color)">
                            <div class="flex items-center gap-2 min-w-0">
                                <i data-lucide="file" class="w-4 h-4 flex-shrink-0" style="color: var(--text-muted)"></i>
                                <div class="min-w-0">
                                    <p class="text-sm truncate" style="color: var(--text-main)">{{ $doc->file_name }}</p>
                                    <p class="text-xs" style="color: var(--text-muted)">{{ round((int) $doc->file_size / 1024, 1) }} KB · {{ strtoupper($doc->mime_type ?? '') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <button type="button"
                                    @click="open=true; previewUrl='/rh/employees/{{ $employee->id }}/documents/{{ $doc->id }}/download?inline=1'; previewName='{{ addslashes($doc->file_name) }}'"
                                    class="btn-secondary px-2.5 py-1.5 text-xs flex items-center gap-1"
                                    title="Visualizar">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Ver
                                </button>
                                <a href="/rh/employees/{{ $employee->id }}/documents/{{ $doc->id }}/download" class="btn-secondary px-2.5 py-1.5 text-xs flex items-center gap-1" title="Descarregar">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <i data-lucide="paperclip" class="w-8 h-8 mx-auto mb-2" style="color: var(--border-color)"></i>
            <p class="text-sm" style="color: var(--text-muted)">Sem documentos</p>
        </div>
        @endif
    </div>

    <!-- Modal de visualização de documento -->
    <div x-show="open"
         x-cloak
         @keydown.escape.window="open=false"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-8"
         style="background-color: rgba(0,0,0,0.6); backdrop-filter: blur(4px);"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="open=false">
        <div class="glass-card rounded-2xl p-0 w-full max-w-3xl flex flex-col max-h-[85vh] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 border-b" style="border-color: var(--border-color)">
                <div class="flex items-center gap-2 min-w-0">
                    <i data-lucide="file-text" class="w-4 h-4 flex-shrink-0" style="color: var(--accent)"></i>
                    <h3 class="text-sm font-semibold truncate" style="color: var(--text-main)">Visualizar documento</h3>
                </div>
                <button type="button" @click="open=false" class="modal-close p-1.5 rounded-lg transition-colors" style="color: var(--text-muted)" title="Fechar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex items-center gap-2 px-5 py-2.5 border-b text-xs" style="border-color: var(--border-color); color: var(--text-muted)">
                <span class="truncate" x-text="previewName"></span>
                <a :href="previewUrl.replace('?inline=1', '')" class="ml-auto flex items-center gap-1 font-semibold flex-shrink-0" style="color: var(--accent)">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download
                </a>
            </div>
            <div class="flex-1 min-h-[60vh] bg-black/25">
                <iframe :src="previewUrl" class="w-full h-[60vh] md:h-[65vh]" style="border:none"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection