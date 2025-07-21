<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTax extends Model
{
    protected $table = 'product_taxes';
    protected $fillable = ['product_id', 'tax_id'];
    public $timestamps = false;
}
