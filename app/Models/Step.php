<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function jenisDokumen(){
        return $this->belongsTo("App\Models\JenisDokumen");
    }

    public function role(){
        return $this->belongsTo("App\Models\Role");
    }
}
