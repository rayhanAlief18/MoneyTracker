<?php

namespace App\Observers;

use App\Models\debtRequestModel;

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
        // buat notif ke user kalau ada perubahan status dan tambahkan ke money placing
    }

    /**
     * Handle the debtRequestModel "deleted" event.
     */
    public function deleted(debtRequestModel $debtRequestModel): void
    {
        //
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
