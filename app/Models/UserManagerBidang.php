<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserManagerBidang extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function bidang(){
        return $this->belongsTo('App\Models\DireksiPengadaan','bidang_id');
    }
}
