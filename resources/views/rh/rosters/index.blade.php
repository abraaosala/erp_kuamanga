@extends('layout.app')

@section('title', 'Rosters')
@section('page-title', 'Rosters de Turnos')
@section('page-subtitle', 'Calendário e geração de rotação')

@section('content')
<div x-data="rosterPage()" x-init="initCalendar()">
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

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="glass-card rounded-2xl p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: rgba(124,58,237,0.12); color: var(--accent)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-2xl font-bold leading-none" style="color: var(--text-main)">{{ $stats['today_work'] }}</p>
                <p class="text-xs mt-1 truncate" style="color: var(--text-muted)">Em trabalho hoje</p>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: rgba(100,116,139,0.12); color: #64748b">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m16 0a8 8 0 11-16 0 8 8 0 0116 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-2xl font-bold leading-none" style="color: var(--text-main)">{{ $stats['today_off'] }}</p>
                <p class="text-xs mt-1 truncate" style="color: var(--text-muted)">De folga hoje</p>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: rgba(16,185,129,0.12); color: #10b981">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-2xl font-bold leading-none" style="color: var(--text-main)">{{ $stats['employees_covered'] }}</p>
                <p class="text-xs mt-1 truncate" style="color: var(--text-muted)">Funcionários em rotação</p>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: rgba(245,158,11,0.12); color: #f59e0b">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-2xl font-bold leading-none" style="color: var(--text-main)">{{ $stats['total_shifts'] }}</p>
                <p class="text-xs mt-1 truncate" style="color: var(--text-muted)">Turnos gerados</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Sidebar: geração -->
        <div class="xl:col-span-1 space-y-6">
            <div class="glass-card rounded-2xl p-5">
                <h3 class="text-sm font-semibold mb-4 flex items-center gap-2" style="color: var(--text-main)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Gerar Rotação
                </h3>

                <form method="POST" action="/rh/rosters" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Nome <span class="text-red-400">*</span></label>
                        <input type="text" name="name" required placeholder="Ex.: Portaria 6×2" class="form-input">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Escala <span class="text-red-400">*</span></label>
                        <select name="work_schedule_id" required class="form-input no-select2" x-model="scheduleId" x-on:change="loadEmployees()">
                            <option value="">Selecione uma escala</option>
                            @foreach($schedules as $schedule)
                            <option value="{{ $schedule->id }}">{{ $schedule->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Padrão do ciclo (trabalho / folga)</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="pattern[]" value="6" min="1" max="60" class="form-input text-center" title="Dias de trabalho" x-model.number="workDays">
                            <span class="text-xs font-bold" style="color: var(--accent)">×</span>
                            <input type="number" name="pattern[]" value="2" min="1" max="60" class="form-input text-center" title="Dias de folga" x-model.number="offDays">
                        </div>
                        <p class="text-[11px] mt-1" style="color: var(--text-muted)">Ex.: 6×2 = 6 dias a trabalhar, 2 de folga.</p>

                        <!-- Preview do ciclo -->
                        <div class="mt-2.5 flex items-center gap-1" x-show="cycle.length">
                            <template x-for="(c, i) in cycle" :key="i">
                                <span class="w-4 h-4 rounded transition-all duration-200" :class="c === 'T' ? 'bg-violet-500' : 'bg-slate-400/60 dark:bg-slate-600'" :title="c === 'T' ? 'Trabalho' : 'Folga'"></span>
                            </template>
                        </div>
                        <p class="text-[10px] mt-1" style="color: var(--text-muted)">
                            <span x-show="cycle.length" x-text="cycle.filter(c => c === 'T').length + ' dias de trabalho · ' + cycle.filter(c => c === 'F').length + ' de folga'"></span>
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Início</label>
                            <input type="date" name="start_date" required class="form-input" x-model="startDate">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Fim</label>
                            <input type="date" name="end_date" required class="form-input" x-model="endDate">
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="setPreset('month')" class="px-2.5 py-1 text-[11px] font-medium rounded-lg transition-colors border" style="color: var(--text-muted); border-color: var(--border-color)" onmouseover="this.style.color='var(--accent)'; this.style.borderColor='var(--accent)'" onmouseout="this.style.color='var(--text-muted)'; this.style.borderColor='var(--border-color)'">Este mês</button>
                        <button type="button" @click="setPreset('quarter')" class="px-2.5 py-1 text-[11px] font-medium rounded-lg transition-colors border" style="color: var(--text-muted); border-color: var(--border-color)" onmouseover="this.style.color='var(--accent)'; this.style.borderColor='var(--accent)'" onmouseout="this.style.color='var(--text-muted)'; this.style.borderColor='var(--border-color)'">90 dias</button>
                        <button type="button" @click="setPreset('year')" class="px-2.5 py-1 text-[11px] font-medium rounded-lg transition-colors border" style="color: var(--text-muted); border-color: var(--border-color)" onmouseover="this.style.color='var(--accent)'; this.style.borderColor='var(--accent)'" onmouseout="this.style.color='var(--text-muted)'; this.style.borderColor='var(--border-color)'">1 ano</button>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-muted)">Funcionários</label>
                        <template x-if="employees.length === 0 && scheduleId">
                            <div class="px-3 py-2 rounded-lg text-xs" style="background-color: var(--bg-main); color: var(--text-muted)">A carregar...</div>
                        </template>
                        <template x-if="employees.length > 0">
                            <div id="employee-list" class="rounded-xl border overflow-hidden" style="border-color: var(--border-color)">
                                <div class="px-3 py-2 flex items-center justify-between border-b" style="border-color: var(--border-color)">
                                    <span class="text-[11px] font-bold uppercase tracking-wide" style="color: var(--text-muted)">Vinculados à escala</span>
                                    <div class="flex items-center gap-3">
                                        <span class="text-[11px] font-semibold" style="color: var(--accent)" x-text="selected.length + ' / ' + employees.length"></span>
                                        <label class="flex items-center gap-1.5 text-[11px] cursor-pointer" style="color: var(--text-muted)">
                                            <input type="checkbox" x-model="selectAll" x-on:change="toggleAll()" class="rounded accent-violet-500">
                                            Todos
                                        </label>
                                    </div>
                                </div>
                                <div class="px-3 py-2 border-b relative" style="border-color: var(--border-color)">
                                    <svg class="w-3.5 h-3.5 absolute left-5 top-1/2 -translate-y-1/2 pointer-events-none" style="color: var(--text-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input type="text" x-model="searchQuery" placeholder="Pesquisar funcionário..." class="form-input !py-1.5 !text-xs !pl-8" aria-label="Pesquisar funcionário">
                                </div>
                                <div class="max-h-44 overflow-y-auto divide-y" style="border-color: var(--border-color)">
                                    <template x-for="emp in filteredEmployees" :key="emp.id">
                                        <label class="flex items-center gap-2 px-3 py-2 text-xs cursor-pointer hover:bg-white/40 dark:hover:bg-white/5 transition-colors">
                                            <input type="checkbox" name="employee_ids[]" :value="emp.id" x-model="selected" class="rounded accent-violet-500">
                                            <span style="color: var(--text-main)" x-text="emp.name"></span>
                                        </label>
                                    </template>
                                    <template x-if="filteredEmployees.length === 0">
                                        <p class="px-3 py-3 text-[11px]" style="color: var(--text-muted)">Nenhum funcionário encontrado.</p>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template x-if="!employees.length">
                            <p class="text-[11px]" style="color: var(--text-muted)">Selecione uma escala para listar os funcionários. Se nenhum for marcado, usa-se a equipa toda da escala.</p>
                        </template>
                    </div>

                    <button type="submit" class="btn-primary w-full" :disabled="loading">
                        <span x-text="loading ? 'A gerar...' : 'Gerar rotação'"></span>
                    </button>
                </form>
            </div>

            @if($rotations->isNotEmpty())
            <div class="glass-card rounded-2xl p-5">
                <h3 class="text-xs font-semibold mb-3 uppercase tracking-wide" style="color: var(--text-muted)">Rotações criadas ({{ $rotations->count() }})</h3>
                <div class="space-y-2 max-h-[420px] overflow-y-auto pr-1">
                    @foreach($rotations as $rotation)
                    <div class="flex items-center justify-between gap-2 px-3 py-2 rounded-xl" style="background-color: var(--bg-main); border: 1px solid var(--border-color)">
                        <div class="min-w-0">
                            <p class="text-xs font-medium truncate" style="color: var(--text-main)">{{ $rotation->name }}</p>
                            <p class="text-[10px] mt-0.5 flex items-center gap-1.5" style="color: var(--text-muted)">
                                <span class="inline-flex items-center gap-0.5">
                                    @php
                                        $p = is_array($rotation->pattern) ? $rotation->pattern : [];
                                        $label = implode('×', array_map(fn($v) => (int) $v, $p));
                                    @endphp
                                    {{ $label ? $label : '—' }}
                                </span>
                                <span>·</span>
                                <span>{{ $rotation->start_date?->format('d/m') ?? '—' }} – {{ $rotation->end_date?->format('d/m/y') ?? '—' }}</span>
                                <span>·</span>
                                <span>{{ $rotation->scheduled_shifts_count ?? 0 }} turnos</span>
                            </p>
                        </div>
                        <form method="POST" action="/rh/rosters/{{ $rotation->id }}/delete" x-data="{ confirm: false }" @submit.prevent="confirm ? $el.submit() : (confirm = true)">
                            <button type="submit" class="p-1.5 rounded-lg transition-colors" style="color: var(--text-muted)" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='var(--text-muted)'" :title="confirm ? 'Clique para confirmar' : 'Excluir rotação'">
                                <svg x-show="!confirm" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                <span x-show="confirm" class="text-[10px] font-bold uppercase tracking-wider">Confirmar</span>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="glass-card rounded-2xl p-5 text-center">
                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--border-color)">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-xs font-medium" style="color: var(--text-muted)">Nenhuma rotação criada ainda</p>
                <p class="text-[11px] mt-1" style="color: var(--text-muted)">Gere a primeira rotação no formulário ao lado.</p>
            </div>
            @endif
        </div>

        <!-- Calendário -->
        <div class="xl:col-span-3">
            <div class="glass-card rounded-2xl p-5">
                <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 text-[11px]" style="color: var(--text-muted)">
                            <span class="w-3 h-3 rounded" style="background-color: #7c3aed"></span> Trabalho
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-[11px]" style="color: var(--text-muted)">
                            <span class="w-3 h-3 rounded" style="background-color: #64748b"></span> Folga
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-[11px]" style="color: var(--text-muted)">
                            <span class="w-3 h-3 rounded border-2" style="border-color: var(--accent); background-color: transparent"></span> Hoje
                        </span>
                    </div>

                    <div class="flex items-center gap-3 flex-wrap">
                        <div class="flex rounded-xl overflow-hidden border" style="border-color: var(--border-color)">
                            <button type="button" @click="setView('calendar')" class="px-3 py-1.5 text-xs font-semibold transition-colors"
                                :class="view === 'calendar' ? 'text-violet-600 dark:text-violet-400' : ''"
                                :style="view === 'calendar' ? 'background-color: var(--accent-soft)' : 'color: var(--text-muted); background-color: transparent'">Calendário</button>
                            <button type="button" @click="setView('summary')" class="px-3 py-1.5 text-xs font-semibold transition-colors"
                                :class="view === 'summary' ? 'text-violet-600 dark:text-violet-400' : ''"
                                :style="view === 'summary' ? 'background-color: var(--accent-soft)' : 'color: var(--text-muted); background-color: transparent'">Resumo</button>
                        </div>
                        <label class="flex items-center gap-2 text-xs" style="color: var(--text-muted)">
                            <span class="font-semibold">Filtrar por rotação</span>
                            <select x-model="rotationFilter" x-on:change="onFilterChange()" class="form-input !py-1.5 !text-xs !w-auto" style="min-width: 180px;">
                                <option value="">Todas</option>
                                @foreach($rotations as $rotation)
                                <option value="{{ $rotation->id }}">{{ $rotation->name }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                </div>

                <!-- Calendário -->
                <div id="roster-calendar" x-show="view === 'calendar'"></div>

                <!-- Tabela resumo diário -->
                <div x-show="view === 'summary'">
                    <template x-if="summaryLoading">
                        <div class="px-3 py-8 text-center text-xs" style="color: var(--text-muted)">A carregar resumo...</div>
                    </template>
                    <template x-if="!summaryLoading && summaryRows.length">
                        <div class="overflow-x-auto">
                            <table class="app-table">
                                <thead>
                                    <tr>
                                        <th>Dia</th>
                                        <th class="text-center">Em trabalho</th>
                                        <th class="text-center">De folga</th>
                                        <th class="text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="row in summaryRows" :key="row.date">
                                        <tr :class="row.isToday ? 'font-semibold' : ''" :style="row.isToday ? 'background-color: var(--accent-soft)' : ''">
                                            <td>
                                                <span style="color: var(--text-main)" x-text="row.label"></span>
                                                <span class="ml-1.5 text-[10px] uppercase tracking-wide" style="color: var(--text-muted)" x-text="row.weekday"></span>
                                                <span x-show="row.isToday" class="ml-1.5 text-[10px] font-bold uppercase tracking-wide" style="color: var(--accent)">hoje</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold" style="color: #7c3aed">
                                                    <span class="w-2 h-2 rounded-full" style="background-color: #7c3aed"></span>
                                                    <span x-text="row.work"></span>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold" style="color: #64748b">
                                                    <span class="w-2 h-2 rounded-full" style="background-color: #64748b"></span>
                                                    <span x-text="row.off"></span>
                                                </span>
                                            </td>
                                            <td class="text-center text-xs font-bold" style="color: var(--text-main)" x-text="row.work + row.off"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                    <template x-if="!summaryLoading && !summaryRows.length">
                        <div class="px-3 py-10 text-center">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--border-color)">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-xs font-medium" style="color: var(--text-muted)">Sem turnos para o período selecionado</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('head-scripts')
