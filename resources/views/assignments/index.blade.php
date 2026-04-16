<x-layouts.app title="Assignments">
<div x-data="assignmentsPage()" x-init="init()" x-cloak>

    <div class="page-header">
        <div>
            <h2 class="page-title">Assignments</h2>
            <p class="page-subtitle" x-text="`${items.length} assignments`"></p>
        </div>
        <button x-show="canCreate" @click="openCreate()" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Assignment
        </button>
    </div>

    <div class="flex gap-3 mb-4">
        <div class="relative flex-1 max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input x-model="search" type="search" placeholder="Search assignments…" class="form-input pl-9">
        </div>
    </div>

    <template x-if="loading">
        <div class="card p-12 text-center text-slate-400">
            <svg class="w-8 h-8 animate-spin mx-auto mb-3 text-indigo-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            Loading assignments…
        </div>
    </template>

    <template x-if="!loading">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Class</th>
                        <th>Teacher</th>
                        <th>Due Date</th>
                        <th>Max Grade</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in filtered" :key="item.id">
                        <tr>
                            <td>
                                <p class="font-medium text-slate-800" x-text="item.title"></p>
                                <p class="text-xs text-slate-400 mt-0.5 line-clamp-1" x-text="item.description ?? ''"></p>
                            </td>
                            <td x-text="item.classroom?.name ?? item.class?.name ?? '—'"></td>
                            <td x-text="item.teacher?.user?.name ?? '—'"></td>
                            <td>
                                <span
                                    :class="item.due_date && new Date(item.due_date) < new Date() ? 'badge-red' : 'badge-green'"
                                    x-text="item.due_date ? new Date(item.due_date).toLocaleDateString() : '—'"
                                ></span>
                            </td>
                            <td class="font-semibold" x-text="item.max_grade ?? '—'"></td>
                            <td>
                                <div class="flex items-center gap-1 justify-end">
                                    <button x-show="canEditItem(item)" @click="openEdit(item)" class="btn-ghost btn-sm text-indigo-600">Edit</button>
                                    <button x-show="canEditItem(item)" @click="confirmDelete(item.id)" class="btn-ghost btn-sm text-red-500">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filtered.length === 0">
                        <tr><td colspan="6" class="text-center py-12 text-slate-400">No assignments found.</td></tr>
                    </template>
                </tbody>
            </table>
        </div>
    </template>

    <template x-teleport="body">
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <div class="modal-backdrop" @click="showModal = false"></div>
            <div class="modal-panel" @click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <h3 class="font-semibold text-slate-900" x-text="editingId ? 'Edit Assignment' : 'New Assignment'"></h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="form-label">Title</label>
                        <input x-model="form.title" type="text" class="form-input" placeholder="Chapter 1 Exercises">
                    </div>
                    <div>
                        <label class="form-label">Description</label>
                        <textarea x-model="form.description" rows="3" class="form-textarea" placeholder="Assignment instructions…"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Class</label>
                            <select x-model="form.class_id" class="form-select">
                                <option value="">Select class…</option>
                                <template x-for="c in classes" :key="c.id">
                                    <option :value="c.id" x-text="c.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Teacher</label>
                            <select x-model="form.teacher_id" class="form-select">
                                <option value="">Select teacher…</option>
                                <template x-for="t in teachers" :key="t.id">
                                    <option :value="t.id" x-text="t.user?.name ?? 'Teacher #' + t.id"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Due Date & Time</label>
                            <input x-model="form.due_date" type="datetime-local" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Max Grade</label>
                            <input x-model="form.max_grade" type="number" min="1" class="form-input" placeholder="100">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl">
                    <button @click="showModal = false" class="btn-secondary">Cancel</button>
                    <button @click="save()" class="btn-primary" x-text="editingId ? 'Save Changes' : 'Create Assignment'"></button>
                </div>
            </div>
        </div>
    </template>

    @include('partials.delete-confirm')
</div>
</x-layouts.app>
