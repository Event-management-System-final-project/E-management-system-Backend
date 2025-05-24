<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TestController extends Controller
{
    // Make sure this alias or import exists

function generateTicketQrCode(Request $request): string
{

    // Generate QR code SVG content
    $qr = QrCode::format('svg')->size(300)->generate("TICKET-{$request->ticketId}");

    // Define storage path relative to 'public' disk
    $path = "qrcodes/ticket_{$request->ticketId}.svg";

    // Save the SVG file to storage/app/public/qrcodes/
    Storage::disk('public')->put($path, $qr);

    // Return the relative path (or URL if you prefer)
    return $path; // you can also return asset(Storage::url($path)) if you want full URL
}

}
