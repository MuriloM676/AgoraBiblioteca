<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Reserva extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'livro_id',
        'data_reserva',
        'data_expiracao',
        'status',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'data_reserva' => 'datetime',
            'data_expiracao' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'livro_id', 'status', 'data_reserva', 'data_expiracao'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Boot do modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reserva) {
            if (!$reserva->data_reserva) {
                $reserva->data_reserva = now();
            }
            if (!$reserva->data_expiracao) {
                $reserva->data_expiracao = now()->addDays(3);
            }
            if (!$reserva->status) {
                $reserva->status = 'pendente';
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
    public function scopePendente($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeAtiva($query)
    {
        return $query->where('status', 'ativa');
    }

    public function scopeExpiradas($query)
    {
        return $query->where('status', 'pendente')
                     ->where('data_expiracao', '<', now());
    }

    /**
     * Métodos auxiliares
     */
    public function isExpirada(): bool
    {
        return $this->status === 'pendente' && $this->data_expiracao < now();
    }

    public function cancelar(): void
    {
        $this->update(['status' => 'cancelada']);
    }

    public function confirmar(): void
    {
        $this->update(['status' => 'confirmada']);
    }

    public function converter_em_emprestimo(): Emprestimo
    {
        $emprestimo = Emprestimo::create([
            'user_id' => $this->user_id,
            'livro_id' => $this->livro_id,
            'data_emprestimo' => now(),
            'data_devolucao_prevista' => now()->addDays(14),
            'status' => 'ativo',
        ]);

        $this->update(['status' => 'convertida']);

        return $emprestimo;
    }
}
