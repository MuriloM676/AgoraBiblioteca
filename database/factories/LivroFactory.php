<?php

namespace Database\Factories;

use App\Models\Livro;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

class LivroFactory extends Factory
{
    protected $model = Livro::class;

    public function definition(): array
    {
        $quantidadeTotal = $this->faker->numberBetween(1, 5);
        
        return [
            'titulo' => $this->faker->sentence(3),
            'autor' => $this->faker->name(),
            'categoria_id' => Categoria::inRandomOrder()->first()?->id ?? Categoria::factory(),
            'isbn' => $this->faker->unique()->isbn13(),
            'sinopse' => $this->faker->paragraphs(3, true),
            'ano_publicacao' => $this->faker->year(),
            'editora' => $this->faker->company(),
            'numero_paginas' => $this->faker->numberBetween(100, 800),
            'idioma' => $this->faker->randomElement(['Português', 'Inglês', 'Espanhol']),
            'quantidade_total' => $quantidadeTotal,
            'quantidade_disponivel' => $quantidadeTotal,
            'status' => 'disponivel',
        ];
    }
}
