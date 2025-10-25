<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmprestimoResource\Pages;
use App\Models\Emprestimo;
use App\Models\User;
use App\Models\Livro;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class EmprestimoResource extends Resource
{
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        if (auth()->user()?->hasRole('user')) {
            $query->where('user_id', auth()->id());
        }
        return $query;
    }
    protected static ?string $model = Emprestimo::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Biblioteca';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Empréstimo';

    protected static ?string $pluralModelLabel = 'Empréstimos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Empréstimo')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuário')
                            ->options(User::where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\Select::make('livro_id')
                            ->label('Livro')
                            ->options(Livro::disponivel()->get()->pluck('titulo', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Somente livros disponíveis'),
                        
                        Forms\Components\DateTimePicker::make('data_emprestimo')
                            ->label('Data do Empréstimo')
                            ->default(now())
                            ->required(),
                        
                        Forms\Components\DateTimePicker::make('data_devolucao_prevista')
                            ->label('Data de Devolução Prevista')
                            ->default(now()->addDays(14))
                            ->required()
                            ->minDate(now()),
                        
                        Forms\Components\DateTimePicker::make('data_devolucao_real')
                            ->label('Data de Devolução Real')
                            ->visible(fn ($record) => $record?->status === 'devolvido'),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'ativo' => 'Ativo',
                                'devolvido' => 'Devolvido',
                                'atrasado' => 'Atrasado',
                                'cancelado' => 'Cancelado',
                            ])
                            ->default('ativo')
                            ->required(),
                        
                        Forms\Components\TextInput::make('renovacoes')
                            ->label('Renovações')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(2)
                            ->helperText('Máximo de 2 renovações'),
                        
                        Forms\Components\TextInput::make('multa')
                            ->label('Multa (R$)')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->prefix('R$')
                            ->disabled()
                            ->dehydrated(),
                        
                        Forms\Components\Textarea::make('observacoes')
                            ->label('Observações')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuário')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('livro.titulo')
                    ->label('Livro')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('data_emprestimo')
                    ->label('Empréstimo')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('data_devolucao_prevista')
                    ->label('Devolução Prevista')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn (Emprestimo $record) => $record->isAtrasado() ? 'danger' : 'success'),
                
                Tables\Columns\TextColumn::make('data_devolucao_real')
                    ->label('Devolvido em')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'ativo',
                        'primary' => 'devolvido',
                        'danger' => 'atrasado',
                        'warning' => 'cancelado',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ativo' => 'Ativo',
                        'devolvido' => 'Devolvido',
                        'atrasado' => 'Atrasado',
                        'cancelado' => 'Cancelado',
                    }),
                
                Tables\Columns\TextColumn::make('renovacoes')
                    ->label('Renov.')
                    ->alignCenter()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('multa')
                    ->label('Multa')
                    ->money('BRL')
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'ativo' => 'Ativo',
                        'devolvido' => 'Devolvido',
                        'atrasado' => 'Atrasado',
                        'cancelado' => 'Cancelado',
                    ]),
                
                Tables\Filters\SelectFilter::make('user')
                    ->label('Usuário')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\Filter::make('atrasados')
                    ->label('Somente atrasados')
                    ->query(fn ($query) => $query->atrasado()),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('data_inicio')
                            ->label('Data Início'),
                        Forms\Components\DatePicker::make('data_fim')
                            ->label('Data Fim'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['data_inicio'], fn ($q, $date) => $q->whereDate('data_emprestimo', '>=', $date))
                            ->when($data['data_fim'], fn ($q, $date) => $q->whereDate('data_emprestimo', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('renovar')
                        ->label('Renovar')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->visible(fn (Emprestimo $record) => $record->status === 'ativo' && $record->renovacoes < 2)
                        ->action(function (Emprestimo $record) {
                            if ($record->renovar()) {
                                Notification::make()
                                    ->title('Empréstimo renovado com sucesso!')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Não foi possível renovar o empréstimo')
                                    ->danger()
                                    ->send();
                            }
                        }),
                    
                    Tables\Actions\Action::make('devolver')
                        ->label('Devolver')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (Emprestimo $record) => $record->status === 'ativo')
                        ->action(function (Emprestimo $record) {
                            $record->devolver();
                            
                            Notification::make()
                                ->title('Livro devolvido com sucesso!')
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmprestimos::route('/'),
            'create' => Pages\CreateEmprestimo::route('/create'),
            'edit' => Pages\EditEmprestimo::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'ativo')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
