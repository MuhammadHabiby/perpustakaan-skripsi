@extends('admin.components.main')

@section('title', 'Edit Buku')
@section('page-title', 'Edit Buku: ' . $book->title)

@section('content')
    <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Formulir Edit Buku</h6>
            </div>
            <div class="card-body">
                @include('admin.components.flash_messages')
                @include('admin.components.validation_errors')

                @include('admin.books._form', ['book' => $book])
            </div>
            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('admin.books.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan Buku</button>
            </div>
        </div>
    </form>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Eksemplar Buku Ini</h6>
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addCopyModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Eksemplar
            </button>
        </div>
        <div class="card-body">
            @if (session('success_copy'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success_copy') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error_copy'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error_copy') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->storeCopy->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading">Gagal Menambahkan Eksemplar:</h6>
                    <ul>
                        @foreach ($errors->storeCopy->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @php $editErrorFound = false; @endphp
            @foreach ($book->copies as $copy)
                @if ($errors->{'updateCopy_' . $copy->id}->any() && !$editErrorFound)
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading">Gagal Memperbarui Eksemplar ({{ $copy->copy_code }}):</h6>
                        <ul>
                            @foreach ($errors->{'updateCopy_' . $copy->id}->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @php $editErrorFound = true; @endphp
                @endif
            @endforeach


            @if ($book->copies->isEmpty())
                <div class="alert alert-info text-center">
                    Belum ada data eksemplar untuk buku ini.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Kode Eksemplar</th>
                                <th>Status</th>
                                <th>Kondisi</th>
                                <th>Ditambahkan</th>
                                <th class="action-column">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($book->copies as $copy)
                                <tr class="align-middle">
                                    <td>{{ $copy->copy_code }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $copy->status->badgeColor() }}">
                                            {{ $copy->status->label() }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $copy->condition->badgeColor() }}">
                                            {{ $copy->condition->label() }}
                                        </span>
                                    </td>
                                    <td>{{ $copy->created_at ? $copy->created_at->diffForHumans() : '-' }}</td>
                                    <td class="action-column">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-warning" title="Edit Eksemplar"
                                                data-bs-toggle="modal" data-bs-target="#editCopyModal-{{ $copy->id }}">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" title="Hapus Eksemplar"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteCopyModal-{{ $copy->id }}">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Tambah Eksemplar --}}
    <div class="modal fade" id="addCopyModal" tabindex="-1" aria-labelledby="addCopyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addCopyModalLabel">Tambah Eksemplar Baru</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.books.copies.store', $book) }}" method="POST">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="copy_code" class="form-label">Kode Eksemplar</label>
                            <input type="text" class="form-control @error('copy_code', 'storeCopy') is-invalid @enderror"
                                id="copy_code" name="copy_code" value="{{ old('copy_code') }}" required>
                            @error('copy_code', 'storeCopy')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="condition" class="form-label">Kondisi Awal</label>
                            <select class="form-select @error('condition', 'storeCopy') is-invalid @enderror"
                                id="condition" name="condition">
                                @foreach ($conditions as $conditionEnum)
                                    <option value="{{ $conditionEnum->value }}"
                                        {{ old('condition') == $conditionEnum->value || $conditionEnum === App\Enum\BookCondition::Good ? 'selected' : '' }}>
                                        {{ $conditionEnum->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('condition', 'storeCopy')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Eksemplar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($book->copies as $copy)
        <div class="modal fade" id="editCopyModal-{{ $copy->id }}" tabindex="-1"
            aria-labelledby="editCopyModalLabel-{{ $copy->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="editCopyModalLabel-{{ $copy->id }}">Edit Eksemplar:
                            {{ $copy->copy_code }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.book-copies.update', $copy) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_status_{{ $copy->id }}" class="form-label">Status</label>
                                <select
                                    class="form-select @error('status', 'updateCopy_' . $copy->id) is-invalid @enderror"
                                    id="edit_status_{{ $copy->id }}" name="status"
                                    {{ $copy->status === App\Enum\BookCopyStatus::Borrowed || $copy->status === App\Enum\BookCopyStatus::Booked ? 'disabled' : '' }}>
                                    @foreach ($statuses as $statusEnum)
                                        <option value="{{ $statusEnum->value }}"
                                            {{ old('status', $copy->status->value) == $statusEnum->value ? 'selected' : '' }}>
                                            {{ $statusEnum->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($copy->status === App\Enum\BookCopyStatus::Borrowed || $copy->status === App\Enum\BookCopyStatus::Booked)
                                    <small class="text-danger d-block mt-1">Status tidak bisa diubah jika sedang
                                        dipinjam/dibooking.</small>
                                @endif
                                @error('status', 'updateCopy_' . $copy->id)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="edit_condition_{{ $copy->id }}" class="form-label">Kondisi</label>
                                <select
                                    class="form-select @error('condition', 'updateCopy_' . $copy->id) is-invalid @enderror"
                                    id="edit_condition_{{ $copy->id }}" name="condition">
                                    @foreach ($conditions as $conditionEnum)
                                        <option value="{{ $conditionEnum->value }}"
                                            {{ old('condition', $copy->condition->value) == $conditionEnum->value ? 'selected' : '' }}>
                                            {{ $conditionEnum->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('condition', 'updateCopy_' . $copy->id)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary"
                                {{ $copy->status === App\Enum\BookCopyStatus::Borrowed || $copy->status === App\Enum\BookCopyStatus::Booked ? 'disabled' : '' }}>Simpan
                                Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteCopyModal-{{ $copy->id }}" tabindex="-1"
            aria-labelledby="deleteCopyModalLabel-{{ $copy->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="deleteCopyModalLabel-{{ $copy->id }}">Konfirmasi Hapus
                            Eksemplar</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus eksemplar dengan kode: <strong>{{ $copy->copy_code }}</strong>?
                        Tindakan ini tidak dapat dibatalkan. Pastikan eksemplar tidak sedang dipinjam atau dibooking.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form action="{{ route('admin.book-copies.destroy', $copy) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"
                                {{ $copy->status === App\Enum\BookCopyStatus::Borrowed || $copy->status === App\Enum\BookCopyStatus::Booked ? 'disabled' : '' }}>Ya,
                                Hapus Eksemplar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach


@endsection

@section('script')
    <script>
        function previewImage() {
            const image = document.querySelector('#cover_image');
            const imgPreview = document.querySelector('#image-preview');
            const defaultImage = "{{ asset('assets/images/no-image.png') }}";

            if (image.files && image.files[0]) {
                imgPreview.style.display = 'block';
                const oFReader = new FileReader();
                oFReader.readAsDataURL(image.files[0]);
                oFReader.onload = function(oFREvent) {
                    imgPreview.src = oFREvent.target.result;
                }
            } else {
                const currentImage =
                    "{{ isset($book) && $book->cover_image ? asset('storage/' . $book->cover_image) : '' }}";
                if (currentImage) {
                    imgPreview.src = currentImage;
                    imgPreview.style.display = 'block';
                } else {
                    imgPreview.src = '#'; // Reset src
                    imgPreview.style.display = 'none';
                }
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            previewImage();
        });

        $(document).ready(function() {
            @if ($errors->hasBag('storeCopy'))
                var addModal = new bootstrap.Modal(document.getElementById('addCopyModal'));
                if (addModal) {
                    addModal.show();
                }
            @endif

            @foreach ($book->copies as $copy)
                @if ($errors->hasBag('updateCopy_' . $copy->id))
                    var editModal = new bootstrap.Modal(document.getElementById(
                        'editCopyModal-{{ $copy->id }}'));
                    if (editModal) {
                        editModal.show();
                    }
                    @break
                @endif
            @endforeach
        });
    </script>
@endsection
