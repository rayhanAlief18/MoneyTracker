<?php

namespace App\Observers;

use App\Models\debtRecord;
use App\Models\debtRequestModel;
use App\Models\User;
use App\Models\MoneyPlacingModel as MoneyPlacing;
use App\Models\transactionModel as Transaction;
use Filament\Notifications\Notification;
use Carbon\Carbon;
class DebtObserver
{
    /**
     * Handle the debtRecord "created" event.
     */
    public function created(debtRecord $debtRecord): void
    {
        Transaction::create([
            'user_id' => auth()->id(),
            'type' => 'pemasukan',
            'categories_id'=> 8, //hutang masuk,
            'amount' => $debtRecord->amount,
            'date' => Carbon::now(),
            'note' => 'Penerimaan hutang dari '.$debtRecord->nama_pemberi_hutang.' sebesar Rp '.$debtRecord->amount.' dengan keterangan '.$debtRecord->keterangan.'.',
            'money_placing_id' => $debtRecord->money_placing_id,
        ]);
        
        // tambah amount money placing tujuan
        // MoneyPlacing::find($debtRecord->money_placing_id)->increment('amount', $debtRecord->amount);

        
    }

    /**
     * Handle the debtRecord "updated" event.
     */
    public function updated(debtRecord $debtRecord): void
    {
        $originalJumlahHutang = $debtRecord->getOriginal('amount');
        $originalMoneyPlacing = $debtRecord->getOriginal('money_placing_id');
        
        $newJumlahHutang = $debtRecord->amount;
        $newMoneyPlacing = $debtRecord->money_placing_id;
        
        $mountChange = $originalJumlahHutang !== $newJumlahHutang;
        $moneyPlacingChange = $originalJumlahHutang !== $originalMoneyPlacing;

        if($mountChange || $moneyPlacingChange)
        {
            MoneyPlacing::find($originalMoneyPlacing)->decrement('amount',$originalJumlahHutang);
            $MoneyPlacingBaru = MoneyPlacing::find($newMoneyPlacing)->increment('amount',$newJumlahHutang);

            Notification::make()
                ->title('Alokasi keuangan telah diperbarui')
                ->success()
                ->send();
        }


    }

    /**
     * Handle the debtRecord "deleted" event.
     */
    public function deleted(debtRecord $debtRecord): void
    {
        // kurangi money placing kalau hapus hutang
        MoneyPlacing::find($debtRecord->money_placing_id)->decrement('amount',$debtRecord->amount);
    }

    /**
     * Handle the debtRecord "restored" event.
     */
    public function restored(debtRecord $debtRecord): void
    {
        //
    }

    /**
     * Handle the debtRecord "force deleted" event.
     */
    public function forceDeleted(debtRecord $debtRecord): void
    {
        //
    }
}
