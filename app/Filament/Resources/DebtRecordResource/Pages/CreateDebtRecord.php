<?php

namespace App\Filament\Resources\DebtRecordResource\Pages;

use App\Filament\Resources\DebtRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDebtRecord extends CreateRecord
{
    protected static ?string $title = 'Tambah Catatan Hutang (mandiri)';
    protected static string $resource = DebtRecordResource::class;
}
