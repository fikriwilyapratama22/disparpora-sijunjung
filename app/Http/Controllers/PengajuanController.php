<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengajuan_data;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PengajuanController extends Controller
{
    public function index()
{
    $setting = \App\Helpers\AppHelper::instance()->requestApiSetting();
    $menu = 'pengajuan';
    $session_data = \App\Helpers\AppHelper::instance()->getSessionData();
    $session_token = \App\Helpers\AppHelper::instance()->getSessionToken();
    $pengajuan = Pengajuan::all();
    $layanans = Layanan::all();
    // Ambil data hanya yang status = 'diterima'
    $pengajuanData = \App\Models\Pengajuan_data::with(['pengajuan.layanan'])
        ->whereIn('status', ['diterima', 'approved'])
        ->get();
    $data['pengajuanData'] = $pengajuanData;
    $pengajuanDatas = \App\Models\Pengajuan_data::with(['pengajuan.layanan'])
        ->whereIn('status', [ 'approved'])
        ->get();
    $data['pengajuanDatas'] = $pengajuanDatas;

    return view('admin.pengajuan_data.dinas', compact('setting', 'menu', 'session_data', 'session_token','pengajuan','layanans'), $data);
}

public function approve($id)
{
    $pengajuan = \App\Models\Pengajuan_data::findOrFail($id);
    $pengajuan->status = 'approved';
    $pengajuan->save();

    return redirect()->back()->with('success', 'Pengajuan berhasil di-approve.');
}

public function reject($id)
{
    $pengajuan = \App\Models\Pengajuan_data::findOrFail($id);
    $pengajuan->status = 'reject';
    $pengajuan->save();

    return redirect()->back()->with('success', 'Pengajuan berhasil di-reject.');
}

public function uploadCompleteFile(Request $request, $id)
{
    $request->validate([
        'file_complete' => 'required|mimes:pdf|max:5120' // maksimal 5MB
    ]);

    $pengajuan = Pengajuan_data::findOrFail($id);

    if ($request->hasFile('file_complete')) {
        $file = $request->file('file_complete');
        $filename = 'file_complete_' . time() . '.' . $file->getClientOriginalExtension();

        // Simpan file ke folder public/upload
        $destinationPath = public_path('upload');
        $file->move($destinationPath, $filename);

        // Simpan path ke database (relative ke public)
        $pengajuan->file_complete = 'upload/' . $filename;
        $pengajuan->save();
    }

    return back()->with('success', 'File PDF berhasil diupload.');
}
public function store(Request $request)
{
    $request->validate([
        'jenis' => 'required|string|max:255',
        'kode' => 'required|string|max:100',
        'layanan_id' => 'required|exists:layanans,id',
    ]);

    Pengajuan::create([
        'jenis' => $request->jenis,
        'kode' => $request->kode,
        'layanan_id' => $request->layanan_id,
        'status' => 'diajukan', // default status awal
    ]);

    return redirect()->back()->with('success', 'Pengajuan berhasil ditambahkan.');
}

// public function downloadSurat($id)
// {
//     $pengajuan = Pengajuan::with('pengajuan_data')->findOrFail($id);
//     $nomorSurat = Pengajuan_data::where('status', 'approved')
//         ->where('id', '<=', $pengajuan->id)
//         ->count();
//     $pdf = Pdf::loadView('admin.surat.penelitian', [
//         'pengajuan' => $pengajuan,
//         'nomorSurat' => $nomorSurat
//     ])->setPaper('a4');

//     return $pdf->download('surat_dinas_'.$pengajuan->id.'.pdf');
// }
public function downloadSurat($id)
{
    // Ambil data pengajuan_data dan relasi pengajuan
    $pengajuanData = Pengajuan_data::with('pengajuan')->findOrFail($id);

    // Hitung nomor surat berdasarkan ID yang disetujui
    $nomorSurat = Pengajuan_data::where('status', 'approved')
        ->where('id', '<=', $pengajuanData->id)
        ->count();

    // Pilih template berdasarkan pengajuan_id
    $template = match ($pengajuanData->pengajuan_id) {
        1 => 'admin.surat.olahraga',
        2 => 'admin.surat.penelitian',
        3 => 'admin.surat.keramaian',
        default => 'admin.surat.default',
    };

    // Load PDF
    $pdf = pdf::loadView($template, [
        'pengajuan' => $pengajuanData->pengajuan,
        'pengajuanData' => $pengajuanData,
        'nomorSurat' => $nomorSurat ,
    ])->setPaper('a4');

    return $pdf->download('surat_dinas_' . $pengajuanData->id . '.pdf');
}

public function edit($id)
{
    $setting = \App\Helpers\AppHelper::instance()->requestApiSetting();
    $menu = 'pengajuan';
    $session_data = \App\Helpers\AppHelper::instance()->getSessionData();
    $session_token = \App\Helpers\AppHelper::instance()->getSessionToken();
    $pengajuan = Pengajuan::findOrFail($id);
    return view('admin.pengajuan_data.edit', compact('pengajuan', 'setting', 'menu', 'session_data', 'session_token'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'jenis' => 'required|string|max:255',
        'kode' => 'required|string|max:255',
    ]);

    $pengajuan = Pengajuan::findOrFail($id);
    $pengajuan->update([
        'jenis' => $request->jenis,
        'kode' => $request->kode,
    ]);

    return redirect()->back()->with('success', 'Kode Surat berhasil diperbarui.');
}

}
