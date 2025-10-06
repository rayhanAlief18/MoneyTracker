<?php

namespace App\Filament\Resources\DebtRequestResource\Widgets;

use App\Filament\Resources\DebtRequestPaymentResource;
use App\Models\DebtRequestModel as DebtRequest;
use App\Models\MoneyPlacingModel as MoneyPlacing;
use App\Models\transactionModel as Transaction;
use App\Models\debtRequestPaymentModel as DebtRequestPayment;
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
use Illuminate\Support\Str;
class TableDebtorRequest extends BaseWidget
{

    protected static ?string $heading = 'Hutang yang saya ajukan (hutang saya)';
    protected int|string|array $columnSpan = 'full'; // Biar penuh lebarnya

    // protected int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                debtRequest::where('debtor_user_id', auth()->id())
                    ->where('status', '!=', 'Lunas')
                    ->orderBy('debt_date', 'desc')
                

            )
            ->columns([
                Tables\Columns\TextColumn::make('creditor.name')
                    ->label('Pemberi Hutang (Kreditur)')
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
                                return [$month_number => Carbon::createFromFormat('m', $month_number)->locale('id')->translatedFormat('F')];
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

                    Action::make('Bayar')
                        ->visible(fn(Model $record): bool => $record->status === 'Diterima (Belum Bayar)')
                        ->label('Bayar')
                        ->color('success')
                        ->icon('heroicon-o-currency-dollar')
                        ->modalHeading('Konfirmasi Pembayaran Hutang')
                        ->modalSubheading('Tindakan bagus, Lunasi hutangmu sekarang!')
                        ->modalWidth('md')
                        ->form(function ($record): array {
                            return [
                                Forms\Components\Radio::make('admin_transfer')
                                    ->label('Apakah ada biaya admin transfer ?')
                                    ->reactive()
                                    ->options([
                                        'Ya' => 'Ya',
                                        'Tidak' => 'Tidak'
                                    ])
                                    ->descriptions([
                                        'Ya' => 'Jika pembayaran anda terkena biaya admin.',
                                        'Tidak' => 'Jika pembayaran anda tidak terkena biaya admin.'
                                    ])
                                    ->default('Tidak')
                                    ->afterStateUpdated(function (Forms\Get $get, $set, $state) use ($record) {
                                        $this->hitungSisaSaldo($get, $set, $get('money_placing_id'), $record);
                                    }),

                                Forms\Components\TextInput::make('biaya_admin')
                                    ->label('Nominal biaya admin')
                                    ->numeric()
                                    ->prefix('Rp.')
                                    ->reactive()
                                    ->visible(
                                        function (Forms\Get $get) {
                                            if ($get('admin_transfer') === 'Ya') {
                                                return $get('admin_transfer') === 'Ya';
                                            }
                                        }
                                    )->afterStateUpdated(function (Forms\Get $get, $set, $state) use ($record) {
                                        $this->hitungSisaSaldo($get, $set, $get('money_placing_id'), $record);
                                    }),

                                Forms\Components\Select::make('money_placing_id')
                                    ->label('Pilih Alokasi Keuangan yang akan diambilsss')
                                    ->options(function () use ($record) {
                                        $option = [];
                                        $moneyPlacing = MoneyPlacing::where('amount', '>=', $record->amount)->where('user_id', auth()->id())->get();
                                        // ->where('amount', '>=', $record->amount);
                                        foreach ($moneyPlacing as $mp) {
                                            $option[$mp->id] = $mp->name . "(Rp. " . number_format($mp->amount, 0, ',', '.') . ")";
                                        }
                                        return $option;




                                    })
                                    ->reactive()
                                    ->afterStateUpdated(function (Forms\Get $get, $set, $state) use ($record) {
                                        $this->hitungSisaSaldo($get, $set, $state, $record);
                                    })
                                    ->required(),


                                Forms\Components\TextInput::make('sisa_saldo')
                                    ->disabled()
                                    ->default('Rp. 0')
                                    ->dehydrated(false),

                                Forms\Components\FileUpload::make('bukti_bayar')
                                    ->label('Upload bukti bayar (opsional)')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('Bukti-bayar/' . auth()->id() . '-' . auth()->user()->name)
                                    ->getUploadedFileNameForStorageUsing(function ($file) use ($record) {
                                        $random = Str::uuid(16);
                                        $keterangan = $record->catatan ? Str::slug($record->keterangan) : 'no-catatan';
                                        return $random . '/' . $keterangan . '.' . $file->getClientOriginalExtension();
                                    })
                                    ->visibility('public')
                                ,

                            ];
                        })->action(function ($data, $record, ) {
                            $moneyPlacing = MoneyPlacing::find($data['money_placing_id']);
                            if ($moneyPlacing) {
                                // Pembayaran debitor (penghutang) dan status menjadi Pembayaran diajukan
                                Transaction::create([
                                    'user_id' => $record->debtor_user_id,
                                    'money_placing_id' => $moneyPlacing->id,
                                    'amount' => $record->amount,
                                    'categories_id' => 12, //bayar hutang
                                    'type' => 'hutang',
                                    'note' => 'Pembayaran hutang kepada ' . $record->debtor->name . ' dengan rincian pembayaran = ' . number_format($record->amount, 0, ',', '.') . ' (hutang)' . (!empty($data['biaya_admin']) ? '+' . number_format(intval($data['biaya_admin']), 0, ',', '.') . ' (biaya admin)' : '') . '. Dengan keterangan hutang ' . $record->keterangan,
                                    'date' => Carbon::now(),
                                ]);
                                //pengurangan money placing
                                MoneyPlacing::find($moneyPlacing->id)->decrement('amount', $record->amount + (isset($data['biaya_admin']) ? intval($data['biaya_admin']) : 0));

                                $record->update([
                                    'status' => 'Pembayaran Diajukan',
                                ]);
                                // Rekap data pengajuan pembayaran
                                DebtRequestPayment::create([
                                    'debt_request_id' => $record->id,
                                    'status' => 'Pembayaran Diajukan',
                                    'bukti_bayar' => $data['bukti_bayar'],
                                    'payment_date' => Carbon::now(),
                                ]);


                                Notification::make()
                                    ->title('Pembayaran hutang berhasil diajukan, data disimpan ke halaman pembayaran hutang (kontrak) dan catatan pengeluaran telah dibuat.')
                                    ->success()
                                    ->send();

                            }

                        })
                ]);


    }

    protected function hitungSisaSaldo($get, $set, $moneyPlacingId, $record)
    {
        if (!$moneyPlacingId) {
            $set('sisa_saldo', 'Rp. 0');
            return;
        }

        $moneyPlacing = MoneyPlacing::find($moneyPlacingId);
        if (!$moneyPlacing) {
            $set('sisa_saldo', 'Rp. 0');
            return;
        }

        $biayaAdmin = 0;
        if ($get('admin_transfer') === 'Ya') {
            $biayaAdmin = intval($get('biaya_admin')) ?? 0;
        }

        // dd($biayaAdmin) ;
        $sisa = $moneyPlacing->amount - ($record->amount + $biayaAdmin);
        $set('sisa_saldo', 'Rp. ' . number_format($sisa, 0, ',', '.'));
    }
}
