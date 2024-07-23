<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avoir extends Model
{
    use HasFactory;
    protected $table = 'avoir';
    protected $fillable =
    [
        'total','idorder','idcompany','iduser','idclient','idfacture',
    ];
}
