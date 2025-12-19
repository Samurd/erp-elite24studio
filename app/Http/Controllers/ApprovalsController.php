<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;
use App\Models\Area;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Storage;

class ApprovalsController extends Controller
{
    public function index()
    {
        $current_user = Auth::user();
        $perPage = 6;

        $received_approvals = Approval::with(['creator', 'status', 'priority', 'approvers.user'])
            ->where('buy', false)
            ->whereHas('approvers', fn($q) => $q->where('user_id', $current_user->id))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'received');

        $received_buy_approvals = Approval::with(['creator', 'status', 'priority', 'approvers.user'])
            ->where('buy', true)
            ->whereHas('approvers', fn($q) => $q->where('user_id', $current_user->id))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'received_buy');

        $approvals_sent = Approval::with(['creator', 'status', 'priority', 'approvers.user'])
            ->where('buy', false)
            ->where('created_by_id', $current_user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'sent');

        $buy_approvals_sent = Approval::with(['creator', 'status', 'priority', 'approvers.user'])
            ->where('buy', true)
            ->where('created_by_id', $current_user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'sent_buy');

        return Inertia::render('Approvals/Index', [
            'received_approvals' => $received_approvals,
            'received_buy_approvals' => $received_buy_approvals,
            'approvals_sent' => $approvals_sent,
            'buy_approvals_sent' => $buy_approvals_sent,
        ]);
    }

    public function create()
    {
        $priority_type = TagCategory::where('slug', 'tipo_prioridad')->first();
        $priorities = $priority_type ? Tag::where('category_id', $priority_type->id)->get() : [];

        $users = \App\Services\PermissionCacheService::getUsersByArea('aprobaciones');

        return Inertia::render('Approvals/Form', [
            'priorities' => $priorities,
            'users' => $users,
        ]);
    }
    public function edit(Approval $approval)
    {
        // Permission Check: Only creator or super admin/manager?
        // Let's assume creator or explicit permission for now.
        if (Auth::id() !== $approval->created_by_id && !Auth::user()->can('aprobaciones.update')) {
            abort(403);
        }

        $approval->load(['approvers', 'priority', 'files']);

        $priority_type = TagCategory::where('slug', 'tipo_prioridad')->first();
        $priorities = $priority_type ? Tag::where('category_id', $priority_type->id)->get() : [];

        $users = \App\Services\PermissionCacheService::getUsersByArea('aprobaciones');

        return Inertia::render('Approvals/Form', [
            'approval' => $approval,
            'priorities' => $priorities,
            'users' => $users,
        ]);
    }

    public function store(Request $request, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, NotificationService $notificationService, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'buy' => 'boolean',
            'approvers' => 'required|array|min:1',
            'approvers.*' => 'exists:users,id',
            'priority_id' => 'required|exists:tags,id',
            'description' => 'nullable|string',
            'all_approvers' => 'boolean',
            'files.*' => 'file|max:51200', // 50MB
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $statusPending = Tag::whereHas('category', fn($q) => $q->where('slug', 'estado_aprobacion'))
            ->where('name', 'En espera')->first();

        $approval = Approval::create([
            'name' => $request->name,
            'description' => $request->description,
            'buy' => $request->buy ?? false,
            'status_id' => $statusPending->id,
            'priority_id' => $request->priority_id,
            'all_approvers' => $request->all_approvers ?? false,
            'created_by_id' => Auth::id(),
        ]);

        // Attach Approvers (Using createMany/Loop since it is HasMany)
        foreach ($request->approvers as $userId) {
            $approval->approvers()->create([
                'user_id' => $userId,
                'status_id' => $statusPending->id,
                // 'order' => ... if needed
            ]);
        }

        // Upload Files
        if ($request->hasFile('files')) {
            $area = Area::where('slug', 'aprobaciones')->first();
            $folder = $folderMaker->execute($approval->name, null); // Create folder by name
            $uploader->execute($request->file('files'), $approval, $folder->id, $area->id);
        }

        if ($request->has('pending_file_ids')) {
            $area = Area::where('slug', 'aprobaciones')->first();
            foreach ($request->pending_file_ids as $fileId) {
                $file = \App\Models\File::find($fileId);
                if ($file) {
                    $linker->execute($file, $approval, $area->id);
                }
            }
        }

        // Notifications
        foreach ($approval->approvers as $approver) {
            $notificationService->createImmediate(
                user: $approver->user,
                title: 'Nueva Solicitud de Aprobación (' . $approval->priority->name . ')',
                message: 'Se te ha asignado como aprobador: ' . $approval->name,
                data: [
                    'approval_name' => $approval->name,
                    'priority' => $approval->priority->name,
                    'requester' => Auth::user()->name,
                ],
                notifiable: $approval,
                sendEmail: true,
                emailTemplate: 'emails.approval-assigned'
            );
        }

        return redirect()->route('approvals.index')->with('success', 'Solicitud creada correctamente');
    }

    public function show(Approval $approval)
    {
        // For AJAX modal
        $approval->load(['creator', 'status', 'priority', 'approvers.user', 'approvers.status', 'files']);
        return response()->json($approval);
    }

    // Actions: Approve/Reject handled via separate endpoints

    public function approve(Request $request, Approval $approval)
    {
        $user = Auth::user();
        $approver = $approval->approvers()->where('user_id', $user->id)->first();

        if (!$approver) {
            return back()->with('error', 'No autorizado');
        }

        $statusApproved = Tag::whereHas('category', fn($q) => $q->where('slug', 'estado_aprobacion'))
            ->where('name', 'Aprobado')->first();
        $statusRejected = Tag::whereHas('category', fn($q) => $q->where('slug', 'estado_aprobacion'))
            ->where('name', 'Rechazado')->first();

        // Update Approver Status direct on model, not pivot
        $approver->update([
            'status_id' => $statusApproved->id,
            'comment' => $request->comment,
            'responded_at' => now(),
        ]);

        // Refresh relation to check all
        $approval->load('approvers');

        // Logic check
        if ($approval->all_approvers) {
            if ($approval->approvers->contains(fn($a) => $a->status_id === $statusRejected->id)) {
                $approval->update(['status_id' => $statusRejected->id, 'rejected_at' => now()]);
            } elseif ($approval->approvers->every(fn($a) => $a->status_id === $statusApproved->id)) {
                $approval->update(['status_id' => $statusApproved->id, 'approved_at' => now()]);
            }
        } else {
            $approval->update(['status_id' => $statusApproved->id, 'approved_at' => now()]);
        }

        return back()->with('success', 'Aprobado correctamente');
    }

    public function reject(Request $request, Approval $approval)
    {
        $user = Auth::user();
        $approver = $approval->approvers()->where('user_id', $user->id)->first();

        if (!$approver) {
            return back()->with('error', 'No autorizado');
        }

        $statusRejected = Tag::whereHas('category', fn($q) => $q->where('slug', 'estado_aprobacion'))
            ->where('name', 'Rechazado')->first();

        // Update Approver Status
        $approver->update([
            'status_id' => $statusRejected->id,
            'comment' => $request->comment,
            'responded_at' => now(),
        ]);

        // Immediate rejection for module logic
        $approval->update(['status_id' => $statusRejected->id, 'rejected_at' => now()]);

        return back()->with('success', 'Rechazado correctamente');
    }

    public function destroy(Approval $approval)
    {
        $approval->delete();
        return back()->with('success', 'Eliminado correctamente');
    }
}
