@extends('layout.app')

@section('title', 'Benefícios')
@section('page-title', 'Benefícios')
@section('page-subtitle', 'Catálogo de benefícios e elegibilidade dos colaboradores')

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

    <div class="mb-5 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="glass-card rounded-2xl p-4 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold" style="color: var(--text-muted)">Benefícios no catálogo</p>
                <p class="text-sm font-bold mt-1" style="color: var(--text-main)">{{ $summary['total'] }}</p>
            </div>
            <svg class="w-6 h-6" style="color: var(--accent)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="glass-card rounded-2xl p-4 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold" style="color: var(--text-muted)">Benefícios ativos</p>
                <p class="text-sm font-bold mt-1 text-emerald-500">{{ $summary['active'] }}</p>
            </div>
            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div class="glass-card rounded-2xl p-4 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold" style="color: var(--text-muted)">Atribuições ativas</p>
                <p class="text-sm font-bold mt-1" style="color: var(--text-main)">{{ $summary['assignments'] }}</p>
            </div>
            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
    </div>

    <div class="table-container">
        <div class="px-6 py-4 border-b flex items-center justify-between" style="border-color: var(--border-color)">
            <h3 class="text-sm font-semibold" style="color: var(--text-main)">Catálogo de Benefícios</h3>
            <div class="flex items-center gap-3">
                <form method="GET" class="relative" x-data="{ s: '{{ $search ?? '' }}' }">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color: var(--text-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" x-model="s" x-on:input.debounce.300ms="if (s.length >= 2 || s.length === 0) $root.submit()" placeholder="Pesquisar..." class="w-48 pl-10 pr-4 py-2 rounded-xl text-sm outline-none transition-all duration-200" style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color)" onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border-color)'">
                </form>
                <a href="/rh/benefits/create" class="btn-primary px-3 py-1.5 text-xs">+ Novo</a>
                <span class="text-xs" style="color: var(--text-muted)">{{ $benefits->total() }} benefício(s)</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Benefício</th>
                        <th>Categoria</th>
                        <th>Elegibilidade</th>
                        <th>Funcionários</th>
                        <th>Estado</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($benefits->items() as $benefit)
                    <tr>
                        <td>
                            <a href="/rh/benefits/{{ $benefit->id }}" class="font-medium hover:underline" style="color: var(--text-main)">{{ $benefit->name }}</a>
                            @if(!empty($benefit->description))
                            <p class="text-xs mt-0.5 max-w-xs truncate" style="color: var(--text-muted)">{{ $benefit->description }}</p>
                            @endif
                        </td>
                        <td>
                            @switch(strtolower((string)($benefit->category ?? '')))
                                @case('saude')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-rose-500/10 text-rose-600 border border-rose-500/20">Saúde</span>
                                @break
                                @case('transporte')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">Transporte</span>
                                @break
                                @case('alimentacao')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-amber-500/10 text-amber-600 border border-amber-500/20">Alimentação</span>
                                @break
                                @case('educacao')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-blue-500/10 text-blue-600 border border-blue-500/20">Educação</span>
                                @break
                                @default
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-slate-500/10 text-slate-600 border border-slate-500/20">{{ $categories[$benefit->category] ?? '—' }}</span>
                            @endswitch
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-1.5">
                                @if($benefit->position)
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-medium" style="background-color: var(--accent-soft); color: var(--accent)">Cargo: {{ $benefit->position->name }}</span>
                                @endif
                                @if($benefit->department)
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-medium" style="background-color: var(--accent-soft); color: var(--accent)">Depto: {{ $benefit->department->name }}</span>
                                @endif
                                @if((int) $benefit->min_tenure_months > 0)
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-medium" style="background-color: var(--accent-soft); color: var(--accent)">Antiguidade ≥ {{ $benefit->min_tenure_months }}m</span>
                                @endif
                                @if(!$benefit->position && !$benefit->department && (int) $benefit->min_tenure_months === 0)
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-medium" style="color: var(--text-muted)">Todos</span>
                                @endif
                            </div>
                        </td>
                        <td style="color: var(--text-muted)">{{ $assignedCounts[$benefit->id] ?? 0 }}</td>
                        <td>
                            @if($benefit->status === 'active')
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-green-500/10 text-green-600 border border-green-500/20">Ativo</span>
                            @else
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-slate-500/10 text-slate-600 border border-slate-500/20">Inativo</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2" x-data="{ confirm: false }">
                                <a href="/rh/benefits/{{ $benefit->id }}" class="p-2 rounded-lg transition-all duration-200" style="color: var(--text-muted)" onmouseover="this.style.color='var(--accent)'; this.style.backgroundColor='var(--accent-soft)'" onmouseout="this.style.color='var(--text-muted)'; this.style.backgroundColor='transparent'" title="Ver detalhes">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-3 8a7 7 0 10-7-7 7 7 0 007 7z"/>
                                    </svg>
                                </a>
                                <a href="/rh/benefits/{{ $benefit->id }}/edit" class="p-2 rounded-lg transition-all duration-200" style="color: var(--text-muted)" onmouseover="this.style.color='var(--accent)'; this.style.backgroundColor='var(--accent-soft)'" onmouseout="this.style.color='var(--text-muted)'; this.style.backgroundColor='transparent'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="/rh/benefits/{{ $benefit->id }}/delete" @submit.prevent="confirm ? $el.submit() : (confirm = true)">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p style="color: var(--text-muted)">Nenhum benefício encontrado</p>
                                <a href="/rh/benefits/create" class="font-medium" style="color: var(--accent)">Criar primeiro benefício</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @php $benefits->appends(request()->query()) @endphp
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
                    Mostrando {{ $benefits->firstItem() }}–{{ $benefits->lastItem() }} de {{ $benefits->total() }} benefícios
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if(!$benefits->onFirstPage())
                <a href="{{ $benefits->previousPageUrl() }}" class="btn-secondary px-3 py-1.5 text-xs">← Anterior</a>
                @endif
                @if($benefits->hasMorePages())
                <a href="{{ $benefits->nextPageUrl() }}" class="btn-secondary px-3 py-1.5 text-xs">Próximo →</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection