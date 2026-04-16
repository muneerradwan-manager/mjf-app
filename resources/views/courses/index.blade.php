<x-layouts.app title="Courses">
<div x-data="coursesPage()" x-init="init()" x-cloak>

    <div class="page-header">
        <div>
            <h2 class="page-title">Courses</h2>
            <p class="page-subtitle" x-text="`${items.length} courses in catalogue`"></p>
        </div>
        <button x-show="canCreate" @click="openCreate()" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Course
        </button>
    </div>

    <div class="flex gap-3 mb-4">
        <div class="relative flex-1 max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input x-model="search" type="search" placeholder="Search courses…" class="form-input pl-9">
        </div>
    </div>

    <template x-if="loading">
        <div class="card p-12 text-center text-slate-400">
            <svg class="w-8 h-8 animate-spin mx-auto mb-3 text-indigo-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            Loading courses…
        </div>
    </template>

    <template x-if="!loading">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <template x-if="items.length === 0">
                <div class="card empty-state col-span-3 text-slate-400">
                    <p class="font-medium text-slate-600">No courses yet</p>
                </div>
            </template>
            <template x-for="item in filtered" :key="item.id">
                <div class="card p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-150 flex flex-col gap-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="font-semibold text-slate-900" x-text="item.name"></p>
                            <p class="text-xs font-mono text-indigo-600 mt-0.5" x-text="item.code"></p>
                        </div>
                        <span
                            :class="item.status === 'active' ? 'badge-green' : item.status === 'archived' ? 'badge-slate' : 'badge-amber'"
                            x-text="item.status"
                        ></span>
                    </div>
                    <p class="text-sm text-slate-500 leading-relaxed flex-1 line-clamp-3" x-text="item.description ?? 'No description.'"></p>
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                        <p class="text-xs text-slate-400" x-text="item.teacher ? item.teacher.user?.name ?? 'No teacher' : 'No teacher'"></p>
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
                    <h3 class="font-semibold text-slate-900" x-text="editingId ? 'Edit Course' : 'New Course'"></h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="form-label">Course Name</label>
                            <input x-model="form.name" type="text" class="form-input" placeholder="Algebra & Trigonometry">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="form-label">Course Code</label>
                            <input x-model="form.code" type="text" class="form-input" placeholder="MATH101">
                        </div>
                        <div class="col-span-2">
                            <label class="form-label">Description</label>
                            <textarea x-model="form.description" rows="3" class="form-textarea" placeholder="Course description…"></textarea>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="form-label">Teacher</label>
                            <select x-model="form.teacher_id" class="form-select">
                                <option value="">Select teacher…</option>
                                <template x-for="t in teachers" :key="t.id">
                                    <option :value="t.id" x-text="t.user?.name ?? 'Teacher #' + t.id"></option>
                                </template>
                            </select>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="form-label">Status</label>
                            <select x-model="form.status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl">
                    <button @click="showModal = false" class="btn-secondary">Cancel</button>
                    <button @click="save()" class="btn-primary" x-text="editingId ? 'Save Changes' : 'Create Course'"></button>
                </div>
            </div>
        </div>
    </template>

    @include('partials.delete-confirm')
</div>
</x-layouts.app>
