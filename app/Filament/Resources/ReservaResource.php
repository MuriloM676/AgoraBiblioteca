<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservaResource\Pages;
use App\Models\Reserva;
use App\Models\User;
use App\Models\Livro;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class ReservaResource extends Resource
{
    protected static ?string $model = Reserva::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    protected static ?string $navigationGroup = 'Biblioteca';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Reserva';

    protected static ?string $pluralModelLabel = 'Reservas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações da Reserva')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuário')
                            ->options(User::where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\Select::make('livro_id')
                            ->label('Livro')
                            ->options(Livro::all()->pluck('titulo', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\DateTimePicker::make('data_reserva')
                            ->label('Data da Reserva')
                            ->default(now())
                            ->required(),
                        
                        Forms\Components\DateTimePicker::make('data_expiracao')
                            ->label('Data de Expiração')
                            ->default(now()->addDays(3))
                            ->required()
                            ->minDate(now()),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'pendente' => 'Pendente',
                                'ativa' => 'Ativa',
                                'confirmada' => 'Confirmada',
                                'cancelada' => 'Cancelada',
                                'expirada' => 'Expirada',
                                'convertida' => 'Convertida em Empréstimo',
                            ])
                            ->default('pendente')
                            ->required(),
                        
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
                
                Tables\Columns\TextColumn::make('data_reserva')
                    ->label('Data Reserva')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('data_expiracao')
                    ->label('Expira em')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn (Reserva $record) => $record->isExpirada() ? 'danger' : 'success'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pendente',
                        'success' => 'ativa',
                        'primary' => 'confirmada',
                        'danger' => 'cancelada',
                        'gray' => 'expirada',
                        'info' => 'convertida',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pendente' => 'Pendente',
                        'ativa' => 'Ativa',
                        'confirmada' => 'Confirmada',
                        'cancelada' => 'Cancelada',
                        'expirada' => 'Expirada',
                        'convertida' => 'Convertida',
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criada em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pendente' => 'Pendente',
                        'ativa' => 'Ativa',
                        'confirmada' => 'Confirmada',
                        'cancelada' => 'Cancelada',
                        'expirada' => 'Expirada',
                        'convertida' => 'Convertida',
                    ]),
                
                Tables\Filters\SelectFilter::make('user')
                    ->label('Usuário')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\Filter::make('expiradas')
                    ->label('Somente expiradas')
                    ->query(fn ($query) => $query->expiradas()),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('confirmar')
                        ->label('Confirmar')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (Reserva $record) => $record->status === 'pendente')
                        ->action(function (Reserva $record) {
                            $record->confirmar();
                            
                            Notification::make()
                                ->title('Reserva confirmada!')
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\Action::make('converter')
                        ->label('Converter em Empréstimo')
                        ->icon('heroicon-o-arrow-right-circle')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->visible(fn (Reserva $record) => in_array($record->status, ['pendente', 'confirmada']))
                        ->action(function (Reserva $record) {
                            if ($record->livro->isDisponivel()) {
                                $emprestimo = $record->converter_em_emprestimo();
                                
                                Notification::make()
                                    ->title('Reserva convertida em empréstimo!')
                                    ->success()
                                    ->body('Empréstimo #' . $emprestimo->id . ' criado.')
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Livro indisponível')
                                    ->danger()
                                    ->body('O livro não está disponível no momento.')
                                    ->send();
                            }
                        }),
                    
                    Tables\Actions\Action::make('cancelar')
                        ->label('Cancelar')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn (Reserva $record) => $record->status === 'pendente')
                        ->action(function (Reserva $record) {
                            $record->cancelar();
                            
                            Notification::make()
                                ->title('Reserva cancelada!')
                                ->warning()
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
            'index' => Pages\ListReservas::route('/'),
            'create' => Pages\CreateReserva::route('/create'),
            'edit' => Pages\EditReserva::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pendente')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
