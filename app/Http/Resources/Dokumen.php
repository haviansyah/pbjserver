<?php

namespace App\Http\Resources;

use App\Models\StatusDokumen;
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

        $keterangan_status_dokumen = "";
        $posisi_role_id = $this->posisi->role->id;
        $posisi_jabatan = $this->posisi->jabatan->jabatan_name;

       

        if($posisi_role_id == RoleConstId::MANAGERBIDANG){
            $bidang = $this->posisi->managerBidang->bidang->direksi_pengadaan;
            $posisi_jabatan = $this->posisi->jabatan->jabatan_name." ".$bidang;
        }

        $status = $this->statusDokumen;
        switch ($status->id) {
            case 1:
                $keterangan_status_dokumen = "Dokumen Baru";
                break;
            case 2:
                $keterangan_status_dokumen = "Diserahkan ke " . $posisi_jabatan;
                break;
            case 3:
                $keterangan_status_dokumen = "Direview oleh " . $posisi_jabatan;
                if($posisi_role_id == RoleConstId::RENDAL){
                    $keterangan_status_dokumen = "Direvisi oleh " . $posisi_jabatan;
                }
                break;
            case StatusDokumenConst::APPROVE:
                $keterangan_status_dokumen = "Approved PBJ";
            break;
        }

        $nomor_dmr = null;
        $nomor_pr = null;
        if($this->jenisDokumen->id == JenisDokumenConst::DMR){
            if($this->dokumenDmr){
                $nomor_dmr = $this->dokumenDmr->nomor_dmr;
            }
        }

        if($this->jenisDokumen->id == JenisDokumenConst::PR){
            if($this->dokumenPr){
                $nomor_pr = $this->dokumenPr->nomor_pr;
            }
        }

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
            'self' => $this->when($user->id == $this->posisi->id, true, false)
        ];
    }
}

