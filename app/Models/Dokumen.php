<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;
    
    function jenisDokumen(){
        return $this->belongsTo("\App\Model\JenisDokumen");
    }

    function statusDokumen(){
        return $this->belongsTo("\App\Models\StatusDokumen");
    }

    function pengadaan(){
        return $this->belongsTo("\App\Models\Pengadaan");
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User',"created_by_user_id");
    }

    public function posisi()
    {
        return $this->belongsTo('App\Models\User',"posisi_by_user_id");
    }

}
