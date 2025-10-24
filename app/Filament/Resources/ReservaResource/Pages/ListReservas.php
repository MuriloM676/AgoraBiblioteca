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
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todas'),
            
            'pendentes' => Tab::make('Pendentes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pendente'))
                ->badge(fn () => \App\Models\Reserva::where('status', 'pendente')->count())
                ->badgeColor('warning'),
            
            'confirmadas' => Tab::make('Confirmadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'confirmada')),
            
            'expiradas' => Tab::make('Expiradas')
                ->modifyQueryUsing(fn (Builder $query) => $query->expiradas())
                ->badge(fn () => \App\Models\Reserva::expiradas()->count())
                ->badgeColor('danger'),
        ];
    }
}
