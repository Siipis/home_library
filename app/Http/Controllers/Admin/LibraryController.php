<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Forms\Exceptions\FormException;
use App\Http\Forms\LibraryForm;
use App\Library;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Library::class, 'library');
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
     * @param Request $request
     * @return mixed
     */
    public function create(Request $request)
    {
        return view('admin.libraries.create', [
            'form' => LibraryForm::get($request, [
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
        if ($library = LibraryForm::handle($request)) {
            $library->save();

            return redirect()->route('admin.libraries.index');
        }

        return LibraryForm::back($request);
    }

    /**
     * Display the specified resource.
     *
     * @param Library $library
     * @return mixed
     */
    public function show(Library $library)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Library $library
     * @return mixed
     */
    public function edit(Library $library)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Library $library
     * @return mixed
     */
    public function update(Request $request, Library $library)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Library $library
     * @return mixed
     */
    public function destroy(Library $library)
    {
        //
    }
}
