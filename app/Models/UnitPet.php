<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitPet extends Model
{
    use HasFactory;

    protected $hidden = ['unit_id'];

    public $fillable = [
        'unit_id',
        'name',
        'race'
    ];

    public $timestamps = false;
}
