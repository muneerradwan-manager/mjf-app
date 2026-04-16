<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Modules\Central\Infrastructure\Models\Tenant;
use App\Modules\Central\Application\Services\TenantProvisionService;
use App\Modules\Tenant\Infrastructure\Models\TenantUser;
use App\Modules\Tenant\Infrastructure\Models\Teacher;
use App\Modules\Tenant\Infrastructure\Models\Student;
use App\Modules\Tenant\Infrastructure\Models\Course;
use App\Modules\Tenant\Infrastructure\Models\Classroom;
use App\Modules\Tenant\Infrastructure\Models\Enrollment;
use App\Modules\Tenant\Infrastructure\Models\Assignment;
use App\Modules\Tenant\Infrastructure\Models\Submission;
use App\Modules\Tenant\Infrastructure\Models\Grade;
use App\Modules\Tenant\Infrastructure\Models\Announcement;
use App\Modules\Tenant\Infrastructure\Models\Event;

class TenantSeeder extends Seeder
{
    // ─── Tenant definitions ───────────────────────────────────────────────────

    private array $tenants = [
        [
            'name'            => 'Al-Nour Islamic School',
            'slug'            => 'al-nour-school',
            'code'            => 'ALNOUR01',
            'email'           => 'info@alnour.edu',
            'phone'           => '+966501234567',
            'type'            => 'school',
            'owner_email'     => 'owner@alnour.edu',
            'owner_name'      => 'Abdullah Al-Rashidi',
            'owner_password'  => 'password',
        ],
        [
            'name'            => 'Al-Furqan Academy',
            'slug'            => 'al-furqan-academy',
            'code'            => 'ALFURQ02',
            'email'           => 'info@alfurqan.edu',
            'phone'           => '+966509876543',
            'type'            => 'masjed',
            'owner_email'     => 'owner@alfurqan.edu',
            'owner_name'      => 'Ibrahim Al-Mansouri',
            'owner_password'  => 'password',
        ],
    ];

    // ─── Per-tenant data ──────────────────────────────────────────────────────

    private array $teacherData = [
        [
            'name'              => 'Dr. Sara Al-Mousa',
            'email'             => 'sara.mousa@tenant.edu',
            'employee_id'       => 'EMP-001',
            'specialization'    => 'Mathematics',
            'bio'               => 'PhD in Applied Mathematics with 12 years of teaching experience. Passionate about making numbers accessible to every student.',
        ],
        [
            'name'              => 'Mr. Khalid Al-Zahrani',
            'email'             => 'khalid.zahrani@tenant.edu',
            'employee_id'       => 'EMP-002',
            'specialization'    => 'Islamic Studies',
            'bio'               => 'Graduate of Umm Al-Qura University. Specialises in Quran memorisation and Tafseer.',
        ],
        [
            'name'              => 'Ms. Fatima Hassan',
            'email'             => 'fatima.hassan@tenant.edu',
            'employee_id'       => 'EMP-003',
            'specialization'    => 'Arabic Language',
            'bio'               => 'MA in Arabic Linguistics. Expert in classical Arabic grammar (Nahw) and rhetoric.',
        ],
        [
            'name'              => 'Mr. Omar Al-Barrak',
            'email'             => 'omar.barrak@tenant.edu',
            'employee_id'       => 'EMP-004',
            'specialization'    => 'Computer Science',
            'bio'               => 'BSc Computer Science, 7 years teaching programming and web development.',
        ],
        [
            'name'              => 'Ms. Noura Al-Otaibi',
            'email'             => 'noura.otaibi@tenant.edu',
            'employee_id'       => 'EMP-005',
            'specialization'    => 'Science',
            'bio'               => 'MSc in Biology. Brings hands-on experiments to every class.',
        ],
    ];

