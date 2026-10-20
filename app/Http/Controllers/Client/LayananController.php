<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\Pengajuan_data;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LayananController extends Controller
{


public function index()
{
    $layanans = Layanan::all(); // Ambil semua data layanan

    $og = [
        'title' => 'Layanan',
        'description' => 'Daftar layanan publik Disparpora',
        'url' => url('/layanan'),
    ];
    return view('client.beranda.index', compact('layanans','og'));
}
public function show($id)
{
    $layanan = Layanan::findOrFail($id);
    $konten = $layanan->content;
    $pengajuan = Pengajuan::all();

    $data = [];
    $data['og'] = [
        'url' => url("/layanan/$id"),
        'title' => $layanan->judul ?? 'Layanan',
        'description' => strip_tags($konten->judul1 ?? ''),
    ];
    $data['setting'] = \App\Helpers\AppHelper::instance()->requestApiSetting();

    $data['layanan'] = $layanan;
    $data['konten'] = $konten;
    $data['pengajuan'] = $pengajuan;
    $data['user'] = Auth::user();

    return view('client.layanan.index', $data);
}

public function storePengajuan(Request $request)
{
    // dd($request->all());
    $validated = $request->validate([
        'nama'           => 'required|string|max:255',
        'pengajuan_id'   => 'required|exists:pengajuans,id',
        'no_hp'          => 'required|string|max:20',
        'nik'            => 'required|string|max:20',
        'alamat'         => 'required|string|max:255',
        'file.*'         => 'nullable|file|max:10240', // maksimal 10MB per file
        'file_url'       => 'nullable|url',

        // Optional fields (tidak wajib)
        'nim'            => 'nullable|string|max:50',
        'fakultas'       => 'nullable|string|max:100',
        'universitas'    => 'nullable|string|max:100',
        'prodi'          => 'nullable|string|max:100',
        'tmp_penelitian' => 'nullable|string|max:255',
        'judul'          => 'nullable|string|max:255',
        'jam_mulai'      => 'nullable|date_format:H:i',
        'jam_selesai'    => 'nullable|date_format:H:i',
        'tgl_mulai'      => 'nullable|date_format:Y-m-d',
        'tgl_selesai'    => 'nullable|date_format:Y-m-d',
        'barang'         => 'nullable|string|max:255',
        'tempat'         => 'nullable|string|max:255',
        'kegiatan'       => 'nullable|string|max:255',
    ]);

    // Upload file jika ada
    $uploadedFiles = [];
    if ($request->hasFile('file')) {
        foreach ($request->file('file') as $file) {
            $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/pengajuan'), $filename);
            $uploadedFiles[] = 'uploads/pengajuan/'.$filename;
        }
    }

    // Simpan data
    Pengajuan_data::create([
        'user_id'        => auth()->id(),
        'pengajuan_id'   => $validated['pengajuan_id'],
        'nama'           => $validated['nama'],
        'no_hp'          => $validated['no_hp'],
        'nik'            => $validated['nik'],
        'alamat'         => $validated['alamat'],
        'status'         => 'diajukan',
        'file'           => json_encode($uploadedFiles),
        'file_url'       => $validated['file_url'] ?? null,
        'nim'            => $validated['nim'] ?? null,
        'fakultas'       => $validated['fakultas'] ?? null,
        'universitas'    => $validated['universitas'] ?? null,
        'prodi'          => $validated['prodi'] ?? null,
        'tmp_penelitian' => $validated['tmp_penelitian'] ?? null,
        'judul'          => $validated['judul'] ?? null,
        'jam_mulai'      => $validated['jam_mulai'] ?? null,
        'jam_selesai'    => $validated['jam_selesai'] ?? null,
        'tgl_mulai'      => $validated['tgl_mulai'] ?? null,
        'tgl_selesai'    => $validated['tgl_selesai'] ?? null,
        'barang'         => $validated['barang'] ?? null,
        'tempat'         => $validated['tempat'] ?? null,
        'kegiatan'       => $validated['kegiatan'] ?? null,
    ]);

    return redirect()->back()->with('success', 'Pengajuan layanan berhasil dikirim.');
}




}
