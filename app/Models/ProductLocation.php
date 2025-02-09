<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLocation extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'location', 'price'];

    // علاقة كل موقع بمنتج معين
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