    private array $studentData = [
        ['name' => 'Ahmad Saleh Al-Ghamdi',    'email' => 'ahmad.ghamdi@student.edu',    'id_number' => 'STD-001', 'dob' => '2007-03-12', 'parent' => 'Saleh Al-Ghamdi',    'parent_phone' => '+966501110001'],
        ['name' => 'Mohammed Al-Harbi',         'email' => 'mohammed.harbi@student.edu',  'id_number' => 'STD-002', 'dob' => '2007-07-22', 'parent' => 'Nasser Al-Harbi',   'parent_phone' => '+966501110002'],
        ['name' => 'Yusuf Ibrahim Al-Qahtani',  'email' => 'yusuf.qahtani@student.edu',   'id_number' => 'STD-003', 'dob' => '2006-11-05', 'parent' => 'Ibrahim Al-Qahtani', 'parent_phone' => '+966501110003'],
        ['name' => 'Abdulrahman Al-Shehri',     'email' => 'abdulrahman.shehri@student.edu', 'id_number' => 'STD-004', 'dob' => '2008-01-30', 'parent' => 'Faisal Al-Shehri', 'parent_phone' => '+966501110004'],
        ['name' => 'Hamza Al-Dossari',          'email' => 'hamza.dossari@student.edu',   'id_number' => 'STD-005', 'dob' => '2007-05-18', 'parent' => 'Ali Al-Dossari',    'parent_phone' => '+966501110005'],
        ['name' => 'Bilal Tariq Al-Mutairi',    'email' => 'bilal.mutairi@student.edu',   'id_number' => 'STD-006', 'dob' => '2006-09-14', 'parent' => 'Tariq Al-Mutairi',  'parent_phone' => '+966501110006'],
        ['name' => 'Ziad Al-Subaie',            'email' => 'ziad.subaie@student.edu',     'id_number' => 'STD-007', 'dob' => '2007-12-02', 'parent' => 'Mansour Al-Subaie', 'parent_phone' => '+966501110007'],
        ['name' => 'Amir Hassan Al-Anzi',       'email' => 'amir.anzi@student.edu',       'id_number' => 'STD-008', 'dob' => '2008-04-25', 'parent' => 'Hassan Al-Anzi',    'parent_phone' => '+966501110008'],
        ['name' => 'Saud Al-Bishi',             'email' => 'saud.bishi@student.edu',      'id_number' => 'STD-009', 'dob' => '2006-08-09', 'parent' => 'Fahad Al-Bishi',    'parent_phone' => '+966501110009'],
        ['name' => 'Omar Walid Al-Shamri',      'email' => 'omar.shamri@student.edu',     'id_number' => 'STD-010', 'dob' => '2007-02-17', 'parent' => 'Walid Al-Shamri',   'parent_phone' => '+966501110010'],
        ['name' => 'Noor Khalid Al-Dosari',     'email' => 'noor.dosari@student.edu',     'id_number' => 'STD-011', 'dob' => '2007-06-28', 'parent' => 'Khalid Al-Dosari',  'parent_phone' => '+966501110011'],
        ['name' => 'Leen Abdulaziz',            'email' => 'leen.abdulaziz@student.edu',  'id_number' => 'STD-012', 'dob' => '2008-10-03', 'parent' => 'Abdulaziz Mansour', 'parent_phone' => '+966501110012'],
        ['name' => 'Rima Al-Qahtani',           'email' => 'rima.qahtani@student.edu',    'id_number' => 'STD-013', 'dob' => '2006-03-21', 'parent' => 'Saeed Al-Qahtani',  'parent_phone' => '+966501110013'],
        ['name' => 'Hana Al-Zahrani',           'email' => 'hana.zahrani@student.edu',    'id_number' => 'STD-014', 'dob' => '2007-08-14', 'parent' => 'Turki Al-Zahrani',  'parent_phone' => '+966501110014'],
        ['name' => 'Sara Yousef Al-Ghamdi',     'email' => 'sara.ghamdi@student.edu',     'id_number' => 'STD-015', 'dob' => '2008-01-07', 'parent' => 'Yousef Al-Ghamdi',  'parent_phone' => '+966501110015'],
    ];

