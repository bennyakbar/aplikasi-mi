<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buku Besar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('accounting.ledger') }}" method="GET" class="flex flex-wrap items-end gap-4">
                        <div class="flex-grow">
                            <label class="block text-sm font-medium text-gray-700">Pilih Akun</label>
                            <select name="account_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Pilih Akun --</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ $selectedAccount && $selectedAccount->id == $account->id ? 'selected' : '' }}>
                                        {{ $account->code }} - {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <input type="date" name="start_date" value="{{ $startDate }}"
                                class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                            <input type="date" name="end_date" value="{{ $endDate }}"
                                class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Tampilkan
                        </button>
                    </form>
                </div>
            </div>

            @if($selectedAccount)
                <!-- Ledger -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                            <h3 class="font-semibold text-lg">{{ $selectedAccount->code }} - {{ $selectedAccount->name }}
                            </h3>
                            <p class="text-gray-600">Tipe: {{ $selectedAccount->type_name }}</p>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Jurnal
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Kredit</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($ledgerData as $row)
                                    <tr>
                                        <td class="px-4 py-3">{{ $row['date']->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3 font-mono text-blue-600">{{ $row['entry_number'] }}</td>
                                        <td class="px-4 py-3">{{ $row['description'] }}</td>
                                        <td class="px-4 py-3 text-right font-mono">
                                            {{ $row['debit'] > 0 ? 'Rp ' . number_format($row['debit'], 0, ',', '.') : '' }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-mono">
                                            {{ $row['credit'] > 0 ? 'Rp ' . number_format($row['credit'], 0, ',', '.') : '' }}
                                        </td>
                                        <td
                                            class="px-4 py-3 text-right font-mono font-semibold {{ $row['balance'] < 0 ? 'text-red-600' : '' }}">
                                            Rp
                                            {{ number_format(abs($row['balance']), 0, ',', '.') }}{{ $row['balance'] < 0 ? ' (K)' : '' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            Tidak ada transaksi untuk akun ini di periode yang dipilih
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        Pilih akun terlebih dahulu untuk melihat buku besar
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>