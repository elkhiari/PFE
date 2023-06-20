<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Annonce;
use App\Models\User;
use Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
       $validator = Validator::make($request->all(), [
              'email' => 'required|email|unique:users',
              'password' => 'required|min:6',
              'sexe' => 'required',
              'telephone' => 'required',
              'username' => 'required',
              'prenom' => 'required',
              'nom' => 'required',
       ]);

        if ($validator->fails()) return response()->json(['error'=>$validator->errors()], 401);
        if ($request->hasFile('profile')){
            $file = $request->file('profile');
            $photo = time().'_profile.'.$file->extension();
            $file->move(public_path('/images/profile/'), $photo);
        }
        else{
            $photo = 'default.png';
        }
        $input = $request->all();
        $input['profile'] = $photo;
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $token = $user->createToken('auth_token');
        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user
        ], 200);
    }

    public function login()
{
    if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) { 
        $user = Auth::user(); 
        $token = $user->createToken('auth_token')->accessToken;
        return response()->json([
            'token' => $token
        ], 200);       
    } else {
        return response()->json(['error' => 'Unauthorized. Token not provided.'], 401);
    }
}

    public function me()
    {
        return response()->json(Auth()->user(), 200);
    }

    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);
        $annonces = Annonce::where('user', $id)->get();
        $user->annonces = $annonces;
        return response()->json($user, 200);
    }



    public function update(Request $request)
    {
        $id = Auth::user()->id;
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'internal server error'], 500);
        $validator = Validator::make($request->all(), [
            'email' => 'email|unique:users,email,',
            'password' => 'min:6',
            'old_password' => 'required_with:password',
            'profile' => 'mimes:jpeg,jpg,png,gif|max:10000'
        ]);
        if ($validator->fails()) return response()->json($validator->errors(), 400);
        if (isset($request->password)) {
            if (!Hash::check($request->old_password, $user->password)) return response()->json(['error' => 'Old password does not match'], 400);
            $user->password = bcrypt($request->password);
        }
        if ($request->hasFile('profile')) {
            $path = public_path('/images/profile/'.$user->profile);
            if (file__exists($path)) unlink($path);
            $file = $request->file('profile');
            $photo = time() . '_profile.' . $file->extension();
            $file->move(public_path('/images/profile/'), $photo);
            $user->profile = $photo;
        }
        if (isset($request->email)) $user->email = $request->email;
        if (isset($request->sexe)) $user->sexe = $request->sexe;
        if (isset($request->telephone)) $user->telephone = $request->telephone;
        if (isset($request->username)) $user->username = $request->username;
        if (isset($request->prenom)) $user->prenom = $request->prenom;
        if (isset($request->nom)) $user->nom = $request->nom;
        $user->save();
        return response()->json($user, 200);
    }


    public function delete_user($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);
        if (Auth::user()->role != 'admin' && Auth::user()->id != $user->id) return response()->json(['error' => 'Unauthorized. Token not provided.'], 401);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
