<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Author;
use App\Models\Publisher;
use App\Http\Requests\Admin\StoreBookRequest;
use App\Http\Requests\Admin\UpdateBookRequest;
use App\Http\Requests\Admin\StoreBookCopyRequest;
use App\Http\Requests\Admin\UpdateBookCopyRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Enum\BookCopyStatus;
use App\Enum\BookCondition;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $books = Book::with(['category', 'author', 'publisher'])
            ->withCount('copies')
            ->orderBy('updated_at', 'desc')
            ->get();
        return view('admin.books.index', compact('books'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->pluck('name', 'id');
        $authors = Author::orderBy('name')->pluck('name', 'id');
        $publishers = Publisher::orderBy('name')->pluck('name', 'id');
        return view('admin.books.create', compact('categories', 'authors', 'publishers'));
    }

    public function show(Book $book): View
    {
        $book->load([
            'category',
            'author',
            'publisher',
            'copies' => function ($query) {
                $query->orderBy('copy_code', 'asc');
            }
        ]);

        return view('admin.books.show', compact('book'));
    }

    public function store(StoreBookRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $relativeStoragePath = null;

        DB::beginTransaction();
        try {
            if ($request->hasFile('cover_image')) {
                $relativeStoragePath = $request->file('cover_image')->store('covers', 'public');
                if ($relativeStoragePath === false) {
                    throw new \Exception("Gagal menyimpan file gambar.");
                }
                $validated['cover_image'] = $relativeStoragePath;
            }

            $book = Book::create($validated);

            $prefix = $validated['copy_code_prefix'];
            $startNum = $validated['copy_code_start'];
            $count = $validated['initial_copies'];

            for ($i = 0; $i < $count; $i++) {
                $newCopyCode = $prefix . ($startNum + $i);
                if (BookCopy::where('copy_code', $newCopyCode)->exists()) {
                    throw new \Exception("Kode eksemplar '{$newCopyCode}' sudah ada.");
                }
                BookCopy::create([
                    'book_id' => $book->id,
                    'copy_code' => $newCopyCode,
                    'status' => BookCopyStatus::Available,
                    'condition' => BookCondition::Good,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.books.index')
                ->with('success', 'Buku baru dan eksemplarnya berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($relativeStoragePath && Storage::disk('public')->exists($relativeStoragePath)) {
                Storage::disk('public')->delete($relativeStoragePath);
            }
            return redirect()->back()
                ->with('error', 'Gagal menambahkan buku: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Book $book): View
    {
        $book->load(['category', 'author', 'publisher', 'copies' => function ($query) {
            $query->orderBy('copy_code', 'asc');
        }]);
        $categories = Category::orderBy('name')->pluck('name', 'id');
        $authors = Author::orderBy('name')->pluck('name', 'id');
        $publishers = Publisher::orderBy('name')->pluck('name', 'id');
        $conditions = BookCondition::cases();
        $statuses = BookCopyStatus::cases();

        return view('admin.books.edit', compact('book', 'categories', 'authors', 'publishers', 'conditions', 'statuses'));
    }

    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $validated = $request->validated();
        $oldRelativePath = $book->cover_image;
        $newRelativePath = null;

        DB::beginTransaction();
        try {
            if ($request->hasFile('cover_image')) {
                $newRelativePath = $request->file('cover_image')->store('covers', 'public');
                if ($newRelativePath === false) {
                    throw new \Exception("Gagal menyimpan file gambar baru.");
                }
                $validated['cover_image'] = $newRelativePath;
            } else {
                unset($validated['cover_image']);
            }

            $book->update($validated);

            DB::commit();

            if ($newRelativePath && $oldRelativePath && Storage::disk('public')->exists($oldRelativePath)) {
                Storage::disk('public')->delete($oldRelativePath);
            }

            return redirect()->route('admin.books.index')
                ->with('success', 'Data buku berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($newRelativePath && Storage::disk('public')->exists($newRelativePath)) {
                Storage::disk('public')->delete($newRelativePath);
            }
            return redirect()->back()
                ->with('error', 'Gagal memperbarui buku: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Book $book): RedirectResponse
    {
        if ($book->copies()->exists()) {
            return redirect()->route('admin.books.index')
                ->with('error', 'Gagal menghapus! Buku masih memiliki eksemplar. Hapus eksemplar terlebih dahulu.');
        }

        DB::beginTransaction();
        try {
            $relativeStoragePath = $book->cover_image;
            $bookTitle = $book->title;

            $book->delete();

            if ($relativeStoragePath && Storage::disk('public')->exists($relativeStoragePath)) {
                Storage::disk('public')->delete($relativeStoragePath);
            }

            DB::commit();

            return redirect()->route('admin.books.index')
                ->with('success', "Buku '{$bookTitle}' berhasil dihapus.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.books.index')
                ->with('error', 'Gagal menghapus buku: ' . $e->getMessage());
        }
    }

    public function storeCopy(StoreBookCopyRequest $request, Book $book): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $validated['book_id'] = $book->id;
            $validated['status'] = BookCopyStatus::Available;

            BookCopy::create($validated);

            return redirect()->route('admin.books.edit', $book)
                ->with('success_copy', 'Eksemplar baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['copy_code' => $e->getMessage()], 'storeCopy')
                ->withInput();
        }
    }

    public function updateCopy(UpdateBookCopyRequest $request, BookCopy $copy): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $currentStatus = $copy->status;
            $newStatusValue = $validated['status'];

            if (($currentStatus === BookCopyStatus::Borrowed || $currentStatus === BookCopyStatus::Booked)
                && $currentStatus->value !== $newStatusValue
            ) {
                throw new \Exception('Status tidak bisa diubah jika sedang dipinjam/dibooking.');
            }

            $copy->update($validated);

            return redirect()->route('admin.books.edit', $copy->book_id)
                ->with('success_copy', "Eksemplar '{$copy->copy_code}' berhasil diperbarui.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['update_error' => "Gagal memperbarui eksemplar: " . $e->getMessage()], 'updateCopy_' . $copy->id)
                ->withInput();
        }
    }

    public function destroyCopy(BookCopy $copy): RedirectResponse
    {
        if ($copy->status === BookCopyStatus::Borrowed || $copy->status === BookCopyStatus::Booked) {
            return redirect()->route('admin.books.edit', $copy->book_id)
                ->with('error_copy', "Gagal menghapus! Eksemplar '{$copy->copy_code}' sedang dipinjam atau dibooking.");
        }

        try {
            $copyCode = $copy->copy_code;
            $bookId = $copy->book_id;
            $copy->delete();

            return redirect()->route('admin.books.edit', $bookId)
                ->with('success_copy', "Eksemplar '{$copyCode}' berhasil dihapus.");
        } catch (\Exception $e) {
            return redirect()->route('admin.books.edit', $copy->book_id)
                ->with('error_copy', "Gagal menghapus eksemplar: " . $e->getMessage());
        }
    }
}
