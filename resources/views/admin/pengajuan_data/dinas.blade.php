@extends('admin.layouts.app')

@section('content')
<style>
    /* Semua teks di area content ini jadi hitam */
    .card, 
    .card * {
        color: #000 !important;
    }
</style>
@php
    use Carbon\Carbon;

    // ----- Siapkan data untuk filter tahun -----
    $years = collect($pengajuanData)
        ->map(fn($i) => Carbon::parse($i->created_at)->year)
        ->unique()->sort()->values();

    if ($years->isEmpty()) {
        $years = collect([Carbon::now()->year]);
    }

    // ----- Siapkan data mentah untuk JS (jenis & created_at) -----
    $rawForJs = collect($pengajuanData)->map(function($i){
        return [
            'jenis' => optional($i->pengajuan)->jenis ?? 'Tidak diketahui',
            'created_at' => \Carbon\Carbon::parse($i->created_at)->toDateString(), // YYYY-MM-DD
        ];
    })->values();

    $currentMonth = Carbon::now()->month; // 1..12
    $currentYear  = Carbon::now()->year;

    // Nama bulan untuk UI
    $bulan = [
        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
        7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
    ];
@endphp

<div class="container-fluid content-inner mt-n5 py-0" style="margin-top: 100px !important;">

    {{-- ====== KARTU DIAGRAM PIE + FILTER BULAN/TAHUN ====== --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header px-3 py-2 d-flex flex-wrap gap-2 justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Distribusi Pengajuan per Jenis Layanan</h5>
                        <small class="text-muted">Filter berdasarkan bulan & tahun</small>
                    </div>
                    <div class="d-flex gap-2">
                        <select id="filterMonth" class="form-select form-select-sm" style="min-width: 160px;">
                            @foreach($bulan as $num => $name)
                                <option value="{{ $num }}" @selected($num == $currentMonth)>{{ $name }}</option>
                            @endforeach
                        </select>
                        <select id="filterYear" class="form-select form-select-sm" style="min-width: 120px;">
                            @foreach($years as $y)
                                <option value="{{ $y }}" @selected($y == $currentYear)>{{ $y }}</option>
                            @endforeach
                        </select>
                        <button id="btnApplyFilter" class="btn btn-sm btn-primary">Terapkan</button>
                    </div>
                </div>
                <div class="card-body">
                    <div style="max-width: 450px; margin:auto;">
                        <canvas id="pieLayanan" height="240"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- ====== END PIE CHART ====== --}}

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

    {{-- ========= TABEL PENGAJUAN UTAMA ========= --}}
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3"></div>
                    <div class="table-responsive">
                        <table id="layananTableUtama" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th class="text-center">Nama pengajuan</th>
                                    <th class="text-center">nama pemohon</th>
                                    <th class="text-center">File</th>
                                    {{-- DIHILANGKAN dari tabel: no_hp, nik, jam mulai, jam selesai, tgl mulai, tgl selesai, alamat, kegiatan --}}
                                    <th class="text-center">Dibuat</th>
                                    <th class="text-center">Di ajukan Kabid</th>
                                    <th class="text-center">Selesai</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Detail</th> {{-- KOLUM BARU --}}
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pengajuanData as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->pengajuan->jenis ?? '-' }}</td>
                                        <td class="text-center">{{ Str::limit($item->nama ?? '-', 50) }}</td>

                                        <td>
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

                                        <td class="text-center">{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}</td>

                                        <td class="text-center">
                                            @if ($item->status == 'approved' || $item->status == 'diterima')
                                                <a href="{{ route('surat.download', ['id' => $item->id]) }}" class="btn btn-sm btn-success" target="_blank">
                                                    <i class="bi bi-download"></i> Download Surat
                                                </a>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if ($item->file_complete)
                                                <a href="{{ asset($item->file_complete) }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="bi bi-file-earmark-pdf"></i> Lihat File
                                                </a>
                                            @endif
                                        </td>

                                        <td>{{ $item->status ?? '-' }}</td>

                                        {{-- Tombol Detail: data lengkap dikirim via data-* --}}
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
                                                data-jam_mulai="{{ $jamMulai }}"      {{-- <- Sudah H:i --}}
                                                data-jam_selesai="{{ $jamSelesai }}"  {{-- <- Sudah H:i --}}
                                                data-tgl_mulai="{{ $item->tgl_mulai ?? '-' }}"
                                                data-tgl_selesai="{{ $item->tgl_selesai ?? '-' }}"
                                                data-alamat="{{ $item->alamat ?? '-' }}"
                                                data-dibuat="{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}"
                                                data-kegiatan="{{ $item->kegiatan ?? '-' }}"
                                                data-status="{{ $item->status ?? '-' }}"
                                            >
                                                <i class="bi bi-eye"></i> Lihat
                                            </button>
                                        </td>

                                        <td class="text-center">
                                            @if ($item->status !== 'approved')
                                                <form action="{{ route('admin.pengajuan.dinas.approve', $item->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success me-1">
                                                        <i class="bi bi-check2-circle"></i> Approve
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($item->status !== 'approved')
                                                <form action="{{ route('admin.pengajuan.reject', $item->id) }}" method="POST" style="display:inline-block;">
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
                    </div> {{-- /table-responsive --}}
                </div>
            </div>
        </div>
    </div>
    {{-- ========= END TABEL PENGAJUAN UTAMA ========= --}}

    <h1>Pengajuan Selesai</h1>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">

                    {{-- === FILTER & CETAK LAPORAN === --}}
                    <div class="d-flex flex-wrap gap-2 align-items-end mb-3">
                        <div>
                            <label class="form-label mb-1">Bulan</label>
                            <select id="selesaiFilterMonth" class="form-select form-select-sm" style="min-width: 160px;">
                                @foreach($bulan as $num => $name)
                                    <option value="{{ $num }}" @selected($num == $currentMonth)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label mb-1">Tahun</label>
                            <select id="selesaiFilterYear" class="form-select form-select-sm" style="min-width: 120px;">
                                @foreach($years as $y)
                                    <option value="{{ $y }}" @selected($y == $currentYear)>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button id="btnFilterSelesai" class="btn btn-sm btn-primary">Terapkan</button>

                        {{-- Tombol Cetak Laporan (mengarah ke route laporan) --}}
                        <a id="btnCetakLaporan"
                           href="{{ route('admin.pengajuan.laporan', ['month' => $currentMonth, 'year' => $currentYear]) }}"
                           target="_blank"
                           class="btn btn-sm btn-success">
                            <i class="bi bi-printer"></i> Cetak Laporan
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table id="layananTableSelesai" class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Nama pengajuan</th>
                                    <th class="text-center">nama pemohon</th>
                                    <th class="text-center">Selesai</th>
                                    <th class="text-center">Tanggal Dibuat</th> {{-- kolom baru --}}
                                    <th class="text-center">Status</th>
                                    <th class="text-center d-none">TanggalDasarFilter</th> {{-- hidden untuk filter --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pengajuanDatas as $item)
                                    <tr>
                                        <td class="text-center">{{ $item->pengajuan->jenis ?? '-' }}</td>
                                        <td class="text-center">{{ Str::limit($item->nama ?? '-', 50) }}</td>
                                        <td class="text-center">
                                            @if ($item->file_complete)
                                                <a href="{{ asset($item->file_complete) }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="bi bi-file-earmark-pdf"></i> Lihat File
                                                </a>
                                            @endif
                                        </td>
                                        {{-- kolom tanggal dibuat (tampil) --}}
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}
                                        </td>
                                        <td class="text-center">{{ $item->status ?? '-' }}</td>
                                        {{-- kolom tanggal untuk filter (hidden, format YYYY-MM-DD) --}}
                                        <td class="text-center d-none">
                                            {{ \Carbon\Carbon::parse($item->created_at)->toDateString() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> {{-- /table-responsive --}}
                </div>
            </div>
        </div>
    </div>

    {{-- ========= DAFTAR LAYANAN ========= --}}
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    {{-- <div class="mb-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPengajuanModal">
                            <i class="bi bi-plus-lg"></i> Tambah Pengajuan
                        </button>
                    </div> --}}

                    <div class="table-responsive">
                        <table id="layananTableMaster" class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Jenis Layanan</th>
                                    <th class="text-center">Kode Surat</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pengajuan as $item)
                                    <tr>
                                        <td class="text-center">{{ $item->jenis ?? '-' }}</td>
                                        <td class="text-center">{{ $item->kode ?? '-' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.pengajuan.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i> edit
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> {{-- /table-responsive --}}
                </div>
            </div>
        </div>
    </div>
    {{-- ========= END DAFTAR LAYANAN ========= --}}

</div> {{-- /container --}}

{{-- ======================= SCRIPTS ======================= --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
    // ---------------- Data mentah untuk chart ----------------
    const RAW_DATA = @json($rawForJs);
    const BULAN_NAMA = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

    function aggregateByMonthYear(month1to12, year4d){
        const labelsSet = new Set();
        const counter = {};
        RAW_DATA.forEach(row => {
            const d = new Date(row.created_at);
            const m = d.getMonth() + 1;
            const y = d.getFullYear();
            if (m === Number(month1to12) && y === Number(year4d)) {
                const jenis = row.jenis || 'Tidak diketahui';
                labelsSet.add(jenis);
                counter[jenis] = (counter[jenis] || 0) + 1;
            }
        });
        const labels = Array.from(labelsSet);
        const counts = labels.map(l => counter[l] ?? 0);
        return { labels, counts };
    }

    function genColors(n){
        const colors = [], borders = [];
        for (let i=0;i<n;i++){
            const hue = Math.floor((360 / (n || 1)) * i);
            colors.push(`hsl(${hue} 70% 65%)`);
            borders.push(`hsl(${hue} 70% 45%)`);
        }
        return { colors, borders };
    }

    const ctx = document.getElementById('pieLayanan');
    let chartPie = null;

    function renderChart(monthVal, yearVal){
        const { labels, counts } = aggregateByMonthYear(monthVal, yearVal);
        const { colors, borders } = genColors(labels.length);

        const data = {
            labels: labels,
            datasets: [{ data: counts, backgroundColor: colors, borderColor: borders, borderWidth: 1 }]
        };
        const options = {
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            const total = ctx.dataset.data.reduce((a,b)=>a+b,0);
                            const v = ctx.parsed || 0;
                            const pct = total ? ((v/total)*100).toFixed(1) : 0;
                            return `${ctx.label}: ${v} (${pct}%)`;
                        }
                    }
                },
                title: {
                    display: true,
                    text: `Distribusi Bulan ${BULAN_NAMA[monthVal]} ${yearVal}`
                }
            }
        };

        if (chartPie) {
            chartPie.data = data;
            chartPie.options = options;
            chartPie.update();
        } else {
            chartPie = new Chart(ctx, { type: 'pie', data, options });
        }
    }

    const selMonth = document.getElementById('filterMonth');
    const selYear  = document.getElementById('filterYear');
    document.getElementById('btnApplyFilter').addEventListener('click', () => {
        renderChart(selMonth.value, selYear.value);
    });
    renderChart({{ $currentMonth }}, {{ $currentYear }});

    // ================= DataTables: Tabel Utama =================
    $('#layananTableUtama').DataTable({
        columnDefs: [
            {
                targets: 0,
                visible: false,
                render: function (data, type, row) {
                    // Setelah pengurangan kolom, kolom "Status" sekarang berada di index 7
                    return row[7] === 'diterima' ? 0 : 1;
                }
            }
        ],
        order: [[0, 'asc']],
        lengthMenu: [[5, 15, 25, 100, -1],[5, 15, 25, 100, 'Semua']],
        pageLength: 5,
    });

    // ======== DataTable Pengajuan Selesai (filter approved + bulan/tahun) ========
    // Custom filter untuk DataTables
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        if (settings.nTable.id !== 'layananTableSelesai') return true;

        // Index kolom setelah penambahan "Tanggal Dibuat":
        // 0: Nama pengajuan
        // 1: nama pemohon
        // 2: Selesai (link file)
        // 3: Tanggal Dibuat (tampil)
        // 4: Status
        // 5: TanggalDasarFilter (hidden, YYYY-MM-DD)
        const status = (data[4] || '').toLowerCase().trim();
        const isApproved = (status === 'approved' || status === 'diterima');

        const tanggalStr = data[5]; // kolom hidden (YYYY-MM-DD)
        if (!tanggalStr) return false;

        const d = new Date(tanggalStr);
        const rowMonth = d.getMonth() + 1;
        const rowYear  = d.getFullYear();

        const selMonth = Number(document.getElementById('selesaiFilterMonth').value);
        const selYear  = Number(document.getElementById('selesaiFilterYear').value);

        const matchMonthYear = (rowMonth === selMonth && rowYear === selYear);

        return isApproved && matchMonthYear;
    });

    const dtSelesai = $('#layananTableSelesai').DataTable({
    lengthMenu: [[5, 15, 25, 100, -1],[5, 15, 25, 100, 'Semua']],
    pageLength: 5,
    order: [[3, 'desc']], // urutkan berdasarkan kolom "Tanggal Dibuat" (index 3)
    columnDefs: [
        { targets: 5, visible: false, searchable: true } // kolom tanggal hidden
    ]
});


    // Trigger filter pertama kali
    dtSelesai.draw();

    // Tombol Terapkan untuk Pengajuan Selesai
    $('#btnFilterSelesai').on('click', function() {
        dtSelesai.draw();

        // Sinkronkan URL Cetak Laporan dengan filter aktif
        const m = document.getElementById('selesaiFilterMonth').value;
        const y = document.getElementById('selesaiFilterYear').value;

        // Route dasar ke laporan (pastikan route ini ada di web.php)
        const baseUrl = "{{ route('admin.pengajuan.laporan') }}";
        const url = new URL(baseUrl, window.location.origin);
        url.searchParams.set('month', m);
        url.searchParams.set('year', y);
        document.getElementById('btnCetakLaporan').setAttribute('href', url.toString());
    });

    // ================= DataTables: Master =================
    $('#layananTableMaster').DataTable({
        lengthMenu: [[5, 15, 25, 100, -1],[5, 15, 25, 100, 'Semua']],
        pageLength: 5,
    });

    // Modal Detail
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
        $('#detail_status').text($(this).data('status'));

        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        modal.show();
    });

    // (Opsional) SweetAlert removeData jika Anda gunakan
    window.removeData = function(id){
        Swal.fire({
            title: "Apakah kamu yakin ingin menghapus?",
            showDenyButton: true,
            confirmButtonText: "Ya",
            denyButtonText: "Tidak",
            confirmButtonColor: '#1AA053',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/admin/layanan/' + id + '/delete';
            }
        });
    };
