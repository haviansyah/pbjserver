<?php

namespace App\Http\Resources;

use App\Helpers\PbjHelper;
use App\Models\StatusPengadaan;
use App\Models\Step;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use JenisDokumenConst;
use StatusDokumenConst;
use JWTAuth;
use MetodePengadaanConst;
use RoleConst;
use RoleConstId;
use StatusPengadaanConst;
use UserIdConst;

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

            // KEUANGAN =========================================================================
            $status_keu  = true;
            foreach ($alldoc as $doc) {
                $status =  $doc->status_dokumen_id;
                $posisi =  $doc->posisi_user_id;
                if ($posisi != UserIdConst::KEUANGAN || $status != StatusDokumenConst::KEU) {
                    $status_keu = false;
                }
            }
            if ($status_keu & ($user->id == UserIdConst::KEUANGAN)) {
                $action = url("api/pengadaans/{$this->id}/lanjut-madm");
            }
            // KEUANGAN =========================================================================

            // MADM =========================================================================
            $status_madm  = true;
            foreach ($alldoc as $doc) {
                $status =  $doc->status_dokumen_id;
                $posisi =  $doc->posisi_user_id;
                if ($status != StatusDokumenConst::REVIEW) {
                    $status_madm = false;
                }
            }
            
            if ($user->id == UserIdConst::MADM && $this->state_pengadaan == 3) {
                $action = url("api/pengadaans/{$this->id}/konfirmasi-madm");

                if($status_madm){
                    $action = url("api/pengadaans/{$this->id}/lanjut-pbj");
                }
            }
            // MADM =========================================================================

            // PBJ =========================================================================
            $status_pbj  = true;
            foreach ($alldoc as $doc) {
                $status =  $doc->status_dokumen_id;
                $posisi =  $doc->posisi_user_id;
                if ($status != StatusDokumenConst::APPROVE) {
                    $status_pbj = false;
                }
            }

            if ($status_pbj & ($user->id == 22)) {
                $action = url("api/pengadaans/{$this->id}/metode");
            }

            if ($this->status_pengadaan_id != StatusPengadaanConst::PRAPENGADAAN && ($user->id == UserIdConst::PBJ)) {
                $action = url("api/pengadaans/{$this->id}/lanjut");
            }

            // KALAU PPH KALAU LELANG SKP DULU KALAU ENGGA LANJUT

            if ($this->status_pengadaan_id == StatusPengadaanConst::PPH && ($user->id == UserIdConst::PBJ)) {
                
                if($this->metode_pengadaan_id == MetodePengadaanConst::PENGADAAN_LANGSUNG){
                    $action = url("api/pengadaans/{$this->id}/kontrak?no=");
                }else{
                    $action = url("api/pengadaans/{$this->id}/lanjut");
                }
                
                
            }
            

            if ($this->status_pengadaan_id == StatusPengadaanConst::SKP && ($user->id == UserIdConst::PBJ)) {
                $action = url("api/pengadaans/{$this->id}/kontrak?no=");
            }

            if ($this->status_pengadaan_id == StatusPengadaanConst::KONTRAK) {
                $action = null;
            }

            // PBJ =========================================================================
        }
        $metode_pengadaan = null;
        $days_remaining = null;
        $time_remaining = 0;
        $sla = null;
        if ($this->metodePengadaan != null) {
            $metode_pengadaan = $this->metodePengadaan->metode_pengadaan;

            $sla =  Step::where(function($q) use($metode_pengadaan){
                return $q->where("jenis_dokumen_id",4)->where("step",$this->metodePengadaan->id);
            })->get()->first()->sla;
            $confirmed_at = $this->confirmed_at?? $this->created_at;
            $elapsed_time = $confirmed_at->diffInHours(new Carbon()); 
            $time_remaining = $sla - $elapsed_time;
            $days_remaining = ($time_remaining < 1 ? "-" : "" ).PbjHelper::convertToDaysHours($time_remaining) ; 
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
            "tanggal_selesai_kontrak" => $this->tanggal_selesai_kontrak ? $this->tanggal_selesai_kontrak->format("d M Y") : "-",
            "aksi" => $action,
            "sla" => $sla,
            "time_remaining" => $time_remaining,
            "days_remaining" => $days_remaining
        ];
    }
}
