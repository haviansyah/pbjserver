<?php

namespace App\Http\Controllers;

use App\Helpers\PbjHelper;
use App\Http\Resources\Dokumen as ResourcesDokumen;
use App\Http\Resources\Pengadaan;
use App\Models\Dokumen;
use App\Models\DokumenDmr;
use App\Models\DokumenPr;
use App\Models\Notification;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use JenisDokumenConst;
use JWTAuth;
use RoleConst;
use RoleConstId;
use StateDocumentConst;
use StatusDokumenConst;
use TypeNotificationConst;

class DokumenController extends Controller
{

    private $step = [
        /* Step Buat TOR */
        JenisDokumenConst::TOR =>
        [
            [RoleConstId::RENDAL, ["submit"]],
            [RoleConstId::SEKERTARISGM, ["teruskan"]],
            [RoleConstId::MANAGERBIDANG, ["approve","revise"]],
            [RoleConstId::SEKERTARISGM, ["approve","revise"]],

            [RoleConstId::KEUANGAN, ["approve","revise"]],
            [RoleConstId::MADM],
            [RoleConstId::PBJ, ["approve","revise"]],

        ],
        JenisDokumenConst::DMR =>
        [
            [RoleConstId::RENDAL, ["submit"]],
            [RoleConstId::SEKERTARISGM, ["teruskan"]],
            [RoleConstId::MANAGERBIDANG, ["approve","revise"]],
            [RoleConstId::LIM, ["approve","revise"]],
            [RoleConstId::MENG, ["approve"]],
            [RoleConstId::SEKERTARISGM, ["approve"]],
            [RoleConstId::LIM, ["input?nomorDMR"]],

            [RoleConstId::KEUANGAN, ["approve","revise"]],
            [RoleConstId::MADM],
            [RoleConstId::PBJ, ["approve","revise"]],
        ],
        JenisDokumenConst::PR =>
        [
            [RoleConstId::RENDAL, ["submit"]],
            [RoleConstId::AMUINVENTORY, ["approve","revise"]],
            [RoleConstId::MANAGERBIDANG, ["approve","revise"]],

            [RoleConstId::KEUANGAN, ["approve","revise"]],
            [RoleConstId::MADM],
            [RoleConstId::PBJ, ["approve","revise"]],
        ]
    ];

    private $filterableAttr = [
        "jenis_dokumen_id",
    ];

    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $pengadaan = Dokumen::create([
            'pengadaan_id' => $request->get("pengadaan_id"),
            'jenis_dokumen_id' => $request->get("jenis_dokumen_id"),
            'status_dokumen_id' => $request->get("status_dokumen_id"),
            'created_by_user_id' => $user->id,
            'posisi_user_id' => $user->id,
        ]);

        if($request->get("nomor_pr") != null){
            $pengadaan->dokumenPr()->save(new DokumenPr(["nomor_pr" => $request->get("nomor_pr")]));
        }
        
