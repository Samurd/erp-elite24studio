<?php

use App\Livewire\Modules\Teams\ChannelManage;
use App\Livewire\Modules\Teams\Chats;
use App\Livewire\Modules\Teams\Create;
use App\Livewire\Modules\Teams\Index;
use App\Livewire\Modules\Teams\Show;
use App\Livewire\Modules\Teams\Update;
use Illuminate\Support\Facades\Route;

Route::middleware('can-area:view,teams')
    ->prefix('teams')
    ->name('teams.')
    ->group(function () {
        Route::get('/', Index::class)->name('index');
        Route::get('/create', Create::class)->name('create');
        // Route::get('/chats/{userId?}', Chats::class)->name('chats'); // Old Livewire
        Route::get('/chats/{userId?}', [\App\Http\Controllers\Teams\TeamsChatsController::class, 'index'])->name('chats');
        Route::post('/chats', [\App\Http\Controllers\Teams\TeamsChatsController::class, 'store'])->name('chats.store');
        Route::get('/chats/{chatId}/messages', [\App\Http\Controllers\Teams\TeamsChatsController::class, 'loadMore'])->name('chats.load-more');
        Route::get('/{team}', Show::class)->name('show');
        Route::get('/{team}/channels', Show::class)->name('channels.index');
        Route::get('/{team}/channels/{channel}', Show::class)->name('channels.show')->where('channel', '[0-9]+');
        Route::get('/{team}/edit', Update::class)->name('edit');
        Route::get('/{team}/channels/create', ChannelManage::class)->name('channels.create');
        Route::get('/{team}/channels/{channel}/edit', ChannelManage::class)->name('channels.edit');
    });