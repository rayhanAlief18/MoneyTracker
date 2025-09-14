<?php

namespace App\Filament\Resources\DebtRequestPaymentResource\Pages;

use App\Filament\Resources\DebtRequestPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDebtRequestPayment extends EditRecord
{
    protected static string $resource = DebtRequestPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
