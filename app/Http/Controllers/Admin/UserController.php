<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Http\Forms\Exceptions\UnsentFormException;
use App\Http\Forms\UserForm;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * @var UserForm
     */
    private $form;

    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');

        $this->form = new UserForm();
    }

    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index()
    {
        return view('admin.users.index', [
            'users' => User::orderBy('name')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return mixed
     */
    public function create()
    {
        return view('admin.users.create', [
            'form' => $this->form->make([
                'action' => route('admin.users.store')
            ]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return mixed
     * @throws UnsentFormException
     */
    public function store(Request $request)
    {
        $user = $this->form->get($request);

        $user->save();

        return redirect()->route('admin.users.show', $user->id)->with(
            Alert::success('user.added')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return mixed
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return mixed
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return mixed
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Update the specified user's role in storage.
     *
     * @param Request $request
     * @param User $user
     * @return mixed
     */
    public function promote(Request $request, User $user)
    {
        Gate::authorize('promote', $user);

        $request->validate([
            'data.user.is_admin' => 'required|boolean'
        ]);

        $user->is_admin = $request->input('data.user.is_admin');

        $user->save();

        if ($request->expectsJson()) {
            return response()->json($user);
        }

        return redirect()->back()->with(
            Alert::success('user.saved')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return mixed
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with(
            Alert::info('user.deleted')
        );
    }
}
