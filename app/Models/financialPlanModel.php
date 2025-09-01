<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class financialPlanModel extends Model
{
    use HasFactory;

    protected $table = 'financial_plans';
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'target_amount',
        'amount_now',
        'target_date',
    ];
    protected $casts = [
        'target_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Masukkan di dalam model FinancialPlan.php
    public function getProgresAttribute()
    {
        if ($this->target_amount <= 0)
            return 0;

        return round(($this->amount_now / $this->target_amount) * 100, 2);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function progress()
    {
        return $this->hasMany(financialPlanProgressModel::class, 'id_financial_plan', 'id');
    }
}
