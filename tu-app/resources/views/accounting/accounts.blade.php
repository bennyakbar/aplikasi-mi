<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Akun (Chart of Accounts)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Akun
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Parent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($accounts as $account)
                                <tr class="{{ $account->parent_id ? '' : 'bg-gray-50 font-semibold' }}">
                                    <td class="px-6 py-3 font-mono">{{ $account->code }}</td>
                                    <td class="px-6 py-3 {{ $account->parent_id ? 'pl-12' : '' }}">
                                        {{ $account->name }}
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $account->type === 'asset' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $account->type === 'liability' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $account->type === 'equity' ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ $account->type === 'revenue' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $account->type === 'expense' ? 'bg-orange-100 text-orange-800' : '' }}
                                            ">
                                            {{ $account->type_name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-gray-500">{{ $account->parent?->name ?? '-' }}</td>
                                    <td class="px-6 py-3">
                                        @if($account->is_active)
                                            <span class="text-green-600">Aktif</span>
                                        @else
                                            <span class="text-red-600">Nonaktif</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>