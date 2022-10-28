<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoundAndLost extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $table = 'found_and_lost';

    public $fillable = [
        'status',
        'photo',
        'description',
        'where',
        'created_at'
    ];

    public $appends = ['photo_url'];

    public function getPhotoUrlAttribute() {
        return $this->attributes['photo_url'] = asset("storage/$this->photo");
    }
}
