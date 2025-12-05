<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'team_id' => $this->team_id,
            'team' => $this->whenLoaded('team'),
            'status_id' => $this->status_id,
            'status' => $this->whenLoaded('status'),
            'notes' => $this->notes,
            'observations' => $this->observations,
            'goal' => $this->goal,
            'url' => $this->url,
            'bookingId' => $this->bookingId,
            'responsibles' => UserResource::collection($this->whenLoaded('responsibles')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
