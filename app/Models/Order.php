<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
     protected $fillable = ['status', 'order_reference', 'customer_name', 'customer_phone', 'total_amount', 'items_summary', 'shipping_address', 'city'];


     public function conversations(){
          return $this->hasMany(Conversation::class);
     }
     }
