<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityPengadaan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function pengadaan(){
        return $this->belongsTo("\App\Models\Pengadaan");
    }

    public function from()
    {
        return $this->belongsTo('App\Models\User',"from_user_id");
    }

    public function to()
    {
        return $this->belongsTo('App\Models\User',"to_user_id");
    }

    public function status()
    {
        return $this->belongsTo('App\Models\StatusPengadaan');
    }
}
