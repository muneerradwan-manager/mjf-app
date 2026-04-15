<?php

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Modules\Tenant\Infrastructure\Models\Course;
use App\Modules\Tenant\Infrastructure\Models\Teacher;
use App\Modules\Tenant\Infrastructure\Models\Classroom;
use App\Modules\Tenant\Infrastructure\Models\TenantUser;

uses(RefreshDatabase::class);

function seedTeacherInTenant(User $user, array $teacherOverrides = [], array $userOverrides = []): Teacher
{
    return withTenant($user, function () use ($teacherOverrides, $userOverrides) {
        $suffix = Str::lower(Str::random(6));

        $tenantUser = TenantUser::query()->create([
            'name' => 'Teacher ' . $suffix,
            'email' => "teacher-{$suffix}@example.com",
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

function seedCourseInTenant(User $user, ?Teacher $teacher = null, array $overrides = []): Course
{
    return withTenant($user, function () use ($teacher, $overrides) {
        return Course::query()->create([
            'name' => 'Course ' . Str::upper(Str::random(4)),
            'code' => 'CRS-' . strtoupper(Str::random(5)),
            'description' => 'Course description',
            'teacher_id' => $teacher?->id,
            'status' => 'active',
            ...$overrides,
        ]);
    });
}

function seedClassroomInTenant(User $user, Course $course, Teacher $teacher, array $overrides = []): Classroom
{
    return withTenant($user, function () use ($course, $teacher, $overrides) {
        return Classroom::query()->create([
            'name' => 'Class ' . Str::upper(Str::random(4)),
            'description' => 'Class description',
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-30',
            'schedule' => [
                ['day' => 'monday', 'start' => '08:00', 'end' => '10:00'],
            ],
            ...$overrides,
        ]);
    });
}

test('an authenticated user can create and list teachers in the active tenant', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/tenant/teachers', [
            'name' => 'Teacher One',
            'email' => 'teacher1@example.com',
            'password' => 'password123',
            'employee_id_number' => 'EMP-001',
            'specialization' => 'Mathematics',
            'bio' => 'Math teacher',
        ])
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.user.email', 'teacher1@example.com')
        ->assertJsonPath('data.employee_id_number', 'EMP-001');

    $this->withToken($token)
        ->getJson('/api/tenant/teachers')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.user.name', 'Teacher One');
});

test('an authenticated user can update and delete a tenant teacher', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $teacher = seedTeacherInTenant($user);

    $this->withToken($token)
        ->putJson("/api/tenant/teachers/{$teacher->id}", [
            'name' => 'Updated Teacher',
            'email' => 'updated.teacher@example.com',
            'password' => 'newpassword123',
            'employee_id_number' => 'EMP-NEW',
            'specialization' => 'Physics',
            'bio' => 'Updated teacher bio',
        ])
        ->assertOk()
        ->assertJsonPath('data.user.name', 'Updated Teacher')
        ->assertJsonPath('data.specialization', 'Physics');

    $this->withToken($token)
        ->deleteJson("/api/tenant/teachers/{$teacher->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Teacher deleted successfully');

    withTenant($user, function () {
        expect(Teacher::query()->count())->toBe(0);
        expect(TenantUser::query()->where('type', 'teacher')->count())->toBe(0);
    });
});

test('an authenticated user can create and list courses in the active tenant', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $teacher = seedTeacherInTenant($user, ['specialization' => 'Computer Science'], [
        'name' => 'Teacher for Course',
    ]);

    $this->withToken($token)
        ->postJson('/api/tenant/courses', [
            'name' => 'Algorithms',
            'code' => 'CS101',
            'description' => 'Intro to algorithms',
            'teacher_id' => $teacher->id,
            'status' => 'active',
        ])
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.code', 'CS101')
        ->assertJsonPath('data.teacher.user.name', 'Teacher for Course');

    $this->withToken($token)
        ->getJson('/api/tenant/courses')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Algorithms');
});

test('an authenticated user can update and delete a tenant course', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $teacherOne = seedTeacherInTenant($user);
    $teacherTwo = seedTeacherInTenant($user, [], ['name' => 'Second Teacher']);
    $course = seedCourseInTenant($user, $teacherOne, [
        'name' => 'Original Course',
        'code' => 'ORG101',
    ]);

    $this->withToken($token)
        ->putJson("/api/tenant/courses/{$course->id}", [
            'name' => 'Updated Course',
            'code' => 'UPD101',
            'description' => 'Updated description',
            'teacher_id' => $teacherTwo->id,
            'status' => 'archived',
        ])
        ->assertOk()
        ->assertJsonPath('data.name', 'Updated Course')
        ->assertJsonPath('data.status', 'archived')
        ->assertJsonPath('data.teacher.user.name', 'Second Teacher');

    $this->withToken($token)
        ->deleteJson("/api/tenant/courses/{$course->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Course deleted successfully');

    withTenant($user, function () {
        expect(Course::query()->count())->toBe(0);
    });
});

test('an authenticated user can create and list classes in the active tenant', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $teacher = seedTeacherInTenant($user, [], ['name' => 'Teacher for Class']);
    $course = seedCourseInTenant($user, $teacher, [
        'name' => 'Course for Class',
        'code' => 'CLS101',
    ]);

    $this->withToken($token)
        ->postJson('/api/tenant/classes', [
            'name' => 'Class A',
            'description' => 'Morning class',
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-30',
            'schedule' => [
                ['day' => 'monday', 'start' => '08:00', 'end' => '10:00'],
                ['day' => 'wednesday', 'start' => '08:00', 'end' => '10:00'],
            ],
        ])
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.name', 'Class A')
        ->assertJsonPath('data.course.code', 'CLS101')
        ->assertJsonPath('data.teacher.user.name', 'Teacher for Class')
        ->assertJsonPath('data.schedule.0.day', 'monday');

    $this->withToken($token)
        ->getJson('/api/tenant/classes')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Class A');
});

test('an authenticated user can update and delete a tenant class', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $teacherOne = seedTeacherInTenant($user, [], ['name' => 'Teacher One']);
    $teacherTwo = seedTeacherInTenant($user, [], ['name' => 'Teacher Two']);
    $courseOne = seedCourseInTenant($user, $teacherOne, ['code' => 'CLS201']);
    $courseTwo = seedCourseInTenant($user, $teacherTwo, ['code' => 'CLS202']);
    $classroom = seedClassroomInTenant($user, $courseOne, $teacherOne, [
        'name' => 'Original Class',
    ]);

    $this->withToken($token)
        ->putJson("/api/tenant/classes/{$classroom->id}", [
            'name' => 'Updated Class',
            'description' => 'Evening class',
            'course_id' => $courseTwo->id,
            'teacher_id' => $teacherTwo->id,
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-31',
            'schedule' => [
                ['day' => 'sunday', 'start' => '18:00', 'end' => '20:00'],
            ],
        ])
        ->assertOk()
        ->assertJsonPath('data.name', 'Updated Class')
        ->assertJsonPath('data.course.code', 'CLS202')
        ->assertJsonPath('data.teacher.user.name', 'Teacher Two')
        ->assertJsonPath('data.schedule.0.day', 'sunday');

    $this->withToken($token)
        ->deleteJson("/api/tenant/classes/{$classroom->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Class deleted successfully');

    withTenant($user, function () {
        expect(Classroom::query()->count())->toBe(0);
    });
});
