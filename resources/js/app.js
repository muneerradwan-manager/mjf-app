import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import { api } from './api.js';

window.Alpine = Alpine;
window.api = api;

Alpine.plugin(persist);

// ─── App config (from meta tags) ──────────────────────────────────────────────
const appConfig = {
    role:      document.querySelector('meta[name="user-role"]')?.content      ?? 'owner',
    profileId: parseInt(document.querySelector('meta[name="tenant-profile-id"]')?.content ?? '0') || 0,
    userId:    parseInt(document.querySelector('meta[name="tenant-user-id"]')?.content    ?? '0') || 0,
};
window.appConfig = appConfig;

const isAdmin   = ['super_admin', 'owner', 'admin'].includes(appConfig.role);
const isTeacher = appConfig.role === 'teacher';
const isStudent = appConfig.role === 'student';

// ─── Sidebar / App Shell ──────────────────────────────────────────────────────
Alpine.data('appShell', () => ({
    sidebarOpen: false,
    isDark: Alpine.$persist(false).as('mjf-dark'),
    toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; },
    toggleTheme() {
        this.isDark = !this.isDark;
        document.documentElement.classList.toggle('dark', this.isDark);
    },
    init() {
        document.documentElement.classList.toggle('dark', this.isDark);
    },
}));

// ─── Toast Notifications ─────────────────────────────────────────────────────
Alpine.data('toastManager', () => ({
    toasts: [],
    add(message, type = 'success') {
        const id = Date.now();
        this.toasts.push({ id, message, type });
        setTimeout(() => this.remove(id), 4000);
    },
    remove(id) { this.toasts = this.toasts.filter(t => t.id !== id); },
}));

