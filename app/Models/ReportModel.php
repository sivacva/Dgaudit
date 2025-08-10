<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class ReportModel extends Model
{
    use HasFactory;

    protected $connection = 'pgsql'; // PostgreSQL connection
    protected $table = BaseModel::REPORTCONTENTS_table;

    protected static $auditplanteammemberTable = BaseModel::TEAMMEMBER_Table;
    protected static $Instschedule_Table            =  BaseModel::INSTSCHEDULE_TABLE;
    protected static $InstscheduleMem_Table         =  BaseModel::INSTSCHEDULEMEM_TABLE;
    protected static $AuditPlan_Table               =  BaseModel::AUDITPLAN_TABLE;
    protected static $AuditPlanTeam_Table           =  BaseModel::AUDITPLANTEAM_TABLE;
    protected static $Institution_Table             =  BaseModel::INSTITUTION_TABLE;
    protected static $AuditeeUserDetail_Table       =  BaseModel::AUDITEEUSERDETAIL_TABLE;
    protected static $TypeofAudit_Table             =  BaseModel::TYPEOFAUDIT_TABLE;
    protected static $Dept_Table                    =  BaseModel::DEPT_TABLE;
    protected static $MstAuditeeInsCategory_Table   =  BaseModel::MSTAUDITEEINSCATEGORY_TABLE;
    protected static $AuditQuarter_Table            =  BaseModel::AUDITQUARTER_TABLE;
    protected static $UserChargeDetails_Table       =  BaseModel::USERCHARGEDETAIL_TABLE;
    protected static $UserDetails_Table             =  BaseModel::USERDETAIL_TABLE;
    protected static $Designation_Table             =  BaseModel::DESIGNATION_TABLE;
    protected static $MapYearcode_Table             =  BaseModel::MAPYEARCODE_TABLE;
    protected static $AuditPeriod_Table             =  BaseModel::AUDITPERIOD_TABLE;
    protected static $FileUpload_Table              =  BaseModel::FILEUPLOAD_TABLE;
    protected static $AccountParticulars_Table      =  BaseModel::ACCOUNTPARTICULARS_TABLE;
    protected static $TransaccountDetails_Table     =  BaseModel::TRANSACCOUNTDETAILS_TABLE;
    protected static $CallforRecordsAuditee_Table   =  BaseModel::CALLFORRECORDS_AUDITEE_TABLE;
    protected static $TransCallforRecords_Table     =  BaseModel::TRANSCALLFORRECORDS_TABLE;
    protected static $MapCallforRecords_Table       =  BaseModel::MAPCALLFORRECORDS_TABLE;
    protected static $ChargeDetails_Table           =  BaseModel::CHARGEDETAIL_TABLE;
    protected static $District_Table                =  BaseModel::DIST_Table;

    protected static $ProcessFlag_Table             =  BaseModel::PROCESSFLAG_TABLE;
    protected static $MajorObj_Table                =  BaseModel::MAINOBJ_TABLE;
    protected static $SubObj_Table                  =  BaseModel::SUBOBJ_TABLE;
    protected static $TransAuditSlip_Table          =  BaseModel::TRANSAUDITSLIP_TABLE;

    protected static $TransWorkAllocation_Table     =  BaseModel::TRANSWORKALLOCATION_TABLE;
    protected static $MapWorkAllocation_Table       =  BaseModel::MAPWORKALLOCATION_TABLE;
    protected static $MajWorkAllocation_Table       =  BaseModel::MAJWORKALLOCATION_TABLE;
    protected static $SlipHistroyDetails_Table      =  BaseModel::SLIPHISTORYDETAILS_TABLE;


    //protected $reportcontents_table = 'audit.report_contents'; // Table name

    // Primary Key
    protected $primaryKey = 'reportid';
    protected $keyType = 'int';
    public $incrementing = true; // Set to `false` if `reportid` is not auto-incrementing

    // Custom timestamps
    const CREATED_AT = 'createdon';
    const UPDATED_AT = null; // Set to null if you don’t have an `updated_at` column

    // Fillable Fields
    protected $fillable = [
        'report_type',
        'report_name',
        'report_contents',
        'statusflag'
    ];

    // Cast JSON field to array when retrieved
    protected $casts = [
        'report_contents' => 'array'
    ];

    public static function fetchpendingparas($sessionuserid)
    {
        return DB::table(self::$InstscheduleMem_Table . ' as scm')
            ->join(self::$Instschedule_Table . ' as sc', 'sc.auditscheduleid', '=', 'scm.auditscheduleid')
            ->join(self::$AuditPlan_Table . ' as ap', 'ap.auditplanid', '=', 'sc.auditplanid')
            ->join(self::$Institution_Table . ' as mi', 'mi.instid', '=', 'ap.instid')
            ->where('sc.auditeeresponse', 'A')
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
    }


    public static function getpendingparadetails($auditscheduleid, $quartercode, $slipsts, $filterapply, $quarter)
    {
        // Fetch the main query data
        $table = self::$TransAuditSlip_Table;
        $schteammem = self::$InstscheduleMem_Table;
        $userdetails = self::$UserDetails_Table;

        $query =  DB::table($table)
            ->join(self::$ProcessFlag_Table . ' as p', 'p.processcode', '=', $table . '.processcode')
            ->join(self::$MajorObj_Table . ' as m', 'm.mainobjectionid', '=', $table . '.mainobjectionid')
            ->leftJoin(self::$SubObj_Table . ' as s', 's.subobjectionid', '=', $table . '.subobjectionid')
            ->join(self::$AuditPlan_Table . ' as ap', 'ap.auditplanid', '=', $table . '.auditplanid')
            ->join(self::$AuditQuarter_Table . ' as maq', 'maq.auditquartercode', '=', 'ap.auditquartercode')
            ->join(self::$InstscheduleMem_Table . ' as schteam', 'schteam.schteammemberid', '=', $table . '.schteammemberid')
            ->leftJoin(self::$UserDetails_Table . ' as dud', 'dud.deptuserid', '=', $table . '.createdby')
            ->where($table . '.auditscheduleid', $auditscheduleid)
	    ->where('ap.auditquartercode', $quarter)
            ->select(
                'm.objectionename',
                'mainslipnumber',
                'amtinvolved',
                'slipdetails',
                'p.processelname',
                'p.processcode',
                'dud.username as auditorname',
                'liability',
                'auditslipid',
                'maq.auditquartercode',
                DB::raw("CASE WHEN $table.subobjectionid IS NOT NULL THEN s.subobjectionename
                                                        ELSE 'N/A'
                                                    END AS subobjectionename"), // Conditionally show subobjectionename or 'N/a'
                DB::raw("TO_CHAR($table.createdon, 'DD-MM-YYYY hh:MI AM') as createddate"),
                DB::raw("CASE WHEN rejoinderstatus = 'Y' THEN 'Yes' ELSE 'No' END AS rejoinderstatus"),
               DB::raw("(SELECT dud.username FROM $schteammem AS schteam
                                                JOIN $userdetails AS dud ON dud.deptuserid = schteam.userid
                                                WHERE schteam.auditscheduleid = $table.auditscheduleid
                                                AND schteam.auditteamhead = 'Y'    AND schteam.statusflag = 'Y' LIMIT 1) AS teamheadname")
            )
            ->orderBy($table . '.auditslipid', 'asc')
            ->distinct();

        // Apply filters conditionally
        if ($filterapply == true) {
            if ($slipsts != 'all') {
                if ($slipsts == 'P') {
                    $query->whereNotIn($table . '.processcode', ['A', 'X']);
                } else {
                    $query->where($table . '.processcode', $slipsts);
                }
            }

            if ($quartercode != 'all') {
                $query->where('maq.auditquartercode', $quartercode);
            }
        }

        // Execute the query to fetch data
        $query = $query->get();

        // Start building the count query
        $countQuery = DB::table($table)->join(self::$AuditPlan_Table . ' as ap', 'ap.auditplanid', '=', $table . '.auditplanid')
            ->join(self::$AuditQuarter_Table . ' as maq', 'maq.auditquartercode', '=', 'ap.auditquartercode')
            ->where($table . '.auditscheduleid', $auditscheduleid);

        // Apply filter if filterapply is true
        if ($filterapply == true) {
            if ($quartercode != 'all') {
                // Filter by quartercode if it's not 'all'
                $countQuery->where('maq.auditquartercode', $quartercode);
            }
        }

        // Add the selectRaw part for counting, with DISTINCT and GROUP BY to avoid duplicates
        $countQuery->selectRaw("
            COUNT(DISTINCT CASE WHEN $table.processcode IS NOT NULL THEN $table.auditslipid END) as totalslips,
            COUNT(DISTINCT CASE WHEN $table.processcode = 'A' THEN $table.auditslipid END) as droppedslips,
            COUNT(DISTINCT CASE WHEN $table.processcode = 'X' THEN $table.auditslipid END) as convertedslips,
            COUNT(DISTINCT CASE WHEN $table.processcode NOT IN ('A', 'X') THEN $table.auditslipid END) as pendingslips
        ")
            ->groupBy($table . '.auditscheduleid'); // Group by 'auditscheduleid' to avoid duplicates

        // Execute the query and get the first result
        $countQuery = $countQuery->first();
        $data = json_decode(json_encode($countQuery), true); // Convert stdClass to array

        // $data = json_decode($countQuery, true);


        // Access the values, ensuring they aren't negative
        $totalslips = isset($data['totalslips']) && $data['totalslips'] >= 0 ? $data['totalslips'] : 0;
        $droppedslips = isset($data['droppedslips']) && $data['droppedslips'] >= 0 ? $data['droppedslips'] : 0;
        $convertedslips = isset($data['convertedslips']) && $data['convertedslips'] >= 0 ? $data['convertedslips'] : 0;
        $pendingSlips = isset($data['pendingslips']) && $data['pendingslips'] >= 0 ? $data['pendingslips'] : 0;

        // Now, the values are guaranteed to be non-negative



        // Prepare the response array
        $response = [
            'totalslips' => $totalslips,
            'droppedslips' => $droppedslips,
            'convertedslips' => $convertedslips,
            'pendingSlips' => $pendingSlips,
            'data' => $query->toArray()
        ];

        // Return the response
        return response()->json($response);
    }

    public static function getSlipDetails($slipId)
    {
        $table = self::$TransAuditSlip_Table;
        // Simulate fetching slip details from the database (replace this with actual logic)
        $slipDetails = DB::table($table)
            ->join(self::$ProcessFlag_Table . ' as p', 'p.processcode', '=', $table . '.processcode')
            ->join(self::$MajorObj_Table . ' as m', 'm.mainobjectionid', '=', $table . '.mainobjectionid')
            ->leftjoin(self::$SubObj_Table . ' as s', 's.subobjectionid', '=', $table . '.subobjectionid')
            ->join(self::$AuditPlan_Table . ' as ap', 'ap.auditplanid', '=', $table . '.auditplanid')
            ->join(self::$AuditQuarter_Table . ' as maq', 'maq.auditquartercode', '=', 'ap.auditquartercode')
            ->join(self::$AuditeeUserDetail_Table . ' as auditee', 'ap.instid', '=', 'auditee.instid')
            ->join(self::$Institution_Table . '  as inst', 'ap.instid', '=', 'inst.instid')
            ->join(self::$InstscheduleMem_Table . ' as schteam', 'schteam.schteammemberid', '=', $table . '.schteammemberid')
            ->join(self::$UserDetails_Table . ' as dud', 'dud.deptuserid', '=', $table . '.createdby')
            ->join(self::$Designation_Table . ' as desig', 'desig.desigcode', '=', 'dud.desigcode')

            ->select(
                $table . '.auditscheduleid',
                'm.objectionename',
                DB::raw("CASE
                        WHEN $table.subobjectionid IS NOT NULL THEN s.subobjectionename
                        ELSE 'N/A'
                     END AS subobjectionename"),
                $table . '.mainslipnumber',
                $table . '.slipdetails',
                $table . '.amtinvolved',
                'dud.username as auditorname',
                'p.processelname',
                DB::raw("CASE WHEN $table.liability = 'Y' THEN 'Yes' ELSE 'No' END AS liability"), // Transform in query
                DB::raw("CASE WHEN $table.severitycode = 'M' THEN 'Medium'
                              WHEN $table.severitycode = 'H' THEN 'High'
                              WHEN $table.severitycode = 'L' THEN 'Low'
                              ELSE 'Unknown'
                        END AS severity"),
            )
            ->where($table . '.auditslipid', $slipId)
            ->first();

        if (!$slipDetails) {
            return response()->json(['status' => 'error', 'message' => 'Slip not found.'], 404);
        }

        $LiabilityDetails = DB::table('audit.liability as liability')
            ->where('liability.auditslipid', $slipId)
            ->get();


        // Get the current user
        /* $currentUser = auth()->user(); // Assuming you're using Laravel's authentication

        // Check if the current user is the same as the 'createdby' user and has the 'teamhead' flag set to 'Y'
        $isTeamMember = DB::table('audit.inst_schteammember')
        ->where('userid', $slipDetails->createdby) // Use the schteamid from the slip details
        ->where('auditteamhead', 'N') // Check for team head flag
        ->exists();*/


        // Get the auditscheduleid from slipDetails
        $auditScheduleId = $slipDetails->auditscheduleid;


        $InstscheduleMem_Table = self::$InstscheduleMem_Table;

        // Check if the current user is a team member with teamhead flag 'Y'
        $TeamHeadget = DB::table($InstscheduleMem_Table)
            ->join(self::$UserDetails_Table . ' as dud', 'dud.deptuserid', '=', $InstscheduleMem_Table . '.userid')
            ->select('dud.username as teamheadname')
            ->where($InstscheduleMem_Table . '.auditscheduleid', $auditScheduleId) // Use auditscheduleid for the team member check
            ->where($InstscheduleMem_Table . '.auditteamhead', 'Y') // Ensure it's a team head
            ->first();
        /*if ($isTeamMember) {
                // Logic if the user is the team head
                return response()->json([
                    'status' => 'success',
                    'data' => $slipDetails,
                    'is_team_member' => true,
                    'teamheadname' => $TeamHeadget->teamheadname
                ]);
            } else {*/
        return response()->json([
            'status' => 'success',
            'data' => $slipDetails,
            //'is_team_member' => false,
            'teamheadname' => $TeamHeadget->teamheadname,
            'liability' => $LiabilityDetails
        ]);
        // }

    }

    public static function getSlipDetailsHistory($slipId)
    {
        $table = self::$SlipHistroyDetails_Table;
        // Simulate fetching slip details from the database (replace this with actual logic)
        $slipDetails = DB::table($table)
            ->leftjoin(self::$ProcessFlag_Table . ' as p', 'p.processcode', '=', $table . '.processcode')
            ->leftjoin(self::$MajorObj_Table . ' as m', 'm.mainobjectionid', '=', $table . '.mainobjectionid')
            ->leftjoin(self::$SubObj_Table . ' as s', 's.subobjectionid', '=', $table . '.subobjectionid')
            ->leftjoin(self::$AuditPlan_Table . ' as ap', 'ap.auditplanid', '=', $table . '.auditplanid')
            //->leftjoin(self::$AuditQuarter_Table . ' as maq', 'maq.auditquartercode', '=', 'ap.auditquartercode')
            ->leftjoin(self::$AuditeeUserDetail_Table . ' as auditee', 'ap.instid', '=', 'auditee.instid')
            ->leftjoin(self::$Institution_Table . '  as inst', 'ap.instid', '=', 'inst.instid')
            ->leftjoin(self::$InstscheduleMem_Table . ' as schteam', 'schteam.schteammemberid', '=', $table . '.schteammemberid')
            ->select(
                $table . '.auditscheduleid',
                'm.objectionename',
                DB::raw("CASE

                            WHEN $table.subobjectionid IS NOT NULL THEN s.subobjectionename
                                    ELSE 'N/A'
                                END AS subobjectionename"),
                $table . '.mainslipnumber',
                $table . '.slipdetails',
                $table . '.amtinvolved',
                $table . '.remarks',
                $table . '.forwardedbyusertypecode',
                'p.processelname',
                DB::raw("CASE WHEN $table.liability = 'Y' THEN 'Yes' ELSE 'No' END AS liability"), // Transform in query
                DB::raw("CASE WHEN $table.severityid = 'M' THEN 'Medium'
                                        WHEN $table.severityid = 'H' THEN 'High'
                                        WHEN $table.severityid = 'L' THEN 'Low'
                                        ELSE 'Unknown'
                                    END AS severity"),
            )
            ->where($table . '.auditslipid', $slipId)
            ->orderBy($table.'.transhistoryid', 'asc') // Order by transhistoryid descending
            ->get();



        $LiabilityDetails = DB::table('audit.liability as liability')
            ->where('liability.auditslipid', $slipId)
            ->get();


        return response()->json([
            'status' => 'success',
            'data' => $slipDetails,
            'liability' => $LiabilityDetails
        ]);
    }


    public static function getSlipHistoryDetails($slipId)
    {
        $auditSlips = DB::table(self::$SlipHistroyDetails_Table . ' as hist')
            ->join(self::$TransAuditSlip_Table . ' as trans_auditslip', 'trans_auditslip.auditslipid', '=', 'hist.auditslipid')
            ->join(self::$ProcessFlag_Table . ' as p', 'p.processcode', '=', 'hist.processcode')
            ->join(self::$InstscheduleMem_Table . ' as schteam', 'schteam.schteammemberid', '=', 'trans_auditslip.schteammemberid')
            ->join(self::$UserDetails_Table . ' as dud_sch', 'dud_sch.deptuserid', '=', 'schteam.userid') // Team member details
            ->leftJoin(self::$UserDetails_Table . ' as dud_forwardedby', 'dud_forwardedby.deptuserid', '=', 'hist.forwardedby') // Forwarded by (Auditor)
            ->leftJoin(self::$UserDetails_Table . ' as dud_forwardedto', 'dud_forwardedto.deptuserid', '=', 'hist.forwardedto') // Forwarded to (Auditor)
            ->leftJoin(self::$AuditeeUserDetail_Table . ' as aud_forwardedby', 'aud_forwardedby.auditeeuserid', '=', 'hist.forwardedby') // Forwarded by (Auditee)
            ->leftJoin(self::$AuditeeUserDetail_Table . ' as aud_forwardedto', 'aud_forwardedto.auditeeuserid', '=', 'hist.forwardedto') // Forwarded to (Auditee)
            ->select(
                'hist.forwardedby',
                'hist.forwardedto',
                'hist.forwardedbyusertypecode',
                'hist.forwardedtousertypecode',
                'p.processelname',
                'hist.remarks',
                DB::raw("TO_CHAR(hist.forwardedon, 'DD-MM-YYYY  hh:MI AM') as forwardedon"), // Date formatting
                DB::raw("
                                CASE
                                    WHEN hist.forwardedbyusertypecode = 'I'
                                    THEN CONCAT(aud_forwardedby.username, ' (Auditee)')
                                    ELSE CONCAT(dud_forwardedby.username, ' (Auditor)')
                                END AS forwardedby_username
                            "), // Append (Auditee) or (Auditor) for forwardedby
                DB::raw("
                                CASE
                                    WHEN hist.forwardedtousertypecode = 'I'
                                    THEN CONCAT(aud_forwardedto.username, ' (Auditee)')
                                    ELSE CONCAT(dud_forwardedto.username, ' (Auditor)')
                                END AS forwardedto_username
                            ") // Append (Auditee) or (Auditor) for forwardedto
            )
            ->where('hist.auditslipid', $slipId)
            ->orderBy('hist.transhistoryid', 'asc') // Order by transhistoryid descending
            ->get();

        return response()->json(['status' => 'success', 'data' => $auditSlips]);
    }
}
