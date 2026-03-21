@extends('layout.app')

@section('title', 'Novo Lançamento')
@section('page-title', 'Contabilidade')
@section('page-subtitle', 'Registrar Lançamento Diário')

@section('content')
<div class="max-w-5xl mx-auto" x-data="journalForm()">
    <form action="/accounting/journal" method="POST" class="space-y-6" onsubmit="console.log('Submitting form data...', new FormData(this))">
        <!-- Header Info -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-6">Informações Gerais</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Data</label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Descrição / Histórico</label>
                    <input type="text" name="description" required placeholder="Ex: Pagamento de Fornecedor X"
                           class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Referência / Documento</label>
                    <input type="text" name="reference" placeholder="Ex: FT 2024/001"
                           class="w-full px-4 py-3 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
            </div>
        </div>

        <!-- Ledger Items -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Partida Dobrada</h3>
            </div>
            
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Conta</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase w-48">Valor</th>
                        <th class="px-6 py-4 w-16"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <template x-for="(item, index) in items" :key="index">
                        <tr>
                            <td class="px-6 py-4">
                                <select :name="'items[' + index + '][account_id]'" required
                                        class="w-full px-3 py-2 rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 focus:ring-indigo-500">
                                    <option value="">Selecione a conta...</option>
                                    @foreach($accounts as $account)
                                        @if($account->is_analytic)
                                            <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-6 py-4">
                                <select :name="'items[' + index + '][type]'" required x-model="item.type"
                                        class="w-full px-3 py-2 rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 focus:ring-indigo-500">
                                    <option value="debit">Débito (+)</option>
                                    <option value="credit">Crédito (-)</option>
                                </select>
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" :name="'items[' + index + '][amount]'" required step="0.01" min="0" x-model.number="item.amount"
                                       class="w-full px-3 py-2 rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 focus:ring-indigo-500 text-right">
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" @click="removeItem(index)" class="text-red-400 hover:text-red-700 transition-colors p-1 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20" title="Remover Linha">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 font-bold">
                        <td class="px-6 py-4" colspan="2">Total Geral</td>
                        <td class="px-6 py-4 text-right" :class="isBalanced ? 'text-green-600 font-bold' : 'text-red-600 font-bold text-sm'">
                            <div class="flex flex-col space-y-1">
                                <div class="flex justify-between items-center gap-4">
                                    <span class="text-[10px] text-gray-500 uppercase">Déb:</span>
                                    <span x-text="formatCurrency(totalDebit)"></span>
                                </div>
                                <div class="flex justify-between items-center gap-4 border-b border-gray-300 dark:border-gray-500 pb-1">
                                    <span class="text-[10px] text-gray-500 uppercase">Créd:</span>
                                    <span x-text="formatCurrency(totalCredit)"></span>
                                </div>
                                <div class="flex justify-between items-center gap-4 pt-1">
                                    <span class="text-[10px] text-gray-400 uppercase">Dif:</span>
                                    <span x-text="formatCurrency(Math.abs(totalDebit - totalCredit))"></span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button type="button" @click="addItem()" class="p-2.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-800 transition-all shadow-sm" title="Adicionar Linha">
                                <i data-lucide="plus" class="w-5 h-5"></i>
                            </button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

            <div class="mt-8 flex flex-col items-end gap-2">
                <div class="flex gap-4 text-[11px] font-bold uppercase tracking-wider">
                    <div class="flex items-center gap-1.5" :class="isBalanced ? 'text-green-500' : 'text-gray-400 opacity-50'">
                        <i :data-lucide="isBalanced ? 'check-circle-2' : 'circle'" class="w-3.5 h-3.5"></i>
                        <span>Balançado</span>
                    </div>
                    <div class="flex items-center gap-1.5" :class="items.length >= 2 ? 'text-green-500' : 'text-gray-400 opacity-50'">
                        <i :data-lucide="items.length >= 2 ? 'check-circle-2' : 'circle'" class="w-3.5 h-3.5"></i>
                        <span>Mín. 2 Linhas</span>
                    </div>
                </div>
                
                <div class="flex justify-end items-center gap-4">
                    <a href="/accounting/journal" class="flex items-center gap-2 px-6 py-3 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 font-semibold hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                        <i data-lucide="x-circle" class="w-5 h-5"></i>
                        <span>Cancelar</span>
                    </a>
                    <button type="submit" :disabled="!isBalanced || items.length < 2"
                            class="flex items-center gap-2 px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-indigo-100 dark:shadow-none hover:bg-indigo-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        <span>Salvar Lançamento</span>
                    </button>
                </div>
            </div>
    </form>
</div>

<script>
function journalForm() {
    return {
        items: [
            { account_id: '', type: 'debit', amount: 0 },
            { account_id: '', type: 'credit', amount: 0 }
        ],
        addItem() {
            this.items.push({ account_id: '', type: 'debit', amount: 0 });
            this.$nextTick(() => {
                if (window.createIcons) window.createIcons({ icons: window.lucideIcons });
            });
        },
        removeItem(index) {
            if (this.items.length > 2) {
                this.items.splice(index, 1);
            }
        },
        get totalDebit() {
            return this.items.reduce((sum, item) => item.type === 'debit' ? sum + (Number(item.amount) || 0) : sum, 0);
        },
        get totalCredit() {
            return this.items.reduce((sum, item) => item.type === 'credit' ? sum + (Number(item.amount) || 0) : sum, 0);
        },
        get isBalanced() {
            const diff = Math.abs(this.totalDebit - this.totalCredit);
            return diff < 0.01 && this.totalDebit > 0;
        },
        formatCurrency(value) {
            try {
                return new Intl.NumberFormat('pt-AO', { 
                    style: 'currency', 
                    currency: 'AOA' 
                }).format(value);
            } catch (e) {
                return 'Kz ' + Number(value).toFixed(2);
            }
        }
    }
}
</script>
@endsection
