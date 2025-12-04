<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendScheduledNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled notifications that are due';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $this->info('Checking for scheduled notification templates...');

        $templates = $notificationService->getPendingScheduledTemplates();

        if ($templates->isEmpty()) {
            $this->info('No scheduled templates to process.');
            return Command::SUCCESS;
        }

        $this->info("Found {$templates->count()} scheduled template(s) to process.");

        $sent = 0;
        $failed = 0;

        foreach ($templates as $template) {
            try {
                $notificationService->createFromTemplate($template);
                $sent++;
                $this->info("✓ Sent notification from template #{$template->id} to user #{$template->user_id}");
            } catch (\Exception $e) {
                $failed++;
                $this->error("✗ Failed to send from template #{$template->id}: {$e->getMessage()}");
            }
        }

        $this->info("Sent: {$sent} | Failed: {$failed}");

        return Command::SUCCESS;
    }
}
