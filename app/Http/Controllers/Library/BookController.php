<?php

namespace App\Http\Controllers\Library;

use App\Book;
use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Http\Forms\BookForm;
use App\Http\Forms\Exceptions\UnsentFormException;
use App\Library;
use Cache;
use Gate;
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

        $book->categories()->sync($request->input('book_form.category_choices'));

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
     * @throws UnsentFormException
     */
    public function update(Request $request, Library $library, Book $book)
    {
        $book = $this->form->get($request, $book);

        $book->categories()->sync($request->input('book_form.category_choices'));
        $book->save();

        return redirect()->route('library.books.show', [$library, $book]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Library $library
     * @param Book $book
     * @return mixed
     * @throws \Exception
     */
    public function destroy(Library $library, Book $book)
    {
        $book->delete();

        return redirect()->route('library.index', $library);
    }

    /**
     * @param Book $book
     * @return mixed
     */
    public function cover(Library $library, Book $book)
    {
        Gate::authorize('view', $book);

        if ($book->getRawOriginal('cover') === route('books.no_cover')) {
            return $this->noCover();
        }

        return $this->serveImage(
            \Storage::disk('covers')->path("$book->id.png"),
            $book->getCacheId()
        );
    }

    /**
     * @return mixed
     */
    public function noCover()
    {
        return $this->serveImage(
            \Storage::disk('assets')->path('no_cover.png'),
            'no_cover'
        );
    }

    private function serveImage($image, string $cacheId)
    {
        $contentType = [
            'Content-Type' => 'image/png'
        ];

        if (Cache::has($cacheId)) {
            return response()->file(Cache::get($cacheId), $contentType);
        }

        Cache::put($cacheId, $image, now()->addDays(30));

        return response()->file($image, $contentType);
    }

    /**
     * @param Request $request
     * @param Book $book
     */
    private function bindRelations(Request $request, Book $book)
    {
        $category_id = $request->input('book_form.category_choices');

        if ($category_id > 0) {
            $category = Category::findOrFail($category_id);
            $category->books()->save($book);
        }
    }
}
