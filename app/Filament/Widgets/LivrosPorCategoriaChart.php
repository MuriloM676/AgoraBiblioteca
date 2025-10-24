<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Livro;

class LivrosPorCategoriaChart extends ChartWidget
{
    protected static ?string $heading = 'Livros por Categoria';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $categorias = \App\Models\Categoria::withCount('livros')
            ->having('livros_count', '>', 0)
            ->orderBy('livros_count', 'desc')
            ->take(10)
            ->get();

        $labels = $categorias->pluck('nome')->toArray();
        $data = $categorias->pluck('livros_count')->toArray();

        $colors = [
            '#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6',
            '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#6366f1'
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Quantidade de Livros',
                    'data' => $data,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
