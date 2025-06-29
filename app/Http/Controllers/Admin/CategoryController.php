<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::orderBy('updated_at', 'desc')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Category::create($request->validated());
        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function show(Category $category): View
    {
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());
        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        try {
            if ($category->books()->exists()) {
                return redirect()->route('admin.categories.index')
                    ->with('error', 'Gagal menghapus! Kategori masih digunakan oleh buku.');
            }

            $category->delete();
            return redirect()->route('admin.categories.index')
                ->with('success', 'Kategori berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Gagal menghapus kategori karena masih terhubung dengan data lain.');
        } catch (\Exception $e) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Terjadi kesalahan saat menghapus kategori.');
        }
    }
}
