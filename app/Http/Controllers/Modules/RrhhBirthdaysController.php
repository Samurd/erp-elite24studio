<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Birthday;
use App\Models\Contact;
use App\Models\Employee;
use App\Models\NotificationTemplate;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RrhhBirthdaysController extends Controller
{
    public function index(Request $request)
    {
        $query = Birthday::with(['employee', 'contact', 'responsible']);

        // Search by name (employee or contact)
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('employee', function ($q) use ($request) {
                    $q->where('full_name', 'like', '%' . $request->search . '%');
                })->orWhereHas('contact', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                })->orWhere('name', 'like', '%' . $request->search . '%'); // Fallback for legacy records
            });
        }

        // Filter by type (Simplified: Employee vs Contact)
        if ($request->type_filter) {
            if ($request->type_filter === 'employee') {
                $query->whereNotNull('employee_id');
            } elseif ($request->type_filter === 'contact') {
                $query->whereNotNull('contact_id');
            }
        }

        // Filter by month
        if ($request->month_filter) {
            $query->whereMonth('date', $request->month_filter);
        }

        $birthdays = $query->orderBy('date', 'asc')
            ->paginate($request->perPage ?? 10)
            ->withQueryString();

        // Month options
        $monthOptions = [
            ['id' => 1, 'name' => 'Enero'],
            ['id' => 2, 'name' => 'Febrero'],
            ['id' => 3, 'name' => 'Marzo'],
            ['id' => 4, 'name' => 'Abril'],
            ['id' => 5, 'name' => 'Mayo'],
            ['id' => 6, 'name' => 'Junio'],
            ['id' => 7, 'name' => 'Julio'],
            ['id' => 8, 'name' => 'Agosto'],
            ['id' => 9, 'name' => 'Septiembre'],
            ['id' => 10, 'name' => 'Octubre'],
            ['id' => 11, 'name' => 'Noviembre'],
            ['id' => 12, 'name' => 'Diciembre'],
        ];

        return Inertia::render('Rrhh/Birthdays/Index', [
            'birthdays' => $birthdays,
            'monthOptions' => $monthOptions,
            'filters' => $request->all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Rrhh/Birthdays/Form', [
            'birthday' => null,
            'users' => User::orderBy('name')->get(),
            'employees' => Employee::orderBy('full_name')->get(),
            'contacts' => Contact::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'is_employee' => 'required|boolean',
            'employee_id' => 'required_if:is_employee,true|nullable|exists:employees,id',
            'contact_id' => 'required_if:is_employee,false|nullable|exists:contacts,id',
            'date' => 'required|date',
            'whatsapp' => 'nullable|string|max:255',
            'comments' => 'nullable|string',
            'responsible_id' => 'nullable|exists:users,id',
        ]);

        // Fix logic for conditional required fields being null
        if ($validated['is_employee']) {
            $validated['contact_id'] = null;
        } else {
            $validated['employee_id'] = null;
        }

        $birthday = Birthday::create([
            'employee_id' => $validated['employee_id'],
            'contact_id' => $validated['contact_id'],
            'date' => $validated['date'],
            'whatsapp' => $validated['whatsapp'],
            'comments' => $validated['comments'],
            'responsible_id' => $validated['responsible_id'] ?? auth()->id(),
        ]);

        $this->manageNotification($birthday, $validated['is_employee']);

        return redirect()->route('rrhh.birthdays.index')->with('success', 'Cumpleaños creado exitosamente.');
    }

    public function edit(Birthday $birthday)
    {
        return Inertia::render('Rrhh/Birthdays/Form', [
            'birthday' => $birthday,
            'users' => User::orderBy('name')->get(),
            'employees' => Employee::orderBy('full_name')->get(),
            'contacts' => Contact::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Birthday $birthday)
    {
        $validated = $request->validate([
            'is_employee' => 'required|boolean',
            'employee_id' => 'required_if:is_employee,true|nullable|exists:employees,id',
            'contact_id' => 'required_if:is_employee,false|nullable|exists:contacts,id',
            'date' => 'required|date',
            'whatsapp' => 'nullable|string|max:255',
            'comments' => 'nullable|string',
            'responsible_id' => 'nullable|exists:users,id',
        ]);

        // Fix logic for conditional required fields being null
        if ($validated['is_employee']) {
            $validated['contact_id'] = null;
        } else {
            $validated['employee_id'] = null;
        }

        $birthday->update([
            'employee_id' => $validated['employee_id'],
            'contact_id' => $validated['contact_id'],
            'date' => $validated['date'],
            'whatsapp' => $validated['whatsapp'],
            'comments' => $validated['comments'],
            'responsible_id' => $validated['responsible_id'],
        ]);

        $this->manageNotification($birthday, $validated['is_employee']);

        return redirect()->route('rrhh.birthdays.index')->with('success', 'Cumpleaños actualizado exitosamente.');
    }

    public function destroy(Birthday $birthday)
    {
        $birthday->delete();
        return redirect()->route('rrhh.birthdays.index')->with('success', 'Cumpleaños eliminado exitosamente.');
    }

    protected function manageNotification(Birthday $birthday, $isEmployee)
    {
        $notificationService = app(NotificationService::class);

        // Calculate next birthday date
        $birthdayDate = Carbon::parse($birthday->date);
        $nextBirthday = Carbon::createFromDate(now()->year, $birthdayDate->month, $birthdayDate->day)->startOfDay();

        if ($nextBirthday->isPast()) {
            $nextBirthday->addYear();
        }

        // Determine if it needs an image (Contractors/Clients)
        $imageUrl = null;
        $contact = null;

        if (!$isEmployee && $birthday->contact_id) {
            $contact = Contact::with('relationType')->find($birthday->contact_id);
            if ($contact && $contact->relationType) {
                if ($contact->relationType->name === 'Cliente') {
                    $imageUrl = public_path('images/birth_client.jpg');
                } elseif ($contact->relationType->name === 'Contratistas') {
                    $imageUrl = public_path('images/birth_contract.jpg');
                }
            }
        }

        // Data for email
        $name = $birthday->employee ? $birthday->employee->full_name : ($birthday->contact ? $birthday->contact->name : 'N/A');

        $relationType = 'Contacto';
        if ($isEmployee) {
            $relationType = 'Empleado';
        } elseif ($contact && $contact->relationType) {
            $relationType = $contact->relationType->name;
        }

        $data = [
            'name' => $name,
            'date' => $birthdayDate->format('d/m'),
            'relation_type' => $relationType,
        ];

        if ($imageUrl && file_exists($imageUrl)) {
            $data['image_url'] = $imageUrl;
        }

        // Get celebrant email
        $celebrantEmail = null;
        if ($isEmployee && $birthday->employee_id) {
            $employee = Employee::find($birthday->employee_id);
            $celebrantEmail = $employee ? ($employee->personal_email ?? $employee->work_email) : null;
        } elseif (!$isEmployee && $birthday->contact_id) {
            // Contact is already fetched above if !isEmployee
            if (!$contact)
                $contact = Contact::find($birthday->contact_id);
            $celebrantEmail = $contact ? $contact->email : null;
        }

        // Find existing template
        $template = NotificationTemplate::where('notifiable_type', get_class($birthday))
            ->where('notifiable_id', $birthday->id)
            ->first();

        $title = 'Cumpleaños de ' . $name;
        $message = 'Hoy es el cumpleaños de ' . $name;
        $responsibleId = $birthday->responsible_id ?? auth()->id();

        if ($template) {
            // Update existing
            $templateData = $template->data ?? [];
            $templateData['email_template'] = 'emails.birthday';
            if ($celebrantEmail) {
                $templateData['custom_email'] = $celebrantEmail;
            }
            $templateData = array_merge($templateData, $data);

            $template->update([
                'user_id' => $responsibleId,
                'title' => $title,
                'message' => $message,
                'data' => $templateData,
                'next_send_at' => $nextBirthday,
                'send_email' => true,
            ]);

        } else {
            // Create new
            $notificationService->createRecurringTemplate(
                User::find($responsibleId),
                $title,
                $message,
                ['interval' => 'yearly'],
                array_merge($data, ['email_template' => 'emails.birthday']),
                $birthday,
                $nextBirthday,
                true, // sendEmail
                $celebrantEmail // customEmail
            );
        }
    }
}
