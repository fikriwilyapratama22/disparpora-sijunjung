    
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Pengajuan Disetujui - {{ $monthName }} {{ $year }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --border: #000;
            --muted: #555;
            --bg-th: #f2f2f2;
            --font: system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
        }
        * { box-sizing: border-box; }
        body { font-family: var(--font); color:#000; margin:0; padding:16px; }
        .header { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:16px; }
        .brand { display:flex; align-items:center; gap:12px; }
        .brand h1 { font-size:18px; line-height:1.2; margin:0; }
        .meta  { font-size:14px; text-align:right; }
        .divider { height:2px; background:#000; margin:8px 0 16px; }

        .info { font-size:13px; color:var(--muted); margin-bottom:12px; }

        table { width:100%; border-collapse:collapse; }
        th, td { border:1px solid var(--border); padding:8px; font-size:13px; }
        th { background:var(--bg-th); text-align:center; }
        td.text-center { text-align:center; }
        td.text-right { text-align:right; }
        td.nowrap { white-space:nowrap; }

        .section-title { margin:16px 0 8px; font-size:16px; }
        .totals { margin-top:4px; }

        .footer { margin-top:28px; display:flex; justify-content:space-between; gap:16px; font-size:13px; }
        .sig { width:280px; text-align:center; }
        .sig .space { height:72px; }
        .small { font-size:12px; color:var(--muted); }

        @media print {
            @page { size: A4 portrait; margin: 14mm; }
            body { padding:0; }
            .no-print { display:none !important; }
            a[href]:after { content: ""; }
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="brand">
            {{-- <img src="{{ asset('path/ke/logo.png') }}" alt="Logo" style="height:40px;"> --}}
            <h1>Laporan Pengajuan Disetujui</h1>
        </div>
        <div class="meta">
            Periode: <strong>{{ $monthName }} {{ $year }}</strong><br>
            Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}
        </div>
    </div>
    <div class="divider"></div>

    @if($rekapPerJenis->count())
        <div class="section-title">Ringkasan per Jenis</div>
        <table>
            <thead>
                <tr>
                    <th style="width:60%;">Jenis Layanan</th>
                    <th style="width:40%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekapPerJenis as $jenis => $jumlah)
                    <tr>
                        <td>{{ $jenis }}</td>
                        <td class="text-center">{{ $jumlah }}</td>
                    </tr>
                @endforeach
                <tr class="totals">
                    <th>Total</th>
                    <th class="text-center">{{ $rekapPerJenis->sum() }}</th>
                </tr>
            </tbody>
        </table>
    @else
        <p class="info">Tidak ada ringkasan jenis layanan pada periode ini.</p>
    @endif

    <div class="section-title">Detail Pengajuan</div>
    <table>
        <thead>
            <tr>
                <th style="width:40px;">No</th>
                <th>Jenis Layanan</th>
                <th>Nama Pemohon</th>
                <th style="width:120px;">Status</th>
                <th style="width:140px;">Tgl. Dibuat</th> {{-- SELALU tampilkan tanggal dibuat --}}
            </tr>
        </thead>
        <tbody>
            @forelse($records as $i => $r)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ optional($r->pengajuan)->jenis ?? '-' }}</td>
                    <td>{{ $r->nama ?? '-' }}</td>
                    <td class="text-center" style="text-transform:capitalize;">{{ $r->status ?? '-' }}</td>
                    <td class="text-center nowrap">
                        {{ $r->created_at ? \Carbon\Carbon::parse($r->created_at)->translatedFormat('d F Y') : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="small">
            Sumber data: Sistem Pengajuan<br>
            Catatan: Laporan ini hanya memuat pengajuan berstatus <em>diterima/approved</em> pada periode yang dipilih.
        </div>
        <div class="sig">
            <div>Tanggal: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
            <div class="space"></div>
            <div><strong>(________________________)</strong></div>
            <div class="small">Tanda Tangan & Nama Terang</div>
        </div>
    </div>

    <div class="no-print" style="margin-top:16px;">
        <button onclick="window.print()">Cetak / Print</button>
    </div>

</body>
</html>
