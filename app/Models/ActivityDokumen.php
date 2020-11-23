<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityDokumen extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function document(){
        return $this->belongsTo("\App\Models\Dokumen");
    }

    public function from()
    {
        return $this->belongsTo('App\Models\User',"from_user_id");
    }

    public function to()
    {
        return $this->belongsTo('App\Models\User',"to_user_id");
    }
}
