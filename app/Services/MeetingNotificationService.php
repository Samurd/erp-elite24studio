<?php

namespace App\Services;

use App\Models\Area;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class MeetingNotificationService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {
    }

    /**
     * Notify users with permissions in the 'reuniones' area about a new meeting.
     */
    public function notifyNewMeeting(Meeting $meeting): void
    {
        // 1. Find the 'reuniones' Area
        $areas = \Illuminate\Support\Facades\Cache::get('area_structure');

        if ($areas) {
            $area = $areas->firstWhere('slug', 'reuniones');
        } else {
            $area = Area::where('slug', 'reuniones')->first();
        }

        if (!$area) {
            Log::warning("Area 'reuniones' not found. Cannot send meeting notifications.");
            return;
        }

        // 2. Retrieve all permission names associated with this Area
        // Assuming permissions are linked to the area via 'area_id' in the permissions table
        // and accessible via the relationship defined in Area model.
        $permissions = $area->permissions()->pluck('name')->toArray();

        if (empty($permissions)) {
            Log::info("No permissions found for area 'reuniones'. No notifications sent.");
            return;
        }

        // 3. Find all Users who have any of these permissions
        // Using Spatie's permission scope
        $users = User::permission($permissions)->get();

        Log::info("Found " . $users->count() . " users to notify for new meeting: " . $meeting->title);

        // 4. Loop through users and send notification
        foreach ($users as $user) {
            try {
                $this->notificationService->createImmediate(
                    user: $user,
                    title: 'Nueva Reunión: ' . $meeting->title,
                    message: 'Se ha programado una nueva reunión para el ' . $meeting->date . ' a las ' . $meeting->start_time,
                    data: [
                        'meeting_id' => $meeting->id,
                        'action_url' => route('meetings.show', $meeting->id),
                        'meeting_title' => $meeting->title,
                    ],
                    notifiable: $meeting,
                    sendEmail: true,
                    emailTemplate: 'emails.meetings.created'
                );
            } catch (\Exception $e) {
                Log::error("Failed to notify user {$user->id} about meeting {$meeting->id}: " . $e->getMessage());
            }
        }
    }
}
