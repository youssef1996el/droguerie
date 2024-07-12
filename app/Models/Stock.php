<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stock';

    protected $fillable =
    [
        'qte','qte_company','price', 'idproduct','idcompany','iduser','idbonentre','qte_notification'
    ];


}
