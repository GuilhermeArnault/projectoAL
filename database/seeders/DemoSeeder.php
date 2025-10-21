<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
        'name' => 'Admin',
        'email' => 'admin@local.com',
        'password' => bcrypt('123456'),
        'role' => 'admin',
    ]);

    Alojamento::create([
        'titulo' => 'Quarto Verao',
        'descricao' => 'Alojamento com vista panorâmica...',
        'preco_noite' => 80.00,
        'endereco' => 'Poiares'
    ]);
    }
}
