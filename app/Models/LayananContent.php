<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananContent extends Model
{
    use HasFactory;
    protected $fillable = [
        'layanan_id',
        'judul1','isi1',
        'judul2','isi2',
        'judul3','isi3',
    ];

    public function layanan()
{
    return $this->belongsTo(Layanan::class);
}

}
