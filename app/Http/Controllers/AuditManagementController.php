<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Support\Facades\Crypt;
use App\Models\AuditManagementModel;
use App\Models\AuditModel;
use App\Models\AuditeeModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

use App\Models\DeptModel;
use App\Models\RegionModel;
use App\Models\DistrictModel;
use App\Models\AuditTeamModel;
use App\Models\DeptMapModel;
use App\Models\InstituteCategoryModel;
use App\Models\TypeofAuditModel;
use App\Models\AuditQuarterModel;
use App\Models\AuditPeriodModel;
use App\Models\AuditSubcategoryModel;
use App\Models\YearcodeMapping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\MajorWorkAllocationtypeModel;

use App\Models\SmsmailModel;
use App\Services\SmsService;
use App\Services\PHPMailerService;

use Illuminate\Http\Request;
use DataTables;
use App\Models\BaseModel;

class AuditManagementController extends Controller
{

    protected static $deptartment_table = BaseModel::DEPARTMENT_TABLE;
    protected static $institution_table = BaseModel::INSTITUTION_TABLE;
    protected static $auditplan_table = BaseModel::AUDITPLAN_TABLE;
    protected static $temprankusers_table = BaseModel::TEMPRANKUSERS_TABLE;
    protected static $designation_table = BaseModel::DESIGNATION_TABLE;
    protected static $userdetail_table = BaseModel::USERDETAIL_TABLE;
    protected static $auditplanteam_table = BaseModel::AUDITPLANTEAM_TABLE;
    protected static $auditplanteammem_table = BaseModel::AUDITPLANTEAMMEM_TABLE;
    protected static $typeofaudit_table = BaseModel::TYPEOFAUDIT_TABLE;
    protected static $mstauditeeinscategory_table = BaseModel::MSTAUDITEEINSCATEGORY_TABLE;
    protected static $instauditschedule_table = BaseModel::INSTSCHEDULE_TABLE;
    protected static $instauditschedulemem_table = BaseModel::INSTSCHEDULEMEM_TABLE;
    protected static $dist_table = BaseModel::DIST_Table;
    protected static $auditquarter_table = BaseModel::AUDITQUARTER_TABLE;
    protected static $subcategory_table = BaseModel::SUBCATEGORY_TABLE;





