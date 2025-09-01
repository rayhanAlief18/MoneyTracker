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
            'type' => 'hutang',
            'categories_id'=> 8, //hutang masuk,
            'amount' => $debtRecord->amount,
            'date' => Carbon::now(),
            'note' => 'Anda hutang kepada '.$debtRecord->nama_pemberi_hutang.' sebesar Rp '.$debtRecord->amount,
            'money_placing_id' => $debtRecord->money_placing_id,
        ]);

        if ($debtRecord->jenis_hutang === "Kontrak") {

            $user = User::where('name',$debtRecord->nama_pemberi_hutang)->first();
            // dd($user);
            $data = debtRequestModel::create([
                'debtor_user_id' => auth()->id(),
                'debt_date'=>$debtRecord->tanggal_hutang,
                'due_date'=>$debtRecord->tanggal_rencana_bayar,
                'creditor_user_id' => $user->id,
                'status' => 'pending',
                'id_debt' => $debtRecord->id,
            ]);

            if ($data) {
                Notification::make()
                    ->title('Debt kontrak berhasil dibuat, tunggu konfirmasi dari pemberi hutang')
                    ->success()
                    ->send();
            }
        }

        if ($debtRecord->jenis_hutang === "Kontrak") {
            Notification::make()
                ->title('Debt Request berhasil dibuat, uang hutang akan masuk ke alokasi yang dipilih')
                ->success()
                ->send();
        }
        MoneyPlacing::find($debtRecord->money_placing_id)->increment('amount', $debtRecord->amount);
    }

    /**
     * Handle the debtRecord "updated" event.
     */
    public function updated(debtRecord $debtRecord): void
    {
        //
    }

    /**
     * Handle the debtRecord "deleted" event.
     */
    public function deleted(debtRecord $debtRecord): void
    {
        //
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
