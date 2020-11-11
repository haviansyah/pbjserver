<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengadaan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function jenisAnggaran()
    {
        return $this->belongsTo('App\Models\JenisAnggaran');
    }

    public function jenisPengadaan()
    {
        return $this->belongsTo('App\Models\JenisPengadaan');
    }

    public function statusPengadaan()
    {
        return $this->belongsTo('App\Models\StatusPengadaan');
    }

    public function direksiPengadaan()
    {
        return $this->belongsTo('App\Models\DireksiPengadaan');
    }

    public function metodePengadaan()
    {
        return $this->belongsTo('App\Models\MetodePengadaan');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User',"created_by_user_id");
    }
}
