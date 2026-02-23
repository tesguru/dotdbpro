<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable; // 👈 change import
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class UserAccount extends Authenticatable // 👈 change this
{
    use HasFactory, SoftDeletes, Notifiable; // 👈 add Notifiable

    protected $table = 'user_accounts';
       protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'username',
        'email_address',
        'phone_number',
        'password',
        'dodo_customer_id',
        'sign_up_type',
        'verify_status',
        'verify_date'
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'verify_status' => 'boolean',
        'verify_date'   => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->user_id)) {
                $model->user_id = (string) Str::ulid();
            }
        });
    }
}
