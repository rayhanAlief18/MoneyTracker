<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\moneyPlacingModel as moneyPlacing;
class MoneyPlacingDashboard extends Widget
{
    protected static string $view = 'filament.widgets.money-placing-dashboard';
    protected static ?int $sort = 1; // Atur urutan muncul (jika ada beberapa widget)

    protected function getViewData(): array
    {
        // Ambil data yang diperlukan untuk tampilan ini
        $placements = moneyPlacing::where('user_id', auth()->id())->get();
        return [
            'totalSaldo' => moneyPlacing::where('user_id', auth()->id())->sum('amount'),
            'placements' => $placements,
        ];
    }
}
