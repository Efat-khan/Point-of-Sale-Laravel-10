<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPurchese extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function supplier_purchese_payment(){
        return $this->belongsTo(SupplierPurchesePayment::class,'id','purchase_id'); // Firstly ID is from SupplierPurchese Model, Invoice_id is from SupplierPurchesePayment 
    }

    public function supplier_purchese_details(){
        return $this->hasMany(SupplierPurcheseDetails::class,'purchase_id','id'); // VVI Note: hasMany Relation is a difference with above belongsTo, firstly Invoice_Id from SupplierPurcheseDetails Model and ID is from SupplierPurchese Model
    }


    // public function delivery_zones(){
    //     return $this->belongsTo(DeliveryZone::class,'delivery_zone_id','id');
    // }
    // public function sales_rep(){
    //     return $this->belongsTo(SalesRep::class,'sales_rep_id','id');
    // }
}
