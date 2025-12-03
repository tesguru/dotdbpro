<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Payment extends Model
{
    use HasFactory;

   protected $fillable = [
    'payment_id',
    'user_id',
    'subscription_id',
    'business_id',
    'status',
    'total_amount',
    'currency',
    'payment_method',
    'card_last_four',
    'card_type',
    'card_network',
    'customer_id',
    'customer_name',
    'customer_email',
    'raw_payload',
];
protected $casts = [
    'raw_payload' => 'array', // ensures it’s stored/retrieved as JSON
];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id', 'subscription_id');
    }

    public function user()
    {
        return $this->belongsTo(UserAccount::class, 'user_id');
    }
}
