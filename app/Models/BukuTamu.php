<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuTamu extends Model
{
    use HasFactory;
    protected $table = 'buku_tamus';
    protected $guarded = [];

   protected $fillable = [
    'nama',
    'alamat',
    'no_telfon',
    'pesan',
    'lokawisata_id'
];


    // app/Models/BukuTamu.php
public function infoTempat()
{
    return $this->belongsTo(InfoTempat::class, 'lokawisata_id');
}


    
}
