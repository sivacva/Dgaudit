<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MastersController;
use App\Models\UserManagementModel;
use App\Models\MastersModel;

//berlin
use App\Http\Controllers\LoginController;
use App\Models\UserModel;
use App\Http\Controllers\AccountSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


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
//----------------------------------------------------------------------Policy Start------------------------------------------------------------------------------

Route::get('disclaimer', function () {
    return view('layouts/disclaimer');
});
Route::get('terms', function () {
    return view('layouts/terms');
});
Route::get('privacy', function () {
    return view('layouts/privacy');
});
Route::get('notification', function () {
    return view('layouts/notification');
});
Route::get('screenreader', function () {
    return view('layouts/screenreader');
});

//----------------------------------------------------------------------Policy End------------------------------------------------------------------------------


//----------------------------------------------------------------------DASHBOARD------------------------------------------------------------------------------
// Route::middleware('check.session')->get('/dashboard', [AccountSettings::class, 'dynamic_modal'])->name('dashboard');

// Route::middleware('check.session')->get('/dashboard', function () {
//     return view('dashboard.dashboard');
// })->name('dashboard');

// Route::middleware('check.session')->get('/dashboard', [AccountSettings::class, 'dynamic_modal'])->name('dashboard')->defaults('viewName', 'dashboard.dashboard');


Route::middleware('check.session')->get('/dashboard', [App\Http\Controllers\DashboardController::class, 'Get_dept'])->name('dashboard');

Route::post('CallingData', [App\Http\Controllers\DashboardController::class, 'CallingData'])->name('CallingData');

Route::get('/sentdetails', [App\Http\Controllers\DashboardController::class, 'sentDetails'])->name('sentdetails');

Route::get('/descriptionData', [App\Http\Controllers\DashboardController::class, 'descriptionData'])->name('descriptionData');

Route::middleware('check.session')->get('/auditeedashboard', [App\Http\Controllers\DashboardController::class, 'auditee_dashboardcount'])->name('auditeedashboard');

Route::post('load_institute_details', [App\Http\Controllers\DashboardController::class, 'InstitutedetailsGet'])->name('load_institute_details')->defaults('viewName', 'audit.load_institute_details');

Route::post('getauditslipdetails', [App\Http\Controllers\DashboardController::class, 'getauditslipdetails'])->name('getauditslipdetails')->defaults('viewName', 'audit.getauditslipdetails');

Route::get('/ajax/deptwise-data', [App\Http\Controllers\DashboardController::class, 'DeptWiseAjax'])->name('deptwisedata.get');

Route::post('load_commenced_institute_details', [App\Http\Controllers\DashboardController::class, 'CommencedInstitutedetailsGet'])->name('load_commenced_institute_details')->defaults('viewName', 'audit.load_institute_details');

Route::post('load_regiondata', [App\Http\Controllers\DashboardController::class, 'RegionwiseDetails'])->name('load_regiondata')->defaults('viewName', 'audit.load_regiondata');

Route::post('load_districtdata', [App\Http\Controllers\DashboardController::class, 'DistrictwiseDetails'])->name('load_districtdata')->defaults('viewName', 'audit.load_districtdata');




// Route::middleware('check.session')->get('/auditeedashboard', function () {
//     return view('dashboard.auditeedashboard');
// // })->name('auditeedashboard');
// Route::middleware('check.session')->get('/auditeedashboard', [AccountSettings::class, 'dynamic_modal'])->name('auditeedashboard')->defaults('viewName', 'dashboard.auditeedashboard');

// Route::middleware('check.session')->get('/auditeedashboard', [AccountSettings::class, 'dynamic_modal'])->name('auditeedashboard')->defaults('viewName', 'dashboard.auditeedashboardd');;



Route::get('/mappingworkallocation', [MastersController::class, 'mappingworkallocationdeptfetch'])->name('mappingworkallocation');;
// Route::post('getworkallocationBasedOnDept', [MastersController::class, 'getworkallocationBasedOnDept'])
// ->name('getworkallocationBasedOnDept');
Route::post('/mappingobjection/subobjectionupdate_mapping', [MastersController::class, 'subobjectionupdate_mapping'])
->name('mappingobjection.subobjectionupdate_mapping');

Route::get('/mappingobjection', [MastersController::class, 'mappingobjectiondeptfetch'])->name('mappingobjection');;
// Route::post('getworkallocationBasedOnDept', [MastersController::class, 'getworkallocationBasedOnDept'])
// ->name('getworkallocationBasedOnDept');
Route::post('/subworkallocation/subworkallocation_mapping', [MastersController::class, 'subworkallocation_mapping'])
->name('subworkallocation.subworkallocation_mapping');



//--------------------------------------------Role Type Mapping--------------------------------------------------------------
Route::get('/roletypemapping', [MastersController::class, 'DeptandRoletypeFetchForRoletype']);

Route::post('/roletypemapping/roletypemapping_insertupdate', [MastersController::class, 'roletypemapping_insertupdate'])
    ->name('roletypemapping.roletypemapping_insertupdate');


Route::post('/roletypemapping/roletypemapping_fetchData', [MastersController::class, 'roletypemapping_fetchData'])
    ->name('roletypemapping.roletypemapping_fetchData');

Route::get('/error-page', function (Request $request) {
    // Get user data from the session
    $userData = session('user');
    if ($userData) {
        $loginid = $userData->loginid;

        // Update the user's status and logout time in the database
        DB::table('audit.userlogindetails')
            ->where('loginid', $loginid) // Condition to identify the record
            ->update([
                'activestatus' => 'N',  // Field and new value
                'logouttime' => now(),   // Use `now()` to get the current timestamp
            ]);

        // Log out the user if authenticated
        if (Auth::check()) {
            Auth::logout();
        }

        // Clear all session data and invalidate the session
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Return the error page view
        return view('site.error');
    } else {
        // If no user data is found, redirect to the homepage
        return redirect('/');
    }
})->name('error');


Route::get('/login', function () {
    return view('site/login');
});


//--------------------------------------------Role Type Mapping--------------------------------------------------------------
Route::get('/roletypemapping', [MastersController::class, 'DeptandRoletypeFetchForRoletype']);

Route::post('/roletypemapping/roletypemapping_insertupdate', [MastersController::class, 'roletypemapping_insertupdate'])
    ->name('roletypemapping.roletypemapping_insertupdate');


Route::post('/roletypemapping/roletypemapping_fetchData', [MastersController::class, 'roletypemapping_fetchData'])
    ->name('roletypemapping.roletypemapping_fetchData');
//





//-----------------------------AUDITEE USERDETAILS-----------------------------------------------------------------

Route::get('/auditeeuserdetails', [MastersController::class, 'auditee_deptfetch']);

Route::post('/getregionbasedondept', [MastersController::class, 'getregionbasedondept']);
Route::post('/getdistrictbasedonregion', [MastersController::class, 'getdistrictbasedonregion']);
Route::post('/getinstitutionbasedondist', [MastersController::class, 'getinstitutionbasedondist']);

Route::post('/auditeeuserdetails_insertupdate', [MastersController::class, 'auditeeuserdetails_insertupdate'])
    ->name('auditeeuserdetails.auditeeuserdetails_insertupdate');

Route::post('/master/auditeeuserdetails_fetchData', [MastersController::class, 'auditeeuserdetails_fetchData'])
    ->name('auditeeuserdetails.auditeeuserdetails_fetchData');

//--------------------------Map call for records----------------------------------------------------------------------------

Route::get('/callforrecords', [MastersController::class, 'fetchdeptAndRecords']);
// Route::get('/callforrecords', [MastersController::class, 'getcategoryByDept']);


Route::post('/getcategoriesbasednndept', [MastersController::class, 'getCategoriesBasedOnDept']);
Route::post('/getCallforrecordsbasednndept', [MastersController::class, 'getCallforrecordsBasedOnDept']);

Route::post('/mapcallforrecords_insertupdate', [MastersController::class, 'mapcallforrecords_insertupdate'])
    ->name('mapcallforrecords.mapcallforrecords_insertupdate');
Route::post('/master/mapcallforrecords_fetchData', [MastersController::class, 'mapcallforrecords_fetchData'])
    ->name('mapcallforrecords.mapcallforrecords_fetchData');

//-----------------Call for records  Start -------------------------

// Route::get('/callforrecord', function () {
//     $dept  =   UserManagementModel::deptdetail();
//     return view('masters.createcallforrecords', compact('dept'));
// });

// Route::post('/get-category', [MastersController::class, 'getCategoryByDept'])->name('get.category');
// Route::post('/callfor/callforrecords_insertupdate', [MastersController::class, 'callforrecords_insertupdate'])->name('callfor.callforrecords_insertupdate');
// Route::post('/callfor/callforrecords_fetchData', [MastersController::class, 'callforrecords_fetchData'])->name('callfor.callforrecords_fetchData');


//-----------------call for records end -------------------

//master

/********************************************************************Create Map Allocation Objection - URL ******************************************************************* */

Route::get('create_map_allc_obj', function () {
    return app()->call('App\Http\Controllers\MastersController@mapAllcObj_dropdown', ['index' => 'masters.createmapallcobj']);
})->name('create_map_allc_obj');

Route::post('/masters/insertmulti_mapWorkObj', [App\Http\Controllers\MastersController::class, 'insertmulti_mapWorkObj'])->name('masters.createmapallcobj');
Route::post('/masters/check_mapAllcObj', [App\Http\Controllers\MastersController::class, 'check_mapAllcObj'])->name('masters.createmapallcobj');


Route::get('map_allc_obj', function () {
    return app()->call('App\Http\Controllers\MastersController@mapAllcObj_dropdown', ['index' => 'masters.mapallocationobjection']);
})->name('map_allc_obj');

// Route::get('map_allc_obj', [App\Http\Controllers\MastersController::class, 'mapAllcObj_dropdown'])->name('map_allc_obj')->defaults('viewName', 'masters.mapallocationobjection');
Route::post('/masters/insertorupdate_mapWorkObj', [App\Http\Controllers\MastersController::class, 'insertorupdate_mapWorkObj'])->name('masters.mapallocationobjection');
Route::post('/masters/fetchall_mapallocationObj', [App\Http\Controllers\MastersController::class, 'fetchall_mapallocationObj'])->name('masters.mapallocationobjection');
Route::post('/masters/FilterByDept', [App\Http\Controllers\MastersController::class, 'filterbyDept'])->name('masters.mapallocationobjection');
// Route::post('/masters/getSubCategoryBasedOnCategory', [App\Http\Controllers\MastersController::class, 'getSubCategoryBasedOnCategory'])->name('masters.getSubCategoryBasedOnCategory');


