<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Livro;
use App\Models\Emprestimo;
use App\Models\User;
use App\Models\Reserva;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = [
            Stat::make('Total de Livros', Livro::count())
                ->description('Livros cadastrados')
                ->descriptionIcon('heroicon-o-book-open')
                ->color('primary')
                ->chart([7, 15, 22, 30, 35, 40, Livro::count()]),
            Stat::make('Livros Disponíveis', Livro::where('quantidade_disponivel', '>', 0)->count())
                ->description('Prontos para empréstimo')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Reservas Pendentes', Reserva::where('status', 'pendente')->count())
                ->description('Aguardando confirmação')
                ->descriptionIcon('heroicon-o-bookmark')
                ->color('info'),
            Stat::make('Usuários Ativos', User::where('is_active', true)->count())
                ->description('Cadastrados no sistema')
                ->descriptionIcon('heroicon-o-users')
                ->color('success')
                ->chart([20, 25, 30, 35, 40, 45, User::where('is_active', true)->count()]),
        ];
        if (auth()->user()?->hasRole('admin')) {
            array_splice($stats, 2, 0, [
                Stat::make('Empréstimos Ativos', Emprestimo::where('status', 'ativo')->count())
                    ->description('Em circulação')
                    ->descriptionIcon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->chart([5, 10, 15, 12, 8, 10, Emprestimo::where('status', 'ativo')->count()]),
                Stat::make('Empréstimos Atrasados', Emprestimo::atrasado()->count())
                    ->description('Devoluções pendentes')
                    ->descriptionIcon('heroicon-o-exclamation-triangle')
                    ->color('danger'),
            ]);
        }
        return $stats;
    }
}
