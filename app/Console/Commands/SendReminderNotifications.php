<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendReminderNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications that are due';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $this->info('Checking for reminder notification templates...');

        $templates = $notificationService->getPendingReminderTemplates();

        if ($templates->isEmpty()) {
            $this->info('No reminder templates to process.');
            return Command::SUCCESS;
        }

        $this->info("Found {$templates->count()} reminder template(s) to process.");

        $sent = 0;
        $failed = 0;

        foreach ($templates as $template) {
            try {
                $notificationService->createFromTemplate($template);
                $sent++;
                $this->info("✓ Sent reminder from template #{$template->id} to user #{$template->user_id}");
                $this->info("  Event date: {$template->event_date}");
            } catch (\Exception $e) {
                $failed++;
                $this->error("✗ Failed to send from template #{$template->id}: {$e->getMessage()}");
            }
        }

        $this->info("Sent: {$sent} | Failed: {$failed}");

        return Command::SUCCESS;
    }
}
