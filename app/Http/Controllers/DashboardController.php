<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

use App\Models\AuditTeamModel;
use App\Models\AuditModel;
use App\Models\DesignationModel;
use App\Models\DistrictModel;
use App\Models\UserChargeDetailsModel;
use App\Models\AuditMemberModel;
use App\Models\DashboardModel;
use App\Models\Charge;
use App\Models\AssignCharge;
use App\Models\UserChargeDetailModel;
use Illuminate\Http\Request;

use DB;

class DashboardController extends Controller
{
    public function dashboard_detail()
    {
        $charge = session('charge');
        $chargeid = $charge->chargeid;
        if ($chargeid == '1') {
            // $auditscheduleid = $request->auditscheduleid;
            $teamdetail = AuditTeamModel::fetch_teamdetail();
            $plandetail = AuditModel::fetch_plandetail();
            return response()->json([

                'teamdetail' => $teamdetail,
                'plandetail' => $plandetail
            ]);
        }
    }

          
    public function Get_dept(Request $request)
    {
        // Session data
        $charge = session('charge');
        $usertypecode = $charge->usertypecode ?? null;
        $user = session('user');
        $userId = $user->userid ?? null;
    
        $profileUpdate = null;
    
        if ($usertypecode && $userId) {
            if ($usertypecode === 'A') {
                $userRecord = DB::table('audit.deptuserdetails')->where('deptuserid', $userId)->first();
            } elseif ($usertypecode === 'I') {
                $userRecord = DB::table('audit.audtieeuserdetails')->where('auditeeuserid', $userId)->first();
            }
    
            if (isset($userRecord) && $userRecord->profile_update === 'Y') {
                $profileUpdate = 'Y';
            }
        }
    
        // Get department-related data
        $sessionChargeDetails = session('charge');
        $sessionRoleType = $sessionChargeDetails->roletypecode ?? null;
        $deptCode = $sessionChargeDetails->deptcode ?? null;
        $regionCode = $sessionChargeDetails->regioncode ?? null;
        $distCode = $sessionChargeDetails->distcode ?? null;
        $userTypeCode = $sessionChargeDetails->usertypecode ?? null; 
        $roleTypeCode = $sessionChargeDetails->roletypecode ?? null;
    
        // Default auditScheduleId to 0 if not provided
        $auditScheduleId = $request->input('auditscheduleid', 0);
    
        $userChargeId = $user->userid ?? null;
    
        // Fetch department, district, and other details
        $dept = DashboardModel::fetchDeptDetails($deptCode);
        $year = DashboardModel::fetchYearDetails($deptCode);
        $dist = DashboardModel::fetchDistDetails($sessionRoleType, $deptCode, $regionCode, $distCode);
        $institutionDetails = DashboardModel::fetchInstitutionDetails('');
        $headinstitutionDetails = DashboardModel::fetchInstitutionDetails('Y');
        $countDetails = DashboardModel::fetchCountDetails(
            $deptCode,
            $regionCode,
            $distCode,
            $userChargeId,
            $userTypeCode,
            $roleTypeCode,
            $auditScheduleId
        );
    
        $deptCode = $sessionChargeDetails->deptcode ?? null;
        $regionCode = $sessionChargeDetails->regioncode ?? null;
        $distCode = $sessionChargeDetails->distcode ?? null;
        
          $auditQuarters = DB::table('audit.auditplan as a')
                        ->leftjoin('audit.mst_dept as d', 'd.nextquarter', '=', 'a.auditquartercode')
                        ->select('a.auditquartercode')
                        ->distinct()
                        // ->orderBy('a.auditquartercode')
                        ->pluck('a.auditquartercode');


        $countdetailsofsch = DashboardModel::GetcountDetails($deptCode,$regionCode,$distCode,$auditQuarters,$quarter =null);

        $countdetailsofsch=$countdetailsofsch[0];
        // Return the view with all necessary data
        return view('dashboard.dashboard', compact(
            'profileUpdate',
            'dept',
            'dist',
            'institutionDetails',
            'countDetails',
            'year',
            'headinstitutionDetails',
            'countdetailsofsch',
             'auditQuarters'
        ));
    }

