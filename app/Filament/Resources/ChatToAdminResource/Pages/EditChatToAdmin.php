<?php

namespace App\Filament\Resources\ChatToAdminResource\Pages;

use App\Filament\Resources\ChatToAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChatToAdmin extends EditRecord
{
    protected static string $resource = ChatToAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
