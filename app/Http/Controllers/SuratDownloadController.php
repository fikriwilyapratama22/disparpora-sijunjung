<?php
namespace App\Http\Controllers;

use App\Models\Pengajuan_data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SuratDownloadController extends Controller
{
    public function show(Request $request, $id)
    {
        try {
            $item = Pengajuan_data::findOrFail($id);

            if (empty($item->file_url)) {
                Log::warning('[surat.download] file_url kosong', ['id' => $id]);
                abort(404, 'File tidak ditemukan.');
            }

            // Normalisasi path relatif ke disk 'public' (contoh benar: 'surat-final/xxx.pdf')
            $path = ltrim(Str::after($item->file_url, 'storage/'), '/');

            $disk = Storage::disk('public');
            if (!$disk->exists($path)) {
                Log::warning('[surat.download] file tidak ada di disk public', ['id' => $id, 'path' => $path]);
                abort(404, 'File tidak ditemukan di penyimpanan.');
            }

            $downloadName = 'Surat-'.$id.'.pdf';

            // Cek driver; jika lokal, pakai storage_path (tanpa ->path())
            $driver = config('filesystems.disks.public.driver', 'local');
            if ($driver === 'local') {
                $absolutePath = storage_path('app/public/'. $path);

                // fallback bila yang tersedia via symlink public/storage
                if (!is_file($absolutePath)) {
                    $symlinkPath = public_path('storage/'. $path);
                    if (is_file($symlinkPath)) {
                        $absolutePath = $symlinkPath;
                    }
                }

                if (!is_file($absolutePath)) {
                    Log::error('[surat.download] absolute path tidak ditemukan', [
                        'id' => $id, 'abs' => $absolutePath
                    ]);
                    abort(404, 'Berkas fisik tidak ditemukan.');
                }

                if ($request->boolean('inline')) {
                    return response()->file($absolutePath, ['Content-Type' => 'application/pdf']);
                }
                return response()->download($absolutePath, $downloadName, ['Content-Type' => 'application/pdf']);
            }

            // Non-local (mis. S3) → stream
            $stream = $disk->readStream($path);
            if ($stream === false) {
                Log::error('[surat.download] gagal open stream', ['id' => $id, 'path' => $path]);
                abort(500, 'Gagal membaca file.');
            }

            if ($request->boolean('inline')) {
                return response()->stream(function () use ($stream) {
                    fpassthru($stream);
                    if (is_resource($stream)) fclose($stream);
                }, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="'.$downloadName.'"',
                ]);
            }

            return response()->streamDownload(function () use ($stream) {
                fpassthru($stream);
                if (is_resource($stream)) fclose($stream);
            }, $downloadName, ['Content-Type' => 'application/pdf']);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Data tidak ditemukan.');
        } catch (\Throwable $e) {
            Log::error('[surat.download] exception', ['id' => $id, 'error' => $e->getMessage()]);
            abort(500, 'Terjadi kesalahan saat mengunduh file.');
        }
    }
}
