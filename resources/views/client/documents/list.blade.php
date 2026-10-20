{{-- resources/views/client/documents/list.blade.php --}}
@extends('client.layouts.app')
@section('title','Dokumen Saya')

@push('styles')
<style>
  /* Khusus halaman ini */
  .doc-card .card-body{padding:.9rem 1rem}
  .doc-card .card-footer{padding:.75rem 1rem}
  .doc-title{font-weight:700;font-size:.98rem}
  .doc-meta{font-size:.82rem;color:#6c757d}
  .filter-card .form-label{font-size:.86rem;font-weight:600;margin-bottom:.25rem}
  .filter-card .btn{height:40px}
</style>
@endpush

@section('content')
<!-- Section wrapper: match hero spacing -->
<div class="container-fluid py-5 wow fadeInUp" data-wow-delay="0.1s">
  <div class="container py-5">
    <div class="row g-5 justify-content-center">

      <div class="col-lg-10">
        {{-- Header + Filter (sejajar dan lega) --}}
        <div class="card filter-card mb-4 shadow-sm">
          <div class="card-body">
            <div class="row g-3 align-items-end">
              <div class="col-12 col-md-3">
                <div class="d-flex align-items-start">
                  <img src="{{ asset('assets/images/dokumen.png') }}" alt="icon"
                       class="img-fluid me-3" style="width:56px;height:56px;">
                       
                  <div>
                    <h3 class="mb-1">
                      Dokumen Saya
                      {{-- Badge jumlah total dokumen --}}
                      {{-- @if($items->total() > 0)
                        <span class="badge bg-primary">{{ $items->total() }}</span>
                      @endif --}}
                    </h3>
                    <div class="text-muted small">Daftar dokumen yang siap diunduh</div>
                  </div>
                </div>
              </div>

              <div class="col-sm-4 col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <form id="filterForm" method="get"></form>
                <input type="date" name="date_from" value="{{ $dateFrom }}" form="filterForm" class="form-control">
              </div>

              <div class="col-sm-4 col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" form="filterForm" class="form-control">
              </div>

              <div class="col-sm-4 col-md-3">
                <label class="form-label d-none d-sm-block">&nbsp;</label>
                <div class="d-flex gap-2">
                  <button class="btn btn-primary flex-fill" form="filterForm">Terapkan</button>
                  <a href="{{ route('client.documents.index') }}" class="btn btn-light">Reset</a>
                  <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Kembali</a>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Grid Dokumen --}}
        @if($items->isEmpty())
          <div class="alert alert-secondary mb-0">
            Belum ada file pada rentang tanggal tersebut.
          </div>
        @else
          {{-- Info jumlah surat --}}
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <strong>Jumlah Surat:</strong> {{ $items->total() }}
            </div>
            <div class="text-muted small">
              Menampilkan {{ $items->count() }} surat pada halaman ini
            </div>
          </div>

          <div class="row g-3">
            @foreach ($items as $item)
              <div class="col-12 col-md-6 col-lg-4">
                <div class="card doc-card h-100 shadow-sm">
                  <div class="card-body">
                    <div class="doc-title">{{ e($item->pengajuan->jenis ?? 'Dokumen') }}</div>
                    <div class="doc-meta mt-1">
                      Terakhir diperbarui: {{ optional($item->updated_at)->format('d M Y, H:i') }}
                    </div>
                  </div>
                  <div class="card-footer bg-transparent">
                    <a class="btn btn-sm btn-primary w-100"
                       href="{{ asset($item->file_complete) }}" target="_blank" rel="noopener">
                      Lihat / Unduh
                    </a>
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <div class="mt-3">
            {{ $items->links() }}
          </div>
        @endif
      </div>

    </div>
  </div>
</div>
@endsection
