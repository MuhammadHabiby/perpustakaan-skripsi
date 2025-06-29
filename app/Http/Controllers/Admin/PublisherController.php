<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePublisherRequest;
use App\Http\Requests\Admin\UpdatePublisherRequest;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublisherController extends Controller
{
    public function index(Request $request): View
    {
        $publishers = Publisher::orderBy('updated_at', 'desc')->get();
        return view('admin.publishers.index', compact('publishers'));
    }

    public function create(): View
    {
        return view('admin.publishers.create');
    }

    public function store(StorePublisherRequest $request): RedirectResponse
    {
        Publisher::create($request->validated());
        return redirect()->route('admin.publishers.index')
            ->with('success', 'Penerbit baru berhasil ditambahkan.');
    }

    public function show(Publisher $publisher): View
    {
        return view('admin.publishers.show', compact('publisher'));
    }

    public function edit(Publisher $publisher): View
    {
        return view('admin.publishers.edit', compact('publisher'));
    }

    public function update(UpdatePublisherRequest $request, Publisher $publisher): RedirectResponse
    {
        $publisher->update($request->validated());
        return redirect()->route('admin.publishers.index')
            ->with('success', 'Penerbit berhasil diperbarui.');
    }

    public function destroy(Publisher $publisher): RedirectResponse
    {
        try {
            if ($publisher->books()->exists()) {
                return redirect()->route('admin.publishers.index')
                    ->with('error', 'Gagal menghapus! Penerbit masih digunakan oleh buku.');
            }

            $publisher->delete();
            return redirect()->route('admin.publishers.index')
                ->with('success', 'Penerbit berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.publishers.index')
                ->with('error', 'Gagal menghapus penerbit karena masih terhubung dengan data lain.');
        } catch (\Exception $e) {
            return redirect()->route('admin.publishers.index')
                ->with('error', 'Terjadi kesalahan saat menghapus penerbit.');
        }
    }
}
