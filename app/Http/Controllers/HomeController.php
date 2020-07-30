<?php

namespace App\Http\Controllers;

use App\Http\Forms\Exceptions\UnsentFormException;
use Auth;
use App\Http\Forms\UserForm;
use App\Library;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $userForm;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        $this->userForm = new UserForm();
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

    /**
     * @return mixed
     */
    public function settings()
    {
        return view('settings', [
            'user_form' => $this->userForm->make([
                'action' => route('settings.account'),
            ], Auth::user()),
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws UnsentFormException
     */
    public function updateAccount(Request $request)
    {
        $user = $this->userForm->get($request, Auth::user());

        $user->save();

        return redirect()->back();
    }
}
