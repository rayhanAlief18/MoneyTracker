<?php

namespace App\Filament\Resources\MoneyPlacingResource\Pages;

use App\Filament\Resources\MoneyPlacingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMoneyPlacing extends EditRecord
{
    protected static string $resource = MoneyPlacingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
