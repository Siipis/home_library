<?php

namespace App\Http\Controllers\Library;

use Alert;
use App\Book;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Http\Forms\BookForm;
use App\Http\Forms\Exceptions\UnsentFormException;
use App\Library;
use Cache;
use DB;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
     * @throws \Throwable
     */
    public function store(Request $request, Library $library)
    {
        DB::beginTransaction();
        $book = $this->form->get($request);
        $category = null;

        $library->books()->save($book);

        $book->categories()->sync($request->input('book_form.category_choices'));
        DB::commit();

        return redirect()->back()->with(
            Alert::success('book.added',
                route('library.books.show', [$library, $book])
            )
        );
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
        DB::beginTransaction();
        $book = $this->form->get($request, $book);

        $book->categories()->sync($request->input('book_form.category_choices'));
        $book->save();
        DB::commit();

        return redirect()->route('library.books.show', [$library, $book])
            ->with(Alert::success('book.saved'));
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

        return redirect()->route('library.index', $library)
            ->with(Alert::warning('book.deleted'));
    }

    /**
     * @param Book $book
     * @return mixed
     */
    public function cover(Library $library, Book $book)
    {
        Gate::authorize('view', $book);

        try {
            return $this->serveImage(
                \Storage::disk('covers')->path("$book->id.png"),
                $book->getCacheId()
            );
        } catch (\Exception $e) {
            return $this->noCover();
        }
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

    /**
     * @param $image
     * @param string $cacheId
     * @return BinaryFileResponse
     */
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
}
