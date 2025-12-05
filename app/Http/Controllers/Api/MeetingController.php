<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMeetingRequest;
use App\Http\Requests\UpdateMeetingRequest;
use App\Http\Resources\MeetingResource;
use App\Models\Meeting;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $meetings = Meeting::with(['team', 'status', 'responsibles'])->paginate(15);
        return MeetingResource::collection($meetings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMeetingRequest $request)
    {
        $data = $request->validated();

        \Illuminate\Support\Facades\Log::debug('Meeting data received by ERP', $data);

        $meeting = Meeting::create($data);

        \Illuminate\Support\Facades\Log::info('Meeting created in DB?', ['id' => $meeting->id ?? 'failed', 'title' => $meeting->title ?? 'failed']);

        if (isset($data['responsibles'])) {
            $meeting->responsibles()->sync($data['responsibles']);
        }

        return new MeetingResource($meeting->load(['team', 'status', 'responsibles']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Meeting $meeting)
    {
        return new MeetingResource($meeting->load(['team', 'status', 'responsibles']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMeetingRequest $request, Meeting $meeting)
    {
        $data = $request->validated();

        $meeting->update($data);

        if (isset($data['responsibles'])) {
            $meeting->responsibles()->sync($data['responsibles']);
        }

        return new MeetingResource($meeting->load(['team', 'status', 'responsibles']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meeting $meeting)
    {
        $meeting->delete();

        return response()->noContent();
    }
}
