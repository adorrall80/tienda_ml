<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categorias.index', [
            'categories' => Category::with('parent')->withCount('products')
                ->orderBy('orden')->orderBy('nombre')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return $this->form(new Category(['activo' => true, 'orden' => 0]));
    }

    public function edit(Category $category): View
    {
        return $this->form($category);
    }

    public function store(Request $request): RedirectResponse
    {
        Category::create($this->validated($request));

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría creada.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->validated($request, $category));

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría actualizada.');
    }

    private function form(Category $category): View
    {
        return view('admin.categorias.form', [
            'category' => $category,
            'parents' => Category::orderBy('nombre')->get()->where('id', '!=', $category->id),
        ]);
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:150', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('categories', 'slug')->ignore($category?->id)],
            'parent_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'icono' => ['nullable', 'string', 'max:40'],
            'imagen' => ['nullable', 'url:http,https', 'max:255'],
            'orden' => ['required', 'integer', 'between:0,255'],
            'activo' => ['required', 'boolean'],
        ]);

        $ancestorId = $data['parent_id'] ?? null;
        $visited = [];
        while ($ancestorId !== null) {
            if ((string) $ancestorId === (string) $category?->id || isset($visited[$ancestorId])) {
                throw ValidationException::withMessages([
                    'parent_id' => 'La categoría padre no puede ser ella misma ni una de sus subcategorías.',
                ]);
            }
            $visited[$ancestorId] = true;
            $ancestorId = Category::findOrFail($ancestorId)->parent_id;
        }

        // Keep existing product/filter URLs when only the category name changes.
        $data['slug'] = $data['slug'] ?? $category?->slug ?? $this->uniqueSlug($data['nombre']);
        $data['parent_id'] = $data['parent_id'] ?? null;
        $data['activo'] = $request->boolean('activo');

        return $data;
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'categoria';
        $slug = $base;
        for ($suffix = 2; Category::where('slug', $slug)->exists(); $suffix++) {
            $slug = $base.'-'.$suffix;
        }

        return $slug;
    }
}
