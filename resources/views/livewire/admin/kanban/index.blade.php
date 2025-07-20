<div class="space-y-6">
    <div class="justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Master Kanban</h1>

        <div class="text-right">
            <a href="{{ route('admin.dashboard') }}" wire:navigate class="text-sm text-red-500 hover:underline">← Kembali</a>
        </div>
    </div>

    {{-- Form Kanban --}}
    <div class="bg-white p-4 rounded shadow space-y-4">
        <div>
            <label class="block text-sm font-medium">Code</label>
            <input type="text" wire:model="code" class="w-full border rounded px-3 py-2" />
            @error('code') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>

        <!-- Kanban Category -->
        <div>
            <label class="block text-sm font-medium">Kategori Kanban</label>
            <select wire:model="kanban_category_id" class="w-full border rounded px-3 py-2">
                <option value="">-- Pilih Kategori --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            @error('kanban_category_id') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>

        <!-- Area -->
        <div>
            <label class="block text-sm font-medium">Area</label>
            <select wire:model="area_id" class="w-full border rounded px-3 py-2">
                <option value="">-- Pilih Area --</option>
                @foreach ($areas as $area)
                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                @endforeach
            </select>
            @error('area_id') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>

        <!-- Conveyor -->
        <div>
            <label class="block text-sm font-medium">Conveyor</label>
            <input type="text" wire:model="conveyor" class="w-full border rounded px-3 py-2" />
        </div>

        <!-- Family -->
        <div>
            <label class="block text-sm font-medium">Family</label>
            <input type="text" wire:model="family" class="w-full border rounded px-3 py-2" />
        </div>

        <!-- Issue Number -->
        <div>
            <label class="block text-sm font-medium">Issue Number</label>
            <input type="text" wire:model="issue_number" class="w-full border rounded px-3 py-2" />
        </div>

        <div>
            <label class="inline-flex items-center space-x-2">
                <input type="checkbox" wire:model="is_active" />
                <span>Aktif</span>
            </label>
        </div>

        <div class="flex gap-3">
            @if ($kanbanIdBeingEdited)
                <button wire:click="update" class="bg-yellow-500 flex text-gray-800 px-4 py-2 rounded"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg> <span>Update</span></button>
                <button wire:click="resetForm" class="text-sm text-gray-500 underline">Batal</button>
            @else
                <button wire:click="save" class="bg-indigo-600 flex text-white px-4 py-2 rounded">
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    <span>Simpan</span> 
                </button>
            @endif
        </div>
    </div>

    {{-- Upload Excel --}}
    <div class="bg-white p-6 rounded shadow border border-gray-200 space-y-4">

        <div>
            <label class="block font-medium text-gray-700 mb-1">Upload File Kanban (.xlsx)</label>
            <input type="file" wire:model="excelFile" class="block w-full text-sm text-gray-700 border rounded px-3 py-2" />
            @error('excelFile') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center gap-4">
            <button wire:click="uploadFile" class="mt-2 bg-blue-600 flex text-white px-4 py-2 rounded cursor-pointer"><svg  xmlns="http://www.w3.org/2000/svg"  width="16" height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="mr-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg> <span> Upload</span></button>

            <!-- Progress message -->
            <div wire:loading wire:target="uploadFile" class="text-sm text-indigo-600 mt-2">
                ⏳ Sedang mengunggah dan memproses file...
            </div>

            @if ($importFinished)
                <div class="text-sm text-green-600 mt-2">
                    ✅ Proses import selesai.
                </div>
            @endif

            @if (session()->has('import_failures'))
                <a href="{{ route('admin.kanban.download-failures') }}"
                class="text-sm text-red-500 hover:underline mt-2 block">
                    📥 Unduh data gagal
                </a>
            @endif
        </div>

    </div>

    {{-- Daftar Kanban --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
        <div class="flex items-center justify-between mb-4 p-2">
            <h2 class="text-xl font-semibold text-gray-800">📁 Daftar Kanban</h2>

            <div class="flex space-x-3">
                <button 
                    class="px-4 py-2 bg-red-400 text-white rounded-md hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed" 
                    wire:click="syncKanbansToLocalServer" 
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="syncKanbansToLocalServer">Sync Kanban</span>
                    <span wire:loading wire:target="syncKanbansToLocalServer">Syncing...</span>
                </button>
                <input
                    type="text"
                    wire:model.defer="search"
                    wire:keydown.enter="applySearch"
                    placeholder="Cari kanban code..."
                    class="border-2 border-gray-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-indigo-200 w-64"
                />
            </div>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 text-gray-700 text-sm">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">#</th>
                    <th class="px-4 py-3 text-left font-medium">Category</th>
                    <th class="px-4 py-3 text-left font-medium">Area</th>
                    <th class="px-4 py-3 text-left font-medium">ID Kanban</th>
                    <th class="px-4 py-3 text-left font-medium">Family</th>
                    <th class="px-4 py-3 text-left font-medium">Issue No.</th>
                    <th class="px-4 py-3 text-left font-medium">Status</th>
                    <th class="px-4 py-3 text-left font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm text-gray-800">
                @forelse($kanbans as $i => $kanban)
                <tr>
                    <td class="px-4 py-3">{{ $i + 1 }}</td>
                    <td class="px-4 py-3">{{ $kanban->kanbanCategory->name }}</td>
                    <td class="px-4 py-3">{{ $kanban->area->name }}</td>
                    <td class="px-4 py-3">{{ $kanban->code }}</td>
                    <td class="px-4 py-3">{{ $kanban->family }}</td>
                    <td class="px-4 py-3">{{ $kanban->issue_number }}</td>
                    <td class="px-4 py-3">
                        @if($kanban->is_active)
                            <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <button wire:click="edit({{ $kanban->id }})"
                            class="text-blue-600 hover:underline text-sm">✏️ Edit</button>
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-center text-gray-400">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mx-4 my-6">
            {{ $kanbans->onEachSide(2)->links() }}
        </div>
    </div>

</div>
