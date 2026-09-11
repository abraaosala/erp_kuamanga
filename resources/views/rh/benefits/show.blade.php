@extends('layout.app')

@section('title', $benefit->name)
@section('page-title', $benefit->name)
@section('page-subtitle', 'Detalhes do benefício e colaboradores')

@section('content')
<div x-data="{ open: false }">
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

    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2">
            @switch(strtolower((string)($benefit->category ?? '')))
                @case('saude')
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-rose-500/10 text-rose-600 border border-rose-500/20">Saúde</span>
                @break
                @case('transporte')
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">Transporte</span>
                @break
                @case('alimentacao')
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-amber-500/10 text-amber-600 border border-amber-500/20">Alimentação</span>
                @break
                @case('educacao')
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-blue-500/10 text-blue-600 border border-blue-500/20">Educação</span>
                @break
                @default
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-slate-500/10 text-slate-600 border border-slate-500/20">{{ $categories[$benefit->category] ?? '—' }}</span>
            @endswitch
            @if($benefit->status === 'active')
            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-green-500/10 text-green-600 border border-green-500/20">Ativo</span>
            @else
            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-slate-500/10 text-slate-600 border border-slate-500/20">Inativo</span>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <a href="/rh/benefits/{{ $benefit->id }}/edit" class="btn-secondary px-3 py-1.5 text-xs">Editar</a>
            <a href="/rh/benefits" class="btn-secondary px-3 py-1.5 text-xs">← Voltar</a>
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6 mb-6">
        @if(!empty($benefit->description))
        <p class="text-sm mb-4" style="color: var(--text-muted)">{{ $benefit->description }}</p>
        @endif
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-xs font-semibold mb-1" style="color: var(--text-muted)">Cargo</p>
                <p class="text-sm font-semibold" style="color: var(--text-main)">{{ $benefit->position->name ?? 'Todos' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold mb-1" style="color: var(--text-muted)">Departamento</p>
                <p class="text-sm font-semibold" style="color: var(--text-main)">{{ $benefit->department->name ?? 'Todos' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold mb-1" style="color: var(--text-muted)">Antiguidade mínima</p>
                <p class="text-sm font-semibold" style="color: var(--text-main)">
                    @if((int) $benefit->min_tenure_months > 0)
                    {{ $benefit->min_tenure_months }} {{ (int) $benefit->min_tenure_months === 1 ? 'mês' : 'meses' }}
                    @else
                    Sem restrição
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6" id="funcionarios">
        <div class="lg:col-span-3 table-container">
            <div class="px-6 py-4 border-b" style="border-color: var(--border-color)">
                <h3 class="text-sm font-semibold" style="color: var(--text-main)">Funcionários com o benefício ({{ $assigned->count() }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Funcionário</th>
                            <th>Cargo</th>
                            <th class="text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assigned as $employee)
                        <tr>
                            <td>
                                <span class="font-medium" style="color: var(--text-main)">{{ $employee->name }}</span>
                                @if($employee->department)
                                <p class="text-xs mt-0.5" style="color: var(--text-muted)">{{ $employee->department->name }}</p>
                                @endif
                            </td>
                            <td style="color: var(--text-muted)">{{ $employee->position->name ?? '—' }}</td>
                            <td class="text-right">
                                <form method="POST" action="/rh/benefits/{{ $benefit->id }}/employees/{{ $employee->id }}/delete" x-data="{ confirm: false }" @submit.prevent="confirm ? $el.submit() : (confirm = true)">
                                    <button type="submit" class="p-2 rounded-lg transition-all duration-200" style="color: var(--text-muted)" onmouseover="this.style.color='#ef4444'; this.style.backgroundColor='#fef2f2'" onmouseout="this.style.color='var(--text-muted)'; this.style.backgroundColor='transparent'" :title="confirm ? 'Clique para confirmar' : 'Remover'">
                                        <svg x-show="!confirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        <span x-show="confirm" class="text-[10px] font-bold uppercase tracking-wider">Confirmar</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center">
                                <p style="color: var(--text-muted)">Ainda não há funcionários com este benefício.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="lg:col-span-2">
            @if($eligible->isEmpty())
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-sm font-semibold mb-1" style="color: var(--text-main)">Atribuir benefício</h3>
                <p class="text-xs mb-4" style="color: var(--text-muted)">Funcionários elegíveis ainda sem o benefício: {{ $eligible->count() }}</p>
                <div class="flex flex-col items-center gap-3 py-6 text-center">
                    <svg class="w-8 h-8" style="color: var(--border-color)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs" style="color: var(--text-muted)">Nenhum funcionário elegível disponível.</p>
                    @php
                    $constraints = [];
                    if ($benefit->position) {
                        $constraints[] = 'cargo ' . $benefit->position->name;
                    }
                    if ($benefit->department) {
                        $constraints[] = 'departamento ' . $benefit->department->name;
                    }
                    $tenure = (int) $benefit->min_tenure_months;
                    if ($tenure > 0) {
                        $constraints[] = 'mínimo de ' . $tenure . ' ' . ($tenure === 1 ? 'mês' : 'meses') . ' de antiguidade';
                    }
                    $constraints = $constraints === [] ? ['sem restrições'] : $constraints;
                    @endphp
                    <p class="text-[11px] leading-relaxed" style="color: var(--text-muted)">
                        Restrições ativas: {{ implode(', ', $constraints) }}.
                    </p>
                    <a href="/rh/benefits/{{ $benefit->id }}/edit" class="text-xs font-semibold underline underline-offset-4" style="color: var(--accent)">Editar restrições</a>
                </div>
            </div>
            @else
            <div class="rounded-2xl p-5 border-2 border-dashed" style="border-color: var(--accent); background-color: var(--accent-soft)">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 flex-shrink-0 rounded-xl flex items-center justify-center" style="background-color: var(--accent); color: #fff">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold" style="color: var(--text-main)">Atribuir benefício</h3>
                        <p class="text-xs mt-0.5" style="color: var(--text-muted)">
                            {{ $eligible->count() }} {{ $eligible->count() === 1 ? 'funcionário elegível' : 'funcionários elegíveis' }} prontos a receber
                        </p>
                    </div>
                </div>
                <button type="button" @click="open = true" class="btn-primary w-full inline-flex items-center justify-center gap-2 mt-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Atribuir benefício</span>
                </button>
            </div>
            @endif
        </div>
    </div>

    @if(!$eligible->isEmpty())
    <!-- Modal de atribuição de benefício -->
    <div x-show="open"
         x-cloak
         @keydown.escape.window="open=false"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-8"
         style="background-color: rgba(0,0,0,0.6); backdrop-filter: blur(4px);"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="open=false">
        <div class="glass-card rounded-2xl w-full max-w-lg flex flex-col max-h-[85vh] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 border-b" style="border-color: var(--border-color)">
                <div class="flex items-center gap-2 min-w-0">
                    <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <h3 class="text-sm font-semibold truncate" style="color: var(--text-main)">Atribuir benefício</h3>
                </div>
                <button type="button" @click="open=false" class="modal-close p-1.5 rounded-lg transition-colors" style="color: var(--text-muted)" title="Fechar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5 overflow-y-auto">
                <form method="POST" action="/rh/benefits/{{ $benefit->id }}/employees" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Funcionário <span class="text-red-400">*</span></label>
                        <select name="employee_id" required class="form-input">
                            <option value="">— Selecione —</option>
                            @foreach($eligible as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Data de início</label>
                        <input type="date" name="started_at" class="form-input">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Notas</label>
                        <textarea name="notes" class="form-input" rows="3" placeholder="Observações sobre a atribuição"></textarea>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                        <button type="button" @click="open=false" class="btn-secondary">Cancelar</button>
                        <button type="submit" class="btn-primary">Atribuir Benefício</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection