<?php

namespace App\Filament\Resources\DebtRequestPaymentResource\Widgets;

use Filament\Tables\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\debtRequestPaymentModel as debtRequestPayment;
use App\Models\moneyPlacingModel as MoneyPlacing;
use App\Models\transactionModel as Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\View\View;
use Filament\Forms;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class PembayaranCreditor extends BaseWidget
{
    protected int|string|array $columnSpan = 'full'; // Biar penuh lebarnya
    protected static ?string $heading = 'Rekap Pembayaran Hutang';


    public function table(Table $table): Table
    {
        return $table
        ->query(
            DebtRequestPayment::with(['debt_request.debtor', 'debt_request.creditor'])->whereHas('debt_request', function (Builder $q) {
                $q->where('debtor_user_id', auth()->id());
            })

        )
        ->columns([
            Tables\Columns\TextColumn::make('debt_request.creditor.name')
                ->label('Nama Pemberi Hutang')
                ->searchable(),

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

            Tables\Columns\TextColumn::make('debt_request.amount')->money('IDR', true),
            Tables\Columns\TextColumn::make('debt_request.due_date')->label('Tenggat Bayar'),
            Tables\Columns\TextColumn::make('payment_date')->label('Tanggal Bayar'),
        ])
        ->actions([
            Action::make('bukti_bayar')
                ->label('Bukti pembayaran')
                ->icon('heroicon-o-information-circle')
                ->modalHeading('Bukti Pembayaran')
                ->modalSubheading('Bukti pembayaran dan deskripsi hutang')
                ->modalContent(function (Model $record): View {
                    return view('filament.pages.debt-request-payment.modalPayment', [
                        'record' => $record,
                    ]);
                })
                ->modalSubmitActionLabel('Tutup') // ganti label submit,
                ->modalCancelAction(false), // hilangkan tombol cancel

            Action::make('terima_pembayaran')
                ->label('Terima Pembayaran')
                ->color('success')
                ->icon('heroicon-o-currency-dollar')
                ->modalHeading('Konfirmasi Pembayaran Hutang')
                ->modalSubheading('Tindakan bagus, Lunasi hutangmu sekarang!')
                ->modalWidth('md')
                ->visible(fn(Model $record):bool => $record->status === "Pembayaran Diajukan" && $record->debt_request->creditor_user_id === auth()->id())
                ->form(function ($record): array {
                    return [

                        Forms\Components\Select::make('money_placing_id')
                            ->label('Pilih Alokasi Keuangan (saldo akan disimpan disini)')
                            ->options(function () use ($record) {
                                return MoneyPlacing::where('user_id', auth()->id())
                                    ->pluck('name', 'id');
                            })
                            ->reactive()
                            ->afterStateUpdated(function ($set, $state) use ($record) {
                                // penambahan money placing
                                $this->hitungSisaSaldo($set, $state, $record);
                            })
                            ->required(),


                        Forms\Components\TextInput::make('sisa_saldo')
                            ->label('Total Saldo (setelah penjumlahan)')
                            ->disabled()
                            ->default('Rp. 0')
                            ->dehydrated(false),

                    ];
                })->action(function($data, $record){
                    $moneyPlacing = MoneyPlacing::find($data['money_placing_id']);
                    if ($moneyPlacing) {
                        // Pemasukan untuk penerima saldo hutang
                        Transaction::create([
                            'user_id' => $record->debt_request->creditor_user_id, //
                            'money_placing_id' => $moneyPlacing->id,
                            'amount' => $record->debt_request->amount,
                            'categories_id' => 13, //hutang dibayar oleh penghutang
                            'type' => 'hutang',
                            'note' => 'Hutang telah dibayar oleh ' . $record->debt_request->debtor->name . ' sebesar Rp '. number_format($record->amount , 0, ',', '.').'. Dengan keterangan hutang '.$record->keterangan,
                            'date' => Carbon::now(),
                        ]);

                        //penambahan penyimpanan saldo
                        $record->update([
                            'status' => 'Lunas',
                            'money_placing_save'=>$moneyPlacing->id,
                            'receipt_date'=>Carbon::now(),
                        ]);
                        // Rekap data pengajuan pembayaran

                        Notification::make()
                            ->title('Pembayaran hutang berhasil diterima dan catatan pemasukan telah dibuat.')
                            ->success()
                            ->send();

                    }
                })
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('Bulan')
            ->label('bulan')
            ->options(function(){
                $bulan = DebtRequestPayment::query()
                    ->selectRaw('DISTINCT MONTH(payment_date) as month_number')
                    ->orderBy('month_number')
                    ->pluck('month_number')
                    ->mapWithKeys(function($month_number):array{
                        return [
                            $month_number => Carbon::createFromFormat('m',$month_number)->locale('ID')->translatedFormat('F')
                        ];
                    })->toArray();
                return $bulan;
            })->query(function(Builder $query, array $data){
                if(isset($data['value']) && $data['value'] !==''){
                    $query->whereMonth('payment_date', intval($data['value']));
                }
                return $query;
            }),

        ])
    ;
    }
    
}
