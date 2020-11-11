<?php

namespace App\Http\Controllers;
use App\Http\Resources\Pengadaan as ResourcesPengadaan;
use App\Models\Pengadaan;
use App\Models\StatusPengadaan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use JWTAuth;
use RoleConst;
use StatusPengadaanConst;

class PengadaanController extends Controller
{   

    private $filterableAttr = [
        "status_pengadaan_id",
        "jenis_pengadaan_id",
        "direksi_pengadaan_id",
        "jenis_anggaran_id",
        "metode_pengadaan_id"
    ];

    public function store(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $pengadaan = Pengadaan::create([
            'judul_pengadaan' => $request->get('judul_pengadaan'),
            'status_pengadaan_id' => StatusPengadaanConst::PRAPENGADAAN,
            'jenis_pengadaan_id' => $request->jenis_pengadaan_id,
            'direksi_pengadaan_id' => $request->direksi_pengadaan_id,
            'jenis_anggaran_id' => $request->jenis_anggaran_id,
            'created_by_user_id' => $user->id,
        ]);
        return response()->json(compact('pengadaan'),201);
    }

    public function delete($id){
        $pengadaan = Pengadaan::findOrFail($id);
        if($pengadaan->delete()){
            return response()->json(["status"=>"OK"],200);
        }
        return response()->json(["status"=>"NOT OK"],500);
    }

    public function update($id, Request $request){
        $pengadaan = Pengadaan::findOrFail($id);
        try{
            if($pengadaan->update($request->all())){
                return response()->json(["status"=>"OK"],200);
            }
        }catch(Exception $e){
            return $e;
        }
    }

    public function index(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $auth_role = $user->RoleName;

        switch($auth_role){
            case RoleConst::ADMIN :
                $data = Pengadaan::whereNotNull('id');
            break;
            case RoleConst::RENDAL:
                $data = Pengadaan::where("created_by_user_id",$user->id);
            break;
            case RoleConst::MANAGERBIDANG:
                $bidang_id = $user->Bidang->id;
                $data = Pengadaan::where("direksi_pengadaan_id",$bidang_id);
            break;
        }

        if($request->term){
            $term = $request->term;
            $data = $data->where("judul_pengadaan","like","%".$term."%");
        }

        foreach($this->filterableAttr as $filter){
            if($request->get($filter)){
                $status_pengadaans = $request->get($filter);
                $data = $data->whereIn($filter,$status_pengadaans);
            }
        }
        

        return ResourcesPengadaan::collection($data->paginate());
    }
}
