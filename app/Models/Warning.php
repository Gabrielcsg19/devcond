<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $appends = ['photos_list'];

    public function getPhotosListAttribute() {
        $photos = explode(',', $this->photos);

        $photosList = [];

        foreach ($photos as $photo) {
            if (!empty($photo)) {
                $photosList[] = asset("storage/$photo");
            }
        }

        return $this->attributes['photos_list'] = $photosList;
    }
}