    public function create_userdet()
    {
        $session = session('charge');

        $distcode = $session->distcode;
        $deptcode = $session->deptcode;
        $dist_det = DB::table(self::$dist_table)
            ->where('distcode', $distcode)
            ->first();
        $dept_det = DB::table(self::$deptartment_table)
            ->where('deptcode', $deptcode)
            ->first();
        $quarter_det = AuditManagementModel::getquarterDet($deptcode);
        // ->where('deptcode', $deptcode)
        // ->orderBy('auditquartercode', 'asc')
        // ->get();

        return view('audit.auditplanning', compact('dist_det', 'dept_det', 'quarter_det'));
    }
    public function fetchall_automatedata(Request $request)
    {
        $session = session('charge');

        $distcode = $session->distcode;
        $deptcode = $session->deptcode;
        // $regioncode = $session->regioncode;
        // $checkparam = $request->checkparam;

        // $request->validate([
        //     'checkparam'     => 'required|string',

        // ]);
        //  return $checkparam;
        $data  = [
            'distcode'      => $distcode,
            'deptcode'      => $deptcode,
            // 'regioncode'    => $regioncode,
            // 'checkparam'    => $checkparam,

        ];
        // return $data;
        // return  $distcode;
        try {

            $audit_plan_status = AuditManagementModel::getUser_planStatus($data);


            // $audit_plan_status = AuditManagementModel::getAuditPlanStatus($checkdata);
            $plan_status = $audit_plan_status[0]->autoplanstatus;
            $user_status = $audit_plan_status[0]->userverified;

            if ($user_status == 'N' || $user_status == '' || empty($user_status)) {

            $details = AuditManagementModel::getAuditorUser($data);
        } else if ($user_status == 'Y' &&   $plan_status == 'Y') {

            $details = AuditManagementModel::getAuditors($deptcode, $distcode);
            //  return  $details;
        } else if ($user_status == 'Y' &&   $plan_status == 'F') {

            $details = AuditManagementModel::getAuditorsfromplan($deptcode, $distcode);
            //  return  $details;
        } else if ($user_status == 'Y' &&  ($plan_status == 'N' || $plan_status == '')) {

            $details = AuditManagementModel::getAuditors($deptcode, $distcode);
        }
            return response()->json(['success' => 'Users are fetched Successfully', 'audit_plan_status' => $audit_plan_status, 'planned_auditors' => $details]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching data'], 500);
        }
    }

    public function checkfordetails(Request $request)
    {
        try {
            $rules = [
                'distcode'     => 'required|string|regex:/^\d+$/',
                'deptcode'     => 'required|string|regex:/^\d+$/',
                'quarter_code'  => 'required|string',
            ];


            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }
            $distcode = $request->input('distcode');
            $deptcode = $request->input('deptcode');
            $auditquartercode       = $request->input('quarter_code');
            // $auditquartercode = 'Q4';
            $data = [
                'distcode'     => $distcode,
                'deptcode'     => $deptcode,
                'auditquartercode' =>  $auditquartercode,
            ];

            $check = AuditManagementModel::checkfordetails($data);
            $status =  $check[0]->readyforautomateplan;

            list($statusCode, $statusMessage) = explode(": ", $status, 2);

            // Trim values to remove extra spaces
            $statusCode = trim($statusCode);
            $statusMessage = trim($statusMessage);


            if ($statusCode == 'Error') {
                return response()->json(['error' => $statusMessage], 500);
            } elseif ($statusCode == 'Success') {

                $audit_plan_status = AuditManagementModel::getUser_planStatus($data);

                return response()->json(['success' => $statusMessage, 'audit_plan_status' => $audit_plan_status]);
            } else {
                return response()->json(['error' => 'An error occurred while fetching data'], 500);
            }
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
        // $deptocode
    }

    public function finalize_data(Request $request)
    {
        try {
            $rules = [
                'distcode'     => 'required|string|regex:/^\d+$/',
                'deptcode'     => 'required|string|regex:/^\d+$/',
                // 'quarter_code'  => 'required|string',
            ];


            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }
            $distcode = $request->input('distcode');
            $deptcode = $request->input('deptcode');
            $auditquartercode       = $request->input('quarter_code');



            $session = session('charge');

            // $distcode = $session->distcode;
            // $deptcode = $session->deptcode;
            // $regioncode = $session->regioncode;

            // $finaliseFlag == 'P';



            // $audit_plan = AuditManagementModel::getAuditPlanStatus($deptcode, $distcode, $regioncode);
            // $finaliseFlag = $audit_plan[0]->finaliseflag;



            // if ($finaliseFlag == 'F') {
            //     return response()->json(['error' => 'Audit Planning was already finalised'], 500);
            // }
            $auditors = AuditManagementModel::finalize_plan($deptcode, $distcode, $auditquartercode);

            $distributeplan_response_json = $auditors[0]->distributeauditteamplan;
            $distributeplan_response = json_decode($distributeplan_response_json, true);
            $distributeplan_status = $distributeplan_response['status'];
            //  return $distributeplan_status;
            if ($distributeplan_status == 'error') {
                return response()->json(['error' => $distributeplan_response['message']], 500);
            } else {
                $auditors_detail =  AuditManagementModel::getAuditors($deptcode, $distcode);
                $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
                $sentsms = $auditModel->sendscheduleInstitutes($distcode,$deptcode,$auditquartercode);
           
                return response()->json(['success' => 'success_finalise', 'auditors' => $auditors_detail]);
            }
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }
    public function automate_plan(Request $request)
    {
        try {

            $rules = [
                'distcode'     => 'required|string|regex:/^\d+$/',
                'deptcode'     => 'required|string|regex:/^\d+$/',
                'quarter_code'  => 'required|string',
            ];


            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            $distcode = $request->input('distcode');
            $deptcode = $request->input('deptcode');
            $auditquartercode = $request->input('quarter_code');


            $auditors = AuditManagementModel::automate_plan($deptcode, $distcode, $auditquartercode);

            $autoplan_status = $auditors[0]->status;
// return $autoplan_status;
            if ($autoplan_status == 'Error') {
                return response()->json(['error' => $auditors[0]->msg], 500);
            } else {
                $auditors_detail =  AuditManagementModel::getAuditors($deptcode, $distcode);
                return response()->json(['success' => 'success_automate', 'auditors' => $auditors_detail]);
            }
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function storeOrUpdateAudit(Request $request, $userId = null)
    {
        \Log::info($request->all());

        // Check if the statusflag is 'delete'
        $isDelete = $request->has('statusflag') && $request->statusflag === 'Y';
        $yeararr = [];

        // Conditional validation: Only validate required fields if not deleting
        if ($isDelete == 1) {
            $request->validate([
                'statusflag' => 'required|string',
                'auditplanid' => 'nullable|int',
            ]);
        } else {

            $request->validate([
                'instcatcode' => 'required|string|max:2',
                'instcode' => 'required|string|max:4',
                'auditteamcode' => 'required|string|max:2',
                'yearcode' => 'required|string|max:2',
                'auditcode' => 'required|string|max:2',
                'periodcode' => 'required|string|max:2',
                'statusflag' => 'nullable|string|max:2',
            ]);

            $isFinalize = $request->has('finalize') && $request->finalize === 'F';

            if ($isFinalize) {

                $FinalizedStsFlag = $request->finalize;
            } else {
                $FinalizedStsFlag = 'Y';
            }
            /**YearMultiple Array */
            $yeararr = $request->input('yearselected');

            $auditplanData = [
                'instid' => $request->instcode,
                'auditteamid' => $request->auditteamcode,
                'typeofauditcode' => $request->auditcode,
                'auditperiodid' => $request->yearcode,  // Insert the generated yearcodemapping_id here
                'auditquartercode' => $request->periodcode,
                'statusflag' => $FinalizedStsFlag
            ];


            if ((isset($_POST['auditplanid']) && $_POST['auditplanid'] != '') || $isFinalize == 'F')
                $userId =   $request->input('auditplanid');
            else
                $userId =   null;
        }


        try {
            if ($request->has('statusflag') && $request->statusflag === 'Y') {
                $auditplanid = Crypt::decryptString($request->auditencryptedplanid);
                $request->merge(['auditplanid' => $auditplanid]);
                $auditId = $request->auditplanid;
                $Audit = AuditModel::find($auditId);
                $auditplanData['statusflag'] = $request->statusflag;

                if ($Audit) {
                    // If the user exists, delete the record
                    $Audit = AuditModel::createIfNotExistsOrUpdate($auditplanData, $auditId, $yeararr, 'Delete');
                    return response()->json(['success' => 'Details deleted successfully.']);
                } else {
                    // If no such record exists to delete
                    return response()->json(['error' => 'Details not found.'], 404);
                }
            } else {
                // Pass the current user ID (if available) for the update or create logic
                $user = AuditModel::createIfNotExistsOrUpdate($auditplanData, $userId, $yeararr);
                if (!$user) {
                    // If user already exists (based on conditions), return an error
                    return response()->json(['error' => 'Details already exists'], 400);
                }

                // Return success message
                return response()->json(['success' => 'Audit Plan Data Saved successfully', 'user' => $user]);
            }
        } catch (QueryException $e) {
            // Handle database exceptions (e.g., duplicate entry)
            return response()->json(['error' => 'Database error occurred: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
            // Handle other exceptions
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Fetch user data for editing.
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchUserDataAudit(Request $request)
    {
        // Retrieve deptuserid from the request
        $auditplanid = Crypt::decryptString($request->auditplanid);
        $request->merge(['auditplanid' => $auditplanid]);

        $request->validate([
            'auditplanid'  =>  'required|integer'
        ], [
            'required' => 'The :attribute field is required.',
            'integer' => 'The :attribute field must be a valid number.'
        ]);

        // Ensure deptuserid is provided
        if (!$auditplanid) {
            return response()->json(['success' => false, 'message' => 'Audit ID not provided'], 400);
        }

        // Fetch user data based on deptuserid
        $auditplanNew = AuditModel::where('auditplanid', $auditplanid)->first(); // Adjust query as needed

        //Get Institute Name
        /*$GetInstitute = DeptMapModel::where('statusflag', '=', 'Y')
                                     ->where('instid', $auditplan->instid)
                                     ->first();*/

        $auditplan = AuditModel::query()
            ->join('audit.mst_institution as inst', 'auditplan.instid', '=', 'inst.instid')
            ->where('inst.statusflag', '=', 'Y')
            ->where('inst.instid', '=', $auditplanNew->instid)
            ->where('auditplan.auditplanid', '=', $auditplanid)
            ->first();
        $auditplan->typeofauditcode = $auditplanNew->typeofauditcode;
        $Yearmapping = YearcodeMapping::fetchYearmapById($auditplanid);
        foreach ($Yearmapping as $yeararr => $yearval) {
            $yearsGet[$yeararr] = $yearval->yearselected;
        }
        $auditplan['yearcode'] = $yearsGet;
        //$auditplan['yearcode']='2024 -2025';
        if ($auditplan) {
            return response()->json(['success' => true, 'data' => $auditplan]);
        } else {
            return response()->json(['success' => false, 'message' => 'Data not found'], 404);
        }
    }


    public function auditfetchAllData()
    {
        // Fetch all users
        $audits = AuditModel::fetchAllusers();

        $Slno = 1;
        foreach ($audits as $audit) {
            $Arr['RegionName'] = $audit->regionename;
            $Arr['DistName'] = $audit->distename;
            $ImplodeReg_Dist = implode('<br>', $Arr);

            $audit->Reg_Dist = $ImplodeReg_Dist;
            $audit->deptname = $audit->deptesname;
            $audit->instcatname = $audit->catename;
            $audit->instname = $audit->instename;
            $audit->auditteamname = $audit->teamname;
            $audit->typeofaudit = $audit->typeofauditename;



            $audit->encrypted_auditid = Crypt::encryptString($audit->auditplanid);

            // $Yearmapping = YearcodeMapping::fetchYearmapById($audit->auditplanid);

            // $audits = AuditPeriodModel::where('statusflag', '=', 'Y')->get();
            // if ($AuditPeriods->isNotEmpty()) {
            //     // Determine the minimum and maximum years
            //     $auditfromperiod = $AuditPeriods->min('fromyear'); // Earliest fromyear
            //     $audittoperiod = $AuditPeriods->max('toyear');     // Latest toyear
            // }
            //     $YearMasterArr = [];
            //     $index = 1;

            //     // Generate year ranges
            //     for ($year = $audittoperiod; $year >= $auditfromperiod; $year--) {
            //         $nextYear = $year + 1;
            //         $YearMasterArr[$index] = "$year-$nextYear";
            //         $index++;
            //     }
            //  }

            // foreach($Yearmapping as $yeararr => $yearval)
            // {
            //    $yearsGet[$yeararr]= $YearMasterArr[$yearval->yearselected];
            // }
            // $implodeArrYrs=implode('<br>',$yearsGet);
            // $audit->auditperiod = $implodeArrYrs;

            // $audit->auditquarter = $audit->auditquarter;

            // $audit->Slno = $Slno;
            // $Slno++;
        }
        // Return data in JSON format
        return response()->json(['data' => $audits]); // Ensure the data is wrapped under "data"
    }


    public function FilterByDept(Request $request)
    {

        $DeptMapping = DeptMapModel::where('statusflag', '=', 'Y')
            ->where('deptcode', $request->deptcode);

        if ($request->regioncode) {
            $DeptMapping->where('regioncode', $request->regioncode);
        }

        if ($request->distcode) {
            $DeptMapping->where('distcode', $request->distcode);
        }

        if ($request->instcatcode) {
            $DeptMapping->where('catcode', $request->instcatcode);
        }

        if ($request->instsubcatcode) {
            $DeptMapping->where('subcatid', $request->instsubcatcode);
        }

        $DeptMapping = $DeptMapping->get();


        $regioncode = [];
        $districtCode = [];
        $InstcatCode = [];
        //print_r($DeptMapping);
        foreach ($DeptMapping as $Deptkey => $DeptVal) {
            $regioncode[] = $DeptVal->regioncode;
            $districtCode[] = $DeptVal->distcode;
            $InstcatCode[] = $DeptVal->catcode;
        }
        if (sizeof($regioncode) > 0) {
            $regioncode = array_unique($regioncode);
        }


        if ($request->regioncode) {
            $districtCode = array_unique($districtCode);
        }

        if ($request->distcode) {
            $InstcatCode = array_unique($InstcatCode);

            $auditteammodalget = AuditTeamModel::where('statusflag', '=', 'F')
                ->where('deptcode', $request->deptcode)
                ->whereIn('distcode', [$request->distcode, 'A'])
                ->get();
        }

        $RegionFinal   = '';
        $DistrictFinal = '';
        $InstCategoryFinal = '';
        $InstNameFinal = '';
        $TypeofAuditFinal = '';
        $AuditQuarterFinal = '';
        $AuditPeriodFinal = '';


        if ($DeptMapping) {
            if ($request->instsubcatcode) {
                $InstNameFinal = self::ArrayCombineFunction($DeptMapping, 'instid', 'instename');
                return $InstNameFinal;
            }

            if ($request->instcatcode) {
                $DeptMappingfetch = DeptMapModel::where('statusflag', '=', 'Y')
                    ->where('deptcode', $request->deptcode)
                    ->where('catcode', $request->instcatcode)
                    ->first();


                if ($DeptMappingfetch->subcatid != '') {
                    $AuditSubcategoryModel = AuditSubcategoryModel::where('statusflag', '=', 'Y')
                        ->where('catcode', $request->instcatcode)
                        ->where('auditeeins_subcategoryid', $DeptMappingfetch->subcatid)
                        ->get();
                    $DynField = 'subcategory';
                    $InstSubCategoryFinal = self::ArrayCombineFunction($AuditSubcategoryModel, 'auditeeins_subcategoryid', 'subcatename');
                    $response = $DynField . '~~' . $InstSubCategoryFinal;
                    return $response;
                } else {
                    $InstNameFinal = self::ArrayCombineFunction($DeptMapping, 'instid', 'instename');

                    $DynField = 'institutename';
                    $response = $DynField . '~~' . $InstNameFinal;
                    return $response;
                }
            }

            if ($request->distcode) {
                $InstCategory = InstituteCategoryModel::where('statusflag', '=', 'Y')
                    ->whereIn('catcode', $InstcatCode)
                    ->get();

                $InstCategoryFinal = self::ArrayCombineFunction($InstCategory, 'catcode', 'catename');
                $AuditTeam = self::ArrayCombineFunction($auditteammodalget, 'auditplanteamid', 'teamname');

                return $InstCategoryFinal . '~' . $AuditTeam;
            }

            if ($request->regioncode) {
                $district = DistrictModel::where('statusflag', '=', 'Y')
                    ->whereIn('distcode', $districtCode)
                    ->get();
                $DistrictFinal = self::ArrayCombineFunction($district, 'distcode', 'distename');
                return $DistrictFinal;
            }

            $region = RegionModel::where('statusflag', '=', 'Y')
                ->whereIn('regioncode', $regioncode)
                ->get();
            $RegionFinal = self::ArrayCombineFunction($region, 'regioncode', 'regionename');

            $AuditQuarter = AuditQuarterModel::where('statusflag', '=', 'Y')
                ->where('deptcode', $request->deptcode)
                ->get();

            $AuditPeriod = AuditPeriodModel::where('statusflag', '=', 'Y')
                ->first();

            $TypeofAudit = TypeofAuditModel::where('statusflag', '=', 'Y')
                ->where('deptcode', $request->deptcode)
                ->get();

            $auditteammodalget = AuditTeamModel::where('statusflag', '=', 'F')
                ->where('deptcode', $request->deptcode)
                ->get();


            $auditfromperiod = $AuditPeriod->fromyear;
            $audittoperiod = $AuditPeriod->toyear;

            $AuditPeriodFinal = $auditfromperiod . ' - ' . $audittoperiod;
            $AuditQuarterFinal = self::ArrayCombineFunction($AuditQuarter, 'auditquartercode', 'auditquarter');
            $TypeofAuditFinal = self::ArrayCombineFunction($TypeofAudit, 'typeofauditcode', 'typeofauditename');


            return $RegionFinal . '~' . $AuditPeriodFinal . '~' . $AuditQuarterFinal . '~' . $TypeofAuditFinal;
        }
    }

    public function ArrayCombineFunction($ARR, $aaa, $bbb)
    {

        $Final = [];
        $Id = [];
        $Name = [];

        if ($ARR) {
            foreach ($ARR as $ArrVal) {
                $Id[]   = $ArrVal->$aaa;
                $Name[] = $ArrVal->$bbb;
            }

            if (sizeof($Id) > 0 && sizeof($Name) > 0) {
                $Final = array_combine($Id, $Name);
            }
        }

        return json_encode($Final);
    }

    public function creatuser_dropdownvalues($view)
    {
        $dept = DeptModel::where('statusflag', '=', 'Y')
            ->orderBy('orderid', 'asc')
            ->get();

        $district = DistrictModel::where('statusflag', '=', 'Y')
            ->get();

        $region = RegionModel::where('statusflag', '=', 'Y')
            ->get();


        return view($view, compact('dept', 'district', 'region'));  // Using 'district' to pass it to the view
    }

   public function audit_plandetails(Request $request)
    {
        try {
            $quartercode = $request->quartercode;
            $session = $request->session();
            if ($session->has('user')) {
                $user = $session->get('user');
                $userid = $user->userid ?? null;
            } else {
                return "No user found in session.";
            }
            $audit_plandetail = AuditManagementModel::fetch_auditplandetails($userid, $quartercode);
            foreach ($audit_plandetail as $item) {
                $item->encrypted_auditplanid = Crypt::encryptString($item->auditplanid);
                $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);
                $item->encrypted_instid = Crypt::encryptString($item->instid);
            }

            return response()->json(['data' => $audit_plandetail]); // Ensure the data is wrapped under "data"
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    public function creatauditschedule_dropdownvalues(Request $request)
    {
        // $auditplanid = $request->query('auditplanid'); // Default to '1' if no value is provided.
        if ($request->auditplanid) {
            $auditplanid = Crypt::decryptString($request->auditplanid);
            $userid = $request->userid;
        } else {
            // print_r($auditplanid);
            $session = $request->session();
            if ($session->has('user')) {
                $user = $session->get('user');
                $userid = $user->userid ?? null;
            } else {
                return "No user found in session.";
            }
        }

        // echo $auditplanid;

        // echo $userid;
        // Fetch the data based on the provided auditplanid
        $inst =   AuditManagementModel::auditplandet($auditplanid, $userid);


        $catcode = $inst->first()->catcode;
        $deptcode = $inst->first()->deptcode;
        $subcatid = $inst->first()->subcatid;
        $planquartercode = $inst->first()->auditquartercode; //fetch from plandet

        $fetchcurrquarter = AuditManagementModel::getCurrentQuarter($deptcode, $planquartercode);
        //  $str_Quarter = "Q2";
        $str_Quarter = $fetchcurrquarter->quarterfrom;
        $str_Quarter = date('Y-m-01', strtotime($str_Quarter));

        $end_Quarter = $fetchcurrquarter->quarterto;
        $end_Quarter = date('Y-m-t', strtotime($end_Quarter));

        $Quarter = ['fromquarter' => $str_Quarter, 'toquarter' => $end_Quarter];

        $Accountparticulars = self::audit_particulars($catcode, $deptcode, $subcatid);
        $quartercode    =   $inst->first()->auditquartercode;
        $schdel = DB::table('audit.inst_auditschedule')
            ->where('auditplanid', '=', $inst->first()->auditplanid)
            ->get();

        if (count($schdel) > 0) {
            $rcno   =   $schdel->first()->rcno;
        } else {
            $deptdel = DB::table('audit.mst_dept')
                // ->where('auditplanid', '=', $inst->first()->auditplanid)
                ->where('deptcode', '=', $deptcode)
                ->get();

            if ($deptdel->isNotEmpty()) {
                // Ensure there's a valid first item before accessing its properties
                $firstItem = $deptdel->first();

                if ($firstItem) {
			$yearSuffix = date('y');
                    // Now safely access properties on the first item
                    $rcnocount = $firstItem->rcno;
                    $deptsname = $firstItem->deptesname;
                    $deptfirstcharacter = substr($deptsname, 0, 1);  // Corrected the typo

                    // Increment the count, and ensure it's padded with leading zeros
                    $incrementcount = $rcnocount ? $rcnocount + 1 : 1;

                    // Pad the increment count with leading zeros to make it 4 digits
                    $incrementcount = str_pad($incrementcount, 4, '0', STR_PAD_LEFT);

                    // Concatenate the values
                    $rcno = $deptfirstcharacter . $yearSuffix . $quartercode . $incrementcount;
                }
            }
        }

        $auditperiod = AuditPeriodModel::select("auditperiodid", DB::raw("CONCAT(fromyear, ' - ', toyear) AS audit_period"))
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->where('financestatus', 'N')
            ->orderBy('fromyear', 'desc')
            ->get();

        $annadhanamperiod = AuditPeriodModel::select("auditperiodid", DB::raw("CONCAT(fromyear, ' - ', toyear) AS audit_period"))
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->where('financestatus', 'Y')
            ->orderBy('fromyear', 'desc')
            ->get();

        $DraftStatus['auditschid'] = '';
        $DraftStatus['exists'] = 'N';

        $hasexists = DB::table('audit.inst_auditschedule')
            ->where('auditplanid', $auditplanid)
            ->where('statusflag', 'Y')
            ->exists();

        if ($hasexists) {
            $schedules = DB::table('audit.inst_auditschedule')
                ->select('auditscheduleid')
                ->where('auditplanid', $auditplanid)
                ->where('statusflag', 'Y')
                ->first();

            $DraftStatus['auditschid'] = $schedules->auditscheduleid;
            $DraftStatus['exists'] = 'Y';
        }


        // Redirect to the view and pass the data using compact
        return view('audit.auditdatefixing', compact('inst', 'Accountparticulars', 'rcno', 'auditperiod', 'annadhanamperiod', 'Quarter', 'DraftStatus'));
    }


    public function audit_particulars($catcode = '', $deptcode = '', $subcatid = '')
    {
        $audit_particulars = MajorWorkAllocationtypeModel::callforrecords($catcode, $deptcode, $subcatid);
        //print_r($audit_particulars);exit;
        //$account_particulars = AccountParticularsModel::where('statusflag', '=', 'Y')
        //->orderBy('accountparticularsename', 'asc')
        //->get();
        $account_particulars = DB::table('audit.mst_accountparticulars')
            ->where('statusflag', '=', 'Y')
            ->orderBy('accountparticularsename', 'asc')
            ->get();

        if ($audit_particulars) {
            return response()->json([
                'data' => $audit_particulars,
                'account_particulars' => $account_particulars
            ]);
        }
    }
    public function audit_members(Request $request)
    {
        $planid = $request->input('planid');

        $inst = AuditManagementModel::audit_members($planid);
        if(!$inst)
        {
            return response()->json([
                'error' => true,
                'message' => 'failedtofetch'
            ]);

        }
        return response()->json($inst);
    }
    public function fetchAllScheduleData(Request $request)
    {
        $sessiondetails = session('charge');
        $deptcode = $sessiondetails->deptcode;
        $inst = AuditManagementModel::fetchAllScheduleData($deptcode);

        foreach ($inst as $item) {
            $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);
        }




        // Return data in JSON format
        return response()->json(['data' => $inst]); // Ensure the data is wrapped under "data"

    }
    public function fetchschedule_data(Request $request)
    {
        // $auditscheduleid = Crypt::decryptString($request->auditscheduleid);
        $auditscheduleid = $request->auditscheduleid;
        $inst = AuditManagementModel::fetchsingle_scheduledata($auditscheduleid);

        foreach ($inst as $item) {
            $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);
        }


        if ($inst) {
            return response()->json(['success' => true, 'data' => $inst]);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }
   
    public function storeOrUpdateAuditSchedule(Request $request, $userId = null)
    {

        $chargedel  =   session('charge');
        $deptcode   =   $chargedel->deptcode;
        $data = $request->all();


        if ($request->action == 'update') {
            $as_code = Crypt::decryptString($request->as_code);
            $request->merge(['as_code' => $as_code]);
        }

        $from_date = Carbon::createFromFormat('d/m/Y', $request->input('from_date'))->format('Y-m-d');
        $to_date = Carbon::createFromFormat('d/m/Y', $request->input('to_date'))->format('Y-m-d');
        $request->merge(['from_date' => $from_date]);
        $request->merge(['to_date' => $to_date]);


        $tm_uid = $request->input('tm_uid');
        $json_tm_uid = json_encode($tm_uid);
        $request->merge(['tm_uid' =>  $json_tm_uid]);

        $request->validate([
            'ap_code'       => 'required',
            'from_date'     =>  'required|date|date_format:Y-m-d|',
            'to_date'       =>  'required|date|date_format:Y-m-d|',
            'rc_no'         =>  'required',
            'tm_uid'        =>  'required|json',
            'th_uid'        =>  'required',
            'yearselected'  =>  'required'

        ], [
            'required' => 'The :attribute field is required.',
            'alpha' => 'The :attribute field must contain only letters.',
            'integer' => 'The :attribute field must be a valid number.',
            'regex'     =>  'The :attribute field must be a valid number.',
            'email' => 'The :attribute field must be a valid email address.',
            'date' => 'The :attribute field must be a valid date.',
            'max' => 'The :attribute field must not exceed :max characters.',

        ]);


        $annadhanam_yearselected =$request->input('annadhanam_yearselected');

        if($annadhanam_yearselected)
        {
            $annadhanam_yearselected = $annadhanam_yearselected;
        }else
        {
            $annadhanam_yearselected =[];
        }

        $data = [
            'auditplanid' => $request->input('ap_code'),
            'fromdate' => $request->input('from_date'),
            'todate' =>   $request->input('to_date'),
            'rcno' => $request->input('rc_no'),
            'statusflag' =>  $request->input('finaliseflag'),
	    'diaryflag' => 'N',
            'workallocationflag'    => 'N',
            'yearselected' => $request->input('yearselected'),
            'annadhanam_yearselected' =>$annadhanam_yearselected


        ];
        $sessiondet = session('user');
        $sessionuserid =  $sessiondet->userid;


        if ($request->action == 'update') {
            $audit_scheduleid =    $request->input('as_code');
        } else
            $audit_scheduleid =   null;


        try {


            $query = DB::table(self::$instauditschedule_table)
                ->where('auditplanid', $request->input('ap_code'))  // Filter by auditplanid
                ->whereNotIn('statusflag', ['C', 'R', 'S','N']);  // Exclude rows where status is either 'C' or 'R'


            // Add a condition for 'update' action
            if ($request->action === 'update') {
                $query->where('auditscheduleid', '!=', $audit_scheduleid);
            }

            $Exists_Instauditschedule = $query->first();

            if ($Exists_Instauditschedule) {
                return response()->json([
                    'error' => true,
                    'message' => 'already_audit_scheduled'
                ], 400);
            }


            // Call the model method for create or update
            $teamMemberIds11 = json_decode($request->input('tm_uid'), true);
            if (!is_array($teamMemberIds11)) {
                return response()->json(['error' =>true,'message'=> 'invalid_teamuser'], 400);
            }
            // $request->merge(['auditscheduleid' =>  $new_auditschedule_id]);
            // Insert each team member using the TeamMember model

            $teamMemberIds1 = $teamMemberIds11;
            $userIds = array_merge([$request->input('th_uid')], $teamMemberIds1);
            //$userIds = $teamMemberIds1;
            $conflictFound = false;

            //MapinstUsercountGet
            $InsufficientUser = AuditManagementModel::UserMatchCheck($request->input('ap_code'));

            if($InsufficientUser == 'insufficient_head_count')
            {
                return response()->json([
                    'success' => true,
                     'message' => 'insufficient_head_count'
                ], 400);

            }else if($InsufficientUser == 'insufficient_user_count')
            {
                return response()->json([
                    'success' => true,
                     'message' => 'insufficient_user_count'
                ], 400);

            }

            // Check if any of the users already exist with the same from and to dates
            foreach ($userIds as $userId) {
                // Build the query to check for overlapping or in-between dates
                $query = AuditManagementModel::DatecheckAuditschedule($userId, $request->input('from_date'), $request->input('to_date'));


                 if ($request->action === 'update') {
       		
 		 $query->where('ism.auditscheduleid', '!=', $audit_scheduleid);
    
		}
  		 $query->whereNotIn('ism.statusflag', ['C', 'R', 'S']);  // Exclude rows where status is 'C

                $existing = $query->first();

                if ($existing) {
                    $conflictFound = true;
                    break;
                }
            }


            if ($conflictFound) {
                return response()->json([
                    'success' => true,
                     'message' => 'already_audit_scheduled_with_daterange'
                ], 400);

            }

            $beforeinsertflag = $request->input('beforeinsert');

            if ($beforeinsertflag == 'finalise_beforeinsert') {
                return response()->json([
                    'finalise_beforeinsert' => "success"
                ]);
            }

            // Call the model method for create or update
            $audit_schedule = AuditManagementModel::createIfNotExistsOrUpdateAuditSchedule($data, $audit_scheduleid, $sessionuserid);
            // print_r($audit_schedule);exit;
            if ($audit_schedule) {

                if ($request->action == 'update') {

                    /*  $membersexist = AuditManagementModel::fetchteamMembers($audit_scheduleid);

                    if ($membersexist->isNotEmpty()) {
                        // Extract the array of member IDs from the existing records
                        $existingMemberIds = $membersexist
                            ->filter(function ($member) {
                                return $member->auditteamhead === 'N' && ($member->statusflag === 'Y' || $member->statusflag === 'R');
                            })
                            ->pluck('userid')
                            ->toArray();


                        // Assuming $newMemberIds is the array of new members you want to compare
                        $newMemberIds = is_string($tm_uid) ? json_decode($tm_uid, true) : $tm_uid;

                        // Find the difference between the existing members and new members
                        $membersToRemove = array_diff($existingMemberIds, $newMemberIds);
                        $membersToAdd = array_diff($newMemberIds, $existingMemberIds);


                        // Optionally, you can perform actions with $membersToRemove and $membersToAdd
                        // For example, delete members to remove
                        if (sizeof($membersToRemove) > 0) {
                            foreach ($membersToRemove as $memberId) {
                                AuditManagementModel::updateAuditScheduleMem($membersToRemove, $audit_scheduleid, $memberId, 'N', 'N', $sessionuserid);
                            }
                            if (!empty($membersToAdd)) {

                                foreach ($membersToAdd as $memberId) {
                                    AuditManagementModel::insertAuditScheduleMem($audit_scheduleid, $memberId, $request->input('from_date'), $request->input('to_date'), 'N', $sessionuserid);
                                }
                            }
                            if (empty($membersToAdd) && empty($membersToRemove)) {*/
                    $statusflag = 'Y';
                    $audit_schedule_member =   AuditManagementModel::update_teamstatus('Y', $audit_scheduleid, $request->input('from_date'), $request->input('to_date'));

                    /* }
                        }
                    }*/

                    $audit_scheduleid = $audit_scheduleid;
                } else {


                    $teamMemberIds = json_decode($request->input('tm_uid'), true);
                    if (!is_array($teamMemberIds)) {
                        return response()->json(['error' =>true,'message'=> 'invalid_teamuser'], 400);
                    }

                    // insert auditscheduled teamhead details;
                    AuditManagementModel::insertAuditScheduleMem($audit_schedule, $request->input('th_uid'), $request->input('from_date'),  $request->input('to_date'), 'Y', $sessionuserid,);
                    // insert auditscheduled teammember details;
                    foreach ($teamMemberIds as $memberId) {
                        AuditManagementModel::insertAuditScheduleMem($audit_schedule,  $memberId,  $request->input('from_date'),  $request->input('to_date'),  'N', $sessionuserid,);
                    }


                    $currentRcno = DB::table('audit.mst_dept')
                        ->where('deptcode', $deptcode)
                        ->value('rcno'); // `value()` will return the first column's value

                    if ($currentRcno !== null) {
                        // Increment the rcno
                        $incrementedRcno = $currentRcno + 1;

                        // Update the rcno value
                        AuditManagementModel::updateRcno($deptcode, $incrementedRcno);
                    } else {
                        AuditManagementModel::updateRcno($deptcode, '1');
                    }
                    $audit_scheduleid = $audit_schedule;
                }

                $status = $request->input('finaliseflag');
                
                $auditplanid=$request->input('ap_code');
                $inst =   AuditManagementModel::auditplandet($auditplanid, $sessionuserid);


                $catcode = $inst->first()->catcode;
                // Access the 'catcode' attribute
                $deptcode = $inst->first()->deptcode;
                $subcatid = $inst->first()->subcatid;
                $Accountparticulars = self::audit_particulars($catcode, $deptcode, $subcatid);
                $Accountparticulars=$Accountparticulars->original;
                $Accountparticulars=$Accountparticulars['data'];

                $Accountparticulars = $Accountparticulars
                                ->pluck('callforrecordsid')
                                ->toArray();

                

                if ($status == 'F') {
                    $json_checked_CFR = json_encode($Accountparticulars);

                    $CFRSelected = AuditManagementModel::CFRStoreData($audit_scheduleid, $json_checked_CFR);
                    $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
                    $sentsms = $auditModel->sendIntimation($audit_scheduleid);
                //    $sentsms = AuditManagementModel::sent_intimation($audit_scheduleid);

                  //  print_r($sentsms);
                    return response()->json([
                        'success' => true,
                         'message' => 'audit_scheduled_finalize'
                    ], 201);
                 
                 
                } else {
                   /* $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
                    $sentsms = $auditModel->sendIntimation($audit_scheduleid);
print_r($sentsms);exit;*/


                                    
                    return response()->json([
                        'success' => true,
			'schdeuleid' => $audit_schedule,
                         'message' => 'audit_scheduled_success'
                    ], 201);
                }
            }
        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


    public function auditee_intimation(Request $request)
    {
        $session = $request->session();
	$quartercode = $request->quartercode;
        if ($session->has('user')) {
            $user = $session->get('user');
            $userid = $user->userid ?? null;
        } else {
            return "No user found in session.";
        }

	$audit_plandetail = AuditManagementModel::fetch_auditscheduledetails($userid, $quartercode);
        foreach ($audit_plandetail as $item) {
            // return $audit_plandetail;

            $item->encrypted_auditplanid = Crypt::encryptString($item->auditplanid);
            $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);

            // return $audit_plandetail;
            $nodalname = $item->nodalname;
            $nodaldesig = $item->nodaldesignation;
            $item->nodalperson_details = $nodalname . '<br>' . $nodaldesig;

            $nodalemail = $item->nodalemail;
            $nodalmobile = $item->nodalmobile;
            $item->nodalperson_contact = $nodalmobile . '<br>' . $nodalemail;
            unset($item->auditscheduleid);

        }

        return response()->json(['data' => $audit_plandetail]); // Ensure the data is wrapped under "data"

        // print_r($audit_plandetail);
    }

    public function auditee_acceptdetails(Request $request)
    {
        $request->validate([
            'auditscheduleid'     => 'required',
        ]);
        $auditscheduleid = Crypt::decryptString($request->auditscheduleid);
        $account_particularsaccept = AuditManagementModel::fetch_Accountaccepteddetails($auditscheduleid);
        $cfr_saccept = AuditManagementModel::fetch_cfraccepteddetails($auditscheduleid);
        $auditeeuserdetails=AuditeeModel::fetch_auditeeofficeusers($auditscheduleid);

        return response()->json([
            'data'               => $account_particularsaccept,
            'cfr'                => $cfr_saccept,
            'auditeeuserdetails' => $auditeeuserdetails
        ]);
        // return response()->json(['data' => $account_particularsaccept]); // Ensure the data is wrapped under "data"
    }

    /* public static function CancelAuditschedule(Request $request)
    {
        $cancelschedule = AuditManagementModel::CancelSchedule($request->scheduleid,$request->cancel_remarks);

        return $cancelschedule;

    }*/

    public static function CancelorRescheduleAudit(Request $request)
    {

        $session = session('user');
        $sessionuser =  $session->userid;
        $data = [
            'auditscheduleid' => $request->scheduleid,
            'remarks' => $request->Remarks,
            'statusflag' => $request->statusflag,
            'updatedby' => $sessionuser,
            'updatedon' => View::shared('get_nowtime'),
        ];
        $CancelorRescheduleAudit = AuditManagementModel::CancelorReSchedule($data);
        if ($CancelorRescheduleAudit) {

            if ($request->statusflag == 'R') {
                $response = 'Audit Rescheduled Successfully!';
            } else if ($request->statusflag == 'S') {
                $response = 'Audit Suspended Successfully!';
            } else {
                $response = 'Audit Cancelled Successfully!';
            }
        }
        return $response;
    }

public function fetchinstitution(Request $request)
    {
        $deptcode = $request->input('deptcode');
        $regioncode = $request->input('regioncode');
        $distcode = $request->input('distcode');

        $institution = AuditManagementModel::fetchInstitutionData($deptcode, $regioncode, $distcode);


        //print_r($institution);
        return response()->json([
            'success' => true,
            'auditor' => $institution,
        ]);
    }
    public function fetchteam(Request $request)
    {
        $deptcode = $request->input('deptcode');
        $regioncode = $request->input('regioncode');
        $distcode = $request->input('distcode');
        $auditteamid = $request->input('auditteamid');
        $instid = $request->input('instid');
        //echo $auditteamid;

        $teams = AuditManagementModel::fetchTeamData($deptcode, $regioncode, $distcode, $auditteamid, $instid);

        //print_r($teams);
        return response()->json([
            'success' => true,
            'auditor' => $teams['scheduledauditors'],
            'membercount' => $teams['membercount'],
        ]);
    }

 public function getAuditors_updateplanuser(Request $request)
    {
        $distcode = $request->distcode;
        $deptcode = $request->deptcode;
        $regioncode = $request->regioncode;
        $auditteamid = $request->auditteamid;
        // print_r( $distcode);
        // $teamcode = $request->teamcode;

        if ($request->auditteamid)
            $auditteamid = Crypt::decryptString($request->auditteamid);
        else    $auditteamid   =   '';


        $auditors    =   AuditManagementModel::getauditors_updateplanuser($deptcode,$regioncode,$distcode,$auditteamid);

        return response()->json(['success' => true, 'auditor' => $auditors]);
    }


public function auditteam_insertupdate(Request $request)
    {
        // print_r($request->all());
        // exit;
        try {
            $session = session('user');
            $userId = $session->userid;

            $action = $request->input('action');
            $finaliseflag = $request->input('finaliseflag');
            $auditteamid = $action === 'update' ? Crypt::decryptString($request->input('auditteamid')) : null;

            $request->validate([
                'oldteamhead'     => 'nullable|string',
                'oldteammembers'  => 'nullable|string',
                'newteamhead'     => 'nullable|string',
                'newteammembers'  => 'nullable|string',
                'remarks'         => 'nullable|string',
            ]);

            $newteammembers = $request->input('newteammembers');
            $newteammembers = $newteammembers ? json_decode($newteammembers, true) : [];

            $data = [
                'auditplanid'    => $request->input('instid'),
                'newteamhead'     => $request->input('newteamhead'),
                'newteammembers'  => $request->input('newteammembers') !== null ? json_encode(json_decode($request->input('newteammembers'))) : null,
                'remarks'        => $request->input('remarks'),
                'updatedby'      => $userId,
                'updatedon'      => View::shared('get_nowtime'),
            ];


            if ($action === 'insert') {
                $data['createdby'] = $userId;
                $data['createdon'] = View::shared('get_nowtime');
                $data['oldteamhead']     = $request->input('oldteamhead');
                $data['oldteammembers']  = $request->input('oldteammembers') !== null ? json_encode(json_decode($request->input('oldteammembers'))) : null;
            }

            $data['statusflag'] = $finaliseflag === 'Y' ? 'F' : 'S';
            $uploadid = $request->input('uploadid');

            //  print_r(($request->hasFile('file')));
            ////File Upload////

            if ((($action === 'insert') || ($action === 'update')) && ($request->hasFile('file'))) {

                $destinationPath = 'uploads/alterplan';
                $sessioncharge = session('charge');
                $designationpathArray = [
                    $request->input('deptcode'),
                    $request->input('regioncode'),
                    $request->input('distcode'),
                    $request->input('instid'),
                    View::shared('alterplan')

                ];
                //return   $designationpathArray;
                if ($uploadid) {
                    $uploadResult = $this->fileUploadService->uploadFile($request->file('file'), $destinationPath, $uploadid,  $designationpathArray);
                } else {
                    // echo 'ads';
                    $uploadResult = $this->fileUploadService->uploadFile($request->file('file'), $destinationPath, '',  $designationpathArray);
                }

                $fileuploadId = $uploadResult->getData()->fileupload_id ?? null;
                //return $fileuploadId;
                $data['fileuploadid'] = $fileuploadId;
            } else {
                $data['fileuploadid'] = NULL;
            }

            // exit;
            $result = AuditManagementModel::updateauditplanuser($data, $auditteamid);

            if ($result['status']) {
                return response()->json([
                    'success' => true,
                    'type' => $result['type'],
                    'message' => $result['message']
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $result['message']
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }



  public function fetchUpdateplanTeam(Request $request)
    {
        // echo $request->auditteamid;
        // echo 'hi';
        // exit;
        // Decrypt the audit team ID from the request, if exists
        $auditteamid = $request->has('auditteamid') ? Crypt::decryptString($request->auditteamid) : null;

        // echo $auditteamid;
        // exit;

        // Instantiate the model and call the method
        $auditManagementModel = new AuditManagementModel();
        $updatedteam = $auditManagementModel->fetchUpdateuserplanData($auditteamid);

        // print_r($updatedteam );

        // exit;


        // Make sure $updatedteam is a collection or array before looping
        if ($updatedteam && $updatedteam->isNotEmpty()) {
            foreach ($updatedteam as $all) {
                // Check if the property exists before using it
                if (isset($all->auditteamsdraftid)) {
                    $all->encrypted_auditteamsdraftid = Crypt::encryptString($all->auditteamsdraftid);
                    // Remove the original auditplanid
                    unset($all->auditteamsdraftid);
                }
            }
        }

        // print_r($updatedteam);
        // exit;

        // Return the response
        return response()->json([
            'success' => $updatedteam->isNotEmpty(),
            'message' => $updatedteam->isEmpty() ? 'User not found' : '',
            'data' => $updatedteam->isEmpty() ? null : $updatedteam
        ], $updatedteam->isEmpty() ? 404 : 200);
    }



    public function fetchupdatedata(Request $request)
    {
        // $auditteamid = $request->auditteamid;

        $auditteamid = $request->has('auditteamid') ? Crypt::decryptString($request->auditteamid) : null;

        if (!$auditteamid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid audit team ID provided.'
            ], 400);
        }

        $auditManagementModel = new AuditManagementModel();
        $updatedteam = $auditManagementModel->fetchUpdateuserplanData($auditteamid);

        if ($updatedteam->isNotEmpty()) {
            foreach ($updatedteam as $all) {
                if (isset($all->auditteamsdraftid)) {
                    $all->encrypted_auditteamsdraftid = Crypt::encryptString($all->auditteamsdraftid);
                    unset($all->auditteamsdraftid);
                }
            }
        }

        return response()->json([
            'success' => $updatedteam->isNotEmpty(),
            'message' => $updatedteam->isEmpty() ? 'User not found' : '',
            'data' => $updatedteam->isEmpty() ? null : $updatedteam
        ], $updatedteam->isEmpty() ? 404 : 200);
    }

public static function changerequestdeptfetch()
{
    $dept = AuditManagementModel::commondeptfetch(); 

    return view('audit.changerequest', compact('dept'));
}


public function getquarterBasedOninst(Request $request)
{
    $request->validate([
        'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
    ], [
        'required' => 'The :attribute field is required.',
        'regex'    => 'The :attribute field must be a valid number.',
    ]);

    $deptcode = $request->input('deptcode');
    $instmappingcode = $request->input('instmappingcode');


    $quarter = AuditManagementModel::commonquarterfetch($deptcode,$instmappingcode);
    $auditperiod_yrselected = AuditManagementModel::Auditperiodfetch($instmappingcode);
    $yearselected = $auditperiod_yrselected->pluck('yearselected');
    $yearselectedArray = $yearselected->toArray();

    $auditplanid = $auditperiod_yrselected->pluck('auditplanid');
    $auditplanid = $auditplanid->toArray();


    $auditperiod = AuditManagementModel::Auditperiodcompactfetch($deptcode);




    return response()->json([
        'success' => true,
        'auditperiod' => $auditperiod, // Include audit periods in the response
        'quarter' => $quarter, // Include audit periods in the response
        'yearselected'=>$yearselectedArray,
        'auditperiod_yrselected' => $auditperiod_yrselected,
        'auditplanid'=>$auditplanid[0]

    ]);
}
    
public function changerequest_insertupdate(Request  $request)
{
    // print_r($_REQUEST);

   try {


    $rules = [
        'deptcode' => 'required|string|regex:/^\d+$/',
        "regioncode" => 'required|string|regex:/^\d+$/',
        'distcode' => 'required|string|regex:/^\d+$/',
        'instmappingcode' => 'required|string|regex:/^\d+$/',
        'yearselected' => 'required|string|regex:/^\d{4}\s*-\s*\d{4}$/',
        'auditplanid' => 'required|string',

    ];



    $auditplan = session('user');
    if (!$auditplan || !isset($auditplan->userid)) {
        return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
    }
    $userchargeid = $auditplan->userid;
    $auditplanid =$request->input('auditplanid');
    // echo 'auditplanid';print_r($auditplanid);exit;


   $data = [
        'yearselected' => $request->yearselected ?? null,
        'updatefield' => $request->updatefield, // this is key
        'updatedby' => $userchargeid

    ];

    //if ($request->input('action') === 'update') {
        $data['createdon'] = View::shared('get_nowtime');
        $data['createdby'] =  $userchargeid;
        $data['updatedon'] = View::shared('get_nowtime');
        $data['updatedby'] =  $userchargeid;
   // }

   

    $result = AuditManagementModel::changerequests_insertupdate($data, $auditplanid, 'audit.yearcode_mapping');
//return   $result;
      return response()->json(['success' => true, 'message' => $result]);

   } 
   catch (ValidationException $e) {
     return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
   }
    catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
    }
}


public function getregionbasedondeptchangerequest(Request $request)
{
    $request->validate([
        'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
    ], [
        'required' => 'The :attribute field is required.',
        'regex'    => 'The :attribute field must be a valid number.',
    ]);

    $deptcode = $request->input('deptcode');


    $regions = AuditManagementModel::getRegionsByDept($deptcode);
    $auditperiod = AuditManagementModel::Auditperiodcompactfetch($deptcode);



    return response()->json([
        'success' => true,
        'data' => $regions,
        'auditperiod' => $auditperiod, // Include audit periods in the response
    ]);
}




public function getdistrictbasedonregionchangerequest(Request $request)
{
    // Validate the input
    $request->validate(
        [
            'region'   => ['required', 'string', 'regex:/^\d+$/'],
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        ],
        [
            'region.required'   => 'The region field is required.',
            'region.regex'      => 'The region field must be a valid number.',
            'deptcode.required' => 'The deptcode field is required.',
            'deptcode.regex'    => 'The deptcode field must be a valid number.',
        ]
    );

    // Get the department code
    $regioncode = $request->input('region');
    $deptcode = $request->input('deptcode');


    // Fetch regions from the model
    $district = AuditManagementModel::getdistrictByregion($regioncode, $deptcode);

    // Return JSON response
    if ($district->isNotEmpty()) {
        return response()->json(['success' => true, 'data' => $district]);
    } else {
        return response()->json(['success' => false, 'message' => 'No regions found'], 404);
    }
}


public function getinstitutionbasedondistchangerequest(Request $request)
{
    // Validate the input
    $request->validate([
        'region'   => ['required', 'string', 'regex:/^\d+$/'],
        'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        'district' => ['required', 'string', 'regex:/^\d+$/'],
   	'updatefield' => ['required', 'string', 'regex:/^\d+$/'],
    ], [
        'region.required' => 'The :attribute field is required.',
        'region.regex'    => 'The :attribute field must be a valid number.',
        'deptcode.required' => 'The deptcode field is required.',
        'deptcode.regex'    => 'The deptcode field must be a valid number.',
        'district.required' => 'The district field is required.',
        'district.regex'    => 'The district field must be a valid number.',
	 'updatefield.required' => 'The updatefield field is required.',
     'updatefield.regex'    => 'The updatefield field must be a valid number.',
    ]);

    // Get the department code
    $regioncode = $request->input('region');
    $deptcode = $request->input('deptcode');
    $district = $request->input('district');
    $updatefield = $request->input('updatefield'); 

    // Fetch regions from the model
	    $institution = AuditManagementModel::getinstitutionBydistrictchange($district, $regioncode, $deptcode, $updatefield);

    // Return JSON response
    if ($institution->isNotEmpty()) {
        return response()->json(['success' => true, 'data' => $institution]);
    } else {
        return response()->json(['success' => false, 'message' => 'No Institutions found'], 200);
    }
}

public function changerequest_fetchData(Request $request)
{
    $auditplanid = $request->has('auditplanid') ? Crypt::decryptString($request->auditplanid) : null;
    $auditplan = AuditManagementModel::changerequestfetchData($auditplanid, 'audit.auditplan');



    if (is_iterable($auditplan)) {
        foreach ($auditplan as $all) {
            $all->encrypted_auditplanid = Crypt::encryptString($all->auditplanid);
            unset($all->auditplanid);
        }
    }

    return response()->json([
        'success' => true,
        'message' => empty($auditplan) ? 'No Details found' : '',
        'data' => $auditplan ?? []
    ], 200);
}

 /*******************************************Manual Plan - start ********************************************************************/

 public function fetchUpdatepManualplan(Request $request)
    {
        // echo $request->auditteamid;
        // echo 'hi';
        // exit;
        // Decrypt the audit team ID from the request, if exists
        $auditteamid = $request->has('auditteamid') ? Crypt::decryptString($request->auditteamid) : null;

        // echo $auditteamid;
        // exit;
        $updatedteam = AuditManagementModel::fetchUpdatepManualplan($auditteamid);

        // print_r($updatedteam);

        // exit;

        if ($updatedteam) {
            foreach ($updatedteam as $all) {
                if (isset($all->auditplanteamid)) {
                    $all->encrypted_auditplanteamid = Crypt::encryptString($all->auditplanteamid);
                    $all->encrypted_auditplanid = Crypt::encryptString($all->auditplanid);

                    //  unset($all->auditplanteamid, $all->auditplanid);
                }
            }
        }
        // Make sure $updatedteam is a collection or array before looping


        // print_r($updatedteam);
        // exit;

        // Return the response
        return response()->json([
            'success' => $updatedteam->isNotEmpty(),
            'message' => $updatedteam->isEmpty() ? 'User not found' : '',
            'data' => $updatedteam->isEmpty() ? null : $updatedteam
        ], $updatedteam->isEmpty() ? 404 : 200);
    }

    public function getAuditors_manualplan(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode'              => ['required', 'string', 'regex:/^\d+$/'],
            'regioncode'            => ['required', 'string', 'regex:/^\d+$/'],
            'distcode'              => ['required', 'string', 'regex:/^\d+$/'],
            'auditteamid'           => ['nullable', 'string'],
            'user_det'              => ['nullable', 'string']
        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid field.',

        ]);
        $distcode = $request->distcode;
        $deptcode = $request->deptcode;
        $regioncode = $request->regioncode;
        $auditteamid = $request->auditteamid;

        $isreservedauditors = $request->user_det;
        // print_r( $distcode);
        // $teamcode = $request->teamcode;

        try {
            if ($request->auditteamid)
                $auditteamid = Crypt::decryptString($request->auditteamid);
            else    $auditteamid   =   '';

            $auditors    =   AuditManagementModel::getAuditors_manualplan($deptcode, $regioncode, $distcode, $auditteamid, $isreservedauditors);

            return response()->json(['success' => true, 'auditor' => $auditors]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    public function fetchExcessinstitution(Request $request)
    {

        $validatedData = $request->validate([
            'deptcode'              => ['required', 'string', 'regex:/^\d+$/'],
            'regioncode'            => ['required', 'string', 'regex:/^\d+$/'],
            'distcode'              => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid field.',

        ]);

        try {
            $deptcode = $request->input('deptcode');
            $regioncode = $request->input('regioncode');
            $distcode = $request->input('distcode');
            $instid = $request->input('selectedinstid') ?? null;

            $institution = AuditManagementModel::fetchExcessinstitution($deptcode, $regioncode, $distcode, $instid);
            // return  $institution;
            return response()->json([
                'success' => true,
                'auditor' => $institution,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }



        //print_r($institution);

    }
    public function manualplan_insertupdate(Request $request)
    {

        // print_r($request->all());
        // exit;

        try {
            $session = session('user');
            $userId = $session->userid;



            //  return $auditteamid;
            $validatedData = $request->validate([
                'deptcode'              => ['required', 'string', 'regex:/^\d+$/'],
                'regioncode'            => ['required', 'string', 'regex:/^\d+$/'],
                'distcode'              => ['required', 'string', 'regex:/^\d+$/'],
                'newteamhead'           => ['required', 'string',],
                'newteammembers'        => ['required', 'string',],
                'teamsize'              => ['required', 'string',],
                'formparam'              => ['required', 'string',],
            ], [
                'required' => 'The :attribute field is required.',
                'regex'    => 'The :attribute field must be a valid field.',

            ]);
            $headlevel = json_decode($request->headlevel, true);
            $teamMemberDesigCodes = json_decode($request->teamMemberdesigcode, true);
            $teamHeaddesigcode = trim($request->teamHeaddesigcode, '"');
            $newTeamMembers = json_decode($request->newteammembers, true);

            $firstHeadDesigCode = $headlevel[0]['desigcode'];

            if (in_array($firstHeadDesigCode, $teamMemberDesigCodes)) {
                throw new Exception("Cannot be a memeber");
            }
            if (count($newTeamMembers) !== ($request->teamsize - 1)) {
                throw new Exception("Team size mismatched");
            }


            $validHeadCodes = array_column($headlevel, 'desigcode');
            if (!in_array($teamHeaddesigcode, $validHeadCodes)) {
                throw new Exception("Team head designation is not valid");
            }




            $action = $request->input('action');
            $finaliseflag = $request->input('finaliseflag');
            $auditteamid = $action === 'update' ? Crypt::decryptString($request->input('auditteamid')) : null;
            $auditplanid = $action === 'update' ? Crypt::decryptString($request->input('auditplanid')) : 0;


            $newteammembers = $request->input('newteammembers');
            $newteammembers = $newteammembers ? json_decode($newteammembers, true) : [];
            // $newTeamMembers = $request->input('newteammembers'); // This is an array: ["1897"]

            // Convert to PostgreSQL array format string:
            $newTeamMembersArray = '{' . implode(',', $newteammembers) . '}';
            // $newTeamMembersArray = 'ARRAY[' . implode(',', $newteammembers) . ']';

            //   return  $newTeamMembersArray;
            $data =
                [
                    'instid'            => $request->input('instid'),
                    'deptcode'          => $request->input('deptcode'),
                    'regioncode'        => $request->input('regioncode'),
                    'distcode'          => $request->input('distcode'),
                    'newteamhead'       => $request->input('newteamhead'),
                    //'newteammembers'    => $request->input('newteamhead'),
                    'newteammembers'    => $newTeamMembersArray,
                    'remarks'           => $request->input('remarks'),
                    'updatedby'         => $userId,
                    'updatedon'         => View::shared('get_nowtime'),
                    'formparam'         =>  $request->input('formparam'),
                ];


            if ($action === 'insert') {
                $data['createdby'] = $userId;
                $data['createdon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userId;
                $data['updatedon'] = View::shared('get_nowtime');
            } else {
                $data['updatedby'] = $userId;
                $data['updatedon'] = View::shared('get_nowtime');
            }

            $data['statusflag'] = $finaliseflag === 'Y' ? 'F' : 'S';



            // exit;

            $result = AuditManagementModel::updatemanualplan($data, $auditteamid, $auditplanid);
            // print_r($result);
            // exit;
            // if ($result['status'] == false) {
            //     return response()->json(['message' =>  $result['message'], 'error' =>  $result['message']], 401);
            // }

            $resultdet = $result[0]->manualplan;

            //$status = $resultdet['status'];
            $resultdata = json_decode($resultdet, true);  // returns associative array
            $status = $resultdata['status'];

            if ($status === "false") {
                // return response()->json(['message' =>  $resultdata['error'], 'error' =>  $resultdata['error']], 401);
                return response()->json([
                    'success' => true,
                    'data' => $resultdet,
                    'message' => $status

                ]);
            } else if ($status === "inserted") {
                return response()->json([
                    'success' => true,
                    'data' => $resultdet,
                    'message' => $status

                ]);
            } else if ($status === "success") {
                return response()->json([
                    'success' => true,
                    'data' => $resultdet,
                    'message' => $status

                ]);
            } else if ($status === "updated") {
                return response()->json([
                    'success' => true,
                    'data' => $resultdet,
                    'message' => $status

                ]);
            } else if ($status === "finalised") {
                return response()->json([
                    'success' => true,
                    'data' => $resultdet,
                    'message' => 'finalised successfully'

                ]);
            } else {

                return response()->json(['message' =>  'Error Occurs', 'error' => 'Error Occurs'], 401);
            }
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    function fetchmanualupdatedata(Request $request)
    {
        $auditteamid = $request->has('auditteamid') ? Crypt::decryptString($request->auditteamid) : null;
        try {
            if (!$auditteamid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid audit team ID provided.'
                ], 400);
            }

            $auditManagementModel = new AuditManagementModel();
            $updatedteam = $auditManagementModel->fetchUpdatepManualplan($auditteamid);

            if ($updatedteam) {
                foreach ($updatedteam as $all) {
                    if (isset($all->auditplanteamid)) {
                        $all->encrypted_auditplanteamid = Crypt::encryptString($all->auditplanteamid);
                        $all->encrypted_auditplanid = Crypt::encryptString($all->auditplanid);
                        //    unset($all->auditplanteamid, $all->auditplanid);
                    }
                }
            }
            return  $updatedteam;
            return response()->json([
                'success' => $updatedteam->isNotEmpty(),
                'message' => $updatedteam->isEmpty() ? 'User not found' : '',
                'data' => $updatedteam->isEmpty() ? null : $updatedteam
            ], $updatedteam->isEmpty() ? 404 : 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }


     //-------------------------------------------Quarter Transaction Start----------------------------

    public function updateschedule_details(Request $request)
    {
        $auditplanid = Crypt::decryptString($request->auditplanid);
        $auditscheduleid = Crypt::decryptString($request->auditscheduleid);
        return $auditscheduleid;
        if (empty($auditplanid)) {
            throw new Exception("Audit Plan ID not available");
        }

        if (empty($auditscheduleid)) {
            throw new Exception("Audit Schedule ID not available");
        }

        $getteammembers = AuditManagementModel::getteamfromplan($auditplanid);

        return $getteammembers;
    }

    public static function fetch_notscheduledinst()
    {
        $data = AuditManagementModel::getnotscheduleinstData();
        $spillous = AuditManagementModel::getSpilloverInstitutions();
        $countinst = AuditManagementModel::getnotscheduleinstCount();

        $pendinginstcheck = AuditManagementModel::pendinginstcheck();
        $Spilloverdatecheck = AuditManagementModel::spilloverdateCheck();


        // print_r($pendinginstcheck);
        // exit;

        $penidnResult = AuditManagementModel::penidninst_fetchData(null, 'audit.temp_inst_q1_pending');
        $penidnData = $penidnResult['data'];
        $penidnCount = $penidnResult['count'];


        //  dd($spillous);

        return view('audit.instchange', compact('data', 'spillous', 'countinst', 'penidnCount', 'pendinginstcheck', 'Spilloverdatecheck'));
    }    

    public function penidninstUpdation(Request $request)
    {
        try {
            //   throw new Exception("Deptcode is not available");
            $session = session('user');
            $updatemap = session('charge');

            if (!$session || !isset($session->userid)) {
                return response()->json(['success' => false, 'message' => 'Session expired. Please login again.'], 401);
            }

            $deptcode = $updatemap->deptcode ?? null;
            $distcode = $updatemap->distcode ?? null;
            $userid   = $session->userid;

		$action = $request->input('action', []);
 		$rows = $request->input('rows', []);
		$spillous = $request->input('spillous', []);
            $tempid   = ($action === 'update') ? Crypt::decryptString($request->input('tempid')) : null;

            $now = View::shared('get_nowtime');

             if ((!is_array($rows) || count($rows) === 0) &&$action === 'finalise' &&(empty($spillous) || (is_array($spillous) && count($spillous) === 0))) {
                AuditManagementModel::finaliseInstitutionStatus($deptcode, $distcode, $userid, $now);
                return response()->json(['success' => true, 'message' => 'done']);
            }

            $spillousIndexed = AuditManagementModel::indexSpillousByInstid($spillous);
            $newQuarter = AuditManagementModel::getQuarterValue($deptcode, 'nextquarter');
            $currentQuarter = AuditManagementModel::getQuarterValue($deptcode, 'currentquarter');


            DB::beginTransaction();

            foreach ($rows as $row) {
                $instid = $row['instid'] ?? null;
                if (!$instid) continue;

                $instidKey = (string)$instid;
                $new_quarter = $row['quarter'] ?? null;

                $data = [
                    'instid'         => $instid,
                    'quartercode'    => $row['audit_quarter'] ?? null,
                    'pendingflag'    => ($action === 'finalise') ? 'F' : 'D',
                    'newquartercode' => $new_quarter,
                    'yearofaudit'    => '2025',
                    'createdon'      => $now,
                    'createdby'      => $userid,
                    'updatedon'      => $now,
                    'updatedby'      => $userid,
                    'spilloverflag'  => 'N'
                ];

                $result = AuditManagementModel::UpdateNotscheduledData($data, $tempid, 'audit.temp_inst_q1_pending');
                if (!$result['status']) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => $result['message']], 422);
                }

                if ($action === 'finalise' && in_array($new_quarter, ['Q1', 'Q2', 'Q3', 'Q4'])) {
                    AuditManagementModel::updateInstitutionQuarter($instid, $new_quarter, $currentQuarter, $userid, $now);
                    AuditManagementModel::deactivateAuditDataByInstid($instid, $userid, $now);
                }
            }

            if ($action === 'finalise') {
                //  return $spillousIndexed;
                AuditManagementModel::updateSpilloverInstitutions($spillousIndexed, $newQuarter, $userid, $now);
                AuditManagementModel::finaliseInstitutionStatus($deptcode, $distcode, $userid, $now);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'done']);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 401);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function penidninst_fetchData(Request $request)
    {
        try {

            $tempid = null;
            if ($request->has('tempid')) {
                $tempid = Crypt::decryptString($request->tempid);
            }

            // Change here to get both count and data
            $result = AuditManagementModel::penidninst_fetchData($tempid, 'audit.temp_inst_q1_pending');

            $data = $result['data'];
            $count = $result['count'];

            foreach ($data as $all) {
                $all->encrypted_tempid = Crypt::encryptString($all->tempid);
                unset($all->tempid);
            }

            return response()->json([
                'success' => $count > 0,
                'message' => $count === 0 ? 'No data found' : '',
                'count' => $count,
                'data' => $count === 0 ? null : $data
            ], $count === 0 ? 404 : 200);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid tempid',
                'error' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }
 /*******************************************Manual Plan - End ********************************************************************/


    //---------------------------------SMS Function----------------------------------//
    public function SendOTP_allocatePlan(Request $request)
    {
        $sessiondet = session('user');
        $username   =  $sessiondet->username;
        $userid     =  $sessiondet->userid;
        $email      =  $sessiondet->email;

       // $email = 'nijisa18@gmail.com';


        //$email =$sessiondet->email;

        $otp = rand(100000, 999999); // 6-digit OTP

        $data = [
            'userid' => $userid,
            'email'  => $email,
            'otp'    => $otp,
        ];

        $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
        $sentsms = $auditModel->sendotp_allocateplan($data, $username);
        // return $sentsms;
        if ($sentsms === 'Message has been sent') {
            // Session::put('customer_otp', $otp);

            return response()->json([
                'status' => 'success',
                'message' => 'OTP has been sent successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send OTP. Please try again later.'
            ], 500); // 500 Internal Server Error
        }
    }

    public function VerifyOTP_allocatePlan(Request $request)
    {
        $userOtp = $request->otp;

        $sessiondet = session('user');
        $userid     =  $sessiondet->userid;
        $email      = $sessiondet->email;

        $data = [
            'userid' => $userid,
            'email'  => $email,
            'otp'    => $userOtp,
        ];


        $storedOtp = SmsmailModel::verifyOTP($data);


        if ($storedOtp) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Incorrect OTP']);
        }
    }

    public function checkexitmeetstatus(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode'              => ['required', 'string', 'regex:/^\d+$/'],
            'distcode'              => ['required', 'string', 'regex:/^\d+$/'],

        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid field.',

        ]);
        try {
            $deptcode = $request->deptcode;
            $distcode = $request->distcode;

            $status = AuditManagementModel::checkexitmeetstatus($deptcode, $distcode);

            if ($status) {
                return response()->json(['message' => 'Some of the  institution has not entered exit meet date', 'error' => 500], 500);
            } else {

                return response()->json([
                    'status' => 'success',
                    'message' => 'Exit Meeting was happened for all instittion'
                ]);
            }



            // return $getauditquarter;
            //$distcode = 'd';
            //   $quartercode = $getauditquarter;

            //  $assignteams = AuditManagementModel::assignteams($deptcode, $distcode, $quartercode);

            //    return response()->json(['success' => true, 'data' => $assignteams[0]]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }



    /************************************************Check List Audit Plan ******************************************************************************* */

    public function checkisteamassigned(Request $request)
    {

        $validatedData = $request->validate([
            'deptcode'              => ['required', 'string', 'regex:/^\d+$/'],
            'distcode'              => ['required', 'string', 'regex:/^\d+$/'],

        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid field.',

        ]);
        try {
            $deptcode = $request->deptcode;
            $distcode = $request->distcode;

            $quartercode = AuditManagementModel::getauditquarter($deptcode);





            $isPlanfinalized = AuditManagementModel::checkisPlanfinalized($deptcode, $distcode);
            $planstatus = $isPlanfinalized[0]->autoplanstatus;
            $pendinginststatus = $isPlanfinalized[0]->pendinginststatus;

            $isteamassigned = AuditManagementModel::checkisteamassigned($deptcode, $distcode);


            if ($planstatus == 'F') {
                $finalisedplanData = AuditManagementModel::getAuditorsfromplan($deptcode, $distcode, $quartercode);
                // return $finalisedplanData;


                return response()->json(['success' => true, 'planned_auditors' => $finalisedplanData, 'teamassignedstatus' => $isteamassigned, 'planstatus' => $planstatus, 'executingquartercode' => $quartercode]);
            } else if ($planstatus == 'N' || $planstatus == 'Y' || $planstatus == '') {

                if ($isteamassigned) {
                    $getalldetails = AuditManagementModel::getalldetails($deptcode, $distcode, $quartercode);
                    $totalinstcount = $getalldetails[0];

                    // If you need it as array:
                    $data = (array) $totalinstcount;

                    // Step 1: Extract the JSON string
                    $jsonString = $data['checklistdel'];



                    // Step 2: Decode the JSON string into an array
                    $checklist = json_decode($jsonString, true);

                    // Step 3: Access the decoded data (it's an array with 1 object)
                    $details = $checklist[0];

                    //return $details;


                    // Now you can access values
                    $institutionCount = $details['institutioncount'];
                    $auditorCount = $details['auditorcount'];
                    $designationDetails = $details['designationdel'];
                    $teamCombinations = $details['teamcombination'];
                    $teamallocation     = $details['teamallocation'];
                    $idelauditorslist     = $details['idelauditorslist'];
                    $idelinstitutionlist     = $details['idelinstitutionlist'];
                    $totalworkingdays = $details['totalworkingdays'];
                    $sumofinstmandays = $details['sumofinstmandays'];
                    $neededmandays = $details['neededmandays'];
                    $allocatedmandays = $details['allocatedmandays'];
                    $quarterfromdate = $details['quarterfromdate'];
                    $quartertodate = $details['quartertodate'];



                    $teamdet = AuditManagementModel::getchecklistteamdet($deptcode, $distcode, $quartercode);
                    $AuditorInst = AuditManagementModel::getAuditorsInstdet($deptcode, $distcode, $quartercode);
                    //  $mandaysDetais =AuditManagementModel::getmandaysDetais($deptcode, $distcode, $quartercode);
                    // //return $teamdet;


                    return response()->json(
                        [
                            'success' => true,
                            'planstatus' => $planstatus,
                            'totalworkingdays' => $totalworkingdays,
                            'sumofinstmandays' => $sumofinstmandays,
                            'neededmandays' => $neededmandays,
                            'allocatedmandays' => $allocatedmandays,
                            'quarterfromdate' => $quarterfromdate,
                            'quartertodate' => $quartertodate,
                            //'mandaysdet'=>$mandaysDetais,
                            'teamassignedstatus' => $isteamassigned,
                            'teamdet' => $teamCombinations,
                            'totalteamdetails' => $teamallocation,
                            'idelusers' => $idelauditorslist,
                            'idleinst' => $idelinstitutionlist,
                            'totalinstcount' => $institutionCount,
                            'totalauditorscount' => $auditorCount,
                            'designationDetails' => $designationDetails,
                            'distname' => $teamdet['distname'],
                            'deptname' => $teamdet['deptname'],
                            'users' => $AuditorInst['users'],
                            'inst_det' => $AuditorInst['inst_det']
                        ]
                    );
                } else {

                    $teamdet = null;
                    return response()->json(['success' => true, 'teamassignedstatus' => $isteamassigned, 'pendinginststatus' => $pendinginststatus]);
                }
            }
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    public function assignteams(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode'              => ['required', 'string', 'regex:/^\d+$/'],
            'distcode'              => ['required', 'string', 'regex:/^\d+$/'],

        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid field.',

        ]);
        try {
            $deptcode = $request->deptcode;
            $distcode = $request->distcode;

            $getauditquarter = AuditManagementModel::getauditquarter($deptcode);
            // return $getauditquarter;
            //$distcode = 'd';
            $quartercode = $getauditquarter;

            $assignteams = AuditManagementModel::assignteams($deptcode, $distcode, $quartercode);

            return response()->json(['success' => true, 'data' => $assignteams[0]]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    public static function finaliseplan(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode'              => ['required', 'string', 'regex:/^\d+$/'],
            'distcode'              => ['required', 'string', 'regex:/^\d+$/'],

        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid field.',

        ]);

        try {
            $deptcode = $request->deptcode;
            $distcode = $request->distcode;

            $quartercode = AuditManagementModel::getauditquarter($deptcode);

            // return 'finalise'

            $finailiseddata = AuditManagementModel::finaliseplan($deptcode, $distcode);

            $finalisedet = $finailiseddata[0]->distributeauditteamplan;
            $finalisedet = json_decode($finalisedet, true); // now it's an array
            $finalisestatus = $finalisedet['status'];       // ✅ this will now work

            if ($finalisestatus == 'error') {
                return response()->json(['message' =>  $finalisedet['message'], 'error' => 500], 500);
            } else {
                $finalisedplanData = AuditManagementModel::getAuditorsfromplan($deptcode, $distcode, $quartercode);
                return response()->json(['success' => true, 'planned_auditors' => $finalisedplanData, 'data' => $finalisestatus, 'executingquartercode' => $quartercode]);
            }
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    public function initschedule_dropdown()
    {
        try {
            $sessioncharge     = session('charge');
            $sessionuser    = session('user');

            if (empty($sessionuser)) {
                throw new \Exception("No session details");
            } else {
                $sessionuserid = $sessionuser->userid;
                $formsessionuserid = Crypt::encryptString($sessionuserid);
            }
            $deptcode =  $sessioncharge->deptcode;
            $quarter_det =  AuditManagementModel::getquarterdetails($deptcode);

            //      $quarter_det = 'dfsd';

            return view('audit.initauditschedule', compact('formsessionuserid', 'quarter_det'));
        } catch (\Exception $e) {
            return view('audit.initauditschedule', [
                'quarter_det' => $quarter_det ?? null,

                'errorMessage' => $e->getMessage(),
                'pageName' => 'initauditschedule',
            ]);
        }
    }

 public function viewintimation_dropdown(Request $request)
    {
        try {
            $sessioncharge     = session('charge');
            $sessionuser    = session('user');

            if (empty($sessionuser)) {
                throw new \Exception("No session details");
            } else {
                $sessionuserid = $sessionuser->userid;
                $formsessionuserid = Crypt::encryptString($sessionuserid);
            }
            $deptcode =  $sessioncharge->deptcode;
            $quarter_det =  AuditManagementModel::getquarterdetails($deptcode);

            //      $quarter_det = 'dfsd';

            return view('audit.viewintimationdetails', compact('formsessionuserid', 'quarter_det'));
        } catch (\Exception $e) {
            return view('audit.initauditschedule', [
                'quarter_det' => $quarter_det ?? null,

                'errorMessage' => $e->getMessage(),
                'pageName' => 'initauditschedule',
            ]);
        }
    }

public function spilloverschedule_values(Request $request)
    {
        try {

            $instid = Crypt::decryptString($request->id);
            $planid = Crypt::decryptString($request->planid);
            if (empty($instid)) {
                throw new \Exception("Institution Details not found");
            }
            if (empty($planid)) {
                throw new \Exception("Plan details  not found");
            }

            $getplandetails = AuditManagementModel::getspilloverplandetails($instid, $planid);
            // print_r($getplandetails);
            // exit;
            foreach ($getplandetails as $all) {
                $all->encrypted_instid = Crypt::encryptString($all->instid);
                unset($all->instid);
            }
            return view('audit.spilloverschedule', compact('getplandetails'));

            // print_r($getplandetails);
            // exit;
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    public function chargetakingover(Request $request)
    {
        try {

            $instid = Crypt::decryptString($request->instid);

            if (empty($instid)) {
                throw new \Exception("Institution Details not found");
            }

            $session = session('user');
            $userid = $session->userid;

            $result = AuditManagementModel::chargetakingover($instid, $userid);

            $jsonString = $result[0]->response;
            $data = json_decode($jsonString, true);


            $status = $data['status'] ?? null;
            $message = $data['message'] ?? null;
            // $status = 'success';
            // $message = 'success';

            if ($status == 'error') {
                return response()->json(['message' =>  $message, 'error' => 500], 500);
            } else if ($status == 'success') {
                return response()->json(['success' => true, 'message' => $message]);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Some error occured',
                ], 200);
            }
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

}