// ─── Dashboard ───────────────────────────────────────────────────────────────
Alpine.data('dashboard', () => ({
    stats: [],
    subtitle: '',
    recentAnnouncements: [],
    upcomingEvents: [],
    upcomingAssignments: [],
    myClasses: [],
    myEnrollments: [],
    loading: true,

    async init() {
        try {
            if (isAdmin) {
                await this.loadAdminDashboard();
            } else if (isTeacher) {
                await this.loadTeacherDashboard();
            } else {
                await this.loadStudentDashboard();
            }
        } catch(e) {
            console.error(e);
        }
        this.loading = false;
    },

    async loadAdminDashboard() {
        const [students, teachers, courses, classes, enrollments, assignments, announcements, events] = await Promise.all([
            api.get('/api/tenant/students'),
            api.get('/api/tenant/teachers'),
            api.get('/api/tenant/courses'),
            api.get('/api/tenant/classes'),
            api.get('/api/tenant/enrollments'),
            api.get('/api/tenant/assignments'),
            api.get('/api/tenant/announcements'),
            api.get('/api/tenant/events'),
        ]);
        const icons = {
            students:    'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            teachers:    'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
            courses:     'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            classes:     'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            enrollments: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            assignments: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
        };
        const colors = [
            { bg: 'bg-blue-100',   color: 'text-blue-600'   },
            { bg: 'bg-violet-100', color: 'text-violet-600' },
            { bg: 'bg-indigo-100', color: 'text-indigo-600' },
            { bg: 'bg-emerald-100',color: 'text-emerald-600'},
            { bg: 'bg-amber-100',  color: 'text-amber-600'  },
            { bg: 'bg-rose-100',   color: 'text-rose-600'   },
        ];
        const counts = [
            [students,    'Students'],
            [teachers,    'Teachers'],
            [courses,     'Courses'],
            [classes,     'Classes'],
            [enrollments, 'Enrollments'],
            [assignments, 'Assignments'],
        ];
        this.stats = counts.map(([res, label], i) => ({
            value: res?.data?.length ?? 0,
            label,
            icon: icons[label.toLowerCase()],
            ...colors[i],
        }));
        this.subtitle = `${students?.data?.length ?? 0} students · ${teachers?.data?.length ?? 0} teachers`;
        this.recentAnnouncements = (announcements?.data ?? []).slice(0, 4);
        this.upcomingEvents      = (events?.data ?? []).slice(0, 4);

        this.$nextTick(() => this.drawCharts(classes?.data ?? [], enrollments?.data ?? []));
    },

    async loadTeacherDashboard() {
        const [classes, assignments, enrollments, announcements, events] = await Promise.all([
            api.get('/api/tenant/classes'),
            api.get('/api/tenant/assignments'),
            api.get('/api/tenant/enrollments'),
            api.get('/api/tenant/announcements'),
            api.get('/api/tenant/events'),
        ]);
        const allClasses     = classes?.data     ?? [];
        const allAssignments = assignments?.data ?? [];
        const allEnrollments = enrollments?.data ?? [];

        this.myClasses          = allClasses.filter(c => c.teacher_id === appConfig.profileId);
        const myClassIds        = new Set(this.myClasses.map(c => c.id));
        this.upcomingAssignments = allAssignments
            .filter(a => a.teacher_id === appConfig.profileId)
            .slice(0, 6);

        const myStudentCount = new Set(
            allEnrollments.filter(e => myClassIds.has(e.class_id)).map(e => e.student_id)
        ).size;

        this.stats = [
            { value: this.myClasses.length,          label: 'My Classes',     icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', bg: 'bg-emerald-100', color: 'text-emerald-600' },
            { value: this.upcomingAssignments.length, label: 'My Assignments', icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', bg: 'bg-indigo-100', color: 'text-indigo-600' },
            { value: myStudentCount,                  label: 'My Students',   icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', bg: 'bg-blue-100', color: 'text-blue-600' },
        ];
        this.subtitle             = `${this.myClasses.length} classes · ${myStudentCount} students`;
        this.recentAnnouncements  = (announcements?.data ?? []).slice(0, 4);
        this.upcomingEvents       = (events?.data ?? []).slice(0, 4);
    },

    async loadStudentDashboard() {
        const [enrollments, assignments, announcements, events] = await Promise.all([
            api.get('/api/tenant/enrollments'),
            api.get('/api/tenant/assignments'),
            api.get('/api/tenant/announcements'),
            api.get('/api/tenant/events'),
        ]);
        const allEnrollments = enrollments?.data ?? [];
        const allAssignments = assignments?.data ?? [];

        this.myEnrollments = allEnrollments.filter(e => e.student_id === appConfig.profileId);
        const myClassIds   = new Set(this.myEnrollments.map(e => e.class_id));

        this.upcomingAssignments = allAssignments
            .filter(a => myClassIds.has(a.class_id))
            .slice(0, 6);

        this.stats = [
            { value: this.myEnrollments.length,       label: 'Enrolled Classes',   icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', bg: 'bg-amber-100',  color: 'text-amber-600'  },
            { value: this.upcomingAssignments.length,  label: 'Assignments Due',    icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',                bg: 'bg-indigo-100', color: 'text-indigo-600' },
        ];
        this.subtitle            = `${this.myEnrollments.length} enrolled classes`;
        this.recentAnnouncements = (announcements?.data ?? []).slice(0, 4);
        this.upcomingEvents      = (events?.data ?? []).slice(0, 4);
    },

    drawCharts(classes, enrollments) {
        const ctx1 = document.getElementById('enrollmentChart');
        if (ctx1) {
            const labels = classes.slice(0, 8).map(c => c.name?.split('—')[0]?.trim() ?? c.name);
            const counts = classes.slice(0, 8).map(c => enrollments.filter(e => e.class_id === c.id).length);
            new Chart(ctx1, {
                type: 'bar',
                data: { labels, datasets: [{ label: 'Enrollments', data: counts, backgroundColor: 'rgba(1,109,93,0.75)', borderRadius: 6 }] },
                options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } },
            });
        }
        const ctx2 = document.getElementById('statusChart');
        if (ctx2) {
            const active    = enrollments.filter(e => e.status === 'active').length;
            const completed = enrollments.filter(e => e.status === 'completed').length;
            const dropped   = enrollments.filter(e => e.status === 'dropped').length;
            new Chart(ctx2, {
                type: 'doughnut',
                data: { labels: ['Active', 'Completed', 'Dropped'], datasets: [{ data: [active, completed, dropped], backgroundColor: ['#016D5D','#289E92','#AC9E6F'], borderWidth: 2, borderColor: '#fff' }] },
                options: { responsive: true, cutout: '70%', plugins: { legend: { position: 'bottom' } } },
            });
        }
    },
}));

// ─── Generic CRUD page factory ────────────────────────────────────────────────
function crudPage(endpoint, emptyForm, rowFilter = null) {
    return {
        items: [],
        loading: true,
        search: '',
        showModal: false,
        editingId: null,
        form: { ...emptyForm },
        errors: {},
        showDeleteConfirm: false,
        deletingId: null,

        // Expose role flags for template use
        canCreate: isAdmin || isTeacher,
        canWrite:  isAdmin || isTeacher,
        isStudent,
        isTeacher,
        isAdmin,

        get filtered() {
            let list = rowFilter ? this.items.filter(item => rowFilter(item)) : this.items;
            const q = this.search.toLowerCase();
            if (!q) return list;
            return list.filter(item =>
                Object.values(item).some(v => String(v ?? '').toLowerCase().includes(q))
            );
        },

        async init() { await this.load(); },

        async load() {
            this.loading = true;
            try {
                const res = await api.get(endpoint);
                this.items = res?.data ?? [];
            } catch(e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Failed to load data.', type: 'error' } }));
            }
            this.loading = false;
        },

        openCreate() {
            this.editingId = null;
            this.form = { ...emptyForm };
            this.errors = {};
            this.showModal = true;
        },

        openEdit(item) {
            this.editingId = item.id;
            this.form = { ...emptyForm, ...item };
            this.errors = {};
            this.showModal = true;
        },

        async save() {
            this.errors = {};
            try {
                if (this.editingId) {
                    await api.put(`${endpoint}/${this.editingId}`, this.form);
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Updated successfully.' } }));
                } else {
                    await api.post(endpoint, this.form);
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Created successfully.' } }));
                }
                this.showModal = false;
                await this.load();
            } catch(e) {
                this.errors = e.errors ?? {};
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: e.message ?? 'An error occurred.', type: 'error' } }));
            }
        },

        confirmDelete(id) {
            this.deletingId = id;
            this.showDeleteConfirm = true;
        },

        async remove() {
            try {
                await api.delete(`${endpoint}/${this.deletingId}`);
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Deleted successfully.' } }));
                this.showDeleteConfirm = false;
                await this.load();
            } catch(e) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: e.message ?? 'Delete failed.', type: 'error' } }));
            }
        },
    };
}

