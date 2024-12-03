<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'dni',
        'email',
        'password',
        'usertype_id',
        'zone_id',
        'license'
    ];

    // protected $hidden = [
    //     'password',
    //     'remember_token',
    //     'two_factor_recovery_codes',
    //     'two_factor_secret',
    // ];

    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

    // protected $appends = [
    //     'profile_photo_url',
    // ];

    // Relación con UserType
    public function userType()
    {
        return $this->belongsTo(UserType::class, 'usertype_id');
    }

    // Relación con Zone
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }
}
