<?php

namespace App\Models\Region;

use App\Models\Product\Product;
use App\Models\Pharmacy\Pharmacy;
use App\Models\Supplier\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function pharmacies()
    {
        return $this->hasMany(Pharmacy::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
