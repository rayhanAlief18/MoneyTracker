<?php

namespace App\Filament\Resources\FinancialPlanResource\Pages;

use App\Filament\Resources\FinancialPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinancialPlan extends EditRecord
{
    protected static string $resource = FinancialPlanResource::class;
    protected static ?string $title = "Edit Rencana Keuangan";

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
