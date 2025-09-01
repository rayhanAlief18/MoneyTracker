<?php

namespace App\Providers;

use App\Models\debtRecord;
use App\Observers\DebtObserver;
use App\Observers\TransactionObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\transactionModel as Transaction;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Transaction::observe(TransactionObserver::class);
        debtRecord::observe(DebtObserver::class);
        
        
    }
}
