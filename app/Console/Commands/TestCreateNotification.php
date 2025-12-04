<?php

namespace App\Console\Commands;

use App\Models\Sub;
use Illuminate\Console\Command;

class TestCreateNotification extends Command
{
    protected $signature = 'test:create-notification {sub_id}';
    protected $description = 'Test creating multiple notifications for a sub';

    public function handle()
    {
        $subId = $this->argument('sub_id');
        $sub = Sub::find($subId);

        if (!$sub) {
            $this->error("Sub not found with ID: {$subId}");
            return 1;
        }

        $this->info("Sub: {$sub->name}");
        $this->info("Current notifications: " . $sub->notificationTemplates()->count());

        // Crear notificación
        $template = $sub->createReminderNotification(
            daysBefore: rand(1, 10),
            title: "Test notification " . now()->format('H:i:s'),
            message: "This is a test notification",
            sendEmail: false
        );

        if ($template) {
            $this->info("✓ Notification created successfully! ID: {$template->id}");
            $this->info("Total notifications now: " . $sub->notificationTemplates()->count());
        } else {
            $this->error("✗ Failed to create notification");
        }

        return 0;
    }
}
