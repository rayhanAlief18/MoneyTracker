<?php

namespace App\Filament\Resources\DebtRecordResource\Pages;

use App\Filament\Resources\DebtRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDebtRecord extends EditRecord
{
    protected static string $resource = DebtRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
