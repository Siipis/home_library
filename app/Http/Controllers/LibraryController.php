<?php

namespace App\Http\Controllers;

use App\Book;
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
     * @return mixed
     */
    public function index(Library $library)
    {
        Gate::authorize('view', $library);

        $bookForm = new BookForm();

        return view('library.index', [
            'library' => $library,
            'book_form' => $bookForm->make([
                'action' => route('library.books.store', [
                    'library' => $library
                ])
            ]),
        ]);
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
