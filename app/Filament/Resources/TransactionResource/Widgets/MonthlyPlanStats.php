<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\monthlyPlanModel as MonthlyPlan; // Import model MonthlyPlan

class MonthlyPlanStats extends BaseWidget
{
    protected static ?int $sort = -2; // Mengatur urutan agar tampil di atas

    protected function getStats(): array
    {
        // Ambil semua rencana bulanan untuk user yang sedang login
        // Sesuaikan query ini jika Anda ingin filter berdasarkan bulan, tahun, dll.
        $monthlyPlans = MonthlyPlan::where('user_id', auth()->id())->get();

        $totalMaxAmount = $monthlyPlans->sum('max_amount');
        $totalAmountNow = $monthlyPlans->sum('amount_now');
        $remainingAmount = $totalMaxAmount - $totalAmountNow;

        return [
            Stat::make('Total Max Plan Amount', 'IDR ' . number_format($totalMaxAmount, 0, ',', '.'))
                ->description('Jumlah target rencana')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),
            Stat::make('Total Current Amount', 'IDR ' . number_format($totalAmountNow, 0, ',', '.'))
                ->description('Jumlah yang sudah tercapai')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
            Stat::make('Remaining Budget', 'IDR ' . number_format($remainingAmount, 0, ',', '.'))
                ->description('Sisa anggaran')
                ->descriptionIcon('heroicon-m-scale')
                ->color($remainingAmount >= 0 ? 'success' : 'danger'), // Warna hijau jika positif, merah jika negatif
        ];
    }
}