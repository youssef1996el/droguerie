<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiements extends Model
{
    use HasFactory;
    protected $table = 'paiements';

    protected $fillable =
    [
        'total','idmode','idcompany','iduser','idreglement'
    ];
}
