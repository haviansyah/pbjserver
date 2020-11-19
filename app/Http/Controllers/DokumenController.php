<?php

namespace App\Http\Controllers;

use App\Http\Resources\Dokumen as ResourcesDokumen;
use App\Models\Dokumen;
use Illuminate\Http\Request;
use JWTAuth;

class DokumenController extends Controller
{
    private $filterableAttr = [
        "jenis_dokumen_id",
    ];

    public function store(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $pengadaan = Dokumen::create([
            'pengadaan_id' => $request->get("pengadaan_id"),
            'jenis_dokumen_id' => $request->get("jenis_dokumen_id"),
            'status_dokumen_id' => $request->get("status_dokumen_id"),
            'created_by_user_id' => $user->id,
            'posisi_user_id' => $user->id,
        ]);
        return response()->json(compact('pengadaan'),201);
    }

    public function get(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $data = Dokumen::where(function($query) use($user){
            return $query->where("posisi_user_id",$user->id)->orWhere("created_by_user_id",$user->id);
        });
        if($request->term && $request->term != "" ){
            $term = $request->term;
            $data =  $data->whereHas("pengadaan",function($query) use($term){
                return $query->where("judul_pengadaan","LIKE","%".$term."%");
            });
        }
        

        foreach($this->filterableAttr as $filter){
            if($request->get($filter) && count($request->get($filter)) != 0){
                $status_pengadaans = $request->get($filter);
                if(!in_array(null,$status_pengadaans)){
                    $data = $data->whereIn($filter,$status_pengadaans);
                }
            }
        }
        return ResourcesDokumen::collection($data->get());
        // return response()->json($dokumens,200);
    }
}
