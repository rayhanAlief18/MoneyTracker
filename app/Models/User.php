<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\FilamentUser;
use App\Models\transactionModel;
use App\Models\financialPlanModel;
use App\Models\debtRecord;
class User extends Authenticatable implements FilamentUser
{
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'job',
        'last_login',
        'login_at',
        'is_online',
        'status', // Added status field
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_login' => 'datetime',
        'login_at' => 'datetime',
        'is_online' => 'boolean',
    ];

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return in_array($this->role , ['admin','user']); // Ensure the role matches
    }

    public function rememberToken()
    {
        return $this->hasMany(RememberToken::class);
    }

    public function transaction()
    {
        return $this->hasMany(transactionModel::class);
    }

    public function financialPlan()
    {
        return $this->hasMany(financialPlanModel::class);
    }

    

    public function moneyPlacing(){
        return $this->hasMany(moneyPlacingModel::class,'user_id','id');
    }

    public function debtRecords()
    {
        return $this->hasMany(debtRecord::class, 'user_id', 'id');
    }

    public function debtorRequest(){
        return $this->hasMany(debtRequestModel::class, 'debtor_user_id', 'id');
    }

    public function creditorRequest(){
        return $this->hasMany(debtRequestModel::class, 'creditor_user_id', 'id');
    }

    
}
