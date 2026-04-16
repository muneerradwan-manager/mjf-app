<x-layouts.guest title="{{ __('app.sign_in') }}">
<div class="min-h-screen flex">

    {{-- Left panel --}}
    <div class="hidden lg:flex flex-col justify-between w-1/2 bg-[#00594F] p-12">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-[#289E92] flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <span class="text-white font-bold text-xl">{{ __('app.platform_name') }}</span>
        </div>

        <div>
            <h2 class="text-4xl font-bold text-white leading-tight mb-4">
                Empowering<br>Education<br>at Every Scale.
            </h2>
            <p class="text-[#D9C89D] text-lg leading-relaxed">
                Manage students, teachers, courses, and more — all from a single, beautiful dashboard.
            </p>
        </div>

        <div class="grid grid-cols-3 gap-4">
            @foreach([['15+', 'Students per tenant'], ['5', 'Courses available'], ['108', 'Graded submissions']] as [$num, $label])
            <div class="bg-white/10 rounded-xl p-4">
                <div class="text-2xl font-bold text-white">{{ $num }}</div>
                <div class="text-[#D9C89D] text-xs mt-1">{{ $label }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Right panel —— Login form --}}
    <div class="flex-1 flex flex-col items-center justify-center px-6 py-12 bg-white dark:bg-slate-900">

        {{-- Top controls --}}
        <div class="w-full max-w-sm flex justify-end gap-2 mb-6">
            {{-- Language switcher --}}
            <div class="flex items-center gap-1 text-sm border border-slate-200 dark:border-slate-600 rounded-lg px-2 py-1">
                <a href="{{ route('language', 'en') }}"
                   class="{{ app()->getLocale() === 'en' ? 'text-[#016D5D] dark:text-[#289E92] font-semibold' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }} transition-colors text-xs">EN</a>
                <span class="text-slate-300 dark:text-slate-600">|</span>
                <a href="{{ route('language', 'ar') }}"
                   class="{{ app()->getLocale() === 'ar' ? 'text-[#016D5D] dark:text-[#289E92] font-semibold' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }} transition-colors text-xs">عربي</a>
            </div>
            {{-- Theme toggle --}}
            <button @click="toggleTheme()" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors border border-slate-200 dark:border-slate-600">
                <svg x-show="isDark" class="w-4 h-4 text-[#D9C89D]" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-10h-1M4.34 12h-1m15.07-6.07-.71.71M6.34 17.66l-.71.71m12.73 0-.71-.71M6.34 6.34l-.71-.71M12 5a7 7 0 100 14A7 7 0 0012 5z"/>
                </svg>
                <svg x-show="!isDark" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                </svg>
            </button>
        </div>

        <div class="w-full max-w-sm">

            {{-- Logo (mobile only) --}}
            <div class="lg:hidden flex items-center gap-2 mb-8">
                <div class="w-8 h-8 rounded-lg bg-[#016D5D] flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <span class="font-bold text-slate-900 dark:text-white">{{ __('app.platform_name') }}</span>
            </div>

            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">{{ __('app.welcome_back') }}</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mb-8">{{ __('app.sign_in_subtitle') }}</p>

            @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6 flex gap-3">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div class="text-sm text-red-700 dark:text-red-400">{{ $errors->first() }}</div>
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="form-label" for="email">{{ __('app.email') }}</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email', 'admin@mjf.edu') }}"
                        required
                        autofocus
                        class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                        placeholder="you@example.com"
                    >
                </div>

                <div>
                    <label class="form-label" for="password">{{ __('app.password') }}</label>
                    <div class="relative" x-data="{ show: false }">
                        <input
                            id="password"
                            :type="show ? 'text' : 'password'"
                            name="password"
                            value="{{ old('password', 'password') }}"
                            required
                            class="form-input pr-10"
                            placeholder="••••••••"
                        >
                        <button
                            type="button"
                            @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                        >
                            <svg x-show="!show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" class="w-4 h-4" x-cloak fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full justify-center py-2.5 text-base">
                    {{ __('app.sign_in') }}
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-3 font-medium">{{ __('app.quick_demo') }}</p>
                <div class="space-y-2 text-xs">
                    @foreach([
                        ['admin@mjf.edu',       'Super Admin',      'bg-[#E4DDD3] text-[#016D5D]'],
                        ['owner@alnour.edu',    'Al-Nour Owner',    'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300'],
                        ['owner@alfurqan.edu',  'Al-Furqan Owner',  'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300'],
                    ] as [$email, $role, $badge])
                    <div
                        class="flex items-center justify-between bg-slate-50 dark:bg-slate-800 rounded-lg px-3 py-2 cursor-pointer hover:bg-[#E4DDD3] dark:hover:bg-slate-700 transition-colors"
                        onclick="document.getElementById('email').value='{{ $email }}'"
                    >
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $badge }}">{{ $role }}</span>
                        <span class="font-mono text-slate-800 dark:text-slate-200 select-all">{{ $email }}</span>
                    </div>
                    @endforeach
                    <p class="text-slate-400 dark:text-slate-500 text-center pt-1">{{ __('app.all_passwords') }} <span class="font-mono font-semibold text-slate-600 dark:text-slate-300">password</span></p>
                    <p class="text-slate-400 dark:text-slate-500 text-center text-xs">{{ __('app.click_fill_email') }}</p>
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-slate-400 dark:text-slate-500">
                <a href="{{ route('home') }}" class="text-[#016D5D] dark:text-[#289E92] hover:underline">{{ __('app.back_home') }}</a>
            </p>
        </div>
    </div>
</div>
</x-layouts.guest>
