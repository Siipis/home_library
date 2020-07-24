<?php

namespace App\Http\Controllers;

use App\Http\Api\Search;
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

        return view('library.index', compact('library'));
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
