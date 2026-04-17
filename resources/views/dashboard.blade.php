<x-layouts.app title="{{ __('app.dashboard') }}">
<div x-data="dashboard()" x-init="init()" x-cloak data-labels="{{ json_encode([
    'students' => __('app.students'),
    'teachers' => __('app.teachers'),
    'courses' => __('app.courses'),
    'classes' => __('app.classes'),
    'enrollments' => __('app.enrollments'),
    'assignments' => __('app.assignments'),
    'my_classes' => __('app.my_classes'),
    'my_assignments' => __('app.my_assignments'),
    'my_students' => __('app.my_students'),
    'enrolled_classes' => __('app.enrolled_classes'),
    'assignments_due' => __('app.assignments_due'),
    'active' => __('app.status_active'),
    'completed' => __('app.status_completed'),
    'dropped' => __('app.status_dropped'),
]) }}">

    {{-- Page header --}}
    <div class="page-header">
        <div>
            <h2 class="page-title">{{ __('app.dashboard') }}</h2>
            <p class="page-subtitle">{{ __('app.welcome_greeting', ['name' => session('user.name')]) }}</p>
        </div>
        <div class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            {{ now()->format('D, d M Y') }}
        </div>
    </div>

    {{-- Loading skeleton --}}
    <template x-if="loading">
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
            <template x-for="i in 6">
                <div class="card p-5 animate-pulse">
                    <div class="w-12 h-12 rounded-xl bg-slate-200 dark:bg-slate-700 mb-3"></div>
                    <div class="h-7 w-16 bg-slate-200 dark:bg-slate-700 rounded mb-1"></div>
                    <div class="h-4 w-24 bg-slate-100 dark:bg-slate-600 rounded"></div>
                </div>
            </template>
        </div>
    </template>

    {{-- Stat cards --}}
    <template x-if="!loading">
        <div>
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
                <template x-for="stat in stats" :key="stat.label">
                    <a :href="stat.route" class="stat-card hover:shadow-md hover:-translate-y-0.5 transition-all duration-150 no-underline">
                        <div class="stat-icon" :class="stat.bg"><svg class="w-6 h-6" :class="stat.color" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stat.icon"/></svg></div>
                        <div>
                            <div class="text-2xl font-bold text-slate-900 dark:text-white" x-text="stat.value"></div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5" x-text="stat.label"></div>
                        </div>
                    </a>
                </template>
            </div>

            {{-- Charts row --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="card p-5 lg:col-span-2">
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100 mb-4">{{ __('app.enrollments_per_class') }}</h3>
                    <canvas id="enrollmentChart" height="180"></canvas>
                </div>
                <div class="card p-5">
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100 mb-4">{{ __('app.enrollment_status') }}</h3>
                    <canvas id="statusChart" height="180"></canvas>
                </div>
            </div>

            {{-- Activity row --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Recent announcements --}}
                <div class="card">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="font-semibold text-slate-800 dark:text-slate-100">{{ __('app.recent_announcements') }}</h3>
                        <a href="{{ route('announcements') }}" class="text-xs text-[#016D5D] dark:text-[#289E92] hover:underline font-medium">{{ __('app.view_all') }}</a>
                    </div>
                    <template x-if="recentAnnouncements.length === 0">
                        <div class="empty-state py-10 text-slate-400 dark:text-slate-500 text-sm">{{ __('app.no_announcements') }}</div>
                    </template>
                    <ul class="divide-y divide-slate-100 dark:divide-slate-700">
                        <template x-for="item in recentAnnouncements" :key="item.id">
                            <li class="px-5 py-3.5 flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#E4DDD3] dark:bg-[#00594F]/40 flex items-center justify-center text-[#016D5D] dark:text-[#289E92] shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate" x-text="item.title"></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1" x-text="item.content"></p>
                                </div>
                                <span class="text-xs text-slate-400 dark:text-slate-500 shrink-0" x-text="item.audience_type"></span>
                            </li>
                        </template>
                    </ul>
                </div>

                {{-- Upcoming events --}}
                <div class="card">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="font-semibold text-slate-800 dark:text-slate-100">{{ __('app.upcoming_events') }}</h3>
                        <a href="{{ route('events') }}" class="text-xs text-[#016D5D] dark:text-[#289E92] hover:underline font-medium">{{ __('app.view_all') }}</a>
                    </div>
                    <template x-if="upcomingEvents.length === 0">
                        <div class="empty-state py-10 text-slate-400 dark:text-slate-500 text-sm">{{ __('app.no_events') }}</div>
                    </template>
                    <ul class="divide-y divide-slate-100 dark:divide-slate-700">
                        <template x-for="item in upcomingEvents" :key="item.id">
                            <li class="px-5 py-3.5 flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate" x-text="item.title"></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5" x-text="item.location"></p>
                                </div>
                                <span class="text-xs text-slate-400 dark:text-slate-500 shrink-0 text-right" x-text="item.start_date ? new Date(item.start_date).toLocaleDateString() : ''"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
@endpush
</x-layouts.app>
