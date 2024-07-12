<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheques extends Model
{
    use HasFactory;

    protected $table = 'cheques';

    protected $fillable = [
        'numero', 'datecheque','datepromise','montant','type','name','bank','idorder'
    ];
}
