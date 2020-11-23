<?php

namespace App\Http\Controllers;

use App\Http\Resources\User as ResourcesUser;
use App\Http\Resources\UserCollection;
use App\Models\FcmToken;
use App\Models\User;
use App\Models\UserManagerBidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use JWTAuth;
use RoleConstId;
use Tymon\JWTAuth\JWTAuth as JWTAuthJWTAuth;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'jabatan_id' => 'required|string',
            'role_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $jabatan_arr = explode("-", $request->get('jabatan_id'));


        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'jabatan_id' => $jabatan_arr[0],
            'role_id' => $request->get('role_id'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        if (count($jabatan_arr) > 1) {
            $user->managerBidang()->save(new UserManagerBidang(["bidang_id" => $jabatan_arr[1]]));
        }

        return redirect("/");
    }

    public function logout(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        
        $fcmToken = $request->get("token");

        $token = $user->fcmToken->where("token",$fcmToken);
        if(count($token) > 0 ){
            $token->each->delete();
        }
        
        \JWTAuth::manager()->invalidate(new \Tymon\JWTAuth\Token($request->bearerToken()), $forceForever = false);
        


        return response()->json(["status" => "OK"]);
    }

    public function getAuthenticatedUser()
    {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {

            return response()->json(['token_invalid'], 400);
        } catch (JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());
        }
        $user_role = $user->role;
        return response()->json([
            "name" => $user->name,
            "email" => $user->email,
            "jabatan" => $user->jabatan->jabatan_name,
            "role" => $user->role->role_name
        ]);
    }


    public function getAllUser(Request $request)
    {
        $term = $request->term;
        $data = User::paginate();
        if ($term) {
            $data = User::where("name", "like", "%" . $term . "%")->paginate();
        }
        return new UserCollection($data);
    }

    public function getUser($id)
    {
        $data = User::findOrFail($id);

        $result = [
            "name" => $data->name,
            "email" => $data->email,
            "role_id" => $data->role_id,
            "jabatan_id" => $data->role_id != RoleConstId::MANAGERBIDANG ? $data->jabatan_id : $data->jabatan_id . "-" . $data->managerBidang->bidang_id,
        ];
        return response()->json($result, 200);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect("/");
    }

    public function updateUser($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'string|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = User::findOrFail($id);

        $jabatan_arr = explode("-", $request->get('jabatan_id'));

        $user->name = $request->name;
        $user->email = $request->email;
        $user->jabatan_id = $jabatan_arr[0];
        $user->role_id = $request->role_id;

        if ($request->password) {
            $user->password = Hash::make($request->get('password'));
        }

        if (count($jabatan_arr) > 1) {
            $user->managerBidang()->update(["bidang_id" => $jabatan_arr[1]]);
        }

        $user->save();
        return response()->json(["status" => "OK"], 200);
    }

    // Admin Page

    public function showUser()
    {
        $data = User::all();

        return view("home", compact("data"));
    }


    public function addFcmToken(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $token = $request->get("token");

        var_dump($request->all());
        $check_token = $user->fcmToken->where("token",$token);
        if(count($check_token) > 0){
            return "Ada";
        }else{
            $user->fcmToken()->save(new FcmToken(["token" => $token]));
            return "Tidak Ada";
        }
    }
}
