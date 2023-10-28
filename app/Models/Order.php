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
}