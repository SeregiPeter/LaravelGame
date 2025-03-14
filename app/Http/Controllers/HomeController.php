<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Contest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if(Auth::user()) {
            return redirect() -> route('characters.index');
        }
        return view('home.index', ['characterCount' => Character::count(), 'contestCount' => Contest::count()]);
    }
}
