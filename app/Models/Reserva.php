<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'alojamento_id',
        'data_checkin',
        'data_checkout',
        'estado',
        'total',
    ];

    // 🔗 Relações
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function alojamento()
    {
        return $this->belongsTo(Alojamento::class);
    }

    public function pagamento()
    {
        return $this->hasOne(Pagamento::class);
    }
}
