<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Neraca Saldo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Date Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('accounting.trial-balance') }}" method="GET" class="flex items-end gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Per Tanggal</label>
                            <input type="date" name="as_of" value="{{ $asOfDate }}"
                                class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Tampilkan
                        </button>
                        <button type="button" onclick="window.print()"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            🖨️ Cetak
                        </button>
                    </form>
                </div>
            </div>

            <!-- Trial Balance -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-center mb-6">
                        <h3 class="font-bold text-xl">NERACA SALDO</h3>
                        <p class="text-gray-600">Per {{ \Carbon\Carbon::parse($asOfDate)->format('d F Y') }}</p>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Akun
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Kredit</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($trialBalance['items'] as $item)
                                <tr>
                                    <td class="px-6 py-3 font-mono">{{ $item['account']->code }}</td>
                                    <td class="px-6 py-3">{{ $item['account']->name }}</td>
                                    <td class="px-6 py-3 text-right font-mono">
                                        {{ $item['debit'] > 0 ? 'Rp ' . number_format($item['debit'], 0, ',', '.') : '' }}
                                    </td>
                                    <td class="px-6 py-3 text-right font-mono">
                                        {{ $item['credit'] > 0 ? 'Rp ' . number_format($item['credit'], 0, ',', '.') : '' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100">
                            <tr class="font-bold">
                                <td colspan="2" class="px-6 py-4">TOTAL</td>
                                <td class="px-6 py-4 text-right font-mono">
                                    Rp {{ number_format($trialBalance['total_debit'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right font-mono">
                                    Rp {{ number_format($trialBalance['total_credit'], 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    @if(!$trialBalance['is_balanced'])
                        <div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            ⚠️ Neraca tidak seimbang! Selisih: Rp
                            {{ number_format(abs($trialBalance['total_debit'] - $trialBalance['total_credit']), 0, ',', '.') }}
                        </div>
                    @else
                        <div class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            ✅ Neraca seimbang
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>