<?php

namespace App;

use App\Role;
use App\SmCompititor;
use App\SmGeneralSettings;
use App\SmInspectingDepartment;
use App\SmStaff;
use App\SmSupplier;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    public static $item = "23876323"; //23876323 //22014245

/**
 * The attributes that are mass assignable.
 *
 * @var array
 */
    protected $fillable = [
        'name', 'email', 'username', 'phone', 'password','role_id','full_name','company_id'
    ];

/**
 * The attributes that should be hidden for arrays.
 *
 * @var array
 */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function student()
    {
        return $this->belongsTo('App\SmStudent', 'id', 'user_id');
    }
    public function staff()
    {
        return $this->belongsTo('App\SmStaff', 'id', 'user_id');
    }

    public static function getId()
    {
        return 1;
    }

    public static function NumberToBangladeshiTakaFormat($amount = 0)
    {

        $tmp      = explode(".", $amount); // for float or double values
        $strMoney = "";
        $amount   = $tmp[0];
        $strMoney .= substr($amount, -3, 3);
        $amount = substr($amount, 0, -3);
        while (strlen($amount) > 0) {
            $strMoney = substr($amount, -2, 2) . "," . $strMoney;
            $amount   = substr($amount, 0, -2);
        }

        if (isset($tmp[1])) // if float and double add the decimal digits here.
        {
            return $strMoney . "." . $tmp[1];
        } else {
            return $strMoney . '.00';
        }

    }

    public static function GetLowestBidder($id = null)
    {

        $Symbol = SmGeneralSettings::select('currency_symbol')->find(1);

        $LowestBidder = SmCompititor::where('tender_id', $id)->orderBy('company_bid_amount', 'ASC')->first();
        if ($LowestBidder != "") {
            $name = '<span class="text-capitalize">' . $LowestBidder->company_name . '<br>'.' (<span class="letterspecing"> ' . $Symbol->currency_symbol . ' ' . User::NumberToBangladeshiTakaFormat($LowestBidder->company_bid_amount) . '</span> )</span>';
        } else {
            $name = "<span class='d-block' title='Competitor Not Yet Set'  data-toggle='tooltip' data-placement='top' >?</span>";
        }
        return $name;
    }


    public static function GetLowestBidderPrintView($id = null)
    {
 

        $LowestBidder = SmCompititor::where('tender_id', $id)->orderBy('company_bid_amount', 'ASC')->first();
        if ($LowestBidder != "") {
            $name = $LowestBidder->company_name . ' ( ' .   User::NumberToBangladeshiTakaFormat($LowestBidder->company_bid_amount) . ' ) ';
        } else {
            $name = "-";
        }
        return $name;
    }

    public static function GetLowestBidderCompany($id = null)
    {

        $Symbol = SmGeneralSettings::select('currency_symbol')->find(1);

        $LowestBidder = SmCompititor::where('tender_id', $id)->orderBy('company_bid_amount', 'ASC')->first();
        if ($LowestBidder != "") {
            $name = '<span class="text-capitalize">' . $LowestBidder->company_name . '</span>';
        } else {
            $name = "<span title='Competitor Not Yet Set'  data-toggle='tooltip' data-placement='top' >?</span>";
        }
        return $name;
    }

    public static function GetLowestBidderAmount($id = null)
    {

        $Symbol = SmGeneralSettings::select('currency_symbol')->find(1);

        $LowestBidder = SmCompititor::where('tender_id', $id)->orderBy('company_bid_amount', 'ASC')->first();
        if ($LowestBidder != "") {
            $name = '<span class="letterspecing"> ' . $Symbol->currency_symbol . ' ' . User::NumberToBangladeshiTakaFormat($LowestBidder->company_bid_amount) . '</span>';
        } else {
            $name = "<span title='Competitor Not Yet Set'  data-toggle='tooltip' data-placement='top' >?</span>";
        }
        return $name;
    }

    public static function Get_IsSetCompetitor($id = null)
    {
        $LowestBidder = SmCompititor::where('tender_id', $id)->orderBy('company_bid_amount', 'ASC')->first();
        if ($LowestBidder != "") {
            return true;
        } else {
            return false;
        }
    }

    public static function getAllCompetitors($id = null)
    {
        $LowestBidder = [];
        $LowestBidder = SmCompititor::where('tender_id', $id)->orderBy('company_bid_amount', 'ASC')->get();
        return $LowestBidder;
    }

    public static function GetStaffname($id = null)
    {

        $user_data = SmStaff::find($id);
        if ($user_data != "") {
            $name = $user_data->full_name;
        } else {
            $name = "-";
        }
        return $name;
    }

    public static function getFileName($path = null)
    {
        //upcoming tender
        if ($path == null) {
            return '';
        }

        $name = explode('/', $path);
        return $name[3];
    }

    public static function getUserDetails($id = null)
    {
        if ($id != "") {
            $user_data = User::find($id);
            $role_id   = !empty($user_data) ? $user_data->role_id : '1';
            $full_name = !empty($user_data) ? $user_data->full_name : '-';
            $roles     = Role::find($role_id);
            $role_name = !empty($roles) ? $roles->name : 'Designation';

            // return  $full_name.' ['.$role_name.']';
            return $full_name;
        } else {
            return '-';
        }
    }

    public static function getUserDesignation($id = null)
    {
        if ($id != "") {
            $user_data = User::find($id);
            $role_id   = !empty($user_data) ? $user_data->role_id : '1';
            $full_name = !empty($user_data) ? $user_data->full_name : '-';
            $roles     = Role::find($role_id);
            $role_name = !empty($roles) ? $roles->name : 'Designation';

            // return  $full_name.' ['.$role_name.']';
            return $role_name;
        } else {
            return '-';
        }
    }

    public static function getVendorName($id = null)
    {
        $vendor = SmSupplier::find($id);
        return $vendor->company_name;
    }
    public static function getVendorAddress($id = null)
    {
        $vendor = SmSupplier::find($id);
        return $vendor->company_address;
    }
    public static function getInspectDepartmentName($id = null)
    {
        $InspectDepartment = SmInspectingDepartment::find($id);
        return $InspectDepartment->department_name;
    }

    public function comments()
    {
        return $this->hasMany(InfixComment::class);
    }
    public function tickets()
    {
        return $this->hasMany(InfixTicket::class);
    }

}
