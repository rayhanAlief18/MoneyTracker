<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class chatToAdminModel extends Model
{
    use HasFactory;

    protected $table = 'chat_to_admin';
    protected $fillable =[
        'user_id',
        'pesan',
        'status',
        'balasan'
    ];

    public function user():BelongsTo{
        return $this->belongsTo(User::class,'user_id','id');
    }
}
