<?php

use App\Http\Controllers\Auth\OauthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UsersController;
use App\Livewire\Modules\PublicShare\Index as PublicShareIndex;
use App\Livewire\Modules\Settings\Index;
use App\Livewire\Modules\Users\CreateOrUpdate;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return redirect()->route("dashboard");
});

Route::get('/inertia-test', [\App\Http\Controllers\InertiaTestController::class, 'show']);

// Ruta pública para compartir
Route::get("/share/{token}/{folder?}", PublicShareIndex::class)->name(
    "public.share",
);


Route::middleware([
    "auth:sanctum",
    config("jetstream.auth_session"),
    "verified",
])->group(function () {

    Route::get('/files/{file}/download', [FileController::class, 'download'])->name('files.download');
    Route::get('/files/{file}/view', [FileController::class, 'stream'])->name('files.view');

    Route::get("/dashboard", [\App\Http\Controllers\DashboardController::class, 'index'])->name("dashboard");

    Route::get("/auth/{provider}", [OauthController::class, "redirect"])->name(
        "oauth.redirect",
    );
    Route::get("/auth/{provider}/callback", [
        OauthController::class,
        "callback",
    ])->name("oauth.callback");
    Route::get("/auth/{provider}/callback", [
        OauthController::class,
        "callback",
    ])->name("oauth.callback");
    Route::post("/auth/{provider}/disconnect", [
        OauthController::class,
        "disconnect",
    ])
        ->name("oauth.disconnect")
        ->middleware(["auth"]);

    require __DIR__ . "/modules/contacts.php";
    require __DIR__ . "/modules/finances.php";
    require __DIR__ . "/modules/rrhh.php";
    require __DIR__ . "/modules/quotes.php";
    require __DIR__ . "/modules/approvals.php";
    require __DIR__ . "/modules/case-record.php";
    require __DIR__ . "/modules/donations.php";
    require __DIR__ . "/modules/reports.php";
    require __DIR__ . "/modules/policies.php";
    require __DIR__ . "/modules/certificates.php";
    require __DIR__ . "/modules/licenses.php";
    require __DIR__ . "/modules/projects.php";
    require __DIR__ . "/modules/worksites.php";
    require __DIR__ . "/modules/calendar.php";
    require __DIR__ . "/modules/kpis.php";
    require __DIR__ . "/modules/marketing.php";
    require __DIR__ . "/modules/planner.php";
    require __DIR__ . "/modules/subs.php";
    require __DIR__ . "/modules/cloud.php";
    require __DIR__ . "/modules/teams.php";
    require __DIR__ . "/modules/meetings.php";
    require __DIR__ . "/modules/notifications.php";

    // Profile (Jetstream Inertia)
    Route::get('/user/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');

    Route::get("/settings", Index::class)->name("settings.index");

    Route::get("/users", [UsersController::class, "index"])->name(
        "users.index",
    );

    Route::get("/users/create", [UsersController::class, "create"])->name("users.create");
    Route::get("/users/{user}/edit", [UsersController::class, "edit"])->name("users.edit");
    Route::put("/users/{user}", [UsersController::class, "update"])->name("users.update");

    // Role/Permission API Routes for Users Form
    Route::post("/users/roles", [UsersController::class, "storeRole"])->name("users.roles.store");
    Route::put("/users/roles/{role}", [UsersController::class, "updateRole"])->name("users.roles.update");
    Route::get("/users/roles/{role}", [UsersController::class, "getRole"])->name("users.roles.get");
    Route::get("/users/permissions", [UsersController::class, "getAllPermissions"])->name("users.permissions.index");
    Route::post("/users/permissions", [UsersController::class, "storePermission"])->name("users.permissions.store");

    Route::post("/users", [UsersController::class, "store"])->name(
        "users.store",
    );
    Route::delete("/users/{user}", [UsersController::class, "destroy"])->name("users.destroy");
});
