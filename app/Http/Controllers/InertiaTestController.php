<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class InertiaTestController extends Controller
{
    public function show()
    {
        return Inertia::render('TestPage', [
            'message' => 'This message is coming from the Laravel backend via Inertia!',
        ]);
    }
}