    private array $courseData = [
        [
            'name'        => 'Algebra & Trigonometry',
            'code'        => 'MATH101',
            'description' => 'Foundations of algebra, equations, functions, and trigonometric identities. Prepares students for advanced calculus.',
            'status'      => 'active',
            'teacher_idx' => 0, // Dr. Sara
        ],
        [
            'name'        => 'Quran & Islamic Studies',
            'code'        => 'ISLM201',
            'description' => 'Quran recitation (Tajweed), memorisation of Juz Amma, and introduction to Fiqh and Seerah.',
            'status'      => 'active',
            'teacher_idx' => 1, // Mr. Khalid
        ],
        [
            'name'        => 'Arabic Language & Literature',
            'code'        => 'ARAB301',
            'description' => 'Classical Arabic grammar (Nahw & Sarf), essay writing, poetry analysis, and comprehension.',
            'status'      => 'active',
            'teacher_idx' => 2, // Ms. Fatima
        ],
        [
            'name'        => 'Introduction to Programming',
            'code'        => 'CS101',
            'description' => 'Problem solving with Python — variables, loops, functions, and building small projects.',
            'status'      => 'active',
            'teacher_idx' => 3, // Mr. Omar
        ],
        [
            'name'        => 'Biology & Life Sciences',
            'code'        => 'SCI201',
            'description' => 'Cell biology, genetics, ecosystems, and the human body. Emphasis on experimental method.',
            'status'      => 'active',
            'teacher_idx' => 4, // Ms. Noura
        ],
    ];

    // ─── Entry point ──────────────────────────────────────────────────────────

    public function run(): void
    {
        $subscription = \App\Modules\Central\Infrastructure\Models\Subscription::where('title', 'Standard Plan')->first();
        $service = app(TenantProvisionService::class);

        foreach ($this->tenants as $i => $tenantDef) {
            $this->command?->info("  → Provisioning tenant: {$tenantDef['name']}");

            // Create central owner user
            $owner = User::firstOrCreate(
                ['email' => $tenantDef['owner_email']],
                [
                    'name'     => $tenantDef['owner_name'],
                    'password' => Hash::make($tenantDef['owner_password']),
                    'is_super_admin' => false,
                ]
            );

            // Create tenant + DB via the provision service
            auth()->login($owner);
            $tenant = $service->createTenantWithDatabase([
                'name'            => $tenantDef['name'],
                'email'           => $tenantDef['email'],
                'subscription_id' => $subscription->id,
                'type'            => $tenantDef['type'],
                'owner_user_id'   => $owner->id,
            ]);

            // Switch to tenant database
            tenancy()->initialize($tenant);

            $this->seedTenant($tenant, $i);

            tenancy()->end();

            $this->command?->info("     ✓ Done — {$tenantDef['name']}");
        }
    }

    // ─── Seed one tenant's database ───────────────────────────────────────────

    private function seedTenant(Tenant $tenant, int $tenantIndex): void
    {
        $prefix = $tenantIndex === 0 ? '' : 'af.'; // namespace emails per tenant

        // 1. Admin TenantUser ─────────────────────────────────────────────────
        $admin = TenantUser::create([
            'name'              => 'Admin',
            'email'             => 'admin@' . str_replace(' ', '', strtolower($tenant->name)) . '.edu',
            'password'          => Hash::make('password'),
            'type'              => 'admin',
            'email_verified_at' => now(),
        ]);

        // 2. Teachers ─────────────────────────────────────────────────────────
        $teachers = [];
        foreach ($this->teacherData as $idx => $t) {
            $email = str_replace('@tenant.edu', '@' . $prefix . 'tenant.edu', $t['email']);

            $user = TenantUser::create([
                'name'              => $t['name'],
                'email'             => $email,
                'password'          => Hash::make('password'),
                'type'              => 'teacher',
                'email_verified_at' => now(),
            ]);

            $teachers[$idx] = Teacher::create([
                'user_id'            => $user->id,
                'employee_id_number' => $t['employee_id'],
                'specialization'     => $t['specialization'],
                'bio'                => $t['bio'],
            ]);
        }

        // 3. Students ─────────────────────────────────────────────────────────
        $students = [];
        foreach ($this->studentData as $s) {
            $email = str_replace('@student.edu', '@' . $prefix . 'student.edu', $s['email']);

            $user = TenantUser::create([
                'name'              => $s['name'],
                'email'             => $email,
                'password'          => Hash::make('password'),
                'type'              => 'student',
                'email_verified_at' => now(),
            ]);

            $students[] = Student::create([
                'user_id'            => $user->id,
                'student_id_number'  => $s['id_number'],
                'date_of_birth'      => $s['dob'],
                'address'            => 'Riyadh, Saudi Arabia',
                'phone'              => '+9665' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                'parent_name'        => $s['parent'],
                'parent_phone'       => $s['parent_phone'],
            ]);
        }

        // 4. Courses ──────────────────────────────────────────────────────────
        $courses = [];
        foreach ($this->courseData as $c) {
            $courses[] = Course::create([
                'name'        => $c['name'],
                'code'        => $c['code'],
                'description' => $c['description'],
                'teacher_id'  => $teachers[$c['teacher_idx']]->id,
                'status'      => $c['status'],
            ]);
        }

        // 5. Classes ──────────────────────────────────────────────────────────
        $classes = $this->createClasses($courses, $teachers);

        // 6. Enrollments ──────────────────────────────────────────────────────
        $this->createEnrollments($students, $classes);

        // 7. Assignments ──────────────────────────────────────────────────────
        $assignments = $this->createAssignments($classes, $teachers);

        // 8. Submissions & Grades ─────────────────────────────────────────────
        $this->createSubmissionsAndGrades($assignments, $students, $teachers, $courses);

        // 9. Announcements ────────────────────────────────────────────────────
        $this->createAnnouncements($admin, $classes);

        // 10. Events ──────────────────────────────────────────────────────────
        $this->createEvents($admin);
    }

