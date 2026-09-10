@extends('layout.app')

@section('title', 'Folha Salarial')
@section('page-title', 'Folha Salarial')
@section('page-subtitle', 'Processamento de salários dos colaboradores')

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
            <h3 class="text-sm font-semibold" style="color: var(--text-main)">Processamentos</h3>
            <div class="flex items-center gap-3">
                <form method="GET" class="relative" x-data="{ s: '{{ $search ?? '' }}' }">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color: var(--text-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" x-model="s" x-on:input.debounce.300ms="if (s.length >= 2 || s.length === 0) $root.submit()" placeholder="Pesquisar..." class="w-48 pl-10 pr-4 py-2 rounded-xl text-sm outline-none transition-all duration-200" style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color)" onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border-color)'">
                </form>
                <a href="/rh/payroll/create" class="btn-primary px-3 py-1.5 text-xs">+ Novo Processamento</a>
                <span class="text-xs" style="color: var(--text-muted)">{{ $runs->total() }} processamento(s)</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Período</th>
                        <th>Descrição</th>
                        <th>Funcionários</th>
                        <th>Total Bruto (AOA)</th>
                        <th>Descontos (AOA)</th>
                        <th>Total Líquido (AOA)</th>
                        <th>Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($runs->items() as $run)
                    <tr>
                        <td>
                            <span class="font-medium" style="color: var(--text-main)">{{ $run->period_start->format('d/m/Y') }} — {{ $run->period_end->format('d/m/Y') }}</span>
                        </td>
                        <td style="color: var(--text-muted)">{{ $run->description ?: '—' }}</td>
                        <td>
                            <span class="font-bold" style="color: var(--text-main)">{{ $run->payslips_count }}</span>
                        </td>
                        <td style="color: var(--text-muted)">{{ number_format((float)$run->total_gross, 2, ',', '.') }}</td>
                        <td class="{{ (float)$run->total_deductions > 0 ? 'text-red-500' : '' }}">{{ number_format((float)$run->total_deductions, 2, ',', '.') }}</td>
                        <td>
                            <span class="font-bold text-emerald-600">{{ number_format((float)$run->total_net, 2, ',', '.') }}</span>
                        </td>
                        <td>
                            @switch($run->status)
                                @case('processado')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">Processado</span>
                                @break
                                @default
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-slate-500/10 text-slate-600 border border-slate-500/20">{{ ucfirst($run->status) }}</span>
                            @endswitch
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2" x-data="{ confirm: false }">
                                <a href="/rh/payroll/{{ $run->id }}" class="p-2 rounded-lg transition-all duration-200" style="color: var(--text-muted)" onmouseover="this.style.color='var(--accent)'; this.style.backgroundColor='var(--accent-soft)'" onmouseout="this.style.color='var(--text-muted)'; this.style.backgroundColor='transparent'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="/rh/payroll/{{ $run->id }}/delete" @submit.prevent="confirm ? $el.submit() : (confirm = true)">
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
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-10 h-10" style="color: var(--border-color)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M6.512 15.69c-1.08.572-1.756 1.407-1.756 2.31 0 1.657 3.582 3 8 3s8-1.343 8-3c0-.903-.676-1.738-1.756-2.31"/>
                                </svg>
                                <p style="color: var(--text-muted)">Nenhum processamento salarial encontrado</p>
                                <a href="/rh/payroll/create" class="font-medium" style="color: var(--accent)">Processar primeira folha</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @php $runs->appends(request()->query()) @endphp
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
                    Mostrando {{ $runs->firstItem() }}–{{ $runs->lastItem() }} de {{ $runs->total() }} processamentos
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if(!$runs->onFirstPage())
                <a href="{{ $runs->previousPageUrl() }}" class="btn-secondary px-3 py-1.5 text-xs">← Anterior</a>
                @endif
                @if($runs->hasMorePages())
                <a href="{{ $runs->nextPageUrl() }}" class="btn-secondary px-3 py-1.5 text-xs">Próximo →</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection