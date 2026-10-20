<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Pengajuan_data;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;


class PengajuanDataController extends Controller
{
    public function index()
    {
        $setting = \App\Helpers\AppHelper::instance()->requestApiSetting();
        $menu = 'pengajuan';
        $session_data = \App\Helpers\AppHelper::instance()->getSessionData();
        $session_token = \App\Helpers\AppHelper::instance()->getSessionToken();
        $pengajuanData = \App\Models\Pengajuan_data::with(['pengajuan.layanan'])->get();
        $data['pengajuanData'] = $pengajuanData;
        $pengajuan = Pengajuan::all();

         $chartData = $pengajuanData->groupBy(function($item) {
        return $item->pengajuan->jenis ?? 'Tidak Diketahui';
    })->map(function($group) {
        return $group->count();
    });
        return view('admin.pengajuan_data.index', [
        'pengajuanData' => $pengajuanData,
        'chartLabels'   => $chartData->keys(),
        'chartValues'   => $chartData->values(),
        ], compact('setting', 'menu', 'session_data', 'session_token','pengajuan'), $data);
    }
    public function show()
    {
        $pengajuanData = \App\Models\Pengajuan_data::with(['pengajuan.layanan'])->get();

        $data = [];
        $data['setting'] = \App\Helpers\AppHelper::instance()->requestApiSetting();
        $data['menu'] = 'pengajuan-show';
        $data['session_data'] = \App\Helpers\AppHelper::instance()->getSessionData();
        $data['session_token'] = \App\Helpers\AppHelper::instance()->getSessionToken();
        $data['pengajuanData'] = $pengajuanData;

        return view('admin.pengajuan_data.index', $data);
    }
    public function approve($id)
    {
        $pengajuan = \App\Models\Pengajuan_data::findOrFail($id);
        $pengajuan->status = 'diterima';
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

public function ajaxMenu(Request $request)
{
    $exclude = collect(explode(',', (string) $request->query('exclude')))
        ->filter()->map('intval')->all();

    $latest = Pengajuan_data::with('pengajuan')
        ->where('user_id', auth()->id())
        ->whereNotNull('file_complete')
        ->where('file_complete', '!=', '')
        ->when(!empty($exclude), fn($q) => $q->whereNotIn('id', $exclude)) // ⬅️ penting
        ->orderByDesc('updated_at')
        ->first();

    if (!$latest) {
        return response('<div class="text-muted px-2 py-1">Belum ada file.</div>');
    }

    $title = e('Surat ' . ($latest->pengajuan->jenis ?? ''));
    $href  = asset($latest->file_complete);

    $html  = '<div class="file-entry mb-2" data-id="'.$latest->id.'">';
    $html .=   '<a href="'.$href.'" target="_blank" rel="noopener"';
    $html .=      ' class="btn btn-sm btn-success w-100 text-start" data-notif-item="1" data-id="'.$latest->id.'">';
    $html .=      $title;
    $html .=   '</a>';
    $html .= '</div>';

    return response($html);
}


public function dokumenSaya(Request $request)
{
    $q = Pengajuan_data::with('pengajuan')
        ->where('user_id', auth()->id())
        ->whereNotNull('file_complete')
        ->where('file_complete', '!=', '');

    // --- FILTER TANGGAL ---
    $dateFrom = $request->get('date_from');
    $dateTo   = $request->get('date_to');

    if (!$dateFrom && !$dateTo) {
        $dateFrom = now()->startOfMonth()->toDateString();
        $dateTo   = now()->endOfMonth()->toDateString();
    }

    if ($dateFrom || $dateTo) {
        $from = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : null;
        $to   = $dateTo   ? Carbon::parse($dateTo)->endOfDay()     : null;

        if ($from && $to)      $q->whereBetween('updated_at', [$from, $to]);
        elseif ($from)         $q->where('updated_at', '>=', $from);
        elseif ($to)           $q->where('updated_at', '<=', $to);
    }

    $q->orderByDesc('updated_at');

    $items = $q->paginate(12)->withQueryString();

    // Ambil jumlah dari daftar ini (sesuai filter)
    $unreadDocumentsCount = $items->total();
    // NOTE: kalau yang dimaksud "unread", ganti query di atas dengan ->where('is_read', 0),
    // lalu $unreadDocumentsCount = $items->total() tetap valid.

    // Variabel lain
    $setting       = \App\Helpers\AppHelper::instance()->requestApiSetting();
    $menu          = 'dokumen-saya';
    $session_data  = \App\Helpers\AppHelper::instance()->getSessionData();
    $session_token = \App\Helpers\AppHelper::instance()->getSessionToken();
    $og = [
        'title'       => 'Dokumen Saya',
        'url'         => url()->current(),
        'description' => 'Daftar dokumen yang pernah Anda unduh.',
    ];

    return view('client.documents.list', compact(
        'items','setting','menu','session_data','session_token','og','dateFrom','dateTo','unreadDocumentsCount'
    ));
}

public function cetakLaporan(Request $request)
{
    $month = (int)($request->query('month', now()->month));
    $year  = (int)($request->query('year',  now()->year));
    $basis = $request->query('basis', 'updated'); // created | updated | approved

    // Tentukan kolom tanggal utama
    $dateColumn = match ($basis) {
        'created'  => 'created_at',
        'approved' => (Schema::hasColumn('pengajuan_data', 'approved_at') ? 'approved_at' : 'updated_at'),
        default    => 'updated_at',
    };

    // Query dasar: status approved/diterima
    $q = \App\Models\Pengajuan_data::with(['pengajuan.layanan'])
        ->whereIn('status', ['diterima','approved']);

    // Jika pakai approved_at tapi kolom ada, pastikan tidak null
    if ($dateColumn === 'approved_at') {
        $q->whereNotNull('approved_at');
    }

    // Filter bulan & tahun
    $q->whereMonth($dateColumn, $month)
      ->whereYear($dateColumn, $year);

    $records = $q->orderBy($dateColumn, 'desc')->get();

    // Jika kosong, fallback otomatis ke created_at (biar tidak "kelihatan kosong" akibat mismatch basis)
    if ($records->isEmpty() && $dateColumn !== 'created_at') {
        $dateColumn = 'created_at';
        $records = \App\Models\Pengajuan_data::with(['pengajuan.layanan'])
            ->whereIn('status', ['diterima','approved'])
            ->whereMonth($dateColumn, $month)
            ->whereYear($dateColumn, $year)
            ->orderBy($dateColumn, 'asc')
            ->get();
    }

    $rekapPerJenis = $records->groupBy(fn($r) => optional($r->pengajuan)->jenis ?? 'Tidak diketahui')
                             ->map->count();

    return view('admin.pengajuan_data.laporan', [
        'records'     => $records,
        'rekapPerJenis'=> $rekapPerJenis,
        'month'       => $month,
        'year'        => $year,
        'monthName'   => \Carbon\Carbon::create()->month($month)->translatedFormat('F'),
        'dateColumn'  => $dateColumn,
    ]);
}



}
