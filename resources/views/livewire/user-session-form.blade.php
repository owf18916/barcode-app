<div class="space-y-4">
    {{-- NIK --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">NIK</label>
        <input
            wire:model="nik"
            type="text"
            class="w-full border rounded px-3 py-2"
            placeholder="Scan atau ketik NIK..." />
        <button wire:click="setNik" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Set NIK</button>
    </div>

    {{-- Area --}}
    <div class="relative">
        <label class="block text-sm font-medium text-gray-700">Area</label>
        <input
            wire:model.live="areaSearchInput"
            type="text"
            class="w-full border rounded px-3 py-2"
            placeholder="Ketik nama area..."
        />


        {{-- Autocomplete Area --}}
        @if(count($suggestions) > 0)
            <ul class="absolute z-10 bg-white border w-full mt-1 rounded shadow max-h-48 overflow-auto">
                @foreach($suggestions as $area)
                    <li
                        wire:click="selectArea({{ $area->id }});"
                        class="px-4 py-2 hover:bg-indigo-100 cursor-pointer"
                    >
                        {{ $area->name }}
                    </li>
                @endforeach
            </ul>
        @endif

        @error('areaSearchInput')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
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