/********************************************************************Create Map Allocation Objection - URL ******************************************************************* */




// Route::get('/department', function () {
//     return view('pages/department');
// });
Route::get('/region', function () {
    return view('pages/region');
});
Route::get('/state', function () {
    return view('pages/state');
});
Route::get('/index', function () {
    return view('index2');
});
Route::get('/header', function () {
    return view('layouts.dash_navbar');
});
Route::get('/sidenav', function () {
    return view('layouts.dash_sidebar');
});
// Route::get('/create_user', function () {
//     return view('usermanagement.createuser');
// });


Route::get('/privileges', function () {
    return view('privileges/privileges');
});

Route::get('/audit_diary', function () {
    return view('audit/auditdiary');
});


// Route::get('/audit_plan', function () {
//     return view('audit/auditplan');
// });
// Route::get('/audit_team', function () {
//     return view('audit/auditteam');
// });
Route::get('/audit_datefixing', function () {
    return view('audit/auditdatefixing');
});

Route::get('/list_objections', function () {
    return view('audit/listofobjections');
});
Route::get('/calendar', function () {
    return view('audit/calendar');
});

Route::get('/manage_workallocation', function () {
    return view('audit/workallocation');
});

Route::get('/create_workallocation', function () {
    return view('audit/createworkallocation');
});

Route::get('/audit_certificate', function () {
    return view('audit/auditcertificate');
});

Route::get('/editable-report', function () {
    return view('audit/editable-report');
});

Route::get('/mapworkallocationobjection', function () {
    return view('audit/mapworkallocationobjection');
});




use App\Http\Controllers\WorkAllocationManagementController;

Route::get('/manage_workallocation', [WorkAllocationManagementController::class, 'Show_Workallocation'])->name('workallocation.view');


Route::post('/MajorWrkAllocationFetch', [WorkAllocationManagementController::class, 'Get_MajorWrkAllocation'])->name('workallocation.view');

Route::post('/MinorWrkAllocationFetch', [WorkAllocationManagementController::class, 'Get_MinorWrkAllocation'])->name('workallocation.view');

Route::post('/CreateWorkAllocation', [WorkAllocationManagementController::class, 'CreateWorkAllocation'])->name('workallocation.view');

Route::post('/fetchAllData', [App\Http\Controllers\WorkAllocationManagementController::class, 'fetchAllData'])->name('workallocation.fetch');

Route::post('/fetchWorkData', [App\Http\Controllers\WorkAllocationManagementController::class, 'fetchWorkData'])->name('workallocation.fetchWorkData');

Route::post('/CreateAuditCertificate', [App\Http\Controllers\AuditCertificateController::class, 'CreateAuditCertificate'])->name('auditcertificate.view');

Route::post('/FetchAllCertificate', [App\Http\Controllers\AuditCertificateController::class, 'FetchAllCertificate'])->name('auditcertificate.fetch');




Route::get('/trans_auditslip', function () {
    return view('audit/transauditslip');
});



Route::get('/auditee', function () {
    return view('audit/auditee');
});
    Route::get('init_auditschedule', [App\Http\Controllers\AuditManagementController::class, 'initschedule_dropdown'])->name('init_auditschedule')->defaults('viewName', 'audit.initauditschedule');

Route::get('/auditplan', function () {
    return view('audit/auditplanning');
});
Route::get('/leave_form', function () {
    return view('leavemanagement/leaveform');
});

Route::get('/transaction', function () {
    return view('transactionmaster/transaction');
});
// Route::get('/othertransprovision', function () {
//     return view('transactionmaster/othertransprovision');
// });
//Route::get('/view_intimation', function () {
    //return view('audit/viewintimationdetails');
//});

Route::get('/datatransfer', function () {
    return view('transactionmaster/datatransfer');
});



// Route::get('/district', [App\Http\Controllers\MasterController::class, 'viewDistrict']);

// Route::post('/departmentSave', [App\Http\Controllers\MasterController::class, 'saveDepartment'])->name('department.save');
// Route::post('/regionSave', [App\Http\Controllers\MasterController::class, 'saveRegion'])->name('region.save');
// Route::post('/districtSave', [App\Http\Controllers\MasterController::class, 'saveDistrict'])->name('district.save');
// Route::post('/stateSave', [App\Http\Controllers\MasterController::class, 'saveState'])->name('state.save');
/*********************************************************************Master Form - URL ******************************************************************* */
Route::get('/department_config', function () {
    return view('masters/departmentConfig');
});


Route::post('/masters/storeOrUpdate', [App\Http\Controllers\MastersController::class, 'storeOrUpdate'])->name('masters.departmentConfig');
Route::post('/masters/fetchAlldata', [App\Http\Controllers\MastersController::class, 'fetchAlldata'])->name('masters.departmentConfig');
Route::post('/masters/fetchdept_data', [App\Http\Controllers\MastersController::class, 'fetchdept_data'])->name('masters.departmentConfig');


/********************************************************************* Master Form - URL ******************************************************************* */

Route::get('/', function () {
    return view('site/homepage');
});
Route::get('/state/data', [App\Http\Controllers\MasterController::class, 'getStatesList'])->name('state.list');
Route::get('/dist/data', [App\Http\Controllers\MasterController::class, 'getdistsList'])->name('dist.list');
Route::get('/dist/edit/{id}', [App\Http\Controllers\MasterController::class, 'getdistsedit'])->name('dist.edit');
Route::post('/audit/storeOrUpdateAuditSchedule', [App\Http\Controllers\AuditManagementController::class, 'storeOrUpdateAuditSchedule'])->name('audit.audit_datefixing');
Route::get('audit_datefixing', [App\Http\Controllers\AuditManagementController::class, 'creatauditschedule_dropdownvalues'])->name('audit_datefixing')->defaults('viewName', 'audit.auditdatefixing');
// Route::get('/audit/audit_members', [App\Http\Controllers\AuditSchedule::class, 'audit_members'])->name('audit.audit_datefixing');
// Route::post('/audit/fetchAllScheduleData', [App\Http\Controllers\AuditSchedule::class, 'fetchAllScheduleData'])->name('audit.audit_datefixing');
// Route::post('/audit/fetchschedule_data', [App\Http\Controllers\AuditSchedule::class, 'fetchschedule_data'])->name('audit.audit_datefixing');

// Route::post('/users', [UserManagementController::class, 'insert'])->name('users.insertuser');
//Route::post('/audit/cancel_schedule', [App\Http\Controllers\AuditManagementController::class, 'CancelAuditschedule'])->name('audit.CancelAuditschedule');
Route::post('/audit/cancelorreschedule', [App\Http\Controllers\AuditManagementController::class, 'CancelorRescheduleAudit'])->name('audit.CancelorRescheduleAudit');


// Login route (POST)
Route::post('/login', [App\Http\Controllers\LoginController::class, 'login'])->name('login');


// Route::get('/dashboard', function () {
//     return view('dashboard/dashboard');
// });


// Route::post('/user/insert', [App\Http\Controllers\UserManagementController::class, 'storeOrUpdate'])->name('user.insert');
// Route::post('/user/fetchAllData', [App\Http\Controllers\UserManagementController::class, 'fetchAllData'])->name('user.fetchAllData');
// Route::post('/user/fetchUserData', [App\Http\Controllers\UserManagementController::class, 'fetchUserData'])->name('user.fetchUserData');
// Route::get('/create_user', [App\Http\Controllers\UserManagementController::class, 'creatuser_dropdownvalues'])->name('create_user')->defaults('viewName', 'usermanagement.createuser');


// // Route for the createuser page
// Route::get('/create_charge', [App\Http\Controllers\UserManagementController::class, 'creatuser_dropdownvalues'])->name('create_charge')->defaults('viewName', 'usermanagement.createcharge');
//Route::get('/dashboard_detail', [App\Http\Controllers\DashboardController::class, 'dashboard_detail'])->name('dashboard.dashboard_detail');


// Protected User Management Routes
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\FieldAuditController;
use App\Http\Controllers\TransactionflowController;
use App\Http\Controllers\WorkAllocationController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\AuditManagementController;

use App\Http\Controllers\FileController;




