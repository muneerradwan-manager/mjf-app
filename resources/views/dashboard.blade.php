<x-layouts.app title="{{ __('app.dashboard') }}">
<div x-data="dashboard()" x-init="init()" x-cloak>

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
                <a href="{{ route('students') }}" class="stat-card hover:shadow-md hover:-translate-y-0.5 transition-all duration-150 no-underline">
                    <div class="stat-icon bg-[#E4DDD3] dark:bg-[#00594F]/40"><svg class="w-6 h-6 text-[#016D5D] dark:text-[#289E92]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900 dark:text-white" x-text="stats.students"></div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ __('app.nav_students') }}</div>
                    </div>
                </a>
                <a href="{{ route('teachers') }}" class="stat-card hover:shadow-md hover:-translate-y-0.5 transition-all duration-150 no-underline">
                    <div class="stat-icon bg-emerald-100 dark:bg-emerald-900/30"><svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg></div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900 dark:text-white" x-text="stats.teachers"></div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ __('app.nav_teachers') }}</div>
                    </div>
                </a>
                <a href="{{ route('courses') }}" class="stat-card hover:shadow-md hover:-translate-y-0.5 transition-all duration-150 no-underline">
                    <div class="stat-icon bg-amber-100 dark:bg-amber-900/30"><svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg></div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900 dark:text-white" x-text="stats.courses"></div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ __('app.nav_courses') }}</div>
                    </div>
                </a>
                <a href="{{ route('classes') }}" class="stat-card hover:shadow-md hover:-translate-y-0.5 transition-all duration-150 no-underline">
                    <div class="stat-icon bg-violet-100 dark:bg-violet-900/30"><svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900 dark:text-white" x-text="stats.classes"></div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ __('app.nav_classes') }}</div>
                    </div>
                </a>
                <a href="{{ route('enrollments') }}" class="stat-card hover:shadow-md hover:-translate-y-0.5 transition-all duration-150 no-underline">
                    <div class="stat-icon bg-sky-100 dark:bg-sky-900/30"><svg class="w-6 h-6 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900 dark:text-white" x-text="stats.enrollments"></div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ __('app.nav_enrollments') }}</div>
                    </div>
                </a>
                <a href="{{ route('assignments') }}" class="stat-card hover:shadow-md hover:-translate-y-0.5 transition-all duration-150 no-underline">
                    <div class="stat-icon bg-rose-100 dark:bg-rose-900/30"><svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900 dark:text-white" x-text="stats.assignments"></div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ __('app.nav_assignments') }}</div>
                    </div>
                </a>
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
