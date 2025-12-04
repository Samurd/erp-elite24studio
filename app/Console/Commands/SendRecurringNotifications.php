<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendRecurringNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-recurring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and send recurring notifications';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $this->info('Processing recurring notification templates...');

        $templates = $notificationService->getActiveRecurringTemplates();

        if ($templates->isEmpty()) {
            $this->info('No recurring templates to process.');
            return Command::SUCCESS;
        }

        $this->info("Found {$templates->count()} recurring template(s) to process.");

        $sent = 0;
        $failed = 0;

        foreach ($templates as $template) {
            try {
                $notificationService->createFromTemplate($template);
                $sent++;
                $this->info("✓ Sent notification from recurring template #{$template->id} to user #{$template->user_id}");
                $this->info("  Next send at: {$template->fresh()->next_send_at}");
            } catch (\Exception $e) {
                $failed++;
                $this->error("✗ Failed to send from template #{$template->id}: {$e->getMessage()}");
            }
        }

        $this->info("Sent: {$sent} | Failed: {$failed}");

        return Command::SUCCESS;
    }
}
