<?php

use App\Models\PushSubscription;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Illuminate\Http\Request;
//For Damo Project

if (Config::get('app.app_sync')) {
    Route::get('/', 'LandingController@index');
} else {
    Route::get('/', 'Auth\LoginController@showLoginForm')->name('login');
}


//For production
// 
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');
Route::get('error-404', function () {
    return view('auth.error');
})->name('error-404');




Route::get('/welcome', function () {
    return view('welcome');
});

Route::get("/admin/notify", function () {
    return view('notify', [
        'subscriptions' => PushSubscription::all()
    ]);
});

Route::post("admin/sendNotif/{sub}", function (PushSubscription $sub, Request $request) {
    try {

        return $sub->data;

        $webPush = new WebPush([
            "VAPID" => [
                "publicKey" => "BOPWfY51U_FzhkN3YGiLoRpNwHEN7Q_R_2YSRgqijTn4VVb8aBy5YoEEoAbevT0hL74L91qig0-hTAW3xo1Eg6M",
                "privateKey" => "KsI8O6YzDK9unkbqlOWpeEaLnWWADw35lexaDx5jDxg",
                "subject" => "http://localhost/syscom-erp-new/"
            ]
        ]);


        $result = $webPush->sendOneNotification(
            Subscription::create(json_decode($sub->data, true)),
            json_encode($request->input())
        );
        //dd($result);
        return redirect()->back();
    } catch (\Throwable $th) {
        return $th;
    }
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
Route::group(['middleware' => ['XSS']], function () {

    Route::group(['middleware' => ['CheckUserMiddleware']], function () {
        Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');

        Route::post('login', 'Auth\LoginController@login')->name('login');
        Route::get('recovery/passord', 'SmAuthController@recoveryPassord'); //forget password
        Route::post('email/verify', 'SmAuthController@emailVerify');
        Route::get('/reset/password/{email}/{code}', 'SmAuthController@resetEmailConfirtmation');
        Route::post('/store/new/password', 'SmAuthController@storeNewPassword');
        Route::get('login-2', 'Auth\LoginController@loginFormTwo');
    });

    Route::get('customer-from/{id}', 'Auth\LoginController@customer_form')->name('customer_form');
    Route::post('/leads/zapier', 'SysCrmLeadsController@storeLeadZapier');

    Route::get('customer-from-submited', 'Auth\LoginController@customer_from_submited')->name('customer_from_submited');
    Route::post('customer-form-store', 'Auth\LoginController@customer_form_store')->name('customer_form_store');

    Route::get('supplier-from/{id}', 'Auth\LoginController@supplier_form')->name('supplier_form');
    Route::get('supplier-from-submited', 'Auth\LoginController@supplier_from_submited')->name('supplier_from_submited');
    Route::post('supplier-form-store', 'Auth\LoginController@supplier_form_store')->name('supplier_form_store');


    Route::get('crm-amc-forms/{id}', 'Auth\LoginController@amcnoteAdd')->name('AmcNoteAdd');
    Route::post('crm-amc-submit', 'Auth\LoginController@amcnoteSubmit')->name('crm-amc-submit');

    Route::get('crm-amc-service-request-customer/{cusid}/{comid}', 'Auth\LoginController@servicerequestcustomer')->name('servicerequestcustomer');
    Route::post('crm-amc-service-request-customer-add', 'Auth\LoginController@servicerequestcustomeradd')->name('servicerequestcustomeradd');
    Route::get('crm-amc-service-request-customer-success', 'Auth\LoginController@servicerequestcustomersuccess')->name('servicerequestcustomersuccess');


    Route::get('get_state_not_logged', 'Auth\LoginController@get_state');
    Route::get('get_vat_details_not_logged', 'Auth\LoginController@getvatdetails');

    Route::get('home', 'HomeController@index')->name('admin-dashboard');





    //ajax theme change
    Route::get('theme-style-active', 'SmSystemSettingController@themeStyleActive');
    Route::get('theme-style-rtl', 'SmSystemSettingController@themeStyleRTL');





    Route::get('ajax-get-login-access', 'SmAuthController@getLoginAccess');
    Route::get('/after-login', 'HomeController@dashboard');
    Route::get('/dashboard', 'HomeController@dashboard');
    Route::get('view/single/notification/{id}', 'SmNotificationController@viewSingleNotification');
    Route::get('view/all/notification/{id}', 'SmNotificationController@viewAllNotification');
    Route::get('view/notice/{id}', 'HomeController@viewNotice');
    // update password
    Route::get('change-password', 'HomeController@updatePassowrd');
    Route::post('change-password', 'HomeController@updatePassowrdStore');

    Route::get('password-exp', 'HomeController@passwordexp');
    Route::post('change-password2', 'HomeController@updatePassowrdStore2');

    // User Auth Routes
    Route::group(['middleware' => ['CheckDashboardMiddleware']], function () {


        /* ************************ TENDER *************************** */
        Route::resource('quotations', 'SysQuotationController');


        Route::get('quotation-details/{id?}/{qid?}', 'SysQuotationController@getDetails');
        Route::get('quotations/{id}/edit', ['as' => 'edit', 'uses' => 'SysQuotationController@edit']);
        //Route::get('journalvoucher/{id}', ['as' => 'view', 'uses' => 'SysJournalVoucherController@view']);
        Route::put('quotations-update/{id}', ['as' => 'quotations-update', 'uses' => 'SysQuotationController@update']);

        Route::get('quotations/delete/{id}', 'SysQuotationController@delete');
        // Route::get('tender/view/{id}', 'SmTenderController@tenderView');  
        /* ************************ TENDER *************************** */

        //Proforma Invoice
        Route::resource('proforma-invoice', 'SysProformaInvoiceController');
        Route::get('proforma-invoice-details/{id}', 'SysProformaInvoiceController@getDetails');
        Route::get('proforma-invoice/{id}/edit', ['as' => 'edit', 'uses' => 'SysProformaInvoiceController@edit']);
        Route::put('proforma-invoice-update/{id}', ['as' => 'proforma-invoice-update', 'uses' => 'SysProformaInvoiceController@update']);
        Route::get('proforma-invoice/{id}/download', ['as' => 'edit', 'uses' => 'SysProformaInvoiceController@download']);

        Route::post('quotation-pending', 'SysProformaInvoiceController@quotationpending');
        Route::get('quotation-pending-item-list', 'SysProformaInvoiceController@quotationpendingitemlist');

        Route::get('proforma-invoice/delete/{id}', 'SysProformaInvoiceController@delete');


        /* ************************ TENDER *************************** */
        Route::get('tender-upcoming', 'SmTenderController@UpcomingTender');
        Route::get('upcoming-tender-list/print', 'SmTenderController@UpcomingTenderListPrintView');

        Route::get('upcoming-tender/print/{id}', 'SmTenderController@UpcomingTenderPrintView');
        Route::get('upcoming-tender-cancelled/{id}', 'SmTenderController@UpcomingTenderCancelled');
        Route::get('upcoming-tender-create', 'SmTenderController@UpcomingTenderCreate');
        Route::get('upcoming-tender-update', 'SmTenderController@UpcomingTenderUpdate');
        Route::post('upcoming-tender-store', 'SmTenderController@UpcomingTenderStore');

        Route::get('upcoming-tender/edit/{id}', 'SmTenderController@UpcomingTenderEdit');
        Route::post('upcoming-tender/update', 'SmTenderController@UpcomingTenderUpdate');
        Route::get('upcoming-tender/delete/{id}', 'SmTenderController@UpcomingTenderDelete');

        /* **************************** END ********************************* */

        Route::get('win-tender', 'SmTenderController@winTender'); //expired-tender

        Route::get('expired-tenders', 'SmTenderController@expiredTender');
        Route::get('expired-tender-create', 'SmTenderController@expiredTenderCreate');
        Route::get('expired-tender-update', 'SmTenderController@expiredTenderUpdate');
        Route::post('expired-tender-store', 'SmTenderController@expiredTenderStore');
        Route::post('ets', 'SmTenderController@ets');
        Route::get('expired-tender/edit/{id}', 'SmTenderController@expiredTenderEdit');
        Route::post('expired-tender/update', 'SmTenderController@expiredTenderUpdate');
        Route::get('expired-tender/delete/{id}', 'SmTenderController@expiredTenderDelete');

        Route::post('tender-lowest-store', 'SmTenderController@tenderLowestStore');
        Route::get('tender', 'SmTenderController@index');
        Route::post('work-order-update', 'SmTenderController@workOrderUpdate');
        Route::get('tender/status/{id}', 'SmTenderController@status');
        Route::get('tender/create', 'SmTenderController@create');
        Route::post('tender/store', 'SmTenderController@store');
        Route::get('tender/edit/{id}', 'SmTenderController@edit');
        Route::post('tender/update', 'SmTenderController@update');
        Route::get('tender/delete/{id}', 'SmTenderController@delete');
        Route::get('tender/view/{id}', 'SmTenderController@tenderView');

        Route::get('tender-shipment', 'SmTenderController@tenderShipment');
        Route::get('tender-delivered', 'SmTenderController@tenderDelivered');
        Route::get('tender-inspection-complete', 'SmTenderController@tenderInspection');
        Route::get('tender-completed', 'SmTenderController@tenderCompleted');

        /* ************************ TENDER *************************** */

        //naim

        Route::post('logout', 'Auth\LoginController@logout')->name('logout');

        Route::view('/admin-setup', 'frontEnd.admin_setup');
        Route::view('/general-setting', 'frontEnd.general_setting');
        Route::view('/student-id', 'frontEnd.student_id');
        Route::view('/add-homework', 'frontEnd.add_homework');
        Route::view('/fees-collection-invoice', 'frontEnd.fees_collection_invoice');
        Route::view('/exam-promotion-naim', 'frontEnd.exam_promotion');
        Route::view('/front-cms-gallery', 'frontEnd.front_cms_gallery');
        Route::view('/front-cms-media-manager', 'frontEnd.front_cms_media_manager');
        Route::view('/reports-class', 'frontEnd.reports_class');
        Route::view('/human-resource-payroll-generate', 'frontEnd.human_resource_payroll_generate');
        Route::view('/fees-collection-collect-fees', 'frontEnd.fees_collection_collect_fees');
        Route::view('/calendar', 'frontEnd.calendar');
        Route::view('/design', 'frontEnd.design');
        Route::view('/loginn', 'frontEnd.login');
        Route::view('/dash-board/super-admin', 'frontEnd.dashBoard.super_admin');

        //Route::get('/dashboard', 'HomeController@index')->name('dashboard');
        Route::get('manage-to-do-list', 'HomeController@manageToDoList');
        Route::get('add-toDo', 'HomeController@addToDo');
        Route::post('saveToDoData', 'HomeController@saveToDoData');
        Route::get('view-toDo/{id}', 'HomeController@viewToDo');
        Route::get('edit-toDo/{id}', 'HomeController@editToDo');
        Route::post('update-to-do', 'HomeController@updateToDo');
        Route::get('remove-to-do', 'HomeController@removeToDo');
        Route::get('get-to-do-list', 'HomeController@getToDoList');

        Route::get('admin-dashboard', 'HomeController@index')->name('admin-dashboard');

        //Role Setup
        Route::get('role', ['as' => 'role', 'uses' => 'RoleController@index']);
        Route::post('role-store', ['as' => 'role_store', 'uses' => 'RoleController@store']);
        Route::get('role-edit/{id}', ['as' => 'role_edit', 'uses' => 'RoleController@edit']);
        Route::post('role-update', ['as' => 'role_update', 'uses' => 'RoleController@update']);
        Route::post('role-delete', ['as' => 'role_delete', 'uses' => 'RoleController@delete']);

        // Role Permission
        Route::get('assign-permission/{id}', ['as' => 'assign_permission', 'uses' => 'SmRolePermissionController@assignPermission']);
        Route::post('role-permission-store', ['as' => 'role_permission_store', 'uses' => 'SmRolePermissionController@rolePermissionStore']);

        //User Route
        Route::get('user', ['as' => 'user', 'uses' => 'UserController@index']);
        Route::get('user-create', ['as' => 'user_create', 'uses' => 'UserController@create']);

        // Base group
        Route::get('base-group', ['as' => 'base_group', 'uses' => 'SmBaseGroupController@index']);
        Route::post('base-group-store', ['as' => 'base_group_store', 'uses' => 'SmBaseGroupController@store']);
        Route::get('base-group-edit/{id}', ['as' => 'base_group_edit', 'uses' => 'SmBaseGroupController@edit']);
        Route::post('base-group-update', ['as' => 'base_group_update', 'uses' => 'SmBaseGroupController@update']);
        Route::get('base-group-delete/{id}', ['as' => 'base_group_delete', 'uses' => 'SmBaseGroupController@delete']);

        // Base setup
        Route::get('base-setup', ['as' => 'base_setup', 'uses' => 'SmBaseSetupController@index']);
        Route::post('base-setup-store', ['as' => 'base_setup_store', 'uses' => 'SmBaseSetupController@store']);
        Route::get('base-setup-edit/{id}', ['as' => 'base_setup_edit', 'uses' => 'SmBaseSetupController@edit']);
        Route::post('base-setup-update', ['as' => 'base_setup_update', 'uses' => 'SmBaseSetupController@update']);
        Route::post('base-setup-delete', ['as' => 'base_setup_delete', 'uses' => 'SmBaseSetupController@delete']);

        //// Academics Routing

        // Class route
        Route::get('class', ['as' => 'class', 'uses' => 'SmClassController@index']);
        Route::post('class-store', ['as' => 'class_store', 'uses' => 'SmClassController@store']);
        Route::get('class-edit/{id}', ['as' => 'class_edit', 'uses' => 'SmClassController@edit']);
        Route::post('class-update', ['as' => 'class_update', 'uses' => 'SmClassController@update']);
        Route::get('class-delete/{id}', ['as' => 'class_delete', 'uses' => 'SmClassController@delete']);

        //Class Section routes
        Route::get('section', ['as' => 'section', 'uses' => 'SmSectionController@index']);
        Route::post('section-store', ['as' => 'section_store', 'uses' => 'SmSectionController@store']);
        Route::get('section-edit/{id}', ['as' => 'section_edit', 'uses' => 'SmSectionController@edit']);
        Route::post('section-update', ['as' => 'section_update', 'uses' => 'SmSectionController@update']);
        Route::get('section-delete/{id}', ['as' => 'section_delete', 'uses' => 'SmSectionController@delete']);

        // Subject routes
        Route::get('subject', ['as' => 'subject', 'uses' => 'SmSubjectController@index']);
        Route::post('subject-store', ['as' => 'subject_store', 'uses' => 'SmSubjectController@store']);
        Route::get('subject-edit/{id}', ['as' => 'subject_edit', 'uses' => 'SmSubjectController@edit']);
        Route::post('subject-update', ['as' => 'subject_update', 'uses' => 'SmSubjectController@update']);
        Route::get('subject-delete/{id}', ['as' => 'subject_delete', 'uses' => 'SmSubjectController@delete']);

        //Class Routine
        // Route::get('class-routine', ['as' => 'class_routine', 'uses' => 'SmAcademicsController@classRoutine']);
        // Route::get('class-routine-create', ['as' => 'class_routine_create', 'uses' => 'SmAcademicsController@classRoutineCreate']);
        Route::get('ajaxSelectSubject', 'SmAcademicsController@ajaxSelectSubject');
        Route::get('ajaxSelectCurrency', 'SmSystemSettingController@ajaxSelectCurrency');

        Route::get('ajaxSubCategory', 'SmSystemSettingController@ajaxSubCategory');

        // Route::post('assign-routine-search', 'SmAcademicsController@assignRoutineSearch');
        // Route::get('assign-routine-search', 'SmAcademicsController@classRoutine');
        // Route::post('assign-routine-store', 'SmAcademicsController@assignRoutineStore');
        // Route::post('class-routine-report-search', 'SmAcademicsController@classRoutineReportSearch');
        // Route::get('class-routine-report-search', 'SmAcademicsController@classRoutineReportSearch');

        // class routine new

        Route::get('class-routine-new', ['as' => 'class_routine_new', 'uses' => 'SmClassRoutineNewController@classRoutine']);

        Route::post('class-routine-new', 'SmClassRoutineNewController@classRoutineSearch');
        Route::get('add-new-routine/{class_time_id}/{day}/{class_id}/{section_id}', 'SmClassRoutineNewController@addNewClassRoutine');

        Route::post('add-new-class-routine-store', 'SmClassRoutineNewController@addNewClassRoutineStore');

        Route::get('get-class-teacher-ajax', 'SmClassRoutineNewController@getClassTeacherAjax');
        Route::get('add-new-class-routine-store', 'SmClassRoutineNewController@classRoutineSearch');

        Route::get('edit-class-routine/{class_time_id}/{day}/{class_id}/{section_id}/{subject_id}/{room_id}/{assigned_id}/{teacher_id}', 'SmClassRoutineNewController@addNewClassRoutineEdit');

        Route::get('delete-class-routine-modal/{id}', 'SmClassRoutineNewController@deleteClassRoutineModal');
        Route::get('delete-class-routine/{id}', 'SmClassRoutineNewController@deleteClassRoutine');
        Route::get('class-routine-new/{class_id}/{section_id}', 'SmClassRoutineNewController@classRoutineRedirect');

        //Student Panel

        Route::get('view-teacher-routine', 'teacher\SmAcademicsController@viewTeacherRoutine');

        //assign subject
        Route::get('assign-subject', ['as' => 'assign_subject', 'uses' => 'SmAcademicsController@assignSubject']);

        Route::get('assign-subject-create', ['as' => 'assign_subject_create', 'uses' => 'SmAcademicsController@assigSubjectCreate']);

        Route::post('assign-subject-search', ['as' => 'assign_subject_search', 'uses' => 'SmAcademicsController@assignSubjectSearch']);
        Route::get('assign-subject-search', 'SmAcademicsController@assigSubjectCreate');
        Route::post('assign-subject-store', 'SmAcademicsController@assignSubjectStore');
        Route::get('assign-subject-store', 'SmAcademicsController@assigSubjectCreate');
        Route::post('assign-subject', 'SmAcademicsController@assignSubjectFind');
        Route::get('assign-subject-get-by-ajax', 'SmAcademicsController@assignSubjectAjax');

        //Assign Class Teacher
        Route::resource('assign-class-teacher', 'SmAssignClassTeacherControler');
        // Class room
        Route::resource('class-room', 'SmClassRoomController');
        Route::resource('class-time', 'SmClassTimeController');

        //Admission Query
        Route::get('admin-add-lead', 'SmAdmissionQueryController@index');
        Route::get('admission-query', ['as' => 'admission_query', 'uses' => 'SmAdmissionQueryController@index']);

        Route::post('admission-query-store', ['as' => 'admission_query_store', 'uses' => 'SmAdmissionQueryController@admissionQueryStore']);
        Route::get('admission-query-edit/{id}', ['as' => 'admission_query_edit', 'uses' => 'SmAdmissionQueryController@admissionQueryEdit']);
        Route::post('admission-query-update', ['as' => 'admission_query_update', 'uses' => 'SmAdmissionQueryController@admissionQueryUpdate']);
        Route::get('add-query/{id}', ['as' => 'add_query', 'uses' => 'SmAdmissionQueryController@addQuery']);
        Route::post('query-followup-store', ['as' => 'query_followup_store', 'uses' => 'SmAdmissionQueryController@queryFollowupStore']);
        Route::get('delete-follow-up/{id}', ['as' => 'delete_follow_up', 'uses' => 'SmAdmissionQueryController@deleteFollowUp']);
        Route::post('admission-query-delete', ['as' => 'admission_query_delete', 'uses' => 'SmAdmissionQueryController@admissionQueryDelete']);

        Route::post('admission-query-search', 'SmAdmissionQueryController@admissionQuerySearch');
        Route::get('admission-query-search', 'SmAdmissionQueryController@index');

        // Visitor routes

        Route::get('visitor', ['as' => 'visitor', 'uses' => 'SmVisitorController@index']);
        Route::post('visitor-store', ['as' => 'visitor_store', 'uses' => 'SmVisitorController@store']);
        Route::get('visitor-edit/{id}', ['as' => 'visitor_edit', 'uses' => 'SmVisitorController@edit']);
        Route::post('visitor-update', ['as' => 'visitor_update', 'uses' => 'SmVisitorController@update']);
        Route::get('visitor-delete/{id}', ['as' => 'visitor_delete', 'uses' => 'SmVisitorController@delete']);

        Route::get('download-visitor-document/{file_name}', function ($file_name = null) {

            $file = public_path() . '/uploads/visitor/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            }
        });

        // Fees Group routes
        Route::get('fees-group', ['as' => 'fees_group', 'uses' => 'SmFeesGroupController@index']);
        Route::post('fees-group-store', ['as' => 'fees_group_store', 'uses' => 'SmFeesGroupController@store']);
        Route::get('fees-group-edit/{id}', ['as' => 'fees_group_edit', 'uses' => 'SmFeesGroupController@edit']);
        Route::post('fees-group-update', ['as' => 'fees_group_update', 'uses' => 'SmFeesGroupController@update']);
        Route::post('fees-group-delete', ['as' => 'fees_group_delete', 'uses' => 'SmFeesGroupController@deleteGroup']);

        // Fees type routes
        Route::get('fees-type', ['as' => 'fees_type', 'uses' => 'SmFeesTypeController@index']);
        Route::post('fees-type-store', ['as' => 'fees_type_store', 'uses' => 'SmFeesTypeController@store']);
        Route::get('fees-type-edit/{id}', ['as' => 'fees_type_edit', 'uses' => 'SmFeesTypeController@edit']);
        Route::post('fees-type-update', ['as' => 'fees_type_update', 'uses' => 'SmFeesTypeController@update']);
        Route::get('fees-type-delete/{id}', ['as' => 'fees_type_delete', 'uses' => 'SmFeesTypeController@delete']);

        // Fees Discount routes
        Route::get('fees-discount', ['as' => 'fees_discount', 'uses' => 'SmFeesDiscountController@index']);
        Route::post('fees-discount-store', ['as' => 'fees_discount_store', 'uses' => 'SmFeesDiscountController@store']);
        Route::get('fees-discount-edit/{id}', ['as' => 'fees_discount_edit', 'uses' => 'SmFeesDiscountController@edit']);
        Route::post('fees-discount-update', ['as' => 'fees_discount_update', 'uses' => 'SmFeesDiscountController@update']);
        Route::get('fees-discount-delete/{id}', ['as' => 'fees_discount_delete', 'uses' => 'SmFeesDiscountController@delete']);
        Route::get('fees-discount-assign/{id}', ['as' => 'fees_discount_assign', 'uses' => 'SmFeesDiscountController@feesDiscountAssign']);
        Route::post('fees-discount-assign-search', 'SmFeesDiscountController@feesDiscountAssignSearch');
        Route::get('fees-discount-assign-store', 'SmFeesDiscountController@feesDiscountAssignStore');

        Route::get('fees-generate-modal/{amount}/{student_id}/{type}', 'SmFeesController@feesGenerateModal');
        Route::get('fees-discount-amount-search', 'SmFeesDiscountController@feesDiscountAmountSearch');
        // delete fees payment
        Route::post('fees-payment-delete', 'SmFeesController@feesPaymentDelete');

        // Fees carry forward
        Route::get('fees-forward', ['as' => 'fees_forward', 'uses' => 'SmFeesController@feesForward']);
        Route::post('fees-forward-search', 'SmFeesController@feesForwardSearch');
        Route::get('fees-forward-search', 'SmFeesController@feesForward');

        Route::post('fees-forward-store', 'SmFeesController@feesForwardStore');
        Route::get('fees-forward-store', 'SmFeesController@feesForward');

        //fees payment store
        Route::post('fees-payment-store', 'SmFeesController@feesPaymentStore');

        // Collect Fees
        Route::get('collect-fees', ['as' => 'collect_fees', 'uses' => 'SmFeesController@collectFees']);
        Route::get('fees-collect-student-wise/{id}', ['as' => 'fees_collect_student_wise', 'uses' => 'SmFeesController@collectFeesStudent']);

        Route::post('collect-fees', ['as' => 'collect_fees', 'uses' => 'SmFeesController@collectFeesSearch']);

        // fees print
        Route::get('fees-group-print/{id}', ['as' => 'fees_group_print', 'uses' => 'SmFeesController@feesGroupPrint']);
        Route::get('fees-payment-print/{id}/{group}', ['as' => 'fees_payment_print', 'uses' => 'SmFeesController@feesPaymentPrint']);

        Route::get('fees-groups-print/{id}/{s_id}', 'SmFeesController@feesGroupsPrint');

        //Search Fees Payment
        Route::get('search-fees-payment', ['as' => 'search_fees_payment', 'uses' => 'SmFeesController@searchFeesPayment']);
        Route::post('fees-payment-search', ['as' => 'fees_payment_search', 'uses' => 'SmFeesController@feesPaymentSearch']);
        Route::get('fees-payment-search', ['as' => 'fees_payment_search', 'uses' => 'SmFeesController@searchFeesPayment']);

        //Fees Search due
        Route::get('search-fees-due', ['as' => 'search_fees_due', 'uses' => 'SmFeesController@searchFeesDue']);
        Route::post('fees-due-search', ['as' => 'fees_due_search', 'uses' => 'SmFeesController@feesDueSearch']);
        Route::get('fees-due-search', ['as' => 'fees_due_search', 'uses' => 'SmFeesController@searchFeesDue']);

        //Fees Statement
        Route::get('fees-statement', ['as' => 'fees_statement', 'uses' => 'SmFeesController@feesStatemnt']);
        Route::post('fees-statement-search', ['as' => 'fees_statement_search', 'uses' => 'SmFeesController@feesStatementSearch']);

        // Balance fees report
        Route::get('balance-fees-report', ['as' => 'balance_fees_report', 'uses' => 'SmFeesController@balanceFeesReport']);
        Route::post('balance-fees-search', ['as' => 'balance_fees_search', 'uses' => 'SmFeesController@balanceFeesSearch']);
        Route::get('balance-fees-search', ['as' => 'balance_fees_search', 'uses' => 'SmFeesController@balanceFeesReport']);

        // Transaction Report
        Route::get('transaction-report', ['as' => 'transaction_report', 'uses' => 'SmFeesController@transactionReport']);
        Route::post('transaction-report-search', ['as' => 'transaction_report_search', 'uses' => 'SmFeesController@transactionReportSearch']);
        Route::get('transaction-report-search', ['as' => 'transaction_report_search', 'uses' => 'SmFeesController@transactionReport']);

        // cost center Report
        Route::get('cost-center-reports', 'SmFeesController@costCenterReports');
        Route::post('cost-center-report-search', 'SmFeesController@costCenterReportSearch');

        // Class Report
        Route::get('class-report', ['as' => 'class_report', 'uses' => 'SmAcademicsController@classReport']);
        Route::post('class-report', ['as' => 'class_report', 'uses' => 'SmAcademicsController@classReportSearch']);

        // merit list Report
        Route::get('merit-list-report', ['as' => 'merit_list_report', 'uses' => 'SmExaminationController@meritListReport']);
        Route::post('merit-list-report', ['as' => 'merit_list_report', 'uses' => 'SmExaminationController@meritListReportSearch']);

        //tabulation sheet report
        Route::get('reports-tabulation-sheet', ['as' => 'reports_tabulation_sheet', 'uses' => 'SmExaminationController@reportsTabulationSheet']);
        Route::post('reports-tabulation-sheet', ['as' => 'reports_tabulation_sheet', 'uses' => 'SmExaminationController@reportsTabulationSheetSearch']);

        // merit list Report
        Route::get('online-exam-report', ['as' => 'online_exam_report', 'uses' => 'SmOnlineExamController@onlineExamReport']);
        Route::post('online-exam-report', ['as' => 'online_exam_report', 'uses' => 'SmOnlineExamController@onlineExamReportSearch']);

        // class routine report

        Route::get('class-routine-report', ['as' => 'class_routine_report', 'uses' => 'SmClassRoutineNewController@classRoutineReport']);
        Route::post('class-routine-report', 'SmClassRoutineNewController@classRoutineReportSearch');

        // exam routine report
        Route::get('exam-routine-report', ['as' => 'exam_routine_report', 'uses' => 'SmExamRoutineController@examRoutineReport']);
        Route::post('exam-routine-report', ['as' => 'exam_routine_report', 'uses' => 'SmExamRoutineController@examRoutineReportSearch']);

        Route::get('teacher-class-routine-report', ['as' => 'teacher_class_routine_report', 'uses' => 'SmClassRoutineNewController@teacherClassRoutineReport']);
        Route::post('teacher-class-routine-report', 'SmClassRoutineNewController@teacherClassRoutineReportSearch');

        // mark sheet Report
        Route::get('mark-sheet-report', ['as' => 'mark_sheet_report', 'uses' => 'SmExaminationController@markSheetReport']);
        Route::post('mark-sheet-report', ['as' => 'mark_sheet_report', 'uses' => 'SmExaminationController@markSheetReportSearch']);

        //mark sheet report student
        Route::get('mark-sheet-report-student', ['as' => 'mark_sheet_report_student', 'uses' => 'SmExaminationController@markSheetReportStudent']);
        Route::post('mark-sheet-report-student', ['as' => 'mark_sheet_report_student', 'uses' => 'SmExaminationController@markSheetReportStudentSearch']);

        //user log
        Route::get('student-fine-report', ['as' => 'student_fine_report', 'uses' => 'SmFeesController@studentFineReport']);
        Route::post('student-fine-report', ['as' => 'student_fine_report', 'uses' => 'SmFeesController@studentFineReportSearch']);

        //user log
        Route::get('user-log', ['as' => 'user_log', 'uses' => 'UserController@userLog']);
        Route::get('user-activity-log', 'UserController@userActivitiyLog');

        // income head routes
        Route::get('income-head', ['as' => 'income_head', 'uses' => 'SmIncomeHeadController@index']);
        Route::post('income-head-store', ['as' => 'income_head_store', 'uses' => 'SmIncomeHeadController@store']);
        Route::get('income-head-edit/{id}', ['as' => 'income_head_edit', 'uses' => 'SmIncomeHeadController@edit']);
        Route::post('income-head-update', ['as' => 'income_head_update', 'uses' => 'SmIncomeHeadController@update']);
        Route::get('income-head-delete/{id}', ['as' => 'income_head_delete', 'uses' => 'SmIncomeHeadController@delete']);

        // Search account
        Route::get('search-account', ['as' => 'search_account', 'uses' => 'SmAccountsController@searchAccount']);
        Route::post('search-account', ['as' => 'search_account', 'uses' => 'SmAccountsController@searchAccountReportByDate']);

        Route::get('daily-expense', 'SmAccountsController@dailyExpense');
        Route::post('daily-expense-store', 'SmAccountsController@dailyExpenseStore');
        Route::get('daily-expense-edit/{id}', 'SmAccountsController@dailyExpenseEdit');
        Route::post('daily-expense-update', 'SmAccountsController@dailyExpenseUpdate');
        Route::get('daily-expense-delete/{id}', 'SmAccountsController@dailyExpenseDelete');


        Route::get('download-expense/{file_name}', function ($file_name = null) {
            $file = public_path() . '/uploads/addExpense/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            }
        });

        Route::get('get-sub-head', 'SmAccountsController@getSubHead');

        // // Search Expense
        // Route::get('search-expense', ['as' => 'search_expense', 'uses' => 'SmAccountsController@searchExpense']);
        // Route::post('search-expense-report-by-date', ['as' => 'search_expense_report_by_date', 'uses' => 'SmAccountsController@searchExpenseReportByDate']);
        // Route::get('search-expense-report-by-date', ['as' => 'search_expense_report_by_date', 'uses' => 'SmAccountsController@searchExpense']);
        // Route::post('search-expense-report-by-income', ['as' => 'search_expense_report_by_income', 'uses' => 'SmAccountsController@searchExpenseReportByIncome']);

        // add income routes
        Route::get('add-income', ['as' => 'add_income', 'uses' => 'SmAddIncomeController@index']);
        Route::post('add-income-store', ['as' => 'add_income_store', 'uses' => 'SmAddIncomeController@store']);
        Route::get('add-income-edit/{id}', ['as' => 'add_income_edit', 'uses' => 'SmAddIncomeController@edit']);
        Route::post('add-income-update', ['as' => 'add_income_update', 'uses' => 'SmAddIncomeController@update']);
        Route::post('add-income-delete', ['as' => 'add_income_delete', 'uses' => 'SmAddIncomeController@delete']);

        Route::get('download-income/{file_name}', function ($file_name = null) {
            $file = public_path() . '/uploads/add_income/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            }
        });

        // Profit of account
        Route::get('profit', ['as' => 'profit', 'uses' => 'SmAccountsController@profit']);
        Route::post('search-profit-by-date', ['as' => 'search_profit_by_date', 'uses' => 'SmAccountsController@searchProfitByDate']);
        Route::get('search-profit-by-date', ['as' => 'search_profit_by_date', 'uses' => 'SmAccountsController@profit']);

        // debit credit voucher

        Route::get('debit-credit-voucher', 'SmAccountsController@debitCreditVoucher');
        Route::post('debit-credit-voucher/store', 'SmAccountsController@debitCreditVoucherStore');
        Route::get('debit-credit-voucher/edit/{id}', 'SmAccountsController@debitCreditVoucherEdit');
        Route::post('debit-credit-voucher/update', 'SmAccountsController@debitCreditVoucherUpdate');
        Route::get('debit-credit-voucher/delete/{id}', 'SmAccountsController@debitCreditVoucherDelete');
        Route::get('debit-credit-voucher/view/{id}', 'SmAccountsController@debitCreditVoucherView');

        // Student Group Routes

        Route::get('payment-method', ['as' => 'payment_method', 'uses' => 'SmPaymentMethodController@index']);
        Route::post('payment-method-store', ['as' => 'payment_method_store', 'uses' => 'SmPaymentMethodController@store']);
        Route::get('payment-method-edit/{id}', ['as' => 'payment_method_edit', 'uses' => 'SmPaymentMethodController@edit']);
        Route::post('payment-method-update', ['as' => 'payment_method_update', 'uses' => 'SmPaymentMethodController@update']);
        Route::get('payment-method-delete/{id}', ['as' => 'payment_method_delete', 'uses' => 'SmPaymentMethodController@delete']);

        //academic year
        Route::resource('academic-year', 'SmAcademicYearController');

        //Session
        Route::resource('session', 'SmSessionController');

        Route::resource('daily-quotes', 'SysDailyQuotesController');

        Route::resource('department', 'SmHumanDepartmentController');
        // Staff Hourly rate
        Route::resource('hourly-rate', 'SmHourlyRateController');
        // Staff leave type
        Route::resource('leave-type', 'SmLeaveTypeController');
        // Staff leave define
        Route::resource('leave-define', 'SmLeaveDefineController');
        // Staff leave define
        Route::resource('apply-leave', 'SmLeaveRequestController');
        // Staff designation
        Route::resource('designation', 'SmDesignationController');
        // Payment Terms
        Route::resource('payment-terms', 'SysPaymentTermsController');
        // Currency Settings
        Route::resource('currency-settings', 'SysCurrencySettingsController');

        Route::post('view-currency-rate', 'SysCurrencySettingsController@view_currency_rate');
        Route::post('delete-currency-rate', 'SysCurrencySettingsController@delete_currency_rate');
        Route::post('add-currency-rate', 'SysCurrencySettingsController@add_currency_rate');
        Route::post('update-currency-rate', 'SysCurrencySettingsController@update_currency_rate');

        // Brands
        Route::resource('brand', 'SysBrandController');
        // Modules
        Route::resource('module', 'SysModuleController');
        // Modules Pages
        Route::resource('module-pages', 'SysModulePagesController');

        Route::resource('approve-leave', 'SmApproveLeaveController');
        Route::post('update-approve-leave', 'SmApproveLeaveController@updateApproveLeave');

        Route::get('/staffNameByRole', 'SmApproveLeaveController@staffNameByRole');
        Route::get('/staffNameByRoleDev', 'SmApproveLeaveController@staffNameByRoleDev');

        Route::get('view-leave-details-approve/{id}', 'SmApproveLeaveController@viewLeaveDetails');
        Route::get('view-leave-details-apply/{id}', 'SmLeaveRequestController@viewLeaveDetails');


        Route::get('cash-issue', 'SmCashIssueController@index');
        Route::post('cash-issue-store', 'SmCashIssueController@store');

        Route::get('return-cash-view/{id}', 'SmCashIssueController@returnCashView');
        Route::get('return-cash/{id}', 'SmCashIssueController@returnCash');

        // Bank Account
        Route::resource('bank-account', 'SmBankAccountController');
        Route::get('bank-ledger', 'SmBankAccountController@bankLedger');
        Route::post('bank-ledger', 'SmBankAccountController@bankLedgerSearch');


        Route::get('petty-cash-view/{id}', 'SmBankAccountController@pettyCashView');

        Route::get('ajax-get-bank-balance', 'SmAddExpenseController@ajaxGetBankBalance');

        // Expense head
        Route::resource('expense-head', 'SmExpenseHeadController');

        // Chart Of Account
        Route::resource('chart-of-account', 'SmChartOfAccountController');

        /***************************************** Route cost center ***************************/
        Route::get('manage-unit', 'SmUnitManageController@index');
        Route::post('manage-unit-modified', 'SmUnitManageController@manageUnitModified');
        Route::get('manage-unit-edit/{id}', 'SmUnitManageController@manageUnitEdit');
        Route::get('manage-unit-delete/{id}', 'SmUnitManageController@unit_destroy');

        Route::get('manage-brand', 'SmUnitManageController@ManageBrand');
        Route::post('manage-brand-modified', 'SmUnitManageController@manageBrandModified');
        Route::get('manage-brand-edit/{id}', 'SmUnitManageController@manageBrandEdit');
        Route::get('manage-brand-delete/{id}', 'SmUnitManageController@brand_destroy');

        /***************************************** Route cost center ***************************/

        /***************************************** Route cost center ***************************/
        Route::get('sub-account', 'SmChartOfAccountController@subAccount');
        Route::post('sub-account-store', 'SmChartOfAccountController@subAccountStore');
        Route::get('sub-account-edit/{id}', 'SmChartOfAccountController@subAccountEdit');
        Route::post('sub-account-update', 'SmChartOfAccountController@subAccountUpdate');
        Route::get('sub-account-delete/{id}', 'SmChartOfAccountController@subAccountDelete');
        /***************************************** End cost center ***************************/

        /***************************************** Route cost center ***************************/
        Route::get('cost-center', 'SmChartOfAccountController@costCenter');
        Route::post('cost-center-store', 'SmChartOfAccountController@costCenterStore');
        Route::get('cost-center-edit/{id}', 'SmChartOfAccountController@costCenterEdit');
        Route::post('cost-center-update', 'SmChartOfAccountController@costCenterUpdate');
        Route::get('cost-center-delete/{id}', 'SmChartOfAccountController@costCenterDelete');
        /***************************************** End cost center ***************************/

        /***************************************** Route customer ***************************/
        //Route::get('customers', 'SysCustomerController@customer');
        Route::get('customers/{id?}', 'SysCustomerController@customer');
        Route::get('customer-details/{id}', 'SysCustomerController@getCustomerDetails');

        //Route::post('customers', 'SysCustomerController@customer');
        Route::get('customer', 'SysCustomerController@customerlist');

        Route::post('autocomplete/customer_name', 'SysCustomerController@customer_name')->name('autocomplete.customer_name');
        Route::post('autocomplete/account_name', 'SysChartofAccountsController@account_name')->name('autocomplete.account_name');

        Route::get('autocomplete/get_account_list_ajax', 'SysChartofAccountsController@get_account_list_ajax')->name('autocomplete.get_account_list_ajax');
        Route::get('autocomplete/get_cust_account_list_ajax', 'SysChartofAccountsController@get_cust_account_list_ajax')->name('autocomplete.get_cust_account_list_ajax');
        Route::get('autocomplete/get_supp_account_list_ajax', 'SysChartofAccountsController@get_supp_account_list_ajax')->name('autocomplete.get_supp_account_list_ajax');

        Route::get('customers-pending', 'SysCustomerController@customer_pending');

        Route::get('add-customer-detail-popup', 'SysCustomerController@add_customer_detail_popup');

        Route::get('autocomplete/get_cust_sup_list_ajax', 'SysCustomerController@get_cust_sup_list_ajax')->name('autocomplete.get_cust_sup_list_ajax');
        Route::get('add-customer', 'SysCustomerController@addCustomer');
        Route::post('customer-store', 'SysCustomerController@addCustomerStore');
        Route::get('customer-edit/{id}', 'SysCustomerController@customerEdit');
        Route::get('customer-inactive/{id}', 'SysCustomerController@customerInactive');
        Route::get('customer-restore/{id}', 'SysCustomerController@customerRestore');
        Route::post('customer-update', 'SysCustomerController@customerUpdate');

        Route::post('customer-merge', 'SysCustomerController@customerMerge');
        Route::post('customer-merge-duplicate', 'SysCustomerController@customerMergeDuplicate');
        //Route::get('customer-delete/{id}', 'SysCustomerController@customerDelete');

        Route::get('view-customer/{id}', 'SysCustomerController@viewCustomer');
        Route::post('add-customer-script', 'SysCustomerController@add_customer_script');
        Route::post('delete-customer-script', 'SysCustomerController@delete_customer_script');

        Route::get('customer-import', 'SysCustomerController@customer_import');
        Route::post('customer-import-list', 'SysCustomerController@customer_import_list');
        Route::post('customer-import-data', 'SysCustomerController@customer_import_data');
        Route::get('customer-import-clear', 'SysCustomerController@customer_import_clear');

        Route::get('delete-cust-suppl-doc/{id}', 'SysCustomerController@delete_cust_suppl_doc');
        Route::get('delete-cust-suppl-address/{id}', 'SysCustomerController@delete_cust_suppl_address');
        Route::post('add-cust-suppl-address', 'SysCustomerController@add_cust_suppl_address');
        Route::post('update-cust-suppl-address', 'SysCustomerController@update_cust_suppl_address');


        Route::post('autocomplete/supplier_name', 'SysSupplierController@supplier_name')->name('autocomplete.supplier_name');

        Route::get('suppliers', 'SysSupplierController@suppliers');
        Route::get('suppliers/search', 'SysSupplierController@search')->name('suppliers.search');
        Route::get('supplier-from-list/{id?}', 'SysSupplierController@supplier_from_list');

        Route::get('add-supplier', 'SysSupplierController@addSupplier');
        Route::post('supplier-store', 'SysSupplierController@addSupplierStore');
        Route::get('supplier-edit/{id}', 'SysSupplierController@supplierEdit');
        Route::get('supplier-inactive/{id}', 'SysSupplierController@supplierInactive');
        Route::get('supplier-restore/{id}', 'SysSupplierController@supplierRestore');
        Route::post('supplier-update', 'SysSupplierController@supplierUpdate');
        Route::get('supplier-delete/{id}', 'SysSupplierController@supplierDelete');
        Route::post('supplier-merge', 'SysSupplierController@supplierMerge');
        Route::post('supplier-merge-duplicate', 'SysSupplierController@supplierMergeDuplicate');

        Route::get('view-supplier/{id}', 'SysSupplierController@viewSupplier');
        Route::post('add-supplier-script', 'SysSupplierController@add_supplier_script');
        Route::post('delete-supplier-script', 'SysSupplierController@delete_supplier_script');

        Route::get('supplier-import', 'SysSupplierController@supplier_import');
        Route::post('supplier-import-list', 'SysSupplierController@supplier_import_list');
        Route::post('supplier-import-data', 'SysSupplierController@supplier_import_data');
        Route::get('supplier-import-clear', 'SysSupplierController@supplier_import_clear');

        Route::get('suppliers/{id?}', 'SysSupplierController@suppliers');
        Route::get('supplier-details/{id}', 'SysSupplierController@getSupplierDetails');


        Route::get('customer-from-list', 'SysCustomerController@customer_from_list');
        Route::get('customer-form-edit/{id}', 'SysCustomerController@customer_form_edit');
        Route::post('update-customer-form-address', 'SysCustomerController@customer_form_address_update');

        Route::post('customer-form-approve', 'SysCustomerController@customer_form_approve');
        Route::get('customer-form-delete/{id}', 'SysCustomerController@customer_form_delete');
        Route::get('customer-form-details/{cust_id}/merge/{sub_id}', 'SysCustomerController@customer_form_merge');

        Route::get('supplier-from-list', 'SysSupplierController@supplier_from_list');
        Route::get('supplier-form-edit/{id}', 'SysSupplierController@supplier_form_edit');
        Route::post('supplier-form-approve', 'SysSupplierController@supplier_form_approve');
        Route::get('supplier-form-delete/{id}', 'SysSupplierController@supplier_form_delete');
        Route::get('supplier-form-details/{supl_id}/merge/{sub_id}', 'SysSupplierController@supplier_form_merge');


        /***************************************** End customer ***************************/

        // Add Expense
        Route::resource('add-expense', 'SmAddExpenseController');

        Route::get('ajax-get-bank-balance', 'SmAddExpenseController@ajaxGetBankBalance');

        // Fees Master
        Route::resource('fees-master', 'SmFeesMasterController');
        Route::post('fees-master-single-delete', 'SmFeesMasterController@deleteSingle');
        Route::post('fees-master-group-delete', 'SmFeesMasterController@deleteGroup');
        Route::get('fees-assign/{id}', ['as' => 'fees_assign', 'uses' => 'SmFeesMasterController@feesAssign']);
        Route::post('fees-assign-search', 'SmFeesMasterController@feesAssignSearch');

        Route::get('btn-assign-fees-group', 'SmFeesMasterController@feesAssignStore');

        // Complaint
        Route::resource('complaint', 'SmComplaintController');
        Route::get('download-complaint-document/{file_name}', function ($file_name = null) {
            $file = public_path() . '/uploads/complaint/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            }
        });

        // Complaint
        Route::resource('admin-lead-setup', 'SmSetupAdminController');
        Route::resource('setup-admin', 'SmSetupAdminController');
        Route::get('setup-admin-delete/{id}', 'SmSetupAdminController@destroy');

        // Postal Receive
        Route::resource('postal-receive', 'SmPostalReceiveController');
        Route::get('postal-receive-document/{file_name}', function ($file_name = null) {
            $file = public_path() . '/uploads/postal/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            }
        });

        // Postal Dispatch
        Route::resource('postal-dispatch', 'SmPostalDispatchController');
        Route::get('postal-dispatch-document/{file_name}', function ($file_name = null) {

            $file = public_path() . '/uploads/postal/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            } else {
                redirect()->back();
            }
        });

        // Phone Call Log
        Route::resource('phone-call', 'SmPhoneCallLogController');

        // Student Module /Student Admission
        Route::get('student-admission', ['as' => 'student_admission', 'uses' => 'SmStudentAdmissionController@admission']);


        //Student details document
        Route::post('upload-document', ['as' => 'upload_document', 'uses' => 'SmStudentAdmissionController@uploadDocument']);

        Route::get('download-document/{file_name}', function ($file_name = null) {
            $file = public_path() . '/uploads/student/document/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            }
        });

        Route::get('delete-document/{id}', ['as' => 'delete_document', 'uses' => 'SmStudentAdmissionController@deleteDocument']);

        // Student timeline upload
        Route::post('student-timeline-store', ['as' => 'student_timeline_store', 'uses' => 'SmStudentAdmissionController@studentTimelineStore']);
        Route::get('download-timeline-doc/{file_name}', function ($file_name = null) {
            $file = public_path() . '/uploads/student/timeline/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            }
        });


        // staff directory
        Route::get('staff-directory', ['as' => 'staff_directory', 'uses' => 'SmStaffController@staffList']);
        Route::get('staff-auth', 'SmStaffController@staffAuth');
        Route::get('staff-auth-approve/{id}', ['as' => 'staff-auth-approve', 'uses' => 'SmStaffController@staffAuthApprove']);

        Route::get('search-staff', 'SmStaffController@staffList');

        Route::post('search-staff', ['as' => 'searchStaff', 'uses' => 'SmStaffController@searchStaff']);

        Route::get('add-staff', ['as' => 'addStaff', 'uses' => 'SmStaffController@addStaff']);

        //adil
        Route::post('staff-store', ['as' => 'staffStore', 'uses' => 'SmStaffController@staffStore'])->name('staff.basic.store');
        Route::post('/hrms/staff/experience/store', 'SmStaffController@storeExperience')->name('staff.experience.store');
        Route::post('/hrms/staff/docs/store', 'SmStaffController@storeDocs')->name('staff.docs.store');
        Route::get('/hrms/staff/docs/peek', 'SmStaffController@docsPeek')->name('staff.docs.peek');
        Route::post('/hrms/staff/basic/store', 'SmStaffController@storeBasic')->name('staff.basic.store');
        Route::post('/hrms/staff/job/store', 'SmStaffController@storeJob')->name('staff.job.store');
        Route::post('/hrms/staff/bank/store', 'SmStaffController@storeBank')->name('staff.bank.store');
        Route::post('/hrms/staff/education/store', 'SmStaffController@storeEducation')->name('staff.education.store');
        Route::get('/hrms/staff/{id}/edit', 'SmStaffController@edit')->name('staff.edit');
        Route::get('staff-details/{id}', 'SmStaffController@details')->name('staff.details');

        Route::get('edit-staff/{id}', ['as' => 'editStaff', 'uses' => 'SmStaffController@editStaff']);
        Route::post('update-staff', ['as' => 'staffUpdate', 'uses' => 'SmStaffController@staffUpdate']);

        Route::get('view-staff/{id}', ['as' => 'viewStaff', 'uses' => 'SmStaffController@viewStaff']);
        Route::get('delete-staff-view/{id}', ['as' => 'deleteStaffView', 'uses' => 'SmStaffController@deleteStaffView']);

        Route::get('deleteStaff/{id}', 'SmStaffController@deleteStaff');
        Route::get('delete/customer/{id}', 'SysCustomerController@deleteSCustomer');

        // staff directory
        Route::get('get-salesperson-list', 'SmStaffController@getsalespersonlist');

        Route::get('login-access-permission', 'SmStaffController@loginAccessPermission');

        Route::get('tender-work-order-status', 'SmTenderController@tenderWorkOrderStatus');





        Route::get('upload-staff-documents/{id}', 'SmStaffController@uploadStaffDocuments');
        Route::post('save_upload_document', 'SmStaffController@saveUploadDocument');
        Route::get('download-staff-document/{file_name}', function ($file_name = null) {
            $file = public_path() . '/uploads/staff/document/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            }
        });

        Route::get('download-staff-joining-letter/{file_name}', function ($file_name = null) {
            $file = public_path() . '/uploads/staff_joining_letter/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            }
        });

        Route::get('download-resume/{file_name}', function ($file_name = null) {
            $file = public_path() . '/uploads/resume/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            }
        });

        Route::get('download-other-document/{file_name}', function ($file_name = null) {
            $file = public_path() . '/uploads/others_documents/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            }
        });

        Route::get('download-staff-timeline-doc/{file_name}', function ($file_name = null) {
            $file = public_path() . '/uploads/staff/timeline/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            }
        });

        Route::get('delete-staff-document-view/{id}', 'SmStaffController@deleteStaffDocumentView');
        Route::get('delete-staff-document/{id}', 'SmStaffController@deleteStaffDocument');

        // staff timeline
        Route::get('add-staff-timeline/{id}', 'SmStaffController@addStaffTimeline');
        Route::post('staff_timeline_store', 'SmStaffController@storeStaffTimeline');
        Route::get('delete-staff-timeline-view/{id}', 'SmStaffController@deleteStaffTimelineView');
        Route::get('delete-staff-timeline/{id}', 'SmStaffController@deleteStaffTimeline');

        //Staff Attendance
        Route::get('staff-attendance', ['as' => 'staff_attendance', 'uses' => 'SmStaffAttendanceController@staffAttendance']);
        Route::post('staff-attendance-search', 'SmStaffAttendanceController@staffAttendanceSearch');
        Route::post('staff-attendance-store', 'SmStaffAttendanceController@staffAttendanceStore');

        Route::get('staff-attendance-report', ['as' => 'staff_attendance_report', 'uses' => 'SmStaffAttendanceController@staffAttendanceReport']);
        Route::post('staff-attendance-report-search', ['as' => 'staff_attendance_report_search', 'uses' => 'SmStaffAttendanceController@staffAttendanceReportSearch']);

        //Company









        //adil 

        Route::post('/company/basic/store',       'SysCompanyController@storeBasic')->name('company.basic.store');
        Route::post('/company/contact/store',     'SysCompanyController@storeContact')->name('company.contact.store');
        Route::post('/company/compliance/store',  'SysCompanyController@storeCompliance')->name('company.compliance.store');
        Route::post('/company/banking/store',     'SysCompanyController@storeBanking')->name('company.banking.store');
        Route::post('/company/hrpayroll/store',   'SysCompanyController@storeHRPayroll')->name('company.hrpayroll.store');
        Route::post('/company/hr-policy/store', 'SysCompanyController@storeHrPolicies')->name('company.hrpolicy.store');
        Route::post('/company/docs/store', 'SysCompanyController@storeDocuments')->name('company.docs.store');
        Route::get('get-cities/{country}', 'LocationController@getCities')->name('get.cities');
        Route::get('company', ['as' => 'company', 'uses' => 'SysCompanyController@companyList']);
        Route::get('company-details/{id}', 'SysCompanyController@details')->name('company.details');
        Route::get('company-add', ['as' => 'company-add', 'uses' => 'SysCompanyController@companyAdd']);
        Route::get('/company-edit/{id}', 'SysCompanyController@edit')->name('company.edit');
        Route::post('company-store', ['as' => 'company-store', 'uses' => 'SysCompanyController@store']);
        Route::get('company/{id}/edit', ['as' => 'edit', 'uses' => 'SysCompanyController@edit']);
        Route::put('company-update/{id}', ['as' => 'company-update', 'uses' => 'SysCompanyController@update']);
        Route::get('delete-company/{id}', ['as' => 'delete-company', 'uses' => 'SysCompanyController@delete']);
        Route::get('vuetest', ['as' => 'company-add', 'uses' => 'SysCompanyController@companyAdd']);
        Route::post('/user-save', 'sysCompanyController@savenew')->name('user.save');
        Route::post('set-company-id', 'SysCompanyController@set_company_id');

        Route::group(['prefix' => 'employee', 'middleware' => ['auth']], function () {
            // List + Create + Store
            Route::get('leaves',              'LeaveController@index')->name('employee.leaves.index');
            Route::get('leaves/create',       'LeaveController@create')->name('employee.leaves.create');
            Route::post('leaves',             'LeaveController@store')->name('employee.leaves.store');


            // Edit + Update (PUT/PATCH)
            Route::get('leaves/{leave}/edit', 'LeaveController@edit')->name('employee.leaves.edit');
            Route::match(['put', 'patch'], 'leaves/{leave}', 'LeaveController@update')->name('employee.leaves.update');


            // Show (keep after edit to avoid conflicts)
            Route::get('leaves/{leave}',      'LeaveController@show')->name('employee.leaves.show');
        });

        // Approval Routes

        // routes/web.php
        Route::get('approvals/inbox', 'ApprovalController@index')->name('approvals.inbox');
        Route::get('approvals/inbox/{id}', 'ApprovalController@show')->name('approvals.show');
        Route::post('approvals/action', 'ApprovalController@action')->name('approvals.action');

        // policy
        Route::match(['get', 'post'], 'company/policy', 'SysCompanyController@policy')->name('policy');

        //adil end

        //Chart of Accounts
        Route::get('chartofaccounts', ['as' => 'chartofaccounts', 'uses' => 'SysChartofAccountsController@chartofaccountsList']);
        Route::post('chartofaccounts', ['as' => 'chartofaccounts', 'uses' => 'SysChartofAccountsController@chartofaccountsList']);

        Route::get('chartofaccounts-search', 'SysChartofAccountsController@search')->name('chartofaccounts.search');
        Route::get('/load-account-modal-data', 'SysChartofAccountsController@loadAccountModal');

        Route::get('chartofaccounts-add', ['as' => 'chartofaccounts-add', 'uses' => 'SysChartofAccountsController@chartofaccountsAdd']);
        Route::post('chartofaccounts-store', ['as' => 'chartofaccounts-store', 'uses' => 'SysChartofAccountsController@store']);
        Route::get('chartofaccounts/{id}/edit', ['as' => 'edit', 'uses' => 'SysChartofAccountsController@edit']);
        Route::get('chartofaccounts/{id}/move', ['as' => 'move', 'uses' => 'SysChartofAccountsController@move']);
        Route::get('chartofaccounts/{id}/delete', ['as' => 'delete', 'uses' => 'SysChartofAccountsController@delete']);
        Route::get('chartofaccounts/{id}/restore', ['as' => 'restore', 'uses' => 'SysChartofAccountsController@restore']);
        Route::put('chartofaccounts-update/{id}', ['as' => 'chartofaccounts-update', 'uses' => 'SysChartofAccountsController@update']);
        Route::get('delete-chartofaccounts/{id}', ['as' => 'delete-chartofaccounts', 'uses' => 'SysChartofAccountsController@delete']);
        Route::get('get_subgroup2', 'SysChartofAccountsController@get_subgroup2');
        Route::post('get-chartofaccounts-info', 'SysChartofAccountsController@getchartofaccountsinfo');

        Route::post('account-merge', 'SysChartofAccountsController@accountMerge');
        Route::post('sub-account-merge', 'SysChartofAccountsController@subAccountMerge');

        Route::post('chartofaccounts-maintosub', ['as' => 'move-maintosub', 'uses' => 'SysChartofAccountsController@move_maintosub']);

        Route::get('chartofaccounts-add-sub', ['as' => 'chartofaccounts-add-sub', 'uses' => 'SysChartofAccountsController@chartofaccounts_add_sub']);
        Route::post('chartofaccounts-store-sub', ['as' => 'chartofaccounts-store-sub', 'uses' => 'SysChartofAccountsController@store_sub']);
        Route::get('chartofaccounts-sub/{id}/edit', ['as' => 'edit-sub', 'uses' => 'SysChartofAccountsController@edit_sub']);
        Route::get('chartofaccounts-sub/{id}/move', ['as' => 'move-sub', 'uses' => 'SysChartofAccountsController@move_sub']);

        Route::post('chartofaccounts-employee-sub-store', ['as' => 'chartofaccounts-employee-sub-store', 'uses' => 'SysChartofAccountsController@store_sub_employee']);

        Route::put('chartofaccounts-sub-update/{id}', ['as' => 'chartofaccounts-update-sub', 'uses' => 'SysChartofAccountsController@update_sub']);
        Route::get('chartofaccounts-sub/{id}/delete', ['as' => 'delete-sub', 'uses' => 'SysChartofAccountsController@delete_sub']);
        Route::get('chartofaccounts-sub/{id}/restore', ['as' => 'restore-sub', 'uses' => 'SysChartofAccountsController@restore_sub']);

        Route::get('chartofaccounts-import', ['as' => 'chartofaccounts-import', 'uses' => 'SysChartofAccountsController@chartofaccounts_import']);
        Route::post('chartofaccounts-import-list', ['as' => 'chartofaccounts-import-list', 'uses' => 'SysChartofAccountsController@chartofaccounts_import_list']);
        Route::post('chartofaccounts-import-data', ['as' => 'chartofaccounts-import-data', 'uses' => 'SysChartofAccountsController@chartofaccounts_import_data']);
        Route::get('chartofaccounts-import-clear', ['as' => 'chartofaccounts-import-clear', 'uses' => 'SysChartofAccountsController@chartofaccounts_import_clear']);

        Route::get('chartofaccounts-import-sub', ['as' => 'chartofaccounts-import-sub', 'uses' => 'SysChartofAccountsController@chartofaccounts_import_sub']);
        Route::post('chartofaccounts-import-sub-list', ['as' => 'chartofaccounts-import-sub-list', 'uses' => 'SysChartofAccountsController@chartofaccounts_import_sub_list']);
        Route::post('chartofaccounts-import-sub-data', ['as' => 'chartofaccounts-import-sub-data', 'uses' => 'SysChartofAccountsController@chartofaccounts_import_sub_data']);
        Route::get('chartofaccounts-import-sub-clear', ['as' => 'chartofaccounts-import-sub-clear', 'uses' => 'SysChartofAccountsController@chartofaccounts_import_sub_clear']);

        Route::get('chartofaccounts-opening-balance', ['as' => 'chartofaccounts-opening-balance', 'uses' => 'SysChartofAccountsController@chartofaccounts_opening_balance']);
        Route::get('chartofaccounts-opening-balance-edit/{id}', ['as' => 'chartofaccounts-opening-balance-edit', 'uses' => 'SysChartofAccountsController@chartofaccounts_opening_balance_edit']);

        Route::get('chartofaccounts-import-invoice', ['as' => 'chartofaccounts-import', 'uses' => 'SysChartofAccountsController@chartofaccounts_import_invoice']);
        Route::post('chartofaccounts-import-invoice-list', ['as' => 'chartofaccounts-import-invoice-list', 'uses' => 'SysChartofAccountsController@chartofaccounts_import_invoice_list']);
        Route::post('chartofaccounts-import-invoice-data', ['as' => 'chartofaccounts-import-invoice-data', 'uses' => 'SysChartofAccountsController@chartofaccounts_import_invoice_data']);
        Route::get('chartofaccounts-import-invoice-clear', ['as' => 'chartofaccounts-import-invoice-clear', 'uses' => 'SysChartofAccountsController@chartofaccounts_import_invoice_clear']);

        Route::post('chartofaccounts-invoice-delete', ['as' => 'chartofaccounts-invoice-delete', 'uses' => 'SysChartofAccountsController@chartofaccounts_invoice_delete']);
        Route::post('chartofaccounts-invoice-update', ['as' => 'chartofaccounts-invoice-update', 'uses' => 'SysChartofAccountsController@chartofaccounts_invoice_update']);



        //Profit & Loss Account
        Route::get('profit-and-loss-account', 'SysProfitAndLossAccountController@index');
        Route::post('profit-and-loss-account', 'SysProfitAndLossAccountController@index');

        //Balancesheet
        Route::get('balancesheet', 'SysBalancesheetController@index');
        Route::post('balancesheet', 'SysBalancesheetController@index');

        //Cash Book
        Route::get('cashbook', 'SysCashbookController@index');
        Route::post('cashbook', 'SysCashbookController@index');

        //Bank Book
        Route::get('bankbook', 'SysBankbookController@index');
        Route::post('bankbook', 'SysBankbookController@index');


        //get_country get_state
        Route::get('get_country', 'SysCountryStateListController@get_country');
        Route::get('get_state', 'SysCountryStateListController@get_state');
        Route::get('get-state-with-vat', 'SysCountryStateListController@getstatewithvat');
        Route::get('get_vat_state', 'SysCountryStateListController@get_vat_state');
        Route::get('get_customer_vat', 'SysCountryStateListController@get_customer_vat');

        //General Ledger
        Route::get('generalledger', ['as' => 'generalledger', 'uses' => 'SysGeneralLedgerController@index']);
        Route::post('generalledger', ['as' => 'generalledger', 'uses' => 'SysGeneralLedgerController@index']);

        Route::get('generalledger-add', ['as' => 'generalledger-add', 'uses' => 'SysGeneralLedgerController@generalledgerAdd']);
        Route::post('generalledger-store', ['as' => 'generalledger-store', 'uses' => 'SysGeneralLedgerController@store']);
        Route::get('generalledger/{id}/edit', ['as' => 'edit', 'uses' => 'SysGeneralLedgerController@edit']);
        Route::put('generalledger-update/{id}', ['as' => 'generalledger-update', 'uses' => 'SysGeneralLedgerController@update']);
        Route::get('delete-generalledger/{id}', ['as' => 'delete-generalledger', 'uses' => 'SysGeneralLedgerController@delete']);
        Route::get('generalledger-search', 'SysGeneralLedgerController@search');


        Route::get('payables-outstanding', ['as' => 'payables-outstanding', 'uses' => 'SysPayablesOutstandingController@index']);
        Route::post('payables-outstanding', ['as' => 'payables-outstanding', 'uses' => 'SysPayablesOutstandingController@index']);
        Route::post('payables-outstanding-store-temp', 'SysPayablesOutstandingController@store_temp');
        Route::post('payables-outstanding-store-temp-delete', 'SysPayablesOutstandingController@store_temp_delete');
        Route::post('payables-outstanding-store-update', 'SysPayablesOutstandingController@store_update');
        Route::post('payables-outstanding-store-delete-before-update', 'SysPayablesOutstandingController@store_delete_before_update');
        Route::get('payables-outstanding-download/{account}/{date}', 'SysPayablesOutstandingController@download');

        Route::post('update-payable-pdc', 'SysPayablesOutstandingController@update_payable_pdc');

        Route::get('pi-adjustment-report', 'SysPIAdjustmentReportController@index');
        Route::post('pi-adjustment-report', 'SysPIAdjustmentReportController@index');

        Route::get('receivable-outstanding-summary', ['as' => 'receivable-outstanding-summary', 'uses' => 'SysReceivableOutstandingController@summary']);
        Route::get('receivable-outstanding', ['as' => 'receivable-outstanding', 'uses' => 'SysReceivableOutstandingController@index']);
        Route::post('receivable-outstanding', ['as' => 'receivable-outstanding', 'uses' => 'SysReceivableOutstandingController@index']);
        Route::post('receivable-outstanding-store-temp', 'SysReceivableOutstandingController@store_temp');
        Route::post('receivable-outstanding-store-temp-delete', 'SysReceivableOutstandingController@store_temp_delete');
        Route::post('receivable-outstanding-store-update', 'SysReceivableOutstandingController@store_update');
        //Route::post('receivable-outstanding-store-delete-before-update', 'SysReceivableOutstandingController@store_delete_before_update');
        Route::get('receivable-outstanding-download/{account}/{date}', 'SysReceivableOutstandingController@download');

        Route::post('update-receivable-pdc', 'SysReceivableOutstandingController@update_receivable_pdc');

        Route::get('si-adjustment-report', 'SysSIAdjustmentReportController@index');
        Route::post('si-adjustment-report', 'SysSIAdjustmentReportController@index');

        //Supplier Ledger
        Route::get('supplierledger', ['as' => 'supplierledger', 'uses' => 'SysSupplierLedgerController@index']);
        Route::post('supplierledger', ['as' => 'supplierledger', 'uses' => 'SysSupplierLedgerController@index']);

        Route::get('supplier-outstanding', ['as' => 'supplier-outstanding', 'uses' => 'SysSupplierLedgerController@supplieroutstanding']);
        Route::post('supplier-outstanding', ['as' => 'supplier-outstanding', 'uses' => 'SysSupplierLedgerController@supplieroutstanding']);

        Route::get('supplier-outstanding-pdc', ['as' => 'supplier-outstanding-pdc', 'uses' => 'SysSupplierLedgerController@supplieroutstandingpdc']);
        Route::post('supplier-outstanding-pdc', ['as' => 'supplier-outstanding-pdc', 'uses' => 'SysSupplierLedgerController@supplieroutstandingpdc']);

        Route::get('supplier-ageing', ['as' => 'supplier-ageing', 'uses' => 'SysSupplierLedgerController@supplierageing']);
        Route::post('supplier-ageing', ['as' => 'supplier-ageing', 'uses' => 'SysSupplierLedgerController@supplierageing']);

        // Route::get('supplierledger', ['as' => 'supplierledger', 'uses' => 'SysSupplierLedgerController@supplierledgerList']);
        // Route::get('supplierledger-add', ['as' => 'supplierledger-add', 'uses' => 'SysSupplierLedgerController@supplierledgerAdd']);
        // Route::post('supplierledger-store', ['as' => 'supplierledger-store', 'uses' => 'SysSupplierLedgerController@store']);
        // Route::get('supplierledger/{id}/edit', ['as' => 'edit', 'uses' => 'SysSupplierLedgerController@edit']);
        // Route::put('supplierledger-update/{id}', ['as' => 'supplierledger-update', 'uses' => 'SysSupplierLedgerController@update']);
        // Route::get('delete-supplierledger/{id}', ['as' => 'delete-supplierledger', 'uses' => 'SysSupplierLedgerController@delete']);        
        // Route::get('supplierledger-search', 'SysSupplierLedgerController@search');

        //Customer Ledger
        Route::get('customerledger', ['as' => 'customerledger', 'uses' => 'SysCustomerLedgerController@index']);
        Route::post('customerledger', ['as' => 'customerledger', 'uses' => 'SysCustomerLedgerController@index']);

        Route::get('customer-outstanding', ['as' => 'customer-outstanding', 'uses' => 'SysCustomerLedgerController@customeroutstanding']);
        Route::post('customer-outstanding', ['as' => 'customer-outstanding', 'uses' => 'SysCustomerLedgerController@customeroutstanding']);

        Route::get('customer-outstanding-pdc', ['as' => 'customer-outstanding-pdc', 'uses' => 'SysCustomerLedgerController@customeroutstandingpdc']);
        Route::post('customer-outstanding-pdc', ['as' => 'customer-outstanding-pdc', 'uses' => 'SysCustomerLedgerController@customeroutstandingpdc']);

        Route::get('customer-ageing', ['as' => 'customer-ageing', 'uses' => 'SysCustomerLedgerController@customerageing']);
        Route::post('customer-ageing', ['as' => 'customer-ageing', 'uses' => 'SysCustomerLedgerController@customerageing']);

        // Route::get('customerledger', ['as' => 'customerledger', 'uses' => 'SysCustomerLedgerController@customerledgerList']);
        // Route::get('customerledger-add', ['as' => 'customerledger-add', 'uses' => 'SysCustomerLedgerController@customerledgerAdd']);
        // Route::post('customerledger-store', ['as' => 'customerledger-store', 'uses' => 'SysCustomerLedgerController@store']);
        // Route::get('customerledger/{id}/edit', ['as' => 'edit', 'uses' => 'SysCustomerLedgerController@edit']);
        // Route::put('customerledger-update/{id}', ['as' => 'customerledger-update', 'uses' => 'SysCustomerLedgerController@update']);
        // Route::get('delete-customerledger/{id}', ['as' => 'delete-customerledger', 'uses' => 'SysCustomerLedgerController@delete']);        
        // Route::get('customerledger-search', 'SysCustomerLedgerController@search');

        //Trial Balance
        Route::get('trialbalance', ['as' => 'trialbalance', 'uses' => 'SysTrialBalanceController@trialbalanceList']);
        Route::get('trialbalance-add', ['as' => 'trialbalance-add', 'uses' => 'SysTrialBalanceController@trialbalanceAdd']);
        Route::post('trialbalance-store', ['as' => 'trialbalance-store', 'uses' => 'SysTrialBalanceController@store']);
        Route::get('trialbalance/{id}/edit', ['as' => 'edit', 'uses' => 'SysTrialBalanceController@edit']);
        Route::put('trialbalance-update/{id}', ['as' => 'trialbalance-update', 'uses' => 'SysTrialBalanceController@update']);
        Route::get('delete-trialbalance/{id}', ['as' => 'delete-trialbalance', 'uses' => 'SysTrialBalanceController@delete']);
        Route::get('trialbalance-search', 'SysTrialBalanceController@search');

        //Trial Balance New
        Route::get('trial-balance', ['as' => 'trial-balance', 'uses' => 'SysTrialBalanceController@trialbalancelist']);
        Route::post('trial-balance', ['as' => 'trial-balance', 'uses' => 'SysTrialBalanceController@trialbalancelist']);

        //Trading Account
        Route::get('trading-account', ['as' => 'trading-account', 'uses' => 'SysTradingAccountController@tradingaccountlist']);
        Route::post('trading-account', ['as' => 'trading-account', 'uses' => 'SysTradingAccountController@tradingaccountlist']);

        /*
        //Cash Receipt
        Route::get('cashreceipt', ['as' => 'cashreceipt', 'uses' => 'SysCashReceiptController@cashreceiptList']);
        Route::get('cashreceipt-add', ['as' => 'cashreceipt-add', 'uses' => 'SysCashReceiptController@cashreceiptAdd']);
        Route::post('cashreceipt-store', ['as' => 'cashreceipt-store', 'uses' => 'SysCashReceiptController@store']);
        Route::get('cashreceipt/{id}/edit', ['as' => 'edit', 'uses' => 'SysCashReceiptController@edit']);
        Route::get('cashreceipt/{id}', ['as' => 'view', 'uses' => 'SysCashReceiptController@view']);
        Route::put('cashreceipt-update/{id}', ['as' => 'cashreceipt-update', 'uses' => 'SysCashReceiptController@update']);
        Route::get('delete-cashreceipt/{id}', ['as' => 'delete-cashreceipt', 'uses' => 'SysCashReceiptController@delete']);
        Route::get('get-cr-custlist', 'SysCashReceiptController@getcrcustlist');
        Route::get('get-cr-balancelist', 'SysCashReceiptController@getcrbalancelist');
        Route::post('receipt-adjustments-store', ['as' => 'receipt-adjustments-store', 'uses' => 'SysCashReceiptController@receiptadjustmentsstore']);
        
        //Bank Receipt
        Route::get('bankreceipt', ['as' => 'bankreceipt', 'uses' => 'SysBankReceiptController@bankreceiptList']);
        Route::get('bankreceipt-add', ['as' => 'bankreceipt-add', 'uses' => 'SysBankReceiptController@bankreceiptAdd']);
        Route::post('bankreceipt-store', ['as' => 'bankreceipt-store', 'uses' => 'SysBankReceiptController@store']);
        Route::get('bankreceipt/{id}/edit', ['as' => 'edit', 'uses' => 'SysBankReceiptController@edit']);
        Route::get('bankreceipt/{id}', ['as' => 'view', 'uses' => 'SysBankReceiptController@view']);
        Route::put('bankreceipt-update/{id}', ['as' => 'bankreceipt-update', 'uses' => 'SysBankReceiptController@update']);
        Route::get('delete-bankreceipt/{id}', ['as' => 'delete-bankreceipt', 'uses' => 'SysBankReceiptController@delete']);
        Route::get('get-br-custlist', 'SysBankReceiptController@getbrcustlist');
        Route::get('get-br-balancelist', 'SysBankReceiptController@getbrbalancelist');
        
        //Cash Payment
        Route::get('cashpayment', ['as' => 'cashpayment', 'uses' => 'SysCashPaymentController@cashpaymentList']);
        Route::get('cashpayment-add', ['as' => 'cashpayment-add', 'uses' => 'SysCashPaymentController@cashpaymentAdd']);
        Route::post('cashpayment-store', ['as' => 'cashpayment-store', 'uses' => 'SysCashPaymentController@store']);
        Route::get('cashpayment/{id}/edit', ['as' => 'edit', 'uses' => 'SysCashPaymentController@edit']);
        Route::get('cashpayment/{id}', ['as' => 'view', 'uses' => 'SysCashPaymentController@view']);
        Route::put('cashpayment-update/{id}', ['as' => 'cashpayment-update', 'uses' => 'SysCashPaymentController@update']);
        Route::get('delete-cashpayment/{id}', ['as' => 'delete-cashpayment', 'uses' => 'SysCashPaymentController@delete']);
        Route::get('get-cp-custlist', 'SysCashPaymentController@getcpcustlist');
        Route::get('get-cp-balancelist', 'SysCashPaymentController@getcpbalancelist');
        
        //Bank Payment
        Route::get('bankpayment', ['as' => 'bankpayment', 'uses' => 'SysBankPaymentController@bankpaymentList']);
        Route::get('bankpayment-add', ['as' => 'bankpayment-add', 'uses' => 'SysBankPaymentController@bankpaymentAdd']);
        Route::post('bankpayment-store', ['as' => 'bankpayment-store', 'uses' => 'SysBankPaymentController@store']);
        Route::get('bankpayment/{id}/edit', ['as' => 'edit', 'uses' => 'SysBankPaymentController@edit']);
        Route::get('bankpayment/{id}', ['as' => 'view', 'uses' => 'SysBankPaymentController@view']);
        Route::put('bankpayment-update/{id}', ['as' => 'bankpayment-update', 'uses' => 'SysBankPaymentController@update']);
        Route::get('delete-bankpayment/{id}', ['as' => 'delete-bankpayment', 'uses' => 'SysBankPaymentController@delete']);
        Route::get('get-bp-custlist', 'SysBankPaymentController@getbpcustlist');
        Route::get('get-bp-balancelist', 'SysBankPaymentController@getbpbalancelist');
        */

        //Receipts
        Route::get('receipt/{id?}', ['as' => 'receipt', 'uses' => 'SysReceiptController@receiptList']);
        Route::post('receipt', ['as' => 'receipt', 'uses' => 'SysReceiptController@receiptList']);
        Route::get('receipt-add', ['as' => 'receipt-add', 'uses' => 'SysReceiptController@receiptAdd']);
        Route::get('receipt-add/{id}', ['as' => 'receipt-add', 'uses' => 'SysReceiptController@receiptAdd']);

        Route::get('receipt-add-deal/{id}/{mode}', ['as' => 'receipt-add-deal', 'uses' => 'SysReceiptController@receiptAddDeal']);
        Route::post('receipt-store', ['as' => 'receipt-store', 'uses' => 'SysReceiptController@store']);
        Route::get('receipt/{id}/edit', ['as' => 'edit', 'uses' => 'SysReceiptController@edit']);
        Route::get('receipt/{id}/view', ['as' => 'view', 'uses' => 'SysReceiptController@view']);
        Route::get('receipt/{id}/download', ['as' => 'download', 'uses' => 'SysReceiptController@download']);
        Route::put('receipt-update/{id}', ['as' => 'receipt-update', 'uses' => 'SysReceiptController@update']);
        Route::get('delete-receipt/{id}', ['as' => 'delete-receipt', 'uses' => 'SysReceiptController@delete']);
        Route::get('restore-receipt/{id}', ['as' => 'restore-receipt', 'uses' => 'SysReceiptController@restore']);
        Route::get('get-re-custlist', 'SysReceiptController@getrecustlist');
        Route::get('get-re-balancelist', 'SysReceiptController@getrebalancelist');
        Route::get('get-re-balancelist-edit', 'SysReceiptController@getrebalancelistedit');
        Route::post('delete-receipt-items', 'SysReceiptController@delete_receipt_items');
        Route::post('receipt-date-update', 'SysReceiptController@receiptdateupdate');
        Route::get('delete-receipt-adjustment/{id}', ['as' => 'delete-receipt-adjustment', 'uses' => 'SysReceiptController@delete_adjustment']);
        Route::post('delete-receipt-adjustment-json', ['as' => 'delete-receipt-adjustment-json', 'uses' => 'SysReceiptController@delete_adjustment_json']);

        Route::get('receipt-details/{id}', 'SysReceiptController@getDetails');



        //STL
        Route::get('stl', 'SysSTLController@index');
        Route::get('stl-add', 'SysSTLController@add');
        Route::post('stl-store', 'SysSTLController@store');
        Route::post('stl-update', 'SysSTLController@update');
        Route::post('stl-add-item', 'SysSTLController@stl_add_item');
        Route::post('stl-update-item', 'SysSTLController@stl_update_item');
        Route::post('get-pi-for-stl', 'SysSTLController@getpiforstl');
        Route::post('get-po-for-stl', 'SysSTLController@getpoforstl');
        Route::post('delete-stl-items', 'SysSTLController@delete_stl_items');
        Route::get('get-pi-list-for-stl', 'SysSTLController@getpilistforstl');
        Route::get('get-po-list-for-stl', 'SysSTLController@getpolistforstl');
        Route::get('stl/{id}/download', 'SysSTLController@download');
        Route::get('stl/{id}/edit', 'SysSTLController@edit');
        Route::get('stl/{id}/view', 'SysSTLController@view');

        Route::get('stl-details/{id}', 'SysSTLController@getDetails');

        Route::get('stl-report', 'SysSTLController@report');
        Route::post('stl-report', 'SysSTLController@report');

        Route::get('stl-supplier-report', 'SysSTLController@supplier_report');
        Route::post('stl-supplier-report', 'SysSTLController@supplier_report');

        Route::post('stl-edit-update', 'SysSTLController@edit_update');
        Route::post('stl-payment-add', 'SysSTLController@payment_add');
        Route::post('stl-payment-update', 'SysSTLController@payment_update');
        Route::get('stl/{id}/delete', 'SysSTLController@delete');
        Route::get('stl/{id}/restore', 'SysSTLController@restore');




        //Payments
        Route::get('payment/{id?}', ['as' => 'payment', 'uses' => 'SysPaymentController@paymentList']);

        Route::get('crm-payment-search', 'SysPaymentController@search')->name('crm-payment.search');
        Route::get('crm-pr-search', 'SysPurchaseReturnController@search')->name('crm-pr.search');
        Route::get('crm-quote-search', 'SysQuotationController@search')->name('crm-quote.search');

        Route::post('payment', ['as' => 'payment', 'uses' => 'SysPaymentController@paymentList']);
        Route::get('payment-add', ['as' => 'payment-add', 'uses' => 'SysPaymentController@paymentAdd']);
        Route::get('payment-add/{id}', ['as' => 'payment-add', 'uses' => 'SysPaymentController@paymentAdd']);
        Route::get('payment-add-from-cheque/{id}', ['as' => 'payment-add-from-cheque', 'uses' => 'SysPaymentController@paymentAddFromCheque']);

        Route::get('payment-details/{id}', 'SysPaymentController@getDetails');

        Route::post('payment-store', ['as' => 'payment-store', 'uses' => 'SysPaymentController@store']);
        Route::get('payment/{id}/edit', ['as' => 'edit', 'uses' => 'SysPaymentController@edit']);
        Route::get('payment/{id}/view', ['as' => 'view', 'uses' => 'SysPaymentController@view']);
        Route::get('payment/{id}/download', ['as' => 'download', 'uses' => 'SysPaymentController@download']);
        Route::put('payment-update/{id}', ['as' => 'payment-update', 'uses' => 'SysPaymentController@update']);
        Route::get('delete-payment/{id}', ['as' => 'delete-payment', 'uses' => 'SysPaymentController@delete']);
        Route::get('restore-payment/{id}', ['as' => 'restore-payment', 'uses' => 'SysPaymentController@restore']);
        Route::get('get-py-custlist', 'SysPaymentController@getpycustlist');
        Route::get('get-py-balancelist', 'SysPaymentController@getpybalancelist');
        Route::get('get-py-balancelist-edit', 'SysPaymentController@getpybalancelistedit');
        Route::post('delete-payment-items', 'SysPaymentController@delete_payment_items');
        Route::get('delete-payment-adjustment/{id}', ['as' => 'delete-payment-adjustment', 'uses' => 'SysPaymentController@delete_adjustment']);
        Route::post('delete-payment-adjustment-json', ['as' => 'delete-payment-adjustment-json', 'uses' => 'SysPaymentController@delete_adjustment_json']);


        Route::get('payment-cheque-list', ['as' => 'payment-cheque-list', 'uses' => 'SysPaymentController@payment_cheque_list']);
        Route::post('payment-cheque-store', ['as' => 'payment-cheque-store', 'uses' => 'SysPaymentController@payment_cheque_store']);
        Route::post('payment-cheque-update', ['as' => 'payment-cheque-update', 'uses' => 'SysPaymentController@payment_cheque_update']);
        Route::get('payment-cheque/{id}/delete', ['as' => 'payment-cheque-delete', 'uses' => 'SysPaymentController@payment_cheque_delete']);
        Route::get('payment-cheque/{id}/restore', ['as' => 'payment-cheque-restore', 'uses' => 'SysPaymentController@payment_cheque_restore']);
        Route::get('payment-cheque-print/{id}', ['as' => 'payment-cheque-print', 'uses' => 'SysPaymentController@payment_cheque_print']);
        Route::get('payment-cheque-print-template', ['as' => 'payment-cheque-print-template', 'uses' => 'SysPaymentController@payment_cheque_print_template']);
        Route::post('payment-cheque-print-template', ['as' => 'payment-cheque-print-template', 'uses' => 'SysPaymentController@payment_cheque_print_template']);



        //Postdated Receipt
        Route::get('postdatedreceipt', ['as' => 'postdatedreceipt', 'uses' => 'SysPostdatedReceiptController@postdatedreceiptList']);
        Route::get('postdatedreceipt-add', ['as' => 'postdatedreceipt-add', 'uses' => 'SysPostdatedReceiptController@postdatedreceiptAdd']);
        Route::post('postdatedreceipt-store', ['as' => 'postdatedreceipt-store', 'uses' => 'SysPostdatedReceiptController@store']);
        Route::get('postdatedreceipt/{id}/edit', ['as' => 'edit', 'uses' => 'SysPostdatedReceiptController@edit']);
        Route::get('postdatedreceipt/{id}', ['as' => 'view', 'uses' => 'SysPostdatedReceiptController@view']);
        Route::put('postdatedreceipt-update/{id}', ['as' => 'postdatedreceipt-update', 'uses' => 'SysPostdatedReceiptController@update']);
        Route::get('delete-postdatedreceipt/{id}', ['as' => 'delete-postdatedreceipt', 'uses' => 'SysPostdatedReceiptController@delete']);
        Route::get('get-pdr-custlist', 'SysPostdatedReceiptController@getpdrcustlist');
        Route::get('get-pdr-balancelist', 'SysPostdatedReceiptController@getpdrbalancelist');

        //Postdated Payment
        Route::get('postdatedpayment', ['as' => 'postdatedpayment', 'uses' => 'SysPostdatedPaymentController@postdatedpaymentList']);
        Route::get('postdatedpayment-add', ['as' => 'postdatedpayment-add', 'uses' => 'SysPostdatedPaymentController@postdatedpaymentAdd']);
        Route::post('postdatedpayment-store', ['as' => 'postdatedpayment-store', 'uses' => 'SysPostdatedPaymentController@store']);
        Route::get('postdatedpayment/{id}/edit', ['as' => 'edit', 'uses' => 'SysPostdatedPaymentController@edit']);
        Route::get('postdatedpayment/{id}', ['as' => 'view', 'uses' => 'SysPostdatedPaymentController@view']);
        Route::put('postdatedpayment-update/{id}', ['as' => 'postdatedpayment-update', 'uses' => 'SysPostdatedPaymentController@update']);
        Route::get('delete-postdatedpayment/{id}', ['as' => 'delete-postdatedpayment', 'uses' => 'SysPostdatedPaymentController@delete']);
        Route::get('get-pdp-custlist', 'SysPostdatedPaymentController@getpdpcustlist');
        Route::get('get-pdp-balancelist', 'SysPostdatedPaymentController@getpdpbalancelist');

        //Journal Voucher
        Route::get('journalvoucher', ['as' => 'journalvoucher', 'uses' => 'SysJournalVoucherController@journalvoucherList']);
        Route::get('journalvoucher/{id}', ['as' => 'journalvoucher', 'uses' => 'SysJournalVoucherController@journalvoucherList']);
        Route::get('journalvoucher-add', ['as' => 'journalvoucher-add', 'uses' => 'SysJournalVoucherController@journalvoucherAdd']);
        Route::get('journalvoucher-add/{id}', ['as' => 'journalvoucher-add', 'uses' => 'SysJournalVoucherController@journalvoucherAdd']);
        Route::get('journalvoucheradd/{date}', ['as' => 'journalvoucher-add', 'uses' => 'SysJournalVoucherController@journalvoucherAdd2']);
        Route::get('journalvoucher-add-deal/{id}/{cust_id}', ['as' => 'journalvoucher-add-deal', 'uses' => 'SysJournalVoucherController@journalvoucherAddDeal']);
        Route::post('journalvoucher-import', 'SysJournalVoucherController@journalvoucherImport');

        Route::get('journalvoucher-details/{id}', 'SysJournalVoucherController@getDetails');

        Route::post('journalvoucher-store', ['as' => 'journalvoucher-store', 'uses' => 'SysJournalVoucherController@store']);
        Route::get('journalvoucher/{id}/edit', ['as' => 'edit', 'uses' => 'SysJournalVoucherController@edit']);
        Route::get('journalvoucher/{id}/view', ['as' => 'view', 'uses' => 'SysJournalVoucherController@view']);
        Route::put('journalvoucher-update/{id}', ['as' => 'journalvoucher-update', 'uses' => 'SysJournalVoucherController@update']);
        Route::get('journalvoucher/{id}/delete', ['as' => 'delete', 'uses' => 'SysJournalVoucherController@delete']);
        Route::get('journalvoucher/{id}/restore', ['as' => 'restore', 'uses' => 'SysJournalVoucherController@restore']);
        Route::get('get-jv-accolist', 'SysJournalVoucherController@getjvaccolist');
        Route::post('journalvoucher-item-delete', 'SysJournalVoucherController@journalvoucher_item_delete');

        Route::get('journalvoucher-get-adjestment-list', 'SysJournalVoucherController@get_adjestment_list');
        Route::get('journalvoucher-get-adjestment-list-cus', 'SysJournalVoucherController@get_adjestment_list_cus');
        Route::get('journalvoucher-get-adjestment-list-sup', 'SysJournalVoucherController@get_adjestment_list_sup');

        Route::get('journalvoucher-get-adjestment-list-edit', 'SysJournalVoucherController@get_adjestment_list_edit');
        Route::get('journalvoucher-get-adjestment-list-edit-cus', 'SysJournalVoucherController@get_adjestment_list_edit_cus');
        Route::get('journalvoucher-get-adjestment-list-edit-sup', 'SysJournalVoucherController@get_adjestment_list_edit_sup');

        Route::post('journalvoucher-get-adjestment-update', 'SysJournalVoucherController@journalvoucher_get_adjestment_update');

        Route::get('get-receipt-adjustment-list-jv', 'SysJournalVoucherController@journalvoucher_get_receipt_adjestment_jv');
        Route::post('add-receipt-adjustment-list-jv', 'SysJournalVoucherController@journalvoucher_add_receipt_adjestment_jv');
        Route::get('get-receipt-adjustment-list-jv-edit', 'SysJournalVoucherController@journalvoucher_get_receipt_adjestment_jv_edit');
        Route::post('update-receipt-adjustment-list-jv', 'SysJournalVoucherController@journalvoucher_update_receipt_adjestment_jv');


        Route::get('get-payment-adjustment-list-jv', 'SysJournalVoucherController@journalvoucher_get_payment_adjestment_jv');
        Route::post('add-payment-adjustment-list-jv', 'SysJournalVoucherController@journalvoucher_add_payment_adjestment_jv');
        Route::get('get-payment-adjustment-list-jv-edit', 'SysJournalVoucherController@journalvoucher_get_payment_adjestment_jv_edit');
        Route::post('update-payment-adjustment-list-jv', 'SysJournalVoucherController@journalvoucher_update_payment_adjestment_jv');




        //Shipping
        Route::get('shipping-add', ['as' => 'shipping-add', 'uses' => 'SysShippingController@shippingAdd']);
        Route::post('shipping-store', ['as' => 'shipping-store', 'uses' => 'SysShippingController@store']);
        Route::post('shipping-store2', ['as' => 'shipping-store', 'uses' => 'SysShippingController@store2']);
        Route::get('shipping/{id}/edit', ['as' => 'edit', 'uses' => 'SysShippingController@edit']);
        Route::put('shipping-update/{id}', ['as' => 'shipping-update', 'uses' => 'SysShippingController@update']);
        Route::get('delete-shipping/{id}', ['as' => 'delete-shipping', 'uses' => 'SysShippingController@delete']);

        //Account Type
        Route::get('accounttype-add', ['as' => 'accounttype-add', 'uses' => 'SysAccountTypeController@accounttypeAdd']);
        Route::post('accounttype-store', ['as' => 'accounttype-store', 'uses' => 'SysAccountTypeController@store']);
        Route::post('accounttype-store2', ['as' => 'accounttype-store', 'uses' => 'SysAccountTypeController@store2']);
        Route::get('accounttype/{id}/edit', ['as' => 'edit', 'uses' => 'SysAccountTypeController@edit']);
        Route::put('accounttype-update/{id}', ['as' => 'accounttype-update', 'uses' => 'SysAccountTypeController@update']);
        Route::get('delete-accounttype/{id}', ['as' => 'delete-accounttype', 'uses' => 'SysAccountTypeController@delete']);

        //Account Group
        Route::get('accountgroup-add', ['as' => 'accountgroup-add', 'uses' => 'SysAccountGroupController@accountgroupAdd']);
        Route::post('accountgroup-store', ['as' => 'accountgroup-store', 'uses' => 'SysAccountGroupController@store']);
        Route::post('accountgroup-store2', ['as' => 'accountgroup-store', 'uses' => 'SysAccountGroupController@store2']);
        Route::get('accountgroup/{id}/edit', ['as' => 'edit', 'uses' => 'SysAccountGroupController@edit']);
        Route::get('accountgroup/{id}/delete', ['as' => 'delete', 'uses' => 'SysAccountGroupController@delete']);
        Route::put('accountgroup-update/{id}', ['as' => 'accountgroup-update', 'uses' => 'SysAccountGroupController@update']);
        Route::get('delete-accountgroup/{id}', ['as' => 'delete-accountgroup', 'uses' => 'SysAccountGroupController@delete']);

        //Account Sub Group
        Route::get('accountgroupsub-add', ['as' => 'accountgroupsub-add', 'uses' => 'SysAccountGroupSubController@accountgroupsubAdd']);
        Route::post('accountgroupsub-store', ['as' => 'accountgroupsub-store', 'uses' => 'SysAccountGroupSubController@store']);
        Route::post('accountgroupsub-store2', ['as' => 'accountgroupsub-store', 'uses' => 'SysAccountGroupSubController@store2']);
        Route::get('accountgroupsub/{id}/edit', ['as' => 'edit', 'uses' => 'SysAccountGroupSubController@edit']);
        Route::get('accountgroupsub/{id}/delete', ['as' => 'delete', 'uses' => 'SysAccountGroupSubController@delete']);
        Route::put('accountgroupsub-update/{id}', ['as' => 'accountgroupsub-update', 'uses' => 'SysAccountGroupSubController@update']);
        Route::get('delete-accountgroupsub/{id}', ['as' => 'delete-accountgroupsub', 'uses' => 'SysAccountGroupSubController@delete']);
        Route::get('ajaxAccountGroupSub', 'SmSystemSettingController@ajaxAccountGroupSub');

        //Account Sub Group2
        Route::get('accountgroupsub2-add', ['as' => 'accountgroupsub2-add', 'uses' => 'SysAccountGroupSub2Controller@accountgroupsub2Add']);
        Route::post('accountgroupsub2-store', ['as' => 'accountgroupsub2-store', 'uses' => 'SysAccountGroupSub2Controller@store']);
        Route::post('accountgroupsub2-store2', ['as' => 'accountgroupsub2-store', 'uses' => 'SysAccountGroupSub2Controller@store2']);
        Route::get('accountgroupsub2/{id}/edit', ['as' => 'edit', 'uses' => 'SysAccountGroupSub2Controller@edit']);
        Route::get('accountgroupsub2/{id}/delete', ['as' => 'delete', 'uses' => 'SysAccountGroupSub2Controller@delete']);
        Route::put('accountgroupsub2-update/{id}', ['as' => 'accountgroupsub2-update', 'uses' => 'SysAccountGroupSub2Controller@update']);
        Route::get('delete-accountgroupsub2/{id}', ['as' => 'delete-accountgroupsub2', 'uses' => 'SysAccountGroupSub2Controller@delete']);
        Route::get('get_sub_group', 'SysAccountGroupSub2Controller@get_sub_group');
        Route::get('chartofaccounts/{id}/get-edit', ['as' => 'edit', 'uses' => 'SysChartofAccountsController@edit_accounts']);
        Route::get('chartofaccounts-sub/{id}/get-edit', ['as' => 'edit-sub', 'uses' => 'SysChartofAccountsController@edit_subaccounts']);
        Route::get('accountgroupsub/{id}/get-edit', ['as' => 'edit', 'uses' => 'SysAccountGroupSubController@getEdit']);
        Route::get('accountgroupsub2/{id}/get-edit', ['as' => 'edit', 'uses' => 'SysAccountGroupSub2Controller@getEdit']);


        Route::get('vat-settings', ['as' => 'vat-settings', 'uses' => 'SysVatController@vatadd']);
        Route::post('vat-settings-store', ['as' => 'vat-settings-store', 'uses' => 'SysVatController@store']);
        Route::get('vat-settings/{id}/edit', ['as' => 'edit', 'uses' => 'SysVatController@edit']);
        Route::put('vat-settings-update/{id}', ['as' => 'vat-settings-update', 'uses' => 'SysVatController@update']);
        Route::get('vat-settings/{id}/delete', ['as' => 'delete', 'uses' => 'SysVatController@delete']);
        Route::get('get-vat-details', 'SysVatController@getvatdetails');
        Route::get('vat-settings/{id}/apply', ['as' => 'apply', 'uses' => 'SysVatController@apply']);

        Route::get('get-vat-by-id', 'SysVatController@get_cust_supp_vat');
        Route::get('get-vat-by-ca', 'SysVatController@get_cust_supp_vat_by_ca');

        //Page Frame
        Route::get('page', ['as' => 'page', 'uses' => 'SysPageFrameController@view']);
        //Page Tabs
        Route::resource('page-tabs', 'SysAppTabsController');
        Route::get('page-tabs-get', 'SysAppTabsController@get');
        Route::post('page-tabs-add', 'SysAppTabsController@create');
        Route::post('page-tabs-del', 'SysAppTabsController@del');

        // Route::group(['prefix' => 'page-tabs'], function () {
        //     Route::post('page-tabs-add','SysAppTabsController@create');
        // });


        Route::get('get-url-deal/{id}', 'SysURLController@deal');
        Route::get('get-url-deal-track/{id}', 'SysURLController@deal_track');
        Route::get('get-url-stock-out/{doc}', 'SysURLController@stock_out');
        Route::get('get-url-stock-in/{doc}', 'SysURLController@stock_in');

        Route::get('get-url-purchase-order/{id}', 'SysURLController@purchase_order');
        Route::get('get-url-purchase-grn/{id}', 'SysURLController@purchase_grn');
        Route::get('get-url-purchase-invoice/{id}', 'SysURLController@purchase_invoice');
        Route::get('get-url-purchase-return/{id}', 'SysURLController@purchase_return');

        Route::get('get-url-proforma-invoice/{id}', 'SysURLController@proforma_invoice');
        Route::get('get-url-sales-invoice/{id}', 'SysURLController@sales_invoice');
        Route::get('get-url-sales-invoice-pdf-download/{id}', 'SysURLController@sales_invoice_pdf_download');
        Route::get('get-url-delivery-note/{id}', 'SysURLController@delivery_note');
        Route::get('get-url-sales-return/{id}', 'SysURLController@sales_return');
        Route::get('get-url-clearance/{id}', 'SysURLController@clearance');

        Route::get('get-url-journalvoucher/{id}', 'SysURLController@journalvoucher');
        Route::get('get-url-receipt/{id}', 'SysURLController@receipt');
        Route::get('get-url-payment/{id}', 'SysURLController@payment');

        Route::get('get-url-customer/{id}', 'SysURLController@customer');
        Route::get('get-url-supplier/{id}', 'SysURLController@supplier');

        Route::get('get-url-generalledger/{id}', 'SysURLController@generalledger');


        // edit
        Route::get('get-edit-url-purchase-order/{id}', 'SysURLController@purchase_order_edit');
        Route::get('get-edit-url-purchase-grn/{id}', 'SysURLController@purchase_grn_edit');
        Route::get('get-edit-url-purchase-invoice/{id}', 'SysURLController@purchase_invoice_edit');
        Route::get('get-edit-url-purchase-return/{id}', 'SysURLController@purchase_return_edit');

        Route::get('get-edit-url-sales-invoice/{id}', 'SysURLController@sales_invoice_edit');
        Route::get('get-edit-url-delivery-note/{id}', 'SysURLController@delivery_note_edit');
        Route::get('get-edit-url-sales-return/{id}', 'SysURLController@sales_return_edit');

        Route::get('get-edit-url-journalvoucher/{id}', 'SysURLController@journalvoucher_edit');
        Route::get('get-edit-url-receipt/{id}', 'SysURLController@receipt_edit');
        Route::get('get-edit-url-payment/{id}', 'SysURLController@payment_edit');

        Route::get('get-edit-url-stock-in/{id}', 'SysURLController@stock_in_edit');
        Route::get('get-edit-url-stock-out/{id}', 'SysURLController@stock_out_edit');
        Route::get('get-edit-url-packing-list/{id}', 'SysURLController@packing_list_edit');
        // edit



        //Goods Receipt Note (GRN)
        Route::get('goods-receipt-note', 'SysGRNController@index');
        Route::get('goods-receipt-note/create', 'SysGRNController@create');
        Route::post('goods-receipt-note-store', 'SysGRNController@store');
        Route::post('goods-receipt-note-pending', 'SysGRNController@goodsreceiptnotepending');
        Route::get('goods-receipt-note-pending-item-list', 'SysGRNController@goodsreceiptnotependingitemlist');
        Route::post('goods-receipt-note-for-pi', 'SysGRNController@goodsreceiptnoteforpi');
        Route::get('goods-receipt-note-for-pi-item-list', 'SysGRNController@goodsreceiptnoteforpiitemlist');
        Route::post('get-deal-code-from-id', 'SysGRNController@get_deal_code_from_id');
        Route::post('remove-grn-items', 'SysGRNController@remove_grn_items');

        Route::get('purchasegrn-details/{id}', 'SysGRNController@getDetails');



        Route::post('goods-receipt-note-add-serialno', 'SysGRNController@addserialno');
        Route::get('goods-receipt-note-get-serialno', 'SysGRNController@getserialno');
        Route::get('goods-receipt-note/{id}/edit', 'SysGRNController@edit');
        //Route::get('goods-receipt-note/{id}/view', 'SysGRNController@view');

        Route::get('goods-receipt-note-list/{id?}', 'SysGRNController@view');
        Route::post('goods-receipt-note-update', 'SysGRNController@update');
        Route::get('goods-receipt-note/{id}/delete', 'SysGRNController@delete');
        Route::get('goods-receipt-note/{id}/restore', 'SysGRNController@restore');
        Route::post('goods-receipt-note-update-currency', 'SysGRNController@goodsreceiptnoteupdate_currency');
        Route::get('goods-receipt-note/{id}/download', 'SysGRNController@download');

        Route::post('delete-grn-items', 'SysGRNController@deletegrnitems');
        Route::post('add-grn-items', 'SysGRNController@addgrnitems');
        Route::post('update-grn-items', 'SysGRNController@updategrnitems');

        Route::post('add-grn-items-cart', 'SysGRNController@addgrnitemscart');
        Route::post('delete-grn-items-cart', 'SysGRNController@deletegrnitemscart');


        Route::post('goods-receipt-note-update-discount', 'SysGRNController@goodsreceiptnoteupdate_discount');
        Route::post('add-grn-items-cart-discount', 'SysGRNController@addgrnitemscart_discount');
        Route::post('goods-receipt-note-update-freight', 'SysGRNController@goodsreceiptnoteupdate_freight');
        Route::post('add-grn-items-cart-freight', 'SysGRNController@addgrnitemscart_freight');
        Route::post('goods-receipt-note-update-custom', 'SysGRNController@goodsreceiptnoteupdate_custom');
        Route::post('add-grn-items-cart-custom', 'SysGRNController@addgrnitemscart_custom');



        //License Key
        Route::post('add-grn-license-key-cart', 'SysLicenseKeyController@add_grn_license_key_cart');
        Route::post('view-grn-license-key-cart', 'SysLicenseKeyController@view_grn_license_key_cart');
        Route::post('delete-grn-license-key-cart', 'SysLicenseKeyController@delete_grn_license_key_cart');
        Route::post('add-grn-license-key-cart-excel', 'SysLicenseKeyController@add_grn_license_key_cart_excel');

        Route::post('add-grn-license-key', 'SysLicenseKeyController@add_grn_license_key');
        Route::post('view-grn-license-key', 'SysLicenseKeyController@view_grn_license_key');
        Route::post('delete-grn-license-key', 'SysLicenseKeyController@delete_grn_license_key');
        Route::post('import-grn-license-key', 'SysLicenseKeyController@import_grn_license_key');


        Route::post('add-ops-license-key', 'SysLicenseKeyController@add_ops_license_key');
        Route::post('view-ops-license-key', 'SysLicenseKeyController@view_ops_license_key');
        Route::post('delete-ops-license-key', 'SysLicenseKeyController@delete_ops_license_key');
        Route::post('add-ops-license-key-excel', 'SysLicenseKeyController@add_ops_license_key_excel');


        Route::post('dn-get-grn-license-key', 'SysLicenseKeyController@dn_get_grn_license_key');
        Route::post('dn-update-grn-license-key', 'SysLicenseKeyController@dn_update_grn_license_key');
        Route::post('dn-get-dln-license-key', 'SysLicenseKeyController@dn_get_dln_license_key');
        Route::post('dn-update-dln-license-key', 'SysLicenseKeyController@dn_update_dln_license_key');

        Route::post('sales-return-get-dn-license-key', 'SysLicenseKeyController@sales_return_get_dn_license_key');
        Route::post('sales-return-update-dn-license-key', 'SysLicenseKeyController@sales_return_update_dn_license_key');
        Route::post('sales-return-get-license-key', 'SysLicenseKeyController@sales_return_get_license_key');
        Route::post('sales-return-update-license-key', 'SysLicenseKeyController@sales_return_update_license_key');

        Route::post('purchase-return-get-dn-license-key', 'SysLicenseKeyController@purchase_return_get_grn_license_key');
        Route::post('purchase-return-update-dn-license-key', 'SysLicenseKeyController@purchase_return_update_grn_license_key');
        Route::post('purchase-return-get-license-key', 'SysLicenseKeyController@purchase_return_get_license_key');
        Route::post('purchase-return-update-license-key', 'SysLicenseKeyController@purchase_return_update_license_key');


        Route::get('license-key-report/{id?}', 'SysLicenseKeyController@report');
        Route::post('license-key-report', 'SysLicenseKeyController@report');

        //License Key



        //purchase-order

        Route::get('customer-search-record', 'SysCustomerController@search');



        Route::get('purchase-order/{id?}', 'SysPurchaseOrderController@index');
        Route::get('purchase-details/{id}', 'SysPurchaseOrderController@getDetails');
        Route::get('purchase-order-search', 'SysPurchaseOrderController@search')->name('purchase.search');
        Route::get('purchase-order/create', 'SysPurchaseOrderController@create');
        Route::post('purchase-order-store', 'SysPurchaseOrderController@store');
        Route::post('purchase-order-update', 'SysPurchaseOrderController@update');
        Route::post('purchase-order-find', 'SysPurchaseOrderController@find');
        Route::get('purchase-order/{id}/print', 'SysPurchaseOrderController@print');
        Route::get('purchase-order/{id}/printexcel', 'SysPurchaseOrderController@printexcel');
        Route::get('purchase-order/{id}/printpreview', 'SysPurchaseOrderController@printpreview');
        Route::post('purchase-order-add-attachment', 'SysPurchaseOrderController@addattachment');
        Route::post('add-purchase-order-items-cart', 'SysPurchaseOrderController@add_purchase_order_items_cart');
        Route::post('add-purchase-order-items-excel-cart', 'SysPurchaseOrderController@add_purchase_order_items_excel_cart');
        Route::post('delete-purchase-order-items-cart', 'SysPurchaseOrderController@delete_purchase_order_items_cart');
        Route::post('update-purchase-order-items-cart', 'SysPurchaseOrderController@update_purchase_order_items_cart');
        Route::post('view-purchase-order-items-cart', 'SysPurchaseOrderController@view_purchase_order_items_cart');
        Route::post('add-deal-items-to-purchase-order-cart', 'SysPurchaseOrderController@adddealitemstopurchaseordercart');
        Route::post('add-selected-deal-items-to-purchase-order-cart', 'SysPurchaseOrderController@deal_add_selected_deal_items_to_purchase_order_cart');
        Route::get('purchase-order/{id}/edit', 'SysPurchaseOrderController@edit');
        Route::get('purchase-order/{id}/view', 'SysPurchaseOrderController@view');
        Route::get('purchase-order/{id}/delete', 'SysPurchaseOrderController@delete');
        Route::get('purchase-order/{id}/restore', 'SysPurchaseOrderController@restore');
        Route::post('delete-purchase-order-items', 'SysPurchaseOrderController@deletepurchaseorderitems');
        Route::post('add-purchase-order-items', 'SysPurchaseOrderController@addpurchaseorderitems');
        Route::post('update-purchase-order-items', 'SysPurchaseOrderController@updatepurchaseorderitems');
        Route::post('purchase-order-update-currency', 'SysPurchaseOrderController@purchaseorderupdate_currency');
        Route::post('purchase-order-update-discount', 'SysPurchaseOrderController@purchaseorderupdate_discount');
        Route::post('purchase-order-update-freight', 'SysPurchaseOrderController@purchaseorderupdate_freight');
        Route::post('purchase-order-update-custom', 'SysPurchaseOrderController@purchaseorderupdate_custom');
        Route::post('add-purchase-order-items-cart-discount', 'SysPurchaseOrderController@add_purchase_order_items_cart_discount');
        Route::post('add-purchase-order-items-cart-freight', 'SysPurchaseOrderController@add_purchase_order_items_cart_freight');
        Route::post('add-purchase-order-items-cart-custom', 'SysPurchaseOrderController@add_purchase_order_items_cart_custom');
        Route::post('add-purchase-order-deal-items-cart-discount', 'SysPurchaseOrderController@add_purchase_order_deal_items_cart_discount');
        Route::post('add-purchase-order-deal-items-cart-freight', 'SysPurchaseOrderController@add_purchase_order_deal_items_cart_freight');
        Route::post('add-purchase-order-deal-items-cart-custom', 'SysPurchaseOrderController@add_purchase_order_deal_items_cart_custom');
        Route::post('add-purchase-order-deal-items-cart', 'SysPurchaseOrderController@add_purchase_order_deal_items_cart');


        Route::get('purchase-order-create2', 'SysPurchaseOrderController@create2')->name('purchase-order.create2');;
        // Route::get('purchase-order/create/{customer_reference?}/{salesman_name?}/{deal_id?}/{deal_code?}', 'SysPurchaseOrderController@create2');
        Route::post('add-deal-purchase-order-items-cart', 'SysPurchaseOrderController@deal_add_purchase_order_items_cart');
        Route::post('update-deal-purchase-order-items-cart', 'SysPurchaseOrderController@deal_update_purchase_order_items_cart');
        Route::post('delete-deal-purchase-order-items-cart', 'SysPurchaseOrderController@deal_delete_purchase_order_items_cart');
        Route::post('deal-purchase-order-store', 'SysPurchaseOrderController@deal_purchase_store');

        Route::post('purchase-order-create-gen', 'SysPurchaseOrderController@create_gen');
        Route::get('purchase-order-create-all/{deal_id}', 'SysPurchaseOrderController@create_all');
        Route::post('purchase-order-store-all', 'SysPurchaseOrderController@store_all');


        //Route::post('purchase-order-pending', 'SysPurchaseOrderController@purchaseorderpending');

        Route::get('purchase-order/delete/{id}', 'SysPurchaseOrderController@delete');

        //purchase-invoice
        Route::resource('purchase-invoice', 'SysPurchaseInvoiceController');
        Route::post('purchase-invoice-store', 'SysPurchaseInvoiceController@store');
        Route::post('purchase-invoice-find', 'SysPurchaseInvoiceController@find');
        Route::get('purchase-invoice/{id}/print', 'SysPurchaseInvoiceController@print');
        Route::get('purchase-invoice/{id}/download', 'SysPurchaseInvoiceController@download');
        Route::get('purchase-invoice/{id}/printpreview', 'SysPurchaseInvoiceController@printpreview');
        Route::post('purchase-invoice-add-attachment', 'SysPurchaseInvoiceController@addattachment');
        Route::get('purchase-invoice/{id}/edit', 'SysPurchaseInvoiceController@edit');
        Route::get('purchase-invoice/{id}/view', 'SysPurchaseInvoiceController@view');
        Route::post('purchase-invoice-update', 'SysPurchaseInvoiceController@update');
        Route::get('purchase-invoice/{id}/delete', 'SysPurchaseInvoiceController@delete');
        Route::get('purchase-invoice/{id}/restore', 'SysPurchaseInvoiceController@restore');
        Route::post('purchase-invoice-update-currency', 'SysPurchaseInvoiceController@purchaseinvoiceupdate_currency');

        Route::get('purchase-invoice-details/{id}', 'SysPurchaseInvoiceController@getDetails');

        Route::post('delete-purchase-invoice-items', 'SysPurchaseInvoiceController@deletepurchaseinvoiceitems');
        Route::post('add-purchase-invoice-items', 'SysPurchaseInvoiceController@addpurchaseinvoiceitems');
        Route::post('update-purchase-invoice-items', 'SysPurchaseInvoiceController@updatepurchaseinvoiceitems');

        Route::post('purchase-order-pending', 'SysPurchaseInvoiceController@purchaseorderpending');
        Route::get('purchase-order-pending-item-list', 'SysPurchaseInvoiceController@purchaseorderpendingitemlist');

        Route::post('purchase-invoice-update-discount', 'SysPurchaseInvoiceController@purchaseinvoiceupdate_discount');
        Route::post('add-purchase-invoice-items-cart-discount', 'SysPurchaseInvoiceController@addpurchaseinvoiceitemscart_discount');
        Route::post('purchase-invoice-update-freight', 'SysPurchaseInvoiceController@purchaseinvoiceupdate_freight');
        Route::post('add-purchase-invoice-items-cart-freight', 'SysPurchaseInvoiceController@addpurchaseinvoiceitemscart_freight');
        Route::post('purchase-invoice-update-custom', 'SysPurchaseInvoiceController@purchaseinvoiceupdate_custom');
        Route::post('add-purchase-invoice-items-cart-custom', 'SysPurchaseInvoiceController@addpurchaseinvoiceitemscart_custom');


        Route::post('purchase-invoice-get-adjustment', 'SysPurchaseInvoiceController@purchaseinvoice_get_adjustment');
        Route::post('purchase-invoice-add-adjustment-cart', 'SysPurchaseInvoiceController@purchaseinvoice_add_adjustment_cart');
        Route::post('purchase-invoice-update-adjustment', 'SysPurchaseInvoiceController@purchaseinvoiceupdate_adjustment');

        //purchase-return
        //Route::resource('purchase-return', 'SysPurchaseReturnController');
        //Route::get('purchase-return/delete/{id}', 'SysPurchaseReturnController@delete');

        //purchase-return
        Route::get('purchase-return/{id?}', ['as' => 'purchase-return', 'uses' => 'SysPurchaseReturnController@purchasereturnList']);
        Route::get('purchase-return-add', ['as' => 'purchase-return-add', 'uses' => 'SysPurchaseReturnController@purchasereturnAdd']);
        Route::post('purchase-return-store', ['as' => 'purchase-return-store', 'uses' => 'SysPurchaseReturnController@store']);
        Route::get('purchase-return/{id}/edit', ['as' => 'edit', 'uses' => 'SysPurchaseReturnController@edit']);
        Route::get('purchase-return/{id}/view', ['as' => 'view', 'uses' => 'SysPurchaseReturnController@view']);
        Route::put('purchase-return-update/{id}', ['as' => 'purchase-return-update', 'uses' => 'SysPurchaseReturnController@update']);
        Route::get('purchase-return/{id}/delete', ['as' => 'delete', 'uses' => 'SysPurchaseReturnController@delete']);
        Route::get('purchase-return/{id}/restore', ['as' => 'restore', 'uses' => 'SysPurchaseReturnController@restore']);
        Route::get('get-pi-list', 'SysPurchaseReturnController@get_pi_list');
        Route::get('get-pi-list-for-pi-return', 'SysPurchaseReturnController@get_pi_list_for_pi_return');
        Route::post('purchase-return-add-adjestment', ['as' => 'purchase-return-add-adjestment', 'uses' => 'SysPurchaseReturnController@purchasereturnadd_adjestment']);
        Route::post('purchase-return-add-adjestment2', ['as' => 'purchase-return-add-adjestment2', 'uses' => 'SysPurchaseReturnController@purchasereturnadd_adjestment2']);
        Route::post('purchase-return-add-adjestment3', ['as' => 'purchase-return-add-adjestment3', 'uses' => 'SysPurchaseReturnController@purchasereturnadd_adjestment3']);
        Route::get('get-purchase-return-adjestment-list', 'SysPurchaseReturnController@adjestment_list');
        Route::get('get-purchase-return-adjestment-list-add', 'SysPurchaseReturnController@adjestment_list_add');
        Route::get('delete-purchase-return-adjustment/{id}', ['as' => 'delete-purchase-return-adjustment', 'uses' => 'SysPurchaseReturnController@delete_adjustment']);

        Route::get('purchase-return-details/{id}', 'SysPurchaseReturnController@getDetails');


        Route::post('purchase-return-add-serialno', 'SysPurchaseReturnController@addserialno');
        Route::get('purchase-return-get-serialno', 'SysPurchaseReturnController@getserialno');

        Route::post('add-purchase-return-items-cart', 'SysPurchaseReturnController@addpurchasereturnitemscart');
        Route::post('update-purchase-return-items-cart', 'SysPurchaseReturnController@updatepurchasereturnitemscart');
        Route::post('delete-purchase-return-items-cart', 'SysPurchaseReturnController@deletepurchasereturnitemscart');
        Route::post('purchase-return-update-currency', 'SysPurchaseReturnController@purchasereturnupdate_currency');


        //sales-invoice Old
        // Route::resource('sales-invoice', 'SysSalesInvoiceController-');
        // Route::get('sales-invoice/delete/{id}', 'SysSalesInvoiceController-@delete');

        //clearance
        Route::resource('clearance', 'SysClearanceController');
        Route::post('clearance-store', 'SysClearanceController@store');
        Route::get('clearance/{id}/preview', 'SysClearanceController@preview');
        Route::get('clearance/{id}/download', 'SysClearanceController@download');
        Route::get('clearance/{id}/edit', 'SysClearanceController@edit');
        Route::post('clearance-update', 'SysClearanceController@update');

        Route::post('add-clearance-items-cart', 'SysClearanceController@add_clearance_items_cart');
        Route::post('update-clearance-items-cart', 'SysClearanceController@update_clearance_items_cart');
        Route::post('delete-clearance-items-cart', 'SysClearanceController@delete_clearance_items_cart');

        Route::post('add-clearance-items', 'SysClearanceController@add_clearance_items');
        Route::post('update-clearance-items', 'SysClearanceController@update_clearance_items');
        Route::post('delete-clearance-items', 'SysClearanceController@delete_clearance_items');

        Route::get('get-clearance-items-list', 'SysClearanceController@get_clearance_items_list');

        Route::post('add-deal-items-to-clearance-cart', 'SysClearanceController@add_deal_items_to_clearance_cart');
        Route::get('clearance-add/{invoice_no?}/{account_id?}/{deal_id?}', 'SysClearanceController@create2');

        //search
        Route::get('sales-invoice/search', 'SysSalesInvoiceNewController@search')->name('sales-invoice.search');
        Route::get('delivery-note/search', 'SysDeliveryNoteController@search')->name('delivery-note.search');
        Route::get('sales-return/search', 'SysSalesReturnController@search')->name('sales-return.search');
        Route::get('receipt-search', 'SysReceiptController@search')->name('receipt.search');
        Route::get('crm-deals/search', 'SysCrmDealsController@search')->name('crm-deals.search');
        Route::get('crm-deals-track/search', 'SysCrmDealTrackController@search')->name('crm-deals-track.search');
        Route::get('journalvoucher/search', 'SysJournalVoucherController@search')->name('journalvoucher.search');

        //erp clear data
        Route::get('delete-all-data', 'SysDataDeleteController@index');
        Route::post('login-delete-all-data', 'SysDataDeleteController@data_login');
        //Route::post('/login-delete-all-data', [App\Http\Controllers\SysDataDeleteController::class, 'data_login']);

        Route::post('delete-all-data-all', 'SysDataDeleteController@all_data');
        Route::get('restore-data-all/{id}', 'SysDataDeleteController@restore_tables');
        
        Route::post('delete-all-data-by-date', 'SysDataDeleteController@all_data_by_date');

        Route::post('delete-all-data-journal', 'SysDataDeleteController@journal');
        Route::post('delete-all-data-receipt', 'SysDataDeleteController@receipt');
        Route::post('delete-all-data-payments', 'SysDataDeleteController@payments');
        Route::post('delete-all-data-leads', 'SysDataDeleteController@leads');
        Route::post('delete-all-data-deals', 'SysDataDeleteController@deals');
        Route::post('delete-all-data-sales', 'SysDataDeleteController@sales');
        Route::post('delete-all-data-purchase', 'SysDataDeleteController@purchase');
        Route::post('delete-all-data-inventory', 'SysDataDeleteController@inventory');
        Route::post('delete-all-data-service-desk', 'SysDataDeleteController@service-desk');
        Route::post('delete-all-data-execution-desk', 'SysDataDeleteController@execution-desk');
        Route::post('delete-all-data-suppliers', 'SysDataDeleteController@suppliers');
        Route::post('delete-all-data-customers', 'SysDataDeleteController@customers');
        Route::post('delete-all-data-accounts', 'SysDataDeleteController@accounts');
        Route::post('delete-all-data-companies', 'SysDataDeleteController@companies');

        //sales-invoice
        Route::get('sales-invoice', 'SysSalesInvoiceNewController@index');
        Route::get('sales-invoice/{id}', 'SysSalesInvoiceNewController@index')->where('id', '[0-9]+');
        Route::get('sales-invoice/create', 'SysSalesInvoiceNewController@create');
        Route::get('sales-invoice-customername', 'SysSalesInvoiceNewController@getcustomername');
        Route::post('sales-invoice-store', 'SysSalesInvoiceNewController@store');
        Route::post('sales-invoice-store2', 'SysSalesInvoiceNewController@store2');
        Route::post('sales-invoice-update', 'SysSalesInvoiceNewController@update');
        Route::post('add-deal-items-to-sales-invoice-cart', 'SysSalesInvoiceNewController@adddealitemstosalesinvoicecart');
        Route::post('add-sales-invoice-items-cart', 'SysSalesInvoiceNewController@addsalesinvoiceitemscart');
        Route::post('add-sales-invoice-items-excel-cart', 'SysSalesInvoiceNewController@addsalesinvoiceitemscart_excel');
        Route::post('update-sales-invoice-items-cart', 'SysSalesInvoiceNewController@updatesalesinvoiceitemscart');
        Route::post('delete-sales-invoice-items-cart', 'SysSalesInvoiceNewController@deletesalesinvoiceitemscart');
        Route::post('add-sales-invoice-items-cart-discount', 'SysSalesInvoiceNewController@add_sales_invoice_items_cart_discount');

        Route::get('sales-invoice-details/{id}', 'SysSalesInvoiceNewController@getDetails');

        Route::post('add-selected-deal-items-to-sales-invoice-cart', 'SysSalesInvoiceNewController@deal_add_selected_deal_items_to_sales_invoice_cart');

        Route::post('add-sales-invoice-items', 'SysSalesInvoiceNewController@addsalesinvoiceitems');
        Route::post('update-sales-invoice-items', 'SysSalesInvoiceNewController@updatesalesinvoiceitems');
        Route::post('delete-sales-invoice-items', 'SysSalesInvoiceNewController@deletesalesinvoiceitems');
        Route::post('sales-invoice-discount-update', 'SysSalesInvoiceNewController@sales_invoice_discount_update');

        Route::post('get-customer-details', 'SysSalesInvoiceNewController@getcustomerdetails');
        Route::post('get-customer-details-arabic', 'SysSalesInvoiceNewController@getcustomerdetailsarabic');

        // Route::get('sales-invoice/create/{customer_reference?}/{salesman_name?}/{deal_id?}/{deal_code?}', 'SysSalesInvoiceNewController@create2');
        Route::get('sales-invoice-deal-track-create/{customer_reference?}/{salesman_name?}/{deal_id?}/{deal_code?}', 'SysSalesInvoiceNewController@create2');

        Route::get('sales-invoice/{id}/edit', 'SysSalesInvoiceNewController@edit');
        Route::get('sales-invoice/{id}/view', 'SysSalesInvoiceNewController@view');
        Route::get('sales-invoice/{id}/download/{type?}', 'SysSalesInvoiceNewController@download');
        Route::get('sales-invoice/{id}/delete', 'SysSalesInvoiceNewController@delete');
        Route::get('sales-invoice/{id}/restore', 'SysSalesInvoiceNewController@restore');
        Route::post('sales-invoice-update-currency', 'SysSalesInvoiceNewController@salesinvoiceupdate_currency');
        Route::post('sales-invoice-update-adjustment', 'SysSalesInvoiceNewController@salesinvoiceupdate_adjustment');

        Route::post('sales-invoice-get-adjustment', 'SysSalesInvoiceNewController@salesinvoice_get_adjustment');
        Route::post('sales-invoice-add-adjustment-cart', 'SysSalesInvoiceNewController@salesinvoice_add_adjustment_cart');

        Route::post('sales-invoice-additems', 'SysSalesInvoiceNewController@additems');

        Route::post('get-proforma-invoice-for-si', 'SysSalesInvoiceNewController@getproformainvoiceforsi');
        Route::get('get-proforma-invoice-items-for-si', 'SysSalesInvoiceNewController@getproformainvoiceitemsforsi');

        Route::post('sales-invoice-find', 'SysSalesInvoiceController@find');
        Route::get('sales-invoice/{id}/print', 'SysSalesInvoiceController@print');
        Route::get('sales-invoice/{id}/printpreview', 'SysSalesInvoiceController@printpreview');
        Route::post('sales-invoice-add-attachment', 'SysSalesInvoiceController@addattachment');
        //Route::get('sales-invoice/delete/{id}', 'SysSalesInvoiceController@delete');

        Route::get('sales-invoice-report', 'SysSalesInvoiceReportController@index');
        Route::post('sales-invoice-report', 'SysSalesInvoiceReportController@index');


        //Route::post('add-sales-invoice-attachment', 'SysSalesInvoiceController@add_sales_invoice_attachment');
        Route::post('view-sales-invoice-attachment', 'SysSalesInvoiceNewController@view_sales_invoice_attachment');
        Route::post('view-sales-invoice-attachment2', 'SysSalesInvoiceNewController@view_sales_invoice_attachment_by_invoice_no');
        Route::post('delete-sales-invoice-attachment', 'SysSalesInvoiceNewController@delete_sales_invoice_attachment');
        Route::post('add-sales-invoice-attachment', 'SysSalesInvoiceNewController@add_sales_invoice_attachment');

        Route::post('view-purchase-invoice-attachment', 'SysPurchaseInvoiceController@view_attachment');
        Route::post('delete-purchase-invoice-attachment', 'SysPurchaseInvoiceController@delete_attachment');
        Route::post('add-purchase-invoice-attachment', 'SysPurchaseInvoiceController@add_attachment');

        Route::post('view-purchase-order-attachment', 'SysPurchaseOrderController@view_attachment');
        Route::post('delete-purchase-order-attachment', 'SysPurchaseOrderController@delete_attachment');
        Route::post('add-purchase-order-attachment', 'SysPurchaseOrderController@add_attachment');

        Route::post('view-clearance-attachment', 'SysClearanceController@view_attachment');
        Route::post('delete-clearance-attachment', 'SysClearanceController@delete_attachment');
        Route::post('add-clearance-attachment', 'SysClearanceController@add_attachment');

        Route::post('view-journal-voucher-attachment', 'SysJournalVoucherController@view_attachment');
        Route::post('delete-journal-voucher-attachment', 'SysJournalVoucherController@delete_attachment');
        Route::post('add-journal-voucher-attachment', 'SysJournalVoucherController@add_attachment');

        //sales-return
        Route::get('crm-grn-search', 'SysGRNController@search')->name('crm-grn.search');
        Route::get('sales-return/{id?}', ['as' => 'sales-return', 'uses' => 'SysSalesReturnController@salesreturnList']);
        Route::get('sales-return/{id}', ['as' => 'sales-return', 'uses' => 'SysSalesReturnController@salesreturnList']);
        Route::get('sales-return-add', ['as' => 'sales-return-add', 'uses' => 'SysSalesReturnController@salesreturnAdd']);
        Route::post('sales-return-store', ['as' => 'sales-return-store', 'uses' => 'SysSalesReturnController@store']);
        Route::get('sales-return/{id}/edit', ['as' => 'edit', 'uses' => 'SysSalesReturnController@edit']);
        Route::get('sales-return/{id}/view', ['as' => 'view', 'uses' => 'SysSalesReturnController@view']);
        Route::put('sales-return-update/{id}', ['as' => 'sales-return-update', 'uses' => 'SysSalesReturnController@update']);
        Route::get('delete-sales-return-adjustment/{id}', ['as' => 'delete-sales-return-adjustment', 'uses' => 'SysSalesReturnController@delete_adjustment']);

        Route::get('sales-return-details/{id}', 'SysSalesReturnController@getDetails');

        Route::post('get-dn-list', 'SysSalesReturnController@get_dn_list');
        Route::get('get-dn-list-for-si-return', 'SysSalesReturnController@get_dn_list_for_si_return');

        Route::post('get-si-list', 'SysSalesReturnController@get_si_list');
        Route::get('get-si-list-for-si-return', 'SysSalesReturnController@get_si_list_for_si_return');

        Route::get('sales-return/{id}/download', ['as' => 'download', 'uses' => 'SysSalesReturnController@download']);
        Route::get('sales-return/{id}/delete', ['as' => 'delete', 'uses' => 'SysSalesReturnController@delete']);
        Route::get('sales-return/{id}/restore', ['as' => 'restore', 'uses' => 'SysSalesReturnController@restore']);
        Route::post('sales-return-add-adjestment', ['as' => 'sales-return-add-adjestment', 'uses' => 'SysSalesReturnController@salesreturnadd_adjestment']);
        Route::post('sales-return-add-adjestment2', ['as' => 'sales-return-add-adjestment2', 'uses' => 'SysSalesReturnController@salesreturnadd_adjestment2']);
        Route::post('sales-return-add-adjestment3', ['as' => 'sales-return-add-adjestment3', 'uses' => 'SysSalesReturnController@salesreturnadd_adjestment3']);
        Route::get('get-sales-return-adjestment-list', 'SysSalesReturnController@adjestment_list');
        Route::get('get-sales-return-adjestment-list-add', 'SysSalesReturnController@adjestment_list_add');

        Route::post('add-sales-return-items-cart', 'SysSalesReturnController@addsalesreturnitemscart');
        Route::post('update-sales-return-items-cart', 'SysSalesReturnController@updatesalesreturnitemscart');
        Route::post('delete-sales-return-items-cart', 'SysSalesReturnController@deletesalesreturnitemscart');
        Route::post('sales-return-update-currency', 'SysSalesReturnController@salesreturnupdate_currency');

        Route::post('add-sales-return-items', 'SysSalesReturnController@addsalesreturnitems');
        Route::post('update-sales-return-items', 'SysSalesReturnController@updatesalesreturnitems');
        Route::post('delete-sales-return-items', 'SysSalesReturnController@deletesalesreturnitems');

        //delivery note
        Route::get('delivery-note', ['as' => 'delivery-note', 'uses' => 'SysDeliveryNoteController@deliverynoteList']);
        Route::get('delivery-note-add', ['as' => 'delivery-note-add', 'uses' => 'SysDeliveryNoteController@deliverynoteAdd']);
        Route::post('delivery-note-store', ['as' => 'delivery-note-store', 'uses' => 'SysDeliveryNoteController@store']);
        Route::post('delivery-note-store2', ['as' => 'delivery-note-store2', 'uses' => 'SysDeliveryNoteController@store2']);
        Route::get('delivery-note/{id}/view', ['as' => 'view', 'uses' => 'SysDeliveryNoteController@view']);
        Route::put('delivery-note-update/{id}', ['as' => 'delivery-note-update', 'uses' => 'SysDeliveryNoteController@update']);
        Route::get('delivery-note/{id}/edit', ['as' => 'edit', 'uses' => 'SysDeliveryNoteController@edit']);
        Route::get('delivery-note/{id}/download/{type?}', ['as' => 'download', 'uses' => 'SysDeliveryNoteController@download']);
        Route::get('delivery-note/{id}/delete', ['as' => 'delete', 'uses' => 'SysDeliveryNoteController@delete']);
        Route::get('delivery-note/{id}/restore', ['as' => 'restore', 'uses' => 'SysDeliveryNoteController@restore']);
        Route::post('delivery-note-item-add', 'SysDeliveryNoteController@item_add');
        Route::post('delivery-note-item-delete', 'SysDeliveryNoteController@item_delete');
        Route::post('delivery-note-item-update', 'SysDeliveryNoteController@item_update');
        Route::post('delivery-note-update-currency', 'SysDeliveryNoteController@deliverynoteupdate_currency');

        Route::get('delivery-note-details/{id}', 'SysDeliveryNoteController@getDetails');


        Route::post('delivery-note-item-add-cart', 'SysDeliveryNoteController@item_add_cart');
        Route::post('add-delivery-note-items-cart-discount', 'SysDeliveryNoteController@item_add_cart_discount');

        Route::post('add-deal-items-to-dln-cart', 'SysDeliveryNoteController@adddealitemstodlncart');
        //Route::post('add-deal-items-to-dln-cart', 'SysDeliveryNoteController@deal_add_selected_deal_items_to_delivery_note_cart');        
        Route::get('delivery-note-add/{customer_reference?}/{salesman_name?}/{account_id?}/{deal_id?}', 'SysDeliveryNoteController@deliverynoteAdd2');
        Route::get('delivery-note-add-deal/{deal_id}', 'SysDeliveryNoteController@deliverynoteAdd3');

        //Route::get('get_si_list_delivery_note', 'SysDeliveryNoteController@get_si_list_delivery_note');        
        //Route::get('get_si_list_for_delivery_note', 'SysDeliveryNoteController@get_si_list_for_delivery_note');

        Route::post('sales-invoice-pending', 'SysDeliveryNoteController@salesinvoicepending');
        Route::get('sales-invoice-pending-item-list', 'SysDeliveryNoteController@salesinvoicependingitemlist');

        //delivery advice
        Route::get('delivery-advice', ['as' => 'delivery-advice', 'uses' => 'SysDeliveryAdviceController@deliveryadviceList']);
        Route::get('delivery-advice-add', ['as' => 'delivery-advice-add', 'uses' => 'SysDeliveryAdviceController@deliveryadviceAdd']);
        Route::post('delivery-advice-store', ['as' => 'delivery-advice-store', 'uses' => 'SysDeliveryAdviceController@store']);
        Route::get('delivery-advice/{id}/edit', ['as' => 'edit', 'uses' => 'SysDeliveryAdviceController@edit']);
        Route::get('delivery-advice/{id}', ['as' => 'view', 'uses' => 'SysDeliveryAdviceController@view']);
        Route::put('delivery-advice-update/{id}', ['as' => 'delivery-advice-update', 'uses' => 'SysDeliveryAdviceController@update']);
        Route::get('get_si_list_delivery_advice', 'SysDeliveryAdviceController@get_si_list_delivery_advice');
        Route::get('get_si_list_for_delivery_advice', 'SysDeliveryAdviceController@get_si_list_for_delivery_advice');


        //payroll
        Route::get('payroll', ['as' => 'payroll', 'uses' => 'SmPayrollController@index']);

        Route::post('payroll', ['as' => 'payroll', 'uses' => 'SmPayrollController@searchStaffPayr']);

        Route::get('generate-Payroll/{id}/{month}/{year}', 'SmPayrollController@generatePayroll');
        Route::post('save-payroll-data', ['as' => 'savePayrollData', 'uses' => 'SmPayrollController@savePayrollData']);

        Route::get('get-bank-account-info', 'SmPayrollController@bankAccountInfo');


        Route::get('pay-payroll/{id}/{role_id}', 'SmPayrollController@paymentPayroll');
        Route::post('savePayrollPaymentData', ['as' => 'savePayrollPaymentData', 'uses' => 'SmPayrollController@savePayrollPaymentData']);
        Route::get('view-payslip/{id}', 'SmPayrollController@viewPayslip');
        // print pay slip

        Route::get('print-payslip/{id}', 'SmPayrollController@printPayslip');
        Route::get('send-payslip/{id}', 'SmPayrollController@mailPayslip');

        //payroll Report
        Route::get('payroll-report', 'SmPayrollController@payrollReport');
        Route::post('search-payroll-report', ['as' => 'searchPayrollReport', 'uses' => 'SmPayrollController@searchPayrollReport']);
        Route::get('search-payroll-report', 'SmPayrollController@searchPayrollReport');


        Route::get('print-payroll-report/{role_id}/{month}/{year}', 'SmPayrollController@printPayrollReport');

        Route::get('evaluation-document/{file_name}', function ($file_name = null) {

            $file = public_path() . '/uploads/homework/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            }
        });

        //teacher
        Route::get('upload-content', 'SmTeacherController@uploadContentList');
        Route::post('save-upload-content', 'SmTeacherController@saveUploadContent');
        Route::get('delete-upload-content/{id}', 'SmTeacherController@deleteUploadContent');

        Route::get('download-content-document/{file_name}', function ($file_name = null) {

            $file = public_path() . '/uploads/upload_contents/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            }
        });

        Route::get('assignment-list', 'SmTeacherController@assignmentList');
        Route::get('study-metarial-list', 'SmTeacherController@studyMetarialList');
        Route::get('syllabus-list', 'SmTeacherController@syllabusList');
        Route::get('other-download-list', 'SmTeacherController@otherDownloadList');

        // Communicate
        Route::get('notice-list', 'SmCommunicateController@noticeList');
        Route::get('send-message', 'SmCommunicateController@sendMessage');
        Route::get('create-notice', 'SmCommunicateController@sendMessage');
        Route::post('save-notice-data', 'SmCommunicateController@saveNoticeData');
        Route::get('edit-notice/{id}', 'SmCommunicateController@editNotice');
        Route::post('update-notice-data', 'SmCommunicateController@updateNoticeData');
        Route::get('delete-notice-view/{id}', 'SmCommunicateController@deleteNoticeView');
        Route::get('send-email-sms-view', 'SmCommunicateController@sendEmailSmsView');
        Route::post('send-email-sms', 'SmCommunicateController@sendEmailSms');
        Route::get('email-sms-log', 'SmCommunicateController@emailSmsLog');
        Route::get('delete-notice/{id}', 'SmCommunicateController@deleteNotice');

        Route::get('studStaffByRole', 'SmCommunicateController@studStaffByRole');

        //Event
        Route::resource('event', 'SmEventController');
        Route::get('delete-event-view/{id}', 'SmEventController@deleteEventView');
        Route::get('delete-event/{id}', 'SmEventController@deleteEvent');

        //Holiday
        Route::resource('holiday', 'SmHolidayController');
        Route::resource('weekend', 'SmWeekendController');
        Route::get('delete-holiday-view/{id}', 'SmHolidayController@deleteHolidayView');
        Route::get('delete-holiday/{id}', 'SmHolidayController@deleteHoliday');

        Route::resource('library-member', 'SmLibraryMemberController');
        Route::get('cancel-membership/{id}', 'SmLibraryMemberController@cancelMembership');

        // Ajax Subject in dropdown by section change
        Route::get('/ajaxSubjectDropdown', 'SmHomeworkController@ajaxSubjectDropdown');

        // Route::get('localization/{locale}','SmLocalizationController@index');

        Route::get('create-sub-category', 'SmItemCategoryController@SubCategory');
        Route::get('create-sub-category/{id}', 'SmItemCategoryController@createSubCategory')->name('createSubCategory');
        Route::post('store-item-sub-category', 'SmItemCategoryController@StoreSubCategory');
        Route::get('edit-sub-category/{id}', 'SmItemCategoryController@editSubCategory');
        Route::post('update-item-sub-category', 'SmItemCategoryController@updateSubCategory');
        Route::get('delete-sub-category-view/{id}', 'SmItemController@deleteSubItemView');
        Route::get('delete-sucategory/{id}', 'SmItemCategoryController@deleteSucategory');

        // notification
        Route::get('notification-read', 'SysNotificationController@notificationread');
        Route::get('notification-read-one', 'SysNotificationController@notificationreadone');

        // CRM

        Route::get('crm-test', 'SysCrmLeadsController@crmtest');

        Route::get('crm-dashboard', 'SysCrmDashboardController@dashboard');
        Route::post('crm-dashboard', 'SysCrmDashboardController@dashboard');

        Route::get('crm-dashboard-views/{comid}/{user}', 'SysCrmDashboardFunctionController@dashboard_views');
        Route::post('crm-dashboard-views/{comid}/{user}', 'SysCrmDashboardFunctionController@dashboard_views');

        Route::post('get-user-company', 'SysCrmDashboardFunctionController@get_user_company');
        Route::resource('crm-leads', 'SysCrmLeadsController');

        Route::get('crm-auth', 'SysCrmDashboardController@check_auth');
        Route::post('crm-auth-update', 'SysCrmDashboardController@check_auth_update');


        //Route::post('crm-leads/show', 'SysCrmLeadsController@show');

        //Route::get('crm-leads-showlist', 'SysCrmLeadsController@showList');
        Route::get('crm-leads/show/{id?}', 'SysCrmLeadsController@show');

        Route::get('crm-leads/{id}/view', 'SysCrmLeadsController@view');
        Route::get('leads-search', 'SysCrmLeadsController@search')->name('leads.search');
        Route::get('crm-leads-comments-restore/{id}', 'SysCrmLeadsController@crmleadscommentsrestore');
        Route::post('crm-leads-comments-add', 'SysCrmLeadsController@crmleadscommentsadd');
        Route::get('crm-leads-comments-delete/{id}', 'SysCrmLeadsController@crmleadscommentsdelete');
        Route::get('crm-leads-customername', 'SysCrmLeadsController@getcustomername');
        Route::get('crm-deals-customername', 'SysCrmLeadsController@getcustomername_deal');
        Route::get('crm-leads/{id}/convert', 'SysCrmLeadsController@convert');
        Route::post('crm-leads-update-status', 'SysCrmLeadsController@crmleadsupdatestatus');
        //Route::get('crm-leads/{id}/delete', 'SysCrmLeadsController@delete');
        Route::get('get-lead-name-to-brand', 'SysCrmLeadsController@getleadnametobrand');

        Route::post('crm-leads/{id}/delete', 'SysCrmLeadsController@delete');
        Route::get('crm-leads/comments/{id}', 'SysCrmLeadsController@getComments');
        Route::post('crm-leads/{id}/restore', 'SysCrmLeadsController@restoreLead');
        Route::post('crm-leads-comments-store', 'SysCrmLeadsController@crmleadscommentsaddAPI');

        Route::resource('price-book', 'SysPriceBookController');
        Route::get('price-book', 'SysPriceBookController@index');
        Route::get('price-book/show', 'SysPriceBookController@show');
        Route::post('price-book/show', 'SysPriceBookController@show');
        // Route::get('price-book/{id}', 'SysPriceBookController@getproduct');
        Route::get('price-book/{id}/{id2}/edit', 'SysPriceBookController@editproduct');

        Route::get('check-company-id', function () {
            return response()->json(session('logged_session_data.company_id'));
        });
        Route::get('company-error', 'SysCrmDealsController@company_error');


        Route::resource('crm-deals', 'SysCrmDealsController');
        Route::get('crm-deals/show/{id?}', 'SysCrmDealsController@show');
        Route::post('crm-deals/show', 'SysCrmDealsController@show');
        Route::get('crm-deals-details/{id}', 'SysCrmDealsController@getDetails');
        Route::get('crm-deals-add', 'SysCrmDealsController@add');
        Route::get('crm-deals-comments-restore/{id}', 'SysCrmDealsController@crmdealscommentsrestore');


        //Route::get('crm-deals-showlist', 'SysCrmDealsController@showList');

        Route::get('crm-deals-track-report', 'SysCrmDealsTrackReportController@trackdealreport');
        Route::post('crm-deals-track-report', 'SysCrmDealsTrackReportController@trackdealreport');

        Route::post('crm-deals-delivery-update-items', 'SysCrmDealTrackController@crmdealsdeliveryupdateitems');

        Route::post('crm-deal-profit-update', 'SysCrmDealsController@profitupdate');

        Route::get('crm-deals-sales-report-gp', 'SysCrmDealsReportController@salesreportgp');
        Route::post('crm-deals-sales-report-gp', 'SysCrmDealsReportController@salesreportgp');

        Route::get('crm-deals-sales-report-company', 'SysCrmDealsReportController@salesreportcompany');
        Route::post('crm-deals-sales-report-company', 'SysCrmDealsReportController@salesreportcompany');

        Route::get('crm-deals-sales-report', 'SysCrmDealsReportController@salesreport');
        Route::post('crm-deals-sales-report', 'SysCrmDealsReportController@salesreport');
        Route::get('crm-deals-sales-report/{cid}/{m1}/{m2}/', 'SysCrmDealsReportController@salesreport');
        Route::get('crm-deals-sales-report-list/{uid}/{cid}/{m1}/{m2}', 'SysCrmDealsReportController@salesreportlist');
        Route::post('crm-deals-sales-report-list', 'SysCrmDealsReportController@salesreportlist');
        Route::get('crm-deals-sales-reports', 'SysCrmDealsReportController@salesreports');

        Route::get('crm-deals-brand-sales-report-new', 'SysCrmDealsReportController@brandsalesreportnew');
        Route::post('crm-deals-brand-sales-report-new', 'SysCrmDealsReportController@brandsalesreportnew');
        Route::get('crm-deals-forecast-report-list-brand/{bid}/{cid}/{m1}/{m2}', 'SysCrmDealsReportController@forecastreportlistbrand');

        Route::get('crm-deals-gitex2023-report', 'SysCrmDealsReportController@gitex2023salesreport');
        Route::get('crm-deals-gitex2023-report-list/{uid}/{sid}', 'SysCrmDealsReportController@gitex2023salesreportlist');

        Route::get('crm-brand-sale-report', 'SysCrmDealBrandSalesReportController@brandsalesreport');
        Route::post('crm-brand-sale-report', 'SysCrmDealBrandSalesReportController@brandsalesreport');

        Route::get('crm-deals-forecast-report', 'SysCrmDealsReportController@forecastreport');
        Route::post('crm-deals-forecast-report', 'SysCrmDealsReportController@forecastreport');
        Route::get('crm-deals-forecast-report-list/{uid}/{cid}/{m1}/{m2}', 'SysCrmDealsReportController@forecastreportlist');
        Route::get('crm-deals-forecast-reports', 'SysCrmDealsReportController@forecastreports');

        Route::get('crm-deals-onprocess-report-list/{uid}/{cid}/{m1}/{m2}', 'SysCrmDealsReportController@onprocessreportlist');

        Route::get('crm-lead-convertion-report', 'SysCrmDealsReportController@leadconvertionreport');
        Route::post('crm-lead-convertion-report', 'SysCrmDealsReportController@leadconvertionreport');

        Route::get('crm-deal-sales/{id}/{mo}/{co}', 'SysCrmDealsReportController@dealpagesalesfilterlist');
        Route::get('crm-deal-service/{id}/{mo}/{co}', 'SysCrmDealsReportController@dealpageservicefilterlist');
        Route::get('crm-deal-amc/{id}/{mo}/{co}', 'SysCrmDealsReportController@dealpageamcfilterlist');
        Route::get('crm-deal-project/{id}/{mo}/{co}', 'SysCrmDealsReportController@dealpageprojectfilterlist');
        Route::get('crm-deal-sales-performance/{id}/{mo}/{co}', 'SysCrmDealsReportController@dealpagesalesperformancefilterlist');

        Route::get('crm-deals/{id}/view', 'SysCrmDealsController@view');
        Route::get('crm-deals/{id}/edit/{qid?}', 'SysCrmDealsController@edit');
        Route::post('crm-deals-comments-add', 'SysCrmDealsController@crmdealscommentsadd');
        Route::get('crm-deals-comments-delete/{id}', 'SysCrmDealsController@crmdealscommentsdelete');
        Route::post('crm-deals-update-stage', 'SysCrmDealsController@crmdealsupdatestage');
        //Route::get('crm-deals/{id}/delete', 'SysCrmDealsController@delete');
        Route::post('crm-deal-collaboration', 'SysCrmDealsController@collaboration');
        Route::post('crm-deal-adddeliveryaddress', 'SysCrmDealsController@adddeliveryaddress');
        Route::post('crm-deal-changedeliveryaddress', 'SysCrmDealsController@changedeliveryaddress');
        Route::post('crm-deal-cancel', 'SysCrmDealsController@dealcancel');
        Route::post('crm-deal-percent', 'SysCrmDealsController@dealpercent');
        Route::post('crm-deal-add-end-user', 'SysCrmDealsController@addenduser');

        Route::get('crm-deals/comments/{id}', 'SysCrmDealsController@getComments');
        Route::post('crm-deals/{id}/restore', 'SysCrmDealsController@restoreDeal');
        Route::post('crm-deals/{id}/delete', 'SysCrmDealsController@delete');
        Route::post('crm-quote-upload-excel-quote-edit', 'SysCrmQuoteNewController@update_excel_in_quoteedit');


        Route::post('crm-deal-service', 'SysCrmServiceController@service');
        Route::post('crm-deal-service-assign', 'SysCrmServiceController@serviceassign');
        Route::post('crm-deal-service-comments', 'SysCrmServiceController@servicecomments');
        Route::post('crm-deal-service-comments-additional', 'SysCrmServiceController@servicecommentsadditional');
        Route::post('crm-deal-service-comments-update', 'SysCrmServiceController@servicecommentsupdate');
        Route::get('crm-deal-service-list', 'SysCrmServiceController@servicelist');
        Route::get('crm-deal-service/{id}/view', 'SysCrmServiceController@serviceview');
        Route::get('crm-deal-service/{sid}/view/{id}', 'SysCrmServiceController@serviceviewedit');
        Route::get('crm-deal-service/{id}/delete', 'SysCrmServiceController@servicedelete');


        Route::post('crm-deal-support', 'SysCrmSupportController@support');
        Route::get('crm-deal-support-list/{id?}', 'SysCrmSupportController@supportlist');
        Route::post('crm-deal-support-list', 'SysCrmSupportController@supportlist');
        Route::post('crm-deal-support-update', 'SysCrmSupportController@supportupdate');
        Route::get('crm-deal-support-list/{id}/view', 'SysCrmSupportController@supportlistview');
        Route::get('crm-deal-support-list/{id}/delete', 'SysCrmSupportController@supportlistdelete');
        Route::get('crm-deal-support-list/{id}/restore', 'SysCrmSupportController@supportlistrestore');

        Route::post('crm-deal-support-list-request-submit', 'SysCrmSupportController@supportlistrequestsubmit');
        Route::post('crm-deal-support-list-request-update', 'SysCrmSupportController@supportlistrequestupdate');
        Route::get('crm-deal-support-requested-list/{id?}', 'SysCrmSupportController@supportrequestedlist');
        Route::post('crm-deal-support-requested-list', 'SysCrmSupportController@supportrequestedlist');
        Route::post('crm-deal-support-list-request-add-new', 'SysCrmSupportController@supportlistrequestsubmit2');

        Route::get('crm-deal-support/{id}/view', 'SysCrmSupportController@supportview');
        Route::get('crm-deal-support/{id}/delete', 'SysCrmSupportController@supportdelete');
        Route::post('crm-deal-support-activity', 'SysCrmSupportController@supportactivity');
        Route::post('crm-deal-support-activity-close', 'SysCrmSupportController@supportactivityclose');
        Route::post('crm-deal-support-activity-comments', 'SysCrmSupportController@supportactivitycomments');
        Route::get('eng-track-search', 'SysCrmEngineerTrackingController@search')->name('eng-track.search');
        Route::get('crm-amc-search', 'SysCrmAmcController@search')->name('crm-amc.search');
        Route::get('crm-amc-req-search', 'SysCrmAmcController@searchReq')->name('crm-amc.req.search');
        Route::get('crm-ps-search', 'SysCrmPSController@search')->name('crm-ps.search');
        Route::get('crm-psreq-search', 'SysCrmPSController@searchReq')->name('crm-psreq.search');
        Route::get('crm-pre-search', 'SysCrmSupportController@search')->name('crm-pre.search');
        Route::get('crm-pre-search-req', 'SysCrmSupportController@searchReq')->name('crm-pre-req.search');
        Route::post('crm-deal-support-activity-comments-add', 'SysCrmSupportController@supportactivitycomments_add');
        Route::post('crm-deal-support-activity-comments-view', 'SysCrmSupportController@supportactivitycomments_view');

        Route::get('crm-engineer-tracking/{type?}/{id?}', 'SysCrmEngineerTrackingController@index');
        Route::post('crm-engineer-tracking', 'SysCrmEngineerTrackingController@index');

        Route::get('crm-amc-form', 'SysCrmAmcController@form');
        Route::post('crm-amc-add', 'SysCrmAmcController@add');
        Route::get('crm-amc/{id}/view', 'SysCrmAmcController@view');
        Route::get('crm-amc/{id}/edit', 'SysCrmAmcController@edit');
        Route::post('crm-amc/{id}/update', 'SysCrmAmcController@update');
        Route::post('crm-amc-update-status', 'SysCrmAmcController@updatestatus');
        Route::get('crm-amc-comments/{id}/delete', 'SysCrmAmcController@commentsdelete');
        Route::post('crm-amc-comments-add', 'SysCrmAmcController@commentsadd');
        Route::post('crm-amc-support-update', 'SysCrmAmcController@supportupdate');
        Route::post('crm-amc-asign-staff', 'SysCrmAmcController@asignstaff');

        Route::get('crm-amc-deal-list', 'SysCrmAmcController@amclist');
        Route::post('crm-amc-deal-list', 'SysCrmAmcController@amclist');
        Route::post('crm-deal-amc', 'SysCrmAmcController@amc');
        Route::post('crm-deal-amc-edit', 'SysCrmAmcController@amcedit');

        Route::get('crm-dashboard-view/{id}', 'SysCrmLeadsController@crmdashboardview');

        //Route::get('crm-dashboard-sales-filter', 'SysCrmLeadsController@dashboardsalesfilter');
        Route::get('crm-dashboard-sales-filter', 'SysCrmDealsController@dashboardsalesfilter');
        Route::get('crm-dashboard-lead-filter', 'SysCrmLeadsController@dashboardleadfilter');
        Route::get('crm-dashboard-deal-filter', 'SysCrmDealsController@dashboarddealfilter');

        Route::get('crm-dashboard-service-filter', 'SysCrmDealsController@dashboardservicefilter');
        Route::get('crm-dashboard-amc-filter', 'SysCrmDealsController@dashboardamcfilter');
        Route::get('crm-dashboard-project-filter', 'SysCrmDealsController@dashboardprojectfilter');

        Route::get('crm-deal/{id}/{mo}/{co}', 'SysCrmDealsController@dealpagefilterlist');
        Route::get('crm-lead/{id}/{mo}/{co}', 'SysCrmDealsController@leadpagefilterlist');
        Route::get('crm-deal-track-list/{track}', 'SysCrmDealTrackController@trackpagefilter');

        Route::resource('crm-deal-track', 'SysCrmDealTrackController');

        Route::get('crm-deal-track/{id}/view', 'SysCrmDealTrackController@view');

        Route::post('crm-deal-track/show', 'SysCrmDealTrackController@show');
        Route::post('crm-deal-track-submit', 'SysCrmDealTrackController@crmdealtracksubmit');
        Route::post('crm-deal-track-submit-edit', 'SysCrmDealTrackController@crmdealtracksubmitedit');
        //Route::get('crm-deal-track-approval-list', 'SysCrmDealTrackController@crmdealtrackapprovallist');
        Route::get('crm-deal-track-approval-list/{id?}', 'SysCrmDealTrackController@crmdealtrackapprovallist');
        Route::get('crm-deal-track-approval-list-sort', 'SysCrmDealTrackController@crmdealtrackapprovallistsort');
        Route::post('crm-deal-track-approval-list', 'SysCrmDealTrackController@crmdealtrackapprovallist');

        Route::get('crm-deal-track-details/{id}', 'SysCrmDealTrackController@getDetails');

        //Route::get('crm-deal-track-approval-listing', 'SysCrmDealTrackController@crmdealtrackapprovallisting');

        Route::get('crm-deal-track-approval/{id}', 'SysCrmDealTrackController@crmdealtrackapproval');
        Route::get('crm-deal-track-grn/{id}', 'SysCrmDealTrackGRNController@crmdealtrackgrn');
        Route::post('crm-deal-track-grn-update/{id}', 'SysCrmDealTrackGRNController@crmdealtrackgrnupdate');
        Route::post('crm-deal-track-grn-no-update', 'SysCrmDealTrackGRNController@crmdealtrackgrnnoupdate');

        //Route::get('crm-deal-track-status', 'SysCrmDealTrackStatusController@index');
        //Route::post('crm-deal-track-status-search', 'SysCrmDealTrackStatusController@search');
        Route::get('crm-deal-track-status/{id?}', 'SysCrmDealTrackStatusController@index');



        Route::get('crm-amc-list/{id?}', 'SysCrmAmcController@crmamclist');
        Route::post('crm-amc-list', 'SysCrmAmcController@crmamclist');
        Route::get('crm-amc-detail/{id}', 'SysCrmAmcController@crmamcdetail');

        Route::post('crm-amc-add-service-request', 'SysCrmAmcController@addservicerequest');
        Route::post('crm-amc-add', 'SysCrmAmcController@addamc');

        Route::post('crm-amc-edit', 'SysCrmAmcController@crmamcedit');
        Route::post('crm-amc-update', 'SysCrmAmcController@crmamcupdate');
        Route::get('crm-amc-deactivate/{id}', 'SysCrmAmcController@crmamcdeactivate');
        Route::get('crm-amc-activate/{id}', 'SysCrmAmcController@crmamcactivate');

        Route::get('crm-amc-customer-details', 'SysCrmAmcController@amccustomerdetails');

        Route::get('crm-amc-service-request-list/{id?}', 'SysCrmAmcController@servicerequestlist');
        Route::post('crm-amc-service-request-list', 'SysCrmAmcController@servicerequestlist');
        Route::post('crm-amc-service-request-list-add', 'SysCrmAmcController@servicerequestlistadd');
        Route::get('crm-amc-service-request-detail/{id}', 'SysCrmAmcController@servicerequestdetail');

        Route::post('crm-amc-service-request-work', 'SysCrmAmcController@servicerequestwork');

        Route::post('crm-amc-service-request-edit', 'SysCrmAmcController@servicerequestedit');
        Route::post('crm-amc-service-request-update', 'SysCrmAmcController@servicerequestupdate');
        Route::get('crm-amc-service-request-deactivate/{id}', 'SysCrmAmcController@servicerequestdeactivate');
        Route::get('crm-amc-service-request-activate/{id}', 'SysCrmAmcController@servicerequestactivate');

        Route::post('crm-amc-service-request-comments', 'SysCrmAmcController@servicerequestcomments');
        Route::post('crm-amc-service-request-get-comments', 'SysCrmAmcController@servicerequest_get_comments');


        Route::get('crm-ps-track-service-list/{id?}', 'SysCrmPSController@pstrackservicelist');
        Route::post('crm-ps-track-service-list', 'SysCrmPSController@pstrackservicelist');
        Route::post('crm-ps-service-track-submit', 'SysCrmPSController@pstrackservicesubmit');
        Route::get('crm-ps-track-service-detail/{id}', 'SysCrmPSController@pstrackservicedetail');

        Route::get('crm-ps-service-list-req/{id?}', 'SysCrmPSController@pstrackservicereqlist');
        Route::post('crm-ps-service-list-req', 'SysCrmPSController@pstrackservicereqlist');
        Route::get('crm-ps-track-list', 'SysCrmPSController@amctracklist');
        Route::get('crm-ps-service-detail/{id}', 'SysCrmPSController@pstrackservicereqdetail');

        Route::post('crm-ps-service-request-edit', 'SysCrmPSController@psservicerequestedit');
        Route::post('crm-ps-service-request-work', 'SysCrmPSController@psservicerequestwork');
        Route::post('crm-ps-service-request-update', 'SysCrmPSController@psservicerequestupdate');
        Route::post('crm-ps-service-request-update2', 'SysCrmPSController@psservicerequestupdate2');
        Route::get('crm-ps-service-request-deactivate/{id}', 'SysCrmPSController@psservicerequestdeactivate');
        Route::get('crm-ps-service-request-activate/{id}', 'SysCrmPSController@psservicerequestactivate');

        Route::post('crm-ps-service-request-comments', 'SysCrmPSController@ps_servicerequestcomments');
        Route::post('crm-ps-service-request-get-comments', 'SysCrmPSController@ps_servicerequest_get_comments');

        Route::post('crm-pre-sales-request-list', 'SysCrmPreSalesController@pre_sales_request_list');


        Route::get('crm-reimbursement-request', 'SysCrmReimbursementRequest@index');
        Route::post('crm-reimbursement-request-add', 'SysCrmReimbursementRequest@store');
        Route::post('crm-reimbursement-request-update', 'SysCrmReimbursementRequest@update');
        Route::post('crm-reimbursement-request-delete', 'SysCrmReimbursementRequest@delete');
        Route::post('crm-reimbursement-request-restore', 'SysCrmReimbursementRequest@restore');
        Route::get('crm-reimbursement-request-get-custname', 'SysCrmReimbursementRequest@get_custname');
        Route::post('crm-reimbursement-request-account-approve', 'SysCrmReimbursementRequest@account_approve');
        Route::post('crm-reimbursement-request-accounts-head-approve', 'SysCrmReimbursementRequest@accounts_head_approve');
        Route::post('crm-reimbursement-request-dept-head-approve', 'SysCrmReimbursementRequest@dept_head_approve');


        //crm-amc-track-service-list
        //crm-amc-service-list-req

        //KUNAL

        Route::get('crm-user-tasks-search', 'SysCrmUserTaskController@search')->name('crm-user-tasks.search');
        Route::get('crm-user-my-tasks-search', 'SysCrmUserTaskController@searchMyTasks')->name('crm-user-my-tasks.search');



        Route::get('crm-user-tasks/{status?}/{id?}', 'SysCrmUserTaskController@index');
        Route::post('crm-user-tasks', 'SysCrmUserTaskController@store');
        Route::get('crm-user-tasks-details/{id}', 'SysCrmUserTaskController@show');
        Route::get('crm-user-tasks-assigned-details/{id}', 'SysCrmUserTaskController@showassignedByme');
        Route::get('tasks-assigned-by-me/{status?}', 'SysCrmUserTaskController@assignedByMe');
        Route::post('crm-task-progress-update', 'SysCrmUserTaskController@taskProgressUpdate');
        Route::get('/crm-user-task/{task}/comments', 'SysCrmUserTaskController@getComments');
        Route::post('/crm-user-task/{task}/comments', 'SysCrmUserTaskController@addComment');
        Route::post('crm-task-status-update', 'SysCrmUserTaskController@taskStatusUpdate');
        Route::get('crm-user-tasks/{id}/view', 'SysCrmUserTaskController@show');

        Route::resource('user-todo-list', 'SysCrmUserTodoController');
        Route::post('crm-todo-progress-update', 'SysCrmUserTodoController@todoProgressUpdate');
        Route::get('/crm-user-todo/{todo}/comments', 'SysCrmUserTodoController@getComments');
        Route::post('/crm-user-todo/{todo}/comments', 'SysCrmUserTodoController@addComment');
        Route::post('crm-todo-update', 'SysCrmUserTodoController@update');
        Route::delete('user-todo-delete/{id}', 'SysCrmUserTodoController@deleteMainTodo');
        Route::post('user-todo-restore/{id}', 'SysCrmUserTodoController@restoreMainTodo');
        Route::get('user-sub-todo-delete/{todo_id}/{todo_item_id}', 'SysCrmUserTodoController@deleteSubTodo');

        Route::get('crm-leads-report-company', 'SysCrmLeadsReportController@leadsreportcompany');
        Route::post('crm-leads-report-company', 'SysCrmLeadsReportController@leadsreportcompany');
        Route::get('crm-leads-report/{company_id}/{from_date}/{to_date}', 'SysCrmLeadsReportController@leadsreport');
        Route::post('crm-leads-report/{company_id}/{from_date}/{to_date}', 'SysCrmLeadsReportController@leadsreport');
        Route::get('crm-leads-staff-report/{staff_id}', 'SysCrmLeadsReportController@leadsreportsalesperson');
        Route::post('crm-leads-staff-report/{staff_id}', 'SysCrmLeadsReportController@leadsreportsalesperson');

        Route::post('crm-sales-req-add', 'SysCrmSupportController@salerequestsubmit');
        Route::post('crm-ps-service-request-add', 'SysCrmPSController@psservicerequestadd');


        //KUNAL

        Route::get('/ajax/get-new-lead-code', function (Request $request) {
            $table     = $request->get('table');
            $prefix    = $request->get('prefix');
            $column    = $request->get('column');
            $companyId = $request->get('company_id');

            if (!$table || !$prefix || !$column || !$companyId) {
                return response()->json(['error' => 'Missing parameters'], 400);
            }

            try {
                $newCode = \App\SysHelper::get_new_code_lead($table, $prefix, $column, $companyId);
                return response()->json(['new_code' => $newCode]);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
            }
        });


        //START GEO

        Route::post('crm-amc-track-submit', 'SysCrmAmcController@amctracksubmit');
        Route::post('crm-amc-track-id', 'SysCrmAmcController@amctrackid');
        Route::post('crm-amc-track-edit', 'SysCrmAmcController@amctrackedit');
        Route::get('crm-amc-track-list/{id}/delete', 'SysCrmAmcController@delete');
        Route::get('crm-amc-list-req', 'SysCrmAmcController@amctrackreqlist');
        Route::post('crm-amc-track-ids', 'SysCrmAmcController@amctrackid2');

        Route::post('crm-amc-service-track-id', 'SysCrmAmcController@amctrackserviceid');
        Route::post('crm-amc-service-track-edit', 'SysCrmAmcController@amctrackserviceedit');
        Route::get('crm-amc-service-track-list/{id}/delete', 'SysCrmAmcController@deleteservice');
        Route::post('crm-amc-service-track-ids', 'SysCrmAmcController@amcservicetrackid2');

        Route::post('crm-amc-track-add', 'SysCrmAmcController@amctrackadd');


        Route::post('outstanding_comment', 'SysReceivableOutstandingController@outstanding_comment');
        Route::post('outstanding_comment_save', 'SysReceivableOutstandingController@outstanding_comment_save');


        Route::post('outstanding_comment_payable', 'SysPayablesOutstandingController@outstanding_comment');
        Route::post('outstanding_comment_save_payable', 'SysPayablesOutstandingController@outstanding_comment_save');



        //END GEO



        Route::post('crm-deal-uploadimg', 'SysCrmDealTrackController@crmdealuploadimg');
        Route::get('crm-deal-uploadimg-view', 'SysCrmDealTrackController@crmdealuploadimgview');

        Route::post('crm-deal-return-submit', 'SysCrmDealReturnController@crmdealreturnsubmit');
        Route::get('crm-deal-return/{id}/view', 'SysCrmDealReturnController@view');
        Route::get('crm-deal-return-list', 'SysCrmDealReturnController@crmdealreturnlist');
        Route::post('crm-deal-return-collection', 'SysCrmDealReturnController@crmdealreturncollection');
        Route::post('crm-deal-return-sales', 'SysCrmDealReturnController@crmdealreturnsales');
        Route::post('crm-deal-return-payable', 'SysCrmDealReturnController@crmdealreturnpayable');


        Route::get('getdriverbyshipping', 'SysCrmDealTrackController@getdriverbyshipping');

        Route::post('crm-deal-track-approval-accounts', 'SysCrmDealTrackController@crmdealtrackapprovalaccounts');
        Route::post('crm-deal-track-approval-sales', 'SysCrmDealTrackController@crmdealtrackapprovalsales');
        Route::post('crm-deal-track-approval-purchease', 'SysCrmDealTrackController@crmdealtrackapprovalpurchease');
        Route::post('crm-deal-track-approval-invoice', 'SysCrmDealTrackController@crmdealtrackapprovalinvoice');
        Route::post('crm-deal-track-approval-delivery', 'SysCrmDealTrackController@crmdealtrackapprovaldelivery');
        Route::post('crm-deal-track-approval-receivables', 'SysCrmDealTrackController@crmdealtrackapprovalreceivables');
        Route::post('crm-deal-track-approval-receivables-payment-mode', 'SysCrmDealTrackController@crmdealtrackapprovalreceivablespaymentmode');
        Route::post('crm-deal-track-approval-receivables-payment-terms-mode', 'SysCrmDealTrackController@crmdealtrackapprovalreceivablespaymenttermsmode');
        Route::post('crm-deal-track-approval-professional-service', 'SysCrmDealTrackController@crmdealtrackapprovalprofessionalservice');

        Route::post('crm-deal-track-approval-invoice-update', 'SysCrmDealTrackController@crmdealtrackapprovalinvoiceupdate');
        Route::post('crm-deal-track-approval-accounts-update', 'SysCrmDealTrackController@crmdealtrackapprovalaccountsupdate');
        Route::post('crm-deal-track-approval-purchase-not-required', 'SysCrmDealTrackController@crm_deal_track_approval_purchase_not_required');


        Route::post('crm-customer-color', 'SysCrmDealTrackController@crmcustomercolor');

        Route::get('get-item-quote', 'SysCrmQuoteController@getitemquote');

        Route::group(['prefix' => 'quote'], function () {
            Route::get('/', ['as' => 'quote.index', 'uses' => 'SysCrmQuoteController@index']);
            Route::post('/', ['as' => 'quote.index', 'uses' => 'SysCrmQuoteController@index']);
            Route::get('/create', ['as' => 'quote.create', 'uses' => 'SysCrmQuoteController@create']);
            Route::post('/chooseitems', ['as' => 'quote.chooseitems', 'uses' => 'SysCrmQuoteController@chooseitems']);
            Route::get('/chooseitems', ['as' => 'quote.chooseitems', 'uses' => 'SysCrmQuoteController@chooseitems']);
            Route::get('/searchitems', ['as' => 'quote.searchitems', 'uses' => 'SysCrmQuoteController@searchitems']);
            Route::post('/searchitems', ['as' => 'quote.searchitems', 'uses' => 'SysCrmQuoteController@searchitems']);
            Route::post('/generatequote', ['as' => 'quote.generatequote', 'uses' => 'SysCrmQuoteController@generatequote']);
        });

        Route::post('crm-quote-additems', 'SysCrmQuoteController@additems');
        Route::post('crm-quote-updateitems', 'SysCrmQuoteController@updateitems');
        Route::post('crm-quote-deleteitems', 'SysCrmQuoteController@deleteitems');

        Route::post('crm-quote/{id}/download/{qid}', 'SysCrmQuoteController@quotedownload');
        Route::get('crm-quote/{id}/download/{qid}', 'SysCrmQuoteController@quotedownload');
        Route::get('crm-quote/{id}/downloadwp/{qid}', 'SysCrmQuoteController@quotedownloadwp');
        Route::get('crm-quote/{id}/downloadev/{qid}', 'SysCrmQuoteController@quotedownloadev');

        Route::get('crm-quote/{id}/setprimary/{qid}', 'SysCrmQuoteController@setprimary');
        Route::get('crm-quote/{id}/createcopy/{qid}', 'SysCrmQuoteController@createcopy');

        //Route::get('crm-quote/{id}/edit/{qid}', 'SysCrmQuoteController@quoteedit');
        //Route::post('crm-quote/{id}/edit/{qid}', 'SysCrmQuoteController@quoteedit');

        // new functions
        Route::get('crm-quote/{id}/create', 'SysCrmQuoteNewController@createquote');
        Route::post('crm-quote-add-items-cart', 'SysCrmQuoteNewController@crmquoteadditemscart');
        Route::get('crm-quote-cart/{id}/delete', 'SysCrmQuoteNewController@crmquotedeleteitemscart');
        Route::post('crm-quote-update-items-cart', 'SysCrmQuoteNewController@crmquoteupdateitemscart');
        Route::post('crm-quote-create', 'SysCrmQuoteNewController@savequote');
        Route::get('crm-quote/{id}/edit/{qid}', 'SysCrmQuoteNewController@quoteedit');
        Route::post('crm-quote/{id}/edit/{qid}', 'SysCrmQuoteNewController@quoteedit');
        Route::post('crm-quote-update-discount', 'SysCrmQuoteNewController@crmquoteupdate_discount');

        Route::post('crm-quote-add-items', 'SysCrmQuoteNewController@crmquoteadditems');
        Route::post('crm-quote-update', 'SysCrmQuoteNewController@crmquoteupdate');
        Route::post('crm-quote-update-currency', 'SysCrmQuoteNewController@crmquoteupdate_currency');
        Route::post('crm-quote-update-items', 'SysCrmQuoteNewController@crmquoteupdateitems');
        Route::get('crm-quote/{id}/delete', 'SysCrmQuoteNewController@crmquotedelete');
        Route::post('crm-quote-upload-excel-cart', 'SysCrmQuoteNewController@quote_upload_excel_cart');
        Route::post('crm-quote-upload-excel', 'SysCrmQuoteNewController@quote_upload_excel');
        Route::post('crm-update-quote-sort-order', 'SysCrmQuoteNewController@crm_update_quote_sort_order');




        Route::get('crm-quote/{id}/addnew/{qid}', 'SysCrmQuoteController@quoteaddnew');
        Route::post('crm-quote/{id}/addnew/{qid}', 'SysCrmQuoteController@quoteaddnew');
        Route::post('crm-quote-addnewbulkitemsedit', 'SysCrmQuoteController@addnewbulkitemsedit');
        Route::post('crm-quote-addnewproduct', 'SysCrmQuoteController@addnewproduct');

        Route::post('crm-quote-additemsedit', 'SysCrmQuoteController@additemsedit');
        Route::post('crm-quote-upditemsedit', 'SysCrmQuoteController@upditemsedit');
        Route::post('crm-quote-deleteitemsedit', 'SysCrmQuoteController@deleteitemsedit');

        Route::post('crm-quote-sort-up', 'SysCrmQuoteController@itemsortup');
        Route::post('crm-quote-sort-down', 'SysCrmQuoteController@itemsortdown');

        Route::post('crm-quote-addbulkitems', 'SysCrmQuoteController@addbulkitems');
        Route::post('crm-quote-addbulkitemsedit', 'SysCrmQuoteController@addbulkitemsedit');
        Route::post('crm-quote-update-payment-terms', 'SysCrmQuoteController@updatepaymentterms');
        Route::post('crm-quote-update-terms-condition', 'SysCrmQuoteController@updatetermsandcondition');
        Route::post('crm-quote-discount', 'SysCrmQuoteController@quotediscount');


        Route::get('crm-quote-cs/{id}/add', 'SysCrmQuoteCSController@quoteadd');
        Route::post('crm-quote-cs/{id}/add', 'SysCrmQuoteCSController@quoteadd');
        Route::post('crm-quote-cs-deleteitems', 'SysCrmQuoteCSController@deleteitems');
        Route::get('crm-quote-cs/{id}/download', 'SysCrmQuoteCSController@download');


        Route::post('crm-boq-create', 'SysCrmBoqController@create');
        Route::post('crm-boq-chooseitems', 'SysCrmBoqController@chooseitems');
        Route::get('crm-boq-items', 'SysCrmBoqController@items');

        Route::group(['prefix' => 'boq'], function () {
            Route::get('/items', ['as' => 'boq.items', 'uses' => 'SysCrmBoqController@items']);
            Route::post('/generatequote', ['as' => 'boq.generatequote', 'uses' => 'SysCrmBoqController@generatequote']);
        });

        Route::post('crm-boq-addtocart', 'SysCrmBoqController@addtocart');
        Route::post('crm-boq-addtocartgroup', 'SysCrmBoqController@addtocartgroup');
        Route::post('crm-boq-deltocart', 'SysCrmBoqController@deltocart');

        // CRM


        //inventory


        Route::post('item-remove-duplicate', 'SmItemController@remove_duplicate');


        Route::resource('item-category', 'SmItemCategoryController');

        Route::get('delete-item-category-view/{id}', 'SmItemCategoryController@deleteItemCategoryView');
        Route::get('delete-item-category/{id}', 'SmItemCategoryController@deleteItemCategory');
        Route::resource('item-list', 'SmItemController');
        Route::get('item-add', 'SmItemController@itemadd');
        Route::post('item-add', 'SmItemController@itemadd');
        Route::get('item-company-access-update', 'SmItemController@item_company_access_update');
        //Route::post('item-add-modal', 'SmItemController@itemadd_modal');
        Route::post('product/modalsave', 'SmItemController@itemadd_modal')->name('product.modalsave');


        Route::get('item-add/{id}/edit', 'SmItemController@itemedit');
        Route::get('delete-item-view/{id}', 'SmItemController@deleteItemView');
        Route::get('delete-item/{id}', 'SmItemController@deleteItem');
        Route::resource('item-store', 'SmItemStoreController');
        Route::get('item-store/{id}/delete', 'SmItemStoreController@delete');
        Route::post('product-merge', 'SmItemStoreController@productMerge');
        Route::post('product-merge-duplicate', 'SmItemStoreController@productMergeDuplicate');

        Route::get('product/get/{id}', 'SmItemController@getProductDetails');
        Route::post('/product/modalupdate', 'SmItemController@update_itemModal')->name('product.modalupdate');

        Route::post('item-store-update', 'SmItemStoreController@update');

        Route::post('item-store-additem', 'SmItemStoreController@additem');
        Route::post('item-store-updateitem', 'SmItemStoreController@updateitem');
        Route::post('item-store-deleteitem', 'SmItemStoreController@deleteitem');

        Route::get('item-store-import', 'SmItemStoreController@item_store_import');
        Route::post('item-store-import-list', 'SmItemStoreController@item_store_import_list');
        Route::post('item-store-import-data', 'SmItemStoreController@item_store_import_data');
        Route::get('item-store-import-clear', 'SmItemStoreController@item_store_import_clear');

        Route::post('add-stock-items-cart', 'SmItemStoreController@addstockitemscart');
        Route::post('delete-stock-items-cart', 'SmItemStoreController@deletestockitemscart');

        Route::get('product-import', 'SysProductImportController@index');
        Route::post('product-import-list', 'SysProductImportController@list');
        Route::post('product-import-data', 'SysProductImportController@import_data');
        Route::get('product-import-clear', 'SysProductImportController@list_clear');

        Route::get('stock-register', 'SmItemStoreController@stockregister');
        Route::post('stock-register', 'SmItemStoreController@stockregister');

        Route::get('stock-register-test', 'SmItemStoreController@stockregister_test');

        Route::get('get-stock-register-group-qty', 'SmItemStoreController@get_stock_register_group_qty');

        Route::get('stock-search', 'SmItemStoreController@stock_search');
        Route::post('stock-search', 'SmItemStoreController@stock_search');
        Route::get('customer-search', 'SysCustomerController@search')->name('customer-crm.search');

        Route::get('proforma-search', 'SysProformaInvoiceController@search')->name('proforma-crm.search');

        Route::get('list-price', 'SmItemStoreController@listprice');
        Route::post('list-price', 'SmItemStoreController@listprice');

        Route::get('delete-store-view/{id}', 'SmItemStoreController@deleteStoreView');
        Route::get('delete-store/{id}', 'SmItemStoreController@deleteStore');
        Route::resource('item-stock', 'SmItemStockController');

        Route::get('stock-ledger/{id?}', 'SysStockLedgerController@index');
        Route::post('stock-ledger', 'SysStockLedgerController@index');

        Route::get('inventory-report/{id?}', 'SysStockLedgerController@inventory_report');
        Route::post('inventory-report', 'SysStockLedgerController@inventory_report');

        //text-box auto compleate
        Route::post('autocomplete/fetch', 'SysStockLedgerController@fetch')->name('autocomplete.fetch');
        Route::post('autocomplete/fetch_name', 'SysStockLedgerController@fetch_name')->name('autocomplete.fetch_name');
        Route::post('autocomplete/fetch_deal_name', 'SysStockLedgerController@fetch_deal_name')->name('autocomplete.fetch_deal_name');
        Route::post('autocomplete/fetch_product_partnumber', 'SysStockLedgerController@fetch_product_partnumber')->name('autocomplete.fetch_product_partnumber');
        Route::post('autocomplete/fetch_product_partnumber_deal', 'SysStockLedgerController@fetch_product_partnumber_deal')->name('autocomplete.fetch_product_partnumber_deal');
        Route::post('autocomplete/fetch_product_partnumber_withcoma', 'SysStockLedgerController@fetch_product_partnumber_withcoma')->name('autocomplete.fetch_product_partnumber_withcoma');

        Route::get('autocomplete/get_product_list_ajax', 'SysStockLedgerController@get_product_list_ajax')->name('autocomplete.get_product_list_ajax');

        Route::resource('stock-in', 'SysStockInController');

        Route::get('stock-in-url/search', 'SysStockInController@search')->name('stock-in-url.search');
        Route::get('stock-out-url/search', 'SysStockOutController@search')->name('stock-out-url.search');
        Route::get('packing-list-url/search', 'SysPackingListController@search')->name('packing-list-url.search');
        Route::get('stock-in/{id}/view', 'SysStockInController@view');
        Route::post('stock-in-cart-add', 'SysStockInController@cart_add');
        Route::post('stock-in-cart-edit', 'SysStockInController@cart_edit');
        Route::post('stock-in-cart-update', 'SysStockInController@cart_update');
        Route::post('stock-in-cart-delete', 'SysStockInController@cart_delete');
        Route::post('stock-in-cart-excel-add', 'SysStockInController@cart_excel_add');

        Route::post('stock-in-items-add', 'SysStockInController@items_add');
        Route::post('stock-in-items-update', 'SysStockInController@items_update');
        Route::post('stock-in-items-delete', 'SysStockInController@items_delete');

        Route::get('stock-in-import', 'SysStockInController@stock_import');
        Route::post('stock-in-import-list', 'SysStockInController@stock_import_list');
        Route::post('stock-in-import-data', 'SysStockInController@stock_import_data');
        Route::get('stock-in-import-clear', 'SysStockInController@stock_import_clear');

        Route::resource('stock-out', 'SysStockOutController');
        Route::get('stock-out/{id}/view', 'SysStockOutController@view');
        Route::post('stock-out-cart-add', 'SysStockOutController@cart_add');
        Route::post('stock-out-cart-edit', 'SysStockOutController@cart_edit');
        Route::post('stock-out-cart-update', 'SysStockOutController@cart_update');
        Route::post('stock-out-cart-delete', 'SysStockOutController@cart_delete');
        Route::post('stock-out-cart-excel-add', 'SysStockOutController@cart_excel_add');

        Route::post('stock-out-items-add', 'SysStockOutController@items_add');
        Route::post('stock-out-items-update', 'SysStockOutController@items_update');
        Route::post('stock-out-items-delete', 'SysStockOutController@items_delete');

        Route::get('stock-out-import', 'SysStockOutController@stock_import');
        Route::post('stock-out-import-list', 'SysStockOutController@stock_import_list');
        Route::post('stock-out-import-data', 'SysStockOutController@stock_import_data');
        Route::get('stock-out-import-clear', 'SysStockOutController@stock_import_clear');

        Route::post('stock-out-getsrl', ['as' => 'stock-out-getsrl', 'uses' => 'SysStockOutController@stockoutgetsrl']);

        Route::resource('packing-list', 'SysPackingListController');
        Route::post('packing-list-cart-add', 'SysPackingListController@cart_add');
        Route::post('packing-list-cart-edit', 'SysPackingListController@cart_edit');
        Route::post('packing-list-cart-update', 'SysPackingListController@cart_update');
        Route::post('packing-list-cart-delete', 'SysPackingListController@cart_delete');
        Route::get('packing-list/{id}/download', 'SysPackingListController@download');
        Route::get('packing-list/{id}/view', 'SysPackingListController@view');

        Route::post('packing-list-items-add', 'SysPackingListController@items_add');
        Route::post('packing-list-items-update', 'SysPackingListController@items_update');
        Route::post('packing-list-items-delete', 'SysPackingListController@items_delete');

        Route::get('item-receive', 'SmItemReceiveController@itemReceive');
        Route::post('item-receive/store', 'SmItemReceiveController@itemReceiveStore');
        Route::get('item-receive/edit/{id}', 'SmItemReceiveController@itemReceiveEdit');
        Route::post('item-receive/update', 'SmItemReceiveController@itemReceiveUpdate');
        Route::get('item-receive/delete/{id}', 'SmItemReceiveController@itemReceiveDelete');

        Route::get('get-receive-item', 'SmItemReceiveController@getReceiveItem');
        Route::get('get-receive-item-tender', 'SmItemReceiveController@getReceiveItemTender');
        Route::get('get-receive-item-details', 'SmItemReceiveController@getReceiveItemDetails');

        Route::post('save-item-receive-data', 'SmItemReceiveController@saveItemReceiveData');
        Route::get('item-receive-list', 'SmItemReceiveController@itemReceiveList');
        Route::get('edit-item-receive/{id}', 'SmItemReceiveController@editItemReceive');
        Route::post('update-edit-item-receive-data/{id}', 'SmItemReceiveController@updateItemReceiveData');
        Route::post('delete-receive-item', 'SmItemReceiveController@deleteReceiveItem');
        Route::get('view-item-receive/{id}', 'SmItemReceiveController@viewItemReceive');
        Route::get('add-payment/{id}', 'SmItemReceiveController@itemReceivePayment');
        Route::post('save-item-receive-payment', 'SmItemReceiveController@saveItemReceivePayment');
        Route::get('view-receive-payments/{id}', 'SmItemReceiveController@viewReceivePayments');
        Route::post('delete-receive-payment', 'SmItemReceiveController@deleteReceivePayment');
        Route::get('delete-item-receive-view/{id}', 'SmItemReceiveController@deleteItemReceiveView');
        Route::get('delete-item-receive/{id}', 'SmItemReceiveController@deleteItemReceive');
        Route::get('cancel-item-receive-view/{id}', 'SmItemReceiveController@cancelItemReceiveView');
        Route::get('cancel-item-receive/{id}', 'SmItemReceiveController@cancelItemReceive');

        // Item Sell in inventory
        Route::get('item-sell-list', 'SmItemSellController@itemSellList');
        Route::get('item-sell', 'SmItemSellController@itemSell');
        Route::get('item-sell', 'SmItemSellController@itemSell');
        Route::post('save-item-sell-data', 'SmItemSellController@saveItemSellData');

        Route::post('check-product-quantity', 'SmItemSellController@checkProductQuantity');
        Route::get('edit-item-sell/{id}', 'SmItemSellController@editItemSell');

        Route::post('update-item-sell-data', 'SmItemSellController@UpdateItemSellData');

        Route::get('item-issue', 'SmItemSellController@itemIssueList');
        Route::post('save-item-issue-data', 'SmItemSellController@saveItemIssueData');
        Route::get('getItemByCategory', 'SmItemSellController@getItemByCategory');
        Route::get('return-item-view/{id}', 'SmItemSellController@returnItemView');
        Route::get('return-item/{id}', 'SmItemSellController@returnItem');

        Route::get('view-item-sell/{id}', 'SmItemSellController@viewItemSell');

        Route::get('add-payment-sell/{id}', 'SmItemSellController@itemSellPayment');
        Route::post('save-item-sell-payment', 'SmItemSellController@saveItemSellPayment');

        /**********************-suppliers *********************************** */
        // Route::resource('suppliers', 'SmSupplierController');
        // Route::get('delete-supplier-view/{id}', 'SmSupplierController@deleteSupplierView');
        // Route::get('delete-supplier/{id}', 'SmSupplierController@deleteSupplier');

        // Route::get('add-supplier', 'SmSupplierController@addsupplier');


        /********************** -suppliers *********************************** */

        /********************** enlisted-suppliers *********************************** */
        Route::resource('enlisted-suppliers', 'SmEnlistedSupplierController');
        Route::get('get-enlisted-suppliers', 'SmEnlistedSupplierController@ajaxSearchEnlistedSupplier');
        Route::get('delete-enlisted-suppliers-view/{id}', 'SmEnlistedSupplierController@deleteSupplierView');
        Route::get('delete-enlisted-suppliers/{id}', 'SmEnlistedSupplierController@deleteSupplier');
        /********************** enlisted-suppliers *********************************** */

        /********************** inspecting-department *********************************** */
        Route::resource('inspecting-department', 'SmInspectingDepartmentController');
        Route::get('get-inspecting-department', 'SmInspectingDepartmentController@ajaxSearchInspectingDepartment');
        Route::get('delete-inspecting-department-view/{id}', 'SmInspectingDepartmentController@deleteInspectingDepartmentView');
        Route::get('delete-inspecting-department/{id}', 'SmInspectingDepartmentController@deleteInspectingDepartment');
        /********************** inspecting-department *********************************** */

        Route::get('view-sell-payments/{id}', 'SmItemSellController@viewSellPayments');

        Route::post('delete-sell-payment', 'SmItemSellController@deleteSellPayment');
        Route::get('cancel-item-sell-view/{id}', 'SmItemSellController@cancelItemSellView');
        Route::get('cancel-item-sell/{id}', 'SmItemSellController@cancelItemSell');

        //library member
        Route::resource('library-member', 'SmLibraryMemberController');
        Route::get('cancel-membership/{id}', 'SmLibraryMemberController@cancelMembership');

        // Sms Settings
        Route::get('sms-settings', 'SmSystemSettingController@smsSettings');
        Route::post('update-clickatell-data', 'SmSystemSettingController@updateClickatellData');
        Route::post('update-twilio-data', 'SmSystemSettingController@updateTwilioData');
        Route::post('update-msg91-data', 'SmSystemSettingController@updateMsg91Data');
        Route::post('activeSmsService', 'SmSystemSettingController@activeSmsService');

        //Language Setting
        Route::get('language-setup/{id}', 'SmSystemSettingController@languageSetup');
        Route::get('language-settings', 'SmSystemSettingController@languageSettings');
        Route::post('language-add', 'SmSystemSettingController@languageAdd');

        Route::post('/language-change', 'SmSystemSettingController@ajaxLanguageChange');

        Route::get('language-edit/{id}', 'SmSystemSettingController@languageEdit');
        Route::post('language-update', 'SmSystemSettingController@languageUpdate');

        Route::post('language-delete', 'SmSystemSettingController@languageDelete');

        Route::get('get-translation-terms', 'SmSystemSettingController@getTranslationTerms');
        Route::post('translation-term-update', 'SmSystemSettingController@translationTermUpdate');

        //Backup Setting
        Route::post('backup-store', 'SmSystemSettingController@BackupStore');
        Route::get('backup-settings', 'SmSystemSettingController@backupSettings');
        Route::get('get-backup-files/{id}', 'SmSystemSettingController@getfilesBackup');
        Route::get('get-backup-db', 'SmSystemSettingController@getDatabaseBackup');
        Route::get('download-database/{id}', 'SmSystemSettingController@downloadDatabase');
        Route::get('download-files/{id}', 'SmSystemSettingController@downloadFiles');
        Route::get('restore-database/{id}', 'SmSystemSettingController@restoreDatabase');
        Route::get('delete-database/{id}', 'SmSystemSettingController@deleteDatabase')->name('delete_database');

        //Update System
        Route::get('update-system', 'SmSystemSettingController@UpdateSystem');
        Route::any('upgrade-settings', 'SmSystemSettingController@UpgradeSettings');

        //Route::get('sendSms','SmSmsTestController@sendSms');
        //Route::get('sendSmsMsg91','SmSmsTestController@sendSmsMsg91');
        //Route::get('sendSmsClickatell','SmSmsTestController@sendSmsClickatell');

        //Settings
        Route::get('general-settings', 'SmSystemSettingController@generalSettingsView');
        Route::get('update-general-settings', 'SmSystemSettingController@updateGeneralSettings');
        Route::post('update-general-settings-data', 'SmSystemSettingController@updateGeneralSettingsData');
        Route::post('update-school-logo', 'SmSystemSettingController@updateSchoolLogo');

        //Email Settings
        Route::get('email-settings', 'SmSystemSettingController@emailSettings');
        Route::post('update-email-settings-data', 'SmSystemSettingController@updateEmailSettingsData');

        // payment Method Settings
        // Route::get('payment-method-settings', 'SmSystemSettingController@paymentMethodSettings');
        Route::post('update-paypal-data', 'SmSystemSettingController@updatePaypalData');
        Route::post('update-stripe-data', 'SmSystemSettingController@updateStripeData');
        Route::post('update-payumoney-data', 'SmSystemSettingController@updatePayumoneyData');
        Route::post('active-payment-gateway', 'SmSystemSettingController@activePaymentGateway');

        //Email Settings
        Route::get('email-settings', 'SmSystemSettingController@emailSettings');
        Route::post('update-email-settings-data', 'SmSystemSettingController@updateEmailSettingsData');

        // payment Method Settings
        Route::get('payment-method-settings', 'SmSystemSettingController@paymentMethodSettings');
        Route::post('update-payment-gateway', 'SmSystemSettingController@updatePaymentGateway');
        Route::post('is-active-payment', 'SmSystemSettingController@isActivePayment');
        //Route::get('stripeTest', 'SmSmsTestController@stripeTest');
        //Route::post('stripe_post', 'SmSmsTestController@stripePost');

        //Collect fees By Online Payment Gateway(Paypal)
        Route::get('collect-fees-gateway/{amount}/{student_id}/{type}', 'SmCollectFeesByPaymentGateway@collectFeesByGateway');
        Route::post('payByPaypal', 'SmCollectFeesByPaymentGateway@payByPaypal');
        Route::get('paypal-return-status', 'SmCollectFeesByPaymentGateway@getPaymentStatus');

        //Collect fees By Online Payment Gateway(Stripe)
        Route::get('collect-fees-stripe/{amount}/{student_id}/{type}', 'SmCollectFeesByPaymentGateway@collectFeesStripe');
        Route::post('collect-fees-stripe-strore', 'SmCollectFeesByPaymentGateway@stripeStore');

        // To Do list

        //Route::get('stripeTest', 'SmSmsTestController@stripeTest');
        //Route::post('stripe_post', 'SmSmsTestController@stripePost');

        // background setting
        Route::get('background-setting', 'SmBackgroundController@index');
        Route::post('background-settings-update', 'SmBackgroundController@backgroundSettingsUpdate');
        Route::post('background-settings-store', 'SmBackgroundController@backgroundSettingsStore');
        Route::get('background-setting-delete/{id}', 'SmBackgroundController@backgroundSettingsDelete');
        Route::get('background_setting-status/{id}', 'SmBackgroundController@backgroundSettingsStatus');


        // advance or loan
        Route::get('add-loan', 'SmAdvanceloanController@addLoanCreate');
        Route::get('loan-edit/{id}', 'SmAdvanceloanController@loanEdit');
        Route::post('loan-store', 'SmAdvanceloanController@loanStore');
        Route::get('loan-list', 'SmAdvanceloanController@loanList');
        Route::get('loan-view/{id}', 'SmAdvanceloanController@loanView');
        Route::post('loan-update', 'SmAdvanceloanController@loanUpdate');
        Route::get('loan-delete/{id}', 'SmAdvanceloanController@loanDelete');


        // ticket sytem
        Route::get('ticket-category', 'SmTicketController@category')->name('ticket.category');
        Route::post('ticket-category', 'SmTicketController@category_store')->name('ticket.category_store');
        Route::get('ticket-category-edit/{id}', 'SmTicketController@category_edit')->name('ticket.category_edit');
        Route::post('ticket-category-update/{id}', 'SmTicketController@category_update')->name('ticket.category_update');
        Route::get('ticket-category-delete-view/{id}', 'SmTicketController@category_delete_view')->name('ticket.category_delete_view');
        Route::get('ticket-category-delete/{id}', 'SmTicketController@category_delete')->name('ticket.category_delete');

        Route::get('ticket-priority', 'SmTicketController@priority')->name('ticket.priority');
        Route::post('ticket-priority', 'SmTicketController@priority_store')->name('ticket.priority_store');
        Route::get('ticket-priority-edit/{id}', 'SmTicketController@priority_edit')->name('ticket.priority_edit');
        Route::post('ticket-priority-update/{id}', 'SmTicketController@priority_update')->name('ticket.priority_update');
        Route::get('ticket-priority-delete-view/{id}', 'SmTicketController@priority_delete_view')->name('ticket.priority_delete_view');
        Route::get('ticket-priority-delete/{id}', 'SmTicketController@priority_delete')->name('ticket.priority_delete');
        //ticket
        Route::get('admin/ticket-view/', 'SmTicketController@index')->name('admin.ticket_list');
        route::get('admin/ticket-view/{id}', 'SmTicketController@ticket_view')->name('admin.ticket_view');

        route::get('admin/add-ticket', 'SmTicketController@add_ticket')->name('admin.add_ticket');
        route::post('admin/ticket-store', 'SmTicketController@ticket_store')->name('admin.ticket_store');
        route::get('admin/ticket-edit/{id}', 'SmTicketController@ticket_edit')->name('admin.ticket_edit');
        route::post('admin/ticket-update/{id}', 'SmTicketController@ticket_update')->name('admin.ticket_update');
        route::get('admin/ticket-delete-view/{id}', 'SmTicketController@ticket_delete_view')->name('admin.ticket_delete_view');
        route::get('admin/ticket-delete/{id}', 'SmTicketController@ticket_delete')->name('admin.ticket_delete');
        route::post('admin/ticket-search/', 'SmTicketController@ticket_search')->name('admin.ticket_search');

        //comment
        Route::post('admin/comment-store', 'SmTicketController@comment_store')->name('admin.comment_store');
        Route::post('admin/comment-reply', 'SmTicketController@comment_reply')->name('admin.comment_reply');
        Route::get('download-file/{id}', 'SmTicketController@download_file')->name('download_file');


        Route::get('income-statement', 'SmReportController@incomeStatement');
        Route::post('income-statement', 'SmReportController@incomeStatementSearch');
        Route::get('upload-staff-documents/{id}', 'SmStaffController@uploadStaffDocuments');
        Route::post('save_upload_document', 'SmStaffController@saveUploadDocument');
        Route::get('download-staff-document/{file_name}', function ($file_name = null) {
            $file = public_path() . '/uploads/staff/document/' . $file_name;
            if (file_exists($file)) {
                return Response::download($file);
            }
        });

        // ledger report 

        Route::get('ledger-report', 'SmReportController@ledgerReport');
        Route::post('ledger-report', 'SmReportController@ledgerReportSearch');


        // bank book
        Route::get('bank-book', 'SmReportController@bankBook');
        Route::post('bank-book', 'SmReportController@bankBookSearch');


        // Purchase report
        Route::get('purchase-report', 'SmReportController@purchaseReport');
        Route::post('purchase-report', 'SmReportController@purchaseReportSearch');


        // Purchase report
        Route::get('sales-report', 'SmReportController@salesReport');
        Route::post('sales-report', 'SmReportController@salesReportSearch');



        Route::get('investment-report', 'SmInvestmentController@investmentReport');


        Route::get('transfer', 'SmInvestmentController@transfer');
        Route::post('transfer-store', 'SmInvestmentController@transferStore');
        Route::get('transfer-edit/{id}', 'SmInvestmentController@transferEdit');
        Route::post('transfer-update', 'SmInvestmentController@transferUpdate');
        Route::get('transfer-delete/{id}', 'SmInvestmentController@transferDelete');

        Route::get('investment', 'SmInvestmentController@index');
        Route::post('investment-store', 'SmInvestmentController@store');
        Route::get('investment-edit/{id}', 'SmInvestmentController@edit');
        Route::post('investment-update', 'SmInvestmentController@update');
        Route::get('investment-delete/{id}', 'SmInvestmentController@delete');



        Route::post('invesment-search', 'SmInvestmentController@invesmentSearch');
    });
    Route::get('download-comment-document/{file_name}', function ($file_name = null) {
        $file = public_path() . '/uploads/comment/' . $file_name;
        if (file_exists($file)) {
            return Response::download($file);
        }
    });
    //customer panel

    Route::group(['middleware' => ['CustomerMiddleware']], function () {
        Route::get('customer-dashboard', ['as' => 'customer_dashboard', 'uses' => 'Customer\SmCustomerPanelController@customerDashboard']);
        Route::get('customer-purchases', 'Customer\SmCustomerPanelController@customerPurchases');
    });

    //Install for Demo
    Route::get('/verified-code', 'InstallController@verifiedCode');
    Route::post('/verified-code', 'InstallController@verifiedCodeStore');

    Route::get('install', 'InstallController@index');
    Route::get('check-purchase-verification', 'InstallController@CheckPurchaseVerificationPage');
    Route::post('check-verified-input', 'InstallController@CheckVerifiedInput');
    Route::get('check-environment', 'InstallController@checkEnvironmentPage');
    Route::any('checking-environment', 'InstallController@checkEnvironment');
    Route::get('system-setup-page', 'InstallController@systemSetupPage');
    Route::post('confirm-installing', 'InstallController@confirmInstalling');
    Route::get('confirmation', 'InstallController@confirmation');

    Route::get('/install2', 'InstallController@installPage2'); // if verified, then success message & database credentials page
    Route::get('/install4', 'InstallController@installPage4');
    Route::post('/installStep2', 'InstallController@installStep2')->name('installStep2');

    //for localization
    Route::get('locale/{locale}', 'SmSystemSettingController@changeLocale');

    Route::post('/installStep4', 'InstallController@installStep4')->name('installStep4');

    //for localization
    Route::get('locale/{locale}', 'SmSystemSettingController@changeLocale');
    Route::get('change-language/{id}', 'SmSystemSettingController@changeLanguage');

    /************* Verify Routes *************/
    Route::get('/verify/', 'VerifyController@index');

    Route::put('/verify/storePurchasecode/{id}', 'VerifyController@storePurchasecode');

    Route::put('/verify/storePurchasecode/{id}', 'VerifyController@storePurchasecode');

    /************* Front End Settings *************/
    Route::get('/news', 'SmNewsController@index')->name('news_index');
    Route::post('/news-store', 'SmNewsController@store')->name('store_news');
    Route::post('/news-update', 'SmNewsController@update')->name('update_news');
    Route::get('newsDetails/{id}', 'SmNewsController@newsDetails');
    Route::get('for-delete-news/{id}', 'SmNewsController@forDeleteNews');
    Route::get('delete-news/{id}', 'SmNewsController@delete');
    Route::get('edit-news/{id}', 'SmNewsController@edit');

    Route::get('news-category', 'SmNewsController@newsCategory');
    Route::post('/news-category-store', 'SmNewsController@storeCategory')->name('store_news_category');
    Route::post('/news-category-update', 'SmNewsController@updateCategory')->name('update_news_category');
    Route::get('for-delete-news-category/{id}', 'SmNewsController@forDeleteNewsCategory');
    Route::get('delete-news-category/{id}', 'SmNewsController@deleteCategory');
    Route::get('edit-news-category/{id}', 'SmNewsController@editCategory');

    //For course module
    Route::get('course-list', 'SmCourseController@index');
    Route::post('/course-store', 'SmCourseController@store')->name('store_course');
    Route::post('/course-update', 'SmCourseController@update')->name('update_course');
    Route::get('for-delete-course/{id}', 'SmCourseController@forDeleteCourse');
    Route::get('delete-course/{id}', 'SmCourseController@destroy');
    Route::get('edit-course/{id}', 'SmCourseController@edit');
    Route::get('course-Details-admin/{id}', 'SmCourseController@courseDetails');

    //for testimonial

    Route::get('/testimonial', 'SmTestimonialController@index')->name('testimonial_index');
    Route::post('/testimonial-store', 'SmTestimonialController@store')->name('store_testimonial');
    Route::post('/testimonial-update', 'SmTestimonialController@update')->name('update_testimonial');
    Route::get('testimonial-details/{id}', 'SmTestimonialController@testimonialDetails');
    Route::get('for-delete-testimonial/{id}', 'SmTestimonialController@forDeleteTestimonial');
    Route::get('delete-testimonial/{id}', 'SmTestimonialController@delete');
    Route::get('edit-testimonial/{id}', 'SmTestimonialController@edit');

    // Contact us
    Route::get('contact-page', 'SmFrontendController@conpactPage');
    Route::get('contact-page/edit', 'SmFrontendController@contactPageEdit');
    Route::post('contact-page/update', 'SmFrontendController@contactPageStore');

    // contact message
    Route::get('contact-message', 'SmFrontendController@contactMessage');

    // About us
    Route::get('about-page', 'SmFrontendController@aboutPage');
    Route::get('about-page/edit', 'SmFrontendController@aboutPageEdit');
    Route::post('about-page/update', 'SmFrontendController@aboutPageStore');

    Route::post('send-message', 'SmFrontendController@sendMessage');


    Route::post('logout', 'Auth\LoginController@logout')->name('logout');
    Route::group(['middleware' => ['User'], 'namespace' => 'Ticket'], function () {
        route::get('user-dashboard', 'SmUserController@index')->name('user.dashboard');
        route::get('tickets', 'SmUserController@tickets')->name('user.ticket');
        route::get('ticket-view/{id}', 'SmUserController@ticket_view')->name('user.ticket_view');
        route::get('add-ticket', 'SmUserController@add_ticket')->name('user.add_ticket');
        route::post('ticket-store', 'SmUserController@ticket_store')->name('user.ticket_store');
        route::get('ticket-edit/{id}', 'SmUserController@ticket_edit')->name('user.ticket_edit');
        route::post('ticket-update/{id}', 'SmUserController@ticket_update')->name('user.ticket_update');
        route::get('ticket-delete-view/{id}', 'SmUserController@ticket_delete_view')->name('user.ticket_delete_view');
        route::get('ticket-delete/{id}', 'SmUserController@ticket_delete')->name('user.ticket_delete');
        route::get('ticket-reopen/{id}', 'SmUserController@reopen_ticket')->name('user.reopen_ticket');
        route::get('ticket-active', 'SmUserController@active_ticket')->name('user.active_ticket');
        route::get('ticket-complete', 'SmUserController@complete_ticket')->name('user.completed_ticket');

        //comment
        Route::post('comment-store', 'SmUserController@comment_store')->name('user.comment_store');
        Route::post('comment-reply', 'SmUserController@comment_reply')->name('user.comment_reply');
    });
    Route::group(['prefix' => 'ticket', 'as' => 'ticket.', 'namespace' => 'Ticket'], function () {
        Route::get('login', 'LoginController@ticket_login')->name('ticket_login');
        Route::get('register', 'RegisterController@ticket_register')->name('ticket_register');
        Route::post('register', 'RegisterController@register')->name('register');
        Route::post('logout', 'LoginController@logout')->name('logout');
        //staff
        Route::get('profile-edit/{id}', 'SmTicketController@customerEdit')->name('edit_profile');
        Route::post('profile-update', 'SmTicketController@customerUpdate');
        Route::get('view-profile/{id}', 'SmTicketController@viewCustomer')->name('view_profile');
    });
    Route::group(['middleware' => 'auth'], function () {
        route::post('admin/ticket-show/', 'SmNotificationController@ticket_show')->name('admin.ticket_show');
    });
});

Route::get('/ccerp', function () {
    Artisan::call('optimize:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('clear-compiled');
    return "Cleared!!";
});
