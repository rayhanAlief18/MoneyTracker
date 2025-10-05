<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DebtRecordResource\Pages;
use App\Models\DebtRecord;
use App\Models\moneyPlacingModel as MoneyPlacing;
use Filament\Tables\Actions\Action;
use App\Models\transactionModel as Transaction;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
class DebtRecordResource extends Resource
{
    protected static ?string $model = DebtRecord::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Hutang Piutang';

    protected static ?string $navigationLabel = 'Hutang / Piutang (mandiri)';
    protected static ?string $pluralModelLabel = 'Catatan Hutang (mandiri)';
    protected static ?int $navigationSort = 3;

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
                Hidden::make('user_id')
                    ->default(auth()->id()),

                Forms\Components\TextInput::make('nama_pemberi_hutang')
                    ->label('Nama Pemberi Hutang')
                    ->required(),

                Forms\Components\TextInput::make('jenis_hutang')
                    ->label('Jenis Hutang (Tidak bisa duibah)')
                    ->default('Individu')
                    ->readOnly()
                    ->required(),

                // Forms\Components\Select::make('nama_pemberi_hutang')
                //     ->label('Pemberi Hutang')
                //     ->relationship('user', 'name')
                //     ->searchable()
                //     ->required()
                //     ->afterStateUpdated(function (Set $set, Get $get) {
                //         $user = User::find($get('nama_pemberi_hutang'));
                //         if ($user) {
                //             $set('nama_pemberi_hutang', $user->name);
                //         }
                //     })
                //     ->visible(fn($get) => $get('jenis_hutang') === 'Kontrak'),

                // Forms\Components\Hidden::make('nama_pemberi_hutang')->dehydrated()->visible(fn(Get $get)=> $get('jenis_hutang')==="Kontrak"),




                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                Forms\Components\Select::make('money_placing_id')
                    ->label('Alokasi Uang (uang hutang akan masuk ke alokasi ini)')
                    ->options(function () {
                        $userId = auth()->id();
                        $moneyPlacings = MoneyPlacing::where('user_id', $userId)->get();
                        $options = [];
                        foreach ($moneyPlacings as $placing) {
                            $options[$placing->id] = $placing->name . ' (Rp ' . number_format($placing->amount, 0, ',', '.') . ')';
                        }
                        return $options;
                    })
                    ->required(),

                Forms\Components\DatePicker::make('tanggal_hutang')
                    ->label('Tanggal Hutang')
                    ->default(Carbon::now())
                    ->required(),

