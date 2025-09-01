<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class moneyPlacingModel extends Model
{
    use HasFactory;
    protected $table = 'money_placing';
    protected $fillable = [
        'user_id',
        'name',
        'amount',
    ];
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function transaction()
    {
        return $this->hasMany(transactionModel::class,'money_placing_id','id');
    }
}
