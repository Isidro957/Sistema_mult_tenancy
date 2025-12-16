<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class TenantUser extends Authenticatable
{
    use HasApiTokens;

    // Conexão específica do tenant
    protected $connection = 'tenant';

    // Campos preenchíveis
    protected $fillable = ['id', 'name', 'email', 'password', 'role'];

    // Campos ocultos em JSON
    protected $hidden = ['password', 'remember_token'];

    // UUID como chave primária
    public $incrementing = false;
    protected $keyType = 'string';

    // Nome da tabela
    protected $table = 'users';

    // Casts opcionais
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Gerar UUID automaticamente
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
