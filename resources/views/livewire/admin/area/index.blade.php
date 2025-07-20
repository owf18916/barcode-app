<div class="space-y-6">
    <div class="justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Master Area</h1>

        <div class="text-right">
            <a href="{{ route('admin.dashboard') }}" wire:navigate class="text-sm text-red-500 hover:underline">← Kembali</a>
        </div>
    </div>

    {{-- Form Area --}}
    <div class="bg-white p-4 rounded shadow space-y-4">
        <div>
            <label class="block text-sm font-medium">Nama Area</label>
            <input type="text" wire:model="name" class="w-full border rounded px-3 py-2" />
            @error('name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="inline-flex items-center space-x-2">
                <input type="checkbox" wire:model="is_active" />
                <span>Aktif</span>
            </label>
        </div>

        <div class="flex gap-3">
            @if ($areaIdBeingEdited)
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
    <div class="bg-white p-4 rounded shadow space-y-3">
        <h2 class="font-semibold text-gray-700">Upload Excel</h2>
        <input type="file" wire:model="excelFile" />
        @error('excelFile') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
        
        <button wire:click="uploadFile" class="mt-2 bg-blue-600 flex text-white px-4 py-2 rounded cursor-pointer"><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-upload"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg> <span> Upload</span></button>

        @if (session('import_success_count') || session('import_failures'))
            <div class="mt-4 p-4 bg-gray-50 border border-gray-300 rounded space-y-2 text-sm">
                @if (session('import_success_count'))
                    <p class="text-green-700">✅ {{ session('import_success_count') }} baris berhasil diimpor.</p>
                @endif

                @if (session('import_failures') && count(session('import_failures')) > 0)
                    <p class="text-red-700 font-semibold">❌ Terdapat {{ count(session('import_failures')) }} baris gagal diimpor:</p>
                    <ul class="text-red-600 list-disc list-inside space-y-1">
                        @foreach (session('import_failures') as $failure)
                            <li>
                                Baris {{ $failure->row() }}:
                                @foreach ($failure->errors() as $error)
                                    <span>{{ $error }}</span>
                                @endforeach
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        {{-- Progress upload --}}
        <div wire:loading wire:target="uploadFile" class="mt-3">
            <div class="w-full bg-gray-200 h-2 rounded overflow-hidden">
                <div class="bg-indigo-600 h-full animate-pulse w-full"></div>
            </div>
            <p class="text-sm text-gray-500 mt-1">Mengunggah file Excel...</p>
        </div>
    </div>

    {{-- Daftar Area --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
        <div class="flex items-center justify-between mb-4 p-2">
            <h2 class="text-xl font-semibold text-gray-800">📁 Daftar Area</h2>

            <div class="flex space-x-3">
                <button 
                    class="px-4 py-2 bg-red-400 text-white rounded-md hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed" 
                    wire:click="syncAreasToLocalServer" 
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="syncAreasToLocalServer">Sync Area</span>
                    <span wire:loading wire:target="syncAreasToLocalServer">Syncing...</span>
                </button>
                
                <input
                    type="text"
                    wire:model.defer="search"
                    wire:keydown.enter="applySearch"
                    placeholder="Cari nama area..."
                    class="border-2 border-gray-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-indigo-200 w-64"
                />
            </div>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 text-gray-700 text-sm">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">#</th>
                    <th class="px-4 py-3 text-left font-medium">Nama Area</th>
                    <th class="px-4 py-3 text-left font-medium">Status</th>
                    <th class="px-4 py-3 text-left font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm text-gray-800">
                @forelse($areas as $i => $area)
                <tr>
                    <td class="px-4 py-3">{{ $i + 1 }}</td>
                    <td class="px-4 py-3">{{ $area->name }}</td>
                    <td class="px-4 py-3">
                        @if($area->is_active)
                            <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <button wire:click="edit({{ $area->id }})"
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
    </div>

</div>
