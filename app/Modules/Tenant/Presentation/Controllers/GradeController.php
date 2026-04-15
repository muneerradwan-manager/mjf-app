<?php

namespace App\Modules\Tenant\Presentation\Controllers;

use App\Modules\Tenant\Infrastructure\Models\Grade;
use App\Shared\Presentation\Controllers\BaseController;
use App\Modules\Tenant\Presentation\Resources\GradeResource;
use App\Modules\Tenant\Presentation\Requests\StoreGradeRequest;
use App\Modules\Tenant\Presentation\Requests\UpdateGradeRequest;

class GradeController extends BaseController
{
    public function index()
    {
        $grades = Grade::query()
            ->with(['student.user', 'course', 'assignment.classroom.course', 'teacher.user'])
            ->latest('id')
            ->get();

        return $this->success(
            GradeResource::collection($grades)->resolve(),
            'Grades retrieved successfully'
        );
    }

    public function store(StoreGradeRequest $request)
    {
        $grade = Grade::query()->create([
            'student_id' => $request->integer('student_id'),
            'course_id' => $request->input('course_id'),
            'assignment_id' => $request->input('assignment_id'),
            'grade' => $request->input('grade'),
            'comments' => $request->input('comments'),
            'graded_by' => $request->input('graded_by'),
        ]);

        return $this->success(
            GradeResource::make($grade->load(['student.user', 'course', 'assignment.classroom.course', 'teacher.user']))->resolve(),
            'Grade created successfully'
        );
    }

    public function show(int $grade)
    {
        $grade = Grade::query()
            ->with(['student.user', 'course', 'assignment.classroom.course', 'teacher.user'])
            ->findOrFail($grade);

        return $this->success(
            GradeResource::make($grade)->resolve(),
            'Grade retrieved successfully'
        );
    }

    public function update(UpdateGradeRequest $request, int $grade)
    {
        $grade = Grade::query()->findOrFail($grade);

        $grade->update([
            'student_id' => $request->integer('student_id'),
            'course_id' => $request->input('course_id'),
            'assignment_id' => $request->input('assignment_id'),
            'grade' => $request->input('grade'),
            'comments' => $request->input('comments'),
            'graded_by' => $request->input('graded_by'),
        ]);

        return $this->success(
            GradeResource::make($grade->fresh()->load(['student.user', 'course', 'assignment.classroom.course', 'teacher.user']))->resolve(),
            'Grade updated successfully'
        );
    }

    public function destroy(int $grade)
    {
        $grade = Grade::query()->findOrFail($grade);
        $grade->delete();

        return $this->success(null, 'Grade deleted successfully');
    }
}
