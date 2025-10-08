<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MoneyPlacingResource\Pages;
use App\Filament\Resources\MoneyPlacingResource\RelationManagers;
use App\Models\moneyPlacingModel as MoneyPlacing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
class MoneyPlacingResource extends Resource
{
    protected static ?string $model = MoneyPlacing::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'Alokasi Uang';
    protected static ?string $pluralModelLabel = 'Alokasi Uang';
    protected static ?string $navigationGroup = 'Money Trakcer';
    protected static ?int $navigationSort = 1;
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('user', function ($query) {
                $query->where('id', auth()->id());
            }); // Filter by the authenticated user
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),

                Forms\Components\TextInput::make('name')
                    ->label('Nama Penempatan')
                    ->required(),

                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah Sekarang')
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    Stack::make([
                        Tables\Columns\TextColumn::make('name')->label('Nama Penempatan')->searchable(),
                        Tables\Columns\TextColumn::make('amount')->label('Saldo')->money('IDR', true)->sortable(),
                    ]),
                    Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->label('Hapus Data') // ubah label tombol di tabel
                        ->modalHeading('Mohon Dibaca Terlebih Dahulu !') // ubah judul modal
                        ->modalDescription('Jika anda menghapus alokasi ini, maka seluruh data cashflow dengan alokasi yang sama akan dihapus juga')
                        ->modalSubmitActionLabel('Ya, Hapus') // ubah teks tombol merah
                        ->modalCancelActionLabel('Batal'), // ubah teks tombol cancel
                ]),
                // Action::make('history')
                //     ->label('History')
                //     ->icon('heroicon-o-clock')
                //     ->modalHeading('Riwayat Transaksi')
                //     ->modalSubmitActionLabel('Tutup') // ganti label submit
                //     ->modalCancelAction(false) // hilangkan tombol cancel
                //     ->action(fn () => null) // agar tombol tidak melakukan apapun
                //     ->modalContent(function ($record) {
                //         return view('filament.money-placing.history-modal', [
                //             'transactions' => $record->transaction()->latest()->get(),
                //         ]);
                //      }), // buka di tab baru (opsional)
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->failureNotificationTitle('Gagal menghapus data')
                        ->successNotificationTitle('Data berhasil dihapus'),
                ]),
            ])
            ->emptyStateHeading('Anda belum membuat "Alokasi uang"')
            ->emptyStateDescription('Alokasi uang adalah tempat anda menyimpan uang, misal cash, bank, dll')
            ->emptyStateIcon('heroicon-o-building-library')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Tambahkan Alokasi uang')
                    ->icon('heroicon-o-plus')
                    ->color('primary'),
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
            'index' => Pages\ListMoneyPlacings::route('/'),
            'create' => Pages\CreateMoneyPlacing::route('/create'),
            'edit' => Pages\EditMoneyPlacing::route('/{record}/edit'),
        ];
    }
}
