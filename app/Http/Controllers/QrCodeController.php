<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QrCodeController extends Controller
{
    public function serve($filename)
    {
        $path = 'public/qrcodes/' . $filename;

        if (!Storage::exists($path)) {
            abort(404, 'QR code not found.');
        }

        $content = Storage::get($path);

        return response($content, 200, [
            'Content-Type' => 'image/svg+xml',
            'Access-Control-Allow-Origin' => '*', // 👈 CORS header added here
        ]);
    }
}


