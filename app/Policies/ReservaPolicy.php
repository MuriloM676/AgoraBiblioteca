<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reserva;

class ReservaPolicy
{
    /**
     * Determina se o usuário pode visualizar qualquer reserva.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determina se o usuário pode visualizar a reserva.
     */
    public function view(User $user, Reserva $reserva): bool
    {
        return $user->hasRole('admin') || $user->id === $reserva->user_id;
    }

    /**
     * Determina se o usuário pode criar reservas.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determina se o usuário pode atualizar a reserva.
     */
    public function update(User $user, Reserva $reserva): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determina se o usuário pode deletar a reserva.
     */
    public function delete(User $user, Reserva $reserva): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->id === $reserva->user_id && $reserva->status === 'pendente';
    }

    /**
     * Determina se o usuário pode cancelar a reserva.
     */
    public function cancelar(User $user, Reserva $reserva): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->id === $reserva->user_id && $reserva->status === 'pendente';
    }

    /**
     * Determina se o usuário pode confirmar a reserva.
     */
    public function confirmar(User $user, Reserva $reserva): bool
    {
        return $user->hasRole('admin');
    }
}
