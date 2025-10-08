<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatToAdminResource\Pages;
use App\Filament\Resources\ChatToAdminResource\RelationManagers;
use App\Models\chatToAdminModel as ChatToAdmin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChatToAdminResource extends Resource
{
    protected static ?string $model = ChatToAdmin::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left';
    protected static ?string $navigationLabel = 'Chat Admin';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('pesan')
                    ->label('Pesan / Kritik / Saran')
                    ->visible(fn ($record) => $record === null)
                    ->required(),
                
                    Forms\Components\Textarea::make('balasan')
                    ->label('Balas Pesan')
                    ->visible(fn () => auth()->user()->role === 'admin')
                    ->required(),
                    Hidden::make('user_id')->default(auth()->id())->dehydrated(fn ($record) => $record === null),
            ]);
    }

    public static function getStats()
    {

    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ChatToAdminModelListCustom::route('/'),
            'create' => Pages\CreateChatToAdmin::route('/create'),
            'edit' => Pages\EditChatToAdmin::route('/{record}/edit'),
        ];
    }
}
