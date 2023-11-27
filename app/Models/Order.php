<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'table_id', 'type', 'status'];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function orderMeals()
    {
        return $this->hasMany(OrderMeal::class);
    }

    public function reservation()
    {
        return $this->hasOne(Reservation::class);
    }

    public function scopeOrderByDefault($query)
    {
        return $query->orderByRaw("FIELD(status, 'pending', 'preparing', 'served', 'paid', 'reserved', 'cancelled')");
    }
}
