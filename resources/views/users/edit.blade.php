@extends('layout.app')

@section('title', 'Editar Usuário')
@section('page-title', 'Editar Usuário')
@section('page-subtitle', 'Atualizar dados do usuário: {{ $user->name }}')

@section('content')
<div class="max-w-2xl">
    @if(!empty($error))
    <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $error }}
    </div>
    @endif

    <div class="glass-card rounded-2xl p-8">
        <form method="POST" action="/users/{{ $user->id }}/update" class="space-y-5" x-data="{ loading: false }" @submit="loading = true">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium mb-2" style="color: var(--text-muted)">Nome completo <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ $user->name }}" class="form-input" required>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium mb-2" style="color: var(--text-muted)">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ $user->email }}" class="form-input" required>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-2" style="color: var(--text-muted)">Nova senha <span class="text-gray-500 font-normal text-xs">(deixe vazio para manter)</span></label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="••••••••">
                </div>

                <div class="md:col-span-2">
                    <label for="roles" class="block text-sm font-medium mb-2" style="color: var(--text-muted)">Funções (Roles)</label>
                    <select name="roles[]" id="roles" class="form-input select2-multiple" multiple data-placeholder="Selecione as funções">
                        @php $userRoleIds = $user->roles->pluck('id')->toArray(); @endphp
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ in_array($role->id, $userRoleIds) ? 'selected' : '' }}>
                            {{ $role->display_name ?? $role->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all duration-200 has-[:checked]:border-emerald-500/30 has-[:checked]:bg-emerald-500/5" style="background-color: var(--bg-main); border-color: var(--border-color)">
                        <input type="checkbox" name="active" value="1" {{ $user->active ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-emerald-500 focus:ring-emerald-500/30">
                        <div>
                            <span class="text-sm font-medium" style="color: var(--text-main)">Usuário ativo</span>
                            <p class="text-xs" style="color: var(--text-muted)">Usuários inativos não podem fazer login</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary flex items-center gap-2" :disabled="loading" x-text="loading ? 'Salvando...' : 'Salvar Alterações'">
                    Salvar Alterações
                </button>
                <a href="/users" class="btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#roles').select2({
            width: '100%',
            allowClear: true,
            placeholder: 'Selecione as funções'
        });
    });
</script>
@endsection
