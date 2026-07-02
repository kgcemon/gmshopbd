<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UpdateNotice;

class NoticeUpdateController extends Controller
{
    public function index()
    {
        $notice = UpdateNotice::latest()->first();
        return view('admin.pages.notice.index', compact('notice'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'notice' => 'required|string|max:5000',
        ]);

        // পুরাতন notice ডিলিট করে নতুন save করা
        UpdateNotice::truncate();

        $notice = UpdateNotice::create([
            'notice' => $request->notice,
            'status' => true,
        ]);

        return response()->json(['success' => true, 'notice' => $notice]);
    }

    public function destroy($id)
    {
        $notice = UpdateNotice::findOrFail($id);
        $notice->delete();

        return response()->json(['success' => true]);
    }
}
