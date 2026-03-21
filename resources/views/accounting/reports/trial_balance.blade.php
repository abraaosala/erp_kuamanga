@extends('layout.app')

@section('title', 'Balancetes')
@section('page-title', 'Balancetes')
@section('page-subtitle', 'Resumo de saldos por conta')

@section('content')
<div class="space-y-6">

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form action="/accounting/reports/trial-balance" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/3">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Data Inicial</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-4 py-2.5 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm">
            </div>
            <div class="w-full md:w-1/3">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Data Final</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-4 py-2.5 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm">
            </div>
            <div class="w-full md:w-1/3">
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-indigo-200 dark:shadow-none">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Filtrar Período
                </button>
            </div>
        </form>
    </div>

    <!-- Tabela do Balancete -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden relative">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Conta</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Descrição da Conta</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Movimento Débito</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Movimento Crédito</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Saldo Débito</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Saldo Crédito</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @if(count($report['items']) > 0)
                        @foreach($report['items'] as $item)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-4 text-sm font-bold text-gray-800 dark:text-gray-200">
                                    {{ $item['account']->code }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $item['account']->name }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-gray-600 dark:text-gray-300">
                                    {{ number_format($item['total_debit'], 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-gray-600 dark:text-gray-300">
                                    {{ number_format($item['total_credit'], 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-semibold {{ $item['final_debit'] > 0 ? 'text-gray-800 dark:text-gray-200' : 'text-gray-300 dark:text-gray-600' }}">
                                    {{ $item['final_debit'] > 0 ? number_format($item['final_debit'], 2, ',', '.') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-semibold {{ $item['final_credit'] > 0 ? 'text-gray-800 dark:text-gray-200' : 'text-gray-300 dark:text-gray-600' }}">
                                    {{ $item['final_credit'] > 0 ? number_format($item['final_credit'], 2, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                Nenhuma conta com movimentos no período selecionado.
                            </td>
                        </tr>
                    @endif
                </tbody>
                @if(count($report['items']) > 0)
                    <tfoot class="bg-gray-50 dark:bg-gray-900/50 border-t-2 border-gray-200 dark:border-gray-700">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-right text-sm font-bold text-gray-600 dark:text-gray-300 uppercase tracking-widest">
                                Totais Finais
                            </td>
                            <td colspan="2" class="px-6 py-4"></td>
                            <td class="px-6 py-4 text-right text-base font-black text-indigo-600 dark:text-indigo-400">
                                {{ number_format($report['total_debit'], 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right text-base font-black text-indigo-600 dark:text-indigo-400 gap-2">
                                {{ number_format($report['total_credit'], 2, ',', '.') }}
                                @if(round($report['total_debit'], 2) === round($report['total_credit'], 2))
                                    <div class="inline-flex mt-1 text-[10px] items-center text-green-500 bg-green-50 dark:bg-green-900/20 px-2 py-0.5 rounded-md">
                                        <i data-lucide="check" class="w-3 h-3 mr-1"></i> Balanceado
                                    </div>
                                @else
                                    <div class="inline-flex mt-1 text-[10px] items-center text-red-500 bg-red-50 dark:bg-red-900/20 px-2 py-0.5 rounded-md">
                                        <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i> Diferença!
                                    </div>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

</div>
@endsection
