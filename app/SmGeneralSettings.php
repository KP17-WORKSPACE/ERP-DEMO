<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SmActivity;
use Auth;

class SmGeneralSettings extends Model
{
    public function sessions()
    {
        return $this->belongsTo('App\SmSession', 'session_id', 'id');
    }

    public function languages()
    {
        return $this->belongsTo('App\SmLanguage', 'language_id', 'id');
    }

    public function dateFormats()
    {
        return $this->belongsTo('App\SmDateFormat', 'date_format_id', 'id');
    }
    public static function getLanguageList()
    {
        $languages = SmLanguage::all();
        return $languages;
    }
    public function timeZone()
    {
        return $this->belongsTo('App\SmTimeZone', 'time_zone_id', 'id');
    }
    public static function value()
    {
        $value = SmGeneralSettings::first();
        return $value->system_purchase_code;
    }


    public static function StoreAllActivities($data)
    {

        $s = new SmActivity();
        $s->note            =   $data['note'];
        $s->model_name      =   $data['model_name'];
        $s->old_data        =   $data['old_data'];
        $s->new_data        =   $data['new_data'];
        $s->action          =   $data['action'];
        $s->action_id       =   $data['action_id'];
        $s->author_id       =   Auth::user()->id;
        $s->author_mode     =   'users';
        $r = $s->save();
        return $r;
    }
}
