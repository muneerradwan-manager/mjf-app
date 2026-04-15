<?php

namespace App\Modules\Tenant\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Tenant\Infrastructure\Models\Grade;
use App\Modules\Tenant\Infrastructure\Models\Course;
use App\Modules\Tenant\Infrastructure\Models\Assignment;
use App\Modules\Tenant\Infrastructure\Models\Enrollment;

class StoreGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required|integer|exists:students,id',
            'course_id' => 'nullable|integer|exists:courses,id',
            'assignment_id' => 'nullable|integer|exists:assignments,id',
            'grade' => 'required|numeric|min:0',
            'comments' => 'nullable|string',
            'graded_by' => 'nullable|integer|exists:teachers,id',
        ];
    }

    public function after(): array
    {
        return [
            fn ($validator) => $this->validateGradePayload($validator),
        ];
    }

    protected function validateGradePayload($validator, ?int $ignoreId = null): void
    {
        $courseId = $this->input('course_id');
        $assignmentId = $this->input('assignment_id');
        $studentId = $this->integer('student_id');

        if (! $courseId && ! $assignmentId) {
            $validator->errors()->add('course_id', 'Either course_id or assignment_id must be provided.');
            return;
        }

        $assignment = $assignmentId ? Assignment::query()->with('classroom')->find($assignmentId) : null;
        $course = $courseId ? Course::query()->find($courseId) : null;

        if ($assignment && (float) $this->input('grade') > (float) $assignment->max_grade) {
            $validator->errors()->add('grade', 'The grade may not exceed the assignment max grade.');
        }

        if ($assignment && $course && $assignment->classroom && (int) $assignment->classroom->course_id !== (int) $course->id) {
            $validator->errors()->add('course_id', 'The provided course does not match the assignment course.');
        }

        if ($assignment) {
            $isEnrolled = Enrollment::query()
                ->where('student_id', $studentId)
                ->where('class_id', $assignment->class_id)
                ->whereIn('status', ['active', 'completed'])
                ->exists();

            if (! $isEnrolled) {
                $validator->errors()->add('student_id', 'The student must be enrolled in the assignment class.');
            }
        }

        if ($course && ! $assignment) {
            $isEnrolled = Enrollment::query()
                ->where('student_id', $studentId)
                ->whereIn('status', ['active', 'completed'])
                ->whereHas('classroom', fn ($query) => $query->where('course_id', $course->id))
                ->exists();

            if (! $isEnrolled) {
                $validator->errors()->add('student_id', 'The student must be enrolled in a class for the selected course.');
            }
        }

        $duplicate = Grade::query()
            ->where('student_id', $studentId)
            ->when($courseId, fn ($query) => $query->where('course_id', $courseId), fn ($query) => $query->whereNull('course_id'))
            ->when($assignmentId, fn ($query) => $query->where('assignment_id', $assignmentId), fn ($query) => $query->whereNull('assignment_id'));

        if ($ignoreId) {
            $duplicate->whereKeyNot($ignoreId);
        }

        if ($duplicate->exists()) {
            $validator->errors()->add('student_id', 'A grade for this target already exists for the selected student.');
        }
    }
}
