<?php

namespace App\Modules\Tenant\Presentation\Controllers;

use App\Modules\Tenant\Infrastructure\Models\Announcement;
use App\Shared\Presentation\Controllers\BaseController;
use App\Modules\Tenant\Presentation\Resources\AnnouncementResource;
use App\Modules\Tenant\Presentation\Requests\StoreAnnouncementRequest;
use App\Modules\Tenant\Presentation\Requests\UpdateAnnouncementRequest;

class AnnouncementController extends BaseController
{
    public function index()
    {
        $announcements = Announcement::query()
            ->with('creator')
            ->latest('id')
            ->get();

        return $this->success(
            AnnouncementResource::collection($announcements)->resolve(),
            'Announcements retrieved successfully'
        );
    }

    public function store(StoreAnnouncementRequest $request)
    {
        $announcement = Announcement::query()->create([
            'title'         => $request->string('title')->toString(),
            'content'       => $request->string('content')->toString(),
            'created_by'    => $request->integer('created_by'),
            'audience_type' => $request->input('audience_type'),
            'audience_id'   => $request->input('audience_id'),
            'published_at'  => $request->input('published_at'),
        ]);

        return $this->success(
            AnnouncementResource::make($announcement->load('creator'))->resolve(),
            'Announcement created successfully'
        );
    }

    public function show(int $announcement)
    {
        $announcement = Announcement::query()
            ->with('creator')
            ->findOrFail($announcement);

        return $this->success(
            AnnouncementResource::make($announcement)->resolve(),
            'Announcement retrieved successfully'
        );
    }

    public function update(UpdateAnnouncementRequest $request, int $announcement)
    {
        $announcement = Announcement::query()->findOrFail($announcement);

        $announcement->update([
            'title'         => $request->string('title')->toString(),
            'content'       => $request->string('content')->toString(),
            'created_by'    => $request->integer('created_by'),
            'audience_type' => $request->input('audience_type'),
            'audience_id'   => $request->input('audience_id'),
            'published_at'  => $request->input('published_at'),
        ]);

        return $this->success(
            AnnouncementResource::make($announcement->fresh()->load('creator'))->resolve(),
            'Announcement updated successfully'
        );
    }

    public function destroy(int $announcement)
    {
        $announcement = Announcement::query()->findOrFail($announcement);
        $announcement->delete();

        return $this->success(null, 'Announcement deleted successfully');
    }
}
