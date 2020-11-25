<?php

namespace App\Helpers;

use App\Models\ActivityDokumen;
use App\Models\Step;
use Illuminate\Support\Facades\DB;
use RoleConstId;
use StatusDokumenConst;

class PbjHelper
{
    public static function get_username()
    {
        return "Cak";
    }

    public static function sendNotification($notification)
    {

        $user = $notification->user;
        $token_list = $user->fcmToken->pluck("token")->toArray();
    
        fcm()
            ->to($token_list) // $recipients must an array
            ->priority('normal')
            ->timeToLive(0)
            ->data([  
                'data' => $notification->data->toArray(),
            ])
            ->notification([
                'title' => $notification->title,
                'body' => $notification->body,
            ])
            ->send();
    }

    public static function buildJudul($judul){
        if(strlen($judul) > 80){
            return substr($judul,0,80)."...";
        }
        return $judul;
    }

    public static function buildDocumentActivity($dokumen, $type){

        switch($type){
            case 1: 
                $keterangan = "Diteruskan / Approved";
            break;
            case 2:
                $keterangan = "Revisi";
            break;
            case 3:
                $keterangan = "Konfirmasi";
            break;
            case 4:
                $keterangan = "Dikembalikan";
            break;   
        }

        $activity = new ActivityDokumen([
            "dokumen_id" => $dokumen->id,
            "from_user_id" => $dokumen->posisi_user_id,
            "to_user_id" => $type == 4 ? $dokumen->posisi_user_id : $dokumen->prev_user_id,
            "state" =>$dokumen->state_document,
            "step" => $dokumen->step,
            "keterangan" => $keterangan,
        ]);
        $activity->save();
    }

    public static function buildKeteranganDokumen($dokumen){
        $posisi_role_id = $dokumen->posisi->role->id;
        $posisi_jabatan = $dokumen->posisi->jabatan->jabatan_name;

        $jenis_dokumen = $dokumen->jenis_dokumen_id;
        $step_dokumen = $dokumen->step;


        $status = $dokumen->statusDokumen;
        switch ($status->id) {
            case StatusDokumenConst::BARU:
                $keterangan_status_dokumen = "Dokumen Baru";
                break;
            case StatusDokumenConst::MASUK:
                if ($step_dokumen == 0) {
                    $keterangan_status_dokumen = "Direvisi oleh " . $posisi_jabatan;
                } else {
                    $keterangan_status_dokumen = Step::where("jenis_dokumen_id", $jenis_dokumen)->where("step", $step_dokumen-1)->first()->step_name;
                }
                break;
            case StatusDokumenConst::REVIEW:
                $keterangan_status_dokumen = Step::where("jenis_dokumen_id", $jenis_dokumen)->where("step", $step_dokumen)->first()->step_name;
                if ($posisi_role_id == RoleConstId::RENDAL) {
                    $keterangan_status_dokumen = "Direvisi oleh " . $posisi_jabatan;
                }
                break;
            case StatusDokumenConst::APPROVE:
                $keterangan_status_dokumen = "Approved PBJ";
                break;
        }

        return $keterangan_status_dokumen;

    }

    public static function convertToDaysHours($time){
        $time = abs($time);
        $days = floor($time / 24);
        $hours = ($time % 24);
        $format = "%d Hari".($hours != 0 ? " %02d Jam" : "");
        return sprintf($format, $days, $hours);
    }
}
