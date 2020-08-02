<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Api\Search;
use App\Library;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string',
            'library' => 'required|exists:libraries,slug',
        ]);

        $search = $request->input('search');
        $library = Library::findBySlugOrFail($request->input('library'));

        return response()->json([
            'existing' => $library->books()->search($search)->take(4)->get(),
            'new' => Search::books($search),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function details(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string',
        ]);

        return response()->json(Search::details($request->input('isbn')));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function cover(Request $request)
    {
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
