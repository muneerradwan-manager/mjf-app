<?php

namespace App\Modules\Tenant\Infrastructure\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TenantUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id');
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'created_by');
    }
}
