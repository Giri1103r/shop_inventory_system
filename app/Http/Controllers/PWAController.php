<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class PWAController extends Controller
{
    public function manifestJson()
    {
        $url = admin_url(); // automatically handles local/UAT/prod
        // dd($url);
        $manifest = [
            "name" => "KARAM EHS",
            "short_name" => "KARAM EHS",
            "start_url" => $url,
            "scope" => $url,
            "display" => "standalone",
            "background_color" => "#ffffff",
            "theme_color" => "#0d6efd",
            "orientation" => "portrait",
            "icons" => [
                [
                    "src" => $url . "public/assets/images/icons/192.png",
                    "sizes" => "192x192",
                    "type" => "image/png"
                ],
                [
                    "src" => $url . "public/assets/images/icons/512.png",
                    "sizes" => "512x512",
                    "type" => "image/png"
                ]
            ]
        ];

        return response()->json($manifest);
    }
}
