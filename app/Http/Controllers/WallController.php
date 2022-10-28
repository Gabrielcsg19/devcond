<?php

namespace App\Http\Controllers;

use App\Models\Wall;
use App\Models\WallLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WallController extends Controller
{
    public function getAll() {
        $walls = Wall::withCount('likes')->get();

        foreach ($walls as $wall) {
            $wall['is_liked'] = !!WallLike::where('wall_id', $wall->id)->where('user_id', Auth::id())->first();
        }

        return setSuccessResponse('', [
            'walls' => $walls
        ]);
    }

    public function like($id) {
        $loggedUserLike = WallLike::where('wall_id', $id)
            ->where('user_id', Auth::id())
        ->first();
        
        if ($loggedUserLike) {
            $loggedUserLike->delete();
        } else {
            WallLike::create([
                'wall_id' => $id,
                'user_id' => Auth::id(),
            ]);
        }

        $isLiked = !$loggedUserLike;

        $likesCount = WallLike::where('wall_id', $id)->count();

        return setSuccessResponse('', [
            'is_liked' => $isLiked,
            'likes_count' => $likesCount
        ]);
    }
}
