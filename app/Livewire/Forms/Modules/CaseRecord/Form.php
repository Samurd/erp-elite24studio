<?php

namespace App\Livewire\Forms\Modules\CaseRecord;

use App\Models\CaseRecord;
use App\Services\FileUploadManager;
use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use Livewire\WithFileUploads;

class Form extends LivewireForm
{
    use WithFileUploads;

    public ?CaseRecord $case_record = null;

    #[Validate("required|date")]
    public $date;

    #[Validate("required|string")]
    public $channel;

    #[Validate("required|integer")]
    public $contact_id;

    #[Validate("required|integer")]
    public $status_id;

    #[Validate("required|integer")]
    public $case_type_id;

    #[Validate("required|integer")]
    public $assigned_to_id;

    #[Validate("nullable|string")]
    public $description;

    /**
     * Establecer el caso a editar
     */
    public function setCaseRecord(CaseRecord $case_record)
    {
        $this->case_record = $case_record;
        $this->date = $case_record->date;
        $this->channel = $case_record->channel;
        $this->contact_id = $case_record->contact_id;
        $this->status_id = $case_record->status_id;
        $this->case_type_id = $case_record->case_type_id;
        $this->assigned_to_id = $case_record->assigned_to_id;
        $this->description = $case_record->description;
    }

    /**
     * Actualizar caso existente
     */
    public function update()
    {
        // Validar datos del formulario
        $this->validate([
            "date" => "required|date",
            "channel" => "required|string",
            "contact_id" => "required|integer",
            "status_id" => "required|integer",
            "case_type_id" => "required|integer",
            "assigned_to_id" => "required|integer",
            "description" => "nullable|string",
        ]);

        try {
            $data = $this->only([
                "date",
                "channel",
                "contact_id",
                "status_id",
                "case_type_id",
                "assigned_to_id",
                "description",
            ]);

            $this->case_record->update($data);


            session()->flash("success", "Caso actualizado correctamente.");
        } catch (\Exception $e) {
            logger()->error("Error al actualizar caso: " . $e->getMessage());
            logger()->error("Stack trace: " . $e->getTraceAsString());
            session()->flash(
                "error",
                "Ocurrió un error al actualizar el caso: " . $e->getMessage(),
            );
        }
    }

    /**
     * Crear nuevo caso
     */
    public function store()
    {
        // Validar datos del formulario
        $this->validate([
            "date" => "required|date",
            "channel" => "required|string",
            "contact_id" => "required|integer",
            "status_id" => "required|integer",
            "case_type_id" => "required|integer",
            "assigned_to_id" => "required|integer",
            "description" => "nullable|string",
        ]);

        try {
            $caseRecord = CaseRecord::create([
                "date" => $this->date,
                "channel" => $this->channel,
                "contact_id" => $this->contact_id,
                "status_id" => $this->status_id,
                "case_type_id" => $this->case_type_id,
                "assigned_to_id" => $this->assigned_to_id,
                "description" => $this->description,
            ]);


            session()->flash("success", "Caso creado correctamente.");

            return $caseRecord;
        } catch (\Exception $e) {
            logger()->error("Error al guardar caso: " . $e->getMessage());
            logger()->error("Stack trace: " . $e->getTraceAsString());
            session()->flash(
                "error",
                "Ocurrió un error al guardar el caso: " . $e->getMessage(),
            );

            return null;
        }
    }
}
