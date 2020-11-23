<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class PbjHelper
{
    public static function get_username()
    {
        return "Cak";
    }

    public static function sendNotification($notification)
    {

        $user = $notification->user;
        $token_list = $user->fcmToken->pluck("token")->toArray();
    
        fcm()
            ->to($token_list) // $recipients must an array
            ->priority('normal')
            ->timeToLive(0)
            ->data([  
                'data' => $notification->data->toArray(),
            ])
            ->notification([
                'title' => $notification->title,
                'body' => $notification->body,
            ])
            ->send();
    }

    public static function buildJudul($judul){
        if(strlen($judul) > 80){
            return substr($judul,0,80)."...";
        }
        return $judul;
    }
}
