<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DebtRequestPaymentResource\Pages;
use App\Models\debtRequestPaymentModel as DebtRequestPayment;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class DebtRequestPaymentResource extends Resource
{
    protected static ?string $model = DebtRequestPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Pembayaran Hutang (kontrak)';
    protected static ?string $navigationGroup = 'Hutang Piutang';
    
    protected static ?int $navigationSort = 3;
    protected static ?string $pluralModelLabel = 'Rekap Pembayaran Hutang(Kontrak)';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateHeading('Belum ada data yang digunakan')
            ->emptyStateDescription('Halaman ini menampung data pembayaran dan penerimaan hutang kontrak. Saldo penerimaan bisa dipindahkan ke Alokasi uang yang anda pilih.')
            ->emptyStateIcon('heroicon-o-clipboard-document')
            ;
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
            'index' => Pages\ListDebtRequestPayments::route('/'),
            // 'create' => Pages\CreateDebtRequestPayment::route('/create'),
            // 'edit' => Pages\EditDebtRequestPayment::route('/{record}/edit'),
        ];
    }
}
