<?php

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\financialPlanModel as financialPlan;
class PlanTable extends BaseWidget
{
    protected static ?int $sort = 3; // Atur urutan muncul (jika ada beberapa widget
    // protected int|string|array $columnSpan = 1; // Biar penuh lebarnya
    protected static ?string $heading = 'Target Rencana Keuangan';


    // protected function getTableContentFooter(): ?\Illuminate\View\View
    // {
    //     $totalToday = financialPlan::where('user_id', auth()->id())
    //         ->where('type', 'pengeluaran')
    //         ->sum('amount');

    //     return view('filament.widgets.transaction-footer', [
    //         'total' => $totalToday,
    //     ]);
    // }

    protected function getTableQuery(): Builder|Relation|null
    {
        return financialPlan::query()
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {

        return [
            Tables\Columns\TextColumn::make('description')->label('Nama Rencana'),
            Tables\Columns\TextColumn::make('target_amount')->label('Target')->money('IDR', true),
            Tables\Columns\TextColumn::make('amount_now')->label('Jumlah Sekarang')->money('IDR', true),

            TextColumn::make('progres')
                ->label('Progres')
                ->formatStateUsing(function ($state, $record) {
                    if ($record->target_amount <= 0) {
                        return '0%';
                    }

                    $progress = ($record->amount_now / $record->target_amount) * 100;
                    return number_format(min(100, $progress), 2) . '%';
                })->color(function ($record){
                    $progress = ($record->amount_now / $record->target_amount) * 100;

                    if($progress < 50){
                        return 'primary';
                    }else if($progress == 100){
                        return 'success'; // Hijau jika sudah 100%
                    }
                })
        ];
    }
}
