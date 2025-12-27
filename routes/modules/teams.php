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
        // Route::get('/chats/{userId?}', Chats::class)->name('chats'); // Old Livewire
        Route::get('/chats/{userId?}', [\App\Http\Controllers\Teams\TeamsChatsController::class, 'index'])->name('chats');
        Route::get('/api/chats/{userId}/conversation', [\App\Http\Controllers\Teams\TeamsChatsController::class, 'getConversation'])->name('chats.conversation');
        Route::post('/chats', [\App\Http\Controllers\Teams\TeamsChatsController::class, 'store'])->name('chats.store');
        Route::get('/chats/{chatId}/messages', [\App\Http\Controllers\Teams\TeamsChatsController::class, 'loadMore'])->name('chats.load-more');




        Route::controller(App\Http\Controllers\Modules\Teams\TeamController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{team}/{channel?}', 'show')->where('channel', '[0-9]+')->name('show');
            Route::get('/{team}/edit', 'edit')->name('edit'); // Usually 'edit' is GET
            // Route::put('/{team}', 'update')->name('update'); // Standard, but check if we need separate route entry or if Resource covers it. 
            // The user wanted specific lines replaced. I'll add the necessary method routes.
            Route::put('/{team}', 'update')->name('update');
            Route::delete('/{team}', 'destroy')->name('destroy');

            // Team Membership
            Route::post('/{team}/join', 'join')->name('join');
            Route::post('/{team}/leave', 'leave')->name('leave');

            // Members Management
            Route::post('/{team}/members', 'addMember')->name('members.add');
            Route::delete('/{team}/members/{user}', 'removeMember')->name('members.remove');
            Route::put('/{team}/members/{user}/role', 'changeRole')->name('members.role');
        });

        // Team Channels
        Route::controller(App\Http\Controllers\Modules\Teams\TeamChannelController::class)->group(function () {
            Route::post('/{team}/channels', 'store')->name('channels.store');
            Route::put('/{team}/channels/{channel}', 'update')->name('channels.update');
            Route::delete('/{team}/channels/{channel}', 'destroy')->name('channels.destroy');

            // Channel Membership
            Route::post('/{team}/channels/{channel}/join', 'join')->name('channels.join');
            Route::post('/{team}/channels/{channel}/leave', 'leave')->name('channels.leave');

            // Chat API
            Route::get('/{team}/channels/{channel}/messages', 'messages')->name('channels.messages');
            Route::post('/{team}/channels/{channel}/messages', 'sendMessage')->name('channels.messages.store');
        });
    });