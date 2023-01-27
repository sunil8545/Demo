<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'paymentID',
        'amount',
        'message',
        'status',
        'response',
    ];

    protected $casts = ['response'=>'array'];

    public function scopeSuccess($query)
    {
        $query->where('status','success');
    }

    public function scopeFailed($query)
    {
        $query->where('status','<>','success');
    }
}
