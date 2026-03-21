@extends('layout.app')

@section('title', 'Lançamentos Diários')
@section('page-title', 'Contabilidade')
@section('page-subtitle', 'Lançamentos Diários')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Lançamentos</h3>
        <a href="/accounting/journal/create" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-indigo-200 dark:shadow-none">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Novo Lançamento</span>
        </a>
    </div>
    @if(count($entries) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">ID / Data</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Descrição / Ref</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Detalhes do Lançamento</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Valor Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($entries as $entry)
                        <tr class="group hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-all duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">#{{ str_pad((string)$entry->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ date('d/m/Y', strtotime($entry->date)) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $entry->description }}</span>
                                    @if($entry->reference)
                                        <span class="text-[10px] text-gray-400 font-medium uppercase">{{ $entry->reference }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    @foreach($entry->items as $item)
                                        <div class="flex items-center gap-2 text-[11px]">
                                            <span class="w-2 h-2 rounded-full {{ $item->type === 'debit' ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                            <span class="font-mono text-gray-500 dark:text-gray-400">{{ $item->account->code }}</span>
                                            <span class="text-gray-600 dark:text-gray-300 truncate max-w-[150px]">{{ $item->account->name }}</span>
                                            <span class="ml-auto font-bold {{ $item->type === 'debit' ? 'text-green-600' : 'text-red-500' }}">
                                                {{ number_format((float)$item->amount, 2, ',', '.') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right align-top">
                                <span class="text-base font-black text-gray-900 dark:text-white">
                                    {{ number_format((float)$entry->items->where('type', 'debit')->sum('amount'), 2, ',', '.') }}
                                    <small class="text-[10px] text-gray-400 ml-1">Kz</small>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="p-12 text-center">
            <div class="w-20 h-20 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                <i data-lucide="clipboard-list" class="w-10 h-10 text-gray-300"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-400 dark:text-gray-500 mb-2">Sem Lançamentos</h4>
            <p class="text-sm text-gray-400 dark:text-gray-600 max-w-xs mx-auto">Ainda não foram registrados movimentos contabilísticos para esta empresa.</p>
        </div>
    @endif
</div>
@endsection
