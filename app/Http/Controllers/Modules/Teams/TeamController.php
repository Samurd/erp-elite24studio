<?php

namespace App\Http\Controllers\Modules\Teams;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $currentUserId = Auth::id();
        $query = Team::with([
            'members' => function ($q) use ($currentUserId) {
                $q->where('user_id', $currentUserId)->select('users.id'); // Only need ID for existence check
            }
        ])->withCount([
                    'members' => function ($q) {
                        $q->select(\Illuminate\Support\Facades\DB::raw('count(distinct users.id)'));
                    },
                    'channels'
                ]);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('isPublicFilter') && $request->input('isPublicFilter') !== null) {
            $query->where('isPublic', $request->input('isPublicFilter'));
        }

        $teams = $query->paginate($request->input('perPage', 10))
            ->withQueryString();

        return Inertia::render('Teams/Index', [
            'teams' => $teams,
            'filters' => $request->only('search', 'isPublicFilter', 'perPage'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Teams/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'isPublic' => 'boolean',
            'photo' => 'nullable|mimes:jpg,jpeg,png,white|max:4096',
        ]);

        $team = Team::create([
            'name' => $request->name,
            'description' => $request->description,
            'isPublic' => $request->isPublic ?? true,
            'owner_id' => Auth::id(),
        ]);

        if ($request->hasFile('photo')) {
            $team->updateProfilePhoto($request->file('photo'));
        }

        // Attach creator as owner
        $ownerRole = TeamRole::where('slug', 'owner')->first();
        $team->members()->attach(Auth::id(), ['role_id' => $ownerRole->id]);

        return redirect()->route('teams.show', $team->id)->with('message', 'Equipo creado exitosamente.');
    }

    public function show(Request $request, Team $team)
    {
        $userId = Auth::id();
        $channelId = $request->input('channel');

        $team->load(['members:id,name,email,profile_photo_path', 'channels']); // Optimized loading

        // Membership check
        $member = $team->members->where('id', $userId)->first();
        $isMember = !!$member;
        $currentUserRole = null;

        if ($isMember) {
            $roleId = $member->pivot->role_id;
            $currentUserRole = TeamRole::find($roleId);
        } elseif (!$team->isPublic) {
            // Private team, non-member
            return Inertia::render('Teams/Show', [
                'team' => $team,
                'isPrivateTeamNonMember' => true,
            ]);
        }

        // Get Members with Roles
        $members = $team->members()
            ->get()
            ->unique('id')
            ->map(function ($m) {
                $role = TeamRole::find($m->pivot->role_id);
                return [
                    'id' => $m->id,
                    'name' => $m->name,
                    'email' => $m->email,
                    'profile_photo_url' => $m->profile_photo_url,
                    'role_id' => $m->pivot->role_id,
                    'role_name' => $role ? $role->name : 'Sin rol',
                    'role_slug' => $role ? $role->slug : null,
                ];
            })
            ->values()
            ->toArray();

        // Load Channel if specific one requested
        $activeChannel = null;
        if ($channelId) {
            $activeChannel = $team->channels()->findOrFail($channelId);

            $isChannelMember = false;
            if ($isMember) {
                if (!$activeChannel->is_private) {
                    // For public channels, implies membership or at least access
                    $isChannelMember = true;
                    // But strictly speaking, if we want them to "join" explicitly to show up in lists, 
                    // we might need to check DB. But the list logic assumed TRUE. 
                    // Let's stick to the list logic for consistency: Public = accessible.
                } else {
                    $isChannelMember = $activeChannel->members()->where('user_id', $userId)->exists();
                }
            }
            $activeChannel->is_channel_member = $isChannelMember;
            $activeChannel->members_count = $activeChannel->is_private ? $activeChannel->members()->count() : count($members);
        }



        // 1. Get User's Channel Memberships for this team in ONE query
        $userChannelIds = DB::table('channel_user')
            ->where('user_id', $userId)
            ->whereIn('channel_id', $team->channels->pluck('id'))
            ->pluck('channel_id')
            ->toArray();

        // 2. Prepare Channels Data
        $isOwner = $currentUserRole && $currentUserRole->slug === 'owner';

        $channels = $team->channels->map(function ($ch) use ($isMember, $userChannelIds, $members, $isOwner) {
            $isChannelMember = false;
            if ($isMember) {
                if (!$ch->is_private || $isOwner) {
                    $isChannelMember = true;
                } else {
                    // Check against loaded IDs
                    $isChannelMember = in_array($ch->id, $userChannelIds);
                }
            }

            return [
                'id' => $ch->id,
                'name' => $ch->name,
                'description' => $ch->description,
                'is_private' => $ch->is_private,
                'members_count' => $ch->is_private ? $ch->members()->count() : count($members),
                'is_channel_member' => $isChannelMember,
                'parent_id' => $ch->parent_id,
            ];
        });

        $teamRoles = TeamRole::all();

        // Available Users
        $memberIds = collect($members)->pluck('id')->toArray();

        return Inertia::render('Teams/Show', [
            'team' => $team,
            'channel' => null, // Deprecated, client-side nav uses initial null or logic
            'channels' => $channels,
            'members' => $members,
            'isMember' => $isMember,
            'currentUserRole' => $currentUserRole,
            'teamRoles' => $teamRoles,
            'availableUsers' => Inertia::lazy(fn() => User::whereNotIn('id', $memberIds)->orderBy('name')->get(['id', 'name', 'email'])),
            'isPrivateTeamNonMember' => false,
        ]);
    }

    public function update(Request $request, Team $team)
    {
        // Validation & Auth check done in Request or here
        // Assuming Owner check logic 
        $user = Auth::user();
        if (!$team->isOwner($user)) {
            abort(403, 'Solo los owners pueden actualizar el equipo.');
        }

        \Illuminate\Support\Facades\Log::info('Updating Team:', ['team_id' => $team->id, 'inputs' => $request->all(), 'has_photo' => $request->hasFile('photo')]);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'isPublic' => 'boolean',
            'photo' => 'nullable|mimes:jpg,jpeg,png|max:4096',
        ]);

        $team->update([
            'name' => $request->name,
            'description' => $request->description,
            'isPublic' => $request->isPublic
        ]);

        if ($request->hasFile('photo')) {
            $team->updateProfilePhoto($request->file('photo'));
        }

        return back()->with('message', 'Equipo actualizado exitosamente.');
    }

    public function destroy(Team $team)
    {
        if (!$team->isOwner(Auth::user())) {
            abort(403);
        }
        $team->delete();
        return redirect()->route('teams.index')->with('message', 'Equipo eliminado.');
    }

    // --- Membership Actions ---

    public function join(Team $team)
    {
        if ($team->members()->where('user_id', Auth::id())->exists()) {
            return back();
        }
        if (!$team->isPublic) {
            abort(403, 'Equipo privado.');
        }

        $memberRole = TeamRole::where('slug', 'member')->first();
        $team->members()->attach(Auth::id(), ['role_id' => $memberRole->id]);

        return redirect()->route('teams.show', $team->id)->with('message', 'Te has unido al equipo.');
    }

    public function leave(Team $team)
    {
        // Owner check similar to Livewire
        $user = Auth::user();
        if ($team->isOwner($user)) {
            // check if last owner
            // (simplified for brevity, should match Livewire logic)
        }
        $team->members()->detach($user->id);
        return redirect()->route('teams.index')->with('message', 'Has salido del equipo.');
    }

    public function addMember(Request $request, Team $team)
    {
        // Owner check
        if (!$team->isOwner(Auth::user()))
            abort(403);

        $request->validate(['user_id' => 'required|exists:users,id']);

        if ($team->members()->where('user_id', $request->user_id)->exists()) {
            return back()->with('error', 'Usuario ya es miembro.');
        }

        $memberRole = TeamRole::where('slug', 'member')->first();
        $team->members()->attach($request->user_id, ['role_id' => $memberRole->id]);

        return back()->with('message', 'Miembro agregado.');
    }

    public function removeMember(Team $team, User $user)
    {
        if (!$team->isOwner(Auth::user()))
            abort(403);
        // Add safety check for removing last owner
        $team->members()->detach($user->id);
        return back()->with('message', 'Miembro eliminado.');
    }

    public function changeRole(Request $request, Team $team, User $user)
    {
        if (!$team->isOwner(Auth::user()))
            abort(403);

        $request->validate(['role_id' => 'required|exists:team_roles,id']);

        // Safety check for last owner

        $team->members()->updateExistingPivot($user->id, ['role_id' => $request->role_id]);
        return back()->with('message', 'Rol actualizado.');
    }
}
