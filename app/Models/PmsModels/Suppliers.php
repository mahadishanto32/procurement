<?php

namespace App\Models\PmsModels;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suppliers extends Model
{
    //use HasFactory;

    CONST ACTIVE='Active';
    CONST INACTIVE='Inactive';

    protected $fillable = [
        'name', 'email', 'phone', 'mobile_no', 'tin', 'trade', 'bin', 'vat', 'website', 'owner_name', 'owner_nid', 'owner_photo', 'owner_email', 'owner_contact_no', 'auth_person_letter', 'term_condition', 'status', 'created_by', 'updated_by'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'products_supplier', 'supplier_id', 'product_id');
    }

    public function SupplierRatings()
    {
        return $this->hasMany(SupplierRatings::class, 'supplier_id', 'id');
    }

    
    public function relPayments()
    {
        return $this->hasMany(SupplierPayment::class, 'supplier_id','id');
    }

    public function relQuotations()
    {
        return $this->hasMany(Quotations::class, 'supplier_id','id');
    }
    public function relPaymentTerms()
    {
        return $this->hasMany(SupplierPaymentTerm::class, 'supplier_id','id');
    }

    public function addresses()
    {
        return $this->hasMany(SupplierAddress::class);
    }

    public function bankAccount()
    {
        return $this->hasOne(SupplierBankAccount::class);
    }

    public function contactPersons()
    {
        return $this->hasMany(SupplierContactPerson::class);
    }

    public function logs(){
        return $this->hasMany(SupplierLog::class);
    }

   
    public static function boot(){
        parent::boot();
        static::creating(function($query){
            if(\Auth::check()){
                $query->created_by = @\Auth::user()->id;
            }
        });
        static::updating(function($query){
            if(\Auth::check()){
                $query->updated_by = @\Auth::user()->id;
            }
        });
    }
}
