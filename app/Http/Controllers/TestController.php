<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use App;
use Auth;

class TestController extends Controller
{
    public function index(Request $request)
    {
       dd(Hash::make('asdF@1234567'));

    }
}
