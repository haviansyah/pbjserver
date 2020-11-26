<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::middleware('auth')->group(function(){
    Route::get('/', [App\Http\Controllers\UserController::class, 'showUser'])->name('home');
    Route::post('admin/users', [App\Http\Controllers\UserController::class, 'register'])->name('store.user');
    Route::get('admin/users/delete/{id}', [App\Http\Controllers\UserController::class, 'deleteUser'])->name('delete.user');
    Route::get('admin/step/', [App\Http\Controllers\DokumenController::class, 'showStep'])->name('step.all');

});

Route::get("/test",function(Request $request){
    $dat = "11";
    $arr = explode("-",$dat);
    var_dump($arr);
});

