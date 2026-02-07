<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Step 1: Select Student -->
                    <form action="{{ route('payments.create') }}" method="GET" class="mb-6 pb-6 border-b">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div class="md:col-span-2">
                                <label for="student_select" class="block text-sm font-medium text-gray-700">Pilih Siswa</label>
                                <select name="student_id" id="student_select" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                                    <option value="">-- Pilih Siswa --</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ $selectedStudent && $selectedStudent->id == $student->id ? 'selected' : '' }}>
                                            {{ $student->nis }} - {{ $student->name }} ({{ $student->category->code }}, Kelas {{ $student->grade }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <button type="submit" class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Pilih
                                </button>
                            </div>
                        </div>
                    </form>

                    @if($selectedStudent)
                        <!-- Step 2: Payment Form -->
                        <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                            <h3 class="font-semibold text-lg">Info Siswa</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-2">
                                <div><span class="text-gray-500">NIS:</span> {{ $selectedStudent->nis }}</div>
                                <div><span class="text-gray-500">Nama:</span> {{ $selectedStudent->name }}</div>
                                <div><span class="text-gray-500">Kategori:</span> {{ $selectedStudent->category->name }}</div>
                                <div><span class="text-gray-500">Kelas:</span> {{ $selectedStudent->grade }}</div>
                            </div>
                        </div>

                        <form action="{{ route('payments.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div>
                                    <label for="payment_date" class="block text-sm font-medium text-gray-700">Tanggal Pembayaran</label>
                                    <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>
                                <div>
                                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                                    <select name="payment_method" id="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                                        <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                                    <input type="text" name="notes" id="notes" value="{{ old('notes') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            <h3 class="font-semibold text-lg mb-4">Item Pembayaran</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Biaya</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarif</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bulan</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tahun</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Bayar</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($fees as $index => $fee)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    {{ $fee->name }}
                                                    <input type="hidden" name="items[{{ $index }}][fee_id]" value="{{ $fee->id }}">
                                                </td>
                                                <td class="px-4 py-3">
                                                    @if($fee->fee_type === 'monthly')
                                                        <span class="px-2 text-xs rounded-full bg-purple-100 text-purple-800">Bulanan</span>
                                                    @elseif($fee->fee_type === 'yearly')
                                                        <span class="px-2 text-xs rounded-full bg-orange-100 text-orange-800">Tahunan</span>
                                                    @else
                                                        <span class="px-2 text-xs rounded-full bg-gray-100 text-gray-800">Sekali</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 font-mono">Rp {{ number_format($fee->amount, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3">
                                                    @if($fee->fee_type === 'monthly')
                                                        <select name="items[{{ $index }}][period_month]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                            @for($m = 1; $m <= 12; $m++)
                                                                <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                                                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                        <input type="hidden" name="items[{{ $index }}][period_month]" value="">
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" name="items[{{ $index }}][period_year]" value="{{ date('Y') }}" class="block w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" name="items[{{ $index }}][amount]" value="0" min="0" step="1000" class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono item-amount" data-rate="{{ $fee->amount }}">
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-4 py-3 text-center text-gray-500">Tidak ada tarif untuk kategori ini</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="5" class="px-4 py-3 text-right font-semibold">Total:</td>
                                            <td class="px-4 py-3 font-mono font-bold text-lg" id="totalAmount">Rp 0</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <a href="{{ route('payments.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Simpan Pembayaran
                                </button>
                            </div>
                        </form>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const amountInputs = document.querySelectorAll('.item-amount');
                                const totalEl = document.getElementById('totalAmount');

                                function updateTotal() {
                                    let total = 0;
                                    amountInputs.forEach(input => {
                                        total += parseFloat(input.value) || 0;
                                    });
                                    totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
                                }

                                amountInputs.forEach(input => {
                                    input.addEventListener('input', updateTotal);
                                    // Double-click to fill with rate
                                    input.addEventListener('dblclick', function() {
                                        this.value = this.dataset.rate;
                                        updateTotal();
                                    });
                                });

                                updateTotal();
                            });
                        </script>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>Pilih siswa terlebih dahulu untuk input pembayaran</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
