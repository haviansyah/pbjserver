<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisDokumen extends Model
{
    use HasFactory;

    public function step(){
        return $this->hasMany("App\Models\Step");
    }
}
