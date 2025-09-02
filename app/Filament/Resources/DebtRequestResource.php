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
        return parent::getEloquentQuery()->where('creditor_user_id', auth()->id());
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

                Tables\Columns\TextColumn::make('debtRecord.amount')
                    ->label('Jumlah')
                    ->money('IDR', true)
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
                    Tables\Actions\ViewAction::make(),
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
                    ->action(fn($record) => $record->update(['status' => 'approved']))

                    ->form(function ($record) {
                        return [
                            Forms\Components\Select::make('money_placing_id')
                                ->label('Pilih Alokasi Keuangan yang akan diambil')
                                ->options(function () use ($record) {
                                    return MoneyPlacing::where('user_id', auth()->id())
                                        ->where('amount', '>=', $record->debtRecord->amount)
                                        ->pluck('name', 'id');
                                })
                                ->reactive()
                                ->afterStateUpdated(function ($state, $set) use ($record) {
                                    if (! $state) {
                                        $set('sisa_saldo', 'Rp. 0');
                                        return;
                                    }
                    
                                    $moneyPlacing = MoneyPlacing::find($state);
                    
                                    if (! $moneyPlacing) {
                                        $set('sisa_saldo', 'Rp. 0');
                                        return;
                                    }
                                    
                                    //moneyplacing dikurang dari sini
                                    $sisa = $moneyPlacing->amount - $record->debtRecord->amount;
                    
                                    $set('sisa_saldo', 'Rp ' . number_format(max($sisa, 0), 0, ',', '.'));
                                })
                                ->required(),
                            
                                // Tampilkan Sisa jika menghutangi, Saldo Berdasarkan Alokasi yang Dipilih
                            Forms\Components\TextInput::make('sisa_saldo')
                                ->label('Sisa Saldo Setelah Transaksi')
                                ->disabled()
                                ->dehydrated(false),
                        ];
                    })
                    
                    ->action(function ($record, array $data) {
                        $moneyPlacing = MoneyPlacing::find($data['money_placing_id']);
                        if ($moneyPlacing) {
                            // Buat catatan pengeluaran untuk yang menghutangi
                            Transaction::create([
                                'user_id' => auth()->id(),
                                'money_placing_id' => $moneyPlacing->id,
                                'amount' => $record->debtRecord->amount,
                                'categories_id' => 9, //hutang keluar,
                                'type' => 'pengeluaran',
                                'note' => 'Pemberian hutang kepada ' . $record->creditor->name.' sebesar Rp ' . number_format($record->debtRecord->amount, 0, ',', '.'),
                                'date' => Carbon::now(),
                            ]);
                            

                            Notification::make()
                                ->title('Permintaan hutang disetujui dan catatan pengeluaran telah dibuat.')
                                ->success()
                                ->send();
                        }
                        
                        // Perbarui status permintaan menjadi 'approved'
                        $record->update(['status' => 'approved']);
                        
                        // buat catatan pemasukan untuk penghutang
                        if ($record->status === 'approved' && $record->creditor_user_id === auth()->id()){

                            
                            Transaction::create([
                                'user_id' => $record->debtor_user_id,
                                'type' => 'hutang',
                                'categories_id' => 8, //hutang masuk,
                                'amount' => $record->debtRecord->amount,
                                'date' => Carbon::now(),
                                'note' => 'Penerimaan hutang dari ' . $record->debtor->name. ' sebesar Rp '. number_format($record->debtRecord->amount, 0, ',', '.'),
                                'money_placing_id' => $record->debtRecord->money_placing_id,
                            ]);

                            $moneyPlacing = MoneyPlacing::find($record->debtRecord->money_placing_id);
                            if ($moneyPlacing) {
                                $moneyPlacing->increment('amount', $record->debtRecord->amount);
                            }
                            
                            if(auth()->id() === $record->debtor_user_id){
                                Notification::make()
                                ->title('Permintaan hutang dari '. $record->debtor->name.' disetujui dan catatan pemasukan telah dibuat.')
                                ->success()
                                ->send();    
                            }
                        }

                    })
                    ->modalHeading('Konfirmasi Persetujuan')
                    ->modalSubheading('Apakah anda yakin menyetujui permintaan ini?')
                    ->modalButton('Ya, Setujui')
                    ->visible(fn($record)=>$record->status==='pending')
                    ,

                Action::make('Tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Penolakan')
                    ->modalSubheading('Apakah anda yakin menolak permintaan ini?')
                    ->modalButton('Ya, Setujui')
                    ->action(fn($record) => $record->update(['status' => 'rejected']))
                    ->visible(fn($record)=>$record->status==='pending')
                    ->after(function ($record) {
                        Notification::make()
                            ->title('Permintaan hutang berhasil ditolak.')
                            ->success()
                            ->send();
                    }),
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