// ─── Page components ──────────────────────────────────────────────────────────

// Students — admins manage; teachers and students read-only
Alpine.data('studentsPage', () => {
    const page = crudPage('/api/tenant/students', {
        name: '', email: '', password: '',
        student_id_number: '', date_of_birth: '', address: '',
        phone: '', parent_name: '', parent_phone: '',
    });
    page.canCreate = isAdmin;
    page.canWrite  = isAdmin;
    return page;
});

// Teachers — admins manage
Alpine.data('teachersPage', () => {
    const page = crudPage('/api/tenant/teachers', {
        name: '', email: '', password: '',
        employee_id_number: '', specialization: '', bio: '',
    });
    page.canCreate = isAdmin;
    page.canWrite  = isAdmin;
    return page;
});

// Courses — admins manage; teachers view their own; students see all
Alpine.data('coursesPage', () => {
    const filter = isTeacher ? (item) => item.teacher_id === appConfig.profileId : null;
    const page = crudPage('/api/tenant/courses', {
        name: '', code: '', description: '', teacher_id: '', status: 'active',
    }, filter);
    page.canCreate = isAdmin;
    page.canWrite  = isAdmin;
    page.teachers  = [];
    page.init = async function() {
        if (isAdmin) {
            const res = await api.get('/api/tenant/teachers');
            this.teachers = res?.data ?? [];
        }
        await this.load();
    };
    return page;
});

