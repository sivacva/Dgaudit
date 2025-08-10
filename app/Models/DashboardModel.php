<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

// class UserModel extends Model
// {
//     use HasFactory;
// }

class DashboardModel extends Model
{
    protected static $deptTable = BaseModel::DEPT_TABLE;

    protected static $distTable = BaseModel::DIST_Table;

    protected static $institutionTable = BaseModel::INSTITUTION_TABLE;

    protected static $sliphistorytable = BaseModel::SLIPHISTORYTRANSACTION_TABLE;

    protected static $AuditeeUserDetail_Table       =  BaseModel::AUDITEEUSERDETAIL_TABLE;

    protected static $auditplan_table = BaseModel::AUDITPLAN_TABLE;

    protected static $instauditschedule_table = BaseModel::INSTSCHEDULE_TABLE;


    public static function createdept_insertupdate(array $data, $currentDeptId = null, $table)
    {
        try {


            $query = DB::table($table);

            if ($currentDeptId) {
                $query->where('deptid', '!=', $currentDeptId);
            }

            $DeptesnameExists = (clone $query)->where('deptesname', $data['deptesname'])->exists();
            $DeptelnameExists = (clone $query)->where('deptelname', $data['deptelname'])->exists();
            $DepttsnameExists = (clone $query)->where('depttsname', $data['depttsname'])->exists();
            $DepttlnameExists = (clone $query)->where('depttlname', $data['depttlname'])->exists();
            $existingDept = (clone $query)
                ->where('deptesname', $data['deptesname'])
                ->where('deptelname', $data['deptelname'])
                ->where('depttsname', $data['depttsname'])
                ->where('depttlname', $data['depttlname'])
                ->first();

            // Duplicate validation
            if ($DeptesnameExists) {
                throw new \Exception('The Department English Short Name address is already exists.');
            }
            if ($DeptelnameExists) {
                throw new \Exception('The Department English Long Name is already associated exists.');
            }
            if ($DepttsnameExists) {
                throw new \Exception('The Department Tamil Short Name is already exists.');
            }
            if ($DepttlnameExists) {
                throw new \Exception('The Department Tamil Long Name is already exists.');
            }
            if ($existingDept) {
                throw new \Exception('The combination of email, mobile number, and IFHRMS number is already associated with a different user.');
            }

            // Create or update
            if ($currentDeptId) {
                DB::table($table)->where('deptid', $currentDeptId)->update($data);
                return DB::table($table)->where('deptid', $currentDeptId)->first();
            } else {
                $lastDeptCode = DB::table($table)->orderBy('deptid', 'desc')->value('deptcode');
                if ($lastDeptCode) {
                    $newDeptCode = str_pad((int)$lastDeptCode + 1, 2, '0', STR_PAD_LEFT);
                    $data['deptcode'] =   $newDeptCode;
                } else {
                    $newDeptCode = '01';
                    $data['deptcode'] =   $newDeptCode;
                }
                $newUserId = DB::table($table)->insertGetId($data, 'deptid');
                return DB::table($table)->where('deptid', $newUserId)->first();
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    public static function fetchAlldata($table)
    {
        return DB::table($table)
            ->where('statusflag', 'Y')
            ->orderBy('updatedon', 'desc')
            ->get();
    }
    public static function  fetchdept_data($deptid, $table)
    {
        return DB::table($table)
            ->where('deptid', $deptid)
            ->first();
    }

    /** ----------Fetch department details based on code  ------------*/

    public static function fetchDeptDetails($deptCode = null)
    {
        $query = DB::table(self::$deptTable)->where('statusflag', 'Y');

        if (!is_null($deptCode)) {
            $query->where('deptid', $deptCode);
        }

        return $query->orderBy('orderid', 'ASC')->get();
    }
    public static function fetchYearDetails($deptCode = null)
    {
        return DB::table('audit.mst_auditquarter')
            ->select('auditquarter', 'auditquartercode')
            ->where('statusflag', 'Y')
            ->orderBy('auditquarterid', 'ASC')
            ->get();
    }




    public static function fetchDistDetails($sessionroletype, $deptcode, $regioncode, $distcode)
    {
        $query = DB::table(self::$distTable . ' as d')
            ->select('d.distcode', 'd.distename')
            ->join(self::$institutionTable . ' as i', 'd.distcode', '=', 'i.distcode')
            ->distinct();


        if ($sessionroletype == view()->shared('Ho_roletypecode') ||$sessionroletype == view()->shared('Re_roletypecode') || $sessionroletype == view()->shared('Dist_roletypecode')) {
            $query->where('i.deptcode', '=', $deptcode);

            if($sessionroletype == view()->shared('Dist_roletypecode') ||$sessionroletype == view()->shared('Re_roletypecode')){
                $query->where('i.regioncode', '=', $regioncode);
            }
            if ($sessionroletype == view()->shared('Dist_roletypecode')) {
                $query->where('i.distcode', '=', $distcode);
            }
        }
        return $query->get();
    }

    public static function fetchInstitutionDetails($head)
    {
        DB::statement('SET search_path TO audit');

        $user = session('user');
        $userid = $user->userid;

        $query = DB::table('inst_auditschedule as aus')
            ->select(
                'inst.instename',
                'inst.insttname',
                'ap.instid',
                'instm.userid',
                'aus.auditscheduleid',
                'instm.auditteamhead'
            )
            ->join('auditplan as ap', 'aus.auditplanid', '=', 'ap.auditplanid')
            ->join('mst_institution as inst', 'ap.instid', '=', 'inst.instid')
            ->join('inst_schteammember as instm', 'instm.auditscheduleid', '=', 'aus.auditscheduleid')
            ->where('instm.userid', '=', $userid); // Get the session's user ID

        // Check if $head equals 'Y'
        if ($head == 'Y') {
            $query->where('instm.auditteamhead', '=', $head)
                  ->where('instm.statusflag', '=', 'Y');
        }

        // Ensure distinct results
        $query->distinct();

        // Return the result of the query
        return $query->get();
    }



    public static function fetchCountDetails($deptCode, $regionCode, $distCode, $userChargeId, $userTypeCode, $roleTypeCode, $auditScheduleId)

    {
        // Set the schema to `audit`
        DB::statement('SET search_path TO audit');

        // Execute the PostgreSQL function and fetch the JSON response
        $result = DB::select('SELECT audit.fn_getDashboardCount(?, ?, ?, ?, ?, ?, ?) AS data', [
            $deptCode,
            $regionCode,
            $distCode,
            $userChargeId,
            $userTypeCode,
            $roleTypeCode,
            $auditScheduleId
        ]);

        // Decode the JSON response into a PHP array or object
        return json_decode($result[0]->data, true);
    }

    // public static function fetchDashboardDescription($deptCode, $regionCode, $distCode, $userId, $userTypeCode, $roleTypeCode, $auditScheduleId, $description)
    // {
    //     DB::statement('SET search_path TO audit');

    //     // Enable query logging
    //     DB::enableQueryLog();

    //     // Execute the function
    //     $result = DB::select('SELECT audit.fn_getdashboarddescription(?, ?, ?, ?, ?, ?, ?, ?) AS data', [
    //         $deptCode,
    //         $regionCode,
    //         $distCode,
    //         $userId,
    //         $userTypeCode,
    //         $roleTypeCode,
    //         $auditScheduleId,
    //         $description
    //     ]);

    //     // Get the last executed query
    //     $queryLog = DB::getQueryLog();

    //     // Print query log
    //     dd($queryLog);

    //     return json_decode($result[0]->data, true);
    // }

    public static function fetchDashboardDescription($deptCode, $regionCode, $distCode, $userId, $userTypeCode, $roleTypeCode, $auditScheduleId, $description)
    {
        DB::statement('SET search_path TO audit');

        // Manually construct the query string
        $sql = sprintf(
            "SELECT audit.fn_getdashboarddescription('%s', '%s', '%s', %d, '%s', '%s', %d, '%s') AS data",
            $deptCode,
            $regionCode,
            $distCode,
            $userId,
            $userTypeCode,
            $roleTypeCode,
            $auditScheduleId,
            $description
        );

        // Print the SQL query
        //dd($sql);

        // Execute the function
        $result = DB::select($sql);

        return json_decode($result[0]->data, true);
    }



// public static function fetchSentDetails($userTypeCode, $userId)
// {
//     $result = DB::table('audit.sliphistorytransactions AS sht')
//         ->select(
//             'sht.auditslipid',
//             'mo.objectionename',
//             'so.subobjectionename',
//             'se.severityelname',
//             'a.amtinvolved',
//             'a.tempslipnumber',
//             'a.mainslipnumber',
//             'a.slipdetails',
//             //DB::raw('a.auditorremarks::json->>\'content\' AS auditorremarks'),
//             //'a.auditeeremarks',
//             //'a.rejoinder_auditeerremarks',
//             'a.liability',
//             'li.liabilityname',
//             'a.processcode',
//             'a.rejoinderstatus',
//             // 'a.memberrejoinderremarks',
//             // 'a.rejoinder_auditorremarks',
//             // 'a.finalremarks',
//             'li.liabilitydesignation',
//             'li.liabilitygpfno',
//             'p.processelname',
//             'ins.auditplanid',
//             'pla.instename',

//             DB::raw("CASE
//                         WHEN sht.forwardedtousertypecode = 'A' THEN dud.username
//                         WHEN sht.forwardedtousertypecode = 'I' THEN aud.username
//                         ELSE 'Unknown'
//                     END AS forwardedtouser"),
//                     DB::raw("CASE
//                     WHEN sht.forwardedtousertypecode = 'A' THEN 'Auditor'
//                     WHEN sht.forwardedtousertypecode = 'I' THEN 'Auditee'
//                     ELSE 'Unknown'
//                 END AS forwardedtousertype"),
//             'sht.forwardedon'
//         )
//         ->join('audit.trans_auditslip AS a', 'sht.auditslipid', '=', 'a.auditslipid')
//         ->join('audit.mst_mainobjection AS mo', 'a.mainobjectionid', '=', 'mo.mainobjectionid')
//         ->join('audit.mst_severity AS se', 'se.severitycode', '=', 'a.severitycode')

//         ->join('audit.liability AS li', 'li.auditslipid', '=', 'a.auditslipid')
//         ->leftJoin('audit.mst_subobjection AS so', 'a.subobjectionid', '=', 'so.subobjectionid')
//         ->leftJoin('audit.deptuserdetails AS dud', function ($join) {
//             $join->on('sht.forwardedto', '=', 'dud.deptuserid')
//                  ->where('sht.forwardedtousertypecode', '=', 'A');
//         })
//         ->leftJoin('audit.audtieeuserdetails AS aud', function ($join) {
//             $join->on('sht.forwardedto', '=', 'aud.auditeeuserid')
//                  ->where('sht.forwardedtousertypecode', '=', 'I');
//         })
//         ->join('audit.mst_process AS p', 'a.processcode', '=', 'p.processcode')

//         ->join('audit.auditplan AS ins', 'a.auditplanid', '=', 'ins.auditplanid')  // Corrected join on auditplanid
//         ->join('audit.mst_institution AS pla', 'ins.instid', '=', 'pla.instid')  // Link based on instid from inst_auditschedule

//         ->where('sht.forwardedby', '=', $userId)
//         ->where('sht.forwardedbyusertypecode', '=', $userTypeCode)
//         ->orderBy( 'sht.forwardedon', 'DESC')
//         ->get();

//     return $result;
// }

public static function fetchSentDetails($userTypeCode, $userId)
{
    $result = DB::table('audit.sliphistorytransactions AS sht')
        ->select(
            'sht.auditslipid',
            'mo.objectionename',
            'so.subobjectionename',
            'se.severityelname',
            'a.amtinvolved',
            'a.tempslipnumber',
            'a.mainslipnumber',
            'a.slipdetails',
            //DB::raw('a.auditorremarks::json->>\'content\' AS auditorremarks'),
            //'a.auditeeremarks',
            //'a.rejoinder_auditeerremarks',
            'a.liability',
            'li.liabilityname',
            'a.processcode',
            'a.rejoinderstatus',
            // 'a.memberrejoinderremarks',
            // 'a.rejoinder_auditorremarks',
            // 'a.finalremarks',
            'li.liabilitydesignation',
            'li.liabilitygpfno',
            'p.processelname',
            'ins.auditplanid',
            'pla.instename',

            DB::raw("CASE
                        WHEN sht.forwardedtousertypecode = 'A' THEN dud.username
                        WHEN sht.forwardedtousertypecode = 'I' THEN aud.username
                        ELSE 'Unknown'
                    END AS forwardedtouser"),
                    DB::raw("CASE
                    WHEN sht.forwardedtousertypecode = 'A' THEN 'Auditor'
                    WHEN sht.forwardedtousertypecode = 'I' THEN 'Auditee'
                    ELSE 'Unknown'
                END AS forwardedtousertype"),
            'sht.forwardedon'
        )
        ->join('audit.trans_auditslip AS a', 'sht.auditslipid', '=', 'a.auditslipid')
        ->join('audit.mst_mainobjection AS mo', 'a.mainobjectionid', '=', 'mo.mainobjectionid')
        ->join('audit.mst_severity AS se', 'se.severitycode', '=', 'a.severitycode')

        ->leftJoin('audit.liability AS li', 'li.auditslipid', '=', 'a.auditslipid')
        ->leftJoin('audit.mst_subobjection AS so', 'a.subobjectionid', '=', 'so.subobjectionid')
        ->leftJoin('audit.deptuserdetails AS dud', function ($join) {
            $join->on('sht.forwardedto', '=', 'dud.deptuserid')
                 ->where('sht.forwardedtousertypecode', '=', 'A');
        })
        ->leftJoin('audit.audtieeuserdetails AS aud', function ($join) {
            $join->on('sht.forwardedto', '=', 'aud.auditeeuserid')
                 ->where('sht.forwardedtousertypecode', '=', 'I');
        })
        ->join('audit.mst_process AS p', 'a.processcode', '=', 'p.processcode')

        ->join('audit.auditplan AS ins', 'a.auditplanid', '=', 'ins.auditplanid')  // Corrected join on auditplanid
        ->join('audit.mst_institution AS pla', 'ins.instid', '=', 'pla.instid')  // Link based on instid from inst_auditschedule

        ->where('sht.forwardedby', '=', $userId)
        ->where('sht.forwardedbyusertypecode', '=', $userTypeCode)
        
        ->orderBy( 'sht.forwardedon', 'DESC')
        ->get();
// ;
// $querySql = $result->toSql();
//         $bindings = $result->getBindings();

//         $finalQuery = vsprintf(
//             str_replace('?', "'%s'", $querySql),
//             array_map('addslashes', $bindings)
//         );

//         print_r($finalQuery);

        

    return $result;
}

public static function getinitimationcount($userid, $userdeptcode)

{
    return DB::table(self::$AuditeeUserDetail_Table . ' as auditee')
        ->join(self::$institutionTable . ' as inst', 'auditee.instid', '=', 'inst.instid')
        ->join(self::$auditplan_table . ' as plan', 'plan.instid', '=', 'auditee.instid')
        ->join(self::$instauditschedule_table . ' as schd', 'schd.auditplanid', '=', 'plan.auditplanid')
        ->where('auditee.auditeeuserid', $userid)
        ->where('auditee.statusflag', 'Y')
        ->where('schd.auditeeresponse',NULL)
        ->where('schd.statusflag', 'F')
        ->selectRaw('COUNT(auditee.auditeeuserid) as total_count')
        ->get();
}

public static function GetcountDetails($deptCode,$regionCode,$distCode,$quarter)
{
    // Set the schema to `audit`
    DB::statement('SET search_path TO audit');

    // Execute the PostgreSQL function and fetch the JSON response
    $result = DB::select('SELECT audit.fn_getcountofdeptwise(?, ?, ?,?) AS data', [
        $deptCode,
        $regionCode,
        $distCode,
        $quarter
    ]);

    // Decode the JSON response into a PHP array or object
    return json_decode($result[0]->data, true);

}


public static function InstitutedetailsGet($deptCode,$regionCode,$distCode,$quarter)
{

        DB::statement('SET search_path TO audit');

        $deptCode = $deptCode ?: null;
        $regionCode = $regionCode ?: null;
        $distCode = $distCode ?: null;
    

        // Execute the PostgreSQL function and fetch the JSON response
        $result = DB::select('SELECT audit.get_allocated_institute_details(?, ?, ?, ?) AS data', [
            $deptCode,
            $regionCode,
            $distCode,
             $quarter
        ]);

        $json = $result[0]->data;  // This will be JSON string

        $data = json_decode($json, true);

        return $data;
    }

// public static function InstitutedetailsGet($deptCode,$regionCode,$distCode,$quarter)
// {
//     return  DB::table('audit.mst_institution as mi')
//             ->join('audit.auditplan as ap', 'ap.instid', '=', 'mi.instid')
//             ->join('audit.mst_district as dist','mi.distcode', '=', 'dist.distcode')
//             ->join('audit.mst_region as reg','reg.regioncode', '=', 'mi.regioncode')
//             ->leftJoin(DB::raw('(
//                 SELECT DISTINCT ON (auditplanid) * 
//                 FROM audit.inst_auditschedule 
//             ) as sch'), 'sch.auditplanid', '=', 'ap.auditplanid')
//             ->when($regionCode, function ($query) use ($regionCode) {
//                 return $query->where('mi.regioncode', $regionCode);
//             })
//             ->when($deptCode, function ($query) use ($deptCode) {
//                 return $query->where('mi.deptcode', $deptCode);
//             })
//             ->when($distCode, function ($query) use ($distCode) {
//                 return $query->where('mi.distcode', $distCode);
//             })
//             ->select(
//                 'mi.instename',
//                 'mi.mandays',
//                 'ap.auditplanid',
//                 'sch.auditscheduleid',
//                 'dist.distename','reg.regionename',
//                 DB::raw("CASE WHEN sch.statusflag = 'F' THEN 'Scheduled' ELSE 'Not Scheduled' END as schedule_status"),
//                 DB::raw("CASE WHEN sch.auditeeresponse = 'A' THEN 'Replied' ELSE 'Waiting for Response' END as response_status"),
//                 DB::raw("CASE WHEN sch.workallocationflag = 'Y' THEN 'work allocated' ELSE 'not allocated' END as workallocation_status"),
//                 DB::raw("CASE 
//                                 WHEN sch.entrymeetdate IS NOT NULL 
//                                 THEN TO_CHAR(sch.entrymeetdate, 'DD/MM/YYYY') 
//                                 ELSE 'No' 
//                             END as entrymeet_status"),  
//                 DB::raw("CASE 
//                             WHEN sch.exitmeetdate IS NOT NULL 
//                             THEN TO_CHAR(sch.exitmeetdate, 'DD/MM/YYYY') 
//                             ELSE 'No' 
//                         END as exitmeet_status"),
//                 DB::raw("(
//                             SELECT head.username || ' - ' || desig.desigelname
//                             FROM audit.auditplanteammember AS head_tm
//                             JOIN audit.deptuserdetails AS head ON head.deptuserid = head_tm.userid
//                             JOIN audit.mst_designation AS desig ON desig.desigcode = head.desigcode
//                             WHERE head_tm.auditplanteamid = ap.auditteamid AND head_tm.teamhead = 'Y' AND head_tm.statusflag ='Y'
//                             LIMIT 1
//                         ) AS team_head_en"),    
//                 DB::raw("(
//                             SELECT COALESCE(STRING_AGG(member.username || ' - ' || desig2.desigelname, ', '), '')
//                             FROM audit.auditplanteammember AS member_tm
//                             JOIN audit.deptuserdetails AS member ON member.deptuserid = member_tm.userid
//                             JOIN audit.mst_designation AS desig2 ON desig2.desigcode = member.desigcode
//                             WHERE member_tm.auditplanteamid = ap.auditteamid AND member_tm.teamhead != 'Y' AND member_tm.statusflag ='Y'
//                         ) AS team_members_en"),
//                 DB::raw('(SELECT COUNT(*) FROM audit.auditplanteammember as sub_atm WHERE sub_atm.auditplanteamid = ap.auditteamid) as total_team_count'),
//                 DB::raw("CASE
//                  WHEN sch.fromdate IS NOT NULL
//                  THEN TO_CHAR(sch.fromdate, 'DD/MM/YYYY')
//                  ELSE '-'
//                  END as fromdate"),  
//  DB::raw("CASE
//                 WHEN sch.todate IS NOT NULL
//                 THEN TO_CHAR(sch.todate, 'DD/MM/YYYY')
//                 ELSE '-'
//                 END as todate")
                
                
//                 )
//                 ->orderByRaw("CASE sch.statusflag WHEN 'F' THEN 0 ELSE 1 END")
//                 ->orderByRaw("CASE sch.auditeeresponse WHEN 'A' THEN 0 ELSE 1 END")
//                 ->orderByRaw("CASE sch.workallocationflag WHEN 'Y' THEN 0 ELSE 1 END")
//                 ->orderByRaw("CASE WHEN sch.entrymeetdate IS NOT NULL THEN 0 ELSE 1 END")
//                 ->orderByRaw("CASE WHEN sch.exitmeetdate IS NOT NULL THEN 0 ELSE 1 END")
//                 ->orderBy('reg.regionename', 'asc')
//                 ->orderBy('dist.distename', 'asc')
//                 ->orderBy('sch.entrymeetdate', 'asc')
//                 ->orderBy('sch.exitmeetdate', 'asc')
//                 ->orderBy('mi.instename', 'asc')
//                 ->get();

// }


public static function CommencedInstitutedetailsGet($deptCode,$regionCode,$distCode,$whichslip='',$quarter)
{
    return DB::table('audit.mst_institution as mi')
    ->join('audit.auditplan as ap', 'ap.instid', '=', 'mi.instid')
    ->join('audit.mst_district as dist', 'mi.distcode', '=', 'dist.distcode')
    ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'mi.regioncode')
    ->leftJoin(DB::raw('(
        SELECT DISTINCT ON (auditplanid) * 
        FROM audit.inst_auditschedule
    ) as sch'), 'sch.auditplanid', '=', 'ap.auditplanid')
    ->when($regionCode, function ($query) use ($regionCode) {
        return $query->where('mi.regioncode', $regionCode);
    })
    ->when($deptCode, function ($query) use ($deptCode) {
        return $query->where('mi.deptcode', $deptCode);
    })
    ->when($distCode, function ($query) use ($distCode) {
        return $query->where('mi.distcode', $distCode);
    })
     ->when($quarter, function ($query) use ($quarter) {
        return $query->where('ap.auditquartercode', $quarter);
    })
    ->whereNotNull('sch.entrymeetdate')
    ->select(
        'mi.instename',
        'mi.mandays',
        'ap.auditplanid',
        'sch.auditscheduleid',
        'dist.distename',
        'reg.regionename',
        DB::raw("(
            SELECT head.username || ' - ' || desig.desigelname
            FROM audit.auditplanteammember AS head_tm
            JOIN audit.deptuserdetails AS head ON head.deptuserid = head_tm.userid
            JOIN audit.mst_designation AS desig ON desig.desigcode = head.desigcode
            WHERE head_tm.auditplanteamid = ap.auditteamid AND head_tm.teamhead = 'Y' AND head_tm.statusflag ='Y'
            LIMIT 1
        ) AS team_head_en"),    
        DB::raw("(
            SELECT COALESCE(STRING_AGG(member.username || ' - ' || desig2.desigelname, ', '), '')
            FROM audit.auditplanteammember AS member_tm
            JOIN audit.deptuserdetails AS member ON member.deptuserid = member_tm.userid
            JOIN audit.mst_designation AS desig2 ON desig2.desigcode = member.desigcode
            WHERE member_tm.auditplanteamid = ap.auditteamid AND member_tm.teamhead != 'Y' AND member_tm.statusflag ='Y'
        ) AS team_members_en"),
        DB::raw('(SELECT COUNT(*) FROM audit.auditplanteammember as sub_atm WHERE sub_atm.auditplanteamid = ap.auditteamid) as total_team_count'),
        DB::raw("CASE 
            WHEN sch.entrymeetdate IS NOT NULL 
            THEN TO_CHAR(sch.entrymeetdate, 'DD/MM/YYYY') 
            ELSE 'No' 
        END as entrymeet_status"),  
        DB::raw("CASE 
            WHEN sch.exitmeetdate IS NOT NULL 
            THEN TO_CHAR(sch.exitmeetdate, 'DD/MM/YYYY') 
            ELSE 'No' 
        END as exitmeet_status"),
        DB::raw("(
            SELECT COUNT(DISTINCT tas.auditslipid)
            FROM audit.trans_auditslip tas
            WHERE tas.auditplanid = ap.auditplanid AND tas.processcode IS NOT NULL
        ) AS totalslips"),
        DB::raw("(
            SELECT COUNT(DISTINCT tas.auditslipid)
            FROM audit.trans_auditslip tas
            WHERE tas.auditplanid = ap.auditplanid AND tas.processcode = 'A'
        ) AS droppedslips"),
        DB::raw("(
            SELECT COUNT(DISTINCT tas.auditslipid)
            FROM audit.trans_auditslip tas
            WHERE tas.auditplanid = ap.auditplanid AND tas.processcode = 'X'
        ) AS convertedslips"),
        DB::raw("(
            SELECT COUNT(DISTINCT tas.auditslipid)
            FROM audit.trans_auditslip tas
            WHERE tas.auditplanid = ap.auditplanid AND tas.processcode NOT IN ('A', 'X')
        ) AS pendingslips"),
         DB::raw("CASE
                 WHEN sch.fromdate IS NOT NULL
                 THEN TO_CHAR(sch.fromdate, 'DD/MM/YYYY')
                 ELSE '-'
                 END as fromdate"),  
         DB::raw("CASE
                WHEN sch.todate IS NOT NULL
                THEN TO_CHAR(sch.todate, 'DD/MM/YYYY')
                ELSE '-'
                END as todate")
    )
    ->when($whichslip == 'pendingslipcount', function ($query) {
        // Apply having on pendingslips instead of pendingslipcount
        $query->whereRaw('(
            SELECT COUNT(DISTINCT tas.auditslipid)
            FROM audit.trans_auditslip tas
            WHERE tas.auditplanid = ap.auditplanid AND tas.processcode NOT IN  (\'A\', \'X\')
        ) > 0');
    })
    ->when($whichslip == 'pendingslipcount', function ($query) {
        // Apply having on pendingslips instead of pendingslipcount
        $query->whereRaw('(
            SELECT COUNT(DISTINCT tas.auditslipid)
            FROM audit.trans_auditslip tas
            WHERE tas.auditplanid = ap.auditplanid AND tas.processcode NOT IN  (\'A\', \'X\')
        ) > 0');
    })
    ->when($whichslip == 'totalslips', function ($query) {
        // Apply having on pendingslips instead of pendingslipcount
        $query->whereRaw('(
            SELECT COUNT(DISTINCT tas.auditslipid)
            FROM audit.trans_auditslip tas
            WHERE tas.auditplanid = ap.auditplanid AND tas.processcode IS NOT NULL
        ) > 0');
    })
    ->when($whichslip == 'convertedslipcount', function ($query) {
        // Apply having on pendingslips instead of pendingslipcount
        $query->whereRaw('(
            SELECT COUNT(DISTINCT tas.auditslipid)
            FROM audit.trans_auditslip tas
            WHERE tas.auditplanid = ap.auditplanid AND tas.processcode = \'X\'
        ) > 0');
    })
    ->when($whichslip == 'droppedslipcount', function ($query) {
        // Apply having on pendingslips instead of pendingslipcount
        $query->whereRaw('(
            SELECT COUNT(DISTINCT tas.auditslipid)
            FROM audit.trans_auditslip tas
            WHERE tas.auditplanid = ap.auditplanid AND tas.processcode = \'A\'
        ) > 0');
    })
    ->orderByRaw("CASE sch.statusflag WHEN 'F' THEN 0 ELSE 1 END")
    ->orderByRaw("CASE sch.auditeeresponse WHEN 'A' THEN 0 ELSE 1 END")
    ->orderByRaw("CASE sch.workallocationflag WHEN 'Y' THEN 0 ELSE 1 END")
    ->orderByRaw("CASE WHEN sch.entrymeetdate IS NOT NULL THEN 0 ELSE 1 END")
    ->orderByRaw("CASE WHEN sch.exitmeetdate IS NOT NULL THEN 0 ELSE 1 END")
    ->orderBy('reg.regionename', 'asc')
    ->orderBy('dist.distename', 'asc')
    ->orderBy('sch.entrymeetdate', 'asc')
    ->orderBy('sch.exitmeetdate', 'asc')
    ->orderBy('mi.instename', 'asc')
    ->get();




}




public static function GetallDept($deptcode = null)
{
    $deptdel = DB::table('audit.mst_dept')
        ->select('deptelname','deptcode')
        ->where('statusflag', '=', 'Y')
        ->when($deptcode, function ($query, $deptcode) {
            return $query->where('deptcode', $deptcode);
        })
        ->orderBy('deptcode', 'asc')
        ->get();
        
    return $deptdel;
}


public static function getslipcount($audit_scheduleid)
{
      // Start building the count query
      $countQuery = DB::table('audit.trans_auditslip as tas')->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'tas.auditplanid')
      ->where('tas.auditscheduleid', $audit_scheduleid);



    // Add the selectRaw part for counting, with DISTINCT and GROUP BY to avoid duplicates
    $countQuery->selectRaw("COUNT(DISTINCT CASE WHEN tas.processcode IS NOT NULL THEN tas.auditslipid END) as totalslips,
                    COUNT(DISTINCT CASE WHEN tas.processcode = 'A' THEN tas.auditslipid END) as droppedslips,
                    COUNT(DISTINCT CASE WHEN tas.processcode = 'X' THEN tas.auditslipid END) as convertedslips,
                    COUNT(DISTINCT CASE WHEN tas.processcode NOT IN ('A', 'X') THEN tas.auditslipid END) as pendingslips
                    ")
                    ->groupBy('tas.auditscheduleid'); // Group by 'auditscheduleid' to avoid duplicates
        
    // Execute the query and get the first result
    $countQuery = $countQuery->first();

    $data = json_decode(json_encode($countQuery), true); // Convert stdClass to array

    return $data;

}

// public static function RegionwiseDetails($deptCode, $regionCode = null, $distCode = null,$quarter)
// {
//     $query = DB::table('audit.auditor_instmapping as ais')
//         ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'ais.regioncode')
//         ->select('reg.regionename', 'reg.regioncode')
//         ->where('reg.statusflag', 'Y')
//         ->where('ais.deptcode', $deptCode)
//         ->orderBy('reg.regionename', 'asc');

//     // Conditionally add regionCode filter
//     if (!empty($regionCode)) {
//         $query->where('ais.regioncode', $regionCode);
//     }

//     // Conditionally add distCode filter
//     if (!empty($distCode)) {
//         $query->where('ais.distcode', $distCode);
//     }

//     return $query->distinct()
//                  ->orderBy('reg.regioncode', 'asc')
//                  ->get();
// }

public static function RegionwiseDetails($deptCode, $regionCode = null, $distCode = null,$quarter)
{
    $query = DB::table('audit.auditplan as ais')
        ->join('audit.mst_institution as ins', 'ins.instid', '=', 'ais.instid')
        ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'ins.regioncode')
        ->select('reg.regionename', 'reg.regioncode')
        ->where('reg.statusflag', 'Y')
         ->where('ais.auditquartercode', $quarter)
        ->where('ins.deptcode', $deptCode)
        ->orderBy('reg.regionename', 'asc');

    // Conditionally add regionCode filter
    if (!empty($regionCode)) {
        $query->where('ins.regioncode', $regionCode);
    }

    // Conditionally add distCode filter
    if (!empty($distCode)) {
        $query->where('ins.distcode', $distCode);
    }

    return $query->distinct()
                 ->orderBy('reg.regioncode', 'asc')
                 ->get();
}

// public static function DistrictwiseDetails($deptCode, $regionCode = null, $distCode = null)
// {
//     $query = DB::table('audit.auditor_instmapping as ais')
//         ->join('audit.mst_district as dist', 'ais.distcode', '=', 'dist.distcode')
//         ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'ais.regioncode')
//         ->select('dist.distename', 'reg.regionename', 'reg.regioncode', 'dist.distcode')
//         ->where('ais.statusflag', 'Y')
//         ->where('ais.deptcode', $deptCode)
//         ->orderBy('reg.regionename', 'asc')
//         ->orderBy('dist.distename', 'asc');

//     // Optional filter by region
//     if (!empty($regionCode)) {
//         $query->where('ais.regioncode', $regionCode);
//     }

//     // Optional filter by district
//     if (!empty($distCode)) {
//         $query->where('ais.distcode', $distCode);
//     }

//     return $query->distinct()
//                  ->orderBy('reg.regioncode', 'asc')
//                  ->get();
                 
// }


public static function DistrictwiseDetails($deptCode, $regionCode = null, $distCode = null,$quarter)


{
    $query = DB::table('audit.auditplan as ais')
    ->join('audit.mst_institution as ins', 'ins.instid', '=', 'ais.instid')
        ->join('audit.mst_district as dist', 'ins.distcode', '=', 'dist.distcode')
        ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'ins.regioncode')
        ->select('dist.distename', 'reg.regionename', 'reg.regioncode', 'dist.distcode')
        ->where('ais.auditquartercode', $quarter)
        ->where('ins.statusflag', 'Y')
        ->where('ins.deptcode', $deptCode)
        ->orderBy('reg.regionename', 'asc')
        ->orderBy('dist.distename', 'asc');

    // Optional filter by region
    if (!empty($regionCode)) {
        $query->where('ins.regioncode', $regionCode);
    }

    // Optional filter by district
    if (!empty($distCode)) {
        $query->where('ins.distcode', $distCode);
    }
  

    return $query->distinct()
                 ->orderBy('reg.regioncode', 'asc')
                 ->get();
                 
}

}
