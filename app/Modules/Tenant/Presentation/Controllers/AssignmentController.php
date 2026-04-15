<?php

namespace App\Modules\Tenant\Presentation\Controllers;

use App\Modules\Tenant\Infrastructure\Models\Assignment;
use App\Shared\Presentation\Controllers\BaseController;
use App\Modules\Tenant\Presentation\Resources\AssignmentResource;
use App\Modules\Tenant\Presentation\Requests\StoreAssignmentRequest;
use App\Modules\Tenant\Presentation\Requests\UpdateAssignmentRequest;

class AssignmentController extends BaseController
{
    public function index()
    {
        $assignments = Assignment::query()
            ->with(['classroom.course', 'teacher.user'])
            ->latest('id')
            ->get();

        return $this->success(
            AssignmentResource::collection($assignments)->resolve(),
            'Assignments retrieved successfully'
        );
    }

    public function store(StoreAssignmentRequest $request)
    {
        $assignment = Assignment::query()->create([
            'title' => $request->string('title')->toString(),
            'description' => $request->input('description'),
            'due_date' => $request->input('due_date'),
            'class_id' => $request->integer('class_id'),
            'teacher_id' => $request->integer('teacher_id'),
            'max_grade' => $request->input('max_grade'),
        ]);

        return $this->success(
            AssignmentResource::make($assignment->load(['classroom.course', 'teacher.user']))->resolve(),
            'Assignment created successfully'
        );
    }

    public function show(int $assignment)
    {
        $assignment = Assignment::query()
            ->with(['classroom.course', 'teacher.user'])
            ->findOrFail($assignment);

        return $this->success(
            AssignmentResource::make($assignment)->resolve(),
            'Assignment retrieved successfully'
        );
    }

    public function update(UpdateAssignmentRequest $request, int $assignment)
    {
        $assignment = Assignment::query()->findOrFail($assignment);

        $assignment->update([
            'title' => $request->string('title')->toString(),
            'description' => $request->input('description'),
            'due_date' => $request->input('due_date'),
            'class_id' => $request->integer('class_id'),
            'teacher_id' => $request->integer('teacher_id'),
            'max_grade' => $request->input('max_grade'),
        ]);

        return $this->success(
            AssignmentResource::make($assignment->fresh()->load(['classroom.course', 'teacher.user']))->resolve(),
            'Assignment updated successfully'
        );
    }

    public function destroy(int $assignment)
    {
        $assignment = Assignment::query()->findOrFail($assignment);
        $assignment->delete();

        return $this->success(null, 'Assignment deleted successfully');
    }
}
