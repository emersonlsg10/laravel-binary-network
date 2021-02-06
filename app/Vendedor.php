<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendedor extends Model
{
    protected $fillable = [
        'nome', 'pai', 'filhoesquerda', 'filhodireita', 'plano'
    ];
}
