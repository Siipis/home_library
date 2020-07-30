<?php

namespace App\Http\Controllers\Library;

use Alert;
use App\Category;
use App\Http\Controllers\LibraryController;
use App\Http\Forms\CategoryForm;
use App\Http\Forms\Exceptions\UnsentFormException;
use App\Library;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $form;

    public function __construct()
    {
        $this->authorizeResource(Category::class, 'category');

        $this->form = new CategoryForm();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Library $library
     * @return mixed
     */
    public function index(Library $library)
    {
        return view('library.category.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Library $library
     * @return mixed
     */
    public function create(Library $library)
    {
        return view('library.category.create', [
            'form' => $this->form->make([
                'action' => route('library.categories.store', $library)
            ])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Library $library
     * @return mixed
     * @throws UnsentFormException
     */
    public function store(Request $request, Library $library)
    {
        $category = $this->form->get($request);

        if ($category instanceof Category) {
            $category->library()->associate($library);

            $category->save();
        }

        return redirect()->route('library.categories.index', $library)->with(
            Alert::success('category.added',
                route('library.categories.show', [$library, $category]
                )
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Library $library
     * @param Category $category
     * @return mixed
     */
    public function show(Library $library, Category $category)
    {
        return view('library.category.show', [
            'book_form' => LibraryController::getStoreForm($library),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Library $library
     * @param Category $category
     * @return mixed
     */
    public function edit(Library $library, Category $category)
    {
        return view('library.category.edit', [
            'form' => $this->form->make([
                'action' => route('library.categories.update', [$library, $category]),
                'method' => 'put',
            ], $category),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Library $library
     * @param Category $category
     * @return mixed
     * @throws UnsentFormException
     */
    public function update(Request $request, Library $library, Category $category)
    {
        $category = $this->form->get($request, $category);

        $category->save();

        return redirect()->route('library.categories.show', [$library, $category])->with(
            Alert::success('category.saved')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Library $library
     * @param Category $category
     * @return mixed
     * @throws \Exception
     */
    public function destroy(Library $library, Category $category)
    {
        $category->delete();

        return redirect()->route('library.categories.index', $library)->with(
            Alert::info('category.deleted')
        );
    }
}
