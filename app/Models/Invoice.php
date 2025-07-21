<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function payment(){
        return $this->belongsTo(Payment::class,'id','invoice_id'); // Firstly ID is from Invoice Model, Invoice_id is from Payment 
    }

    public function invoice_details(){
        return $this->hasMany(InvoiceDetail::class,'invoice_id','id'); // VVI Note: hasMany Relation is a difference with above belongsTo, firstly Invoice_Id from InvoiceDetail Model and ID is from Invoice Model
    }

    public function delivery_zones(){
        return $this->belongsTo(DeliveryZone::class,'delivery_zone_id','id');
    }
    public function sales_rep(){
        return $this->belongsTo(SalesRep::class,'sales_rep_id','id');
    }
}
