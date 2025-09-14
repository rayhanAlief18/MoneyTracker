<?php

namespace App\Filament\Resources\DebtRequestPaymentResource\Pages;

use App\Filament\Resources\DebtRequestPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDebtRequestPayments extends ListRecords
{
    protected static string $resource = DebtRequestPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return[
            DebtRequestPaymentResource\Widgets\PembayaranReqTable::class,
            DebtRequestPaymentResource\Widgets\PembayaranCreditor::class
        ];
    }
}
