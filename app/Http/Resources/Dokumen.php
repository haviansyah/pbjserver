<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        return [
            'id' => $this->id,
            'pengadaan' => new Pengadaan($this->pengadaan),
            'posisi_jabatan' => $this->posisi->jabatan->jabatan_name,
            'posisi_user' => new User($this->posisi),
            'status_dokumen' => $this->statusDokumen,
            'jenis_dokumen' => $this->jenisDokumen,
        ];
    }
}
