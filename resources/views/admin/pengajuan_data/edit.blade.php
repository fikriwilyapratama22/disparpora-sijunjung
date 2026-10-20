@extends('admin.layouts.app')

@section('content')
<div class="container-fluid content-inner mt-n5 py-0" style="margin-top: 100px !important;">

    {{-- Header / Breadcrumb (opsional) --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h3 class="mb-0">Edit Data Pengajuan</h3>
        {{-- <a href="{{ url('admin/pengajuan/detail') }}" class="btn btn-outline-secondary btn-sm">
            &larr; Kembali
        </a> --}}
    </div>

    {{-- Notifikasi sederhana --}}
    @if(session('success'))
        <div class="alert alert-success py-2 px-3 mb-3 alert-auto-hide">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger py-2 px-3 mb-3 alert-auto-hide">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-warning py-2 px-3 mb-3">
            <strong>Periksa kembali isian Anda:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('admin.pengajuan.update', $pengajuan->id) }}" method="POST" novalidate>
                        @csrf
                        @method('PUT')

                        {{-- Jenis Layanan --}}
                        <div class="mb-3">
                            <label for="jenis" class="form-label fw-semibold">Jenis Layanan</label>
                            <input
                                type="text"
                                name="jenis"
                                id="jenis"
                                class="form-control @error('jenis') is-invalid @enderror"
                                value="{{ old('jenis', $pengajuan->jenis) }}"
                                placeholder="Contoh: Rekomendasi Kegiatan"
                                required
                                autofocus
                            >
                            @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="form-text">Tulis nama/jenis layanan yang sesuai.</div>
                            @enderror
                        </div>

                        {{-- Kode Surat --}}
                        <div class="mb-4">
                            <label for="kode" class="form-label fw-semibold">Kode Surat</label>
                            <input
                                type="text"
                                name="kode"
                                id="kode"
                                class="form-control @error('kode') is-invalid @enderror"
                                value="{{ old('kode', $pengajuan->kode) }}"
                                placeholder="Contoh: 070/DPPO/IX/2025"
                                required
                            >
                            @error('kode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="form-text"></div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
                            </button>
                            <a href="{{ url('admin/pengajuan/detail') }}" class="btn btn-secondary">
                                Batal
                            </a>
                        </div>

                    </form>
                </div>
            </div>

            {{-- Info kecil (opsional) --}}
            <p class="text-muted small mt-3 mb-0">
                Pastikan data yang diubah sudah benar sebelum menyimpan.
            </p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Sentuhan kecil agar nyaman dilihat */
    .card .form-label { margin-bottom: 6px; }
    .alert-auto-hide { transition: opacity .3s ease; }
</style>
@endpush

@push('scripts')
<script>
    // Auto-hide alert sederhana (3 detik)
    setTimeout(() => {
        document.querySelectorAll('.alert-auto-hide').forEach(el => {
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 300);
        });
    }, 3000);
</script>
@endpush
