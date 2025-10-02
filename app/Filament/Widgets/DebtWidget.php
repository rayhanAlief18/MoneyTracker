<?php

namespace App\Filament\Widgets;

use App\Models\debtRecord;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;
class DebtWidget extends BaseWidget
{
    protected static ?string $heading = 'Daftar Hutang';
    protected static ?int $maxResults = 5;
    protected static ?int $sort = 3; // Atur urutan muncul (jika ada beberapa widget
    public function table(Table $table): Table
    {
        return $table
            ->query(
                debtRecord::where('user_id', auth()->id())->where('status', 'Belum bayar')->latest('created_at')
            )
            ->columns([
                
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Total Hutang')
                    ->money('IDR', true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_pemberi_hutang')
                    ->label('Pemberi Hutang')
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->getStateUsing(function ($record) {
                        $tanggalHutang = Carbon::parse($record->tanggal_hutang);
                        $tanggalBayar = Carbon::parse($record->tanggal_rencana_bayar);
                        return $tanggalBayar->diffInDays($tanggalHutang, false) . ' hari';
                    })
                    ->color(function ($record){
                        $tanggalHutang = Carbon::parse($record->tanggal_hutang);
                        $tanggalBayar = Carbon::parse($record->tanggal_rencana_bayar);
                        $selisihHari = $tanggalBayar->diffInDays($tanggalHutang, false);

                        if ($selisihHari < 0) {
                            return 'danger'; // Merah jika sudah lewat
                        } elseif ($selisihHari <= 3) {
                            return 'warning'; // Kuning jika kurang dari atau sama dengan 7 hari
                        }
                        return 'success'; // Hijau jika lebih dari 7 hari
                    }),
            ]);
    }
}
