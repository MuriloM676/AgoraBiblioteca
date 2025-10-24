<?php

namespace Database\Factories;

use App\Models\Reserva;
use App\Models\User;
use App\Models\Livro;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservaFactory extends Factory
{
    protected $model = Reserva::class;

    public function definition(): array
    {
        $dataReserva = $this->faker->dateTimeBetween('-30 days', 'now');
        $dataExpiracao = (clone $dataReserva)->modify('+3 days');
        
        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'livro_id' => Livro::inRandomOrder()->first()?->id ?? Livro::factory(),
            'data_reserva' => $dataReserva,
            'data_expiracao' => $dataExpiracao,
            'status' => $this->faker->randomElement(['pendente', 'confirmada', 'cancelada']),
            'observacoes' => $this->faker->optional()->sentence(),
        ];
    }

    public function pendente(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pendente',
        ]);
    }

    public function confirmada(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmada',
        ]);
    }
}
