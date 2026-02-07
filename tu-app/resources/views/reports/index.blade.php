@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-6">Laporan & Ekspor Data</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Export Tunggakan -->
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Laporan Tunggakan</h3>
                                    <p class="text-sm text-gray-500">Daftar siswa dengan tunggakan pembayaran</p>
                                </div>
                            </div>
                            <p class="text-gray-600 mb-4 text-sm">
                                Export daftar siswa yang memiliki tunggakan pembayaran beserta detail nominal dan persentase
                                pembayaran.
                            </p>
                            <a href="{{ route('reports.tunggakan') }}"
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download Excel
                            </a>
                        </div>

                        <!-- Export Rekap Bulanan -->
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Rekap Bulanan</h3>
                                    <p class="text-sm text-gray-500">Laporan penerimaan per bulan</p>
                                </div>
                            </div>

                            <form action="{{ route('reports.rekap-bulanan') }}" method="GET" class="space-y-4">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Bulan</label>
                                        <select name="month" class="w-full rounded-lg border-gray-300 text-sm">
                                            @for($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}" {{ $i == now()->month ? 'selected' : '' }}>
                                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Tahun</label>
                                        <select name="year" class="w-full rounded-lg border-gray-300 text-sm">
                                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Download PDF
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection