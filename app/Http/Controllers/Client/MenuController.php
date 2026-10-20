<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengajuan_data;

class MenuController extends Controller
{
    public function markRead(Request $request)
    {
        $id = $request->input('id');

        // Sesuaikan guard jika perlu: Auth::guard('client')->id()
        Pengajuan_data::where('id', $id)
            ->where('user_id', Auth::id())
            ->update(['is_read' => 1]);  // pastikan kolom is_read ada

        return response()->noContent();
    }
}
