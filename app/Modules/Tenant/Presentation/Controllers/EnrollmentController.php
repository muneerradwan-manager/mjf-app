<?php

namespace App\Modules\Tenant\Presentation\Controllers;

use App\Modules\Tenant\Infrastructure\Models\Enrollment;
use App\Shared\Presentation\Controllers\BaseController;
use App\Modules\Tenant\Presentation\Resources\EnrollmentResource;
use App\Modules\Tenant\Presentation\Requests\StoreEnrollmentRequest;
use App\Modules\Tenant\Presentation\Requests\UpdateEnrollmentRequest;

class EnrollmentController extends BaseController
{
    public function index()
    {
        $enrollments = Enrollment::query()
            ->with(['student.user', 'classroom.course', 'classroom.teacher.user'])
            ->latest('id')
            ->get();

        return $this->success(
            EnrollmentResource::collection($enrollments)->resolve(),
            'Enrollments retrieved successfully'
        );
    }

    public function store(StoreEnrollmentRequest $request)
    {
        $enrollment = Enrollment::query()->create([
            'student_id' => $request->integer('student_id'),
            'class_id' => $request->integer('class_id'),
            'enrollment_date' => $request->input('enrollment_date'),
            'status' => $request->string('status')->toString(),
        ]);

        return $this->success(
            EnrollmentResource::make($enrollment->load(['student.user', 'classroom.course', 'classroom.teacher.user']))->resolve(),
            'Enrollment created successfully'
        );
    }

    public function show(int $enrollment)
    {
        $enrollment = Enrollment::query()
            ->with(['student.user', 'classroom.course', 'classroom.teacher.user'])
            ->findOrFail($enrollment);

        return $this->success(
            EnrollmentResource::make($enrollment)->resolve(),
            'Enrollment retrieved successfully'
        );
    }

    public function update(UpdateEnrollmentRequest $request, int $enrollment)
    {
        $enrollment = Enrollment::query()->findOrFail($enrollment);

        $enrollment->update([
            'student_id' => $request->integer('student_id'),
            'class_id' => $request->integer('class_id'),
            'enrollment_date' => $request->input('enrollment_date'),
            'status' => $request->string('status')->toString(),
        ]);

        return $this->success(
            EnrollmentResource::make($enrollment->fresh()->load(['student.user', 'classroom.course', 'classroom.teacher.user']))->resolve(),
            'Enrollment updated successfully'
        );
    }

    public function destroy(int $enrollment)
    {
        $enrollment = Enrollment::query()->findOrFail($enrollment);
        $enrollment->delete();

        return $this->success(null, 'Enrollment deleted successfully');
    }
}
