<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Validation\ValidationException;
class financialPlanProgressModel extends Model
{
    use HasFactory;

    protected $table = 'financial_plan_progress';
    protected $fillable = [
        'id_financial_plan',
        'amount',
        'presentase_progress',
        'date',
        'money_placing_id'
    ];
    protected $casts = [
        'date' => 'datetime',
    ];


    public function financialPlan()
    {
        return $this->belongsTo(financialPlanModel::class, 'id_financial_plan', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected static function booted()
    {
        // Validasi sebelum data disimpan
        static::creating(function ($progress) {
            $plan = $progress->financialPlan;

            if ($plan && ($plan->progress()->sum('amount') + $progress->amount) > $plan->target_amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Jumlah progress melebihi target rencana.',
                ]);
            }
        });

        // Update jumlah setelah progress disimpan
    static::created(function ($progress) {
        $plan = $progress->financialPlan;
        if ($plan) {
            $total = $plan->progress()->sum('amount');
            $plan->update(['amount_now' => $total]);
        }
    });
        static::deleted(function ($progress) {
            $plan = $progress->financialPlan;
            if($plan){
                $total = $plan->progress()->sum('amount');
                $plan->update(['amount_now' => $total]);
            }
        });

        static::updated(function ($progress){
            $plan = $progress->financialPlan;
            if($plan){
                $total = $plan->progress()->sum('amount');
                $plan->update(['amount_now' => $total]);
            }
        });
    }

}
