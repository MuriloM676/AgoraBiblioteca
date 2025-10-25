<?php

namespace App\Filament\Resources\ReservaResource\Pages;

use App\Filament\Resources\ReservaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListReservas extends ListRecords
{
    protected static string $resource = ReservaResource::class;

    protected function getHeaderActions(): array
    {
        return auth()->user()?->hasRole('admin')
            ? [Actions\CreateAction::make()]
            : [];
    }

    public function getTabs(): array
    {
        $user = auth()->user();
        $baseQuery = fn (Builder $query) => $user->hasRole('admin') ? $query : $query->where('user_id', $user->id);
        return [
            'todos' => Tab::make('Todas')
                ->modifyQueryUsing($baseQuery),
            'pendentes' => Tab::make('Pendentes')
                ->modifyQueryUsing(fn (Builder $query) => $baseQuery($query)->where('status', 'pendente'))
                ->badge(fn () => $user->hasRole('admin') ? \App\Models\Reserva::where('status', 'pendente')->count() : \App\Models\Reserva::where('user_id', $user->id)->where('status', 'pendente')->count())
                ->badgeColor('warning'),
            'confirmadas' => Tab::make('Confirmadas')
                ->modifyQueryUsing(fn (Builder $query) => $baseQuery($query)->where('status', 'confirmada')),
            'expiradas' => Tab::make('Expiradas')
                ->modifyQueryUsing(fn (Builder $query) => $baseQuery($query)->expiradas())
                ->badge(fn () => $user->hasRole('admin') ? \App\Models\Reserva::expiradas()->count() : \App\Models\Reserva::where('user_id', $user->id)->where('status', 'expirada')->count())
                ->badgeColor('danger'),
        ];
    }
}
