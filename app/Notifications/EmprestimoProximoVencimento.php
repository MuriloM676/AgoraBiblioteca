<?php

namespace App\Notifications;

use App\Models\Emprestimo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmprestimoProximoVencimento extends Notification
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
        $diasRestantes = now()->diffInDays($this->emprestimo->data_devolucao_prevista);

        return (new MailMessage)
            ->subject('Empréstimo próximo do vencimento - Biblioteca Digital')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Seu empréstimo do livro "' . $this->emprestimo->livro->titulo . '" está próximo do vencimento.')
            ->line('Data de devolução prevista: ' . $this->emprestimo->data_devolucao_prevista->format('d/m/Y'))
            ->line('Faltam ' . $diasRestantes . ' dia(s) para a devolução.')
            ->line('Por favor, devolva o livro na data prevista para evitar multas.')
            ->action('Ver Empréstimo', url('/admin/emprestimos/' . $this->emprestimo->id))
            ->line('Obrigado por utilizar nossa biblioteca!');
    }

    public function toArray($notifiable): array
    {
        return [
            'emprestimo_id' => $this->emprestimo->id,
            'livro_titulo' => $this->emprestimo->livro->titulo,
            'data_devolucao_prevista' => $this->emprestimo->data_devolucao_prevista,
            'mensagem' => 'Empréstimo próximo do vencimento',
        ];
    }
}
