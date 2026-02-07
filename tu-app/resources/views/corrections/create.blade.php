<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Request Koreksi Pembayaran
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Payment Info -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold mb-2">Informasi Pembayaran</h3>
                        <dl class="grid grid-cols-2 gap-2 text-sm">
                            <dt class="text-gray-500">No. Kwitansi:</dt>
                            <dd class="font-mono">{{ $payment->receipt_number }}</dd>
                            <dt class="text-gray-500">Siswa:</dt>
                            <dd>{{ $payment->student->name }} ({{ $payment->student->nis }})</dd>
                            <dt class="text-gray-500">Tanggal:</dt>
                            <dd>{{ $payment->payment_date->format('d/m/Y') }}</dd>
                            <dt class="text-gray-500">Total:</dt>
                            <dd class="font-semibold">Rp {{ number_format($payment->total_amount, 0, ',', '.') }}</dd>
                        </dl>
                    </div>

                    @if($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                            <ul class="text-red-700 text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('corrections.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_id" value="{{ $payment->id }}">

                        <!-- Correction Type -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Koreksi
                            </label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="void" class="mr-2" {{ old('type') === 'void' ? 'checked' : '' }}>
                                    <span class="text-red-600 font-medium">Pembatalan</span>
                                    <span class="text-sm text-gray-500 ml-2">(Hapus pembayaran ini)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="edit" class="mr-2" {{ old('type') === 'edit' ? 'checked' : '' }}>
                                    <span class="text-blue-600 font-medium">Perubahan</span>
                                    <span class="text-sm text-gray-500 ml-2">(Ubah nominal pembayaran)</span>
                                </label>
                            </div>
                        </div>

                        <!-- New Amount (for edit) -->
                        <div class="mb-4" id="new-amount-field" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nominal Baru
                            </label>
                            <input type="number" name="new_amount" value="{{ old('new_amount') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Masukkan nominal baru">
                        </div>

                        <!-- Reason -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Alasan Koreksi <span class="text-red-500">*</span>
                            </label>
                            <textarea name="reason" rows="4"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Jelaskan alasan mengapa koreksi diperlukan (minimal 10 karakter)">{{ old('reason') }}</textarea>
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-between">
                            <a href="{{ route('payments.show', $payment) }}"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Batal
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Kirim Permintaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('input[name="type"]').forEach(radio => {
            radio.addEventListener('change', function () {
                const newAmountField = document.getElementById('new-amount-field');
                newAmountField.style.display = this.value === 'edit' ? 'block' : 'none';
            });
        });
        // Initialize on page load
        const checkedRadio = document.querySelector('input[name="type"]:checked');
        if (checkedRadio && checkedRadio.value === 'edit') {
            document.getElementById('new-amount-field').style.display = 'block';
        }
    </script>
</x-app-layout>