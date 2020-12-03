<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityDocument extends JsonResource
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
            "from" => $this->to->name." (".$this->from->jabatan->jabatan_name.")",
            "keterangan" => $this->keterangan,
            "jam" => $this->created_at->format('H:i:s'),
            "step" => $this->StepModel->step_name
        ];
    }
}
