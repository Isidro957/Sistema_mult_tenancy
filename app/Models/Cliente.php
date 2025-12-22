<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    // 🔑 SEMPRE usar a conexão tenant
    protected $connection = 'tenant';

    protected $table = 'clientes';

    protected $fillable = [
        'tenant_id',
        'nome',
        'email',
        'telefone',
    ];
        protected $casts = [
        'tenant_id' => 'string', 
    ];
}
