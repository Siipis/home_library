<?php

namespace App\Policies;

use App\Book;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Http\Request;

class BookPolicy
{
    use HandlesAuthorization;

    private $library;

    public function __construct(Request $request)
    {
        $this->library = $request->route()->parameter('library');
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isMemberOf($this->library);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Book $book
     * @return mixed
     */
    public function view(User $user, Book $book)
    {
        return $user->isMemberOf($book->library);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isOwnerOf($this->library);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Book $book
     * @return mixed
     */
    public function update(User $user, Book $book)
    {
        return $user->isOwnerOf($book->library);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Book $book
     * @return mixed
     */
    public function delete(User $user, Book $book)
    {
        return $user->isOwnerOf($book->library);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Book $book
     * @return mixed
     */
    public function restore(User $user, Book $book)
    {
        return $user->isOwnerOf($book->library);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Book $book
     * @return mixed
     */
    public function forceDelete(User $user, Book $book)
    {
        return $user->isOwnerOf($book->library);
    }
}
