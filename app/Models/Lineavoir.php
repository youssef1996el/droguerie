<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lineavoir extends Model
{
    use HasFactory;
    protected $table = 'lineavoir';
    protected $fillable =
    [
        'qte','price','total','idproduct','idavoir','idsetting','idstock','accessoire'
    ];
}
