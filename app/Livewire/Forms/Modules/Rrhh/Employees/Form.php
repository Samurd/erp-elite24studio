<?php

namespace App\Livewire\Forms\Modules\Rrhh\Employees;

use App\Models\Employee;
use App\Models\File;
use App\Models\Folder;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form as LivewireForm;
use Livewire\WithFileUploads;

class Form extends LivewireForm
{
    use WithFileUploads;
    
    public $employeeId = null;

    // Work Information
    public $full_name = '';
    public $job_title = '';
    public $work_email = '';
    public $mobile_phone = '';
    public $work_address = '';
    public $work_schedule = '40 hours/week';
    public $department_id = '';
    
    // Private Information
    public $home_address = '';
    public $personal_email = '';
    public $private_phone = '';
    public $bank_account = '';
    public $identification_number = '';
    public $social_security_number = '';
    public $passport_number = '';
    public $gender_id = '';
    public $birth_date = '';
    public $birth_place = '';
    public $birth_country = '';
    public $has_disability = false;
    public $disability_details = '';
    
    // Emergency Contact
    public $emergency_contact_name = '';
    public $emergency_contact_phone = '';
    
    // Education
    public $education_type_id = '';
    
    // Family Status
    public $marital_status_id = '';
    public $number_of_dependents = 0;

    // File management
    public $curriculum_files = [];
    public $bank_certificate_files = [];

    public $files_db = []; // Archivos ya guardados (desde DB)

    // Para la selección de carpeta desde el Cloud
    public $selected_folder_path = null;
    public $selected_folder_id = null;

    /**
     * Obtiene o crea una carpeta para el módulo RRHH
     */
    private function getOrCreateEmployeeFilesFolder()
    {
        $user = Auth::user();
        $folderName = "employee-files";

        // Buscar si ya existe una carpeta employee-files para este usuario en el root
        $folder = Folder::where("name", $folderName)
            ->whereNull("parent_id")
            ->first();

        if (!$folder) {
            // Crear la carpeta si no existe
            $folder = Folder::create([
                "name" => $folderName,
                "parent_id" => null, // Carpeta en el root
                "user_id" => $user->id,
                "path" => "cloud/root/{$folderName}",
            ]);
        }

        return $folder;
    }

    protected function rules()
    {
        return [
            // Work Information
            'full_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'work_email' => 'required|email|unique:employees,work_email,' . $this->employeeId,
            'mobile_phone' => 'required|string|max:20',
            'work_address' => 'required|string|max:500',
            'work_schedule' => 'required|string|max:100',
            'department_id' => 'required|exists:departments,id',
            
            // Private Information
            'home_address' => 'nullable|string|max:500',
            'personal_email' => 'nullable|email|max:255',
            'private_phone' => 'nullable|string|max:20',
            'bank_account' => 'nullable|string|max:50',
            'identification_number' => 'required|string|max:50|unique:employees,identification_number,' . $this->employeeId,
            'social_security_number' => 'nullable|string|max:50',
            'passport_number' => 'nullable|string|max:50',
            'gender_id' => 'nullable|exists:tags,id',
            'birth_date' => 'nullable|date|before:today',
            'birth_place' => 'nullable|string|max:255',
            'birth_country' => 'nullable|string|max:255',
            'has_disability' => 'boolean',
            'disability_details' => 'required_if:has_disability,true|string|max:500',
            
            // Emergency Contact
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            
            // Education
            'education_type_id' => 'nullable|exists:tags,id',
            
            // Family Status
            'marital_status_id' => 'nullable|exists:tags,id',
            'number_of_dependents' => 'required|integer|min:0|max:20',
        ];
    }

    protected function messages()
    {
        return [
            'full_name.required' => 'El nombre completo es obligatorio',
            'job_title.required' => 'El cargo es obligatorio',
            'work_email.required' => 'El email de trabajo es obligatorio',
            'work_email.email' => 'El email de trabajo debe ser válido',
            'work_email.unique' => 'Este email de trabajo ya está registrado',
            'mobile_phone.required' => 'El teléfono móvil es obligatorio',
            'work_address.required' => 'La dirección de trabajo es obligatoria',
            'identification_number.required' => 'El número de identificación es obligatorio',
            'identification_number.unique' => 'Este número de identificación ya está registrado',
            'emergency_contact_name.required' => 'El nombre del contacto de emergencia es obligatorio',
            'emergency_contact_phone.required' => 'El teléfono del contacto de emergencia es obligatorio',
            'birth_date.before' => 'La fecha de nacimiento debe ser anterior a hoy',
            'disability_details.required_if' => 'Los detalles de la discapacidad son obligatorios cuando se marca como discapacitado',
            'gender_id.exists' => 'El género seleccionado no es válido',
            'education_type_id.exists' => 'El tipo de educación seleccionado no es válido',
            'marital_status_id.exists' => 'El estado civil seleccionado no es válido',
            'curriculum_files.*.mimes' => 'El archivo del currículum debe ser PDF, DOC o DOCX',
            'curriculum_files.*.max' => 'El archivo del currículum no debe superar 2MB',
            'bank_certificate_files.*.mimes' => 'El certificado bancario debe ser PDF, JPG o PNG',
            'bank_certificate_files.*.max' => 'El certificado bancario no debe superar 2MB',
        ];
    }

