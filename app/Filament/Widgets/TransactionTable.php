<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\transactionModel as Transaction;
class TransactionTable extends BaseWidget
{
    protected static ?int $maxResults = 5;
    protected static ?int $sort = 2; // Atur urutan muncul (jika ada beberapa widget

    public function table(Table $table): Table
    {

        return $table
            ->query(
                Transaction::query()
            ->where('user_id', auth()->id())
            ->latest('created_at') // Urutkan berdasarkan yang terbaru
            
            
            )
            ->columns([
                Tables\Columns\TextColumn::make('categories.name')->label('Kategori'),
                Tables\Columns\TextColumn::make('type')->label('Tipe')
                ->color(function ($state){
                    return $state === 'pemasukan' ? 'success' : 'danger';
                }),
                Tables\Columns\TextColumn::make('amount')->label('Jumlah')->money('IDR', true),
                Tables\Columns\TextColumn::make('date')->label('Tanggal')->date(),
            ]);
    }
}
