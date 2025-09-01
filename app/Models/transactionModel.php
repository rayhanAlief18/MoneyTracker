<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\categoriesModel as Categories;
class transactionModel extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    protected $fillable = [
        'user_id',
        'type', // 'pemasukan' or 'pengeluaran'
        'categories_id', // Foreign key to categories table
        'amount',
        'date',
        'note',
        'money_placing_id', // Foreign key to money placing table
    ];
    protected $casts = [
        'transaction_date' => 'datetime',
    ];  

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function categories(){
        return $this->belongsTo(Categories::class, 'categories_id', 'id');
    }

    public function moneyPlacing(){
        return $this->belongsTo(moneyPlacingModel::class, 'money_placing_id', 'id');
    }

    public function monthlyPlan()
    {
        return $this->belongsTo(monthlyPlanModel::class, 'monthly_plan_id', 'id');
    }
    protected static function booted(){
        static::created(function ($transaction){
            // Perbarui data setelah transaksi dibuat
            $placing = $transaction->moneyPlacing;

            if($transaction->moneyPlacing && $transaction->type === 'pemasukan'){
                $placing = $transaction->moneyPlacing;
                $placing->increment('amount',$transaction->amount);
            } elseif ($transaction->moneyPlacing && $transaction->type === 'pengeluaran') {
                $placing = $transaction->moneyPlacing;
                $placing->decrement('amount',$transaction->amount);
            }
        });

        static::deleted(function ($transaction){
            // Perbarui data setelah transaksi dihapus
            $placing = $transaction->moneyPlacing;

            if($transaction->moneyPlacing && $transaction->type === 'pemasukan'){
                $placing = $transaction->moneyPlacing;
                $placing->decrement('amount',$transaction->amount);
            } elseif ($transaction->moneyPlacing && $transaction->type === 'pengeluaran') {
                $placing = $transaction->moneyPlacing;
                $placing->increment('amount',$transaction->amount);
            }
        });
    }
}
