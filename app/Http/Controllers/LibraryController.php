<?php

namespace App\Http\Controllers;

use App\Http\Api\Search;
use App\Http\Forms\BookForm;
use Gate;
use App\Library;
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
            'bookForm' => $bookForm->make([
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
     */
    public function search(Request $request, Library $library)
    {
        Gate::authorize('view', $library);

        $request->validate([
            'search' => 'required|string',
        ]);

        return response()->json(Search::make($library, $request->input('search')));
    }
}
