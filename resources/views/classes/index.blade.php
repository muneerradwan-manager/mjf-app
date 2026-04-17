<x-layouts.app :title="__('app.classes')">
<div x-data="classesPage()" x-init="init()" x-cloak>

    <div class="page-header">
        <div>
            <h2 class="page-title">{{ __('app.classes') }}</h2>
            <p class="page-subtitle" x-text="`${items.length} {{ __('app.classes_subtitle', ['count' => '']) }}`.replace(/^\d+\s/, items.length + ' ')"></p>
        </div>
        <button x-show="canCreate" @click="openCreate()" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ __('app.new_class') }}
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
                        <th>{{ __('app.class_name') }}</th>
                        <th>{{ __('app.course') }}</th>
                        <th>{{ __('app.teacher') }}</th>
                        <th>{{ __('app.dates') }}</th>
                        <th>{{ __('app.schedule') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in filtered" :key="item.id">
                        <tr>
                            <td>
                                <p class="font-medium text-slate-800" x-text="item.name"></p>
                                <p class="text-xs text-slate-400 mt-0.5" x-text="item.description ?? ''"></p>
                            </td>
                            <td x-text="item.course?.name ?? '—'"></td>
                            <td x-text="item.teacher?.user?.name ?? '—'"></td>
                            <td>
                                <p class="text-xs" x-text="formatDate(item.start_date)"></p>
                                <p class="text-xs text-slate-400" x-text="item.end_date ? '→ ' + formatDate(item.end_date) : ''"></p>
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    <template x-for="slot in (item.schedule ?? [])" :key="slot.day">
                                        <span class="badge-slate text-xs capitalize" x-text="slot.day.slice(0,3) + ' ' + slot.start"></span>
                                    </template>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-1 justify-end">
                            <button x-show="canEditItem(item)" @click="openEdit(item)" class="btn-ghost btn-sm text-indigo-600">{{ __('app.edit') }}</button>
                            <button x-show="canEditItem(item)" @click="confirmDelete(item.id)" class="btn-ghost btn-sm text-red-500">{{ __('app.delete') }}</button>
                        </div>
                    </td>
                </tr>
            </template>
            <template x-if="filtered.length === 0">
                <tr><td colspan="6" class="text-center py-12 text-slate-400">{{ __('app.no_classes') }}</td></tr>
            </template>
        </tbody>
    </table>
</div>
</template>

<template x-teleport="body">
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="modal-backdrop" @click="showModal = false"></div>
        <div class="modal-panel-lg" @click.stop>
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-900" x-text="editingId ? '{{ __('app.edit_class') }}' : '{{ __('app.new_class') }}'"></h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="form-label">{{ __('app.class_name') }}</label>
                        <input x-model="form.name" type="text" class="form-input" placeholder="{{ __('app.class_name') }}">
                    </div>
                    <div class="col-span-2">
                        <label class="form-label">{{ __('app.description') }}</label>
                        <input x-model="form.description" type="text" class="form-input" placeholder="{{ __('app.description') }}">
                    </div>
                    <div>
                        <label class="form-label">{{ __('app.course') }}</label>
                        <select x-model="form.course_id" class="form-select">
                            <option value="">{{ __('app.select_course') }}</option>
                            <template x-for="c in courses" :key="c.id">
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
                        <label class="form-label">{{ __('app.start_date') }}</label>
                        <input x-model="form.start_date" type="date" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">{{ __('app.end_date') }}</label>
                        <input x-model="form.end_date" type="date" class="form-input">
                    </div>
                </div>

                {{-- Schedule builder --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="form-label mb-0">{{ __('app.weekly_schedule') }}</label>
                        <button @click="addScheduleRow()" type="button" class="text-xs text-indigo-600 hover:underline font-medium">{{ __('app.add_slot') }}</button>
                    </div>
                    <div class="space-y-2">
                        <template x-for="(slot, i) in form.schedule" :key="i">
                            <div class="flex items-center gap-2">
                                <select x-model="slot.day" class="form-select flex-1 capitalize">
                                    <template x-for="d in days" :key="d">
                                        <option :value="d" x-text="d.charAt(0).toUpperCase() + d.slice(1)"></option>
                                    </template>
                                </select>
                                <input x-model="slot.start" type="time" class="form-input w-28">
                                <span class="text-slate-400 text-sm">→</span>
                                <input x-model="slot.end" type="time" class="form-input w-28">
                                <button @click="removeScheduleRow(i)" type="button" class="text-slate-400 hover:text-red-500 transition-colors shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl">
                <button @click="showModal = false" class="btn-secondary">{{ __('app.cancel') }}</button>
                <button @click="save()" class="btn-primary" x-text="editingId ? '{{ __('app.save_changes') }}' : '{{ __('app.create_class') }}'"></button>
            </div>
        </div>
    </template>

    @include('partials.delete-confirm')
</div>
</x-layouts.app>
