<?php

namespace App\Observers;

use App\Models\debtRequestModel;
use App\Models\transactionModel as Transaction;
use App\Models\moneyPlacingModel as MoneyPlacing;
use Carbon\Carbon;
use Filament\Notifications\Notification;
class DebtRequestObserver
{
    /**
     * Handle the debtRequestModel "created" event.
     */
    public function created(debtRequestModel $debtRequestModel): void
    {
        //
    }

    /**
     * Handle the debtRequestModel "updated" event.
     */
    public function updated(debtRequestModel $debtRequestModel): void
    {
        // // buat notif ke user kalau ada perubahan status dan tambahkan ke money placing
        // if ($debtRequestModel->status == 'Diterima (Belum Bayar)') {
        //     // buat transaction pengurangan untuk pemberi hutang &  kurangi money placing pemberi hutang            
        //     // *sudah di handle di reosource DebtRequestResource*

        //     //handle penerima hutang
        //     // tambah money placingnya
        //     MoneyPlacing::find($debtRequestModel->money_placing_id)->increment('amount', $debtRequestModel->amount);

        //     // buat transaction untuk penerima hutang
        //     Transaction::create([
        //         'user_id' =>$debtRequestModel->debtor_user_id,
        //         'type' => 'pemasukan',
        //         'categories_id' => 8, //hutang masuk,
        //         'amount' => $debtRequestModel->amount,
        //         'date' => Carbon::now(),
        //         'note' => 'Penerimaan hutang dari ' . $debtRequestModel->nama_pemberi_hutang . ' sebesar Rp ' . $debtRequestModel->amount . '. Dengan catatan hutang'. $debtRequestModel->keterangan,
        //         'money_placing_id' => $debtRequestModel->money_placing_id,
        //     ]);
        // }
    }

    /**
     * Handle the debtRequestModel "deleted" event.
     */
    public function deleted(debtRequestModel $debtRequestModel): void
    {
    }

    /**
     * Handle the debtRequestModel "restored" event.
     */
    public function restored(debtRequestModel $debtRequestModel): void
    {
        //
    }

    /**
     * Handle the debtRequestModel "force deleted" event.
     */
    public function forceDeleted(debtRequestModel $debtRequestModel): void
    {
        //
    }
}
