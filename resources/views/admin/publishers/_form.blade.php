@csrf
<div class="mb-3">
    <label for="name" class="form-label">Nama Penerbit</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
        value="{{ old('name', $publisher->name ?? '') }}" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="address" class="form-label">Alamat (Opsional)</label>
    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $publisher->address ?? '') }}</textarea>
    @error('address')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="d-flex justify-content-end">
    <a href="{{ route('admin.publishers.index') }}" class="btn btn-secondary me-2">Batal</a>
    <button type="submit" class="btn btn-primary">Simpan</button>
</div>
