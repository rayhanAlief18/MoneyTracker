<?php

namespace App\Filament\Resources\DebtRequestResource\Pages;

use App\Filament\Resources\DebtRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Resources\Components\Tab;

use App\Models\DebtRequestModel as DebtRequest;
class ListDebtRequests extends ListRecords
{
    protected static string $resource = DebtRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Buat Permintaan Hutang (Kontrak)')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            DebtRequestResource\Widgets\TableCreditorRequest::class,
            DebtRequestResource\Widgets\TableDebtorRequest::class,
        ];
    }

    // public function getTabs(): array
    // {
    //     return [
    //         'Orang Berhutang ke Saya' => Tab::make()
    //             ->modifyQueryUsing(fn ($query) =>
    //                 $query->where('creditor_user_id', auth()->id())
    //             ),
    
    //         'Saya Berhutang' => Tab::make()
    //             ->modifyQueryUsing(fn ($query) =>
    //                 $query->where('debtor_user_id', auth()->id())
    //             ),
    //     ];
    // }
}
