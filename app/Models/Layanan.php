<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use HasFactory;
    protected $fillable = [
        'layanan','gambar','deskripsi'
    ];
    public function content()
{
    return $this->hasOne(LayananContent::class,'layanan_id');
}
    public function pengajuan()
{
    return $this->hasMany(pengajuan::class,'layanan_id');
}

}
