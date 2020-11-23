<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TypeNotificationConst;

class Notification extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function data(){
        
        $data_type = $this->data_type;

        $model = $data_type == TypeNotificationConst::DOKUMEN ? "Dokumen" : "Pengadaan";
        
        return $this->belongsTo("App\Models\\".$model,"data_id");
    }
}
