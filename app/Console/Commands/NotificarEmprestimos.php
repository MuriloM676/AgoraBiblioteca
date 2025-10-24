<?php

namespace App\Console\Commands;

use App\Models\Emprestimo;
use App\Notifications\EmprestimoProximoVencimento;
use App\Notifications\EmprestimoAtrasado;
use Illuminate\Console\Command;

class NotificarEmprestimos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emprestimos:notificar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica usuários sobre empréstimos próximos do vencimento e atrasados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando verificação de empréstimos...');

        // Notificar empréstimos próximos do vencimento (3 dias)
        $proximosVencimento = Emprestimo::proximoVencimento(3)->get();
        
        foreach ($proximosVencimento as $emprestimo) {
            $emprestimo->notificarProximoVencimento();
            $this->info("Notificação enviada para {$emprestimo->user->name} - Livro: {$emprestimo->livro->titulo}");
        }

        // Notificar empréstimos atrasados
        $atrasados = Emprestimo::atrasado()->get();
        
        foreach ($atrasados as $emprestimo) {
            $emprestimo->notificarAtraso();
            
            // Atualiza status para atrasado
            if ($emprestimo->status === 'ativo') {
                $emprestimo->update(['status' => 'atrasado']);
            }
            
            // Calcula e atualiza multa
            $multa = $emprestimo->calcularMulta();
            $emprestimo->update(['multa' => $multa]);
            
            $this->warn("Notificação de atraso enviada para {$emprestimo->user->name} - Livro: {$emprestimo->livro->titulo} - Multa: R$ " . number_format($multa, 2, ',', '.'));
        }

        // Expirar reservas vencidas
        $reservasExpiradas = \App\Models\Reserva::expiradas()->get();
        
        foreach ($reservasExpiradas as $reserva) {
            $reserva->update(['status' => 'expirada']);
            $this->comment("Reserva #{$reserva->id} expirada - Usuário: {$reserva->user->name}");
        }

        $this->info("Processo concluído!");
        $this->info("Empréstimos próximos do vencimento: {$proximosVencimento->count()}");
        $this->info("Empréstimos atrasados: {$atrasados->count()}");
        $this->info("Reservas expiradas: {$reservasExpiradas->count()}");

        return Command::SUCCESS;
    }
}
