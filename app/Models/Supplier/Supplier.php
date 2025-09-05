<?php

namespace App\Models\Supplier;

use App\Models\User\User;
use App\Models\Region\Region;
use App\Models\Order\Discount;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'company_name', 'address', 'phone', 'region_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function discounts()
    {
        return $this->hasMany(SupplierDiscount::class);
    }
}
