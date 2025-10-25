<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Emprestimo;

class EmprestimoPolicy
{
    /**
     * Determina se o usuário pode visualizar qualquer empréstimo.
     */
    public function viewAny(User $user): bool
    {
        return true; // Listagem permitida; filtragem ocorre no Resource
    }

    /**
     * Determina se o usuário pode visualizar o empréstimo.
     */
    public function view(User $user, Emprestimo $emprestimo): bool
    {
        return $user->hasRole('admin') || $user->id === $emprestimo->user_id;
    }

    /**
     * Determina se o usuário pode criar empréstimos.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determina se o usuário pode atualizar o empréstimo.
     */
    public function update(User $user, Emprestimo $emprestimo): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determina se o usuário pode deletar o empréstimo.
     */
    public function delete(User $user, Emprestimo $emprestimo): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determina se o usuário pode renovar o empréstimo.
     */
    public function renovar(User $user, Emprestimo $emprestimo): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->id === $emprestimo->user_id 
            && $emprestimo->status === 'ativo'
            && !$emprestimo->isAtrasado();
    }

    /**
     * Determina se o usuário pode devolver o empréstimo.
     */
    public function devolver(User $user, Emprestimo $emprestimo): bool
    {
        return $user->hasRole('admin');
    }
}
