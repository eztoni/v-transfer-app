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
    const ROLE_RECEPTION = 'reception';

    const ROLE_REPORTAGENT = 'reportagent';
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

    protected $attributes = [
      'city' => '-',
      'zip' => '-',
    ];



    public function getNameAttribute($value)
    {
        return $value;
    }

    public function getEmailAttribute($value)
    {
        return $value;
    }

    public function getOibAttribute($value)
    {

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

    public function changeCompany($companyId){

    }

    public function changeOwner(){

    }

    public function changeDestination(){

    }

    //CURRENT DESTINATION IN WHICH USER IS IN (DESTIONATION SELECTOR)
    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function availableDestinations()
    {
        return $this->belongsToMany(Destination::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    private function maskValue($value){

        if (App::environment('production')) {
            $value =
                \Auth::user()->hasRole(self::ROLE_SUPER_ADMIN)
                    ? \Str::mask($value, '*', 3)
                    : $value;
        }

        return $value;
    }
}
