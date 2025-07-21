<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase2 extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function purchase_details(){
        return $this->hasMany(PurchasDetails::class,'purchase_id','id'); // VVI Note: hasMany Relation is a difference with above belongsTo, firstly Invoice_Id from InvoiceDetail Model and ID is from Invoice Model
    }

    public function purchase_details2(){
        return $this->belongsTo(PurchasDetails::class,'id','purchase_id');
    }
    
    // public function purchase_payment() {
    //     return $this->hasMany(PurchasePayment::class,'purchase_id','id');
    // }
}
