<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SmShiftMaster;

class ShiftMasterController extends Controller
{
    //
    public function index()
    {
        $shifts = SmShiftMaster::orderBy('name','asc')->get();
        return view('backEnd.shift_master.index', compact('shifts'));
    }

    public function create()
    {
        return view('backEnd.shift_master.form'); // same view for create/edit
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'               => 'required|string|max:100',
            'shift_type'         => 'nullable|string|max:20',   // Fixed/Rotational/Custom
            'start_time'         => 'required|date_format:H:i',
            'end_time'           => 'required|date_format:H:i',
            'work_hours_per_day' => 'nullable|numeric|min:0|max:24',
            'grace_period'       => 'nullable|integer|min:0|max:180',
            'is_active'          => 'nullable|in:0,1',
        ]);

        SmShiftMaster::create([
            'name'               => $request->get('name'),
            'shift_type'         => $request->get('shift_type'),
            'start_time'         => $request->get('start_time'),
            'end_time'           => $request->get('end_time'),
            'work_hours_per_day' => $request->get('work_hours_per_day'),
            'grace_period'       => $request->get('grace_period', 0),
            'is_active'          => $request->has('is_active') ? (int)$request->get('is_active') : 1,
        ]);

        return redirect()->route('shift.index')->with('message-success','Shift created.');
    }

    public function edit($id)
    {
        $editData = SmShiftMaster::findOrFail($id);
        $shifts   = SmShiftMaster::orderBy('name','asc')->get();
        return view('admin.shift_master.form', compact('editData','shifts'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'               => 'required|string|max:100',
            'shift_type'         => 'nullable|string|max:20',
            'start_time'         => 'required|date_format:H:i',
            'end_time'           => 'required|date_format:H:i',
            'work_hours_per_day' => 'nullable|numeric|min:0|max:24',
            'grace_period'       => 'nullable|integer|min:0|max:180',
            'is_active'          => 'nullable|in:0,1',
        ]);

        $shift = SmShiftMaster::findOrFail($id);
        $shift->name               = $request->get('name');
        $shift->shift_type         = $request->get('shift_type');
        $shift->start_time         = $request->get('start_time');
        $shift->end_time           = $request->get('end_time');
        $shift->work_hours_per_day = $request->get('work_hours_per_day');
        $shift->grace_period       = $request->get('grace_period', 0);
        $shift->is_active          = $request->has('is_active') ? (int)$request->get('is_active') : 1;
        $shift->save();

        return redirect()->route('shift.index')->with('message-success','Shift updated.');
    }

}
