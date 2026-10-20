<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan_data extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_datas';
    protected $casts = [
    'file' => 'array',
];

    protected $guarded = [];
    protected $fillable = [
    'user_id', 'pengajuan_id', 'nama', 'no_hp', 'nik', 'alamat',
    'status', 'file', 'file_url', 'nim', 'fakultas', 'universitas',
    'prodi', 'tmp_penelitian', 'judul','jam_mulai', 'jam_selesai', 'tgl_mulai', 'tgl_selesai','tempat','kegiatan','barang'
];


    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }
}
