<?php

namespace App\Modules\Tenant\Presentation\Controllers;

use App\Modules\Tenant\Infrastructure\Models\Classroom;
use App\Shared\Presentation\Controllers\BaseController;
use App\Modules\Tenant\Presentation\Resources\ClassroomResource;
use App\Modules\Tenant\Presentation\Requests\StoreClassroomRequest;
use App\Modules\Tenant\Presentation\Requests\UpdateClassroomRequest;

class ClassroomController extends BaseController
{
    public function index()
    {
        $classes = Classroom::query()
            ->with(['course', 'teacher.user'])
            ->latest('id')
            ->get();

        return $this->success(
            ClassroomResource::collection($classes)->resolve(),
            'Classes retrieved successfully'
        );
    }

    public function store(StoreClassroomRequest $request)
    {
        $classroom = Classroom::query()->create([
            'name' => $request->string('name')->toString(),
            'description' => $request->input('description'),
            'course_id' => $request->integer('course_id'),
            'teacher_id' => $request->integer('teacher_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'schedule' => $request->input('schedule'),
        ]);

        return $this->success(
            ClassroomResource::make($classroom->load(['course', 'teacher.user']))->resolve(),
            'Class created successfully'
        );
    }

    public function show(int $class)
    {
        $classroom = Classroom::query()->with(['course', 'teacher.user'])->findOrFail($class);

        return $this->success(
            ClassroomResource::make($classroom)->resolve(),
            'Class retrieved successfully'
        );
    }

    public function update(UpdateClassroomRequest $request, int $class)
    {
        $classroom = Classroom::query()->findOrFail($class);

        $classroom->update([
            'name' => $request->string('name')->toString(),
            'description' => $request->input('description'),
            'course_id' => $request->integer('course_id'),
            'teacher_id' => $request->integer('teacher_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'schedule' => $request->input('schedule'),
        ]);

        return $this->success(
            ClassroomResource::make($classroom->fresh()->load(['course', 'teacher.user']))->resolve(),
            'Class updated successfully'
        );
    }

    public function destroy(int $class)
    {
        $classroom = Classroom::query()->findOrFail($class);
        $classroom->delete();

        return $this->success(null, 'Class deleted successfully');
    }
}
