<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard Admin</h1>
        <button wire:click="logout" class="text-sm text-red-500 hover:underline">Logout</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Master Area --}}
        <a href="{{ route('admin.area') }}" wire:navigate class="p-6 bg-white shadow rounded border hover:bg-gray-50 text-center">
            <h3 class="font-semibold text-lg text-gray-800">📁 Master Area</h3>
            <p class="text-sm text-gray-600 mt-2">Tambah, edit, dan upload area produksi.</p>
        </a>

        {{-- Master Kanban --}}
        <a href="{{ route('admin.kanban') }}" wire:navigate class="p-6 bg-white shadow rounded border hover:bg-gray-50 text-center">
            <h3 class="font-semibold text-lg text-gray-800">🏷️ Master Kanban</h3>
            <p class="text-sm text-gray-600 mt-2">Kelola data kanban untuk scan.</p>
        </a>

        {{-- Hasil Scan --}}
        <a href="{{ route('admin.result-kanban') }}" wire:navigate class="p-6 bg-white shadow rounded border hover:bg-gray-50 text-center">
            <h3 class="font-semibold text-lg text-gray-800">📥 Hasil Scan Kanban</h3>
            <p class="text-sm text-gray-600 mt-2">Tabel hasil scan dan export hasil scan ke Excel.</p>
        </a>

    </div>
</div>


