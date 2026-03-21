@extends('layout.app')

@section('title', 'Plano de Contas')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-gray-700 text-3xl font-medium">Plano Geral de Contabilidade (PGC) - Angola</h3>
        <a href="/accounting/accounts/create" class="flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-xl hover:bg-indigo-500 transition-all duration-200 shadow-sm shadow-indigo-100 dark:shadow-none">
            <i data-lucide="plus-circle" class="w-5 h-5"></i>
            <span class="font-semibold">Nova Conta</span>
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-4 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center gap-2">
                            <i data-lucide="hash" class="w-3.5 h-3.5"></i>
                            <span>Código</span>
                        </div>
                    </th>
                    <th class="px-5 py-4 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center gap-2">
                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                            <span>Nome da Conta</span>
                        </div>
                    </th>
                    <th class="px-5 py-4 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center gap-2">
                            <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                            <span>Tipo</span>
                        </div>
                    </th>
                    <th class="px-5 py-4 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center gap-2">
                            <i data-lucide="list-checks" class="w-3.5 h-3.5"></i>
                            <span>Analítica</span>
                        </div>
                    </th>
                    <th class="px-5 py-4 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center gap-2">
                            <i data-lucide="settings" class="w-3.5 h-3.5"></i>
                            <span>Ações</span>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($accounts as $account)
                    @include('accounting.accounts.partials.account_row', ['account' => $account, 'level' => 0])
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
