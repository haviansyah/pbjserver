<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use JWTAuth;
use RoleConstId;
use StatusDokumenConst;

class DokumenOnly extends JsonResource
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

        return [
            'id' => $this->id,
            'posisi_jabatan' => $posisi_jabatan,
            'posisi_user' => new User($this->posisi),
            'status_dokumen' => $this->statusDokumen,
            'jenis_dokumen' => $this->jenisDokumen,
            'status_dokumen_ket' => $keterangan_status_dokumen,
            'self' => $this->when($user->id == $this->posisi->id, true, false)
        ];
    }
}