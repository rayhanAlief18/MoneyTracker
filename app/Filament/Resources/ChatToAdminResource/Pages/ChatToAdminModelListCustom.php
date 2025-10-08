<?php

namespace App\Filament\Resources\ChatToAdminResource\Pages;

use App\Filament\Resources\ChatToAdminResource;
use Filament\Resources\Pages\Page;

class ChatToAdminModelListCustom extends Page
{
    protected static string $resource = ChatToAdminResource::class;

    protected static string $view = 'filament.resources.chat-to-admin-resource.pages.chat-to-admin-model-list-custom';
    
    protected static ?string $title = 'Chat To Admin';

    public function getViewData(): array
    {
        if(auth()->user()->role === 'user'){
            $chat = \App\Models\chatToAdminModel::where('user_id', auth()->id())->get();
        }else if(auth()->user()->role === 'admin'){
            $chat = \App\Models\chatToAdminModel::orderByRaw("CASE WHEN status = 'Belum Dibaca' THEN 0 ELSE 1 END")
        ->orderBy('created_at', 'desc')
        ->get() // ambil dulu datanya
        ->groupBy('user_id') // kelompokkan per user
        ->map(function ($userChats) {
            return $userChats->values()->map(function ($chat, $index) {
                $chat->urutan = $index + 1; // kasih nomor urut
                return $chat;
            });
        })
        ->flatten(); // gabungkan jadi 1 collection lagi
        
        }
        return [
            'chat' => $chat,
            'urut' => 1,
        ];
    }

}
