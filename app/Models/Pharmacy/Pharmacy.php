<?php

namespace App\Models\Customer;

use App\Models\User\User;
use App\Models\Order\Order;
use App\Models\Region\Region;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'Customer_name', 'owner_name', 'address', 'phone', 'region_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
