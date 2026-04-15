<?php

namespace App\Modules\Tenant\Presentation\Controllers;

use Illuminate\Support\Facades\DB;
use App\Modules\Tenant\Infrastructure\Models\Teacher;
use App\Modules\Tenant\Infrastructure\Models\TenantUser;
use App\Shared\Presentation\Controllers\BaseController;
use App\Modules\Tenant\Presentation\Resources\TeacherResource;
use App\Modules\Tenant\Presentation\Requests\StoreTeacherRequest;
use App\Modules\Tenant\Presentation\Requests\UpdateTeacherRequest;

class TeacherController extends BaseController
{
    public function index()
    {
        $teachers = Teacher::query()
            ->with('user')
            ->latest('id')
            ->get();

        return $this->success(
            TeacherResource::collection($teachers)->resolve(),
            'Teachers retrieved successfully'
        );
    }

    public function store(StoreTeacherRequest $request)
    {
        $teacher = DB::transaction(function () use ($request) {
            $user = TenantUser::query()->create([
                'name' => $request->string('name')->toString(),
                'email' => $request->string('email')->toString(),
                'password' => $request->string('password')->toString(),
                'type' => 'teacher',
            ]);

            return Teacher::query()->create([
                'user_id' => $user->id,
                'employee_id_number' => $request->input('employee_id_number'),
                'specialization' => $request->input('specialization'),
                'bio' => $request->input('bio'),
            ]);
        });

        return $this->success(
            TeacherResource::make($teacher->load('user'))->resolve(),
            'Teacher created successfully'
        );
    }

    public function show(int $teacher)
    {
        $teacher = Teacher::query()->with('user')->findOrFail($teacher);

        return $this->success(
            TeacherResource::make($teacher)->resolve(),
            'Teacher retrieved successfully'
        );
    }

    public function update(UpdateTeacherRequest $request, int $teacher)
    {
        $teacher = Teacher::query()->with('user')->findOrFail($teacher);

        DB::transaction(function () use ($request, $teacher) {
            $teacher->user()->update([
                'name' => $request->string('name')->toString(),
                'email' => $request->string('email')->toString(),
                ...($request->filled('password') ? [
                    'password' => $request->string('password')->toString(),
                ] : []),
            ]);

            $teacher->update([
                'employee_id_number' => $request->input('employee_id_number'),
                'specialization' => $request->input('specialization'),
                'bio' => $request->input('bio'),
            ]);
        });

        return $this->success(
            TeacherResource::make($teacher->fresh()->load('user'))->resolve(),
            'Teacher updated successfully'
        );
    }

    public function destroy(int $teacher)
    {
        $teacher = Teacher::query()->with('user')->findOrFail($teacher);

        DB::transaction(function () use ($teacher) {
            $teacher->delete();
            $teacher->user()->delete();
        });

        return $this->success(null, 'Teacher deleted successfully');
    }
}
