<?php

namespace App\Modules\Tenant\Presentation\Controllers;

use App\Modules\Tenant\Infrastructure\Models\Submission;
use App\Shared\Presentation\Controllers\BaseController;
use App\Modules\Tenant\Presentation\Resources\SubmissionResource;
use App\Modules\Tenant\Presentation\Requests\StoreSubmissionRequest;
use App\Modules\Tenant\Presentation\Requests\UpdateSubmissionRequest;

class SubmissionController extends BaseController
{
    public function index()
    {
        $submissions = Submission::query()
            ->with(['assignment.classroom.course', 'student.user'])
            ->latest('id')
            ->get();

        return $this->success(
            SubmissionResource::collection($submissions)->resolve(),
            'Submissions retrieved successfully'
        );
    }

    public function store(StoreSubmissionRequest $request)
    {
        $submission = Submission::query()->create([
            'assignment_id' => $request->integer('assignment_id'),
            'student_id' => $request->integer('student_id'),
            'submission_date' => $request->input('submission_date'),
            'file_path' => $request->input('file_path'),
            'content' => $request->input('content'),
            'grade' => $request->input('grade'),
            'feedback' => $request->input('feedback'),
        ]);

        return $this->success(
            SubmissionResource::make($submission->load(['assignment.classroom.course', 'student.user']))->resolve(),
            'Submission created successfully'
        );
    }

    public function show(int $submission)
    {
        $submission = Submission::query()
            ->with(['assignment.classroom.course', 'student.user'])
            ->findOrFail($submission);

        return $this->success(
            SubmissionResource::make($submission)->resolve(),
            'Submission retrieved successfully'
        );
    }

    public function update(UpdateSubmissionRequest $request, int $submission)
    {
        $submission = Submission::query()->findOrFail($submission);

        $submission->update([
            'assignment_id' => $request->integer('assignment_id'),
            'student_id' => $request->integer('student_id'),
            'submission_date' => $request->input('submission_date'),
            'file_path' => $request->input('file_path'),
            'content' => $request->input('content'),
            'grade' => $request->input('grade'),
            'feedback' => $request->input('feedback'),
        ]);

        return $this->success(
            SubmissionResource::make($submission->fresh()->load(['assignment.classroom.course', 'student.user']))->resolve(),
            'Submission updated successfully'
        );
    }

    public function destroy(int $submission)
    {
        $submission = Submission::query()->findOrFail($submission);
        $submission->delete();

        return $this->success(null, 'Submission deleted successfully');
    }
}