// Protected User Management Routes
Route::middleware('check.session')->group(function () {

Route::get('liability_details', function () {
    return view('audit/Liability_details');
});
Route::post('/callcodecheck', [App\Http\Controllers\FormatController::class, 'callcodecheck'])->name('callcodecheck');

Route::get('/updatereport', [App\Http\Controllers\FormatController::class, 'reportdeptfetch'])->name('report_form');
Route::post('/getregionbasedondeptforreportdept', [App\Http\Controllers\FormatController::class, 'getregionbasedondeptforreportdept']);
Route::post('/getdistrictbasedonregionreport', [App\Http\Controllers\FormatController::class, 'getdistrictbasedonregionreport']);
Route::post('/getinstitutionbasedondistreport', [App\Http\Controllers\FormatController::class, 'getinstitutionbasedondistreport']);

Route::post('/report_fetchData', [App\Http\Controllers\FormatController::class, 'report_fetchData'])
    ->name('report.report_fetchData');

Route::post('/report_insertupdate', [App\Http\Controllers\FormatController::class, 'report_insertupdate'])
    ->name('report.report_insertupdate');   



Route::get('/updateauditdiary', [App\Http\Controllers\AuditDiaryController::class, 'auditdiarydeptfetch'])->name('auditdiary_form');


Route::post('/getregionbasedondeptforuditdiary', [App\Http\Controllers\AuditDiaryController::class, 'getregionbasedondeptuditdiary']);
Route::post('/getdistrictbasedonregionuditdiary', [App\Http\Controllers\AuditDiaryController::class, 'getdistrictbasedonregionuditdiary']);
Route::post('/getinstitutionbasedondistuditdiary', [App\Http\Controllers\AuditDiaryController::class, 'getinstitutionbasedondistuditdiary']);
Route::post('/getusernameBasedOninstitution', [App\Http\Controllers\AuditDiaryController::class, 'getusernameBasedOninstitution']);

Route::post('/auditdiary_fetchData', [App\Http\Controllers\AuditDiaryController::class, 'auditdiary_fetchData'])
    ->name('auditdiary.auditdiary_fetchData');


Route::post('/auditdiary_insertupdate', [App\Http\Controllers\AuditDiaryController::class, 'auditdiary_insertupdate'])
    ->name('auditdiary.auditdiary_insertupdate');  
	

Route::get('/home', function () {
    return view('dashboard/home');
});

Route::POST('/pendingusersfornotspillover', [FieldAuditController::class, 'pendingusersfornotspillover'])->name('FiedAudit.pendingusersfornotspillover');

Route::get('view_intimation', [App\Http\Controllers\AuditManagementController::class, 'viewintimation_dropdown'])->name('view_intimation')->defaults('viewName', 'audit.viewintimationdetails');

 Route::get('/spillover_schedule', [App\Http\Controllers\AuditManagementController::class, 'spilloverschedule_values'])->name('audit.spilloverschedule')->defaults('viewName', 'audit.spilloverschedule');
    Route::post('/spillover/chargetakingover', [App\Http\Controllers\AuditManagementController::class, 'chargetakingover'])->name('audit.spilloverschedule');


Route::get('/download-file', [FileController::class, 'downloadFile'])->name('download.file');


   Route::post('checklistplan/sendOtp_allocateplan', [App\Http\Controllers\AuditManagementController::class, 'SendOTP_allocatePlan'])->name('send.otp');
    Route::post('checklistplan/verifyOtp_allocateplan', [App\Http\Controllers\AuditManagementController::class, 'VerifyOTP_allocatePlan'])->name('verify.otp');
    Route::post('checklistplan/checkexitmeetstatus', [App\Http\Controllers\AuditManagementController::class, 'checkexitmeetstatus'])->name('checkexitmeetstatus');


  
Route::get('/instchange', [App\Http\Controllers\AuditManagementController::class, 'fetch_notscheduledinst']);
Route::post('/instchange/penidninstUpdation', [App\Http\Controllers\AuditManagementController::class, 'penidninstUpdation'])->name('/instchange/penidninstUpdation');
Route::post('/instchange/penidninstUpdate', [App\Http\Controllers\AuditManagementController::class, 'penidninst_fetchData'])->name('/instchange/penidninstUpdate');

 Route::get('/checklistplan', function () {
        return view('audit/checklistplan');
    });

    Route::post('/checklistplan/checkisteamassigned', [AuditManagementController::class, 'checkisteamassigned'])
        ->name('checklistplan');
    Route::post('/checklistplan/assignteams', [AuditManagementController::class, 'assignteams'])
        ->name('assignteams');
    Route::post('/checklistplan/finaliseplan', [AuditManagementController::class, 'finaliseplan'])
        ->name('finaliseplan');



Route::get('map_inst', [App\Http\Controllers\MastersController::class, 'mapInst_dropdown'])->name('map_inst')->defaults('viewName', 'masters.mapinst');
Route::post('/masters/fetch_mapInstDet', [App\Http\Controllers\MastersController::class, 'fetch_mapInstDet'])->name('masters.mapinst');
Route::post('/masters/FilterInst', [App\Http\Controllers\MastersController::class, 'FilterInst'])->name('masters.mapinst');
Route::post('/masters/FilterAuditInst', [App\Http\Controllers\MastersController::class, 'FilterAuditInst'])->name('masters.mapinst');
Route::post('/masters/insertorupdate_mapInst', [App\Http\Controllers\MastersController::class, 'insertorupdate_mapInst'])->name('masters.mapinst');





Route::get('/listofwork', [WorkAllocationController::class, 'fetchinstdet'])->name('listofwork');;
Route::post('/workalloc/fetchworkdata', [WorkAllocationController::class, 'fetchworkdata'])
    ->name('fieldaudit.workallocationlist');
///////////////////////////////////////////////Inspection-START/////////////////////////////////////////////////////////////////////////

Route::get('/inspectview', [InspectionController::class, 'inspectview_dropdown'])->name('inspectview')->defaults('viewName', 'inspection.inspectionview');
Route::post('/inspection/fetch_instDetails', [InspectionController::class, 'fetch_instDetails'])
->name('inspectview');
Route::post('/inspection/fetch_deptbaseddata', [InspectionController::class, 'fetch_deptbaseddata'])
->name('inspectview');
Route::get('/inspectionquery/{status}&{id}&{inspectionid}/', [InspectionController::class, 'inspectionquery_dropdown'])
->name('inspectionquery')
->defaults('viewvalue', 'inspection.inspectionquery');

Route::post('/inspection/getpendingparadetails', [InspectionController::class, 'getpendingparadetails'])
->name('inspectionquery');
Route::post('/inspection/getslipdetails', [InspectionController::class, 'getSlipDetailsHistory'])
->name('inspectionquery');

Route::post('/inspection/getsliphistorydetails', [InspectionController::class, 'getsliphistorydetails'])->name('inspectionquery');
Route::post('/inspection/getchecklistdetails', [InspectionController::class, 'getchecklistdetails'])->name('inspectionquery');
Route::post('/inspection/inspectchecklist_insert', [InspectionController::class, 'inspectchecklist_insert'])->name('inspectionquery');
Route::post('/inspection/getinspectionDetails', [InspectionController::class, 'getinspectionDetails'])->name('inspectionquery');
Route::post('/inspection/completeinspect', [InspectionController::class, 'completeinspect'])->name('inspectionquery');
Route::post('/inspection/insertslipremarks', [InspectionController::class, 'insertslipremarks'])->name('inspectionquery');

// Route::get('/inspectionreply', [InspectionController::class, 'inspectreply_dropdown'])->name('inspectreply')->defaults('viewName', 'inspection.inspectionreply');

//////////////////////////////////////////////Inspection-END///////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////// Masters Form ///////////////////////////////////////////////////////////////////////////////////////

 /****************************************Manual  Audit Team Creation - URL ******************************************************************* */

    Route::post('/get-auditors', [App\Http\Controllers\AuditTeam::class, 'getAuditors']);
    Route::get('/audit_team', [App\Http\Controllers\AuditTeam::class, 'creatuser_dropdownvalues'])->name('audit_team')->defaults('viewName', 'audit.auditteam');
    Route::post('/audit/createAuditTeam', [App\Http\Controllers\AuditTeam::class, 'createAuditTeam'])->name('audit.audit_team');
    Route::post('/audit/fetchAllData', [App\Http\Controllers\AuditTeam::class, 'fetchAllData'])->name('audit.audit_team');
    Route::post('/audit/fetchTeamData', [App\Http\Controllers\AuditTeam::class, 'fetchTeamData'])->name('audit.audit_team');
    Route::post('/audit_team/FilterByDept', [App\Http\Controllers\AuditTeam::class, 'FilterByDept']);

    Route::get('/manualplan', [App\Http\Controllers\AuditManagementController::class, 'creatuser_dropdownvalues'])->name('manualplan')->defaults('viewName', 'audit.manualplan');
    Route::post('/manualplan/getAuditors_manualplan', [App\Http\Controllers\AuditManagementController::class, 'getAuditors_manualplan']);

    Route::post('/audit/fetchExcessinstitution', [App\Http\Controllers\AuditManagementController::class, 'fetchExcessinstitution'])->name('audit.fetchExcessinstitution');
    Route::post('/audit/fetchteam', [App\Http\Controllers\AuditManagementController::class, 'fetchteam'])->name('audit.fetchteam');
    Route::post('/manualplan/manualplan_insertupdate', [App\Http\Controllers\AuditManagementController::class, 'manualplan_insertupdate'])
        ->name('audit.manualplan_insertupdate');
    Route::post('/manualplan/fetchUpdatepManualplan', [App\Http\Controllers\AuditManagementController::class, 'fetchUpdatepManualplan'])
        ->name('fetchUpdatepManualplan');
    Route::post('/fetchmanualupdatedata', [App\Http\Controllers\AuditManagementController::class, 'fetchmanualupdatedata'])
        ->name('fetchmanualupdatedata');


    /*******************************************Manual  Audit Team Creation - END ******************************************************************* */


/********************************************************************* Audit Inspection Form ******************************************************************* */
Route::get('/auditinspectform', function () {
return app()->call('App\Http\Controllers\MastersController@fetchdeptforauditinspection', ['index' => 'masters.createauditinspection']);
})->name('/auditinspectform');

Route::post('/auditinspectform/auditinspectform_insertupdate', [MastersController::class, 'auditinspectform_insertupdate'])
->name('auditinspectform.auditinspectform_insertupdate');

Route::post('/auditinspectform/auditinspectform_fetchData', [MastersController::class, 'auditinspectform_fetchData'])
->name('auditinspectform.auditinspectform_fetchData');


Route::post('/getsubcategoriesbasedondeptforauditinspection', [MastersController::class, 'getsubcatbasedoncategoryinspection']);



Route::post('/getCategoriesBasedOnDeptforinspection', [MastersController::class, 'getCategoriesBasedOnDeptforinspection']);

/********************************************************************* Audit Inspection Form End ******************************************************************* */

    /********************************************************************* Subwork Allocation ******************************************************************* */

    // Route::get('/subworkallocation_form', function () {
    // return view('masters.createsubworkallocation');
    // });
    Route::post('getworkallocationBasedOnDept', [MastersController::class, 'getworkallocationBasedOnDept'])
        ->name('getworkallocationBasedOnDept');
    Route::get('/subworkallocation_form', [MastersController::class, 'SubworkDeptFetch']);

    Route::post('/subworkallocation/subworkallocation_insertupdate', [MastersController::class, 'subworkallocation_insertupdate'])
        ->name('subworkallocation.subworkallocation_insertupdate');

    Route::post('/master/subworkallocationtype_fetchData', [MastersController::class, 'subworkallocationtype_fetchData'])
        ->name('subworkallocationtype.subworkallocationtype_fetchData');

    /********************************************************************* Subwork Allocation End ******************************************************************* */

    /********************************************************************* Work Allocation ******************************************************************* */

    Route::get('/workallocation_form', [MastersController::class, 'workallocationdeptfetch'])->name('workallocation_form');;

    //Route::get('/workallocation_form', [MastersController::class, 'newform']);
    Route::post('/workallocation/workallocation_insertupdate', [MastersController::class, 'workallocation_insertupdate'])
        ->name('workallocationhome.workallocation_insertupdate');
    //Route::post('/newform_insertupdate', [MastersController::class, 'majorworkallocation']);
    Route::post('/master/workallocationtype_fetchData', [MastersController::class, 'workallocationtype_fetchData'])
        ->name('workallocationtype.workallocationtype_fetchData');

    /********************************************************************* Work Allocation End ******************************************************************* */

    /********************************************************************* SubCategory ******************************************************************* */


    Route::get('/subcategory', [MastersController::class, 'fetchdept']);

    Route::post('/subcategory_insertupdate', [MastersController::class, 'subcategory_insertupdate'])
        ->name('subcategory.subcategory_insertupdate');
    Route::post('/master/subcategory_fetchData', [MastersController::class, 'subcategory_fetchData'])
        ->name('subcategory.subcategory_fetchData');

    /********************************************************************* SubCategory End ******************************************************************* */

    /********************************************************************* Category ******************************************************************* */



    Route::get('/category_form', [MastersController::class, 'categorydeptfetch']);
    Route::post('/category/category_insertupdate', [MastersController::class, 'category_insertupdate'])
        ->name('category.category_insertupdate');
    Route::post('/master/category_fetchData', [MastersController::class, 'category_fetchData'])
        ->name('category.category_fetchData');

    /********************************************************************* Category End******************************************************************* */

    /********************************************************************* Main Objection ******************************************************************* */

    Route::post('/get-categories', [MastersController::class, 'getCategoriesByDept'])->name('get.categories');
    Route::get('/mainobjection', function () {
        $dept  =   UserManagementModel::departmenttdetail();
        return view('masters.createmainobjection', compact('dept'));
    });
    Route::post('/mainobjection/mainobjection_insertupdate', [MastersController::class, 'mainobjection_insertupdate'])
        ->name('mainobjectionhome.mainobjection_insertupdate');
    Route::post('/master/mainobjection_fetchData', [MastersController::class, 'mainobjection_fetchData'])
        ->name('mainobjection.mainobjection_fetchData');

    /********************************************************************* Main Objection End ******************************************************************* */


    /********************************************************************* Sub Objection ******************************************************************* */

    Route::get('/subobjection', function () {
        $dept  =   UserManagementModel::departmenttdetail();
        // $mainobj = MastersModel::mainobjectiondetails('audit.mst_mainobjection');
        return view('masters.createsubobjection', compact('dept'));
    });

    Route::post('/getobjectionBasedOnDept', [MastersController::class, 'getobjectionBasedOnDept']);
    Route::post('/subobjection/subobjection_insertupdate', [MastersController::class, 'subobjection_insertupdate'])
        ->name('subobjectionhome.subobjection_insertupdate');
    Route::post('/master/subobjection_fetchData', [MastersController::class, 'subobjection_fetchData'])
        ->name('subobjection.subobjection_fetchData');

    /********************************************************************* Sub Objection End ******************************************************************* */



    /********************************************************************* Call For Records  ******************************************************************* */

    Route::get('/callforrecord', function () {
        $dept  =   UserManagementModel::departmenttdetail();
        return view('masters.createcallforrecords', compact('dept'));
    });

    Route::post('/get-category', [MastersController::class, 'getCategoryByDept'])->name('get.category');
    Route::post('/callfor/callforrecords_insertupdate', [MastersController::class, 'callforrecords_insertupdate'])->name('callfor.callforrecords_insertupdate');
    Route::post('/callfor/callforrecords_fetchData', [MastersController::class, 'callforrecords_fetchData'])->name('callfor.callforrecords_fetchData');


    /********************************************************************* Call For Records End  ******************************************************************* */


    /********************************************************************* Group  ******************************************************************* */


    Route::get('/group', [MastersController::class, 'fetchdeptforgroup']);

    Route::post('/group/group_insertupdate', [MastersController::class, 'group_insertupdate'])
        ->name('group.group_insertupdate');

    Route::post('/group/group_fetchData', [MastersController::class, 'group_fetchData'])
        ->name('group.group_fetchData');


    /********************************************************************* Group End ******************************************************************* */

     /********************************************************************* Auditee Departent start  ******************************************************************* */

          Route::get('/auditeedepartment', [MastersController::class, 'fetchdeptforauditeedepartment']);


          Route::post('/auditeedepartment/auditeedepartment_insertupdate', [MastersController::class, 'auditeedepartment_insertupdate'])
              ->name('auditeedepartment.auditeedepartment_insertupdate');
      
      
          Route::post('/auditeedepartment/auditeedepartment_fetchData', [MastersController::class, 'auditeedepartment_fetchData'])
          ->name('auditeedepartment.auditeedepartment_fetchData');
    
     /********************************************************************* Auditee Departent End  ******************************************************************* */
      

    /********************************************************************* Irregularities  ******************************************************************* */
    Route::get('/irregularities', function () {
        return view('masters.irregularities');
    });
    Route::post('/irregularities/irregularities_insertupdate', [MastersController::class, 'irregularities_insertupdate'])
        ->name('irregularities.irregularities_insertupdate');
    Route::post('/irregularities/irregularities_fetchData', [MastersController::class, 'irregularities_fetchData'])->name('irregularities.irregularities_fetchData');

    /********************************************************************* Irregularities End ******************************************************************* */

    
    /********************************************************************* Irregularities Category ******************************************************************* */

    Route::get('/irregularitiescategory', [MastersController::class, 'irregularitiesfetch']);

    Route::post('/irregularitiescategory/irregularitiescategory_insertupdate', [MastersController::class, 'irregularitiescategory_insertupdate'])
        ->name('irregularitiescategory.irregularitiescategory_insertupdate');
    Route::post('/irregularitiescategory/irregularitiescategory_fetchData', [MastersController::class, 'irregularitiescategory_fetchData'])->name('irregularitiescategory.irregularitiescategory_fetchData');

    /********************************************************************* Irregularities Category End ******************************************************************* */

    
    /********************************************************************* Irregularities SubCategory ******************************************************************* */

    Route::get('/irregularitiessubcategory', [MastersController::class, 'irregularitiescategoryfetch']);

    Route::post('/getirrcategoriesbasedonirr', [MastersController::class, 'getirrCategoriesBasedOnirr']);



    Route::post('/irregularitiessubcategory/irregularitiessubcategory_insertupdate', [MastersController::class, 'irregularitiessubcategory_insertupdate'])
        ->name('irregularitiessubcategory.irregularitiessubcategory_insertupdate');
    Route::post('/irregularitiessubcategory/irregularitiessubcategory_fetchData', [MastersController::class, 'irregularitiessubcategory_fetchData'])->name('irregularitiessubcategory.irregularitiessubcategory_fetchData');

    /********************************************************************* Irregularities SubCategory End ******************************************************************* */



      /********************************************************************* Schema start  ******************************************************************* */

      Route::get('/scheme', [MastersController::class, 'fetchdeptforscheme']);

      Route::post('/getcategoriesbasednndeptforscheme', [MastersController::class, 'getCategoriesBasedOnDeptforscheme']);
  
      Route::post('/getsubcategoriesbasedondeptforscheme', [MastersController::class, 'getsubcatbasedoncategoryscheme']);
  
  
      Route::post('/scheme/scheme_insertupdate', [MastersController::class, 'scheme_insertupdate'])
          ->name('scheme.scheme_insertupdate');
  
  
      Route::post('/scheme/scheme_fetchData', [MastersController::class, 'scheme_fetchData'])
      ->name('scheme.scheme_fetchData');

      /********************************************************************* Schema End  ******************************************************************* */


    /********************************************************************* District ******************************************************************* */

    Route::get('/district', [MastersController::class, 'masterstatefetch']);
    Route::post('/masters/district_insertupdate', [MastersController::class, 'district_insertupdate'])
        ->name('district.district_insertupdate');

    Route::post('/master/district_fetchData', [MastersController::class, 'district_fetchData'])
        ->name('district.district_fetchData');

    /********************************************************************* District End ******************************************************************* */

    /********************************************************************* Department ******************************************************************* */

    Route::get('/department', function () {
        return view('masters.departmentconfig');
    });

    Route::post('/masters/department_insertupdate', [MastersController::class, 'department_insertupdate'])
        ->name('department.department_insertupdate');

    Route::post('/master/department_fetchData', [MastersController::class, 'department_fetchData'])
        ->name('department.department_fetchData');

    // Route::post('/login', [App\Http\Controllers\LoginController::class, 'login'])->name('login');

    // Dashboard route (Protected by check.session middleware)
    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->middleware(['auth', 'no.cache', 'check.session'])->name('dashboard');

    /********************************************************************* Department End ******************************************************************* */


    /********************************************************************* Audit District ******************************************************************* */

    Route::get('/auditdistrict', [MastersController::class, 'auditdistrictdeptfetch']);
    Route::post('/auditdistrict/auditdistrict_insertupdate', [MastersController::class, 'auditdistrict_insertupdate'])
        ->name('auditdistrict.auditdistrict_insertupdate');

    Route::post('/master/auditdistrict_fetchData', [MastersController::class, 'auditdistrict_fetchData'])
        ->name('auditdistrict.auditdistrict_fetchData');


    /********************************************************************* Audit District End ******************************************************************* */


    /********************************************************************* Designation  ******************************************************************* */

    Route::resource('/designation', MastersController::class);
    Route::post('/designation/designation_insertupdate', [MastersController::class, 'designation_insertupdate'])
        ->name('designationhome.designation_insertupdate');
    Route::post('/designation/designation_fetchData', [MastersController::class, 'designation_fetchData'])->name('designation.designation_fetchData');

    /********************************************************************* Designation End ******************************************************************* */

    /********************************************************************* Auditor Inst Mapping ******************************************************************* */
    Route::get('/auditorinstmapping', [MastersController::class, 'DeptandRoletypeFetch']);
    Route::post('/ForAuditorgetregionbasedondept', [MastersController::class, 'ForAuditorgetRegionBasedOnDept']);
    Route::post('/ForAuditorgetdesignationbasedondept', [MastersController::class, 'ForAuditorgetdesignationbasedondept']);
    Route::post('/auditorinstmapping_insertupdate', [MastersController::class, 'auditorinstmapping_insertupdate'])
        ->name('auditorinstmapping_insertupdate');

    Route::post('/master/auditorinstmapping_fetchData', [MastersController::class, 'auditorinstmapping_fetchData'])
        ->name('auditorinstmapping.auditorinstmapping_fetchData');

    /********************************************************************* Auditor Inst Mapping End ******************************************************************* */



    /********************************************************************* Region End ******************************************************************* */
    Route::get('/regionhome', function () {
        $dept  =   UserManagementModel::departmenttdetail();
        return view('masters.createregion', compact('dept'));
    });
    Route::post('/region/region_insertupdate', [MastersController::class, 'region_insertupdate'])
        ->name('regionhome.region_insertupdate');
    Route::post('/master/region_fetchData', [MastersController::class, 'region_fetchData'])
        ->name('region.region_fetchData');

    /********************************************************************* Region End ******************************************************************* */

    /********************************************************************* Role Action  ******************************************************************* */
    Route::get('/roleaction', function () {
        return view('masters.createroleaction');
    });

    Route::post('/roleaction/roleaction_insertupdate', [MastersController::class, 'roleaction_insertupdate'])
        ->name('roleaction.roleaction_insertupdate');

    Route::post('/roleaction/roleaction_fetchData', [MastersController::class, 'roleaction_fetchData'])
        ->name('roleaction.roleaction_fetchData');

    /********************************************************************* Role Action End ******************************************************************* */


    /********************************************************************* Audit Quater  ******************************************************************* */

    Route::get('/auditquarters', [MastersController::class, 'deptfetchforauditquarter']);

    Route::post('/auditquarter/auditquarter_insertupdate', [MastersController::class, 'auditquarter_insertupdate'])
        ->name('auditquarter.auditquarter_insertupdate');

    Route::post('/auditquarter/auditquarter_fetchData', [MastersController::class, 'auditquarter_fetchData'])
        ->name('auditquarter.auditquarter_fetchData');

    /********************************************************************* Audit Quater End  ******************************************************************* */

    /********************************************************************* Audit Period  ******************************************************************* */

    Route::get('/auditperiod', function () {

        return view('masters.createauditperiod');
    });

    Route::post('/auditperiod_insertupdate', [MastersController::class, 'auditperiod_insertupdate'])
        ->name('auditperiod.auditperiod_insertupdate');

    Route::post('/master/auditperiod_fetchData', [MastersController::class, 'auditperiod_fetchData'])
        ->name('auditperiod.auditperiod_fetchData');

    /********************************************************************* Audit Period End  ******************************************************************* */

    
    /********************************************************************* Super Check End  ******************************************************************* */

    Route::get('/supercheck', function () {
        return app()->call('App\Http\Controllers\MastersController@fetchdeptforsupercheck', ['index' => 'masters.createsupercheck']);
    })->name('/supercheck');
    Route::get('/multisupercheck', function () {
        return app()->call('App\Http\Controllers\MastersController@fetchdeptforsupercheck', ['index' => 'masters.multisupercheck']);
    })->name('/multisupercheck');
    Route::post('/supercheck/supercheck_multiinsert', [MastersController::class, 'supercheck_multiinsert'])
        ->name('supercheck.supercheck_multiinsert');

    Route::post('/getcategoriesbasednndeptforsupercheck', [MastersController::class, 'getCategoriesBasedOnDeptforsupercheck']);

    Route::post('/getsubcategoriesbasedondeptforsupercheck', [MastersController::class, 'getsubcatbasedoncategory']);


    Route::post('/supercheck/supercheck_insertupdate', [MastersController::class, 'supercheck_insertupdate'])
        ->name('supercheck.supercheck_insertupdate');


    Route::post('/supercheck/supercheck_fetchData', [MastersController::class, 'supercheck_fetchData'])
    ->name('supercheck.supercheck_fetchData');
    /********************************************************************* Super Check End  ******************************************************************* */


    /********************************************************************* Account Particulars  ******************************************************************* */

    Route::get('/accountparticulars', [MastersController::class, 'fetchdeptforaccounts']);

    Route::post('/getaccountcategoriesbasednndept', [MastersController::class, 'getaccountcategoriesbasednndept']);

    Route::post('/getsubCategoriesBasedOncategory', [MastersController::class, 'getsubCategoriesBasedOncategory']);

    Route::post('/accountparticular/accountparticular_insertupdate', [MastersController::class, 'accountparticular_insertupdate'])
        ->name('accountparticular.accountparticular_insertupdate');

    Route::post('/accountparticular/accountparticular_fetchdata', [MastersController::class, 'accountparticular_fetchdata'])
        ->name('accountparticular.accountparticular_fetchdata');

    /********************************************************************* Account Particulars End ******************************************************************* */



    ///////////////////////////////////////////////////////////// User Managment ///////////////////////////////////////////////////

    // Route::post('/change-charge', [UserManagementController::class, 'changeCharge'])->name('change.charge');
    Route::post('/change-charge', [UserManagementController::class, 'changeCharge'])->name('change.charge');

    /********************************************************************* Create User - URL ******************************************************************* */


    Route::post('/insert', [UserManagementController::class, 'storeOrUpdate'])->name('user.insert');
    Route::post('/fetchAllData', [UserManagementController::class, 'fetchUserData'])->name('user.fetchUserData');
    Route::post('/fetchUserData', [UserManagementController::class, 'fetchUserData'])->name('user.fetchUserData');
    Route::get('/create_user', [UserManagementController::class, 'creatuser_dropdownvalues'])->name('create_user')->defaults('viewName', 'usermanagement.createuser');
    Route::post('/getDesignationBasedonDept', [UserManagementController::class, 'getDesignationBasedonDept']);



    /********************************************************************* Create User - URL ******************************************************************* */


    /********************************************************************* Create charge - URL ******************************************************************* */

    Route::get('/create_charge', [UserManagementController::class, 'creatuser_dropdownvalues'])->name('create_charge')->defaults('viewName', 'usermanagement.createcharge');
    Route::post('/getroletypecode_basedondept', [UserManagementController::class, 'getroletypecode_basedondept'])->name('UserManagementController.getroletypecode_basedondept');
    Route::post('/getRegionDistInstBasedOnDept', [UserManagementController::class, 'getRegionDistInstBasedOnDept'])->name('UserManagementController.getRegionDistInstBasedOnDept');
    Route::post('/charge_insertupdate', [UserManagementController::class, 'charge_insertupdate'])->name('UserManagementController.charge_insertupdate');
    Route::post('/fetchchargeData', [UserManagementController::class, 'fetchchargeData'])->name('UserManagementController.fetchchargeData');
    Route::post('/getRoleactionBasedOnRoletype', [UserManagementController::class, 'getRoleactionBasedOnRoletype']);

    /********************************************************************* Create charge - URL ******************************************************************* */

    /********************************************************************* Assign charge - URL ******************************************************************* */

    Route::get('/assign_charge', [UserManagementController::class, 'creatuser_dropdownvalues'])->name('assign_charge')->defaults('viewName', 'usermanagement.assigncharge');
    Route::post('/getdesignation_fromchargedet', [UserManagementController::class, 'getdesignation_fromchargedet'])->name('UserManagementController.getdesignation_fromchargedet');
    Route::post('/getchargedescription', [UserManagementController::class, 'getchargedescription'])->name('UserManagementController.getchargedescription');
    Route::post('/getuserbasedonroletype', [UserManagementController::class, 'getuserbasedonroletype'])->name('UserManagementController.getuserbasedonroletype');
    Route::post('/assigncharge_insertupdate', [UserManagementController::class, 'assigncharge_insertupdate'])->name('UserManagementController.assigncharge_insertupdate');
    Route::post('/get_assignchargevalue', [UserManagementController::class, 'get_assignchargevalue'])->name('UserManagementController.get_assignchargevalue');
    Route::post('/fetchuserchargeData', [UserManagementController::class, 'fetchuserchargeData'])->name('UserManagementController.fetchuserchargeData');

    /********************************************************************* Assign charge - URL ******************************************************************* */


    /********************************************************************* Additional charge - URL ******************************************************************* */
    Route::get('/additional_charge', [UserManagementController::class, 'creatuser_dropdownvalues'])->name('additional_charge')->defaults('viewName', 'usermanagement.additionalcharge');
    Route::post('/getroletypecodeDesignation_basedonotherdept', [UserManagementController::class, 'getroletypecodeDesignation_basedonotherdept'])->name('UserManagementController.getroletypecodeDesignation_basedonotherdept');

    /********************************************************************* Additional charge - URL ******************************************************************* */


    Route::get('/unassign_charge', [UserManagementController::class, 'creatuser_dropdownvalues'])->name('unassign_charge')->defaults('viewName', 'usermanagement.unassigncharge');
    Route::post('/unassigncharge_insertupdate', [UserManagementController::class, 'unassigncharge_insertupdate'])->name('UserManagementController.unassigncharge_insertupdate');


    ///////////////////////////////////////////////////////////// User Managment ///////////////////////////////////////////////////


    /********************************************************************* Assign charge - URL ******************************************************************* */

    ///////////////////////////////////////////////////////////// User Managment ///////////////////////////////////////////////////



    /********************************************************************* Audit Team - URL ******************************************************************* */

    Route::post('/get-auditors', [App\Http\Controllers\AuditTeam::class, 'getAuditors']);
    Route::get('/audit_team', [App\Http\Controllers\AuditTeam::class, 'creatuser_dropdownvalues'])->name('audit_team')->defaults('viewName', 'audit.auditteam');
    Route::post('/audit/createAuditTeam', [App\Http\Controllers\AuditTeam::class, 'createAuditTeam'])->name('audit.audit_team');
    Route::post('/audit/fetchAllData', [App\Http\Controllers\AuditTeam::class, 'fetchAllData'])->name('audit.audit_team');
    Route::post('/audit/fetchTeamData', [App\Http\Controllers\AuditTeam::class, 'fetchTeamData'])->name('audit.audit_team');
    Route::post('/audit_team/FilterByDept', [App\Http\Controllers\AuditTeam::class, 'FilterByDept']);

Route::get('/updateplanuser', [App\Http\Controllers\AuditManagementController::class, 'creatuser_dropdownvalues'])->name('updateuserplan')->defaults('viewName', 'audit.updateplanuser');
Route::post('/get-auditors_updateplanuser', [App\Http\Controllers\AuditManagementController::class, 'getAuditors_updateplanuser']);

Route::post('/audit/fetchinstitution', [App\Http\Controllers\AuditManagementController::class, 'fetchinstitution'])->name('audit.fetchinstitution');
Route::post('/audit/fetchteam', [App\Http\Controllers\AuditManagementController::class, 'fetchteam'])->name('audit.fetchteam');
Route::post('/auditteam_insertupdate', [App\Http\Controllers\AuditManagementController::class, 'auditteam_insertupdate'])
    ->name('audit.auditteam_insertupdate');
Route::post('/fetchUpdateplanTeam', [App\Http\Controllers\AuditManagementController::class, 'fetchUpdateplanTeam'])
    ->name('fetchUpdateplanTeam');
Route::post('/fetchupdatedata', [App\Http\Controllers\AuditManagementController::class, 'fetchupdatedata'])
    ->name('fetchupdatedata');


    /********************************************************************* Audit Team - URL ******************************************************************* */

    /********************************************************************* Field Audit - URL ******************************************************************* */


    Route::post('/getcategoryBasedOnSerious', [App\Http\Controllers\FieldAuditController::class, 'getcategoryBasedOnSerious']);
    Route::post('/getsubcategoryBasedOnCategory', [App\Http\Controllers\FieldAuditController::class, 'getsubcategoryBasedOnCategory']);

    Route::post('/getobjectionForHead', [FieldAuditController::class, 'getobjectionForHead'])->name('FiedAudit.getobjectionForHead');
    Route::post('/getObjectionBasedOnSlip', [FieldAuditController::class, 'getobjectionForHead'])->name('FiedAudit.getobjectionForHead');
    Route::post('/getsubobjection', [FieldAuditController::class, 'getsubobjection'])->name('FiedAudit.getsubobjection');
    Route::post('/getauditslip', [FieldAuditController::class, 'getauditslip'])->name('FiedAudit.getauditslip');
    Route::post('/audislip_insert', [FieldAuditController::class, 'audislip_insert'])->name('FiedAudit.audislip_insert');
    Route::post('/auditeereply', [FieldAuditController::class, 'auditeereply'])->name('FiedAudit.auditeereply');
    Route::get('/trans_auditslip', [App\Http\Controllers\AuditSlipController::class, 'showAuditSlips']);

    Route::get('trans_auditslip/{id}', [FieldAuditController::class, 'audittrans_dropdown'])
        ->name('trans_auditslip')
        ->defaults('viewvalue', 'audit.transauditslip');

    Route::get('/auditee_fieldaudit', [FieldAuditController::class, 'slipdetails_dropdown'])
        ->name('slipdetails_dropdown')
        ->defaults('viewvalue', 'fieldaudit.auditeeslip');

    Route::get('/audit_slip/{id}', [FieldAuditController::class, 'auditslip_dropdown'])
        ->name('audit_slip')
        ->defaults('viewvalue', 'fieldaudit.auditslip');

    Route::get('/field_audit/{id}', [FieldAuditController::class, 'auditfield_dropdown'])
        ->name('field_audit')
        ->defaults('viewvalue', 'fieldaudit.fieldaudit');


    Route::get('/init_fieldaudit', [FieldAuditController::class, 'init_fieldaudit'])->name('FiedAudit.init_fieldaudit');

    Route::get('/List_Institute', [FieldAuditController::class, 'view_fieldaudit'])->name('FiedAudit.List_Institute');
    Route::get('/Download_Reports', [App\Http\Controllers\FormatController::class, 'finalizedinstitutesforreport'])->name('Format.Download_Reports');
    Route::post('/download-report', [App\Http\Controllers\FormatController::class, 'DownloadReport'])->name('download.report');
    Route::post('/sendauditeemail', [App\Http\Controllers\FormatController::class, 'sendAuditeeMail']);

    //////workall/////
    Route::post('/fetchminorworkdel', [FieldAuditController::class, 'fetchminorworkdel'])->name('FiedAudit.fetchminorworkdel');
    Route::post('/insert_workAllocation', [FieldAuditController::class, 'insert_workAllocation'])->name('FiedAudit.insert_workAllocation');
    // Route::post('/fetchAllWorkData', [FieldAuditController::class, 'fetchAllWorkData'])->name('FieldAuditController.fetchAllWorkData');
    Route::post('/fetchAllWorkData', [FieldAuditController::class,  'fetchAllWorkData'])->name('fetchAllWorkData');


    Route::post('/fetch_singleworkdet', [FieldAuditController::class, 'fetch_singleworkdet'])->name('FiedAudit.fetch_singleworkdet');


    Route::post('/Supercheck_QuesAns', [App\Http\Controllers\FieldAuditController::class, 'Supercheck_QuesAns'])->name('FiedAudit.Supercheck_QuesAns');

    //////workall/////
    /********************************************************************* Field Audit - URL ******************************************************************* */

/********************************************************************* Transaction flow ********************************************************/

       

        Route::get('othertransaction', [TransactionflowController::class, 'usertrans_dropdown'])->name('usertrans')->defaults('viewName', 'transactionmaster.othertransaction');
        Route::post('/transaction/fetchOtherTranDel', [TransactionflowController::class, 'fetchOtherTranDel']);
        Route::post('/transaction/getdeptbaseddesig', [TransactionflowController::class, 'getdeptbaseddesig'])->name('transactionmaster.othertransaction');
        Route::post('/transaction/fetchRegDistInstbasedondept', [TransactionflowController::class, 'fetchRegDistInstbasedondept'])->name('transactionmaster.othertransaction');
        Route::post('/transaction/instdataforothers', [TransactionflowController::class, 'instdataforothers'])->name('transactionmaster.othertransaction');
        Route::post('/getroletypecode_basedondept_othertrans', [TransactionflowController::class, 'getroletypecode_basedondept_othertrans'])->name('UserManagementController.getroletypecode_basedondept');
        Route::post('/transaction/filterforusertrans', [TransactionflowController::class, 'filterforusertrans'])->name('transactionmaster.othertransaction');

        Route::post('/transaction/othertransction_insertupdate', [TransactionflowController::class, 'othertransction_insertupdate'])->name('transactionmaster.othertransaction');

        Route::post('/transaction/forward_application', [TransactionflowController::class, 'forward_application'])->name('leavemanagement.leaveform');

        Route::view('transactionflow', 'transactionflow.transactionflow');
        Route::post('/transaction/fetchall_transflowdata', [TransactionflowController::class, 'fetchall_transflowdata'])->name('transactionmaster.fetchall_transflowdata');


        Route::get('datatransfer', [TransactionflowController::class, 'datatrans_dropdown'])->name('datatransfer')->defaults('viewName', 'transactionmaster.datatransfer');
        Route::post('/getworkalloactionbasedonSchedulemember', [TransactionflowController::class, 'getworkalloactionbasedonSchedulemember'])->name('transactionmaster.getworkalloactionbasedonSchedulemember');
        Route::post('/getinstitutiondel', [TransactionflowController::class, 'getinstitutiondel'])->name('transactionmaster.getinstitutiondel');

        Route::post('/getslipdetailsbasedon_schedulemember', [TransactionflowController::class, 'getslipdetailsbasedon_schedulemember'])->name('transactionmaster.getslipdetailsbasedon_schedulemember');
        Route::post('/insert_datatransfer', [TransactionflowController::class, 'insert_datatransfer'])->name('transactionmaster.insert_datatransfer');


        Route::get('applyleave', [TransactionflowController::class, 'leavetype_dropdownvalues'])->name('leave_form')->defaults('viewName', 'leavemanagement.leaveform');
        Route::post('/storeOrUpdateLeave', [TransactionflowController::class, 'storeOrUpdateLeave'])->name('leavemanagement.leaveform');
        Route::post('/fetchall_leavedata', [TransactionflowController::class, 'fetchall_leavedata'])->name('leavemanagement.leaveform');
        Route::post('/fetchsingle_data', [TransactionflowController::class, 'fetchsingle_data'])->name('leavemanagement.leaveform');



        Route::post('/transaction/reject_application', [TransactionflowController::class, 'reject_application'])->name('transactionmaster.transaction');


        Route::get('approveddetails', [TransactionflowController::class, 'transactionapproveddetails'])->name('leave_form')->defaults('viewName', 'transactionflow.transactionapproveddetails');
        Route::get('/viewdatatransferdel', [TransactionflowController::class, 'viewdatatransferdel'])->name('viewdatatransferdel');


    /********************************************************************* Transaction flow ********************************************************/


    /*********************************************************************Automation Plan - URL ******************************************************************* */

    Route::get('auditplan', [App\Http\Controllers\AuditManagementController::class, 'create_userdet'])->name('auditplan')->defaults('viewName', 'audit/auditplanning');
    Route::post('/audit/fetchall_automatedata', [App\Http\Controllers\AuditManagementController::class, 'fetchall_automatedata'])->name('auditplan.fetchall_automatedata');
    Route::post('/audit/finalize_data', [App\Http\Controllers\AuditManagementController::class, 'finalize_data'])->name('auditplan.finalize_data');
    Route::post('/audit/automate_plan', [App\Http\Controllers\AuditManagementController::class, 'automate_plan'])->name('auditplan.automate_plan');
    Route::post('/audit/checkfordetails', [App\Http\Controllers\AuditManagementController::class, 'checkfordetails'])->name('auditplan.checkfordetails');

    /*********************************************************************Automation Plan - URL ******************************************************************* */

    /*********************************************************************View Audit Plan Details - URL ******************************************************************* */
    Route::post('/audit/audit_plandetails', [App\Http\Controllers\AuditManagementController::class, 'audit_plandetails'])->name('audit.initauditschedule');

    /********************************************************************View Audit Plan Details - URL ******************************************************************* */

////session end///////
// Route::get('/audit_diary', function () {
//     return view('audit/auditdiary');
// });

// Route::get('audit_diary', [App\Http\Controllers\AuditDiaryController::class, 'FetchworkallocationDetailsdropdown'])->name('audit_diary_data')->defaults('viewName', 'audit.audit_diary_data');
// Route::post('/audit_diary/insert', [App\Http\Controllers\AuditDiaryController::class, 'storeOrUpdateAuditDiary'])->name('auditdiary.insert');

Route::get('/auditdiary_home', function () {
	return view('audit/auditdiarytable');
	});

	Route::post('/auditdiarytable_fetchData', [App\Http\Controllers\AuditDiaryController::class, 'auditdiarytablehomefetch'])->name('auditdiarytable_fetchData');

   
	
	Route::get('/audit_diary', [App\Http\Controllers\AuditDiaryController::class, 'FetchworkallocationDetailsdropdown'])
	->name('audit_diary_data')
	->defaults('viewName', 'audit.audit_diary_data');
   
	
	Route::post('/audit_diary/insert', [App\Http\Controllers\AuditDiaryController::class, 'storeOrUpdateAuditDiary'])->name('auditdiary.insert');
	Route::post('/auditdiary/finalize', [App\Http\Controllers\AuditDiaryController::class, 'auditdiaryfinalize'])->name('auditdiary.finalize');

	
	Route::get('/download-auditor-diary', [App\Http\Controllers\AuditDiaryController::class, 'downloadDiary']);
	
	
	Route::post('/auditdiary/fetchAllData', [App\Http\Controllers\AuditDiaryController::class, 'FetchDiarydetails'])->name('auditdiary.fetch');

	Route::post('/auditdiary/history', [App\Http\Controllers\AuditDiaryController::class, 'showDiaryhistory'])->name('auditdiary.history');


	    Route::POST('/confirmationdiary', [FieldAuditController::class, 'confirmationdiary'])->name('FiedAudit.confirmationdiary');
/********************************************************************* Audit Plan - URL ******************************************************************* */



Route::post('/audit_plan/insert', [App\Http\Controllers\AuditManagementController::class, 'storeOrUpdateAudit'])->name('auditplan.insert');
Route::post('/audit_plan/fetchAllData', [App\Http\Controllers\AuditManagementController::class, 'auditfetchAllData'])->name('auditplan.fetchAllData');
Route::get('/audit_plan/{id}', [App\Http\Controllers\AuditManagementController::class, 'show'])->name('auditplan.show');
Route::post('/audit_plan/update', [App\Http\Controllers\AuditManagementController::class, 'storeOrUpdateAudit'])->name('auditplan.update');
Route::post('/audit_plan/fetchUserData', [App\Http\Controllers\AuditManagementController::class, 'fetchUserDataAudit'])->name('auditplan.fetchUserData');
Route::post('/audit_plan/FilterByDept', [App\Http\Controllers\AuditManagementController::class, 'FilterByDept'])->name('auditplan.FilterByDept');
Route::get('/audit_plan', [App\Http\Controllers\AuditManagementController::class, 'creatuser_dropdownvalues'])->name('audit_plan')->defaults('viewName', 'audit/auditplan');

/********************************************************************* Audit Plan - URL ******************************************************************* */


/********************************************************************* Audit Schedule - URL ******************************************************************* */

// Route::get('audit_datefixing/', [App\Http\Controllers\AuditSchedule::class, 'creatauditschedule_dropdownvalues'])->name('audit_datefixing')->defaults('viewName', 'audit.auditdatefixing');
Route::post('/audit/audit_members', [App\Http\Controllers\AuditManagementController::class, 'audit_members'])->name('audit.audit_datefixing');
Route::post('/audit/fetchAllScheduleData', [App\Http\Controllers\AuditManagementController::class, 'fetchAllScheduleData'])->name('audit.audit_datefixing');
Route::post('/audit/fetchschedule_data', [App\Http\Controllers\AuditManagementController::class, 'fetchschedule_data'])->name('audit.audit_datefixing');
// Route::post('/audit/audit_scheduledetails', [App\Http\Controllers\AuditSchedule::class, 'audit_scheduledetails'])->name('audit.auditee');

Route::post('/audit/auditee_intimation', [App\Http\Controllers\AuditManagementController::class, 'auditee_intimation'])->name('audit.view_intimation');

Route::post('/automateWorkAllocation', [App\Http\Controllers\WorkAllocationController::class, 'automateWorkAllocation'])->name('AuditSchedule.automateWorkAllocation');


/********************************************************************* Audit Schedule - URL ******************************************************************* */



/********************************************************************* Auditee ******************************************************************* */
Route::post('/audit/audit_scheduledetails', [App\Http\Controllers\AuditeeController::class, 'audit_scheduledetails'])->name('audit.auditee');
// Route::post('/audit/auditee_acceptdetails', [App\Http\Controllers\AuditeeController::class, 'auditee_acceptdetails'])->name('audit.auditee');
Route::post('/audit/auditee_acceptdetails', [App\Http\Controllers\AuditManagementController::class, 'auditee_acceptdetails'])->name('audit.viewintimationdetails');

Route::post('/audit/auditee_partialchange', [App\Http\Controllers\AuditeeController::class, 'auditee_partialchange'])->name('audit.audit_datefixing');
Route::get('/audit/audit_particulars', [App\Http\Controllers\AuditeeController::class, 'audit_particulars'])->name('audit.auditee');
Route::post('/audit/auditee_accept', [App\Http\Controllers\AuditeeController::class, 'auditee_accept'])->name('audit.auditee');
Route::post('/audit/store_auditeeofficeusers', [App\Http\Controllers\AuditeeController::class, 'store_auditeeofficeusers'])->name('audit.store_auditeeofficeusers');
Route::post('/audit/fetch_auditeeofficeusers', [App\Http\Controllers\AuditeeController::class, 'fetch_auditeeofficeusers'])->name('audit.fetch_auditeeofficeusers');

Route::get('/download-auditor-diary', [App\Http\Controllers\AuditDiaryController::class, 'downloadDiary']);






});


