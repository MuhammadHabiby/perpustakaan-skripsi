@csrf
<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <label for="title" class="form-label">Judul Buku</label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                value="{{ old('title', $book->title ?? '') }}" required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="author_id" class="form-label">Pengarang</label>
                <select class="form-select @error('author_id') is-invalid @enderror" id="author_id" name="author_id">
                    <option value="">-- Pilih Pengarang --</option>
                    @foreach ($authors as $id => $name)
                        <option value="{{ $id }}"
                            {{ old('author_id', $book->author_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('author_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="publisher_id" class="form-label">Penerbit</label>
                <select class="form-select @error('publisher_id') is-invalid @enderror" id="publisher_id"
                    name="publisher_id">
                    <option value="">-- Pilih Penerbit --</option>
                    @foreach ($publishers as $id => $name)
                        <option value="{{ $id }}"
                            {{ old('publisher_id', $book->publisher_id ?? '') == $id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
                @error('publisher_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="category_id" class="form-label">Kategori</label>
                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                    name="category_id">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $id => $name)
                        <option value="{{ $id }}"
                            {{ old('category_id', $book->category_id ?? '') == $id ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="isbn" class="form-label">ISBN</label>
                <input type="text" class="form-control @error('isbn') is-invalid @enderror" id="isbn"
                    name="isbn" value="{{ old('isbn', $book->isbn ?? '') }}">
                @error('isbn')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="publication_year" class="form-label">Tahun Terbit</label>
                <input type="number" class="form-control @error('publication_year') is-invalid @enderror"
                    id="publication_year" name="publication_year"
                    value="{{ old('publication_year', $book->publication_year ?? '') }}" min="1000"
                    max="{{ date('Y') }}">
                @error('publication_year')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="location" class="form-label">Lokasi Rak</label>
                <input type="text" class="form-control @error('location') is-invalid @enderror" id="location"
                    name="location" value="{{ old('location', $book->location ?? '') }}">
                @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="synopsis" class="form-label">Sinopsis</label>
            <textarea class="form-control @error('synopsis') is-invalid @enderror" id="synopsis" name="synopsis" rows="5">{{ old('synopsis', $book->synopsis ?? '') }}</textarea>
            @error('synopsis')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="cover_image" class="form-label">Gambar Sampul</label>
            <input class="form-control @error('cover_image') is-invalid @enderror" type="file" id="cover_image"
                name="cover_image" onchange="previewImage()">
            @error('cover_image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @if (isset($book) && $book->cover_image)
                <img id="image-preview" src="{{ asset('storage/' . $book->cover_image) }}" alt="Current Cover"
                    class="img-thumbnail mt-2" style="max-height: 200px;">
            @else
                <img id="image-preview" src="#" alt="Image Preview" class="img-thumbnail mt-2"
                    style="max-height: 200px; display: none;">
            @endif
            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah gambar (saat edit).</small>
        </div>
    </div>
</div>
