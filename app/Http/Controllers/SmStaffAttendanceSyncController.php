<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SmStaffAttendanceSyncController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public static function connectAndFetch()
    {
        try {
            db::beginTransaction();
            // Config::set('database.connections.temp_connection', [
            //     'driver'    => 'mysql',
            //     'host'      => '127.0.0.1',
            //     'port'      => '3306',
            //     'database'  => 'db_employee_portal',
            //     'username'  => 'root',
            //     'password'  => 'mysql',
            //     'charset'   => 'utf8',
            //     'collation' => 'utf8_unicode_ci',
            //     'prefix'    => '',
            //     'strict'    => false,
            // ]);
            Config::set('database.connections.temp_connection', [
                'driver'    => 'mysql',
                'host'      => 'localhost',
                'port'      => '3306',
                'database'  => 'venushrms_db_employee_portal',
                'username'  => 'venushrms_erp_sync',
                'password'  => '3meI7oj]Oz2%*;e',
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
                'strict'    => false,
            ]);
        
            DB::purge('temp_connection');

            $last = DB::table('sm_staff_attendences_last_updated')->orderby('id','desc')->first();
            if ($last) {
                $last_updated_id = $last->last_updated_id;
            } else {
                $last_updated_id = 0;
            }
            $data = DB::connection('temp_connection')
            ->table('employee_attendance')
            ->select(
                'employee_attendance_id',
                'finger_print_id',
                'type_id',
                DB::raw('DATE(in_out_time) as attendance_date'),
                DB::raw('MIN(in_out_time) as in_time'),
                DB::raw("
                    CASE 
                        WHEN COUNT(*) > 1 
                        THEN MAX(in_out_time) 
                        ELSE NULL 
                    END as out_time
                ")
            )
            ->where('employee_attendance_id', '>', $last_updated_id)
            ->groupBy(
                'finger_print_id',
                DB::raw('DATE(in_out_time)')
            )
            ->orderBy('attendance_date')
            ->get();
            if(count($data)>0){
                foreach ($data as $res) {
                    $insert_data[] = [
                        'finger_print_id' => $res->finger_print_id,
                        'attendence_date' => Carbon::parse($res->attendance_date)->format('Y-m-d'),
                        'in_time'         => Carbon::parse($res->in_time)->format('H:i'),
                        'out_time'        => $res->out_time 
                                                ? Carbon::parse($res->out_time)->format('H:i') 
                                                : null,
                        'attendence_type' => 'P',
                        'staff_id' => 0,
                        'type_id' => $res->type_id
                    ];
                }
                
                DB::table('sm_staff_attendences')->insert($insert_data);
                DB::table('sm_staff_attendences_last_updated')->update([
                    'last_updated_id' => $data->max('employee_attendance_id'),
                    'last_updated_date' => Carbon::now('+04:00')->format('Y-m-d H:i:s'),
                ]);
            
            }
            db::commit();
            return "Success";

        } catch (\Throwable $th) {            
            db::rollBack();
            return $th;
        }
    }
}