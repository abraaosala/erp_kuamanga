@extends('layout.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Visão geral do sistema')

@section('content')
<div x-data="{}">
    @if(!empty($success))
    <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $success }}
    </div>
    @endif

    <!-- Welcome -->
    <div class="mb-8 p-6 rounded-2xl border transition-all duration-300" style="background: linear-gradient(135deg, var(--accent-soft) 0%, rgba(79, 70, 229, 0.05) 100%); border-color: rgba(124, 58, 237, 0.1)">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-violet-500/20">
                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
            </div>
            <div>
                <h3 class="text-xl font-bold" style="color: var(--text-main)">Bem-vindo, {{ $user->name ?? 'Usuário' }}! 👋</h3>
                <p class="text-sm" style="color: var(--text-muted)">{{ date('l, d \d\e F \d\e Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium" style="color: var(--text-muted)">Usuários</span>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background-color: var(--accent-soft)">
                    <svg class="w-4 h-4" style="color: var(--accent)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold" style="color: var(--text-main)">—</p>
            <p class="text-xs mt-1" style="color: var(--text-muted)">Total no sistema</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium" style="color: var(--text-muted)">Módulos</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold" style="color: var(--text-main)">1</p>
            <p class="text-xs mt-1" style="color: var(--text-muted)">Módulo ativo</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium" style="color: var(--text-muted)">Sistema</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold" style="color: var(--text-main)">OK</p>
            <p class="text-xs text-emerald-500 mt-1">Operacional</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium" style="color: var(--text-muted)">Versão PHP</span>
                <div class="w-9 h-9 rounded-xl bg-blue-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold" style="color: var(--text-main)">{{ PHP_MAJOR_VERSION }}.{{ PHP_MINOR_VERSION }}</p>
            <p class="text-xs mt-1" style="color: var(--text-muted)">PHP Runtime</p>
        </div>
    </div>

    <!-- Quick links -->
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Acesso Rápido</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="/users" class="flex flex-col items-center gap-2 p-4 rounded-xl transition-all duration-200 group" style="background-color: var(--bg-main); border: 1px solid var(--border-color)">
                <svg class="w-6 h-6 group-hover:scale-110 transition-transform duration-200" style="color: var(--accent)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="text-xs font-medium" style="color: var(--text-muted)">Usuários</span>
            </a>
            <a href="/users/create" class="flex flex-col items-center gap-2 p-4 rounded-xl transition-all duration-200 group" style="background-color: var(--bg-main); border: 1px solid var(--border-color)">
                <svg class="w-6 h-6 group-hover:scale-110 transition-transform duration-200" style="color: #6366f1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span class="text-xs font-medium" style="color: var(--text-muted)">Novo Usuário</span>
            </a>
        </div>
    </div>
</div>
@endsection
