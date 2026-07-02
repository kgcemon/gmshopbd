<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\HelpLine;
use Illuminate\Http\Request;

class HelpLineControllers extends Controller
{
    public function index()
    {
        $lines = HelpLine::latest()->paginate(20);
        return view('admin.helpline.index', compact('lines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // max 2MB
            'url' => 'nullable|url',
        ]);

        $data = $request->only('name', 'url');

        if ($request->hasFile('image')) {
            $imageName = time().'_'.$request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/helpline'), $imageName);
            $data['image'] = $imageName;
        }

        HelpLine::create($data);

        return redirect()->back()->with('success', 'Help line added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'url' => 'nullable|url',
        ]);

        $line = HelpLine::findOrFail($id);
        $data = $request->only('name','url');

        // If new image uploaded, replace old one
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($line->image && file_exists(public_path('uploads/helpline/'.$line->image))) {
                unlink(public_path('uploads/helpline/'.$line->image));
            }

            $imageName = time().'_'.$request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/helpline'), $imageName);
            $data['image'] = $imageName;
        }

        $line->update($data);

        return redirect()->back()->with('success', 'Help line updated successfully!');
    }

    public function destroy($id)
    {
        $line = HelpLine::findOrFail($id);

        // Delete image from storage
        if ($line->image && file_exists(public_path('uploads/helpline/'.$line->image))) {
            unlink(public_path('uploads/helpline/'.$line->image));
        }

        $line->delete();

        return redirect()->back()->with('success', 'Help line deleted successfully!');
    }
}
