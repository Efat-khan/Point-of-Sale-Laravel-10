<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function quotationDescriptions(){
        return $this->hasMany(QuotationDescription::class,'quotation_id','id'); // VVI Note: hasMany Relation is a difference with above belongsTo, firstly Invoice_Id from QuotationDescription Model and ID is from Quotation Model
    }
}
