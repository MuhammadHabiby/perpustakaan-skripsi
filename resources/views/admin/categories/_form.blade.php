@csrf
<div class="mb-3">
    <label for="name" class="form-label">Nama Kategori</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
        value="{{ old('name', $category->name ?? '') }}" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="description" class="form-label">Deskripsi (Opsional)</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
        rows="3">{{ old('description', $category->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="d-flex justify-content-end">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary me-2">Batal</a>
    <button type="submit" class="btn btn-primary">Simpan</button>
</div>
