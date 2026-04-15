<?php

namespace App\Modules\Tenant\Presentation\Controllers;

use Illuminate\Support\Facades\DB;
use App\Modules\Tenant\Infrastructure\Models\Student;
use App\Modules\Tenant\Infrastructure\Models\TenantUser;
use App\Shared\Presentation\Controllers\BaseController;
use App\Modules\Tenant\Presentation\Resources\StudentResource;
use App\Modules\Tenant\Presentation\Requests\StoreStudentRequest;
use App\Modules\Tenant\Presentation\Requests\UpdateStudentRequest;

class StudentController extends BaseController
{
    public function index()
    {
        $students = Student::query()
            ->with('user')
            ->latest('id')
            ->get();

        return $this->success(
            StudentResource::collection($students)->resolve(),
            'Students retrieved successfully'
        );
    }

    public function store(StoreStudentRequest $request)
    {
        $student = DB::transaction(function () use ($request) {
            $user = TenantUser::query()->create([
                'name' => $request->string('name')->toString(),
                'email' => $request->string('email')->toString(),
                'password' => $request->string('password')->toString(),
                'type' => 'student',
            ]);

            return Student::query()->create([
                'user_id' => $user->id,
                'student_id_number' => $request->input('student_id_number'),
                'date_of_birth' => $request->input('date_of_birth'),
                'address' => $request->input('address'),
                'phone' => $request->input('phone'),
                'parent_name' => $request->input('parent_name'),
                'parent_phone' => $request->input('parent_phone'),
            ]);
        });

        return $this->success(
            StudentResource::make($student->load('user'))->resolve(),
            'Student created successfully'
        );
    }

    public function show(int $student)
    {
        $student = Student::query()->with('user')->findOrFail($student);

        return $this->success(
            StudentResource::make($student)->resolve(),
            'Student retrieved successfully'
        );
    }

    public function update(UpdateStudentRequest $request, int $student)
    {
        $student = Student::query()->with('user')->findOrFail($student);

        DB::transaction(function () use ($request, $student) {
            $student->user()->update([
                'name' => $request->string('name')->toString(),
                'email' => $request->string('email')->toString(),
                ...($request->filled('password') ? [
                    'password' => $request->string('password')->toString(),
                ] : []),
            ]);

            $student->update([
                'student_id_number' => $request->input('student_id_number'),
                'date_of_birth' => $request->input('date_of_birth'),
                'address' => $request->input('address'),
                'phone' => $request->input('phone'),
                'parent_name' => $request->input('parent_name'),
                'parent_phone' => $request->input('parent_phone'),
            ]);
        });

        return $this->success(
            StudentResource::make($student->fresh()->load('user'))->resolve(),
            'Student updated successfully'
        );
    }

    public function destroy(int $student)
    {
        $student = Student::query()->with('user')->findOrFail($student);

        DB::transaction(function () use ($student) {
            $student->delete();
            $student->user()->delete();
        });

        return $this->success(null, 'Student deleted successfully');
    }
}
