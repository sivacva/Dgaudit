<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


use App\Models\FieldAuditModel;
use App\Models\TransWorkAllocationModel;
use Illuminate\Http\Request;            
use App\Services\FileUploadService;
use App\Models\AuditManagementModel;
class FieldAuditController extends Controller
{

    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }


  /*  public function init_fieldaudit()
    {
        $userData = session('user');
        $session_userid = $userData->userid;


        $results = DB::table('audit.inst_schteammember as scm')
            ->join('audit.inst_auditschedule as sc', 'sc.auditscheduleid', '=', 'scm.auditscheduleid')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'sc.auditplanid')
            ->join('audit.mst_institution as mi', 'mi.instid', '=', 'ap.instid')
            ->where('auditeeresponse', 'A')
            ->where('scm.userid', $session_userid)
            ->where('sc.statusflag', 'F')
            ->where('scm.statusflag', 'Y')
            ->groupBy(
                'sc.auditscheduleid',
                'ap.auditplanid',
                'ap.instid',
                'mi.instename',
                'sc.fromdate',
                'sc.todate',
                'sc.entrymeetdate',
                'sc.exitmeetdate',
                'mi.deptcode',
                'mi.catcode',
                'mi.subcatid',
                'scm.auditteamhead',
                'sc.workallocationflag'
            )
            ->select(
                'sc.auditscheduleid',
                'sc.fromdate',
                'sc.todate',
                'ap.auditplanid',
                'ap.instid',
                'mi.instename',
                'sc.entrymeetdate',
                'sc.exitmeetdate',
                'mi.deptcode',
                'mi.catcode',
                'mi.subcatid',
                'scm.auditteamhead',
                'sc.workallocationflag'
            )
            ->get();

            $deptcode = '';
            $auditscheduleid='';
            $auditplanid='';    
        foreach ($results as $all) {
            $all->encrypted_auditscheduleid = Crypt::encryptString($all->auditscheduleid);
            $all->formatted_fromdate = Carbon::createFromFormat('Y-m-d', $all->fromdate)->format('d/m/Y');
            $all->formatted_todate = Carbon::createFromFormat('Y-m-d', $all->todate)->format('d/m/Y');
            if($all->entrymeetdate)
            {
                $all->entrymeetdate = Carbon::createFromFormat('Y-m-d', $all->entrymeetdate)->format('d/m/Y');
            }

            if($all->exitmeetdate)
            {
                $all->exitmeetdate = Carbon::createFromFormat('Y-m-d', $all->exitmeetdate)->format('d/m/Y');
            }

            $all->slipexists=FieldAuditModel::Slipexists($all->auditscheduleid);

 	    $all->exceed_exitmeetdate=FieldAuditModel::ExceedexitMeet($all->auditscheduleid);

            $deptcode = $all->deptcode;
            $catcode = $all->catcode;
            // return $catcode;
            $subcatid = $all->subcatid;
            $auditscheduleid=$all->auditscheduleid;
            $auditplanid=$all->auditplanid;
        }

        if($deptcode)
        {
            $fetchcurrquarter = AuditManagementModel::getCurrentQuarter($deptcode);
            $str_Quarter = $fetchcurrquarter->quarterfrom;
            $str_Quarter = date('Y-m-01',strtotime($str_Quarter));
    
            $end_Quarter = $fetchcurrquarter->quarterto;
            $end_Quarter = date('Y-m-t',strtotime($end_Quarter));
    
            $Quarter =['fromquarter'=>$str_Quarter,'toquarter'=>$end_Quarter];

            $supercheckquestions = FieldAuditModel::FetchSuperCheckList($auditscheduleid, $deptcode, $catcode, $subcatid);

        }else
        {
            $Quarter =['fromquarter'=>'','toquarter'=>''];

            $supercheckquestions = [];


        }

        


        return view('fieldaudit.init_fieldaudit', compact('results','Quarter','supercheckquestions','auditscheduleid','auditplanid'));
    }*/

