<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'pax', 'datetime'];
    protected $casts = [
        'datetime' => 'datetime'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
