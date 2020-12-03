<?php

use App\Http\Controllers\DokumenController;
use App\Http\Controllers\NotifController;
use App\Http\Controllers\PengadaanController;
use App\Http\Controllers\UserController;
use App\Models\Dokumen;
use App\Models\FcmToken;
use App\Models\Notification;
use App\Models\Step;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

function multiroles($roles){
    $jml = count($roles);
    $return = "";
    foreach($roles as $key=>$role){
        $return .= $role;
        if($key < $jml-1){
            $return .= "|";
        }
    }
    return $return;
}

Route::post('login', [UserController::class,'login']);
Route::get('me', [UserController::class,'getAuthenticatedUser']);

// User Api Endpoint
Route::get('users', [UserController::class,'getAllUser']);    
Route::post('users', [UserController::class,'register']);
Route::get('users/{id}', [UserController::class,'getUser']);
Route::put('users/{id}', [UserController::class,'updateUser']);
Route::delete('users/{id}', [UserController::class,'deleteUser']);

Route::post('users/logout', [UserController::class,'logout']);

Route::post('token', [UserController::class,'addFcmToken']);


Route::get('notif', [NotifController::class,'get']);

Route::middleware(['jwt.verify:'.RoleConst::ADMIN])->group(function () {
    

    //Pengadaan Api Endpoint
    Route::put('pengadaans/{id}',[PengadaanController::class,"update"]);
    Route::delete('pengadaans/{id}',[PengadaanController::class,"delete"]);

});

Route::middleware(['jwt.verify:'.RoleConst::RENDAL])->group(function () {
    Route::post('pengadaans',[PengadaanController::class,'store']);
    Route::post('dokumens',[DokumenController::class,'store']);
        
});

Route::middleware(['jwt.verify:'.multiroles([RoleConst::ADMIN,RoleConst::RENDAL,RoleConst::MANAGERBIDANG,RoleConst::SEKERTARISGM,RoleConst::LIM,RoleConst::PBJ,RoleConst::KEUANGAN,RoleConst::AMUINVENTORY])])->group(function () {
    Route::get('pengadaans',[PengadaanController::class,'index']);

    Route::get('pengadaans/{id}/lanjut-pbj',[PengadaanController::class,'lanjutPBJ']);

    Route::get('pengadaans/{id}/lanjut-madm',[PengadaanController::class,'lanjutMADM']);
    
    Route::get('pengadaans/{id}/konfirmasi-madm',[PengadaanController::class,'konfirmasiMADM']);


    Route::get('pengadaans/{id}/metode/{metode}',[PengadaanController::class,'setPengadaan']);
    Route::get('pengadaans/{id}/lanjut',[PengadaanController::class,'lanjutPengadaan']);
    Route::get('pengadaans/{id}/kontrak',[PengadaanController::class,'kontrak']);

    Route::get('dokumens',[DokumenController::class,'get']);
    Route::get('dokumens/{id}',[DokumenController::class,'getOne']);
    Route::get('dokumens/{id}/submit',[DokumenController::class,'dokumenSubmit']);
    Route::get('dokumens/{id}/teruskan',[DokumenController::class,'dokumenSubmit']);
    
    Route::get('dokumens/{id}/approve',[DokumenController::class,'dokumenSubmit']);
    
    Route::get('dokumens/{id}/revise',[DokumenController::class,'dokumenRevise']);

    Route::get('dokumens/{id}/konfirmasi',[DokumenController::class,'dokumenKonfirmasi']);
    Route::get('dokumens/{id}/tidak',[DokumenController::class,'dokumenBack']);


    Route::get('dokumens/{id}/input',[DokumenController::class,'input']);
});

Route::get("test", function(Request $request){

    // $token = FcmToken::all("token")->pluck('token');
    // $recipients = $token->toArray();


    // fcm()
    // ->to($recipients) // $recipients must an array
    // ->priority('normal')
    // ->timeToLive(0)
    // ->data([
    //     'title' => 'Test FCM',
    //     'body' => 'This is a test of FCM',
    // ])
    // ->notification([
    //     'title' => 'Test FCM',
    //     'body' => 'This is a test of FCM',
    // ])
    // ->send();
    $notif = Notification::first();
    
    return PbjHelper::sendNotification($notif);

});


Route::post("step/{id}",[DokumenController::class,"editStep"]);

Route::delete('admin/dokumen/{id}', [App\Http\Controllers\DokumenController::class, 'admin_delete'])->name('dokumen.delete');
Route::delete('admin/pengadaan/{id}', [App\Http\Controllers\PengadaanController::class, 'admin_delete'])->name('pengadaan.delete');


Route::get("havi",function(Request $request){
   $dokumen = Dokumen::all()->first();
    dd($dokumen->stepModel);
});
