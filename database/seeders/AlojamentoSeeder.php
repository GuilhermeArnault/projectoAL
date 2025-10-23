<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Alojamento;

class AlojamentoSeeder extends Seeder
{
    public function run(): void
    {
        Alojamento::create([
            'titulo' => 'Quarto Verão',
            'descricao' => 'Vista incrível para o oceano, perfeita para férias relaxantes.',
            'preco_noite' => 120.00,
        ]);

        Alojamento::create([
            'titulo' => 'Quarto Inverno',
            'descricao' => 'Localização excelente, perto de tudo.',
            'preco_noite' => 80.00,
        ]);


    }
}
