<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DebtRecordResource\Pages;
use App\Filament\Resources\DebtRecordResource\RelationManagers;
use App\Models\DebtRecord;
use App\Models\User;
use App\Models\MoneyPlacingModel as MoneyPlacing;
use App\Models\transactionModel as Transaction;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
class DebtRecordResource extends Resource
{
    protected static ?string $model = DebtRecord::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Money Trakcer';

    protected static ?string $navigationLabel = 'Hutang / Piutang';
    protected static ?string $pluralModelLabel = 'Catatan Hutang';
    protected static ?int $navigationSort = 5;

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

                Forms\Components\Select::make('jenis_hutang')
                    ->label('Jenis Hutang')
                    ->options([
                        'Individu' => 'Individu',
                        'Kontrak' => 'Kontrak'
                    ])->live()
                    ->required(),

                Forms\Components\Select::make('nama_pemberi_hutang')
                    ->label('Pemberi Hutang')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $user = User::find($get('nama_pemberi_hutang'));
                        if ($user) {
                            $set('nama_pemberi_hutang', $user->name);
                        }
                    })
                    ->visible(fn($get) => $get('jenis_hutang') === 'Kontrak'),
                
                Forms\Components\Hidden::make('nama_pemberi_hutang')->dehydrated()->visible(fn(Get $get)=> $get('jenis_hutang')==="Kontrak"),

                Forms\Components\TextInput::make('nama_pemberi_hutang')
                    ->label('Pemberi Hutang')
                    ->required()
                    ->visible(fn($get) => $get('jenis_hutang') === 'Individu'),

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
                            $options[$placing->id] = $placing->name;
                        }
                        return $options;
                    })
                    ->required(),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(3),

                Forms\Components\DatePicker::make('tanggal_hutang')
                    ->label('Tanggal Hutang')
                    ->required(),

                Forms\Components\DatePicker::make('tanggal_rencana_bayar')
                    ->label('Rencana Bayar'),

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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
