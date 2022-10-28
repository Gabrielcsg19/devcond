<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $hidden = ['owner_id'];

    public function peoples() {
        return $this->hasMany('App\Models\UnitPeople');
    }

    public function vehicles() {
        return $this->hasMany('App\Models\UnitVehicle');
    }

    public function pets() {
        return $this->hasMany('App\Models\UnitPet');
    }
}
