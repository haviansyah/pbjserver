<?php

namespace App\Http\Controllers;

use App\Http\Resources\Notif;
use Illuminate\Http\Request;

use JWTAuth;

class NotifController extends Controller
{
    public function get(){
        $user = JWTAuth::parseToken()->authenticate();
        return Notif::collection($user->notification->sortByDesc('created_at'));
    }
}
