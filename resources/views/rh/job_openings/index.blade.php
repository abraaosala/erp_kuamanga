@extends('layout.app')

@section('title', 'Vagas')
@section('page-title', 'Recrutamento')
@section('page-subtitle', 'Gestão de vagas e candidaturas')

@section('content')
<div>
    @if(!empty($success))
    <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $success }}
    </div>
    @endif

    @if(!empty($error))
    <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $error }}
    </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="glass-card rounded-2xl p-4">
            <p class="text-xs font-medium mb-1" style="color: var(--text-muted)">Total de Vagas</p>
            <p class="text-2xl font-bold" style="color: var(--text-main)">{{ $summary['total'] }}</p>
        </div>
        <div class="glass-card rounded-2xl p-4">
            <p class="text-xs font-medium mb-1" style="color: var(--text-muted)">Abertas</p>
            <p class="text-2xl font-bold" style="color: var(--text-main)">{{ $summary['abertas'] }}</p>
        </div>
        <div class="glass-card rounded-2xl p-4">
            <p class="text-xs font-medium mb-1" style="color: var(--text-muted)">Candidaturas</p>
            <p class="text-2xl font-bold" style="color: var(--text-main)">{{ $summary['candidaturas'] }}</p>
        </div>
        <div class="glass-card rounded-2xl p-4">
            <p class="text-xs font-medium mb-1" style="color: var(--text-muted)">Contratados</p>
            <p class="text-2xl font-bold" style="color: var(--text-main)">{{ $summary['contratados'] }}</p>
        </div>
    </div>

    <div class="table-container">
        <div class="px-6 py-4 border-b flex items-center justify-between" style="border-color: var(--border-color)">
            <h3 class="text-sm font-semibold" style="color: var(--text-main)">Vagas de Emprego</h3>
            <div class="flex items-center gap-3">
                <form method="GET" class="relative" x-data="{ s: '{{ $search ?? '' }}' }">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color: var(--text-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" x-model="s" x-on:input.debounce.300ms="if (s.length >= 2 || s.length === 0) $root.submit()" placeholder="Pesquisar..." class="w-48 pl-10 pr-4 py-2 rounded-xl text-sm outline-none transition-all duration-200" style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color)" onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border-color)'">
                </form>
                <a href="/rh/job-openings/create" class="btn-primary px-3 py-1.5 text-xs">+ Nova Vaga</a>
                <span class="text-xs" style="color: var(--text-muted)">{{ $openings->total() }} vaga(s)</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Departamento / Cargo</th>
                        <th>Vagas</th>
                        <th>Status</th>
                        <th>Encerra</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($openings->items() as $opening)
                    <tr>
                        <td>
                            <a href="/rh/job-openings/{{ $opening->id }}" class="font-medium hover:underline" style="color: var(--text-main)">{{ $opening->title }}</a>
                        </td>
                        <td style="color: var(--text-muted)">{{ $opening->department?->name ?? '—' }}{{ $opening->position ? ' / ' . $opening->position->name : '' }}</td>
                        <td style="color: var(--text-muted)">{{ $opening->openings_count }}</td>
                        <td>
                            @if($opening->status === 'aberta')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Aberta
                            </span>
                            @elseif($opening->status === 'pausada')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-amber-500/10 text-amber-600 border border-amber-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Pausada
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-gray-500/10 text-gray-500 border border-gray-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                Encerrada
                            </span>
                            @endif
                        </td>
                        <td style="color: var(--text-muted)">{{ $opening->closes_at ? date('d/m/Y', strtotime($opening->closes_at)) : '—' }}</td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2" x-data="{ confirm: false }">
                                <a href="/rh/job-openings/{{ $opening->id }}" class="p-2 rounded-lg transition-all duration-200" title="Ver" style="color: var(--text-muted)" onmouseover="this.style.color='var(--accent)'; this.style.backgroundColor='var(--accent-soft)'" onmouseout="this.style.color='var(--text-muted)'; this.style.backgroundColor='transparent'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="/rh/job-openings/{{ $opening->id }}/edit" class="p-2 rounded-lg transition-all duration-200" title="Editar" style="color: var(--text-muted)" onmouseover="this.style.color='var(--accent)'; this.style.backgroundColor='var(--accent-soft)'" onmouseout="this.style.color='var(--text-muted)'; this.style.backgroundColor='transparent'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="/rh/job-openings/{{ $opening->id }}/delete" @submit.prevent="confirm ? $el.submit() : (confirm = true)">
                                    <button type="submit" class="p-2 rounded-lg transition-all duration-200" style="color: var(--text-muted)" onmouseover="this.style.color='#ef4444'; this.style.backgroundColor='#fef2f2'" onmouseout="this.style.color='var(--text-muted)'; this.style.backgroundColor='transparent'" :title="confirm ? 'Clique para confirmar' : 'Excluir'">
                                        <svg x-show="!confirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span x-show="confirm" class="text-[10px] font-bold uppercase tracking-wider">Confirmar</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-10 h-10" style="color: var(--border-color)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <p style="color: var(--text-muted)">Nenhuma vaga encontrada</p>
                                <a href="/rh/job-openings/create" class="font-medium" style="color: var(--accent)">Criar primeira vaga</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @php $openings->appends(request()->query()) @endphp
        <div class="px-6 py-4 border-t flex items-center justify-between" style="border-color: var(--border-color)">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2" x-data>
                    <label class="text-xs" style="color: var(--text-muted)">Por página:</label>
                    <select x-on:change="window.location='?perPage='+$event.target.value+'&search='+encodeURIComponent('{{ $search ?? '' }}')" class="text-xs rounded-lg px-2 py-1 outline-none" style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color)">
                        <option value="10" {{ ($perPage ?? 15) == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ ($perPage ?? 15) == 15 ? 'selected' : '' }}>15</option>
                        <option value="20" {{ ($perPage ?? 15) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ ($perPage ?? 15) == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                <p class="text-xs" style="color: var(--text-muted)">
                    Mostrando {{ $openings->firstItem() }}–{{ $openings->lastItem() }} de {{ $openings->total() }} registrados
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if(!$openings->onFirstPage())
                <a href="{{ $openings->previousPageUrl() }}" class="btn-secondary px-3 py-1.5 text-xs">← Anterior</a>
                @endif
                @if($openings->hasMorePages())
                <a href="{{ $openings->nextPageUrl() }}" class="btn-secondary px-3 py-1.5 text-xs">Próximo →</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection