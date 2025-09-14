<?php

namespace App\Filament\Resources\UsersResource\Pages;

use App\Filament\Resources\UsersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Pengguna')
                ->icon('heroicon-o-user-plus')
                ->color('primary')
        ];
    }
    public function getCreateButtonLabel(): string
    {
        return 'Tambah Pengguna';
    }
    
}
