<?php

namespace App\Observers;

use App\Models\transactionModel;
use Carbon\Carbon;
use App\Models\monthlyPlanModel;
use App\Models\moneyPlacingModel;
class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(transactionModel $transaction): void
    {
        //note bagian money placing sudah di handle di model transactionModel
        
        // akan mempengaruhi monthly plan
        $this->updateMonthlyPlan($transaction, 'created');
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(transactionModel $transaction): void
    {
        // Dapatkan nilai asli (sebelum perubahan) dari transaksi
        $originalAmount = $transaction->getOriginal('amount');
        $originalType = $transaction->getOriginal('type');
        $originalCategoryId = $transaction->getOriginal('categories_id');
        $originalCreatedAt = Carbon::parse($transaction->getOriginal('date'));
        $originalMonthName = $originalCreatedAt->locale('id')->translatedFormat('F');
        $originalYear = $originalCreatedAt->year;

        // Dapatkan nilai baru (setelah perubahan) dari transaksi
        $newAmount = $transaction->amount;
        $newType = $transaction->type;
        $newCategoryId = $transaction->categories_id;
        $newCreatedAt = Carbon::parse($transaction->date);
        $newMonthName = $newCreatedAt->locale('id')->translatedFormat('F');
        $newYear = $newCreatedAt->year;

        $categoryChange = $originalCategoryId !== $newCategoryId;
        $createdAtChange = $originalMonthName !== $newMonthName || $originalYear !== $newYear;
        $amountChange = $originalAmount !== $newAmount;
        $typeChange = $originalType !== $newType;
        $moneyPlacingChange = $transaction->getOriginal('money_placing_id') !== $transaction->money_placing_id;
        // dd("bulan awalawads",$originalMonthName, "tahun awal",$originalYear, " || ", "bulan baru",$newMonthName, "tahun baru",$newYear, "apakah bulan atau tahun berubah?",$createdAtChange, "apakah kategori berubah?",$categoryChange, "apakah tipe berubah?",$createdAtChange);
        
        if ($originalType === 'pengeluaran' && $newType === 'pengeluaran') {
            if ($categoryChange || $createdAtChange || $amountChange) {
                // Update monthly plan for original transaction 
                $this->updateMonthlyPlanForOriginal($originalAmount, $originalCategoryId, $originalMonthName, $originalYear, 'subtract');
                $this->updateMonthlyPlanForNew($newAmount, $newCategoryId, $newMonthName, $newYear, 'add');

                
                // update money placing if changed
                if($moneyPlacingChange){
                    $moneyPlacingLama = MoneyPlacingModel::where('id',$transaction->getOriginal('money_placing_id'));
                    $moneyPlacingBaru = MoneyPlacingModel::where('id',$transaction->money_placing_id)->first();
                    
                    $moneyPlacingLama->increment('amount', $originalAmount);
                    $moneyPlacingBaru->decrement('amount', $newAmount);

                }
            } 
            // else if ( $originalAmount !== $newAmount) {
            //     $this->updateMonthlyPlanForOriginal($originalAmount - $newAmount, $newCategoryId, $newMonthName, $newYear, 'subtract');
            // }
        }else if($originalType === 'pengeluaran' && $newType === 'pemasukan'){
            $this->updateMonthlyPlanForOriginal($originalAmount, $originalCategoryId, $originalMonthName, $originalYear, 'subtract');
            
            // ubah money placing
            MoneyPlacingModel::where('id',$transaction->money_placing_id)
            ->increment('amount', $newAmount);
            
        }else if($originalType === 'pemasukan' && $newType === 'pengeluaran'){
            $this->updateMonthlyPlanForNew($newAmount, $newCategoryId, $newMonthName, $newYear, 'add');
            
            // ubah money placing
            MoneyPlacingModel::where('id',$transaction->money_placing_id)
                ->decrement('amount', $newAmount);
        
        }

    }


    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(transactionModel $transaction): void
    {
        // bagian money placing sudah di handle di model transactionModel


        // akan mempengaruhi monthly plan
        $this->updateMonthlyPlan($transaction, 'deleted');
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(transactionModel $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(transactionModel $transaction): void
    {
        //
    }

    private function updateMonthlyPlanForNew($amountIn, $newCategoryId, $newMonthName, $newYear,)
    {
        $monthlyPlanNew = monthlyPlanModel::where('category_id', $newCategoryId)
            ->where('year', $newYear)
            ->where('month', $newMonthName)
            ->first();
        
        if($monthlyPlanNew){
            $monthlyPlanNew->amount_now += (int)$amountIn;
            $monthlyPlanNew->amount_now = max(0, $monthlyPlanNew->amount_now);
            $monthlyPlanNew->save();
        }
    }

    private function updateMonthlyPlanForOriginal($amountDifference, $categoryId, $monthNamePick, $yearPick, $operation )
    {
        $monthlyPlanOriginal = monthlyPlanModel::where('category_id', $categoryId)
            ->where('year', $yearPick)
            ->where('month', $monthNamePick)
            ->first();

        if ($monthlyPlanOriginal) {
            if($operation === 'subtract'){
                $monthlyPlanOriginal->amount_now -= $amountDifference;
                $monthlyPlanOriginal->amount_now = max(0, $monthlyPlanOriginal->amount_now);
            }else if ($operation === 'delete'){
                $monthlyPlanOriginal->amount_now += (int)$amountDifference;
            }
        $monthlyPlanOriginal->save();
        }
    }

    public function updateMonthlyPlan(transactionModel $transaction, string $operation)
    {

        if ($transaction->type === "pengeluaran") {
            
            $monthNumber = Carbon::parse($transaction->created_at)->month;
            $monthName = Carbon::createFromFormat('m', $monthNumber)->locale('id')->translatedFormat('F');
            $year = Carbon::parse($transaction->created_at)->year;
            
            $monthlyPlan = monthlyPlanModel::where('category_id', $transaction->categories_id) // <-- PERBAIKAN PENTING DI SINI
            ->where('year', $year)
            ->where('month', $monthName)
            ->first();
            
            if ($monthlyPlan) {
                if ($operation === 'created') {
                    $monthlyPlan->amount_now = $monthlyPlan->amount_now + $transaction->amount;
                } 
                elseif ($operation === 'deleted') {
                    $monthlyPlan->amount_now -= $transaction->amount;
                    $monthlyPlan->amount_now = max(0, $monthlyPlan->amount_now);
                }
                $monthlyPlan->save();
            }
        }
    }
}
