<?php

namespace App\Http\Controllers;

use App\Models\Billet;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BilletController extends Controller
{
    public function getAll(Request $request) {
        $fields = $request->validate([
            'property' => 'required'
        ]);

        $currentUnit = Unit::where('id', $fields['property'])
            ->where('owner_id', Auth::id())
        ->first();

        if (!$currentUnit) {
            return setErrorResponse('Esta unidade não é sua', 401);
        }

        $billets = Billet::where('unit_id', $fields['property'])->get();

        return setSuccessResponse('', [
            'billets' => $billets,
        ]);
    }
}
