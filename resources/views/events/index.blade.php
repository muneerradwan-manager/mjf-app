<x-layouts.app title="Events">
<div x-data="eventsPage()" x-init="init()" x-cloak>

    <div class="page-header">
        <div>
            <h2 class="page-title">Events</h2>
            <p class="page-subtitle" x-text="`${items.length} events`"></p>
        </div>
        <button x-show="canCreate" @click="openCreate()" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Event
        </button>
    </div>

    <div class="flex gap-3 mb-4">
        <div class="relative flex-1 max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input x-model="search" type="search" placeholder="Search events…" class="form-input pl-9">
        </div>
    </div>

    <template x-if="loading">
        <div class="card p-12 text-center text-slate-400">
            <svg class="w-8 h-8 animate-spin mx-auto mb-3 text-indigo-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            Loading events…
        </div>
    </template>

    <template x-if="!loading">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            <template x-if="filtered.length === 0">
                <div class="card empty-state col-span-3 text-slate-400">
                    <p class="font-medium text-slate-600">No events scheduled</p>
                </div>
            </template>
            <template x-for="item in filtered" :key="item.id">
                <div class="card p-5 flex flex-col gap-3 hover:shadow-md hover:-translate-y-0.5 transition-all duration-150">
                    <div class="flex items-start justify-between gap-2">
                        <div class="w-12 h-12 rounded-xl bg-indigo-100 flex flex-col items-center justify-center text-indigo-700 shrink-0">
                            <span class="text-xs font-bold leading-none" x-text="item.start_date ? new Date(item.start_date).toLocaleString('en', {month:'short'}) : ''"></span>
                            <span class="text-lg font-extrabold leading-tight" x-text="item.start_date ? new Date(item.start_date).getDate() : ''"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-slate-900 leading-tight" x-text="item.title"></p>
                            <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span x-text="item.location ?? 'TBA'"></span>
                            </p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-500 leading-relaxed flex-1 line-clamp-3" x-text="item.description ?? 'No description.'"></p>
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs text-slate-400">
                        <span x-text="item.start_date ? new Date(item.start_date).toLocaleString() : '—'"></span>
                        <div class="flex gap-1">
                            <button x-show="canWrite" @click="openEdit(item)" class="btn-ghost btn-sm text-indigo-600">Edit</button>
                            <button x-show="canWrite" @click="confirmDelete(item.id)" class="btn-ghost btn-sm text-red-500">Delete</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </template>

    <template x-teleport="body">
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <div class="modal-backdrop" @click="showModal = false"></div>
            <div class="modal-panel" @click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <h3 class="font-semibold text-slate-900" x-text="editingId ? 'Edit Event' : 'New Event'"></h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="form-label">Title</label>
                        <input x-model="form.title" type="text" class="form-input" placeholder="Annual Science Fair">
                    </div>
                    <div>
                        <label class="form-label">Description</label>
                        <textarea x-model="form.description" rows="3" class="form-textarea" placeholder="Event details…"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Start Date & Time</label>
                            <input x-model="form.start_date" type="datetime-local" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">End Date & Time</label>
                            <input x-model="form.end_date" type="datetime-local" class="form-input">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Location</label>
                        <input x-model="form.location" type="text" class="form-input" placeholder="Main Hall">
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl">
                    <button @click="showModal = false" class="btn-secondary">Cancel</button>
                    <button @click="save()" class="btn-primary" x-text="editingId ? 'Save Changes' : 'Create Event'"></button>
                </div>
            </div>
        </div>
    </template>

    @include('partials.delete-confirm')
</div>
</x-layouts.app>
