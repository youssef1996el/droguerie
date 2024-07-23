<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tmplineavoir extends Model
{
    use HasFactory;
    protected $table = 'tmpavoir';

    protected $fillable =
    [

        'qte', 'price', 'total', 'idproduct', 'idclient', 'iduser','idcompany','idsetting','idstock','accessoire'
    ];
}
