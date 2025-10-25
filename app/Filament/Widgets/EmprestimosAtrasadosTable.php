<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Emprestimo;

class EmprestimosAtrasadosTable extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('admin');
    }
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Empréstimos Atrasados')
            ->query(
                Emprestimo::query()
                    ->atrasado()
                    ->with(['user', 'livro'])
                    ->orderBy('data_devolucao_prevista', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuário')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('user.email')
                    ->label('E-mail')
                    ->searchable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('livro.titulo')
                    ->label('Livro')
                    ->searchable()
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('data_devolucao_prevista')
                    ->label('Devolução Prevista')
                    ->date('d/m/Y')
                    ->color('danger'),
                
                Tables\Columns\TextColumn::make('dias_atraso')
                    ->label('Dias de Atraso')
                    ->getStateUsing(fn (Emprestimo $record) => $record->diasAtraso())
                    ->badge()
                    ->color('danger'),
                
                Tables\Columns\TextColumn::make('multa_calculada')
                    ->label('Multa')
                    ->getStateUsing(fn (Emprestimo $record) => 'R$ ' . number_format($record->calcularMulta(), 2, ',', '.'))
                    ->color('danger'),
            ])
            ->paginated([5, 10, 25]);
    }
}
