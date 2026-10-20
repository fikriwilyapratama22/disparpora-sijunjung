<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Dinas</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            margin: 2cm;
        }

        .kop {
            text-align: center;
            border-bottom: 3px double black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .kop h2 {
            margin: 0;
            font-size: 14pt;
        }

        .kop h3 {
            margin: 0;
            font-size: 13pt;
        }

        .kop p {
            margin: 2px;
            font-size: 11pt;
        }

        .header-surat td {
            padding: 2px;
            vertical-align: top;
        }

        .isi {
            margin-top: 10px;
        }

        .ttd {
            margin-top: 60px;
            width: 100%;
            text-align: right;
        }

        .ttd p {
            margin: 2px;
        }

        .footer {
            margin-top: 30px;
        }
    </style>
</head>

<body>

    <div class="kop">
        <img src="{{ public_path('assets/images/sijunjung.png') }}" alt="Logo" width="80"
            style="float: left; margin-right: 10px;">
        <h2>PEMERINTAH KABUPATEN SIJUNJUNG</h2>
        <h3>DINAS PARIWISATA PEMUDA DAN OLAHRAGA</h3>
        <p>Alamat: Gedung Bersama Lantai III Komplek Pasar Inpres Muaro</p>
        <p>Telp (0754) 20531 | Muaro Sijunjung, 27511</p>
    </div>
    <p style="text-align: right;margin-right: 10px;">Muaro Sijunjung,
        {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
    <table class="header-surat">
        <tr>

            <td style="width:10%;">Nomor</td>
            <td style="width: 80%;">: {{ $pengajuan->kode }}/{{ $nomorSurat }}/Sekre/Parpora-{{ \Carbon\Carbon::now()->translatedFormat('Y') }}</td>

        </tr>
        </tr>
        <tr>
            <td>Sifat</td>
            <td>: Penting</td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>: -</td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>: {{ $pengajuan->jenis }}</td>
        </tr>
    </table>

    <div class="isi">
        <p>Yth.{{ $pengajuanData->nama }}</p>
        <p>di Tempat</p>

        <p style="margin-top: 20px; text-indent: 20px; text-align: justify;">
    Menindak lanjuti permohonan Saudara perihal {{ $pengajuan->jenis }} pada:
</p>

<table style="margin-left: 60px; text-align: justify;">
    <tr>
        <td style="width: 70px;">Hari</td>
        <td>: {{ \Carbon\Carbon::parse($pengajuanData->tgl_mulai)->translatedFormat('l, d F Y') }} -
              {{ \Carbon\Carbon::parse($pengajuanData->tgl_selesai)->translatedFormat('l, d F Y') }}</td>
    </tr>
    <tr>
        <td>Jam</td>
        <td>: {{ $pengajuanData->jam_mulai }} - {{ $pengajuanData->jam_selesai }}</td>
    </tr>
    <tr>
        <td>Tempat</td>
        <td>: {{ $pengajuanData->tempat }}</td>
    </tr>
    <tr>
        <td>Peminjaman</td>
        <td>: {{ $pengajuanData->barang }}</td>
    </tr>
</table>

<p style="text-indent: 20px; text-align: justify;">
    Dengan keluarnya surat ini kami menyatakan bahwa permohonan Saudara
    {{ $pengajuanData->nama }} atas permohonan {{ $pengajuan->jenis }} ke Dinas Pariwisata Pemuda dan
    Olahraga. Maka bersamaan dengan keluarnya surat ini kami menyatakan bersedia memenuhi permintaan saudara.
</p>

<p style="text-indent: 20px; text-align: justify;">
    Demikian kami sampaikan, atas perhatiannya kami ucapkan terima kasih.
</p>

    </div>

    <div class="ttd">
        <p>KEPALA DINAS PARIWISATA PEMUDA</p>
        <p style="margin-right: 80px;">DAN OLAHRAGA</p><br><br><br>
        <p style="margin-right: 80px;"><strong>AFRINELDI, SH</strong></p>
        <p style="margin-right: 60px;">NIP. 19720407 199202 1 002</p>
    </div>

</body>

</html>
