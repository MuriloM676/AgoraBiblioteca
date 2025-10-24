<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LivroResource\Pages;
use App\Models\Livro;
use App\Models\Categoria;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class LivroResource extends Resource
{
    protected static ?string $model = Livro::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Biblioteca';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Livro';

    protected static ?string $pluralModelLabel = 'Livros';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Básicas')
                    ->schema([
                        Forms\Components\TextInput::make('titulo')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('autor')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('categoria_id')
                            ->label('Categoria')
                            ->options(Categoria::where('is_active', true)->pluck('nome', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nome')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('descricao')
                                    ->rows(3),
                            ]),
                        
                        Forms\Components\TextInput::make('isbn')
                            ->label('ISBN')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\TextInput::make('editora')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('ano_publicacao')
                            ->label('Ano de Publicação')
                            ->numeric()
                            ->minValue(1000)
                            ->maxValue(date('Y')),
                        
                        Forms\Components\TextInput::make('numero_paginas')
                            ->label('Número de Páginas')
                            ->numeric()
                            ->minValue(1),
                        
                        Forms\Components\Select::make('idioma')
                            ->options([
                                'Português' => 'Português',
                                'Inglês' => 'Inglês',
                                'Espanhol' => 'Espanhol',
                                'Francês' => 'Francês',
                                'Alemão' => 'Alemão',
                                'Italiano' => 'Italiano',
                                'Outro' => 'Outro',
                            ])
                            ->default('Português')
                            ->required(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Sinopse')
                    ->schema([
                        Forms\Components\RichEditor::make('sinopse')
                            ->label('')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                
                Forms\Components\Section::make('Arquivos')
                    ->schema([
                        Forms\Components\FileUpload::make('capa')
                            ->label('Capa do Livro')
                            ->image()
                            ->imageEditor()
                            ->directory('livros/capas')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg']),
                        
                        Forms\Components\FileUpload::make('arquivo_pdf')
                            ->label('Arquivo PDF')
                            ->directory('livros/pdfs')
                            ->visibility('public')
                            ->maxSize(51200)
                            ->acceptedFileTypes(['application/pdf'])
                            ->helperText('Arquivo digital do livro (máximo 50MB)'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                
                Forms\Components\Section::make('Controle de Estoque')
                    ->schema([
                        Forms\Components\TextInput::make('quantidade_total')
                            ->label('Quantidade Total')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                if ($get('quantidade_disponivel') === null) {
                                    $set('quantidade_disponivel', $state);
                                }
                            }),
                        
                        Forms\Components\TextInput::make('quantidade_disponivel')
                            ->label('Quantidade Disponível')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(0)
                            ->maxValue(fn (callable $get) => $get('quantidade_total') ?? 1),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'disponivel' => 'Disponível',
                                'indisponivel' => 'Indisponível',
                                'manutencao' => 'Em Manutenção',
                            ])
                            ->default('disponivel')
                            ->required(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('capa')
                    ->circular()
                    ->defaultImageUrl(url('/images/no-cover.png')),
                
                Tables\Columns\TextColumn::make('titulo')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('autor')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('categoria.nome')
                    ->label('Categoria')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('quantidade_disponivel')
                    ->label('Disponível')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('quantidade_total')
                    ->label('Total')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'disponivel',
                        'danger' => 'indisponivel',
                        'warning' => 'manutencao',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'disponivel' => 'Disponível',
                        'indisponivel' => 'Indisponível',
                        'manutencao' => 'Manutenção',
                    }),
                
                Tables\Columns\IconColumn::make('arquivo_pdf')
                    ->label('PDF')
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Cadastrado em')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria')
                    ->relationship('categoria', 'nome')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'disponivel' => 'Disponível',
                        'indisponivel' => 'Indisponível',
                        'manutencao' => 'Em Manutenção',
                    ]),
                
                Tables\Filters\Filter::make('com_pdf')
                    ->label('Com arquivo PDF')
                    ->query(fn ($query) => $query->whereNotNull('arquivo_pdf')),
                
                Tables\Filters\TernaryFilter::make('quantidade_disponivel')
                    ->label('Disponibilidade')
                    ->placeholder('Todos')
                    ->trueLabel('Somente disponíveis')
                    ->falseLabel('Somente indisponíveis')
                    ->queries(
                        true: fn ($query) => $query->where('quantidade_disponivel', '>', 0),
                        false: fn ($query) => $query->where('quantidade_disponivel', '=', 0),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('qrcode')
                    ->label('QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->modalContent(fn (Livro $record) => view('filament.pages.qrcode', ['livro' => $record]))
                    ->modalSubmitAction(false),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informações do Livro')
                    ->schema([
                        Infolists\Components\ImageEntry::make('capa')
                            ->height(200),
                        
                        Infolists\Components\TextEntry::make('titulo'),
                        
                        Infolists\Components\TextEntry::make('autor'),
                        
                        Infolists\Components\TextEntry::make('categoria.nome')
                            ->label('Categoria')
                            ->badge(),
                        
                        Infolists\Components\TextEntry::make('isbn')
                            ->label('ISBN'),
                        
                        Infolists\Components\TextEntry::make('editora'),
                        
                        Infolists\Components\TextEntry::make('ano_publicacao')
                            ->label('Ano de Publicação'),
                        
                        Infolists\Components\TextEntry::make('numero_paginas')
                            ->label('Número de Páginas'),
                        
                        Infolists\Components\TextEntry::make('idioma'),
                        
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'disponivel' => 'success',
                                'indisponivel' => 'danger',
                                'manutencao' => 'warning',
                            }),
                    ])
                    ->columns(2),
                
                Infolists\Components\Section::make('Sinopse')
                    ->schema([
                        Infolists\Components\TextEntry::make('sinopse')
                            ->html()
                            ->columnSpanFull(),
                    ]),
                
                Infolists\Components\Section::make('Estoque')
                    ->schema([
                        Infolists\Components\TextEntry::make('quantidade_total')
                            ->label('Quantidade Total'),
                        
                        Infolists\Components\TextEntry::make('quantidade_disponivel')
                            ->label('Quantidade Disponível'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLivros::route('/'),
            'create' => Pages\CreateLivro::route('/create'),
            'view' => Pages\ViewLivro::route('/{record}'),
            'edit' => Pages\EditLivro::route('/{record}/edit'),
        ];
    }
}
