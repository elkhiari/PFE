<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ville;
use Validator;

class VilleController extends Controller
{
    public function index()
    {
        $villes = Ville::all();
        if ($villes->isEmpty()) return response()->json(['message' => 'No villes found'], 404);
        return response()->json($villes, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all() ,[
            'ville' => 'required'
        ]);
        if ($validator -> fails()) return response()->json($validator->errors(), 400);
        $ville = Ville::create($request->all());
        return response()->json($ville, 201);
    }

    public function show($id)
    {
        $ville = Ville::find($id);
        if (!$ville) return response()->json(['message' => 'Ville not found'], 404);
        return response()->json($ville, 200);
    }

    public function update(Request $request, $id)
    {
        $existingVille = Ville::find($id);

        if (!$existingVille) return response()->json(['message' => 'Ville not found'], 404);

        
        $existingVille->update($request->all());

        return response()->json(['message' => 'Ville successfully updated'], 200);
    }



    public function destroy($id)
    {
        $existingVille = Ville::find($id);
        if (!$existingVille) {
            return response()->json(['message' => 'Ville not found'], 404);
        }
        $existingVille->delete();
        return response()->json(['message' => 'Ville deleted'], 204);
    }
}
