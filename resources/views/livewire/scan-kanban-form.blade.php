<form wire:submit.prevent="submit" class="space-y-4 text-sm text-gray-700">
    <input type="text" wire:model="kanbanCode" wire:keydown.tab="submit"
        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 shadow-sm"
        placeholder="Scan kanban code" autofocus>
</form>
