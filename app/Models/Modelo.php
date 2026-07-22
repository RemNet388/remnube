<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Modelo extends Model
{
    use HasFactory;

    protected $fillable = ['marca_id', 'nombre'];

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }
}
