<?php

namespace App\Filament\Resources\DebtRequestResource\Widgets;

use App\Models\debtRequestModel as DebtRequest;
use App\Models\moneyPlacingModel as MoneyPlacing;
use App\Models\transactionModel as Transaction;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class TableCreditorRequest extends BaseWidget
{
    protected static ?string $heading = 'Hutang yang di ajukan ke saya (Hutang orang lain)';

    protected int|string|array $columnSpan = 'full'; // Biar penuh lebarnya
    //  protected int $sort = 1; // Urutan widget

    public function table(Table $table): Table
    {
        return $table
            ->query(
                debtRequest::where('creditor_user_id', auth()->id())->orderBy('debt_date', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('debtor.name')
                    ->label('Peminjam (Debitur)')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
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
                        'Pending' => 'warning',
                        'Diterima (Belum Bayar)' => 'success',
                        'Lunas' => 'success',
                        'Pembayaran Diajukan' => 'success',
                        'Ditolak' => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('bulan')
                    ->label('Bulan')
                    ->options(function () {
                        $bulan = DebtRequest::query()
                            ->selectRaw('DISTINCT MONTH(debt_date) as month_number')
                            ->orderBy('month_number')
                            ->pluck('month_number')
                            ->mapWithKeys(function ($month_number) {
                                return [
                                    $month_number => Carbon::createFromFormat('m', $month_number)->locale('id')->translatedFormat('F')
                                ];
                            })->toArray();
                        return $bulan;
                    })
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value']) && $data['value'] !== '') {
                            $query->whereMonth('debt_date', intval($data['value']));
                        }

                        return $query;
                    })
            ])->actions([
                    Action::make('Deskripsi')
                        ->label('Deskripsi')
                        ->icon('heroicon-o-information-circle')
                        ->modalHeading('Deskripsi Hutang')
                        ->modalContent(function (Model $record): View {
                            return view('filament.components.debt-request-modal-description', [
                                'record' => $record,
                            ]);
                        })
                        ->modalSubmitActionLabel('Tutup') // ganti label submit,
                        ->modalCancelAction(false), // hilangkan tombol cancel

                    Action::make('Terima')
                        ->label('Terima')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn($record) => $record->update(['status' => 'Diterima (Belum Bayar)']))

                        // lanjut sini 

                        ->form(function ($record) {
                            return [
                                Forms\Components\Select::make('money_placing_id')
                                    ->label('Pilih Alokasi Keuangan yang akan diambil')
                                    ->options(function () use ($record) {
                                        $option=[];
                                        $moneyPlacing = MoneyPlacing::where('amount', '>=', $record->amount)->where('user_id', auth()->id())->get();
                                            // ->where('amount', '>=', $record->amount);
                                        foreach($moneyPlacing as $mp){
                                            $option[$mp->id] = $mp->name. "(Rp. ".number_format($mp->amount,0,',','.').")";
                                        }
                                        return $option;
                                    })
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set) use ($record) {
                                        if (!$state) {
                                            $set('sisa_saldo', 'Rp. 0');
                                            return;
                                        }

                                        $moneyPlacing = MoneyPlacing::find($state);

                                        if (!$moneyPlacing) {
                                            $set('sisa_saldo', 'Rp. 0');
                                            return;
                                        }

                                        //money placing sudah dikurangi melalui model
                        
                                        $sisa = $moneyPlacing->amount - $record->amount;

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
                                    'amount' => $record->amount,
                                    'categories_id' => 14, //hutang keluar,
                                    'type' => 'hutang',
                                    'note' => 'Pemberian hutang kepada ' . $record->creditor->name . ' sebesar Rp ' . number_format($record->amount, 0, ',', '.'),
                                    'date' => Carbon::now(),
                                ]);
                                //money placing sudah dikurangi melalui model
                                MoneyPlacing::find($moneyPlacing->id)->decrement('amount', $record->amount);


                                Notification::make()
                                    ->title('Permintaan hutang disetujui dan catatan pengeluaran telah dibuat.')
                                    ->success()
                                    ->send();
                            }

                            // Perbarui status permintaan menjadi 'Diterima (Belum Bayar)'
                            $record->update(['status' => 'Diterima (Belum Bayar)']);

                            // buat catatan pemasukan untuk penghutang
                            if ($record->status === 'Diterima (Belum Bayar)' && $record->creditor_user_id === auth()->id()) {

                                Transaction::create([
                                    'user_id' => $record->debtor_user_id,
                                    'type' => 'hutang',
                                    'categories_id' => 11, //hutang masuk,
                                    'amount' => $record->amount,
                                    'date' => Carbon::now(),
                                    'note' => 'Penerimaan hutang dari ' . $record->debtor->name . ' sebesar Rp ' . number_format($record->amount, 0, ',', '.') . ' dengan catatan : ' . $record->keterangan,
                                    'money_placing_id' => $record->money_placing_id,
                                ]);
                                //money placing sudah ditambahkan
                                MoneyPlacing::find($record->money_placing_id)->increment('amount', $record->amount);
                
                                if (auth()->id() === $record->debtor_user_id) {
                                    Notification::make()
                                        ->title('Permintaan hutang dari ' . $record->debtor->name . ' disetujui dan catatan pemasukan telah dibuat.')
                                        ->success()
                                        ->send();
                                }
                            }

                        })
                        ->modalHeading('Konfirmasi Persetujuan')
                        ->modalSubheading('Apakah anda yakin menyetujui permintaan ini?')
                        ->modalButton('Ya, Setujui')
                        ->visible(fn($record) => $record->status === 'Pending')
                    ,

                    Action::make('Tolak')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Penolakan')
                        ->modalSubheading('Apakah anda yakin menolak permintaan ini?')
                        ->modalButton('Ya, Setujui')
                        ->action(fn($record) => $record->update(['status' => 'Ditolak']))
                        ->visible(fn($record) => $record->status === 'Pending')
                        ->after(function ($record) {
                            Notification::make()
                                ->title('Permintaan hutang berhasil ditolak.')
                                ->success()
                                ->send();
                        }),
                ])->emptyStateHeading('Belum ada data hutang')
                ->emptyStateDescription('Halaman ini menampung data hutang pengguna lain yang diajukan ke saya.')
                ->emptyStateIcon('heroicon-o-clipboard-document');
    }
}
