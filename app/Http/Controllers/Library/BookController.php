<?php

namespace App\Http\Controllers\Library;

use App\Book;
use App\Facades\Alert;
use App\Facades\Cover;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Http\Forms\BookForm;
use App\Http\Forms\Exceptions\UnsentFormException;
use App\Library;
use App\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Throwable;

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
     * @throws Throwable
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
        $user = Auth::user();

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
    public function cover(Library $library, Book $book, string $size = null)
    {
        Gate::authorize('view', $book);

        return Cover::response($book, $size);
    }

    /**
     * @param string|null $size
     * @return mixed
     */
    public function noCover(string $size = null)
    {
        return Cover::response(null, $size);
    }

    public function listView(Library $library, string $type)
    {
        Gate::authorize('view', $library);

        return view('library.list', [
            'title' => trans('list.' . $type),
            'items' => Listing::where('type', Str::singular($type))
                ->where('user_id', Auth::id())
                ->get()
                ->map(function ($listing) use ($type) {
                    return [
                        'title' => $listing->book->title,
                        'added' => $listing->created_at->diffForHumans(),
                        'link' => $listing->book->link,
                        'unlist' => route('books.list.remove', [
                            'list' => Str::singular($type),
                            'book' => $listing->book,
                        ]),
                    ];
                }),
            'fields' => [
                [
                    'key' => 'title',
                    'label' => trans('fields.title'),
                    'sortable' => true
                ],
                [
                    'key' => 'added',
                    'label' => trans('list.added'),
                    'sortable' => true
                ],
                [
                    'key' => 'unlist',
                    'label' => '',
                    'sortable' => false,
                ]
            ]
        ]);
    }

    /**
     * @param string $type
     * @param Book $book
     * @return mixed
     */
    public function listAdd(string $type, Book $book)
    {
        Gate::authorize('view', $book);

        if (Listing::exists(Auth::user(), $book, $type)) {
            return abort(409, 'Book is already listed.');
        }

        Listing::list(Auth::user(), $book, $type);

        return response(true);
    }

    /**
     * @param string $type
     * @param Book $book
     * @return mixed
     */
    public function listRemove(string $type, Book $book)
    {
        Gate::authorize('view', $book);

        if (Listing::missing(Auth::user(), $book, $type)) {
            return abort(404);
        }

        Listing::unlist(Auth::user(), $book, $type);

        return response(true);
    }
}
