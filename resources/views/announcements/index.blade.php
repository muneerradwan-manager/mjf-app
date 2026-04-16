<x-layouts.app title="Announcements">
<div x-data="announcementsPage()" x-init="init()" x-cloak>

    <div class="page-header">
        <div>
            <h2 class="page-title">Announcements</h2>
            <p class="page-subtitle" x-text="`${items.length} announcements`"></p>
        </div>
        <button x-show="canCreate" @click="openCreate()" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Announcement
        </button>
    </div>

    <div class="flex gap-3 mb-4">
        <div class="relative flex-1 max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input x-model="search" type="search" placeholder="Search announcements…" class="form-input pl-9">
        </div>
    </div>

    <template x-if="loading">
        <div class="card p-12 text-center text-slate-400">
            <svg class="w-8 h-8 animate-spin mx-auto mb-3 text-indigo-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            Loading…
        </div>
    </template>

    <template x-if="!loading">
        <div class="space-y-4">
            <template x-if="filtered.length === 0">
                <div class="card empty-state text-slate-400">
                    <p class="font-medium text-slate-600">No announcements yet</p>
                </div>
            </template>
            <template x-for="item in filtered" :key="item.id">
                <div class="card p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <h3 class="font-semibold text-slate-900" x-text="item.title"></h3>
                                <span
                                    :class="item.audience_type === 'all' ? 'badge-blue' : item.audience_type === 'students' ? 'badge-green' : item.audience_type === 'teachers' ? 'badge-amber' : 'badge-slate'"
                                    x-text="item.audience_type"
                                ></span>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed line-clamp-3" x-text="item.content"></p>
                            <p class="text-xs text-slate-400 mt-2" x-text="item.published_at ? 'Published: ' + new Date(item.published_at).toLocaleString() : 'Draft'"></p>
                        </div>
                        <div class="flex gap-1 shrink-0">
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
                    <h3 class="font-semibold text-slate-900" x-text="editingId ? 'Edit Announcement' : 'New Announcement'"></h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="form-label">Title</label>
                        <input x-model="form.title" type="text" class="form-input" placeholder="Announcement title…">
                    </div>
                    <div>
                        <label class="form-label">Content</label>
                        <textarea x-model="form.content" rows="5" class="form-textarea" placeholder="Write your announcement…"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Audience</label>
                            <select x-model="form.audience_type" class="form-select">
                                <template x-for="t in audienceTypes" :key="t">
                                    <option :value="t" x-text="t.charAt(0).toUpperCase() + t.slice(1)"></option>
                                </template>
                            </select>
                        </div>
                        <div x-show="form.audience_type === 'class'">
                            <label class="form-label">Target Class</label>
                            <select x-model="form.audience_id" class="form-select">
                                <option value="">Select class…</option>
                                <template x-for="c in classes" :key="c.id">
                                    <option :value="c.id" x-text="c.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Publish Date & Time</label>
                        <input x-model="form.published_at" type="datetime-local" class="form-input">
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl">
                    <button @click="showModal = false" class="btn-secondary">Cancel</button>
                    <button @click="save()" class="btn-primary" x-text="editingId ? 'Save Changes' : 'Publish'"></button>
                </div>
            </div>
        </div>
    </template>

    @include('partials.delete-confirm')
</div>
</x-layouts.app>
