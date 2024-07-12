<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    use HasFactory;
    protected $table = 'personnels';

    protected $fillable =
    [
         'nom', 'prenom', 'adresse', 'cin', 'ville', 'idcompany', 'iduser','telephone'
    ];
}