// Route::get('/field_audit', function () {
//     return view('fieldaudit');
// });

// Protected Charge Route
// Route::middleware('check.session')->get('/create_charge', [App\Http\Controllers\UserManagementController::class, 'creatuser_dropdownvalues'])->name('create_charge')->defaults('viewName', 'usermanagement.createcharge');

// Route::middleware('check.session')->get('/create_charge', [App\Http\Controllers\UserManagementController::class, 'creatuser_dropdownvalues'])->name('create_charge')->defaults('viewName', 'usermanagement.createcharge');


// Route::get('/dashboard', function () {
//     dd(session()->all());  // Dump the session data to check if 'username' exists

//     return view('dashboard');
// })->middleware(['auth', 'no.cache', 'check.session'])->name('dashboard');



Route::post('/logout', [App\Http\Controllers\LoginController::class, 'logout'])->name('logout');







Route::post('/auditee_validatelogin', [App\Http\Controllers\LoginController::class, 'auditee_validatelogin'])->name('auditee_validatelogin');




Route::post('/auditdiary/fetchAllData', [App\Http\Controllers\AuditDiaryController::class, 'FetchDiarydetails'])->name('auditdiary.fetch');

Route::get('/auditeelogin', function () {
    return view('site/auditeelogin');
});

//Route::get('/auditeelogin', [App\Http\Controllers\CaptchaController::class, 'showCaptchaForm']);
//Route::get('/captcha', [CaptchaController::class, 'showCaptchaForm']);

