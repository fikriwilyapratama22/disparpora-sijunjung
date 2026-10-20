<?php

namespace App\Http\Controllers;

use App\Models\BukuTamu;
use Illuminate\Http\Request;
use App\Models\InfoTempat;

class BukuTamuController extends Controller
{

    public function index()
    {
        $data = [];
        $data['setting'] = \App\Helpers\AppHelper::instance()->requestApiSetting();
        $data['menu'] = 'BukuTamu';
        $data['session_data'] = \App\Helpers\AppHelper::instance()->getSessionData();
        $data['session_token'] = \App\Helpers\AppHelper::instance()->getSessionToken();
        // $bukutamu = BukuTamu::all();
        // $data['bukutamu'] = $bukutamu;
       $data['bukutamu'] = BukuTamu::with('infoTempat')->get();
        return view('admin.bukutamu.index', $data);
    }
    public function create()
{
    $lokawisata = InfoTempat::all(); // Ambil semua data
    return view('buku-tamu.create', compact('lokawisata'));
}
    public function store(Request $request)
{
    $request->validate([
        'nama' => 'required',
        'lokawisata_id' => 'nullable|exists:info_tempats,id',
        'alamat' => 'required',
        'no_telfon' => 'required',
        'pesan' => 'required',
    ]);

    BukuTamu::create([
        'nama' => $request->nama,
        'lokawisata_id' => $request->lokawisata_id,
        'alamat' => $request->alamat,
        'no_telfon' => $request->no_telfon,
        'pesan' => $request->pesan,
    ]);

    return redirect()->back()->with('success', 'Data tamu berhasil ditambahkan');
}

    public function destroy($id)
    {
        $bukutamu = BukuTamu::findOrFail($id);
        $bukutamu->delete();
        return redirect()->back()->with('success', 'Pesan berhasil dihapus.');
    }
}
