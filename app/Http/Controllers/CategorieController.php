<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categorie;
use Validator;

class CategorieController extends Controller
{
    public function index()
    {
        $categories = Categorie::all();
        if ($categories->isEmpty()) return response()->json(['message' => 'No categories found'], 404);
        return response()->json($categories, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all() ,[
            'libelle' => 'required',
            'description' => 'required'
        ]);
        if ($validator -> fails()) return response()->json($validator->errors(), 400);
        $categorie = Categorie::create($request->all());
        return response()->json($categorie, 201);
    }

    public function show($id)
    {
        $categorie = Categorie::find($id);
        if (!$categorie) return response()->json(['message' => 'Categorie not found'], 404);
        return response()->json($categorie, 200);
    }

    public function update($id, Request $request)
    {
        $categorie = Categorie::find($id);
        if (!$categorie) return response()->json(['message' => 'Categorie not found'], 404);
        $validator = Validator::make($request->all() ,[
            'libelle' => 'required',
            'description' => 'required'
        ]);
        $categorie->update($request->all());
        return response()->json($categorie, 200);
    }

    public function destroy($id)
    {
        $categorie = Categorie::find($id);
        if (!$categorie) {
            return response()->json(['message' => 'Categorie not found'], 404);
        }
        $categorie->delete();
        return response()->json(['message' => 'Categorie deleted'], 204);
    }

}
