<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AreaResource;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $areas = Area::with(['parent', 'children'])->get();
        return AreaResource::collection($areas);
    }

    /**
     * Display the specified resource.
     */
    public function show(Area $area)
    {
        return new AreaResource($area->load(['parent', 'children']));
    }
}
