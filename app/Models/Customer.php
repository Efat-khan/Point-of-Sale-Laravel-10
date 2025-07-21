<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];
     // Define the relationship with the Payment model
     public function payments()
     {
         return $this->hasMany(Payment::class);
     }
}
