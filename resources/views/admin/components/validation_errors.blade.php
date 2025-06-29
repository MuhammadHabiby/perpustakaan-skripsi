@if ($errors->default->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Gagal memproses data!</strong> Mohon periksa kembali isian Anda:
        <ul>
            @foreach ($errors->default->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
