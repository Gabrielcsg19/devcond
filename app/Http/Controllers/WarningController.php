<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Warning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WarningController extends Controller
{
    public function getLoggedUserWarnings(Request $request) {
        $fields = $request->validate([
            'property' => 'required'
        ]);

        $currentUnit = Unit::where('id', $fields['property'])
            ->where('owner_id', Auth::id())
        ->first();

        if (!$currentUnit) {
            return setErrorResponse('Esta unidade não é sua', 401);
        }

        $warnings = Warning::where('unit_id', $fields['property'])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
        ->get();

        return setSuccessResponse('', [
            'warnings' => $warnings
        ]);
    }

    public function insertFile(Request $request) {
        $request->validate([
            'photo' => 'required|file|mimes:png,jpg'
        ]);

        $file = $request->file('photo')->store('public');

        return setSuccessResponse('', [
            'photo' => asset(Storage::url($file))
        ]);
    }

    public function create(Request $request) {
        $fields = $request->validate([
            'title' => 'required',
            'property' => 'required',
        ]);

        $photosList = $request->input('photos_list', []);

        $newWarning = new Warning();

        $newWarning->unit_id = $fields['property'];
        $newWarning->title = $fields['title'];
        $newWarning->status = 'in_review';
        $newWarning->created_at = date('Y-m-d');

        if (count($photosList) > 0) {
            $photos = [];

            foreach ($photosList as $photo) {
                $url = explode('/', $photo);
                $photos[] = end($url);
            }

            $newWarning->photos = implode(',', $photos);
        } else {
            $newWarning->photos = '';
        }

        $newWarning->save();

        return setSuccessResponse('', [
            'warning' => $newWarning
        ]);
    }
}
