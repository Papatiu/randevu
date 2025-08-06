<?php
namespace App\Http\Controllers;

use App\Models\Sport;

class HomeController extends Controller
{
    public function index()
    {
        $sports = Sport::all();
        return view('home', compact('sports'));
    }
}
