<?php

namespace App\Http\Controllers;

use App\SysCompany;
use App\SmCountry;
use App\SysCompanyCompliance;
use App\SysCompanyHrPolicy;
use App\SysCompanyBanking;
use App\CompanyHrPayroll;
use App\ApiBaseMethod;
use App\SmItem;
use App\SysCities;
use App\SysCompanyDocument;
use App\SysCompanySetting;
use App\SysStates;
use App\SysCountries;
use App\SysCurrency;
use App\SysHelper;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        // VALIDATION
        $rules = [
            'company_name'       => 'required|string|max:255',
            'business_entity_id' => 'required',
            'industry_id'        => 'required',
            'business_sector_id' => 'required',
            'company_type'       => 'required',
            'company_code'       => 'required',
            'country' => 'required|integer',
            'state'   => 'required|integer',
        ];

        $v = Validator::make($request->all(), $rules);

        if ($v->fails()) {
            return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
        }

        $company_id = $request->company_id; // null if new

        $data = $request->only([
            'company_name',
            'trade_name',
            'business_entity_id',
            'industry_id',
            'business_sector_id',
            'company_type',
            'company_code'
        ]);

        // COMPANY TYPE LOGIC
        if ($request->company_type == 'parent') {
            $data['parent_company']    = $request->company_name;
            $data['parent_company_id'] = null;
            $data['type']              = 'Parent Company';
        } elseif ($request->company_type == 'subsidiary') {
            $data['parent_company']    = null;
            $data['parent_company_id'] = $request->parent_company_id;
            $data['type']              = 'Subsidiary';
        } elseif ($request->company_type == 'branch') {
            $data['parent_company']    = null;
            $data['parent_company_id'] = $request->parent_company_id;
            $data['type']              = 'Branch';
        }

        // FILES
        foreach (['company_logo', 'digital_stamp', 'company_profile'] as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);
                $filename = time() . '_' . $fileKey . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/company'), $filename);
                $data[$fileKey] = 'uploads/company/' . $filename;
            }
        }
        $company_id = $request->company_id; // now this will have value
        if ($company_id) {
            $company = SysCompany::find($company_id); // WHERE id = company_id
            if ($company) {
                $company->update($data);
            } else {
                $company = SysCompany::create($data);
            }
        } else {
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
            'owners'            => 'nullable|array',
            'owners.*.name'     => 'nullable|string|max:255',
            'owners.*.mobile'   => 'nullable|string|max:100',
            'owners.*.email'    => 'nullable|email',

            // Sponsors
            'sponsors'            => 'nullable|array',
            'sponsors.*.name'     => 'nullable|string|max:255',
            'sponsors.*.mobile'   => 'nullable|string|max:100',
            'sponsors.*.email'    => 'nullable|email',

            // Contact people
            'contacts'               => 'nullable|array',
            'contacts.*.name'        => 'nullable|string|max:255',
            'contacts.*.mobile'      => 'nullable|string|max:100',
            'contacts.*.email'       => 'nullable|email',
            'contacts.*.designation' => 'nullable|string|max:255'
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
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

        // Delete old people rows
        DB::table('sys_company_people')->where('company_id', $company->id)->delete();

        // ---------- OWNERS ----------
        if ($request->owners) {
            foreach ($request->owners as $i => $o) {
                DB::table('sys_company_people')->insert([
                    'company_id'   => $company->id,
                    'type'         => 'owner',
                    'name'         => $o['name'] ?? null,
                    'mobile'       => $o['mobile'] ?? null,
                    'email'        => $o['email'] ?? null,
                    'passport_copy' => $saveFile($request->file("owner_files.$i.passport_copy")),
                    'emirates_id'  => $saveFile($request->file("owner_files.$i.emirates_id")),
                    'visa_copy'    => $saveFile($request->file("owner_files.$i.visa_copy")),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }

        // ---------- SPONSORS ----------
        if ($request->sponsors) {
            foreach ($request->sponsors as $i => $s) {
                DB::table('sys_company_people')->insert([
                    'company_id'   => $company->id,
                    'type'         => 'sponsor',
                    'name'         => $s['name'] ?? null,
                    'mobile'       => $s['mobile'] ?? null,
                    'email'        => $s['email'] ?? null,
                    'passport_copy' => $saveFile($request->file("sponsor_files.$i.passport_copy")),
                    'emirates_id'  => $saveFile($request->file("sponsor_files.$i.emirates_id")),
                    'visa_copy'    => $saveFile($request->file("sponsor_files.$i.visa_copy")),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }

        // ---------- CONTACT PERSON ----------
        if ($request->contacts) {
            foreach ($request->contacts as $i => $c) {
                DB::table('sys_company_people')->insert([
                    'company_id'   => $company->id,
                    'type'         => 'contact',
                    'name'         => $c['name'] ?? null,
                    'mobile'       => $c['mobile'] ?? null,
                    'email'        => $c['email'] ?? null,
                    'designation'  => $c['designation'] ?? null,
                    'passport_copy' => $saveFile($request->file("contact_files.$i.passport_copy")),
                    'emirates_id'  => $saveFile($request->file("contact_files.$i.emirates_id")),
                    'visa_copy'    => $saveFile($request->file("contact_files.$i.visa_copy")),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }

        return response()->json([
            'ok' => true,
            'message' => 'Contact Information saved successfully'
        ]);
    }


    public function storeCompliance(Request $request)
    {
        $rules = [
            'company_id'              => 'exists:sys_company,id',
            'trade_license_no' => 'required|string|max:150',

            'license_issue_date'      => 'required|string',
            'license_expiry_date'     => 'required|string',

            'issuing_authority'       => 'required|string|max:150',

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
        $data = [
            'trade_license_no' => $request->trade_license_no,

            'license_issue_date'      => $this->toSqlDate($request->license_issue_date),
            'license_expiry_date'     => $this->toSqlDate($request->license_expiry_date),

            'issuing_authority'       => $request->issuing_authority,

            // VAT
            'vat_registration_number' => $request->tax_applicable !== 'none' ? $request->vat_registration_number : null,
            'vat_percentage'          => $request->tax_applicable !== 'none' ? $request->vat_percentage : null,
            'vat_date'                => $request->tax_applicable !== 'none' ? $this->toSqlDate($request->vat_date) : null,
            'vat_issuing_authority'   => $request->tax_applicable !== 'none' ? $request->vat_issuing_authority : null,

            // CT
            'corporate_tax_number'       => $request->tax_applicable !== 'none' ? $request->corporate_tax_number : null,
            'corporate_tax_date'         => $request->tax_applicable !== 'none' ? $this->toSqlDate($request->corporate_tax_date) : null,
            'corporate_tax_vat'          => $request->tax_applicable !== 'none' ? $request->corporate_tax_vat : null,
            'corporate_issuing_authority' => $request->ct_issuing_authority,
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
        // 1) VALIDATION
        $rules = [
            // 'id'      => 'nullable|exists:sys_company,id',
            'bank_name'       => 'required|string|max:150',
            'branch_name'     => 'nullable|string|max:150',
            'account_number'  => 'required|string|max:100',
            'iban_number'     => 'required|string|max:100',
            'swift_code'      => 'nullable|string|max:50',
            'finance_code'    => 'nullable|string|max:50',
            'currency'        => 'nullable|string|max:10',
            'bank_letter'     => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ];

        $v = Validator::make($request->all(), $rules);

        if ($v->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => $v->errors()
            ], 422);
        }

        // 2) FILE UPLOAD
        $filePath = null;
        if ($request->hasFile('bank_letter')) {
            $filePath = $request->file('bank_letter')
                ->store('uploads/company/banking_letters', 'public');
        }

        // 3) SAVE RECORD IN sys_company_bankings
        $bank = SysCompanyBanking::create([
            'company_id'     => $request->company_id,
            'bank_name'      => $request->bank_name,
            'branch_name'    => $request->branch_name,
            'account_number' => $request->account_number,
            'iban_number'    => $request->iban_number,
            'swift_code'     => $request->swift_code,
            'finance_code'   => $request->finance_code,
            'currency'       => $request->currency,
            'bank_letter'    => $filePath,
        ]);

        // 4) RETURN REAL-TIME RESPONSE FOR VUE
        return response()->json([
            'ok' => true,
            'msg' => 'Bank added successfully!',
            'bank' => $bank
        ], 201);
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

            'moa_aoa_file'          => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:5120',
            'board_resolution_file' => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:5120',
            'poa_file'              => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:5120',
        ]);

        $parseDate = function ($date) {
            if (!$date) return null;
            try {
                return \Carbon\Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
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

            'moa_aoa_file'          => $saveFile('moa_aoa_file', 'moa'),
            'board_resolution_file' => $saveFile('board_resolution_file', 'brd'),
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
            'currency_digit'            => 'nullable|integer|min:0|max:4',
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

        // DATA ARRAY
        $data = [
            'is_customer_code'           => $request->has('is_customer_code') ? 1 : 0,
            'is_supplier_code'           => $request->has('is_supplier_code') ? 1 : 0,
            'is_account_code'            => $request->has('is_account_code') ? 1 : 0,
            'is_subaccount_code'         => $request->has('is_subaccount_code') ? 1 : 0,

            'currency'                   => $request->currency,
            'currency_digit'             => $request->currency_digit ?: 0,
            'book_closed'                => $bookClosed,

            'sales_code'                 => $request->sales_code,
            'other_code'                 => $request->other_code,

            'hr_wps_establishment_id'    => $request->hr_wps_establishment_id,
            'hr_wps_bank'                => $request->hr_wps_bank,
            'hr_wps_salary_file_code'    => $request->hr_wps_salary_file_code,
            'hr_payroll_cycle'           => $request->hr_payroll_cycle ?: null,
            'hr_payroll_start'           => $request->hr_payroll_start,
            'hr_payroll_end'             => $request->hr_payroll_end,
            'hr_weekly_off'              => $request->hr_weekly_off,
            'hr_gratuity_method'         => $request->hr_gratuity_method,

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
}
