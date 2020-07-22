<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Http\Controllers\Controller;
use App\Http\Forms\Exceptions\FormException;
use App\Http\Forms\Exceptions\UnsentFormException;
use App\Http\Forms\LibraryForm;
use App\Library;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    private $form;

    public function __construct()
    {
        $this->authorizeResource(Library::class, 'library');

        $this->form = new LibraryForm();
    }

    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index()
    {
        return view('admin.libraries.index', [
            'libraries' => Library::orderBy('name')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return mixed
     */
    public function create()
    {
        return view('admin.libraries.create', [
            'form' => $this->form->make([
                'action' => route('admin.libraries.store')
            ])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return mixed
     * @throws FormException
     */
    public function store(Request $request)
    {
        $library = $this->form->get($request);

        $library->save();

        return redirect()->route('admin.libraries.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Library $library
     * @return mixed
     */
    public function show(Library $library)
    {
        $library->members = collect($library->members)->map(function ($member) {
            $member->role = $member->pivot->role;

            return $member;
        });

        return view('admin.libraries.show', [
            'library' => $library,
            'nonMembers' => $library->nonMembers(),
        ]);
    }

    /**
     * Update the library members.
     *
     * @param Request $request
     * @param Library $library
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function members(Request $request, Library $library)
    {
        Gate::authorize('members', $library);

        $members = [];

        foreach ($request->input('data.members') as $member) {
            $members[$member['id']] = [
                'role' => $member['role']]
            ;
        }

        $library->members()->sync($members);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Library $library
     * @return mixed
     */
    public function edit(Library $library)
    {
        return view('admin.libraries.edit', [
            'form' => $this->form->make([
                'action' => route('admin.libraries.update', $library->id),
                'method' => 'put',
            ], $library)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Library $library
     * @return mixed
     * @throws UnsentFormException
     */
    public function update(Request $request, Library $library)
    {
        $library = $this->form->get($request, $library);

        $library->save();

        return redirect()->route('admin.libraries.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Library $library
     * @return mixed
     * @throws \Exception
     */
    public function destroy(Library $library)
    {
        $library->delete();

        return redirect()->route('admin.libraries.index');
    }
}