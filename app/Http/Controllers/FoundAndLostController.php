<?php

namespace App\Http\Controllers;

use App\Models\FoundAndLost;
use Illuminate\Http\Request;

class FoundAndLostController extends Controller
{
    public function getAll() {

        $lost = FoundAndLost::where('status', 'lost')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $recovered = FoundAndLost::where('status', 'recovered')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return setSuccessResponse('', [
            'lost' => $lost,
            'recovered' => $recovered,
        ]);
    }

    public function create(Request $request) {
        $fields = $request->validate([
            'description' => 'required|string',
            'where' => 'required|string',
            'photo' => 'required|file|mimes:png,jpg',
        ]);

        $photo = $request->file('photo');

        $photoName = $photo->hashName();

        $photo->store('/public');

        $newLost = FoundAndLost::create([
            'status' => 'lost',
            'photo' => $photoName,
            'description' => $fields['description'],
            'where' => $fields['where'],
            'created_at' => date('Y-m-d'),
        ]);

        return setSuccessResponse('', [
            'lost' => $newLost
        ], 201);
    }

    public function update(Request $request, $id) {
        $fields = $request->validate([
            'status' => 'required|in:lost,recovered'
        ]);

        $item = FoundAndLost::find($id);

        if (!$item) {
            return setErrorResponse('Item inexistente', 404);
        }

        $item->status = $fields['status'];
        $item->save();

        return setSuccessResponse('', [
            'item' => $item
        ]);
    }
}
