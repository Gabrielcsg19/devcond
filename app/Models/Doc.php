<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doc extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $appends = ['full_file_url'];

    public function getFullFileUrlAttribute() {
        return $this->attributes['file_url'] = asset("storage/$this->file_url");
    }
}
