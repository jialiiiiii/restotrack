<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['name', 'description', 'category', 'price', 'sales', 'sold', 'available', 'deleted'];

    public function scopeNotDeleted($query)
    {
        return $query->where('deleted', false);
    }

    public function scopeIsAvailable($query)
    {
        return $query->where('available', true);
    }
}
