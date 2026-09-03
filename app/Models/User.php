<?php

namespace App\Models;

use App\Enums\UserGender;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'username',
    'email',
    'password',
    'firstname',
    'lastname',
    'birthday',
    'country',
    'gender',
    'locale',
    'theme'
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function fullName(): string
    {
        return trim("$this->firstname $this->lastname");
    }

    public function initials(): string
    {
        if ($this->firstname && $this->lastname) {
            return mb_strtoupper(mb_substr((string) $this->firstname, 0, 1))
                ."\u{200C}"
                .mb_strtoupper(mb_substr((string) $this->lastname, 0, 1));
        }

        $first = mb_strtoupper(mb_substr((string) $this->username, 0, 1));
        $second = mb_strtoupper(mb_substr((string) $this->username, 1, 1));

        return $second === '' ? $first : $first."\u{200C}".$second;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birthday' => 'date:Y-m-d',
            'gender' => UserGender::class,
            'password' => 'hashed',
        ];
    }
}
