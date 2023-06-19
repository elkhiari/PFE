<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ville;
use App\Models\User;
use App\Models\Categorie;

class Annonce extends Model
{
    use HasFactory;
    protected $fillable = [
        'titre',
        'description',
        'prix',
        'ville',
        'user',
        'categorie',
        'images',
    ];

    public function ville()
    {
        return $this->belongsTo(Ville::class,'ville');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user');
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class,'categorie');
    }
}
