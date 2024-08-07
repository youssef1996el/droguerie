<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TmpDevis extends Model
{
    use HasFactory;
    protected $table = 'tmpdevis';

    protected $fillable = [

        'qte', 'price', 'total', 'accessoire', 'idproduct', 'idclient', 'idsetting', 'idstock', 'iduser', 'idcompany',
    ];
}
