@extends('layout.app')

@section('title', 'Cargos')
@section('page-title', 'Cargos')
@section('page-subtitle', 'Gerir cargos da empresa')

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

    <div class="table-container">
        <div class="px-6 py-4 border-b flex items-center justify-between" style="border-color: var(--border-color)">
            <h3 class="text-sm font-semibold" style="color: var(--text-main)">Lista de Cargos</h3>
            <div class="flex items-center gap-3">
                <form method="GET" class="relative" x-data="{ s: '{{ $search ?? '' }}' }">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color: var(--text-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" x-model="s" x-on:input.debounce.300ms="if (s.length >= 2 || s.length === 0) $root.submit()" placeholder="Pesquisar..." class="w-48 pl-10 pr-4 py-2 rounded-xl text-sm outline-none transition-all duration-200" style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color)" onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border-color)'">
                </form>
                <a href="/rh/positions/create" class="btn-primary px-3 py-1.5 text-xs">+ Novo</a>
                <span class="text-xs" style="color: var(--text-muted)">{{ $positions->total() }} cargo(s)</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Cargo</th>
                        <th>Departamento</th>
                        <th>Faixa Salarial</th>
                        <th>Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($positions->items() as $position)
                    <tr>
                        <td>
                            <span class="font-medium" style="color: var(--text-main)">{{ $position->name }}</span>
                        </td>
                        <td style="color: var(--text-muted)">{{ $position->department->name ?? '—' }}</td>
                        <td style="color: var(--text-muted)">
                            @if($position->salary_range_min || $position->salary_range_max)
                                {{ number_format($position->salary_range_min ?? 0, 2, ',', '.') }} – {{ number_format($position->salary_range_max ?? 0, 2, ',', '.') }} AOA
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($position->status === 'active')
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
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2" x-data="{ confirm: false }">
                                <a href="/rh/positions/{{ $position->id }}/edit" class="p-2 rounded-lg transition-all duration-200" style="color: var(--text-muted)" onmouseover="this.style.color='var(--accent)'; this.style.backgroundColor='var(--accent-soft)'" onmouseout="this.style.color='var(--text-muted)'; this.style.backgroundColor='transparent'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="/rh/positions/{{ $position->id }}/delete" @submit.prevent="confirm ? $el.submit() : (confirm = true)">
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
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-10 h-10" style="color: var(--border-color)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.193 23.193 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <p style="color: var(--text-muted)">Nenhum cargo encontrado</p>
                                <a href="/rh/positions/create" class="font-medium" style="color: var(--accent)">Criar primeiro cargo</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @php $positions->appends(request()->query()) @endphp
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
                    Mostrando {{ $positions->firstItem() }}–{{ $positions->lastItem() }} de {{ $positions->total() }} registrados
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if(!$positions->onFirstPage())
                <a href="{{ $positions->previousPageUrl() }}" class="btn-secondary px-3 py-1.5 text-xs">← Anterior</a>
                @endif
                @if($positions->hasMorePages())
                <a href="{{ $positions->nextPageUrl() }}" class="btn-secondary px-3 py-1.5 text-xs">Próximo →</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
