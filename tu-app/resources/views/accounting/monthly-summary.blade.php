<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rekap Bulanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Year Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('accounting.monthly-summary') }}" method="GET" class="flex items-end gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tahun</label>
                            <select name="year"
                                class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @for($y = now()->year; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Tampilkan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Monthly Summary Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-lg mb-4">Rekap Keuangan Tahun {{ $year }}</h3>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bulan</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pendapatan
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Beban</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Laba/Rugi
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo Kas
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                $totalRevenue = 0;
                                $totalExpense = 0;
                            @endphp
                            @foreach($summaries as $month => $summary)
                                @php
                                    $totalRevenue += $summary['revenue'];
                                    $totalExpense += $summary['expense'];
                                @endphp
                                <tr class="{{ $month == now()->month && $year == now()->year ? 'bg-yellow-50' : '' }}">
                                    <td class="px-6 py-4 font-medium">{{ $months[$month] }}</td>
                                    <td class="px-6 py-4 text-right font-mono text-green-600">
                                        Rp {{ number_format($summary['revenue'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono text-red-600">
                                        Rp {{ number_format($summary['expense'], 0, ',', '.') }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-right font-mono font-semibold {{ $summary['net_income'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        Rp {{ number_format($summary['net_income'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono">
                                        Rp {{ number_format($summary['cash_balance'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100">
                            <tr class="font-bold">
                                <td class="px-6 py-4">TOTAL</td>
                                <td class="px-6 py-4 text-right font-mono text-green-600">
                                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right font-mono text-red-600">
                                    Rp {{ number_format($totalExpense, 0, ',', '.') }}
                                </td>
                                <td
                                    class="px-6 py-4 text-right font-mono {{ $totalRevenue - $totalExpense >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Rp {{ number_format($totalRevenue - $totalExpense, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>