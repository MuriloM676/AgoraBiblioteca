<?php

namespace App\Filament\Resources\EmprestimoResource\Pages;

use App\Filament\Resources\EmprestimoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmprestimo extends CreateRecord
{
    protected static string $resource = EmprestimoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
