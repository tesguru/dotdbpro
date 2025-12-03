<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
class Domain extends Model
{
    use SoftDeletes;

    protected $table = 'domains';

    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'domain_name',
        'status',
        'keywords',
        'lander_sold',
        'sold_price',
        'sale_mode',
        'date_sold',
        'sale_note',
        'revenue',
        'domain_id',
        'registered_with',
        'dns',
        'expires_at',
        'deleted_at',
        'renewed_price',
        'renewed_times'
    ];


    protected $dates = [
        'sold_at',
        'expires_at',
        'deleted_at',
        'created_at',
        'updated_at',
    ];


    protected $casts = [
        'sold_price' => 'double',
        'revenue' => 'double',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

     protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->domain_id)) {
                $model->domain_id = 'domain_'.(string) Str::ulid();
            }
        });
    }
}
