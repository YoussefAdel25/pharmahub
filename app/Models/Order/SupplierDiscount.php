<?php

namespace App\Models\Order;

use App\Models\User;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierDiscount extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'product_id', 'discount_rate'];


    public function supplier() {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
