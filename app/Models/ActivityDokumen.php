<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityDokumen extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function document(){
        return $this->belongsTo("App\Models\Dokumen","dokumen_id");
    }

    public function from()
    {
        return $this->belongsTo('App\Models\User',"from_user_id");
    }

    public function to()
    {
        return $this->belongsTo('App\Models\User',"to_user_id");
    }
    
    public function stepRelationship(){
        return Step::where(function($q){
            $q->where("jenis_dokumen_id",$this->document->jenis_dokumen_id)->where("step",$this->step);
        });
    }

    public function getStepModelAttribute(){
        return $this->stepRelationship()->first();
    }
}
