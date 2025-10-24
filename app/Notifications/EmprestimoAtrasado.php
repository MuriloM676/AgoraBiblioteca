<?php

namespace App\Notifications;

use App\Models\Emprestimo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmprestimoAtrasado extends Notification
{
    use Queueable;

    protected $emprestimo;

    public function __construct(Emprestimo $emprestimo)
    {
        $this->emprestimo = $emprestimo;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $diasAtraso = $this->emprestimo->diasAtraso();
        $multa = $this->emprestimo->calcularMulta();

        return (new MailMessage)
            ->subject('Empréstimo em atraso - Biblioteca Digital')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Seu empréstimo do livro "' . $this->emprestimo->livro->titulo . '" está em atraso.')
            ->line('Data de devolução prevista: ' . $this->emprestimo->data_devolucao_prevista->format('d/m/Y'))
            ->line('Dias em atraso: ' . $diasAtraso)
            ->line('Multa acumulada: R$ ' . number_format($multa, 2, ',', '.'))
            ->line('Por favor, devolva o livro o quanto antes para evitar o acúmulo de multas.')
            ->action('Ver Empréstimo', url('/admin/emprestimos/' . $this->emprestimo->id))
            ->line('Obrigado pela compreensão!');
    }

    public function toArray($notifiable): array
    {
        return [
            'emprestimo_id' => $this->emprestimo->id,
            'livro_titulo' => $this->emprestimo->livro->titulo,
            'dias_atraso' => $this->emprestimo->diasAtraso(),
            'multa' => $this->emprestimo->calcularMulta(),
            'mensagem' => 'Empréstimo em atraso',
        ];
    }
}
