<?php

namespace App\Livewire\Modules\Settings;

use App\Models\OauthConnection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{

    public $googleConnected;
    public $googleConnection;

    public function mount()
    {

        $user = Auth::user();

        $this->googleConnection = OauthConnection::where('user_id', $user->id)
            ->where('provider', 'google')
            ->first();

        $this->googleConnected = $this->googleConnection !== null;
    }
    public function render()
    {
        return view('livewire.modules.settings.index');
    }
}
