<?php

namespace App\Livewire\Forms\Modules\Rrhh\Birthdays;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\Birthday;

class Form extends LivewireForm
{
    public ?Birthday $birthday = null;

    public $is_employee = 1;

    #[Validate('nullable|exists:employees,id')]
    public $employee_id = null;

    #[Validate('nullable|exists:contacts,id')]
    public $contact_id = null;

    #[Validate('required|date')]
    public $date = '';

    #[Validate('nullable|string|max:255')]
    public $whatsapp = '';

    #[Validate('nullable|string')]
    public $comments = '';

    #[Validate('nullable|exists:users,id')]
    public $responsible_id = null;

    public function setBirthday(Birthday $birthday)
    {
        $this->birthday = $birthday;
        $this->employee_id = $birthday->employee_id;
        $this->contact_id = $birthday->contact_id;
        $this->is_employee = !is_null($birthday->employee_id) ? 1 : 0;
        $this->date = $birthday->date ? $birthday->date->format('Y-m-d') : '';
        $this->whatsapp = $birthday->whatsapp;
        $this->comments = $birthday->comments;
        $this->responsible_id = $birthday->responsible_id;
    }

    public function rules()
    {
        return [
            'employee_id' => 'required_if:is_employee,1|nullable|exists:employees,id',
            'contact_id' => 'required_if:is_employee,0|nullable|exists:contacts,id',
            'date' => 'required|date',
            'whatsapp' => 'nullable|string|max:255',
            'comments' => 'nullable|string',
            'responsible_id' => 'nullable|exists:users,id',
        ];
    }

    public function store()
    {
        $this->validate();


        $birthday = Birthday::create([
            'employee_id' => $this->employee_id,
            'contact_id' => $this->contact_id,
            'date' => $this->date,
            'whatsapp' => $this->whatsapp,
            'comments' => $this->comments,
            'responsible_id' => $this->responsible_id ?? auth()->id(),
        ]);

        // Crear notificación recurrente
        $this->manageNotification($birthday);
    }

    public function update()
    {
        $this->validate();

        $this->birthday->update([
            'employee_id' => $this->employee_id,
            'contact_id' => $this->contact_id,
            'date' => $this->date,
            'whatsapp' => $this->whatsapp,
            'comments' => $this->comments,
            'responsible_id' => $this->responsible_id,
        ]);

        // Actualizar notificación recurrente
        $this->manageNotification($this->birthday);
    }

    protected function manageNotification(Birthday $birthday)
    {
        $notificationService = app(\App\Services\NotificationService::class);

        // Calcular próxima fecha de cumpleaños
        $birthdayDate = \Carbon\Carbon::parse($birthday->date);
        $nextBirthday = \Carbon\Carbon::createFromDate(now()->year, $birthdayDate->month, $birthdayDate->day)->startOfDay();

        if ($nextBirthday->isPast()) {
            $nextBirthday->addYear();
        }

        // Determinar si lleva imagen
        $imageUrl = null;
        if (!$this->is_employee && $birthday->contact_id) {
            $contact = \App\Models\Contact::with('relationType')->find($birthday->contact_id);
            if ($contact && $contact->relationType) {
                if ($contact->relationType->name === 'Cliente') {
                    $imageUrl = public_path('images/birth_client.jpg');
                } elseif ($contact->relationType->name === 'Contratistas') {
                    $imageUrl = public_path('images/birth_contract.jpg');
                }
            }
        }

        // Datos para el emailava
        $data = [
            'name' => $birthday->employee ? $birthday->employee->full_name : $birthday->contact->name,
            'date' => $birthdayDate->format('d/m'),
            'relation_type' => $this->is_employee ? 'Empleado' : ($contact->relationType->name ?? 'Contacto'),
        ];

        if ($imageUrl && file_exists($imageUrl)) {
            $data['image_url'] = $imageUrl;
        }

        // Obtener email del cumpleañero
        $celebrantEmail = null;
        if ($this->is_employee && $birthday->employee_id) {
            $employee = \App\Models\Employee::find($birthday->employee_id);
            $celebrantEmail = $employee ? ($employee->personal_email ?? $employee->work_email) : null;
        } elseif (!$this->is_employee && $birthday->contact_id) {
            $contact = \App\Models\Contact::find($birthday->contact_id);
            $celebrantEmail = $contact ? $contact->email : null;
        }

        // Buscar template existente
        $template = \App\Models\NotificationTemplate::where('notifiable_type', get_class($birthday))
            ->where('notifiable_id', $birthday->id)
            ->first();

        if ($template) {
            // Actualizar existente
            $templateData = $template->data ?? [];
            $templateData['email_template'] = 'emails.birthday';
            if ($celebrantEmail) {
                $templateData['custom_email'] = $celebrantEmail;
            }
            $templateData = array_merge($templateData, $data);

            $template->update([
                'user_id' => $birthday->responsible_id ?? auth()->id(),
                'title' => 'Cumpleaños de ' . $birthday->employee ? $birthday->employee->full_name : $birthday->contact->name,
                'message' => 'Hoy es el cumpleaños de ' . $birthday->employee ? $birthday->employee->full_name : $birthday->contact->name,
                'data' => $templateData,
                'next_send_at' => $nextBirthday,
                'send_email' => true,
            ]);

        } else {
            // Crear nuevo
            $notificationService->createRecurringTemplate(
                user: \App\Models\User::find($birthday->responsible_id ?? auth()->id()),
                title: 'Cumpleaños de ' . $birthday->employee ? $birthday->employee->full_name : $birthday->contact->name,
                message: 'Hoy es el cumpleaños de ' . $birthday->employee ? $birthday->employee->full_name : $birthday->contact->name,
                recurringPattern: ['interval' => 'yearly'],
                data: array_merge($data, ['email_template' => 'emails.birthday']),
                notifiable: $birthday,
                startsAt: $nextBirthday,
                sendEmail: true,
                customEmail: $celebrantEmail
            );
        }
    }
}