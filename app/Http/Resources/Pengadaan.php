<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        return [
            'id' => $this->id,
            'judul_pengadaan' => $this->judul_pengadaan,
            'jenis_pengadaan' => $this->jenisPengadaan->jenis_pengadaan,
            'status_pengadaan' => $this->statusPengadaan->status_pengadaan,
            'jenis_anggaran' => $this->jenisAnggaran->jenis_anggaran,
            'direksi_pekerjaan' => $this->direksiPengadaan->direksi_pengadaan,
            'created_by' => $this->createdBy->name
        ];
    }
}
