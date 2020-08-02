<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Forms\BookForm;
use App\Library;
use Gate;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    /**
     * @param Library $library
     * @return mixed
     * @throws AuthorizationException
     */
    public function index(Library $library)
    {
        Gate::authorize('view', $library);

        return view('library.index');
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws AuthorizationException
     */
    public function books(Request $request, Library $library)
    {
        Gate::authorize('view', $library);

        if ($request->has('category')) {
            return Category::whereLibraryId($library->id)->books()->paginate();
        }

        return $library->paginate();
    }

    /**
     * @param Library $library
     * @return \Symfony\Component\Form\Form
     */
    public static function getStoreForm(Library $library)
    {
        $bookForm = new BookForm();

        return $bookForm->make([
            'action' => route('library.books.store', [
                'library' => $library
            ])
        ]);
    }
}
