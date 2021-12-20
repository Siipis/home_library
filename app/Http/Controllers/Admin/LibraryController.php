<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Http\Forms\Exceptions\FormException;
use App\Http\Forms\Exceptions\UnsentFormException;
use App\Http\Forms\LibraryForm;
use App\Library;
use App\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LibraryController extends Controller
{
    private $form;

    public function __construct()
    {
        $this->middleware(['can:access-backend']);

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

        return redirect()->route('admin.libraries.index')->with(
            Alert::success('library.added',
                route('admin.libraries.show', $library)
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Library $library
     * @return mixed
     */
    public function show(Library $library)
    {
        return view('admin.libraries.show', [
            'library' => $library,
            'members' => $library->members()
                ->withPivot('role')->get()
                ->map(function (User $user) {
                    $user->role = $user->pivot->role;

                    return $user;
                }),
            'nonMembers' => $library->nonMembers(),
        ]);
    }

    /**
     * Update the library members.
     *
     * @param Request $request
     * @param Library $library
     * @return JsonResponse
     */
    public function members(Request $request, Library $library)
    {
        Gate::authorize('members', $library);

        $members = [];

        foreach ($request->input('data.members') as $member) {
            $members[$member['id']] = [
                'role' => $member['role']
            ];
        }

        $library->members()->sync($members);

        return response()->json($library->members);
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

        return redirect()->route('admin.libraries.index')->with(
            Alert::success('library.saved')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Library $library
     * @return mixed
     * @throws Exception
     */
    public function destroy(Library $library)
    {
        $library->delete();

        return redirect()->route('admin.libraries.index')->with(
            Alert::info('library.deleted')
        );
    }
}