use Mews\Captcha\Facades\Captcha;

Route::get('/captcha-reload', function () {
    // Generate and return a new CAPTCHA image
    return response()->json(['captcha' => captcha_src()]);
})->name('captcha.reload');


use Illuminate\Support\Facades\Session;


Route::get('/captcha-text', function () {
    $code = substr(str_shuffle('ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789'), 0, 6);
    Session::put('captcha_code', $code);
    return response()->json(['code' => $code]);
});



// Master ----- Designation Start-----
Route::resource('/designation', MastersController::class);
Route::post('/designation/designation_insertupdate', [MastersController::class, 'designation_insertupdate'])
    ->name('designationhome.designation_insertupdate');
Route::post('/designation/designation_fetchData', [MastersController::class, 'designation_fetchData'])->name('designation.designation_fetchData');
// Master ----- Designation End-----


// Master ----- Region Start-----
Route::get('/regionhome', function () {
    $dept  =   UserManagementModel::departmenttdetail();
    return view('masters.createregion', compact('dept'));
});
Route::post('/region/region_insertupdate', [MastersController::class, 'region_insertupdate'])
    ->name('regionhome.region_insertupdate');
Route::post('/master/region_fetchData', [MastersController::class, 'region_fetchData'])
    ->name('region.region_fetchData');

