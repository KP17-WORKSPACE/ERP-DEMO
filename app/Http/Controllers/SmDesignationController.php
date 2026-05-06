<?php



namespace App\Http\Controllers;



use App\ApiBaseMethod;

use App\SmDesignation;
use App\SmHumanDepartment;
use Illuminate\Http\Request;

use Brian2694\Toastr\Facades\Toastr;

use Illuminate\Support\Facades\Validator;



class SmDesignationController extends Controller
{



    public function __construct()
    {

        $this->middleware('PM');

    }



    public function index(Request $request)
    {
        try {

            $designations = SmDesignation::with('department')
                ->where('active_status', 1)
                ->get();

            $departments = SmHumanDepartment::where('active_status', 1)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($designations, null);
            }

            return view('backEnd.humanResource.designation', compact('designations', 'departments'));

        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }



    public function store(Request $request)
    {
        // VALIDATION
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'department_id' => 'required|integer|exists:sm_human_departments,id',
        ]);

        if ($validator->fails()) {

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // CREATE NEW
            $designation = new SmDesignation();
            $designation->title = $request->title;
            $designation->department_id = $request->department_id;
            $designation->grade = $request->grade;
            $designation->active_status = 1;
            $designation->save();

            // API RESPONSE
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse(null, 'Designation added successfully.');
            }

            // WEB RESPONSE
            Toastr::success('Designation added successfully!', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Operation Failed', ['error' => $e->getMessage()]);
            }

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }




    public function show(Request $request, $id)
    {

        try {

            $designation = SmDesignation::find($id);

            $designations = SmDesignation::all();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {

                $data = [];

                $data['designation'] = $designation->toArray();

                $data['designations'] = $designations->toArray();

                return ApiBaseMethod::sendResponse($data, null);

            }

            return view('backEnd.humanResource.designation', compact('designation', 'designations'));

        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();

        }

    }



    public function update(Request $request, $id)
    {
        // VALIDATION
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'department_id' => 'required|integer|exists:sm_human_departments,id',
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {

            // FIND DESIGNATION
            $designation = SmDesignation::findOrFail($id);

            // UPDATE FIELDS
            $designation->title = $request->title;
            $designation->department_id = $request->department_id;
            $designation->grade = $request->grade;

            $designation->save();

            // API RESPONSE
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse(null, 'Designation updated successfully.');
            }

            // WEB RESPONSE
            Toastr::success('Designation updated successfully!', 'Success');
            return redirect()->back();

        } catch (\Exception $e) {

            // API ERROR RESPONSE
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Operation Failed', ['error' => $e->getMessage()]);
            }

            // WEB ERROR RESPONSE
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }




    public function destroy(Request $request, $id)
    {

        try {

            $designation = SmDesignation::destroy($id);

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {

                if ($designation) {

                    return ApiBaseMethod::sendResponse(null, 'Designation has been deleted successfully');

                } else {

                    return ApiBaseMethod::sendError('Something went wrong, please try again.');

                }

            } else {

                if ($designation) {

                    Toastr::success('Operation successful', 'Success');

                    return redirect('designation');

                } else {

                    Toastr::error('Operation Failed', 'Failed');

                    return redirect()->back();

                }

            }

        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();

        }

    }


    public function getByDepartment(Request $request, $department_id)
    {
        try {
            $designations = SmDesignation::where('department_id', $department_id)
                ->where('active_status', 1)
                ->select('id', 'title')
                ->get();

            return response()->json([
                'success' => true,
                'designations' => $designations
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching designations'
            ], 500);
        }
    }

    /**
     * Return grade for a designation (AJAX)
     * GET: /designation/{id}/grade
     */
    public function getGrade($id)
    {
        try {
            $designation = SmDesignation::find($id);
            if (!$designation) {
                return response()->json(['status' => false, 'message' => 'Designation not found'], 404);
            }

            return response()->json(['status' => true, 'grade' => $designation->grade], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Something went wrong'], 500);
        }
    }

    public function storeDesignationAjax(Request $request)
    {

        try {

            $designation = new SmDesignation();
            $designation->title = $request->title;
            $designation->department_id = $request->department_id;
            $designation->active_status = 1;
            $designation->grade = 'g6';
            $result = $designation->save();
            if ($result) {
                return response()->json([
                    'status' => true,
                    'message' => 'Designation has been added successfully',
                    'data' => $designation
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong'
                ], 500);
            }


        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    public function updateGrade(Request $request)
    {
        //         try {
//             $path = public_path('grade_update.xlsx');

        //             if (!file_exists($path)) {
//                 echo "File not found at $path";
//                 return;
//             }

        //             $data = \Excel::load($path, function ($reader) {
//             })->get();

        //             if (!$data || $data->count() == 0) {
//                 echo "No rows found in the Excel file.";
//                 return;
//             }

        //             $sr = 1;

        //             foreach ($data as $row) {

        //                 // ✅ Extract values directly
//                 $department = $row->department ?? '';
//                 $designation = $row->designation ?? '';
//                 $grade = $row->grade ?? '';

        //                 $normalizedDepartment = strtolower(str_replace(' ', '', $department));

        //                 $db_department = SmHumanDepartment::whereRaw(
//                     "REPLACE(LOWER(name), ' ', '') = ?",
//                     [$normalizedDepartment]
//                 )->first();

        //                 if(!$db_department){
//                     echo "Department not found for row $sr: $department\n";
//                     $sr++;
//                     continue;
//                 }


        //                 $normalizedDesignation = strtolower(str_replace(' ', '', $designation));

        //                 $db_designation = SmDesignation::whereRaw(
//                     "REPLACE(LOWER(title), ' ', '') = ?",
//                     [strtolower(str_replace(' ', '', $normalizedDesignation))]
//                 )->where('department_id', $db_department->id)->first();

        //                 $normalizedGrade = strtolower(str_replace(' ', '', $grade));

        //                 if(!$db_designation){
//                     echo "Designation not found for row $sr: $designation in Department ID {$db_department->id}\n";
//                     $sr++;
//                     continue;
//                 }


        //              if ($normalizedGrade == 'grade1') {
//                 $db_designation->grade = 'g1';
//                 $db_designation->save();

        // } else if ($normalizedGrade == 'grade2') {
//                 $db_designation->grade = 'g2';
//                 $db_designation->save();

        // } else if ($normalizedGrade == 'grade3') {
//                 $db_designation->grade = 'g3';
//                 $db_designation->save();

        // } else if ($normalizedGrade == 'grade4') {
//                 $db_designation->grade = 'g4';
//                 $db_designation->save();

        // } else if ($normalizedGrade == 'grade5') {
//                 $db_designation->grade = 'g5';
//                 $db_designation->save();

        // } else if ($normalizedGrade == 'grade6') {
//                 $db_designation->grade = 'g6';
//                 $db_designation->save();
// }





        //                 // 🔥 Example DB update (optional)
//                 /*
//                 DB::table('sm_designations')
//                     ->where('designation_name', $designation)
//                     ->update(['grade' => $grade]);
//                 */

        //                 $sr++;
//             }

        //             return 1;

        //         } catch (\Exception $e) {
//             dd($e);
//             echo 'Error reading file: ' . $e->getMessage();
//             return;
//         }


        try {
            $path = public_path('create_and_update_designation.xlsx');

            if (!file_exists($path)) {
                echo "File not found at $path";
                return;
            }

            $data = \Excel::load($path, function ($reader) {
            })->get();

            if (!$data || $data->count() == 0) {
                echo "No rows found in the Excel file.";
                return;
            }

            $sr = 1;

            foreach ($data as $row) {

                // ✅ Extract values directly
                $department = $row->department ?? '';
                $designation = $row->designation ?? '';
                $grade = $row->grade ?? '';

                // echo "Processing row $sr: Department='$department', Designation='$designation', Grade='$grade'\n<br>";

                $normalizedDepartment = strtolower(str_replace(' ', '', $department));



                $db_department = SmHumanDepartment::whereRaw(
                    "REPLACE(LOWER(name), ' ', '') = ?",
                    [$normalizedDepartment]
                )->first();


                if (!$db_department) {
                    echo "Department not found for row $sr: $department\n<br>";
                    $sr++;
                    continue;
                }



                // echo "Found Department Name: {$db_department->name} for row $sr\n<br>";

                $normalizedDesignation = strtolower(str_replace(' ', '', $designation));

                $db_designation = SmDesignation::whereRaw(
                    "REPLACE(LOWER(title), ' ', '') = ?",
                    [strtolower(str_replace(' ', '', $normalizedDesignation))]
                )->where('department_id', $db_department->id)->first();


                $normalizedGrade = strtolower(str_replace(' ', '', $grade));

                if ($db_designation) {
                    echo "Designation not found for row $sr: $designation in Department ID {$db_department->id}\n";
                    $sr++;
                    continue;
                }







                $db_grade = '';

                if ($normalizedGrade == 'grade1') {
                    $db_grade = 'g1';


                } else if ($normalizedGrade == 'grade2') {
                    $db_grade = 'g2';


                } else if ($normalizedGrade == 'grade3') {
                    $db_grade = 'g3';


                } else if ($normalizedGrade == 'grade4') {
                    $db_grade = 'g4';


                } else if ($normalizedGrade == 'grade5') {
                    $db_grade = 'g5';


                } else if ($normalizedGrade == 'grade6') {
                    $db_grade = 'g6';

                }

                echo "adding designaton" . $designation . "with grade " . $db_grade . "\n<br>";


                $add_designation = new SmDesignation();
                $add_designation->title = $designation;
                $add_designation->department_id = $db_department->id;
                $add_designation->grade = $db_grade;
                $add_designation->active_status = 1;
                $add_designation->save();


                $sr++;
            }

            return 1;

        } catch (\Exception $e) {
            dd($e);
            echo 'Error reading file: ' . $e->getMessage();
            return;
        }
    }



}

