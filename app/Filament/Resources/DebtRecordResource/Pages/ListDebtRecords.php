<?php

namespace App\Filament\Resources\DebtRecordResource\Pages;

use App\Filament\Resources\DebtRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDebtRecords extends ListRecords
{
    protected static string $resource = DebtRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Buat Catatan Hutang')
            ->icon('heroicon-o-plus'),
        ];
    }
}
