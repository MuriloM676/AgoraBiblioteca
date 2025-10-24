<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livros', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('autor');
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->string('isbn')->unique()->nullable();
            $table->text('sinopse')->nullable();
            $table->integer('ano_publicacao')->nullable();
            $table->string('editora')->nullable();
            $table->integer('numero_paginas')->nullable();
            $table->string('idioma')->default('Português');
            $table->string('capa')->nullable();
            $table->string('arquivo_pdf')->nullable();
            $table->integer('quantidade_total')->default(1);
            $table->integer('quantidade_disponivel')->default(1);
            $table->enum('status', ['disponivel', 'indisponivel', 'manutencao'])->default('disponivel');
            $table->string('qr_code')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livros');
    }
};
