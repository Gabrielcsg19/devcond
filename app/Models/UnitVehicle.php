<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitVehicle extends Model
{
    use HasFactory;

    protected $hidden = ['unit_id'];

    public $fillable = [
        'unit_id',
        'title',
        'color',
        'plate'
    ];

    public $timestamps = false;
}
