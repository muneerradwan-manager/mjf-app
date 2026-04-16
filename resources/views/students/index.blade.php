<x-layouts.app :title="__('app.students')">
<div x-data="studentsPage()" x-init="init()" x-cloak>

    <div class="page-header">
        <div>
            <h2 class="page-title">{{ __('app.students') }}</h2>
            <p class="page-subtitle" x-text="`${items.length} {{ __('app.students_subtitle', ['count' => '']) }}`.replace(/^\d+\s/, items.length + ' ')"></p>
        </div>
        <button x-show="canCreate" @click="openCreate()" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ __('app.add_student') }}
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
            <svg class="w-8 h-8 animate-spin mx-auto mb-3 text-primary-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            {{ __('app.loading') }}
        </div>
    </template>

    <template x-if="!loading">
        <div>
            <template x-if="filtered.length === 0">
                <div class="card empty-state text-slate-400">
                    <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <p class="font-medium text-slate-600">{{ __('app.no_students') }}</p>
                    <p class="text-sm mt-1">{{ __('app.no_students_hint') }}</p>
                </div>
            </template>
            <template x-if="filtered.length > 0">
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('app.student') }}</th>
                                <th>{{ __('app.student_id_number') }}</th>
                                <th>{{ __('app.date_of_birth') }}</th>
                                <th>{{ __('app.phone') }}</th>
                                <th>{{ __('app.parent') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="item in filtered" :key="item.id">
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 text-xs font-bold shrink-0" x-text="(item.user?.name ?? item.name ?? '?')[0].toUpperCase()"></div>
                                            <div>
                                                <p class="font-medium text-slate-800" x-text="item.user?.name ?? item.name ?? '—'"></p>
                                                <p class="text-xs text-slate-400" x-text="item.user?.email ?? ''"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="font-mono text-xs" x-text="item.student_id_number ?? '—'"></td>
                                    <td x-text="item.date_of_birth ?? '—'"></td>
                                    <td x-text="item.phone ?? '—'"></td>
                                    <td>
                                        <div x-text="item.parent_name ?? '—'"></div>
                                        <div class="text-xs text-slate-400" x-text="item.parent_phone ?? ''"></div>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-1 justify-end">
                                            <button x-show="canWrite" @click="openEdit(item)" class="btn-ghost btn-sm text-primary-600">{{ __('app.edit') }}</button>
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
            <div class="modal-panel-lg" @click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <h3 class="font-semibold text-slate-900" x-text="editingId ? '{{ __('app.edit_student') }}' : '{{ __('app.add_student') }}'"></h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="form-label">{{ __('app.full_name') }}</label>
                            <input x-model="form.name" type="text" class="form-input" :class="errors.name && 'error'" placeholder="Ahmad Al-Ghamdi">
                            <p x-show="errors.name" x-text="errors.name?.[0]" class="form-error"></p>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="form-label">{{ __('app.email') }}</label>
                            <input x-model="form.email" type="email" class="form-input" :class="errors.email && 'error'" placeholder="student@school.edu">
                            <p x-show="errors.email" x-text="errors.email?.[0]" class="form-error"></p>
                        </div>
                        <template x-if="!editingId">
                            <div class="col-span-2 sm:col-span-1">
                                <label class="form-label">{{ __('app.password') }}</label>
                                <input x-model="form.password" type="password" class="form-input" :class="errors.password && 'error'" placeholder="min. 8 characters">
                                <p x-show="errors.password" x-text="errors.password?.[0]" class="form-error"></p>
                            </div>
                        </template>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="form-label">{{ __('app.student_id_number') }}</label>
                            <input x-model="form.student_id_number" type="text" class="form-input" placeholder="STD-001">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="form-label">{{ __('app.date_of_birth') }}</label>
                            <input x-model="form.date_of_birth" type="date" class="form-input">
                        </div>
                        <div class="col-span-2">
                            <label class="form-label">{{ __('app.address') }}</label>
                            <input x-model="form.address" type="text" class="form-input" placeholder="Riyadh, Saudi Arabia">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="form-label">{{ __('app.phone') }}</label>
                            <input x-model="form.phone" type="text" class="form-input" placeholder="+966501234567">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="form-label">{{ __('app.parent_name') }}</label>
                            <input x-model="form.parent_name" type="text" class="form-input" placeholder="Parent / Guardian">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="form-label">{{ __('app.parent_phone') }}</label>
                            <input x-model="form.parent_phone" type="text" class="form-input" placeholder="+966501234567">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl">
                    <button @click="showModal = false" class="btn-secondary">{{ __('app.cancel') }}</button>
                    <button @click="save()" class="btn-primary" x-text="editingId ? '{{ __('app.save_changes') }}' : '{{ __('app.create_student') }}'"></button>
                </div>
            </div>
        </div>
    </template>

    @include('partials.delete-confirm')
</div>
</x-layouts.app>
