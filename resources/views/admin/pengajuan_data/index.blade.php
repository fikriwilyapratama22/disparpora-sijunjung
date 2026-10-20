@extends('admin.layouts.app')

@section('content')
<style>
    /* Semua teks di area content ini jadi hitam */
    .card, 
    .card * {
        color: #000 !important;
    }
</style>
    <div class="container-fluid content-inner mt-n5 py-0" style="margin-top: 100px !important;">
        <div class="card-header mb-2 px-3">
            <div class="flex-wrap d-flex justify-content-between align-items-center">
                <div>
                    <div class="header-title">
                        <h2 class="card-title">Pengajuan</h2>
                        <p>List data layanan</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3"></div>

                        <div class="table-responsive">
                            <table id="layananTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        {{-- <th>#</th> --}}
                                        <th class="text-center">Nama pengajuan</th>
                                        <th class="text-center">nama pemohon</th>
                                        <th class="text-center">Template surat</th>
                                        <th class="text-center">File Pemohon</th>
                                        {{-- Kolom-kolom berikut DIHAPUS dari tampilan tabel:
                                             no_hp, nik, jam Mulai, jam Selesai, Tanggal Mulai, Tanggal Selesai, alamat, kegiatan --}}
                                        <th class="text-center">Dibuat</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Upload File Complete</th>
                                        <th class="text-center">Detail</th>  {{-- kolom baru untuk tombol modal --}}
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pengajuanData as $item)
                                        <tr>
                                            {{-- No: diisi oleh DataTables --}}
                                            <td></td>

                                            {{-- <td>{{ $item->pengajuan->id ?? '-' }} --}}
                                            <td class="text-center">{{ $item->pengajuan->jenis ?? '-' }}</td>
                                            <td class="text-center">{{ Str::limit($item->nama ?? '-', 50) }}</td>

                                            <td class="text-center">
                                                @if ($item->status == 'approved')
                                                    <a href="{{ route('surat.download', ['id' => $item->id]) }}"
                                                       class="btn btn-sm btn-success" target="_blank">
                                                        <i class="bi bi-download"></i> Download Surat
                                                    </a>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                @php
                                                    $files = is_string($item->file) ? json_decode($item->file, true) : $item->file;
                                                @endphp
                                                @if (!empty($files) && is_array($files))
                                                    @foreach ($files as $file)
                                                        @if ($file)
                                                            <a href="{{ asset($file) }}" target="_blank" class="btn btn-sm btn-info mb-1">
                                                                <i class="bi bi-file-earmark-pdf"></i> Lihat File
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">Tidak ada file</span>
                                                @endif
                                            </td>

                                            {{-- DIBUAT (pakai data-order ISO agar sorting akurat) --}}
                                            <td class="text-center"
                                                data-order="{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d H:i:s') }}">
                                                {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}
                                            </td>

                                            {{-- STATUS --}}
                                            <td class="text-center">{{ $item->status ?? '-' }}</td>

                                            {{-- UPLOAD FILE COMPLETE --}}
                                            <td class="text-center">
                                                @if ($item->file_complete)
                                                    <a href="{{ asset($item->file_complete) }}" target="_blank" class="btn btn-sm btn-info">
                                                        <i class="bi bi-file-earmark-pdf"></i> Lihat File
                                                    </a>
                                                @elseif ($item->status == 'approved' || $item->status == 'diterima')
                                                    <form action="{{ route('admin.pengajuan.uploadCompleteFile', $item->id) }}"
                                                          method="POST" enctype="multipart/form-data"
                                                          style="display:inline-block; margin-top: 5px;">
                                                        @csrf
                                                        <input type="file" name="file_complete" accept="application/pdf" required
                                                               style="width: 140px; margin-bottom: 5px;">
                                                        <button type="submit" class="btn btn-sm btn-primary">
                                                            <i class="bi bi-upload"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>

                                            {{-- DETAIL (BUTTON MODAL) --}}
                                            @php
                                                $jamMulai   = $item->jam_mulai   ? \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') : '-';
                                                $jamSelesai = $item->jam_selesai ? \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') : '-';
                                            @endphp
                                            <td class="text-center">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-warning btn-detail"
                                                    data-nama_pengajuan="{{ $item->pengajuan->jenis ?? '-' }}"
                                                    data-nama_pemohon="{{ $item->nama ?? '-' }}"
                                                    data-nik="{{ $item->nik ?? '-' }}"
                                                    data-no_hp="{{ $item->no_hp ?? '-' }}"
                                                    data-jam_mulai="{{ $jamMulai }}"        
                                                    data-jam_selesai="{{ $jamSelesai }}"    
                                                    data-tgl_mulai="{{ $item->tgl_mulai ?? '-' }}"
                                                    data-tgl_selesai="{{ $item->tgl_selesai ?? '-' }}"
                                                    data-alamat="{{ $item->alamat ?? '-' }}"
                                                    data-dibuat="{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}"
                                                    data-kegiatan="{{ $item->kegiatan ?? '-' }}"
                                                     data-barang="{{ $item->barang ?? '-' }}"
                                                    data-status="{{ $item->status ?? '-' }}"
                                                >
                                                    <i class="bi bi-eye"></i> Lihat
                                                </button>
                                            </td>

                                            {{-- AKSI --}}
                                            <td class="text-center">
                                                @if ($item->status !== 'approved' && $item->status !== 'diterima')
                                                    <form action="{{ route('admin.pengajuan.approve', $item->id) }}"
                                                          method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-success me-1">
                                                            <i class="bi bi-check2-circle"></i> Approve
                                                        </button>
                                                    </form>
                                                @endif

                                                @if ($item->file_complete == null)
                                                    <form action="{{ route('admin.pengajuan.reject', $item->id) }}"
                                                          method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-x-circle"></i> Reject
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> <!-- /.table-responsive -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL --}}
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="detailModalLabel">Detail Pengajuan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered mb-0">
              <tbody>
                <tr><th style="width:30%">Nama Pengajuan</th><td id="detail_nama_pengajuan"></td></tr>
                <tr><th>Nama Pemohon</th><td id="detail_nama_pemohon"></td></tr>
                <tr><th>NIK</th><td id="detail_nik"></td></tr>
                <tr><th>No. HP</th><td id="detail_no_hp"></td></tr>
                <tr><th>Jam Mulai</th><td id="detail_jam_mulai"></td></tr>
                <tr><th>Jam Selesai</th><td id="detail_jam_selesai"></td></tr>
                <tr><th>Tanggal Mulai</th><td id="detail_tgl_mulai"></td></tr>
                <tr><th>Tanggal Selesai</th><td id="detail_tgl_selesai"></td></tr>
                <tr><th>Alamat</th><td id="detail_alamat"></td></tr>
                <tr><th>Dibuat</th><td id="detail_dibuat"></td></tr>
                <tr><th>Kegiatan</th><td id="detail_kegiatan"></td></tr>
                 <tr><th>Barang</th><td id="detail_barang"></td></tr>
                <tr><th>Status</th><td id="detail_status"></td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <script>
    $(function () {
      // Hapus instance lama jika pernah di-init dua kali
      if ($.fn.DataTable.isDataTable('#layananTable')) {
        $('#layananTable').DataTable().destroy();
      }

      const t = $('#layananTable').DataTable({
        order: [[5, 'desc']],             // urut berdasarkan kolom "Dibuat" (index 5 setelah penghapusan kolom)
        lengthMenu: [[5, 15, 25, 100, -1],[5, 15, 25, 100, 'Semua']],
        pageLength: 25,
        stateSave: false,
        orderMulti: false,
        columnDefs: [
          {
            targets: 0,                    // kolom "No"
            orderable: false,
            searchable: false
          }
        ],
        // Nomor urut SELALU 1..n dari baris teratas halaman saat ini
        rowCallback: function (row, data, displayIndex, displayIndexFull) {
          const api  = this.api();
          const info = api.page.info();
          $('td:eq(0)', row).html(info.start + displayIndex + 1);
        }
      });

      // Pastikan re-number setiap kali sort/paging/search
      t.on('draw.dt', function () {
        const api  = t;
        const info = api.page.info();
        api.column(0, {page:'current'}).nodes().each(function (cell, i) {
          cell.innerHTML = info.start + i + 1;
        });
      });

      // Handler tombol Detail -> isi dan tampilkan modal
      $(document).on('click', '.btn-detail', function() {
        $('#detail_nama_pengajuan').text($(this).data('nama_pengajuan'));
        $('#detail_nama_pemohon').text($(this).data('nama_pemohon'));
        $('#detail_nik').text($(this).data('nik'));
        $('#detail_no_hp').text($(this).data('no_hp'));
        $('#detail_jam_mulai').text($(this).data('jam_mulai'));
        $('#detail_jam_selesai').text($(this).data('jam_selesai'));
        $('#detail_tgl_mulai').text($(this).data('tgl_mulai'));
        $('#detail_tgl_selesai').text($(this).data('tgl_selesai'));
        $('#detail_alamat').text($(this).data('alamat'));
        $('#detail_dibuat').text($(this).data('dibuat'));
        $('#detail_kegiatan').text($(this).data('kegiatan'));
        $('#detail_barang').text($(this).data('barang'));
        $('#detail_status').text($(this).data('status'));

        // Bootstrap 5: modal API
        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        modal.show();
      });
    });
    </script>
@endsection
