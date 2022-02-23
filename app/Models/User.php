<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    const ROLE_SUPER_ADMIN = 'super-admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'zip',
        'city',
        'country_code',
        'password',
        'company_id',
        'owner_id'
    ];

    public function getNameAttribute($value)
    {
        if (App::environment('production')) {

            $value =
                \Auth::user()->hasRole(self::ROLE_SUPER_ADMIN)
                    ? \Str::mask($value, '*', 3)
                    : $value;
        }
        return $value;
    }

    public function getEmailAttribute($value)
    {
        if (App::environment('production')) {
            $value =
                \Auth::user()->hasRole(self::ROLE_SUPER_ADMIN)
                    ? \Str::mask($value, '*', 3)
                    : $value;
        }
        return $value;
    }

    public function getOibAttribute($value)
    {
        if (App::environment('production')) {
            $value =
                \Auth::user()->hasRole(self::ROLE_SUPER_ADMIN)
                    ? \Str::mask($value, '*', 3)
                    : $value;
        }
        return $value;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
