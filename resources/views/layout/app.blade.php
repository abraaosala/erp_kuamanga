<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ERP Sistema') | ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="font-inter antialiased">

    <div x-data="{ 
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
        },
        searchOpen: false
    }" class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside :class="sidebarCollapsed ? 'w-20' : 'w-64'" 
               class="flex-shrink-0 flex flex-col transition-all duration-300 ease-in-out z-30 bg-indigo-950 dark:bg-[#0a0a1a] border-r border-white/5">
            
            <!-- Logo -->
            <div class="flex items-center h-16 px-4 border-b border-white/5">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-400 to-indigo-500 flex items-center justify-center shadow-lg shadow-violet-500/20 flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                        <h1 class="text-sm font-bold tracking-wide truncate text-white">ERP Kuamanga</h1>
                        <p class="text-[10px] text-indigo-300/60">v1.0.0</p>
                    </div>
                </div>
                <button @click="toggleSidebar" class="ml-auto p-1.5 rounded-lg hover:bg-white/5 text-indigo-300/50 hover:text-indigo-300 transition-colors hidden md:block">
                    <svg :class="sidebarCollapsed ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>
            </div>

            <!-- Nav -->
            <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
                <div class="px-3 pb-2">
                    <p x-show="!sidebarCollapsed" class="text-[10px] font-bold tracking-widest text-indigo-300/40 uppercase">Menu</p>
                </div>

                <a href="/dashboard" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group @if(request()->is('dashboard*')) bg-violet-500/15 text-violet-300 @else text-indigo-300/60 hover:text-white hover:bg-white/5 @endif"
                   :title="sidebarCollapsed ? 'Dashboard' : ''">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" class="text-sm font-medium whitespace-nowrap">Dashboard</span>
                </a>

                <a href="/users" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group @if(request()->is('users*')) bg-violet-500/15 text-violet-300 @else text-indigo-300/60 hover:text-white hover:bg-white/5 @endif"
                   :title="sidebarCollapsed ? 'Usuários' : ''">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" class="text-sm font-medium whitespace-nowrap">Usuários</span>
                </a>

                <!-- Contabilidade -->
                <div x-data="{ open: {{ request()->is('accounting*') ? 'true' : 'false' }} }">
                    <button @click="if(sidebarCollapsed) { sidebarCollapsed = false; open = true; } else { open = !open; }" 
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 group @if(request()->is('accounting*')) bg-violet-500/15 text-violet-300 @else text-indigo-300/60 hover:text-white hover:bg-white/5 @endif"
                            :title="sidebarCollapsed ? 'Contabilidade' : ''">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span x-show="!sidebarCollapsed" class="text-sm font-medium whitespace-nowrap">Contabilidade</span>
                        </div>
                        <svg x-show="!sidebarCollapsed" :class="open ? 'rotate-90' : ''" class="w-3.5 h-3.5 text-indigo-300/40 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>

                    <div x-show="open && !sidebarCollapsed" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-1 pl-11 space-y-0.5">
                        <a href="/accounting/dashboard" class="block py-1.5 text-xs font-medium transition-colors @if(request()->is('accounting/dashboard')) text-violet-300 @else text-indigo-300/50 hover:text-indigo-300 @endif">Dashboard</a>
                        <a href="/accounting/accounts" class="block py-1.5 text-xs font-medium transition-colors @if(request()->is('accounting/accounts*')) text-violet-300 @else text-indigo-300/50 hover:text-indigo-300 @endif">Plano de Contas</a>
                        <a href="/accounting/journal" class="block py-1.5 text-xs font-medium transition-colors @if(request()->is('accounting/journal*')) text-violet-300 @else text-indigo-300/50 hover:text-indigo-300 @endif">Lançamentos Diários</a>
                        <a href="/accounting/reports/ledger" class="block py-1.5 text-xs font-medium transition-colors @if(request()->is('accounting/reports/ledger*')) text-violet-300 @else text-indigo-300/50 hover:text-indigo-300 @endif">Razão Geral</a>
                        <a href="/accounting/reports/trial-balance" class="block py-1.5 text-xs font-medium transition-colors @if(request()->is('accounting/reports/trial-balance*')) text-violet-300 @else text-indigo-300/50 hover:text-indigo-300 @endif">Balancetes</a>
                        <div class="pt-2 pb-0.5">
                            <span class="text-[9px] font-bold tracking-widest text-indigo-300/30 uppercase">Mapas Oficiais</span>
                        </div>
                        <a href="/accounting/reports/balance-sheet" class="block py-1.5 text-xs font-medium transition-colors @if(request()->is('accounting/reports/balance-sheet*')) text-violet-300 @else text-indigo-300/50 hover:text-indigo-300 @endif">Balanço Patrimonial</a>
                        <a href="/accounting/reports/income-statement" class="block py-1.5 text-xs font-medium transition-colors @if(request()->is('accounting/reports/income-statement*')) text-violet-300 @else text-indigo-300/50 hover:text-indigo-300 @endif">DRE</a>
                    </div>
                </div>

                <!-- RH -->
                <div x-data="{ open: {{ request()->is('rh*') ? 'true' : 'false' }} }">
                    <button @click="if(sidebarCollapsed) { sidebarCollapsed = false; open = true; } else { open = !open; }" 
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 group @if(request()->is('rh*')) bg-violet-500/15 text-violet-300 @else text-indigo-300/60 hover:text-white hover:bg-white/5 @endif"
                            :title="sidebarCollapsed ? 'RH' : ''">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span x-show="!sidebarCollapsed" class="text-sm font-medium whitespace-nowrap">RH</span>
                        </div>
                        <svg x-show="!sidebarCollapsed" :class="open ? 'rotate-90' : ''" class="w-3.5 h-3.5 text-indigo-300/40 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>

                    <div x-show="open && !sidebarCollapsed" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-1 pl-11 space-y-0.5">
                        <a href="/rh/employees" class="block py-1.5 text-xs font-medium transition-colors @if(request()->is('rh/employees*')) text-violet-300 @else text-indigo-300/50 hover:text-indigo-300 @endif">Funcionários</a>
                        <a href="/rh/contracts" class="block py-1.5 text-xs font-medium transition-colors @if(request()->is('rh/contracts*')) text-violet-300 @else text-indigo-300/50 hover:text-indigo-300 @endif">Contratos</a>
                        <a href="/rh/attendance" class="block py-1.5 text-xs font-medium transition-colors @if(request()->is('rh/attendance*')) text-violet-300 @else text-indigo-300/50 hover:text-indigo-300 @endif">Frequência</a>
                        <a href="/rh/departments" class="block py-1.5 text-xs font-medium transition-colors @if(request()->is('rh/departments*')) text-violet-300 @else text-indigo-300/50 hover:text-indigo-300 @endif">Departamentos</a>
                        <a href="/rh/positions" class="block py-1.5 text-xs font-medium transition-colors @if(request()->is('rh/positions*')) text-violet-300 @else text-indigo-300/50 hover:text-indigo-300 @endif">Cargos</a>
                    </div>
                </div>

                <!-- Empresas -->
                <div x-data="{ open: {{ request()->is('companies*') ? 'true' : 'false' }} }">
                    <button @click="if(sidebarCollapsed) { sidebarCollapsed = false; open = true; } else { open = !open; }" 
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 group @if(request()->is('companies*')) bg-violet-500/15 text-violet-300 @else text-indigo-300/60 hover:text-white hover:bg-white/5 @endif"
                            :title="sidebarCollapsed ? 'Empresas' : ''">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span x-show="!sidebarCollapsed" class="text-sm font-medium whitespace-nowrap">Empresas</span>
                        </div>
                        <svg x-show="!sidebarCollapsed" :class="open ? 'rotate-90' : ''" class="w-3.5 h-3.5 text-indigo-300/40 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>

                    <div x-show="open && !sidebarCollapsed" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-1 pl-11 space-y-0.5">
                        <a href="/companies" class="block py-1.5 text-xs font-medium transition-colors @if(request()->is('companies')) text-violet-300 @else text-indigo-300/50 hover:text-indigo-300 @endif">Gerir Clientes</a>
                        <div class="pt-2 pb-0.5">
                            <span class="text-[9px] font-bold tracking-widest text-indigo-300/30 uppercase">Trabalhar com:</span>
                        </div>
                        @foreach(all_empresas() as $emp)
                            <form action="/company/switch" method="POST">
                                <button type="submit" class="w-full text-left py-1.5 text-xs font-medium transition-colors flex items-center gap-2 {{ current_empresa()->id == $emp->id ? 'text-violet-300' : 'text-indigo-300/50 hover:text-indigo-300' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ current_empresa()->id == $emp->id ? 'bg-violet-400' : 'bg-indigo-300/20' }}"></span>
                                    <span class="truncate">{{ $emp->nome }}</span>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </nav>

            <!-- User footer -->
            <div class="px-3 py-4 border-t border-white/5">
                <div class="flex items-center gap-3 px-1">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-400 to-indigo-500 flex items-center justify-center text-xs font-bold text-white flex-shrink-0 shadow-lg shadow-violet-500/10">
                        {{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}
                    </div>
                    <div x-show="!sidebarCollapsed" class="min-w-0">
                        <p class="text-xs font-medium truncate text-indigo-200">{{ session('user_name', 'Usuário') }}</p>
                        <p class="text-[10px] truncate text-indigo-300/40">{{ session('user_email', '') }}</p>
                    </div>
                </div>
                <form method="POST" action="/logout" class="mt-2">
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-all duration-200 text-indigo-300/40 hover:text-red-400 hover:bg-red-500/5" :title="sidebarCollapsed ? 'Sair' : ''">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span x-show="!sidebarCollapsed">Sair</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden relative" style="background-color: var(--bg-main)">
            <!-- Topbar -->
            <header class="flex items-center justify-between h-16 px-6 border-b z-20 flex-shrink-0" style="background-color: var(--bg-card); border-color: var(--border-color)">
                <div class="flex items-center gap-4 flex-1">
                    <button class="md:hidden p-2 rounded-lg transition-colors" style="color: var(--text-muted)" @click="sidebarCollapsed = !sidebarCollapsed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <!-- Search -->
                    <div class="hidden sm:flex items-center gap-2 px-3 py-2 rounded-xl flex-1 max-w-xs transition-all duration-200" 
                         style="background-color: var(--bg-main); border: 1px solid var(--border-color)"
                         @click="searchOpen = true">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--text-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" placeholder="Pesquisar..." class="bg-transparent text-sm outline-none w-full" style="color: var(--text-main); placeholder-color: var(--text-muted)" readonly>
                        <span class="text-[10px] font-medium px-1.5 py-0.5 rounded" style="background-color: var(--border-color); color: var(--text-muted)">⌘K</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @yield('header-actions')

                    <!-- Notifications -->
                    <button class="relative p-2 rounded-xl transition-all duration-200" style="color: var(--text-muted)" onmouseover="this.style.backgroundColor='var(--border-color)'" onmouseout="this.style.backgroundColor='transparent'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 ring-2" style="ring-color: var(--bg-card)"></span>
                    </button>

                    <!-- Theme Toggle -->
                    <div x-data="{ 
                        darkMode: localStorage.getItem('theme') === 'dark',
                        toggleTheme() {
                            this.darkMode = !this.darkMode;
                            localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
                            if (this.darkMode) document.documentElement.classList.add('dark');
                            else document.documentElement.classList.remove('dark');
                        }
                    }">
                        <button @click="toggleTheme" class="p-2 rounded-xl transition-all duration-200" style="color: var(--text-muted)" onmouseover="this.style.backgroundColor='var(--border-color)'" onmouseout="this.style.backgroundColor='transparent'">
                            <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                            <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.95 16.95l.707.707M7.05 7.05l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Avatar -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 p-1.5 rounded-xl transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-800">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-xs font-bold text-white shadow-sm">
                                {{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}
                            </div>
                            <svg class="w-3.5 h-3.5 hidden sm:block transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: var(--text-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             class="absolute right-0 top-full mt-2 w-48 rounded-xl shadow-xl border py-1.5 z-50"
                             style="background-color: var(--bg-card); border-color: var(--border-color)"
                             x-cloak>
                            <div class="px-4 py-2 border-b" style="border-color: var(--border-color)">
                                <p class="text-sm font-medium truncate" style="color: var(--text-main)">{{ session('user_name', 'Usuário') }}</p>
                                <p class="text-xs truncate" style="color: var(--text-muted)">{{ session('user_email', '') }}</p>
                            </div>
                            <a href="#" class="flex items-center gap-2 px-4 py-2 text-sm transition-colors" style="color: var(--text-muted)" onmouseover="this.style.backgroundColor='var(--border-color)'" onmouseout="this.style.backgroundColor='transparent'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Perfil
                            </a>
                            <form method="POST" action="/logout" class="border-t" style="border-color: var(--border-color)">
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm transition-colors" style="color: var(--text-muted)" onmouseover="this.style.color='#ef4444'; this.style.backgroundColor='var(--border-color)'" onmouseout="this.style.color='var(--text-muted)'; this.style.backgroundColor='transparent'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page title + Content -->
            <div class="flex-1 overflow-y-auto">
                <!-- Page header -->
                <div class="px-6 md:px-8 pt-6 pb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold" style="color: var(--text-main)">@yield('page-title', 'Dashboard')</h2>
                        <p class="text-xs font-medium" style="color: var(--text-muted)">@yield('page-subtitle', '')</p>
                    </div>
                </div>

                <div class="px-6 md:px-8 pb-8">
                    @if(session()->hasFlash('success'))
                        <div class="mb-6 p-4 rounded-xl flex items-center gap-3 text-sm" style="background-color: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: rgb(16,185,129)">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session()->getFlash('success') }}</span>
                        </div>
                    @endif

                    @if(session()->hasFlash('error'))
                        <div class="mb-6 p-4 rounded-xl flex items-center gap-3 text-sm" style="background-color: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: rgb(239,68,68)">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session()->getFlash('error') }}</span>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <script src="/js/app.js"></script>
    @yield('scripts')
</body>
</html>
