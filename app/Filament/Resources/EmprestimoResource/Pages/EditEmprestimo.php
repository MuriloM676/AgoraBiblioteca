<?php

namespace App\Filament\Resources\EmprestimoResource\Pages;

use App\Filament\Resources\EmprestimoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmprestimo extends EditRecord
{
    protected static string $resource = EmprestimoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
