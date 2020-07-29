<?php

namespace App\Http\Controllers;

use App\Book;
use App\Category;
use App\Http\Api\Search;
use App\Http\Forms\BookForm;
use App\Library;
use Exception;
use Gate;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LibraryController extends Controller
{

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
            return Category::whereLibraryId($library->id)->books()->simplePaginate(Library::$paginate);
        }

        return $library->paginate();
    }

    /**
     * @param Request $request
     * @param Library $library
     * @return JsonResponse
     * @throws Exception
     */
    public function search(Request $request, Library $library)
    {
        Gate::authorize('view', $library);

        $request->validate([
            'search' => 'required|string',
        ]);

        return response()->json(Search::books($library, $request->input('search')));
    }

    /**
     * @param Request $request
     * @param Library $library
     * @return JsonResponse
     * @throws Exception
     */
    public function details(Request $request, Library $library)
    {
        Gate::authorize('update', $library);

        $request->validate([
            'isbn' => 'required|string',
        ]);

        return response()->json(Search::details($library, $request->input('isbn')));
    }

    /**
     * @param Request $request
     * @param Library $library
     * @return JsonResponse
     * @throws Exception
     */
    public function cover(Request $request, Library $library)
    {
        Gate::authorize('update', $library);

        $request->validate([
            'id' => 'exists,books',
            'title' => 'required|string',
            'isbn' => 'string|nullable',
        ]);

        $book = new Book();
        $book->id = $request->input('id');
        $book->title = $request->input('title');
        $book->isbn = $request->input('isbn');

        return response()->json(Search::cover($book));
    }
}
