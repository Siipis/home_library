<?php

namespace App\Policies;

use App\Category;
use App\Library;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Http\Request;

class CategoryPolicy
{
    use HandlesAuthorization;

    /**
     * @var Library
     */
    private $library;

    public function __construct(Request $request)
    {
        $this->library = $request->route('library');
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @param Library $library
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
     * @param Category $category
     * @return mixed
     */
    public function view(User $user, Category $category)
    {
        return $user->isMemberOf($category->library);
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
     * @param Category $category
     * @return mixed
     */
    public function update(User $user, Category $category)
    {
        return $user->isOwnerOf($category->library);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Category $category
     * @return mixed
     */
    public function delete(User $user, Category $category)
    {
        return $user->isOwnerOf($category->library);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Category $category
     * @return mixed
     */
    public function restore(User $user, Category $category)
    {
        return $user->isOwnerOf($category->library);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Category $category
     * @return mixed
     */
    public function forceDelete(User $user, Category $category)
    {
        return $user->isOwnerOf($category->library);
    }
}
