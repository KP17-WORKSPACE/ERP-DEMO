<?php



namespace App\Http\Controllers;



use App\ApiBaseMethod;

use App\SmHumanDepartment;

use Illuminate\Http\Request;

use Brian2694\Toastr\Facades\Toastr;

use Illuminate\Support\Facades\Validator;



class SmHumanDepartmentController extends Controller

{

    public function __construct()

    {

        $this->middleware('PM');

    }



    public function index(Request $request)

    {

        try{

            $departments = SmHumanDepartment::all();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {

                return ApiBaseMethod::sendResponse($departments, null);

            }

            return view('backEnd.humanResource.human_resource_department', compact('departments'));

        }catch (\Exception $e) {

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }

    public function update(Request $request, $id)
{
    try {

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $department = SmHumanDepartment::findOrFail($id);
        $department->name = $request->name;
        $department->save();

        Toastr::success('Department updated successfully!', 'Success');
        return redirect()->back();

    } catch (\Exception $e) {

        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back();
    }
}





    public function store(Request $request)

    {

        $input = $request->all();

        $validator = Validator::make($input, [

            'name' => "required"

        ]);



        if ($validator->fails()) {

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {

                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());

            }

            return redirect()->back()

                ->withErrors($validator)

                ->withInput();

        }

        try{

            $department = new SmHumanDepartment();

            $department->name = $request->name;

            $result = $department->save();

    

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {

                if ($result) {

                    return ApiBaseMethod::sendResponse(null, 'Department has been created successfully');

                } else {

                    return ApiBaseMethod::sendError('Something went wrong, please try again.');

                }

            } else {

                if ($result) {

                    return redirect()->back()->with('message-success', 'Department has been created successfully');

                } else {

                    return redirect()->back()->with('message-danger', 'Something went wrong, please try again');

                }

            }

        }catch (\Exception $e) {

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }



    public function show(Request $request, $id)

    {

        try{

            $department = SmHumanDepartment::find($id);

            $departments = SmHumanDepartment::all();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {

                $data = [];

                $data['department'] = $department->toArray();

                $data['departments'] = $departments->toArray();

                return ApiBaseMethod::sendResponse($data, null);

            }

            return view('backEnd.humanResource.human_resource_department', compact('department', 'departments'));

        }catch (\Exception $e) {

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }






    public function destroy(Request $request, $id)

    {

        try{

            $department = SmHumanDepartment::destroy($id);

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {

                if ($department) {

                    return ApiBaseMethod::sendResponse(null, 'Department has been deleted successfully');

                } else {

                    return ApiBaseMethod::sendError('Something went wrong, please try again.');

                }

            } else {

                if ($department) {

                    return redirect('department')->with('message-success-delete', 'Department has been deleted successfully');

                } else {

                    return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');

                }

            }

        }catch (\Exception $e) {

           Toastr::error('Operation Failed', 'Failed');

           return redirect()->back(); 

        }

    }

    
    public function storeDepartmentAjax(Request $request)
    {

        try {

            $department = new SmHumanDepartment();
            $department->name = $request->title;
            $result = $department->save();  
            
            if ($result) {
                return response()->json([
                    'status' => true,
                    'message' => 'Department has been added successfully',
                    'data' => $department
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

}

