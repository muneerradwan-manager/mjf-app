<?php

namespace App\Modules\Tenant\Presentation\Controllers;

use App\Modules\Tenant\Infrastructure\Models\Event;
use App\Shared\Presentation\Controllers\BaseController;
use App\Modules\Tenant\Presentation\Resources\EventResource;
use App\Modules\Tenant\Presentation\Requests\StoreEventRequest;
use App\Modules\Tenant\Presentation\Requests\UpdateEventRequest;

class EventController extends BaseController
{
    public function index()
    {
        $events = Event::query()
            ->with('creator')
            ->latest('id')
            ->get();

        return $this->success(
            EventResource::collection($events)->resolve(),
            'Events retrieved successfully'
        );
    }

    public function store(StoreEventRequest $request)
    {
        $event = Event::query()->create([
            'title'       => $request->string('title')->toString(),
            'description' => $request->input('description'),
            'start_date'  => $request->input('start_date'),
            'end_date'    => $request->input('end_date'),
            'location'    => $request->input('location'),
            'created_by'  => $request->integer('created_by'),
        ]);

        return $this->success(
            EventResource::make($event->load('creator'))->resolve(),
            'Event created successfully'
        );
    }

    public function show(int $event)
    {
        $event = Event::query()
            ->with('creator')
            ->findOrFail($event);

        return $this->success(
            EventResource::make($event)->resolve(),
            'Event retrieved successfully'
        );
    }

    public function update(UpdateEventRequest $request, int $event)
    {
        $event = Event::query()->findOrFail($event);

        $event->update([
            'title'       => $request->string('title')->toString(),
            'description' => $request->input('description'),
            'start_date'  => $request->input('start_date'),
            'end_date'    => $request->input('end_date'),
            'location'    => $request->input('location'),
            'created_by'  => $request->integer('created_by'),
        ]);

        return $this->success(
            EventResource::make($event->fresh()->load('creator'))->resolve(),
            'Event updated successfully'
        );
    }

    public function destroy(int $event)
    {
        $event = Event::query()->findOrFail($event);
        $event->delete();

        return $this->success(null, 'Event deleted successfully');
    }
}
