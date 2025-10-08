<?php

namespace App\Observers;

use App\Models\financialPlanModel;
use App\Models\financialPlanProgressModel;
use App\Models\moneyPlacingModel;
use App\Models\transactionModel;
use Carbon\Carbon;

class FinancialPlanProgressObserver
{
    /**
     * Handle the financialPlanProgressModel "created" event.
     */
    public function created(financialPlanProgressModel $financialPlanProgressModel): void
    {
        $plan = financialPlanModel::find($financialPlanProgressModel->id_financial_plan);
        $financialPlanProgressModel->update([
            'presentase_progress'=> ($financialPlanProgressModel->amount / $plan->target_amount) * 100,
        ]);

        transactionModel::create([
                    'user_id' =>auth()->id(),
                    'type' => 'pengeluaran',
                    'categories_id' => 7, //hutang masuk,
                    'amount' => $financialPlanProgressModel->amount,
                    'date' => Carbon::now(),
                    'note' => 'Menabung pada tabungan dengan nama "' . $financialPlanProgressModel->financialPlan->description. ' sebesar Rp ' . $financialPlanProgressModel->amount,
                    'money_placing_id' => $financialPlanProgressModel->money_placing_id,
                ]);

        
    }

    /**
     * Handle the financialPlanProgressModel "updated" event.
     */
    public function updated(financialPlanProgressModel $financialPlanProgressModel): void
    {
        //
    }

    /**
     * Handle the financialPlanProgressModel "deleted" event.
     */
    public function deleted(financialPlanProgressModel $financialPlanProgressModel): void
    {
        //
    }

    /**
     * Handle the financialPlanProgressModel "restored" event.
     */
    public function restored(financialPlanProgressModel $financialPlanProgressModel): void
    {
        //
    }

    /**
     * Handle the financialPlanProgressModel "force deleted" event.
     */
    public function forceDeleted(financialPlanProgressModel $financialPlanProgressModel): void
    {
        //
    }
}
