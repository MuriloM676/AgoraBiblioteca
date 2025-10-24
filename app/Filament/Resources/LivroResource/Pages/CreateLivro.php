<?php

namespace App\Filament\Resources\LivroResource\Pages;

use App\Filament\Resources\LivroResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLivro extends CreateRecord
{
    protected static string $resource = LivroResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
