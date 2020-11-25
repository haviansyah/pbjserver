<?php

namespace App\Http\Resources;

use App\Helpers\PbjHelper;
use App\Models\StatusDokumen;
use App\Models\Step;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use JenisDokumenConst;
use JWTAuth;
use RoleConstId;
use StatusDokumenConst;

class Dokumen extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $keterangan_status_dokumen = PbjHelper::buildKeteranganDokumen($this);
        $posisi_role_id = $this->posisi->role->id;
        $posisi_jabatan = $this->posisi->jabatan->jabatan_name;
        
        

        if ($posisi_role_id == RoleConstId::MANAGERBIDANG) {
            $bidang = $this->posisi->managerBidang->bidang->direksi_pengadaan;
            $posisi_jabatan = $this->posisi->jabatan->jabatan_name . " " . $bidang;
        }
        
        $nomor_dmr = null;
        $nomor_pr = null;
        if ($this->jenisDokumen->id == JenisDokumenConst::DMR) {
            if ($this->dokumenDmr) {
                $nomor_dmr = $this->dokumenDmr->nomor_dmr;
            }
        }

        if ($this->jenisDokumen->id == JenisDokumenConst::PR) {
            if ($this->dokumenPr) {
                $nomor_pr = $this->dokumenPr->nomor_pr;
            }
        }


        $sla =  $this->StepModel->sla;
        $confirmed_at = $this->confirmed_at?? $this->created_at;
        $elapsed_time = $confirmed_at->diffInHours(new Carbon()); 
        $time_remaining = $sla - $elapsed_time;
        $days_remaining = floor($time_remaining / 24);     


        return [
            'id' => $this->id,
            'pengadaan' => new Pengadaan($this->pengadaan),
            'posisi_jabatan' => $posisi_jabatan,
            'posisi_user' => new User($this->posisi),
            'status_dokumen' => $this->statusDokumen,
            'jenis_dokumen' => $this->jenisDokumen,
            'status_dokumen_ket' => $keterangan_status_dokumen,
            "nomor_dmr" => $nomor_dmr,
            "nomor_pr" => $nomor_pr,
            'self' => $this->when($user->id == $this->posisi->id, true, false),
            'confirmed_at' => $confirmed_at->format('d M Y h:i:s A'),
            'sla' => $sla,
            'elapsed_time' => $elapsed_time,
            'time_remaining' => $time_remaining,
            'days_remaining' => ($time_remaining < 1 ? "-" : "" ).PbjHelper::convertToDaysHours($time_remaining)
        ];
    }
}
