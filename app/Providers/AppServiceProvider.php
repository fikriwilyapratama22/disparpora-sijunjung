<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengajuan_data;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
  public function boot(): void
{
    View::composer('client.layouts.*', function ($view) {
        // pakai guard yang sesuai: Auth::id() atau Auth::guard('client')->id()
        $userId = Auth::id();
        $count = 0;

        if ($userId) {
            $count = Pengajuan_data::query()
                ->where('user_id', $userId)
                ->whereNotNull('file_complete')
                ->where('file_complete', '!=', '')
                // ->where('is_read', 0) // aktifkan jika hanya hitung yang belum dibaca
                ->count();
        }

        $view->with('unreadDocumentsCount', $count);
    });
}

}
