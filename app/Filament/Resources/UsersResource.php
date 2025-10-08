<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsersResource\Pages;
use App\Filament\Resources\UsersResource\RelationManagers;
use App\Models\User as Users;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UsersResource extends Resource
{
    protected static ?string $model = Users::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Pengguna';
    protected static ?string $pluralModelLabel = 'Data Pengguna';
    protected static ?string $navigationGroup = 'Master Data';


    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('email')->email()->required(),
            Forms\Components\Select::make('role')
                ->required()
                ->options([
                    'admin' => 'admin',
                    'user' => 'user',
                ])
                ->label('Role')
                ->extraAttributes([
                    'class' => 'text-black dark:text-white bg-white dark:bg-gray-800',
                ]),
            Forms\Components\TextInput::make('job'),
            Forms\Components\TextInput::make('password')
                ->password()
                ->dehydrateStateUsing(fn ($state) => \Hash::make($state))
                ->required(fn ($context) => $context === 'create')
                ->label('Password')->required(),
            Forms\Components\TextInput::make('password_confirmation')
                ->password()
                ->same('password')
                ->label('Konfirmasi Password')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('role'),
                Tables\Columns\TextColumn::make('job'),
            Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada User')
            ->emptyStateDescription('Silakan tambahkan user untuk memulai.')
            ->emptyStateIcon('heroicon-o-user-plus')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                ->label('Tambah Pengguna')
                ->icon('heroicon-o-plus'),
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUsers::route('/create'),
            'edit' => Pages\EditUsers::route('/{record}/edit'),
        ];
    }    

    // public static function canViewAny(): bool{
    //     return auth()->user() === 'admin';
    // }
}