     public function auditee_dashboardcount(Request $request)
    {
        $charge = session('charge');
        $usertypecode = $charge->usertypecode ?? null;
        $user = session('user');
        $userId = $user->userid ?? null;
    
        // Profile update flag
        $profileUpdate = null;
    
        if ($usertypecode && $userId) {
            if ($usertypecode === 'A') {
                $userRecord = DB::table('audit.deptuserdetails')->where('deptuserid', $userId)->first();
            } elseif ($usertypecode === 'I') {
                $userRecord = DB::table('audit.audtieeuserdetails')->where('auditeeuserid', $userId)->first();
            }
    
            if (isset($userRecord) && $userRecord->profile_update === 'Y') {
                $profileUpdate = 'Y';
            }
        }
    
        // Get session data
        $sessionChargeDetails = session('charge');
        $sessionRoleType = $sessionChargeDetails->roletypecode ?? null;
        $deptCode = $sessionChargeDetails->deptcode ?? null;
        $regionCode = $sessionChargeDetails->regioncode ?? null;
        $distCode = $sessionChargeDetails->distcode ?? null;
        $userTypeCode = $sessionChargeDetails->usertypecode ?? null; // Corrected variable name
        $roleTypeCode = $sessionChargeDetails->roletypecode ?? null; // Corrected variable name

        // Default auditScheduleId to 0 if not provided
        $auditScheduleId = $request->input('auditscheduleid', 0); // Use the value from the request or default to 0
        //  echo $auditScheduleId;
        $user = session('user');
        $userChargeId = $user->userid ?? null;


        //echo($headinstitutionDetails);

        // Fetch count details using the PostgreSQL function
        $countDetails = DashboardModel::fetchCountDetails(
            $deptCode,
            $regionCode,
            $distCode,
            $userChargeId,
            $userTypeCode,
            $roleTypeCode,
            $auditScheduleId
        );
        
     
        $intimationcount = DashboardModel::getinitimationcount($userChargeId, $deptCode,);


        //print_r ($countDetails[0]);


        return view('dashboard.auditeedashboard', compact('countDetails','profileUpdate','intimationcount'));
        // return view('dashboard.dashboard', compact('dept', 'dist', 'institutionDetails', 'countDetails','year','headinstitutionDetails'));

    }

    public function CallingData(Request $request)
    {
        // Retrieve session details
        $sessionChargeDetails = session('charge');
        $sessionRoleType = $sessionChargeDetails->roletypecode ?? null;
        $deptCode = $sessionChargeDetails->deptcode ?? null;
        $regionCode = $sessionChargeDetails->regioncode ?? null;
        $distCode = $sessionChargeDetails->distcode ?? null;
        $userTypeCode = $sessionChargeDetails->usertypecode ?? null;
        $roleTypeCode = $sessionChargeDetails->roletypecode ?? null;

        // Retrieve the auditscheduleid and activeTab from the request
        $auditscheduleid = $request->input('auditscheduleid');
        $activeTab = $request->input('activeTab'); // 0 for the third tab, 1 for the first tab, etc.

        if ($activeTab == 3) {
            $userChargeId = 0;
        } else {

            $user = session('user');
            $userChargeId = $user->userid ?? null;
        }


        //     echo "User Charge ID: " . $userChargeId;
        //    exit;
        $countDetails = DashboardModel::fetchCountDetails(
            $deptCode,
            $regionCode,
            $distCode,
            $userChargeId,
            $userTypeCode,
            $roleTypeCode,
            $auditscheduleid
        );

        // Return the count details as a JSON response
        return response()->json($countDetails);
    }



    public function sentDetails()
    {
        $user = session('user');
        $sessionChargeDetails = session('charge');
        $userId = $user->userid ?? null;
        $userTypeCode = $sessionChargeDetails->usertypecode ?? null;

        // echo $userTypeCode;
        $sentDetails = DashboardModel::fetchSentDetails($userTypeCode,  $userId);
        // print_r($sentDetails);

        return view('dashboard.sentdetails', compact('sentDetails')); // Replace with your view name
    }


