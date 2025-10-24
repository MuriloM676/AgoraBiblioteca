<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Livro;
use App\Models\Emprestimo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class EmprestimoTest extends TestCase
{
    use RefreshDatabase;

    public function test_emprestimo_pode_ser_criado()
    {
        $user = User::factory()->create();
        $livro = Livro::factory()->create();

        $emprestimo = Emprestimo::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'data_emprestimo' => now(),
            'data_devolucao_prevista' => now()->addDays(14),
            'status' => 'ativo',
        ]);

        $this->assertDatabaseHas('emprestimos', [
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'status' => 'ativo',
        ]);
    }

    public function test_emprestimo_calcula_dias_de_atraso()
    {
        $emprestimo = Emprestimo::factory()->create([
            'data_devolucao_prevista' => Carbon::now()->subDays(5),
            'status' => 'ativo',
        ]);

        $this->assertEquals(5, $emprestimo->diasAtraso());
    }

    public function test_emprestimo_calcula_multa_corretamente()
    {
        $emprestimo = Emprestimo::factory()->create([
            'data_devolucao_prevista' => Carbon::now()->subDays(5),
            'status' => 'ativo',
        ]);

        // R$ 2,00 por dia * 5 dias = R$ 10,00
        $this->assertEquals(10.00, $emprestimo->calcularMulta());
    }

    public function test_emprestimo_pode_ser_renovado()
    {
        $emprestimo = Emprestimo::factory()->create([
            'renovacoes' => 0,
            'status' => 'ativo',
            'data_devolucao_prevista' => now()->addDays(7),
        ]);

        $dataPrevistaOriginal = $emprestimo->data_devolucao_prevista;
        
        $resultado = $emprestimo->renovar();

        $this->assertTrue($resultado);
        $this->assertEquals(1, $emprestimo->fresh()->renovacoes);
        $this->assertTrue($emprestimo->fresh()->data_devolucao_prevista->greaterThan($dataPrevistaOriginal));
    }

    public function test_emprestimo_nao_pode_ser_renovado_mais_de_duas_vezes()
    {
        $emprestimo = Emprestimo::factory()->create([
            'renovacoes' => 2,
            'status' => 'ativo',
        ]);

        $resultado = $emprestimo->renovar();

        $this->assertFalse($resultado);
        $this->assertEquals(2, $emprestimo->fresh()->renovacoes);
    }

    public function test_emprestimo_atrasado_nao_pode_ser_renovado()
    {
        $emprestimo = Emprestimo::factory()->create([
            'renovacoes' => 0,
            'status' => 'ativo',
            'data_devolucao_prevista' => Carbon::now()->subDays(1),
        ]);

        $resultado = $emprestimo->renovar();

        $this->assertFalse($resultado);
    }

    public function test_devolucao_incrementa_quantidade_disponivel()
    {
        $livro = Livro::factory()->create([
            'quantidade_total' => 5,
            'quantidade_disponivel' => 4,
        ]);

        $emprestimo = Emprestimo::factory()->create([
            'livro_id' => $livro->id,
            'status' => 'ativo',
        ]);

        // Decrementa ao criar
        $this->assertEquals(3, $livro->fresh()->quantidade_disponivel);

        $emprestimo->devolver();

        // Incrementa ao devolver
        $this->assertEquals(4, $livro->fresh()->quantidade_disponivel);
        $this->assertEquals('devolvido', $emprestimo->fresh()->status);
        $this->assertNotNull($emprestimo->fresh()->data_devolucao_real);
    }
}
