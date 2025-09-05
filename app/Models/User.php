<?php

namespace App\Models;

use App\Models\Order\Order;
use App\Models\Region\Region;
use App\Models\Customer\Customer;
use App\Models\Supplier\Supplier;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'region_id'];

    protected $hidden = ['password', 'remember_token'];


    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function supplier()
    {
        return $this->hasOne(Supplier::class);
    }

    public function Customer()
    {
        return $this->hasOne(Customer::class);
    }
    public function regions()
    {
        return $this->belongsToMany(Region::class, 'region_supplier', 'supplier_id', 'region_id');
    }

    public function deliveryRegions()
    {
        return $this->belongsToMany(Region::class, 'region_supplier', 'supplier_id', 'region_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }
}
