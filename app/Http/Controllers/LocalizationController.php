<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use Auth;

class LocalizationController extends Controller
{
    public function lang_change(Request $request)
    {
        App::setLocale($request->lang);
        session()->put('locale', $request->lang);
        session()->put('lang_id', decryptId($request->lang_id));

        $user = Auth::user();
        $user->language = $request->lang;
        $user->update();

        return redirect()->back();
    }
}
