<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class TenantUser extends Authenticatable
{
    use HasApiTokens;

    protected $connection = 'tenant';
    protected $fillable = ['id', 'name', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];
    public $incrementing = false;
    protected $table = 'users';
    protected $keyType = 'string';

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
