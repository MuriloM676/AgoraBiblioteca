<?php

namespace App\Filament\Resources\ReservaResource\Pages;

use App\Filament\Resources\ReservaResource;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Filament\Resources\Pages\CreateRecord;

class CreateReserva extends CreateRecord
{
    protected static string $resource = ReservaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // If a normal user somehow opens this page, force the reservation to their user_id
        if (Auth::check() && Auth::user()->hasRole('user')) {
            $data['user_id'] = Auth::id();
        }

        // Prevent duplicate reservations of the same book by the same user when status is active/pending
        $exists = Reserva::query()
            ->where('user_id', $data['user_id'] ?? null)
            ->where('livro_id', $data['livro_id'] ?? null)
            ->whereIn('status', ['pendente', 'ativa', 'confirmada'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'livro_id' => 'Este usuário já possui uma reserva ativa para este livro.',
            ]);
        }

        return $data;
    }
}
