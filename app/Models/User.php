<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role_id',
        'organization_unit_id',
        'nik',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the device tokens for FCM
     */
    public function deviceTokens()
    {
        return $this->hasMany(UserDeviceToken::class);
    }

    /**
     * Get notification inbox for this user (our custom App\Models\Notification)
     * Named 'appNotifications' to avoid conflict with Notifiable trait's notifications().
     */
    public function appNotifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->role->name === $role;
        }
        return $this->role->id === $role->id;
    }

    public function hasPermission($permission)
    {
        return $this->role->hasPermission($permission);
    }

    public function organizationUnit()
    {
        return $this->belongsTo(OrganizationUnit::class);
    }

    /**
     * Check if user has a NIK set
     */
    public function hasNik(): bool
    {
        return !empty($this->nik);
    }
}
