<?php

namespace App\Livewire\Modules\Cloud\Components;

use App\Models\Share;
use App\Models\User;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Carbon\Carbon;

class ShareDialog extends Component
{
    protected $listeners = [
        "open-share-dialog" => "open",
        "close-share-dialog" => "closeModal",
    ];

    public $showModal = false;

    public $shareableId;
    public $shareableType;

    public $shareWith = "user";

    public $selectedUserId;
    public $selectedTeamId;
    public $permission = "view";
    public $expiresAt;
    public $shareLink;

    public $users = [];
    public $teams = [];

    public $existingShares = [];

    protected $rules = [
        "permission" => "required|in:view,edit",
        "selectedUserId" => "nullable|exists:users,id",
        "selectedTeamId" => "nullable|exists:teams,id",
        "expiresAt" => "nullable|date|after:today",
    ];

    public function mount()
    {
        $this->users = User::where("id", "!=", Auth::id())->get([
            "id",
            "name",
            "email",
        ]);
        $this->teams = Team::all(["id", "name"]);
    }

    public function open($type, $id)
    {
        $this->reset([
            "selectedUserId",
            "selectedTeamId",
            "permission",
            "expiresAt",
            "shareLink",
            "shareWith",
            "existingShares",
        ]);

        $this->clearValidation();

        $this->shareableType = "App\\Models\\" . ucfirst($type);
        $this->shareableId = $id;
        $this->showModal = true;

        $this->loadExistingShares();
    }

    public function loadExistingShares()
    {
        // Cargar shares activos (usuarios y equipos)
        // Excluyendo el link público que se maneja aparte
        $this->existingShares = Share::where("shareable_id", $this->shareableId)
            ->where("shareable_type", $this->shareableType)
            ->whereNull("share_token") // Solo shares directos
            ->with(["sharedWithUser", "sharedWithTeam"])
            ->get();

        // Cargar link público si existe
        $existingLink = Share::where("shareable_id", $this->shareableId)
            ->where("shareable_type", $this->shareableType)
            ->whereNotNull("share_token")
            ->first();

        if ($existingLink) {
            $carbonDate = Carbon::parse($existingLink->expires_at);
            if ($existingLink->expires_at && $carbonDate->isPast()) {
                $existingLink->delete();
                $this->shareLink = null;
            } else {
                $this->shareLink = route("public.share", [
                    "token" => $existingLink->share_token,
                ]);
                $this->expiresAt = $existingLink->expires_at
                    ? $carbonDate->format("Y-m-d")
                    : null;
            }
        }
    }

    public function removeShare($shareId)
    {
        $share = Share::find($shareId);
        if ($share && $share->user_id === Auth::id()) {
            $share->delete();
            $this->loadExistingShares();
            $this->dispatch("shared-updated");
            session()->flash("success", "Acceso revocado correctamente.");
        }
    }

    /** 🔗 Genera o reutiliza un enlace público */
    public function generateLink()
    {
        $this->validate([
            "expiresAt" => "nullable|date|after:today",
        ]);

        $existing = Share::where("shareable_id", $this->shareableId)
            ->where("shareable_type", $this->shareableType)
            ->whereNotNull("share_token")
            ->first();

        if (
            $existing &&
            $existing->expires_at &&
            $existing->expires_at->isPast()
        ) {
            $existing->delete();
            $existing = null;
        }

        if ($existing) {
            $this->shareLink = route("public.share", [
                "token" => $existing->share_token,
            ]);
            return;
        }

        $share = Share::create([
            "user_id" => Auth::id(),
            "shareable_id" => $this->shareableId,
            "shareable_type" => $this->shareableType,
            "permission" => "view",
            "share_token" => Str::random(40),
            "expires_at" => $this->expiresAt
                ? Carbon::parse($this->expiresAt)
                : null,
        ]);

        $this->shareLink = route("public.share", [
            "token" => $share->share_token,
        ]);
        $this->dispatch("shared-updated");
        session()->flash("success", "Enlace público generado correctamente.");
    }

    public function save()
    {
        $this->validate();

        if (!$this->selectedUserId && !$this->selectedTeamId) {
            $this->addError(
                "selectedUserId",
                "Debe seleccionar un usuario o un grupo.",
            );
            return;
        }

        Share::updateOrCreate(
            [
                "user_id" => Auth::id(),
                "shareable_id" => $this->shareableId,
                "shareable_type" => $this->shareableType,
                "shared_with_user_id" => $this->selectedUserId,
                "shared_with_team_id" => $this->selectedTeamId,
            ],
            [
                "permission" => $this->permission,
                "share_token" => null,
                "expires_at" => null,
            ],
        );

        $this->loadExistingShares();
        $this->dispatch("shared-updated");

        // Reset selection inputs
        $this->selectedUserId = null;
        $this->selectedTeamId = null;

        session()->flash("success", "Compartido correctamente.");
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view("livewire.modules.cloud.components.share-dialog");
    }
}
