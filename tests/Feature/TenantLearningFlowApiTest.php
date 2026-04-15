<?php

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Modules\Tenant\Infrastructure\Models\Grade;
use App\Modules\Tenant\Infrastructure\Models\Course;
use App\Modules\Tenant\Infrastructure\Models\Student;
use App\Modules\Tenant\Infrastructure\Models\Teacher;
use App\Modules\Tenant\Infrastructure\Models\Classroom;
use App\Modules\Tenant\Infrastructure\Models\Assignment;
use App\Modules\Tenant\Infrastructure\Models\Enrollment;
use App\Modules\Tenant\Infrastructure\Models\Submission;
use App\Modules\Tenant\Infrastructure\Models\TenantUser;

uses(RefreshDatabase::class);

function buildTenantStudentFixture(User $user, array $studentOverrides = [], array $userOverrides = []): Student
{
    return withTenant($user, function () use ($studentOverrides, $userOverrides) {
        $suffix = Str::lower(Str::random(6));

        $tenantUser = TenantUser::query()->create([
            'name' => 'Student ' . $suffix,
            'email' => "student-{$suffix}@example.com",
            'password' => 'password123',
            'type' => 'student',
            ...$userOverrides,
        ]);

        return Student::query()->create([
            'user_id' => $tenantUser->id,
            'student_id_number' => 'STD-' . strtoupper(Str::random(5)),
            ...$studentOverrides,
        ]);
    });
}

function buildTenantTeacherFixture(User $user, array $teacherOverrides = [], array $userOverrides = []): Teacher
{
    return withTenant($user, function () use ($teacherOverrides, $userOverrides) {
        $suffix = Str::lower(Str::random(6));

        $tenantUser = TenantUser::query()->create([
            'name' => 'Teacher ' . $suffix,
            'email' => "teacher-learning-{$suffix}@example.com",
            'password' => 'password123',
            'type' => 'teacher',
            ...$userOverrides,
        ]);

        return Teacher::query()->create([
            'user_id' => $tenantUser->id,
            'employee_id_number' => 'EMP-' . strtoupper(Str::random(5)),
            'specialization' => 'General',
            'bio' => 'Teacher bio',
            ...$teacherOverrides,
        ]);
    });
}

function buildTenantCourseFixture(User $user, ?Teacher $teacher = null, array $overrides = []): Course
{
    return withTenant($user, function () use ($teacher, $overrides) {
        return Course::query()->create([
            'name' => 'Course ' . Str::upper(Str::random(4)),
            'code' => 'LRN-' . strtoupper(Str::random(5)),
            'description' => 'Course description',
            'teacher_id' => $teacher?->id,
            'status' => 'active',
            ...$overrides,
        ]);
    });
}

function buildTenantClassroomFixture(User $user, Course $course, Teacher $teacher, array $overrides = []): Classroom
{
    return withTenant($user, function () use ($course, $teacher, $overrides) {
        return Classroom::query()->create([
            'name' => 'Class ' . Str::upper(Str::random(4)),
            'description' => 'Class description',
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-31',
            'schedule' => [
                ['day' => 'monday', 'start' => '09:00', 'end' => '11:00'],
            ],
            ...$overrides,
        ]);
    });
}

function buildTenantEnrollmentFixture(User $user, Student $student, Classroom $classroom, array $overrides = []): Enrollment
{
    return withTenant($user, function () use ($student, $classroom, $overrides) {
        return Enrollment::query()->create([
            'student_id' => $student->id,
            'class_id' => $classroom->id,
            'enrollment_date' => '2026-08-05',
            'status' => 'active',
            ...$overrides,
        ]);
    });
}

function buildTenantAssignmentFixture(User $user, Classroom $classroom, Teacher $teacher, array $overrides = []): Assignment
{
    return withTenant($user, function () use ($classroom, $teacher, $overrides) {
        return Assignment::query()->create([
            'title' => 'Assignment ' . Str::upper(Str::random(4)),
            'description' => 'Assignment description',
            'due_date' => '2026-08-20 12:00:00',
            'class_id' => $classroom->id,
            'teacher_id' => $teacher->id,
            'max_grade' => 100,
            ...$overrides,
        ]);
    });
}

