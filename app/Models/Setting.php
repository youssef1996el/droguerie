<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'setting';

    protected $fillable =
    [
        'type','qte', 'idcategory','convert','iduser','idcompany','name_product','idstock'
    ];


}