    public function descriptionData(Request $request)
    {
        $sessionChargeDetails = session('charge');
        $sessionRoleType = $sessionChargeDetails->roletypecode ?? null;
        $deptCode = $sessionChargeDetails->deptcode ?? null;
        $regionCode = $sessionChargeDetails->regioncode ?? null;
        $distCode = $sessionChargeDetails->distcode ?? null;
        $userTypeCode = $sessionChargeDetails->usertypecode ?? null;
        $roleTypeCode = $sessionChargeDetails->roletypecode ?? null;

        //   echo $sessionChargeDetails->scheduleid ?? null;


        $auditScheduleId = $request->input('auditscheduleid', 0);
        $activeTab = $request->input('activeTab'); // 0 for the third tab, 1 for the first tab, etc.

        if ($activeTab == 3) {
            $userId = 0;
        } else {

            $user = session('user');
            $userId = $user->userid ?? null;
        }
        $description = $request->input('description', 'allslip'); // Default to 'allslip'

        // Fetch data from model
        $dashboardDetails = DashboardModel::fetchDashboardDescription(
            $deptCode,
            $regionCode,
            $distCode,
            $userId,
            $userTypeCode,
            $roleTypeCode,
            $auditScheduleId,
            $description
        );
        //print_r($dashboardDetails);
        // Pass data to Blade view
        //  return view('dashboard.dashboard', compact('dashboardDetails'));
        return response()->json($dashboardDetails);
    }

    
  public function DeptWiseAjax(Request $request)
    {

        $sourceForm = $request->input('source_form');

        $sessionChargeDetails = session('charge');
        $deptCode = $sessionChargeDetails->deptcode ?? null;
        $regionCode = $sessionChargeDetails->regioncode ?? null;
        $distCode = $sessionChargeDetails->distcode ?? null;

          $quarterVal = $request->input('quarter');
            $slipQuarterVal = $request->input('quarterslip'); 


                if ($sourceForm === 'plantabform') {
                    $quarter = $quarterVal;
                } else {
                    $quarter = $slipQuarterVal;
                }


        $getDeptName = DashboardModel::GetallDept($deptCode);

        $deptwisedata = [];

        if ($deptCode) {
            $countdetails = DashboardModel::GetcountDetails($deptCode, $regionCode, $distCode,$quarter)[0];

            if($sourceForm == 'sliptabform')
            {
                $countdetails['alloc_inscount'] = $countdetails['commencedinscount'];
            }

            $deptwisedata[] = [
                'deptname'        => $getDeptName[0]->deptelname,
                'regioncount'     => $countdetails['regioncount'],
                'distcount'       => $countdetails['distcount'],
                'alloc_inscount'  => $countdetails['alloc_inscount'],
                'deptCode'        => $deptCode,
                'regionCode'      => $regionCode,
                'distCode'        => $distCode,
                'totalslips'      => $countdetails['totalslipcount'],
                'pendingslipcount'      => $countdetails['pendingslipcount'],
                'convertedslipcount'      => $countdetails['convertedslipcount'],
                'droppedslipcount'      => $countdetails['droppedslipcount']
            ];
        } else {
            $getDeptName = DashboardModel::GetallDept();

            foreach ($getDeptName as $deptval) {
                $deptCode = $deptval->deptcode;
                $regionCode = null;
                $distCode = null;
                $getdetails = DashboardModel::GetcountDetails($deptCode, $regionCode, $distCode,$quarter)[0];

                if($sourceForm == 'sliptabform')
                {
                    $getdetails['alloc_inscount'] = $getdetails['commencedinscount'];
                }

                $deptwisedata[] = [
                    'deptname'        => $deptval->deptelname,
                    'regioncount'     => $getdetails['regioncount'],
                    'distcount'       => $getdetails['distcount'],
                    'alloc_inscount'  => $getdetails['alloc_inscount'],
                    'deptCode'        => $deptCode,
                    'regionCode'      => $regionCode,
                    'distCode'        => $distCode,
                    'totalslips'      => $getdetails['totalslipcount'],
                    'pendingslipcount'      => $getdetails['pendingslipcount'],
                    'convertedslipcount'      => $getdetails['convertedslipcount'],
                    'droppedslipcount'      => $getdetails['droppedslipcount']
                ];
            }
        }

        return response()->json(['data' => $deptwisedata]);
    }

