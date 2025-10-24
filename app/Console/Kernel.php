<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Notificar empréstimos todos os dias às 9h
        $schedule->command('emprestimos:notificar')
            ->dailyAt('09:00')
            ->emailOutputOnFailure('admin@biblioteca.com');

        // Limpar notificações antigas (mais de 30 dias)
        $schedule->command('model:prune', ['--model' => 'Illuminate\\Notifications\\DatabaseNotification'])
            ->weekly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
