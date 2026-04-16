<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
      class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.platform_name') }} — Multi-Tenant Educational Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- Prevent dark-mode flash --}}
    <script>try{if(JSON.parse(localStorage.getItem('mjf-dark')))document.documentElement.classList.add('dark')}catch(e){}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100" x-data="appShell()">

{{-- ─── Navbar ──────────────────────────────────────────────────────────────── --}}
<header class="fixed top-0 inset-x-0 z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm border-b border-slate-200/60 dark:border-slate-700/60">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-[#016D5D] flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <span class="text-lg font-bold text-slate-900 dark:text-white">{{ __('app.platform_name') }}</span>
        </div>
        <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600 dark:text-slate-300">
            <a href="#features" class="hover:text-[#016D5D] dark:hover:text-[#289E92] transition-colors">{{ __('app.features') }}</a>
            <a href="#pricing"  class="hover:text-[#016D5D] dark:hover:text-[#289E92] transition-colors">{{ __('app.pricing') }}</a>
        </nav>
        <div class="flex items-center gap-3">
            {{-- Language switcher --}}
            <div class="flex items-center gap-1 text-sm border border-slate-200 dark:border-slate-600 rounded-lg px-2 py-1">
                <a href="{{ route('language', 'en') }}"
                   class="{{ app()->getLocale() === 'en' ? 'text-[#016D5D] dark:text-[#289E92] font-semibold' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }} transition-colors text-xs">
                    EN
                </a>
                <span class="text-slate-300 dark:text-slate-600">|</span>
                <a href="{{ route('language', 'ar') }}"
                   class="{{ app()->getLocale() === 'ar' ? 'text-[#016D5D] dark:text-[#289E92] font-semibold' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300' }} transition-colors text-xs">
                    عربي
                </a>
            </div>
            {{-- Theme toggle --}}
            <button @click="toggleTheme()" class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <svg x-show="isDark" class="w-4.5 h-4.5 text-[#D9C89D]" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-10h-1M4.34 12h-1m15.07-6.07-.71.71M6.34 17.66l-.71.71m12.73 0-.71-.71M6.34 6.34l-.71-.71M12 5a7 7 0 100 14A7 7 0 0012 5z"/>
                </svg>
                <svg x-show="!isDark" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                </svg>
            </button>
            <a href="{{ route('login') }}" class="btn-primary text-sm py-2 px-4">
                {{ __('app.sign_in_arrow') }}
            </a>
        </div>
    </div>
</header>

{{-- ─── Hero ────────────────────────────────────────────────────────────────── --}}
<section class="pt-32 pb-20 px-4">
    <div class="max-w-4xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 bg-[#E4DDD3] dark:bg-[#00594F]/30 border border-[#D9C89D] dark:border-[#016D5D] text-[#016D5D] dark:text-[#289E92] text-xs font-semibold px-3 py-1.5 rounded-full mb-6">
            <span class="w-1.5 h-1.5 rounded-full bg-[#289E92] animate-pulse"></span>
            {{ __('app.hero_badge') }}
        </div>
        <h1 class="text-5xl md:text-6xl font-extrabold text-slate-900 dark:text-white leading-tight tracking-tight mb-6">
            {{ __('app.hero_title') }}<br>
            <span class="text-[#016D5D] dark:text-[#289E92]">{{ __('app.hero_title_span') }}</span>
        </h1>
        <p class="text-xl text-slate-500 dark:text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed">
            {{ __('app.hero_subtitle') }}
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('login') }}" class="btn-primary px-6 py-3 text-base w-full sm:w-auto justify-center">
                {{ __('app.get_started_free') }}
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            <a href="#features" class="btn-secondary px-6 py-3 text-base w-full sm:w-auto justify-center">
                {{ __('app.see_features') }}
            </a>
        </div>
    </div>

    {{-- Dashboard preview --}}
    <div class="max-w-5xl mx-auto mt-16 rounded-2xl overflow-hidden shadow-2xl shadow-[#016D5D]/10 border border-slate-200 dark:border-slate-700">
        <div class="bg-[#00594F] h-9 flex items-center px-4 gap-1.5">
            <div class="w-3 h-3 rounded-full bg-red-400/60"></div>
            <div class="w-3 h-3 rounded-full bg-amber-400/60"></div>
            <div class="w-3 h-3 rounded-full bg-emerald-400/60"></div>
            <div class="flex-1 mx-4">
                <div class="bg-white/10 rounded text-white/50 text-xs px-3 py-0.5 text-center max-w-48 mx-auto">localhost:8000/dashboard</div>
            </div>
        </div>
        <div class="bg-slate-50 dark:bg-slate-800 p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([['Students','👨‍🎓','15'],['Teachers','👩‍🏫','5'],['Courses','📚','5'],['Classes','📅','10']] as [$label,$emoji,$count])
            <div class="bg-white dark:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-600 p-4 shadow-sm">
                <div class="text-2xl mb-2">{{ $emoji }}</div>
                <div class="text-2xl font-bold text-slate-800 dark:text-white">{{ $count }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $label }}</div>
            </div>
            @endforeach
        </div>
        <div class="bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 px-6 pb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-600 p-4 shadow-sm h-28 flex items-center justify-center gap-2 text-slate-400 dark:text-slate-500 text-sm">
                <span class="text-2xl">📊</span> Enrollment by Class
            </div>
            <div class="bg-white dark:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-600 p-4 shadow-sm h-28 flex items-center justify-center gap-2 text-slate-400 dark:text-slate-500 text-sm">
                <span class="text-2xl">🍩</span> Enrollment Status
            </div>
        </div>
    </div>
</section>

