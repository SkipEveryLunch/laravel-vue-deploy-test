<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Exception;


class AuthController extends Controller
{
    public function register(Request $req){
        try{
            $user = User::create([
                "name"=>$req->input("name"),
                "email"=>$req->input("email"),
                "password"=>Hash::make($req->input("password")),
            ]);
            $jwt = $user->createToken("token")->plainTextToken;
            //トークンの変数名を「jwt」に変えたよ
            $cookie = cookie("jwt",$jwt, 60 * 24);
            return response([
                "jwt"=>$jwt,
                "user"=>$user
            ])->withCookie($cookie);
        }catch(Exception $e){
            if($e->errorInfo[0]==="23000"){
                return response()->json([
                    "message"=>"this email address is already used.",
                    "error"=>$e->errorInfo[2]
                ],Response::HTTP_CONFLICT );
            }else{
                return response()->json([
                    "message"=>"error occurred during creating a new user"
                    // "error"=>$e->errorInfo
                ],Response::HTTP_BAD_REQUEST );
            }
        } 
    }
    public function login(Request $req){
        if(!Auth::attempt($req->only("email","password"))){
            return response([
                "error" => "invalid credentials"
            ],Response::HTTP_UNAUTHORIZED);
        }
        $user = Auth::user();
        $jwt = $user->createToken("token")->plainTextToken;
//トークンの変数名を「jwt」に変えたよ
        $cookie = cookie(
            "jwt",$jwt, 60 * 24
        );
//cookie関数の引数は
//「トークン名、トークンにする値、トークンの寿命(分単位)」だよ
        return response([
            "jwt"=>$jwt,
            "user"=>$user
        ])->withCookie($cookie);
//withCookieでクッキーを添付できるよ
    }
    public function user(Request $req){
        return $req->user();
    }
    public function logout(){
        $cookie = Cookie::forget("jwt");
        return response([
            "message" => "success"
        ])->withCookie($cookie);
    }
}
