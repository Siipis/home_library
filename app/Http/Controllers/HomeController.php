<?php

namespace App\Http\Controllers;

use App\Library;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return mixed
     */
    public function index()
    {
        return view('index', [
            'libraries' => Library::all()
        ]);
    }
}
