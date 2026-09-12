@extends('layout.app')

@section('title', 'Candidatos')
@section('page-title', 'Recrutamento')
@section('page-subtitle', 'Pipeline de candidatos')

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
            <p class="text-xs font-medium mb-1" style="color: var(--text-muted)">Total de Candidatos</p>
            <p class="text-2xl font-bold" style="color: var(--text-main)">{{ $summary['total'] }}</p>
        </div>
        <div class="glass-card rounded-2xl p-4">
            <p class="text-xs font-medium mb-1" style="color: var(--text-muted)">Em Análise</p>
            <p class="text-2xl font-bold" style="color: var(--text-main)">{{ $summary['novos'] + $summary['triagem'] }}</p>
        </div>
        <div class="glass-card rounded-2xl p-4">
            <p class="text-xs font-medium mb-1" style="color: var(--text-muted)">Em Entrevista</p>
            <p class="text-2xl font-bold" style="color: var(--text-main)">{{ $summary['entrevista'] }}</p>
        </div>
        <div class="glass-card rounded-2xl p-4">
            <p class="text-xs font-medium mb-1" style="color: var(--text-muted)">Aprovados / Contratados</p>
            <p class="text-2xl font-bold" style="color: var(--text-main)">{{ $summary['aprovados'] }} / {{ $summary['contratados'] }}</p>
        </div>
    </div>

    <div class="table-container">
        <div class="px-6 py-3 border-b flex items-center gap-3 flex-wrap" style="border-color: var(--border-color)">
            @php
                $tabs = [
                    ''               => ['Todos', $summary['total']],
                    'novo'           => ['Novos', $counts['novo'] ?? 0],
                    'triagem'        => ['Triagem', $counts['triagem'] ?? 0],
                    'entrevista'     => ['Entrevista', $counts['entrevista'] ?? 0],
                    'aprovado'       => ['Aprovados', $counts['aprovado'] ?? 0],
                    'contratado'     => ['Contratados', $counts['contratado'] ?? 0],
                    'rejeitado'      => ['Rejeitados', $counts['rejeitado'] ?? 0],
                ];
            @endphp
            @foreach($tabs as $tabValue => $tab)
            <a href="/rh/candidates?@if($tabValue)status={{ $tabValue }}&@endif{{ request()->has('search') ? 'search=' . urlencode(request()->get('search')) . '&' : '' }}@if(request()->has('job_opening_id'))job_opening_id={{ (int) request()->get('job_opening_id') }}@endif"
               class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all duration-200 {{ ($filterStatus ?? '') === $tabValue ? 'bg-violet-500/15 text-violet-300' : 'hover:bg-white/5' }}" style="color: var(--text-muted)">
                {{ $tab[0] }} <span class="opacity-60">{{ $tab[1] }}</span>
            </a>
            @endforeach
        </div>

        <div class="px-6 py-4 border-b flex items-center justify-between gap-3" style="border-color: var(--border-color)">
            <h3 class="text-sm font-semibold" style="color: var(--text-main)">Candidaturas</h3>
            <div class="flex items-center gap-3">
                <form method="GET" class="relative" x-data="{ s: '{{ $search ?? '' }}' }">
                    @if(!empty($filterStatus))
                    <input type="hidden" name="status" value="{{ $filterStatus }}">
                    @endif
                    @if(!empty($filterOpening))
                    <input type="hidden" name="job_opening_id" value="{{ $filterOpening }}">
                    @endif
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color: var(--text-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" x-model="s" x-on:input.debounce.300ms="if (s.length >= 2 || s.length === 0) $root.submit()" placeholder="Pesquisar candidato..." class="w-48 pl-10 pr-4 py-2 rounded-xl text-sm outline-none transition-all duration-200" style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color)" onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border-color)'">
                </form>
                <a href="/rh/candidates/create" class="btn-primary px-3 py-1.5 text-xs">+ Registar</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Vaga</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($candidates->items() as $candidate)
                    <tr>
                        <td>
                            <a href="/rh/candidates/{{ $candidate->id }}" class="font-medium hover:underline" style="color: var(--text-main)">{{ $candidate->name }}</a>
                        </td>
                        <td style="color: var(--text-muted)">{{ $candidate->jobOpening?->title ?? 'Banco de Talentos' }}</td>
                        <td style="color: var(--text-muted)">{{ $candidate->email }}</td>
                        <td>
                            @php
                                $statusMap = [
                                    'novo'       => ['Novo', 'bg-violet-500/10 text-violet-600 border-violet-500/20'],
                                    'triagem'    => ['Triagem', 'bg-sky-500/10 text-sky-600 border-sky-500/20'],
                                    'entrevista' => ['Entrevista', 'bg-blue-500/10 text-blue-600 border-blue-500/20'],
                                    'aprovado'   => ['Aprovado', 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20'],
                                    'contratado' => ['Contratado', 'bg-teal-500/10 text-teal-600 border-teal-500/20'],
                                    'rejeitado'  => ['Rejeitado', 'bg-red-500/10 text-red-600 border-red-500/20'],
                                ];
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide {{ $statusMap[$candidate->status][1] ?? 'bg-gray-500/10 text-gray-500 border-gray-500/20' }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                {{ $statusMap[$candidate->status][0] ?? ucfirst($candidate->status) }}
                            </span>
                        </td>
                        <td class="text-right">
                            <a href="/rh/candidates/{{ $candidate->id }}" class="btn-secondary px-3 py-1.5 text-xs">Pipeline</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-10 h-10" style="color: var(--border-color)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <p style="color: var(--text-muted)">Nenhum candidato encontrado</p>
                                <a href="/rh/candidates/create" class="font-medium" style="color: var(--accent)">Registar candidato</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @php $candidates->appends(request()->query()) @endphp
        <div class="px-6 py-4 border-t flex items-center justify-between" style="border-color: var(--border-color)">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2" x-data>
                    <label class="text-xs" style="color: var(--text-muted)">Por página:</label>
                    <select x-on:change="window.location='?perPage='+$event.target.value" class="text-xs rounded-lg px-2 py-1 outline-none" style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color)">
                        <option value="10" {{ ($perPage ?? 15) == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ ($perPage ?? 15) == 15 ? 'selected' : '' }}>15</option>
                        <option value="20" {{ ($perPage ?? 15) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ ($perPage ?? 15) == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                <p class="text-xs" style="color: var(--text-muted)">
                    Mostrando {{ $candidates->firstItem() }}–{{ $candidates->lastItem() }} de {{ $candidates->total() }} registrados
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if(!$candidates->onFirstPage())
                <a href="{{ $candidates->previousPageUrl() }}" class="btn-secondary px-3 py-1.5 text-xs">← Anterior</a>
                @endif
                @if($candidates->hasMorePages())
                <a href="{{ $candidates->nextPageUrl() }}" class="btn-secondary px-3 py-1.5 text-xs">Próximo →</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection