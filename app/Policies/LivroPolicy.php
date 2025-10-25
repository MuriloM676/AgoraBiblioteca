<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Livro;

class LivroPolicy
{
    /**
     * Determina se o usuário pode visualizar qualquer livro.
     */
    public function viewAny(User $user): bool
    {
    return true; // Livros podem ser vistos por todos
    }

    /**
     * Determina se o usuário pode visualizar o livro.
     */
    public function view(User $user, Livro $livro): bool
    {
        return true;
    }

    /**
     * Determina se o usuário pode criar livros.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determina se o usuário pode atualizar o livro.
     */
    public function update(User $user, Livro $livro): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determina se o usuário pode deletar o livro.
     */
    public function delete(User $user, Livro $livro): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determina se o usuário pode fazer upload de arquivos.
     */
    public function uploadFiles(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
