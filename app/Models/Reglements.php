<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reglements extends Model
{
    use HasFactory;
    protected $table = 'reglements';

    protected $fillable =
    [
        'total','datepaiement','idclient','idorder','idcompany','iduser','idmode','status'
    ];
}
