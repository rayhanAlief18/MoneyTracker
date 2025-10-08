<?php

namespace App\Filament\Resources\ChatToAdminResource\Pages;

use App\Filament\Resources\ChatToAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChatToAdmin extends EditRecord
{
    protected static string $resource = ChatToAdminResource::class;
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Jika admin yang menyimpan, ubah status jadi "Sudah Dibaca"
        if (auth()->user()->role === 'admin') {
            $data['status'] = 'Sudah Dibaca';
        }

        return $data;
    }
}
