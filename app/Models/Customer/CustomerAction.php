<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class CustomerAction extends Model
{
     protected $fillable = ['user_id', 'session_id', 'action_type','product_id'];

}
