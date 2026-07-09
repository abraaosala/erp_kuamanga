<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | ERP Sistema</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-4px); }
            20%, 40%, 60%, 80% { transform: translateX(4px); }
        }
        .animate-shake { animation: shake 0.5s ease-in-out; }
    </style>
</head>
<body class="font-inter antialiased min-h-screen flex">

    <!-- Theme toggle -->
    <div class="fixed top-5 right-5 z-50" x-data="{ 
        darkMode: localStorage.getItem('theme') === 'dark',
        toggleTheme() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
            if (this.darkMode) document.documentElement.classList.add('dark');
            else document.documentElement.classList.remove('dark');
        }
    }">
        <button @click="toggleTheme" class="p-2 rounded-xl text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200" style="background-color: var(--bg-card); border: 1px solid var(--border-color)">
            <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
            <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.95 16.95l.707.707M7.05 7.05l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
            </svg>
        </button>
    </div>

    <!-- Left panel: Branding -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden items-center justify-center"
         style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 50%, #2563eb 100%);">
        <!-- Decorative circles -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-20 -left-20 w-72 h-72 rounded-full bg-white/5"></div>
            <div class="absolute -bottom-20 -right-20 w-96 h-96 rounded-full bg-white/5"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] rounded-full border border-white/10"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[350px] h-[350px] rounded-full border border-white/10"></div>
        </div>

        <div class="relative z-10 text-center px-12 max-w-lg">
            <!-- Logo -->
            <div class="w-16 h-16 rounded-2xl bg-white/15 backdrop-blur-sm flex items-center justify-center mx-auto mb-8 shadow-2xl">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>

            <h2 class="text-3xl font-bold text-white mb-3">ERP Kuamanga</h2>
            <p class="text-white/70 text-lg leading-relaxed mb-10">
                Gestão empresarial inteligente para o seu negócio em Angola.
            </p>

            <!-- Feature pills -->
            <div class="space-y-3 text-left">
                <div class="flex items-center gap-3 text-white/80">
                    <svg class="w-5 h-5 text-white/60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Contabilidade PGC Angola</span>
                </div>
                <div class="flex items-center gap-3 text-white/80">
                    <svg class="w-5 h-5 text-white/60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Multi-empresa e multi-moeda</span>
                </div>
                <div class="flex items-center gap-3 text-white/80">
                    <svg class="w-5 h-5 text-white/60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Relatórios oficiais (Balanço & DRE)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right panel: Form -->
    <div class="flex-1 flex items-center justify-center p-6 lg:p-10" style="background-color: var(--bg-main)">
        <div class="w-full max-w-sm mx-auto">
            <!-- Mobile logo -->
            <div class="lg:hidden flex flex-col items-center mb-8">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-violet-500/30 mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h1 class="text-xl font-bold" style="color: var(--text-main)">ERP Kuamanga</h1>
            </div>

            <!-- Form -->
            <div class="animate-fade-in">
                <h2 class="text-2xl font-bold mb-1" style="color: var(--text-main)">Bem-vindo de volta</h2>
                <p class="text-sm mb-8" style="color: var(--text-muted)">Faça login para aceder ao sistema.</p>

                @if(!empty($error))
                <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 text-sm animate-shake">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $error }}</span>
                </div>
                @endif

                <form method="POST" action="/login" class="space-y-5"
                      x-data="{
                          loading: false,
                          showPassword: false,
                          email: '',
                          password: '',
                          emailTouched: false,
                          passwordTouched: false
                      }"
                      @submit="loading = true">
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2" style="color: var(--text-muted)">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4" style="color: var(--text-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                x-model="email"
                                @blur="emailTouched = true"
                                class="form-input pl-10"
                                :class="emailTouched && !email ? 'border-red-400 ring-2 ring-red-500/20' : ''"
                                placeholder="seu@email.com"
                                required
                                autofocus
                                autocomplete="email"
                            >
                        </div>
                        <template x-if="emailTouched && !email">
                            <p class="mt-1.5 text-xs text-red-500">O email é obrigatório</p>
                        </template>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium mb-2" style="color: var(--text-muted)">Senha</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4" style="color: var(--text-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                id="password"
                                name="password"
                                x-model="password"
                                @blur="passwordTouched = true"
                                class="form-input pl-10 pr-10"
                                :class="passwordTouched && !password ? 'border-red-400 ring-2 ring-red-500/20' : ''"
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                            >
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center transition-colors duration-200"
                                style="color: var(--text-muted)"
                                :style="showPassword ? 'color: var(--accent)' : ''"
                                :title="showPassword ? 'Ocultar senha' : 'Mostrar senha'"
                                tabindex="-1"
                            >
                                <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        <template x-if="passwordTouched && !password">
                            <p class="mt-1.5 text-xs text-red-500">A senha é obrigatória</p>
                        </template>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer select-none group">
                            <input
                                type="checkbox"
                                name="remember"
                                class="w-4 h-4 rounded transition-all duration-200"
                                style="color: var(--accent); background-color: var(--input-bg); border-color: var(--border-color)"
                            >
                            <span class="text-sm transition-colors duration-200 group-hover:opacity-80" style="color: var(--text-muted)">Manter sessão</span>
                        </label>
                        <a
                            href="#"
                            class="text-sm font-medium transition-colors duration-200 hover:underline"
                            style="color: var(--accent)"
                            onclick="alert('Contacte o administrador para redefinir a sua senha.')"
                        >Esqueceu a senha?</a>
                    </div>

                    <button
                        type="submit"
                        class="w-full flex items-center justify-center gap-2 h-11 rounded-xl text-sm font-semibold text-white transition-all duration-200"
                        :class="loading ? 'opacity-70 cursor-not-allowed' : 'hover:opacity-90'"
                        style="background: linear-gradient(135deg, #7c3aed, #4f46e5)"
                        :disabled="loading"
                    >
                        <span x-show="!loading">Entrar</span>
                        <span x-show="loading" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Aguarde...
                        </span>
                    </button>

                    <div class="text-center pt-2">
                        <p class="text-sm" style="color: var(--text-muted)">
                            Entre em contacto com o administrador para criar sua conta.
                        </p>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <p class="mt-10 text-xs text-center" style="color: var(--text-muted)">
                &copy; {{ date('Y') }} ERP Kuamanga
            </p>
        </div>
    </div>

    <script src="/js/app.js"></script>
</body>
</html>