        return response()->json($request->all(), 201);
    }


    function buildAction($id, $action)
    {
        return [
            "action_name" => ucfirst($action),
            "url" => url("api/dokumens/{$id}/{$action}")
        ];
    }

    public function getOne($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $data = Dokumen::findOrFail($id);

        $status_dokumen = $data->statusDokumen->id;
        $posisi_dokumen = $data->posisi->id;
        $jenis_dokumen = $data->jenisDokumen->id;
        $step_dokumen = $data->step;

        $data_array = new ResourcesDokumen($data);
        $action = [];

        if ($posisi_dokumen == $user->id) {
            if ($status_dokumen == StatusDokumenConst::MASUK) {
                $action = [
                    $this->buildAction($id, "konfirmasi"),
                    $this->buildAction($id, "tidak")
                ];
            } else {
                if(array_key_exists(1,$this->step[$jenis_dokumen][$step_dokumen])){
                    $actions_list = $this->step[$jenis_dokumen][$step_dokumen][1];
                    foreach ($actions_list as $act) {
                        array_push($action, $this->buildAction($id, $act));
                    }
                }
               
            }
        }
        return response()->json([
            "data" => $data_array,
            "aksi" => $action
        ]);
    }


    public function buildNotifBody(int $type,$dari, Dokumen $data){
        switch($type){
            case 1:
                return "Dokumen diteruskan dari {$dari} : {$data->jenisDokumen->jenis_dokumen_name} ".PbjHelper::buildJudul($data->pengadaan->judul_pengadaan);
            break;
        }
    }

    public function dokumenSubmit($id)
    {
        $data = Dokumen::findOrFail($id);

        $status_dokumen = $data->statusDokumen->id;
        $posisi_dokumen = $data->posisi->id;
        $posisi_dokumen_role = $data->posisi->role_id;
        $jenis_dokumen = $data->jenisDokumen->id;

        // Ambil Step Dokumen Dari Database Dan Convert Ke ID Array
        $step_dokumen = $data->step;

        $step_next = $step_dokumen + 1;
        if ($data->state_document == 1) {
            // Get Next STEP
            
            // Ambil role id selanjutnya
            $role_next = $this->step[$jenis_dokumen][$step_next][0];

            // Cari User dengan role selanjutnya 
            $role_user = User::where("role_id", $role_next)->first();
            

            // Cari User kalau manager bidang sesuai bidang 
            if ($role_next == RoleConstId::MANAGERBIDANG) {
                $bidang_dokumen = $data->pengadaan->direksi_pengadaan_id;
                $role_user = User::where("role_id", $role_next)->whereHas("managerBidang", function ($query) use ($bidang_dokumen) {
                    return $query->where("bidang_id", (int) $bidang_dokumen);
                })->first();
                // dd($role_user);
                $role_user_id =$role_user->id;
            }else if ($role_next == RoleConstId::MENG) {
                $role_user_id = 18;
            }else{
                $role_user_id = $role_user->id;
            }


            try {
                $data->prev_user_id = $data->posisi_user_id;
                $data->posisi_user_id = $role_user_id;
                $data->step = $step_next;
                $data->last_step =  $step_dokumen;
                $data->status_dokumen_id = StatusDokumenConst::MASUK;
               

                $notifBody = $this->buildNotifBody(1, User::find($data->prev_user_id)->name,$data);
                $notif = new Notification([
                    "user_id" => $role_user_id,
                    "title" => "Dokumen Di Teruskan",
                    "body" => $notifBody,
                    "data_id" => $data->id,
                    "data_type" => TypeNotificationConst::DOKUMEN
                ]);
                
                $data->save();
                $notif->save();


                PbjHelper::sendNotification($notif);
            } catch (Exception $e) {
                return $e;
            }

            return response()->json(["status" => "OK"], 200);
        }else if ($data->state_document == 2){

            if($posisi_dokumen_role == RoleConstId::RENDAL){
                try {
                    $keu_id =  User::where("role_id", RoleConstId::KEUANGAN)->first()->id;
                    $data->prev_user_id = $data->posisi_user_id;
                    $data->posisi_user_id = $keu_id;
                    $data->step = $data->last_step;
                    $data->last_step = 0;
                    $data->status_dokumen_id = StatusDokumenConst::MASUK;
                    $data->save();
                } catch (Exception $e) {
                    return $e;
                }
            }else{
                // IF APPROVED BY KEUANGAN
                $madm_id =  20;
                try {
                    $data->prev_user_id = $data->posisi_user_id;
                    $data->posisi_user_id = $madm_id;
                    $data->step = $step_next;
                    $data->last_step =  $step_dokumen;
                    $data->status_dokumen_id = StatusDokumenConst::MASUK;
                    $data->save();
                } catch (Exception $e) {
                    return $e;
                }
            }
            
        }
        else if ($data->state_document == 3){

            if($posisi_dokumen_role == RoleConstId::RENDAL){
                try {
                    // Move TO PBJ
                    $pbj_id =  User::where("role_id", RoleConstId::PBJ)->first()->id;
                    $data->prev_user_id = $data->posisi_user_id;
                    $data->posisi_user_id = $pbj_id;
                    $data->step = $data->last_step;
                    $data->last_step = 0;
                    $data->status_dokumen_id = StatusDokumenConst::MASUK;
                    $data->save();
                } catch (Exception $e) {
                    return $e;
                }
            }else{
                // IF APPROVED BY PBJ
                try {
                    $data->status_dokumen_id = StatusDokumenConst::APPROVE;
                    $data->save();
                } catch (Exception $e) {
                    return $e;
                }
            }
            
        }
    }

    // Konfirmasi Dokumen
    public function dokumenKonfirmasi($id)
    {
        try {
            $data = Dokumen::findOrFail($id);
            $data->prev_user_id = $data->posisi_user_id;
            $data->status_dokumen_id = StatusDokumenConst::REVIEW;

            if($data->posisi->role_id == RoleConstId::KEUANGAN){
                $data->state_document = StateDocumentConst::KEU;
            }if($data->posisi->role_id == RoleConstId::PBJ){
                $data->state_document = StateDocumentConst::PBJ;
            }
            $data->save();
        } catch (Exception $e) {
            return response()->json($e, 400);
        }
        return response()->json(["status" => "OK"], 200);
    }


    public function dokumenRevise($id)
    {
        try {
            $data = Dokumen::findOrFail($id);
            $data->posisi_user_id = $data->created_by_user_id;
            $data->last_step = $data->step;
            $data->step = 0;
            $data->status_dokumen_id = StatusDokumenConst::MASUK;
            $data->save();
        } catch (Exception $e) {
            return response()->json($e, 400);
        }
        return response()->json(["status" => "OK"], 200);
    }

    // Kembalikan Tolak Dokumen
    public function dokumenBack($id)
    {
        $data = Dokumen::findOrFail($id);
        try {
            $ls = $data->last_step;
            $data->posisi_user_id = $data->prev_user_id;
            $data->last_step = $data->step;
            $data->step = $ls;
            $data->status_dokumen_id = StatusDokumenConst::REVIEW;
            $data->save();
        } catch (Exception $e) {
            return response()->json($e, 400);
        }

        return response()->json(["status" => "OK"], 200);
    }

    public function input($id,Request $request){
        $data = Dokumen::findOrFail($id);
        // var_dump($request->all());
        if($request->get("nomorDMR") != null){
            $data->dokumenDmr()->save(new DokumenDmr(["nomor_dmr" => $request->get("nomorDMR")]));
            $this->dokumenSubmit($id);
        }

        echo response()->json(["status"=>"OK",200]);
    }

    public function get(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $auth_role = $user->role->role_name;

        switch($auth_role){
            case RoleConst::PBJ:
                $data = Dokumen::where(function ($query) use ($user) {
                    return $query->where("posisi_user_id", $user->id)->orWhere("created_by_user_id", $user->id)->orWhere("state_document",3);
                });
            break;
            case RoleConst::KEUANGAN:
                $data = Dokumen::where(function ($query) use ($user) {
                    return $query->where("posisi_user_id", $user->id)->orWhere("created_by_user_id", $user->id)->orWhere("state_document",2);
                });
            break;
            default:
                $data = Dokumen::where(function ($query) use ($user) {
                    return $query->where("posisi_user_id", $user->id)->orWhere("created_by_user_id", $user->id);
                });

        }
        
        if ($request->term && $request->term != "") {
            $term = $request->term;
            $data =  $data->whereHas("pengadaan", function ($query) use ($term) {
                return $query->where("judul_pengadaan", "LIKE", "%" . $term . "%");
            });
        }


        foreach ($this->filterableAttr as $filter) {
            if ($request->get($filter) && count($request->get($filter)) != 0) {
                $status_pengadaans = $request->get($filter);
                if (!in_array(null, $status_pengadaans)) {
                    $data = $data->whereIn($filter, $status_pengadaans);
                }
            }
        }
        $data = $data->where("status_dokumen_id","!=",StatusDokumenConst::APPROVE);
        return ResourcesDokumen::collection($data->get());
        // return response()->json($dokumens,200);
    }
}
