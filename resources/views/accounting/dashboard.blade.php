@extends('layout.app')

@section('title', 'Dashboard de Contabilidade')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Visão geral da saúde financeira da empresa')

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection

@section('content')
<div class="space-y-6" x-data="dashboardData()">

    <!-- Header / Filters -->
    <div class="flex flex-col md:flex-row justify-between items-center bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm gap-4">
        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
            <i data-lucide="bar-chart-2" class="w-5 h-5 text-indigo-500"></i>
            Métricas Financeiras
        </h2>
        
        <form action="/accounting/dashboard" method="GET" class="flex gap-2 items-center">
            @php
                $meses = [1=>'Janeiro', 2=>'Fevereiro', 3=>'Março', 4=>'Abril', 5=>'Maio', 6=>'Junho', 7=>'Julho', 8=>'Agosto', 9=>'Setembro', 10=>'Outubro', 11=>'Novembro', 12=>'Dezembro'];
            @endphp
            <select name="month" class="px-3 py-1.5 rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm font-medium">
                @foreach($meses as $num => $nome)
                    <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>
                        {{ $nome }}
                    </option>
                @endforeach
            </select>
            <select name="year" class="px-3 py-1.5 rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm font-medium">
                @for($y = date('Y') - 5; $y <= date('Y') + 1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 dark:bg-indigo-900/40 dark:text-indigo-400 rounded-lg transition-colors">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
            </button>
        </form>
    </div>

    <!-- KPI Cards (YTD & Monthly) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Receitas do Mês -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 transform translate-x-4 -translate-y-4 group-hover:scale-110 transition-transform">
                <i data-lucide="trending-up" class="w-24 h-24 text-green-500"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-green-50 dark:bg-green-900/40 flex items-center justify-center text-green-600 dark:text-green-400">
                    <i data-lucide="arrow-up-right" class="w-5 h-5"></i>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">Este Mês</span>
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Proveitos (Receitas)</h3>
                <div class="text-3xl font-black text-gray-800 dark:text-white">
                    {{ number_format($metrics['revenue_month'], 2, ',', '.') }}
                    <span class="text-sm font-medium text-gray-400 ml-1">AOA</span>
                </div>
            </div>
        </div>

        <!-- Despesas do Mês -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 transform translate-x-4 -translate-y-4 group-hover:scale-110 transition-transform">
                <i data-lucide="trending-down" class="w-24 h-24 text-red-500"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/40 flex items-center justify-center text-red-600 dark:text-red-400">
                    <i data-lucide="arrow-down-right" class="w-5 h-5"></i>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">Este Mês</span>
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Custos (Despesas)</h3>
                <div class="text-3xl font-black text-gray-800 dark:text-white">
                    {{ number_format($metrics['expenses_month'], 2, ',', '.') }}
                    <span class="text-sm font-medium text-gray-400 ml-1">AOA</span>
                </div>
            </div>
        </div>

        <!-- Resultado Líquido -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group lg:col-span-2">
            @php $isProfit = $metrics['net_income_month'] >= 0; @endphp
            <div class="absolute top-0 right-0 p-4 opacity-5 transform translate-x-4 -translate-y-4 group-hover:scale-110 transition-transform">
                <i data-lucide="wallet" class="w-32 h-32 {{ $isProfit ? 'text-indigo-500' : 'text-orange-500' }}"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl {{ $isProfit ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400' : 'bg-orange-50 dark:bg-orange-900/40 text-orange-600 dark:text-orange-400' }} flex items-center justify-center">
                    <i data-lucide="activity" class="w-5 h-5"></i>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Resultado Mensal</span>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $isProfit ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300' }}">
                        {{ $isProfit ? 'LUCRO' : 'PREJUÍZO' }}
                    </span>
                </div>
            </div>
            <div class="relative z-10 flex items-end justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1">Resultado Líquido Exercício</h3>
                    <div class="text-4xl font-black {{ $isProfit ? 'text-indigo-600 dark:text-indigo-400' : 'text-orange-600 dark:text-orange-400' }}">
                        {{ number_format(abs($metrics['net_income_month']), 2, ',', '.') }}
                        <span class="text-sm font-medium opacity-60 ml-1">AOA</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Total Activos (Acumulado) -->
        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 dark:bg-blue-900/30 flex items-center justify-center">
                    <i data-lucide="building" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-0.5">Total Activos (YTD)</p>
                    <p class="text-lg font-black text-gray-800 dark:text-gray-100">{{ number_format($metrics['total_assets'], 2, ',', '.') }} AOA</p>
                </div>
            </div>
        </div>
        <!-- Total Passivos (Acumulado) -->
        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-500 dark:bg-rose-900/30 flex items-center justify-center">
                    <i data-lucide="landmark" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-0.5">Total Passivos & C.P. (YTD)</p>
                    <p class="text-lg font-black text-gray-800 dark:text-gray-100">{{ number_format(abs($metrics['total_liabilities']), 2, ',', '.') }} AOA</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        <!-- Bar Chart (Receitas vs Despesas Anual) -->
        <div class="xl:col-span-2 bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Fluxo Anual ({{ $year }})</h3>
            </div>
            <div id="cashflowChart" class="w-full h-80"></div>
        </div>

        <!-- Donut Chart (Breakdown Despesas Mês) -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Estrutura de Custos</h3>
                <span class="text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-gray-500">Mês Selecionado</span>
            </div>
            @if(count($metrics['expense_breakdown']) > 0)
                <div id="expenseChart" class="w-full h-80 flex justify-center"></div>
            @else
                <div class="w-full h-80 flex flex-col items-center justify-center text-gray-400">
                    <i data-lucide="pie-chart" class="w-16 h-16 mb-4 opacity-20"></i>
                    <p class="text-sm font-medium">Sem despesas registadas.</p>
                </div>
            @endif
        </div>

    </div>

</div>

<!-- Alpine Chart Configs -->
<script>
function dashboardData() {
    return {
        init() {
            this.initCashflowChart();
            this.initExpenseChart();
        },
        initCashflowChart() {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#9ca3af' : '#6b7280';
            
            // Convert to JS arrays
            const monthlyRevenue = [{{ implode(',', $metrics['monthly_revenue']) }}];
            const monthlyExpenses = [{{ implode(',', $metrics['monthly_expenses']) }}];

            const options = {
                series: [{
                    name: 'Receitas (Proveitos)',
                    data: monthlyRevenue
                }, {
                    name: 'Despesas (Custos)',
                    data: monthlyExpenses
                }],
                chart: {
                    type: 'bar',
                    height: 320,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '50%',
                        borderRadius: 4
                    },
                },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: {
                    categories: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                    labels: { style: { colors: textColor } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: { style: { colors: textColor } }
                },
                colors: ['#22c55e', '#ef4444'], // Tailwind green-500 and red-500
                fill: { opacity: 1 },
                grid: {
                    borderColor: isDark ? '#374151' : '#f3f4f6',
                    strokeDashArray: 4,
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: { colors: textColor }
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light',
                    y: {
                        formatter: function (val) {
                            return val.toLocaleString('pt-AO', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + " AOA"
                        }
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#cashflowChart"), options);
            chart.render();
        },
        initExpenseChart() {
            const chartDiv = document.querySelector("#expenseChart");
            if(!chartDiv) return;

            const isDark = document.documentElement.classList.contains('dark');
            const data = {!! json_encode($metrics['expense_breakdown']) !!};
            
            const series = data.map(item => item.value);
            const labels = data.map(item => item.name);

            const options = {
                series: series,
                chart: {
                    type: 'donut',
                    height: 320,
                    fontFamily: 'inherit'
                },
                labels: labels,
                colors: ['#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f97316', '#eab308'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: { show: true },
                                value: {
                                    show: true,
                                    color: isDark ? '#f3f4f6' : '#111827',
                                    formatter: function (val) {
                                        return val.toLocaleString('pt-AO', {minimumFractionDigits: 2}) + " AOA"
                                    }
                                },
                                total: {
                                    show: true,
                                    showAlways: true,
                                    label: 'Total Custos',
                                    color: isDark ? '#9ca3af' : '#6b7280',
                                    formatter: function (w) {
                                        const total = w.globals.seriesTotals.reduce((a, b) => { return a + b }, 0);
                                        return total.toLocaleString('pt-AO', {maximumFractionDigits: 0}) + " AOA"
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: { enabled: false },
                stroke: { width: 0 },
                legend: {
                    position: 'bottom',
                    labels: { colors: isDark ? '#9ca3af' : '#6b7280' }
                },
                tooltip: { theme: isDark ? 'dark' : 'light' }
            };

            const chart = new ApexCharts(chartDiv, options);
            chart.render();
        }
    }
}
</script>
@endsection
