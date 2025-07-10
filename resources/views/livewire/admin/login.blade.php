<div class="max-w-md mx-auto bg-white p-6 rounded shadow border border-gray-200">
    <h2 class="text-xl font-bold text-gray-800 mb-4 text-center">Login Admin</h2>

    <form wire:submit.prevent="login" class="space-y-4">
        <div>
            <label class="text-sm font-medium text-gray-700">Username</label>
            <input type="text" wire:model="username" class="w-full border rounded px-3 py-2" autofocus>
            @error('username') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="text-sm font-medium text-gray-700">Password</label>
            <input type="password" wire:model="password" class="w-full border rounded px-3 py-2">
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Login</button>
    </form>
</div>
