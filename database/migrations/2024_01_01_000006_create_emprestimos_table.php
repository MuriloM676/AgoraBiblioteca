<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emprestimos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('livro_id')->constrained('livros')->onDelete('cascade');
            $table->timestamp('data_emprestimo');
            $table->timestamp('data_devolucao_prevista');
            $table->timestamp('data_devolucao_real')->nullable();
            $table->enum('status', ['ativo', 'devolvido', 'atrasado', 'cancelado'])->default('ativo');
            $table->integer('renovacoes')->default(0);
            $table->text('observacoes')->nullable();
            $table->decimal('multa', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emprestimos');
    }
};
