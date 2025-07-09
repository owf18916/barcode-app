<form wire:submit.prevent="submit" class="space-y-4 text-sm text-gray-700">
    <input type="text" wire:model="kanbanCode"
        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 shadow-sm"
        placeholder="Scan kanban code" autofocus>

    <button type="submit"
        class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">Submit</button>
</form>
