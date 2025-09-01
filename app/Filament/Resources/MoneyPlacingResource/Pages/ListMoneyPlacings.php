<?php

namespace App\Filament\Resources\MoneyPlacingResource\Pages;

use App\Filament\Resources\MoneyPlacingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMoneyPlacings extends ListRecords
{
    protected static string $resource = MoneyPlacingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambahkan Alokasi uang')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
}
