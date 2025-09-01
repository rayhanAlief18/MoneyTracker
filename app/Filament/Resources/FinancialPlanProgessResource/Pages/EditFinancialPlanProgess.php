<?php

namespace App\Filament\Resources\FinancialPlanProgessResource\Pages;

use App\Filament\Resources\FinancialPlanProgessResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinancialPlanProgess extends EditRecord
{
    protected static string $resource = FinancialPlanProgessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