    public function setEmployee(Employee $employee)
    {
        $this->employeeId = $employee->id;
        $this->full_name = $employee->full_name;
        $this->job_title = $employee->job_title;
        $this->work_email = $employee->work_email;
        $this->mobile_phone = $employee->mobile_phone;
        $this->work_address = $employee->work_address;
        $this->work_schedule = $employee->work_schedule;
        $this->department_id = $employee->department_id;
        $this->home_address = $employee->home_address;
        $this->personal_email = $employee->personal_email;
        $this->private_phone = $employee->private_phone;
        $this->bank_account = $employee->bank_account;
        $this->identification_number = $employee->identification_number;
        $this->social_security_number = $employee->social_security_number;
        $this->passport_number = $employee->passport_number;
        $this->gender_id = $employee->gender_id;
        $this->birth_date = $employee->birth_date?->format('Y-m-d');
        $this->birth_place = $employee->birth_place;
        $this->birth_country = $employee->birth_country;
        $this->has_disability = $employee->has_disability;
        $this->disability_details = $employee->disability_details;
        $this->emergency_contact_name = $employee->emergency_contact_name;
        $this->emergency_contact_phone = $employee->emergency_contact_phone;
        $this->education_type_id = $employee->education_type_id;
        $this->marital_status_id = $employee->marital_status_id;
        $this->number_of_dependents = $employee->number_of_dependents;

        // Cargar archivos con sus relaciones para mostrar correctamente
        $employee->refresh(); // Forzar recarga desde DB
        $this->files_db = $employee
            ->files()
            ->with(["folder", "user"])
            ->get();
    }

    /**
     * Recargar los archivos desde la base de datos
     */
    public function reloadFiles()
    {
        if ($this->employeeId) {
            $employee = Employee::find($this->employeeId);
            if ($employee) {
                $this->files_db = $employee
                    ->files()
                    ->with(["folder", "user"])
                    ->get();
            }
        }
    }

    public function store()
    {
        $this->validate();

        try {
            $employee = Employee::create($this->only([
                'full_name',
                'job_title',
                'work_email',
                'mobile_phone',
                'work_address',
                'work_schedule',
                'department_id',
                'home_address',
                'personal_email',
                'private_phone',
                'bank_account',
                'identification_number',
                'social_security_number',
                'passport_number',
                'gender_id',
                'birth_date',
                'birth_place',
                'birth_country',
                'has_disability',
                'disability_details',
                'emergency_contact_name',
                'emergency_contact_phone',
                'education_type_id',
                'marital_status_id',
                'number_of_dependents',
            ]));

            // Procesar archivos de currículum
            if (!empty($this->curriculum_files)) {
                foreach ($this->curriculum_files as $file) {
                    if ($file && $file instanceof \Illuminate\Http\UploadedFile) {
                        $this->processFile($file, $employee, 'curriculum');
                    }
                }
            }

            // Procesar archivos de certificado bancario
            if (!empty($this->bank_certificate_files)) {
                foreach ($this->bank_certificate_files as $file) {
                    if ($file && $file instanceof \Illuminate\Http\UploadedFile) {
                        $this->processFile($file, $employee, 'bank_certificate');
                    }
                }
            }

            session()->flash('success', 'Empleado creado exitosamente.');
            return $employee;
        } catch (\Exception $e) {
            logger()->error("Error al guardar empleado: " . $e->getMessage());
            logger()->error("Stack trace: " . $e->getTraceAsString());
            session()->flash('error', 'Ocurrió un error al guardar el empleado: ' . $e->getMessage());
            return null;
        }
    }

    public function update()
    {
        $this->validate();

        try {
            $employee = Employee::find($this->employeeId);
            if (!$employee) {
                session()->flash('error', 'No se encontró el empleado a actualizar.');
                return;
            }

            $employee->update($this->only([
                'full_name',
                'job_title',
                'work_email',
                'mobile_phone',
                'work_address',
                'work_schedule',
                'department_id',
                'home_address',
                'personal_email',
                'private_phone',
                'bank_account',
                'identification_number',
                'social_security_number',
                'passport_number',
                'gender_id',
                'birth_date',
                'birth_place',
                'birth_country',
                'has_disability',
                'disability_details',
                'emergency_contact_name',
                'emergency_contact_phone',
                'education_type_id',
                'marital_status_id',
                'number_of_dependents',
            ]));

            // Procesar archivos de currículum
            if (!empty($this->curriculum_files)) {
                foreach ($this->curriculum_files as $file) {
                    if ($file && $file instanceof \Illuminate\Http\UploadedFile) {
                        $this->processFile($file, $employee, 'curriculum');
                    }
                }
            }

            // Procesar archivos de certificado bancario
            if (!empty($this->bank_certificate_files)) {
                foreach ($this->bank_certificate_files as $file) {
                    if ($file && $file instanceof \Illuminate\Http\UploadedFile) {
                        $this->processFile($file, $employee, 'bank_certificate');
                    }
                }
            }

            session()->flash('success', 'Empleado actualizado exitosamente.');
        } catch (\Exception $e) {
            logger()->error("Error al actualizar empleado: " . $e->getMessage());
            logger()->error("Stack trace: " . $e->getTraceAsString());
            session()->flash('error', 'Ocurrió un error al actualizar el empleado: ' . $e->getMessage());
        }
    }

