<?php

namespace App\Filament\Resources\ChatToAdminResource\Pages;

use App\Models\chatToAdminModel;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChatToAdmins extends ListRecords
{
    protected static string $resource = ChatToAdminResource::class;

    protected function getHeaderActions(): array
    {

        return [
            Actions\CreateAction::make(),
        ];
    }

}
