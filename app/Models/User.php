<?php

namespace App\Models;

use App\Models\Region\Region;
use App\Models\Pharmacy\Pharmacy;
use App\Models\Supplier\Supplier;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'region_id'];

    protected $hidden = ['password', 'remember_token'];


    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    public function isSupplier()
    {
        return $this->role === 'supplier';
    }
    public function isPharmacy()
    {
        return $this->role === 'pharmacy';
    }

    public function supplier()
    {
        return $this->hasOne(Supplier::class);
    }

    public function pharmacy()
    {
        return $this->hasOne(Pharmacy::class);
    }
    public function region() {
    return $this->belongsTo(Region::class);
}

}
