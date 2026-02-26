<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Colocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Only the colocation owner may manage categories.
     */
    private function ensureOwner(Colocation $colocation): void
    {
        if ($colocation->owner_id !== Auth::id()) {
            abort(403, 'Only the colocation owner can manage categories.');
        }
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request, Colocation $colocation)
    {
        $this->ensureOwner($colocation);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $colocation->categories()->create($validated);

        return back()->with('success', 'Category created.');
    }

    /**
     * Update an existing category.
     */
    public function update(Request $request, Colocation $colocation, Category $category)
    {
        $this->ensureOwner($colocation);

        if ($category->colocation_id !== $colocation->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $category->update($validated);

        return back()->with('success', 'Category updated.');
    }

    /**
     * Delete a category.
     */
    public function destroy(Colocation $colocation, Category $category)
    {
        $this->ensureOwner($colocation);

        if ($category->colocation_id !== $colocation->id) {
            abort(404);
        }

        // Null out category on related expenses before deleting
        $category->expenses()->update(['category_id' => null]);
        $category->delete();

        return back()->with('success', 'Category deleted.');
    }
}
