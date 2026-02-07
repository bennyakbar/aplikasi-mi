<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Tunggakan</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Tunggakan</div>
                    <div class="text-3xl font-bold text-red-600">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Siswa dengan Tunggakan</div>
                    <div class="text-3xl font-bold text-orange-600">{{ $studentsCount }} siswa</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Kewajiban
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Terbayar
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tunggakan
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">%</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($students as $data)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono">{{ $data['student']->nis }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $data['student']->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $data['student']->grade }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $data['student']->category->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">Rp
                                        {{ number_format($data['total_fees'], 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-green-600">Rp
                                        {{ number_format($data['total_paid'], 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-red-600">Rp
                                        {{ number_format($data['tunggakan'], 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-green-600 h-2.5 rounded-full"
                                                style="width: {{ $data['percentage_paid'] }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $data['percentage_paid'] }}%</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">Tidak ada tunggakan 🎉</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>