    // ─── Classes ──────────────────────────────────────────────────────────────

    private function createClasses(array $courses, array $teachers): array
    {
        $scheduleTemplates = [
            [
                ['day' => 'sunday',    'start' => '08:00', 'end' => '09:30'],
                ['day' => 'tuesday',   'start' => '08:00', 'end' => '09:30'],
                ['day' => 'thursday',  'start' => '08:00', 'end' => '09:30'],
            ],
            [
                ['day' => 'monday',    'start' => '10:00', 'end' => '11:30'],
                ['day' => 'wednesday', 'start' => '10:00', 'end' => '11:30'],
            ],
            [
                ['day' => 'sunday',    'start' => '12:00', 'end' => '13:30'],
                ['day' => 'tuesday',   'start' => '12:00', 'end' => '13:30'],
            ],
            [
                ['day' => 'monday',    'start' => '14:00', 'end' => '15:30'],
                ['day' => 'thursday',  'start' => '14:00', 'end' => '15:30'],
            ],
            [
                ['day' => 'wednesday', 'start' => '16:00', 'end' => '17:30'],
                ['day' => 'saturday',  'start' => '09:00', 'end' => '10:30'],
            ],
        ];

        $suffixes = ['— Group A', '— Group B'];
        $classes  = [];

        foreach ($courses as $idx => $course) {
            foreach ($suffixes as $sIdx => $suffix) {
                $scheduleIdx = ($idx * 2 + $sIdx) % count($scheduleTemplates);

                $classes[] = Classroom::create([
                    'name'        => $course->name . ' ' . $suffix,
                    'description' => ($sIdx === 0 ? 'Morning' : 'Afternoon') . ' section for ' . $course->name . '.',
                    'course_id'   => $course->id,
                    'teacher_id'  => $teachers[$idx % count($teachers)]->id,
                    'start_date'  => '2026-09-01',
                    'end_date'    => '2027-01-31',
                    'schedule'    => $scheduleTemplates[$scheduleIdx],
                ]);
            }
        }

        return $classes;
    }

    // ─── Enrollments ─────────────────────────────────────────────────────────

    private function createEnrollments(array $students, array $classes): void
    {
        $totalClasses  = count($classes);
        $enrolled      = []; // track [student_id => [class_id, ...]] to avoid duplicates

        foreach ($students as $student) {
            // Assign each student to 3 classes from different course groups
            $picked = [];
            $courseGroups = array_chunk($classes, 2); // each course has 2 classes

            foreach ($courseGroups as $group) {
                if (count($picked) >= 3) break; // cap at 3 courses
                $picked[] = $group[array_rand($group)];
            }

            foreach ($picked as $class) {
                $key = $student->id . '_' . $class->id;
                if (isset($enrolled[$key])) continue;

                Enrollment::create([
                    'student_id'      => $student->id,
                    'class_id'        => $class->id,
                    'enrollment_date' => '2026-09-01',
                    'status'          => 'active',
                ]);

                $enrolled[$key] = true;
            }
        }
    }

