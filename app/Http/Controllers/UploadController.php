<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust max size as needed
        ]);

        $imagePath = $request->file('image')->store('uploads', 'public');

        $image = new Image();
        $image->title = $request->title;
        $image->description = $request->description;
        $image->image_path = $imagePath;
        $image->save();

        return response()->json(['message' => 'Image uploaded successfully'], 200);
    }

    public function getImage()
    {
        $image = Image::latest()->first(); // Get the latest uploaded image
        if ($image) {
            return response()->json(['image_url' => asset('storage/' . $image->image_path)], 200);
        } else {
            return response()->json(['message' => 'No image found'], 404);
        }
    }
}