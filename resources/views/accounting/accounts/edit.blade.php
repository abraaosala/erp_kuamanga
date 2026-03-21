@extends('layout.app')

@section('title', 'Editar Conta - PGC')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex items-center gap-4 mb-8">
        <a href="/accounting/accounts" class="p-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5 text-gray-500"></i>
        </a>
        <h3 class="text-gray-700 dark:text-gray-200 text-3xl font-semibold">Editar Conta: {{ $account->code }}</h3>
    </div>

    <div class="max-w-4xl bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <form action="/accounting/accounts/{{ $account->id }}/update" method="POST" class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Código -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Código da Conta</label>
                    <input type="text" name="code" value="{{ $account->code }}" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all outline-none">
                </div>

                <!-- Nome -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Descrição / Nome</label>
                    <input type="text" name="name" value="{{ $account->name }}" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all outline-none">
                </div>

                <!-- Tipo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Natureza / Tipo</label>
                    <select name="type" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all outline-none">
                        <option value="asset" {{ $account->type === 'asset' ? 'selected' : '' }}>Ativo (Devedora)</option>
                        <option value="liability" {{ $account->type === 'liability' ? 'selected' : '' }}>Passivo (Credora)</option>
                        <option value="equity" {{ $account->type === 'equity' ? 'selected' : '' }}>Capital Próprio</option>
                        <option value="revenue" {{ $account->type === 'revenue' ? 'selected' : '' }}>Proveito / Rendimento</option>
                        <option value="expense" {{ $account->type === 'expense' ? 'selected' : '' }}>Custo / Gasto</option>
                    </select>
                </div>

                <!-- Conta Pai -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Conta Mãe (Opcional)</label>
                    <select name="parent_id"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all outline-none">
                        <option value="">Nenhuma (Conta Raiz)</option>
                        @foreach($parentAccounts as $parent)
                            @if($parent->id !== $account->id)
                                <option value="{{ $parent->id }}" {{ $account->parent_id == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->code }} - {{ $parent->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Analítica -->
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_analytic" value="1" id="is_analytic" {{ $account->is_analytic ? 'checked' : '' }}
                           class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-all">
                    <label for="is_analytic" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Esta é uma conta analítica? (Pode receber lançamentos)
                    </label>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="/accounting/accounts" class="flex items-center gap-2 px-6 py-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-medium">
                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                    <span>Cancelar</span>
                </a>
                <button type="submit" class="flex items-center gap-2 px-8 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 shadow-md shadow-indigo-100 dark:shadow-none transition-all">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    <span>Salvar Alterações</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
