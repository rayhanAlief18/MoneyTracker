<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use app\Models\User;

class debtRecord extends Model
{
    use HasFactory;

    protected $table = 'debt_records';
    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'jenis_hutang',
        'nama_pemberi_hutang',
        'keterangan',
        'tanggal_hutang',
        'tanggal_rencana_bayar',
        'user_id',
        'money_placing_id',
    ];

    protected $casts = [
        'tanggal_hutang' => 'date',
        'tanggal_rencana_bayar' => 'date',
        'amount' => 'float',
    ];
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function debtRequests(){
        return $this->hasMany(debtRequestModel::class, 'id_debt', 'id');
    }
}
