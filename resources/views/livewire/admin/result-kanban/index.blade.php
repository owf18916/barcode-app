<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-800">Hasil Scan Kanban</h1>

    {{-- Daftar Kanban --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
        <div class="flex items-center justify-between mb-4 p-2">
            <h2 class="text-xl font-semibold text-gray-800">📁 List Scan Kanban</h2>

            <div>
                <div class="mb-4 text-right">
                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="text-sm text-red-500 hover:underline">← Kembali</a>
                </div>
                <div>
                    <input
                        type="text"
                        wire:model.defer="search"
                        wire:keydown.enter="applySearch"
                        placeholder="Cari kanban code..."
                        class="border-2 border-gray-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-indigo-200 w-64"
                    />
                </div>
            </div>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 text-gray-700 text-sm">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">#</th>
                    <th class="px-4 py-3 text-left font-medium">Area</th>
                    <th class="px-4 py-3 text-left font-medium">Category</th>
                    <th class="px-4 py-3 text-left font-medium">Kanban Code</th>
                    <th class="px-4 py-3 text-left font-medium">Status Kanban</th>
                    <th class="px-4 py-3 text-left font-medium">NIK</th>
                    <th class="px-4 py-3 text-left font-medium">Scanned at</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm text-gray-800">
                @forelse($resultKanbans as $i => $result)
                <tr>
                    <td class="px-4 py-3">{{ $i + 1 }}</td>
                    <td class="px-4 py-3">{{ $result->area->name }}</td>
                    <td class="px-4 py-3">{{ $result->kanban->kanbanCategory->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3">{{ $result->scanned_kanban }}</td>
                    <td class="px-4 py-3">
                        @if($result->valid_kanban)
                            <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Tidak Aktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $result->nik }}</td>
                    <td class="px-4 py-3">{{ $result->created_at }}</td>
                </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-center text-gray-400">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mx-4 my-6">
            {{ $resultKanbans->onEachSide(2)->links() }}
        </div>
    </div>

</div>
