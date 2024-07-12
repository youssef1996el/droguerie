<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonEntre extends Model
{
    use HasFactory;
    protected $table = 'bonentres';
    protected $fillable = ['numero_bon', 'date', 'numero', 'commercial', 'mode_paiement', 'matricule', 'chauffeur', 'cin', 'idcompany', 'iduser'];
}
