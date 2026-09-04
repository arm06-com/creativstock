<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage-categories');

        $categories = Category::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        Gate::authorize('manage-categories');

        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-categories');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('status', __('Category created successfully.'));
    }

    public function edit(Category $category): View
    {
        Gate::authorize('manage-categories');

        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        Gate::authorize('manage-categories');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
        ]);

        // Update slug if name changed
        if ($validated['name'] !== $category->name) {
            $slug = Str::slug($validated['name']);
            $original = $slug;
            $i = 1;
            while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = $original.'-'.($i++);
            }
            $validated['slug'] = $slug;
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('status', __('Category updated successfully.'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        Gate::authorize('manage-categories');

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('status', __('Category deleted successfully.'));
    }
}