</script>

{{-- ======================= MODAL DETAIL ======================= --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail Pengajuan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <tr><th style="width:30%">Nama Pengajuan</th><td id="detail_nama_pengajuan"></td></tr>
          <tr><th>Nama Pemohon</th><td id="detail_nama_pemohon"></td></tr>
          <tr><th>NIK</th><td id="detail_nik"></td></tr>
          <tr><th>No HP</th><td id="detail_no_hp"></td></tr>
          <tr><th>Jam Mulai</th><td id="detail_jam_mulai"></td></tr>
          <tr><th>Jam Selesai</th><td id="detail_jam_selesai"></td></tr>
          <tr><th>Tanggal Mulai</th><td id="detail_tgl_mulai"></td></tr>
          <tr><th>Tanggal Selesai</th><td id="detail_tgl_selesai"></td></tr>
          <tr><th>Alamat</th><td id="detail_alamat"></td></tr>
          <tr><th>Dibuat</th><td id="detail_dibuat"></td></tr>
          <tr><th>Kegiatan</th><td id="detail_kegiatan"></td></tr>
          <tr><th>Status</th><td id="detail_status"></td></tr>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- ======================= MODAL TAMBAH (TIDAK DIUBAH) ======================= --}}
<div class="modal fade" id="createPengajuanModal" tabindex="-1" aria-labelledby="createPengajuanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.pengajuan.pengajuan') }}" method="POST">
            @csrf
            <div class="modal-content">
                {{-- <div class="modal-header">
                    <h5 class="modal-title" id="createPengajuanModalLabel">Buat Pengajuan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div> --}}
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="layanan_id" class="form-label">Jenis Layanan</label>
                        <select name="layanan_id" id="layanan_id" class="form-control" required>
                            <option value="">-- Pilih Layanan --</option>
                            @foreach ($layanans as $item)
                                <option value="{{ $item->id }}">{{ $item->layanan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="jenis" class="form-label">Jenis Pengajuan</label>
                        <input type="text" class="form-control" id="jenis" name="jenis" required>
                    </div>

                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode Surat</label>
                        <input type="text" class="form-control" id="kode" name="kode" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
