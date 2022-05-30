<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedore extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'contacto', 'telefono_contacto'
    ];

    public $timestamps = false;

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
}
