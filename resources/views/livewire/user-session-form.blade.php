<div class="space-y-4">
    {{-- NIK --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">NIK</label>
        <input
            wire:model.live="nik"
            wire:keydown.tab="setNik"
            type="text"
            class="w-full border rounded px-3 py-2"
            placeholder="Scan atau ketik NIK..." />
    </div>

    {{-- Area --}}
    <div class="relative">

        <label class="block text-sm font-medium">Area</label>
        <select wire:model.live="area" wire:change="setArea" class="w-full border rounded px-3 py-2">
            <option value="">-- Pilih Area --</option>
            @foreach ($areas as $area)
                <option value="{{ $area->id }}">{{ $area->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Pesan feedback --}}
    @if ($message)
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000); $watch('$wire.message', () => show = false)"
            x-show="show"
            x-transition
            class="text-green-700 bg-green-100 border border-green-300 p-2 rounded"
        >
            {{ $message }}
        </div>
    @endif


    {{-- Info session --}}
    @if (session('nik') && $selectedArea)
        <div class="bg-gray-100 border border-gray-300 p-3 rounded text-sm text-gray-700">
            <strong>NIK:</strong> {{ session('nik') }}<br>
            <strong>Area:</strong> {{ $selectedArea->name }}
        </div>
    @endif
</div>
