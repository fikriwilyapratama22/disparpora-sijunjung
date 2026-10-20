<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;
    protected $fillable = [ 'layanan_id','jenis','kode','nomor'];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }
    public function pengajuan_data()
    {
        return $this->hasMany(Pengajuan_data::class,'pengajuan_id');
    }
}
