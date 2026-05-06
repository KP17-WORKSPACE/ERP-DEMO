<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\EmployeeOnboarding;
use App\EmployeeOnboardingBankDetail;
use App\EmployeeOnboardingEducation;
use App\EmployeeOnboardingExperience;
use App\SysCountries;
use App\SysStates;
use App\SmBaseSetup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\OnboardingEmployeeDocument;
use App\SmStaff;
use App\SmStaffBankDetail;
use App\SmStaffEducationQualification;
use App\SmStaffProfessionalExperience;
use App\SmStaffDocument;
use App\SysHelper;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class SmReligionController extends Controller
{


    public function storeReligionAjax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:sm_base_setups,base_setup_name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $base_setup = new SmBaseSetup();
            $base_setup->base_setup_name = $request->title;
            $base_setup->base_group_id = 2; // Religion group
            $base_setup->save();

            return response()->json([
                'status' => true,
                'message' => 'Religion added successfully',
                'data' => [
                    'id' => $base_setup->id,
                    'name' => $base_setup->base_setup_name
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error adding religion: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to add religion'
            ], 500);
        }
    }


}

