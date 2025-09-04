<?php

namespace App\Models\Order;

use App\Models\User;
use App\Models\Pharmacy\Pharmacy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['pharmacy_id', 'status', 'total_price'];

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