    // public function InstitutedetailsGet(Request $request)
    // {
    //     $deptCode = $request->deptCode;
    //     $regionCode = $request->regionCode;
    //     $distCode = $request->distCode;
    //     $quarter = $request->input('quarter');
    //     $institutes=DashboardModel::InstitutedetailsGet($deptCode,$regionCode,$distCode, $quarter);
    //     //print_r($institutes);exit;

    //     foreach ($institutes as $item) {
    //         $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);
    //     }
    
    //     return response()->json(['data' => $institutes]); // format required by DataTables
    
    // }
 public function InstitutedetailsGet(Request $request)
    {
        $deptCode = $request->deptCode;
        $regionCode = $request->regionCode;
        $distCode = $request->distCode;

        $quarter = $request->input('quarter');

        $institutes=DashboardModel::InstitutedetailsGet($deptCode,$regionCode,$distCode, $quarter);
        //print_r($institutes);exit;

       /* foreach ($institutes as $item) {
            $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);
        }*/

        $institutes = collect($institutes)->map(function ($item) {
            $item['encrypted_auditscheduleid'] = Crypt::encryptString($item['auditscheduleid']);
            return $item;
        })->values(); 
    
        return response()->json(['data' => $institutes]); // format required by DataTables
    
    }


    
    public function CommencedInstitutedetailsGet(Request $request)
    {
        $deptCode = $request->deptCode;
        $regionCode = $request->regionCode;
        $distCode = $request->distCode;
        $whichslip =$request->whichslip;
        $quarter = $request->input('quarter');
        $institutes=DashboardModel::CommencedInstitutedetailsGet($deptCode,$regionCode,$distCode,$whichslip,$quarter);
        //print_r($institutes);exit;

        foreach ($institutes as $item) {
            $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);
        }
    
        return response()->json(['data' => $institutes]); // format required by DataTables
    
    }

    

    public function getauditslipdetails(Request $request)
    {
        $auditscheduleid =$request->auditscheduleid;

        $alldetails = DashboardModel::getslipcount($auditscheduleid);

        return $alldetails;

    }

    public function RegionwiseDetails(Request $request)
    {
        $deptCode = $request->deptCode;
        $regionCode = $request->regionCode;
        $distCode = $request->distCode;
        $sourceForm = $request->sourceform;
        $quarter = $request->input('quarter');

        $RegionwiseDetails=DashboardModel::RegionwiseDetails($deptCode,$regionCode,$distCode,$quarter);

        foreach ($RegionwiseDetails as $item) 
        {
            if(!$distCode)
            {
                $distCode=null;

            }
            $countdetails = DashboardModel::GetcountDetails($deptCode,$item->regioncode,$distCode,$quarter);


            $item->distcount =$countdetails[0]['distcount'];
            $item->alloc_inscount =$countdetails[0]['alloc_inscount'];
            $item->totalslips =$countdetails[0]['totalslipcount'];
            $item->pendingslipcount =$countdetails[0]['pendingslipcount'];
            $item->convertedslipcount =$countdetails[0]['convertedslipcount'];
            $item->droppedslipcount =$countdetails[0]['droppedslipcount'];
            $item->distcode =$distCode;

            if($sourceForm == 'sliptabform')
            {
                $item->alloc_inscount = $countdetails[0]['commencedinscount'];
            }



        }
    
        return response()->json(['data' => $RegionwiseDetails]); // format required by DataTables
    

    }

    public function DistrictwiseDetails(Request $request)
    {
        $deptCode = $request->deptCode;
        $regionCode = $request->regionCode;
        $distCode = $request->distCode;
        $sourceForm = $request->sourceform;
         $quarter = $request->input('quarter');

        $DistrictwiseDetails=DashboardModel::DistrictwiseDetails($deptCode,$regionCode,$distCode,$quarter);

        foreach ($DistrictwiseDetails as $item) 
        {
            if(!$distCode)
            {
                $distCode=null;

            }
            $countdetails = DashboardModel::GetcountDetails($deptCode,$item->regioncode,$item->distcode,$quarter);

            $item->alloc_inscount =$countdetails[0]['alloc_inscount'];

            if($sourceForm == 'sliptabform')
            {
                $item->alloc_inscount = $countdetails[0]['commencedinscount'];
            }


        }
    
        return response()->json(['data' => $DistrictwiseDetails]); // format required by DataTables
    

    }

}
