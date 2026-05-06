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
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Carbon\Carbon;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

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
            $q       = trim($request->get('q', ''));
            $code    = trim($request->get('code', ''));
            $name    = trim($request->get('name', ''));
            $country = trim($request->get('country', ''));

            // ---------------------------------------------
            // RELATIONS
            // ---------------------------------------------
            $query = SysCompany::with([
                'people',
                'compliance',
                'banking',
                'documents',
                'countryRelation', // FIXED
                'stateRelation',
                'businessEntity',
                'businessIndustry',
                'businessSector',
                'settings'
            ]);

            // ---------------------------------------------
            // QUICK SEARCH
            // ---------------------------------------------
            if ($q !== '') {
                $query->where(function ($x) use ($q) {
                    $x->where('company_name', 'like', "%{$q}%")
                        ->orWhere('trade_name',   'like', "%{$q}%");

                    if (is_numeric($q)) {
                        $x->orWhere('id', (int)$q);
                    }
                });
            }

            // ---------------------------------------------
            // LONG FILTERS
            // ---------------------------------------------
            if ($code !== '') {
                if (is_numeric($code)) {
                    $query->where('id', (int)$code);
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
            $company  = $query->orderBy('sort_id', 'asc')->get();
            $countryL = SysCountries::all();
            $currency = SysCurrency::all();

            // SELECTED COMPANY
            $selectedCompany = null;
            if ($request->filled('active')) {
                $selectedCompany = SysCompany::with([
                    'people',
                    'compliance',
                    'banking',
                    'documents',
                    'countryRelation',  // FIXED
                    'stateRelation',
                    'businessEntity',
                    'businessIndustry',
                    'businessSector',
                    'settings'
                ])->find($request->input('active'));
            }

            // ---------------------------------------------
            // RETURN VIEW
            // ---------------------------------------------
            return view('backEnd.company.companyList', [
                'company'         => $company,
                'country'         => $countryL,
                'currency'        => $currency,
                'selectedCompany' => $selectedCompany,
            ]);
        } catch (\Exception $e) {
            return $e;
        }
    }



    // AJAX details (partial)
    public function details($id)
    {
        $company = SysCompany::findOrFail($id);
        // add ->with(...) if you want relations
        return view('backEnd.company.company_details', compact('company'));
    }



    public function companyAdd(Request $request)
    {
        try {
            // Clear old input so that form 'old()' values do not persist across refreshes
            session()->forget('_old_input');
            // Clear session-based banking and policy data to start fresh
            session()->forget(['company_banks', 'company_policies']);
            // For add, we don't need all companies collection
            $company = null; // no company yet
            $parentCompanies = SysCompany::where('company_type', 'parent')->get();
            $country   = SysCountries::all();
            $currency  = SysCurrency::all();
            $industries = \App\SmIndustry::orderBy('name')->get();
            $activities = \App\SmBusinessActivity::with('industry')->orderBy('name')->get();
            $entities = \App\SmBusinessEntityType::orderBy('name')->get();

            $latestId = SysCompany::max('id');
            $nextId = $latestId ? $latestId + 1 : 1;

            return view(
                'backEnd.company.addCompany',
                compact('company', 'country', 'currency', 'nextId', 'industries', 'activities', 'entities', 'parentCompanies')
            );
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
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
            'country' => "required",
            'city' => "required",
            'email' => "required",
            'website' => "required",
            'company_address' => "required",
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
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($results) {
                    return ApiBaseMethod::sendResponse(null,  'Company has been updated successfully');
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

public function storeBasic(Request $request)
{
    // ---------------------------------------------
    // VALIDATION
    // ---------------------------------------------
    $rules = [
        'company_name'       => 'required|string|max:255'
        // 'business_entity_id' => 'required',
        // 'industry_id'        => 'required',
        // 'business_sector_id' => 'required',
        // 'company_type'       => 'required',
        // 'company_code'       => 'required',
        // 'country'            => 'required|integer',
        // 'state'              => 'required|integer',
    ];

    $v = Validator::make($request->all(), $rules);

    if ($v->fails()) {
        return response()->json([
            'ok' => false,
            'errors' => $v->errors()
        ], 422);
    }

    // ---------------------------------------------
    // PREPARE DATA
    // ---------------------------------------------
    $data = $request->only([
        'company_name',
        'trade_name',
        'date_of_incorporation',
        'business_entity_id',
        'industry_id',
        'business_sector_id',
        'company_type',
        'company_code',
        'company_address',
        'country',
        'state',
        'email',
        'telephone',
        'mobile',
        'website',
        'linkedin',
        'facebook',
        'instagram',
        'twitter_x',
        'youtube',
        'other_social',
        'city',
        'area',
        'building_no',
        'floor_shop_no'
    ]);

    // ---------------------------------------------
    // COMPANY TYPE LOGIC
    // ---------------------------------------------
    if ($request->company_type == 'parent') {
        $data['parent_company']    = $request->company_name;
        $data['parent_company_id'] = null;
        $data['type']              = 'Parent Company';

    } elseif ($request->company_type == 'subsidiary') {
        $data['parent_company']    = null;
        $data['parent_company_id'] = !empty($request->parent_company_id) ? $request->parent_company_id : null;
        $data['type']              = 'Subsidiary';

    } elseif ($request->company_type == 'branch') {
        $data['parent_company']    = null;
        $data['parent_company_id'] = !empty($request->parent_company_id) ? $request->parent_company_id : null;
        $data['type']              = 'Branch';
        
    } elseif ($request->company_type == 'sub_branch') {
        $data['parent_company']    = null;
        $data['parent_company_id'] = !empty($request->parent_company_id) ? $request->parent_company_id : null;
        $data['type']              = 'Sub Branch';
        
    } else {
        // Default case for other company types
        $data['parent_company']    = null;
        $data['parent_company_id'] = null;
        $data['type']              = ucfirst($request->company_type);
    }

    $data['date_of_incorporation'] = SysHelper::normalizeToYmd($request->date_of_incorporation);

    // ---------------------------------------------
    // HANDLE FILE UPLOADS
    // ---------------------------------------------
    foreach (['company_logo', 'digital_stamp', 'company_profile'] as $fileKey) {
        if ($request->hasFile($fileKey)) {

            $file = $request->file($fileKey);

            $filename = time() . '_' . $fileKey . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('uploads/company'), $filename);

            $data[$fileKey] = 'uploads/company/' . $filename;
        }
    }

    // ---------------------------------------------
    // CREATE OR UPDATE COMPANY
    // ---------------------------------------------
    $company_id = $request->company_code;
    if ($company_id) {

        // UPDATE EXISTING COMPANY
        $company = SysCompany::find($company_id);

        if ($company) {
            $company->update($data);
        } else {
            // FALLBACK → If ID not found, create new
            $company = SysCompany::create($data);
        }

    } else {

        // CREATE NEW COMPANY
        $company = SysCompany::create($data);
    }

    return response()->json([
        'ok' => true,
        'company_id' => $company->id
    ]);
}



    public function storeContact(Request $request)
    {
        // Validate request
        $rules = [
            'company_id' => 'required|exists:sys_company,id',

            'email'       => 'nullable|email',
            'website'     => 'nullable|url',
            'telephone'   => 'nullable|string|max:100',
            'fax'         => 'nullable|string|max:100',
            'mobile'      => 'nullable|string|max:100',
            'country'     => 'nullable',
            'state'       => 'nullable',
            'company_address' => 'nullable|string',

            // Owners
            'owners'                     => 'nullable|array',
            'owners.*.salutation'        => 'nullable|string|max:200',
            'owners.*.first_name'        => 'nullable|string|max:255',
            'owners.*.last_name'         => 'nullable|string|max:255',
            'owners.*.mobile'            => 'nullable|string|max:50',
            'owners.*.email'             => 'nullable|email',
            'owners.*.share_percentage'  => 'nullable|string|max:255',

            // Sponsors
            'sponsors'                   => 'nullable|array',
            'sponsors.*.salutation'      => 'nullable|string|max:200',
            'sponsors.*.first_name'      => 'nullable|string|max:255',
            'sponsors.*.last_name'       => 'nullable|string|max:255',
            'sponsors.*.mobile'          => 'nullable|string|max:50',
            'sponsors.*.email'           => 'nullable|email',

            // Contact people
            'contacts'                   => 'nullable|array',
            'contacts.*.salutation'      => 'nullable|string|max:200',
            'contacts.*.first_name'      => 'nullable|string|max:255',
            'contacts.*.last_name'       => 'nullable|string|max:255',
            'contacts.*.mobile'          => 'nullable|string|max:50',
            'contacts.*.email'           => 'nullable|email',
            'contacts.*.designation'     => 'nullable|string|max:255'
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
        }

        // Debug: Log full session data at start
        \Log::info("Full session data at start of storeContact:");
        \Log::info("All session data: " . json_encode($request->session()->all()));
        
        // Check specifically for documents in session with different possible keys
        $sessionKeys = ['documentSessions', 'documents_temp_' . $request->company_id, 'documents_temp_new'];
        foreach ($sessionKeys as $sessionKey) {
            \Log::info("Checking session key: {$sessionKey}");
            \Log::info("Data in {$sessionKey}: " . json_encode($request->session()->get($sessionKey, [])));
        }

        $company = SysCompany::findOrFail($request->company_id);

        // Update basic contact fields
        $company->update([
            'email'            => $request->email,
            'website'          => $request->website,
            'telephone'        => $request->telephone,
            'fax'              => $request->fax,
            'mobile'           => $request->mobile,
            'country'          => $request->country,
            'state'            => $request->state,
            'company_address'  => $request->company_address,

            // Social media
            'linkedin'         => $request->linkedin,
            'facebook'         => $request->facebook,
            'instagram'        => $request->instagram,
            'twitter_x'        => $request->twitter_x,
            'youtube'          => $request->youtube,
            'other_social'     => $request->other_social,
        ]);

        // Prepare upload folder
        $dir = public_path('uploads/company');
        if (!file_exists($dir)) mkdir($dir, 0777, true);

        // Helper to upload files
        $saveFile = function ($file) use ($dir) {
            if (!$file) return null;
            $name = uniqid("file_") . "." . $file->getClientOriginalExtension();
            $file->move($dir, $name);
            return "uploads/company/" . $name;
        };

        // Delete old people rows (documents are stored directly in this table)
        DB::table('sys_company_people')->where('company_id', $company->id)->delete();

        // Helper function to get document data from session
        $getDocumentsFromSession = function($type, $index) {
            try {
                // Get documents from session
                $sessionKey = 'documentSessions';
                $documents = session($sessionKey, []);
                
                \Log::info("Getting documents for type: {$type}, index: {$index}");
                \Log::info("Session data: " . json_encode($documents));
                
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
                    
                    \Log::info("Processing document: " . json_encode($docArray));
                    
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
                            \Log::warning("Invalid issue date: " . $docArray['date']);
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
                            \Log::warning("Invalid expiry date: " . $docArray['expiry_date']);
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
                    
                    \Log::info("Document data prepared: " . json_encode($documentData));
                }
                
                return $documentData;
                
            } catch (\Exception $e) {
                \Log::error("Error getting documents from session: " . $e->getMessage());
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
            \Log::info("Processing " . count($request->owners) . " owners");
            foreach ($request->owners as $i => $o) {
                // Get document data from session for this owner
                $documentData = $getDocumentsFromSession('owner', $i);
                
                \Log::info("Owner {$i} document data: " . json_encode($documentData));
                
                $insertData = [
                    'company_id'      => $company->id,
                    'type'            => 'owner',
                    'salutation'      => $o['salutation'] ?? null,
                    'name'            => trim(($o['first_name'] ?? '') . ' ' . ($o['last_name'] ?? '')),
                    'mobile'          => $o['mobile'] ?? null,
                    'email'           => $o['email'] ?? null,
                    'share_percentage' => $o['share_percentage'] ?? null,
                    'document_name'   => $documentData['document_name'],
                    'document_no'     => $documentData['document_no'],
                    'issue_date'      => $documentData['issue_date'],
                    'expiry_date'     => $documentData['expiry_date'],
                    'attachment'      => $documentData['attachment'],
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
                
                \Log::info("Inserting owner data: " . json_encode($insertData));
                
                $personId = DB::table('sys_company_people')->insertGetId($insertData);
                
                \Log::info("Owner inserted with ID: {$personId}");
            }
        }

        // ---------- SPONSORS ----------
        if ($request->sponsors) {
            \Log::info("Processing " . count($request->sponsors) . " sponsors");
            foreach ($request->sponsors as $i => $s) {
                // Get document data from session for this sponsor
                $documentData = $getDocumentsFromSession('sponsor', $i);
                
                \Log::info("Sponsor {$i} document data: " . json_encode($documentData));
                
                $insertData = [
                    'company_id'      => $company->id,
                    'type'            => 'sponsor',
                    'salutation'      => $s['salutation'] ?? null,
                    'name'            => trim(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? '')),
                    'mobile'          => $s['mobile'] ?? null,
                    'email'           => $s['email'] ?? null,
                    'document_name'   => $documentData['document_name'],
                    'document_no'     => $documentData['document_no'],
                    'issue_date'      => $documentData['issue_date'],
                    'expiry_date'     => $documentData['expiry_date'],
                    'attachment'      => $documentData['attachment'],
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
                
                \Log::info("Inserting sponsor data: " . json_encode($insertData));
                
                $personId = DB::table('sys_company_people')->insertGetId($insertData);
                
                \Log::info("Sponsor inserted with ID: {$personId}");
            }
        }

        // ---------- CONTACT PERSON ----------
        if ($request->contacts) {
            \Log::info("Processing " . count($request->contacts) . " contacts");
            foreach ($request->contacts as $i => $c) {
                // Get document data from session for this contact
                $documentData = $getDocumentsFromSession('contact', $i);
                
                \Log::info("Contact {$i} document data: " . json_encode($documentData));
                
                $insertData = [
                    'company_id'      => $company->id,
                    'type'            => 'contact',
                    'salutation'      => $c['salutation'] ?? null,
                    'name'            => trim(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? '')),
                    'mobile'          => $c['mobile'] ?? null,
                    'email'           => $c['email'] ?? null,
                    'designation'     => $c['designation'] ?? null,
                    'document_name'   => $documentData['document_name'],
                    'document_no'     => $documentData['document_no'],
                    'issue_date'      => $documentData['issue_date'],
                    'expiry_date'     => $documentData['expiry_date'],
                    'attachment'      => $documentData['attachment'],
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
                
                \Log::info("Inserting contact data: " . json_encode($insertData));
                
                $personId = DB::table('sys_company_people')->insertGetId($insertData);
                
                \Log::info("Contact inserted with ID: {$personId}");
            }
        }

        // Log final session state before clearing
        $allSessionData = session('documentSessions', []);
        \Log::info("Final session data before clearing: " . json_encode($allSessionData));
        
        // Clear document sessions after saving
        session()->forget('documentSessions');

        return response()->json([
            'ok' => true,
            'message' => 'Contact Information saved successfully'
        ]);
    }


    public function storeCompliance(Request $request)
    {
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
            
            \Log::info("Non-UAE compliance - checking session for compliance documents");
            \Log::info("Compliance documents: " . json_encode($complianceDocuments));
            
            // Initialize compliance data
            $data = [
                'trade_license_no' => null,
                'license_issue_date' => null,
                'license_expiry_date' => null,
                'issuing_authority' => null,
                'trade_license_file' => null,
                'vat_registration_number' => null,
                'vat_percentage' => null,
                'vat_date' => null,
                'vat_issuing_authority' => null,
                'corporate_tax_number' => null,
                'corporate_tax_date' => null,
                'corporate_tax_vat' => null,
                'corporate_issuing_authority' => null,
            ];
            
            // Check for compliance documents in session
            if (!empty($complianceDocuments) && is_array($complianceDocuments)) {
                // Get the first document (assuming one compliance document)
                $doc = $complianceDocuments[0];
                
                \Log::info("Processing compliance document: " . json_encode($doc));
                
                // Map session fields to compliance fields
                $data['trade_license_no'] = $doc['document_number'] ?? null;
                $data['issuing_authority'] = $doc['issuing_authority'] ?? null;
                
                // Handle dates
                if (!empty($doc['issue_date']) && $doc['issue_date'] !== '') {
                    $data['license_issue_date'] = $this->toSqlDate($doc['issue_date']);
                }
                
                if (!empty($doc['expiry_date']) && $doc['expiry_date'] !== '') {
                    $data['license_expiry_date'] = $this->toSqlDate($doc['expiry_date']);
                }
                
                // Handle attachment file
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
                            $data['trade_license_file'] = "uploads/company/{$fileName}";
                        }
                    } else {
                        // If temp file doesn't exist, use the path as is
                        $data['trade_license_file'] = $attachmentPath;
                    }
                }
            }
            
            \Log::info("Non-UAE compliance data to save: " . json_encode($data));
            
            // Save compliance record
            $compliance = SysCompanyCompliance::updateOrCreate(
                ['company_id' => $request->company_id],
                $data
            );
            
            // Clear compliance documents from session
            session()->forget('company_compliance_documents');
            
            return response()->json(['ok' => true, 'compliance_id' => $compliance->id, 'message' => 'Non-UAE compliance saved successfully']);
        }

        // UAE-specific validation rules
        $rules = [
            'company_id'              => 'exists:sys_company,id',
            'trade_license_no' =>       'nullable|string|max:150',

            'license_issue_date'      => 'nullable|string',
            'license_expiry_date'     => 'nullable|string',

            'issuing_authority'       => 'nullable|string|max:150',

            'tax_applicable'          => 'nullable|in:vat,ct,both,none',

            'vat_registration_number' => 'nullable|string|max:150',
            'vat_percentage'          => 'nullable|numeric|min:0|max:100',
            'vat_certificate'         => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',

            'corporate_tax_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',

            'corporate_tax_number'    => 'nullable|string|max:150',
            'corporate_tax_date'      => 'nullable|string',
            'corporate_tax_vat'       => 'nullable|string',
            'ct_issuing_authority'    => 'nullable|string|max:150',

            'vat_date'                => 'nullable|string',
            'vat_issuing_authority'   => 'nullable|string|max:150',
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

        // BUILD PAYLOAD
        // Persist VAT/CT fields directly from request so values are not unintentionally dropped.
        $data = [
            'trade_license_no' => $request->trade_license_no,

            'license_issue_date'      => $this->toSqlDate($request->license_issue_date),
            'license_expiry_date'     => $this->toSqlDate($request->license_expiry_date),

            'issuing_authority'       => $request->issuing_authority,

            // VAT (save values if present)
            'vat_registration_number' => $request->vat_registration_number ?: null,
            'vat_percentage'          => $request->vat_percentage ?: null,
            'vat_date'                => $this->toSqlDate($request->vat_date),
            'vat_issuing_authority'   => $request->vat_issuing_authority ?: null,

            // CT (save values if present)
            'corporate_tax_number'       => $request->corporate_tax_number ?: null,
            'corporate_tax_date'         => $this->toSqlDate($request->corporate_tax_date),
            'corporate_tax_vat'          => $request->corporate_tax_vat ?: null,
            // Accept either 'corporate_issuing_authority' or older 'ct_issuing_authority' key
            'corporate_issuing_authority' => $request->corporate_issuing_authority ?? $request->ct_issuing_authority ?? null,
        ];

        // FILES
        $dir = public_path('uploads/company');
        if (!is_dir($dir)) @mkdir($dir, 0777, true);

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

        // SAVE (fixed key)
        $compliance = SysCompanyCompliance::updateOrCreate(
            ['company_id' => $request->company_id],  // ✔ CORRECT
            $data
        );

        return response()->json(['ok' => true, 'compliance_id' => $compliance->id]);
    }



    // Convert date to SQL format
    protected function toSqlDate($value)
    {
        if (!$value) return null;

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
            'policy_date'       => $request->input('policy_date'),
            'policy_name'       => $request->input('policy_name'),
            'policy_category'   => $request->input('policy_category'),
            'policy_valid'      => $request->input('policy_valid'),
            'view_to_employees' => $request->input('view_to_employees'),
            'policy_details'    => $request->input('policy_details'),
            'policy_file'       => $request->file('policy_file') // simple name only
        ];

        $request->merge(['policies' => [$policy]]);

        // VALIDATION
        $validator = Validator::make($request->all(), [
            'policies'                        => 'required|array|min:1',
            'policies.0.policy_date'          => 'required|date',
            'policies.0.policy_name'          => 'required|string|max:255',
            'policies.0.policy_category'      => 'nullable|string|max:50',
            'policies.0.policy_valid'         => 'nullable|date',
            'policies.0.view_to_employees'    => 'required|in:0,1',
            'policies.0.policy_details'       => 'nullable|string',
            'policy_file'                     => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:5120',
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
            if (!file_exists($path)) mkdir($path, 0777, true);

            $file->move($path, $filename);
            $filePath = 'uploads/company/hr_policies/' . $filename;
        }

        // SAVE
        $policy = SysCompanyHrPolicy::create([
            'company_id'        => $companyId,
            'policy_date'       => $request->input('policy_date'),
            'policy_name'       => $request->input('policy_name'),
            'policy_category'   => $request->input('policy_category'),
            'policy_valid'      => $request->input('policy_valid'),
            'view_to_employees' => (int)$request->input('view_to_employees'),
            'policy_details'    => $request->input('policy_details'),
            'policy_file'       => $filePath
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
        if (!$value) return null;
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
    }
    else {

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
            $f   = $request->file($fileKey);
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
            $ext  = 'bin';
            if ($mime === 'application/pdf') $ext = 'pdf';
            else if ($mime === 'image/png')  $ext = 'png';
            else if ($mime === 'image/webp') $ext = 'webp';
            else if ($mime === 'image/jpeg' || $mime === 'image/jpg') $ext = 'jpg';

            $commaPos = strpos($maybeDataUrl, ',');
            $b64      = substr($maybeDataUrl, $commaPos + 1);
            $binary   = base64_decode($b64, true);

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
            'company_id'               => 'nullable|integer',
            'wps_establishment_id'     => 'required|string|max:150',
            'wps_bank'                 => 'required|string|max:150',
            'wps_salary_file_code'     => 'nullable|string|max:100',
            'payroll_cycle'            => 'required|in:monthly,bi-weekly,weekly',
            'payroll_start'            => 'required|integer|min:1|max:30',
            'payroll_end'              => 'required|integer|min:1|max:30',
            'weekly_off'               => 'nullable|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'gratuity_method'          => 'nullable|in:basic_salary,gross_salary',
            'insurance_provider'       => 'nullable|string|max:150',
            'insurance_policy_number'  => 'nullable|string|max:150',
            'insurance_policy_expiry'  => 'nullable|date_format:Y-m-d', // ← strict Y-m-d
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
            $addField = function($dbColumn, $formField) use ($r, &$data) {
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
            $data['weekly_off_day'] = (empty($weeklyOff) || $weeklyOff === 'null') ? null : substr(trim($weeklyOff), 0, 50);
            
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
            $addField('comp_off_allowed', 'comp_off_allowed');
            $addField('carry_forward_unused_leaves', 'carry_forward');
            $addField('max_carry_forward_days', 'max_carry_forward');
            $addField('encashable_leaves', 'leave_encashment');

            // Handle weekly_off_days as JSON array (OPTIONAL - only if user selects)
            $weeklyOffDays = $r->input('weekly_off_days', []);
            if (is_array($weeklyOffDays) && !empty($weeklyOffDays)) {
                // Filter out empty values
                $filtered = array_filter($weeklyOffDays, function($val) {
                    return !empty($val);
                });
                $data['weekly_off_days'] = !empty($filtered) ? json_encode(array_values($filtered)) : null;
            } else {
                $data['weekly_off_days'] = null;
            }

            // Remove null values to avoid updating unnecessary columns
            // This ensures we only update fields that user explicitly set
            $dataToUpdate = array_filter($data, function($value, $key) {
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

            return response()->json([
                'status' => true,
                'message' => 'HR Payroll Settings saved successfully',
                'data' => $setting
            ]);

        } catch (\Exception $e) {
            \Log::error('storeHrPayrollSetting error: ' . $e->getMessage(), [
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
        if (!$r->hasFile($key)) return null;

        $dir = public_path('uploads/company/docs');
        if (!is_dir($dir)) @mkdir($dir, 0777, true);

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
            'establishment_file'   => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:5120',

            'immigration_number' => 'nullable|string|max:150',
            'immigration_expiry' => 'nullable|string',
            'immigration_file'   => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:5120',

            'labour_number' => 'nullable|string|max:150',
            'labour_expiry' => 'nullable|string',
            'labour_file'   => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:5120',

            'chamber_number' => 'nullable|string|max:150',
            'chamber_expiry' => 'nullable|string',
            'chamber_file'   => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:5120',

            'insurance_certificate_number' => 'nullable|string|max:150',
            'insurance_certificate_expiry' => 'nullable|string',
            'insurance_file'               => 'nullable|mimes:pdf,jpg,jpeg,png,webp|max:5120',

            'moa_aoa_number'        => 'nullable|string|max:100',
            'moa_aoa_expiry'        => 'nullable|string',
            'moa_aoa_file'          => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:5120',
            
            'board_resolution_number' => 'nullable|string|max:100',
            'board_resolution_expiry' => 'nullable|string',
            'board_resolution_file' => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:5120',
            
            'poa_number'            => 'nullable|string|max:100',
            'poa_expiry'            => 'nullable|string',
            'poa_file'              => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:5120',
        ]);

        $parseDate = function ($date) {
            if (!$date) return null;

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
                if (!is_dir($dir)) mkdir($dir, 0777, true);

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
            'establishment_file'   => $saveFile('establishment_file', 'est'),

            'immigration_number'   => $r->immigration_number,
            'immigration_expiry'   => $parseDate($r->immigration_expiry),
            'immigration_file'     => $saveFile('immigration_file', 'imm'),

            'labour_number'        => $r->labour_number,
            'labour_expiry'        => $parseDate($r->labour_expiry),
            'labour_file'          => $saveFile('labour_file', 'lab'),

            'chamber_number'       => $r->chamber_number,
            'chamber_expiry'       => $parseDate($r->chamber_expiry),
            'chamber_file'         => $saveFile('chamber_file', 'chm'),

            'insurance_certificate_number' => $r->insurance_certificate_number,
            'insurance_certificate_expiry' => $parseDate($r->insurance_certificate_expiry),
            'insurance_file'               => $saveFile('insurance_file', 'ins'),

            'moa_aoa_number'        => $r->moa_aoa_number,
            'moa_aoa_expiry'        => $parseDate($r->moa_aoa_expiry),
            'moa_aoa_file'          => $saveFile('moa_aoa_file', 'moa'),
            
            'board_resolution_number' => $r->board_resolution_number,
            'board_resolution_expiry' => $parseDate($r->board_resolution_expiry),
            'board_resolution_file' => $saveFile('board_resolution_file', 'brd'),
            
            'poa_number'            => $r->poa_number,
            'poa_expiry'            => $parseDate($r->poa_expiry),
            'poa_file'              => $saveFile('poa_file', 'poa'),
        ];

        // REMOVE NULL FILE KEYS (to avoid overwriting existing file)
        foreach (['establishment_file', 'immigration_file', 'labour_file', 'chamber_file', 'insurance_file', 'moa_aoa_file', 'board_resolution_file', 'poa_file'] as $f) {
            if ($data[$f] === null) unset($data[$f]);
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
            \Log::error('storeDocuments failed: ' . $ex->getMessage(), [
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
        if (!$path) return null;
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
        $country   = SysCountries::all();
        $currency  = SysCurrency::all();

        // --- HR & Payroll (1:1) ---
        $hr = CompanyHrPayroll::where('company_id', $company->id)->first();

        // --- Banking (multi) ---
        $banks = SysCompanyBanking::where('company_id', $company->id)
            ->orderBy('id')
            ->get()
            ->map(function ($b) {
                return [
                    'bank_name'      => $b->bank_name,
                    'branch_name'    => $b->branch_name,
                    'account_number' => $b->account_number,
                    'iban_number'    => $b->iban_number,
                    'swift_code'     => $b->swift_code,
                    'finance_code'   => $b->finance_code,
                    'currency'       => $b->currency,
                    'bank_letter'    => null, // file input blank
                    'bank_letter_url' => $this->fileUrl($b->bank_letter),
                ];
            })->values();

        // --- HR Policies (multi) ---
        $policies = SysCompanyHrPolicy::where('company_id', $company->id)
            ->orderBy('id')
            ->get()
            ->map(function ($p) {
                return [
                    'id'                 => $p->id,
                    'uid'                => 'pol_' . $p->id, // editor key
                    'policy_date'        => optional($p->policy_date)->format('Y-m-d') ?? ($p->policy_date ?: ''),
                    'policy_name'        => $p->policy_name,
                    'policy_category'    => $p->policy_category,
                    'policy_valid'       => optional($p->policy_valid)->format('Y-m-d') ?? ($p->policy_valid ?: ''),
                    'view_to_employees'  => (int) $p->view_to_employees,
                    'policy_details'     => $p->policy_details,
                    'policy_file'        => null, // file input blank
                    'policy_file_url'    => $this->fileUrl($p->policy_file),
                ];
            })->values();

        // --- Documents (8 items) ---
        // Agar docs same SysCompany table me columns me stored hain:
        $docsSeed = [
            'establishment' => [
                'number' => $company->establishment_number ?? '',
                'expiry' => $company->establishment_expiry ?? '',
                'file'   => null,
                'url'    => $this->fileUrl($company->establishment_file ?? null),
            ],
            'immigration' => [
                'number' => $company->immigration_number ?? '',
                'expiry' => $company->immigration_expiry ?? '',
                'file'   => null,
                'url'    => $this->fileUrl($company->immigration_file ?? null),
            ],
            'labour' => [
                'number' => $company->labour_number ?? '',
                'expiry' => $company->labour_expiry ?? '',
                'file'   => null,
                'url'    => $this->fileUrl($company->labour_file ?? null),
            ],
            'chamber' => [
                'number' => $company->chamber_number ?? '',
                'expiry' => $company->chamber_expiry ?? '',
                'file'   => null,
                'url'    => $this->fileUrl($company->chamber_file ?? null),
            ],
            'insurance' => [
                'number' => $company->insurance_certificate_number ?? '',
                'expiry' => $company->insurance_certificate_expiry ?? '',
                'file'   => null,
                'url'    => $this->fileUrl($company->insurance_file ?? null),
            ],
            'moa_aoa' => [
                'file' => null,
                'url'  => $this->fileUrl($company->moa_aoa_file ?? null),
            ],
            'board_resolution' => [
                'file' => null,
                'url'  => $this->fileUrl($company->board_resolution_file ?? null),
            ],
            'poa' => [
                'file' => null,
                'url'  => $this->fileUrl($company->poa_file ?? null),
            ],
        ];

        // --- SEED payload Vue ke liye (create wale keys jaisey) ---
        $seed = [
            'form' => [
                'company_id'          => $company->id,
                'company_name'        => $company->company_name,
                'trade_name'          => $company->trade_name,
                'legal_entity_type'   => $company->legal_entity_type,
                'industry'            => $company->industry,
                'parent_company'      => $company->parent_company,
                'date_of_incorporation' => $company->date_of_incorporation,
                'country'             => $company->country,
                'city'                => $company->city,
                'company_address'     => $company->company_address,
                'sales_code'          => $company->sales_code,
                'other_code'          => $company->other_code,
                'currency'            => $company->currency,
                'currency_digit'      => $company->currency_digit,
                'book_closed'         => $company->book_closed,
                // file inputs blank, but show preview via URL in blade if needed
                'email'               => $company->company_email,
                'website'             => $company->website,
                'telephone'           => $company->telephone,
                'fax'                 => $company->fax,
                'mobile'              => $company->mobile,
                'contact_sections'    => $company->contact_sections ?? [],

                'owner' => [
                    'name'   => $company->owner_name,
                    'mobile' => $company->owner_mobile,
                    'email'  => $company->owner_email,
                    'files'  => ['passport_copy' => null, 'emirates_id' => null, 'visa_copy' => null],
                ],
                'sponsor' => [
                    'name'   => $company->sponsor_name,
                    'mobile' => $company->sponsor_mobile,
                    'email'  => $company->sponsor_email,
                    'files'  => ['passport_copy' => null, 'emirates_id' => null, 'visa_copy' => null],
                ],
                'contact' => [
                    'name'        => $company->contact_person_name,
                    'mobile'      => $company->contact_person_mobile,
                    'email'       => $company->contact_person_email,
                    'designation' => $company->contact_person_designation,
                    'files'       => ['passport_copy' => null, 'emirates_id' => null, 'visa_copy' => null],
                ],

                // Compliance (agar same table me columns hain)
                'compliance' => [
                    'business_license_number' => $company->business_license_number,
                    'license_issue_date'      => $company->license_issue_date,
                    'license_expiry_date'     => $company->license_expiry_date,
                    'issuing_authority'       => $company->issuing_authority,
                    'tax_applicable'          => $company->tax_applicable,
                    'vat_registration_number' => $company->vat_registration_number,
                    'vat_percentage'          => $company->vat_percentage,
                    'vat_date'                => $company->vat_date,
                    'corporate_tax_number'    => $company->corporate_tax_number,
                    'corporate_tax_vat'       => $company->corporate_tax_vat,
                    'corporate_tax_date'      => $company->corporate_tax_date,
                ],

                // HR
                'hr' => [
                    'wps_establishment_id'   => optional($hr)->wps_establishment_id,
                    'wps_bank'               => optional($hr)->wps_bank,
                    'wps_salary_file_code'   => optional($hr)->wps_salary_file_code,
                    'payroll_cycle'          => optional($hr)->payroll_cycle,
                    'weekly_off'             => optional($hr)->weekly_off,
                    'gratuity_method'        => optional($hr)->gratuity_method,
                    'insurance_provider'     => optional($hr)->insurance_provider,
                    'insurance_policy_number' => optional($hr)->insurance_policy_number,
                    'insurance_policy_expiry' => optional($hr)->insurance_policy_expiry,
                ],
            ],

            'banks'    => $banks,
            'policies' => $policies,
            'docs'     => $docsSeed,
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
            'seed'    => $seed,
            'nextId'  => $company->id, // JS me company_id set
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
            'industries'       => \App\SmIndustry::orderBy('name')->get(),
            'activities'       => \App\SmBusinessActivity::orderBy('name')->get(),
            'parent_companies' => SysCompany::where('company_type', 'parent')->get(),
            'countries'        => SysCountries::orderBy('name')->get(),
            'states'           => SysStates::orderBy('name')->get(),
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
            'currency'                  => 'nullable|string|max:10',
            'currency_symbol'           => 'nullable|string|max:10',
            'currency_digit_display'    => 'nullable|integer|min:0|max:4',
            'r_code'                    => 'nullable|string|max:50',
            'p_code'                    => 'nullable|string|max:50',
            'book_closed'               => 'nullable|string',
            'sales_code'                => 'nullable|string|max:50',
            'other_code'                => 'nullable|string|max:50',
            'hr_wps_establishment_id'   => 'nullable|string|max:100',
            'hr_wps_bank'               => 'nullable|string|max:100',
            'hr_wps_salary_file_code'   => 'nullable|string|max:50',
            'hr_payroll_cycle'          => 'nullable|in:monthly,bi-weekly,weekly',
            'hr_payroll_start'          => 'nullable|integer|min:1|max:30',
            'hr_payroll_end'            => 'nullable|integer|min:1|max:30',
            'hr_weekly_off'             => 'nullable|string|max:15',
            'hr_gratuity_method'        => 'nullable|in:basic_salary,gross_salary',
            'hr_insurance_provider'     => 'nullable|string|max:100',
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
            'is_customer_code'           => $request->has('is_customer_code') ? 1 : 0,
            'is_supplier_code'           => $request->has('is_supplier_code') ? 1 : 0,
            'is_account_code'            => $request->has('is_account_code') ? 1 : 0,
            'is_subaccount_code'         => $request->has('is_subaccount_code') ? 1 : 0,

            'currency'                   => $request->currency,
            'currency_symbol'            => $request->currency_symbol ?: null,
            'currency_digit_display'     => $request->currency_digit_display ?: 0,
            'r_code'                     => $request->r_code ?: null,
            'p_code'                     => $request->p_code ?: null,
            'book_closed'                => $bookClosed,

            'sales_code'                 => $request->sales_code,
            'other_code'                 => $request->other_code,

            'hr_wps_establishment_id'    => $request->hr_wps_establishment_id,
            'hr_wps_bank'                => $request->hr_wps_bank,
            'hr_wps_salary_file_code'    => $request->hr_wps_salary_file_code,
            'hr_payroll_cycle'           => $request->hr_payroll_cycle ?: null,
            'hr_payroll_start'           => $request->filled('hr_payroll_start') ? (int) $request->hr_payroll_start : null,
            'hr_payroll_end'             => $request->filled('hr_payroll_end') ? (int) $request->hr_payroll_end : null,
            'hr_weekly_off'              => $request->hr_weekly_off,
            'hr_gratuity_method'         => $gratuityMethod,

            'hr_insurance_provider'      => $request->hr_insurance_provider,
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


    public function companyEdit($id)
    {
        try {
            // Clear old input to prevent previous validation data showing up on page refresh
            session()->forget('_old_input');
            // Clear session-based banking and policy data to prevent stale values on refresh
            session()->forget(['company_banks', 'company_policies']);

            $company = SysCompany::with([
                'countryRelation',
                'stateRelation',
                'currency',
                'businessEntity',
                'businessIndustry',
                'businessSector',
                'parentCompany',
                'settings',
                'hrPolicies',
                'compliance',
                'people',
                'banking',
                'documents'
            ])->findOrFail($id);

            $parentCompanies = SysCompany::where('company_type', 'parent')->get();
            $country   = SysCountries::all();
            $currency  = SysCurrency::all();
            $industries = \App\SmIndustry::orderBy('name')->get();
            $activities = \App\SmBusinessActivity::with('industry')->orderBy('name')->get();
            $entities = \App\SmBusinessEntityType::orderBy('name')->get();

            // Read-only company code
            $nextId = $company->company_code ?? $company->id;

            // Preload states for the company's country so the state select shows saved value
            $states = collect();
            if (!empty($company->country)) {
                $states = SysStates::where('country_id', $company->country)->get();
            }

            // Prepare owners, sponsors, contacts arrays for the edit form
            $owners = [];
            $sponsors = [];
            $contacts = [];

            if ($company->people && $company->people->count()) {
                foreach ($company->people as $p) {
                    $row = [
                        'name' => $p->name ?? '',
                        'mobile' => $p->mobile ?? '',
                        'email' => $p->email ?? '',
                    ];

                    if ($p->type === 'owner') {
                        $owners[] = $row;
                    } elseif ($p->type === 'sponsor') {
                        $sponsors[] = $row;
                    } elseif ($p->type === 'contact') {
                        // include designation for contact if available
                        $row['designation'] = $p->designation ?? '';
                        $contacts[] = $row;
                    }
                }
            }

            // Ensure at least one blank row exists for each section to render inputs
            if (empty($owners)) $owners = [['name' => '', 'mobile' => '', 'email' => '']];
            if (empty($sponsors)) $sponsors = [['name' => '', 'mobile' => '', 'email' => '']];
            if (empty($contacts)) $contacts = [['name' => '', 'mobile' => '', 'email' => '', 'designation' => '']];

            // Seed session-backed banks and policies for the edit UI
            // Only populate if the session keys are not already set (to avoid clobbering in-progress edits)
            if (!session()->has('company_banks') || empty(session('company_banks'))) {
                $bankSession = [];
                if ($company->banking && $company->banking->count()) {
                    foreach ($company->banking as $b) {
                        $bankSession[(string)$b->id] = [
                            'id' => (string)$b->id,
                            'bank_name' => $b->bank_name,
                            'branch_name' => $b->branch_name,
                            'account_number' => $b->account_number,
                            'iban_number' => $b->iban_number,
                            'swift_code' => $b->swift_code,
                            'finance_code' => $b->finance_code,
                            'currency' => $b->currency,
                            'bank_letter' => $b->bank_letter ?? null,
                        ];
                    }
                }
                session(['company_banks' => $bankSession]);
            }

            if (!session()->has('company_policies') || empty(session('company_policies'))) {
                $policySession = [];
                if ($company->hrPolicies && $company->hrPolicies->count()) {
                    foreach ($company->hrPolicies as $p) {
                        $policySession[(string)$p->id] = [
                            'id' => (string)$p->id,
                            'policy_date' => $p->policy_date,
                            'policy_name' => $p->policy_name,
                            'policy_category' => $p->policy_category,
                            'policy_valid' => $p->policy_valid,
                            'view_to_employees' => (int) $p->view_to_employees,
                            'policy_details' => $p->policy_details,
                            'policy_file' => $p->policy_file ?? null,
                        ];
                    }
                }
                session(['company_policies' => $policySession]);
            }

            return view(
                'backEnd.company.addCompany',
                compact('company', 'country', 'currency', 'nextId', 'industries', 'activities', 'entities', 'parentCompanies', 'states', 'owners', 'sponsors', 'contacts')
            );
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


   

public function storeBankSession(Request $request)
{
    // Validate required fields
    $request->validate([
        'bank_name'      => 'required',
        'account_number' => 'required',
        'iban_number'    => 'required'
    ]);

    $banks = session('company_banks', []);

    // If bank_id exists, update; else create new
    $id = $request->bank_id ?: uniqid();

    $bank = [
        'id'             => $id,
        'bank_name'      => $request->bank_name,
        'branch_name'    => $request->branch_name,
        'account_number' => $request->account_number,
        'iban_number'    => $request->iban_number,
        'swift_code'     => $request->swift_code,
        'finance_code'   => $request->finance_code,
        'currency'       => $request->currency,
        'bank_letter'    => $banks[$id]['bank_letter'] ?? null,
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
                'company_id'     => $companyId,
                'bank_name'      => $b['bank_name'],
                'branch_name'    => $b['branch_name'] ?? null,
                'account_number' => $b['account_number'],
                'iban_number'    => $b['iban_number'],
                'swift_code'     => $b['swift_code'] ?? null,
                'finance_code'   => $b['finance_code'] ?? null,
                'currency'       => $b['currency'] ?? null,
                'bank_letter'    => $b['bank_letter'] ?? null,
            ]);
            $savedCount++;
        } catch (\Exception $e) {
            \Log::error('Failed to save bank for company ' . $companyId . ': ' . $e->getMessage());
            return response()->json([
                'ok' => false,
                'message' => 'Error saving banking record: ' . $e->getMessage()
            ], 500);
        }
    }

    // Clear session banks after successful persistence to avoid duplicate inserts
    session()->forget('company_banks');

    return response()->json([
        'ok'      => true,
        'message' => "Saved: $savedCount new banking records. Skipped: $skippedCount duplicates.",
        'saved' => $savedCount,
        'skipped' => $skippedCount,
        'count'   => count($banks)
    ]);

}

public function checkBankingSession($company_id)
{
    $banking = session('company_banks', []);

    return response()->json([
        'hasSession' => count($banking) > 0,
        'count'      => count($banking)
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
        if (!is_dir($dir)) @mkdir($dir, 0777, true);
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
        \Log::error('saveAllPolicies failed: ' . $ex->getMessage(), [
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
        'designation' => $request->designation,
        'share_percentage' => $request->share_percentage,
        'documents' => []
    ];

    // Handle documents
    if ($request->has('documents')) {
        foreach ($request->documents as $docIndex => $doc) {
            if (empty($doc['document_name'])) continue;

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
                if (!is_dir($dir)) @mkdir($dir, 0777, true);
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
                    if (empty($doc['document_name'])) continue;

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
        
        \Log::error('saveAllPeople failed: ' . $ex->getMessage(), [
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
    
    $documents = array_filter($documents, function($doc) use ($documentId) {
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
        
        \Log::error('saveAllComplianceDocuments failed: ' . $ex->getMessage(), [
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
            \Log::info('storeDocumentSession called with method: ' . $request->method());
            \Log::info('storeDocumentSession called with data: ' . json_encode($request->all()));
            
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
                'document_date' => 'nullable|date',
                'expiry_date' => 'nullable|date',
                'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:5120',
                'person_type' => 'required|in:owner,sponsor,contact',
                'person_index' => 'required|integer'
            ]);

            $documentSessions = session('documentSessions', []);
            \Log::info('Current session state: ' . json_encode($documentSessions));
            
            // Handle file upload if provided
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = uniqid('people_doc_') . '.' . $file->getClientOriginalExtension();
                
                // Store in temporary location
                $uploadPath = public_path('uploads/company/people/temp');
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                $file->move($uploadPath, $fileName);
                $attachmentPath = "uploads/company/people/temp/{$fileName}";
                \Log::info('File uploaded to: ' . $attachmentPath);
            }

            $document = [
                'id' => uniqid(),
                'name' => $request->document_name,
                'number' => $request->document_number,
                'date' => $request->document_date,
                'expiry_date' => $request->expiry_date,
                'attachment' => $attachmentPath,
                'attachment_name' => $request->hasFile('attachment') ? $request->file('attachment')->getClientOriginalName() : null
            ];
            
            \Log::info('Document created: ' . json_encode($document));

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
            
            \Log::info('Updated session state: ' . json_encode($documentSessions));
            
            // Immediately check if session was saved
            \Log::info('Verification - session after save: ' . json_encode(session('documentSessions', [])));
            \Log::info('Session ID: ' . session()->getId());
            \Log::info('Session driver: ' . config('session.driver'));

            return response()->json([
                'ok' => true,
                'document' => $document,
                'message' => 'Document saved to session successfully'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in storeDocumentSession: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
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
                    // Delete temporary file if exists
                    if (!empty($doc['attachment']) && file_exists(public_path($doc['attachment']))) {
                        @unlink(public_path($doc['attachment']));
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
                if (!is_dir($dir)) mkdir($dir, 0777, true);

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
            \Log::error('storeCompanyDocumentSession failed', [
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
            $items = array_filter($items, function($item) use ($rowId) {
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
            \Log::error('persistCompanyDocuments failed', [
                'error' => $e->getMessage(),
                'request' => $r->all()
            ]);
            return response()->json([
                'ok' => false,
                'message' => 'Failed to save documents: ' . $e->getMessage()
            ], 500);
        }
    }
}
