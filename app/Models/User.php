<?php

namespace App\Models;

use App\Models\Region\Region;
use App\Models\Customer\Customer;
use App\Models\Supplier\Supplier;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];


    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    public function isSupplier()
    {
        return $this->role === 'supplier';
    }
    public function isCustomer()
    {
        return $this->role === 'Customer';
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

    // User.php
    public function deliveryRegions()
    {
        return $this->belongsToMany(Region::class, 'region_supplier', 'supplier_id', 'region_id');
    }
}
