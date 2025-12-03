<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

  protected $fillable = [
    'subscription_id',
    'user_id',
    'product_id',
    'status',
    'currency',
    'amount',
    'payment_frequency_count',
    'payment_frequency_interval',
    'subscription_period_count',
    'subscription_period_interval',
    'next_billing_date',
    'previous_billing_date',
    'expires_at',
    'raw_payload',
];

protected $casts = [
    'raw_payload' => 'array', // ensures it’s stored/retrieved as JSON
];


    public function payments()
    {
        return $this->hasMany(Payment::class, 'subscription_id', 'subscription_id');
    }

    public function user()
    {
        return $this->belongsTo(UserAccount::class, 'user_id');
    }
}
