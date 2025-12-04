<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use App\Models\User;
use Illuminate\Console\Command;

class CreateTestTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:create-test-template {user_id} {--type=recurring} {--minutes=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test notification template';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $userId = $this->argument('user_id');
        $type = $this->option('type');
        $minutes = $this->option('minutes');

        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return Command::FAILURE;
        }

        $this->info("Creating {$type} notification template for {$user->name} (ID: {$userId})...");

        if ($type === 'recurring') {
            $template = $notificationService->createRecurringTemplate(
                $user,
                'Notificación Recurrente de Prueba',
                'Esta es una notificación recurrente que se envía cada ' . $minutes . ' minuto(s).',
                ['interval' => 'minutes', 'value' => (int) $minutes],
                ['test' => true, 'created_at' => now()->toDateTimeString()]
            );

            $this->info("✓ Recurring template created successfully! Template ID: {$template->id}");
            $this->info("  Will send every {$minutes} minute(s)");
            $this->info("  Next send at: {$template->next_send_at}");
        } elseif ($type === 'scheduled') {
            $template = $notificationService->createScheduledTemplate(
                $user,
                'Notificación Programada de Prueba',
                'Esta es una notificación programada para dentro de ' . $minutes . ' minuto(s).',
                now()->addMinutes((int) $minutes),
                ['test' => true]
            );

            $this->info("✓ Scheduled template created successfully! Template ID: {$template->id}");
            $this->info("  Scheduled for: {$template->scheduled_at}");
        }

        $this->info("\nRun the appropriate command to process:");
        $this->info("  php artisan notifications:send-{$type}");

        return Command::SUCCESS;
    }
}