    // ─── Assignments ─────────────────────────────────────────────────────────

    private function createAssignments(array $classes, array $teachers): array
    {
        $assignmentTemplates = [
            ['title' => 'Chapter 1 — Exercises',   'description' => 'Complete all exercises from Chapter 1 of the textbook.',     'days_from_now' => 14],
            ['title' => 'Chapter 2 — Problem Set',  'description' => 'Solve the problem set covering Chapter 2 concepts.',        'days_from_now' => 28],
            ['title' => 'Mid-Term Project',          'description' => 'Submit a short research paper (min 3 pages) on the topic.', 'days_from_now' => 45],
        ];

        $assignments = [];

        foreach ($classes as $class) {
            foreach ($assignmentTemplates as $tpl) {
                $assignments[] = Assignment::create([
                    'title'       => $class->name . ': ' . $tpl['title'],
                    'description' => $tpl['description'],
                    'due_date'    => now()->addDays($tpl['days_from_now'])->format('Y-m-d H:i:s'),
                    'class_id'    => $class->id,
                    'teacher_id'  => $class->teacher_id,
                    'max_grade'   => 100,
                ]);
            }
        }

        return $assignments;
    }

    // ─── Submissions & Grades ─────────────────────────────────────────────────

    private function createSubmissionsAndGrades(
        array $assignments,
        array $students,
        array $teachers,
        array $courses
    ): void {
        $submissionContents = [
            'I carefully reviewed the chapter and solved all required problems step by step.',
            'Attached is my completed work. I referenced the textbook and additional online resources.',
            'I found this topic challenging but managed to work through each part methodically.',
            'Solved all problems. I double-checked my answers using the examples from class.',
            'My submission addresses each question. I have included explanations for my reasoning.',
        ];

        $feedbacks = [
            'Excellent work! Your reasoning is clear and all answers are correct.',
            'Good effort. Minor calculation errors on questions 3 and 7, but overall solid.',
            'Well done. Please review the last section — the approach was slightly off.',
            'Outstanding submission. Keep up this level of detail.',
            'Satisfactory. Work on showing more intermediate steps next time.',
        ];

        // Build a quick lookup: class_id => [student_ids enrolled]
        $enrolledInClass = [];
        $enrollments = Enrollment::all();
        foreach ($enrollments as $e) {
            $enrolledInClass[$e->class_id][] = $e->student_id;
        }

        // Build student id => student object map
        $studentMap = [];
        foreach ($students as $s) {
            $studentMap[$s->id] = $s;
        }

        // Build class_id => course_id
        $classToCoursed = [];
        $allClasses = Classroom::all();
        foreach ($allClasses as $cls) {
            $classToCoursed[$cls->id] = $cls->course_id;
        }

        foreach ($assignments as $assignment) {
            $classId         = $assignment->class_id;
            $enrolledStudents = $enrolledInClass[$classId] ?? [];

            if (empty($enrolledStudents)) continue;

            // ~80% of enrolled students submit
            $submitCount = max(1, (int) round(count($enrolledStudents) * 0.8));
            $submitters  = array_slice($enrolledStudents, 0, $submitCount);

            foreach ($submitters as $studentId) {
                $grade   = rand(55, 100);
                $content = $submissionContents[array_rand($submissionContents)];
                $feedback = $feedbacks[array_rand($feedbacks)];

                $submission = Submission::create([
                    'assignment_id'   => $assignment->id,
                    'student_id'      => $studentId,
                    'submission_date' => now()->subDays(rand(1, 5))->format('Y-m-d H:i:s'),
                    'content'         => $content,
                    'file_path'       => null,
                    'grade'           => $grade,
                    'feedback'        => $feedback,
                ]);

                // Create linked grade record
                Grade::create([
                    'student_id'    => $studentId,
                    'course_id'     => $classToCoursed[$classId] ?? null,
                    'assignment_id' => $assignment->id,
                    'grade'         => $grade,
                    'comments'      => $feedback,
                    'graded_by'     => $assignment->teacher_id,
                ]);
            }
        }
    }

    // ─── Announcements ────────────────────────────────────────────────────────

