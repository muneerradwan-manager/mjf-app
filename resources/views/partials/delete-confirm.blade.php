{{-- Reusable delete confirmation modal --}}
{{-- Requires the parent Alpine component to have: showDeleteConfirm, deletingId, remove() --}}
<template x-teleport="body">
    <div x-show="showDeleteConfirm" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="modal-backdrop" @click="showDeleteConfirm = false"></div>
        <div class="modal-panel max-w-sm" @click.stop>
            <div class="p-6 text-center">
                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 mb-1">Are you sure?</h3>
                <p class="text-sm text-slate-500 mb-6">This action cannot be undone. The record will be permanently deleted.</p>
                <div class="flex gap-3">
                    <button @click="showDeleteConfirm = false" class="btn-secondary flex-1 justify-center">Cancel</button>
                    <button @click="remove()" class="btn-danger flex-1 justify-center">Delete</button>
                </div>
            </div>
        </div>
    </div>
</template>
