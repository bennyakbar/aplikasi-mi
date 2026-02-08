<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Admin Master Data
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Siswa</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['total_students'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Siswa Aktif</div>
                    <div class="text-3xl font-bold text-green-600">{{ $stats['active_students'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Kategori</div>
                    <div class="text-3xl font-bold text-purple-600">{{ $stats['total_categories'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Tarif Aktif</div>
                    <div class="text-3xl font-bold text-orange-600">{{ $stats['active_fees'] }}</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Kelola Master Data</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('students.index') }}"
                        class="block p-6 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                        <div class="flex items-center">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m0 0V10m0-6a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <div class="ml-4">
                                <div class="text-lg font-medium text-blue-900">Kelola Siswa</div>
                                <div class="text-sm text-blue-600">Tambah, edit, hapus data siswa</div>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('student-categories.index') }}"
                        class="block p-6 bg-purple-50 hover:bg-purple-100 rounded-lg transition">
                        <div class="flex items-center">
                            <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <div class="ml-4">
                                <div class="text-lg font-medium text-purple-900">Kelola Kategori</div>
                                <div class="text-sm text-purple-600">Reguler, Anak Guru, Yatim/Dhuafa</div>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('fees.index') }}"
                        class="block p-6 bg-orange-50 hover:bg-orange-100 rounded-lg transition">
                        <div class="flex items-center">
                            <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="ml-4">
                                <div class="text-lg font-medium text-orange-900">Kelola Tarif</div>
                                <div class="text-sm text-orange-600">SPP, Uang Kegiatan, dll</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>