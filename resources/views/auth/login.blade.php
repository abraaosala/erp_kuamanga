<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | ERP Sistema</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body class="font-inter antialiased flex items-center justify-center min-h-screen" style="background-color: var(--bg-main)">

    <!-- Background glow - subtle -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full blur-3xl opacity-20" style="background-color: var(--accent)"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full blur-3xl opacity-20" style="background-color: #4f46e5"></div>
    </div>

    <div class="w-full max-w-md px-6 relative z-10">
        <!-- Logo -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center shadow-xl shadow-violet-500/30 mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold" style="color: var(--text-main)">ERP Sistema</h1>
            <p class="text-sm mt-1" style="color: var(--text-muted)">Bem-vindo de volta! Faça login para continuar.</p>
        </div>

        <!-- Card -->
        <div class="glass-card rounded-2xl p-8 shadow-2xl">
            @if(!empty($error))
            <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $error }}
            </div>
            @endif

            <form method="POST" action="/login" class="space-y-5" x-data="{ loading: false }" @submit="loading = true">
                <div>
                    <label for="email" class="block text-sm font-medium mb-2" style="color: var(--text-muted)">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        placeholder="seu@email.com"
                        required
                        autocomplete="email"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-2" style="color: var(--text-muted)">Senha</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    >
                </div>

                <button
                    type="submit"
                    class="btn-primary w-full flex items-center justify-center gap-2"
                    :disabled="loading"
                >
                    <span x-show="!loading">Entrar</span>
                    <span x-show="loading">Aguarde...</span>
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-sm" style="color: var(--text-muted)">
                    Entre em contato com o administrador para criar sua conta.
                </p>
            </div>
        </div>
    </div>

    <script src="/js/app.js"></script>
</body>
</html>
