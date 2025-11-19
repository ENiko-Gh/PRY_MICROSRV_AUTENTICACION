<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'titulo',
        'contenido',
        'estado', // 'publicado' o 'borrador'
    ];

    /**
     * Un artículo pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
