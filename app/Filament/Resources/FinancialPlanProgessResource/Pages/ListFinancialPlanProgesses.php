<?php

namespace App\Filament\Resources\FinancialPlanProgessResource\Pages;

use App\Filament\Resources\FinancialPlanProgessResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFinancialPlanProgesses extends ListRecords
{
    protected static string $resource = FinancialPlanProgessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Masukan Progress Anda')
            ->icon('heroicon-o-plus'),
        ];
    }
}
