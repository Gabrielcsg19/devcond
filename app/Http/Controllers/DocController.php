<?php

namespace App\Http\Controllers;

use App\Models\Doc;
use Illuminate\Http\Request;

class DocController extends Controller
{
    public function getAll() {
        $docs = Doc::all();

        return setSuccessResponse('', [
            'docs' => $docs
        ]);
    }
}
