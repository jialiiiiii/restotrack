<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['row', 'col', 'seat', 'status'];

    public function scopeRealOnly($query)
    {
        return $query->where('row', '>', 0)->where('col', '>', 0);
    }
}
