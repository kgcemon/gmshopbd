<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Giveaway;
use Illuminate\Http\Request;

class GiveawayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $giveaways = Giveaway::paginate(10);
        return response()->json(
            [
                'status' => true,
                'data' => $giveaways->Items()
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Giveaway $giveaway)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Giveaway $giveaway)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Giveaway $giveaway)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Giveaway $giveaway)
    {
        //
    }
}
