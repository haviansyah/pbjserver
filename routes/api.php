<?php
use App\Http\Controllers\PengadaanController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
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

Route::middleware(['jwt.verify:'.RoleConst::ADMIN])->group(function () {
    // User Api Endpoint
    Route::get('users', [UserController::class,'getAllUser']);    
    Route::post('users', [UserController::class,'register']);
    Route::get('users/{id}', [UserController::class,'getUser']);
    Route::put('users/{id}', [UserController::class,'updateUser']);
    Route::delete('users/{id}', [UserController::class,'deleteUser']);
    Route::get('users/logout/{id}', [UserController::class,'logout']);

    //Pengadaan Api Endpoint
    Route::put('pengadaans/{id}',[PengadaanController::class,"update"]);
    Route::delete('pengadaans/{id}',[PengadaanController::class,"delete"]);
});

Route::middleware(['jwt.verify:'.RoleConst::RENDAL])->group(function () {
    Route::post('pengadaans',[PengadaanController::class,'store']);
});

Route::middleware(['jwt.verify:'.multiroles([RoleConst::ADMIN,RoleConst::RENDAL,RoleConst::MANAGERBIDANG])])->group(function () {
    Route::get('pengadaans',[PengadaanController::class,'index']);
});

Route::get("test", function(Request $request){
    $user = User::find(11);
    dd($user->ManagerBidangName);
});