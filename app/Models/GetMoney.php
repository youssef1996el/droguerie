<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GetMoney extends Model
{
    use HasFactory;
    protected $table = 'getmoney';

    protected $fillable = [
        'friend','total','idcompany','iduser'
    ];
}
