<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class debtRequestModel extends Model
{
    use HasFactory;

    protected $table = 'debt_request';

    protected $fillable = [
        'debtor_user_id',
        'creditor_user_id',
        'amount',
        'debt_date',
        'due_date',
        'status',
        'id_debt',
        'keterangan',
        'jenis_hutang',
        'money_placing_id'
        
    ];

    protected $casts = [
        'debt_date' => 'date',
        'due_date' => 'date',
    ];
    public function paymentDebtRequest()
    {
        return $this->hasMany(debtRequestPaymentModel::class, foreignKey:'debt_request_id',localKey:'id');
    }

    //penghutang
    public function debtor(){
        return $this->belongsTo(User::class, 'debtor_user_id', 'id');
    }

    // pemberi hutang
    public function creditor(){
        return $this->belongsTo(User::class, 'creditor_user_id', 'id');
    }

    public function debtRecord(){
        return $this->belongsTo(DebtRecord::class, 'id_debt', 'id');
    }




}
