<x-layouts.app title="Enrollments">
<div x-data="enrollmentsPage()" x-init="init()" x-cloak>

    <div class="page-header">
        <div>
            <h2 class="page-title">Enrollments</h2>
            <p class="page-subtitle" x-text="`${items.length} total enrollments`"></p>
        </div>
        <button x-show="canCreate" @click="openCreate()" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Enroll Student
        </button>
    </div>

    <div class="flex gap-3 mb-4">
        <div class="relative flex-1 max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input x-model="search" type="search" placeholder="Search enrollments…" class="form-input pl-9">
        </div>
    </div>

    <template x-if="loading">
        <div class="card p-12 text-center text-slate-400">
            <svg class="w-8 h-8 animate-spin mx-auto mb-3 text-indigo-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            Loading enrollments…
        </div>
    </template>

    <template x-if="!loading">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Enrollment Date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in filtered" :key="item.id">
                        <tr>
                            <td class="font-medium text-slate-800" x-text="item.student?.user?.name ?? '—'"></td>
                            <td x-text="item.classroom?.name ?? item.class?.name ?? '—'"></td>
                            <td x-text="item.enrollment_date ?? '—'"></td>
                            <td>
                                <span
                                    :class="item.status === 'active' ? 'badge-green' : item.status === 'completed' ? 'badge-blue' : 'badge-amber'"
                                    x-text="item.status"
                                ></span>
                            </td>
                            <td>
                                <div class="flex items-center gap-1 justify-end">
                                    <button x-show="canWrite" @click="openEdit(item)" class="btn-ghost btn-sm text-indigo-600">Edit</button>
                                    <button x-show="canWrite" @click="confirmDelete(item.id)" class="btn-ghost btn-sm text-red-500">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filtered.length === 0">
                        <tr><td colspan="5" class="text-center py-12 text-slate-400">No enrollments found.</td></tr>
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
                    <h3 class="font-semibold text-slate-900" x-text="editingId ? 'Edit Enrollment' : 'Enroll Student'"></h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="form-label">Student</label>
                        <select x-model="form.student_id" class="form-select">
                            <option value="">Select student…</option>
                            <template x-for="s in students" :key="s.id">
                                <option :value="s.id" x-text="s.user?.name ?? 'Student #' + s.id"></option>
                            </template>
                        </select>
                    </div>
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
                        <label class="form-label">Enrollment Date</label>
                        <input x-model="form.enrollment_date" type="date" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select x-model="form.status" class="form-select">
                            <template x-for="s in statuses" :key="s">
                                <option :value="s" x-text="s.charAt(0).toUpperCase() + s.slice(1)"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl">
                    <button @click="showModal = false" class="btn-secondary">Cancel</button>
                    <button @click="save()" class="btn-primary" x-text="editingId ? 'Save Changes' : 'Enroll'"></button>
                </div>
            </div>
        </div>
    </template>

    @include('partials.delete-confirm')
</div>
</x-layouts.app>
