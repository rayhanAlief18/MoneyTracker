<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class monthlyPlanModel extends Model
{
    use HasFactory;

    protected $table = 'monthly_plan';
    protected $fillable = [
        'name',
        'user_id',
        'category_id',
        'name',
        'description',
        'max_amount',
        'amount_now',
        'year',
        'month',
        
    ];

    public function transaction(){
        return $this->hasMany(transactionModel::class, 'monthly_plan_id');
    }

    public function category(){
        return $this->belongsTo(categoriesModel::class, 'category_id', 'id');
    }
}
