<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Tarif') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('fees.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Tarif</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori
                                    Siswa</label>
                                <select name="category_id" id="category_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }} ({{ $category->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="amount" class="block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
                                <input type="number" name="amount" id="amount" value="{{ old('amount') }}" min="0"
                                    step="1000"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="fee_type" class="block text-sm font-medium text-gray-700">Tipe Tarif</label>
                                <select name="fee_type" id="fee_type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                    <option value="monthly" {{ old('fee_type') == 'monthly' ? 'selected' : '' }}>Bulanan
                                    </option>
                                    <option value="yearly" {{ old('fee_type') == 'yearly' ? 'selected' : '' }}>Tahunan
                                    </option>
                                    <option value="one_time" {{ old('fee_type') == 'one_time' ? 'selected' : '' }}>Sekali
                                        Bayar</option>
                                </select>
                                @error('fee_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="academic_year" class="block text-sm font-medium text-gray-700">Tahun
                                    Ajaran</label>
                                <input type="text" name="academic_year" id="academic_year"
                                    value="{{ old('academic_year', '2025/2026') }}" placeholder="2025/2026"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required maxlength="9">
                                @error('academic_year')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4 flex items-center">
                                <label class="flex items-center mt-6">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600">Aktif</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('fees.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>