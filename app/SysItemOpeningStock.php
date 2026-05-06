<?php



namespace App;



use Illuminate\Database\Eloquent\Model;



class SysItemOpeningStock extends Model

{

    protected $table = 'sys_item_opening_stock';

    protected $primaryKey = 'id';



    protected $fillable = [

        'id', 'doc_number','doc_date','bill_date','currency','narration', 'status', 'created_by','created_at','updated_by','updated_at','company_id'

    ];


    public function items()
    {
        return $this->hasMany(SysItemStock::class, 'ops_id', 'id');
    }

    public function part_no(){

      return $this->belongsTo('App\SmItem', 'partno', 'id');

    }



    public function createdby(){

		   return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');

     }

    // public function suppliername(){

		//   return $this->belongsTo('App\SmSupplier', 'vendors', 'id');

    // }

    // public function suppliertype(){

		//   return $this->belongsTo('App\SysSupplierType', 'supplier_type', 'id');

    // }

    // public function paymentterms(){

		//   return $this->belongsTo('App\SysPaymentTerms', 'payment_terms', 'id');

    // }

    

    // public function updatedby(){

		//   return $this->belongsTo('App\SmStsffs', 'id', 'updated_by');

    // }    

}