    private function createAnnouncements(TenantUser $admin, array $classes): void
    {
        $announcements = [
            [
                'title'         => 'Welcome to the New Academic Year 2026–2027',
                'content'       => "Dear students and parents,\n\nWe are pleased to welcome you to the new academic year. Classes begin on Sunday, 1 September 2026. Please ensure all required textbooks and materials are ready.\n\nWishing everyone a successful and blessed year.",
                'audience_type' => 'all',
                'audience_id'   => null,
                'published_at'  => '2026-08-25 08:00:00',
            ],
            [
                'title'         => 'Mid-Term Examination Schedule',
                'content'       => "The mid-term examinations will be held from 15–19 November 2026. Detailed timetables have been distributed to each class. Students are reminded to review their notes and attend all revision sessions.",
                'audience_type' => 'students',
                'audience_id'   => null,
                'published_at'  => '2026-11-01 09:00:00',
            ],
            [
                'title'         => 'Staff Meeting — Curriculum Review',
                'content'       => "All teaching staff are invited to attend the semester curriculum review meeting on Monday, 10 November 2026 at 14:00 in the main conference room. Please bring your progress reports.",
                'audience_type' => 'teachers',
                'audience_id'   => null,
                'published_at'  => '2026-11-03 10:00:00',
            ],
            [
                'title'         => 'Class-Specific: Assignment Submission Reminder',
                'content'       => "This is a reminder that all outstanding assignments for this class are due by the end of this week. Late submissions will receive a 10% deduction.",
                'audience_type' => 'class',
                'audience_id'   => $classes[0]->id ?? null,
                'published_at'  => now()->format('Y-m-d H:i:s'),
            ],
        ];

        foreach ($announcements as $data) {
            Announcement::create([
                'title'         => $data['title'],
                'content'       => $data['content'],
                'created_by'    => $admin->id,
                'audience_type' => $data['audience_type'],
                'audience_id'   => $data['audience_id'],
                'published_at'  => $data['published_at'],
            ]);
        }
    }

    // ─── Events ───────────────────────────────────────────────────────────────

    private function createEvents(TenantUser $admin): void
    {
        $events = [
            [
                'title'       => 'Annual Quran Recitation Competition',
                'description' => 'Students from all grades compete in Quran recitation (Tarteel & Tajweed). Open to families and community members. Prizes will be awarded for the top three reciters.',
                'start_date'  => '2026-10-15 09:00:00',
                'end_date'    => '2026-10-15 13:00:00',
                'location'    => 'Main Hall',
            ],
            [
                'title'       => 'Science & Technology Fair',
                'description' => 'Students showcase their semester projects. Categories include robotics, environmental science, and app development. Judges from local universities will attend.',
                'start_date'  => '2026-11-20 10:00:00',
                'end_date'    => '2026-11-20 16:00:00',
                'location'    => 'Science Laboratory & Courtyard',
            ],
            [
                'title'       => 'Parent–Teacher Open Day',
                'description' => 'Parents are invited to meet teachers, review their children\'s academic progress, and discuss any concerns. Please book your appointment slot in advance.',
                'start_date'  => '2026-12-05 08:00:00',
                'end_date'    => '2026-12-05 15:00:00',
                'location'    => 'Classrooms & Meeting Rooms',
            ],
            [
                'title'       => 'End-of-Semester Awards Ceremony',
                'description' => 'Recognition of top students, perfect attendance awards, and teacher appreciation. Refreshments will be served. All families are warmly welcome.',
                'start_date'  => '2027-01-28 17:00:00',
                'end_date'    => '2027-01-28 20:00:00',
                'location'    => 'Auditorium',
            ],
            [
                'title'       => 'First Aid & Safety Workshop',
                'description' => 'A mandatory half-day workshop for all teaching staff on first aid, emergency procedures, and student safety protocols.',
                'start_date'  => '2026-09-10 09:00:00',
                'end_date'    => '2026-09-10 13:00:00',
                'location'    => 'Training Room B',
            ],
        ];

        foreach ($events as $e) {
            Event::create([
                'title'       => $e['title'],
                'description' => $e['description'],
                'start_date'  => $e['start_date'],
                'end_date'    => $e['end_date'],
                'location'    => $e['location'],
                'created_by'  => $admin->id,
            ]);
        }
    }
}