{{-- ─── Features ────────────────────────────────────────────────────────────── --}}
<section id="features" class="py-20 bg-slate-50 dark:bg-slate-800/50 px-4">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-14">
            <h2 class="text-4xl font-bold text-slate-900 dark:text-white mb-3">{{ __('app.features_heading') }}</h2>
            <p class="text-slate-500 dark:text-slate-400 text-lg">{{ __('app.features_subtitle') }}</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach([
                ['Multi-Tenancy',         'Each school gets its own isolated database. Complete data separation guaranteed.', '🏛️'],
                ['Student Management',    'Full student profiles with parent contacts, ID numbers, and academic history.',  '👨‍🎓'],
                ['Teacher Profiles',      'Staff with specializations, employee IDs, and rich bio information.',            '👩‍🏫'],
                ['Courses & Classes',     'Flexible course catalogue with multiple sections and weekly schedules.',          '📚'],
                ['Enrollments',           'Enroll students in classes. Track active, completed, and dropped statuses.',     '📋'],
                ['Assignments & Grading', 'Create assignments, collect submissions, record grades and feedback.',           '✏️'],
                ['Announcements',         'Broadcast messages to all, students only, teachers only, or a single class.',   '📢'],
                ['Events Calendar',       'Plan and publish school events with location, dates, and descriptions.',         '📅'],
                ['Role-Based Access',     'Super admins, owners, teachers, and students — each with the right access.',    '🔐'],
            ] as [$title, $desc, $icon])
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                <div class="text-3xl mb-3">{{ $icon }}</div>
                <h3 class="font-semibold text-slate-800 dark:text-white mb-1.5">{{ $title }}</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── Pricing ─────────────────────────────────────────────────────────────── --}}
<section id="pricing" class="py-20 px-4 dark:bg-slate-900">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-14">
            <h2 class="text-4xl font-bold text-slate-900 dark:text-white mb-3">{{ __('app.pricing_heading') }}</h2>
            <p class="text-slate-500 dark:text-slate-400 text-lg">{{ __('app.pricing_subtitle') }}</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([
                ['Basic',    'Free',     '/mo',  'For small mosques & community circles', ['Up to 50 students', '5 teachers', '1 GB storage', 'Core features'],                    false],
                ['Standard', '$49.99',   '/mo',  'Full academic workflow for schools',    ['Up to 500 students', '50 teachers', '10 GB storage', 'Events & announcements'],         true],
                ['Premium',  '$199.99',  '/yr',  'Unlimited for large institutions',      ['Unlimited students & teachers', '100 GB storage', 'API access', 'Priority support'],  false],
            ] as [$name, $price, $period, $desc, $features, $featured])
            <div class="rounded-2xl border p-7 flex flex-col {{ $featured ? 'border-[#016D5D] bg-[#016D5D] text-white shadow-xl shadow-[#016D5D]/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' }}">
                <p class="text-sm font-semibold {{ $featured ? 'text-[#D9C89D]' : 'text-[#016D5D] dark:text-[#289E92]' }}">{{ $name }}</p>
                <div class="mt-3 flex items-end gap-1">
                    <span class="text-4xl font-extrabold {{ $featured ? 'text-white' : 'text-slate-900 dark:text-white' }}">{{ $price }}</span>
                    <span class="text-sm mb-1 {{ $featured ? 'text-[#D9C89D]' : 'text-slate-400 dark:text-slate-500' }}">{{ $period }}</span>
                </div>
                <p class="mt-2 text-sm {{ $featured ? 'text-[#E4DDD3]' : 'text-slate-500 dark:text-slate-400' }}">{{ $desc }}</p>
                <ul class="mt-6 space-y-2.5 flex-1">
                    @foreach($features as $f)
                    <li class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 shrink-0 {{ $featured ? 'text-[#D9C89D]' : 'text-emerald-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span class="{{ $featured ? 'text-[#E4DDD3]' : 'text-slate-600 dark:text-slate-300' }}">{{ $f }}</span>
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('login') }}" class="mt-8 block text-center text-sm font-semibold py-2.5 rounded-xl transition-all duration-150 {{ $featured ? 'bg-white text-[#016D5D] hover:bg-[#E4DDD3]' : 'bg-[#016D5D] text-white hover:bg-[#00594F]' }}">
                    {{ __('app.get_started_free') }} →
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── CTA ─────────────────────────────────────────────────────────────────── --}}
<section class="py-20 px-4 bg-[#00594F]">
    <div class="max-w-2xl mx-auto text-center">
        <h2 class="text-4xl font-bold text-white mb-4">{{ __('app.cta_heading') }}</h2>
        <p class="text-[#D9C89D] text-lg mb-8">{{ __('app.cta_subtitle') }}</p>
        <div class="bg-white/10 border border-white/20 rounded-xl p-5 mb-8 text-left max-w-sm mx-auto">
            <p class="text-[#D9C89D] text-xs font-semibold uppercase tracking-wider mb-3">{{ __('app.demo_credentials') }}</p>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-[#AC9E6F]">Email</span><span class="text-white font-mono">admin@mjf.edu</span></div>
                <div class="flex justify-between"><span class="text-[#AC9E6F]">Password</span><span class="text-white font-mono">password</span></div>
            </div>
        </div>
        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-[#289E92] hover:bg-[#D9C89D] text-white hover:text-[#00594F] font-bold px-8 py-3.5 rounded-xl transition-all duration-150 text-base">
            {{ __('app.open_dashboard') }}
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </a>
    </div>
</section>

<footer class="border-t border-slate-200 dark:border-slate-700 py-8 px-4 text-center text-sm text-slate-400 dark:bg-slate-900">
    {!! __('app.footer_copy', ['year' => date('Y')]) !!}
</footer>

</body>
</html>
