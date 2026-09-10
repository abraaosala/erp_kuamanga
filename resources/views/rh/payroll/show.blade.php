@extends('layout.app')

@section('title', 'Detalhe da Folha Salarial')
@section('page-title', 'Folha Salarial - Detalhe')
@section('page-subtitle')
{{ $run->period_start->format('d/m/Y') }} a {{ $run->period_end->format('d/m/Y') }}
@endsection

@section('content')
<div x-data="{ open: false, reciboUrl: '' }">
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

    <div class="mb-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass-card rounded-2xl p-4">
            <p class="text-xs font-semibold" style="color: var(--text-muted)">Funcionários</p>
            <p class="text-2xl font-bold mt-1" style="color: var(--text-main)">{{ $run->employee_count }}</p>
        </div>
        <div class="glass-card rounded-2xl p-4">
            <p class="text-xs font-semibold" style="color: var(--text-muted)">Total Bruto</p>
            <p class="text-2xl font-bold mt-1" style="color: var(--text-main)">{{ number_format((float)$run->total_gross, 2, ',', '.') }} AOA</p>
        </div>
        <div class="glass-card rounded-2xl p-4">
            <p class="text-xs font-semibold" style="color: var(--text-muted)">Descontos</p>
            <p class="text-2xl font-bold mt-1 {{ (float)$run->total_deductions > 0 ? 'text-red-500' : '' }}">{{ number_format((float)$run->total_deductions, 2, ',', '.') }} AOA</p>
        </div>
        <div class="glass-card rounded-2xl p-4">
            <p class="text-xs font-semibold" style="color: var(--text-muted)">Total Líquido</p>
            <p class="text-2xl font-bold mt-1 text-emerald-600">{{ number_format((float)$run->total_net, 2, ',', '.') }} AOA</p>
        </div>
    </div>

    <div class="table-container">
        <div class="px-6 py-4 border-b flex items-center justify-between" style="border-color: var(--border-color)">
            <h3 class="text-sm font-semibold" style="color: var(--text-main)">Recibos de Vencimento</h3>
            <a href="/rh/payroll" class="btn-secondary px-3 py-1.5 text-xs">← Voltar</a>
        </div>

        <div class="overflow-x-auto">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Funcionário</th>
                        <th>Cargo</th>
                        <th>Salário Base</th>
                        <th>Horas Extra</th>
                        <th>V. H. Extra</th>
                        <th>Faltas</th>
                        <th>Desc. Faltas</th>
                        <th>SS (3%)</th>
                        <th>IRT</th>
                        <th class="text-right">Líquido</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payslips as $payslip)
                    <tr>
                        <td>
                            <span class="font-medium" style="color: var(--text-main)">{{ $payslip->employee->name ?? '—' }}</span>
                        </td>
                        <td style="color: var(--text-muted)">{{ $payslip->employee->position->name ?? '—' }}</td>
                        <td style="color: var(--text-muted)">{{ number_format((float)$payslip->base_salary, 2, ',', '.') }}</td>
                        <td>
                            @if((float)$payslip->overtime_hours > 0)
                            <span class="text-amber-600">{{ number_format((float)$payslip->overtime_hours, 2) }} h</span>
                            @else
                            <span style="color: var(--text-muted)">—</span>
                            @endif
                        </td>
                        <td style="color: var(--text-muted)">{{ number_format((float)$payslip->overtime_amount, 2, ',', '.') }}</td>
                        <td>
                            @if((float)$payslip->absent_days > 0)
                            <span class="text-red-500">{{ number_format((float)$payslip->absent_days, 2) }}</span>
                            @else
                            <span style="color: var(--text-muted)">—</span>
                            @endif
                        </td>
                        <td style="color: var(--text-muted)">{{ (float)$payslip->absent_deduction > 0 ? '-' . number_format((float)$payslip->absent_deduction, 2, ',', '.') : '—' }}</td>
                        <td style="color: var(--text-muted)">{{ (float)$payslip->social_security > 0 ? '-' . number_format((float)$payslip->social_security, 2, ',', '.') : '—' }}</td>
                        <td style="color: var(--text-muted)">{{ (float)$payslip->irt_amount > 0 ? '-' . number_format((float)$payslip->irt_amount, 2, ',', '.') : '—' }}</td>
                        <td class="text-right">
                            <span class="font-bold text-emerald-600">{{ number_format((float)$payslip->net_salary, 2, ',', '.') }}</span>
                        </td>
                        <td class="text-right">
                            <button type="button" @click="open=true; reciboUrl='/rh/payroll/payslip/{{ $payslip->id }}/recibo'" title="Recibo (PDF)" class="p-2 rounded-lg transition-all duration-200" style="color: var(--text-muted)" onmouseover="this.style.color='var(--accent)'; this.style.backgroundColor='var(--accent-soft)'" onmouseout="this.style.color='var(--text-muted)'; this.style.backgroundColor='transparent'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-10 h-10" style="color: var(--border-color)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                <p style="color: var(--text-muted)">Nenhum recibo gerado nesta folha</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de visualização de recibo -->
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
        <div class="glass-card rounded-2xl p-0 w-full max-w-4xl flex flex-col max-h-[85vh] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 border-b" style="border-color: var(--border-color)">
                <div class="flex items-center gap-2 min-w-0">
                    <i data-lucide="file-text" class="w-4 h-4 flex-shrink-0" style="color: var(--accent)"></i>
                    <h3 class="text-sm font-semibold truncate" style="color: var(--text-main)">Recibo de Vencimento</h3>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    <a :href="reciboUrl" target="_blank" class="flex items-center gap-1 text-xs font-semibold" style="color: var(--accent)">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Abrir em nova aba
                    </a>
                    <button type="button" @click="open=false" class="modal-close p-1.5 rounded-lg transition-colors" style="color: var(--text-muted)" title="Fechar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <div class="flex-1 bg-black/25">
                <iframe :src="reciboUrl" class="w-full h-[60vh] md:h-[68vh]" style="border:none"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection