<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\InfoTempat;
use App\Models\Pengajuan_data;   // ⬅️ tambahkan ini

class BerandaController extends Controller
{
    public function __construct() {}

    public function index()
    {
        $data = [];
        $data['og'] = [];
        $data['og']['url'] = url('/') . '/beranda';
        $data['og']['title'] = 'Beranda';
        $data['og']['description'] = 'beranda';
        $data['setting'] = \App\Helpers\AppHelper::instance()->requestApiSetting();
        $data['contents'] = \App\Helpers\AppHelper::instance()->requestApiGet('api/content');
        $data['beritas'] = array_slice(\App\Helpers\AppHelper::instance()->requestApiGet('api/content?type=berita'), 0, 6);
        $data['categories'] = \App\Helpers\AppHelper::instance()->requestApiGet('api/category');
        $data['posters'] = \App\Helpers\AppHelper::instance()->requestApiGet('api/poster');
        $data['big_numbers'] = \App\Helpers\AppHelper::instance()->requestApiGet('api/big_number');
        $data['teams'] = \App\Helpers\AppHelper::instance()->requestApiGet('api/team');
        $data['mitras'] = \App\Helpers\AppHelper::instance()->requestApiGet('api/mitra');
        $data['testimonies'] = \App\Helpers\AppHelper::instance()->requestApiGet('api/testimony');
        $data['layanans'] = \App\Models\Layanan::all();
        $data['lokawisata'] = InfoTempat::all();

        // ⬇️ Tambahkan: jumlah dokumen untuk badge layout
        $data['unreadDocumentsCount'] = Pengajuan_data::query()
            ->where('user_id', Auth::id())
            ->whereNotNull('file_complete')
            ->where('file_complete', '!=', '')
            // ->where('is_read', 0) // aktifkan jika mau hitung "unread" saja
            ->count();

             $hasNotif = Pengajuan_data::query()
        ->where('user_id', Auth::id())
        ->whereNotNull('file_complete')
        ->where('file_complete', '!=', '')
        ->exists();

    $data['hasNotif'] = $hasNotif;

        return view('client.beranda.index', $data);
    }
}
