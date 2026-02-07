<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard System Admin
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Users</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['total_users'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Siswa</div>
                    <div class="text-3xl font-bold text-green-600">{{ $stats['total_students'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Transaksi Hari Ini</div>
                    <div class="text-3xl font-bold text-purple-600">{{ $stats['total_payments_today'] }}</div>
                </div>
            </div>

            <!-- Admin Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Administrasi Sistem</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('admin.users.index') }}"
                        class="block p-6 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                        <div class="flex items-center">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <div class="ml-4">
                                <div class="text-lg font-medium text-blue-900">Kelola User</div>
                                <div class="text-sm text-blue-600">Tambah, edit, nonaktifkan user</div>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.backup.index') }}"
                        class="block p-6 bg-green-50 hover:bg-green-100 rounded-lg transition">
                        <div class="flex items-center">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                            </svg>
                            <div class="ml-4">
                                <div class="text-lg font-medium text-green-900">Backup Database</div>
                                <div class="text-sm text-green-600">Unduh backup SQL</div>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.audit-logs.index') }}"
                        class="block p-6 bg-purple-50 hover:bg-purple-100 rounded-lg transition">
                        <div class="flex items-center">
                            <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <div class="ml-4">
                                <div class="text-lg font-medium text-purple-900">Audit Logs</div>
                                <div class="text-sm text-purple-600">Lihat aktivitas sistem</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- All Dashboards Access -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mt-6">
                <h3 class="text-lg font-semibold mb-4">Akses Dashboard Lain</h3>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('dashboard.bendahara') }}"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm">
                        Dashboard Bendahara
                    </a>
                    <a href="{{ route('dashboard.yayasan') }}"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm">
                        Dashboard Yayasan
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>