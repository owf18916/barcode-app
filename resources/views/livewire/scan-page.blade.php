<div class="space-y-6">

    {{-- User Info Form --}}
    <div class="p-6 bg-white rounded-lg shadow border border-gray-200">
        <livewire:user-session-form />
    </div>

    {{-- Pilihan Modul --}}
    @if ($selectedModule === '')
        <div class="p-6 bg-white rounded-lg shadow border border-gray-200 text-center space-y-4">
            <h2 class="text-lg font-semibold text-gray-800">Pilih Modul Scan</h2>

            {{-- Notifikasi jika belum set NIK/Area --}}
            <div
                x-data="{ show: false }"
                x-on:missing-session.window="show = true; setTimeout(() => show = false, 4000)"
                x-show="show"
                class="bg-red-100 text-red-800 text-sm rounded px-4 py-3 border border-red-300"
                style="display: none;"
            >
                ⚠️ Anda harus mengisi NIK dan memilih Area terlebih dahulu.
            </div>

            {{-- Tombol pilih modul --}}
            <div class="flex flex-col md:flex-row justify-center gap-4">
                {{-- Tombol Scan Barcode --}}
                <div class="relative">
                    <button
                        wire:click="selectModule('barcode')"
                        wire:loading.attr="disabled"
                        wire:target="selectModule('barcode')"
                        class="px-6 py-3 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition disabled:opacity-50 flex items-center justify-center gap-2"
                    >
                        <svg wire:loading wire:target="selectModule('barcode')" class="animate-spin h-5 w-5 text-white" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8v4l3.5-3.5L12 0v4a8 8 0 100 16v-4l3.5 3.5L12 24v-4a8 8 0 01-8-8z">
                            </path>
                        </svg>
                        <span wire:loading.remove wire:target="selectModule('barcode')">📦 Scan Barcode</span>
                    </button>
                </div>

                {{-- Tombol Scan Kanban --}}
                <div class="relative">
                    <button
                        wire:click="selectModule('kanban')"
                        wire:loading.attr="disabled"
                        wire:target="selectModule('kanban')"
                        class="px-6 py-3 rounded-lg bg-green-600 text-white hover:bg-green-700 transition disabled:opacity-50 flex items-center justify-center gap-2"
                    >
                        <svg wire:loading wire:target="selectModule('kanban')" class="animate-spin h-5 w-5 text-white" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8v4l3.5-3.5L12 0v4a8 8 0 100 16v-4l3.5 3.5L12 24v-4a8 8 0 01-8-8z">
                            </path>
                        </svg>
                        <span wire:loading.remove wire:target="selectModule('kanban')">🏷️ Scan Kanban</span>
                    </button>
                </div>
            </div>

        </div>
    @endif

    {{-- Modul Barcode --}}
    @if ($selectedModule === 'barcode')
        <div class="p-6 bg-white rounded-lg shadow border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Modul Scan Barcode</h2>
                <button wire:click="$set('selectedModule', '')"
                    class="text-sm text-red-500 hover:underline">← Kembali</button>
            </div>
            <livewire:scan-barcode-form />
        </div>
    @endif

    {{-- Modul Kanban --}}
    @if ($selectedModule === 'kanban')
        <div class="p-6 bg-white rounded-lg shadow border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Modul Scan Kanban</h2>
                <button wire:click="$set('selectedModule', '')"
                    class="text-sm text-red-500 hover:underline">← Kembali</button>
            </div>
            <livewire:scan-kanban-form />
        </div>
    @endif

</div>