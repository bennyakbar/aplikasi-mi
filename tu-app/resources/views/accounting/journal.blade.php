<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jurnal Umum') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('accounting.journal') }}" method="GET" class="flex items-end gap-4">
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
                            Filter
                        </button>
                    </form>
                </div>
            </div>

            <!-- Journal Entries -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @forelse($entries as $entry)
                        <div class="mb-6 border rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-3 flex justify-between items-center">
                                <div>
                                    <span class="font-mono font-semibold text-blue-600">{{ $entry->entry_number }}</span>
                                    <span class="text-gray-500 ml-4">{{ $entry->entry_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="text-sm text-gray-500">
                                    @if($entry->payment)
                                        <a href="{{ route('payments.show', $entry->payment) }}"
                                            class="text-blue-600 hover:underline">
                                            {{ $entry->payment->receipt_number }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="px-4 py-2 bg-gray-100 text-sm text-gray-600">
                                {{ $entry->description }}
                            </div>
                            <table class="min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Kode</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Akun</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Debit</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Kredit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($entry->lines as $line)
                                        <tr class="border-t">
                                            <td class="px-4 py-2 font-mono text-sm">{{ $line->account->code }}</td>
                                            <td class="px-4 py-2">{{ $line->account->name }}</td>
                                            <td class="px-4 py-2 text-right font-mono">
                                                {{ $line->debit > 0 ? 'Rp ' . number_format($line->debit, 0, ',', '.') : '' }}
                                            </td>
                                            <td class="px-4 py-2 text-right font-mono">
                                                {{ $line->credit > 0 ? 'Rp ' . number_format($line->credit, 0, ',', '.') : '' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-100">
                                    <tr>
                                        <td colspan="2" class="px-4 py-2 font-semibold">Total</td>
                                        <td class="px-4 py-2 text-right font-mono font-semibold">Rp
                                            {{ number_format($entry->total_debit, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 text-right font-mono font-semibold">Rp
                                            {{ number_format($entry->total_credit, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            Tidak ada jurnal untuk periode ini
                        </div>
                    @endforelse

                    <div class="mt-4">
                        {{ $entries->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>