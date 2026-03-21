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
    
    <link rel="stylesheet" href="/css/app.css">
    
    <script>
        // Early theme detection to prevent flash
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="font-inter antialiased">

    <!-- Main Container -->
    <div x-data="{ 
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
        }
    }" class="flex h-screen overflow-hidden bg-gray-50 dark:bg-gray-900">
        
        <!-- Sidebar -->
        <aside :class="sidebarCollapsed ? 'w-20' : 'w-64'" 
               class="sidebar flex-shrink-0 flex flex-col transition-all duration-300 ease-in-out border-r z-30" 
               style="border-color: var(--border-color)">
            
            <!-- Logo & Toggle -->
            <div class="flex items-center justify-between px-4 py-5 border-b" style="border-color: var(--border-color)">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-violet-500/30 flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                        <h1 class="text-sm font-bold tracking-wide truncate" style="color: var(--text-main)">ERP Sistema</h1>
                        <p class="text-[10px]" style="color: var(--text-muted)">v1.0.0</p>
                    </div>
                </div>
                <!-- Collapse Button (Desktop) -->
                <button @click="toggleSidebar" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 transition-colors hidden md:block">
                    <svg :class="sidebarCollapsed ? 'rotate-180' : ''" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>
            </div>

            <!-- Nav -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto custom-scrollbar">
                <!-- Dashboard -->
                <a href="/dashboard" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 @if(request()->is('dashboard*')) active @endif"
                   :title="sidebarCollapsed ? 'Dashboard' : ''">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm font-medium whitespace-nowrap">Dashboard</span>
                </a>

                <!-- Usuários -->
                <a href="/users" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 @if(request()->is('users*')) active @endif"
                   :title="sidebarCollapsed ? 'Usuários' : ''">
                    <i data-lucide="users" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm font-medium whitespace-nowrap">Usuários</span>
                </a>

                <!-- Contabilidade Group -->
                <div x-data="{ open: {{ request()->is('accounting*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="console.log('Contabilidade clicked', {sidebarCollapsed, open}); if(sidebarCollapsed) { sidebarCollapsed = false; open = true; } else { open = !open; }" 
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-800 group"
                            :class="{ 'bg-gray-50 dark:bg-gray-800/50': open && !sidebarCollapsed }"
                            :title="sidebarCollapsed ? 'Contabilidade' : ''">
                        <div class="flex items-center gap-3">
                            <i data-lucide="calculator" class="w-5 h-5 flex-shrink-0 text-gray-400 group-hover:text-indigo-600 transition-colors"></i>
                            <span x-show="!sidebarCollapsed" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Contabilidade</span>
                        </div>
                        <i data-lucide="chevron-right" x-show="!sidebarCollapsed" :class="open ? 'rotate-90' : ''" class="w-4 h-4 text-gray-400 transition-transform duration-200"></i>
                    </button>

                    <div x-show="open && !sidebarCollapsed" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="pl-11 pr-2 space-y-1">
                        
                        <a href="/accounting/dashboard" class="block py-2 text-xs font-medium transition-colors @if(request()->is('accounting/dashboard*')) text-indigo-600 @else text-gray-500 hover:text-indigo-500 @endif">
                            Dashboard
                        </a>

                        <a href="/accounting/accounts" class="block py-2 text-xs font-medium transition-colors @if(request()->is('accounting/accounts*')) text-indigo-600 @else text-gray-500 hover:text-indigo-500 @endif">
                            Plano de Contas
                        </a>
                        
                        <a href="/accounting/journal" class="block py-2 text-xs font-medium transition-colors @if(request()->is('accounting/journal*')) text-indigo-600 @else text-gray-500 hover:text-indigo-500 @endif">
                            Lançamentos Diários
                        </a>

                        <a href="/accounting/reports/ledger" class="block py-2 text-xs font-medium transition-colors @if(request()->is('accounting/reports/ledger*')) text-indigo-600 @else text-gray-500 hover:text-indigo-500 @endif">
                            Razão Geral
                        </a>

                        <a href="/accounting/reports/trial-balance" class="block py-2 text-xs font-medium transition-colors @if(request()->is('accounting/reports/trial-balance*')) text-indigo-600 @else text-gray-500 hover:text-indigo-500 @endif">
                            Balancetes
                        </a>

                        <div class="pt-2 pb-1">
                            <p class="text-[10px] font-bold tracking-wider text-gray-400 uppercase">Mapas Oficiais</p>
                        </div>
                        <a href="/accounting/reports/balance-sheet" class="block py-2 text-xs font-medium transition-colors @if(request()->is('accounting/reports/balance-sheet*')) text-indigo-600 @else text-gray-500 hover:text-indigo-500 @endif">
                            Balanço Patrimonial
                        </a>
                        <a href="/accounting/reports/income-statement" class="block py-2 text-xs font-medium transition-colors @if(request()->is('accounting/reports/income-statement*')) text-indigo-600 @else text-gray-500 hover:text-indigo-500 @endif">
                            Dem. de Resultados (DRE)
                        </a>
                    </div>
                </div>

                <!-- Empresas Group (New) -->
                <div x-data="{ open: {{ request()->is('companies*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="console.log('Empresas clicked', {sidebarCollapsed, open}); if(sidebarCollapsed) { sidebarCollapsed = false; open = true; } else { open = !open; }" 
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-800 group"
                            :class="{ 'bg-gray-50 dark:bg-gray-800/50': open && !sidebarCollapsed }"
                            :title="sidebarCollapsed ? 'Empresas' : ''">
                        <div class="flex items-center gap-3">
                            <i data-lucide="building-2" class="w-5 h-5 flex-shrink-0 text-gray-400 group-hover:text-indigo-600 transition-colors"></i>
                            <span x-show="!sidebarCollapsed" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Empresas</span>
                        </div>
                        <i data-lucide="chevron-right" x-show="!sidebarCollapsed" :class="open ? 'rotate-90' : ''" class="w-4 h-4 text-gray-400 transition-transform duration-200"></i>
                    </button>

                    <div x-show="open && !sidebarCollapsed" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="pl-11 pr-2 space-y-1">
                        
                        <a href="/companies" class="block py-2 text-xs font-medium transition-colors @if(request()->is('companies') && !request()->has('switch')) text-indigo-600 @else text-gray-500 hover:text-indigo-500 @endif">
                            Gerir Clientes
                        </a>

                        <div class="pt-2 pb-1">
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Trabalhar com:</span>
                        </div>

                        @foreach(all_empresas() as $emp)
                            <form action="/company/switch" method="POST" class="block">
                                <input type="hidden" name="empresa_id" value="{{ $emp->id }}">
                                <button type="submit" class="w-full text-left py-2 text-xs font-medium transition-colors flex items-center gap-2 group/item {{ current_empresa()->id == $emp->id ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-500' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ current_empresa()->id == $emp->id ? 'bg-indigo-600' : 'bg-gray-300 group-hover/item:bg-indigo-400' }}"></span>
                                    <span class="truncate">{{ $emp->nome }}</span>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </nav>

            <!-- User footer -->
            <div class="px-4 py-4 border-t transition-all duration-300" style="border-color: var(--border-color)">
                <div class="flex items-center gap-3 mb-3 overflow-hidden">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-400 to-indigo-500 flex items-center justify-center text-xs font-bold text-white flex-shrink-0 shadow-sm">
                        {{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}
                    </div>
                    <div x-show="!sidebarCollapsed" class="min-w-0" x-transition:enter="transition ease-out duration-200">
                        <p class="text-xs font-medium truncate" style="color: var(--text-main)">{{ session('user_name', 'Usuário') }}</p>
                        <p class="text-[10px] truncate opacity-60" style="color: var(--text-muted)">{{ session('user_email', '') }}</p>
                    </div>
                </div>
                <form method="POST" action="/logout">
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-xs transition-all duration-200 hover:bg-red-50 dark:hover:bg-red-900/10 group" 
                            style="color: var(--text-muted)" :title="sidebarCollapsed ? 'Sair' : ''">
                        <svg class="w-4 h-4 flex-shrink-0 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="group-hover:text-red-500">Sair</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden relative">
            <!-- Top bar -->
            <header class="flex items-center justify-between px-8 py-4 border-b backdrop-blur-md z-20" style="background-color: var(--glass-bg); border-color: var(--border-color)">
                <div class="flex items-center gap-4">
                    <!-- Mobile Menu Button (Optional integration) -->
                    <button class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div>
                        <h2 class="text-base font-bold text-gray-800 dark:text-gray-100 leading-tight">@yield('page-title', 'Dashboard')</h2>
                        <p class="text-[10px] font-medium text-gray-400 dark:text-gray-500">@yield('page-subtitle', '')</p>
                    </div>
                </div>
                
                <!-- Header Actions (Theme & User) -->
                <div class="flex items-center gap-4" x-data="{ 
                    darkMode: localStorage.getItem('theme') === 'dark',
                    toggleTheme() {
                        this.darkMode = !this.darkMode;
                        localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
                        if (this.darkMode) {
                            document.documentElement.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                        }
                    }
                }">
                    <!-- Theme Toggle -->
                    <button @click="toggleTheme" class="p-2 rounded-xl text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.95 16.95l.707.707M7.05 7.05l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                        </svg>
                    </button>

                    @yield('header-actions')
                </div>
            </header>

            <!-- Content area -->
            <div class="flex-1 overflow-y-auto p-6 md:p-8 custom-scrollbar">
                <!-- Flash Messages -->
                @if(session()->hasFlash('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        <span>{{ session()->getFlash('success') }}</span>
                    </div>
                @endif

                @if(session()->hasFlash('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                        <span>{{ session()->getFlash('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script src="/js/app.js"></script>
    @yield('scripts')
</body>
</html>
