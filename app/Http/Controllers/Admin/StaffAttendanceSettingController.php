<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SmAttendancePoliciesMaster;
use App\SmShiftMaster;
use App\SmStaffAttendanceSetting;
use DB;


class StaffAttendanceSettingController extends Controller
{
    //
    public function create(Request $request)
    {
        $prefill = [
            'user_id'  => $request->get('user_id'),
            'staff_id' => $request->get('staff_id'),
        ];

        $shifts   = SmShiftMaster::where('is_active', 1)->orderBy('name', 'asc')->get();
        $policies = SmAttendancePoliciesMaster::where('is_active', 1)->orderBy('code', 'asc')->get();

        $weeklyOffOptions = [
            'sunday_all'   => 'Sunday (All)',
            'saturday_all' => 'Saturday (All)',
            '1_3_saturday' => '1 & 3 Saturday (Only 1 & 3)',
            '2_4_saturday' => '2 & 4 Saturday (Only 2 & 4)',
            'friday_all'   => 'Friday (All)',
        ];

        return view('admin.attendance_settings.create', compact('shifts', 'policies', 'prefill', 'weeklyOffOptions'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'shift_id'          => 'required|numeric',
            'policy_id'         => 'required|numeric',
            'weekly_off_days'   => 'array',
        ]);

        $data = [
            'user_id'        => $request->get('user_id'),
            'staff_id'       => $request->get('staff_id'),
            'shift_id'       => $request->get('shift_id'),
            'policy_id'      => $request->get('policy_id'),
            'weekly_off_days'=> $request->get('weekly_off_days') ? json_encode($request->get('weekly_off_days')) : json_encode([]),
        ];

        DB::table('sm_staff_attendance_settings')->insert($data);

        return redirect()->back()->with('message-success', 'Attendance setting created successfully.');
    }

    public function edit($id)
    {
        $setting = SmStaffAttendanceSetting::findOrFail($id);

        $shifts   = SmShiftMaster::where('is_active', 1)->orderBy('name', 'asc')->get();
        $policies = SmAttendancePoliciesMaster::where('is_active', 1)->orderBy('code', 'asc')->get();

        $weeklyOffOptions = [
            'sunday_all'   => 'Sunday (All)',
            'saturday_all' => 'Saturday (All)',
            '1_3_saturday' => '1 & 3 Saturday (Only 1 & 3)',
            '2_4_saturday' => '2 & 4 Saturday (Only 2 & 4)',
            'friday_all'   => 'Friday (All)',
        ];

        return view('admin.attendance_settings.edit', compact('setting', 'shifts', 'policies', 'weeklyOffOptions'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'shift_id'          => 'required|numeric',
            'policy_id'         => 'required|numeric',
            'weekly_off_days'   => 'array',
        ]);

        $setting = SmStaffAttendanceSetting::findOrFail($id);

        $setting->shift_id        = $request->get('shift_id');
        $setting->policy_id       = $request->get('policy_id');
        $setting->weekly_off_days = $request->get('weekly_off_days') ? json_encode($request->get('weekly_off_days')) : json_encode([]);
        $setting->save();

        return redirect()->back()->with('message-success', 'Attendance setting updated successfully.');
    }

}
