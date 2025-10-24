<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Emprestimo;
use Illuminate\Support\Carbon;

class EmprestimosChart extends ChartWidget
{
    protected static ?string $heading = 'Empréstimos nos Últimos 7 Dias';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');
            
            $count = Emprestimo::whereDate('data_emprestimo', $date->format('Y-m-d'))->count();
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Empréstimos',
                    'data' => $data,
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#2563eb',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
