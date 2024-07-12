<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lineorder extends Model
{
    use HasFactory;
    protected $table = 'lineorder';
    protected $fillable =
    [
        'qte','price','total','idproduct','idorder','idsetting','idstock','accessoire'
    ];
}
