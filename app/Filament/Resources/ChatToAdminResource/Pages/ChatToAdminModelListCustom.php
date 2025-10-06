<?php

namespace App\Filament\Resources\ChatToAdminResource\Pages;

use App\Filament\Resources\ChatToAdminResource;
use Filament\Resources\Pages\Page;

class ChatToAdminModelListCustom extends Page
{
    protected static string $resource = ChatToAdminResource::class;

    protected static string $view = 'filament.resources.chat-to-admin-resource.pages.chat-to-admin-model-list-custom';
    protected static ?string $title = 'Chat To Admin Models';

    public function getViewData(): array
    {
        $chat = \App\Models\chatToAdminModel::where('user_id', auth()->id())->get();
        return [
            'chat' => $chat,
            'urut' => 1,
        ];
    }

}
