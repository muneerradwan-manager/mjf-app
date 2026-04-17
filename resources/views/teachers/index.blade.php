<x-layouts.app :title="__('app.teachers')">
<div x-data="teachersPage()" x-init="init()" x-cloak>

    <div class="page-header">
        <div>
            <h2 class="page-title">{{ __('app.teachers') }}</h2>
            <p class="page-subtitle" x-text="`${items.length} {{ __('app.teachers_subtitle', ['count' => '']) }}`.replace(/^\d+\s/, items.length + ' ')"></p>
        </div>
        <button x-show="canCreate" @click="openCreate()" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ __('app.add_teacher') }}
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
        <div>
            <template x-if="filtered.length === 0">
                <div class="card empty-state text-slate-400">
                    <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <p class="font-medium text-slate-600">{{ __('app.no_teachers') }}</p>
                </div>
            </template>
            <template x-if="filtered.length > 0">
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('app.teacher') }}</th>
                                <th>{{ __('app.employee_id') }}</th>
                                <th>{{ __('app.specialization') }}</th>
                                <th>{{ __('app.bio') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="item in filtered" :key="item.id">
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-bold shrink-0" x-text="(item.user?.name ?? '?')[0].toUpperCase()"></div>
                                            <div>
                                                <p class="font-medium text-slate-800" x-text="item.user?.name ?? '—'"></p>
                                                <p class="text-xs text-slate-400" x-text="item.user?.email ?? ''"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="font-mono text-xs" x-text="item.employee_id_number ?? '—'"></td>
                                    <td>
                                        <span class="badge-blue" x-text="item.specialization ?? '—'"></span>
                                    </td>
                                    <td>
                                        <p class="text-slate-500 text-xs line-clamp-2 max-w-64" x-text="item.bio ?? '—'"></p>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-1 justify-end">
                                            <button x-show="canWrite" @click="openEdit(item)" class="btn-ghost btn-sm text-indigo-600">{{ __('app.edit') }}</button>
                                            <button x-show="canWrite" @click="confirmDelete(item.id)" class="btn-ghost btn-sm text-red-500">{{ __('app.delete') }}</button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </template>
        </div>
    </template>

    <template x-teleport="body">
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <div class="modal-backdrop" @click="showModal = false"></div>
            <div class="modal-panel" @click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <h3 class="font-semibold text-slate-900" x-text="editingId ? '{{ __('app.edit_teacher') }}' : '{{ __('app.add_teacher') }}'"></h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="form-label">{{ __('app.full_name') }}</label>
                            <input x-model="form.name" type="text" class="form-input" placeholder="{{ __('app.full_name') }}">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="form-label">{{ __('app.email') }}</label>
                            <input x-model="form.email" type="email" class="form-input" placeholder="{{ __('app.email') }}">
                        </div>
                        <template x-if="!editingId">
                            <div class="col-span-2 sm:col-span-1">
                                <label class="form-label">{{ __('app.password') }}</label>
                                <input x-model="form.password" type="password" class="form-input" placeholder="{{ __('app.password') }}">
                            </div>
                        </template>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="form-label">{{ __('app.employee_id') }}</label>
                            <input x-model="form.employee_id_number" type="text" class="form-input" placeholder="{{ __('app.employee_id') }}">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="form-label">{{ __('app.specialization') }}</label>
                            <input x-model="form.specialization" type="text" class="form-input" placeholder="{{ __('app.specialization') }}">
                        </div>
                        <div class="col-span-2">
                            <label class="form-label">{{ __('app.bio') }}</label>
                            <textarea x-model="form.bio" rows="3" class="form-textarea" placeholder="{{ __('app.bio') }}"></textarea>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl">
                    <button @click="showModal = false" class="btn-secondary">{{ __('app.cancel') }}</button>
                    <button @click="save()" class="btn-primary" x-text="editingId ? '{{ __('app.save_changes') }}' : '{{ __('app.create_teacher') }}'"></button>
                </div>
            </div>
        </div>
    </template>

    @include('partials.delete-confirm')
</div>
</x-layouts.app>
