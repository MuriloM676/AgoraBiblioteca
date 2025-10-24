<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Livro;
use App\Models\Categoria;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LivroTest extends TestCase
{
    use RefreshDatabase;

    public function test_livro_pode_ser_criado()
    {
        $categoria = Categoria::factory()->create();
        
        $livro = Livro::create([
            'titulo' => 'Teste de Livro',
            'autor' => 'Autor Teste',
            'categoria_id' => $categoria->id,
            'isbn' => '9781234567890',
            'sinopse' => 'Uma sinopse de teste',
            'ano_publicacao' => 2024,
            'editora' => 'Editora Teste',
            'numero_paginas' => 300,
            'idioma' => 'Português',
            'quantidade_total' => 5,
            'quantidade_disponivel' => 5,
        ]);

        $this->assertDatabaseHas('livros', [
            'titulo' => 'Teste de Livro',
            'autor' => 'Autor Teste',
        ]);
    }

    public function test_livro_pode_decrementar_quantidade()
    {
        $livro = Livro::factory()->create([
            'quantidade_total' => 5,
            'quantidade_disponivel' => 5,
            'status' => 'disponivel',
        ]);

        $livro->decrementarQuantidade();

        $this->assertEquals(4, $livro->fresh()->quantidade_disponivel);
    }

    public function test_livro_fica_indisponivel_quando_quantidade_zero()
    {
        $livro = Livro::factory()->create([
            'quantidade_total' => 1,
            'quantidade_disponivel' => 1,
            'status' => 'disponivel',
        ]);

        $livro->decrementarQuantidade();

        $this->assertEquals(0, $livro->fresh()->quantidade_disponivel);
        $this->assertEquals('indisponivel', $livro->fresh()->status);
    }

    public function test_livro_pode_incrementar_quantidade()
    {
        $livro = Livro::factory()->create([
            'quantidade_total' => 5,
            'quantidade_disponivel' => 3,
        ]);

        $livro->incrementarQuantidade();

        $this->assertEquals(4, $livro->fresh()->quantidade_disponivel);
    }
}
