@extends('layout.app')

@section('title', 'Gerir Empresas')
@section('page-title', 'Empresas')
@section('page-subtitle', 'Gestão de Clientes e Entidades')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Lista de Empresas</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-xs">Gerencie os clientes que a Kuamanga atende.</p>
        </div>
        <button class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-indigo-200 dark:shadow-none">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Nova Empresa</span>
        </button>
    </div>

    <!-- Companies Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($companies as $company)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md transition-shadow group">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-xl font-bold text-indigo-600 dark:text-indigo-400 shadow-inner group-hover:scale-110 transition-transform">
                        {{ substr($company->nome, 0, 1) }}
                    </div>
                    <div class="flex gap-2">
                        <button class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-colors" title="Editar">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </button>
                        @if($company->id != current_empresa()->id)
                            <form action="/company/switch" method="POST">
                                <input type="hidden" name="empresa_id" value="{{ $company->id }}">
                                <button type="submit" class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 dark:hover:bg-green-900/30 rounded-lg transition-colors" title="Trabalhar nesta">
                                    <i data-lucide="play-circle" class="w-4 h-4"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="mb-4">
                    <h4 class="text-base font-bold text-gray-800 dark:text-white truncate" title="{{ $company->nome }}">{{ $company->nome }}</h4>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">NIF: {{ $company->nif }}</p>
                </div>

                <div class="space-y-2 border-t border-gray-50 dark:border-gray-700/50 pt-4">
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                        <span class="truncate">{{ $company->cidade ?: 'Luanda' }}, Angola</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        <span>Desde {{ $company->created_at->format('M Y') }}</span>
                    </div>
                </div>

                <div class="mt-6">
                    @if($company->id == current_empresa()->id)
                        <div class="flex items-center justify-center gap-2 w-full py-2 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 text-xs font-bold rounded-xl border border-indigo-100 dark:border-indigo-800/50">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            EMPRESA ATIVA
                        </div>
                    @else
                        <form action="/company/switch" method="POST">
                            <input type="hidden" name="empresa_id" value="{{ $company->id }}">
                            <button type="submit" class="w-full py-2 bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 text-xs font-bold rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all">
                                TRABALHAR NESTA
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
