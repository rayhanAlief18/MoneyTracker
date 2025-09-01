<?php

namespace App\Filament\Resources\FinancialPlanResource\Pages;

use App\Filament\Resources\FinancialPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFinancialPlans extends ListRecords
{
    protected static string $resource = FinancialPlanResource::class;
    protected static ?string $createButtonLabel = 'Buat Rencana'; // Tambahkan baris ini
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Buat Rencana Keuangan')->icon('heroicon-o-plus')->color('primary'),
                
        ];
    }
}
