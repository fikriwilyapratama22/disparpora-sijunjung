@extends('admin.layouts.app')

@section('title', 'Edit Layanan')

@section('content')
<div class="container-lg py-4"> {{-- Container lebih lebar --}}
    <h3 class="mb-4">Edit Layanan</h3>

    <form action="{{ url('/admin/layanan/' . $layanan->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Informasi Umum Layanan --}}
        <div class="mb-4">
            <label for="layanan" class="form-label fs-5">Nama Layanan <span class="text-danger">*</span></label>
            <input type="text" id="layanan" name="layanan"
                   value="{{ old('layanan', $layanan->layanan) }}"
                   class="form-control form-control-lg"
                   required
                   placeholder="Masukkan nama layanan...">
        </div>

        <div class="mb-4">
            <label for="deskripsi" class="form-label fs-5">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi"
                      class="form-control form-control-lg"
                      rows="5"
                      placeholder="Tuliskan deskripsi singkat layanan...">{{ old('deskripsi', $layanan->deskripsi) }}</textarea>
        </div>

        <div class="mb-4">
            <label for="gambar" class="form-label fs-5">Gambar Layanan</label>
            <input type="file" id="gambar" name="gambar" class="form-control form-control-lg">
            @if ($layanan->gambar)
                <div class="mt-3">
                    <img src="{{ asset('layanan/' . $layanan->gambar) }}" alt="{{ $layanan->layanan }}" class="img-thumbnail" width="200">
                </div>
            @endif
        </div>

        {{-- Konten Detail Layanan --}}
        <hr class="my-4">
        <h4 class="mb-3">Konten Detail Layanan</h4>

        {{-- Konten 1 --}}
        <div class="mb-3">
            <label for="judul1" class="form-label fs-5">Judul Konten 1</label>
            <input type="text" id="judul1" name="judul1"
                   class="form-control form-control-lg"
                   value="{{ old('judul1', $layanan->content->judul1 ?? '') }}"
                   placeholder="Masukkan judul konten 1...">
        </div>
        <div class="mb-4">
            <label for="isi1" class="form-label fs-5">Isi Konten 1</label>
            <textarea id="isi1" name="isi1"
                      class="form-control form-control-lg"
                      rows="4"
                      placeholder="Isi detail konten 1...">{{ old('isi1', $layanan->content->isi1 ?? '') }}</textarea>
        </div>

        {{-- Konten 2 --}}
        <div class="mb-3">
            <label for="judul2" class="form-label fs-5">Judul Konten 2</label>
            <input type="text" id="judul2" name="judul2"
                   class="form-control form-control-lg"
                   value="{{ old('judul2', $layanan->content->judul2 ?? '') }}"
                   placeholder="Masukkan judul konten 2...">
        </div>
        <div class="mb-4">
            <label for="isi2" class="form-label fs-5">Isi Konten 2</label>
            <textarea id="isi2" name="isi2"
                      class="form-control form-control-lg"
                      rows="4"
                      placeholder="Isi detail konten 2...">{{ old('isi2', $layanan->content->isi2 ?? '') }}</textarea>
        </div>

        {{-- Konten 3 --}}
        <div class="mb-3">
            <label for="judul3" class="form-label fs-5">Judul Konten 3</label>
            <input type="text" id="judul3" name="judul3"
                   class="form-control form-control-lg"
                   value="{{ old('judul3', $layanan->content->judul3 ?? '') }}"
                   placeholder="Masukkan judul konten 3...">
        </div>
        <div class="mb-4">
            <label for="isi3" class="form-label fs-5">Isi Konten 3</label>
            <textarea id="isi3" name="isi3"
                      class="form-control form-control-lg"
                      rows="4"
                      placeholder="Isi detail konten 3...">{{ old('isi3', $layanan->content->isi3 ?? '') }}</textarea>
        </div>

        {{-- Tombol Aksi --}}
        <div class="d-flex justify-content-between mt-5">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-save"></i> Simpan Perubahan
            </button>
            <a href="{{ url('/admin/layanan') }}" class="btn btn-secondary btn-lg">
                <i class="bi bi-arrow-left-circle"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection
