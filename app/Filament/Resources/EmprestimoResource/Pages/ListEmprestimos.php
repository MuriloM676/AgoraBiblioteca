<?php

namespace App\Filament\Resources\EmprestimoResource\Pages;

use App\Filament\Resources\EmprestimoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEmprestimos extends ListRecords
{
    protected static string $resource = EmprestimoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos'),
            
            'ativos' => Tab::make('Ativos')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'ativo'))
                ->badge(fn () => \App\Models\Emprestimo::where('status', 'ativo')->count()),
            
            'atrasados' => Tab::make('Atrasados')
                ->modifyQueryUsing(fn (Builder $query) => $query->atrasado())
                ->badge(fn () => \App\Models\Emprestimo::atrasado()->count())
                ->badgeColor('danger'),
            
            'devolvidos' => Tab::make('Devolvidos')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'devolvido')),
        ];
    }
}
