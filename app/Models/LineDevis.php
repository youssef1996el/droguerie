<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineDevis extends Model
{
    use HasFactory;
    protected $table = 'linedevis';

    protected $fillable = [

        'qte', 'price', 'total', 'accessoire', 'idsetting', 'idstock', 'idproduct', 'iddevis',
    ];
}
