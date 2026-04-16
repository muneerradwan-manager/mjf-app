{{-- Legacy layout — kept in sync with resources/views/components/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
      class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="api-token" content="{{ session('api_token') }}">
    <title>{{ $title ?? __('app.dashboard') }} — {{ __('app.platform_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>try{if(JSON.parse(localStorage.getItem('mjf-dark')))document.documentElement.classList.add('dark')}catch(e){}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="h-full bg-slate-50 dark:bg-slate-900 font-sans antialiased" x-data="appShell()">

{{-- Toast --}}
<div x-data="toastManager()" @toast.window="add($event.detail.message, $event.detail.type ?? 'success')"
     class="fixed top-4 right-4 z-[100] flex flex-col gap-2" x-cloak>
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="true"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             :class="toast.type === 'error' ? 'toast-error' : toast.type === 'info' ? 'toast-info' : 'toast-success'">
            <svg x-show="toast.type !== 'error'" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <svg x-show="toast.type === 'error'" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span x-text="toast.message"></span>
        </div>
    </template>
</div>

<div class="flex h-full">
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-30 flex w-64 flex-col bg-[#00594F] transition-transform duration-200 ease-in-out lg:static lg:translate-x-0">
        <div class="flex h-16 items-center gap-3 px-5 border-b border-white/10">
            <div class="w-8 h-8 rounded-lg bg-[#289E92] flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <span class="text-white font-bold text-lg tracking-tight">{{ __('app.platform_name') }}</span>
        </div>

        @if(session('tenants') && count(session('tenants')) > 0)
        <div class="px-4 py-3 border-b border-white/10">
            <p class="text-[#D9C89D] text-[10px] font-semibold uppercase tracking-wider mb-1.5">{{ __('app.active_tenant') }}</p>
            <form method="POST" action="{{ route('switch-tenant') }}" id="tenantSwitchForm">
                @csrf
                <input type="hidden" name="tenant_id" id="tenantIdInput" value="{{ session('current_tenant.id') }}">
            </form>
            <select onchange="document.getElementById('tenantIdInput').value=this.value; document.getElementById('tenantSwitchForm').submit()"
                    class="w-full bg-white/10 border border-white/10 text-white text-sm rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-1 focus:ring-[#289E92]">
                @foreach(session('tenants') as $tenant)
                    <option value="{{ $tenant['id'] }}" {{ $tenant['id'] == session('current_tenant.id') ? 'selected' : '' }} class="text-slate-900">
                        {{ $tenant['name'] }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
            <p class="text-[#D9C89D] text-[10px] font-semibold uppercase tracking-wider px-3 mb-2">{{ __('app.nav_main') }}</p>
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                {{ __('app.nav_dashboard') }}
            </a>
            <p class="text-[#D9C89D] text-[10px] font-semibold uppercase tracking-wider px-3 mt-4 mb-2">{{ __('app.nav_people') }}</p>
            <a href="{{ route('students') }}" class="sidebar-link {{ request()->routeIs('students') ? 'active' : '' }}">
                <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                {{ __('app.nav_students') }}
            </a>
            <a href="{{ route('teachers') }}" class="sidebar-link {{ request()->routeIs('teachers') ? 'active' : '' }}">
                <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                {{ __('app.nav_teachers') }}
            </a>
            <p class="text-[#D9C89D] text-[10px] font-semibold uppercase tracking-wider px-3 mt-4 mb-2">{{ __('app.nav_academics') }}</p>
            <a href="{{ route('courses') }}" class="sidebar-link {{ request()->routeIs('courses') ? 'active' : '' }}">
                <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                {{ __('app.nav_courses') }}
            </a>
            <a href="{{ route('classes') }}" class="sidebar-link {{ request()->routeIs('classes') ? 'active' : '' }}">
                <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ __('app.nav_classes') }}
            </a>
            <a href="{{ route('enrollments') }}" class="sidebar-link {{ request()->routeIs('enrollments') ? 'active' : '' }}">
                <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                {{ __('app.nav_enrollments') }}
            </a>
            <a href="{{ route('assignments') }}" class="sidebar-link {{ request()->routeIs('assignments') ? 'active' : '' }}">
                <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                {{ __('app.nav_assignments') }}
            </a>
            <p class="text-[#D9C89D] text-[10px] font-semibold uppercase tracking-wider px-3 mt-4 mb-2">{{ __('app.nav_communication') }}</p>
            <a href="{{ route('announcements') }}" class="sidebar-link {{ request()->routeIs('announcements') ? 'active' : '' }}">
                <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                {{ __('app.nav_announcements') }}
            </a>
            <a href="{{ route('events') }}" class="sidebar-link {{ request()->routeIs('events') ? 'active' : '' }}">
                <svg class="w-4.5 h-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ __('app.nav_events') }}
            </a>
        </nav>

        <div class="border-t border-white/10 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-[#289E92] flex items-center justify-center text-white text-xs font-bold shrink-0">
                    {{ strtoupper(substr(session('user.name', 'U'), 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-medium truncate">{{ session('user.name') }}</p>
                    <p class="text-[#D9C89D] text-xs truncate">{{ session('user.email') }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit" title="{{ __('app.logout') }}" class="text-[#D9C89D] hover:text-white transition-colors">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <header class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 flex items-center gap-4 px-4 lg:px-6 shrink-0">
            <button @click="toggleSidebar()" class="lg:hidden btn-ghost p-1.5 -ml-1">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="flex-1">
                <h1 class="font-semibold text-slate-800 dark:text-slate-100">{{ session('current_tenant.name', __('app.select_tenant')) }}</h1>
            </div>
            <div class="flex items-center gap-1 text-sm border border-slate-200 dark:border-slate-600 rounded-lg px-2 py-1">
                <a href="{{ route('language', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'text-[#016D5D] dark:text-[#289E92] font-semibold' : 'text-slate-400' }} text-xs">EN</a>
                <span class="text-slate-300">|</span>
                <a href="{{ route('language', 'ar') }}" class="{{ app()->getLocale() === 'ar' ? 'text-[#016D5D] dark:text-[#289E92] font-semibold' : 'text-slate-400' }} text-xs">عربي</a>
            </div>
            <button @click="toggleTheme()" class="btn-ghost p-1.5">
                <svg x-show="isDark" class="w-4.5 h-4.5 text-[#D9C89D]" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-10h-1M4.34 12h-1m15.07-6.07-.71.71M6.34 17.66l-.71.71m12.73 0-.71-.71M6.34 6.34l-.71-.71M12 5a7 7 0 100 14A7 7 0 0012 5z"/></svg>
                <svg x-show="!isDark" class="w-4.5 h-4.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
            </button>
            <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span class="hidden sm:inline">{{ session('current_tenant.slug', '') }}</span>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto">
            <div class="p-4 lg:p-6 max-w-7xl mx-auto">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
