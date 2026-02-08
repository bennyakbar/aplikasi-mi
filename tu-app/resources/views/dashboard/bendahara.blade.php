<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Bendahara
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Today's Income -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Pemasukan Hari Ini</div>
                    <div class="text-3xl font-bold text-green-600">
                        Rp {{ number_format($todayIncome, 0, ',', '.') }}
                    </div>
                </div>

                <!-- Monthly Income -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Pemasukan Bulan Ini</div>
                    <div class="text-3xl font-bold text-blue-600">
                        Rp {{ number_format($monthIncome, 0, ',', '.') }}
                    </div>
                </div>

                <!-- Tunggakan Count -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Siswa dengan Tunggakan</div>
                    <div class="text-3xl font-bold text-red-600">
                        {{ $studentsWithTunggakan->count() }} siswa
                    </div>
                    <a href="{{ route('tunggakan.index') }}" class="text-sm text-blue-500 hover:underline">
                        Lihat detail →
                    </a>
                </div>
            </div>

            <!-- Pending Corrections Badge -->
            @if($pendingCorrections > 0)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Ada <strong>{{ $pendingCorrections }}</strong> koreksi menunggu approval.
                                <a href="{{ route('corrections.index') }}" class="font-medium underline">Lihat sekarang</a>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Monthly Trend Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold mb-4">Trend Pemasukan 6 Bulan Terakhir</h3>
                <div class="h-64">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Aksi Cepat</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('accounting.journal') }}"
                        class="bg-blue-50 hover:bg-blue-100 p-4 rounded-lg text-center">
                        <div class="text-blue-600 font-medium">Jurnal</div>
                    </a>
                    <a href="{{ route('accounting.monthly-summary') }}"
                        class="bg-green-50 hover:bg-green-100 p-4 rounded-lg text-center">
                        <div class="text-green-600 font-medium">Laporan Bulanan</div>
                    </a>
                    <a href="{{ route('accounting.trial-balance') }}"
                        class="bg-purple-50 hover:bg-purple-100 p-4 rounded-lg text-center">
                        <div class="text-purple-600 font-medium">Trial Balance</div>
                    </a>
                    <a href="{{ route('corrections.index') }}"
                        class="bg-yellow-50 hover:bg-yellow-100 p-4 rounded-lg text-center">
                        <div class="text-yellow-600 font-medium">Approve Koreksi</div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
            const monthlyData = @json($monthlyTrend);

            const labels = monthlyData.map(d => {
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                return months[d.month - 1] + ' ' + d.year;
            });

            const data = monthlyData.map(d => d.total);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Pemasukan',
                        data: data,
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        </script>
    @endpush
</x-app-layout>