// Master ----- Region End-----



// Master ----- Menu Form Start-----
Route::get('/menu', function () {
    $menus = MastersModel::getTopLevelMenus('audit.mst_menu');
    return view('masters.createmenu', compact('menus'));
});
Route::post('/menu/menu_insertupdate', [MastersController::class, 'menu_insertupdate'])
    ->name('menuhome.menu_insertupdate');
Route::post('/master/menu_fetchData', [MastersController::class, 'menu_fetchData'])
    ->name('menu.menu_fetchData');
Route::post('menu/saveOrderId', [MastersController::class, 'saveOrderId'])->name('menu.saveOrderId');
// Master ----- Menu Form End-----

Route::get('/view_workallocated', function () {
    return view('audit/viewworkallocation');
});
Route::post('/audit/fetch_allocatedwork', [App\Http\Controllers\WorkAllocationController::class, 'fetch_allocatedwork'])->name('AuditSchedule.fetch_allocatedwork');
Route::post('/audit/update_exitmeet', [App\Http\Controllers\AuditSchedule::class, 'update_exitmeet'])->name('AuditSchedule.update_exitmeet');
Route::post('/audit/update_entrymeet', [App\Http\Controllers\AuditSchedule::class, 'update_entrymeet'])->name('AuditSchedule.update_entrymeet');



