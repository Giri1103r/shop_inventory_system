<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use Auth;

class SettingsController extends Controller
{
    public function theme_change(Request $request)
    {
        $theme =  $request->theme;

        $user = Auth::user();
        $user->theme = $theme;
        $user->update();

        $response = [
            'success' => 'success',
        ];

        return response()->json($response);

    }
}
