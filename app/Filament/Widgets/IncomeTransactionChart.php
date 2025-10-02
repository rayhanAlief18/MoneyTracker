<?php

namespace App\Filament\Widgets;

use App\Models\transactionModel as Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
class IncomeTransactionChart extends ChartWidget
{
    protected static ?string $heading = 'Pengeluaran bulan ini';
    protected static ?int $sort = 2; // Atur urutan muncul (jika ada beberapa widget
    protected function getData(): array
    {
        $userId = auth()->id();

        $startDate = now()->subDays(6)->startOfDay();
        $endDate = now()->endOfDay();

        $dates = collect();
        for ($i = 0; $i < 7; $i++) {
            $dates->push($startDate->copy()->addDays($i)->format('Y-m-d'));
        }

        // Simulasi pengambilan data berdasarkan created_at
        $dataPengeluaran = DB::table('transactions')
        ->where('user_id', auth()->id())
        ->where('type', 'pengeluaran')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
        ->groupBy(DB::raw('DATE(created_at)'))
        ->pluck('total', 'date');

        $dataPemasukan = DB::table('transactions')
        ->where('user_id', auth()->id())
        ->where('type', 'pemasukan')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
        ->groupBy(DB::raw('DATE(created_at)'))
        ->pluck('total', 'date');

        // Bangun data dengan default 0 jika tidak ada data
        $chartDataPengeluaran = $dates->map(function ($date) use ($dataPengeluaran) {
            return $dataPengeluaran[$date] ?? 0;
        });

        $chartDataPemasukan = $dates->map(function ($date) use ($dataPemasukan) {
            return $dataPemasukan[$date] ?? 0;
        });

        // $chartDataPemasukan = $dates->map(function ($date) use ($dataPemasukan) {
        //     return $dataPemasukan[$date] ?? 0;
        // });

        return [
            'labels' => $dates->map(fn($d) => Carbon::parse($d)->translatedFormat('D, d M')),
            'datasets' => [
                [
                    'label' => 'Pengeluaran bulan ini',
                    'data' => $chartDataPengeluaran->toArray(),
                    'backgroundColor' => '#F59E0B', // biru

                    // 'borderColor' => '#F59E0B', // biru
                ],
                [
                    'label' => 'Pemasukan bulan ini',
                    'data' => $chartDataPemasukan->toArray(),
                    'backgroundColor' => '#3B82F6', // biru
                    'borderColor' => '#3B82F6', // biru
                ]
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