// Classes — admins manage; teachers manage their own; students read-only (their enrolled classes)
Alpine.data('classesPage', () => {
    const filter = isTeacher
        ? (item) => item.teacher_id === appConfig.profileId
        : isStudent
            ? null   // will filter after load based on enrollments
            : null;

    const page = crudPage('/api/tenant/classes', {
        name: '', description: '', course_id: '', teacher_id: '',
        start_date: '', end_date: '',
        schedule: [{ day: 'sunday', start: '08:00', end: '09:30' }],
    }, filter);

    page.canCreate = isAdmin || isTeacher;
    page.canWrite  = isAdmin || isTeacher;
    page.courses   = [];
    page.teachers  = [];
    page.days      = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];
    page.enrolledClassIds = [];

    page.init = async function() {
        const promises = [
            api.get('/api/tenant/courses'),
            isAdmin ? api.get('/api/tenant/teachers') : Promise.resolve({ data: [] }),
        ];
        if (isStudent) promises.push(api.get('/api/tenant/enrollments'));

        const [courses, teachers, enrollments] = await Promise.all(promises);
        this.courses  = courses?.data  ?? [];
        this.teachers = teachers?.data ?? [];

        if (isStudent && enrollments) {
            this.enrolledClassIds = (enrollments.data ?? [])
                .filter(e => e.student_id === appConfig.profileId)
                .map(e => e.class_id);
        }
        await this.load();
    };

    // Override filtered getter for student — only show enrolled classes
    if (isStudent) {
        Object.defineProperty(page, 'filtered', {
            get() {
                let list = this.enrolledClassIds.length > 0
                    ? this.items.filter(c => this.enrolledClassIds.includes(c.id))
                    : this.items;
                const q = this.search.toLowerCase();
                if (!q) return list;
                return list.filter(item =>
                    Object.values(item).some(v => String(v ?? '').toLowerCase().includes(q))
                );
            },
            configurable: true,
        });
    }

    page.addScheduleRow = function() {
        this.form.schedule.push({ day: 'monday', start: '08:00', end: '09:30' });
    };
    page.removeScheduleRow = function(i) {
        this.form.schedule.splice(i, 1);
    };
    page.openEdit = function(item) {
        this.editingId = item.id;
        this.form = {
            ...item,
            schedule: Array.isArray(item.schedule)
                ? JSON.parse(JSON.stringify(item.schedule))
                : [{ day: 'sunday', start: '08:00', end: '09:30' }],
        };
        this.errors = {};
        this.showModal = true;
    };
    // Teacher: can only edit their own
    page.canEditItem = function(item) {
        if (isAdmin) return true;
        if (isTeacher) return item.teacher_id === appConfig.profileId;
        return false;
    };
    return page;
});

// Enrollments — admins manage; teachers see enrolled in their classes; students see own
Alpine.data('enrollmentsPage', () => {
    const filter = isTeacher
        ? null   // will filter after load
        : isStudent
            ? (item) => item.student_id === appConfig.profileId
            : null;

    const page = crudPage('/api/tenant/enrollments', {
        student_id: '', class_id: '', enrollment_date: '', status: 'active',
    }, filter);

    page.canCreate = isAdmin;
    page.canWrite  = isAdmin;
    page.students  = [];
    page.classes   = [];
    page.statuses  = ['active', 'completed', 'dropped'];
    page.myClassIds = [];

    page.init = async function() {
        const [students, classes] = await Promise.all([
            isAdmin ? api.get('/api/tenant/students') : Promise.resolve({ data: [] }),
            api.get('/api/tenant/classes'),
        ]);
        this.students = students?.data ?? [];
        this.classes  = classes?.data  ?? [];

        if (isTeacher) {
            this.myClassIds = (classes?.data ?? [])
                .filter(c => c.teacher_id === appConfig.profileId)
                .map(c => c.id);
        }
        await this.load();
    };

    // Override filtered for teacher
    if (isTeacher) {
        Object.defineProperty(page, 'filtered', {
            get() {
                let list = this.myClassIds.length > 0
                    ? this.items.filter(e => this.myClassIds.includes(e.class_id))
                    : this.items;
                const q = this.search.toLowerCase();
                if (!q) return list;
                return list.filter(item =>
                    Object.values(item).some(v => String(v ?? '').toLowerCase().includes(q))
                );
            },
            configurable: true,
        });
    }
    return page;
});

