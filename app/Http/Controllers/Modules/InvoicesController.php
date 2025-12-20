<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class InvoicesController extends Controller
{
    public function index()
    {
        return Inertia::render('Invoices/Index');
    }
}
