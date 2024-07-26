<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Versement extends Model
{
    use HasFactory;
    protected $table = 'versement';

    protected $fillable =
    [

      'comptable', 'total', 'iduser', 'idcompany',
    ];
}
