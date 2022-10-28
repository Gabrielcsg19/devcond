<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = [
        'unit_id',
        'area_id',
        'reservation_at',
    ];
}
