<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DebtRequestResource\Pages;
use App\Filament\Resources\DebtRequestResource\RelationManagers;
use App\Models\debtRecord;
use App\Models\debtRequestModel as DebtRequest;
use App\Models\MoneyPlacingModel as MoneyPlacing;
use App\Models\transactionModel as Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Hidden;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\View\View;
use Carbon\Carbon;

class DebtRequestResource extends Resource
{
    protected static ?string $model = DebtRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Hutang / Piutang (kontrak)';
    protected static ?string $pluralModelLabel = 'Hutang / Piutang (kontrak)';
    protected static ?string $navigationGroup = 'Hutang Piutang';
    protected static ?int $navigationSort = 3;


    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()->where('creditor_user_id', auth()->id());
    // }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('debtor_user_id')
                    ->default(auth()->id()),

                Forms\Components\Select::make('creditor_user_id')
                    ->label('Nama Pemberi Hutang')
                    ->relationship('creditor', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),


                Hidden::make('status')
                    ->default('Pending'),

                Forms\Components\DatePicker::make('debt_date')
                    ->label('Tanggal Hutang')
                    ->default(Carbon::now())
                    ->required(),

                Forms\Components\DatePicker::make('due_date')
                    ->label('Tanggal Jatuh Tempo')
                    ->required(),

                Forms\Components\Select::make('money_placing_id')
                    ->label('Alokasi Uang (Uang hutang akan masuk alokasi ini)')
                    ->options(fn($record) => MoneyPlacing::where('user_id', auth()->id())->pluck(column: 'name',key: 'id')),

                Forms\Components\TextInput::make('jenis_hutang')
                    ->label('Jenis Hutang')
                    ->default('Kontrak')
                    ->readOnly()
                    ->required(),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan / Catatan')
                    ->rows(3)
                    ->required(),


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
            'index' => Pages\ListDebtRequests::route('/'),
            'create' => Pages\CreateDebtRequest::route('/create'),
            // 'edit' => Pages\EditDebtRequest::route('/{record}/edit'),
        ];
    }
}
