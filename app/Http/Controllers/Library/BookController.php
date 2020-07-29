<?php

namespace App\Http\Controllers\Library;

use App\Book;
use App\Category;
use App\Http\Api\Search;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Http\Forms\BookForm;
use App\Http\Forms\Exceptions\UnsentFormException;
use App\Library;
use Gate;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    private $form;

    public function __construct()
    {
        $this->authorizeResource(Book::class);

        $this->form = new BookForm();
    }

    /**
     * @param Book $book
     * @return mixed
     * @throws AuthorizationException
     */
    public function cover(Library $library, Book $book)
    {
        Gate::authorize('view', $book);

        return response()->file(\Storage::path($book::$coverPath . '/' . $book->id . '.png'), [
            'Content-Type' => 'image/png'
        ]);
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
            'book_form' => LibraryController::getStoreForm($library),
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
        $category = null;

        $library->books()->save($book);

        $this->bindRelations($request, $book);

        return redirect()->back();
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
        return view('library.books.show');
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
        return view('library.books.edit', [
            'book_form' => $this->form->make([
                'method' => 'put',
                'action' => route('library.books.update', [$library, $book])
            ], $book)
        ]);
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
        $book = $this->form->get($request, $book);

        $this->bindRelations($request, $book);
        $book->save();

        return redirect()->back();
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

    /**
     * @param Request $request
     * @param Book $book
     */
    private function bindRelations(Request $request, Book $book)
    {
        $category_id = $request->input('book_form.category_id');

        if ($category_id > 0) {
            $category = Category::findOrFail($category_id);
            unset($book->category_id);

            $category->books()->save($book);
        }
    }
}
