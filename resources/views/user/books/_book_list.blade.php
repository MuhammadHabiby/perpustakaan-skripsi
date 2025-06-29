@forelse ($books as $book)
    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
        <div class="card h-100 shadow-sm border-0 book-card">
            <a href="{{ route('catalog.show', $book->slug) }}" class="text-decoration-none">
                <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('assets/images/no-image-book.png') }}"
                    class="card-img-top book-cover" alt="{{ $book->title }}">
            </a>
            <div class="card-body d-flex flex-column">
                <h6 class="card-title fw-bold book-title flex-grow-1">
                    <a href="{{ route('catalog.show', $book->slug) }}"
                        class="text-dark text-decoration-none stretched-link">
                        {{ $book->title }}
                    </a>
                </h6>
                <p class="card-text text-muted small mb-1">
                    <i class="bi bi-person"></i> {{ $book->author?->name ?? 'N/A' }}
                </p>
                <p class="card-text text-muted small">
                    <i class="bi bi-tag"></i> {{ $book->category?->name ?? 'N/A' }}
                </p>
            </div>
        </div>
    </div>
@empty
    <div class="col-12">
        <div class="alert alert-warning text-center">
            <i class="bi bi-exclamation-triangle me-2"></i> Buku tidak ditemukan. Coba kata kunci atau filter lain.
        </div>
    </div>
@endforelse

@if (isset($books) && $books instanceof \Illuminate\Pagination\LengthAwarePaginator && !request()->ajax())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            <small class="text-muted">
                Menampilkan {{ $books->firstItem() }}
                hingga {{ $books->lastItem() }}
                dari {{ $books->total() }} hasil
            </small>
        </div>
        <div>
            {{ $books->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
@endif
