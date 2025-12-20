<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class RrhhController extends Controller
{
    public function index()
    {
        return Inertia::render('Rrhh/Index');
    }
}
