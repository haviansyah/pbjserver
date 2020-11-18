<?php

namespace App\Http\Controllers;

use App\Http\Resources\User as ResourcesUser;
use App\Http\Resources\UserCollection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use JWTAuth;
use Tymon\JWTAuth\JWTAuth as JWTAuthJWTAuth;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
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
            'jabatan_id' => 'required|integer',
            'role_id' => 'required|integer',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'jabatan_id' => $request->get('jabatan_id'),
            'role_id' => $request->get('role_id'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
    }

    public function logout($id){
        $tokenString = JWTAuth::fromUser(User::findOrFail($id));
        \JWTAuth::manager()->invalidate(new \Tymon\JWTAuth\Token($tokenString), $forceForever = false);
        return response()->json(["status"=> "OK"]);
    }

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

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


    public function getAllUser(Request $request){
        $term = $request->term;
        $data = User::paginate();
        if($term){
            $data = User::where("name","like","%".$term."%")->paginate();
        }
        return new UserCollection($data);
    }

    public function getUser($id){
        $data = User::findOrFail($id);
        return new ResourcesUser($data);
    }

    public function deleteUser($id){
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(["status"=> "OK"]);
    }

    public function updateUser($id, Request $request){
        $validator = Validator::make($request->all(), [
            'password' => 'string|min:6|confirmed'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $user = User::findOrFail($id);
        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        $user->jabatan_id = $request->jabatan_id ?? $user->jabatan_id;
        $user->role_id = $request->role_id ?? $user->role_id;

        if($request->password){
            $user->password = Hash::make($request->get('password'));
        }
        $user->save();
        return response()->json(["status"=>"OK"],200);
    }
}
