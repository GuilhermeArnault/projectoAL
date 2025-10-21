<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'alojamento_id',
        'caminho',
        'descricao',
    ];

    public function alojamento()
    {
        return $this->belongsTo(Alojamento::class);
    }
}

