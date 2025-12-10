<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasPermissionsTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class   User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Notifiable, HasPermissionsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'image',
        'active',
        'password',
        'locked_until',
        'failed_login_attempts',
        'role_id',
        'kode_satker',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'locked_until' => 'datetime',
        ];
    }

    public function roles()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function app_settings()
    {
        return $this->hasOne(AppSetting::class, 'user_id');
    }

    public function satker()
    {
        return $this->belongsTo(Satker::class, 'kode_satker', 'kode_satker');
    }
}
