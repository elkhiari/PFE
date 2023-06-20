<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Annonce;
use Validator;

class AnnonceController extends Controller
{
    public function index()
    {
        $annonces = Annonce::where('validated',1)->with('categorie', 'ville', 'user')->get();
        foreach ($annonces as $annonce) {
            $annonce->images = json_decode($annonce->images);
        }
        if ($annonces->isEmpty()) return response()->json(['message' => 'No Annonce found'], 404);
        return response()->json($annonces);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required',
            'description' => 'required',
            'prix' => 'required',
            'ville' => 'required',
            'categorie' => 'required',
            'image1' => 'required|mimes:jpeg,jpg,png,gif|required|max:10000',
            'image2' => 'mimes:jpeg,jpg,png,gif|max:10000',
            'image3' => 'mimes:jpeg,jpg,png,gif|max:10000',
            'image4' => 'mimes:jpeg,jpg,png,gif|max:10000',
            'image5' => 'mimes:jpeg,jpg,png,gif|max:10000',

        ]);

        $user = Auth::guard('api')->user();
        if ($validator->fails()) return response()->json($validator->errors(), 400);
        $images = [];
        for ($i = 1; $i <= 5; $i++) {
            if ($request->hasFile('image' . $i)) {
                $image = $request->file('image' . $i);
                $imageName = time().'_image'.$i.'.'.$image->extension();
                $image->move(public_path('images/annonces'), $imageName);
                $images['image' . $i] = $imageName;
            }
        }
        if (!$user) return response()->json(['message' => 'token !!!!!!!!'], 404); 
        $annonce = new Annonce();
        $annonce->titre = $request->titre;
        $annonce->description = $request->description;
        $annonce->prix = $request->prix;
        $annonce->ville = $request->ville;
        $annonce->categorie = $request->categorie;
        $annonce->user = $user->id;
        $annonce->images = json_encode($images);
        $annonce->save();

        return response()->json($annonce, 201);
    }

    public function show($id)
    {
        $annonce = Annonce::with('categorie', 'ville', 'user')->find($id);
        if (!$annonce) {
            return response()->json(['message' => 'No Annonce found'], 404);
        }
        if ($annonce->validated == 0 && Auth::guard('api')->user()->id != $annonce->user && Auth::guard('api')->user()->role != 'admin') {
            return response()->json(['message' => 'Annonce not validated'], 404);
        }
        $annonce->load('categorie', 'ville', 'user');
        $annonce->images = json_decode($annonce->images);

        return response()->json($annonce);
    }

    public function update(Request $request, $id)
    {
        $annonce = Annonce::find($id);
        if (!$annonce) {
            return response()->json(['message' => 'No Annonce found'], 404);
        }
        $user = Auth::guard('api')->user();
        if ($user->id != $annonce->user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $validator = Validator::make($request->all(),[
            'titre' => '',
            'description' => '',
            'prix' => '',
            'ville' => '',
            'categorie' => '',
            'image1' => 'mimes:jpeg,jpg,png,gif|max:10000',
            'image2' => 'mimes:jpeg,jpg,png,gif|max:10000',
            'image3' => 'mimes:jpeg,jpg,png,gif|max:10000',
            'image4' => 'mimes:jpeg,jpg,png,gif|max:10000',
            'image5' => 'mimes:jpeg,jpg,png,gif|max:10000',
        ]);
        if($validator->fails()) return response()->json($validator->errors(), 400);
        $images = [];
        for ($i = 1; $i <= 5; $i++) {
            if ($request->hasFile('image' . $i)) {
                $images = json_decode($annonce->images, true);
                    if (isset($images['image'.$i])) {
                        $path = public_path('images/annonces/' . $images['image' . $i]);
                        if (file_exists($path)) unlink($path);
                    }
                $image = $request->file('image' . $i);
                $imageName = time().'_image'.$i.'.'.$image->extension();
                $image->move(public_path('images/annonces'), $imageName);
                $images['image' . $i] = $imageName;
            }
        }
        if ($request->titre) $annonce->titre = $request->titre;
        if ($request->description) $annonce->description = $request->description;
        if ($request->prix) $annonce->prix = $request->prix;
        if ($request->ville) $annonce->ville = $request->ville;
        if ($request->categorie) $annonce->categorie = $request->categorie;
        if ($images) $annonce->images = json_encode($images);
        $annonce->save();
        return response()->json($annonce);
    }

    public function getvalidateFalse()
    {
        $annonces = Annonce::where('validated',0)->with('categorie', 'ville', 'user')->get();
        foreach ($annonces as $annonce) {
            $annonce->images = json_decode($annonce->images);
        }
        if ($annonces->isEmpty()) return response()->json(['message' => 'No Annonce found'], 404);
        return response()->json($annonces);
    }

    public function setVlidateTrue($id)
    {
        $annonce = Annonce::find($id);
        if (!$annonce) {
            return response()->json(['message' => 'No Annonce found'], 404);
        }
        $annonce->validated = 1;
        $annonce->save();
        return response()->json($annonce);
    }

    public function destroy($id)
    {
        $annonce = Annonce::find($id);
        if (!$annonce) return response()->json(['message' => 'Annonce not found']);

        $user = Auth::guard('api')->user();
        if ($annonce->user == $user->id || $user->role == 'admin'){
            $images = json_decode($annonce->images, true);
            for ($i = 1; $i < 6; $i++) {
                if (isset($images['image'.$i])) {
                    $path = public_path('images/annonces/' . $images['image' . $i]);
                    if (file_exists($path)) unlink($path);
                }
            }
            $annonce->delete();
            return response()->json(['message' => 'annonce bien supprime']);
        }
        return response()->json(["message" => "401"], 401);    
    }
}
