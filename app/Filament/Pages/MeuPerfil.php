<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MeuPerfil extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Minha Conta';

    protected static ?string $navigationLabel = 'Meu Perfil';

    protected static string $view = 'filament.pages.meu-perfil';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return true; // visível para todos autenticados
    }

    public function mount(): void
    {
        $user = Auth::user();
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function form(Form $form): Form
    {
        $userId = Auth::id();
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->rule(Rule::unique('users', 'email')->ignore($userId))
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->label('Nova senha')
                    ->password()
                    ->minLength(8)
                    ->revealable()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('password_confirmation')
                    ->label('Confirmar senha')
                    ->password()
                    ->same('password')
                    ->dehydrated(false),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $user = Auth::user();
        $data = $this->form->getState();

        $user->name = $data['name'] ?? $user->name;
        $user->email = $data['email'] ?? $user->email;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        Notification::make()
            ->title('Perfil atualizado com sucesso!')
            ->success()
            ->send();
    }
}