    private function processFile($file, $employee, $fileType)
    {
        $filename = time() . "_" . $file->getClientOriginalName();
        $disk = config("filesystems.default", "public");

        // Determinar la carpeta a usar
        if ($this->selected_folder_id) {
            $folder = Folder::find($this->selected_folder_id);
            if (!$folder) {
                logger()->error("Carpeta seleccionada no encontrada: " . $this->selected_folder_id);
                return;
            }
            $folderPath = $folder->path;
            $folderId = $folder->id;
        } else {
            $folder = $this->getOrCreateEmployeeFilesFolder();
            $folderPath = $folder->path;
            $folderId = $folder->id;
        }

        $path = $file->storeAs($folderPath, $filename, $disk);

        if ($path) {
            File::create([
                "fileable_id" => $employee->id,
                "fileable_type" => Employee::class,
                "disk" => $disk,
                "path" => $path,
                "name" => $file->getClientOriginalName(),
                "mime_type" => $file->getMimeType(),
                "size" => $file->getSize(),
                "folder_id" => $folderId,
                "user_id" => auth()->user()->id,
            ]);

            logger()->info("Archivo guardado correctamente: " . $file->getClientOriginalName() . " en " . $path);
        } else {
            logger()->error("Error al guardar archivo: " . $file->getClientOriginalName());
        }
    }

    public function removeTempFile($index, $fileType)
    {
        if ($fileType === 'curriculum' && isset($this->curriculum_files[$index])) {
            $file = $this->curriculum_files[$index];
            if ($file instanceof TemporaryUploadedFile) {
                $file->delete();
            }
            unset($this->curriculum_files[$index]);
            $this->curriculum_files = array_values($this->curriculum_files);
        } elseif ($fileType === 'bank_certificate' && isset($this->bank_certificate_files[$index])) {
            $file = $this->bank_certificate_files[$index];
            if ($file instanceof TemporaryUploadedFile) {
                $file->delete();
            }
            unset($this->bank_certificate_files[$index]);
            $this->bank_certificate_files = array_values($this->bank_certificate_files);
        }
    }

    public function removeStoredFile($id)
    {
        $file = File::find($id);
        if ($file) {
            // Verificar que el archivo pertenezca al empleado actual
            if ($file->fileable_type === Employee::class) {
                // Elimina del storage
                Storage::delete($file->path);
                
                // Elimina de la base de datos
                $file->delete();
                
                // Actualiza la lista de archivos
                $this->reloadFiles();
                
                session()->flash('success', 'Archivo eliminado correctamente.');
            } else {
                session()->flash('error', 'No tienes permiso para eliminar este archivo.');
            }
        } else {
            session()->flash('error', 'No se encontró el archivo a eliminar.');
        }
    }

    public function cleanupTempFiles()
    {
        foreach ($this->curriculum_files as $file) {
            if ($file instanceof TemporaryUploadedFile) {
                $file->delete();
            }
        }
        foreach ($this->bank_certificate_files as $file) {
            if ($file instanceof TemporaryUploadedFile) {
                $file->delete();
            }
        }
        $this->curriculum_files = [];
        $this->bank_certificate_files = [];
    }

    public function resetForm()
    {
        $this->reset();
        $this->work_schedule = '40 hours/week';
        $this->number_of_dependents = 0;
        $this->has_disability = false;
        $this->curriculum_files = [];
        $this->bank_certificate_files = [];
        $this->files_db = [];
    }

    public function getGendersProperty()
    {
        $genderCategory = TagCategory::where('slug', 'genero')->first();
        return $genderCategory ? Tag::where('category_id', $genderCategory->id)->get() : collect();
    }

    public function getEducationTypesProperty()
    {
        $educationCategory = TagCategory::where('slug', 'educacion')->first();
        return $educationCategory ? Tag::where('category_id', $educationCategory->id)->get() : collect();
    }

    public function getMaritalStatusesProperty()
    {
        $maritalStatusCategory = TagCategory::where('slug', 'estado_civil')->first();
        return $maritalStatusCategory ? Tag::where('category_id', $maritalStatusCategory->id)->get() : collect();
    }
}
