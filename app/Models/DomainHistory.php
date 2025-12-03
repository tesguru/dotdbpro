<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DomainHistory extends Model
{
    protected $table = 'domain_histories';

    protected $fillable = [
        'user_id',
        'domain_id',
        'domain_name',
        'status',
        'sold_price',
        'sale_mode',
        'sale_note',
        'revenue',
        'lander_sold',
        'total_acquisition_amount',
        'dns',
        'sale_mode',
        'date_sold',
        'expires_at',
        'is_deleted',
        'keywords',
        'renewed_price',
            'renewed_times',
        'change_type',
        'snapshot_at'
    ];

    protected $casts = [
        'sold_at' => 'datetime',
        'expires_at' => 'datetime',
        'snapshot_at' => 'datetime',  // Add this cast too
        'is_deleted' => 'boolean',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id', 'domain_id');
    }
}
