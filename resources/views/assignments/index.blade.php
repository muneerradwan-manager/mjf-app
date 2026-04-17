<x-layouts.app :title="__('app.assignments')">
<div x-data="assignmentsPage()" x-init="init()" x-cloak>

    <div class="page-header">
        <div>
            <h2 class="page-title">{{ __('app.assignments') }}</h2>
            <p class="page-subtitle" x-text="`${items.length} {{ __('app.assignments_subtitle', ['count' => '']) }}`.replace(/^\d+\s/, items.length + ' ')"></p>
        </div>
        <button x-show="canCreate" @click="openCreate()" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ __('app.new_assignment') }}
        </button>
    </div>

    <div class="flex gap-3 mb-4">
        <div class="search-wrapper">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input x-model="search" type="search" placeholder="{{ __('app.search') }}" class="form-input">
        </div>
    </div>

    <template x-if="loading">
        <div class="card p-12 text-center text-slate-400">
            <svg class="w-8 h-8 animate-spin mx-auto mb-3 text-indigo-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            {{ __('app.loading') }}
        </div>
    </template>

    <template x-if="!loading">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('app.title') }}</th>
                        <th>{{ __('app.class') }}</th>
                        <th>{{ __('app.teacher') }}</th>
                        <th>{{ __('app.due_date') }}</th>
                        <th>{{ __('app.max_grade') }}</th>
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
                                    x-text="formatDate(item.due_date)"
                                ></span>
                            </td>
                            <td class="font-semibold" x-text="item.max_grade ?? '—'"></td>
                            <td>
                                <div class="flex items-center gap-1 justify-end">
                                    <button x-show="canEditItem(item)" @click="openEdit(item)" class="btn-ghost btn-sm text-indigo-600">{{ __('app.edit') }}</button>
                                    <button x-show="canEditItem(item)" @click="confirmDelete(item.id)" class="btn-ghost btn-sm text-red-500">{{ __('app.delete') }}</button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filtered.length === 0">
                        <tr><td colspan="5" class="text-center py-12 text-slate-400">{{ __('app.no_assignments') }}</td></tr>
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
                    <h3 class="font-semibold text-slate-900" x-text="editingId ? '{{ __('app.edit_assignment') }}' : '{{ __('app.new_assignment') }}'"></h3>
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
                            <label class="form-label">{{ __('app.class') }}</label>
                            <select x-model="form.class_id" class="form-select">
                                <option value="">{{ __('app.select_class') }}</option>
                                <template x-for="c in classes" :key="c.id">
                                    <option :value="c.id" x-text="c.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">{{ __('app.teacher') }}</label>
                            <select x-model="form.teacher_id" class="form-select">
                                <option value="">{{ __('app.select_teacher') }}</option>
                                <template x-for="t in teachers" :key="t.id">
                                    <option :value="t.id" x-text="t.user?.name ?? '{{ __('app.teacher') }} #' + t.id"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">{{ __('app.due_date') }}</label>
                            <input x-model="form.due_date" type="datetime-local" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">{{ __('app.max_grade') }}</label>
                            <input x-model="form.max_grade" type="number" min="1" class="form-input" placeholder="{{ __('app.max_grade') }}">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl">
                    <button @click="showModal = false" class="btn-secondary">{{ __('app.cancel') }}</button>
                    <button @click="save()" class="btn-primary" x-text="editingId ? '{{ __('app.save_changes') }}' : '{{ __('app.create_assignment') }}'"></button>
                </div>
            </div>
        </div>
    </template>

    @include('partials.delete-confirm')
</div>
</x-layouts.app>
