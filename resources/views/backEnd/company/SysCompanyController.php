<?php

namespace App\Http\Controllers;

use App\SysCompany;
use App\SmCountry;
use App\SysCompanyCompliance;
use App\SysCompanyHrPolicy;
use App\SysCompanyBanking;
use App\SysCompanyPeople;
use App\SysCompanyPeopleDocument;
use App\CompanyHrPayroll;
use App\ApiBaseMethod;
use App\SmItem;
use App\SmStaff;
use App\User;
use Illuminate\Support\Facades\Storage;
use App\SysCities;
use App\SysCompanyDocument;
use App\SysCompanyDocumentItem;
use App\SysCompanySetting;
use App\SysCompanyHrPayrollSetting;
use App\SysStates;
use App\SysCountries;
use App\SysCurrency;
use App\SysHelper;
use App\SmStaffJobDetail;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Carbon\Carbon;
use App\WorkingShift;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\SmIndustry;
use App\SmBusinessActivity;
use App\SmBusinessEntityType;
use Illuminate\Support\Facades\Log;
use App\CompanyWarehouse;
use App\WeeklyOff;

use Illuminate\Support\Facades\Hash;



class SysCompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function companyList(Request $request)
    {
        try {
            $q = trim($request->get('q', ''));
            $code = trim($request->get('code', ''));
            $name = trim($request->get('name', ''));
            $country = trim($request->get('country', ''));

            // ---------------------------------------------
            // RELATIONS
            // ---------------------------------------------
            $query = SysCompany::with([
                'settings',
                'compliance',
                'banking',
                'warehouses',
                'hrPolicies',

                // People (filter in controller or blade)
                'people',

                // Location / master data
                'countryRelation',
                'stateRelation',
                'businessEntity',
                'businessIndustry',
                'businessSector',
                'parentCompany',
                'documentItems',
                'hrpayrollsettings'
            ]);

            // ---------------------------------------------
            // QUICK SEARCH
            // ---------------------------------------------
            if ($q !== '') {
                $query->where(function ($x) use ($q) {
                    $x->where('company_name', 'like', "%{$q}%")
                        ->orWhere('trade_name', 'like', "%{$q}%");

                    if (is_numeric($q)) {
                        $x->orWhere('id', (int) $q);
                    }
                });
            }

            // ---------------------------------------------
            // LONG FILTERS
            // ---------------------------------------------
            if ($code !== '') {
                if (is_numeric($code)) {
                    $query->where('id', (int) $code);
                } else {
                    $query->where('id', -1);
                }
            }

            if ($name !== '') {
                $query->where('company_name', 'like', "%{$name}%");
            }

            if ($country !== '') {
                $query->where('country', 'like', "%{$country}%");
            }

            // ---------------------------------------------
            // GET DATA
            // ---------------------------------------------
            $company = $query->orderBy('sort_id', 'asc')->get();
            $countryL = SysCountries::all();
            $currency = SysCurrency::all();

            // SELECTED COMPANY
            $selectedCompany = null;
            if ($request->filled('active')) {
                $selectedCompany = SysCompany::with([
                    // Core relations
                    'settings',
                    'compliance',
                    'banking',
                    'warehouses',
                    'hrPolicies',

                    // People (filter in controller or blade)
                    'people',

                    // Location / master data
                    'countryRelation',
                    'stateRelation',
                    'businessEntity',
                    'businessIndustry',
                    'businessSector',
                    'parentCompany',
                    'documentItems',
                    'hrpayrollsettings',
                    'documents',
                ])->find($request->input('active'));
            }


            // ---------------------------------------------
            // RETURN VIEW
            // ---------------------------------------------
            return view('backEnd.company.companyList', [
                'company' => $company,
                'country' => $countryL,
                'currency' => $currency,
                'selectedCompany' => $selectedCompany,
            ]);
        } catch (\Exception $e) {
            return $e;
        }
    }



    // AJAX details (partial)
    public function details($id)
    {
        $company = SysCompany::with([
            'compliance',
            'hrPolicies',
            'banking',
            'warehouses.country',
            'warehouses.state',
            'documents',
            'documentItems',
            'people',
            'countryRelation',
            'stateRelation',
            'businessEntity',
            'businessIndustry',
            'businessSector',
            'parentCompany',
            'settings'
        ])->findOrFail($id);

        return view('backEnd.company.company_details', compact('company'));
    }



    public function companyAdd(Request $request)
    {
        try {
            $company = null; // no company yet
            $parentCompanies = SysCompany::where('company_type', 'parent')->get();
            $country = SysCountries::all();
            $states = SysStates::all();
            $currency = SysCurrency::all();
            $industries = SmIndustry::orderBy('name')->get();
            $entities = SmBusinessEntityType::orderBy('name')->get();
            $activities = SmBusinessActivity::with('industry')->orderBy('name')->get();

            $latestId = SysCompany::max('id');
            $nextId = $latestId ? $latestId + 1 : 1;


            return view(
                'backEnd.company.addCompany',
                compact('company', 'country', 'currency', 'nextId', 'industries', 'activities', 'entities', 'parentCompanies', 'states')
            );
        } catch (\Exception $e) {
            dd($e);
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }




    public function set_company_id(Request $request)
    {
        try {
            $decimal = DB::table('sys_company')->select('decimal_point')->where('id', $request->companyid)->value('decimal_point');
            session(['logged_session_data.company_id' => $request->companyid]);
            session(['logged_session_data.decimal_point' => @$decimal]);
            $ret = session('logged_session_data.company_id');
            return json_encode(array('data' => $ret));
        } catch (\Exception $e) {
            $ret = 'ERROR';
            return json_encode(array('data' => $ret));
        }
    }

    public function store(Request $request)
    {
        $check = SysCompany::where('company_name', $request->company_name)->get();
        if (count($check) > 0) {
            Toastr::error('Company Name Already Exist!', 'Failed');
            return redirect()->back();
        }
        // return $request;
        $input = $request->all();
        $validator = Validator::make($input, [
            'company_name' => "required",
            'company_address' => "required",
            'country' => "required",
            'city' => "required",
            'email' => "required",
            'website' => "required",
            'telephone' => "required",
            'fax' => "required",
            'mobile' => "required",
            'vat_number' => "required",
            'trade_license_no' => "required",
            'trade_license_exp_date' => "required",
            'bank_name' => "required",
            'account_number' => "required",
            'sales_code' => "required",
            'other_code' => "required",
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $company_logo = "";
        if ($request->file('company_logo') != "") {
            $file = $request->file('company_logo');
            $company_logo = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('public/uploads/company/', $company_logo);
            $company_logo = 'public/uploads/company/' . $company_logo;
        }
        $digital_stamp = "";
        if ($request->file('digital_stamp') != "") {
            $file = $request->file('digital_stamp');
            $digital_stamp = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('public/uploads/company/', $digital_stamp);
            $digital_stamp = 'public/uploads/company/' . $digital_stamp;
        }

        try {
            $company = new SysCompany();

            $company->company_name = $request->company_name;
            $company->company_address = $request->company_address;
            $company->country = $request->country;
            $company->city = $request->city;
            $company->email = $request->email;
            $company->website = $request->website;
            $company->telephone = $request->telephone;
            $company->fax = $request->fax;
            $company->mobile = $request->mobile;
            $company->vat_number = $request->vat_number;
            $company->trade_license_no = $request->trade_license_no;
            $company->trade_license_exp_date = date('Y-m-d', strtotime($request->trade_license_exp_date));
            $company->company_vat_rate = $request->company_vat_rate;
            $company->bank_name = $request->bank_name;
            $company->account_number = $request->account_number;
            $company->iban_no = $request->iban_no;
            $company->finance_code = $request->finance_code;
            $company->branch_swift_code = $request->branch_swift_code;
            $company->company_logo = $company_logo;
            $company->digital_stamp = $digital_stamp;
            $company->sales_code = $request->sales_code;
            $company->other_code = $request->other_code;
            $company->currency_id = $request->currency_id;
            $company->status = 1;
            $company->created_by = Auth::user()->id;

            $results = $company->save();



            // Log staff activity: who created this company (if a staff record exists for current user)
            try {
                $staff = \App\SmStaff::where('user_id', Auth::id())->first();
                if ($staff) {
                    \App\StaffActivity::create([
                        'staff_id' => $staff->id,
                        'doc_number' => $company->id,
                        'type' => 'company_create',
                        'message' => 'Created company: ' . ($company->company_name ?? 'N/A') . ' (ID: ' . $company->id . ')',
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('StaffActivity create error (company create): ' . $e->getMessage());
            }

            $company_ids1 = SysCompany::select('id')->pluck('id');
            $company_ids2 = str_replace('[', '', $company_ids1);
            $company_ids = str_replace(']', '', $company_ids2);
            SmItem::where('status', 1)->update(['company_id' => $company_ids]);

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($results) {
                    return ApiBaseMethod::sendResponse(null, 'New Company has been added successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {
                if ($results) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
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

    public function editbkp(Request $request, $id)
    {
        try {
            $editData = SysCompany::find($id);
            $company = SysCompany::orderby('sort_id', 'asc')->get();
            $country = SysCountries::all();
            $currency = SysCurrency::all();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['editData'] = $editData->toArray();
                $data['company'] = $company->toArray();
                $data['country'] = $country->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.company.companyAdd', compact('editData', 'company', 'country', 'currency'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'company_name' => "required",
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try {

            $company_logo = "";
            if ($request->file('company_logo') != "") {
                $photos = SysCompany::find($id);
                if ($photos->company_logo != '' && file_exists($photos->company_logo)) {
                    unlink($photos->company_logo);
                }
                $file = $request->file('company_logo');
                $company_logo = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/staff/', $company_logo);
                $company_logo = 'public/uploads/staff/' . $company_logo;
            } else {
                $photos = SysCompany::find($id);
                $company_logo = $photos->company_logo;
            }

            $digital_stamp = "";
            if ($request->file('digital_stamp') != "") {
                $photos = SysCompany::find($id);
                if ($photos->digital_stamp != '' && file_exists($photos->digital_stamp)) {
                    unlink($photos->digital_stamp);
                }
                $file = $request->file('digital_stamp');
                $digital_stamp = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/staff/', $digital_stamp);
                $digital_stamp = 'public/uploads/staff/' . $digital_stamp;
            } else {
                $photos = SysCompany::find($id);
                $digital_stamp = $photos->digital_stamp;
            }



            $company = SysCompany::find($id);
            $company->company_name = $request->company_name;
            $company->company_address = $request->company_address;
            $company->country = $request->country;
            $company->city = $request->city;
            $company->email = $request->email;
            $company->website = $request->website;
            $company->telephone = $request->telephone;
            $company->fax = $request->fax;
            $company->mobile = $request->mobile;
            $company->vat_number = $request->vat_number;
            $company->trade_license_no = $request->trade_license_no;
            $company->trade_license_exp_date = SysHelper::normalizeToYmd($request->trade_license_exp_date);
            $company->company_vat_rate = $request->company_vat_rate;
            $company->bank_name = $request->bank_name;
            $company->account_number = $request->account_number;
            $company->iban_no = $request->iban_no;
            $company->finance_code = $request->finance_code;
            $company->branch_swift_code = $request->branch_swift_code;
            $company->company_logo = $company_logo;
            $company->digital_stamp = $digital_stamp;
            $company->sales_code = $request->sales_code;
            $company->other_code = $request->other_code;
            $company->currency_id = $request->currency_id;
            //$company->company_logo = $request->company_logo;
            //$company->digital_stamp = $request->digital_stamp;
            $company->updated_by = Auth()->user()->id;
            $results = $company->update();

            // Log staff activity: who updated this company (if a staff record exists for current user)
            try {
                $staff = \App\SmStaff::where('user_id', Auth::id())->first();
                if ($staff && $results) {
                    \App\StaffActivity::create([
                        'staff_id' => $staff->id,
                        'doc_number' => $company->id,
                        'type' => 'company_update',
                        'message' => 'Updated company: ' . ($company->company_name ?? 'N/A') . ' (ID: ' . $company->id . ')',
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('StaffActivity create error (company update): ' . $e->getMessage());
            }

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($results) {
                    return ApiBaseMethod::sendResponse(null, 'Company has been updated successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again');
                }
            } else {
                if ($results) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('company');
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


    public function delete(Request $request, $id)
    {

        //  try{
        //     if (ApiBaseMethod::checkUrl($request->fullUrl())) {
        //         return ApiBaseMethod::sendResponse($id, null);
        //     }
        //      return view('backEnd.inventory.deleteSupportView', compact('id'));
        // }catch (\Exception $e) {
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back();
        //}
    }

    // public function deleteSupplier(Request $request,$id){

    //     try{
    //         $result = SmSupplier::destroy($id);

    //         if (ApiBaseMethod::checkUrl($request->fullUrl())) {
    //             if ($result) {
    //                 return ApiBaseMethod::sendResponse(null, 'Supplier has been deleted successfully');
    //             } else {
    //                 return ApiBaseMethod::sendError('Something went wrong, please try again.');
    //             }
    //         } else {
    //             if ($result) {
    //                 Toastr::success('Operation successful', 'Success');
    //                 return redirect('suppliers');
    //             } else {
    //                 Toastr::error('Operation Failed', 'Failed');
    //                 return redirect()->back();
    //             }
    //         }
    //     }catch (\Exception $e) {
    //        Toastr::error('Operation Failed', 'Failed');
    //        return redirect()->back(); 
    //     }
    // }


    //adil

    private function parseUiDate($date)
    {
        if (!$date || trim($date) == '') {
            return null;
        }

        try {
            return \Carbon\Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }


    private function resolveCompanyOrFail(Request $request): SysCompany
    {
        // prefer explicit hidden field from the UI, else fall back to session anchor
        $id = $request->input('company_id') ?: session('current_company_id');

        abort_unless($id, 422, 'No company selected. Please save Basic Company info first.');
        return SysCompany::findOrFail($id);
    }


    private function savePersonDocuments($peopleId, $documents, $request, $baseKey)
    {
        $docDir = public_path('uploads/company/people_docs');
        if (!file_exists($docDir))
            mkdir($docDir, 0777, true);

        foreach ($documents as $docIndex => $docData) {
            $fileKey = "$baseKey.$docIndex.attachment";

            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);
                $name = 'person_doc_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($docDir, $name);

                SysCompanyPeopleDocument::create([
                    'people_id' => $peopleId,
                    'document_type' => $docData['document_type'] ?? 'Other',
                    'document_name' => $docData['document_name'] ?? 'Document',
                    'issue_date' => !empty($docData['issue_date']) ?
                        $this->parseUiDate($docData['issue_date']) : null,
                    'expiry_date' => !empty($docData['expiry_date']) ?
                        $this->parseUiDate($docData['expiry_date']) : null,
                    'file_path' => 'uploads/company/people_docs/' . $name,
                ]);
            }
        }
    }



    public function storeContact(Request $request)
    {
        // Validate request
        $rules = [
            'company_id' => 'required|exists:sys_company,id',

            'email' => 'nullable|email',
            'website' => 'nullable',
            'telephone' => 'nullable|string|max:100',
            'fax' => 'nullable|string|max:100',
            'mobile' => 'nullable|string|max:100',
            'country' => 'nullable',
            'state' => 'nullable',
            'company_address' => 'nullable|string',

            // Owners
            'owners' => 'nullable|array',
            'owners.*.salutation' => 'nullable|string|max:200',
            'owners.*.first_name' => 'nullable|string|max:255',
            'owners.*.last_name' => 'nullable|string|max:255',
            'owners.*.mobile' => 'nullable|string|max:50',
            'owners.*.email' => 'nullable|email',
            'owners.*.share_percentage' => 'nullable|string|max:255',
            'owners.*.designation_id' => 'nullable|exists:sm_designations,id',



            // Sponsors
            'sponsors' => 'nullable|array',
            'sponsors.*.salutation' => 'nullable|string|max:200',
            'sponsors.*.first_name' => 'nullable|string|max:255',
            'sponsors.*.last_name' => 'nullable|string|max:255',
            'sponsors.*.mobile' => 'nullable|string|max:50',
            'sponsors.*.email' => 'nullable|email',

            // Contact people
            'contacts' => 'nullable|array',
            'contacts.*.salutation' => 'nullable|string|max:200',
            'contacts.*.first_name' => 'nullable|string|max:255',
            'contacts.*.last_name' => 'nullable|string|max:255',
            'contacts.*.mobile' => 'nullable|string|max:50',
            'contacts.*.email' => 'nullable|email',
            'contacts.*.designation' => 'nullable|string|max:255'

        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
        }

        // Debug: Log full session data at start
        Log::info("Full session data at start of storeContact:");
        Log::info("All session data: " . json_encode($request->session()->all()));

        // Check specifically for documents in session with different possible keys
        $sessionKeys = ['documentSessions', 'documents_temp_' . $request->company_id, 'documents_temp_new'];
        foreach ($sessionKeys as $sessionKey) {
            Log::info("Checking session key: {$sessionKey}");
            Log::info("Data in {$sessionKey}: " . json_encode($request->session()->get($sessionKey, [])));
        }

        $company = SysCompany::findOrFail($request->company_id);

        // Convert empty strings to NULL for integer fields
        $contactData = [
            'email' => $request->email,
            'website' => $request->website,
            'telephone' => $request->telephone,
            'fax' => $request->fax,
            'mobile' => $request->mobile,
            'country' => $request->country === '' ? null : $request->country,
            'state' => $request->state === '' ? null : $request->state,
            'company_address' => $request->company_address,

            // Social media
            'linkedin' => $request->linkedin,
            'facebook' => $request->facebook,
            'instagram' => $request->instagram,
            'twitter_x' => $request->twitter_x,
            'youtube' => $request->youtube,
            'other_social' => $request->other_social,
        ];

        // Update basic contact fields
        $company->update($contactData);

        // SysHelper::logStaffActivity("Updated contact info for company name: " . $company->company_name, "company", $company->id);

        // Prepare upload folder
        $dir = public_path('uploads/company');
        if (!file_exists($dir))
            mkdir($dir, 0777, true);

        // Helper to upload files
        $saveFile = function ($file) use ($dir) {
            if (!$file)
                return null;
            $name = uniqid("file_") . "." . $file->getClientOriginalExtension();
            $file->move($dir, $name);
            return "uploads/company/" . $name;
        };

        // Delete old people rows (documents are stored directly in this table)
        DB::table('sys_company_people')->where('company_id', $company->id)->delete();

        // Helper function to get document data from session
        $getDocumentsFromSession = function ($type, $index) {
            try {
                // Get documents from session
                $sessionKey = 'documentSessions';
                $documents = session($sessionKey, []);

                Log::info("Getting documents for type: {$type}, index: {$index}");
                Log::info("Session data: " . json_encode($documents));

                $documentData = [
                    'document_name' => null,
                    'document_no' => null,
                    'issue_date' => null,
                    'expiry_date' => null,
                    'attachment' => null
                ];

                if (isset($documents[$type][$index]) && is_array($documents[$type][$index]) && !empty($documents[$type][$index])) {
                    // Get the first document from the session (assuming one document per person for now)
                    $doc = $documents[$type][$index][0];
                    $docArray = is_array($doc) ? $doc : (array) $doc;

                    Log::info("Processing document: " . json_encode($docArray));

                    // Map session field names to database field names
                    $documentData['document_name'] = $docArray['name'] ?? null;          // session uses 'name'
                    $documentData['document_no'] = $docArray['number'] ?? null;         // session uses 'number'

                    // Handle issue date - session uses 'date' field
                    if (!empty($docArray['date']) && $docArray['date'] !== '') {
                        try {
                            // Try multiple date formats
                            $issueDate = null;
                            $dateFormats = ['Y-m-d', 'd-m-Y', 'd/m/Y'];
                            foreach ($dateFormats as $format) {
                                try {
                                    $parsedDate = \DateTime::createFromFormat($format, $docArray['date']);
                                    if ($parsedDate) {
                                        $issueDate = $parsedDate->format('Y-m-d');
                                        break;
                                    }
                                } catch (\Exception $e) {
                                    continue;
                                }
                            }
                            $documentData['issue_date'] = $issueDate;
                        } catch (\Exception $e) {
                            Log::warning("Invalid issue date: " . $docArray['date']);
                        }
                    }

                    // Handle expiry date
                    if (!empty($docArray['expiry_date']) && $docArray['expiry_date'] !== '') {
                        try {
                            // Try multiple date formats
                            $expiryDate = null;
                            $dateFormats = ['Y-m-d', 'd-m-Y', 'd/m/Y'];
                            foreach ($dateFormats as $format) {
                                try {
                                    $parsedDate = \DateTime::createFromFormat($format, $docArray['expiry_date']);
                                    if ($parsedDate) {
                                        $expiryDate = $parsedDate->format('Y-m-d');
                                        break;
                                    }
                                } catch (\Exception $e) {
                                    continue;
                                }
                            }
                            $documentData['expiry_date'] = $expiryDate;
                        } catch (\Exception $e) {
                            Log::warning("Invalid expiry date: " . $docArray['expiry_date']);
                        }
                    }



                    // Handle attachment file
                    $attachmentPath = $docArray['attachment'] ?? '';
                    if (!empty($attachmentPath)) {
                        $tempPath = public_path($attachmentPath);
                        if (file_exists($tempPath)) {
                            $permanentDir = public_path('uploads/company/people');
                            if (!is_dir($permanentDir)) {
                                mkdir($permanentDir, 0777, true);
                            }

                            $fileName = basename($attachmentPath);
                            $permanentPath = $permanentDir . '/' . $fileName;

                            if (rename($tempPath, $permanentPath)) {
                                $documentData['attachment'] = "uploads/company/people/{$fileName}";
                            }
                        } else {
                            // If temp file doesn't exist, use the path as is (might be already permanent)
                            $documentData['attachment'] = $attachmentPath;
                        }
                    }

                    Log::info("Document data prepared: " . json_encode($documentData));
                }

                return $documentData;

            } catch (\Exception $e) {
                Log::error("Error getting documents from session: " . $e->getMessage());
                return [
                    'document_name' => null,
                    'document_no' => null,
                    'issue_date' => null,
                    'expiry_date' => null,
                    'attachment' => null
                ];
            }
        };

        // ---------- OWNERS ----------
        if ($request->owners) {
            Log::info("Processing " . count($request->owners) . " owners");
            foreach ($request->owners as $i => $o) {
                // Get document data from session for this owner
                $documentData = $getDocumentsFromSession('owner', $i);

                Log::info("Owner {$i} document data: " . json_encode($documentData));

                // Prepare person data (without document fields)
                $personData = [
                    'company_id' => $company->id,
                    'type' => 'owner',
                    'salutation' => $o['salutation'] ?? null,
                    'first_name' => $o['first_name'] ?? null,
                    'last_name' => $o['last_name'] ?? null,
                    'name' => trim(($o['first_name'] ?? '') . ' ' . ($o['last_name'] ?? '')),
                    'mobile' => $o['mobile'] ?? null,
                    'email' => $o['email'] ?? null,
                    'share_percentage' => $o['share_percentage'] ?? null,
                ];

                // Map designation_id (if submitted) to designation title and save as 'designation' string
                try {
                    $designationTitle = null;
                    if (!empty($o['designation_id'])) {
                        $des = \App\SmDesignation::find($o['designation_id']);
                        if ($des)
                            $designationTitle = $des->title;
                    } elseif (!empty($o['designation'])) {
                        // legacy: if raw designation string provided
                        $designationTitle = $o['designation'];
                    }
                    $personData['designation'] = $designationTitle;
                } catch (\Exception $e) {
                    Log::warning('Could not map designation id to title: ' . $e->getMessage());
                    $personData['designation'] = $o['designation'] ?? null;
                }

                Log::info("Inserting owner data: " . json_encode($personData));

                // Create person record
                $person = SysCompanyPeople::create($personData);

                Log::info("Owner inserted with ID: {$person->id}");

                // Save document data to separate table if exists
                if (!empty($documentData['document_name']) || !empty($documentData['attachment'])) {
                    $docData = [
                        'people_id' => $person->id,
                        'document_name' => $documentData['document_name'],
                        'document_no' => $documentData['document_no'],
                        'issue_date' => $documentData['issue_date'],
                        'expiry_date' => $documentData['expiry_date'],
                        'attachment' => $documentData['attachment'],
                    ];

                    SysCompanyPeopleDocument::create($docData);
                    Log::info("Owner document saved for person ID: {$person->id}");
                }
            }
        }

        // ---------- SPONSORS ----------
        if ($request->sponsors) {
            Log::info("Processing " . count($request->sponsors) . " sponsors");
            foreach ($request->sponsors as $i => $s) {
                // Get document data from session for this sponsor
                $documentData = $getDocumentsFromSession('sponsor', $i);

                Log::info("Sponsor {$i} document data: " . json_encode($documentData));

                $insertData = [
                    'company_id' => $company->id,
                    'type' => 'sponsor',
                    'salutation' => $s['salutation'] ?? null,
                    'first_name' => $s['first_name'] ?? null,
                    'last_name' => $s['last_name'] ?? null,
                    'name' => trim(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? '')),
                    'mobile' => $s['mobile'] ?? null,
                    'email' => $s['email'] ?? null,
                ];

                Log::info("Inserting sponsor data: " . json_encode($insertData));

                // Create person record
                $person = SysCompanyPeople::create($insertData);

                Log::info("Sponsor inserted with ID: {$person->id}");

                // Save document data to separate table if exists
                if (!empty($documentData['document_name']) || !empty($documentData['attachment'])) {
                    $docData = [
                        'people_id' => $person->id,
                        'document_name' => $documentData['document_name'],
                        'document_no' => $documentData['document_no'],
                        'issue_date' => $documentData['issue_date'],
                        'expiry_date' => $documentData['expiry_date'],
                        'attachment' => $documentData['attachment'],
                    ];

                    SysCompanyPeopleDocument::create($docData);
                    Log::info("Sponsor document saved for person ID: {$person->id}");
                }
            }
        }

        // ---------- CONTACT PERSON ----------
        if ($request->contacts) {
            Log::info("Processing " . count($request->contacts) . " contacts");
            foreach ($request->contacts as $i => $c) {
                // Get document data from session for this contact
                $documentData = $getDocumentsFromSession('contact', $i);

                Log::info("Contact {$i} document data: " . json_encode($documentData));

                $insertData = [
                    'company_id' => $company->id,
                    'type' => 'contact',
                    'salutation' => $c['salutation'] ?? null,
                    'first_name' => $c['first_name'] ?? null,
                    'last_name' => $c['last_name'] ?? null,
                    'name' => trim(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? '')),
                    'mobile' => $c['mobile'] ?? null,
                    'email' => $c['email'] ?? null,
                    'designation' => $c['designation'] ?? null,
                ];

                Log::info("Inserting contact data: " . json_encode($insertData));

                // Create person record
                $person = SysCompanyPeople::create($insertData);

                Log::info("Contact inserted with ID: {$person->id}");

                // Save document data to separate table if exists
                if (!empty($documentData['document_name']) || !empty($documentData['attachment'])) {
                    $docData = [
                        'people_id' => $person->id,
                        'document_name' => $documentData['document_name'],
                        'document_no' => $documentData['document_no'],
                        'issue_date' => $documentData['issue_date'],
                        'expiry_date' => $documentData['expiry_date'],
                        'attachment' => $documentData['attachment'],
                    ];

                    SysCompanyPeopleDocument::create($docData);
                    Log::info("Contact document saved for person ID: {$person->id}");
                }
            }
        }

        // Log final session state before clearing
        $allSessionData = session('documentSessions', []);
        Log::info("Final session data before clearing: " . json_encode($allSessionData));

        // Clear document sessions after saving
        session()->forget('documentSessions');

        return response()->json([
            'ok' => true,
            'message' => 'Contact Information saved successfully'
        ]);
    }


    public function storeCompliance(Request $request)
    {
        try {
            // Get company to check country
            $company = SysCompany::find($request->company_id);
            if (!$company) {
                return response()->json(['ok' => false, 'errors' => ['company_id' => ['Company not found']]], 422);
            }

            // Check if UAE (country_id = 231)
            $isUAE = $company->country == '231';

            if (!$isUAE) {
                // For non-UAE countries, get document data from session
                $complianceDocuments = session('company_compliance_documents', []);

                Log::info("Non-UAE compliance - Country: {$company->country}, Documents in session: " . count($complianceDocuments));

                // Initialize compliance data with safe defaults for non-UAE (only required fields)
                $data = [
                    'company_id' => $request->company_id,
                    'trade_license_no' => null,
                    'license_issue_date' => null,
                    'license_expiry_date' => null,
                    'issuing_authority' => null,
                    'attachment' => null, // File upload field for non-UAE
                ];

                // Process compliance documents from session if available
                if (!empty($complianceDocuments) && is_array($complianceDocuments)) {
                    foreach ($complianceDocuments as $doc) {
                        // Map document fields to compliance fields safely
                        if (!empty($doc['document_number'])) {
                            $data['trade_license_no'] = substr($doc['document_number'], 0, 150); // Ensure field length
                        }

                        if (!empty($doc['issuing_authority'])) {
                            $data['issuing_authority'] = substr($doc['issuing_authority'], 0, 150);
                        }

                        // Handle dates with validation
                        if (!empty($doc['issue_date'])) {
                            $parsedDate = $this->toSqlDate($doc['issue_date']);
                            if ($parsedDate) {
                                $data['license_issue_date'] = $parsedDate;
                            }
                        }

                        if (!empty($doc['expiry_date'])) {
                            $parsedDate = $this->toSqlDate($doc['expiry_date']);
                            if ($parsedDate) {
                                $data['license_expiry_date'] = $parsedDate;
                            }
                        }

                        // Handle file attachment safely
                        if (!empty($doc['attachment'])) {
                            $attachmentPath = $doc['attachment'];
                            $tempPath = public_path($attachmentPath);

                            if (file_exists($tempPath)) {
                                $permanentDir = public_path('uploads/company');
                                if (!is_dir($permanentDir)) {
                                    mkdir($permanentDir, 0777, true);
                                }

                                $fileName = basename($attachmentPath);
                                $permanentPath = $permanentDir . '/' . $fileName;

                                if (rename($tempPath, $permanentPath)) {
                                    $data['attachment'] = "uploads/company/{$fileName}";
                                }
                            }
                        }

                        // For non-UAE, we typically save the first/main document
                        // If multiple documents are needed, they can be saved separately
                        break;
                    }

                    Log::info("Non-UAE compliance data mapped: " . json_encode($data));
                }

                // Save compliance record for non-UAE country
                $compliance = SysCompanyCompliance::updateOrCreate(
                    ['company_id' => $request->company_id],
                    $data
                );

                // Clear compliance documents from session after successful save
                session()->forget('company_compliance_documents');

                Log::info("Non-UAE compliance saved successfully with ID: " . $compliance->id);

                return response()->json([
                    'ok' => true,
                    'compliance_id' => $compliance->id,
                    'message' => 'Non-UAE compliance data saved successfully'
                ]);
            }

            // UAE-specific validation and processing (existing logic)
            $rules = [
                'company_id' => 'exists:sys_company,id',
                'trade_license_no' => 'nullable|string|max:150',

                'license_issue_date' => 'nullable|string',
                'license_expiry_date' => 'nullable|string',

                'issuing_authority' => 'nullable|string|max:150',

                'tax_applicable' => 'nullable|in:vat,ct,both,none',

                'vat_registration_number' => 'nullable|string|max:150',
                'vat_percentage' => 'nullable|numeric|min:0|max:100',
                'vat_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',

                'corporate_tax_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',

                'corporate_tax_number' => 'nullable|string|max:150',
                'corporate_tax_date' => 'nullable|string',
                'corporate_tax_vat' => 'nullable|string',
                'ct_issuing_authority' => 'nullable|string|max:150',

                'vat_date' => 'nullable|string',
                'vat_issuing_authority' => 'nullable|string|max:150',
            ];

            $v = Validator::make($request->all(), $rules);

            // DATE FORMAT CHECK
            $v->after(function ($v) use ($request) {
                foreach (['license_issue_date', 'license_expiry_date', 'corporate_tax_date', 'vat_date'] as $field) {
                    $val = $request->input($field);
                    if ($val && !$this->toSqlDate($val)) {
                        $v->errors()->add($field, 'Invalid date. Use DD/MM/YYYY or DD-MM-YYYY.');
                    }
                }
            });

            if ($v->fails()) {
                return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
            }

            // BUILD PAYLOAD FOR UAE
            // Persist VAT/CT fields directly from request so values are not unintentionally dropped.
            $data = [
                'trade_license_no' => $request->trade_license_no,

                'license_issue_date' => $this->toSqlDate($request->license_issue_date),
                'license_expiry_date' => $this->toSqlDate($request->license_expiry_date),

                'issuing_authority' => $request->issuing_authority,

                // VAT (save values if present)
                'vat_registration_number' => $request->vat_registration_number ?: null,
                'vat_percentage' => $request->vat_percentage ?: null,
                'vat_date' => $this->toSqlDate($request->vat_date),
                'vat_issuing_authority' => $request->vat_issuing_authority ?: null,

                // CT (save values if present)
                'corporate_tax_number' => $request->corporate_tax_number ?: null,
                'corporate_tax_date' => $this->toSqlDate($request->corporate_tax_date),
                'corporate_tax_vat' => $request->corporate_tax_vat ?: null,
                // Accept either 'corporate_issuing_authority' or older 'ct_issuing_authority' key
                'corporate_issuing_authority' => $request->corporate_issuing_authority ?? $request->ct_issuing_authority ?? null,
            ];

            // FILES
            $dir = public_path('uploads/company');
            if (!is_dir($dir))
                @mkdir($dir, 0777, true);

            if ($request->hasFile('vat_certificate')) {
                $f = $request->file('vat_certificate');
                $name = uniqid('vat_') . '.' . $f->getClientOriginalExtension();
                $f->move($dir, $name);
                $data['vat_certificate'] = "uploads/company/$name";
            }

            if ($request->hasFile('corporate_tax_certificate')) {
                $f = $request->file('corporate_tax_certificate');
                $name = uniqid('ct_') . '.' . $f->getClientOriginalExtension();
                $f->move($dir, $name);
                $data['corporate_tax_certificate'] = "uploads/company/$name";
            }

            // SAVE UAE COMPLIANCE (fixed key)
            $compliance = SysCompanyCompliance::updateOrCreate(
                ['company_id' => $request->company_id],  // ✔ CORRECT
                $data
            );

            return response()->json(['ok' => true, 'compliance_id' => $compliance->id]);

        } catch (\Exception $e) {
            Log::error('storeCompliance error: ' . $e->getMessage(), [
                'company_id' => $request->company_id,
                'country' => $company->country ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Error saving compliance data: ' . $e->getMessage()
            ], 500);
        }
    }



    // Convert date to SQL format
    protected function toSqlDate($value)
    {
        if (!$value)
            return null;

        $value = trim($value);
        $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'm/d/Y'];

        foreach ($formats as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, $value)->format('Y-m-d');
            } catch (\Exception $e) {
            }
        }

        // last resort parse
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function storeHrPolicies(Request $request)
    {
        $companyId = (int) $request->input('company_id');

        if (!$companyId) {
            return response()->json([
                'ok' => false,
                'errors' => ['company_id' => ['Company not found. Save basic info first.']]
            ], 422);
        }

        // Convert simple fields to array for backend validation
        $policy = [
            'policy_date' => $request->input('policy_date'),
            'policy_name' => $request->input('policy_name'),
            'policy_category' => $request->input('policy_category'),
            'policy_valid' => $request->input('policy_valid'),
            'view_to_employees' => $request->input('view_to_employees'),
            'policy_details' => $request->input('policy_details'),
            'policy_file' => $request->file('policy_file') // simple name only
        ];

        $request->merge(['policies' => [$policy]]);

        // VALIDATION
        $validator = Validator::make($request->all(), [
            'policies' => 'required|array|min:1',
            'policies.0.policy_date' => 'required|date',
            'policies.0.policy_name' => 'required|string|max:255',
            'policies.0.policy_category' => 'nullable|string|max:50',
            'policies.0.policy_valid' => 'nullable|date',
            'policies.0.view_to_employees' => 'required|in:0,1',
            'policies.0.policy_details' => 'nullable|string',
            'policy_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // FILE UPLOAD
        $filePath = null;

        if ($request->hasFile('policy_file')) {
            $file = $request->file('policy_file');
            $filename = uniqid('policy_') . '.' . $file->getClientOriginalExtension();

            $path = public_path('uploads/company/hr_policies');
            if (!file_exists($path))
                mkdir($path, 0777, true);

            $file->move($path, $filename);
            $filePath = 'uploads/company/hr_policies/' . $filename;
        }

        // SAVE
        $policy = SysCompanyHrPolicy::create([
            'company_id' => $companyId,
            'policy_date' => $request->input('policy_date'),
            'policy_name' => $request->input('policy_name'),
            'policy_category' => $request->input('policy_category'),
            'policy_valid' => $request->input('policy_valid'),
            'view_to_employees' => (int) $request->input('view_to_employees'),
            'policy_details' => $request->input('policy_details'),
            'policy_file' => $filePath
        ]);

        return response()->json([
            'ok' => true,
            'id' => $policy->id
        ]);
    }




    public function getHrPolicies($companyId)
    {
        $policies = SysCompanyHrPolicy::where('company_id', $companyId)
            ->orderBy('id', 'desc')
            ->get();

        // Generate ONLY the table rows
        $html = '';

        if (count($policies)) {

            foreach ($policies as $p) {
                $html .= '
            <tr>
                <td>' . $p->policy_date . '</td>
                <td>' . $p->policy_name . '</td>
                <td>' . ($p->policy_category ?: '-') . '</td>
                <td>' . ($p->policy_valid ?: '-') . '</td>
                <td>' . ($p->view_to_employees ? "Yes" : "No") . '</td>
                <td>' . basename($p->policy_file) . '</td>
                <td>
                    <button class="btn btn-sm btn-danger deletePolicyBtn" data-id="' . $p->id . '">
                        <i class="ico icon-bold-trash-bin-minimalistic-2"></i>
                    </button>
                </td>
            </tr>';
            }
        } else {
            $html = '
        <tr>
            <td colspan="7" class="text-center text-muted">No policies added yet.</td>
        </tr>';
        }

        return response()->json([
            'html' => $html
        ]);
    }





    /**
     * Accepts d/m/Y, d-m-Y, Y-m-d, m/d/Y and returns Y-m-d or null.
     */
    protected function hrpToSqlDate($value)
    {
        if (!$value)
            return null;
        $value = trim($value);
        $fmts = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y'];
        foreach ($fmts as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, $value)->format('Y-m-d');
            } catch (\Exception $e) {
            }
        }
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    // banks


    public function storeBanking(Request $request)
    {
        $rules = [
            'company_id' => 'required|exists:sys_company,id',
            'bank_name' => 'required|string|max:150',
            'branch_name' => 'nullable|string|max:150',
            'account_number' => 'required|string|max:100',
            'iban_number' => 'required|string|max:100',
            'swift_code' => 'nullable|string|max:50',
            'finance_code' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:10',
            'bank_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => $v->errors()
            ], 422);
        }

        // ================================
        // CREATE OR UPDATE
        // ================================
        if ($request->bank_id) {
            $bank = SysCompanyBanking::find($request->bank_id);

            if (!$bank) {
                return response()->json(['ok' => false, 'msg' => 'Bank record not found'], 404);
            }

            $bank->update([
                'bank_name' => $request->bank_name,
                'branch_name' => $request->branch_name,
                'account_number' => $request->account_number,
                'iban_number' => $request->iban_number,
                'swift_code' => $request->swift_code,
                'finance_code' => $request->finance_code,
                'currency' => $request->currency,
            ]);
        } else {

            $bank = SysCompanyBanking::create([
                'company_id' => $request->company_id,
                'bank_name' => $request->bank_name,
                'branch_name' => $request->branch_name,
                'account_number' => $request->account_number,
                'iban_number' => $request->iban_number,
                'swift_code' => $request->swift_code,
                'finance_code' => $request->finance_code,
                'currency' => $request->currency,
            ]);
        }

        // ================================
        // FILE UPLOAD
        // ================================
        if ($request->hasFile('bank_letter')) {
            $path = $request->file('bank_letter')->store(
                'uploads/company/banking_letters',
                'public'
            );

            $bank->bank_letter = $path;
            $bank->save();
        }

        return response()->json([
            'ok' => true,
            'bank' => $bank,
            'msg' => 'Bank saved successfully'
        ]);
    }




    /**
     * Save bank_letter from multipart (banks[i][bank_letter]) OR base64 data URL.
     * Returns relative path like 'uploads/company/banking_letters/xxxx.pdf'
     */
    private function saveBankLetter(Request $request, $i, $baseDir, $maybeDataUrl)
    {
        $fileKey = "banks.$i.bank_letter";

        // Multipart upload
        if ($request->hasFile($fileKey)) {
            $f = $request->file($fileKey);
            $ext = strtolower($f->getClientOriginalExtension() ?: 'bin');
            if (!in_array($ext, array('pdf', 'jpg', 'jpeg', 'png', 'webp'))) {
                $ext = 'bin';
            }
            // UUID available in 5.8; fallback to uniqid if needed
            $name = (method_exists(Str::class, 'uuid') ? Str::uuid()->toString() : uniqid('bl_')) . '.' . $ext;
            $f->move($baseDir, $name);

            return 'uploads/company/banking_letters/' . $name;
        }

        // Base64 data URL
        if (is_string($maybeDataUrl) && preg_match('/^data:(application\/pdf|image\/jpe?g|image\/png|image\/webp);base64,/', $maybeDataUrl, $m)) {
            $mime = $m[1];
            $ext = 'bin';
            if ($mime === 'application/pdf')
                $ext = 'pdf';
            else if ($mime === 'image/png')
                $ext = 'png';
            else if ($mime === 'image/webp')
                $ext = 'webp';
            else if ($mime === 'image/jpeg' || $mime === 'image/jpg')
                $ext = 'jpg';

            $commaPos = strpos($maybeDataUrl, ',');
            $b64 = substr($maybeDataUrl, $commaPos + 1);
            $binary = base64_decode($b64, true);

            $name = (method_exists(Str::class, 'uuid') ? Str::uuid()->toString() : uniqid('bl_')) . '.' . $ext;
            file_put_contents($baseDir . DIRECTORY_SEPARATOR . $name, $binary);

            return 'uploads/company/banking_letters/' . $name;
        }

        // Should not reach here due to validation
        $name = uniqid('bl_') . '.bin';
        file_put_contents($baseDir . DIRECTORY_SEPARATOR . $name, '');
        return 'uploads/company/banking_letters/' . $name;
    }


    public function storeHRPayroll(Request $r)
    {
        // 1) Normalize incoming date(s) to Y-m-d (or null)
        $r->merge([
            'insurance_policy_expiry' => $this->parseUiDate($r->input('insurance_policy_expiry')),
        ]);

        // 2) Validate with strict format
        $data = $r->validate([
            'company_id' => 'nullable|integer',
            'wps_establishment_id' => 'required|string|max:150',
            'wps_bank' => 'required|string|max:150',
            'wps_salary_file_code' => 'nullable|string|max:100',
            'payroll_cycle' => 'required|in:monthly,bi-weekly,weekly',
            'payroll_start' => 'required|integer|min:1|max:30',
            'payroll_end' => 'required|integer|min:1|max:30',
            'weekly_off' => 'nullable|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'gratuity_method' => 'nullable|in:basic_salary,gross_salary',
            'insurance_provider' => 'nullable|string|max:150',
            'insurance_policy_number' => 'nullable|string|max:150',
            'insurance_policy_expiry' => 'nullable|date_format:Y-m-d', // ← strict Y-m-d
        ]);

        // 3) Clean up empty strings
        if (array_key_exists('insurance_policy_expiry', $data) && $data['insurance_policy_expiry'] === '') {
            $data['insurance_policy_expiry'] = null;
        }

        // 4) Upsert (1:1 per company)
        $row = !empty($data['company_id'])
            ? CompanyHrPayroll::updateOrCreate(['company_id' => $data['company_id']], $data)
            : CompanyHrPayroll::create($data);

        return response()->json(['ok' => true, 'id' => $row->id]);
    }

    /**
     * Save HR Payroll Settings (sys_company_hrpayrollsetting)
     * One record per company (updateOrCreate pattern)
     */
    public function storeHrPayrollSetting(Request $r)
    {
        try {
            $companyId = $r->input('company_id');

            if (!$companyId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Company ID is required'
                ], 400);
            }

            // Prepare data mapping from form inputs to database columns
            // All fields are optional - empty values become NULL
            $data = [];

            // Helper function to safely add field (only if non-empty)
            $addField = function ($dbColumn, $formField) use ($r, &$data) {
                $value = $r->input($formField);
                $data[$dbColumn] = (empty($value) || $value === 'null') ? null : trim($value);
            };

            // WPS / Salary Fields
            $addField('wps_establishment_id', 'hr_wps_establishment_id');
            $addField('wps_bank', 'hr_wps_bank');
            $addField('wps_salary_file_code', 'hr_wps_salary_file_code');

            // Payroll Cycle Fields
            $addField('payroll_cycle', 'hr_payroll_cycle');
            $addField('payroll_start_day', 'hr_payroll_start');
            $addField('payroll_end_day', 'hr_payroll_end');

            // Weekly Off (OPTIONAL - only if user selects)
            $weeklyOff = $r->input('hr_weekly_off');
            if (!empty($weeklyOff) && $weeklyOff !== 'null') {
                // If multiple values were selected, store as JSON array; otherwise store as string
                if (is_array($weeklyOff)) {
                    $data['weekly_off_day'] = json_encode(array_values($weeklyOff));
                } else {
                    $data['weekly_off_day'] = trim($weeklyOff);
                }
            } else {
                $data['weekly_off_day'] = null;
            }

            // Gratuity Method
            $addField('gratuity_calculation_method', 'hr_gratuity_method');

            // Attendance Policy Fields
            $addField('attendance_policy', 'attendance_policy');
            $addField('minimum_working_hours', 'min_working_hours');
            $addField('grace_period_minutes', 'grace_period');
            $addField('half_day_after_hours', 'half_day_after');
            $addField('absent_if_hours_below', 'absent_below_hours');
            $addField('late_mark_count_allowed', 'late_mark_allowed');
            $addField('consecutive_late_to_halfday', 'late_mark_halfday');
            $addField('auto_mark_absent_after_days', 'auto_absent_after');

            // Shift & Time Fields
            $addField('shift_start_time', 'shift_start_time');
            $addField('shift_end_time', 'shift_end_time');

            // Leave Policy Fields
            $addField('leave_policy_type', 'leave_policy_type');
            $addField('annual_leave_cl_sl', 'annual_leave');
            $addField('sick_leave_sl', 'sick_leave');
            $addField('casual_leave_cl', 'casual_leave');
            $addField('max_carry_forward_days', 'max_carry_forward');

            // Boolean fields - convert 'yes'/'no' to 1/0
            $compOffAllowed = $r->input('comp_off_allowed');
            $data['comp_off_allowed'] = ($compOffAllowed === 'yes') ? 1 : (($compOffAllowed === 'no') ? 0 : null);

            $carryForward = $r->input('carry_forward');
            $data['carry_forward_unused_leaves'] = ($carryForward === 'yes') ? 1 : (($carryForward === 'no') ? 0 : null);

            $encashableLeaves = $r->input('leave_encashment');
            $data['encashable_leaves'] = ($encashableLeaves === 'yes') ? 1 : (($encashableLeaves === 'no') ? 0 : null);

            // Handle weekly_off_days as JSON array (OPTIONAL - only if user selects)
            $weeklyOffDays = $r->input('weekly_off_days', []);
            if (is_array($weeklyOffDays) && !empty($weeklyOffDays)) {
                // Filter out empty values
                $filtered = array_filter($weeklyOffDays, function ($val) {
                    return !empty($val);
                });
                $data['weekly_off_days'] = !empty($filtered) ? json_encode(array_values($filtered)) : null;
            } else {
                $data['weekly_off_days'] = null;
            }

            // Remove null values to avoid updating unnecessary columns
            // This ensures we only update fields that user explicitly set
            $dataToUpdate = array_filter($data, function ($value, $key) {
                return $value !== null;
            }, ARRAY_FILTER_USE_BOTH);

            // If no data to update, return early
            if (empty($dataToUpdate)) {
                return response()->json([
                    'status' => true,
                    'message' => 'No data to save',
                    'data' => null
                ]);
            }

            // UpdateOrCreate: one record per company
            $setting = \App\SysCompanyHrPayrollSetting::updateOrCreate(
                ['company_id' => $companyId],
                $dataToUpdate
            );

            // If a shift was submitted, also persist it to the main company record (sys_companies.shift_id)
            if ($r->has('shift_id')) {
                $shiftId = $r->input('shift_id');
                // Only set if shift exists, otherwise clear
                if ($shiftId && \App\WorkingShift::where('id', $shiftId)->exists()) {
                    \App\SysCompany::where('id', $companyId)->update(['shift_id' => $shiftId]);
                } else {
                    \App\SysCompany::where('id', $companyId)->update(['shift_id' => null]);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'HR Payroll Settings saved successfully',
                'data' => $setting
            ]);

        } catch (\Exception $e) {
            Log::error('storeHrPayrollSetting error: ' . $e->getMessage(), [
                'company_id' => $r->input('company_id'),
                'request_data' => $r->except('_token')
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Error saving HR Payroll Settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get HR Payroll Settings for a company (for edit mode)
     */
    public function getHrPayrollSetting($companyId)
    {
        try {
            $setting = \App\SysCompanyHrPayrollSetting::where('company_id', $companyId)->first();

            if (!$setting) {
                return response()->json([
                    'status' => false,
                    'message' => 'No settings found for this company'
                ], 404);
            }

            // Decode JSON fields for response
            $settingData = $setting->toArray();
            if ($setting->weekly_off_days) {
                $settingData['weekly_off_days'] = json_decode($setting->weekly_off_days, true);
            }

            return response()->json([
                'status' => true,
                'data' => $settingData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching HR Payroll Settings: ' . $e->getMessage()
            ], 500);
        }
    }


    private function saveDocFile($r, $key, $prefix)
    {
        if (!$r->hasFile($key))
            return null;

        $dir = public_path('uploads/company/docs');
        if (!is_dir($dir))
            @mkdir($dir, 0777, true);

        $file = $r->file($key);
        $name = $prefix . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        $file->move($dir, $name);

        return "uploads/company/docs/$name";
    }


    public function storeDocuments(Request $r)
    {
        // VALIDATION
        $this->validate($r, [
            'company_id' => 'required|exists:sys_company,id',

            'establishment_number' => 'nullable|string|max:150',
            'establishment_expiry' => 'nullable|string',
            'establishment_file' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:5120',

            'immigration_number' => 'nullable|string|max:150',
            'immigration_expiry' => 'nullable|string',
            'immigration_file' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:5120',

            'labour_number' => 'nullable|string|max:150',
            'labour_expiry' => 'nullable|string',
            'labour_file' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:5120',

            'chamber_number' => 'nullable|string|max:150',
            'chamber_expiry' => 'nullable|string',
            'chamber_file' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:5120',

            'insurance_certificate_number' => 'nullable|string|max:150',
            'insurance_certificate_expiry' => 'nullable|string',
            'insurance_file' => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:5120',

            'moa_aoa_number' => 'nullable|string|max:100',
            'moa_aoa_expiry' => 'nullable|string',
            'moa_aoa_file' => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:5120',

            'board_resolution_number' => 'nullable|string|max:100',
            'board_resolution_expiry' => 'nullable|string',
            'board_resolution_file' => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:5120',

            'poa_number' => 'nullable|string|max:100',
            'poa_expiry' => 'nullable|string',
            'poa_file' => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:5120',
        ]);

        $parseDate = function ($date) {
            if (!$date)
                return null;

            $date = trim($date);
            $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'm/d/Y'];

            foreach ($formats as $fmt) {
                try {
                    return Carbon::createFromFormat($fmt, $date)->format('Y-m-d');
                } catch (\Exception $e) {
                    // try next
                }
            }

            // Fallback: try best-effort parse
            try {
                return Carbon::parse($date)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        };

        // FILE SAVE HELPER
        $saveFile = function ($inputName, $prefix) use ($r) {
            if ($r->hasFile($inputName)) {
                $dir = public_path("uploads/company/docs");
                if (!is_dir($dir))
                    mkdir($dir, 0777, true);

                $file = $r->file($inputName);
                $name = uniqid($prefix . "_") . "." . $file->getClientOriginalExtension();
                $file->move($dir, $name);
                return "uploads/company/docs/" . $name;
            }
            return null;
        };

        try {

            // DATA ARRAY
            $data = [
                'establishment_number' => $r->establishment_number,
                'establishment_expiry' => $parseDate($r->establishment_expiry),
                'establishment_file' => $saveFile('establishment_file', 'est'),

                'immigration_number' => $r->immigration_number,
                'immigration_expiry' => $parseDate($r->immigration_expiry),
                'immigration_file' => $saveFile('immigration_file', 'imm'),

                'labour_number' => $r->labour_number,
                'labour_expiry' => $parseDate($r->labour_expiry),
                'labour_file' => $saveFile('labour_file', 'lab'),

                'chamber_number' => $r->chamber_number,
                'chamber_expiry' => $parseDate($r->chamber_expiry),
                'chamber_file' => $saveFile('chamber_file', 'chm'),

                'insurance_certificate_number' => $r->insurance_certificate_number,
                'insurance_certificate_expiry' => $parseDate($r->insurance_certificate_expiry),
                'insurance_file' => $saveFile('insurance_file', 'ins'),

                'moa_aoa_number' => $r->moa_aoa_number,
                'moa_aoa_expiry' => $parseDate($r->moa_aoa_expiry),
                'moa_aoa_file' => $saveFile('moa_aoa_file', 'moa'),

                'board_resolution_number' => $r->board_resolution_number,
                'board_resolution_expiry' => $parseDate($r->board_resolution_expiry),
                'board_resolution_file' => $saveFile('board_resolution_file', 'brd'),

                'poa_number' => $r->poa_number,
                'poa_expiry' => $parseDate($r->poa_expiry),
                'poa_file' => $saveFile('poa_file', 'poa'),
            ];

            // REMOVE NULL FILE KEYS (to avoid overwriting existing file)
            foreach (['establishment_file', 'immigration_file', 'labour_file', 'chamber_file', 'insurance_file', 'moa_aoa_file', 'board_resolution_file', 'poa_file'] as $f) {
                if ($data[$f] === null)
                    unset($data[$f]);
            }

            // CREATE OR UPDATE
            SysCompanyDocument::updateOrCreate(
                ['company_id' => $r->company_id],
                $data
            );

            return response()->json([
                'ok' => true,
                'msg' => 'Documents saved successfully'
            ]);

        } catch (\Exception $ex) {
            Log::error('storeDocuments failed: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'request' => $r->all()
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Server error while saving documents'
            ], 500);
        }
    }




    private function fileUrl(?string $path): ?string
    {
        if (!$path)
            return null;
        $p = ltrim($path, '/');
        // ensure "public/" prefix exactly once
        if (strpos($p, 'public/') !== 0) {
            $p = 'public/' . $p;
        }
        return asset($p);
    }

    public function edit($id)
    {
        $company = SysCompany::findOrFail($id);
        $country = SysCountries::all();
        $currency = SysCurrency::all();

        // --- HR & Payroll (1:1) ---
        $hr = CompanyHrPayroll::where('company_id', $company->id)->first();

        // --- Banking (multi) ---
        $banks = SysCompanyBanking::where('company_id', $company->id)
            ->orderBy('id')
            ->get()
            ->map(function ($b) {
                return [
                    'bank_name' => $b->bank_name,
                    'branch_name' => $b->branch_name,
                    'account_number' => $b->account_number,
                    'iban_number' => $b->iban_number,
                    'swift_code' => $b->swift_code,
                    'finance_code' => $b->finance_code,
                    'currency' => $b->currency,
                    'bank_letter' => null, // file input blank
                    'bank_letter_url' => $this->fileUrl($b->bank_letter),
                ];
            })->values();

        // --- HR Policies (multi) ---
        $policies = SysCompanyHrPolicy::where('company_id', $company->id)
            ->orderBy('id')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'uid' => 'pol_' . $p->id, // editor key
                    'policy_date' => optional($p->policy_date)->format('Y-m-d') ?? ($p->policy_date ?: ''),
                    'policy_name' => $p->policy_name,
                    'policy_category' => $p->policy_category,
                    'policy_valid' => optional($p->policy_valid)->format('Y-m-d') ?? ($p->policy_valid ?: ''),
                    'view_to_employees' => (int) $p->view_to_employees,
                    'policy_details' => $p->policy_details,
                    'policy_file' => null, // file input blank
                    'policy_file_url' => $this->fileUrl($p->policy_file),
                ];
            })->values();

        // --- Documents (8 items) ---
        // Agar docs same SysCompany table me columns me stored hain:
        $docsSeed = [
            'establishment' => [
                'number' => $company->establishment_number ?? '',
                'expiry' => $company->establishment_expiry ?? '',
                'file' => null,
                'url' => $this->fileUrl($company->establishment_file ?? null),
            ],
            'immigration' => [
                'number' => $company->immigration_number ?? '',
                'expiry' => $company->immigration_expiry ?? '',
                'file' => null,
                'url' => $this->fileUrl($company->immigration_file ?? null),
            ],
            'labour' => [
                'number' => $company->labour_number ?? '',
                'expiry' => $company->labour_expiry ?? '',
                'file' => null,
                'url' => $this->fileUrl($company->labour_file ?? null),
            ],
            'chamber' => [
                'number' => $company->chamber_number ?? '',
                'expiry' => $company->chamber_expiry ?? '',
                'file' => null,
                'url' => $this->fileUrl($company->chamber_file ?? null),
            ],
            'insurance' => [
                'number' => $company->insurance_certificate_number ?? '',
                'expiry' => $company->insurance_certificate_expiry ?? '',
                'file' => null,
                'url' => $this->fileUrl($company->insurance_file ?? null),
            ],
            'moa_aoa' => [
                'file' => null,
                'url' => $this->fileUrl($company->moa_aoa_file ?? null),
            ],
            'board_resolution' => [
                'file' => null,
                'url' => $this->fileUrl($company->board_resolution_file ?? null),
            ],
            'poa' => [
                'file' => null,
                'url' => $this->fileUrl($company->poa_file ?? null),
            ],
        ];

        // --- SEED payload Vue ke liye (create wale keys jaisey) ---
        $seed = [
            'form' => [
                'company_id' => $company->id,
                'company_name' => $company->company_name,
                'trade_name' => $company->trade_name,
                'legal_entity_type' => $company->legal_entity_type,
                'industry' => $company->industry,
                'parent_company' => $company->parent_company,
                'date_of_incorporation' => $company->date_of_incorporation,
                'country' => $company->country,
                'city' => $company->city,
                'company_address' => $company->company_address,
                'sales_code' => $company->sales_code,
                'other_code' => $company->other_code,
                'currency' => $company->currency,
                'currency_digit' => $company->currency_digit,
                'book_closed' => $company->book_closed,
                // file inputs blank, but show preview via URL in blade if needed
                'email' => $company->company_email,
                'website' => $company->website,
                'telephone' => $company->telephone,
                'fax' => $company->fax,
                'mobile' => $company->mobile,
                'contact_sections' => $company->contact_sections ?? [],

                'owner' => [
                    'name' => $company->owner_name,
                    'mobile' => $company->owner_mobile,
                    'email' => $company->owner_email,
                    'files' => ['passport_copy' => null, 'emirates_id' => null, 'visa_copy' => null],
                ],
                'sponsor' => [
                    'name' => $company->sponsor_name,
                    'mobile' => $company->sponsor_mobile,
                    'email' => $company->sponsor_email,
                    'files' => ['passport_copy' => null, 'emirates_id' => null, 'visa_copy' => null],
                ],
                'contact' => [
                    'name' => $company->contact_person_name,
                    'mobile' => $company->contact_person_mobile,
                    'email' => $company->contact_person_email,
                    'designation' => $company->contact_person_designation,
                    'files' => ['passport_copy' => null, 'emirates_id' => null, 'visa_copy' => null],
                ],

                // Compliance (agar same table me columns hain)
                'compliance' => [
                    'business_license_number' => $company->business_license_number,
                    'license_issue_date' => $company->license_issue_date,
                    'license_expiry_date' => $company->license_expiry_date,
                    'issuing_authority' => $company->issuing_authority,
                    'tax_applicable' => $company->tax_applicable,
                    'vat_registration_number' => $company->vat_registration_number,
                    'vat_percentage' => $company->vat_percentage,
                    'vat_date' => $company->vat_date,
                    'corporate_tax_number' => $company->corporate_tax_number,
                    'corporate_tax_vat' => $company->corporate_tax_vat,
                    'corporate_tax_date' => $company->corporate_tax_date,
                ],

                // HR
                'hr' => [
                    'wps_establishment_id' => optional($hr)->wps_establishment_id,
                    'wps_bank' => optional($hr)->wps_bank,
                    'wps_salary_file_code' => optional($hr)->wps_salary_file_code,
                    'payroll_cycle' => optional($hr)->payroll_cycle,
                    'weekly_off' => optional($hr)->weekly_off,
                    'gratuity_method' => optional($hr)->gratuity_method,
                    'insurance_provider' => optional($hr)->insurance_provider,
                    'insurance_policy_number' => optional($hr)->insurance_policy_number,
                    'insurance_policy_expiry' => optional($hr)->insurance_policy_expiry,
                ],
            ],

            'banks' => $banks,
            'policies' => $policies,
            'docs' => $docsSeed,
        ];

        // Load non-UAE documents from DB into session for editing
        $nonUaeItems = SysCompanyDocumentItem::where('company_id', $company->id)->get();
        if ($nonUaeItems->count() > 0) {
            $sessionKey = 'company_document_items_' . $company->id;
            $sessionItems = [];
            foreach ($nonUaeItems as $item) {
                $sessionItems[] = [
                    'id' => $item->id,
                    'company_id' => $item->company_id,
                    'document_name' => $item->document_name,
                    'document_number' => $item->document_number,
                    'document_date' => $item->document_date,
                    'expiry_date' => $item->expiry_date,
                    'attachment_file' => $item->attachment_file,
                ];
            }
            session([$sessionKey => $sessionItems]);
        }

        // Edit view: same form partial + same JS (IS_EDIT flags)
        return view('backEnd.company.companyEdit', [
            'company' => $company,
            'seed' => $seed,
            'nextId' => $company->id, // JS me company_id set
            'country' => $country,
            'currency' => $currency,
        ]);
    }

    public function policy(Request $request)
    {
        $companyId = session('logged_session_data.company_id');

        $policies = SysCompanyHrPolicy::where('company_id', $companyId)
            ->orderBy('id')
            ->get();

        $total = $policies->count();

        // default 0
        $index = (int) session('policy_step_index', 0);

        // If user clicked next
        if ($request->has('next')) {
            $index = min($index + 1, $total - 1);
        }

        // If user clicked prev
        if ($request->has('prev')) {
            $index = max($index - 1, 0);
        }

        // Save in session
        session(['policy_step_index' => $index]);

        return view('backEnd.company.companyPolicy', compact('policies', 'index', 'total'));
    }


    public function getParentCompanies()
    {
        $companies = SysCompany::where('company_type', 'parent')
            ->select('id', 'company_name')
            ->orderBy('company_name')
            ->get();

        return response()->json($companies);
    }


    public function index()
    {
        $companies = SysCompany::latest()->get();
        return view('backEnd.company.index', compact('companies'));
    }

    public function initData()
    {
        return response()->json([
            'industries' => \App\SmIndustry::orderBy('name')->get(),
            'activities' => \App\SmBusinessActivity::orderBy('name')->get(),
            'parent_companies' => SysCompany::where('company_type', 'parent')->get(),
            'countries' => SysCountries::orderBy('name')->get(),
            'states' => SysStates::orderBy('name')->get(),
        ]);
    }

    public function storeSetting(Request $request)
    {
        $companyId = (int) $request->company_id;

        if (!$companyId) {
            return response()->json([
                'ok' => false,
                'errors' => ['company_id' => ['Company not found.']]
            ], 422);
        }

        // VALIDATION RULES
        $rules = [
            'currency' => 'nullable|string|max:10',
            'currency_symbol' => 'nullable|string|max:10',
            'currency_digit' => 'nullable|integer|min:0',
            'r_code' => 'nullable|string|max:50',
            'p_code' => 'nullable|string|max:50',
            'book_closed' => 'nullable|string',
            'sales_code' => 'nullable|string|max:50',
            'other_code' => 'nullable|string|max:50',
            'hr_wps_establishment_id' => 'nullable|string|max:100',
            'hr_wps_bank' => 'nullable|string|max:100',
            'hr_wps_salary_file_code' => 'nullable|string|max:50',
            'hr_payroll_cycle' => 'nullable|in:monthly,bi-weekly,weekly',
            'hr_payroll_start' => 'nullable|integer|min:1|max:30',
            'hr_payroll_end' => 'nullable|integer|min:1|max:30',
            // allow either a single string or an array of selections
            'hr_weekly_off' => 'nullable',
            'hr_weekly_off.*' => 'string|max:200',
            'hr_gratuity_method' => 'nullable|in:basic_salary,gross_salary',
            'hr_insurance_provider' => 'nullable|string|max:100',
            'hr_insurance_policy_number' => 'nullable|string|max:100',
            'hr_insurance_policy_expiry' => 'nullable',
        ];

        $v = Validator::make($request->all(), $rules);

        if ($v->fails()) {
            return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
        }

        // SAFELY PARSE DATES
        $bookClosed = null;
        if ($request->filled('book_closed')) {
            try {
                $bookClosed = \Carbon\Carbon::createFromFormat('d/m/Y', $request->book_closed)->format('Y-m-d');
            } catch (\Exception $e) {
                return response()->json(['ok' => false, 'errors' => ['book_closed' => ['Invalid date format']]], 422);
            }
        }

        $insuranceExpiry = null;
        if ($request->filled('hr_insurance_policy_expiry')) {
            try {
                $insuranceExpiry = \Carbon\Carbon::createFromFormat('d/m/Y', $request->hr_insurance_policy_expiry)->format('Y-m-d');
            } catch (\Exception $e) {
                return response()->json(['ok' => false, 'errors' => ['hr_insurance_policy_expiry' => ['Invalid date format']]], 422);
            }
        }

        // SANITIZE HR GRATUITY METHOD
        $gratuityMethod = null;
        if ($request->filled('hr_gratuity_method')) {
            $gm = trim($request->hr_gratuity_method);
            if (in_array($gm, ['basic_salary', 'gross_salary'])) {
                $gratuityMethod = $gm;
            }
        }

        // DATA ARRAY
        $data = [
            'is_customer_code' => $request->has('is_customer_code') ? 1 : 0,
            'is_supplier_code' => $request->has('is_supplier_code') ? 1 : 0,
            'is_account_code' => $request->has('is_account_code') ? 1 : 0,
            'is_subaccount_code' => $request->has('is_subaccount_code') ? 1 : 0,

            'currency' => $request->currency,
            'currency_symbol' => $request->currency_symbol ?: null,
            'currency_digit' => $request->currency_digit ?: 2,
            'r_code' => $request->r_code ?: null,
            'p_code' => $request->p_code ?: null,
            'book_closed' => $bookClosed,

            'sales_code' => $request->sales_code,
            'other_code' => $request->other_code,

            'hr_wps_establishment_id' => $request->hr_wps_establishment_id,
            'hr_wps_bank' => $request->hr_wps_bank,
            'hr_wps_salary_file_code' => $request->hr_wps_salary_file_code,
            'hr_payroll_cycle' => $request->hr_payroll_cycle ?: null,
            'hr_payroll_start' => $request->filled('hr_payroll_start') ? (int) $request->hr_payroll_start : null,
            'hr_payroll_end' => $request->filled('hr_payroll_end') ? (int) $request->hr_payroll_end : null,
            'hr_weekly_off' => is_array($request->hr_weekly_off) ? json_encode(array_values($request->hr_weekly_off)) : ($request->hr_weekly_off ?: null),
            'hr_gratuity_method' => $gratuityMethod,

            'hr_insurance_provider' => $request->hr_insurance_provider,
            'hr_insurance_policy_number' => $request->hr_insurance_policy_number,
            'hr_insurance_policy_expiry' => $insuranceExpiry,
        ];

        // UPDATE OR CREATE
        SysCompanySetting::updateOrCreate(
            ['company_id' => $companyId],
            $data
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Store a new working shift (AJAX)
     */
    // public function storeWorkingShift(Request $request)
    // {
    //     $v = Validator::make($request->all(), [
    //         'shift_name' => 'required|string|max:100',
    //         'start_time' => 'required|date_format:H:i',
    //         'end_time' => 'required|date_format:H:i',
    //     ]);

    //     if ($v->fails()) {
    //         return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
    //     }

    //     try {
    //         $start = Carbon::createFromFormat('H:i', $request->start_time);
    //         $end = Carbon::createFromFormat('H:i', $request->end_time);

    //         if ($end->lte($start)) {
    //             return response()->json(['ok' => false, 'errors' => ['end_time' => ['End time must be after start time']]], 422);
    //         }

    //         $shift = WorkingShift::create([
    //             'shift_name' => trim($request->shift_name),
    //             'start_time' => $start->format('H:i:00'),
    //             'end_time' => $end->format('H:i:00'),
    //             'is_active' => 1,
    //         ]);

    //         return response()->json([
    //             'ok' => true,
    //             'shift' => [
    //                 'id' => $shift->id,
    //                 'shift_name' => $shift->shift_name,
    //                 'start_time' => $start->format('h:i A'),
    //                 'end_time' => $end->format('h:i A'),
    //             ]
    //         ]);

    //     } catch (\Exception $e) {
    //         Log::error('storeWorkingShift error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    //         return response()->json(['ok' => false, 'errors' => ['server' => ['Could not save shift.']]], 500);
    //     }
    // }


    public function storeWorkingShift(Request $request)
    {
        $v = Validator::make($request->all(), [
            'shift_name' => 'required|string|max:100|unique:working_shifts,shift_name',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
        ]);

        if ($v->fails()) {
            return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
        }

        try {
            $start = Carbon::createFromFormat('H:i', $request->start_time);
            $end = Carbon::createFromFormat('H:i', $request->end_time);

            if ($end->lte($start)) {
                return response()->json([
                    'ok' => false,
                    'errors' => ['end_time' => ['End time must be after start time']]
                ], 422);
            }

            // 🔴 CHECK duplicate start + end time
            $exists = WorkingShift::where('start_time', $start->format('H:i:00'))
                ->where('end_time', $end->format('H:i:00'))
                ->exists();

            if ($exists) {
                return response()->json([
                    'ok' => false,
                    'errors' => [
                        'start_time' => ['A shift with the same start and end time already exists']
                    ]
                ], 422);
            }

            $shift = WorkingShift::create([
                'shift_name' => trim($request->shift_name),
                'start_time' => $start->format('H:i:00'),
                'end_time' => $end->format('H:i:00'),
                'is_active' => 1,
            ]);

            return response()->json([
                'ok' => true,
                'shift' => [
                    'id' => $shift->id,
                    'shift_name' => $shift->shift_name,
                    'start_time' => $start->format('h:i A'),
                    'end_time' => $end->format('h:i A'),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('storeWorkingShift error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'ok' => false,
                'errors' => ['server' => ['Could not save shift.']]
            ], 500);
        }
    }



    public function companyEdit(Request $request, $id)
    {
        try {
            $company = SysCompany::with([
                // Core relations
                'settings',
                'compliance',
                'banking',
                'warehouses',
                'hrPolicies',

                // People (filter in controller or blade)
                'people',

                // Location / master data
                'countryRelation',
                'stateRelation',
                'businessEntity',
                'businessIndustry',
                'businessSector',
                'parentCompany',
                'documentItems',
                'hrpayrollsettings',
                'documents',
            ])->findOrFail($id);

            /*
            |--------------------------------------------------------------------------
            | Split people by type (for edit form)
            |--------------------------------------------------------------------------
            */
            $owners = $company->people->where('type', 'owner')->values();
            $sponsors = $company->people->where('type', 'sponsor')->values();
            $contacts = $company->people->where('type', 'contact')->values();

            /*
            |--------------------------------------------------------------------------
            | Decode JSON fields
            |--------------------------------------------------------------------------
            */
            if ($company->hrPayrollSetting) {
                $company->hrPayrollSetting->weekly_off_days =
                    $company->hrPayrollSetting->weekly_off_days
                    ? json_decode($company->hrPayrollSetting->weekly_off_days, true)
                    : [];
            }

            /*
            |--------------------------------------------------------------------------
            | Lookup / dropdown data
            |--------------------------------------------------------------------------
            */
            $country = SysCountries::orderBy('name')->get();
            $state = SysStates::orderBy('name')->get();
            $currency = SysCurrency::orderBy('code')->get();
            $industries = SmIndustry::orderBy('name')->get();
            $businessSectors = SmBusinessActivity::orderBy('name')->get();
            $entities = SmBusinessEntityType::orderBy('name')->get();
            $nationalities = SysCountries::orderBy('name')->get();



            $parentCompanies = SysCompany::where('company_type', 'parent')
                ->where('id', '!=', $id)
                ->orderBy('company_name')
                ->get();



            /*
            |--------------------------------------------------------------------------
            | HR Payroll Settings
            |--------------------------------------------------------------------------
            */
            $hrPayroll = SysCompanyHrPayrollSetting::where('company_id', $id)->first();

            return view('backEnd.company.editCompany', compact(
                'company',
                'owners',
                'sponsors',
                'contacts',
                'country',
                'state',
                'currency',
                'industries',
                'businessSectors',
                'entities',
                'parentCompanies',
                'nationalities',
                'hrPayroll'
            ));

        } catch (\Exception $e) {
            dd($e->getMessage());
            Toastr::error('Operation Failed: ' . $e->getMessage(), 'Failed');
            return redirect()->back();
        }
    }




    public function storeBankSession(Request $request)
    {
        // Validate required fields
        $request->validate([
            'bank_name' => 'required',
            'account_number' => 'required',
            'iban_number' => 'required'
        ]);

        $banks = session('company_banks', []);

        // If bank_id exists, update; else create new
        $id = $request->bank_id ?: uniqid();

        $bank = [
            'id' => $id,
            'bank_name' => $request->bank_name,
            'branch_name' => $request->branch_name,
            'account_number' => $request->account_number,
            'iban_number' => $request->iban_number,
            'swift_code' => $request->swift_code,
            'finance_code' => $request->finance_code,
            'currency' => $request->currency,
            'bank_letter' => $banks[$id]['bank_letter'] ?? null,
        ];

        // FILE UPLOAD (IF NEW)
        if ($request->hasFile('bank_letter')) {
            $file = $request->file('bank_letter');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bank_letters', $filename, 'public');
            $bank['bank_letter'] = $path;
        }

        // Save / Update in session
        $banks[$id] = $bank;
        session(['company_banks' => $banks]);

        return response()->json([
            'banks' => array_values($banks)
        ]);
    }


    public function getBankSession(Request $request)
    {
        $banks = session('company_banks', []);

        if ($request->id) {
            return response()->json([
                'bank' => $banks[$request->id] ?? null
            ]);
        }

        return response()->json([
            'banks' => array_values($banks)
        ]);
    }


    public function deleteBankSession(Request $request)
    {
        $banks = session('company_banks', []);
        unset($banks[$request->bank_id]);

        session(['company_banks' => $banks]);

        return response()->json([
            'banks' => array_values($banks)
        ]);
    }

    public function saveAllBanking(Request $r)
    {
        $this->validate($r, [
            'company_id' => 'required|exists:sys_company,id'
        ]);

        $companyId = $r->company_id;

        // Get session banks
        $banks = session('company_banks', []);

        if (empty($banks)) {
            return response()->json([
                'ok' => true,
                'message' => 'No banking data to save',
                'count' => 0
            ]);
        }

        $savedCount = 0;
        $skippedCount = 0;

        foreach ($banks as $index => $b) {

            // Check if bank already exists in DB
            $exists = SysCompanyBanking::where('company_id', $companyId)
                ->where('bank_name', $b['bank_name'])
                ->where('account_number', $b['account_number'])
                ->exists();

            if ($exists) {
                // SKIP — do nothing
                $skippedCount++;
                continue;
            }

            // Insert NEW bank
            try {
                SysCompanyBanking::create([
                    'company_id' => $companyId,
                    'bank_name' => $b['bank_name'],
                    'branch_name' => $b['branch_name'] ?? null,
                    'account_number' => $b['account_number'],
                    'iban_number' => $b['iban_number'],
                    'swift_code' => $b['swift_code'] ?? null,
                    'finance_code' => $b['finance_code'] ?? null,
                    'currency' => $b['currency'] ?? null,
                    'bank_letter' => $b['bank_letter'] ?? null,
                ]);
                $savedCount++;
            } catch (\Exception $e) {
                Log::error('Failed to save bank for company ' . $companyId . ': ' . $e->getMessage());
                return response()->json([
                    'ok' => false,
                    'message' => 'Error saving banking record: ' . $e->getMessage()
                ], 500);
            }
        }

        // Clear session banks after successful persistence to avoid duplicate inserts
        session()->forget('company_banks');

        return response()->json([
            'ok' => true,
            'message' => "Saved: $savedCount new banking records. Skipped: $skippedCount duplicates.",
            'saved' => $savedCount,
            'skipped' => $skippedCount,
            'count' => count($banks)
        ]);

    }

    public function checkBankingSession($company_id)
    {
        $banking = session('company_banks', []);

        return response()->json([
            'hasSession' => count($banking) > 0,
            'count' => count($banking)
        ]);
    }


    /**
     * POLICY session endpoints — allow adding company policies into session
     * before the company record exists, then persist them when company is saved.
     */
    public function storePolicySession(Request $request)
    {
        $input = $request->all();

        $v = Validator::make($input, [
            'policy_date' => 'required',
            'policy_name' => 'required|string|max:255',
            'view_to_employees' => 'required|in:0,1',
        ]);

        if ($v->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => $v->errors()
            ], 422);
        }

        $policies = session('company_policies', []);

        $id = $request->policy_id ?: uniqid();

        $policy = [
            'id' => $id,
            'policy_date' => $request->policy_date,
            'policy_name' => $request->policy_name,
            'policy_category' => $request->policy_category,
            'policy_valid' => $request->policy_valid,
            'view_to_employees' => (int) $request->view_to_employees,
            'policy_details' => $request->policy_details,
            'policy_file' => $policies[$id]['policy_file'] ?? null,
        ];

        // FILE UPLOAD (if provided)
        if ($request->hasFile('policy_file')) {
            $f = $request->file('policy_file');
            $dir = public_path('uploads/company/policies');
            if (!is_dir($dir))
                @mkdir($dir, 0777, true);
            $name = uniqid('policy_') . '.' . $f->getClientOriginalExtension();
            $f->move($dir, $name);
            $policy['policy_file'] = 'uploads/company/policies/' . $name;
        }

        $policies[$id] = $policy;
        session(['company_policies' => $policies]);

        return response()->json([
            'ok' => true,
            'policies' => array_values($policies)
        ]);
    }


    public function getPolicySession(Request $request)
    {
        $policies = session('company_policies', []);

        if ($request->id) {
            return response()->json([
                'policy' => $policies[$request->id] ?? null
            ]);
        }

        return response()->json([
            'policies' => array_values($policies)
        ]);
    }


    public function deletePolicySession(Request $request)
    {
        $policies = session('company_policies', []);
        unset($policies[$request->policy_id]);
        session(['company_policies' => $policies]);

        return response()->json([
            'policies' => array_values($policies)
        ]);
    }


    public function saveAllPolicies(Request $r)
    {
        try {
            $this->validate($r, [
                'company_id' => 'required|exists:sys_company,id'
            ]);

            $companyId = $r->company_id;

            $policies = session('company_policies', []);

            if (empty($policies)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'No policies in session'
                ]);
            }

            foreach ($policies as $p) {
                // normalize values
                $p = is_array($p) ? $p : (array) $p;

                // Find existing policy by company + name + date
                $existing = SysCompanyHrPolicy::where('company_id', $companyId)
                    ->where('policy_name', $p['policy_name'] ?? null)
                    ->where('policy_date', $p['policy_date'] ?? null)
                    ->first();

                // Prepare data to insert/update
                $data = [
                    'policy_date' => $p['policy_date'] ?? null,
                    'policy_name' => $p['policy_name'] ?? null,
                    'policy_category' => $p['policy_category'] ?? null,
                    'policy_valid' => $p['policy_valid'] ?? null,
                    'view_to_employees' => !empty($p['view_to_employees']) ? 1 : 0,
                    'policy_details' => $p['policy_details'] ?? null,
                ];

                // Handle policy_file carefully:
                // - If session provides a file path, use it
                // - Else if updating, preserve existing file path
                // - Else default to empty string to satisfy NOT NULL columns
                if (array_key_exists('policy_file', $p) && $p['policy_file'] !== null) {
                    $data['policy_file'] = $p['policy_file'];
                } elseif ($existing) {
                    $data['policy_file'] = $existing->policy_file ?? '';
                } else {
                    $data['policy_file'] = '';
                }

                if ($existing) {
                    // Update existing record
                    $existing->update($data);
                } else {
                    // Create new record
                    $data['company_id'] = $companyId;
                    SysCompanyHrPolicy::create($data);
                }
            }

            // Clear session policies after saving
            session()->forget('company_policies');

            return response()->json([
                'ok' => true,
                'message' => 'Policies persisted',
                'count' => count($policies)
            ]);

        } catch (\Exception $ex) {
            // Log and return error info for debugging
            Log::error('saveAllPolicies failed: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'request' => $r->all(),
                'session_policies_sample' => array_slice((array) session('company_policies', []), 0, 5)
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Server error while persisting policies',
                'error' => $ex->getMessage()
            ], 500);
        }
    }

    /**
     * Save all warehouses from session/request to database
     */
    public function saveAllWarehouses(Request $r)
    {
        try {
            $this->validate($r, [
                'company_id' => 'required|exists:sys_company,id'
            ]);

            $companyId = $r->company_id;

            // Get warehouses from request (session storage sends them directly)
            $warehouses = $r->input('warehouses', []);

            if (empty($warehouses)) {
                return response()->json([
                    'ok' => true,
                    'message' => 'No warehouse data to save',
                    'count' => 0
                ]);
            }

            $savedCount = 0;
            $skippedCount = 0;

            foreach ($warehouses as $warehouse) {
                // Normalize values
                $warehouse = is_array($warehouse) ? $warehouse : (array) $warehouse;

                // Check if warehouse already exists by company + warehouse_code
                if (!empty($warehouse['warehouse_code'])) {
                    $exists = CompanyWarehouse::where('company_id', $companyId)
                        ->where('warehouse_code', $warehouse['warehouse_code'])
                        ->exists();

                    if ($exists) {
                        $skippedCount++;
                        continue;
                    }
                }

                // Prepare data for insertion
                $data = [
                    'company_id' => $companyId,
                    'warehouse_code' => $warehouse['warehouse_code'] ?? null,
                    'warehouse_name' => $warehouse['warehouse_name'] ?? null,
                    'warehouse_address' => $warehouse['warehouse_address'] ?? null,
                    'warehouse_country' => $warehouse['warehouse_country'] ?? null,
                    'warehouse_state' => $warehouse['warehouse_state'] ?? null,
                    'warehouse_city' => $warehouse['warehouse_city'] ?? null,
                    'warehouse_area' => $warehouse['warehouse_area'] ?? null,
                    'warehouse_building_name' => $warehouse['warehouse_building_name'] ?? null,
                    'warehouse_flat_office_no' => $warehouse['warehouse_flat_office_no'] ?? null,
                    'contact_first_name' => $warehouse['contact_first_name'] ?? null,
                    'contact_last_name' => $warehouse['contact_last_name'] ?? null,
                    'contact_mobile' => $warehouse['contact_mobile'] ?? null,
                    'contact_email' => $warehouse['contact_email'] ?? null,
                    'contact_designation' => $warehouse['contact_designation'] ?? null,
                    'contact_documents' => isset($warehouse['contact_documents']) ? json_encode($warehouse['contact_documents']) : null,
                    'fire_safety_compliance_status' => !empty($warehouse['fire_safety_compliance_status']) ? $warehouse['fire_safety_compliance_status'] : null,
                    'fire_noc_certificate_number' => $warehouse['fire_noc_certificate_number'] ?? null,
                    'safety_equipment_available' => !empty($warehouse['safety_equipment_available']) ? $warehouse['safety_equipment_available'] : null,
                    'fire_noc_expiry_date' => !empty($warehouse['fire_noc_expiry_date']) ? $warehouse['fire_noc_expiry_date'] : null,
                    'last_safety_inspection_date' => !empty($warehouse['last_safety_inspection_date']) ? $warehouse['last_safety_inspection_date'] : null,
                ];

                // Remove null values to avoid database constraints but keep company_id
                $data = array_filter($data, function ($value, $key) {
                    return $key === 'company_id' || ($value !== null && $value !== '');
                }, ARRAY_FILTER_USE_BOTH);

                try {
                    CompanyWarehouse::create($data);
                    $savedCount++;
                } catch (\Exception $e) {
                    Log::error('Failed to save warehouse for company ' . $companyId . ': ' . $e->getMessage());
                    return response()->json([
                        'ok' => false,
                        'message' => 'Error saving warehouse record: ' . $e->getMessage()
                    ], 500);
                }
            }

            return response()->json([
                'ok' => true,
                'message' => "Saved: $savedCount new warehouse records. Skipped: $skippedCount duplicates.",
                'saved' => $savedCount,
                'skipped' => $skippedCount,
                'count' => count($warehouses)
            ]);

        } catch (\Exception $ex) {
            Log::error('saveAllWarehouses failed: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'request' => $r->all()
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Server error while persisting warehouses',
                'error' => $ex->getMessage()
            ], 500);
        }
    }

    /**
     * PEOPLE session endpoints — allow adding company people (owners, sponsors, contacts) 
     * with documents into session before the company record exists, then persist them when company is saved.
     */
    public function storePeopleSession(Request $request)
    {
        $input = $request->all();

        $v = Validator::make($input, [
            'type' => 'required|in:owner,sponsor,contact',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'designation' => 'nullable|string|max:255',
            'designation_id' => 'nullable|exists:sm_designations,id',
            'share_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($v->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => $v->errors()
            ], 422);
        }

        $people = session('company_people', []);
        $editIndex = $request->edit_index;

        // Normalize designation_id into a designation title for session storage
        try {
            $designationTitle = null;
            if (!empty($request->designation_id)) {
                $des = \App\SmDesignation::find($request->designation_id);
                if ($des)
                    $designationTitle = $des->title;
            } else {
                $designationTitle = $request->designation;
            }
        } catch (\Exception $e) {
            Log::warning('Could not map designation id in storePeopleSession: ' . $e->getMessage());
            $designationTitle = $request->designation;
        }

        // Build full name
        $fullName = trim(($request->salutation ?? '') . ' ' . ($request->first_name ?? '') . ' ' . ($request->last_name ?? ''));

        $person = [
            'id' => $editIndex !== '' && $editIndex !== null ? $people[$editIndex]['id'] ?? uniqid() : uniqid(),
            'type' => $request->type,
            'salutation' => $request->salutation,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $fullName,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'designation' => $designationTitle,
            'share_percentage' => $request->share_percentage,
            'documents' => []
        ];

        // Handle documents
        if ($request->has('documents')) {
            foreach ($request->documents as $docIndex => $doc) {
                if (empty($doc['document_name']))
                    continue;

                $document = [
                    'document_name' => $doc['document_name'],
                    'document_no' => $doc['document_no'] ?? '',
                    'issue_date' => $doc['issue_date'] ?? '',
                    'expiry_date' => $doc['expiry_date'] ?? '',
                    'attachment' => ''
                ];

                // Handle file upload
                if ($request->hasFile("documents.{$docIndex}.attachment")) {
                    $file = $request->file("documents.{$docIndex}.attachment");
                    $dir = public_path('uploads/company/people');
                    if (!is_dir($dir))
                        @mkdir($dir, 0777, true);
                    $fileName = uniqid('people_doc_') . '.' . $file->getClientOriginalExtension();
                    $file->move($dir, $fileName);
                    $document['attachment'] = 'uploads/company/people/' . $fileName;
                }

                $person['documents'][] = $document;
            }
        }

        // Update or add person
        if ($editIndex !== '' && $editIndex !== null && isset($people[$editIndex])) {
            // Preserve existing documents if not replaced
            if (isset($people[$editIndex]['documents']) && empty($person['documents'])) {
                $person['documents'] = $people[$editIndex]['documents'];
            }
            $people[$editIndex] = $person;
        } else {
            $people[] = $person;
        }

        session(['company_people' => $people]);

        return response()->json([
            'ok' => true,
            'people' => array_values($people)
        ]);
    }

    public function getPeopleSession(Request $request)
    {
        $people = session('company_people', []);

        if ($request->index !== null) {
            return response()->json([
                'person' => $people[$request->index] ?? null
            ]);
        }

        return response()->json([
            'people' => array_values($people)
        ]);
    }

    public function deletePeopleSession(Request $request)
    {
        $people = session('company_people', []);

        if (isset($people[$request->index])) {
            // Remove associated files
            if (isset($people[$request->index]['documents'])) {
                foreach ($people[$request->index]['documents'] as $doc) {
                    if (!empty($doc['attachment']) && file_exists(public_path($doc['attachment']))) {
                        @unlink(public_path($doc['attachment']));
                    }
                }
            }

            array_splice($people, $request->index, 1);
            session(['company_people' => $people]);
        }

        return response()->json([
            'people' => array_values($people)
        ]);
    }

    public function saveAllPeople(Request $r)
    {
        try {
            $this->validate($r, [
                'company_id' => 'required|exists:sys_company,id'
            ]);

            $companyId = $r->company_id;
            $people = session('company_people', []);

            if (empty($people)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'No people in session'
                ]);
            }

            DB::beginTransaction();

            foreach ($people as $p) {
                $p = is_array($p) ? $p : (array) $p;

                // Create person record
                $personData = [
                    'company_id' => $companyId,
                    'type' => $p['type'] ?? 'contact',
                    'name' => $p['name'] ?? '',
                    'mobile' => $p['mobile'] ?? '',
                    'email' => $p['email'] ?? '',
                    'designation' => $p['designation'] ?? '',
                ];

                $person = SysCompanyPeople::create($personData);

                // Create documents if any
                if (isset($p['documents']) && is_array($p['documents'])) {
                    foreach ($p['documents'] as $doc) {
                        if (empty($doc['document_name']))
                            continue;

                        $docData = [
                            'people_id' => $person->id,
                            'document_name' => $doc['document_name'],
                            'document_no' => $doc['document_no'] ?? '',
                            'issue_date' => !empty($doc['issue_date']) ? $doc['issue_date'] : null,
                            'expiry_date' => !empty($doc['expiry_date']) ? $doc['expiry_date'] : null,
                            'attachment' => $doc['attachment'] ?? '',
                        ];

                        SysCompanyPeopleDocument::create($docData);
                    }
                }
            }

            DB::commit();

            // Clear session people after saving
            session()->forget('company_people');

            return response()->json([
                'ok' => true,
                'message' => 'People and documents saved successfully',
                'count' => count($people)
            ]);

        } catch (\Exception $ex) {
            DB::rollBack();

            Log::error('saveAllPeople failed: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'request' => $r->all(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Server error while saving people',
                'error' => $ex->getMessage()
            ], 500);
        }
    }

    // =====================================================================
// COMPLIANCE DOCUMENTS SESSION MANAGEMENT (Non-UAE Countries)
// =====================================================================

    public function storeComplianceDocumentSession(Request $request)
    {
        // Preprocess dates to handle different formats
        $issueDate = $request->compliance_issue_date;
        $expiryDate = $request->compliance_expiry_date;

        // Convert dates to standard format if provided
        if ($issueDate) {
            $convertedIssueDate = $this->toSqlDate($issueDate);
            if ($convertedIssueDate) {
                $request->merge(['compliance_issue_date' => $convertedIssueDate]);
            }
        }

        if ($expiryDate) {
            $convertedExpiryDate = $this->toSqlDate($expiryDate);
            if ($convertedExpiryDate) {
                $request->merge(['compliance_expiry_date' => $convertedExpiryDate]);
            }
        }

        $rules = [
            'compliance_document_number' => 'required|string|max:255',
            'compliance_issue_date' => 'nullable|date_format:Y-m-d',
            'compliance_expiry_date' => 'nullable|date_format:Y-m-d',
            'compliance_issuing_authority' => 'required|string|max:255',
            'compliance_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:5120'
        ];

        // Only add the 'after' rule if both dates are provided and valid
        if ($request->compliance_issue_date && $request->compliance_expiry_date) {
            $rules['compliance_expiry_date'] .= '|after:compliance_issue_date';
        }

        $request->validate($rules);

        $complianceDocuments = session('company_compliance_documents', []);

        // Handle file upload if provided
        $attachmentPath = null;
        if ($request->hasFile('compliance_attachment')) {
            $file = $request->file('compliance_attachment');
            $fileName = uniqid('compliance_') . '.' . $file->getClientOriginalExtension();
            $uploadPath = public_path('uploads/compliance/temp');

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $file->move($uploadPath, $fileName);
            $attachmentPath = "uploads/compliance/temp/{$fileName}";
        }

        $document = [
            'id' => uniqid(),
            'document_number' => $request->compliance_document_number,
            'issue_date' => $request->compliance_issue_date,
            'expiry_date' => $request->compliance_expiry_date,
            'issuing_authority' => $request->compliance_issuing_authority,
            'attachment' => $attachmentPath,
            'attachment_name' => $request->hasFile('compliance_attachment') ? $request->file('compliance_attachment')->getClientOriginalName() : null
        ];

        $complianceDocuments[] = $document;
        session(['company_compliance_documents' => $complianceDocuments]);

        return response()->json([
            'ok' => true,
            'document' => $document
        ]);
    }

    public function getComplianceDocumentSession(Request $request)
    {
        $documents = session('company_compliance_documents', []);
        return response()->json([
            'ok' => true,
            'documents' => $documents
        ]);
    }

    public function deleteComplianceDocumentSession(Request $request)
    {
        $documentId = $request->input('document_id');
        $documents = session('company_compliance_documents', []);

        $documents = array_filter($documents, function ($doc) use ($documentId) {
            if ($doc['id'] === $documentId && $doc['attachment']) {
                // Delete temporary file
                $filePath = public_path($doc['attachment']);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            return $doc['id'] !== $documentId;
        });

        session(['company_compliance_documents' => array_values($documents)]);

        return response()->json([
            'ok' => true,
            'message' => 'Document deleted successfully'
        ]);
    }

    public function saveAllComplianceDocuments(Request $request)
    {
        DB::beginTransaction();

        try {
            $companyId = $request->input('company_id');
            $company = SysCompany::findOrFail($companyId);

            // Only save if non-UAE country
            if ($company->country == '231') {
                return response()->json(['ok' => true, 'message' => 'UAE compliance handled separately']);
            }

            $documents = session('company_compliance_documents', []);

            if (empty($documents)) {
                return response()->json(['ok' => true, 'message' => 'No compliance documents to save']);
            }

            $savedCount = 0;
            foreach ($documents as $doc) {
                // Move temp file to permanent location if exists
                $finalAttachmentPath = null;
                if ($doc['attachment']) {
                    $tempPath = public_path($doc['attachment']);
                    if (file_exists($tempPath)) {
                        $permanentDir = public_path('uploads/compliance');
                        if (!is_dir($permanentDir)) {
                            mkdir($permanentDir, 0777, true);
                        }

                        $fileName = basename($doc['attachment']);
                        $permanentPath = $permanentDir . '/' . $fileName;

                        if (rename($tempPath, $permanentPath)) {
                            $finalAttachmentPath = "uploads/compliance/{$fileName}";
                        }
                    }
                }

                // Save basic compliance record for non-UAE
                SysCompanyCompliance::updateOrCreate(
                    ['company_id' => $companyId],
                    [
                        'trade_license_no' => $doc['document_number'],
                        'license_issue_date' => $doc['issue_date'],
                        'license_expiry_date' => $doc['expiry_date'],
                        'vat_issuing_authority' => $doc['issuing_authority'], // Reusing this field for issuing authority
                        'business_license_upload' => $finalAttachmentPath,
                        // Set other UAE-specific fields to safe defaults
                        'vat_registration_number' => 'N/A',
                        'vat_percentage' => 0,
                        'vat_date' => null,
                        'corporate_tax_number' => null,
                        'corporate_tax_date' => null,
                        'corporate_tax_vat' => null,
                        'corporate_issuing_authority' => null,
                        'issuing_authority' => $doc['issuing_authority']
                    ]
                );

                $savedCount++;
            }

            DB::commit();

            // Clear session after saving
            session()->forget('company_compliance_documents');

            return response()->json([
                'ok' => true,
                'message' => 'Compliance documents saved successfully',
                'count' => $savedCount
            ]);

        } catch (\Exception $ex) {
            DB::rollBack();

            Log::error('saveAllComplianceDocuments failed: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Server error while saving compliance documents',
                'error' => $ex->getMessage()
            ], 500);
        }
    }

    // =====================================================================
    // DOCUMENT SESSION MANAGEMENT FOR PEOPLE (Owner, Sponsor, Contact)
    // =====================================================================

    public function storeDocumentSession(Request $request)
    {
        try {
            Log::info('storeDocumentSession called with method: ' . $request->method());
            Log::info('storeDocumentSession called with data: ' . json_encode($request->all()));

            // If GET request, return test data
            if ($request->method() === 'GET') {
                return response()->json([
                    'ok' => true,
                    'message' => 'Route is working for GET requests',
                    'method' => $request->method(),
                    'url' => $request->url(),
                    'session_id' => session()->getId()
                ]);
            }

            $request->validate([
                'document_name' => 'required|string|max:255',
                'document_number' => 'required|string|max:100',
                'document_date' => 'nullable|',
                'expiry_date' => 'nullable|',
                'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:5120',
                'person_type' => 'required|in:owner,sponsor,contact',
                'person_index' => 'required|integer'
            ]);

            $documentSessions = session('documentSessions', []);
            Log::info('Current session state: ' . json_encode($documentSessions));

            // Handle file upload if provided
            $attachmentPath = null;
            $storedFileName = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');

                // Use original filename but sanitize it to prevent traversal and invalid chars
                $originalName = basename($file->getClientOriginalName());
                $originalName = preg_replace('/[^A-Za-z0-9_\.-]/', '_', $originalName);

                $extension = $file->getClientOriginalExtension();
                $baseName = pathinfo($originalName, PATHINFO_FILENAME);

                // Ensure upload directory exists
                $uploadPath = public_path('uploads/company/people/temp');
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                // Avoid collisions: if file exists, append counter
                $finalName = $originalName;
                $counter = 0;
                while (file_exists($uploadPath . DIRECTORY_SEPARATOR . $finalName)) {
                    $counter++;
                    $finalName = $baseName . '_' . $counter . '.' . $extension;
                }

                // Move file
                $file->move($uploadPath, $finalName);
                $attachmentPath = 'uploads/company/people/temp/' . $finalName;
                $storedFileName = $finalName; // filename only (what we'll expose in the session)

                Log::info('File uploaded to: ' . $attachmentPath . ' (stored as: ' . $storedFileName . ')');
            }

            // If document_id present, update existing document in session
            if ($request->filled('document_id')) {
                $docId = $request->document_id;
                $found = false;
                if (isset($documentSessions[$request->person_type][$request->person_index])) {
                    foreach ($documentSessions[$request->person_type][$request->person_index] as $i => $existing) {
                        if ($existing['id'] === $docId) {
                            // Delete old file if replaced
                            if ($request->hasFile('attachment')) {
                                // remove old file
                                if (!empty($existing['attachment_path']) && file_exists(public_path($existing['attachment_path']))) {
                                    @unlink(public_path($existing['attachment_path']));
                                } else {
                                    $candidate = public_path('uploads/company/people/temp/' . ($existing['attachment'] ?? ''));
                                    if (file_exists($candidate))
                                        @unlink($candidate);
                                }
                            }

                            // Update fields
                            $documentSessions[$request->person_type][$request->person_index][$i]['name'] = $request->document_name;
                            $documentSessions[$request->person_type][$request->person_index][$i]['number'] = $request->document_number;
                            $documentSessions[$request->person_type][$request->person_index][$i]['date'] = SysHelper::normalizeToYmd($request->document_date);
                            $documentSessions[$request->person_type][$request->person_index][$i]['expiry_date'] = SysHelper::normalizeToYmd($request->expiry_date);

                            if ($request->hasFile('attachment')) {
                                $documentSessions[$request->person_type][$request->person_index][$i]['attachment'] = $storedFileName;
                                $documentSessions[$request->person_type][$request->person_index][$i]['attachment_name'] = ($originalName ?? $request->file('attachment')->getClientOriginalName());
                                $documentSessions[$request->person_type][$request->person_index][$i]['attachment_path'] = $attachmentPath;
                            }

                            $document = $documentSessions[$request->person_type][$request->person_index][$i];
                            $found = true;
                            break;
                        }
                    }
                }

                if ($found) {
                    session(['documentSessions' => $documentSessions]);
                    session()->save();

                    Log::info('Updated session state (after update): ' . json_encode($documentSessions));

                    return response()->json([
                        'ok' => true,
                        'document' => $document,
                        'updated' => true,
                        'message' => 'Document updated in session successfully'
                    ]);
                } else {
                    return response()->json([
                        'ok' => false,
                        'message' => 'Document to update not found in session'
                    ], 404);
                }
            }

            // Create new document
            $document = [
                'id' => uniqid(),
                'name' => $request->document_name,
                'number' => $request->document_number,
                'date' => SysHelper::normalizeToYmd($request->document_date),
                'expiry_date' => SysHelper::normalizeToYmd($request->expiry_date),
                // Expose filename only to client (stored file name). Keep original name separate.
                'attachment' => $storedFileName,
                'attachment_name' => $request->hasFile('attachment') ? ($originalName ?? $request->file('attachment')->getClientOriginalName()) : null,
                // Internal path for server-side operations
                'attachment_path' => $attachmentPath
            ];

            Log::info('Document created: ' . json_encode($document));

            // Initialize session structure if not exists
            if (!isset($documentSessions[$request->person_type])) {
                $documentSessions[$request->person_type] = [];
            }
            if (!isset($documentSessions[$request->person_type][$request->person_index])) {
                $documentSessions[$request->person_type][$request->person_index] = [];
            }

            $documentSessions[$request->person_type][$request->person_index][] = $document;
            session(['documentSessions' => $documentSessions]);

            // Force session to save immediately
            session()->save();

            Log::info('Updated session state: ' . json_encode($documentSessions));

            // Immediately check if session was saved
            Log::info('Verification - session after save: ' . json_encode(session('documentSessions', [])));
            Log::info('Session ID: ' . session()->getId());
            Log::info('Session driver: ' . config('session.driver'));

            return response()->json([
                'ok' => true,
                'document' => $document,
                'message' => 'Document saved to session successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in storeDocumentSession: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'ok' => false,
                'error' => $e->getMessage(),
                'message' => 'Failed to save document'
            ], 500);
        }
    }

    public function getDocumentSession(Request $request)
    {
        $request->validate([
            'person_type' => 'required|in:owner,sponsor,contact',
            'person_index' => 'required|integer'
        ]);

        $documentSessions = session('documentSessions', []);
        $documents = $documentSessions[$request->person_type][$request->person_index] ?? [];

        return response()->json([
            'documents' => $documents
        ]);
    }

    public function deleteDocumentSession(Request $request)
    {
        $request->validate([
            'person_type' => 'required|in:owner,sponsor,contact',
            'person_index' => 'required|integer',
            'document_id' => 'required|string'
        ]);

        $documentSessions = session('documentSessions', []);

        if (isset($documentSessions[$request->person_type][$request->person_index])) {
            $documents = &$documentSessions[$request->person_type][$request->person_index];

            foreach ($documents as $index => $doc) {
                if ($doc['id'] === $request->document_id) {
                    // Delete temporary file if exists. Support both full path and filename-only stored in session.
                    if (!empty($doc['attachment'])) {
                        // If attachment_path is available, prefer that
                        $pathToDelete = null;
                        if (!empty($doc['attachment_path']) && file_exists(public_path($doc['attachment_path']))) {
                            $pathToDelete = public_path($doc['attachment_path']);
                        } else {
                            // If only filename is present, look into the temp folder
                            $candidate = public_path('uploads/company/people/temp/' . $doc['attachment']);
                            if (file_exists($candidate)) {
                                $pathToDelete = $candidate;
                            }
                        }

                        if ($pathToDelete) {
                            @unlink($pathToDelete);
                        }
                    }

                    array_splice($documents, $index, 1);
                    break;
                }
            }
        }

        session(['documentSessions' => $documentSessions]);

        return response()->json([
            'ok' => true
        ]);
    }

    // Debug endpoint to check session data
    public function debugSession(Request $request)
    {
        $documentSessions = session('documentSessions', []);
        return response()->json([
            'session_data' => $documentSessions,
            'all_session' => session()->all()
        ]);
    }

    // Method to ensure the table exists
    public function ensureTableExists()
    {
        try {
            // Create the documents table if it doesn't exist
            if (!Schema::hasTable('sys_company_people_documents')) {
                Schema::create('sys_company_people_documents', function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('people_id')->unsigned();
                    $table->string('document_name', 255)->nullable();
                    $table->string('document_no', 100)->nullable();
                    $table->date('issue_date')->nullable();
                    $table->date('expiry_date')->nullable();
                    $table->string('attachment', 255)->nullable();
                    $table->timestamps();

                    $table->index('people_id');
                });

                return response()->json(['message' => 'Table created successfully']);
            }

            // Add share_percentage column if it doesn't exist
            if (Schema::hasTable('sys_company_people') && !Schema::hasColumn('sys_company_people', 'share_percentage')) {
                Schema::table('sys_company_people', function (Blueprint $table) {
                    $table->decimal('share_percentage', 5, 2)->nullable()->after('designation');
                });

                return response()->json(['message' => 'Column added successfully']);
            }

            return response()->json(['message' => 'Tables already exist']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store non-UAE document in session (Company Documents)
     */
    public function storeCompanyDocumentSession(Request $r)
    {
        try {
            $r->validate([
                'company_id' => 'required|integer',
                'document_name' => 'required|string|max:150',
                'document_number' => 'nullable|string|max:100',
                'document_date' => 'nullable|string',
                'expiry_date' => 'nullable|string',
                'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:5120',
            ]);

            $cid = $r->company_id;
            $sessionKey = 'company_document_items_' . $cid;
            $items = session($sessionKey, []);

            // Handle file upload
            $attachmentPath = null;
            if ($r->hasFile('attachment')) {
                $dir = public_path('uploads/company/documents');
                if (!is_dir($dir))
                    mkdir($dir, 0777, true);

                $file = $r->file('attachment');
                $name = uniqid('doc_') . '.' . $file->getClientOriginalExtension();
                $file->move($dir, $name);
                $attachmentPath = 'uploads/company/documents/' . $name;
            }

            // Create session item
            $item = [
                'id' => uniqid('tmp_'),
                'company_id' => $cid,
                'document_name' => $r->document_name,
                'document_number' => $r->document_number,
                'document_date' => $r->document_date,
                'expiry_date' => $r->expiry_date,
                'attachment_file' => $attachmentPath,
            ];

            $items[] = $item;
            session([$sessionKey => $items]);

            return response()->json([
                'ok' => true,
                'message' => 'Document added to session',
                'item' => $item
            ]);
        } catch (\Exception $e) {
            Log::error('storeCompanyDocumentSession failed', [
                'error' => $e->getMessage(),
                'request' => $r->all()
            ]);
            return response()->json([
                'ok' => false,
                'message' => 'Failed to add document'
            ], 400);
        }
    }

    /**
     * Get non-UAE documents from session (Company Documents)
     */
    public function getCompanyDocumentSession(Request $r)
    {
        try {
            $cid = $r->company_id;
            $sessionKey = 'company_document_items_' . $cid;
            $items = session($sessionKey, []);

            return response()->json([
                'ok' => true,
                'items' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Failed to retrieve documents'
            ], 400);
        }
    }

    /**
     * Delete non-UAE document from session (Company Documents)
     */
    public function deleteCompanyDocumentSession(Request $r)
    {
        try {
            $cid = $r->company_id;
            $rowId = $r->row_id;
            $sessionKey = 'company_document_items_' . $cid;
            $items = session($sessionKey, []);

            // Remove item by id
            $items = array_filter($items, function ($item) use ($rowId) {
                return $item['id'] !== $rowId;
            });
            $items = array_values($items); // re-index

            session([$sessionKey => $items]);

            return response()->json([
                'ok' => true,
                'message' => 'Document removed from session'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Failed to delete document'
            ], 400);
        }
    }

    /**
     * Clear non-UAE documents from session (Company Documents)
     */
    public function clearCompanyDocumentSession(Request $r)
    {
        try {
            $cid = $r->company_id;
            $sessionKey = 'company_document_items_' . $cid;
            session()->forget($sessionKey);

            return response()->json([
                'ok' => true,
                'message' => 'Documents cleared'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Failed to clear documents'
            ], 400);
        }
    }

    /**
     * Persist non-UAE documents from session to database (Company Documents)
     */
    public function persistCompanyDocuments(Request $r)
    {
        try {
            $cid = $r->company_id;
            $sessionKey = 'company_document_items_' . $cid;
            $items = session($sessionKey, []);

            if (empty($items)) {
                // Delete all items for this company
                SysCompanyDocumentItem::where('company_id', $cid)->delete();
                session()->forget($sessionKey);

                return response()->json([
                    'ok' => true,
                    'message' => 'Documents cleared from database'
                ]);
            }

            // Delete existing items for this company and re-insert from session
            SysCompanyDocumentItem::where('company_id', $cid)->delete();

            $itemsToInsert = [];
            foreach ($items as $item) {
                $itemsToInsert[] = [
                    'company_id' => $cid,
                    'document_name' => $item['document_name'],
                    'document_number' => $item['document_number'],
                    'document_date' => !empty($item['document_date']) ? $item['document_date'] : null,
                    'expiry_date' => !empty($item['expiry_date']) ? $item['expiry_date'] : null,
                    'attachment_file' => $item['attachment_file'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($itemsToInsert)) {
                SysCompanyDocumentItem::insert($itemsToInsert);
            }

            // Clear session
            session()->forget($sessionKey);

            return response()->json([
                'ok' => true,
                'message' => 'Documents saved successfully',
                'count' => count($itemsToInsert)
            ]);
        } catch (\Exception $e) {
            Log::error('persistCompanyDocuments failed', [
                'error' => $e->getMessage(),
                'request' => $r->all()
            ]);
            return response()->json([
                'ok' => false,
                'message' => 'Failed to save documents: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get company documents for AJAX request
     */
    public function getDocuments($companyId)
    {
        try {
            $documents = SysCompanyDocumentItem::where('company_id', $companyId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'documents' => $documents
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching documents: ' . $e->getMessage()
            ], 500);
        }
    }




    // new functions


    public function storeBasic(Request $request)
    {

        //   dd(
        //     $request->all()
        //   );
        DB::beginTransaction();
        try {
            // =====================================================
            // 1. CREATE MAIN COMPANY RECORD
            // =====================================================

            $items = [];
            $company = new SysCompany();

            // Basic Company Information
            $company->company_name = $request->company_name ?: null;
            $company->trade_name = $request->trade_name ?: null;
            $company->company_code = $request->company_code ?: null;
            $company->business_entity_type_id = $request->business_entity_type_id ?: null;
            $company->industry_type_id = $request->industry_type_id ?: null;
            $company->business_sector_id = $request->business_sector_id ?: null;
            $company->date_of_incorporation = $request->date_of_incorporation ? $this->parseDate($request->date_of_incorporation) : null;
            $company->company_type = $request->company_type ?: null;
            $company->other_code = $request->other_code ?: null;

            // Parent Company Logic
            if ($request->company_type === 'parent') {
                $company->parent_company = $request->parent_company;
            } elseif (in_array($request->company_type, ['subsidiary', 'branch', 'sub_branch'])) {
                $company->parent_company_id = $request->parent_company_id;
            }

            // Address Information
            $company->country = $request->country ?: null;
            $company->state = $request->state ?: null;
            $company->city = $request->city ?: null;
            $company->area = $request->area ?: null;
            $company->building_no = $request->building_no ?: null;
            $company->floor_shop_no = $request->floor_shop_no ?: null;
            $company->company_address = $request->company_address ?: null;

            // Contact Information
            $company->email = $request->email ?: null;
            $company->website = $request->website ?: null;
            $company->telephone = $request->telephone ?: null;
            $company->mobile_code = $request->mobile_code ?: null;
            $company->mobile = $request->mobile ?: null;
            $company->fax = $request->fax ?: null;
            // Social Media
            $company->linkedin = $request->linkedin ?: null;
            $company->facebook = $request->facebook ?: null;
            $company->instagram = $request->instagram ?: null;
            $company->twitter_x = $request->twitter_x ?: null;
            $company->youtube = $request->youtube ?: null;
            $company->other_social = $request->other_social ?: null;

            // File Uploads (Logo, Stamp, Profile)
            $company->company_logo = $this->uploadSingleFile($request, 'company_logo', 'uploads/company/logos');
            $company->digital_stamp = $this->uploadSingleFile($request, 'digital_stamp', 'uploads/company/stamps');
            $company->company_profile = $this->uploadSingleFile($request, 'company_profile', 'uploads/company/profiles');


            $company->created_by = Auth::id();
            $company->save();

            $companyId = $company->id;

            // =====================================================
            // 2. COMPANY SETTINGS
            // =====================================================
            $setting = new SysCompanySetting();
            $setting->company_id = $companyId;
            $setting->currency = $request->currency ?: null;
            $setting->currency_symbol = $request->currency_symbol ?: null;
            $setting->currency_digit = $request->currency_digit ?: null;
            $setting->r_code = $request->r_code ?: null;
            $setting->p_code = $request->p_code ?: null;
            // normalize book_closed to Y-m-d
            $setting->book_closed = $this->parseDate($request->book_closed);
            $setting->sales_code = $request->sales_code ?: null;

            $setting->other_code = $request->other_code ?: null;
            $setting->is_customer_code = $request->has('is_customer_code') ? 1 : 0;
            $setting->is_supplier_code = $request->has('is_supplier_code') ? 1 : 0;
            $setting->is_account_code = $request->has('is_account_code') ? 1 : 0;
            $setting->is_subaccount_code = $request->has('is_subaccount_code') ? 1 : 0;
            $setting->save();

            // =====================================================
            // 3. UAE COMPLIANCE
            // =====================================================
            if ($request->country == 'UAE' || $request->country == '231') { // 231 is UAE ID
                $compliance = new SysCompanyCompliance();
                $compliance->company_id = $companyId;
                $compliance->trade_license_no = $request->trade_license_no ?: null;
                $compliance->license_issue_date = $this->parseDate($request->license_issue_date);
                $compliance->license_expiry_date = $this->parseDate($request->license_expiry_date);
                $compliance->issuing_authority = $request->issuing_authority ?: null;
                $compliance->business_license_upload = $this->uploadSingleFile($request, 'business_license_upload', 'uploads/company/compliance');

                // Tax Information
                $compliance->tax_applicable = $request->tax_applicable ?: null;
                $compliance->vat_registration_number = $request->vat_registration_number ?: null;
                $compliance->vat_percentage = $request->vat_percentage ?: null;
                $compliance->vat_date = $this->parseDate($request->vat_date);
                $compliance->vat_issuing_authority = $request->vat_issuing_authority ?: null;
                $compliance->vat_certificate = $this->uploadSingleFile($request, 'vat_certificate', 'uploads/company/compliance');

                // Corporate Tax
                $compliance->corporate_tax_number = $request->corporate_tax_number ?: null;
                $compliance->corporate_tax_vat = $request->corporate_tax_vat ?: null;
                $compliance->corporate_tax_date = $this->parseDate($request->corporate_tax_date);
                // accept either new name or legacy input name
                $compliance->corporate_issuing_authority = $request->ct_issuing_authority ?? null;
                $compliance->corporate_tax_certificate = $this->uploadSingleFile($request, 'corporate_tax_certificate', 'uploads/company/compliance');

                $compliance->save();

                // Persist UAE-specific company document cards into sys_company_documents
                SysCompanyDocument::updateOrCreate(
                    ['company_id' => $companyId],
                    [
                        'establishment_file' => $this->uploadSingleFile($request, 'establishment_file', 'uploads/company/compliance'),
                        'establishment_expiry' => $this->parseDate($request->establishment_expiry),
                        'establishment_number' => $request->establishment_number ?: null,
                        'establishment_start_date' => $request->establishment_date ? $this->parseDate($request->establishment_date) : null,

                        'immigration_file' => $this->uploadSingleFile($request, 'immigration_file', 'uploads/company/compliance'),
                        'immigration_expiry' => $this->parseDate($request->immigration_expiry),
                        'immigration_number' => $request->immigration_number ?: null,
                        'immigration_start_date' => $request->immigration_date ? $this->parseDate($request->immigration_date) : null,

                        'labour_file' => $this->uploadSingleFile($request, 'labour_file', 'uploads/company/compliance'),
                        'labour_expiry' => $this->parseDate($request->labour_expiry),
                        'labour_number' => $request->labour_number ?: null,
                        'labour_start_date' => $request->labour_date ? $this->parseDate($request->labour_date) : null,

                        'chamber_file' => $this->uploadSingleFile($request, 'chamber_file', 'uploads/company/compliance'),
                        'chamber_expiry' => $this->parseDate($request->chamber_expiry),
                        'chamber_number' => $request->chamber_number ?: null,
                        'chamber_start_date' => $request->chamber_date ? $this->parseDate($request->chamber_date) : null,

                        'insurance_file' => $this->uploadSingleFile($request, 'insurance_file', 'uploads/company/compliance'),
                        'insurance_certificate_expiry' => $this->parseDate($request->insurance_certificate_expiry),
                        'insurance_certificate_number' => $request->insurance_certificate_number ?: null,
                        'insurance_start_date' => $request->insurance_certificate_date ? $this->parseDate($request->insurance_certificate_date) : null,

                        'moa_aoa_file' => $this->uploadSingleFile($request, 'moa_aoa_file', 'uploads/company/compliance'),
                        'moa_aoa_expiry' => $this->parseDate($request->moa_aoa_expiry),
                        'moa_aoa_number' => $request->moa_aoa_number ?: null,


                        'board_resolution_file' => $this->uploadSingleFile($request, 'board_resolution_file', 'uploads/company/compliance'),
                        'board_resolution_expiry' => $this->parseDate($request->board_resolution_expiry),
                        'board_resolution_number' => $request->board_resolution_number ?: null,

                        'poa_file' => $this->uploadSingleFile($request, 'poa_file', 'uploads/company/compliance'),
                        'poa_expiry' => $this->parseDate($request->poa_expiry),
                        'poa_number' => $request->poa_number ?: null,
                    ]
                );
            } else {

                // Non-UAE: handle dynamic compliance documents submitted via the form as
                // compliance_documents[<i>][document_number|issue_date|expiry_date|issuing_authority|attachment]
                if ($request->has('compliance_documents') && is_array($request->compliance_documents)) {




                    foreach ($request->compliance_documents as $idx => $doc) {


                        $file = $request->hasFile("compliance_documents.$idx.attachment")
                            ? $this->uploadSingleFile($request, "compliance_documents.$idx.attachment", 'uploads/company/compliance')
                            : ($doc['attachment'] ?? null);

                        if ($doc['document_number'] || $file) {
                            $items[] = [
                                'company_id' => $companyId,
                                'document_type' => 'compliance',
                                'document_name' => $doc['issuing_authority'] ?? null,
                                'document_number' => $doc['document_number'] ?? null,
                                'document_date' => $this->parseDate($doc['issue_date'] ?? null),
                                'expiry_date' => $this->parseDate($doc['expiry_date'] ?? null),
                                'attachment_file' => $file,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }


                    }
                }
            }

            // =====================================================
            // 4. HR PAYROLL SETTINGS
            // =====================================================
            $hrPayroll = new SysCompanyHrPayrollSetting();
            $hrPayroll->company_id = $companyId;

            // Leave Policy
            $hrPayroll->leave_policy_type = $request->leave_policy_type ?: null;
            // $hrPayroll->annual_leave_cl_sl = $request->annual_leave ?: null;
            // $hrPayroll->sick_leave_sl = $request->sick_leave ?: null;
            // $hrPayroll->casual_leave_cl = $request->casual_leave ?: null;
            // $hrPayroll->comp_off_allowed = $request->has('comp_off_allowed') ? 1 : 0;
            // $hrPayroll->carry_forward_unused_leaves = $request->has('carry_forward') ? 1 : 0;
            // $hrPayroll->max_carry_forward_days = $request->max_carry_forward ?: null;
            // $hrPayroll->encashable_leaves = $request->has('leave_encashment') ? 1 : 0;

            $hrPayroll->annual_leave_cl_sl =
                $request->filled('annual_leave') ? (int) $request->annual_leave : null;

            $hrPayroll->sick_leave_sl =
                $request->filled('sick_leave') ? (int) $request->sick_leave : null;

            $hrPayroll->casual_leave_cl =
                $request->filled('casual_leave') ? (int) $request->casual_leave : null;




            // $hrPayroll->comp_off_allowed = $request->has('comp_off_allowed') ? 1 : 0;
            // $hrPayroll->carry_forward_unused_leaves = $request->has('carry_forward') ? 1 : 0;
            // $hrPayroll->max_carry_forward_days = $request->max_carry_forward ?: null;
            // $hrPayroll->encashable_leaves = $request->has('leave_encashment') ? 1 : 0;

            $hrPayroll->comp_off_allowed = ($request->input('comp_off_allowed') === 'yes') ? 1 : 0;

            $hrPayroll->carry_forward_unused_leaves =
                ($request->input('carry_forward') === 'yes') ? 1 : 0;

            $hrPayroll->max_carry_forward_days =
                $request->filled('max_carry_forward') ? (int) $request->max_carry_forward : null;

            $hrPayroll->encashable_leaves =
                ($request->input('leave_encashment') === 'yes') ? 1 : 0;

            // Attendance Policy
            $hrPayroll->attendance_policy = $request->attendance_policy ?: null;
            $hrPayroll->minimum_working_hours = $request->min_working_hours ?: null;
            $hrPayroll->grace_period_minutes = $request->grace_period ?: null;
            $hrPayroll->half_day_after_hours = $request->half_day_after ?: null;
            // Accept both form variants: blade uses 'absent_below_hours' while backend historically used 'absent_if_below'
            $hrPayroll->absent_if_hours_below = $request->absent_below_hours ?: null;
            // Accept both 'late_mark_count' and blade's 'late_mark_allowed'
            $hrPayroll->late_mark_count_allowed = $request->late_mark_allowed ?: null;
            // Accept both 'consecutive_late_halfday' and blade's 'late_mark_halfday'
            $hrPayroll->consecutive_late_to_halfday = $request->late_mark_halfday ?: null;
            $hrPayroll->auto_mark_absent_after_days = $request->auto_absent_after ?: null;

            // Working Days
            $hrPayroll->weekly_off_days = $request->has('hr_weekly_off') ? json_encode($request->hr_weekly_off) : null;

            // WPS Settings (model fields: wps_*)
            $hrPayroll->wps_establishment_id = $request->hr_wps_establishment_id ?: null;

            $hrPayroll->wps_salary_file_code = $request->hr_wps_salary_file_code ?: null;

            // Payroll Cycle (model fields: payroll_*_day)
            $hrPayroll->payroll_cycle = $request->hr_payroll_cycle ?: null;
            $hrPayroll->payroll_start_day = $request->hr_payroll_start ?: null;
            $hrPayroll->payroll_end_day = $request->hr_payroll_end ?: null;

            // Gratuity method (model field: gratuity_calculation_method)
            $hrPayroll->gratuity_calculation_method = $request->hr_gratuity_method ?: null;

            $hrPayroll->save();

            // =====================================================
            // 5. OWNERS (Dynamic)
            // =====================================================
            if ($request->has('owners') && is_array($request->owners)) {


                foreach ($request->owners as $index => $owner) {
                    if (!isset($owner['first_name']) || trim($owner['first_name']) === '') {
                        continue; // Skip if first_name is not set or empty
                    }
                    // Normalize and ignore rows where all main fields are empty (defensive)
                    $first = trim($owner['first_name'] ?? '');
                    $last = trim($owner['last_name'] ?? '');
                    $mobile = trim($owner['mobile'] ?? '');
                    $email = trim($owner['email'] ?? '');
                    if ($first === '' && $last === '' && $mobile === '' && $email === '')
                        continue;

                    $person = new SysCompanyPeople();
                    $person->company_id = $companyId;
                    $person->type = 'owner';
                    $person->salutation = $owner['salutation'] ?? null;
                    $person->first_name = $owner['first_name'];
                    $person->last_name = $owner['last_name'] ?? null;
                    $person->mobile = $owner['mobile'] ?? null;
                    $person->email = $owner['email'] ?? null;
                    $person->designation = $owner['designation_id'] ?? null;
                    $person->share_percentage = $owner['share_percentage'] ?? 0;
                    $person->save();

                    $staff = new SmStaff();
                    $staff->staff_no = SysHelper::get_new_staff_code();
                    $staff->employee_salutation = $owner['salutation'] ?? null;
                    $staff->first_name = $owner['first_name'];
                    $staff->last_name = $owner['last_name'] ?? null;
                    $staff->full_name = trim(implode(' ', array_filter([$owner['first_name'], $owner['last_name'] ?? null])));
                    $staff->mobile = $owner['mobile'] ?? null;
                    $staff->email = $owner['email'] ?? null;
                    $staff->designation_id = (int) ($owner['designation_id'] ?? 0);
                    $staff->created_at = now();
                    $staff->updated_at = now();


                    // 2) Create or update user and link
                    $user = User::where('email', $staff->email)->first();
                    if (!$user)
                        $user = new User();
                    $fullName = trim(implode(' ', array_filter([$staff->first_name, $staff->last_name])));
                    $username = $request->input('username') ?: (strpos($staff->email, '@') !== false ? strstr($staff->email, '@', true) : Str::slug($fullName, '_'));

                    $user->role_id = 2;
                    $user->full_name = $fullName;
                    $user->username = $username;
                    $user->email = $staff->email;
                    $user->usertype = $request->input('usertype', 'staff');
                    $user->access_status = 1;
                    $user->password = Hash::make($owner['first_name']);
                    $user->save();

                    $staff->user_id = $user->id;
                    $staff->save();


                    $jobPayload = [
                        'staff_id' => $staff->id,
                        'date_of_joining' => now(),
                        'designation_id' => $request->input('designation_id'),
                        'grade' => 'g1',
                        'role_id' => 2,

                    ];

                    $job = SmStaffJobDetail::create($jobPayload);

                    $job->save();

                    // Save owner documents if any
                    $this->savePersonDocumentsArray($person->id, $request, "owners.$index", 'owner');
                }
            }

            // =====================================================
            // 6. SPONSORS (Dynamic)
            // =====================================================
            if ($request->has('sponsors') && is_array($request->sponsors)) {
                foreach ($request->sponsors as $index => $sponsor) {
                    // Normalize and ignore rows where all main fields are empty (defensive)
                    $first = trim($sponsor['first_name'] ?? '');
                    $last = trim($sponsor['last_name'] ?? '');
                    $mobile = trim($sponsor['mobile'] ?? '');
                    $email = trim($sponsor['email'] ?? '');
                    if ($first === '' && $last === '' && $mobile === '' && $email === '')
                        continue;

                    $person = new SysCompanyPeople();
                    $person->company_id = $companyId;
                    $person->type = 'sponsor';
                    $person->salutation = $sponsor['salutation'] ?? null;
                    $person->first_name = $sponsor['first_name'];
                    $person->last_name = $sponsor['last_name'] ?? null;
                    $person->mobile = $sponsor['mobile'] ?? null;
                    $person->email = $sponsor['email'] ?? null;
                    // $person->nationality_id = $sponsor['nationality_id'] ?? null;
                    $person->save();

                    // Save sponsor documents
                    $this->savePersonDocumentsArray($person->id, $request, "sponsors.$index", 'sponsor');
                }
            }

            // =====================================================
            // 7. CONTACTS (Dynamic)
            // =====================================================
            if ($request->has('contacts') && is_array($request->contacts)) {
                foreach ($request->contacts as $index => $contact) {
                    // Normalize and ignore rows where all main fields are empty (defensive)
                    $first = trim($contact['first_name'] ?? '');
                    $last = trim($contact['last_name'] ?? '');
                    $mobile = trim($contact['mobile'] ?? '');
                    $email = trim($contact['email'] ?? '');
                    if ($first === '' && $last === '' && $mobile === '' && $email === '')
                        continue;

                    $person = new SysCompanyPeople();
                    $person->company_id = $companyId;
                    $person->type = 'contact';
                    $person->salutation = $contact['salutation'] ?? null;
                    $person->first_name = $contact['first_name'];
                    $person->last_name = $contact['last_name'] ?? null;
                    $person->mobile = $contact['mobile'] ?? null;
                    $person->email = $contact['email'] ?? null;
                    $person->designation = $contact['designation'] ?? null;
                    $person->save();

                    // Save contact documents
                    $this->savePersonDocumentsArray($person->id, $request, "contacts.$index", 'contact');
                }
            }

            // =====================================================
            // 8. BANKING (Dynamic) - from hidden inputs
            // =====================================================
            $bankCount = 0;
            while ($request->has("banks.$bankCount.bank_name") || $request->hasFile("banks.$bankCount.bank_letter")) {
                // If both bank name and bank letter are empty, skip this index
                if (empty($request->input("banks.$bankCount.bank_name")) && !$request->hasFile("banks.$bankCount.bank_letter")) {
                    $bankCount++;
                    continue;
                }

                $banking = new SysCompanyBanking();
                $banking->company_id = $companyId;
                $banking->bank_name = $request->input("banks.$bankCount.bank_name") ?: '';
                $banking->branch_name = $request->input("banks.$bankCount.branch_name") ?: null;
                $banking->account_number = $request->input("banks.$bankCount.account_number") ?: null;
                $banking->iban_number = $request->input("banks.$bankCount.iban_number") ?: null;
                $banking->swift_code = $request->input("banks.$bankCount.swift_code") ?: null;
                $banking->finance_code = $request->input("banks.$bankCount.finance_code") ?: null;
                $banking->currency = $request->input("banks.$bankCount.currency") ?: null;

                // Bank letter file (save path or empty string so DB NOT NULL column is satisfied)
                if ($request->hasFile("banks.$bankCount.bank_letter")) {
                    $banking->bank_letter = $this->uploadSingleFile($request, "banks.$bankCount.bank_letter", 'uploads/company/banking');
                } else {
                    $banking->bank_letter = null;
                }

                $banking->save();
                $bankCount++;
            }




            if ($request->hr_wps_bank && is_array($request->hr_wps_bank)) {

                $banks_stored = SysCompanyBanking::where('company_id', $companyId)->get()->toArray();



                $result = array_map(function ($v) {
                    return explode('_', $v)[1] ?? null;
                }, $request->hr_wps_bank);

                // COLLECT BANK IDS BASED ON INDEXES
                $bankIds = [];


                foreach ($result as $index) {
                    if (isset($banks_stored[$index]['id'])) {
                        $bankIds[] = $banks_stored[$index]['id'];
                    }
                }

             



                $hrPayroll->wps_bank = json_encode($bankIds);

                $hrPayroll->save();
            }


            // =====================================================
            // 9. WAREHOUSES (Dynamic)
            // =====================================================
            $warehouseCount = 0;



            while ($request->has("warehouses.$warehouseCount.warehouse_name")) {

                $warehouse = new CompanyWarehouse();
                $warehouse->document_number = SysHelper::get_new_code_lead('company_warehouses', 'WH', 'document_number', session('logged_session_data.company_id'));
                $warehouse->company_id = $companyId;
                $warehouse->contact_person_id = $request->input("warehouses.$warehouseCount.contact_person_name") ?: null;
                $warehouse->warehouse_code = $request->input("warehouses.$warehouseCount.warehouse_code") ?: null;
                $warehouse->warehouse_name = $request->input("warehouses.$warehouseCount.warehouse_name") ?: null;
                // use model column names
                $warehouse->warehouse_country = $request->input("warehouses.$warehouseCount.warehouse_country") ?: null;
                $warehouse->warehouse_state = $request->input("warehouses.$warehouseCount.warehouse_state") ?: null;
                $warehouse->warehouse_city = $request->input("warehouses.$warehouseCount.warehouse_city") ?: null;
                $warehouse->warehouse_area = $request->input("warehouses.$warehouseCount.warehouse_area") ?: null;
                $warehouse->warehouse_building_name = $request->input("warehouses.$warehouseCount.warehouse_building_name") ?: null;
                $warehouse->warehouse_flat_office_no = $request->input("warehouses.$warehouseCount.warehouse_flat_office_no") ?: null;

                // Contact Person
                // $warehouse->contact_salutation = $request->input("warehouses.$warehouseCount.contact_salutation");
                // $warehouse->contact_first_name = $request->input("warehouses.$warehouseCount.contact_first_name");
                // $warehouse->contact_last_name = $request->input("warehouses.$warehouseCount.contact_last_name");
                $warehouse->contact_mobile = $request->input("warehouses.$warehouseCount.contact_mobile") ?: null;
                $warehouse->contact_email = $request->input("warehouses.$warehouseCount.contact_email") ?: null;
                $warehouse->contact_designation = $request->input("warehouses.$warehouseCount.contact_designation") ?: null;

                // Fire Safety
                $warehouse->fire_safety_compliance_status = $request->input("warehouses.$warehouseCount.fire_safety_compliance_status") ?: null;
                $warehouse->fire_noc_certificate_number = $request->input("warehouses.$warehouseCount.fire_noc_certificate_number") ?: null;
                $warehouse->safety_equipment_available = $request->input("warehouses.$warehouseCount.safety_equipment_available") ?: null;
                $warehouse->fire_noc_expiry_date = $this->parseDate($request->input("warehouses.$warehouseCount.fire_noc_expiry_date")) ?: null;
                $warehouse->last_safety_inspection_date = $this->parseDate($request->input("warehouses.$warehouseCount.last_safety_inspection_date")) ?: null;

                // Documents: store as array (model casts contact_documents => array)
                if ($request->hasFile("warehouses.$warehouseCount.contact_documents")) {
                    $filesStr = $this->uploadMultipleFiles($request, "warehouses.$warehouseCount.contact_documents", 'uploads/company/warehouse_docs');
                    $warehouse->contact_documents = $filesStr ? explode('|', $filesStr) : null;
                }

                $warehouse->save();
                $warehouseCount++;
            }

            // =====================================================
            // 10. COMPANY POLICIES (Dynamic)
            // =====================================================
            $policyCount = 0;
            while ($request->has("policies.$policyCount.policy_name")) {
                $policy = new SysCompanyHrPolicy();
                $policy->company_id = $companyId;
                $policy->policy_date = $this->parseDate($request->input("policies.$policyCount.policy_date"));
                $policy->policy_name = $request->input("policies.$policyCount.policy_name");
                $policy->policy_category = $request->input("policies.$policyCount.policy_category");
                $policy->policy_valid = $this->parseDate($request->input("policies.$policyCount.policy_valid"));
                $policy->view_to_employees = $request->input("policies.$policyCount.view_to_employees");
                $policy->policy_details = $request->input("policies.$policyCount.policy_details");

                // Policy file
                if ($request->hasFile("policies.$policyCount.policy_file")) {
                    $policy->policy_file = $this->uploadSingleFile($request, "policies.$policyCount.policy_file", 'uploads/company/policies');
                } else {
                    $policy->policy_file = null;
                }



                $policy->save();
                $policyCount++;
            }

            // =====================================================
            // 11. DYNAMIC DOCUMENT ROWS (UAE / Non-UAE)
            // Save any dynamic document rows into SysCompanyDocumentItem
            // =====================================================
            // try {
            //     // Remove existing items for company (we'll re-insert from request)
            //     SysCompanyDocumentItem::where('company_id', $companyId)->delete();

            //     $itemsToInsert = [];

            //     // Helper to process a document array (prefix: name/number/date/expiry/file)
            //     $processDocs = function ($docs, $prefixUploadPath) use (&$itemsToInsert, $request, $companyId) {
            //         if (!is_array($docs))
            //             return;
            //         foreach ($docs as $i => $d) {
            //             $docName = trim($d['name'] ?? '');
            //             $docNumber = trim($d['number'] ?? '');
            //             $docDate = !empty($d['date']) ? $d['date'] : null;
            //             $expiry = !empty($d['expiry']) ? $d['expiry'] : null;

            //             $attachmentPath = null;

            //             // Uploaded file via multipart
            //             if ($request->hasFile("{$d['__orig_key']}") || $request->hasFile("{$d['__key']}")) {
            //                 // This branch is unlikely; prefer explicit request name usage below
            //             }

            //             // Try standard name: e.g. uae_documents.0.file or non_uae_documents.0.file
            //             $fileField = ($d['__fieldName'] ?? null) ?: null;
            //             if ($fileField && $request->hasFile($fileField)) {
            //                 $attachmentPath = $this->uploadSingleFile($request, $fileField, $prefixUploadPath);
            //             } elseif (!empty($d['file'])) {
            //                 // Hidden input carrying existing path
            //                 $attachmentPath = $d['file'];
            //             }

            //             dd($attachmentPath);

            //             // insert when any meaningful info
            //             if ($docName || $docNumber || $attachmentPath) {
            //                 $itemsToInsert[] = [
            //                     'company_id' => $companyId,
            //                     'document_name' => $docName ?: null,
            //                     'document_number' => $docNumber ?: null,
            //                     'document_date' => $docDate ?: null,
            //                     'expiry_date' => $expiry ?: null,
            //                     'attachment_file' => $attachmentPath,
            //                     'created_at' => now(),
            //                     'updated_at' => now(),
            //                 ];
            //             }
            //         }
            //     };

            //     // The request carries uae_documents and non_uae_documents arrays with structure:
            //     // [{ name, number, date, expiry, file }]
            //     if ($request->has('uae_documents') && is_array($request->uae_documents)) {

            //         // Normalize file field name for each doc so processing can check request->hasFile correctly
            //         foreach ($request->uae_documents as $idx => &$doc) {
            //             $doc['__fieldName'] = "uae_documents.$idx.file";
            //         }
            //         unset($doc);

            //         $processDocs($request->uae_documents, 'uploads/company/compliance');
            //     }

            //     if ($request->has('non_uae_documents') && is_array($request->non_uae_documents)) {
            //         foreach ($request->non_uae_documents as $idx => &$doc) {
            //             $doc['__fieldName'] = "non_uae_documents.$idx.file";
            //         }
            //         unset($doc);
            //         $processDocs($request->non_uae_documents, 'uploads/company/compliance');
            //     }

            //     if (!empty($itemsToInsert)) {
            //         dd( $itemsToInsert);
            //         SysCompanyDocumentItem::insert($itemsToInsert);
            //     }
            // } catch (\Exception $ex) {
            //     Log::error('Saving dynamic documents failed: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            // }


            SysCompanyDocumentItem::where('company_id', $companyId)->delete();



            /* UAE documents */
            if ($request->has('uae_documents')) {
                foreach ($request->uae_documents as $i => $doc) {

                    $file = $request->hasFile("uae_documents.$i.file")
                        ? $this->uploadSingleFile($request, "uae_documents.$i.file", 'uploads/company/compliance')
                        : ($doc['file'] ?? null);

                    if ($doc['name'] || $doc['number'] || $file) {
                        $items[] = [
                            'company_id' => $companyId,
                            'document_type' => 'uae',
                            'document_name' => $doc['name'] ?? null,
                            'document_number' => $doc['number'] ?? null,
                            'document_date' => $this->parseDate($doc['date'] ?? null),
                            'expiry_date' => $this->parseDate($doc['expiry'] ?? null),
                            'attachment_file' => $file,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            /* NON-UAE documents */
            if ($request->has('non_uae_documents')) {
                foreach ($request->non_uae_documents as $i => $doc) {

                    $file = $request->hasFile("non_uae_documents.$i.file")
                        ? $this->uploadSingleFile($request, "non_uae_documents.$i.file", 'uploads/company/compliance')
                        : ($doc['file'] ?? null);

                    if ($doc['name'] || $doc['number'] || $file) {
                        $items[] = [
                            'company_id' => $companyId,
                            'document_type' => 'non_uae',
                            'document_name' => $doc['name'] ?? null,
                            'document_number' => $doc['number'] ?? null,
                            'document_date' => $this->parseDate($doc['date'] ?? null),
                            'expiry_date' => $this->parseDate($doc['expiry'] ?? null),
                            'attachment_file' => $file,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }



            if ($items) {
                SysCompanyDocumentItem::insert($items);
            }


            // store shifts


            if ($request->filled('working_shifts')) {
                foreach ($request->working_shifts as $shiftData) {

                    $start = Carbon::createFromFormat('H:i', $shiftData['start_time']);
                    $end = Carbon::createFromFormat('H:i', $shiftData['end_time']);

                    WorkingShift::create([
                        'company_id' => $company->id, // if applicable
                        'shift_name' => trim($shiftData['shift_name']),
                        'start_time' => $start->format('H:i:00'),
                        'end_time' => $end->format('H:i:00'),
                        'is_active' => 1,
                    ]);
                }
            }



            if ($request->filled('hr_weekly_off')) {
                foreach ($request->hr_weekly_off as $weeklyOff) {
                    WeeklyOff::create([
                        'company_id' => $company->id,
                        'name' => trim($weeklyOff),

                    ]);
                }
            }













            DB::commit();

            Toastr::success('Company created successfully with all details!', 'Success');
            return redirect('company?active=' . $company->id);

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);

            Toastr::error('Operation Failed: ' . $e->getMessage(), 'Failed');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Helper: Upload single file with original name
     */
    private function uploadSingleFile($request, $fieldName, $directory)
    {
        if (!$request->hasFile($fieldName)) {
            return null;
        }

        $file = $request->file($fieldName);
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
        $uniqueName = $nameWithoutExt . '_' . time() . '_' . uniqid() . '.' . $extension;

        $path = public_path($directory);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file->move($path, $uniqueName);
        return $directory . '/' . $uniqueName;
    }

    /**
     * Helper: Upload multiple files and return pipe-separated string
     */
    private function uploadMultipleFiles($request, $fieldName, $directory)
    {
        if (!$request->hasFile($fieldName)) {
            return null;
        }

        $files = $request->file($fieldName);
        if (!is_array($files)) {
            $files = [$files];
        }

        $uploadedFiles = [];
        $path = public_path($directory);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        foreach ($files as $i => $file) {
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $uniqueName = $nameWithoutExt . '_' . time() . '_' . $i . '.' . $extension;

            $file->move($path, $uniqueName);
            $uploadedFiles[] = $uniqueName;
        }

        return implode('|', $uploadedFiles);
    }

    /**
     * Helper: Parse date from d/m/Y or other formats
     */
    private function parseDate($dateString)
    {
        if (empty($dateString))
            return null;

        try {
            if (strpos($dateString, '/') !== false) {
                return Carbon::createFromFormat('d/m/Y', $dateString)->format('Y-m-d');
            }
            return Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Helper: Save person documents (owners/sponsors/contacts)
     */
    private function savePersonDocumentsArray($peopleId, $request, $baseKey, $type)
    {
        $docDir = public_path('uploads/company/people_docs');
        if (!file_exists($docDir))
            mkdir($docDir, 0777, true);

        // Iterate over any document entries present (either uploaded files or hidden fields)
        $docIndex = 0;
        while (
            $request->has("$baseKey.documents.$docIndex.name") ||
            $request->has("$baseKey.documents.$docIndex.number") ||
            $request->hasFile("$baseKey.documents.$docIndex.attachment") ||
            $request->has("$baseKey.documents.$docIndex.attachment")
        ) {
            $docName = $request->input("$baseKey.documents.$docIndex.name") ?? 'Document';
            $docNumber = $request->input("$baseKey.documents.$docIndex.number") ?? null;
            $issueDate = $this->parseDate($request->input("$baseKey.documents.$docIndex.issue_date"));
            $expiryDate = $this->parseDate($request->input("$baseKey.documents.$docIndex.expiry_date"));
            $attachmentPath = null;

            // If a new file was uploaded, move it to the docs folder
            if ($request->hasFile("$baseKey.documents.$docIndex.attachment")) {
                $file = $request->file("$baseKey.documents.$docIndex.attachment");
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $uniqueName = $type . '_doc_' . time() . '_' . uniqid() . '.' . $extension;

                $file->move($docDir, $uniqueName);
                $attachmentPath = 'uploads/company/people_docs/' . $uniqueName;
            } elseif ($request->has("$baseKey.documents.$docIndex.attachment")) {
                // Hidden input may contain existing path
                $attachmentPath = $request->input("$baseKey.documents.$docIndex.attachment") ?: null;
            }

            // Only create a DB record if there is meaningful data
            if ($docName || $docNumber || $attachmentPath) {
                SysCompanyPeopleDocument::create([
                    'people_id' => $peopleId,
                    'document_name' => $docName,
                    'document_no' => $docNumber,
                    'issue_date' => $issueDate,
                    'expiry_date' => $expiryDate,
                    'attachment' => $attachmentPath,
                ]);
            }

            $docIndex++;
        }
    }

    public function getcompanyShiftandWeekOff(Request $request)
    {
        $companyId = $request->company_id;

        $shifts = WorkingShift::where('company_id', $companyId)->get();

        $weeklyOffs = WeeklyOff::where('company_id', $companyId)->get();

        return response()->json([
            'shifts' => $shifts,
            'weekly_off_days' => $weeklyOffs,
        ]);
    }


    public function updateBasic(Request $request, $id)
    {

        DB::beginTransaction();
        try {

            $items = [];

            $company = SysCompany::findOrFail($id);

            // --------------------------
            // Update main company fields
            // --------------------------
            $company->company_name = $request->company_name ?: null;
            $company->trade_name = $request->trade_name ?: null;
            $company->company_code = $request->company_code ?: null;
            $company->business_entity_type_id = $request->business_entity_type_id ?: null;
            $company->industry_type_id = $request->industry_type_id ?: null;
            $company->business_sector_id = $request->business_sector_id ?: null;
            $company->date_of_incorporation = $request->date_of_incorporation ? $this->parseDate($request->date_of_incorporation) : null;
            $company->company_type = $request->company_type ?: null;
            $company->other_code = $request->other_code ?: null;

            // Parent company fields
            if ($request->company_type === 'parent') {
                $company->parent_company = $request->parent_company ?: null;
                $company->parent_company_id = null;
            } elseif (in_array($request->company_type, ['subsidiary', 'branch', 'sub_branch'])) {
                $company->parent_company_id = $request->parent_company_id ?: null;
                $company->parent_company = null;
            } else {
                $company->parent_company_id = null;
                $company->parent_company = null;
            }

            // Address
            $company->country = $request->country ?: null;
            $company->state = $request->state ?: null;
            $company->city = $request->city ?: null;
            $company->area = $request->area ?: null;
            $company->building_no = $request->building_no ?: null;
            $company->floor_shop_no = $request->floor_shop_no ?: null;
            $company->company_address = $request->company_address ?: null;

            // Contact
            $company->email = $request->email ?: null;
            $company->website = $request->website ?: null;
            $company->telephone = $request->telephone ?: null;
            $company->mobile_code = $request->mobile_code ?: null;
            $company->mobile = $request->mobile ?: null;
            $company->fax = $request->fax ?: null;

            // Social
            $company->linkedin = $request->linkedin ?: null;
            $company->facebook = $request->facebook ?: null;
            $company->instagram = $request->instagram ?: null;
            $company->twitter_x = $request->twitter_x ?: null;
            $company->youtube = $request->youtube ?: null;
            $company->other_social = $request->other_social ?: null;

            // Files: replace if new uploaded, otherwise keep existing
            if ($request->hasFile('company_logo')) {
                if ($company->company_logo && file_exists(public_path($company->company_logo))) {
                    @unlink(public_path($company->company_logo));
                }
                $company->company_logo = $this->uploadSingleFile($request, 'company_logo', 'uploads/company/logos');
            }

            if ($request->hasFile('digital_stamp')) {
                if ($company->digital_stamp && file_exists(public_path($company->digital_stamp))) {
                    @unlink(public_path($company->digital_stamp));
                }
                $company->digital_stamp = $this->uploadSingleFile($request, 'digital_stamp', 'uploads/company/stamps');
            }

            if ($request->hasFile('company_profile')) {
                if ($company->company_profile && file_exists(public_path($company->company_profile))) {
                    @unlink(public_path($company->company_profile));
                }
                $company->company_profile = $this->uploadSingleFile($request, 'company_profile', 'uploads/company/profiles');
            }

            // $company->shift_id = $request->shift_id ?: null;
            $company->updated_by = Auth::id();
            $company->save();

            // --------------------------
            // Settings (update or create)
            // --------------------------
            $setting = SysCompanySetting::firstOrNew(['company_id' => $company->id]);
            $setting->currency = $request->currency ?: null;
            $setting->currency_symbol = $request->currency_symbol ?: null;
            $setting->currency_digit = $request->currency_digit ?: null;
            $setting->r_code = $request->r_code ?: null;
            $setting->p_code = $request->p_code ?: null;
            $setting->book_closed = $this->parseDate($request->book_closed);
            $setting->sales_code = $request->sales_code ?: null;
            $setting->other_code = $request->other_code ?: null;
            $setting->is_customer_code = $request->has('is_customer_code') ? 1 : 0;
            $setting->is_supplier_code = $request->has('is_supplier_code') ? 1 : 0;
            $setting->is_account_code = $request->has('is_account_code') ? 1 : 0;
            $setting->is_subaccount_code = $request->has('is_subaccount_code') ? 1 : 0;
            $setting->save();

            SysCompanyDocumentItem::where('company_id', $company->id)->delete();

            // --------------------------
            // UAE Compliance or Non-UAE docs
            // --------------------------
            if ($request->country == 'UAE' || $request->country == '231') {
                $compliance = SysCompanyCompliance::firstOrNew(['company_id' => $company->id]);
                $compliance->trade_license_no = $request->trade_license_no ?: null;
                $compliance->license_issue_date = $this->parseDate($request->license_issue_date);
                $compliance->license_expiry_date = $this->parseDate($request->license_expiry_date);
                $compliance->issuing_authority = $request->issuing_authority ?: null;

                if ($request->hasFile('business_license_upload')) {
                    if ($compliance->business_license_upload && file_exists(public_path($compliance->business_license_upload))) {
                        @unlink(public_path($compliance->business_license_upload));
                    }
                    $compliance->business_license_upload = $this->uploadSingleFile($request, 'business_license_upload', 'uploads/company/compliance');
                }

                $compliance->tax_applicable = $request->tax_applicable ?: null;
                $compliance->vat_registration_number = $request->vat_registration_number ?: null;
                $compliance->vat_percentage = $request->vat_percentage ?: null;
                $compliance->vat_date = $this->parseDate($request->vat_date);
                $compliance->vat_issuing_authority = $request->vat_issuing_authority ?: null;

                if ($request->hasFile('vat_certificate')) {
                    if ($compliance->vat_certificate && file_exists(public_path($compliance->vat_certificate))) {
                        @unlink(public_path($compliance->vat_certificate));
                    }
                    $compliance->vat_certificate = $this->uploadSingleFile($request, 'vat_certificate', 'uploads/company/compliance');
                }

                $compliance->corporate_tax_number = $request->corporate_tax_number ?: null;
                $compliance->corporate_tax_vat = $request->corporate_tax_vat ?: null;
                $compliance->corporate_tax_date = $this->parseDate($request->corporate_tax_date);
                $compliance->corporate_issuing_authority = $request->ct_issuing_authority ?? null;

                if ($request->hasFile('corporate_tax_certificate')) {
                    if ($compliance->corporate_tax_certificate && file_exists(public_path($compliance->corporate_tax_certificate))) {
                        @unlink(public_path($compliance->corporate_tax_certificate));
                    }
                    $compliance->corporate_tax_certificate = $this->uploadSingleFile($request, 'corporate_tax_certificate', 'uploads/company/compliance');
                }

                $compliance->save();

                // Optionally update SysCompanyDocument (UAE cards)
                // Keep updateOrCreate using known keys if present in request
                if ($request->has('uae_compliance_cards') && is_array($request->uae_compliance_cards)) {
                    foreach ($request->uae_compliance_cards as $card) {
                        if (empty($card['type']))
                            continue;
                        SysCompanyDocument::updateOrCreate(
                            ['company_id' => $company->id, 'type' => $card['type']],
                            ['data' => $card['data'] ?? null]
                        );
                    }
                }
            } else {
                // Non-UAE: remove any UAE compliance record and handle dynamic non-uae documents
                SysCompanyCompliance::where('company_id', $company->id)->delete();

                // === Non-UAE Compliance Documents (submitted from compliance modal) ===
                if ($request->has('compliance_documents') && is_array($request->compliance_documents)) {
                    foreach ($request->compliance_documents as $idx => $cdoc) {
                        $file = $request->hasFile("compliance_documents.$idx.attachment")
                            ? $this->uploadSingleFile($request, "compliance_documents.$idx.attachment", 'uploads/company/compliance')
                            : ($cdoc['attachment'] ?? null);

                        if (!empty($cdoc['document_number']) || $file) {
                            $items[] = [
                                'company_id' => $company->id,
                                'document_type' => 'compliance',
                                'document_name' => $cdoc['issuing_authority'] ?? null,
                                'document_number' => $cdoc['document_number'] ?? null,
                                'document_date' => $this->parseDate($cdoc['issue_date'] ?? null),
                                'expiry_date' => $this->parseDate($cdoc['expiry_date'] ?? null),
                                'attachment_file' => $file,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                }
            }

            // --------------------------
            // HR Payroll (update or create)
            // --------------------------
            $hrPayroll = SysCompanyHrPayrollSetting::firstOrNew(['company_id' => $company->id]);
            $hrPayroll->leave_policy_type = $request->leave_policy_type ?: null;
            $hrPayroll->annual_leave_cl_sl =
                $request->filled('annual_leave') ? (int) $request->annual_leave : null;

            $hrPayroll->sick_leave_sl =
                $request->filled('sick_leave') ? (int) $request->sick_leave : null;

            $hrPayroll->casual_leave_cl =
                $request->filled('casual_leave') ? (int) $request->casual_leave : null;




            // $hrPayroll->comp_off_allowed = $request->has('comp_off_allowed') ? 1 : 0;
            // $hrPayroll->carry_forward_unused_leaves = $request->has('carry_forward') ? 1 : 0;
            // $hrPayroll->max_carry_forward_days = $request->max_carry_forward ?: null;
            // $hrPayroll->encashable_leaves = $request->has('leave_encashment') ? 1 : 0;

            $hrPayroll->comp_off_allowed = ($request->input('comp_off_allowed') === 'yes') ? 1 : 0;

            $hrPayroll->carry_forward_unused_leaves =
                ($request->input('carry_forward') === 'yes') ? 1 : 0;

            $hrPayroll->max_carry_forward_days =
                $request->filled('max_carry_forward') ? (int) $request->max_carry_forward : null;

            $hrPayroll->encashable_leaves =
                ($request->input('leave_encashment') === 'yes') ? 1 : 0;



            $hrPayroll->attendance_policy = $request->attendance_policy ?: null;
            $hrPayroll->minimum_working_hours = $request->min_working_hours ?: null;
            $hrPayroll->grace_period_minutes = $request->grace_period ?: null;
            $hrPayroll->half_day_after_hours = $request->half_day_after ?: null;
            $hrPayroll->absent_if_hours_below = $request->absent_below_hours ?: null;
            $hrPayroll->late_mark_count_allowed = $request->late_mark_allowed ?: null;
            $hrPayroll->consecutive_late_to_halfday = $request->late_mark_halfday ?: null;
            $hrPayroll->auto_mark_absent_after_days = $request->auto_absent_after ?: null;
            $hrPayroll->weekly_off_days = $request->has('hr_weekly_off') ? json_encode($request->hr_weekly_off) : $hrPayroll->weekly_off_days;
            $hrPayroll->wps_establishment_id = $request->hr_wps_establishment_id ?: null;
            // $hrPayroll->wps_bank = $request->hr_wps_bank ?: null;
            $hrPayroll->wps_salary_file_code = $request->hr_wps_salary_file_code ?: null;
            $hrPayroll->payroll_cycle = $request->hr_payroll_cycle ?: null;
            $hrPayroll->payroll_start_day = $request->hr_payroll_start ?: null;
            $hrPayroll->payroll_end_day = $request->hr_payroll_end ?: null;
            $hrPayroll->gratuity_calculation_method = $request->hr_gratuity_method ?: null;
            $hrPayroll->save();

            // --------------------------
            // OWNERS (update/create/delete)
            // --------------------------
            $existingOwners = SysCompanyPeople::where('company_id', $company->id)->where('type', 'owner')->pluck('id')->toArray();
            $submittedOwnerIds = [];

            if ($request->has('owners') && is_array($request->owners)) {
                foreach ($request->owners as $index => $owner) {
                    $first = trim($owner['first_name'] ?? '');
                    $last = trim($owner['last_name'] ?? '');
                    $mobile = trim($owner['mobile'] ?? '');
                    $email = trim($owner['email'] ?? '');

                    if ($first === '' && $last === '' && $mobile === '' && $email === '')
                        continue;

                    if (!empty($owner['id'])) {
                        $p = SysCompanyPeople::where('id', $owner['id'])->where('company_id', $company->id)->first();
                        if (!$p)
                            continue;
                        $p->salutation = $owner['salutation'] ?? null;
                        $p->first_name = $owner['first_name'] ?? $p->first_name;
                        $p->last_name = $owner['last_name'] ?? $p->last_name;
                        $p->mobile = $owner['mobile'] ?? $p->mobile;
                        $p->email = $owner['email'] ?? $p->email;
                        $p->designation = $owner['designation_id'] ?? $p->designation;
                        $p->share_percentage = $owner['share_percentage'] ?? $p->share_percentage;
                        $p->save();

                        // refresh documents: remove existing docs and save new ones from request
                        SysCompanyPeopleDocument::where('people_id', $p->id)->delete();
                        $this->savePersonDocumentsArray($p->id, $request, "owners.$index", 'owner');

                        $submittedOwnerIds[] = $p->id;
                    } else {
                        // create new owner & associated staff/user like storeBasic
                        $person = new SysCompanyPeople();
                        $person->company_id = $company->id;
                        $person->type = 'owner';
                        $person->salutation = $owner['salutation'] ?? null;
                        $person->first_name = $owner['first_name'];
                        $person->last_name = $owner['last_name'] ?? null;
                        $person->mobile = $owner['mobile'] ?? null;
                        $person->email = $owner['email'] ?? null;
                        $person->designation = $owner['designation_id'] ?? null;
                        $person->share_percentage = $owner['share_percentage'] ?? 0;
                        $person->save();

                        // create staff & user for owner (best-effort)
                        try {
                            $staff = new SmStaff();
                            $staff->staff_no = SysHelper::get_new_staff_code();
                            $staff->employee_salutation = $owner['salutation'] ?? null;
                            $staff->first_name = $owner['first_name'];
                            $staff->last_name = $owner['last_name'] ?? null;
                            $staff->full_name = trim(implode(' ', array_filter([$owner['first_name'], $owner['last_name'] ?? null])));
                            $staff->mobile = $owner['mobile'] ?? null;
                            $staff->email = $owner['email'] ?? null;
                            $staff->designation_id = (int) ($owner['designation_id'] ?? 0);
                            $staff->created_at = now();
                            $staff->updated_at = now();

                            $user = User::where('email', $staff->email)->first();
                            if (!$user)
                                $user = new User();
                            $fullName = trim(implode(' ', array_filter([$staff->first_name, $staff->last_name])));
                            $username = $request->input('username') ?: (strpos($staff->email, '@') !== false ? strstr($staff->email, '@', true) : Str::slug($fullName, '_'));

                            $user->role_id = 2;
                            $user->full_name = $fullName;
                            $user->username = $username;
                            $user->email = $staff->email;
                            $user->usertype = $request->input('usertype', 'staff');
                            $user->access_status = 1;
                            $user->password = Hash::make($owner['first_name'] ?? '123');
                            $user->save();

                            $staff->user_id = $user->id;
                            $staff->save();

                            $jobPayload = [
                                'staff_id' => $staff->id,
                                'company_id' => $company->id,
                                'department_id' => null,
                                'designation_id' => (int) ($owner['designation_id'] ?? 0),
                                'job_title' => null,
                                'start_date' => now(),
                            ];

                            $job = SmStaffJobDetail::create($jobPayload);
                            $job->save();
                        } catch (\Exception $ex) {
                            // don't break update on staff/user creation failures, log and continue
                            Log::warning('Owner staff creation failed: ' . $ex->getMessage(), ['owner' => $owner]);
                        }

                        // save owner documents
                        $this->savePersonDocumentsArray($person->id, $request, "owners.$index", 'owner');

                        $submittedOwnerIds[] = $person->id;
                    }
                }

                // delete owners removed in the edit form
                $toDelete = array_diff($existingOwners, $submittedOwnerIds);
                if (!empty($toDelete)) {
                    SysCompanyPeopleDocument::whereIn('people_id', $toDelete)->delete();
                    SysCompanyPeople::whereIn('id', $toDelete)->delete();
                }
            }

            // --------------------------
            // SPONSORS (update/create/delete)
            // --------------------------
            $existingSponsors = SysCompanyPeople::where('company_id', $company->id)->where('type', 'sponsor')->pluck('id')->toArray();
            $submittedSponsorIds = [];

            if ($request->has('sponsors') && is_array($request->sponsors)) {
                foreach ($request->sponsors as $index => $sponsor) {
                    $first = trim($sponsor['first_name'] ?? '');
                    $last = trim($sponsor['last_name'] ?? '');
                    $mobile = trim($sponsor['mobile'] ?? '');
                    $email = trim($sponsor['email'] ?? '');

                    if ($first === '' && $last === '' && $mobile === '' && $email === '')
                        continue;

                    if (!empty($sponsor['id'])) {
                        $p = SysCompanyPeople::where('id', $sponsor['id'])->where('company_id', $company->id)->first();
                        if (!$p)
                            continue;
                        $p->salutation = $sponsor['salutation'] ?? null;
                        $p->first_name = $sponsor['first_name'] ?? $p->first_name;
                        $p->last_name = $sponsor['last_name'] ?? $p->last_name;
                        $p->mobile = $sponsor['mobile'] ?? $p->mobile;
                        $p->email = $sponsor['email'] ?? $p->email;
                        $p->save();

                        SysCompanyPeopleDocument::where('people_id', $p->id)->delete();
                        $this->savePersonDocumentsArray($p->id, $request, "sponsors.$index", 'sponsor');

                        $submittedSponsorIds[] = $p->id;
                    } else {
                        $person = new SysCompanyPeople();
                        $person->company_id = $company->id;
                        $person->type = 'sponsor';
                        $person->salutation = $sponsor['salutation'] ?? null;
                        $person->first_name = $sponsor['first_name'];
                        $person->last_name = $sponsor['last_name'] ?? null;
                        $person->mobile = $sponsor['mobile'] ?? null;
                        $person->email = $sponsor['email'] ?? null;
                        $person->save();

                        $this->savePersonDocumentsArray($person->id, $request, "sponsors.$index", 'sponsor');

                        $submittedSponsorIds[] = $person->id;
                    }
                }

                $toDelete = array_diff($existingSponsors, $submittedSponsorIds);
                if (!empty($toDelete)) {
                    SysCompanyPeopleDocument::whereIn('people_id', $toDelete)->delete();
                    SysCompanyPeople::whereIn('id', $toDelete)->delete();
                }
            }

            // --------------------------
            // CONTACTS (update/create/delete)
            // --------------------------
            $existingContacts = SysCompanyPeople::where('company_id', $company->id)->where('type', 'contact')->pluck('id')->toArray();
            $submittedContactIds = [];

            if ($request->has('contacts') && is_array($request->contacts)) {
                foreach ($request->contacts as $index => $contact) {
                    $first = trim($contact['first_name'] ?? '');
                    $last = trim($contact['last_name'] ?? '');
                    $mobile = trim($contact['mobile'] ?? '');
                    $email = trim($contact['email'] ?? '');

                    if ($first === '' && $last === '' && $mobile === '' && $email === '')
                        continue;

                    if (!empty($contact['id'])) {
                        $p = SysCompanyPeople::where('id', $contact['id'])->where('company_id', $company->id)->first();
                        if (!$p)
                            continue;
                        $p->salutation = $contact['salutation'] ?? null;
                        $p->first_name = $contact['first_name'] ?? $p->first_name;
                        $p->last_name = $contact['last_name'] ?? $p->last_name;
                        $p->mobile = $contact['mobile'] ?? $p->mobile;
                        $p->email = $contact['email'] ?? $p->email;
                        $p->designation = $contact['designation'] ?? $p->designation;
                        $p->save();

                        SysCompanyPeopleDocument::where('people_id', $p->id)->delete();
                        $this->savePersonDocumentsArray($p->id, $request, "contacts.$index", 'contact');

                        $submittedContactIds[] = $p->id;
                    } else {
                        $person = new SysCompanyPeople();
                        $person->company_id = $company->id;
                        $person->type = 'contact';
                        $person->salutation = $contact['salutation'] ?? null;
                        $person->first_name = $contact['first_name'];
                        $person->last_name = $contact['last_name'] ?? null;
                        $person->mobile = $contact['mobile'] ?? null;
                        $person->email = $contact['email'] ?? null;
                        $person->designation = $contact['designation'] ?? null;
                        $person->save();

                        $this->savePersonDocumentsArray($person->id, $request, "contacts.$index", 'contact');

                        $submittedContactIds[] = $person->id;
                    }
                }

                $toDelete = array_diff($existingContacts, $submittedContactIds);
                if (!empty($toDelete)) {
                    SysCompanyPeopleDocument::whereIn('people_id', $toDelete)->delete();
                    SysCompanyPeople::whereIn('id', $toDelete)->delete();
                }
            }

            // --------------------------
            // BANKS (update/create/delete)
            // --------------------------
            $existingBanks = SysCompanyBanking::where('company_id', $company->id)->pluck('id')->toArray();
            $submittedBankIds = [];



            $bankIndex = 0;
            while ($request->has("banks.$bankIndex.bank_name") || $request->hasFile("banks.$bankIndex.bank_letter")) {
                $bankData = [
                    'company_id' => $company->id,
                    'bank_name' => $request->input("banks.$bankIndex.bank_name") ?: '',
                    'branch_name' => $request->input("banks.$bankIndex.branch_name") ?: null,
                    'account_number' => $request->input("banks.$bankIndex.account_number") ?: null,
                    'iban_number' => $request->input("banks.$bankIndex.iban_number") ?: null,
                    'swift_code' => $request->input("banks.$bankIndex.swift_code") ?: null,
                    'finance_code' => $request->input("banks.$bankIndex.finance_code") ?: null,
                    'currency' => $request->input("banks.$bankIndex.currency") ?: null,
                ];

                if (!empty($request->input("banks.$bankIndex.id"))) {
                    $b = SysCompanyBanking::where('id', $request->input("banks.$bankIndex.id"))->where('company_id', $company->id)->first();
                    if ($b) {
                        $b->fill($bankData);

                        if ($request->hasFile("banks.$bankIndex.bank_letter")) {
                            if ($b->bank_letter && file_exists(public_path($b->bank_letter))) {
                                @unlink(public_path($b->bank_letter));
                            }
                            $b->bank_letter = $this->uploadSingleFile($request, "banks.$bankIndex.bank_letter", 'uploads/company/banking');
                        }
                        $b->save();
                        $submittedBankIds[] = $b->id;
                    }
                } else {
                    $b = new SysCompanyBanking($bankData);
                    if ($request->hasFile("banks.$bankIndex.bank_letter")) {
                        $b->bank_letter = $this->uploadSingleFile($request, "banks.$bankIndex.bank_letter", 'uploads/company/banking');
                    } elseif ($request->has("banks.$bankIndex.bank_letter")) {
                        // allow hidden input to pass existing paths
                        $b->bank_letter = $request->input("banks.$bankIndex.bank_letter");

                    }
                    $b->save();
                    $submittedBankIds[] = $b->id;
                }

                $bankIndex++;
            }

            $banksToDelete = array_diff($existingBanks, $submittedBankIds);
            if (!empty($banksToDelete))
                SysCompanyBanking::whereIn('id', $banksToDelete)->delete();


             if ($request->hr_wps_bank && is_array($request->hr_wps_bank)) {

                $banks_stored = SysCompanyBanking::where('company_id', $company->id)->get()->toArray();



                $result = array_map(function ($v) {
                    return explode('_', $v)[1] ?? null;
                }, $request->hr_wps_bank);

                // COLLECT BANK IDS BASED ON INDEXES
                $bankIds = [];


                foreach ($result as $index) {
                    if (isset($banks_stored[$index]['id'])) {
                        $bankIds[] = $banks_stored[$index]['id'];
                    }
                }

               

             



                $hrPayroll->wps_bank = json_encode($bankIds);

                $hrPayroll->save();
            }

            // --------------------------
            // WAREHOUSES (update/create/delete)
            // --------------------------
            $existingWh = CompanyWarehouse::where('company_id', $company->id)->pluck('id')->toArray();
            $submittedWhIds = [];

            $wIndex = 0;
            while ($request->has("warehouses.$wIndex.warehouse_name")) {
                if (!empty($request->input("warehouses.$wIndex.id"))) {
                    $wh = CompanyWarehouse::where('id', $request->input("warehouses.$wIndex.id"))->where('company_id', $company->id)->first();
                    if (!$wh) {
                        $wIndex++;
                        continue;
                    }
                } else {
                    $wh = new CompanyWarehouse();
                    $wh->company_id = $company->id;
                }

                $wh->warehouse_code = $request->input("warehouses.$wIndex.warehouse_code") ?: null;
                $wh->warehouse_name = $request->input("warehouses.$wIndex.warehouse_name") ?: null;
                $wh->warehouse_country = $request->input("warehouses.$wIndex.warehouse_country") ?: null;
                $wh->warehouse_state = $request->input("warehouses.$wIndex.warehouse_state") ?: null;
                $wh->warehouse_city = $request->input("warehouses.$wIndex.warehouse_city") ?: null;
                $wh->warehouse_area = $request->input("warehouses.$wIndex.warehouse_area") ?: null;
                $wh->warehouse_building_name = $request->input("warehouses.$wIndex.warehouse_building_name") ?: null;
                $wh->warehouse_flat_office_no = $request->input("warehouses.$wIndex.warehouse_flat_office_no") ?: null;
                // contact
                // $wh->contact_salutation = $request->input("warehouses.$wIndex.contact_salutation");
                // $wh->contact_first_name = $request->input("warehouses.$wIndex.contact_first_name");
                // $wh->contact_last_name = $request->input("warehouses.$wIndex.contact_last_name");
                $wh->contact_person_id = $request->input("warehouses.$wIndex.contact_person_name") ?: null;

                $wh->contact_mobile = $request->input("warehouses.$wIndex.contact_mobile") ?: null;
                $wh->contact_email = $request->input("warehouses.$wIndex.contact_email") ?: null;
                $wh->contact_designation = $request->input("warehouses.$wIndex.contact_designation") ?: null;
                // fire safety
                $wh->fire_safety_compliance_status = $request->input("warehouses.$wIndex.fire_safety_compliance_status") ?: null;
                $wh->fire_noc_certificate_number = $request->input("warehouses.$wIndex.fire_noc_certificate_number") ?: null;
                $wh->safety_equipment_available = $request->input("warehouses.$wIndex.safety_equipment_available") ?: null;
                $wh->fire_noc_expiry_date = $this->parseDate($request->input("warehouses.$wIndex.fire_noc_expiry_date")) ?: null;
                $wh->last_safety_inspection_date = $this->parseDate($request->input("warehouses.$wIndex.last_safety_inspection_date")) ?: null;


                if ($request->hasFile("warehouses.$wIndex.contact_documents")) {
                    $filesStr = $this->uploadMultipleFiles($request, "warehouses.$wIndex.contact_documents", 'uploads/company/warehouse_docs');
                    $wh->contact_documents = $filesStr ? explode('|', $filesStr) : null;
                } elseif ($request->has("warehouses.$wIndex.contact_documents")) {

                    // allow hidden input to pass existing paths
                    $wh->contact_documents = $request->input("warehouses.$wIndex.contact_documents");
                }

                $wh->save();
                $submittedWhIds[] = $wh->id;
                $wIndex++;
            }

            $whToDelete = array_diff($existingWh, $submittedWhIds);
            if (!empty($whToDelete))
                CompanyWarehouse::whereIn('id', $whToDelete)->delete();

            // --------------------------
            // POLICIES (update/create/delete)
            // --------------------------
            $existingPolicies = SysCompanyHrPolicy::where('company_id', $company->id)->pluck('id')->toArray();
            $submittedPolicyIds = [];

            $pIndex = 0;
            while ($request->has("policies.$pIndex.policy_name")) {
                if (!empty($request->input("policies.$pIndex.id"))) {
                    $pol = SysCompanyHrPolicy::where('id', $request->input("policies.$pIndex.id"))->where('company_id', $company->id)->first();
                    if (!$pol) {
                        $pIndex++;
                        continue;
                    }
                } else {
                    $pol = new SysCompanyHrPolicy();
                    $pol->company_id = $company->id;
                }

                $pol->policy_date = $this->parseDate($request->input("policies.$pIndex.policy_date"));
                $pol->policy_name = $request->input("policies.$pIndex.policy_name");
                $pol->policy_category = $request->input("policies.$pIndex.policy_category");
                $pol->policy_valid = $this->parseDate($request->input("policies.$pIndex.policy_valid"));
                $pol->view_to_employees = $request->input("policies.$pIndex.view_to_employees");
                $pol->policy_details = $request->input("policies.$pIndex.policy_details");

                if ($request->hasFile("policies.$pIndex.policy_file")) {
                    if ($pol->policy_file && file_exists(public_path($pol->policy_file))) {
                        @unlink(public_path($pol->policy_file));
                    }
                    $pol->policy_file = $this->uploadSingleFile($request, "policies.$pIndex.policy_file", 'uploads/company/policies');
                } elseif ($request->has("policies.$pIndex.policy_file")) {
                    $pol->policy_file = $request->input("policies.$pIndex.policy_file");
                }

                $pol->save();
                $submittedPolicyIds[] = $pol->id;
                $pIndex++;
            }

            $policiesToDelete = array_diff($existingPolicies, $submittedPolicyIds);
            if (!empty($policiesToDelete))
                SysCompanyHrPolicy::whereIn('id', $policiesToDelete)->delete();

            // --------------------------
            // DYNAMIC DOCUMENT ROWS (uae + non_uae)
            // Re-create by deleting existing then inserting new
            // --------------------------



            if ($request->has('uae_documents') && is_array($request->uae_documents)) {
                foreach ($request->uae_documents as $i => $doc) {
                    $file = $request->hasFile("uae_documents.$i.file")
                        ? $this->uploadSingleFile($request, "uae_documents.$i.file", 'uploads/company/compliance')
                        : ($doc['file'] ?? null);

                    if (!empty(trim($doc['name'] ?? '')) || !empty(trim($doc['number'] ?? '')) || $file) {
                        $items[] = [
                            'company_id' => $company->id,
                            'document_name' => $doc['name'] ?? null,
                            'document_type' => 'uae',
                            'document_number' => $doc['number'] ?? null,
                            'document_date' => !empty($doc['date']) ? $this->parseDate($doc['date']) : null,
                            'expiry_date' => !empty($doc['expiry']) ? $this->parseDate($doc['expiry']) : null,
                            'attachment_file' => $file,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            if ($request->has('non_uae_documents') && is_array($request->non_uae_documents)) {
                foreach ($request->non_uae_documents as $i => $doc) {
                    $file = $request->hasFile("non_uae_documents.$i.file")
                        ? $this->uploadSingleFile($request, "non_uae_documents.$i.file", 'uploads/company/compliance')
                        : ($doc['file'] ?? null);

                    if (!empty(trim($doc['name'] ?? '')) || !empty(trim($doc['number'] ?? '')) || $file) {
                        $items[] = [
                            'company_id' => $company->id,
                            'document_type' => 'non_uae',
                            'document_name' => $doc['name'] ?? null,
                            'document_number' => $doc['number'] ?? null,
                            'document_date' => !empty($doc['date']) ? $this->parseDate($doc['date']) : null,
                            'expiry_date' => !empty($doc['expiry']) ? $this->parseDate($doc['expiry']) : null,
                            'attachment_file' => $file,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }



            if (!empty($items))
                SysCompanyDocumentItem::insert($items);



            // store shifts


            if ($request->filled('working_shifts')) {
                // delete existing shifts   
                WorkingShift::where('company_id', $company->id)->delete();
                foreach ($request->working_shifts as $shiftData) {



                    $start = Carbon::parse($shiftData['start_time']);
                    $end = Carbon::parse($shiftData['end_time']);



                    WorkingShift::create([
                        'company_id' => $company->id, // if applicable
                        'shift_name' => trim($shiftData['shift_name']),
                        'start_time' => $start->format('H:i:00'),
                        'end_time' => $end->format('H:i:00'),
                        'is_active' => 1,
                    ]);
                }
            }




            if ($request->filled('hr_weekly_off')) {
                // delete existing weekly offs
                WeeklyOff::where('company_id', $company->id)->delete();
                foreach ($request->hr_weekly_off as $weeklyOff) {
                    WeeklyOff::create([
                        'company_id' => $company->id,
                        'name' => trim($weeklyOff),

                    ]);
                }
            }


            DB::commit();

            Toastr::success('Company basic details updated successfully!', 'Success');
            return redirect('company?active=' . $company->id);

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            Log::error('updateBasic failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'request' => $request->all()]);

            Toastr::error('Operation Failed: ' . $e->getMessage(), 'Failed');
            return redirect()->back()->withInput();
        }
    }

}
