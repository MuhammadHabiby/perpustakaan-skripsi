@csrf
<div class="mb-3">
    <label for="name" class="form-label">Nama Pengarang</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
        value="{{ old('name', $author->name ?? '') }}" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="bio" class="form-label">Bio (Opsional)</label>
    <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="3">{{ old('bio', $author->bio ?? '') }}</textarea>
    @error('bio')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="d-flex justify-content-end">
    <a href="{{ route('admin.authors.index') }}" class="btn btn-secondary me-2">Batal</a>
    <button type="submit" class="btn btn-primary">Simpan</button>
</div>
