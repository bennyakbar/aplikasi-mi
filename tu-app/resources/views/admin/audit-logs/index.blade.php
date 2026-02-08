<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Audit Logs</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <select name="action" class="border-gray-300 rounded-md">
                            <option value="">-- Semua Action --</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                                    {{ $action }}</option>
                            @endforeach
                        </select>
                        <select name="user_id" class="border-gray-300 rounded-md">
                            <option value="">-- Semua User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}</option>
                            @endforeach
                        </select>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="border-gray-300 rounded-md" placeholder="Dari">
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="border-gray-300 rounded-md" placeholder="Sampai">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Filter</button>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Waktu</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">User</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Action</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Model</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">IP</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Changes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr>
                                    <td class="px-4 py-3">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                    <td class="px-4 py-3">{{ $log->user?->name ?? 'System' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs
                                            {{ $log->action === 'create' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $log->action === 'update' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $log->action === 'delete' ? 'bg-red-100 text-red-800' : '' }}
                                        ">{{ $log->action_label }}</span>
                                    </td>
                                    <td class="px-4 py-3">{{ $log->model_name }} #{{ $log->model_id }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $log->ip_address }}</td>
                                    <td class="px-4 py-3 text-xs">
                                        @if($log->old_values || $log->new_values)
                                            <details>
                                                <summary class="cursor-pointer text-blue-600">Lihat</summary>
                                                @if($log->old_values)
                                                    <div class="text-red-600">Old:
                                                {{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</div>@endif
                                                @if($log->new_values)
                                                    <div class="text-green-600">New:
                                                {{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</div>@endif
                                            </details>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-3 text-center text-gray-500">Tidak ada log</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $logs->withQueryString()->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>