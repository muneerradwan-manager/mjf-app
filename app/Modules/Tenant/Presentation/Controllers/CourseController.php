<?php

namespace App\Modules\Tenant\Presentation\Controllers;

use App\Modules\Tenant\Infrastructure\Models\Course;
use App\Shared\Presentation\Controllers\BaseController;
use App\Modules\Tenant\Presentation\Resources\CourseResource;
use App\Modules\Tenant\Presentation\Requests\StoreCourseRequest;
use App\Modules\Tenant\Presentation\Requests\UpdateCourseRequest;

class CourseController extends BaseController
{
    public function index()
    {
        $courses = Course::query()
            ->with(['teacher.user'])
            ->latest('id')
            ->get();

        return $this->success(
            CourseResource::collection($courses)->resolve(),
            'Courses retrieved successfully'
        );
    }

    public function store(StoreCourseRequest $request)
    {
        $course = Course::query()->create([
            'name' => $request->string('name')->toString(),
            'code' => $request->string('code')->toString(),
            'description' => $request->input('description'),
            'teacher_id' => $request->input('teacher_id'),
            'status' => $request->string('status')->toString(),
        ]);

        return $this->success(
            CourseResource::make($course->load(['teacher.user']))->resolve(),
            'Course created successfully'
        );
    }

    public function show(int $course)
    {
        $course = Course::query()->with(['teacher.user'])->findOrFail($course);

        return $this->success(
            CourseResource::make($course)->resolve(),
            'Course retrieved successfully'
        );
    }

    public function update(UpdateCourseRequest $request, int $course)
    {
        $course = Course::query()->findOrFail($course);

        $course->update([
            'name' => $request->string('name')->toString(),
            'code' => $request->string('code')->toString(),
            'description' => $request->input('description'),
            'teacher_id' => $request->input('teacher_id'),
            'status' => $request->string('status')->toString(),
        ]);

        return $this->success(
            CourseResource::make($course->fresh()->load(['teacher.user']))->resolve(),
            'Course updated successfully'
        );
    }

    public function destroy(int $course)
    {
        $course = Course::query()->findOrFail($course);
        $course->delete();

        return $this->success(null, 'Course deleted successfully');
    }
}
