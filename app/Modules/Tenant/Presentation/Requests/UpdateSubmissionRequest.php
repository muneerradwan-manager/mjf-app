<?php

namespace App\Modules\Tenant\Presentation\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Tenant\Infrastructure\Models\Assignment;
use App\Modules\Tenant\Infrastructure\Models\Enrollment;
use App\Modules\Tenant\Infrastructure\Models\Submission;

class UpdateSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $submission = Submission::query()->findOrFail((int) $this->route('submission'));

        return [
            'assignment_id' => 'required|integer|exists:assignments,id',
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
                Rule::unique('submissions', 'student_id')
                    ->ignore($submission->id)
                    ->where(fn ($query) => $query->where('assignment_id', $this->integer('assignment_id'))),
            ],
            'submission_date' => 'required|date',
            'file_path' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'grade' => 'nullable|numeric|min:0',
            'feedback' => 'nullable|string',
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                $assignment = Assignment::query()->find($this->integer('assignment_id'));

                if (! $this->filled('file_path') && ! $this->filled('content')) {
                    $validator->errors()->add('content', 'Either file_path or content must be provided.');
                }

                if ($assignment && $this->filled('grade') && (float) $this->input('grade') > (float) $assignment->max_grade) {
                    $validator->errors()->add('grade', 'The grade may not exceed the assignment max grade.');
                }

                if ($assignment && $this->filled('student_id')) {
                    $isEnrolled = Enrollment::query()
                        ->where('student_id', $this->integer('student_id'))
                        ->where('class_id', $assignment->class_id)
                        ->whereIn('status', ['active', 'completed'])
                        ->exists();

                    if (! $isEnrolled) {
                        $validator->errors()->add('student_id', 'The student must be enrolled in the assignment class.');
                    }
                }
            },
        ];
    }
}
