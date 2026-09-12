@extends('layout.app')

@section('title', 'Editar Candidato')
@section('page-title', 'Recrutamento')
@section('page-subtitle', 'Editar candidato')

@section('content')
<div class="max-w-2xl mx-auto">
    @if(!empty($error))
    <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $error }}
    </div>
    @endif

    <form method="POST" action="/rh/candidates/{{ $candidate->id }}/update" class="glass-card rounded-2xl p-6 space-y-6">
        <div>
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Dados do Candidato</h3>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Nome completo <span class="text-red-400">*</span></label>
                    <input type="text" name="name" required maxlength="150" value="{{ $candidate->name }}" class="form-input">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Email <span class="text-red-400">*</span></label>
                        <input type="email" name="email" required maxlength="190" value="{{ $candidate->email }}" class="form-input">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Telefone</label>
                        <input type="text" name="phone" maxlength="30" value="{{ $candidate->phone }}" class="form-input">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Vaga</label>
                        <select name="job_opening_id" class="form-input">
                            <option value="">Banco de Talentos</option>
                            @foreach($openings as $opening)
                            <option value="{{ $opening->id }}" {{ $candidate->job_opening_id == $opening->id ? 'selected' : '' }}>{{ $opening->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Origem</label>
                        <select name="source" class="form-input">
                            @foreach($sources as $source)
                            <option value="{{ $source }}" {{ $candidate->source === $source ? 'selected' : '' }}>{{ ucfirst($source) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Notas</label>
                    <textarea name="notes" class="form-input" rows="3">{{ $candidate->notes }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t" style="border-color: var(--border-color)">
            <button type="submit" class="btn-primary">Salvar</button>
            <a href="/rh/candidates/{{ $candidate->id }}" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection