<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TmpLineOrder extends Model
{
    use HasFactory;

    protected $table = 'tmplineorder';

    protected $fillable =
    [

        'qte', 'price', 'total', 'idproduct', 'idclient', 'iduser','idcompany','idsetting','idstock','accessoire'
    ];
}
