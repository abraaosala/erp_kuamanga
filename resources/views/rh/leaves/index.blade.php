@extends('layout.app')

@section('title', 'Férias e Licenças')
@section('page-title', 'Férias e Licenças')
@section('page-subtitle', 'Pedidos, saldo de férias e regras legais (LGT Angola)')

@section('content')
@php $activeTab = request('tab', 'pedidos'); @endphp
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

    <div class="mb-5 flex items-center gap-2 p-1 rounded-xl w-fit" style="background-color: var(--bg-main); border: 1px solid var(--border-color)">
        <a href="{{ '?tab=pedidos' . (!empty($search) ? '&search=' . urlencode($search) : '') }}"
           class="px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200"
           style="{{ $activeTab === 'pedidos' ? 'background-color: var(--accent); color: #fff' : 'color: var(--text-muted)' }}">Pedidos</a>
        <a href="?tab=saldos"
           class="px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200"
           style="{{ $activeTab === 'saldos' ? 'background-color: var(--accent); color: #fff' : 'color: var(--text-muted)' }}">Saldos</a>
    </div>

    @if($activeTab === 'pedidos')
    <div class="mb-5 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="glass-card rounded-2xl p-4">
            <p class="text-xs font-semibold" style="color: var(--text-muted)">Pedidos pendentes</p>
            <p class="text-2xl font-bold mt-1 {{ $pendingCount > 0 ? 'text-amber-500' : 'text-slate-400' }}">{{ $pendingCount }}</p>
        </div>
        <div class="glass-card rounded-2xl p-4">
            <p class="text-xs font-semibold" style="color: var(--text-muted)">Dias de férias por ano</p>
            <p class="text-2xl font-bold mt-1 text-violet-500">22 úteis</p>
        </div>
        <div class="glass-card rounded-2xl p-4">
            <p class="text-xs font-semibold" style="color: var(--text-muted)">Base legal</p>
            <p class="text-sm font-medium mt-1" style="color: var(--text-muted)">LGT Angola · 2 dias/mês no 1.º ano (mín. 6)</p>
        </div>
    </div>

    <div class="table-container">
        <div class="px-6 py-4 border-b flex items-center justify-between" style="border-color: var(--border-color)">
            <h3 class="text-sm font-semibold" style="color: var(--text-main)">Pedidos de férias / licenças</h3>
            <div class="flex items-center gap-3">
                <form method="GET" class="relative" x-data="{ s: '{{ $search ?? '' }}' }">
                    <input type="hidden" name="tab" value="pedidos">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color: var(--text-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" x-model="s" x-on:input.debounce.300ms="if (s.length >= 2 || s.length === 0) $root.submit()" placeholder="Pesquisar..." class="w-48 pl-10 pr-4 py-2 rounded-xl text-sm outline-none transition-all duration-200" style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color)" onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border-color)'">
                </form>
                <a href="/rh/leaves/create" class="btn-primary px-3 py-1.5 text-xs">+ Novo Pedido</a>
                <span class="text-xs" style="color: var(--text-muted)">{{ $leaveRequests->total() }} pedido(s)</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Funcionário</th>
                        <th>Tipo</th>
                        <th>Período</th>
                        <th>Dias</th>
                        <th>Status</th>
                        <th>Motivo</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaveRequests->items() as $request)
                    <tr>
                        <td>
                            <span class="font-medium" style="color: var(--text-main)">{{ $request->employee->name ?? '—' }}</span>
                        </td>
                        <td>
                            @switch($request->leave_type)
                                @case('ferias')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-violet-500/10 text-violet-600 border border-violet-500/20">Férias</span>
                                @break
                                @case('licenca_maternidade')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-pink-500/10 text-pink-600 border border-pink-500/20">Licença Maternidade</span>
                                @break
                                @case('licenca_paternidade')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-sky-500/10 text-sky-600 border border-sky-500/20">Licença Paternidade</span>
                                @break
                                @case('licenca_doenca')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-red-500/10 text-red-600 border border-red-500/20">Licença Doença</span>
                                @break
                                @case('licenca_remunerada')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">Licença Remunerada</span>
                                @break
                                @case('licenca_nao_remunerada')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-slate-500/10 text-slate-600 border border-slate-500/20">Licença não Remunerada</span>
                                @break
                                @case('falta_justificada')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-amber-500/10 text-amber-600 border border-amber-500/20">Falta Justificada</span>
                                @break
                            @endswitch
                        </td>
                        <td style="color: var(--text-muted)">
                            {{ $request->start_date->format('d/m/Y') }} – {{ $request->end_date->format('d/m/Y') }}
                        </td>
                        <td><span class="font-bold" style="color: var(--text-main)">{{ $request->days }} dia(s)</span></td>
                        <td>
                            @switch($request->status)
                                @case('pendente')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-amber-500/10 text-amber-600 border border-amber-500/20">Pendente</span>
                                @break
                                @case('aprovado')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">Aprovado</span>
                                @break
                                @case('rejeitado')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-red-500/10 text-red-600 border border-red-500/20">Rejeitado</span>
                                @break
                                @case('cancelado')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-slate-500/10 text-slate-600 border border-slate-500/20">Cancelado</span>
                                @break
                            @endswitch
                        </td>
                        <td class="max-w-xs truncate" style="color: var(--text-muted)">{{ $request->reason ?: ($request->decision_notes ?: '—') }}</td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                @if($request->status === 'pendente')
                                <form method="POST" action="/rh/leaves/{{ $request->id }}/approve">
                                    <button type="submit" class="p-2 rounded-lg transition-all duration-200 text-emerald-500 hover:bg-emerald-500/10" title="Aprovar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </form>
                                <form method="POST" action="/rh/leaves/{{ $request->id }}/reject" @submit.prevent="confirm ? $el.submit() : (confirm = true)" x-data="{ confirm: false }">
                                    <button type="submit" class="p-2 rounded-lg transition-all duration-200 text-red-500 hover:bg-red-500/10" :title="confirm ? 'Confirmar rejeição' : 'Rejeitar'">
                                        <svg x-show="!confirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        <span x-show="confirm" class="text-[10px] font-bold uppercase tracking-wider">Confirmar</span>
                                    </button>
                                </form>
                                <form method="POST" action="/rh/leaves/{{ $request->id }}/cancel">
                                    <button type="submit" class="p-2 rounded-lg transition-all duration-200 text-slate-500 hover:bg-slate-500/10" title="Cancelar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                <form method="POST" action="/rh/leaves/{{ $request->id }}/delete" @submit.prevent="confirm ? $el.submit() : (confirm = true)" x-data="{ confirm: false }">
                                    <button type="submit" class="p-2 rounded-lg transition-all duration-200 text-slate-500 hover:bg-red-500/10" :title="confirm ? 'Clique para confirmar' : 'Excluir'">
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
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-10 h-10" style="color: var(--border-color)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p style="color: var(--text-muted)">Nenhum pedido encontrado</p>
                                <a href="/rh/leaves/create" class="font-medium" style="color: var(--accent)">Criar primeiro pedido</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @php $leaveRequests->appends(request()->query()) @endphp
        <div class="px-6 py-4 border-t flex items-center justify-between" style="border-color: var(--border-color)">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2" x-data>
                    <label class="text-xs" style="color: var(--text-muted)">Por página:</label>
                    <select x-on:change="window.location='?perPage='+$event.target.value+'&search='+encodeURIComponent('{{ $search ?? '' }}')+'&tab=pedidos'" class="text-xs rounded-lg px-2 py-1 outline-none" style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color)">
                        <option value="10" {{ ($perPage ?? 15) == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ ($perPage ?? 15) == 15 ? 'selected' : '' }}>15</option>
                        <option value="20" {{ ($perPage ?? 15) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ ($perPage ?? 15) == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                <p class="text-xs" style="color: var(--text-muted)">
                    Mostrando {{ $leaveRequests->firstItem() }}–{{ $leaveRequests->lastItem() }} de {{ $leaveRequests->total() }} pedidos
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if(!$leaveRequests->onFirstPage())
                <a href="{{ $leaveRequests->previousPageUrl() }}" class="btn-secondary px-3 py-1.5 text-xs">← Anterior</a>
                @endif
                @if($leaveRequests->hasMorePages())
                <a href="{{ $leaveRequests->nextPageUrl() }}" class="btn-secondary px-3 py-1.5 text-xs">Próximo →</a>
                @endif
            </div>
        </div>
    </div>
@elseif($activeTab === 'saldos')
    <div class="mb-5 flex items-center justify-between">
        <h3 class="text-sm font-semibold" style="color: var(--text-main)">Saldo de férias por funcionário</h3>
    </div>
    @if(!empty($balances))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($balances as $row)
        <div class="glass-card rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold" style="color: var(--text-muted)">{{ $row['employee'] }}</p>
                <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded-full"
                      style="color: var(--accent); background-color: var(--accent-soft)">Saldo de férias</span>
            </div>
            <div class="grid grid-cols-3 gap-2 text-center">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wide" style="color: var(--text-muted)">Direito</p>
                    <p class="text-sm font-bold mt-0.5" style="color: var(--text-main)">{{ $row['entitled'] }} d</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wide" style="color: var(--text-muted)">Usados</p>
                    <p class="text-sm font-bold mt-0.5 text-amber-500">{{ $row['used'] }} d</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-wide" style="color: var(--text-muted)">Disponível</p>
                    <p class="text-sm font-bold mt-0.5 {{ $row['available'] > 0 ? 'text-emerald-500' : 'text-red-500' }}">{{ $row['available'] }} d</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="glass-card rounded-2xl p-10 text-center">
        <p style="color: var(--text-muted)">Sem saldos para exibir. Cadastre funcionários para ver o saldo de férias por antiguidade.</p>
    </div>
    @endif
    @endif
</div>
@endsection