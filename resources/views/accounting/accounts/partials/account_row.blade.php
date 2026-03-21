<tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
    <td class="px-5 py-4 border-b border-gray-200 bg-white dark:bg-gray-800 text-sm">
        <div class="flex items-center">
            <p class="text-gray-900 dark:text-gray-100 whitespace-no-wrap" style="padding-left: {{ $level * 20 }}px">
                <strong>{{ $account->code }}</strong>
            </p>
        </div>
    </td>
    <td class="px-5 py-4 border-b border-gray-200 bg-white dark:bg-gray-800 text-sm">
        <p class="text-gray-900 dark:text-gray-100 whitespace-no-wrap" style="padding-left: {{ $level * 20 }}px">
            {{ $account->name }}
        </p>
    </td>
    <td class="px-5 py-4 border-b border-gray-200 bg-white dark:bg-gray-800 text-sm">
        <span class="relative inline-block px-3 py-1 font-semibold text-gray-900 leading-tight">
            <span aria-hidden class="absolute inset-0 opacity-50 rounded-full {{ $account->type === 'asset' ? 'bg-green-200' : ($account->type === 'liability' ? 'bg-red-200' : 'bg-blue-200') }}"></span>
            <span class="relative text-[10px] uppercase font-bold">{{ $account->type }}</span>
        </span>
    </td>
    <td class="px-5 py-4 border-b border-gray-200 bg-white dark:bg-gray-800 text-sm">
        @if($account->is_analytic)
            <span class="text-green-600 dark:text-green-400 font-bold">Sim</span>
        @else
            <span class="text-gray-400 dark:text-gray-500">Sintética</span>
        @endif
    </td>
    <td class="px-5 py-4 border-b border-gray-200 bg-white dark:bg-gray-800 text-sm">
        <div class="flex items-center justify-end gap-5 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
            <a href="/accounting/accounts/{{ $account->id }}/edit" 
               class="p-1.5 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-all"
               title="Editar">
                <i data-lucide="pencil" class="w-4 h-4"></i>
            </a>
            <form action="/accounting/accounts/{{ $account->id }}/delete" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta conta?');" class="inline">
                <button type="submit" 
                        class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10 rounded-lg transition-all"
                        title="Excluir">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </td>
</tr>

@if($account->children)
    @foreach($account->children as $child)
        @include('accounting.accounts.partials.account_row', ['account' => $child, 'level' => $level + 1])
    @endforeach
@endif
