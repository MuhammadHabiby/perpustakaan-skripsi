@extends('user.components.main')

@section('title', 'Katalog Buku')
@section('page-title', 'Katalog Buku Perpustakaan')

@section('content')
    <div class="row mb-4">
        <div class="col-md-4 col-lg-3 mb-3 mb-md-0">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="bi bi-filter me-2"></i>Filter</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('catalog.index') }}" method="GET">
                        <div class="mb-3">
                            <label for="search" class="form-label fw-semibold">Pencarian</label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" id="search" name="search"
                                    placeholder="Judul, Pengarang, ISBN..." value="{{ $searchQuery ?? '' }}">
                                <button class="btn btn-sm btn-outline-secondary" type="submit" id="search-button-submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                            <small class="text-muted">Tekan Enter atau klik ikon cari.</small>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label fw-semibold">Kategori</label>
                            <select class="form-select form-select-sm" id="category" name="category">
                                <option value="">-- Semua Kategori --</option>
                                @foreach ($categories as $id => $name)
                                    <option value="{{ $id }}"
                                        {{ ($categoryFilter ?? '') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">Terapkan Filter</button>
                            <a href="{{ route('catalog.index') }}" class="btn btn-outline-secondary btn-sm">Reset Filter</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8 col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row g-3" id="book-list-container">
                        @include('user.books._book_list', ['books' => $books])
                    </div>
                    <div id="loading-indicator" class="text-center mt-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .book-card {
            transition: transform .2s ease-in-out, box-shadow .2s ease-in-out;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        .book-cover {
            height: 250px;
            object-fit: cover;
            border-top-left-radius: var(--bs-card-inner-border-radius);
            border-top-right-radius: var(--bs-card-inner-border-radius);
        }

        .book-title {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 2.5em;
            font-size: 0.95rem;
        }

        .card-body .book-title a:hover {
            color: var(--bs-primary) !important;
        }
    </style>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            let searchTimeout;
            const searchInput = $('#search');
            const resultsContainer = $('#book-list-container');
            const loadingIndicator = $('#loading-indicator');
            const baseCatalogUrl = "{{ route('catalog.index') }}";
            const categoryFilter = $('#category').val();

            function resetToFilteredList() {
                let url = baseCatalogUrl;
                if (categoryFilter) {
                    url += '?category=' + categoryFilter;
                }
                window.location.href = url;
            }

            searchInput.on('keyup', function() {
                clearTimeout(searchTimeout);
                const query = $(this).val().trim();

                searchTimeout = setTimeout(function() {
                    if (query.length >= 3) {
                        loadingIndicator.show();
                        resultsContainer.css('opacity', 0.5).find('.pagination').hide();

                        $.ajax({
                            url: "{{ route('catalog.search.api') }}",
                            type: "GET",
                            data: {
                                search: query
                            },
                            success: function(response) {
                                resultsContainer.html(response.html);
                                loadingIndicator.hide();
                                resultsContainer.css('opacity', 1);
                            },
                            error: function(xhr) {
                                console.error("Error searching:", xhr);
                                resultsContainer.html(
                                    '<div class="col-12"><div class="alert alert-danger">Gagal memuat hasil pencarian.</div></div>'
                                );
                                loadingIndicator.hide();
                                resultsContainer.css('opacity', 1);
                            }
                        });
                    } else if (query.length === 0) {
                        resetToFilteredList();
                    } else {
                        resultsContainer.html(
                            '<div class="col-12 text-center text-muted">Ketik minimal 3 karakter untuk memulai pencarian...</div>'
                        ).find('.pagination').hide();
                        loadingIndicator.hide();
                        resultsContainer.css('opacity', 1);
                    }
                }, 500);
            });

            $('form[action="{{ route('catalog.index') }}"]').on('submit', function(e) {
                if (searchInput.val().trim() === '') {
                    searchInput.prop('disabled', true);
                }
            });

            $('#search-button-submit').on('click', function(e) {
                e.preventDefault();
                searchInput.trigger('keyup');
            });

        });
    </script>
@endsection
