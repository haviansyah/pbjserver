<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;

class DokumenController extends Controller
{
    public function store(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $pengadaan = Dokumen::create([
            'pengadaan_id' => $request->get("pengadaan_id"),
            'jenis_dokumen_id' => $request->get("jenis_dokumen_id"),
            'status_dokumen_id' => $request->get("status_dokumen_id"),
            'created_by_user_id' => $user->id,
            'posisi_user_id' => $user->id,
        ]);
        return response()->json(compact('pengadaan'),201);
    }
}
