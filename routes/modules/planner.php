<?php

use App\Http\Controllers\Modules\PlannerController;
use Illuminate\Support\Facades\Route;

Route::prefix('planner')->name('planner.')->group(function () {
    // Indexes
    Route::get('/', [PlannerController::class, 'index'])->name('index');

    // Plan CRUD
    Route::post('/', [PlannerController::class, 'storePlan'])->name('store');
    Route::put('/{id}', [PlannerController::class, 'updatePlan'])->name('update'); // careful with this one too, but usually PUT isn't ambiguous with GET
    Route::delete('/plan/{id}', [PlannerController::class, 'destroyPlan'])->name('destroy');

    // Buckets
    Route::post('/bucket', [PlannerController::class, 'storeBucket'])->name('buckets.store');
    Route::put('/bucket/{id}', [PlannerController::class, 'updateBucket'])->name('buckets.update');
    Route::delete('/bucket/{id}', [PlannerController::class, 'destroyBucket'])->name('buckets.destroy');
    Route::post('/bucket/reorder', [PlannerController::class, 'reorderBuckets'])->name('buckets.reorder');

    // Tasks
    Route::post('/task', [PlannerController::class, 'storeTask'])->name('tasks.store');
    Route::put('/task/{id}', [PlannerController::class, 'updateTask'])->name('tasks.update');
    Route::delete('/task/{id}', [PlannerController::class, 'destroyTask'])->name('tasks.destroy');
    Route::post('/task/reorder', [PlannerController::class, 'reorderTasks'])->name('tasks.reorder');

    // Show Plan - Catch-all for /planner/{id}
    // Must be last to avoid catching /planner/task or /planner/bucket if they were just /planner/{something}
    // Although /planner/task is distinct, having this last is safer practice.
    Route::get('/{id}', [PlannerController::class, 'show'])->name('show');
});