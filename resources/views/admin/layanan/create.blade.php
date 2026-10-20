@extends('admin.layouts.app')

@section('content')
<div class="container mt-5">
    <br>
    <h3>Tambah Layanan</h3>

    <form action="{{ url('/admin/layanan') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama Layanan</label>
            <input type="text" name="layanan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Gambar</label>
            <input type="file" name="gambar" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ url('/admin/layanan') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
