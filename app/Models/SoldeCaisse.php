<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoldeCaisse extends Model
{
    use HasFactory;
    protected $table = 'soldecaisse';
    protected $fillable =
    [
        'total','idcompany','iduser'
    ];
}
