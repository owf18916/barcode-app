<div
    x-data
    x-init="$refs.barcode1.focus();"
    x-on:play-sound.window="new Audio($event.detail.res ? '/sounds/success.mp3' : '/sounds/fail.mp3').play()"
    wire:loading.class="opacity-50 pointer-events-none"
    class="space-y-4"
>
    {{-- Barcode 1 --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Barcode 1</label>
        <input
            x-ref="barcode1"
            wire:model.live.debounce.300ms="barcode1"
            type="text"
            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            placeholder="Scan barcode pertama"a
        />
    </div>

    {{-- Barcode 2 --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Barcode 2</label>
        <input
            wire:model.live.debounce.300ms="barcode2"
            type="text"
            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            placeholder="Scan barcode kedua"
        />
    </div>

    {{-- Barcode 3 --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Barcode 3</label>
        <input
            wire:model.live.debounce.300ms="barcode3"
            type="text"
            class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            placeholder="Scan barcode ketiga"
        />
    </div>

    {{-- Spinner --}}
    <div
        wire:loading.class="flex"
        wire:target="checkBarcodes"
        class="hidden items-center gap-2 text-gray-600 text-sm mt-2"
    >
        <svg class="w-5 h-5 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10"
                stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8h4z"></path>
        </svg>
        <span>Memproses scan...</span>
    </div>

    {{-- Message --}}
    @if ($message)
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition
            class="p-3 rounded mt-2 @if($result) bg-green-100 text-green-700 @else bg-red-100 text-red-700 @endif"
        >
            {{ $message }}
        </div>

        {{-- Sound tetap jalan --}}
        <audio autoplay>
            <source src="{{ asset($result ? 'sounds/success.mp3' : 'sounds/error.mp3') }}" type="audio/mpeg">
        </audio>
    @endif

</div>
