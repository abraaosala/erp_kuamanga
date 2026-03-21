@extends('layout.app')

@section('title', 'Balanço Patrimonial')
@section('page-title', 'Balanço Patrimonial')
@section('page-subtitle', 'Demonstrativo contabilístico da posição financeira')

@section('content')
<div class="space-y-6">

    <!-- Filters & Actions -->
    <div class="flex flex-col md:flex-row justify-between items-center bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm gap-4 print:hidden">
        <form action="/accounting/reports/balance-sheet" method="GET" class="flex gap-4 items-end w-full md:w-auto">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Data Fim (Posição em)</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-indigo-500 outline-none w-full md:w-auto">
            </div>
            <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                Atualizar Mapas
            </button>
        </form>

        <button onclick="window.print()" class="px-4 py-2 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center gap-2">
            <i data-lucide="printer" class="w-4 h-4"></i> Imprimir / Exportar PDF
        </button>
    </div>

    <!-- Printable Report Container -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 shadow-sm print:shadow-none print:border-none print:p-0">
        
        <!-- Document Header (Visible mainly on print) -->
        <div class="text-center mb-10 pb-6 border-b border-gray-200 dark:border-gray-700 pt-4">
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight uppercase">Balanço Patrimonial</h1>
            <p class="text-sm font-medium text-gray-500 mt-2">Posição financeira apurada em: <span class="text-gray-800 dark:text-gray-200 font-bold">{{ date('d/m/Y', strtotime($endDate)) }}</span></p>
            <p class="text-xs text-gray-400 mt-1">Valores expressos em (AOA)</p>
        </div>

        <!-- Balance Sheet Layout: 2 Columns on large screens -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            
            <!-- Column 1: ACTIVO -->
            <div>
                <h2 class="text-lg font-bold text-gray-800 dark:text-white border-b-2 border-indigo-500 pb-2 mb-4 uppercase tracking-wider">Activo</h2>
                
                @if(count($data['assets']) > 0)
                    <table class="w-full text-sm">
                        <tbody>
                            @foreach($data['assets'] as $asset)
                                <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                    <td class="py-3 font-medium text-gray-600 dark:text-gray-300">{{ $asset['name'] }}</td>
                                    <td class="py-3 text-right text-gray-900 dark:text-white font-semibold">{{ number_format($asset['balance'], 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 dark:bg-gray-900/50">
                                <td class="py-4 font-black text-gray-800 dark:text-white px-2 rounded-l-lg uppercase text-right">Total do Activo</td>
                                <td class="py-4 text-right text-lg font-black text-indigo-600 dark:text-indigo-400 px-2 rounded-r-lg">
                                    {{ number_format($data['total_assets'], 2, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                @else
                    <p class="text-gray-400 italic text-sm py-4">Sem registos movimentados no Activo.</p>
                @endif
            </div>

            <!-- Column 2: PASSIVO E CAPITAL PRÓPRIO -->
            <div>
                <h2 class="text-lg font-bold text-gray-800 dark:text-white border-b-2 border-indigo-500 pb-2 mb-4 uppercase tracking-wider">Passivo e Capital Próprio</h2>
                
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest bg-gray-50 dark:bg-gray-800 py-1.5 px-2 mb-2">Passivo (Classe 5)</h3>
                @php $hasPassivo = false; @endphp
                <table class="w-full text-sm mb-6">
                    <tbody>
                        @foreach($data['liabilities_and_equity'] as $liability)
                            @if(substr($liability['code'], 0, 1) === '5' && !in_array($liability['code'], ['51', '52', '56', '57', '58', '59'])) 
                                <!-- Simples aproximação: contas 53, 54, 55 (Passivos). Contas 51, 56+ muitas vezes são capital-->
                                <!-- The plan dictates 5 = Liability except Capital. Let's just list what AccountService sent.-->
                                @if(substr($liability['code'], 0, 2) !== '88')
                                    @php $hasPassivo = true; @endphp
                                    <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                        <td class="py-2.5 font-medium text-gray-600 dark:text-gray-300">{{ $liability['name'] }}</td>
                                        <td class="py-2.5 text-right text-gray-900 dark:text-white font-semibold">{{ number_format($liability['balance'], 2, ',', '.') }}</td>
                                    </tr>
                                @endif
                            @endif
                        @endforeach
                        @if(!$hasPassivo)
                            <tr><td colspan="2" class="py-2 text-gray-400 italic text-xs">Sem passivos registados.</td></tr>
                        @endif
                    </tbody>
                </table>

                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest bg-gray-50 dark:bg-gray-800 py-1.5 px-2 mb-2 mt-4">Capital Próprio (Resultados)</h3>
                <table class="w-full text-sm">
                    <tbody>
                        <tr class="border-b border-gray-100 dark:border-gray-700/50">
                            <td class="py-2.5 font-medium text-gray-600 dark:text-gray-300">Ganhos / Posição (Resultados Transitados)</td>
                            <td class="py-2.5 text-right text-gray-900 dark:text-white font-semibold">{{ number_format($data['total_liabilities_and_equity'] - $data['net_income'], 2, ',', '.') }}</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-700/50 bg-indigo-50/30 dark:bg-indigo-900/10">
                            <td class="py-3 font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                                Resultado Líquido do Exercício
                            </td>
                            <td class="py-3 text-right font-bold {{ $data['net_income'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ number_format($data['net_income'], 2, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 dark:bg-gray-900/50">
                            <td class="py-4 font-black text-gray-800 dark:text-white px-2 rounded-l-lg uppercase text-right">Total Passivo + C.P.</td>
                            <td class="py-4 text-right text-lg font-black text-indigo-600 dark:text-indigo-400 px-2 rounded-r-lg">
                                {{ number_format($data['total_liabilities_and_equity'], 2, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
        
        <!-- Validation Check -->
        @php 
            $diff = abs($data['total_assets'] - $data['total_liabilities_and_equity']); 
        @endphp
        
        <div class="mt-12 pt-6 border-t border-gray-200 dark:border-gray-700 text-center">
            @if($diff < 0.05)
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-full text-sm font-bold">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Balanço Equilibrado (Activo = Passivo + C.P.)
                </div>
            @else
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-full text-sm font-bold">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    Balanço Desequilibrado (Diferença: {{ number_format($diff, 2, ',', '.') }})
                </div>
                <p class="text-xs text-gray-500 mt-2">Isto pode indicar lançamentos manuais incorretos em exercícios anteriores ou sem fecho do exercício concluido.</p>
            @endif
        </div>

    </div>
</div>
@endsection
