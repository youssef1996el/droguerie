<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailCategorys extends Model
{
    use HasFactory;

    protected $table = 'detailcategorys';

    protected $fillable = [
        'name','idcompany','idcategory'
    ];
}
