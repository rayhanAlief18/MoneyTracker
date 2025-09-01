<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\transactionModel as Transaction;

class categoriesModel extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $fillable = ['name', 'type'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'category', 'name');
    }

    public function monthlyPlans(){
        return $this->hasMany(monthlyPlanModel::class, 'category_id', 'id');
    }
}
