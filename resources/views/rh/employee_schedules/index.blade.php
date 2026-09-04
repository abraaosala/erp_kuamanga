@extends('layout.app')

@section('title', 'Funcionários da Escala')
@section('page-title', 'Funcionários vinculados')
@section('page-subtitle', $schedule->name)

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
            <div class="flex items-center gap-3">
                <a href="/rh/schedules" class="p-2 rounded-lg transition-all duration-200" style="color: var(--text-muted)" onmouseover="this.style.color='var(--accent)'; this.style.backgroundColor='var(--accent-soft)'" onmouseout="this.style.color='var(--text-muted)'; this.style.backgroundColor='transparent'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h3 class="text-sm font-semibold" style="color: var(--text-main)">{{ $schedule->name }}</h3>
                    <p class="text-xs" style="color: var(--text-muted)">
                        @php
                            $days = array_filter(explode(',', $schedule->days_of_week ?? ''));
                            $labels = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
                            $out = [];
                            foreach ($days as $d) { $out[] = $labels[(int)$d - 1] ?? $d; }
                        @endphp
                        {{ $schedule->check_in_time ? substr($schedule->check_in_time, 0, 5) : '—' }} – {{ $schedule->check_out_time ? substr($schedule->check_out_time, 0, 5) : '—' }}
                        {{ $out ? '· ' . implode(', ', $out) : '' }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="/rh/schedules/{{ $schedule->id }}/employees/assign" class="btn-primary px-3 py-1.5 text-xs">+ Vincular</a>
                <span class="text-xs" style="color: var(--text-muted)">{{ $employees->count() }} funcionário(s)</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Funcionário</th>
                        <th>Departamento</th>
                        <th>Cargo</th>
                        <th>Principal</th>
                        <th>Vigência</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr>
                        <td>
                            <div class="flex flex-col">
                                <span class="font-medium" style="color: var(--text-main)">{{ $employee->name }}</span>
                                @if($employee->email)
                                <span class="text-xs" style="color: var(--text-muted)">{{ $employee->email }}</span>
                                @endif
                            </div>
                        </td>
                        <td style="color: var(--text-muted)">{{ $employee->department?->name ?? '—' }}</td>
                        <td style="color: var(--text-muted)">{{ $employee->position?->name ?? '—' }}</td>
                        <td>
                            @if($employee->pivot->is_default)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-violet-500/10 text-violet-600 border border-violet-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-violet-500"></span>
                                Principal
                            </span>
                            @else
                            <form method="POST" action="/rh/schedules/{{ $schedule->id }}/employees/{{ $employee->id }}/default" class="inline">
                                <button type="submit" class="text-xs underline transition-all duration-200" style="color: var(--text-muted)" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-muted)'">
                                    Definir principal
                                </button>
                            </form>
                            @endif
                        </td>
                        <td class="text-xs" style="color: var(--text-muted)">
                            @if($employee->pivot->start_date || $employee->pivot->end_date)
                                {{ $employee->pivot->start_date ? date('d/m/Y', strtotime($employee->pivot->start_date)) : '—' }}
                                –
                                {{ $employee->pivot->end_date ? date('d/m/Y', strtotime($employee->pivot->end_date)) : '—' }}
                            @else
                                Indefinida
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2" x-data="{ confirm: false }">
                                <form method="POST" action="/rh/schedules/{{ $schedule->id }}/employees/{{ $employee->id }}/delete" @submit.prevent="confirm ? $el.submit() : (confirm = true)">
                                    <button type="submit" class="p-2 rounded-lg transition-all duration-200" style="color: var(--text-muted)" onmouseover="this.style.color='#ef4444'; this.style.backgroundColor='#fef2f2'" onmouseout="this.style.color='var(--text-muted)'; this.style.backgroundColor='transparent'" :title="confirm ? 'Clique para confirmar' : 'Remover vínculo'">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p style="color: var(--text-muted)">Nenhum funcionário vinculado a esta escala</p>
                                <a href="/rh/schedules/{{ $schedule->id }}/employees/assign" class="font-medium" style="color: var(--accent)">Vincular primeiro funcionário</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
