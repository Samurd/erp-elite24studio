<?php

use App\Livewire\Modules\Cloud\Index;
use Illuminate\Support\Facades\Route;

Route::middleware("can-area:view,cloud")
    ->prefix("cloud")
    ->name("cloud.")
    ->group(function () {
        Route::get("/", [\App\Http\Controllers\CloudController::class, 'index'])->name("index");
        Route::get("/selector-data", [\App\Http\Controllers\CloudController::class, 'selectorData'])->name("selector.data");
        Route::post("/folder", [\App\Http\Controllers\CloudController::class, 'createFolder'])->name("folder.create");
        Route::post("/upload", [\App\Http\Controllers\CloudController::class, 'uploadFile'])->name("file.upload");
        Route::put("/{id}/rename", [\App\Http\Controllers\CloudController::class, 'rename'])->name("rename");
        Route::delete("/{id}", [\App\Http\Controllers\CloudController::class, 'delete'])->name("delete");
        Route::get("/file/{file}/download", [\App\Http\Controllers\CloudController::class, 'download'])->name("file.download");

        // Sharing
        Route::get("/{type}/{id}/share-data", [\App\Http\Controllers\CloudController::class, 'getShareData'])->name("share.data");
        Route::post("/{type}/{id}/share", [\App\Http\Controllers\CloudController::class, 'share'])->name("share");
        Route::delete("/share/{shareId}", [\App\Http\Controllers\CloudController::class, 'unshare'])->name("unshare");
        Route::post("/{type}/{id}/public-link", [\App\Http\Controllers\CloudController::class, 'generatePublicLink'])->name("public-link.create");
        Route::delete("/{type}/{id}/public-link", [\App\Http\Controllers\CloudController::class, 'deletePublicLink'])->name("public-link.delete");

        // Model Attachments
        Route::controller(\App\Http\Controllers\Modules\Cloud\AttachmentsController::class)->prefix('attachments')->name('attachments.')->group(function () {
            Route::post('/', 'store')->name('store');
            Route::post('/link', 'link')->name('link');
            Route::post('/{file}/detach', 'detach')->name('detach');
            Route::delete('/{file}', 'destroy')->name('destroy');
        });
    });
