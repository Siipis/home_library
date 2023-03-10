<?php


namespace App\Facades\Classes;


use App\Book;
use App\Http\Images\DownloadProvider;
use App\Http\Images\Templates\LargeFilter;
use Exception;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class Cover
{
    /**
     * @var FilesystemAdapter
     */
    private $storage;

    /**
     * @var DownloadProvider
     */
    private $provider;

    /**
     * @var string
     */
    private $extension = 'png';

    /**
     * Cover constructor.
     */
    public function __construct()
    {
        $this->storage = Storage::disk('covers');
        $this->provider = new DownloadProvider();
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param Book $book
     * @return string
     * @throws Exception
     */
    public function getFilename(Book $book)
    {
        if (empty($book->hash)) {
            throw new Exception("Book hash is empty.");
        }

        return $book->hash . '.' . $this->extension;
    }

    /**
     * @param Book $book
     * @return int
     * @throws Exception
     */
    public function getLastModified(Book $book)
    {
        return $this->storage->lastModified($this->getFilename($book));
    }

    /**
     * @param Book $book
     * @return int
     * @throws Exception
     */
    public function getFilesize(Book $book)
    {
        return $this->storage->size($this->getFilename($book));
    }

    /**
     * @param Book $book
     * @return string
     * @throws Exception
     */
    public function getCoverPath(Book $book)
    {
        return $this->storage->path(
            $this->getFilename($book)
        );
    }

    /**
     * @return string
     */
    public function getPlaceholderPath()
    {
        return Storage::disk('assets')->path('no_cover.png');
    }

    /**
     * @param Book $book
     * @return bool
     * @throws Exception
     */
    public function make(Book $book)
    {
        if (empty($book->upload_cover) && (!$book->cover || $book->cover === route('books.no_cover'))) {
            $this->storage->delete(
                $this->getFilename($book)
            );
        } else {
            if ($this->isDirty($book)) {
                if (Request::hasFile('book_form.upload_cover')) {
                    $image = Image::make(Request::file('book_form.upload_cover')->get());
                    unset($book->upload_cover);
                } else {
                    $image = $this->provider->download($book->cover);
                }

                if ($image instanceof \Intervention\Image\Image) {
                    $image->filter(new LargeFilter());
                    $image->encode('image/' . $this->extension);

                    $this->storage->put($this->getFilename($book), $image);
                } else {
                    abort(404);
                }
            }
        }

        return $this->storage->exists($this->getFilename($book));
    }

    /**
     * @param Book|null $book
     * @param string|null $filter
     * @return \Intervention\Image\Image
     * @throws Exception
     */
    public function get(Book $book = null, string $filter = null)
    {
        if (!is_null($book) && $this->storage->exists($this->getFilename($book))) {
            $path = $this->getCoverPath($book);
        } else {
            $path = $this->getPlaceholderPath();
        }

        $image = Image::make($path);

        if ($filter = $this->getFilter($filter)) {
            $image->filter($filter);
        }

        return $image->encode($this->extension);
    }

    /**
     * @param Book|null $book
     * @param string|null $filter
     * @return Response
     * @throws Exception
     */
    public function response(Book $book = null, string $filter = null)
    {
        if (!is_null($book) && $this->storage->exists($this->getFilename($book))) {
            $lastModified = $this->getLastModified($book);
            $etag = md5($this->getFilename($book) . $filter . $lastModified);
        } else {
            $lastModified = File::lastModified($this->getPlaceholderPath());
            $etag = md5($this->getPlaceholderPath() . $filter . $lastModified);
        }

        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
            if (trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
                return response(null, 304);
            }
        } else if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            if (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) === $lastModified) {
                return response(null, 304);
            }
        }

        return response($this->get($book, $filter), 200)
            ->header('Content-Type', 'image/' . $this->extension)
            ->header('Cache-Control', 'public')
            ->header('ETag', $etag)
            ->header('Last-Modified', $lastModified);
    }

    /**
     * @param string|null $filter
     * @return mixed
     */
    private function getFilter(string $filter = null)
    {
        Validator::make(compact('filter'), [
            'filter' => 'nullable|alpha'
        ]);

        if (!is_null($filter)) {
            $filter = config('image.filters.' . $filter);
            if (!isset($filter)) {
                abort(404);
            }

            return new $filter;
        }

        return false;
    }

    /**
     * @param Book $book
     * @return bool
     */
    private function isDirty(Book $book)
    {
        return Request::hasFile('book_form.upload_cover') || Str::startsWith($book->cover, 'http') && !Str::contains($book->cover, config('app.url'));
    }
}
