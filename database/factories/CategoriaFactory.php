<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoriaFactory extends Factory
{
    protected $model = Categoria::class;

    public function definition(): array
    {
        $nome = $this->faker->unique()->randomElement([
            'Ficção Científica',
            'Romance',
            'Suspense',
            'Terror',
            'Fantasia',
            'Biografia',
            'História',
            'Filosofia',
            'Autoajuda',
            'Tecnologia',
            'Ciência',
            'Poesia',
            'Drama',
            'Aventura',
            'Policial',
        ]);

        return [
            'nome' => $nome,
            'slug' => Str::slug($nome),
            'descricao' => $this->faker->paragraph(),
            'is_active' => true,
        ];
    }
}
