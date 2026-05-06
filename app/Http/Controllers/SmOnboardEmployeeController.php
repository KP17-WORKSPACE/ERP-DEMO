<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\EmployeeOnboarding;
use App\EmployeeOnboardingBankDetail;
use App\EmployeeOnboardingEducation;
use App\EmployeeOnboardingExperience;
use App\SysCountries;
use App\SysStates;
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
use App\SysCurrencySettings;
use App\SmBaseSetup;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\SmStaffJobDetail;
use App\OnboardEmployeeJobDetail;
class SmOnboardEmployeeController extends Controller
{

    public function onboardEmployee(Request $request, $id)
    {
        $countries = SysCountries::all();
        $states = SysStates::all();
        $company_id = $id;
        $religions = SmBaseSetup::where('base_group_id', 2)->get();
        $currencies = SysCurrencySettings::select('id', 'code')->where('status', 1)->orderBy('code', 'ASC')->get();

        return view('backEnd.humanResource.onboard_employee', compact('countries', 'states', 'company_id', 'religions', 'currencies'));
    }

    public function onboardingEmployeeList(Request $request, $id = null)
    {
        $active_id = $id;
        $selectedEmployee = [];

        $company_id = session('logged_session_data.company_id');

        // Order so that pending (not yet approved) employees appear first, then most recent
        if ($company_id == 1) {
            $employees = EmployeeOnboarding::orderByRaw("CASE WHEN approved_by IS NULL THEN 0 ELSE 1 END")
                ->orderByDesc('created_at')
                ->get();
        } else {
            $employees = EmployeeOnboarding::where('company_id', $company_id)
                ->orderByRaw("CASE WHEN approved_by IS NULL THEN 0 ELSE 1 END")
                ->orderByDesc('created_at')
                ->get();
        }


        if ($id) {
            $selectedEmployee = $this->onboardingEmployeeDetails($id);
        } else {
            $selectedEmployee = $employees->first();
        }


        return view('backEnd.humanResource.onboarding-employee.index', compact('employees', 'active_id', 'selectedEmployee'));
    }

    public function onboardingEmployeeDetails($id)
    {
        try {
            $employee = EmployeeOnboarding::with(['documents', 'bankDetails', 'educations', 'experiences'])->find($id);
            if (!$employee) {
                return response()->json(['error' => 'Employee not found'], 404);
            }

            return $employee;
        } catch (\Exception $e) {
            Log::error('Error fetching employee details: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'An error occurred while fetching employee details'], 500);
        }
    }


    public function onboardingEmployeeDetailsView($id)
    {
        try {
            $employee = EmployeeOnboarding::with(['documents', 'bankDetails', 'educations', 'experiences'])->find($id);


            return view('backEnd.humanResource.onboarding-employee.view', compact('employee'));
        } catch (\Exception $e) {
            Log::error('Error fetching employee details: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'An error occurred while fetching employee details'], 500);
        }
    }



    public function saveOnboardEmployee(Request $request)
    {


        // Validation rules (Laravel 5 compatible via Validator facade)
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:25',
            'password' => 'nullable|string|min:8|regex:/[0-9]/',
            'staff_photo' => 'nullable|image|max:5000',
            'father_attachment' => 'nullable|file|mimes:pdf,jpeg,png,gif,webp|max:5000',
            'mother_attachment' => 'nullable|file|mimes:pdf,jpeg,png,gif,webp|max:5000',

            // addresses
            'perm_country' => 'nullable|string',
            'perm_state' => 'nullable|string',
            'perm_city' => 'nullable|string',
            'perm_area' => 'nullable|string',
            'perm_building_name' => 'nullable|string',
            'perm_flat_office_no' => 'nullable|string',
            'curr_country' => 'nullable|string',
            'curr_state' => 'nullable|string',
            'curr_city' => 'nullable|string',
            'curr_area' => 'nullable|string',
            'curr_building_name' => 'nullable|string',
            'curr_flat_office_no' => 'nullable|string',

            'banks' => 'nullable|array',
            'docs' => 'nullable|array',
            // allow single or multiple files for docs groups
            'docs.*.*.file' => 'nullable|file|mimes:pdf,jpeg,png,gif,webp|max:5000',
            'docs.*.*.file.*' => 'nullable|file|mimes:pdf,jpeg,png,gif,webp|max:5000',

            // emergency contacts
            'emergency1_name' => 'nullable|string|max:255',
            'emergency1_mobile' => 'nullable|string|max:25',
            'emergency1_email' => 'nullable|email|max:255',
            'emergency1_relationship' => 'nullable|string|max:100',
            'emergency2_name' => 'nullable|string|max:255',
            'emergency2_mobile' => 'nullable|string|max:25',
            'emergency2_email' => 'nullable|email|max:255',
            'emergency2_relationship' => 'nullable|string|max:100',
            'banks.*.bank_name' => 'required_with:banks|string|max:255',
            'banks.*.account_holder' => 'required_with:banks|string|max:255',
            'banks.*.iban_letter' => 'nullable|file|mimes:pdf,jpeg,png,gif,webp|max:5000',

            'educations' => 'nullable|array',
            'educations.*.qualification' => 'required_with:educations|string|max:255',
            'educations.*.university' => 'required_with:educations|string|max:255',
            'educations.*.year' => 'nullable|digits:4',
            'educations.*.gpa' => 'nullable|numeric',
            'educations.*.duration' => 'nullable|numeric|min:0',
            'educations.*.certificate' => 'nullable|file|mimes:pdf,jpeg,png,gif,webp|max:5000',

            'experiences' => 'nullable|array',
            'experiences.*.organization' => 'required_with:experiences|string|max:255',
            'experiences.*.years' => 'nullable|integer|min:0',
            'experiences.*.months' => 'nullable|integer|min:0|max:12',
            'experiences.*.certificate' => 'nullable|file|mimes:pdf,jpeg,png,gif,webp|max:5000',
        ];

        // $validator = Validator::make($request->all(), $rules);
        // if ($validator->fails()) {
        //     // flash first validation error with Toastr so user sees it immediately
        //     dd($validator->errors());
        //     $first = $validator->errors()->first();
        //     Toastr::error($first ?: 'Validation failed. Please check the inputs.');
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }

        DB::beginTransaction();
        try {
            // Create staff base record
            $notes = '';
            if ($request->filled('place_of_birth')) {
                $notes .= "Place of birth: " . $request->input('place_of_birth') . "\n";
            }
            if ($request->filled('salutation')) {
                $notes .= "Salutation: " . $request->input('salutation') . "\n";
            }

            // Father / Mother names
            $fathersName = trim($request->input('father_first_name') . ' ' . $request->input('father_last_name'));
            if ($request->filled('father_mobile')) {
                $notes .= "Father mobile: " . $request->input('father_mobile') . "\n";
            }
            if ($request->filled('father_email')) {
                $notes .= "Father email: " . $request->input('father_email') . "\n";
            }

            $mothersName = trim($request->input('mother_first_name') . ' ' . $request->input('mother_last_name'));
            if ($request->filled('mother_mobile')) {
                $notes .= "Mother mobile: " . $request->input('mother_mobile') . "\n";
            }
            if ($request->filled('mother_email')) {
                $notes .= "Mother email: " . $request->input('mother_email') . "\n";
            }

            // emergency contact 2
            if ($request->filled('emergency2_name')) {
                $notes .= "Emergency 2: " . $request->input('emergency2_name') . " (" . $request->input('emergency2_mobile') . ") - " . $request->input('emergency2_relationship') . "\n";
            }

            // Compute top-level qualification & experience summaries from repeated inputs (optional)
            $qualificationSummary = null;
            $educationsInput = $request->input('educations', []);
            if (!empty($educationsInput)) {
                $quals = array_filter(array_column($educationsInput, 'qualification'));
                if (!empty($quals)) {
                    $qualificationSummary = implode(', ', $quals);
                }
            }

            $experienceSummary = null;
            $exps = $request->input('experiences', []);
            if (!empty($exps)) {
                $totalMonths = 0;
                foreach ($exps as $ee) {
                    $y = isset($ee['years']) ? intval($ee['years']) : 0;
                    $m = isset($ee['months']) ? intval($ee['months']) : 0;
                    $totalMonths += ($y * 12) + $m;
                }
                $yrs = intdiv($totalMonths, 12);
                $mths = $totalMonths % 12;
                $experienceSummary = trim($yrs . ' Y, ' . $mths . ' M');
            }

            $staffData = [
                'employee_salutation' => $request->input('salutation') ?? null,
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'full_name' => trim($request->input('first_name') . ' ' . $request->input('last_name')),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'date_of_birth' => null,
                'religion' => $request->input('religion') ?? null,
                'gender_id' => $request->input('gender_id') ?? null,
                'marital_status' => $request->input('marital_status') ?? null,
                'fathers_first_name' => $request->input('father_first_name') ?? null,
                'fathers_last_name' => $request->input('father_last_name') ?? null,
                'father_mobile' => $request->input('father_mobile') ?? null,
                'father_email' => $request->input('father_email') ?? null,
                'mothers_first_name' => $request->input('mother_first_name') ?? null,
                'mothers_last_name' => $request->input('mother_last_name') ?? null,
                'mother_mobile' => $request->input('mother_mobile') ?? null,
                'mother_email' => $request->input('mother_email') ?? null,
                'em1_salutation' => $request->input('emergency1_salutation') ?? null,
                'emergency_contact_name' => $request->input('emergency1_name') ?? null,
                'emergency_mobile' => $request->input('emergency1_mobile') ?? null,
                'emergency_email' => $request->input('emergency1_email') ?? null,
                'emergency_contact_relationship' => $request->input('emergency1_relationship') ?? null,
                'em2_salutation' => $request->input('emergency2_salutation') ?? null,
                'emergency2_contact_name' => $request->input('emergency2_name') ?? null,
                'emergency2_mobile' => $request->input('emergency2_mobile') ?? null,
                'emergency2_contact_relationship' => $request->input('emergency2_relationship') ?? null,
                'emergency2_email' => $request->input('emergency2_email') ?? null,
                'permanent_country' => $request->input('perm_country') ?? null,
                'permanent_state' => $request->input('perm_state') ?? null,
                'permanent_city' => $request->input('perm_city') ?? null,
                'permanent_area' => $request->input('perm_area') ?? null,
                'permanent_building_no' => $request->input('perm_building_name') ?? null,
                'permanent_flat_no' => $request->input('perm_flat_office_no') ?? null,
                'permanent_address' => trim(implode(', ', array_filter([$request->input('perm_building_name'), $request->input('perm_area'), $request->input('perm_city'), $request->input('perm_state')]))),
                'current_country' => $request->input('curr_country') ?? null,
                'current_state' => $request->input('curr_state') ?? null,
                'current_city' => $request->input('curr_city') ?? null,
                'current_area' => $request->input('curr_area') ?? null,
                'current_building_no' => $request->input('curr_building_name') ?? null,
                'current_flat_no' => $request->input('curr_flat_office_no') ?? null,
                'current_address' => trim(implode(', ', array_filter([$request->input('curr_building_name'), $request->input('curr_area'), $request->input('curr_city'), $request->input('curr_state')]))),
                'qualification' => $qualificationSummary,
                'experience' => $experienceSummary,
                'notes' => trim($notes),
                'created_by' => auth()->id() ?: null,
                'place_of_birth' => $request->input('place_of_birth') ?? null,
                'blood_group' => $request->input('blood_group') ?? null,
                'spouse_first_name' => $request->input('spouse_first_name') ?? null,
                'spouse_last_name' => $request->input('spouse_last_name') ?? null,
                'spouse_mobile' => $request->input('spouse_mobile') ?? null,
                'spouse_email' => $request->input('spouse_email') ?? null,
                'spouse_attachment' => null,
                'company_id' => $request->input('company_id'),
            ];



            // parse date_of_birth if provided (expecting d/m/Y)
            if ($request->filled('date_of_birth')) {
                try {
                    $dob = Carbon::createFromFormat('d/m/Y', $request->input('date_of_birth'));
                    $staffData['date_of_birth'] = $dob->format('Y-m-d');
                } catch (\Exception $ex) {
                    // ignore parse errors, validator should have caught formats if required
                    Log::warning('DOB parse failed: ' . $ex->getMessage());
                }
            }

            // staff photo (store using original filename, sanitized, avoid collisions)
            if ($request->hasFile('staff_photo')) {
                $file = $request->file('staff_photo');
                $originalName = $file->getClientOriginalName();
                $ext = $file->getClientOriginalExtension();
                $base = pathinfo($originalName, PATHINFO_FILENAME);
                $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
                $candidate = $safeBase . '.' . $ext;
                $folder = 'employee/staff_photos';
                $i = 0;
                while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                    $i++;
                    $candidate = $safeBase . '_' . $i . '.' . $ext;
                }
                $path = $file->storeAs($folder, $candidate, 'public');
                $staffData['staff_photo'] = $path;
            }

            // password (if provided)
            if ($request->filled('password')) {
                $staffData['password'] = Hash::make($request->input('password'));
            }

            $staff = EmployeeOnboarding::create($staffData);

            // Save parent attachments (father/mother) as documents if present
            if ($request->hasFile('father_attachment')) {
                $fa = $request->file('father_attachment');
                $orig = $fa->getClientOriginalName();
                $ext = $fa->getClientOriginalExtension();
                $base = pathinfo($orig, PATHINFO_FILENAME);
                $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
                $candidate = $safeBase . '.' . $ext;
                $folder = 'employee/family_attachments';
                $n = 0;
                while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                    $n++;
                    $candidate = $safeBase . '_' . $n . '.' . $ext;
                }
                $path = $fa->storeAs($folder, $candidate, 'public');

                OnboardingEmployeeDocument::create([
                    'staff_id' => $staff->id,
                    'group' => 'family',
                    'key' => 'father_attachment',
                    'name' => $orig,
                    'path' => $path,
                    'file_path' => $path,
                ]);
            }