test('an authenticated user can create and list enrollments and duplicate enrollment is rejected', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $teacher = buildTenantTeacherFixture($user);
    $course = buildTenantCourseFixture($user, $teacher);
    $classroom = buildTenantClassroomFixture($user, $course, $teacher);
    $studentEmail = 'enrollment.student@example.com';
    $student = buildTenantStudentFixture($user, [], ['email' => $studentEmail]);

    $this->withToken($token)
        ->postJson('/api/tenant/enrollments', [
            'student_id' => $student->id,
            'class_id' => $classroom->id,
            'enrollment_date' => '2026-08-06',
            'status' => 'active',
        ])
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.student.user.email', $studentEmail)
        ->assertJsonPath('data.class.name', $classroom->name);

    $this->withToken($token)
        ->postJson('/api/tenant/enrollments', [
            'student_id' => $student->id,
            'class_id' => $classroom->id,
            'enrollment_date' => '2026-08-06',
            'status' => 'active',
        ])
        ->assertStatus(422);

    $this->withToken($token)
        ->getJson('/api/tenant/enrollments')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

test('an authenticated user can update and delete an enrollment', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $teacher = buildTenantTeacherFixture($user);
    $courseOne = buildTenantCourseFixture($user, $teacher);
    $courseTwo = buildTenantCourseFixture($user, $teacher);
    $classroomOne = buildTenantClassroomFixture($user, $courseOne, $teacher);
    $classroomTwo = buildTenantClassroomFixture($user, $courseTwo, $teacher);
    $student = buildTenantStudentFixture($user);
    $enrollment = buildTenantEnrollmentFixture($user, $student, $classroomOne);

    $this->withToken($token)
        ->putJson("/api/tenant/enrollments/{$enrollment->id}", [
            'student_id' => $student->id,
            'class_id' => $classroomTwo->id,
            'enrollment_date' => '2026-08-10',
            'status' => 'completed',
        ])
        ->assertOk()
        ->assertJsonPath('data.status', 'completed')
        ->assertJsonPath('data.class.id', $classroomTwo->id);

    $this->withToken($token)
        ->deleteJson("/api/tenant/enrollments/{$enrollment->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Enrollment deleted successfully');

    withTenant($user, function () {
        expect(Enrollment::query()->count())->toBe(0);
    });
});

test('an authenticated user can create and list assignments', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $teacher = buildTenantTeacherFixture($user, [], ['name' => 'Assignment Teacher']);
    $course = buildTenantCourseFixture($user, $teacher);
    $classroom = buildTenantClassroomFixture($user, $course, $teacher);

    $this->withToken($token)
        ->postJson('/api/tenant/assignments', [
            'title' => 'Homework 1',
            'description' => 'First homework',
            'due_date' => '2026-08-22 10:00:00',
            'class_id' => $classroom->id,
            'teacher_id' => $teacher->id,
            'max_grade' => 50,
        ])
        ->assertOk()
        ->assertJsonPath('data.title', 'Homework 1')
        ->assertJsonPath('data.teacher.user.name', 'Assignment Teacher')
        ->assertJsonPath('data.class.id', $classroom->id);

    $this->withToken($token)
        ->getJson('/api/tenant/assignments')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

test('an authenticated user can update and delete an assignment', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $teacherOne = buildTenantTeacherFixture($user, [], ['name' => 'Teacher One']);
    $teacherTwo = buildTenantTeacherFixture($user, [], ['name' => 'Teacher Two']);
    $courseOne = buildTenantCourseFixture($user, $teacherOne);
    $courseTwo = buildTenantCourseFixture($user, $teacherTwo);
    $classroomOne = buildTenantClassroomFixture($user, $courseOne, $teacherOne);
    $classroomTwo = buildTenantClassroomFixture($user, $courseTwo, $teacherTwo);
    $assignment = buildTenantAssignmentFixture($user, $classroomOne, $teacherOne, ['title' => 'Original Homework']);

    $this->withToken($token)
        ->putJson("/api/tenant/assignments/{$assignment->id}", [
            'title' => 'Updated Homework',
            'description' => 'Updated description',
            'due_date' => '2026-08-25 15:30:00',
            'class_id' => $classroomTwo->id,
            'teacher_id' => $teacherTwo->id,
            'max_grade' => 80,
        ])
        ->assertOk()
        ->assertJsonPath('data.title', 'Updated Homework')
        ->assertJsonPath('data.teacher.user.name', 'Teacher Two')
        ->assertJsonPath('data.max_grade', '80.00');

    $this->withToken($token)
        ->deleteJson("/api/tenant/assignments/{$assignment->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Assignment deleted successfully');

    withTenant($user, function () {
        expect(Assignment::query()->count())->toBe(0);
    });
});

test('an authenticated user can create and list submissions and unenrolled students are rejected', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $teacher = buildTenantTeacherFixture($user);
    $course = buildTenantCourseFixture($user, $teacher);
    $classroom = buildTenantClassroomFixture($user, $course, $teacher);
    $assignment = buildTenantAssignmentFixture($user, $classroom, $teacher);
    $enrolledStudent = buildTenantStudentFixture($user, [], ['name' => 'Enrolled Student']);
    $unenrolledStudent = buildTenantStudentFixture($user, [], ['name' => 'Unenrolled Student']);
    buildTenantEnrollmentFixture($user, $enrolledStudent, $classroom);

    $this->withToken($token)
        ->postJson('/api/tenant/submissions', [
            'assignment_id' => $assignment->id,
            'student_id' => $enrolledStudent->id,
            'submission_date' => '2026-08-15 10:00:00',
            'content' => 'My answer',
            'grade' => 45,
        ])
        ->assertOk()
        ->assertJsonPath('data.assignment.title', $assignment->title)
        ->assertJsonPath('data.student.user.name', 'Enrolled Student');

    $this->withToken($token)
        ->postJson('/api/tenant/submissions', [
            'assignment_id' => $assignment->id,
            'student_id' => $unenrolledStudent->id,
            'submission_date' => '2026-08-15 10:00:00',
            'content' => 'Should fail',
        ])
        ->assertStatus(422);

    $this->withToken($token)
        ->getJson('/api/tenant/submissions')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

test('an authenticated user can update and delete a submission', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $teacher = buildTenantTeacherFixture($user);
    $course = buildTenantCourseFixture($user, $teacher);
    $classroom = buildTenantClassroomFixture($user, $course, $teacher);
    $assignment = buildTenantAssignmentFixture($user, $classroom, $teacher, ['max_grade' => 60]);
    $student = buildTenantStudentFixture($user);
    buildTenantEnrollmentFixture($user, $student, $classroom);

    $submission = withTenant($user, function () use ($assignment, $student) {
        return Submission::query()->create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'submission_date' => '2026-08-14 11:00:00',
            'content' => 'Initial submission',
        ]);
    });

    $this->withToken($token)
        ->putJson("/api/tenant/submissions/{$submission->id}", [
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'submission_date' => '2026-08-16 11:30:00',
            'file_path' => '/files/submission.pdf',
            'grade' => 55,
            'feedback' => 'Well done',
        ])
        ->assertOk()
        ->assertJsonPath('data.file_path', '/files/submission.pdf')
        ->assertJsonPath('data.grade', '55.00');

    $this->withToken($token)
        ->deleteJson("/api/tenant/submissions/{$submission->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Submission deleted successfully');

    withTenant($user, function () {
        expect(Submission::query()->count())->toBe(0);
    });
});

test('an authenticated user can create and list grades and invalid grade payloads are rejected', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $teacher = buildTenantTeacherFixture($user, [], ['name' => 'Grading Teacher']);
    $course = buildTenantCourseFixture($user, $teacher);
    $classroom = buildTenantClassroomFixture($user, $course, $teacher);
    $assignment = buildTenantAssignmentFixture($user, $classroom, $teacher, ['max_grade' => 40]);
    $student = buildTenantStudentFixture($user, [], ['name' => 'Graded Student']);
    $outsideStudent = buildTenantStudentFixture($user, [], ['name' => 'Outside Student']);
    buildTenantEnrollmentFixture($user, $student, $classroom);

    $this->withToken($token)
        ->postJson('/api/tenant/grades', [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'assignment_id' => $assignment->id,
            'grade' => 35,
            'comments' => 'Strong performance',
            'graded_by' => $teacher->id,
        ])
        ->assertOk()
        ->assertJsonPath('data.student.user.name', 'Graded Student')
        ->assertJsonPath('data.teacher.user.name', 'Grading Teacher');

    $this->withToken($token)
        ->postJson('/api/tenant/grades', [
            'student_id' => $outsideStudent->id,
            'assignment_id' => $assignment->id,
            'grade' => 50,
        ])
        ->assertStatus(422);

    $this->withToken($token)
        ->getJson('/api/tenant/grades')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

test('an authenticated user can update and delete a grade', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $teacherOne = buildTenantTeacherFixture($user, [], ['name' => 'Teacher Grader One']);
    $teacherTwo = buildTenantTeacherFixture($user, [], ['name' => 'Teacher Grader Two']);
    $course = buildTenantCourseFixture($user, $teacherOne);
    $classroom = buildTenantClassroomFixture($user, $course, $teacherOne);
    $assignment = buildTenantAssignmentFixture($user, $classroom, $teacherOne, ['max_grade' => 100]);
    $student = buildTenantStudentFixture($user);
    buildTenantEnrollmentFixture($user, $student, $classroom);

    $grade = withTenant($user, function () use ($student, $course, $assignment, $teacherOne) {
        return Grade::query()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'assignment_id' => $assignment->id,
            'grade' => 70,
            'comments' => 'Initial grade',
            'graded_by' => $teacherOne->id,
        ]);
    });

    $this->withToken($token)
        ->putJson("/api/tenant/grades/{$grade->id}", [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'assignment_id' => $assignment->id,
            'grade' => 88,
            'comments' => 'Improved grade',
            'graded_by' => $teacherTwo->id,
        ])
        ->assertOk()
        ->assertJsonPath('data.grade', '88.00')
        ->assertJsonPath('data.teacher.user.name', 'Teacher Grader Two');

    $this->withToken($token)
        ->deleteJson("/api/tenant/grades/{$grade->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Grade deleted successfully');

    withTenant($user, function () {
        expect(Grade::query()->count())->toBe(0);
    });
});
