<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class debtRequestPaymentModel extends Model
{
    use HasFactory;

    protected $table = 'debt_request_payment';

    protected $fillable =[
        'id',
        'debt_request_id',
        'status',
        'bukti_bayar',
        'money_placing_save',
        'payment_date',
        'receipt_date'
    ];

    public function debt_request(){
        return $this->belongsTo(debtRequestModel::class,foreignKey:'debt_request_id',ownerKey:'id');
    }

}
