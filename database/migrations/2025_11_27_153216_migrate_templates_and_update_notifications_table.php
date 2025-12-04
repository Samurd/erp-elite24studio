<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Migrar datos existentes de templates a notification_templates
        DB::table('notifications')
            ->whereIn('type', ['scheduled', 'recurring', 'reminder'])
            ->orderBy('id')
            ->chunk(100, function ($notifications) {
                foreach ($notifications as $notification) {
                    DB::table('notification_templates')->insert([
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'data' => $notification->data,
                        'user_id' => $notification->user_id,
                        'notifiable_type' => $notification->notifiable_type,
                        'notifiable_id' => $notification->notifiable_id,
                        'scheduled_at' => $notification->scheduled_at,
                        'recurring_pattern' => $notification->recurring_pattern,
                        'reminder_days' => $notification->reminder_days,
                        'event_date' => $notification->data ? json_decode($notification->data, true)['event_date'] ?? null : null,
                        'last_sent_at' => $notification->sent_at,
                        'next_send_at' => $notification->scheduled_at ?? now(),
                        'is_active' => $notification->status === 'pending' || $notification->status === 'sent',
                        'expires_at' => $notification->expires_at,
                        'created_at' => $notification->created_at,
                        'updated_at' => $notification->updated_at,
                    ]);
                }
            });

        // 2. Eliminar notificaciones de tipo template de la tabla notifications
        DB::table('notifications')
            ->whereIn('type', ['scheduled', 'recurring', 'reminder'])
            ->delete();

        // 3. Modificar tabla notifications para simplificarla
        Schema::table('notifications', function (Blueprint $table) {
            // Eliminar campos que ya no se necesitan
            $table->dropColumn([
                'type',
                'scheduled_at',
                'recurring_pattern',
                'reminder_days',
                'expires_at',
                'status'
            ]);

            // Agregar referencia al template (nullable)
            $table->foreignId('template_id')->nullable()->after('id')->constrained('notification_templates')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Restaurar campos eliminados
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn('template_id');

            $table->string('type')->after('id');
            $table->timestamp('scheduled_at')->nullable()->after('read_at');
            $table->json('recurring_pattern')->nullable()->after('scheduled_at');
            $table->integer('reminder_days')->nullable()->after('recurring_pattern');
            $table->timestamp('expires_at')->nullable()->after('reminder_days');
            $table->string('status')->default('pending')->after('sent_at');
        });

        // 2. Migrar templates de vuelta a notifications
        DB::table('notification_templates')
            ->orderBy('id')
            ->chunk(100, function ($templates) {
                foreach ($templates as $template) {
                    DB::table('notifications')->insert([
                        'type' => $template->type,
                        'title' => $template->title,
                        'message' => $template->message,
                        'data' => $template->data,
                        'user_id' => $template->user_id,
                        'notifiable_type' => $template->notifiable_type,
                        'notifiable_id' => $template->notifiable_id,
                        'read_at' => null,
                        'scheduled_at' => $template->scheduled_at,
                        'recurring_pattern' => $template->recurring_pattern,
                        'reminder_days' => $template->reminder_days,
                        'expires_at' => $template->expires_at,
                        'sent_at' => $template->last_sent_at,
                        'status' => $template->is_active ? 'pending' : 'expired',
                        'created_at' => $template->created_at,
                        'updated_at' => $template->updated_at,
                    ]);
                }
            });
    }
};
