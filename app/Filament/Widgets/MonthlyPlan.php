<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\MonthlyPlanModel; // Import model MonthlyPlanModel
use Carbon\Carbon; 
class MonthlyPlan extends BaseWidget
{
    protected int|string|array $columnSpan = 'full'; // Biar penuh lebarnya
    protected function getStats(): array
    {
        $monthlyPlans = MonthlyPlanModel::where('user_id', auth()->id())
        ->where('year',Carbon::now()->year)
        ->where('month', Carbon::now()->locale('id')->translatedFormat('F'))
        ->get();

        if ($monthlyPlans->isEmpty()) {
            return [
                Stat::make('Belum Ada Rencana Bulan Ini', 'N/A')
                    ->description('Buat rencana bulanan Anda!')
                    ->color('warning')
                    ->icon('heroicon-m-exclamation-triangle'),
            ];
        } else {
            $stats = [];
            foreach ($monthlyPlans as $plan) {
                $progress = ($plan->amount_now / $plan->max_amount) * 100;

                $stats[] = Stat::make( $plan->name, "IDR ". number_format($plan->amount_now, 0, ',', '.'))
                    ->description('Max: ' . "IDR " .  number_format($plan->max_amount, 0, ',', '.') . ' (' . number_format($progress, 2) . '%)')
                    ->descriptionIcon($progress >= 100 ? 'heroicon-m-x-circle' : 'heroicon-m-chart-bar')
                    ->color($progress >= 100 ? 'danger' : ($progress > 50 ? 'info' : 'success'));
            }
            return $stats;
        }
    }
}
