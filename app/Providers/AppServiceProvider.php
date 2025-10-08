<?php

namespace App\Providers;

use App\Models\chatToAdminModel;
use App\Models\debtRecord;
use App\Models\debtRequestModel;
use App\Observers\ChatToAdminObserver;
use App\Observers\DebtObserver;
use App\Observers\DebtRequestObserver;
use App\Observers\TransactionObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\transactionModel as Transaction;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        FilamentColor::register([
            'danger' => Color::Red,
            'gray' => Color::Zinc,
            'info' => Color::Blue,
            'primary' => Color::Amber,
            'success' => Color::Green,
            'warning' => Color::Amber,
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Transaction::observe(TransactionObserver::class);
        debtRecord::observe(DebtObserver::class);
        debtRequestModel::observe(DebtRequestObserver::class);


    }
}
