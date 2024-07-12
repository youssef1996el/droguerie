<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Info extends Model
{
    use HasFactory;
    protected $table = 'infos';

    protected $fillable = [

        'title', 'ice', 'phone', 'fix', 'cnss', 'rc', 'if', 'address', 'idcompany', 'iduser'
    ];
}