Route::get('/forgetpassword', function (Request $request) {
    return view('site.forgetpassword', ['user' => $request->query('user')]);
})->name('forgetpassword.page'); 

Route::get('/dashboardchangepassword', function () {
    return view('site/changepassword');
});

Route::get('/changepasswordfordashboard', [AccountSettings::class, 'dashboard_ChangePassword'])->name('dashboardchangepassword');

Route::get('/profile', function () {
    return view('site/profile');
});
Route::post('/forgetpassword', [LoginController::class, 'forgetpassword'])->name('forgetpassword');

Route::post('/changepassword', [AccountSettings::class, 'ChangePassword'])->name('changepassword');





/********************************************************************* FORMAT CONTROLLER  for  PDF & Word Files ---- Start ******************************************************************* */
Route::get('/List_Institute', function () {
    return view('audit/listinstitute');
});

Route::get('/trans_auditslip', function () {
    return view('audit/transauditslip');
});

use App\Http\Controllers\FormatController;

Route::get('/generate-pdf', [FormatController::class, 'previewgeneratepdf']);
Route::post('/getstatusflagsfromdb', [FormatController::class, 'getstatusflagfromDB']);
Route::get('/codeofethics', [FormatController::class, 'codeofethics']);
Route::get('/entrymeeting', [FormatController::class, 'entrymeeting']);
Route::get('/exitmeeting', [FormatController::class, 'exitmeeting']);
Route::get('/auditcertificate', [FormatController::class, 'auditcertificate']); 
Route::get('/preview-word-single', [FormatController::class, 'previewWordforSingleFile']);
Route::get('/partc_contents', [FormatController::class, 'PartC_Contents']); 

Route::get('/intimationletter', [FormatController::class, 'intimationletter']);
Route::get('/entrymeeting_editreport', [FormatController::class, 'entrymeeting_editreport']);
Route::get('/codeofethics_editreport', [FormatController::class, 'codeofethics_editreport']);
Route::get('/exitmeeting_editreport', [FormatController::class, 'exitmeeting_editreport']); 





Route::get('/List_Institute', [App\Http\Controllers\FormatController::class, 'view_fieldaudit'])->name('Format.List_Institute');
Route::get('trans_auditslip/{id}', [App\Http\Controllers\FormatController::class, 'audittrans_dropdown'])
    ->name('trans_auditslip')
    ->defaults('viewvalue', 'audit.transauditslip');
Route::post('/getstatusflagsfromdb', [FormatController::class, 'getstatusflagfromDB']);
Route::get('/preview-word', [FormatController::class, 'previewWordFile']);
Route::get('/single-slip-details', [FormatController::class, 'singleSlipDetails']);
Route::get('/download-word-file/{fileName}', [FormatController::class, 'downloadWordFile']);
Route::post('/delete-file', [FormatController::class, 'deleteFile']);

Route::post('/finalize-auditreport', [FormatController::class, 'finalize_auditreport']);
Route::post('/Report_Prefil', [FormatController::class, 'Report_Prefil'])->name('FiedAudit.Report_Prefil');
Route::post('/Finalize_PartA', [FormatController::class, 'Finalize_PartA'])->name('FiedAudit.Finalize_PartA');
Route::post('/save-slip-order', [FormatController::class, 'saveSlipOrder']);
Route::post('/annextures_upload', [FormatController::class, 'AnnextureUpload'])->name('annextures_upload');

