<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Livro extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'titulo',
        'autor',
        'categoria_id',
        'isbn',
        'sinopse',
        'ano_publicacao',
        'editora',
        'numero_paginas',
        'idioma',
        'capa',
        'arquivo_pdf',
        'quantidade_total',
        'quantidade_disponivel',
        'status',
        'qr_code',
    ];

    protected function casts(): array
    {
        return [
            'ano_publicacao' => 'integer',
            'numero_paginas' => 'integer',
            'quantidade_total' => 'integer',
            'quantidade_disponivel' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['titulo', 'autor', 'categoria_id', 'status', 'quantidade_total', 'quantidade_disponivel'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Boot do modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($livro) {
            $livro->generateQrCode();
        });

        static::deleting(function ($livro) {
            // Remove arquivos ao deletar o livro
            if ($livro->capa) {
                Storage::disk('public')->delete($livro->capa);
            }
            if ($livro->arquivo_pdf) {
                Storage::disk('public')->delete($livro->arquivo_pdf);
            }
            if ($livro->qr_code) {
                Storage::disk('public')->delete($livro->qr_code);
            }
        });
    }

    /**
     * Relacionamentos
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function emprestimos()
    {
        return $this->hasMany(Emprestimo::class);
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    /**
     * Accessors
     */
    public function getCapaUrlAttribute()
    {
        return $this->capa ? Storage::url($this->capa) : null;
    }

    public function getArquivoPdfUrlAttribute()
    {
        return $this->arquivo_pdf ? Storage::url($this->arquivo_pdf) : null;
    }

    public function getQrCodeUrlAttribute()
    {
        return $this->qr_code ? Storage::url($this->qr_code) : null;
    }

    /**
     * Scopes
     */
    public function scopeDisponivel($query)
    {
        return $query->where('status', 'disponivel')
                     ->where('quantidade_disponivel', '>', 0);
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    public function scopePorAutor($query, $autor)
    {
        return $query->where('autor', 'like', "%{$autor}%");
    }

    public function scopeBuscar($query, $termo)
    {
        return $query->where(function ($q) use ($termo) {
            $q->where('titulo', 'like', "%{$termo}%")
              ->orWhere('autor', 'like', "%{$termo}%")
              ->orWhere('isbn', 'like', "%{$termo}%");
        });
    }

    /**
     * Métodos auxiliares
     */
    public function isDisponivel(): bool
    {
        return $this->status === 'disponivel' && $this->quantidade_disponivel > 0;
    }

    public function decrementarQuantidade(): void
    {
        if ($this->quantidade_disponivel > 0) {
            $this->decrement('quantidade_disponivel');
            
            if ($this->quantidade_disponivel === 0) {
                $this->update(['status' => 'indisponivel']);
            }
        }
    }

    public function incrementarQuantidade(): void
    {
        $this->increment('quantidade_disponivel');
        
        if ($this->quantidade_disponivel > 0 && $this->status === 'indisponivel') {
            $this->update(['status' => 'disponivel']);
        }
    }

    /**
     * Gera QR Code para o livro
     */
    public function generateQrCode(): void
    {
        $url = route('livros.show', $this->id);
        // Usamos SVG para evitar dependência do Imagick/GD na geração de PNG
        $qrCode = QrCode::format('svg')
                        ->size(300)
                        ->generate($url);

        $path = "qrcodes/livro_{$this->id}.svg";
        Storage::disk('public')->put($path, $qrCode);

        $this->update(['qr_code' => $path]);
    }
}
