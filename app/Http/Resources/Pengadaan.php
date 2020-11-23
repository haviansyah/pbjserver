<?php

namespace App\Http\Resources;

use App\Models\StatusPengadaan;
use Illuminate\Http\Resources\Json\JsonResource;
use JenisDokumenConst;
use StatusDokumenConst;
use JWTAuth;
use RoleConst;
use RoleConstId;
use StatusPengadaanConst;

class Pengadaan extends JsonResource
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
        $user_role = $user->role->id;
        $tor = $this->dokumen->where("jenis_dokumen_id", JenisDokumenConst::TOR)->first();
        $dmr = $this->dokumen->where("jenis_dokumen_id", JenisDokumenConst::DMR)->first();
        $pr = $this->dokumen->where("jenis_dokumen_id", JenisDokumenConst::PR)->first();

        $alldoc = [$tor, $dmr, $pr];

        $action = null;

        $lengkap  = false;
        // Check Apakah Sudah Lengkap
        if (!in_array(null, $alldoc)) {
            // Get All Status;

            // MADM
            $status_madm  = true;
            foreach ($alldoc as $doc) {
                $status =  $doc->status_dokumen_id;
                $posisi =  $doc->posisi_user_id;
                if ($posisi != 20 || $status != StatusDokumenConst::REVIEW) {
                    $status_madm = false;
                }
            }
            // $action = $user->role;
            if($status_madm & ($user->id == 20)){
                $action = url("api/pengadaans/{$this->id}/lanjut-pbj");
            }


            // PBJ
            $status_pbj  = true;
            foreach ($alldoc as $doc) {
                $status =  $doc->status_dokumen_id;
                $posisi =  $doc->posisi_user_id;
                if ($status != StatusDokumenConst::APPROVE) {
                    $status_pbj = false;
                }
            }

            if($status_pbj & ($user->id == 22)){
                $action = url("api/pengadaans/{$this->id}/metode");
            }
            
            if($this->status_pengadaan_id != StatusPengadaanConst::PRAPENGADAAN && ($user->id == 22)){
                $action = url("api/pengadaans/{$this->id}/lanjut");
            }


            

            if(($this->status_pengadaan_id == StatusPengadaanConst::SKP || $this->status_pengadaan_id == StatusPengadaanConst::PPH ) && ($user->id == 22)){
                $action = url("api/pengadaans/{$this->id}/kontrak?no=");
            }

            if($this->status_pengadaan_id == StatusPengadaanConst::KONTRAK ){
                $action = null;
            }
        }
        $metode_pengadaan = null;
        if($this->metodePengadaan != null){
            $metode_pengadaan = $this->metodePengadaan->metode_pengadaan;
        }

        return [
            'id' => $this->id,
            'judul_pengadaan' => $this->judul_pengadaan,
            'jenis_pengadaan' => $this->jenisPengadaan->jenis_pengadaan,
            'status_pengadaan' => $this->statusPengadaan->status_pengadaan,
            'jenis_anggaran' => $this->jenisAnggaran->jenis_anggaran,
            'direksi_pekerjaan' => $this->direksiPengadaan->direksi_pengadaan,
            'created_by' => $this->createdBy->name,
            'dokumens' => [
                new DokumenOnly($tor),
                new DokumenOnly($dmr),
                new DokumenOnly($pr),
            ],
            "metode_pengadaan" => $metode_pengadaan,
            "nomor_kontrak" => $this->nomor_kontrak,
            "aksi" => $action
        ];
    }
}