public function init_fieldaudit()
    {
        $sessionchargedel = session('charge');
        $deptcode = $sessionchargedel->deptcode;

        $userData = session('user');
        $userid = $userData->userid;

        $results = DB::select("SELECT audit.getscheduledinstdel(?, ?) AS result", [$deptcode, $userid]);

        $results = json_decode($results[0]->result);
                 
            $deptcode = '';
            $auditscheduleid='';
            $auditplanid='';    

            
        foreach ($results as $all) {
          
            $all->encrypted_auditscheduleid = Crypt::encryptString($all->auditscheduleid);
            $all->formatted_fromdate = Carbon::createFromFormat('Y-m-d', $all->fromdate)->format('d/m/Y');
            $all->formatted_todate = Carbon::createFromFormat('Y-m-d', $all->todate)->format('d/m/Y');
            if($all->entrymeetdate)
            {
                $all->entrymeetdate = Carbon::createFromFormat('Y-m-d', $all->entrymeetdate)->format('d/m/Y');
            }

            if($all->exitmeetdate)
            {
                $all->exitmeetdate = Carbon::createFromFormat('Y-m-d', $all->exitmeetdate)->format('d/m/Y');
            }

            $all->slipexists=FieldAuditModel::Slipexists($all->auditscheduleid);

            $deptcode = $all->deptcode;
            $catcode = $all->catcode;
            // return $catcode;
            $subcatid = $all->subcatid;
            $auditscheduleid=$all->auditscheduleid;
            $auditplanid=$all->auditplanid;

             $supercheckquestions[$all->auditscheduleid] = FieldAuditModel::FetchSuperCheckList($auditscheduleid, $deptcode, $catcode, $subcatid);

        }


        // print_r($results);
        // exit;

        if($deptcode)
        {
            $fetchcurrquarter = AuditManagementModel::getCurrentQuarter($deptcode,'Q1');
            $str_Quarter = $fetchcurrquarter->quarterfrom;
            $str_Quarter = date('Y-m-01',strtotime($str_Quarter));
    
            $end_Quarter = $fetchcurrquarter->quarterto;
            $end_Quarter = date('Y-m-t',strtotime($end_Quarter));
    
            $Quarter =['fromquarter'=>$str_Quarter,'toquarter'=>$end_Quarter];

          //  $supercheckquestions = FieldAuditModel::FetchSuperCheckList($auditscheduleid, $deptcode, $catcode, $subcatid);

        }else
        {
            $Quarter =['fromquarter'=>'','toquarter'=>''];

            $supercheckquestions = [];


        }

        


        return view('fieldaudit.init_fieldaudit', compact('results','Quarter','supercheckquestions','auditscheduleid','auditplanid'));
    }




    public function getcategoryBasedOnSerious(Request $request)
    {
        // Validate the input
        $request->validate([
            'serious' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid number.',
        ]);

        // Get the department code
        $serious = $request->input('serious');


        // Fetch regions from the model
        $catcode = FieldAuditModel::getcategoryBasedSerious($serious);

        // Return JSON response
        if ($catcode->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $catcode]);
        } else {
            return response()->json(['success' => false, 'message' => 'No catcode found'], 404);
        }
    }



    public function getsubcategoryBasedOnCategory(Request $request)
    {
        // Validate the input
        $request->validate([
            'category' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid number.',
        ]);

        // Get the department code
        $category = $request->input('category');


        // Fetch regions from the model
        $subcategory = FieldAuditModel::getsubcategoryBasedCatgory($category);

        // Return JSON response
        if ($subcategory->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $subcategory]);
        } else {
            return response()->json(['success' => false, 'message' => 'No subcategory found'], 404);
        }
    }

    public function auditslip_dropdown($encrypted_auditscheduleid)
    {
        try {
            // Decrypt the encrypted audit schedule ID
            if ($encrypted_auditscheduleid) {
                $auditscheduleid = Crypt::decryptString($encrypted_auditscheduleid);
            }

            if ($auditscheduleid === null) {
                throw new \Exception("Audit schedule ID not found");
            }

            $chargeData = session('charge');
            $session_deptcode = $chargeData->deptcode; // Accessing the department code from the session
            $session_usertypecode = $chargeData->usertypecode;
            $userData = session('user');
            $session_userid = $userData->userid;

            if ($session_userid === null) {
                throw new \Exception("User ID not found");
            }

            $scheduledel = FieldAuditModel::getscheduledel_basedonuser($session_userid, $auditscheduleid);

            $teamheaddel = FieldAuditModel::getAuditScheduleHeaddel($auditscheduleid);

            $severitydel = FieldAuditModel::getSeverity();

            $schemename = FieldAuditModel::getSchemename();

            $serious = FieldAuditModel::getSerious();

            $getMainobjection = FieldAuditModel::getMainobjection($session_userid, $auditscheduleid);

            $session_userid = Crypt::encryptString($session_userid);


            if ($getMainobjection->isEmpty()) {

                throw new \Exception("No Main Objection found for the user");
            }

            if ($scheduledel[0]->auditteamhead == 'N') {
                $sessionuserTeamheadOrNot    =   'N';
            } else {

                $sessionuserTeamheadOrNot    =   'Y';
                $getMainobjection =   '';
            }

            if (($scheduledel->isEmpty()) || ($teamheaddel->isEmpty())) {
                Log::info("No audit schedule details found for user ID: {$session_userid} and audit schedule ID: {$auditscheduleid}");
                return redirect()->route('site.error')->with('error', 'No audit schedule details found.');
            }

            $scheduleheadid =   $teamheaddel[0]->userid;
 return view('fieldaudit.auditslip', compact('scheduledel', 'scheduleheadid', 'getMainobjection', 'sessionuserTeamheadOrNot', 'severitydel', 'schemename', 'serious','session_userid','encrypted_auditscheduleid'));
        } catch (\Exception $e) {
            Log::error("Error in auditslip_dropdown: " . $e->getMessage());
            echo $e->getMessage();
            //return redirect()->route('error')->with('error', 'An error occurred while processing the auditslip. Please try again later.');
        }
    }

    public function getobjectionForHead(Request $request)
    {
        try {
            $auditscheduleid  =   $request->input('auditscheduleid');
            $createdby  =   $request->input('createdby');

            $userData = session('user');
            $session_userid = $userData->userid;

            if (!($createdby))  $request->merge(['createdby' => $session_userid]);

            $rules = [
                'auditscheduleid' => 'required|integer',
                'createdby' => 'required|integer',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            $alldetails = FieldAuditModel::getMainobjection($request->input('createdby'), $auditscheduleid);

            if ($alldetails->isNotEmpty()) {
                return response()->json(['success' => true, 'data' => $alldetails], 200);
            } else {
                return response()->json(['success' => true, 'message' => 'nomainobjectionfound', 'error' => '400'], 400);
            }
        } catch (\Exception $e) {
            Log::error("Error in auditslip_dropdown: " . $e->getMessage());
            return $e->getMessage();
            // return redirect()->route('error')->with('error', 'An error occurred while processing the auditslip. Please try again later.');
        }
    }


    public function view_fieldaudit()
    {
        $userData = session('user');
        $session_userid = $userData->userid;


        $results = DB::table('audit.inst_schteammember as scm')
            ->join('audit.inst_auditschedule as sc', 'sc.auditscheduleid', '=', 'scm.auditscheduleid')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'sc.auditplanid')
            ->join('audit.mst_institution as mi', 'mi.instid', '=', 'ap.instid')
            ->where('auditeeresponse', 'A')
            ->where('scm.userid', $session_userid)
            ->groupBy(
                'sc.auditscheduleid',
                'ap.auditplanid',
                'ap.instid',
                'mi.instename',
                'sc.fromdate',
                'sc.todate'
            )
            ->select(
                'sc.auditscheduleid',
                'sc.fromdate',
                'sc.todate',
                'ap.auditplanid',
                'ap.instid',
                'mi.instename',
                'sc.exitmeetdate'
            )
            ->get();
        $resultsNew = [];
        foreach ($results as $all) {

            if ($all->exitmeetdate) {
                $currdate = strtotime(date('d-m-Y'));
                $exitmeetdate = strtotime($all->exitmeetdate);

                if ($currdate > $exitmeetdate) {
                    $all->encrypted_auditscheduleid = Crypt::encryptString($all->auditscheduleid);
                    $all->formatted_fromdate = Carbon::createFromFormat('Y-m-d', $all->fromdate)->format('d/m/Y');
                    $all->formatted_todate = Carbon::createFromFormat('Y-m-d', $all->todate)->format('d/m/Y');
                    $resultsNew[] = $all;
                }
            }
        }
        $results = json_encode($resultsNew);
        return view('audit.listinstitute', compact('results'));
    }

    public function auditfield_dropdown($encrypted_auditscheduleid)
    {
        if ($encrypted_auditscheduleid) {
            $auditscheduleid = Crypt::decryptString($encrypted_auditscheduleid);
        }
     
        // Echo the ID to verify it's being passed correctly
        // Access session data
        $chargeData = session('charge');
        $session_deptcode = $chargeData->deptcode; // Accessing the department code from the session
        $session_usertypecode = $chargeData->usertypecode;
        $userData = session('user');
        $session_userid = $userData->userid;

        $get_majorobjection = DB::table('audit.mst_mainobjection as ma')
            ->where('ma.deptcode', $session_deptcode) // Query based on department code
            ->where('ma.statusflag', '=', 'Y') // Filter for active or enabled records
            ->select('ma.objectionename', 'ma.objectiontname', 'ma.mainobjectionid') // Select the necessary fields
            ->orderBy('ma.objectionename', 'asc')
            ->get();

        $inst_details = DB::table('audit.inst_schteammember as sm')
            ->join('audit.inst_auditschedule as is', 'is.auditscheduleid', '=', 'sm.auditscheduleid')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'is.auditplanid')
            ->join('audit.mst_institution as in', 'in.instid', '=', 'ap.instid')
            ->join('audit.mst_auditeeins_category as incat', 'incat.catcode', '=', 'in.catcode')
            ->join('audit.mst_typeofaudit as ta', 'ta.typeofauditcode', '=', 'ap.typeofauditcode')
            //  ->join('audit.mst_auditperiod as d', 'd.auditperiodid', '=', 'ap.auditperiodid')
            ->join('audit.yearcode_mapping as yrmap', 'yrmap.auditplanid', '=', 'ap.auditplanid')
            ->join(
                'audit.mst_auditperiod as d',
                DB::raw('CAST(yrmap.yearselected AS INTEGER)'),
                '=',
                'd.auditperiodid'
            )
            ->where('yrmap.statusflag','Y')
            ->where('userid', $session_userid)
            ->where('is.auditscheduleid', $auditscheduleid)
            // Apply STRING_AGG to aggregate years

            ->select(
                'is.auditscheduleid',
                'sm.auditscheduleid',
                'sm.auditteamhead',
                'is.auditplanid',
                'is.fromdate',
                'is.todate',
                'ap.instid',
                'in.instename',
                'incat.catename',
                'in.mandays',
                'in.catcode',
                'in.deptcode',
                'in.subcatid',
                'sm.auditteamhead',
                'ta.typeofauditename',
                'sm.schteammemberid',
                DB::raw('STRING_AGG(DISTINCT d.fromyear || \'-\' || d.toyear, \', \') as yearname')
            )
            ->groupby('is.auditscheduleid', 'sm.auditscheduleid', 'sm.auditteamhead', 'is.auditplanid', 'is.fromdate', 'is.todate', 'ap.instid', 'in.instename', 'incat.catename', 'in.mandays', 'sm.auditteamhead', 'ta.typeofauditename', 'sm.schteammemberid', 'in.catcode', 'in.deptcode', 'in.subcatid')

            ->get();
        $teammemdel = DB::table('audit.inst_schteammember as sm');
        $teamheadid = 'N';
        if ($inst_details[0]->auditteamhead == 'N') {

            $teamheaddel = DB::table('audit.inst_schteammember as sm')
                ->where('auditscheduleid', $auditscheduleid)
                ->where('auditteamhead', 'Y')
                ->select('sm.userid')
                ->get();  // added 'get()' to fetch data
            $teamheadid =   $teamheaddel[0]->userid;
        }
        $teammemdel = DB::table('audit.inst_schteammember as sm')

            ->join('audit.userchargedetails as uc', 'sm.userid', '=', 'uc.userid')
            ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid')
            ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('sm.statusflag', 'Y')
            ->select(
                'sm.schteammemberid',
                'sm.userid',
                'de.desigelname',
                'du.username',
                'sm.auditteamhead'
            )
            ->get();
        $majorworkdel = DB::table('audit.mst_majorworkallocationtype')
            ->where('statusflag', 'Y')
            ->select(
                'mst_majorworkallocationtype.majorworkallocationtypeename',
                'mst_majorworkallocationtype.majorworkallocationtypeid',
            )
            ->orderBy('mst_majorworkallocationtype.updatedby', 'asc')
            ->get();
        // Option 1: Returning a view with the data (pass the data to the view)

        // print_r($inst_details);

        $deptcode = $inst_details->first()->deptcode;
        $catcode = $inst_details->first()->catcode;
        // return $catcode;
        $subcatid = $inst_details->first()->subcatid;

        $supercheckquestions = FieldAuditModel::FetchSuperCheckList($auditscheduleid, $deptcode, $catcode, $subcatid);

        return view('fieldaudit.fieldaudit', compact('get_majorobjection', 'inst_details', 'teamheadid', 'teammemdel', 'majorworkdel', 'supercheckquestions'));


        // You can also add logic to handle the ID if needed
    }







    public function slipdetails_dropdown($viewvalue)
    {
        $chargeData = session('charge');
        $session_deptcode = $chargeData->deptcode; // Accessing the department code from the session
        $session_usertypecode = $chargeData->usertypecode;
        $userData = session('user');
        $session_userid = $userData->userid;

        $get_majorobjection = DB::table('audit.mst_mainobjection as ma')
            ->where('ma.deptcode', $session_deptcode) // Query based on department code
            ->where('ma.statusflag', '=', 'Y') // Filter for active or enabled records
            ->select('ma.objectionename', 'ma.objectiontname', 'ma.mainobjectionid') // Select the necessary fields
            ->orderBy('ma.objectionename', 'asc')
            ->get();

        $inst_details = DB::table('audit.trans_auditslip as ta')
            ->join('audit.sliphistorytransactions as t', 'ta.auditslipid', '=', 't.auditslipid')
            // ->join('audit.trans_auditslip as ta', 'ta.auditslipid', '=', 'st.auditslipid')
            ->join('audit.inst_auditschedule as sm', 'sm.auditscheduleid', '=', 'ta.auditscheduleid')
            ->join('audit.inst_schteammember as sme', 'sme.auditscheduleid', '=', 'ta.auditscheduleid')
            ->join('audit.deptuserdetails as dud', 'dud.deptuserid', '=', 'sme.userid')
            ->where('sme.auditteamhead', 'Y') // Assuming this is part of your condition
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'sm.auditplanid')
            ->join('audit.mst_institution as "in"', '"in".instid', '=', 'ap.instid') // Quote reserved keywords like 'in'
            ->join('audit.mst_auditeeins_category as incat', 'incat.catcode', '=', '"in".catcode')
            ->join('audit.mst_typeofaudit as tad', 'tad.typeofauditcode', '=', 'ap.typeofauditcode')
            ->join('audit.yearcode_mapping as yrmap', 'yrmap.auditplanid', '=', 'ap.auditplanid')
            ->join(
                'audit.mst_auditperiod as d',
                DB::raw('CAST(yrmap.yearselected AS INTEGER)'),
                '=',
                'd.auditperiodid'
            )
            // ->where(function ($query) use ($session_userid) {
            //     $query->where('st.forwardedto', $session_userid)
            //         ->where('st.forwardedtousertypecode', 'I')
            //         ->orWhere(function ($query) use ($session_userid) {
            //             $query->where('t.forwardedby', $session_userid)
            //                 ->where('t.forwardedbyusertypecode', 'A');
            //         });
            // })
            ->where(function ($query) use ($session_userid) {
                $query->where(function ($subquery) use ($session_userid) {
                    $subquery->where('ta.forwardedto', $session_userid)
                        ->where('ta.forwardedtousertypecode', 'I');
                })->orWhere(function ($subquery) use ($session_userid) {
                    $subquery->where('t.forwardedby', $session_userid)
                        ->where('t.forwardedbyusertypecode', 'I');
                });
            })
            ->select(
                'sm.auditscheduleid',
                'sme.schteammemberid',
                'sme.userid',
                'sm.auditplanid',
                'ap.instid',
                '"in".instename',
                'incat.catename',
                '"in".mandays',
                '"in".annadhanam_only',
                '"in".deptcode',
                'tad.typeofauditename',
                'dud.username',
                DB::raw("STRING_AGG(DISTINCT d.fromyear || '-' || d.toyear, ', ') 
                    FILTER (WHERE d.financestatus = 'N') as yearname"),
                DB::raw("STRING_AGG(DISTINCT d.fromyear || '-' || d.toyear, ', ') 
                    FILTER (WHERE d.financestatus = 'Y') as annadhanamyear")
                //DB::raw('STRING_AGG(DISTINCT d.fromyear || \'-\' || d.toyear, \', \') as yearname') // Apply STRING_AGG to aggregate years
            )
            ->groupBy(
                'sm.auditscheduleid',
                'sme.schteammemberid',
                'sme.userid',
                'sm.auditplanid',
                'ap.instid',
                '"in".instename',
                'incat.catename',
                '"in".mandays',
                '"in".annadhanam_only',
                '"in".deptcode',
                'tad.typeofauditename',
                'dud.username'
            )
            ->get();




        $teammemdel = '';
        $majorworkdel = '';

        $get_majorobjection = DB::table('audit.mst_mainobjection as ma')
            ->where('ma.deptcode', $session_deptcode) // Query based on department code
            ->where('ma.statusflag', '=', 'Y') // Filter for active or enabled records
            ->select('ma.objectionename', 'ma.objectiontname', 'ma.mainobjectionid') // Select the necessary fields
            ->get();

        if (count($inst_details)) {
            $teamheadid = $inst_details[0]->userid;
        } else $teamheadid = '';

        $severitydel = FieldAuditModel::getSeverity();

        //echo 'jo';

        //print_r($inst_details);

        return view($viewvalue, compact('get_majorobjection', 'inst_details', 'teamheadid', 'teammemdel', 'majorworkdel', 'severitydel'));
    }


    public function getauditslip(Request $request)
    {
        // Retrieve 'charge' from session
        $chargedel = session('charge');
        $userdel = session('user');
        $filter =   $request->input('filter');
        $action =   $request->input('action');

        $usertypecode = $chargedel->usertypecode;

        if ($usertypecode  == View::shared('auditorlogin')) {
            $userchargeid = $chargedel->userchargeid;
            $auditteamhead = $chargedel->auditteamhead;
            $auditscheduleid        =   $request->input('auditscheduleid');
        }
        $userid = $userdel->userid;

        // Validate auditslipid if it's provided in the request
        if ($request->input('auditslipid')) {
            try {
                // Decrypt the auditslipid
                $auditslipid = Crypt::decryptString($request->auditslipid);
                $request->merge(['auditslipid' => $auditslipid]);

                // Validate decrypted auditslipid
                $request->validate([
                    'auditslipid' => 'required|integer',
                ], [
                    'required' => 'The :attribute field is required.',
                    'integer' => 'The :attribute field must be a valid number.',
                ]);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                return response()->json(['success' => false, 'message' => 'Invalid auditslipid.'], 400);
            }
        } else {
            $auditslipid    =   null;
        }

        // Since 'userchargeid' is from session, no need to validate it via request
        // But ensure userchargeid exists in session


        if ($usertypecode  == View::shared('auditorlogin')) {
            if (!$userchargeid) {
                return response()->json(['success' => false, 'message' => 'User ID not provided'], 400);
            }
            //echo $auditslipid;
            $alldetails = FieldAuditModel::getslipdetails($userid, $auditslipid, $auditteamhead, $auditscheduleid, $filter, $action);

            //print_r($alldetails);
            // exit;

            if ($alldetails['auditDetails']->isNotEmpty()) {
                foreach ($alldetails['auditDetails'] as $all) {
                    $all->encrypted_auditslipid = Crypt::encryptString($all->auditslipid);
                }
            }
        } else if ($usertypecode  == View::shared('auditeelogin')) {
            if (!$userid) {
                return response()->json(['success' => false, 'message' => 'User ID not provided'], 400);
            }
            $alldetails = FieldAuditModel::fetchdata_auditee($userid, $auditslipid, $action, $filter);

            // Check if 'auditDetails' is not empty
            if ($alldetails['auditDetails']->isNotEmpty()) {
                foreach ($alldetails['auditDetails'] as $all) {
                    $all->encrypted_auditslipid = Crypt::encryptString($all->auditslipid);
                }
            }
        }


        // Return response with the data
        if ($alldetails->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $alldetails]);
        } else {
            return response()->json(['success' => true, 'message' => 'No auditslips found'], 200);
        }
    }


    public function auditeereply(Request $request, $userId = null)
    {
        $action = $request->input('action');
        $rejoinderstatus    =   $request->input('rejoinderstatus');
        $rejoindercycle    =   $request->input('rejoindercycle');

        $auditscheduleid    =   $request->input('auditscheduleid');

        $auditslipid =  Crypt::decryptString($request->auditslipid);

        $fileupload = $request->file('fileupload');
        $destinationPath = 'uploads/slipauditor';

        $userdel = session('user');
        $chargeData = session('charge');

        $session_userid = $userdel->userid ?? null;
        $sessionDeptcode = $chargeData->deptcode ?? null;
        if (!$session_userid) {
            return response()->json(['error' => 'User session is invalid.'], 400);
        }

        $deactive_fileuploadids = $request->input('deactive_fileid') ? explode(',', $request->input('deactive_fileid')) : [];
        $active_fileuploadids = $request->input('active_fileid') ? explode(',', $request->input('active_fileid')) : [];

        // Deactivate Files
        if (!empty($deactive_fileuploadids)) {
            $this->fileUploadService->deactive_uploadefile($auditslipid, $deactive_fileuploadids);
        }

        $fileUploadId = null;

        if (($request->hasFile('fileupload'))) {

            //print_r($fileupload);

            // public function insert_slipfileupload($auditslipid, array $fileUploadIds,$rejoinderstatus,$rejoindercycle,$processcode)

            // public function slipMultipleFileUpload(array $files, string $destinationPath, $auditslipid, $active_fileuploadid, $deactive_fileuploadid, $rejoinderstatus,$auditscheduleid)


            $uploadResult = $this->fileUploadService->slipMultipleFileUpload(
                $fileupload,
                $destinationPath,
                $auditslipid,
                $active_fileuploadids,
                $deactive_fileuploadids,
                $rejoinderstatus,
                $auditscheduleid
            );

            //  print_r($uploadResult );


            if (is_array($uploadResult) && isset($uploadResult['error'])) {
                return response()->json(['errors' => $uploadResult['error']], 400);
            } elseif ($uploadResult instanceof \Illuminate\Http\JsonResponse) {
                $fileUploadId = $uploadResult->getData(true)['uploaded_files'];
                // $fileUploadId  =   $fileUploadId[0];

            }
        }


        $request->validate([
            // 'auditee_upload' => 'required',  // Optional, max length of 255
            'auditeeremarks_append' => 'required', // Optional, max length of 255
        ], [
            'required' => 'The :attribute field is required.',
            'alpha' => 'The :attribute field must contain only letters.',
            'integer' => 'The :attribute field must be a valid number.',
            'regex' => 'The :attribute field must be a valid number.',
            'alpha_num' => 'The :attribute field must contain only letters and numbers.',
            'max' => 'The :attribute field must not exceed :max characters.',
        ]);

        // Process content for remarks
        $content = json_encode([
            'content' => $request->input('auditeeremarks_append')
        ]);

        $userdel = session('user');
        $userid = $userdel->userid;




        // Prepare the data to insert or update
        $data = [
            'updatedon'         =>  View::shared('get_nowtime'),
            'updatedby'         =>  $userid
        ];

        $data['remarks']  =   $content;
        $processcode    =   'U';
        $data['processcode']  =   $processcode;


        try {
            // Insert or update the audit slip record
            $auditslipdel = FieldAuditModel::createIfNotExistsOrUpdate($data, $auditslipid, '', $sessionDeptcode);

            $scheduleheaddel = FieldAuditModel::getAuditScheduleHeaddel($auditslipdel['auditscheduleid']);
            $scheduleheadid =   $scheduleheaddel[0]->userid;



            $auditslipnumber    =   $auditslipdel['slipnumber'];
            $auditslipid    =   $auditslipdel['auditslipid'];
            $createdby      =   $auditslipdel['createdby'];


            $teamlead   =   'N';


            if ($createdby  == $scheduleheadid) {
                $teamlead   =   'Y';
                $processcode_slipfileupload    =   'R';
            } else {
                $processcode_slipfileupload    =   'M';
            }




            // Proceed only if the audit slip was successfully created/updated
            if ($auditslipid) {


                // Create a relation for the file upload
                $data = [
                    'fileuploadid' => $fileUploadId,
                    'auditslipid' => $auditslipid,
                    'statusflag' => 'Y',
                    'updatedon'         =>  View::shared('get_nowtime'),
                    'updatedby'         =>  $userid
                ];


                if ($request->input('rejoinderstatus') == 'Y')
                    $data['rejoinderstatus']    =   'Y';

                // echo 'slipfileupload';


                // print_r($data);


                if ($fileUploadId) {
                    $this->fileUploadService->insert_slipfileupload($auditslipid, $fileUploadId, $rejoinderstatus, $rejoindercycle, $processcode);
                }

                // echo $processcode_slipfileupload;
                // echo $processcode;
                // exit;

                if ($request->input('finaliseflag') == 'Y') {

                    // echo 'statusflagY';

                    $chargeData = session('charge');
                    $session_usertypecode = $chargeData->usertypecode; // Accessing the department code from the session


                    // $teamheadids = FieldAuditModel::fetchdata_teamheaduserid($auditslipid);
                    // $teamheadids = FieldAuditModel::fetchdata_slipcreatedby($auditslipid);

                    // $teamheadid  =   $teamheadids[0];


                    if ($createdby) {
                        // Handle the insertion of new transaction for the auditee
                        // $insertdata = [
                        //     'auditslipid' => $auditslipid,
                        //     'createdby' => $userid,
                        //     'createdon' => View::shared('get_nowtime'),
                        //     'forwardedto' => $createdby,
                        //     'forwardedtousertypecode' => 'A',
                        //     'updatedby' => $userid,
                        //     'updatedbyusertypecode' => $session_usertypecode,
                        //     'updatedon' => View::shared('get_nowtime'),
                        // ];

                        $updatedata = [
                            'forwardedto' => $createdby,
                            'forwardedtousertypecode' => 'A',
                            'updatedby' => $userid,
                            'updatedbyusertypecode' => 'I',
                            'updatedon' => View::shared('get_nowtime'),
                        ];

                        // print_r($insertdata);
                        // print_r($updatedata);


                        // // Insert transaction and update
                        // $transactionResult = FieldAuditModel::create_transactiondel($insertdata, $updatedata, $auditslipid);

                        // if ($transactionResult) {
                        // Insert history transaction if transaction was successful
                        $historyData = [
                            'auditslipid' => $auditslipid,
                            'forwardedby' => $userid,
                            'forwardedbyusertypecode' => $session_usertypecode,
                            'forwardedto' => $createdby,
                            'forwardedtousertypecode' => 'A',
                            'forwardedon' => View::shared('get_nowtime'),
                            'transstatus' => 'A',
                            'processcode' => $processcode_slipfileupload,
                            'remarks' => $content,
                        ];


                        if ($rejoinderstatus ==  'Y')    $historyData['rejoinderstatus']    =   'Y';
                        if (($rejoindercycle > 0))    $historyData['rejoindercycle']    =   $rejoindercycle;

                        // print_r($updatedata);
                        $historyTransaction = FieldAuditModel::insert_historytransactiondel($historyData);

                        if ($historyTransaction) {
                            // Update the auditslip table after inserting history transaction
                            $updateData = [
                                'processcode' => $processcode_slipfileupload,
                                'remarks' => $content,
                                'forwardedto' => $createdby,
                                'forwardedtousertypecode' => 'A',
                                'updatedby' => $userid,
                                'updatedbyusertypecode' => 'I',
                                'updatedon' => View::shared('get_nowtime'),

                            ];

                            //  print_r($updateData);
                            $updateSlip = FieldAuditModel::update_auditsliptable($updateData, $auditslipid);

                            if ($updateSlip) {

                                FieldAuditModel::updateslipfileupload($processcode_slipfileupload, $session_userid, $processcode, $auditslipid, $session_usertypecode, $rejoinderstatus, $rejoindercycle);
                                //DB::commit();
                                return response()->json(['success' => true, 'message' => 'Audit slip forwarded to Audit Team Successfully.', 'data' => array('slid' => Crypt::encryptString($auditslipid), 'auditslipnumber' => $auditslipnumber)]);
                            } else throw new \Exception("Failed to insert history transaction.");
                        } else {
                            throw new \Exception("Failed to insert or update transaction.");
                        }
                        //}
                    }
                } else {
                    return response()->json(['success' => true, 'message' => 'Audit slip Data Saved successfully.', 'data' => array('slid' => Crypt::encryptString($auditslipid), 'auditslipnumber' => $auditslipnumber)]);
                }



                // if ($request->input('finaliseflag') === 'Y') {
                //     $session_usertypecode = $chargeData->usertypecode ?? null;
                //     $session_userchargeid = $chargeData->userchargeid ?? null;


                //     if ($createdby) {

                //         $historyData = [
                //             // 'auditslipid' => $auditslipid,
                //             // 'auditscheduleid' => $request->input('auditscheduleid'),
                //             // 'schteammemberid' => $request->input('schteammemberid'),
                //             // 'auditplanid' => $request->input('auditplanid'),
                //             // 'mainobjectionid' => $request->input('majorobjectioncode'),
                //             // 'subobjectionid' => $request->input('minorobjectioncode'),
                //             // 'tempslipnumber' => 1,
                //             // 'tempslipnumber' => $auditslipnumber,
                //             // 'severityid' => $request->input('severityid'),
                //             // 'liability' => $request->input('liability'),
                //             // 'slipdetails' => $request->input('slipdetails'),
                //             'remarks' => $content,
                //             'processcode'   =>  $processcode_slipfileupload,
                //             'forwardedby' => $session_userid,
                //             'forwardedbyusertypecode' => $session_usertypecode,
                //             'forwardedto' => $createdby,
                //             'transstatus' => 'A',
                //             'forwardedon' => View::shared('get_nowtime'),
                //         ];

                //         $updateTransauditData = [

                //             'updatedby' => $session_userid,
                //             'updatedbyusertypecode' => $session_usertypecode,
                //             'updatedon' => View::shared('get_nowtime'),
                //             'processcode' => $processcode_slipfileupload,
                //             'forwardedto' => $createdby,
                //             'forwardedtousertypecode' => 'A',
                //         ];

                //         $transactionResult = FieldAuditModel::insert_historytransactiondel($historyData, $auditslipid);

                //         if ($transactionResult) {

                //             $updateSlip = FieldAuditModel::update_auditsliptable($updateTransauditData, $auditslipid);
                //             if ($updateSlip) {

                //                 FieldAuditModel:: updateslipfileupload($processcode_slipfileupload,$session_userid,$processcode,$auditslipid,$session_usertypecode,$rejoinderstatus,$rejoindercycle);

                //                 // DB::commit();
                //                 return response()->json(['success' => true, 'message' => $message, 'data' => $auditslipnumber]);
                //             } else {
                //                 throw new \Exception("Failed to update the auditslip table.");
                //             }
                //         }
                //     }
                //     else
                //     {
                //         return response()->json(['success' => true, 'message' => 'No User Found', 'data' => $auditslipnumber]);
                //     }
                // }
                // else
                // {
                //     return response()->json(['success' => true, 'message' => 'Audit Slip saved successfully.', 'data' => $auditslipnumber]);
                // }
                // } else {
                //     throw new \Exception("Failed to create file upload relation.");
                // }
            } else {
                throw new \Exception("Failed to create or update the audit slip.");
            }
        } catch (\Exception $e) {
            // Rollback the transaction on failure
            // DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }




    // public function audislip_insert(Request $request, $userId = null)
    // {
    //     // print_r($request->all());
    //     // exit;

    //     $action = $request->input('action');

    //     if (($request->input('liability') == 'Y')) {

    //         $notype   =   $request->input('notype');
    //         $name   =   $request->input('name');
    //         $gpfno   =   $request->input('gpfno');
    //         $amount   =   $request->input('amount');
    //         $designation   =   $request->input('designation');

    //         $liabilityid    =   $request->input('liabilityid');

    //         $liabilitydel  =   $request->input('liabilityid');

    //         $count_name = count($name);
    //         $deleted_liabilityid   =   $request->input('deleted_liabilityid');
    //         // $liabilityid   =   $request->input('liabilityid');



    //     }

    //     if (($request->input('scheme') == 'Y')) {

    //         $schemename   =   $request->input('schemename');
    //     }




    //     $teamhead   =   $request->input('teamhead');
    //     $auditscheduleid =   $request->input('auditscheduleid');
    //     $rejoinderstatus    =   $request->input('rejoinderstatus');

    //     $rejoindercycle     =    $request->input('rejoindercount');
    //     $slipcreatedby     =    $request->input('slipcreatedby');

    //     $actionfor     =    $request->input('actionfor');
    //     $rejoindersuggestion     =    $request->input('rejoindersuggestion');

    //     $auditslipid = ($action == 'update' && $request->auditslipid) ? Crypt::decryptString($request->auditslipid) : null;


    //     $fileupload = $request->file('fileupload');
    //     // $destinationPath = 'slipauditor';

    //     $destinationPath = '';


    //     $userdel = session('user');
    //     $chargeData = session('charge');

    //     $sessionDeptcode = $chargeData->deptcode ?? null;

    //     $session_userid = $userdel->userid ?? null;
    //     if (!$session_userid) {
    //         return response()->json(['error' => 'User session is invalid.'], 400);
    //     }

    //     $deactive_fileuploadids = $request->input('deactive_fileid') ? explode(',', $request->input('deactive_fileid')) : [];
    //     $active_fileuploadids = $request->input('active_fileid') ? explode(',', $request->input('active_fileid')) : [];

    //     // Deactivate Files
    //     if (!empty($deactive_fileuploadids)) {
    //         $this->fileUploadService->deactive_uploadefile($auditslipid, $deactive_fileuploadids);
    //     }

    //     $fileUploadId = null;

    //     if ((($action === 'insert') || ($action === 'update')) && ($request->hasFile('fileupload'))) {
    //         $uploadResult = $this->fileUploadService->slipMultipleFileUpload(
    //             $fileupload,
    //             $destinationPath,
    //             $auditslipid,
    //             $active_fileuploadids,
    //             $deactive_fileuploadids,
    //             '',
    //             $auditscheduleid
    //         );

    //         // print_r($uploadResult );

    //         if (is_array($uploadResult) && isset($uploadResult['error'])) {
    //             return response()->json(['errors' => $uploadResult['error']], 400);
    //         } elseif ($uploadResult instanceof \Illuminate\Http\JsonResponse) {
    //             $fileUploadId = $uploadResult->getData(true)['uploaded_files'];
    //             // print_r($fileUploadId);
    //         }
    //     }


    //     $request->validate([
    //         'majorobjectioncode' => ['required_if:actionfor,fresh', 'string', 'regex:/^\d+$/'],
    //         'minorobjectioncode' => ['required_if:actionfor,fresh', 'regex:/^\d+$/'],
    //         'amount_involved' => 'nullable|regex:/^\d{1,10}(\.\d{1,2})?$/',
    //         'severityid' => 'required|alpha|max:1',
    //         'liability' => 'required|alpha|max:1',
    //         'slipdetails' => 'required|string|max:500',

    //         'scheme' => 'required|alpha|max:1',
    //         'serious' => 'required|string|max:2',
    //         'category' => 'required|string|max:2',
    //         'subcategory' => 'required|string|max:2',
    //     ]);

    //     $content = json_encode(['content' => $request->input('remarks')]);

    //     $data = [
    //         'auditscheduleid' => $request->input('auditscheduleid'),
    //         'schteammemberid' => $request->input('schteammemberid'),
    //         'auditplanid' => $request->input('auditplanid'),
    //         'mainobjectionid' => $request->input('majorobjectioncode'),
    //         'subobjectionid' => $request->input('minorobjectioncode'),
    //         'tempslipnumber' => $request->input('currentslipnumber'),
    //         'severitycode' => $request->input('severityid'),
    //         'liability' => $request->input('liability'),

    //         'schemastatus' => $request->input('scheme'),
    //         'auditeeschemecode' => $request->input('schemename'),

    //         'irregularitiescode' => $request->input('serious'),
    //         'irregularitiescatcode' => $request->input('category'),
    //         'irregularitiessubcatcode' => $request->input('subcategory'),


    //         'slipdetails' => $request->input('slipdetails'),
    //         'remarks' => $content,
    //         'statusflag' => 'Y',
    //         // 'liabilityname' => $request->input('liability') == 'Y' ? $request->input('liabilityname') : '',
    //         // 'liabilitygpfno' => $request->input('liability') == 'Y' ? $request->input('liabilitygpfno') : '',
    //         // 'liabilitydesig' => $request->input('liability') == 'Y' ? $request->input('liabilitydesig') : '',

    //     ];

    //     if ($request->input('amount_involved')) {
    //         $data['amtinvolved'] = $request->input('amount_involved');
    //     } else    $data['amtinvolved']    =   null;

    //     if ($action === 'insert') {
    //         $processcode    =    'E';
    //         $data['processcode'] = 'E';
    //         $data['createdon'] = View::shared('get_nowtime');
    //         $data['createdby'] = $session_userid;
    //     } elseif ($action === 'update') {
    //         if (($slipcreatedby != $session_userid) &&  $teamhead == 'Y' && $actionfor == 'fresh') {
    //             $processcode    =    'T';
    //         } elseif (($slipcreatedby == $session_userid) && $actionfor == 'fresh') {
    //             $processcode    =    'E';
    //         } elseif (($actionfor == 'memeberrejoinder')) {
    //             $processcode    =    'R';
    //             if ($rejoindersuggestion ==  'Y')
    //                 $rejoinderstatus    =  'R';
    //             $data['rejoinderstatus'] =  $rejoinderstatus;
    //         } elseif (($actionfor == 'drop')) {
    //             $processcode    =    'A';
    //         } elseif (($actionfor == 'converttopara')) {
    //             $processcode    =    'X';
    //         } elseif (($actionfor == 'rejoinder')) {
    //             $processcode    =    'F';
    //             $data['rejoinderstatus'] =  'Y';
    //             $rejoinderstatus    =   'Y';
    //             if ($rejoindercycle ==  '') $rejoindercycle = 0;
    //             $rejoindercycle =   $rejoindercycle +   1;
    //             $data['rejoindercycle'] =  $rejoindercycle;
    //         }

    //         $data['updatedon'] = View::shared('get_nowtime');
    //         $data['updatedby'] = $session_userid;
    //     }

    //     // print_r($data);

    //     // exit;


    //     // DB::beginTransaction();

    //     try {
    //         $auditslipdel = FieldAuditModel::createIfNotExistsOrUpdate($data, $auditslipid, $auditscheduleid, $sessionDeptcode);
    //         $auditslipnumber = $auditslipdel['slipnumber'];
    //         $auditslipid = $auditslipdel['auditslipid'];

    //         if ($fileUploadId) {
    //             $this->fileUploadService->insert_slipfileupload($auditslipid, $fileUploadId, $rejoinderstatus, $rejoindercycle, $processcode);
    //         }


    //         // if(($request->input('liability') == 'Y'))
    //         // {
    //         //     FieldAuditModel::insertUpdateliability($auditslipid,$name,$gpfno,$designation,$amount,$liabilitydel[$i],)
    //         // }

    //         if (($request->input('liability') == 'Y')) {

    //             $activestatus   =   '';
    //             if ($processcode == 'E') {

    //                 if ($deleted_liabilityid) {
    //                     $deletedliabilitydel  = explode(",", $deleted_liabilityid);
    //                     FieldAuditModel::deleteLiability($deletedliabilitydel, $session_userid);
    //                 }
    //             } else {

    //                 $activestatus   =   $request->input('activestatus');
    //                 // return $activestatus;
    //             }

    //             FieldAuditModel::insertupdateLiability($liabilitydel, $notype, $name, $gpfno, $designation, $amount, $processcode, $auditslipid, $session_userid, $activestatus);
    //         }

    //         if ($request->input('finaliseflag') === 'Y') {
    //             $session_usertypecode = $chargeData->usertypecode ?? null;
    //             $session_userchargeid = $chargeData->userchargeid ?? null;


    //             $historyData = [
    //                 'auditslipid' => $auditslipid,
    //                 'auditscheduleid' => $request->input('auditscheduleid'),
    //                 'schteammemberid' => $request->input('schteammemberid'),
    //                 'auditplanid' => $request->input('auditplanid'),
    //                 'mainobjectionid' => $request->input('majorobjectioncode'),
    //                 'subobjectionid' => $request->input('minorobjectioncode'),
    //                 'tempslipnumber' => $auditslipnumber,
    //                 'severityid' => $request->input('severityid'),
    //                 'liability' => $request->input('liability'),
    //                 'slipdetails' => $request->input('slipdetails'),
    //                 'remarks' => $content,
    //                 'forwardedby' => $session_userid,
    //                 'forwardedbyusertypecode' => $session_usertypecode,
    //                 'transstatus' => 'A',
    //                 'forwardedon' => View::shared('get_nowtime'),
 	// 	    'schemastatus' => $request->input('scheme'),
    //                 'irregularitiescode' => $request->input('serious'),
    //                 'irregularitiescatcode' => $request->input('category'),
    //                 'irregularitiessubcatcode' => $request->input('subcategory'),
    //             ];


    //             if ($rejoinderstatus ==  'Y')    $historyData['rejoinderstatus']    =   'Y';
    //             if (($rejoindercycle > 0))    $historyData['rejoindercycle']    =   $rejoindercycle;

 	// 	if (($request->input('scheme') == 'Y')) {
    //                 $historyData['auditeeschemecode']    =   $request->input('schemename');
    //             }


    //             //print_r($historyData);




    //             $updateTransauditData = [
    //                 'updatedby' => $session_userid,
    //                 'updatedbyusertypecode' => $session_usertypecode,
    //                 'updatedon' => View::shared('get_nowtime'),
    //             ];


    //             if ($teamhead == 'N') {
    //                 $forwardto = $request->input('teamheadid');
    //                 $historyData['forwardedto'] =  $forwardto;
    //                 $historyData['forwardedtousertypecode'] =  'A';
    //                 if ($actionfor == 'fresh') {
    //                     $updateTransauditData['processcode'] =  'T';
    //                     $processcode_slipfileupload =   'T';
    //                     $historyData['processcode']   =  'T';
    //                 } else {
    //                     $updateTransauditData['processcode']  = $processcode;
    //                     $processcode_slipfileupload =  $processcode;
    //                     $historyData['processcode']   =  $processcode;
    //                 }

    //                 $updateTransauditData['forwardedto'] =  $forwardto;
    //                 $updateTransauditData['forwardedtousertypecode'] =  'A';
    //                 $message    =   'Audit slip Details Forward to Team Head successfully.';
    //             } else {
    //                 $instid = $request->input('instid');
    //                 $forwardto = FieldAuditModel::fetchdata_auditeeuserid($instid);
    //                 $updateTransauditData['remarks'] =   null;;

    //                 if (($actionfor == 'fresh') || ($actionfor == 'rejoinder')) {
    //                     $updateTransauditData['processcode'] =  'F';
    //                     $processcode_slipfileupload =   'F';
    //                     $updateTransauditData['forwardedto'] =  $forwardto[0];
    //                     $updateTransauditData['forwardedtousertypecode'] =  'I';
    //                     $historyData['forwardedtousertypecode'] =  'I';
    //                     $historyData['forwardedto'] =  $forwardto[0];
    //                     $historyData['processcode']   =  'F';

    //                     $message    =   'Audit Slip forwarded to Auditee successfully.';
    //                 } else {
    //                     $updateTransauditData['processcode']  = $processcode;
    //                     $processcode_slipfileupload =  $processcode;
    //                     $updateTransauditData['forwardedto'] =  null;
    //                     $historyData['processcode']   =  $processcode;
    //                     $updateTransauditData['forwardedtousertypecode'] =  null;
    //                     $message    =   'Audit Slip Completed successfully.';
    //                 }

    //                 // FieldAuditModel::insertupdateLiability($liabilitydel,$notype,$name,$gpfno,$designation,$amount,$processcode,$auditslipid,$session_userid);

    //             }

    //             // echo $processcode_slipfileupload;
    //             // echo $processcode;
    //             // print_r($historyData);
    //             // print_r($updateTransauditData);




    //             if ($forwardto) {

    //                 $transactionResult = FieldAuditModel::insert_historytransactiondel($historyData, $auditslipid);

    //                 if ($transactionResult) {

    //                     $updateSlip = FieldAuditModel::update_auditsliptable($updateTransauditData, $auditslipid);
    //                     if ($updateSlip) {

    //                         FieldAuditModel::updateslipfileupload($processcode_slipfileupload, $session_userid, $processcode, $auditslipid, $session_usertypecode, $rejoinderstatus, $rejoindercycle);

    //                         // DB::commit();
    //                         return response()->json(['success' => true, 'message' => $message, 'data' => array('slid' => Crypt::encryptString($auditslipid), 'auditslipnumber' => $auditslipnumber)]);
    //                     } else {
    //                         throw new \Exception("Failed to update the auditslip table.");
    //                     }
    //                 }
    //             } else {
    //                 return response()->json(['success' => true, 'message' => 'No User Found', 'data' => $auditslipid]);
    //             }
    //         } else {
    //             return response()->json(['success' => true, 'message' => 'Audit Slip saved successfully.', 'data' => array('slid' => Crypt::encryptString($auditslipid), 'auditslipnumber' => $auditslipnumber)]);
    //         }
    //     } catch (\Exception $e) {
    //         // Rollback the transaction on failure
    //         // DB::rollBack();
    //         return response()->json(['error' => $e->getMessage()], 400);
    //     }
    // }

        public function audislip_insert(Request $request, $userId = null)
    {

        $userdel = session('user');
            $chargeData = session('charge');

            $sessionDeptcode = $chargeData->deptcode ?? null;

            $session_userid = $userdel->userid ?? null;
            if (!$session_userid) {
                return response()->json(['error' => 'User session is invalid.'], 400);
            }

            $formsessionuserid = Crypt::decryptString($request->ens);

           if($session_userid != $formsessionuserid )
            return response()->json(['success' => false, 'message' => 'Please refresh the page maintain one login at a time'],402); 

        $auditscheduleid = Crypt::decryptString($request->auditscheduleid);

        $request->merge(['auditscheduleid' => $auditscheduleid]);

        $action = $request->input('action');

        $auditslipid = ($action == 'update' && $request->auditslipid) ? Crypt::decryptString($request->auditslipid) : null;

        if($action == 'update')
        {
            $scheduledel = FieldAuditModel::checkscheduleid($auditscheduleid,$auditslipid);

            if($scheduledel == 'false')
                return response()->json(['success' => false, 'message' => 'Wrongly mapped with institution.pls Contact administator'],402); 
        }


       // $action = $request->input('action');
        $liabilitydel   =    $request->input('liability');
       

        if ( $liabilitydel === 'Y') {

            $request->validate([
                'name'            => 'required|array',
                'name.*'          => ['required', 'max:50', 'regex:/^[\p{Tamil}A-Za-z\s]+$/u'],

                'gpfno'           => 'required|array',
                'gpfno.*'         => ['required',  'max:20' ,'regex:/^\d+$/'],

                'amount'          => 'required|array',
                'amount.*'        => ['required', 'numeric', 'max:999999999'],

                'designation'     => 'required|array',
                'designation.*'   => ['required', 'max:50', 'regex:/^[\p{Tamil}A-Za-z\s]+$/u'],

                'notype'          => 'required|array',
                'notype.*'        => ['required', 'max:20', 'regex:/^\d+$/'],  // Replace with your allowed types

                'liabilityid'     => 'required|array',
                'liabilityid.*'   => ['nullable', 'integer'],
            ],
[
        'name.required'            => 'The name field is required.',
        'name.*.required'          => 'Liability name is required.',
        'name.*.max'               => 'Liability name must not exceed 50 characters.',
        'name.*.regex'             => 'Liability name must contain only letters and spaces.',

        'gpfno.required'           => 'The GPF number field is required.',
        'gpfno.*.required'         => 'Liability GPF number is required.',
        'gpfno.*.max'              => 'Liability GPF number must not exceed 20 digits.',
        'gpfno.*.regex'            => 'Liability GPF number must be numeric.',

        'amount.required'          => 'The amount field is required.',
        'amount.*.required'        => 'Liability amount is required.',
        'amount.*.numeric'         => 'Liability amount must be a valid number.',
        'amount.*.max'             => 'EacLiabilityh amount must not exceed 999999.',

        'designation.required'     => 'Liability designation field is required.',
        'designation.*.required'   => 'Liability designation is required.',
        'designation.*.max'        => 'Liability designation must not exceed 50 characters.',

        'notype.required'          => 'Liability Number type field is required.',
        'notype.*.required'        => 'Liability Number type is required.',
        'notype.*.max'             => 'Liability Number type must not exceed 20 characters.',
        'notype.*.regex'           => 'Liability Number type must be numeric.',

        'liabilityid.required'     => 'The liability ID field is required.',
        'liabilityid.*.integer'    => 'Each liability ID must be an integer.',
    ]);

            $notype         =   $request->input('notype');
            $name           =   $request->input('name');
            $gpfno          =   $request->input('gpfno');
            $amount         =   $request->input('amount');
            $designation    =   $request->input('designation');
            $liabilityid    =   $request->input('liabilityid');
            $liabilitydel   =   $request->input('liabilityid');
            $count_name     = count($name);
            $deleted_liabilityid   =   $request->input('deleted_liabilityid');
        }


        if (($request->input('scheme') == 'Y')) 
        {
            $schemename   =   $request->input('schemename');
        }

        $teamhead               =    $request->input('teamhead');
        $auditscheduleid        =    $request->input('auditscheduleid');
        $rejoinderstatus        =    $request->input('rejoinderstatus');
        $rejoindercycle         =    $request->input('rejoindercount');
        $slipcreatedby          =    $request->input('slipcreatedby');
        $actionfor              =    $request->input('actionfor');
        $rejoindersuggestion    =    $request->input('rejoindersuggestion');

      //  $auditslipid = ($action == 'update' && $request->auditslipid) ? Crypt::decryptString($request->auditslipid) : null;

        $fileupload = $request->file('fileupload');
        $destinationPath = '';

       

        $deactive_fileuploadids = $request->input('deactive_fileid') ? explode(',', $request->input('deactive_fileid')) : [];
        $active_fileuploadids = $request->input('active_fileid') ? explode(',', $request->input('active_fileid')) : [];

        // Deactivate Files
        if (!empty($deactive_fileuploadids)) {
            $this->fileUploadService->deactive_uploadefile($auditslipid, $deactive_fileuploadids);
        }

        $fileUploadId = null;

        if ((($action === 'insert') || ($action === 'update')) && ($request->hasFile('fileupload'))) 
        {
            $uploadResult = $this->fileUploadService->slipMultipleFileUpload(
                $fileupload,
                $destinationPath,
                $auditslipid,
                $active_fileuploadids,
                $deactive_fileuploadids,
                '',
                $auditscheduleid
            );

            if (is_array($uploadResult) && isset($uploadResult['error'])) {
                return response()->json(['errors' => $uploadResult['error']], 400);
            } elseif ($uploadResult instanceof \Illuminate\Http\JsonResponse) {
                $fileUploadId = $uploadResult->getData(true)['uploaded_files'];
            }
        }

        $request->validate([
            'majorobjectioncode'    => ['required_if:actionfor,fresh', 'string', 'regex:/^\d+$/'],
            'minorobjectioncode'    => ['required_if:actionfor,fresh', 'regex:/^\d+$/'],
            'amount_involved'       => 'nullable|regex:/^\d{1,10}(\.\d{1,2})?$/',
            'severityid'            => 'required|alpha|max:1',
            'liability'             => 'required|alpha|max:1',
            'slipdetails'           => 'required|string|max:500|min:10',
            'scheme'                => 'required|alpha|max:1',
            'schemename' => [
                'nullable',
                'required_if:scheme,Y',
                'string',
                'regex:/^\d+$/',
            ],
            'serious'               => 'required|string|max:2',
            'category'              => 'required|string|max:2',
            'subcategory'           => 'required|string|max:2',
            'remarks'               => 'required|string|min:20',
            'auditscheduleid'       => 'required|string|regex:/^\d+$/',
        ]);

        $content = json_encode(['content' => $request->input('remarks')]);

        $data = [
            //'auditscheduleid'       => $request->input('auditscheduleid'),
            //'schteammemberid'       => $request->input('schteammemberid'),
            //'auditplanid'           => $request->input('auditplanid'),
            'mainobjectionid'       => $request->input('majorobjectioncode'),
            'subobjectionid'        => $request->input('minorobjectioncode'),
            'tempslipnumber'        => $request->input('currentslipnumber'),
            'severitycode'          => $request->input('severityid'),
            'liability'             => $request->input('liability'),
            'schemastatus'          => $request->input('scheme'),
            'auditeeschemecode'     => $request->input('schemename'),
            'irregularitiescode'    => $request->input('serious'),
            'irregularitiescatcode' => $request->input('category'),
            'irregularitiessubcatcode'  => $request->input('subcategory'),
            'slipdetails'               => $request->input('slipdetails'),
            'remarks'                   => $content,
            'statusflag'                => 'Y',
            
        ];
	if($action == 'insert')
        {
            $data['auditscheduleid'] = $request->input('auditscheduleid');
            $data['auditplanid'] = $request->input('auditplanid');
            $data['schteammemberid'] = $request->input('schteammemberid');

        }

        if ($request->input('amount_involved')) {
            $data['amtinvolved'] = $request->input('amount_involved');
        } else    $data['amtinvolved']    =   null;

        if ($action === 'insert') 
        {
            $processcode    =    'E';
            $data['processcode'] = 'E';
            $data['createdon'] = View::shared('get_nowtime');
            $data['createdby'] = $session_userid;

            $data['updatedon'] = View::shared('get_nowtime');
            $data['updatedby'] = $session_userid;
        } 
        elseif ($action === 'update') 
        {
            if (($slipcreatedby != $session_userid) &&  $teamhead == 'Y' && $actionfor == 'fresh') {
                $processcode    =    'T';
            } elseif (($slipcreatedby == $session_userid) && $actionfor == 'fresh') {
                $processcode    =    'E';
            } elseif (($actionfor == 'memeberrejoinder')) {
                $processcode    =    'R';
                if ($rejoindersuggestion ==  'Y')
                    $rejoinderstatus    =  'R';
                $data['rejoinderstatus'] =  $rejoinderstatus;
            } elseif (($actionfor == 'drop')) {
                $processcode    =    'A';
            } elseif (($actionfor == 'converttopara')) {
                $processcode    =    'X';
            } elseif (($actionfor == 'rejoinder')) {
                $processcode    =    'F';
                $data['rejoinderstatus'] =  'Y';
                $rejoinderstatus    =   'Y';
                if ($rejoindercycle ==  '') $rejoindercycle = 0;
                $rejoindercycle =   $rejoindercycle +   1;
                $data['rejoindercycle'] =  $rejoindercycle;
            }

            $data['updatedon'] = View::shared('get_nowtime');
            $data['updatedby'] = $session_userid;
        }
        else 
        {
            return response()->json(['error' => true, 'message' => 'No Action Found', 'data' => '']);
        }

        try 
        {
           DB::beginTransaction();
            if($session_userid)
            {
                $auditslipdel = FieldAuditModel::createIfNotExistsOrUpdate($data, $auditslipid, $auditscheduleid, $sessionDeptcode);
                $auditslipnumber = $auditslipdel['slipnumber'];
                $auditslipid = $auditslipdel['auditslipid'];

                if ($fileUploadId) {
                    $this->fileUploadService->insert_slipfileupload($auditslipid, $fileUploadId, $rejoinderstatus, $rejoindercycle, $processcode);
                }

                if (($request->input('liability') == 'Y')) {
                    $activestatus = '';

                    if ($processcode == 'E' && $deleted_liabilityid) 
                    {
                        $deletedliabilitydel = explode(",", $deleted_liabilityid);
                        FieldAuditModel::deleteLiability($deletedliabilitydel, $session_userid);
                    } else {
                        $activestatus = $request->input('activestatus');
                    }

                    FieldAuditModel::insertupdateLiability($liabilitydel, $notype, $name, $gpfno, $designation, $amount, $processcode, $auditslipid, $session_userid, $activestatus);
                }

                if ($request->input('finaliseflag') === 'Y') 
                {
                    $session_usertypecode = $chargeData->usertypecode ?? null;
                    $session_userchargeid = $chargeData->userchargeid ?? null;

                    $historyData = [
                        'auditslipid' => $auditslipid,
                        'auditscheduleid' => $request->input('auditscheduleid'),
                        'schteammemberid' => $request->input('schteammemberid'),
                        'auditplanid' => $request->input('auditplanid'),
                        'mainobjectionid' => $request->input('majorobjectioncode'),
                        'subobjectionid' => $request->input('minorobjectioncode'),
                        'tempslipnumber' => $auditslipnumber,
                        'severityid' => $request->input('severityid'),
                        'liability' => $request->input('liability'),
                        'slipdetails' => $request->input('slipdetails'),
                        'remarks' => $content,
                        'forwardedby' => $session_userid,
                        'forwardedbyusertypecode' => $session_usertypecode,
                        'transstatus' => 'A',
                        'forwardedon' => View::shared('get_nowtime'),
                        'schemastatus' => $request->input('scheme'),
                        'irregularitiescode' => $request->input('serious'),
                        'irregularitiescatcode' => $request->input('category'),
                        'irregularitiessubcatcode' => $request->input('subcategory'),
                    ];

                    if ($rejoinderstatus == 'Y') {
                        $historyData['rejoinderstatus'] = 'Y';
                    }

                    if (($rejoindercycle > 0)) {
                        $historyData['rejoindercycle'] = $rejoindercycle;
                    }

                    if (($request->input('scheme') == 'Y')) {
                        $historyData['auditeeschemecode'] = $request->input('schemename');
                    }

                    $updateTransauditData = [
                        'updatedby' => $session_userid,
                        'updatedbyusertypecode' => $session_usertypecode,
                        'updatedon' => View::shared('get_nowtime'),
                    ];

                    if ($teamhead == 'N') {
                        $forwardto = $request->input('teamheadid');
                        $historyData['forwardedto'] = $forwardto;
                        $historyData['forwardedtousertypecode'] = 'A';
                        $historyData['processcode'] = ($actionfor == 'fresh') ? 'T' : $processcode;

                        $updateTransauditData['forwardedto'] = $forwardto;
                        $updateTransauditData['forwardedtousertypecode'] = 'A';
                        $updateTransauditData['processcode'] = $historyData['processcode'];

                        $processcode_slipfileupload = $historyData['processcode'];
                        $message = 'Audit slip Details Forward to Team Head successfully.';
                    } else {
                        $instid = $request->input('instid');
                        $forwardto = FieldAuditModel::fetchdata_auditeeuserid($instid);
                        $forwardtoUser = $forwardto[0] ?? null;

                        if (($actionfor == 'fresh') || ($actionfor == 'rejoinder')) {
                            $historyData['forwardedto'] = $forwardtoUser;
                            $historyData['forwardedtousertypecode'] = 'I';
                            $historyData['processcode'] = 'F';

                            $updateTransauditData['forwardedto'] = $forwardtoUser;
                            $updateTransauditData['forwardedtousertypecode'] = 'I';
                            $updateTransauditData['processcode'] = 'F';

                            $processcode_slipfileupload = 'F';
                            $message = 'Audit Slip forwarded to Auditee successfully.';
                        } else {
                            $historyData['processcode'] = $processcode;
                            $updateTransauditData['processcode'] = $processcode;
                            $processcode_slipfileupload = $processcode;
                            $message = 'Audit Slip Completed successfully.';
                        }
                    }

                    if (isset($forwardto)) {
                        $transactionResult = FieldAuditModel::insert_historytransactiondel($historyData, $auditslipid);

                        if ($transactionResult) {
                            $updateSlip = FieldAuditModel::update_auditsliptable($updateTransauditData, $auditslipid);

                            if ($updateSlip) {
                                FieldAuditModel::updateslipfileupload($processcode_slipfileupload, $session_userid, $processcode, $auditslipid, $session_usertypecode, $rejoinderstatus, $rejoindercycle);

                                DB::commit(); // ✅ COMMIT TRANSACTION
                                return response()->json([
                                    'success' => true,
                                    'message' => $message,
                                    'data' => ['slid' => Crypt::encryptString($auditslipid), 'auditslipnumber' => $auditslipnumber]
                                ]);
                            } else {
                                 return response()->json(['success' => false, 'message' => 'Failed to update auditslip table.', 'data' => $auditslipid]);
                            }
                        }
                        else {
                                 return response()->json(['success' => false, 'message' => 'Failed to insert history auditslip table.', 'data' => $auditslipid]);
                            }
                    } else {
                     DB::commit(); // ✅ COMMIT TRANSACTION
                        return response()->json(['success' => false, 'message' => 'No User Found', 'data' => $auditslipid]);
                    }
                } else {
                    DB::commit(); // ✅ COMMIT TRANSACTION
                    return response()->json([
                        'success' => true,
                        'message' => 'Audit Slip saved successfully.',
                        'data' => ['slid' => Crypt::encryptString($auditslipid), 'auditslipnumber' => $auditslipnumber]
                    ]);
                }
            }
            else
            {
                return response()->json(['error' => true, 'message' => 'No Session user not Found', 'data' => '']);
            }
            
           
        } catch (\Exception $e) {
            DB::rollBack(); // ❌ ROLLBACK ON ERROR
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }




    public function getsubobjection(Request $request)
    {
        $mainobjectioncode  =   $request->input('mainobjectioncode');
        $subobjectiondel    =   FieldAuditModel::getsubobjection($mainobjectioncode);
        // Fetch user data based on deptuserid
        // $user = UserModel::where('deptuserid', $deptuserid)->first(); // Adjust query as needed

        if ($subobjectiondel) {
            return response()->json(['success' => true, 'data' => $subobjectiondel]);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }



    ////////////////////////////////Work Allocation/////////////////////////////////////////////

    public function insert_workAllocation(Request $request)
    {


        $request->validate([
            'finaliseflag'         => 'required', // Ensures only digits, allows leading zeros
            'auditscheduleId'      =>  'required',         // Only alphabets (no numbers or symbols)
            'team_mem'             =>  'required',             // Alphanumeric (letters and numbers)
            'majorwa'              =>  'required',
        ]);

        if ($request->work_action == 'update') {
            $workallocationid     = Crypt::decryptString($request->workallocationid);
            $request->merge(['workallocationid	' => $workallocationid]);
        }
        $data = [
            'auditscheduleid' => $request->input('auditscheduleId'),
            'schteammemberid' => $request->input('team_mem'),
            'majorwa'         => $request->input('majorwa'),
            'statusflag' => $request->input('finaliseflag'),
            'subtypecode' => $request->input('minorwa'),

        ];
        $minortypeID = $request->input('minorwa');
        if ($request->work_action == 'update') {

            $existingwork = TransWorkAllocationModel::fetchexistingwork($data);
            // foreach ($minortypeID as $subtypecode) {
            $auditscheduleid = trim($request->input('auditscheduleId'));

            if ($existingwork) {

                $existingWorkIds = $existingwork
                    ->filter(function ($work) use ($auditscheduleid) { // Pass $auditscheduleid into the callback
                        return  $work->auditscheduleid == $auditscheduleid && $work->statusflag === 'Y';
                    })
                    ->pluck('subtypecode')
                    ->toArray();

                $minortypeID  = $request->input('minorwa');
                // print_r($existingWorkIds);
                // print_r($minortypeID);
                $existingWorkIdsEqualToMinortypecode = empty(array_diff($minortypeID, $existingWorkIds)) && empty(array_diff($existingWorkIds, $minortypeID));
                $newIdsExist = array_diff($minortypeID, $existingWorkIds);
                $idsToRemove = array_diff($existingWorkIds, $minortypeID);
                // print_r($newIdsExist);
                if (!empty($newIdsExist)) {
                    foreach ($minortypeID as $subtypecodeAdd) {
                        // Check if the subtypecode exists in minortype
                        if (in_array($subtypecodeAdd, $newIdsExist)) {
                            // echo 'if';
                            // print_r($subtypecodeAdd);
                            TransWorkAllocationModel::create([
                                'auditscheduleid' => $request->input('auditscheduleId'),
                                'schteammemberid' => $request->input('team_mem'),
                                'statusflag' => $request->input('finaliseflag'),
                                'subtypecode' => $subtypecodeAdd,
                                'createdon' => View::shared('get_nowtime'),
                                'updatedon' => View::shared('get_nowtime'),
                            ]);
                        } else {
                            // echo 'else';
                            // print_r($subtypecodeAdd);
                            // Add the new subtypecode that is not in minortype
                            TransWorkAllocationModel::updatework($data, $subtypecodeAdd, $request->input('auditscheduleId'));
                        }
                    }
                }
                if (!empty($idsToRemove)) {
                    foreach ($minortypeID as $subtypecodeAdd) {
                        // Check if the current minor type is in the removal list
                        if (in_array($subtypecodeAdd, $idsToRemove)) {
                            // If it's in the removal list, update it as removed
                            // TransWorkAllocationModel::where('subtypecode', $subtypecodeAdd)
                            //     ->where('auditscheduleid', $request->input('auditscheduleId'))
                            //     ->where('schteammemberid', $request->input('team_mem'))
                            //     ->update([
                            //         'statusflag' => 'N', // Mark as removed
                            //         'updatedon' => View::shared('get_nowtime'), // Update timestamp
                            //     ]);
                        } else {
                            // If not in the removal list, keep it active
                            TransWorkAllocationModel::where('subtypecode', $subtypecodeAdd)
                                ->where('auditscheduleid', $request->input('auditscheduleId'))
                                ->where('schteammemberid', $request->input('team_mem'))
                                ->update([
                                    'statusflag' => $request->input('finaliseflag'), // Keep it active or finalized
                                    'updatedon' => View::shared('get_nowtime'), // Update timestamp
                                ]);
                        }
                    }

                    // Loop through idsToRemove to find any IDs not in minortypeID
                    foreach ($idsToRemove as $subtypecodeToRemove) {
                        if (!in_array($subtypecodeToRemove, $minortypeID)) {
                            // Update the records for IDs that are not in the minor type
                            TransWorkAllocationModel::where('subtypecode', $subtypecodeToRemove)
                                ->where('auditscheduleid', $request->input('auditscheduleId'))
                                ->where('schteammemberid', $request->input('team_mem'))
                                ->update([
                                    'statusflag' => 'N', // Mark as removed
                                    'updatedon' => View::shared('get_nowtime'), // Update timestamp
                                ]);
                        }
                    }
                }

                // if (!empty($idsToRemove)) {
                //     foreach ($idsToRemove as $subtypecodeAdd) {
                //         TransWorkAllocationModel::whereIn('subtypecode', $idsToRemove)
                //             ->where('auditscheduleid', $auditscheduleid)
                //             ->where('schteammemberid', $request->input('team_mem'))
                //             // ->where('subtypecode', $subtypecode)
                //             ->update(['statusflag' => 'N']);

                //         // other fields as necessary

                //     }
                // }
                if (empty($newIdsExist) && empty($idsToRemove)) {
                    // print_r($minortypeID);
                    foreach ($minortypeID as $subtypecode) {
                        TransWorkAllocationModel::updatework($data, $subtypecode, $request->input('auditscheduleId'));
                    }
                    // foreach ($idsToRemove as $subtypecodeAdd) {
                    //     TransWorkAllocationModel::updatework($data, $subtypecode, $request->input('auditscheduleId'));
                    // }
                }
            }
            // }
            // $updatework = TransWorkAllocationModel::updatework($data, $subtypecode, $request->input('auditscheduleId'));
            return response()->json(['success' => true, 'message' => 'Work Allocation Data Saved Successfully']);


            // return $workallocationid;
        } else {
            foreach ($minortypeID as $subtypecode) {
                $checkforsubtype = TransWorkAllocationModel::checkforsubtype($data, $subtypecode);
                if ($checkforsubtype) {
                    return  $checkforsubtype;
                }
            }
            foreach ($minortypeID as $subtypecode) {

                TransWorkAllocationModel::create([
                    'auditscheduleid'    =>  $request->input('auditscheduleId'),
                    'schteammemberid'    => $request->input('team_mem'),
                    'statusflag'         =>  $request->input('finaliseflag'),
                    'subtypecode'        => $subtypecode,
                    'createdon'          => View::shared('get_nowtime'),
                    'updatedon'          => View::shared('get_nowtime'),
                ]);
            }


            // print_r($request->input('team_name'),);
        }
        return response()->json(['success' => true, 'message' => 'Work Allocation Data Saved Successfully']);
    }

    public function fetchAllWorkData(Request $request)
    {
        $TeamHead = $request['teamhead'];
        $userid = $request['userid'];
        $auditscheduleid = $request->auditscheduleid;

        $randomizesWA = AuditManagementModel::checkrandomizedWA($auditscheduleid);
        $randomizesWA_status = $randomizesWA[0]->workallocationflag;

        $workallDetail = TransWorkAllocationModel::fetchworkdetail($auditscheduleid, $TeamHead, $userid);
        foreach ($workallDetail as $item) {
            $item->encrypted_schteammemberid = Crypt::encryptString($item->schteammemberid);
            $item->encrypted_workallocationid = Crypt::encryptString($item->workallocationid);
        }

        return response()->json(['data' => $workallDetail, 'workallc_status' => $randomizesWA_status]);
    }

    public function fetch_singleworkdet(Request $request)
    {
        $schteammemberid = Crypt::decryptString($request->schteammemberid);
        $auditscheduleid     = $request->auditscheduleid;
        $major_id = $request->major_id;
        $workallDetail = TransWorkAllocationModel::fetchSingleworkdetail($schteammemberid, $auditscheduleid, $major_id);
        foreach ($workallDetail as $item) {
            $item->encrypted_workallocationid = Crypt::encryptString($item->workallocationid);
        }


        if ($workallDetail) {
            return response()->json(['success' => true, 'data' => $workallDetail]);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }


    public function fetchminorworkdel(Request $request)
    {

        $majorworkid = $request->majorworkid;
        $minorworkdel = DB::table('audit.mst_subworkallocationtype')

            ->where('statusflag', 'Y')
            ->where('majorworkallocationtypeid',  $majorworkid)
            ->select(
                'mst_subworkallocationtype.subworkallocationtypeename',
                'mst_subworkallocationtype.subworkallocationtypeid',
            )
            ->orderBy('mst_subworkallocationtype.orderid', 'asc')
            ->get();
        return response()->json($minorworkdel);
    }


    ////////////////////////////////Work Allocation/////////////////////////////////////////////


    //////////////////////////////// Pending Parra /////////////////////////////////////////////


    public function audittrans_dropdown($encrypted_auditscheduleid)
    {
        if ($encrypted_auditscheduleid) {
            $auditscheduleid = Crypt::decryptString($encrypted_auditscheduleid);
        }
        // Echo the ID to verify it's being passed correctly
        // Access session data
        $chargeData = session('charge');
        $session_deptcode = $chargeData->deptcode; // Accessing the department code from the session
        $session_usertypecode = $chargeData->usertypecode;
        $userData = session('user');
        $session_userid = $userData->userid;


        // exit;
        $inst_details = DB::table('audit.inst_schteammember as sm')
            ->join('audit.inst_auditschedule as is', 'is.auditscheduleid', '=', 'sm.auditscheduleid')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'is.auditplanid')
            ->join('audit.mst_institution as in', 'in.instid', '=', 'ap.instid')
            ->join('audit.mst_auditeeins_category as incat', 'incat.catcode', '=', 'in.catcode')
            ->join('audit.mst_typeofaudit as ta', 'ta.typeofauditcode', '=', 'ap.typeofauditcode')
            ->join('audit.mst_dept as dept', 'in.deptcode', '=', 'dept.deptcode')
            //  ->join('audit.mst_auditperiod as d', 'd.auditperiodid', '=', 'ap.auditperiodid')
            ->join('audit.yearcode_mapping as yrmap', 'yrmap.auditplanid', '=', 'ap.auditplanid')
            ->join(
                'audit.mst_auditperiod as d',
                DB::raw('CAST(yrmap.yearselected AS INTEGER)'),
                '=',
                'd.auditperiodid'
            )
            ->where('userid', $session_userid)
            ->where('is.auditscheduleid', $auditscheduleid)
            // Apply STRING_AGG to aggregate years

            ->select(
                'is.auditscheduleid',
                'sm.auditscheduleid',
                'sm.auditteamhead',
                'is.auditplanid',
                'is.fromdate',
                'is.todate',
                'ap.instid',
                'dept.deptelname',
                'in.instename',
                'incat.catename',
                'in.mandays',
                'sm.auditteamhead',
                'ta.typeofauditename',
                'sm.schteammemberid',
                DB::raw('STRING_AGG(DISTINCT d.fromyear || \'-\' || d.toyear, \', \') as yearname')
            )
            ->groupby('is.auditscheduleid', 'sm.auditscheduleid', 'sm.auditteamhead', 'is.auditplanid', 'is.fromdate', 'is.todate', 'ap.instid', 'dept.deptelname', 'in.instename', 'incat.catename', 'in.mandays', 'sm.auditteamhead', 'ta.typeofauditename', 'sm.schteammemberid')

            ->get();

        return view('audit.transauditslip', compact('inst_details'));


        // You can also add logic to handle the ID if needed
    }

    public function getpendingparadetails(Request $request)
    {
        $auditscheduleid = Crypt::decryptString($request->auditscheduleid);

        $quartercode = $request->quartercode;
        $slipsts = $request->slipsts;
        $filterapply = $request->filterapply;

        // Sanitize and validate input
        // $request->validate([
        //     'auditscheduleid' => 'required|integer'
        // ]);


        // $auditscheduleid = $request->auditscheduleid;

        // Fetch details
        $alldetails = FieldAuditModel::getpendingparadetails($auditscheduleid, $quartercode, $slipsts, $filterapply);
        $responseData = json_decode($alldetails->getContent(), true);

        foreach ($responseData['data'] as &$record) {
            $record['auditslipid'] = Crypt::encryptString($record['auditslipid']);
        }

        // Replace the original 'data' inside the response
        $responseData['data'] = $responseData['data'];

        $jsonencoded_response = $responseData;





        if ($responseData['totalslips'] > 0) {
            return response()->json(['success' => true, 'data' => $jsonencoded_response]);
        } else {
            return response()->json(['success' => true, 'message' => 'No auditslips found'], 200);
        }
    }

    public function pendingparra()
    {
        $sessionuserdel    =    session('user');
        $sessionuserid    =    $sessionuserdel->userid;

        $results = DB::table('audit.inst_schteammember as scm')
            ->join('audit.inst_auditschedule as sc', 'sc.auditscheduleid', '=', 'scm.auditscheduleid')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'sc.auditplanid')
            ->join('audit.mst_institution as mi', 'mi.instid', '=', 'ap.instid')
            ->where('auditeeresponse', 'A')
            ->where('scm.userid', '=', $sessionuserid)
            ->groupBy(
                'sc.auditscheduleid',
                'ap.auditplanid',
                'ap.instid',
                'mi.instename',
                'sc.fromdate',
                'sc.todate'
            )
            ->select(
                'sc.auditscheduleid',
                'sc.fromdate',
                'sc.todate',
                'ap.auditplanid',
                'ap.instid',
                'mi.instename'
            )
            ->get();
        foreach ($results as $all) {
            $all->encrypted_auditscheduleid = Crypt::encryptString($all->auditscheduleid);
            $all->formatted_fromdate = Carbon::createFromFormat('Y-m-d', $all->fromdate)->format('d-m-Y');
            $all->formatted_todate = Carbon::createFromFormat('Y-m-d', $all->todate)->format('d-m-Y');
        }

        return view('fieldaudit.pendingpara', compact('results'));
    }

    //////////////////////////////// Pending Parra /////////////////////////////////////////////

    public static function Supercheck_QuesAns(Request $request)
    {
        $Supercheck_QuesAns = [
            'auditscheduleid' => $request->auditscheduleid,
            'auditplanid' => $request->auditplanid,
            'quesno' => $request->quesno,
            'questiontype' => $request->questiontype,  // Insert the generated yearcodemapping_id here
            'answer_remarks' => $request->answer_remarks
        ];

        $supercheckquesinsert = FieldAuditModel::Supercheck_QuesAns($Supercheck_QuesAns);

        return response()->json(['success' => 'Superchecklist added successfully']);

    }

public function confirmationdiary(Request $request)
{
    DB::beginTransaction();

    try {
        $request->validate([
            'diaryflag' => 'required|string|in:Y,N',
            'auditscheduleid' => 'required'
        ]);

        $auditscheduleid = $request->auditscheduleid;
        $instid = $request->instid;
        $exitmeetdate = $request->exitmeetdate;
        $nextQuarterDate = $request->nextQuarterDate;

        $session = session('user');
        $userid = $session->userid;
        $now = View::shared('get_nowtime');

        // ✅ Check if any users haven't completed diary
        $pendingUsers = FieldAuditModel::getPendingUsers($auditscheduleid);

        if (!empty($pendingUsers)) {
            DB::rollBack(); // Roll back if users haven't completed diary
            return response()->json([
                'success' => false,
                'message' => 'not_all_statusflags_are_Y',
                'pending_userids' => $pendingUsers['user_en'],
                'user_list_en' => implode(', ', $pendingUsers['user_en']),
                'user_list_ta' => implode(', ', $pendingUsers['user_ta'])
            ], 400);
        }

        // ✅ Insert exit meeting entry
        FieldAuditModel::insertExitMeeting($auditscheduleid, $instid, $nextQuarterDate, $userid, $now);

        // ✅ Update diary flag
        FieldAuditModel::updateDiaryFlag($auditscheduleid, $request->diaryflag, $nextQuarterDate, $userid, $now);

        DB::commit(); // ✅ Commit all changes if everything succeeds

        return response()->json([
            'success' => true,
            'message' => 'diaryconfirm'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack(); // Roll back on validation errors
        return response()->json([
            'success' => false,
            'message' => 'validation_error',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        DB::rollBack(); // Roll back on general exception
        return response()->json([
            'success' => false,
            'message' => 'submission_failed',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function pendingusersfornotspillover(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'diaryflag' => 'required|string|in:Y,N',
                'auditscheduleid' => 'required'
            ]);

            $auditscheduleid = $request->auditscheduleid;

            $session = session('user');
            $userid = $session->userid;
            $now = View::shared('get_nowtime');

            $pendingUsers = FieldAuditModel::getPendingUsers($auditscheduleid);

            // print_r($request->diaryflag);
            // exit;
            if (!empty($pendingUsers)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'not_all_statusflags_are_Y',
                    'pending_userids' => $pendingUsers['user_en'],
                    'user_list_en' => implode(', ', $pendingUsers['user_en']),
                    'user_list_ta' => implode(', ', $pendingUsers['user_ta'])
                ], 400);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'diaryconfirm'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false
            ], 500);
        }
    }

}
