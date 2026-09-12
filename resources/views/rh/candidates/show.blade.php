@extends('layout.app')

@section('title', $candidate->name)
@section('page-title', 'Recrutamento')
@section('page-subtitle', 'Pipeline do candidato')

@section('content')
<div>
    @if(!empty($success))
    <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $success }}
    </div>
    @endif

    @if(!empty($error))
    <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $error }}
    </div>
    @endif

    @php
        $pipeline = ['novo' => 'Novo', 'triagem' => 'Triagem', 'entrevista' => 'Entrevista', 'aprovado' => 'Aprovado'];
        $currentIdx = array_search($candidate->status, array_keys($pipeline), true);
        $isTerminal = in_array($candidate->status, ['rejeitado', 'contratado'], true);
    @endphp

    <div class="glass-card rounded-2xl p-6 mb-6">
        <div class="flex items-start justify-between gap-6">
            <div class="space-y-2">
                <h2 class="text-xl font-bold" style="color: var(--text-main)">{{ $candidate->name }}</h2>
                <div class="flex flex-wrap items-center gap-4 text-xs" style="color: var(--text-muted)">
                    <span>{{ $candidate->email }}</span>
                    @if($candidate->phone)<span>{{ $candidate->phone }}</span>@endif
                    <span>Vaga: {{ $candidate->jobOpening?->title ?? 'Banco de Talentos' }}</span>
                    <span>Origem: {{ ucfirst($candidate->source) }}</span>
                    @if($candidate->decided_at)
                    <span>Decidido em {{ date('d/m/Y', strtotime($candidate->decided_at)) }}</span>
                    @endif
                    @if($candidate->employee_id)
                    <a href="/rh/employees/{{ $candidate->employee_id }}" class="font-medium" style="color: var(--accent)">Funcionário associado</a>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="/rh/candidates/{{ $candidate->id }}/edit" class="btn-secondary px-3 py-1.5 text-xs">Editar</a>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-1.5">
            @foreach($pipeline as $stage => $label)
                @if(is_int($currentIdx) && $currentIdx >= 0)
                <a href="#" onclick="return false;"
                   class="flex-1 text-center px-3 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide transition-all duration-200 border {{ array_search($stage, array_keys($pipeline), true) <= $currentIdx ? 'bg-violet-500/15 text-violet-300 border-violet-500/30' : 'hover:bg-white/5' }}"
                   style="color: {{ array_search($stage, array_keys($pipeline), true) <= $currentIdx ? 'var(--violet-300, #c4b5fd)' : 'var(--text-muted)' }}; border-color: var(--border-color)">
                    {{ $label }}
                </a>
                @endif
            @endforeach
        </div>

        @if(!$isTerminal)
        <div class="mt-4 flex items-center gap-3 flex-wrap">
            @if($candidate->status === 'novo')
            <form method="POST" action="/rh/candidates/{{ $candidate->id }}/transition">
                <input type="hidden" name="stage" value="triagem">
                <button type="submit" class="btn-primary px-3 py-1.5 text-xs">Avançar para Triagem</button>
            </form>
            @elseif($candidate->status === 'triagem')
            <form method="POST" action="/rh/candidates/{{ $candidate->id }}/transition">
                <input type="hidden" name="stage" value="entrevista">
                <button type="submit" class="btn-primary px-3 py-1.5 text-xs">Avançar para Entrevista</button>
            </form>
            @elseif($candidate->status === 'entrevista')
            <form method="POST" action="/rh/candidates/{{ $candidate->id }}/transition">
                <input type="hidden" name="stage" value="aprovado">
                <button type="submit" class="btn-primary px-3 py-1.5 text-xs">Aprovar Candidato</button>
            </form>
            @elseif($candidate->status === 'aprovado')
            <form method="POST" action="/rh/candidates/{{ $candidate->id }}/hire">
                <button type="submit" class="btn-primary px-3 py-1.5 text-xs">Contratar</button>
            </form>
            @endif

            @if(!in_array($candidate->status, ['rejeitado', 'contratado'], true))
            <form method="POST" action="/rh/candidates/{{ $candidate->id }}/reject" x-data="{ confirm: false }" @submit.prevent="confirm ? $el.submit() : (confirm = true)">
                <button type="submit" class="btn-secondary px-3 py-1.5 text-xs" :title="confirm ? 'Clique para confirmar' : 'Rejeitar candidato'">
                    <span x-show="!confirm">Rejeitar</span>
                    <span x-show="confirm" class="font-bold text-red-500">Confirmar rejeição</span>
                </button>
            </form>
            @endif
        </div>
        @else
        <div class="mt-4">
            @if($candidate->status === 'contratado')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-teal-500/10 text-teal-600 border border-teal-500/20">
                <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                Contratado
            </span>
            @else
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-red-500/10 text-red-600 border border-red-500/20">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                Rejeitado
            </span>
            @endif
        </div>
        @endif

        @if($candidate->notes)
        <div class="mt-5 pt-5 border-t" style="border-color: var(--border-color)">
            <h4 class="text-xs font-bold uppercase tracking-wide mb-2" style="color: var(--text-muted)">Notas</h4>
            <p class="text-sm whitespace-pre-line" style="color: var(--text-muted)">{{ $candidate->notes }}</p>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="table-container">
            <div class="px-6 py-4 border-b" style="border-color: var(--border-color)">
                <h3 class="text-sm font-semibold" style="color: var(--text-main)">Entrevistas</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Entrevistador</th>
                            <th>Resultado</th>
                            <th class="text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($interviews as $interview)
                        <tr>
                            <td style="color: var(--text-main)">{{ $interview->scheduled_at ? date('d/m/Y H:i', strtotime($interview->scheduled_at)) : '—' }}</td>
                            <td style="color: var(--text-muted)">{{ $interview->interviewer ?? '—' }}</td>
                            <td>
                                <form method="POST" action="/rh/candidates/{{ $candidate->id }}/interviews/{{ $interview->id }}/result">
                                    <select name="result" onchange="this.form.submit()" class="text-xs rounded-lg px-2 py-1 outline-none" style="background-color: var(--bg-main); color: var(--text-main); border: 1px solid var(--border-color)">
                                        <option value="pendente" {{ $interview->result === 'pendente' ? 'selected' : '' }}>Pendente</option>
                                        <option value="aprovado" {{ $interview->result === 'aprovado' ? 'selected' : '' }}>Aprovado</option>
                                        <option value="reprovado" {{ $interview->result === 'reprovado' ? 'selected' : '' }}>Reprovado</option>
                                    </select>
                                </form>
                            </td>
                            <td class="text-right">
                                <form method="POST" action="/rh/candidates/{{ $candidate->id }}/interviews/{{ $interview->id }}/delete" x-data="{ confirm: false }" @submit.prevent="confirm ? $el.submit() : (confirm = true)">
                                    <button type="submit" class="p-2 rounded-lg transition-all duration-200" style="color: var(--text-muted)" onmouseover="this.style.color='#ef4444'; this.style.backgroundColor='#fef2f2'" onmouseout="this.style.color='var(--text-muted)'; this.style.backgroundColor='transparent'" :title="confirm ? 'Clique para confirmar' : 'Excluir'">
                                        <svg x-show="!confirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span x-show="confirm" class="text-[10px] font-bold uppercase tracking-wider">Confirmar</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center" style="color: var(--text-muted)">
                                Nenhuma entrevista agendada.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(!$isTerminal)
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-sm font-semibold mb-4" style="color: var(--text-main)">Agendar Entrevista</h3>
            <form method="POST" action="/rh/candidates/{{ $candidate->id }}/interviews" class="space-y-4">
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
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Data e Hora</label>
                    <input type="datetime-local" name="scheduled_at" class="form-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Entrevistador</label>
                    <input type="text" name="interviewer" maxlength="150" class="form-input" placeholder="Nome do entrevistador">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Notas da entrevista</label>
                    <textarea name="notes" class="form-input" rows="3" placeholder="Temas a abordar, observações"></textarea>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary">Agendar</button>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection