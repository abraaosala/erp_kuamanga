@extends('layout.app')

@section('title', 'Razão Geral')
@section('page-title', 'Razão Geral')
@section('page-subtitle', 'Extrato detalhado de movimentos por conta')

@section('content')
<div class="space-y-6">

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form action="/accounting/reports/ledger" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/3">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Conta</label>
                <select name="account_id" class="w-full px-4 py-2.5 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm">
                    <option value="">Todas as Contas</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ $selectedAccountId == $acc->id ? 'selected' : '' }}>
                            {{ $acc->code }} - {{ $acc->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-1/4">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Data Inicial</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-4 py-2.5 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm">
            </div>
            <div class="w-full md:w-1/4">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Data Final</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-4 py-2.5 rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm">
            </div>
            <div class="w-full md:w-1/6">
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-indigo-200 dark:shadow-none">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    @if($selectedAccount)
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                <i data-lucide="file-text" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">{{ $selectedAccount->code }} - {{ $selectedAccount->name }}</h3>
                <p class="text-xs text-gray-500">
                    Extracto de movimentos {{ $startDate ? 'desde ' . date('d/m/Y', strtotime($startDate)) : '' }} {{ $endDate ? 'até ' . date('d/m/Y', strtotime($endDate)) : '' }}
                </p>
            </div>
        </div>
    @endif

    <!-- Tabela do Razão -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Conta</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Lançamento / Descrição</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Débito</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Crédito</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Saldo Acumulado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @if(count($ledger) > 0)
                        @foreach($ledger as $item)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors group">
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    {{ date('d/m/Y', strtotime($item->date)) }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $item->account->code }}</span>
                                        <span class="text-[10px] text-gray-400 truncate max-w-[150px]">{{ $item->account->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-600 dark:text-gray-300">{{ $item->entry->description }}</span>
                                        <span class="text-[10px] text-indigo-500 mt-0.5">#{{ str_pad($item->entry->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    @if($item->type === 'debit')
                                        <span class="text-gray-800 dark:text-gray-200">{{ number_format((float)$item->amount, 2, ',', '.') }}</span>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    @if($item->type === 'credit')
                                        <span class="text-gray-800 dark:text-gray-200">{{ number_format((float)$item->amount, 2, ',', '.') }}</span>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @php
                                        $balanceClass = '';
                                        if ($item->running_balance > 0) $balanceClass = 'text-green-600 dark:text-green-400';
                                        elseif ($item->running_balance < 0) $balanceClass = 'text-red-600 dark:text-red-400';
                                        else $balanceClass = 'text-gray-500 dark:text-gray-400';
                                        
                                        $nature = $item->running_balance > 0 ? 'D' : ($item->running_balance < 0 ? 'C' : '');
                                    @endphp
                                    <span class="text-sm font-bold {{ $balanceClass }}">
                                        {{ number_format(abs($item->running_balance), 2, ',', '.') }} {{ $nature }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-lucide="inbox" class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-3"></i>
                                    <p class="text-sm">Nenhum movimento encontrado para os filtros selecionados.</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
