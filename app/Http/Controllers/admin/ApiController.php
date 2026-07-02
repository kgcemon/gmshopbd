<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Api;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Display a listing of APIs.
     */
    public function index()
    {
        $apis = Api::all();
        return view('admin.api', compact('apis'));
    }

    /**
     * Store a newly created API.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'key' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        try {
            Api::create([
                'type' => $request->type,
                'name' => $request->name,
                'url' => $request->url,
                'key' => $request->key,
                'status' => $request->status,
            ]);

            return response()->json(['success' => 'API added successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong: '.$e->getMessage()]);
        }
    }

    /**
     * Update the specified API.
     */
    public function update(Request $request, Api $api)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'key' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        try {
            $api->update([
                'type' => $request->type,
                'name' => $request->name,
                'url' => $request->url,
                'key' => $request->key,
                'status' => $request->status,
            ]);

            return response()->json(['success' => 'API updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong: '.$e->getMessage()]);
        }
    }

    /**
     * Remove the specified API.
     */
    public function destroy(Api $api)
    {
        try {
            $api->delete();
            return response()->json(['success' => 'API deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong: '.$e->getMessage()]);
        }
    }
}
