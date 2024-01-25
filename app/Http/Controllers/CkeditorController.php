<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\CSRF;

class CkeditorController extends Controller
{
    public function upload(Request $request)
    {

        $uploadedFile = $request->file('upload');
        $filename = time() . '.' . $uploadedFile->getClientOriginalExtension();
        $uploadedFile->move(public_path('uploads'), $filename);
        return response()->json([
            'uploaded' => true,
            'url' => asset('uploads/' . $filename),
        ]);
    }

}
