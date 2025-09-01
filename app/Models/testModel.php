<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class testModel extends Model
{
    use HasFactory;

    protected $table = 'test'; // Ganti dengan nama tabel yang sesuai    

    protected $fillable = [
        'tes',
    ];
}
