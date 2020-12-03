<?php

namespace App\Http\Controllers;

use App\Helpers\PbjHelper;
use App\Http\Resources\Pengadaan as ResourcesPengadaan;
use App\Models\Notification;
use App\Models\Pengadaan;
use App\Models\StatusPengadaan;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use JenisDokumenConst;
use JWTAuth;
use MetodePengadaanConst;
use RoleConst;
use StateDocumentConst;
use StatusDokumenConst;
use StatusPengadaanConst;
use TypeNotificationConst;
use UserIdConst;

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


    public function admin_index(){
        $data = Pengadaan::all();
        return view("pengadaan",compact("data"));
    }

    public function admin_detail($id){
        $data = Pengadaan::findOrFail($id);
        return view("detailpengadaan",compact("data"));
    }

    public function admin_delete($id){
        $data = Pengadaan::findOrFail($id);
        try{
            $data->delete();
            return response("OK",200);
        }catch(Exception $e){
            return response("NOT OK",400);
        }
        
        
        
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
            case RoleConst::ADMIN:
            case RoleConst::PBJ:
            case RoleConst::KEUANGAN:
                $data = Pengadaan::whereNotNull('id');
            break;
            case RoleConst::RENDAL:
                $data = Pengadaan::where("created_by_user_id",$user->id);
            break;
            case RoleConst::MANAGERBIDANG:
                $bidang_id = $user->Bidang->id;
                $data = Pengadaan::whereHas("dokumen",function($query) use($user){
                    return $query->where("posisi_user_id",$user->id);
                })->orWhere("direksi_pengadaan_id",$bidang_id);
            break;
            default:
                $data = Pengadaan::whereHas("dokumen",function($query) use($user){
                    return $query->where("posisi_user_id",$user->id);
                });
        }

        if($request->term && $request->term != "" ){
            $term = $request->term;
            $data = $data->where("judul_pengadaan","like","%".$term."%");
        }

        foreach($this->filterableAttr as $filter){
            if($request->get($filter) && count($request->get($filter)) != 0){
                $status_pengadaans = $request->get($filter);
                if(!in_array(null,$status_pengadaans)){
                    $data = $data->whereIn($filter,$status_pengadaans);
                }
            }
        }
        

        return ResourcesPengadaan::collection($data->get());
    }

    public function setPengadaan($id,$metode){
        $pengadaan = Pengadaan::findOrFail($id);
        $pengadaan->metode_pengadaan_id = (int)$metode;
        $pengadaan->confirmed_at = new DateTime();
        $pengadaan->status_pengadaan_id = StatusPengadaanConst::DPHPS;
        $pengadaan->save();
    }

    public function lanjutPengadaan($id){
        $pengadaan = Pengadaan::findOrFail($id);
        $statusPengadaan = $pengadaan->status_pengadaan_id;
        $metodePengadaan = $pengadaan->metode_pengadaan_id;

        if($statusPengadaan == StatusPengadaanConst::DPHPS){
            if($metodePengadaan == MetodePengadaanConst::PENGADAAN_LANGSUNG){
                $next_status = StatusPengadaanConst::PPH;
            }else{
                $next_status = StatusPengadaanConst::AANWIZJING;
            }
        }

        if($statusPengadaan == StatusPengadaanConst::AANWIZJING){
            $next_status = StatusPengadaanConst::PPH;
        }

        if($statusPengadaan == StatusPengadaanConst::PPH){
            if($metodePengadaan != MetodePengadaanConst::PENGADAAN_LANGSUNG){
                $next_status = StatusPengadaanConst::SKP;
            }else{
                $next_status = StatusPengadaanConst::KONTRAK; 
            }
        }

        if($statusPengadaan == StatusPengadaanConst::SKP){
            $next_status = StatusPengadaanConst::KONTRAK;
        }

        $pengadaan->status_pengadaan_id = $next_status;
        $pengadaan->save();        
    }

    public function kontrak($id,Request $request){
        $pengadaan = Pengadaan::findOrFail($id);
        $nomor_kontrak = $request->no;
        $tanggal_kontrak = $request->tgl;

        $dateFromString =date_create_from_format("d/m/Y",$tanggal_kontrak);
        $dateToSQL = date_format($dateFromString,"Y-m-d");

        $pengadaan->nomor_kontrak = $nomor_kontrak;
        $pengadaan->tanggal_selesai_kontrak = $dateToSQL;
        $pengadaan->status_pengadaan_id = StatusPengadaanConst::KONTRAK;
        $pengadaan->save();
    }


    public function lanjutPBJ($id){

        try{
            $pengadaan = Pengadaan::findOrFail($id);

            $tor = $pengadaan->dokumen->where("jenis_dokumen_id", JenisDokumenConst::TOR)->first();
            $dmr = $pengadaan->dokumen->where("jenis_dokumen_id", JenisDokumenConst::DMR)->first();
            $pr = $pengadaan->dokumen->where("jenis_dokumen_id", JenisDokumenConst::PR)->first();

            $alldoc = [$tor, $dmr, $pr];

            $pengadaan->state_pengadaan = 2;
            $pengadaan->save();

            foreach ($alldoc as $doc) {
                $step_dokumen = $doc->step;

                $step_next = $step_dokumen + 1;
                $doc->last_step = $doc->step;
                $doc->step = $step_next;
                $doc->posisi_user_id = 22;
                $doc->state_document = 3;
                $doc->status_dokumen_id = StatusDokumenConst::MASUK;
                $doc->save();
            }

            $notifBody = "Dokumen Masuk Dari MADM : ".PbjHelper::buildJudul($pengadaan->judul_pengadaan);
            $notif = new Notification([
                "user_id" => UserIdConst::PBJ,
                "title" => "Dokumen masuk dari MADM",
                "body" => $notifBody,
                "data_id" => $pengadaan->id,
                "data_type" => TypeNotificationConst::PENGADAAN
            ]);
            $notif->save();
            PbjHelper::buildDocumentActivity($doc, 1);
            
            PbjHelper::sendNotification($notif);
        }catch(Exception $e){
            return response()->json(["error"=>$e],400);

        }
        return response()->json(["status"=>"ok"],200);
    }

    public function lanjutMADM($id){

        try{
            $pengadaan = Pengadaan::findOrFail($id);

            $tor = $pengadaan->dokumen->where("jenis_dokumen_id", JenisDokumenConst::TOR)->first();
            $dmr = $pengadaan->dokumen->where("jenis_dokumen_id", JenisDokumenConst::DMR)->first();
            $pr = $pengadaan->dokumen->where("jenis_dokumen_id", JenisDokumenConst::PR)->first();

            $alldoc = [$tor, $dmr, $pr];

            $pengadaan->state_pengadaan = 3; //STATE PENGADAAN DI MADM
            $pengadaan->save();
            

            foreach ($alldoc as $doc) {
                $step_dokumen = $doc->step;

                $step_next = $step_dokumen + 1;
                $doc->last_step = $doc->step;
                $doc->step = $step_next;
                $doc->posisi_user_id = UserIdConst::MADM;
                $doc->state_document = StateDocumentConst::KEU;
                $doc->status_dokumen_id = StatusDokumenConst::MASUK;
                $doc->save();
                PbjHelper::buildDocumentActivity($doc, 1);
            }
            $notifBody = "Dokumen Masuk Dari Keuangan : ".PbjHelper::buildJudul($pengadaan->judul_pengadaan);
            $notif = new Notification([
                "user_id" => UserIdConst::MADM,
                "title" => "Dokumen masuk dari Keuangan",
                "body" => $notifBody,
                "data_id" => $pengadaan->id,
                "data_type" => TypeNotificationConst::PENGADAAN
            ]);
            $notif->save();
            
            PbjHelper::sendNotification($notif);

        }catch(Exception $e){
            return response()->json(["error"=>$e],400);

        }
        return response()->json(["status"=>"ok"],200);
    }

    // ROUTE `konfirmasi-madm`
    public function konfirmasiMADM($id){
        try{
            $pengadaan = Pengadaan::findOrFail($id);

            $tor = $pengadaan->dokumen->where("jenis_dokumen_id", JenisDokumenConst::TOR)->first();
            $dmr = $pengadaan->dokumen->where("jenis_dokumen_id", JenisDokumenConst::DMR)->first();
            $pr = $pengadaan->dokumen->where("jenis_dokumen_id", JenisDokumenConst::PR)->first();

            $alldoc = [$tor, $dmr, $pr];
            
            foreach ($alldoc as $doc) {
                $doc->status_dokumen_id = StatusDokumenConst::REVIEW;
                $doc->save();
                PbjHelper::buildDocumentActivity($doc,3);
            }
        }catch(Exception $e){
            return response()->json(["error"=>$e],400);

        }
        return response()->json(["status"=>"ok"],200);
    }
}
