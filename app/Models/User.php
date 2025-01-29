<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles, HasFactory;
    protected $fillable = [
        'name',        // Include this to fix the error
        'email',
        'password',
        'role',
        'base_id',
    ];

    protected $guard_name = 'api';
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
    
    public function base(): BelongsTo
    {
        return $this->belongsTo(Base::class);
    }
}
