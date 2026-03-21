@extends('layout.app')

@section('title', 'Demonstração de Resultados')
@section('page-title', 'Demonstração de Resultados (DRE)')
@section('page-subtitle', 'Demonstrativo oficial de fluxos e viabilidade do período')

@section('content')
<div class="space-y-6">

    <!-- Filters & Actions -->
    <div class="flex flex-col md:flex-row justify-between items-center bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm gap-4 print:hidden">
        <form action="/accounting/reports/income-statement" method="GET" class="flex flex-col md:flex-row gap-4 w-full md:w-auto items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Data Início</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-indigo-500 outline-none w-full md:w-auto">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Data Fim</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-indigo-500 outline-none w-full md:w-auto">
            </div>
            <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                Filtrar Resultados
            </button>
        </form>

        <button onclick="window.print()" class="px-4 py-2 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center gap-2">
            <i data-lucide="printer" class="w-4 h-4"></i> Imprimir / Exportar PDF
        </button>
    </div>

    <!-- Printable Report Container -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 shadow-sm print:shadow-none print:border-none print:p-0 max-w-4xl mx-auto">
        
        <!-- Document Header -->
        <div class="text-center mb-10 pb-6 border-b border-gray-200 dark:border-gray-700 pt-4">
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight uppercase">Demonstração de Resultados</h1>
            <h2 class="text-lg font-semibold text-gray-600 dark:text-gray-300 mt-1 uppercase">Por Natureza</h2>
            <p class="text-sm font-medium text-gray-500 mt-2">Período de <span class="text-gray-800 dark:text-gray-200 font-bold">{{ date('d/m/Y', strtotime($startDate)) }}</span> a <span class="text-gray-800 dark:text-gray-200 font-bold">{{ date('d/m/Y', strtotime($endDate)) }}</span></p>
            <p class="text-xs text-gray-400 mt-1">Saldos expressos em Moeda Nacional (AOA)</p>
        </div>

        <div class="space-y-8">
            
            <!-- REVENUES (PROVEITOS E GANHOS) -->
            <div>
                <h2 class="text-md font-bold text-gray-800 dark:text-gray-200 uppercase tracking-widest bg-gray-100 dark:bg-gray-900 py-2 px-3 rounded text-center">Proveitos e Ganhos (Classe 7)</h2>
                <div class="mt-4">
                    <table class="w-full text-sm">
                        <tbody>
                            @if(count($data['revenues']) === 0)
                                <tr><td class="py-2 text-gray-400 italic text-center">Não existem proveitos registados (Faturação Vazia).</td></tr>
                            @else
                                @foreach($data['revenues'] as $rev)
                                    <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                        <td class="py-3 font-medium text-gray-600 dark:text-gray-300">{{ $rev['name'] }}</td>
                                        <td class="py-3 text-right text-gray-900 dark:text-white font-semibold">{{ number_format($rev['balance'], 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="py-3 font-bold text-gray-700 dark:text-gray-300 text-right pr-4 uppercase text-xs">Total de Proveitos Operacionais (+)</td>
                                <td class="py-3 text-right font-black text-gray-900 dark:text-white border-t-2 border-gray-800 dark:border-gray-400">
                                    {{ number_format($data['total_revenues'], 2, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- EXPENSES (CUSTOS E PERDAS) -->
            <div>
                <h2 class="text-md font-bold text-gray-800 dark:text-gray-200 uppercase tracking-widest bg-gray-100 dark:bg-gray-900 py-2 px-3 rounded text-center">Custos e Perdas (Classe 6)</h2>
                <div class="mt-4">
                    <table class="w-full text-sm">
                        <tbody>
                            @if(count($data['expenses']) === 0)
                                <tr><td class="py-2 text-gray-400 italic text-center">Não existem custos registados neste período.</td></tr>
                            @else
                                @foreach($data['expenses'] as $exp)
                                    <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                        <td class="py-3 font-medium text-gray-600 dark:text-gray-300 pl-4">{{ $exp['name'] }}</td>
                                        <td class="py-3 text-right text-gray-900 dark:text-white font-semibold">{{ number_format($exp['balance'], 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="py-3 font-bold text-gray-700 dark:text-gray-300 text-right pr-4 uppercase text-xs">Total de Custos Operacionais (-)</td>
                                <td class="py-3 text-right font-black text-gray-900 dark:text-white border-t-2 border-gray-800 dark:border-gray-400">
                                    {{ number_format($data['total_expenses'], 2, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- RESULTADO LIQUIDO -->
            <div class="mt-8 pt-6 border-double border-t-4 border-gray-300 dark:border-gray-600">
                @php $isProfit = $data['net_income'] >= 0; @endphp
                <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-900/50 p-6 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="font-black text-xl text-gray-800 dark:text-gray-100 uppercase tracking-tight">Resultado Líquido do Exercício</h3>
                        <p class="text-sm font-medium text-gray-500 mt-1">Lucro / Prejuízo apurado antes de impostos (RAI)</p>
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-black {{ $isProfit ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ number_format($data['net_income'], 2, ',', '.') }}
                        </span>
                        <span class="text-sm font-bold ml-1 text-gray-500">AOA</span>
                    </div>
                </div>
            </div>

            <!-- Assinaturas -->
            <div class="mt-20 pt-8 grid grid-cols-2 gap-12 text-center text-sm font-medium text-gray-800 dark:text-gray-200 print:grid">
                <div>
                    <div class="border-t border-gray-400 dark:border-gray-600 w-3/4 mx-auto pt-2">
                        O(A) Contabilista Executante
                    </div>
                </div>
                <div>
                    <div class="border-t border-gray-400 dark:border-gray-600 w-3/4 mx-auto pt-2">
                        Administração / Gerência
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
