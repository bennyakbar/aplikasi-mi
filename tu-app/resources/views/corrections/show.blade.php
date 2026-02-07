<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Koreksi #{{ $correction->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                    <ul class="text-red-700 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <!-- Status Banner -->
                    <div class="mb-6 p-4 rounded-lg 
                        {{ $correction->status === 'pending' ? 'bg-yellow-50 border border-yellow-200' : '' }}
                        {{ $correction->status === 'approved' ? 'bg-green-50 border border-green-200' : '' }}
                        {{ $correction->status === 'rejected' ? 'bg-red-50 border border-red-200' : '' }}
                    ">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-semibold">Status: {{ $correction->status_label }}</span>
                                @if($correction->reviewer)
                                    <span class="text-sm text-gray-500 ml-2">
                                        oleh {{ $correction->reviewer->name }} pada
                                        {{ $correction->reviewed_at->format('d/m/Y H:i') }}
                                    </span>
                                @endif
                            </div>
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                {{ $correction->type === 'void' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}
                            ">
                                {{ $correction->type_label }}
                            </span>
                        </div>
                        @if($correction->rejection_reason)
                            <div class="mt-2 text-sm text-red-700">
                                <strong>Alasan Penolakan:</strong> {{ $correction->rejection_reason }}
                            </div>
                        @endif
                    </div>

                    <!-- Payment Info -->
                    <h3 class="font-semibold text-lg mb-4">Informasi Pembayaran</h3>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <dt class="text-sm text-gray-500">No. Kwitansi</dt>
                            <dd class="font-mono">{{ $correction->payment->receipt_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Tanggal Pembayaran</dt>
                            <dd>{{ $correction->payment->payment_date->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Siswa</dt>
                            <dd>{{ $correction->payment->student->name }} ({{ $correction->payment->student->nis }})
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Metode Pembayaran</dt>
                            <dd class="capitalize">{{ $correction->payment->payment_method }}</dd>
                        </div>
                    </div>

                    <!-- Old vs New Values -->
                    @if($correction->type === 'edit' && $correction->new_values)
                        <h3 class="font-semibold text-lg mb-4">Perbandingan Nilai</h3>
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="p-4 bg-red-50 rounded-lg">
                                <h4 class="font-medium text-red-800 mb-2">Nilai Lama</h4>
                                <p class="text-2xl font-bold text-red-600">
                                    Rp {{ number_format($correction->old_values['total_amount'] ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="p-4 bg-green-50 rounded-lg">
                                <h4 class="font-medium text-green-800 mb-2">Nilai Baru</h4>
                                <p class="text-2xl font-bold text-green-600">
                                    Rp {{ number_format($correction->new_values['total_amount'] ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-medium mb-2">Nilai yang akan dibatalkan</h4>
                            <p class="text-2xl font-bold text-gray-800">
                                Rp {{ number_format($correction->old_values['total_amount'] ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                    @endif

                    <!-- Reason -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-lg mb-2">Alasan Koreksi</h3>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p>{{ $correction->reason }}</p>
                            <p class="text-sm text-gray-500 mt-2">
                                Diajukan oleh: {{ $correction->requester->name }} pada
                                {{ $correction->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons (for Bendahara) -->
                    @if($correction->isPending())
                        @can('approve corrections')
                            <div class="border-t pt-6">
                                <h3 class="font-semibold text-lg mb-4">Tindakan</h3>
                                <div class="flex space-x-4">
                                    <form action="{{ route('corrections.approve', $correction) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menyetujui koreksi ini?')">
                                        @csrf
                                        <button type="submit"
                                            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                            ✓ Setujui
                                        </button>
                                    </form>

                                    <button type="button"
                                        onclick="document.getElementById('reject-form').classList.toggle('hidden')"
                                        class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                        ✕ Tolak
                                    </button>
                                </div>

                                <!-- Rejection Form -->
                                <form id="reject-form" action="{{ route('corrections.reject', $correction) }}" method="POST"
                                    class="hidden mt-4">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Alasan Penolakan
                                        </label>
                                        <textarea name="rejection_reason" rows="3" required
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Masukkan alasan penolakan (minimal 10 karakter)"></textarea>
                                    </div>
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                        Konfirmasi Tolak
                                    </button>
                                </form>
                            </div>
                        @endcan
                    @endif

                    <!-- Back Button -->
                    <div class="mt-6 pt-6 border-t">
                        <a href="{{ route('corrections.index') }}" class="text-blue-600 hover:underline">
                            ← Kembali ke daftar koreksi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>