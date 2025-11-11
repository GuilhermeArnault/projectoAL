<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    // Se o nome da tabela for diferente de 'reservas', especifica aqui
    // protected $table = 'nome_da_tua_tabela';

    // Colunas que podem ser preenchidas via create() ou update()
    protected $fillable = [
        'user_id',
        'inicio',
        'fim',
        'estado',
        'preco_total'
    ];

    public static function calcularPreco($inicio, $fim)
    {
        $dias = (new \DateTime($inicio))->diff(new \DateTime($fim))->days;
        $precoPorNoite = 100; // Podes mudar para um campo vindo do alojamento
        return $dias * $precoPorNoite;
    }

}