                Forms\Components\DatePicker::make('tanggal_rencana_bayar')
                    ->label('Rencana Bayar'),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_pemberi_hutang')->label('Pemberi Hutang')->searchable(),
                Tables\Columns\TextColumn::make('amount')->label('Jumlah')->money('IDR', true),
                Tables\Columns\TextColumn::make('tanggal_hutang')->label('Tgl Hutang')->date(),
                Tables\Columns\TextColumn::make('tanggal_rencana_bayar')->label('Rencana Bayar')->date(),
                Tables\Columns\TextColumn::make('status')->label('Status Hutang')->badge()
                    ->color(fn($state) => match ($state) {
                        'Belum bayar' => 'danger',
                        'Lunas' => 'success',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->getStateUsing(function ($record) {
                        $due_date = Carbon::parse($record->tanggal_rencana_bayar);
                        $tanggal_sekarang = Carbon::now();
                        $diff_day = $due_date->diffInDays($tanggal_sekarang);

                        if ($diff_day > 0) {
                            return "Kurang {$diff_day} hari lagi.";
                        } elseif ($diff_day < 0) {
                            return "Terlambat " . abs($diff_day) . " hari";
                        } else {
                            return "Hari ini";
                        }
                    })
                // ->color(fn($record) => match (true) {
                //     // Kondisi pertama: jika tanggal jatuh tempo sudah lewat
                //     Carbon::parse($record->due_date)->isPast() => 'danger',
                //     // Kondisi kedua: jika jatuh tempo dalam 3 hari ke depan
                //     Carbon::parse($record->due_date)->diffInDays(Carbon::now(), false) <= 3 => 'warning',
                //     // Kondisi default untuk semua kondisi lain (jatuh tempo masih jauh)
                //     default => 'success',
                // }),
            ])
            ->defaultSort('created_at', 'desc')->filters([])
            ->filters([
                //
            ])
            ->actions([

                Tables\Actions\EditAction::make(),
                Action::make('Bayar')
                    ->label('Bayar')
                    ->color('success')
                    ->icon('heroicon-o-currency-dollar')
                    ->modalHeading('Konfirmasi Pembayaran Hutang')
                    ->modalSubheading('Pilih Alokasi uang yang akan digunakan untuk membayar hutang ini.')
                    ->modalButton('Bayar')
                    ->modalWidth('md')
                    ->visible(fn(DebtRecord $record) => $record->status !== 'Lunas' || $record->status !== 'Pembayaran Diajukan')
                    ->form(function ($record): array {
                        return [
                            Forms\Components\Select::make('money_placing_id')
                                ->label('Pilih Alokasi Uang')
                                ->options(function ($record) {
                                    $option = [];
                                    $moneyPlacing = MoneyPlacing::where('amount', '>=', $record->amount)->where('user_id',auth()->id())->get();
                                    foreach ($moneyPlacing as $mp) {
                                        $option[$mp->id] = $mp->name;
                                    }
                                    return $option;

                                })
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function(Forms\Set $set, $state) use ($record){
                                    if(!$state){
                                        $set('sisa_saldo','Rp. 0');
                                    }

                                    $moneyPlacing = MoneyPlacing::find($state);
                                    if(!$moneyPlacing){
                                        $set('sisa_saldo','Rp. 0');
                                    }

                                    $sisa = $moneyPlacing->amount - $record->amount;
                                    $set('sisa_saldo','Rp. '.number_format($sisa,0,',','.'));
                                }),

                            Forms\Components\TextInput::make('sisa_saldo')
                            ->label('Sisa saldo')
                            ->reactive()
                            ->disabled(),
                        ];
                    })
                    ->action(
                        function (DebtRecord $record) {
                            // Kurangi amount money placing tujuan
                            //cek saldo money placing
                            $moneyPlacing = MoneyPlacing::find($record->money_placing_id);
                            if ($moneyPlacing->amount < $record->amount) {
                                Notification::make()
                                    ->title('Saldo anda tidak mencukupi untuk membayar hutang ini.')
                                    ->danger()
                                    ->send();
                                return;
                            } else {
                                // catat pengeluaran di tabel transaction
                                Transaction::create([
                                    'user_id' => auth()->id(),
                                    'type' => 'pengeluaran',
                                    'categories_id' => 9, //hutang keluar,
                                    'amount' => $record->amount,
                                    'date' => Carbon::now(),
                                    'note' => 'Pembayaran hutang kepada ' . $record->nama_pemberi_hutang . ' (Rp ' . number_format($record->amount, 0, ',', '.') . ')',
                                    'money_placing_id' => $record->money_placing_id,
                                ]);

                                // Ubah jadi lunas
                                $record->status = 'Lunas';
                                $record->save();

                                // Membuat notifikasi sukses
                                Notification::make()
                                    ->title('Hutang telah dibayar.')
                                    ->success()
                                    ->send();
                            }
                        }
                    ),
                action::make('Detail')
                    ->label('Detail')
                    ->icon('heroicon-o-information-circle')
                    ->color('primary')
                    ->modalHeading('Detail Catatan Hutang')
                    ->modalContent(function (DebtRecord $record) {
                        return view('filament.pages.debt-record-resource.detailModal', ['record' => $record]);
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada Catatan Hutang')
            ->emptyStateDescription('Silakan tambahkan catatan hutang baru untuk memulai.')
            ->emptyStateIcon('heroicon-o-clipboard-document')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Buat Catatan Hutang')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                ,
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
            'index' => Pages\ListDebtRecords::route('/'),
            'create' => Pages\CreateDebtRecord::route('/create'),
            'edit' => Pages\EditDebtRecord::route('/{record}/edit'),
        ];
    }
}
