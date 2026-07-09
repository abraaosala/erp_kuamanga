@extends('layout.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Visão geral do sistema')

@section('content')
<div class="space-y-6" x-ignore>
    @if(!empty($success))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $success }}
    </div>
    @endif

    <!-- Welcome -->
    <div class="relative overflow-hidden rounded-2xl p-6 border" style="background: linear-gradient(135deg, var(--accent-soft) 0%, rgba(79, 70, 229, 0.03) 100%); border-color: rgba(124, 58, 237, 0.1)">
        <div class="absolute top-0 right-0 w-48 h-48 rounded-full blur-3xl opacity-10" style="background-color: var(--accent)"></div>
        <div class="relative flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-violet-500/20">
                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-lg font-bold" style="color: var(--text-main)">Bem-vindo, {{ $user->name ?? 'Usuário' }}!</h3>
                <p class="text-sm" style="color: var(--text-muted)">{{ ucfirst((new IntlDateFormatter('pt_BR', IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, "EEEE, dd 'de' MMMM 'de' yyyy"))->format(new DateTime())) }}</p>
            </div>
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium" style="background-color: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-muted)">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Sistema operacional
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card group cursor-default">
            <div class="flex items-start justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-muted)">Usuários</span>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform duration-200 group-hover:scale-110" style="background-color: var(--accent-soft)">
                    <svg class="w-4 h-4" style="color: var(--accent)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold" style="color: var(--text-main)">—</p>
            <p class="text-xs mt-1" style="color: var(--text-muted)">Total registados</p>
        </div>

        <div class="stat-card group cursor-default">
            <div class="flex items-start justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-muted)">Módulos</span>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform duration-200 group-hover:scale-110" style="background-color: rgba(99,102,241,0.1)">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold" style="color: var(--text-main)">2</p>
            <p class="text-xs mt-1" style="color: var(--text-muted)">Auth + Contabilidade</p>
        </div>

        <div class="stat-card group cursor-default">
            <div class="flex items-start justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-muted)">Empresas</span>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform duration-200 group-hover:scale-110" style="background-color: rgba(16,185,129,0.1)">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold" style="color: var(--text-main)">{{ all_empresas()->count() }}</p>
            <p class="text-xs mt-1" style="color: var(--text-muted)">Cadastradas</p>
        </div>

        <div class="stat-card group cursor-default">
            <div class="flex items-start justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-muted)">PHP</span>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-transform duration-200 group-hover:scale-110" style="background-color: rgba(59,130,246,0.1)">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold" style="color: var(--text-main)">{{ PHP_MAJOR_VERSION }}.{{ PHP_MINOR_VERSION }}</p>
            <p class="text-xs mt-1" style="color: var(--text-muted)">Runtime</p>
        </div>
    </div>

    <!-- Charts row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold" style="color: var(--text-main)">Movimento Mensal</h3>
                <span class="text-xs" style="color: var(--text-muted)">Lançamentos contabéis</span>
            </div>
            <canvas id="chartLine" height="180" class="w-full"></canvas>
        </div>

        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold" style="color: var(--text-main)">Distribuição</h3>
                <span class="text-xs" style="color: var(--text-muted)">Por módulo</span>
            </div>
            <canvas id="chartDoughnut" height="200" class="w-full"></canvas>
        </div>
    </div>

    <!-- Quick Access + Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Quick Access -->
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Acesso Rápido</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="/users" class="flex flex-col items-center gap-2 p-4 rounded-xl transition-all duration-200 group hover:scale-[1.02]" style="background-color: var(--bg-main); border: 1px solid var(--border-color)">
                    <svg class="w-6 h-6 transition-transform duration-200 group-hover:scale-110" style="color: var(--accent)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="text-xs font-medium" style="color: var(--text-muted)">Usuários</span>
                </a>
                <a href="/users/create" class="flex flex-col items-center gap-2 p-4 rounded-xl transition-all duration-200 group hover:scale-[1.02]" style="background-color: var(--bg-main); border: 1px solid var(--border-color)">
                    <svg class="w-6 h-6 transition-transform duration-200 group-hover:scale-110" style="color: #6366f1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span class="text-xs font-medium" style="color: var(--text-muted)">Novo Usuário</span>
                </a>
                <a href="/accounting/accounts" class="flex flex-col items-center gap-2 p-4 rounded-xl transition-all duration-200 group hover:scale-[1.02]" style="background-color: var(--bg-main); border: 1px solid var(--border-color)">
                    <svg class="w-6 h-6 transition-transform duration-200 group-hover:scale-110" style="color: #f59e0b" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-xs font-medium" style="color: var(--text-muted)">Plano Contas</span>
                </a>
                <a href="/accounting/journal" class="flex flex-col items-center gap-2 p-4 rounded-xl transition-all duration-200 group hover:scale-[1.02]" style="background-color: var(--bg-main); border: 1px solid var(--border-color)">
                    <svg class="w-6 h-6 transition-transform duration-200 group-hover:scale-110" style="color: #10b981" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span class="text-xs font-medium" style="color: var(--text-muted)">Lançamentos</span>
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Atividade Recente</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3 py-2 border-b" style="border-color: var(--border-color)">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></div>
                    <p class="text-xs flex-1" style="color: var(--text-muted)">Sistema operacional</p>
                    <span class="text-[10px]" style="color: var(--text-muted)">Agora</span>
                </div>
                <div class="flex items-center gap-3 py-2 border-b" style="border-color: var(--border-color)">
                    <div class="w-2 h-2 rounded-full bg-violet-500 flex-shrink-0"></div>
                    <p class="text-xs flex-1" style="color: var(--text-muted)">Módulo de Contabilidade ativo</p>
                    <span class="text-[10px]" style="color: var(--text-muted)">Hoje</span>
                </div>
                <div class="flex items-center gap-3 py-2 border-b" style="border-color: var(--border-color)">
                    <div class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0"></div>
                    <p class="text-xs flex-1" style="color: var(--text-muted)">Login efetuado</p>
                    <span class="text-[10px]" style="color: var(--text-muted)">Hoje</span>
                </div>
                <div class="flex items-center gap-3 py-2">
                    <div class="w-2 h-2 rounded-full bg-amber-500 flex-shrink-0"></div>
                    <p class="text-xs flex-1" style="color: var(--text-muted)">ERP Kuamanga iniciado</p>
                    <span class="text-[10px]" style="color: var(--text-muted)">—</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bar chart (full width) -->
    <div class="glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold" style="color: var(--text-main)">Comparativo Mensal</h3>
            <span class="text-xs" style="color: var(--text-muted)">Receitas vs Despesas</span>
        </div>
        <canvas id="chartBar" height="120" class="w-full"></canvas>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const isDark = document.documentElement.classList.contains('dark');
    const grid = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
    const textColor = isDark ? '#9ca3af' : '#64748b';
    const violet = '#7c3aed';
    const indigo = '#4f46e5';
    const emerald = '#10b981';
    const amber = '#f59e0b';

    // Line Chart
    const ctxLine = document.getElementById('chartLine');
    if (ctxLine) {
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                datasets: [{
                    label: 'Lançamentos',
                    data: [12, 19, 15, 22, 18, 25, 20, 28, 24, 30, 26, 32],
                    borderColor: violet,
                    backgroundColor: violet + '15',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: violet,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: grid }, ticks: { color: textColor, font: { size: 10 } } },
                    y: { grid: { color: grid }, ticks: { color: textColor, font: { size: 10 }, stepSize: 5 }, beginAtZero: true }
                }
            }
        });
    }

    // Doughnut Chart
    const ctxDoughnut = document.getElementById('chartDoughnut');
    if (ctxDoughnut) {
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Auth', 'Contabilidade'],
                datasets: [{
                    data: [30, 70],
                    backgroundColor: [violet, indigo],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: textColor, font: { size: 11 }, padding: 12, usePointStyle: true }
                    }
                }
            }
        });
    }

    // Bar Chart
    const ctxBar = document.getElementById('chartBar');
    if (ctxBar) {
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                datasets: [
                    { label: 'Receitas', data: [450, 520, 480, 610, 550, 680], backgroundColor: emerald + '80', borderRadius: 4 },
                    { label: 'Despesas', data: [320, 380, 340, 420, 390, 450], backgroundColor: amber + '80', borderRadius: 4 }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top', labels: { color: textColor, font: { size: 11 }, usePointStyle: true, padding: 12 } }
                },
                scales: {
                    x: { grid: { color: grid }, ticks: { color: textColor, font: { size: 10 } } },
                    y: { grid: { color: grid }, ticks: { color: textColor, font: { size: 10 } }, beginAtZero: true }
                }
            }
        });
    }
});
</script>
@endsection