Route::get('/get-annexure-data', [FormatController::class, 'getAnnexureData']);
Route::get('get-accountstatement-files/{auditscheduleid}', [FormatController::class, 'getUploadedAnnexures']);
Route::post('/delete-annexure-file', [FormatController::class, 'deleteAnnexureFile'])->name('deleteAnnexureFile');


/********************************************************************* FORMAT CONTROLLER  for  PDF & Word Files ---- End******************************************************************* */

/****************************************** Report Controller - Pending Paras report Start ********************************************************** */
Route::get('/pendingparra', [App\Http\Controllers\ReportController::class, 'pendingparra'])->name('FiedAudit.pendingparra');
Route::post('/getpendingparadetails', [App\Http\Controllers\ReportController::class, 'getpendingparadetails'])->name('Audit.getpendingparadetails');
Route::get('/get-slip-details/{slipId}', [App\Http\Controllers\ReportController::class, 'getSlipDetailsHistory'])->name('getSlipDetailsHistory'); // Route::get('/get-slip-details/{slipId}', [App\Http\Controllers\ReportController::class, 'getSlipDetails'])->name('getSlipDetails');

// Route::get('/get-slip-details/{slipId}', [App\Http\Controllers\ReportController::class, 'getSlipDetails'])->name('getSlipDetails');
Route::get('/get-sliphistory-details/{slipId}', [App\Http\Controllers\ReportController::class, 'getSlipHistoryDetails'])->name('getSlipHistoryDetails');
/****************************************** Report Controller - Pending Paras report End ********************************************************** */

/****************************************** Calendar Controller - Holiday & Institute Calendar Start********************************************************** */
Route::get('/holidaycalendar', function () {
    return view('audit/holidaycalendar');
});

Route::post('/add-holiday', [App\Http\Controllers\CalendarController::class, 'AddHoliday']);
Route::get('/get-holidays', [App\Http\Controllers\CalendarController::class, 'getHolidays']);
Route::delete('/delete-holiday/{id}', [App\Http\Controllers\CalendarController::class, 'RemoveHoliday']);
Route::get('/fetch-holidays', [App\Http\Controllers\CalendarController::class, 'FetchHolidays']);

###Institute Calender Events###
Route::get('/calendar', function () {
    return view('audit/calendar');
});
Route::get('/event-details', [App\Http\Controllers\CalendarController::class, 'getEventsDetails']);
Route::get('/events', [App\Http\Controllers\CalendarController::class, 'getEvents']);

/****************************************** Calendar Controller - Holiday & Institute Calendar End********************************************************** */
/********************************************************************* Followup - URL ******************************************************************* */

Route::get('/followup', function () {
    return view('lagacy/followup');
});
Route::get('/initfollowup', function () {
    return view('lagacy/initfollowup');
});

Route::get('followup', [App\Http\Controllers\LagacyController::class, 'followup_dropdown'])->name('followup')->defaults('viewName', 'lagacy.followup');
Route::post('/followup/getminordet', [App\Http\Controllers\LagacyController::class, 'getminordet'])->name('lagacy.followup');
Route::post('/lagacy/followup_insert', [App\Http\Controllers\LagacyController::class, 'followup_insert'])->name('lagacy.followup');
Route::post('/lagacy/fetch_lagacydata', [App\Http\Controllers\LagacyController::class, 'fetch_lagacydata'])->name('lagacy.followup');




/********************************************************************* Followup - URL ******************************************************************* */


// //---------------------------subworkallocation--------------------------------------------------------------------------
// // Route::get('/subworkallocation_form', function () {
// // return view('masters.createsubworkallocation');
// // });
// Route::post('getworkallocationBasedOnDept', [MastersController::class, 'getworkallocationBasedOnDept'])
//     ->name('getworkallocationBasedOnDept');
// Route::get('/subworkallocation_form', [MastersController::class, 'SubworkDeptFetch']);

// Route::post('/subworkallocation/subworkallocation_insertupdate', [MastersController::class, 'subworkallocation_insertupdate'])
//     ->name('subworkallocation.subworkallocation_insertupdate');

// Route::post('/master/subworkallocationtype_fetchData', [MastersController::class, 'subworkallocationtype_fetchData'])
//     ->name('subworkallocationtype.subworkallocationtype_fetchData');

// //------------------------------workallocaton--------------------------------------------------------------------

// Route::get('/workallocation_form', [MastersController::class, 'workallocationdeptfetch'])->name('workallocation_form');;

// //Route::get('/workallocation_form', [MastersController::class, 'newform']);
// Route::post('/workallocation/workallocation_insertupdate', [MastersController::class, 'workallocation_insertupdate'])
//     ->name('workallocationhome.workallocation_insertupdate');
// //Route::post('/newform_insertupdate', [MastersController::class, 'majorworkallocation']);
// Route::post('/master/workallocationtype_fetchData', [MastersController::class, 'workallocationtype_fetchData'])
//     ->name('workallocationtype.workallocationtype_fetchData');

//---------------------------------------------------------END---------------------------------------------------------
//------------------------------------------master sub- category-----------------------------------------------------------


// Route::get('/subcategory', [MastersController::class, 'fetchdept']);

// Route::post('/subcategory_insertupdate', [MastersController::class, 'subcategory_insertupdate'])
//     ->name('subcategory.subcategory_insertupdate');
// Route::post('/master/subcategory_fetchData', [MastersController::class, 'subcategory_fetchData'])
//     ->name('subcategory.subcategory_fetchData');



//------------------------------------------Change Request start-----------------------------------------------------------

Route::get('/changerequest', [App\Http\Controllers\AuditManagementController::class, 'changerequestdeptfetch'])->name('changerequest_form');


Route::post('/getregionbasedondeptforchangerequest', [App\Http\Controllers\AuditManagementController::class, 'getregionbasedondeptchangerequest']);
Route::post('/getdistrictbasedonregionchangerequest', [App\Http\Controllers\AuditManagementController::class, 'getdistrictbasedonregionchangerequest']);
Route::post('/getinstitutionbasedondistchangerequest', [App\Http\Controllers\AuditManagementController::class, 'getinstitutionbasedondistchangerequest']);
Route::post('/getquarterBasedOninst', [App\Http\Controllers\AuditManagementController::class, 'getquarterBasedOninst']);


Route::post('/changerequest_insertupdate', [App\Http\Controllers\AuditManagementController::class, 'changerequest_insertupdate'])
    ->name('changerequest.changerequest_insertupdate');


Route::post('/changerequest_fetchData', [App\Http\Controllers\AuditManagementController::class, 'changerequest_fetchData'])
    ->name('changerequest.changerequest_fetchData');




//------------------------------------------Change Request End-----------------------------------------------------------





// //------------------------------------------master category-----------------------------------------------------------



// Route::get('/category_form', [MastersController::class, 'categorydeptfetch']);
// Route::post('/category/category_insertupdate', [MastersController::class, 'category_insertupdate'])
//     ->name('category.category_insertupdate');
// Route::post('/master/category_fetchData', [MastersController::class, 'category_fetchData'])
//     ->name('category.category_fetchData');

//---------------------------------------------District------------------------------------------------------------------

// Route::get('/district', [MastersController::class, 'masterstatefetch']);
// Route::post('/masters/district_insertupdate', [MastersController::class, 'district_insertupdate'])
//     ->name('district.district_insertupdate');

// Route::post('/master/district_fetchData', [MastersController::class, 'district_fetchData'])
//     ->name('district.district_fetchData');


// //---------------------------------------------Department--------------------------------------------------------------------
// Route::get('/department', function () {
//     return view('masters.departmentconfig');
// });

// Route::post('/masters/department_insertupdate', [MastersController::class, 'department_insertupdate'])
//     ->name('department.department_insertupdate');

// Route::post('/master/department_fetchData', [MastersController::class, 'department_fetchData'])
//     ->name('department.department_fetchData');

// // Route::post('/login', [App\Http\Controllers\LoginController::class, 'login'])->name('login');

// // Dashboard route (Protected by check.session middleware)
// // Route::get('/dashboard', function () {
// //     return view('dashboard');
// // })->middleware(['auth', 'no.cache', 'check.session'])->name('dashboard');

// //-------------------------------------------Audit District-------------------------------------------------------------
// // Route::get('/auditdistrict', [MastersController::class, 'auditdistrictdeptfetch']);
// // Route::post('/auditdistrict/auditdistrict_insertupdate', [MastersController::class, 'auditdistrict_insertupdate'])
// //     ->name('auditdistrict.auditdistrict_insertupdate');

// // Route::post('/master/auditdistrict_fetchData', [MastersController::class, 'auditdistrict_fetchData'])
// //     ->name('auditdistrict.auditdistrict_fetchData');

//-------------------------------------------------------Group Form -----------------------------------------------------------------


// Route::get('/group', [MastersController::class, 'fetchdeptforgroup']);

// Route::post('/group/group_insertupdate', [MastersController::class, 'group_insertupdate'])
//     ->name('group.group_insertupdate');

// Route::post('/group/group_fetchData', [MastersController::class, 'group_fetchData'])
//     ->name('group.group_fetchData');



// // Master ---------------------------- MainObjection Start--------------------------------------
// Route::post('/get-categories', [MastersController::class, 'getCategoriesByDept'])->name('get.categories');
// Route::get('/mainobjection', function () {
//     $dept  =   UserManagementModel::deptdetail();
//     return view('masters.createmainobjection', compact('dept'));
// });
// Route::post('/mainobjection/mainobjection_insertupdate', [MastersController::class, 'mainobjection_insertupdate'])
//     ->name('mainobjectionhome.mainobjection_insertupdate');
// Route::post('/master/mainobjection_fetchData', [MastersController::class, 'mainobjection_fetchData'])
//     ->name('mainobjection.mainobjection_fetchData');

// // Master ----- MainObjection End-----
// // Master ----- Sub Objection Start-----
// Route::get('/subobjection', function () {
//     $dept  =   UserManagementModel::deptdetail();
//     // $mainobj = MastersModel::mainobjectiondetails('audit.mst_mainobjection');
//     return view('masters.createsubobjection', compact('dept'));
// });

// Route::post('/getobjectionBasedOnDept', [MastersController::class, 'getobjectionBasedOnDept']);


// Route::post('/subobjection/subobjection_insertupdate', [MastersController::class, 'subobjection_insertupdate'])
//     ->name('subobjectionhome.subobjection_insertupdate');
// Route::post('/master/subobjection_fetchData', [MastersController::class, 'subobjection_fetchData'])
//     ->name('subobjection.subobjection_fetchData');
// // Master ----- Sub Objection  End-----
