<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAuthorRequest;
use App\Http\Requests\Admin\UpdateAuthorRequest;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthorController extends Controller
{
    public function index(Request $request): View
    {
        $authors = Author::orderBy('updated_at', 'desc')->get();
        return view('admin.authors.index', compact('authors'));
    }

    public function create(): View
    {
        return view('admin.authors.create');
    }

    public function store(StoreAuthorRequest $request): RedirectResponse
    {
        Author::create($request->validated());
        return redirect()->route('admin.authors.index')
            ->with('success', 'Pengarang baru berhasil ditambahkan.');
    }

    public function show(Author $author): View
    {
        return view('admin.authors.show', compact('author'));
    }

    public function edit(Author $author): View
    {
        return view('admin.authors.edit', compact('author'));
    }

    public function update(UpdateAuthorRequest $request, Author $author): RedirectResponse
    {
        $author->update($request->validated());
        return redirect()->route('admin.authors.index')
            ->with('success', 'Pengarang berhasil diperbarui.');
    }

    public function destroy(Author $author): RedirectResponse
    {
        try {
            if ($author->books()->exists()) {
                return redirect()->route('admin.authors.index')
                    ->with('error', 'Gagal menghapus! Pengarang masih digunakan oleh buku.');
            }

            $author->delete();
            return redirect()->route('admin.authors.index')
                ->with('success', 'Pengarang berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.authors.index')
                ->with('error', 'Gagal menghapus pengarang karena masih terhubung dengan data lain.');
        } catch (\Exception $e) {
            return redirect()->route('admin.authors.index')
                ->with('error', 'Terjadi kesalahan saat menghapus pengarang.');
        }
    }
}
