<?php

namespace App\Http\Controllers\Library;

use Gate;
use App\Book;
use App\Http\Api\Search;
use App\Http\Forms\BookForm;
use App\Http\Forms\Exceptions\UnsentFormException;
use App\Library;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookController extends Controller
{
    private $form;

    public function __construct()
    {
        $this->authorizeResource(Book::class);

        $this->form = new BookForm();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function cover(Request $request)
    {
        $book = new Book();
        $book->title = $request->input('title');
        $book->isbn = $request->input('isbn');
        $book->original_data = $request->input('original_data');

        Gate::authorize('update', $book);

        return response()->json(Search::cover($book));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Library $library
     * @return mixed
     */
    public function create(Library $library)
    {
        return view('library.books.create', [
            'form' => $this->form->make([
                'action' => route('library.books.store', [
                    'library' => $library
                ])
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
        $book = $this->form->get($request);

        if ($book instanceof Book) {
            $book->library()->associate($library);
            $book->save();
        }

        return redirect()->route('library.index', $library->slug);
    }

    /**
     * Display the specified resource.
     *
     * @param Library $library
     * @param Book $book
     * @return mixed
     */
    public function show(Library $library, Book $book)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Library $library
     * @param Book $book
     * @return mixed
     */
    public function edit(Library $library, Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Library $library
     * @param Book $book
     * @return mixed
     */
    public function update(Request $request, Library $library, Book $book)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Library $library
     * @param Book $book
     * @return mixed
     */
    public function destroy(Library $library, Book $book)
    {
        //
    }
}
