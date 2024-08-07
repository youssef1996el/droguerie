<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    use HasFactory;
    protected $table = 'devis';

    protected $fillable = [

        'total', 'type', 'idclient', 'idcompany', 'iduser',
    ];
}
