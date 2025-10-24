<?php

namespace Database\Factories;

use App\Models\Emprestimo;
use App\Models\User;
use App\Models\Livro;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmprestimoFactory extends Factory
{
    protected $model = Emprestimo::class;

    public function definition(): array
    {
        $dataEmprestimo = $this->faker->dateTimeBetween('-60 days', 'now');
        $dataDevolucaoPrevista = (clone $dataEmprestimo)->modify('+14 days');
        
        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'livro_id' => Livro::inRandomOrder()->first()?->id ?? Livro::factory(),
            'data_emprestimo' => $dataEmprestimo,
            'data_devolucao_prevista' => $dataDevolucaoPrevista,
            'status' => $this->faker->randomElement(['ativo', 'devolvido']),
            'renovacoes' => $this->faker->numberBetween(0, 2),
            'observacoes' => $this->faker->optional()->sentence(),
            'multa' => 0,
        ];
    }

    public function ativo(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ativo',
            'data_devolucao_real' => null,
        ]);
    }

    public function devolvido(): static
    {
        return $this->state(function (array $attributes) {
            $dataDevolucao = $this->faker->dateTimeBetween($attributes['data_emprestimo'], 'now');
            
            return [
                'status' => 'devolvido',
                'data_devolucao_real' => $dataDevolucao,
            ];
        });
    }

    public function atrasado(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ativo',
            'data_devolucao_prevista' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
            'data_devolucao_real' => null,
        ]);
    }
}