            if ($request->hasFile('mother_attachment')) {
                $ma = $request->file('mother_attachment');
                $orig = $ma->getClientOriginalName();
                $ext = $ma->getClientOriginalExtension();
                $base = pathinfo($orig, PATHINFO_FILENAME);
                $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
                $candidate = $safeBase . '.' . $ext;
                $folder = 'employee/family_attachments';
                $n = 0;
                while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                    $n++;
                    $candidate = $safeBase . '_' . $n . '.' . $ext;
                }
                $path = $ma->storeAs($folder, $candidate, 'public');

                OnboardingEmployeeDocument::create([
                    'staff_id' => $staff->id,
                    'group' => 'family',
                    'key' => 'mother_attachment',
                    'name' => $orig,
                    'path' => $path,
                    'file_path' => $path,
                ]);
            }

            if ($request->hasFile('spouse_attachment')) {
                $sa = $request->file('spouse_attachment');
                $orig = $sa->getClientOriginalName();
                $ext = $sa->getClientOriginalExtension();
                $base = pathinfo($orig, PATHINFO_FILENAME);
                $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
                $candidate = $safeBase . '.' . $ext;
                $folder = 'employee/family_attachments';
                $n = 0;
                while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                    $n++;
                    $candidate = $safeBase . '_' . $n . '.' . $ext;
                }
                $path = $sa->storeAs($folder, $candidate, 'public');

                // Update staff record with spouse attachment path
                $staff->spouse_attachment = $path;
                $staff->save();
            }

            // Process joining documents (docs[joining][<key>][file]) - support single file or arrays of files
            $docs = $request->file('docs') ?: [];
            foreach ($docs as $group => $items) {
                if (!is_array($items))
                    continue;
                foreach ($items as $key => $item) {
                    $remarks = $request->input("docs.$group.$key.remarks");
                    $expiry = $request->input("docs.$group.$key.expiry");

                    // Normalize to an array of UploadedFile objects (may be a single file or array)
                    $files = [];

                    if (is_array($item) && isset($item['file'])) {
                        if (is_array($item['file'])) {
                            $files = $item['file'];
                        } elseif ($item['file'] instanceof \Illuminate\Http\UploadedFile) {
                            $files = [$item['file']];
                        }
                    } elseif ($item instanceof \Illuminate\Http\UploadedFile) {
                        $files = [$item];
                    } elseif (is_array($item)) {
                        // fallback: check for any UploadedFile values inside
                        foreach ($item as $v) {
                            if ($v instanceof \Illuminate\Http\UploadedFile) {
                                $files[] = $v;
                            }
                        }
                    }

                    // Loop each file (handle arrays)
                    foreach ($files as $file) {
                        if (!($file instanceof \Illuminate\Http\UploadedFile))
                            continue;

                        $orig = $file->getClientOriginalName();
                        $ext = $file->getClientOriginalExtension();
                        $base = pathinfo($orig, PATHINFO_FILENAME);
                        $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
                        $candidate = $safeBase . '.' . $ext;
                        $folder = 'employee/docs/' . $group;
                        $n = 0;
                        while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                            $n++;
                            $candidate = $safeBase . '_' . $n . '.' . $ext;
                        }
                        $path = $file->storeAs($folder, $candidate, 'public');

                        OnboardingEmployeeDocument::create([
                            'staff_id' => $staff->id,
                            'group' => $group,
                            'key' => $key,
                            'name' => $orig,
                            'remarks' => $remarks ?? null,
                            'path' => $path,
                            'file_path' => $path,
                            'document_number' => $request->input("docs.$group.$key.number") ?? null,
                            'expiry_date' => $expiry ? Carbon::createFromFormat('d/m/Y', $expiry)->format('Y-m-d') : null,
                        ]);
                    }
                }
            }

            // Process banks (files may be in $request->file('banks') as array)
            $banksInput = $request->input('banks', []);
            $bankFiles = $request->file('banks') ?: [];

            foreach ($banksInput as $i => $b) {
                $ibanPath = null;
                if (isset($bankFiles[$i]) && isset($bankFiles[$i]['iban_letter']) && $bankFiles[$i]['iban_letter']) {
                    $file = $bankFiles[$i]['iban_letter'];
                    $originalName = $file->getClientOriginalName();
                    $ext = $file->getClientOriginalExtension();
                    $base = pathinfo($originalName, PATHINFO_FILENAME);
                    $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
                    $candidate = $safeBase . '.' . $ext;
                    $folder = 'employee/iban_letters';
                    $j = 0;
                    while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                        $j++;
                        $candidate = $safeBase . '_' . $j . '.' . $ext;
                    }
                    $ibanPath = $file->storeAs($folder, $candidate, 'public');
                }

                EmployeeOnboardingBankDetail::create([
                    'staff_id' => $staff->id,
                    'bank_name' => $b['bank_name'] ?? null,
                    'bank_branch' => $b['branch_name'] ?? null,
                    'bank_ac_holder' => $b['account_holder'] ?? null,
                    'bank_ac_number' => $b['account_number'] ?? null,
                    'iban_number' => $b['iban_number'] ?? null,
                    'swift_code' => $b['swift_code'] ?? null,
                    'bank_currency' => $b['currency'] ?? null,
                    'att_iban_letter' => $ibanPath,
                ]);
            }

            // Process educations
            $edInput = $request->input('educations', []);
            $edFiles = $request->file('educations') ?: [];
            foreach ($edInput as $i => $e) {
                $certPath = null;
                if (isset($edFiles[$i]) && isset($edFiles[$i]['certificate']) && $edFiles[$i]['certificate']) {
                    $f = $edFiles[$i]['certificate'];
                    $originalName = $f->getClientOriginalName();
                    $ext = $f->getClientOriginalExtension();
                    $base = pathinfo($originalName, PATHINFO_FILENAME);
                    $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
                    $candidate = $safeBase . '.' . $ext;
                    $folder = 'employee/education_certificates';
                    $k = 0;
                    while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                        $k++;
                        $candidate = $safeBase . '_' . $k . '.' . $ext;
                    }
                    $certPath = $f->storeAs($folder, $candidate, 'public');
                }

                EmployeeOnboardingEducation::create([
                    'staff_id' => $staff->id,
                    'qualification' => $e['qualification'] ?? null,
                    'university' => $e['university'] ?? null,
                    'specialization' => $e['specialization'] ?? null,
                    'year' => $e['year'] ?? null,
                    'result' => $e['result'] ?? null,
                    'gpa' => $e['gpa'] ?? null,
                    'mode' => $e['mode'] ?? null,
                    'country' => $e['country'] ?? null,
                    'duration_years' => $e['duration'] ?? null,
                    'certificate_path' => $certPath,
                ]);
            }

            // Process experiences
            $exInput = $request->input('experiences', []);
            $exFiles = $request->file('experiences') ?: [];
            foreach ($exInput as $i => $ex) {
                $expCert = null;
                if (isset($exFiles[$i]) && isset($exFiles[$i]['certificate']) && $exFiles[$i]['certificate']) {
                    $f = $exFiles[$i]['certificate'];
                    $originalName = $f->getClientOriginalName();
                    $ext = $f->getClientOriginalExtension();
                    $base = pathinfo($originalName, PATHINFO_FILENAME);
                    $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
                    $candidate = $safeBase . '.' . $ext;
                    $folder = 'employee/experience_certificates';
                    $m = 0;
                    while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                        $m++;
                        $candidate = $safeBase . '_' . $m . '.' . $ext;
                    }
                    $expCert = $f->storeAs($folder, $candidate, 'public');
                }

                EmployeeOnboardingExperience::create([
                    'staff_id' => $staff->id,
                    'organization' => $ex['organization'] ?? null,
                    'designation' => $ex['designation'] ?? null,
                    'years' => $ex['years'] ?? null,
                    'months' => $ex['months'] ?? null,
                    'responsibilities' => $ex['responsibilities'] ?? null,
                    'certificate_path' => $expCert,
                ]);
            }


            $staff->document_number = SysHelper::get_new_code_lead('employees_onboarding', 'OB', 'document_number', $request->input('company_id'));

            $staff->save();
            DB::commit();

            // Post/Redirect/Get: redirect to a thank-you page to avoid form re-submission on refresh
            $redirectUrl = url('onboarding-employee/thank-you');
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['ok' => true, 'message' => 'Saved', 'redirect' => $redirectUrl]);
            }
            return redirect()->to($redirectUrl)->with('success', 'Onboarding submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            Log::error('Onboard save error: ' . $e->getMessage(), ['exception' => $e]);
            Toastr::error('An error occurred while saving onboarding data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while saving onboarding data.');
        }
    }

    /**
     * Thank-you page after successful onboarding (GET)
     */
    public function thankYou()
    {
        return view('backEnd.humanResource.onboarding-employee.thank-you');
    }

    /**
     * Approve onboarding and convert to SmStaff + migrate related data
     */
    public function approveOnboardEmployee(Request $request, $id)
    {
        $roleId = (int) ($request->input('role_id') ?? config('hr.default_onboard_role', 5));

        DB::beginTransaction();
        try {
            $employee = EmployeeOnboarding::with(['documents', 'bankDetails', 'educations', 'experiences'])->findOrFail($id);

            // if ($employee->auth_status == 1 && $employee->approved_by != null) {
            //     Toastr::error('This employee has already been approved.');
            //     return redirect()->back();
            // }

            // --- User: create or reuse ---
            $user = null;
            if (!empty($employee->email)) {
                $user = User::where('email', $employee->email)->first();
            }

            if (!$user) {
                $usernameBase = $employee->email ? strstr($employee->email, '@', true) : Str::slug(($employee->full_name ?? 'user' . $employee->id), '_');
                $username = $usernameBase;
                $i = 0;
                while (User::where('username', $username)->exists()) {
                    $i++;
                    $username = $usernameBase . '_' . $i;
                }

                $user = new User();
                $user->role_id = $roleId;
                $user->username = $username;
                $user->email = $employee->email ?? $username . '@local';
                $user->full_name = $employee->full_name ?: trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? ''));
                if (!empty($employee->password)) {
                    $user->password = $employee->password; // assume already hashed
                } else {
                    $user->password = Hash::make(Str::random(12));
                }
                $user->usertype = 'staff';
                $user->access_status = 1;
                $user->save();
            }

            // helper to normalize stored paths across onboarding vs staff tables
            $normalizePath = function ($p) {
                if (!$p)
                    return null;
                $s = (string) $p;
                // strip common storage prefixes to avoid double 'storage/'
                $s = preg_replace('#^storage/app/public/#', '', $s);
                $s = preg_replace('#^storage/#', '', $s);
                return ltrim($s, '/');
            };

            // --- Create SmStaff and map all onboarding fields ---
            $staff = new SmStaff();
            $staff->staff_no = SysHelper::get_new_staff_code();
            $staff->role_id = $roleId;


            // Company / identity
            $staff->company_id = session('logged_session_data.company_id') ?: 1;
            $staff->main_company = $employee->company_id ?? $staff->company_id;


            // Basic identity
            $staff->first_name = $this->normalizeFirstName($employee->first_name);
            $staff->first_name_full = $employee->first_name;
            $staff->middle_name = $employee->middle_name ?? null;
            $staff->last_name = $employee->last_name;
            $staff->full_name = $employee->full_name ?: trim(($staff->first_name ?: '') . ' ' . ($employee->last_name ?? ''));
            $staff->email = $employee->email;
            $staff->mobile = $employee->mobile;
            $staff->gender_id = $employee->gender_id ?? null;
            $staff->blood_group = $employee->blood_group ?? null;

            // Dates
            $staff->date_of_joining = $employee->date_of_joining ? Carbon::parse($employee->date_of_joining)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
            $staff->date_of_birth = $employee->date_of_birth ?? null;

            // Job / role


            $staff->employment_type = $employee->employment_type ?? null;


            // Addresses
            $staff->permanent_country = $employee->permanent_country ?? null;
            $staff->permanent_state = $employee->permanent_state ?? null;
            $staff->permanent_city = $employee->permanent_city ?? null;
            $staff->permanent_area = $employee->permanent_area ?? null;
            $staff->permanent_building_no = $employee->permanent_building_no ?? null;
            $staff->permanent_flat_no = $employee->permanent_flat_no ?? null;
            $staff->permanent_address = $employee->permanent_address ?? null;

            $staff->current_country = $employee->current_country ?? null;
            $staff->current_state = $employee->current_state ?? null;
            $staff->current_city = $employee->current_city ?? null;
            $staff->current_area = $employee->current_area ?? null;
            $staff->current_building_no = $employee->current_building_no ?? null;
            $staff->current_flat_no = $employee->current_flat_no ?? null;
            $staff->current_address = $employee->current_address ?? null;

            // Contact & emergency
            $staff->emergency_contact_name = $employee->emergency_contact_name ?? null;
            $staff->emergency_mobile = $employee->emergency_mobile ?? null;
            $staff->emergency_contact_relationship = $employee->emergency_contact_relationship ?? null;
            $staff->emergency_email = $employee->emergency_email ?? null;
            $staff->emergency2_contact_name = $employee->emergency2_contact_name ?? null;
            $staff->emergency2_mobile = $employee->emergency2_mobile ?? null;
            $staff->emergency2_contact_relationship = $employee->emergency2_contact_relationship ?? null;
            $staff->emergency2_email = $employee->emergency2_email ?? null;

            // Family / spouse
            $staff->employee_salutation = $employee->employee_salutation ?? null;
            // Copy emergency contact salutations from onboarding
            $staff->em1_salutation = $employee->em1_salutation ?? null;
            $staff->em2_salutation = $employee->em2_salutation ?? null;

            $staff->fathers_first_name = $employee->fathers_first_name ?? null;
            $staff->fathers_last_name = $employee->fathers_last_name ?? null;
            $staff->father_mobile = $employee->father_mobile ?? null;
            $staff->father_email = $employee->father_email ?? null;

            $staff->mothers_first_name = $employee->mothers_first_name ?? null;
            $staff->mothers_last_name = $employee->mothers_last_name ?? null;
            $staff->mother_mobile = $employee->mother_mobile ?? null;
            $staff->mother_email = $employee->mother_email ?? null;

            $staff->spouse_first_name = $employee->spouse_first_name ?? null;
            $staff->spouse_last_name = $employee->spouse_last_name ?? null;
            $staff->spouse_mobile = $employee->spouse_mobile ?? null;
            $staff->spouse_email = $employee->spouse_email ?? null;

            // Personal / HR fields
            $staff->marital_status = $employee->marital_status ?? null;
            $staff->religion = $employee->religion ?? null;
            $staff->nationality = $employee->nationality ?? null;
            $staff->place_of_birth = $employee->place_of_birth ?? null;

            // Qualifications, salary & leaves
            $staff->qualification = $employee->qualification ?? null;
            $staff->experience = $employee->experience ?? null;
            $staff->epf_no = $employee->epf_no ?? null;
            $staff->basic_salary = $employee->basic_salary ?? null;
            $staff->contract_type = $employee->contract_type ?? null;
            $staff->location = $employee->location ?? null;
            $staff->casual_leave = $employee->casual_leave ?? null;
            $staff->medical_leave = $employee->medical_leave ?? null;
            $staff->metarnity_leave = $employee->metarnity_leave ?? null;

            // Bank & payment meta (top-level convenience fields)
            $staff->bank_account_name = $employee->bank_account_name ?? null;
            $staff->bank_account_no = $employee->bank_account_no ?? null;
            $staff->bank_name = $employee->bank_name ?? null;
            $staff->bank_brach = $employee->bank_brach ?? null;

            // Social / payment accounts
            $staff->paypal_account = $employee->paypal_account ?? null;
            $staff->payoneer_account = $employee->payoneer_account ?? null;
            $staff->skrill_account = $employee->skrill_account ?? null;
            $staff->stripe_account = $employee->stripe_account ?? null;
            $staff->wepay_account = $employee->wepay_account ?? null;
            $staff->amazon_account = $employee->amazon_account ?? null;

            // Documents & misc
            $staff->joining_letter = $employee->joining_letter ?? null;
            $staff->resume = $employee->resume ?? null;
            $staff->other_document = $employee->other_document ?? null;
            $staff->driving_license = $employee->driving_license ?? null;
            $staff->driving_license_ex_date = $employee->driving_license_ex_date ?? null;
            $staff->notes = $employee->notes ?? null;
            $staff->brands = $employee->brands ?? null;

            if (!empty($employee->password)) {
                $staff->password = $employee->password;
            }
            $staff->created_by = auth()->id() ?: null;
            $staff->user_id = $user->id;

            // copy photo from storage public to public uploads to follow SmStaff convention
            $empPhoto = $employee->staff_photo ?? null;
            if (!empty($empPhoto)) {
                // normalize possible storage prefixes to match public disk layout
                $empPhoto = preg_replace('#^storage/app/public/#', '', $empPhoto);
                $empPhoto = preg_replace('#^storage/#', '', $empPhoto);
                $empPhoto = ltrim((string) $empPhoto, '/');

                if (Storage::disk('public')->exists($empPhoto)) {
                    $src = storage_path('app/public/' . $empPhoto);
                    $destDir = public_path('uploads/staff');
                    if (!is_dir($destDir)) {
                        mkdir($destDir, 0777, true);
                    }
                    $destName = time() . '_' . basename($empPhoto);
                    copy($src, $destDir . '/' . $destName);
                    // SmStaff expects paths like 'public/uploads/staff/...'
                    $staff->staff_photo = 'public/uploads/staff/' . $destName;
                } else {
                    // If it's already stored in public (e.g. 'public/uploads/staff/...') reuse it
                    if (strpos($empPhoto, 'public/') === 0 && file_exists(public_path(substr($empPhoto, 7)))) {
                        $staff->staff_photo = $empPhoto;
                    } elseif (strpos($empPhoto, 'uploads/') === 0 && file_exists(public_path($empPhoto))) {
                        $staff->staff_photo = 'public/' . ltrim($empPhoto, '/');
                    } elseif (file_exists(public_path('uploads/staff/' . basename($empPhoto)))) {
                        $staff->staff_photo = 'public/uploads/staff/' . basename($empPhoto);
                    } else {
                        Log::warning('Staff photo for onboarding could not be found or copied: ' . $employee->staff_photo);
                    }
                }
            }

            $staff->save();

            // Transfer job detail from onboarding job detail into staff job detail (copy all fields; null when absent)
            try {
                $onboardJob = OnboardEmployeeJobDetail::where('staff_id', $employee->id)->first();

                $staff->designation_id = $onboardJob->designation_id ?? null;
                $staff->department_id = $onboardJob->department_id ?? null;
                $staff->company_access = $onboardJob->company_access ?? null;
                $staff->ext_no = $onboardJob->ext_no ?? null;
                $staff->grade = $onboardJob->grade ?? null;
                $staff->reporting_manager = $onboardJob->reporting_manager ?? null;
                $staff->is_target = (int) ($onboardJob->is_target ?? 0);

                $revenue = $this->numOrNull($onboardJob->revenue_target);
                $revenueTargets = $this->calculateTargets($revenue, $onboardJob->target_period);

                $staff->revenue_target_weekly = $revenueTargets['weekly'];
                $staff->revenue_target_monthly = $revenueTargets['monthly'];
                $staff->revenue_target_quaterly = $revenueTargets['quarterly'];
                $staff->revenue_target_yearly = $revenueTargets['yearly'];


                $gp = $this->numOrNull($onboardJob->gp_target);
                $gpTargets = $this->calculateTargets($gp, $onboardJob->target_period);

                $staff->gp_target_weekly = $gpTargets['weekly'];
                $staff->gp_target_monthly = $gpTargets['monthly'];
                $staff->gp_target_quaterly = $gpTargets['quarterly'];
                $staff->gp_target_yearly = $gpTargets['yearly'];



                // $staff->revenue_target_weekly = '';
                // $staff->revenue_target_monthly = '';
                // $staff->revenue_target_quaterly = '';
                // $staff->revenue_target_yearly = '';
                // $staff->gp_target_weekly = '';
                // $staff->gp_target_monthly = '';
                // $staff->gp_target_quaterly = '';
                // $staff->gp_target_yearly = '';
                $staff->target_month_from = $onboardJob->target_month_from ?? null;




                $staff->save();
                if ($onboardJob) {
                    $payload = [
                        'staff_id' => $staff->id,
                        'department_id' => $onboardJob->department_id ?? null,
                        'designation_id' => $onboardJob->designation_id ?? null,
                        'employment_type' => $onboardJob->employment_type ?? null,
                        'reporting_manager' => $onboardJob->reporting_manager ?? null,
                        'probation_end_date' => $onboardJob->probation_end_date ?? null,
                        'target_type' => $onboardJob->target_type ?? null,
                        'work_location' => $onboardJob->work_location ?? null,
                        'work_hours' => $onboardJob->work_hours ?? null,
                        'ext_no' => $onboardJob->ext_no ?? null,
                        'company_email' => $onboardJob->company_email ?? null,
                        'company_mobile' => $onboardJob->company_mobile ?? null,
                        'visa_company_name' => $onboardJob->visa_company_name ?? null,
                        'working_company_name' => $onboardJob->working_company_name ?? null,
                        'company_access' => $onboardJob->company_access ?? null,
                        'week_off' => $onboardJob->week_off ?? null,
                        'salary_basic' => $this->numOrNull($onboardJob->salary_basic) ?: null,
                        'salary_allowances' => $this->numOrNull($onboardJob->salary_allowances) ?: null,
                        'salary_other_allowances' => $this->numOrNull($onboardJob->salary_other_allowances) ?: null,
                        'transport_allowance' => $this->numOrNull($onboardJob->transport_allowance) ?: null,
                        'other_benefits' => $this->numOrNull($onboardJob->other_benefits) ?: null,
                        'salary_gross' => $this->numOrNull($onboardJob->salary_gross) ?: null,
                        'is_target' => (int) ($onboardJob->is_target ?? 0),
                        'target_month_from' => $onboardJob->target_month_from ?? null,
                        'brand_ids' => $onboardJob->brand_ids ?? null,
                        'grade' => $onboardJob->grade ?? null,
                        'role_id' => $onboardJob->role_id ?? null,
                        'target_period' => $onboardJob->target_period ?? null,
                        'revenue_target' => $this->numOrNull($onboardJob->revenue_target) ?: null,
                        'gp_target' => $this->numOrNull($onboardJob->gp_target) ?: null,
                        'channel_distribution' => $onboardJob->channel_distribution ?? null,
                        'date_of_joining' => $onboardJob->date_of_joining ?? $staff->date_of_joining ?? null,
                        'att_resume' => $onboardJob->att_resume ? $normalizePath($onboardJob->att_resume) : null,
                        'att_offer_letter' => $onboardJob->att_offer_letter ? $normalizePath($onboardJob->att_offer_letter) : null,
                        'att_signed_contract' => $onboardJob->att_signed_contract ? $normalizePath($onboardJob->att_signed_contract) : null,
                    ];



                    SmStaffJobDetail::create($payload);
                } else {
                    // Fallback: copy minimal top-level job fields when onboarding job row is missing
                    SmStaffJobDetail::create([
                        'staff_id' => $staff->id,
                        'department_id' => $employee->department_id ?? null,
                        'designation_id' => $employee->designation_id ?? null,
                        'grade' => $employee->grade ?? null,
                        'reporting_manager' => $employee->reporting_manager ?? null,
                        'date_of_joining' => $staff->date_of_joining ?? null,
                    ]);
                }
            } catch (\Exception $ex) {
                // non-fatal: log and continue
                Log::warning('Failed to create SmStaffJobDetail during approval: ' . $ex->getMessage());
            }

            // --- Migrate spouse attachment (legacy field) as a family document if present ---
            if (!empty($employee->spouse_attachment)) {
                try {
                    $spPath = $normalizePath($employee->spouse_attachment);
                    // avoid duplicate if staff doc already created by other logic
                    $exists = SmStaffDocument::where('staff_id', $staff->id)->where('group', 'family')->where('key', 'spouse_attachment')->exists();
                    if (!$exists) {
                        SmStaffDocument::create([
                            'staff_id' => $staff->id,
                            'group' => 'family',
                            'key' => 'spouse_attachment',
                            'name' => pathinfo($spPath, PATHINFO_BASENAME),
                            'path' => $spPath,
                        ]);
                    }
                    // keep legacy column on SmStaff for backward compatibility
                    $staff->spouse_attachment = $spPath;
                    $staff->save();
                } catch (\Exception $ex) {
                    Log::warning('Failed to migrate spouse attachment for onboarding ' . $employee->id . ': ' . $ex->getMessage());
                }
            }

            // --- Migrate bank details ---
            foreach ($employee->bankDetails as $b) {
                SmStaffBankDetail::create([
                    'staff_id' => $staff->id,
                    'bank_name' => $b->bank_name ?? null,
                    'bank_branch' => $b->bank_branch ?? $b->bank_brach ?? null,
                    'bank_ac_holder' => $b->bank_ac_holder ?? null,
                    'bank_ac_number' => $b->bank_ac_number ?? null,
                    'iban_number' => $b->iban_number ?? null,
                    'swift_code' => $b->swift_code ?? null,
                    'bank_currency' => $b->bank_currency ?? null,
                    // preserve the onboarding path format (normalize to remove any leading 'storage/')
                    'att_iban_letter' => $normalizePath($b->att_iban_letter),
                ]);
            }

            // --- Migrate educations ---
            foreach ($employee->educations as $ed) {
                SmStaffEducationQualification::create([
                    'staff_id' => $staff->id,
                    'qualification' => $ed->qualification ?? null,
                    'university' => $ed->university ?? null,
                    'specialization' => $ed->specialization ?? null,
                    'year' => $ed->year ?? null,
                    'result' => $ed->result ?? null,
                    'gpa' => blank($ed->gpa) ? null : $ed->gpa,

                    'mode' => $ed->mode ?? null,
                    'country' => $ed->country ?? null,
                    'duration_years' => $ed->duration_years ?? $ed->duration ?? null,
                    'certificate_path' => $normalizePath($ed->certificate_path),
                ]);
            }

            // --- Migrate experiences ---
            foreach ($employee->experiences as $ex) {
                SmStaffProfessionalExperience::create([
                    'staff_id' => $staff->id,
                    'organization' => $ex->organization ?? null,
                    'designation' => $ex->designation ?? null,
                    'years' => $ex->years ?? null,
                    'months' => $ex->months ?? null,
                    'responsibilities' => $ex->responsibilities ?? null,
                    'certificate_path' => $normalizePath($ex->certificate_path),
                ]);
            }

            // --- Migrate documents ---
            foreach ($employee->documents as $d) {
                SmStaffDocument::create([
                    'staff_id' => $staff->id,
                    'group' => $d->group ?? null,
                    'key' => $d->key ?? null,
                    'name' => $d->name ?? null,
                    'path' => $normalizePath($d->path),
                    'remarks' => $d->remarks ?? null,
                    'expiry_date' => $d->expiry_date ?? null,
                    'document_number' => $d->document_number ?? null,
                ]);
            }

            // mark onboarding approved
            $employee->auth_status = 1;
            $employee->auth_date = Carbon::now();
            $employee->approved_by = auth()->id() ?: null;
            $employee->staff_rec_id = $staff->id;
            $employee->user_id = $user->id;
            $employee->staff_no = $staff->staff_no;
            $employee->save();

            DB::commit();

            // If this was an AJAX request, return JSON success
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Onboarding approved and staff record created successfully.',
                    'staff_id' => $staff->id,
                    'redirect' => url('hrms/staff/' . $staff->id . '/edit')
                ]);
            }

            Toastr::success('Onboarding approved and staff record created successfully.');
            return redirect('hrms/staff/' . $staff->id . '/edit');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approve onboarding error: ' . $e->getMessage(), ['exception' => $e]);

            // If AJAX, return JSON error
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Failed to approve onboarding: ' . $e->getMessage()
                ], 500);
            }

            Toastr::error('Failed to approve onboarding: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve onboarding: ' . $e->getMessage());
        }
    }


    public function edit(Request $request, $id)
    {
        $employee = EmployeeOnboarding::with(['documents', 'bankDetails', 'educations', 'experiences'])->findOrFail($id);
        $countries = SysCountries::all();
        $states = SysStates::all();

        // Provide convenient variables to the view for rendering existing collections
        $bankRows = $employee->bankDetails;
        $eduRows = $employee->educations;
        $expRows = $employee->experiences;
        $docRows = $employee->documents;
        $job = $employee->jobDetail;



        $currencies = SysCurrencySettings::select('id', 'code')->where('status', 1)->orderBy('code', 'ASC')->get();


        return view('backEnd.humanResource.onboarding-employee.edit', compact('employee', 'countries', 'states', 'bankRows', 'eduRows', 'expRows', 'docRows', 'job', 'currencies'));
    }

    public function updateOnboardEmployee(Request $request, $id)
    {
        $employee = EmployeeOnboarding::with(['documents'])->findOrFail($id);

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:employees_onboarding,email,' . $id,
            'mobile' => 'nullable|string|max:25',
            'password' => 'nullable|string|min:8|regex:/[0-9]/',
            'staff_photo' => 'nullable|image|max:5000',
            'father_attachment' => 'nullable|file|mimes:pdf,jpeg,png,gif,webp|max:5000',
            'mother_attachment' => 'nullable|file|mimes:pdf,jpeg,png,gif,webp|max:5000',
            'perm_country' => 'nullable|string',
            'perm_state' => 'nullable|string',
            'perm_city' => 'nullable|string',
            'perm_area' => 'nullable|string',
            'perm_building_name' => 'nullable|string',
            'perm_flat_office_no' => 'nullable|string',

            'banks' => 'nullable|array',
            'banks.*.bank_name' => 'required_with:banks|string|max:255',
            'banks.*.account_holder' => 'required_with:banks|string|max:255',
            'banks.*.iban_letter' => 'nullable|file|mimes:pdf,jpeg,png,gif,webp|max:5000',

            'educations' => 'nullable|array',
            'educations.*.qualification' => 'required_with:educations|string|max:255',
            'educations.*.university' => 'required_with:educations|string|max:255',
            'educations.*.year' => 'nullable|digits:4',
            'educations.*.gpa' => 'nullable|numeric',
            'educations.*.duration' => 'nullable|numeric|min:0',
            'educations.*.certificate' => 'nullable|file|mimes:pdf,jpeg,png,gif,webp|max:5000',

            'experiences' => 'nullable|array',
            'experiences.*.organization' => 'required_with:experiences|string|max:255',
            'experiences.*.years' => 'nullable|integer|min:0',
            'experiences.*.months' => 'nullable|integer|min:0|max:12',
            'experiences.*.certificate' => 'nullable|file|mimes:pdf,jpeg,png,gif,webp|max:5000',

            // other fields can be validated similarly
        ];

        // $validator = Validator::make($request->all(), $rules);
        // if ($validator->fails()) {
        //     $first = $validator->errors()->first();
        //     Toastr::error($first ?: 'Validation failed. Please check the inputs.');
        //     return redirect()->back()->withErrors($validator)->withInput();
        // }

        DB::beginTransaction();
        try {
            // Top-level updates (sync more fields to mirror saveOnboardEmployee)
            $employee->employee_salutation = $request->input('salutation') ?: null;
            $employee->first_name = $request->input('first_name') ?: null;
            $employee->last_name = $request->input('last_name') ?: null;
            $employee->full_name = trim($request->input('first_name') . ' ' . $request->input('last_name'));
            $employee->email = $request->input('email') ?: null;
            $employee->mobile = $request->input('mobile') ?: null;
            $employee->place_of_birth = $request->input('place_of_birth') ?: null;
            $employee->religion = $request->input('religion') ?: null;
            $employee->gender_id = $request->input('gender_id') ?: null;
            $employee->marital_status = $request->input('marital_status') ?: null;

            // Parents
            $employee->fathers_first_name = $request->input('father_first_name') ?: null;
            $employee->fathers_last_name = $request->input('father_last_name') ?: null;
            $employee->father_mobile = $request->input('father_mobile') ?: null;
            $employee->father_email = $request->input('father_email') ?: null;

            $employee->mothers_first_name = $request->input('mother_first_name') ?: null;
            $employee->mothers_last_name = $request->input('mother_last_name') ?: null;
            $employee->mother_mobile = $request->input('mother_mobile') ?: null;
            $employee->mother_email = $request->input('mother_email') ?: null;

            // Emergency contacts
            $employee->em1_salutation = $request->input('emergency1_salutation') ?: null;
            $employee->emergency_contact_name = $request->input('emergency1_name') ?: null;
            $employee->emergency_mobile = $request->input('emergency1_mobile') ?: null;
            $employee->emergency_email = $request->input('emergency1_email') ?: null;
            $employee->emergency_contact_relationship = $request->input('emergency1_relationship') ?: null;
            $employee->em2_salutation = $request->input('emergency2_salutation') ?: null;
            $employee->emergency2_contact_name = $request->input('emergency2_name') ?: null;
            $employee->emergency2_mobile = $request->input('emergency2_mobile') ?: null;
            $employee->emergency2_contact_relationship = $request->input('emergency2_relationship') ?: null;
            $employee->emergency2_email = $request->input('emergency2_email') ?: null;

            // addresses
            $employee->permanent_country = $request->input('perm_country') ?: null;
            $employee->permanent_state = $request->input('perm_state') ?: null;
            $employee->permanent_city = $request->input('perm_city') ?: null;
            $employee->permanent_area = $request->input('perm_area') ?: null;
            $employee->permanent_building_no = $request->input('perm_building_name') ?: null;
            $employee->permanent_flat_no = $request->input('perm_flat_office_no') ?: null;
            $employee->permanent_address = trim(implode(', ', array_filter([$request->input('perm_building_name'), $request->input('perm_area'), $request->input('perm_city'), $request->input('perm_state')])));

            $employee->current_country = $request->input('curr_country') ?: null;
            $employee->current_state = $request->input('curr_state') ?: null;
            $employee->current_city = $request->input('curr_city') ?: null;
            $employee->current_area = $request->input('curr_area') ?: null;
            $employee->current_building_no = $request->input('curr_building_name') ?: null;
            $employee->current_flat_no = $request->input('curr_flat_office_no') ?: null;
            $employee->current_address = trim(implode(', ', array_filter([$request->input('curr_building_name'), $request->input('curr_area'), $request->input('curr_city'), $request->input('curr_state')])));

            // Top-level computed summaries
            $qualificationSummary = null;
            $educationsInput = $request->input('educations', []);
            if (!empty($educationsInput)) {
                $quals = array_filter(array_column($educationsInput, 'qualification'));
                if (!empty($quals)) {
                    $qualificationSummary = implode(', ', $quals);
                }
            }

            $experienceSummary = null;
            $exps = $request->input('experiences', []);
            if (!empty($exps)) {
                $totalMonths = 0;
                foreach ($exps as $ee) {
                    $y = isset($ee['years']) ? intval($ee['years']) : 0;
                    $m = isset($ee['months']) ? intval($ee['months']) : 0;
                    $totalMonths += ($y * 12) + $m;
                }
                $yrs = intdiv($totalMonths, 12);
                $mths = $totalMonths % 12;
                $experienceSummary = trim($yrs . ' Y, ' . $mths . ' M');
            }

            $employee->qualification = $qualificationSummary;
            $employee->experience = $experienceSummary;

            // other optional fields
            $employee->notes = $request->input('notes', $employee->notes ?? '');
            $employee->place_of_birth = $request->input('place_of_birth') ?? $employee->place_of_birth;
            $employee->blood_group = $request->input('blood_group') ?? $employee->blood_group;
            $employee->spouse_first_name = $request->input('spouse_first_name') ?? $employee->spouse_first_name;
            $employee->spouse_last_name = $request->input('spouse_last_name') ?? $employee->spouse_last_name;
            $employee->spouse_mobile = $request->input('spouse_mobile') ?? $employee->spouse_mobile;
            $employee->spouse_email = $request->input('spouse_email') ?? $employee->spouse_email;
            $employee->company_id = $request->input('company_id') ?? $employee->company_id;

            // parse date_of_birth if provided (expecting d/m/Y)
            $employee->date_of_birth = null;
            if ($request->filled('date_of_birth')) {
                try {
                    $dob = Carbon::createFromFormat('d/m/Y', $request->input('date_of_birth'));
                    $employee->date_of_birth = $dob->format('Y-m-d');
                } catch (\Exception $ex) {
                    Log::warning('DOB parse failed on update: ' . $ex->getMessage());
                }
            }
            // photo replace
            if ($request->hasFile('staff_photo')) {
                // delete old
                if (!empty($employee->staff_photo) && Storage::disk('public')->exists($employee->staff_photo)) {
                    Storage::disk('public')->delete($employee->staff_photo);
                }
                $file = $request->file('staff_photo');
                $orig = $file->getClientOriginalName();
                $ext = $file->getClientOriginalExtension();
                $base = pathinfo($orig, PATHINFO_FILENAME);
                $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
                $candidate = $safeBase . '.' . $ext;
                $folder = 'employee/staff_photos';
                $i = 0;
                while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                    $i++;
                    $candidate = $safeBase . '_' . $i . '.' . $ext;
                }
                $path = $file->storeAs($folder, $candidate, 'public');
                $employee->staff_photo = $path;
            }

            // father / mother attachments
            if ($request->hasFile('father_attachment')) {
                $f = $request->file('father_attachment');
                $orig = $f->getClientOriginalName();
                $ext = $f->getClientOriginalExtension();
                $base = pathinfo($orig, PATHINFO_FILENAME);
                $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
                $candidate = $safeBase . '.' . $ext;
                $folder = 'employee/family_attachments';
                $n = 0;
                while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                    $n++;
                    $candidate = $safeBase . '_' . $n . '.' . $ext;
                }
                $path = $f->storeAs($folder, $candidate, 'public');

                $doc = OnboardingEmployeeDocument::where('staff_id', $employee->id)->where('group', 'family')->where('key', 'father_attachment')->first();
                if ($doc && Storage::disk('public')->exists($doc->path)) {
                    Storage::disk('public')->delete($doc->path);
                }
                OnboardingEmployeeDocument::updateOrCreate([
                    'staff_id' => $employee->id,
                    'group' => 'family',
                    'key' => 'father_attachment',
                ], [
                    'name' => $orig,
                    'path' => $path,
                    'file_path' => $path,
                ]);
            }

            if ($request->hasFile('mother_attachment')) {
                $m = $request->file('mother_attachment');
                $orig = $m->getClientOriginalName();
                $ext = $m->getClientOriginalExtension();
                $base = pathinfo($orig, PATHINFO_FILENAME);
                $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
                $candidate = $safeBase . '.' . $ext;
                $folder = 'employee/family_attachments';
                $n = 0;
                while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                    $n++;
                    $candidate = $safeBase . '_' . $n . '.' . $ext;
                }
                $path = $m->storeAs($folder, $candidate, 'public');

                $doc = OnboardingEmployeeDocument::where('staff_id', $employee->id)->where('group', 'family')->where('key', 'mother_attachment')->first();
                if ($doc && Storage::disk('public')->exists($doc->path)) {
                    Storage::disk('public')->delete($doc->path);
                }
                OnboardingEmployeeDocument::updateOrCreate([
                    'staff_id' => $employee->id,
                    'group' => 'family',
                    'key' => 'mother_attachment',
                ], [
                    'name' => $orig,
                    'path' => $path,
                    'file_path' => $path,
                ]);
            }

            // spouse attachment replace
            if ($request->hasFile('spouse_attachment')) {
                // delete old file if exists
                if (!empty($employee->spouse_attachment) && Storage::disk('public')->exists($employee->spouse_attachment)) {
                    Storage::disk('public')->delete($employee->spouse_attachment);
                }
                $sa = $request->file('spouse_attachment');
                $orig = $sa->getClientOriginalName();
                $ext = $sa->getClientOriginalExtension();
                $base = pathinfo($orig, PATHINFO_FILENAME);
                $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
                $candidate = $safeBase . '.' . $ext;
                $folder = 'employee/family_attachments';
                $n = 0;
                while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                    $n++;
                    $candidate = $safeBase . '_' . $n . '.' . $ext;
                }
                $path = $sa->storeAs($folder, $candidate, 'public');
                $employee->spouse_attachment = $path;
            }

            // process uploaded docs: docs[group][key][file]
            $docs = $request->file('docs') ?: [];
            foreach ($docs as $group => $items) {
                if (!is_array($items))
                    continue;
                foreach ($items as $key => $item) {
                    if (is_array($item) && isset($item['file']) && $item['file']) {
                        // file may be an array (multiple uploaded files) or a single UploadedFile

                        // If original value is an array (name ended with [] in the form), then always append each file as a new document
                        if (is_array($item['file'])) {
                            foreach ($item['file'] as $file) {
                                if (!$file instanceof \Illuminate\Http\UploadedFile)
                                    continue;
                                $orig = $file->getClientOriginalName();
                                $ext = $file->getClientOriginalExtension();
                                $base = pathinfo($orig, PATHINFO_FILENAME);
                                $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
                                $candidate = $safeBase . '.' . $ext;
                                $folder = 'employee/docs/' . $group;
                                $n = 0;
                                while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                                    $n++;
                                    $candidate = $safeBase . '_' . $n . '.' . $ext;
                                }
                                $path = $file->storeAs($folder, $candidate, 'public');

                                OnboardingEmployeeDocument::create([
                                    'staff_id' => $employee->id,
                                    'group' => $group,
                                    'key' => $key,
                                    'name' => $orig,
                                    'path' => $path,
                                    'file_path' => $path,
                                    'remarks' => $request->input("docs.$group.$key.remarks") ?? null,
                                    'document_number' => $request->input("docs.$group.$key.number") ?? null,
                                    'expiry_date' => (function ($v) {
                                        try {
                                            return Carbon::createFromFormat('d/m/Y', $v)->format('Y-m-d'); } catch (\Exception $e) {
                                            return null; } })($request->input("docs.$group.$key.expiry") ?? null),
                                ]);
                            }
                        } else {
                            // single file case: replace the single existing document for this group/key
                            $file = $item['file'];
                            if (!$file instanceof \Illuminate\Http\UploadedFile)
                                continue;
                            $orig = $file->getClientOriginalName();
                            $ext = $file->getClientOriginalExtension();
                            $base = pathinfo($orig, PATHINFO_FILENAME);
                            $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
                            $candidate = $safeBase . '.' . $ext;
                            $folder = 'employee/docs/' . $group;
                            $n = 0;
                            while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                                $n++;
                                $candidate = $safeBase . '_' . $n . '.' . $ext;
                            }
                            $path = $file->storeAs($folder, $candidate, 'public');

                            $doc = OnboardingEmployeeDocument::where('staff_id', $employee->id)->where('group', $group)->where('key', $key)->first();
                            if ($doc && Storage::disk('public')->exists($doc->path)) {
                                Storage::disk('public')->delete($doc->path);
                            }

                            // parse expiry input safely (allow d/m/Y input)
                            $expiryInput = $request->input("docs.$group.$key.expiry") ?? null;
                            $expiry = null;
                            if (!empty($expiryInput)) {
                                try {
                                    $expiry = Carbon::createFromFormat('d/m/Y', $expiryInput)->format('Y-m-d');
                                } catch (\Exception $ex) {
                                    $expiry = null;
                                }
                            }

                            OnboardingEmployeeDocument::updateOrCreate([
                                'staff_id' => $employee->id,
                                'group' => $group,
                                'key' => $key,
                            ], [
                                'name' => $orig,
                                'path' => $path,
                                'file_path' => $path,
                                'remarks' => $request->input("docs.$group.$key.remarks") ?? null,
                                'document_number' => $request->input("docs.$group.$key.number") ?? null,
                                'expiry_date' => $expiry,
                            ]);
                        }
                    }
                }
            }

            // Persist docs metadata even when file is not replaced (for joining docs)
            $inputDocs = $request->input('docs', []);
            foreach ($inputDocs as $group => $items) {
                if (!is_array($items))
                    continue;
                foreach ($items as $key => $vals) {
                    // Normalize scalar inputs (e.g. docs[joining][academic][remarks] -> 'some text') into an array
                    if (!is_array($vals)) {
                        $vals = ['remarks' => $vals];
                    }

                    // use array_key_exists to allow explicit clearing (empty string)
                    $existingPath = array_key_exists('existing', $vals) ? $vals['existing'] : null;
                    $remarks = array_key_exists('remarks', $vals) ? $vals['remarks'] : null;
                    $number = array_key_exists('number', $vals) ? $vals['number'] : null;
                    $expiryRaw = array_key_exists('expiry', $vals) ? $vals['expiry'] : null;
                    $expiry = null;
                    if ($expiryRaw !== null && $expiryRaw !== '') {
                        try {
                            $expiry = Carbon::createFromFormat('d/m/Y', $expiryRaw)->format('Y-m-d');
                        } catch (\Exception $ex) {
                            $expiry = null;
                        }
                    } elseif ($expiryRaw === '') {
                        $expiry = null; // explicit clear
                    }

                    if (array_key_exists('existing', $vals) || array_key_exists('number', $vals) || array_key_exists('remarks', $vals) || array_key_exists('expiry', $vals)) {
                        // get existing DB record if present so we don't clear a path that was just created above
                        $doc = OnboardingEmployeeDocument::where('staff_id', $employee->id)->where('group', $group)->where('key', $key)->first();

                        // Prefer any path currently stored in DB (this may have been updated above if a file was uploaded).
                        // Fall back to the explicit hidden existing path when DB has none.
                        $finalPath = ($doc && !empty($doc->path)) ? $doc->path : ($existingPath ?? null);

                        // Prefer DB name if present (reflects uploaded file name), else derive from existing path
                        $name = ($doc && !empty($doc->name)) ? $doc->name : ($existingPath ? basename($existingPath) : null);

                        OnboardingEmployeeDocument::updateOrCreate([
                            'staff_id' => $employee->id,
                            'group' => $group,
                            'key' => $key,
                        ], [
                            'name' => $name,
                            'path' => $finalPath,
                            'file_path' => $finalPath,
                            'remarks' => $remarks,
                            'document_number' => $number,
                            'expiry_date' => $expiry,
                        ]);
                    }
                }
            }

            // --- BANKS: rebuild list from submitted banks[] (preserve existing file when not replaced) ---
            $banksInput = $request->input('banks', []);
            $bankFiles = $request->file('banks') ?: [];
            $oldBanks = $employee->bankDetails()->get();
            $oldBankPaths = $oldBanks->pluck('att_iban_letter')->filter()->toArray();
            $newBankPaths = [];

            $newBanks = [];
            foreach ($banksInput as $i => $b) {
                $ibanPath = null;
                if (isset($bankFiles[$i]) && isset($bankFiles[$i]['iban_letter']) && $bankFiles[$i]['iban_letter']) {
                    $file = $bankFiles[$i]['iban_letter'];
                    $originalName = $file->getClientOriginalName();
                    $ext = $file->getClientOriginalExtension();
                    $base = pathinfo($originalName, PATHINFO_FILENAME);
                    $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
                    $candidate = $safeBase . '.' . $ext;
                    $folder = 'employee/iban_letters';
                    $j = 0;
                    while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                        $j++;
                        $candidate = $safeBase . '_' . $j . '.' . $ext;
                    }
                    $ibanPath = $file->storeAs($folder, $candidate, 'public');
                } elseif (!empty($b['iban_letter_existing'])) {
                    $ibanPath = $b['iban_letter_existing'];
                }

                $newBankPaths[] = $ibanPath;

                $newBanks[] = [
                    'staff_id' => $employee->id,
                    'bank_name' => $b['bank_name'] ?? null,
                    'bank_branch' => $b['branch_name'] ?? null,
                    'bank_ac_holder' => $b['account_holder'] ?? null,
                    'bank_ac_number' => $b['account_number'] ?? null,
                    'iban_number' => $b['iban_number'] ?? null,
                    'swift_code' => $b['swift_code'] ?? null,
                    'bank_currency' => $b['currency'] ?? null,
                    'att_iban_letter' => $ibanPath,
                ];
            }

            // Remove old bank records
            EmployeeOnboardingBankDetail::where('staff_id', $employee->id)->delete();
            // Insert new ones
            foreach ($newBanks as $nb) {
                EmployeeOnboardingBankDetail::create($nb);
            }
            // Delete orphaned files (paths that existed before but are not referenced anymore)
            $toDelete = array_diff($oldBankPaths, array_filter($newBankPaths));
            foreach ($toDelete as $p) {
                if ($p && Storage::disk('public')->exists($p)) {
                    Storage::disk('public')->delete($p);
                }
            }

            // --- EDUCATIONS: rebuild list ---
            $edInput = $request->input('educations', []);
            $edFiles = $request->file('educations') ?: [];

            $oldEds = $employee->educations()->get();
            $oldEdPaths = $oldEds->pluck('certificate_path')->filter()->toArray();
            $newEdPaths = [];

            $newEds = [];
            foreach ($edInput as $i => $e) {
                $certPath = null;
                if (isset($edFiles[$i]) && isset($edFiles[$i]['certificate']) && $edFiles[$i]['certificate']) {
                    $f = $edFiles[$i]['certificate'];
                    $originalName = $f->getClientOriginalName();
                    $ext = $f->getClientOriginalExtension();
                    $base = pathinfo($originalName, PATHINFO_FILENAME);
                    $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
                    $candidate = $safeBase . '.' . $ext;
                    $folder = 'employee/education_certificates';
                    $k = 0;
                    while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                        $k++;
                        $candidate = $safeBase . '_' . $k . '.' . $ext;
                    }
                    $certPath = $f->storeAs($folder, $candidate, 'public');
                } elseif (!empty($e['certificate_existing'])) {
                    $certPath = $e['certificate_existing'];
                }

                $newEdPaths[] = $certPath;

                $newEds[] = [
                    'staff_id' => $employee->id,
                    'qualification' => $e['qualification'] ?? null,
                    'university' => $e['university'] ?? null,
                    'specialization' => $e['specialization'] ?? null,
                    'year' => $e['year'] ?? null,
                    'result' => $e['result'] ?? null,
                    'gpa' => $e['gpa'] ?? null,
                    'mode' => $e['mode'] ?? null,
                    'country' => $e['country'] ?? null,
                    'duration_years' => $e['duration'] ?? null,
                    'certificate_path' => $certPath,
                ];
            }

            EmployeeOnboardingEducation::where('staff_id', $employee->id)->delete();
            foreach ($newEds as $ne) {
                EmployeeOnboardingEducation::create($ne);
            }
            $toDeleteEd = array_diff($oldEdPaths, array_filter($newEdPaths));
            foreach ($toDeleteEd as $p) {
                if ($p && Storage::disk('public')->exists($p)) {
                    Storage::disk('public')->delete($p);
                }
            }

            // --- EXPERIENCES: rebuild list ---
            $exInput = $request->input('experiences', []);
            $exFiles = $request->file('experiences') ?: [];

            $oldEx = $employee->experiences()->get();
            $oldExPaths = $oldEx->pluck('certificate_path')->filter()->toArray();
            $newExPaths = [];

            $newExs = [];
            foreach ($exInput as $i => $ex) {
                $expCert = null;
                if (isset($exFiles[$i]) && isset($exFiles[$i]['certificate']) && $exFiles[$i]['certificate']) {
                    $f = $exFiles[$i]['certificate'];
                    $originalName = $f->getClientOriginalName();
                    $ext = $f->getClientOriginalExtension();
                    $base = pathinfo($originalName, PATHINFO_FILENAME);
                    $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
                    $candidate = $safeBase . '.' . $ext;
                    $folder = 'employee/experience_certificates';
                    $m = 0;
                    while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                        $m++;
                        $candidate = $safeBase . '_' . $m . '.' . $ext;
                    }
                    $expCert = $f->storeAs($folder, $candidate, 'public');
                } elseif (!empty($ex['certificate_existing'])) {
                    $expCert = $ex['certificate_existing'];
                }

                $newExPaths[] = $expCert;

                $newExs[] = [
                    'staff_id' => $employee->id,
                    'organization' => $ex['organization'] ?? null,
                    'designation' => $ex['designation'] ?? null,
                    'years' => $ex['years'] ?? null,
                    'months' => $ex['months'] ?? null,
                    'responsibilities' => $ex['responsibilities'] ?? null,
                    'certificate_path' => $expCert,
                ];
            }

            EmployeeOnboardingExperience::where('staff_id', $employee->id)->delete();
            foreach ($newExs as $ne) {
                EmployeeOnboardingExperience::create($ne);
            }
            $toDeleteEx = array_diff($oldExPaths, array_filter($newExPaths));
            foreach ($toDeleteEx as $p) {
                if ($p && Storage::disk('public')->exists($p)) {
                    Storage::disk('public')->delete($p);
                }
            }






            //sqave job details tab
            // small helpers contained in this function only
            $parseDate = function ($val) {
                $val = trim((string) ($val ?? ''));
                if ($val === '')
                    return null;
                try {
                    $d = \DateTime::createFromFormat('d/m/Y', $val);
                    if ($d)
                        return $d->format('Y-m-d');
                } catch (\Exception $e) {
                }
                try {
                    return (new Carbon($val))->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            };
            $storeFile = function ($file, $folder) {
                if (!$file)
                    return null;
                $orig = $file->getClientOriginalName();
                $ext = $file->getClientOriginalExtension();
                $base = pathinfo($orig, PATHINFO_FILENAME);
                $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
                $candidate = $safeBase . '.' . $ext;
                $n = 0;
                while (Storage::disk('public')->exists($folder . '/' . $candidate)) {
                    $n++;
                    $candidate = $safeBase . '_' . $n . '.' . $ext;
                }
                $path = $file->storeAs($folder, $candidate, 'public');
                return $path; // relative path on public disk
            };


            // Job details: update existing or create
            $job = OnboardEmployeeJobDetail::where('staff_id', $employee->id)->first();

            // Parse incoming dates and ensure probation is set when missing
            $doj = $parseDate($request->input('date_of_joining_2'));
            $probationFromRequest = $parseDate($request->input('probation_end_date'));
            if (!$probationFromRequest && $doj) {
                try {
                    $probationFromRequest = Carbon::createFromFormat('Y-m-d', $doj)->addMonths(6)->format('Y-m-d');
                } catch (\Exception $e) {
                    $probationFromRequest = null;
                }
            }

            $jobPayload = [
                'staff_id' => $employee->id,
                'date_of_joining' => $doj ?: null,
                'probation_end_date' => $probationFromRequest ?: null,
                'department_id' => $request->input('department_id') ?: null,
                'designation_id' => $request->input('designation_id') ?: null,
                'reporting_manager' => $request->input('reporting_manager') ?: null,
                'employment_type' => $request->input('employment_type') ?: null,
                'visa_company_name' => $request->input('visa_company_name') ?: null,
                'working_company_name' => $request->input('working_company_name') ?: null,
                'company_access' => is_array($request->input('company_access')) ? implode(',', $request->input('company_access')) : $request->input('company_access'),
                'ext_no' => $request->input('ext_no_2') ?: null,
                'company_email' => $request->input('company_email') ?: null,
                'company_mobile' => $request->input('company_mobile') ?: null,
                'work_location' => $request->input('work_location') ?: null,
                'work_hours' => $request->input('work_hours') ?: null,
                'week_off' => is_array($request->input('hr_weekly_off')) ? implode(',', $request->input('hr_weekly_off')) : $request->input('hr_weekly_off'),
                'salary_basic' => $this->numOrNull($request->input('salary_basic')) ?: null,
                'salary_allowances' => $this->numOrNull($request->input('salary_allowances')) ?: null,
                'salary_other_allowances' => $this->numOrNull($request->input('salary_other_allowances')) ?: null,
                'transport_allowance' => $this->numOrNull($request->input('transport_allowance')) ?: null,
                'other_benefits' => $this->numOrNull($request->input('other_benefits')) ?: null,
                'salary_gross' => $this->numOrNull($request->input('salary_gross')) ?: null,
                'is_target' => (int) ($request->input('is_target') ?: 0),
                'target_month_from' => $parseDate($request->input('target_month_from')) ?: null,
                'brand_ids' => $request->input('brands', []) ? implode(',', $request->input('brands', [])) : null,
                'role_id' => $request->input('role_id') ?: null,
                'grade' => $request->input('grade') ?: null,
                'shift_id' => $request->input('shift_id') ?: null,
            ];

      



            if ($request->filled('target_type') && $request->target_type != null && $request->target_type != "") {

                $jobPayload['target_type'] = $request->target_type;
            } else {
                $jobPayload['target_type'] = null;
            }

            if ($request->filled('target_period') && $request->target_period != null && $request->target_period != "") {
                $jobPayload['target_period'] = $request->target_period;
            } else {
                $jobPayload['target_period'] = null;
            }

            if ($request->filled('revenue_target') && $request->revenue_target != null && $request->revenue_target != "") {
                $jobPayload['revenue_target'] = $this->numOrNull($request->revenue_target);
            } else {
                $jobPayload['revenue_target'] = null;
            }

            if ($request->filled('gp_target') && $request->gp_target != null && $request->gp_target != "") {
                $jobPayload['gp_target'] = $this->numOrNull($request->gp_target);
            } else {
                $jobPayload['gp_target'] = null;
            }

            if ($request->filled('channel_distribution') && $request->channel_distribution != null && $request->channel_distribution != "") {
                $jobPayload['channel_distribution'] = $request->channel_distribution;
            } else {
                $jobPayload['channel_distribution'] = null;
            }

            if ($job) {
                $job->update($jobPayload);
            } else {
                $job = OnboardEmployeeJobDetail::create($jobPayload);
            }

            // Job attachments: replace only when new files are uploaded; otherwise keep existing
            if ($request->hasFile('att_resume')) {
                if ($job->att_resume && Storage::disk('public')->exists($job->att_resume)) {
                    Storage::disk('public')->delete($job->att_resume);
                }
                $job->att_resume = $storeFile($request->file('att_resume'), 'employee/job');
            }
            if ($request->hasFile('att_offer_letter')) {
                if ($job->att_offer_letter && Storage::disk('public')->exists($job->att_offer_letter)) {
                    Storage::disk('public')->delete($job->att_offer_letter);
                }
                $job->att_offer_letter = $storeFile($request->file('att_offer_letter'), 'employee/job');
            }
            if ($request->hasFile('att_signed_contract')) {
                if ($job->att_signed_contract && Storage::disk('public')->exists($job->att_signed_contract)) {
                    Storage::disk('public')->delete($job->att_signed_contract);
                }
                $job->att_signed_contract = $storeFile($request->file('att_signed_contract'), 'employee/job');
            }
            $job->save();

            //sqave job details tab

            $employee->save();
            DB::commit();
            Toastr::success('Onboarding employee updated successfully.');
            return redirect('onboarding-employee-list/' . $employee->id);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            Log::error('Onboard update error: ' . $e->getMessage(), ['exception' => $e]);
            Toastr::error('An error occurred while updating onboarding data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating onboarding data.');
        }
    }

    private function numOrNull($v)
    {
        return ($v === null || $v === '') ? null : (float) str_replace(',', '', $v);
    }

    private function calculateTargets($amount, $period)
    {
        if (!$amount || !$period) {
            return [
                'weekly' => null,
                'monthly' => null,
                'quarterly' => null,
                'yearly' => null,
            ];
        }

        switch ($period) {
            case 'weekly':
                return [
                    'weekly' => $amount,
                    'monthly' => $amount * 4.33,
                    'quarterly' => $amount * 13,
                    'yearly' => $amount * 52,
                ];

            case 'monthly':
                return [
                    'weekly' => $amount / 4.33,
                    'monthly' => $amount,
                    'quarterly' => $amount * 3,
                    'yearly' => $amount * 12,
                ];

            case 'quarterly':
                return [
                    'weekly' => $amount / 13,
                    'monthly' => $amount / 3,
                    'quarterly' => $amount,
                    'yearly' => $amount * 4,
                ];

            case 'halfyear':
                return [
                    'weekly' => $amount / 26,
                    'monthly' => $amount / 6,
                    'quarterly' => $amount / 2,
                    'yearly' => $amount * 2,
                ];

            case 'yearly':
                return [
                    'weekly' => $amount / 52,
                    'monthly' => $amount / 12,
                    'quarterly' => $amount / 4,
                    'yearly' => $amount,
                ];
        }
    }

       private function normalizeFirstName($value): ?string
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return null;
        }
        $parts = preg_split('/\s+/', $value);
        return $parts[0] ?? null;
    }


}

