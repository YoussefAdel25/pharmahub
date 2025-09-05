<?php

namespace App\Models\Product;

use App\Models\User;
use App\Models\Region\Region;
use App\Models\Order\OrderItem;
use App\Models\Order\SupplierDiscount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'name', 'description', 'price', 'stock', 'quota_limit', 'image','type','quota_period'];

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function discounts()
    {
        return $this->hasMany(SupplierDiscount::class);
    }
}
