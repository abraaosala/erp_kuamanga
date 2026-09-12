@extends('layout.app')

@section('title', $opening->title)
@section('page-title', 'Recrutamento')
@section('page-subtitle', 'Detalhes da vaga')

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

    <div class="glass-card rounded-2xl p-6 mb-6">
        <div class="flex items-start justify-between gap-6">
            <div class="space-y-2">
                <h2 class="text-xl font-bold" style="color: var(--text-main)">{{ $opening->title }}</h2>
                <div class="flex flex-wrap items-center gap-4 text-xs" style="color: var(--text-muted)">
                    <span>{{ $opening->department?->name ?? '—' }} / {{ $opening->position?->name ?? '—' }}</span>
                    <span>{{ $opening->openings_count }} vaga(s)</span>
                    <span>{{ $opening->location ?? 'Local a definir' }}</span>
                    @if($opening->closes_at)
                    <span>Encerra em {{ date('d/m/Y', strtotime($opening->closes_at)) }}</span>
                    @endif
                </div>
                @if($opening->salary_range_min)
                <p class="text-sm font-semibold" style="color: var(--text-main)">
                    {{ number_format((float) $opening->salary_range_min, 0, ',', '.') }} – {{ $opening->salary_range_max ? number_format((float) $opening->salary_range_max, 0, ',', '.') : '—' }} Kz
                </p>
                @endif
            </div>
            <div class="flex flex-col items-end gap-3">
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
                <form method="POST" action="/rh/job-openings/{{ $opening->id }}/status">
                    <select name="status" onchange="this.form.submit()" class="text-xs rounded-lg px-2 py-1.5 outline-none" style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color)">
                        @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ $opening->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="/rh/job-openings/{{ $opening->id }}/edit" class="btn-secondary px-3 py-1.5 text-xs">Editar vaga</a>
            </div>
        </div>

        @if($opening->description)
        <div class="mt-5 pt-5 border-t" style="border-color: var(--border-color)">
            <h4 class="text-xs font-bold uppercase tracking-wide mb-2" style="color: var(--text-muted)">Descrição</h4>
            <p class="text-sm whitespace-pre-line" style="color: var(--text-muted)">{{ $opening->description }}</p>
        </div>
        @endif

        @if($opening->requirements)
        <div class="mt-4">
            <h4 class="text-xs font-bold uppercase tracking-wide mb-2" style="color: var(--text-muted)">Requisitos</h4>
            <p class="text-sm whitespace-pre-line" style="color: var(--text-muted)">{{ $opening->requirements }}</p>
        </div>
        @endif
    </div>

    <div class="table-container">
        <div class="px-6 py-4 border-b flex items-center justify-between" style="border-color: var(--border-color)">
            <h3 class="text-sm font-semibold" style="color: var(--text-main)">Candidatos desta vaga</h3>
            <a href="/rh/candidates/create" class="btn-primary px-3 py-1.5 text-xs">+ Registar Candidato</a>
        </div>
        <div class="overflow-x-auto">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($candidates as $candidate)
                    <tr>
                        <td>
                            <a href="/rh/candidates/{{ $candidate->id }}" class="font-medium hover:underline" style="color: var(--text-main)">{{ $candidate->name }}</a>
                        </td>
                        <td style="color: var(--text-muted)">{{ $candidate->email }}</td>
                        <td>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-violet-500/10 text-violet-600 border border-violet-500/20">
                                {{ $candidate->status }}
                            </span>
                        </td>
                        <td class="text-right">
                            <a href="/rh/candidates/{{ $candidate->id }}" class="btn-secondary px-3 py-1.5 text-xs">Ver</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center" style="color: var(--text-muted)">
                            Ainda não há candidatos para esta vaga.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection