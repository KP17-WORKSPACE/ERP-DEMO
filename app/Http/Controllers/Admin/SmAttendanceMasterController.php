<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SmAttendanceMaster;

class SmAttendanceMasterController extends Controller
{
    // List + blank form (same view you already have)
    public function index()
    {
        $records = SmAttendanceMaster::orderBy('id', 'desc')->get();
        // no $editData => create mode
        return view('backEnd.attendance_master.index', compact('records'));
    }

    // Load edit mode (same view, but with $editData)
    public function edit($id)
    {
        $editData = SmAttendanceMaster::findOrFail($id);
        $records  = SmAttendanceMaster::orderBy('id', 'desc')->get();

        // view checks isset($editData) to flip to "Update" mode
        return view('backEnd.attendance_master.index', compact('records', 'editData'));
    }

    // Create
    public function store(Request $request)
    {
        $this->validate($request, [
            'code'               => 'required|string|max:32|unique:sm_attendance_master,code',
            'name'               => 'required|string|max:100',
            'start_time'         => 'required|date_format:H:i',
            'end_time'           => 'required|date_format:H:i',
            'attendance_policy'  => 'nullable|string|max:100',
            'shift_type'         => 'nullable|string|max:20',
            'work_hours_per_day' => 'nullable|numeric|min:0|max:24',
            'grace_period'       => 'nullable|integer|min:0|max:180',
            'min_working_hours'  => 'nullable|numeric|min:0|max:24',
            'half_day_after'     => 'nullable|numeric|min:0|max:24',
            'absent_below_hours' => 'nullable|numeric|min:0|max:24',
            'late_mark_allowed'  => 'nullable|integer|min:0',
            'late_mark_halfday'  => 'nullable|integer|min:0',
            'auto_absent_after'  => 'nullable|integer|min:0',
            'break_minutes'      => 'nullable|integer|min:0',
            'description'        => 'nullable|string|max:255',
            'is_active'          => 'nullable|in:0,1',
        ]);

        $isActive = $request->has('is_active') ? 1 : 0;

        SmAttendanceMaster::create([
            'code'               => $request->get('code'),
            'name'               => $request->get('name'),
            'attendance_policy'  => $request->get('attendance_policy'),
            'shift_type'         => $request->get('shift_type'),
            'start_time'         => $request->get('start_time'),
            'end_time'           => $request->get('end_time'),
            'work_hours_per_day' => $request->get('work_hours_per_day'),
            'grace_period'       => $request->get('grace_period'),
            'min_working_hours'  => $request->get('min_working_hours'),
            'half_day_after'     => $request->get('half_day_after'),
            'absent_below_hours' => $request->get('absent_below_hours'),
            'late_mark_allowed'  => $request->get('late_mark_allowed'),
            'late_mark_halfday'  => $request->get('late_mark_halfday'),
            'auto_absent_after'  => $request->get('auto_absent_after'),
            'break_minutes'      => $request->get('break_minutes'),
            'description'        => $request->get('description'),
            'is_active'          => $isActive,
        ]);

        return redirect()->route('attendance-master.index')
            ->with('message-success', 'Attendance Master created successfully.');
    }

    // Save edit
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'code'               => 'required|string|max:32|unique:sm_attendance_master,code,' . (int)$id,
            'name'               => 'required|string|max:100',
            'start_time'         => 'required|date_format:H:i',
            'end_time'           => 'required|date_format:H:i',
            'attendance_policy'  => 'nullable|string|max:100',
            'shift_type'         => 'nullable|string|max:20',
            'work_hours_per_day' => 'nullable|numeric|min:0|max:24',
            'grace_period'       => 'nullable|integer|min:0|max:180',
            'min_working_hours'  => 'nullable|numeric|min:0|max:24',
            'half_day_after'     => 'nullable|numeric|min:0|max:24',
            'absent_below_hours' => 'nullable|numeric|min:0|max:24',
            'late_mark_allowed'  => 'nullable|integer|min:0',
            'late_mark_halfday'  => 'nullable|integer|min:0',
            'auto_absent_after'  => 'nullable|integer|min:0',
            'break_minutes'      => 'nullable|integer|min:0',
            'description'        => 'nullable|string|max:255',
            'is_active'          => 'nullable|in:0,1',
        ]);

        $row = SmAttendanceMaster::findOrFail($id);
        $row->code               = $request->get('code');
        $row->name               = $request->get('name');
        $row->attendance_policy  = $request->get('attendance_policy');
        $row->shift_type         = $request->get('shift_type');
        $row->start_time         = $request->get('start_time');
        $row->end_time           = $request->get('end_time');
        $row->work_hours_per_day = $request->get('work_hours_per_day');
        $row->grace_period       = $request->get('grace_period');
        $row->min_working_hours  = $request->get('min_working_hours');
        $row->half_day_after     = $request->get('half_day_after');
        $row->absent_below_hours = $request->get('absent_below_hours');
        $row->late_mark_allowed  = $request->get('late_mark_allowed');
        $row->late_mark_halfday  = $request->get('late_mark_halfday');
        $row->auto_absent_after  = $request->get('auto_absent_after');
        $row->break_minutes      = $request->get('break_minutes');
        $row->description        = $request->get('description');
        $row->is_active          = $request->has('is_active') ? 1 : 0; // checkbox handling
        $row->save();

        return redirect()->route('attendance-master.index')
            ->with('message-success', 'Attendance Master updated successfully.');
    }
}
