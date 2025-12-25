<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'user_id',
        'date',
        'count'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(UserAccount::class);
    }
}