// Assignments — admins manage; teachers manage their own; students read-only (their classes)
Alpine.data('assignmentsPage', () => {
    const filter = isTeacher
        ? (item) => item.teacher_id === appConfig.profileId
        : isStudent
            ? null   // will filter after load
            : null;

    const page = crudPage('/api/tenant/assignments', {
        title: '', description: '', due_date: '', class_id: '',
        teacher_id: isTeacher ? appConfig.profileId : '',
        max_grade: 100,
    }, filter);

    page.canCreate = isAdmin || isTeacher;
    page.canWrite  = isAdmin || isTeacher;
    page.classes   = [];
    page.teachers  = [];
    page.enrolledClassIds = [];

    page.init = async function() {
        const promises = [
            api.get('/api/tenant/classes'),
            isAdmin ? api.get('/api/tenant/teachers') : Promise.resolve({ data: [] }),
        ];
        if (isStudent) promises.push(api.get('/api/tenant/enrollments'));

        const [classes, teachers, enrollments] = await Promise.all(promises);
        this.classes  = classes?.data  ?? [];
        this.teachers = teachers?.data ?? [];

        if (isStudent && enrollments) {
            this.enrolledClassIds = (enrollments.data ?? [])
                .filter(e => e.student_id === appConfig.profileId)
                .map(e => e.class_id);
        }
        if (isTeacher) {
            this.form.teacher_id = appConfig.profileId;
            this.classes = (classes?.data ?? []).filter(c => c.teacher_id === appConfig.profileId);
        }
        await this.load();
    };

    if (isStudent) {
        Object.defineProperty(page, 'filtered', {
            get() {
                let list = this.enrolledClassIds.length > 0
                    ? this.items.filter(a => this.enrolledClassIds.includes(a.class_id))
                    : this.items;
                const q = this.search.toLowerCase();
                if (!q) return list;
                return list.filter(item =>
                    Object.values(item).some(v => String(v ?? '').toLowerCase().includes(q))
                );
            },
            configurable: true,
        });
    }

    page.canEditItem = function(item) {
        if (isAdmin) return true;
        if (isTeacher) return item.teacher_id === appConfig.profileId;
        return false;
    };
    return page;
});

// Announcements — admins & teachers can post; students read-only
Alpine.data('announcementsPage', () => {
    const page = crudPage('/api/tenant/announcements', {
        title: '', content: '',
        created_by: appConfig.userId || 1,
        audience_type: 'all', audience_id: null, published_at: '',
    });
    page.canCreate      = isAdmin || isTeacher;
    page.canWrite       = isAdmin || isTeacher;
    page.classes        = [];
    page.audienceTypes  = ['all', 'students', 'teachers', 'class'];
    page.init = async function() {
        this.form.created_by = appConfig.userId || 1;
        const res = await api.get('/api/tenant/classes');
        this.classes = isTeacher
            ? (res?.data ?? []).filter(c => c.teacher_id === appConfig.profileId)
            : (res?.data ?? []);
        await this.load();
    };
    return page;
});

// Events — admins & teachers can post; students read-only
Alpine.data('eventsPage', () => {
    const page = crudPage('/api/tenant/events', {
        title: '', description: '', start_date: '', end_date: '',
        location: '', created_by: appConfig.userId || 1,
    });
    page.canCreate = isAdmin || isTeacher;
    page.canWrite  = isAdmin || isTeacher;
    page.init = async function() {
        this.form.created_by = appConfig.userId || 1;
        await this.load();
    };
    return page;
});

// ─── Boot ─────────────────────────────────────────────────────────────────────
Alpine.start();
