<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagsController extends Controller
{
    /**
     * Store a new tag.
     * Expects 'category_slug' to know where to attach it.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_slug' => 'required|string|exists:tag_categories,slug',
            'color' => 'nullable|string|max:50',
        ]);

        $category = TagCategory::where('slug', $request->category_slug)->firstOrFail();

        $tag = Tag::create([
            'category_id' => $category->id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'color' => $request->color ?? '#CCCCCC', // Default color if none provided
        ]);

        return back()->with('message', 'Etiqueta creada exitosamente.');
    }

    /**
     * Update an existing tag.
     */
    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
        ]);

        $tag->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'color' => $request->color,
        ]);

        return back()->with('message', 'Etiqueta actualizada exitosamente.');
    }

    /**
     * Remove the specified tag.
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();
        return back()->with('message', 'Etiqueta eliminada.');
    }
}
