<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'is_admin',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    // @link https://laravel.com/docs/7.x/eloquent#events
    protected static function booted()
    {
        static::saving(function (User $user) {
            if ($user->isDirty('password')) {
                $user->password = \Hash::make($user->password);
            }
            unset($user->password_confirmation);
        });
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->is_admin;
    }

    /**
     * @return BelongsToMany
     */
    public function libraries()
    {
        return $this->belongsToMany(Library::class)->withPivot(['role']);
    }

    /**
     * @param Library $library
     * @return bool
     */
    public function isMemberOf(Library $library)
    {
        if ($this->isAdmin()) return true;

        return $library->members()->where('user.id', '=', $this->id)->count('id') > 0;
    }

    /**
     * @param Library $library
     * @return bool
     */
    public function isOwnerOf(Library $library)
    {
        if ($this->isAdmin()) return true;

        return $library->members()->where('user.id', '=', $this->id)->where('role', '=', 'owner')->count('id') > 0;
    }
}
