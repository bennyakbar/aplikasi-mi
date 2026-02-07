<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Pembayaran') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('payments.print', $payment) }}" target="_blank"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    🖨️ Cetak Kwitansi
                </a>
                <a href="{{ route('payments.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Receipt Header -->
                    <div class="text-center mb-6 pb-6 border-b">
                        <h1 class="text-2xl font-bold text-gray-800">KWITANSI PEMBAYARAN</h1>
                        <p class="text-gray-600">No. {{ $payment->receipt_number }}</p>
                    </div>

                    <!-- Payment Info -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-2">Informasi Siswa</h3>
                            <table class="text-sm">
                                <tr>
                                    <td class="text-gray-500 pr-4">NIS</td>
                                    <td>: {{ $payment->student->nis }}</td>
                                </tr>
                                <tr>
                                    <td class="text-gray-500 pr-4">Nama</td>
                                    <td>: {{ $payment->student->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-gray-500 pr-4">Kategori</td>
                                    <td>: {{ $payment->student->category->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-gray-500 pr-4">Kelas</td>
                                    <td>: {{ $payment->student->grade }}</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-2">Informasi Pembayaran</h3>
                            <table class="text-sm">
                                <tr>
                                    <td class="text-gray-500 pr-4">Tanggal</td>
                                    <td>: {{ $payment->payment_date->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-gray-500 pr-4">Metode</td>
                                    <td>: {{ $payment->payment_method === 'cash' ? 'Tunai' : 'Transfer' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-gray-500 pr-4">Petugas</td>
                                    <td>: {{ $payment->user->name }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Payment Items -->
                    <h3 class="font-semibold text-gray-700 mb-2">Rincian Pembayaran</h3>
                    <table class="min-w-full divide-y divide-gray-200 mb-6">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Biaya
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($payment->items as $index => $item)
                                <tr>
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">{{ $item->fee->name }}</td>
                                    <td class="px-4 py-3">{{ $item->formatted_period }}</td>
                                    <td class="px-4 py-3 text-right font-mono">Rp
                                        {{ number_format($item->amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right font-semibold">Total Pembayaran:</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-lg">Rp
                                    {{ number_format($payment->paid_amount, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    @if($payment->notes)
                        <div class="mt-4 p-4 bg-yellow-50 rounded">
                            <h4 class="font-semibold text-gray-700">Catatan:</h4>
                            <p class="text-gray-600">{{ $payment->notes }}</p>
                        </div>
                    @endif

                    <!-- Footer -->
                    <div class="mt-8 pt-6 border-t text-sm text-gray-500 text-center">
                        <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>