@extends('admin.components.main')

@section('title', 'Tambah Buku Baru')
@section('page-title', 'Tambah Buku Baru')

@section('content')
    <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Formulir Tambah Buku</h6>
            </div>
            <div class="card-body">
                @include('admin.components.flash_messages')
                @include('admin.components.validation_errors')

                @include('admin.books._form')

                <hr>
                <h6 class="mt-4 mb-3 text-primary">Tambah Eksemplar Awal</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="initial_copies" class="form-label">Jumlah Eksemplar</label>
                        <input type="number" class="form-control @error('initial_copies') is-invalid @enderror"
                            id="initial_copies" name="initial_copies" value="{{ old('initial_copies', 1) }}" required
                            min="1">
                        @error('initial_copies')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="copy_code_prefix" class="form-label">Kode Awal Eksemplar (Prefix)</label>
                        <input type="text" class="form-control @error('copy_code_prefix') is-invalid @enderror"
                            id="copy_code_prefix" name="copy_code_prefix" value="{{ old('copy_code_prefix') }}" required
                            placeholder="Contoh: BK/INV/">
                        @error('copy_code_prefix')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="copy_code_start" class="form-label">Nomor Awal</label>
                        <input type="number" class="form-control @error('copy_code_start') is-invalid @enderror"
                            id="copy_code_start" name="copy_code_start" value="{{ old('copy_code_start', 1) }}" required
                            min="1">
                        @error('copy_code_start')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <small class="form-text text-muted">Contoh: Jika prefix 'BK-' dan nomor awal 1, eksemplar akan
                            menjadi BK-1, BK-2, dst.</small>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('admin.books.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Buku & Eksemplar</button>
            </div>
        </div>
    </form>
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
                imgPreview.src = '#'; // Reset src
                imgPreview.style.display = 'none';
            }
        }
    </script>
@endsection
