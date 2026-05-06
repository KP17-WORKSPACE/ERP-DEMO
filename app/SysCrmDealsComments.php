<?php



namespace App;



use Illuminate\Database\Eloquent\Model;



class SysCrmDealsComments extends Model
{

    protected $table = 'sys_crm_deals_comments';

    protected $primaryKey = 'id';



    protected $fillable = [

        'id',
        'deal_id',
        'comments',
        'commentsdoc',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_by',
        'deleted_at'

    ];



    public function createdby()
    {

        return $this->belongsTo('App\SmStaff', 'created_by', 'user_id');

    }



    public function deletedby()
    {
        return $this->belongsTo('App\SmStaff', 'deleted_by', 'user_id');
    }

    public function softDelete($userId)
    {
        $this->deleted_by = $userId;
        $this->deleted_at = now();
        return $this->save();
    }

    /** ✅ Restore */
    public function restoreComment()
    {
        $this->deleted_by = null;
        $this->deleted_at = null;
        return $this->save();


    }

}