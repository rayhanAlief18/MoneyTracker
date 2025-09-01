<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DebtRequestResource\Pages;
use App\Filament\Resources\DebtRequestResource\RelationManagers;
use App\Models\debtRequestModel as DebtRequest;
use App\Models\MoneyPlacingModel as MoneyPlacing;
use App\Models\transactionModel as Transaction;
use Filament\Forms;
use Filament\Forms\Form;
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('debtor_user_id', auth()->id())->orWhere('creditor_user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('debtor.name')
                    ->label('Debitur (Peminjam)')
                    ->searchable(),

                Tables\Columns\TextColumn::make('creditor.name')
                    ->label('Kreditor (Pemberi Pinjaman)')
                    ->searchable(),

                Tables\Columns\TextColumn::make('debtRecord.amount')
                    ->label('Jumlah')
                    ->prefix('Rp')
                    ->sortable(),

                Tables\Columns\TextColumn::make('debt_date')
                    ->label('Tanggal Hutang')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Tanggal Jatuh Tempo')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
                // 🔹 Tambahkan kolom deskripsi di sini
            ])->filters([
                    Tables\Filters\SelectFilter::make('Bulan')
                        ->label('Bulan')
                        ->options(function () {
                            $bulan = DebtRequest::query()
                                ->selectRaw('DISTINCT MONTH(debt_date) as month_number')
                                ->orderBy('month_number')
                                ->pluck('month_number')
                                ->mapWithKeys(function ($month_number) {
                                    return [Carbon::createFromFormat('m', $month_number)->locale('id')->translatedFormat('F')];

                                })->toArray();
                            return $bulan;
                        })
                ])->actions([
                    // Tables\Actions\ViewAction::make(),
                ])->bulkActions([
                    // Tables\Actions\BulkActionGroup::make([
                    //     Tables\Actions\DeleteBulkAction::make(),
                    // ]),
                ])
            ->actions([
                Action::make('Deskripsi')
                    ->label('Deskripsi')
                    ->icon('heroicon-o-information-circle')
                    ->modalHeading('Deskripsi Hutang')
                    ->modalContent(function (Model $record): View {
                        return view('filament.components.debt-request-modal-description', [
                            'record' => $record->debtRecord,
                        ]);
                    })
                    ->modalSubmitActionLabel('Tutup') // ganti label submit,
                    ->modalCancelAction(false), // hilangkan tombol cancel

                Action::make('Terima')
                    ->label('Terima')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Select::make('money_placing_id')
                            ->label('Pilih Alokasi Keuangan')
                            ->options(MoneyPlacing::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        // Perbarui status permintaan menjadi 'approved'
                        $moneyPlacing = MoneyPlacing::find($data['money_placing_id']);
                        if ($moneyPlacing) {
                            // Buat catatan pengeluaran baru
                            Transaction::create([
                                'user_id' => auth()->id(),
                                'money_placing_id' => $moneyPlacing->id,
                                'amount' => $record->debtRecord->amount,
                                'type' => 'pengeluaran',
                                'note' => 'Pemberian hutang kepada ' . $record->creditor->name,
                                'date' => Carbon::now(),
                            ]);

                            //     // Kurangi Money Placing yang dipilih
                            $moneyPlacing->decrement('amount', $record->amount);

                            Notification::make()
                                ->title('Permintaan hutang disetujui dan catatan pengeluaran telah dibuat.')
                                ->success()
                                ->send();
                        }
                        $record->update(['status' => 'approved']);

                        // if ($record->status === 'approved' && $record->creditor_user_id === auth()->id()){
                            // loakukan penambahan pada money placing user pemberi pinjaman
                        // })

                    })
                    ->visible(fn($record)=>$record->status==='pending' && $record->creditor_user_id === auth()->id())
                    ->modalHeading('Konfirmasi Persetujuan')
                    ->modalSubheading('Apakah anda yakin menyetujui permintaan ini?')
                    ->modalButton('Ya, Setujui'),

                Action::make('Tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Penolakan')
                    ->modalSubheading('Apakah anda yakin menolak permintaan ini?')
                    ->modalButton('Ya, Setujui')
                    ->visible(fn($record)=>$record->status==='pending' && $record->creditor_user_id === auth()->id())
                    ->action(fn($record) => $record->update(['status' => 'rejected'])),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            // 'create' => Pages\CreateDebtRequest::route('/create'),
            // 'edit' => Pages\EditDebtRequest::route('/{record}/edit'),
        ];
    }
}