<script>
function rosterPage() {
    return {
        scheduleId: '',
        employees: [],
        selected: [],
        selectAll: false,
        searchQuery: '',
        loading: false,
        workDays: 6,
        offDays: 2,
        rotationFilter: '',
        view: 'calendar',
        summaryRows: [],
        summaryLoading: false,
        todayStr: (() => new Date().toISOString().slice(0, 10))(),
        startDate: (() => {
            const d = new Date();
            return d.toISOString().slice(0, 7) + '-01';
        })(),
        endDate: (() => {
            const d = new Date();
            return new Date(d.getFullYear(), d.getMonth() + 1, 0).toISOString().slice(0, 10);
        })(),
        calendar: null,

        get cycle() {
            const w = Math.max(1, Math.min(60, this.workDays || 0));
            const o = Math.max(1, Math.min(60, this.offDays || 0));
            return [...Array(w).fill('T'), ...Array(o).fill('F')];
        },

        get filteredEmployees() {
            const q = this.searchQuery.trim().toLowerCase();
            if (!q) return this.employees;
            return this.employees.filter(e => (e.name || '').toLowerCase().includes(q));
        },

        initCalendar() {
            if (typeof FullCalendar === 'undefined') return;
            this.calendar = new FullCalendar.Calendar(document.getElementById('roster-calendar'), {
                plugins: [FullCalendar.dayGridPlugin, FullCalendar.interactionPlugin],
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek'
                },
                height: 'auto',
                dayMaxEvents: 6,
                displayEventTime: false,
                events: (info, success, failure) => {
                    const params = new URLSearchParams({ start: info.startStr, end: info.endStr });
                    if (this.rotationFilter) params.set('rotation_id', this.rotationFilter);
                    fetch('/rh/rosters/events?' + params.toString())
                        .then(r => r.json())
                        .then(success)
                        .catch(failure);
                },
                eventDidMount(info) {
                    const el = info.el;
                    el.style.fontSize = '11px';
                    if (info.event.extendedProps.classification === 'FOLGA') {
                        el.style.opacity = '0.75';
                    }
                    el.title = info.event.title
                        + (info.event.extendedProps.work_schedule ? '\nTurno: ' + info.event.extendedProps.work_schedule : '')
                        + (info.event.extendedProps.check_in ? '\nEntrada: ' + info.event.extendedProps.check_in : '')
                        + (info.event.extendedProps.check_out ? '\nSaída: ' + info.event.extendedProps.check_out : '')
                        + (info.event.extendedProps.rotation ? '\nRotação: ' + info.event.extendedProps.rotation : '');
                },
                eventClick(info) {
                    if (info.event.url) {
                        info.jsEvent.preventDefault();
                    }
                }
            });
            this.calendar.render();
        },

        loadEmployees() {
            this.employees = [];
            this.selected = [];
            this.selectAll = false;
            this.searchQuery = '';
            if (!this.scheduleId) return;

            fetch('/rh/rosters/schedules/' + this.scheduleId + '/employees')
                .then(r => r.json())
                .then(data => {
                    this.employees = data.employees || [];
                    this.selected = this.employees.map(e => e.id);
                    this.selectAll = this.employees.length > 0;
                })
                .catch(() => { this.employees = []; });
        },

        toggleAll() {
            if (this.selectAll) {
                this.selected = this.employees.map(e => e.id);
            } else {
                this.selected = [];
            }
        },

        setPreset(type) {
            const now = new Date();
            if (type === 'month') {
                this.startDate = now.toISOString().slice(0, 7) + '-01';
                this.endDate = new Date(now.getFullYear(), now.getMonth() + 1, 0).toISOString().slice(0, 10);
            } else if (type === 'quarter') {
                this.startDate = now.toISOString().slice(0, 10);
                this.endDate = new Date(now.getFullYear(), now.getMonth() + 3, 0).toISOString().slice(0, 10);
            } else if (type === 'year') {
                this.startDate = now.toISOString().slice(0, 10);
                this.endDate = new Date(now.getFullYear() + 1, now.getMonth(), 0).toISOString().slice(0, 10);
            }
        },

        setView(mode) {
            this.view = mode;
            if (mode === 'summary') this.loadSummary();
        },

        onFilterChange() {
            if (this.calendar) this.calendar.refetchEvents();
            if (this.view === 'summary') this.loadSummary();
        },

        loadSummary() {
            if (!this.calendar) return;
            this.summaryLoading = true;
            this.summaryRows = [];

            const view = this.calendar.getCurrentData().currentRange;
            const start = view.start.toISOString().slice(0, 10);
            const end = new Date(view.end.getTime() - 86400000).toISOString().slice(0, 10);

            const params = new URLSearchParams({ start, end });
            if (this.rotationFilter) params.set('rotation_id', this.rotationFilter);

            fetch('/rh/rosters/events?' + params.toString())
                .then(r => r.json())
                .then(events => {
                    const grouped = {};
                    events.forEach(ev => {
                        const key = ev.start.slice(0, 10);
                        grouped[key] = grouped[key] || { work: 0, off: 0 };
                        if (ev.extendedProps.classification === 'TRABALHO') grouped[key].work++;
                        else grouped[key].off++;
                    });

                    const rows = [];
                    let cursor = new Date(start);
                    const last = new Date(end);
                    while (cursor <= last) {
                        const key = cursor.toISOString().slice(0, 10);
                        const r = grouped[key] || { work: 0, off: 0 };
                        rows.push({
                            date: key,
                            label: key.slice(8, 10) + '/' + key.slice(5, 7),
                            weekday: cursor.toLocaleDateString('pt-BR', { weekday: 'short' }).replace('.', ''),
                            work: r.work,
                            off: r.off,
                            isToday: key === this.todayStr
                        });
                        cursor.setDate(cursor.getDate() + 1);
                    }
                    this.summaryRows = rows;
                })
                .catch(() => { this.summaryRows = []; })
                .finally(() => { this.summaryLoading = false; });
        },

        async init() {}
    };
}
</script>
@endsection