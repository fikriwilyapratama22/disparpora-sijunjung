<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layanan;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Storage;

class LayananController extends Controller
{
    public function index()
    {
        $data = [];
        $data['setting'] = \App\Helpers\AppHelper::instance()->requestApiSetting();
        $data['menu'] = 'layanan-show';
        $data['session_data'] = \App\Helpers\AppHelper::instance()->getSessionData();
        $data['session_token'] = \App\Helpers\AppHelper::instance()->getSessionToken();
        $layanan = Layanan::all();

        return view('admin.layanan.index', array_merge($data, [
            'layanan' => $layanan,
        ]));
    }

    public function edit($id)
    {
        // Ambil data layanan berdasarkan ID
        $layanan = Layanan::with('content')->findOrFail($id);

        // Siapkan data tambahan untuk view
        $data = [];
        $data['menu'] = 'layanan-show';
        $data['session_data'] = \App\Helpers\AppHelper::instance()->getSessionData();
        $data['session_token'] = \App\Helpers\AppHelper::instance()->getSessionToken();
        $data['setting'] = \App\Helpers\AppHelper::instance()->requestApiSetting();
        $data['layanan'] = $layanan;

        return view('admin.layanan.edit', $data);
    }

     public function update(Request $request, $id)
{
    $validated = $request->validate([
        'layanan'   => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
        'gambar'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

        'judul1' => 'nullable|string|max:255',
        'isi1'   => 'nullable|string',
        'judul2' => 'nullable|string|max:255',
        'isi2'   => 'nullable|string',
        'judul3' => 'nullable|string|max:255',
        'isi3'   => 'nullable|string',
    ]);

    $layanan = Layanan::with('content')->findOrFail($id);

    // Update field utama
    $layanan->layanan   = $validated['layanan'];
    $layanan->deskripsi = $validated['deskripsi'] ?? $layanan->deskripsi;

    // Upload gambar
    if ($request->hasFile('gambar')) {
        $file = $request->file('gambar');
        $filename = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('layanan'), $filename);
        $layanan->gambar = $filename;
    }

    $layanan->save();

    // Data untuk content
    $contentData = [
        'judul1' => $request->judul1,
        'isi1'   => $request->isi1,
        'judul2' => $request->judul2,
        'isi2'   => $request->isi2,
        'judul3' => $request->judul3,
        'isi3'   => $request->isi3,
    ];

    // Update atau buat baru
    if ($layanan->content) {
        $layanan->content->update($contentData);
    } else {
        $layanan->content()->create($contentData);
    }

    return redirect()->route('admin.layanan.index')
                     ->with('success', 'Data layanan berhasil diperbarui.');
}


    public function create()
{
    $data = [];
    $data['menu'] = 'layanan-show';
    $data['session_data'] = \App\Helpers\AppHelper::instance()->getSessionData();
    $data['session_token'] = \App\Helpers\AppHelper::instance()->getSessionToken();
    $data['setting'] = \App\Helpers\AppHelper::instance()->requestApiSetting();

    return view('admin.layanan.create', $data);
}

public function store(Request $request)
{
    $request->validate([
        'layanan' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
        'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $layanan = new Layanan();
    $layanan->layanan = $request->layanan;
    $layanan->deskripsi = $request->deskripsi;

    // Simpan gambar
    if ($request->hasFile('gambar')) {
        $filename = time() . '.' . $request->gambar->extension();
        $request->gambar->move(public_path('layanan'), $filename);
        $layanan->gambar = $filename;
    }

    $layanan->save();

    return redirect('/admin/layanan')->with('success', 'Layanan berhasil ditambahkan.');
}

public function delete($id)
{
    $layanan = Layanan::findOrFail($id);

    // Hapus file gambar jika ada
    if ($layanan->gambar && file_exists(public_path('layanan/' . $layanan->gambar))) {
        unlink(public_path('layanan/' . $layanan->gambar));
    }

    // Hapus konten terkait jika ada
    if ($layanan->content) {
        $layanan->content()->delete();
    }

    $layanan->delete();

    return redirect()->route('admin.layanan.index')->with('success', 'Data layanan berhasil dihapus.');
}



}
