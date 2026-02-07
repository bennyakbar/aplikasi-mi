<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Yayasan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Main Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Yearly Income -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Pemasukan Tahun Ini</div>
                    <div class="text-3xl font-bold text-green-600">
                        Rp {{ number_format($yearlyIncome, 0, ',', '.') }}
                    </div>
                </div>

                <!-- Completion Rate -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Tingkat Pelunasan</div>
                    <div
                        class="text-3xl font-bold {{ $completionRate >= 80 ? 'text-green-600' : ($completionRate >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $completionRate }}%
                    </div>
                    <div class="text-sm text-gray-400">
                        {{ $totalStudents - $studentsWithTunggakan }} dari {{ $totalStudents }} siswa lunas
                    </div>
                </div>

                <!-- Active Students -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Siswa Aktif</div>
                    <div class="text-3xl font-bold text-blue-600">
                        {{ $totalStudents }}
                    </div>
                </div>
            </div>

            <!-- Monthly Trend Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold mb-4">Trend Pemasukan Tahun {{ now()->year }}</h3>
                <div class="h-64">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>

            <!-- Category Summary Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Ringkasan per Kategori Siswa</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kategori
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Pemasukan
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($categoryStats as $stat)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $stat->category }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    Rp {{ number_format($stat->total, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-center text-gray-500">
                                    Belum ada data pemasukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">
                                Total
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-700">
                                Rp {{ number_format($yearlyIncome, 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Read-only Notice -->
            <div class="mt-8 text-center text-gray-400 text-sm">
                <svg class="inline h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Dashboard ini bersifat read-only untuk monitoring
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
            const monthlyData = @json($monthlyTrend);

            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const labels = monthlyData.map(d => months[d.month - 1]);
            const data = monthlyData.map(d => d.total);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pemasukan',
                        data: data,
                        fill: true,
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderColor: 'rgb(34, 197, 94)',
                        tension: 0.3
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