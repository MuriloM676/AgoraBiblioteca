<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\EmprestimoProximoVencimento;
use App\Notifications\EmprestimoAtrasado;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Emprestimo extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'livro_id',
        'data_emprestimo',
        'data_devolucao_prevista',
        'data_devolucao_real',
        'status',
        'renovacoes',
        'observacoes',
        'multa',
    ];

    protected function casts(): array
    {
        return [
            'data_emprestimo' => 'datetime',
            'data_devolucao_prevista' => 'datetime',
            'data_devolucao_real' => 'datetime',
            'renovacoes' => 'integer',
            'multa' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'livro_id', 'status', 'data_emprestimo', 'data_devolucao_prevista', 'data_devolucao_real', 'renovacoes'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Boot do modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($emprestimo) {
            if (!$emprestimo->data_emprestimo) {
                $emprestimo->data_emprestimo = now();
            }
            if (!$emprestimo->data_devolucao_prevista) {
                $emprestimo->data_devolucao_prevista = now()->addDays(14);
            }
            if (!$emprestimo->status) {
                $emprestimo->status = 'ativo';
            }
            if (!$emprestimo->renovacoes) {
                $emprestimo->renovacoes = 0;
            }
        });

        static::created(function ($emprestimo) {
            // Decrementa quantidade disponível do livro
            $emprestimo->livro->decrementarQuantidade();
        });

        static::updated(function ($emprestimo) {
            // Incrementa quantidade disponível quando devolvido
            if ($emprestimo->isDirty('status') && $emprestimo->status === 'devolvido') {
                $emprestimo->livro->incrementarQuantidade();
            }
        });
    }

    /**
     * Relacionamentos
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }

    /**
     * Scopes
     */
    public function scopeAtivo($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopeAtrasado($query)
    {
        return $query->where('status', 'ativo')
                     ->where('data_devolucao_prevista', '<', now());
    }

    public function scopeProximoVencimento($query, $dias = 3)
    {
        return $query->where('status', 'ativo')
                     ->whereBetween('data_devolucao_prevista', [
                         now(),
                         now()->addDays($dias)
                     ]);
    }

    /**
     * Métodos auxiliares
     */
    public function isAtrasado(): bool
    {
        return $this->status === 'ativo' && $this->data_devolucao_prevista < now();
    }

    public function diasAtraso(): int
    {
        if (!$this->isAtrasado()) {
            return 0;
        }

        // Garantir valor positivo de dias de atraso
        return $this->data_devolucao_prevista->diffInDays(now());
    }

    public function calcularMulta(): float
    {
        if (!$this->isAtrasado()) {
            return 0;
        }

        $diasAtraso = $this->diasAtraso();
        $multaPorDia = 2.00; // R$ 2,00 por dia de atraso

        return $diasAtraso * $multaPorDia;
    }

    public function renovar(): bool
    {
        $maxRenovacoes = 2;

        if ($this->renovacoes >= $maxRenovacoes) {
            return false;
        }

        if ($this->isAtrasado()) {
            return false;
        }

        $this->increment('renovacoes');
        $this->update([
            'data_devolucao_prevista' => $this->data_devolucao_prevista->addDays(14)
        ]);

        return true;
    }

    public function devolver(): void
    {
        $multa = $this->calcularMulta();

        $this->update([
            'status' => 'devolvido',
            'data_devolucao_real' => now(),
            'multa' => $multa,
        ]);
    }

    public function notificarProximoVencimento(): void
    {
        $this->user->notify(new EmprestimoProximoVencimento($this));
    }

    public function notificarAtraso(): void
    {
        $this->user->notify(new EmprestimoAtrasado($this));
    }
}
