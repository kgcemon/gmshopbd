<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FreeFireLikeController extends Controller
{
    public function index(){
        return view('user.ff-like-sender');
    }

    public function sendLike(Request $request)
    {
        $validatedData = $request->validate([
            'region' => 'required|string',
            'player_id' => 'required|string',
        ]);

        $region = $validatedData['region'];
        $player_id = $validatedData['player_id'];

        try {
            $response = Http::get("https://likes-api-lkteam-v3.onrender.com/like?uid=$player_id&region=$region&count=100");
            //reponse example
//            {
//                "failed_likes": 0,
//  "level": 67,
//  "likes_added": 0,
//  "likes_after": 8320,
//  "likes_before": 8320,
//  "name": "ᎧᎮ么ʀᴀʙʙɪ࿐모",
//  "region": "BD",
//  "uid": "611311243"
//}

            return response()->json([
                'success' => true,
                'data' => $response->json()
            ]);
        }catch (\Exception $exception){
            return 'সার্ভার সমস্যা আবার চেস্টা করুন';
        }
    }
}
