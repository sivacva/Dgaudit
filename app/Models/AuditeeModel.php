<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\View;


class AuditeeModel extends Model
{
    protected $connection   = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL

    protected $table        =  BaseModel::INSTSCHEDULE_TABLE;

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



    // No auto-increment
    protected $primaryKey = 'auditscheduleid'; // No primary key
    // public $timestamps = true;
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon';

    protected $fillable = [
        'auditplanid',
        'fromdate',
        'todate',
        'rcno',
        'statusflag'

    ];

     public static function  fetch_auditscheduledetails($userid, $deptcode)
    {
        $table = self::$Instschedule_Table;
 	$splinst = 1570;
        return self::query()
            ->join(self::$InstscheduleMem_Table . ' as inm', 'inm.auditscheduleid', '=', 'inst_auditschedule.auditscheduleid')
            ->join(self::$AuditPlan_Table . ' as ap', 'ap.auditplanid', '=', 'inst_auditschedule.auditplanid')
            ->join(self::$AuditPlanTeam_Table . ' as at', 'ap.auditteamid', '=', 'at.auditplanteamid')
            ->join(self::$Institution_Table . ' as ai', 'ai.instid', '=', 'ap.instid')
            ->join(self::$AuditeeUserDetail_Table . ' as auser', 'auser.instid', '=', 'ap.instid')
            ->join(self::$TypeofAudit_Table . ' as mst', 'mst.typeofauditcode', '=', 'ap.typeofauditcode')
            ->join(self::$Dept_Table . ' as msd', 'msd.deptcode', '=', 'ai.deptcode')
            ->join(self::$MstAuditeeInsCategory_Table . ' as mac', 'mac.catcode', '=', 'ai.catcode')

            ->join(self::$AuditQuarter_Table . ' as maq', function ($join) use ($table, $deptcode,$splinst) {
                $join->on('maq.auditquartercode', '=', 'ap.auditquartercode') // Your existing join condition
                    ->where('maq.deptcode', '=', $deptcode);
                   // ->whereColumn('ap.auditquartercode', 'msd.currentquarter');

                   $join->where(function ($q) use ($splinst) {
                    $q->where(function ($sub) use ($splinst) {
                        $sub->where('ai.instid', '!=', $splinst)
                            ->whereColumn('ap.auditquartercode', 'msd.currentquarter');
                    })->orWhere(function ($sub) use ($splinst) {
                        $sub->where('ai.instid', '=', $splinst)
                            ->where('ap.auditquartercode', 'Q1');
                    });
                });
                

            })
            ->join(self::$UserChargeDetails_Table . ' as uc', 'uc.userid', '=', 'inm.userid')
            ->join(self::$UserDetails_Table . ' as du', 'uc.userid', '=', 'du.deptuserid')
            // ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join(self::$Designation_Table . ' as de', 'de.desigcode', '=', 'du.desigcode')
            // ->join('audit.mst_designation as nd', 'nd.desigcode', '=', 'ai.nodalperson_desigcode')
            ->join(self::$MapYearcode_Table . ' as yrmap', 'yrmap.auditplanid', '=', 'ap.auditplanid')
            ->join(
                self::$AuditPeriod_Table . ' as period',
                DB::raw('CAST(yrmap.yearselected AS INTEGER)'),
                '=',
                'period.auditperiodid'
            )
            ->select(
                $table . '.auditscheduleid',
                $table . '.fromdate',
                $table . '.todate',
                $table . '.auditeeresponse',
                'inm.userid',
                'du.username',
                'du.usertamilname',
                'ai.instename',
                'ai.insttname',
                'ai.nodalperson_ename',
                'ai.nodalperson_tname',
                'ai.email',
                'ai.mobile',
                'ai.nodalperson_desigcode',
                'ai.deptcode',
                'ai.catcode',
                'ai.instid',
                'ai.annadhanam_only',
                'ap.auditteamid',
                'ap.auditplanid',
                'at.teamname',
                'mst.typeofauditename',
                'mst.typeofaudittname',
                'msd.deptesname',
                'msd.auditee_ofcusercount',
                'mac.catename',
                'maq.auditquarter',
                'maq.auditquartertname',
                'ap.statusflag',
                'ap.auditquartercode',
                // 'cd.chargedescription',
                'de.desigelname',
                'de.desigtlname',
                'ai.nodalperson_desigcode',
                // DB::raw('nd.desigelname as nodal_desigename'),
                'inm.auditteamhead',
                DB::raw("STRING_AGG(DISTINCT period.fromyear || '-' || period.toyear, ', ') 
                         FILTER (WHERE yrmap.financestatus = 'N') as yearname"),
                DB::raw("STRING_AGG(DISTINCT period.fromyear || '-' || period.toyear, ', ') 
                         FILTER (WHERE yrmap.financestatus = 'Y') as annadhanamyear")

                //DB::raw('STRING_AGG(DISTINCT period.fromyear || \'-\' || period.toyear, \', \') as yearname'),

            )
            ->groupBy(
                $table . '.auditscheduleid',
                $table . '.fromdate',
                $table . '.todate',
                $table . '.auditeeresponse',
                'inm.userid',
                'du.username',
                'du.usertamilname',
                'ai.instename',
                'ai.insttname',
                'ai.deptcode',
                'ai.catcode',
                'ai.instid',
                'ap.auditteamid',
                'ap.auditplanid',
                'at.teamname',
                'mst.typeofauditename',
                'mst.typeofaudittname',
                'msd.deptesname',
                'msd.auditee_ofcusercount',
                'mac.catename',
                'maq.auditquarter',
                'maq.auditquartertname',
                'ap.statusflag',
                // 'cd.chargedescription',
                'de.desigelname',
                'de.desigtlname',
                'ai.nodalperson_desigcode',
                // 'nd.desigelname',
                'inm.auditteamhead',
                'du.deptuserid'
            )
            // ->where('inst_auditschedule.auditscheduleid', function ($query) {
            //     $query->select('auditscheduleid')
            //         ->from('audit.inst_auditschedule')
            //         ->whereColumn('auditscheduleid', 'inst_auditschedule.auditscheduleid')
            //         ->where('statusflag', 'F');
            // })

            ->where('auser.auditeeuserid', '=', $userid)
            ->whereColumn('auser.instid', '=', 'ap.instid')
            ->where('inm.statusflag', '=', 'Y')
            ->where('yrmap.statusflag', 'Y')
            ->where($table . '.statusflag', 'F')
            //->where('inm.auditteamhead', '=', 'N') // Exclude team head
            ->orderBy('du.desigcode', 'asc')
            ->orderBy('du.deptuserid', 'asc')
            ->get();

        //             $querySql = $query->toSql();
        // $bindings = $query->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );
        // print_r($finalQuery);
        // dd($get_majorobjection->toSql());
    }

    public static function update_partialchange($data, $auditescheduleid, $status)
    {
        if ($status == 'P') {
            return   self::query()
                ->where('auditscheduleid', $auditescheduleid)
                ->update([
                    'auditeeremarks' => $data['auditeeremarks'],
                    'auditeeresponsedt' => View::shared('get_nowtime'),
                    'auditeeresponse' => $data['auditeeresponse'],
                    //'entrymeetdate' => $data['entrymeetdate'],
                    'auditeeproposeddate' => $data['auditeeproposeddate'],
                    'updatedon' => View::shared('get_nowtime'),
                ]);
        } else {
            return   self::query()
                ->where('auditscheduleid', $auditescheduleid)
                ->update([
                    'auditeeresponsedt' => View::shared('get_nowtime'),
                    'auditeeresponse' =>$status,
                    //'entrymeetdate' => View::shared('get_nowtime'),
                    'updatedon' => View::shared('get_nowtime'),
                    'auditeeremarks' =>  $data['auditeeremarks'],
                    'nodalname'            => $data['nodalname'],
                    'nodalmobile'          => $data['nodalmobile'],
                    'nodalemail'           => $data['nodalemail'],
                    'nodaldesignation'     => $data['nodaldesignation'],

                ]);
        }
    }

    public static function update_auditstatus($auditscheduleid)
    {
        return   self::query()
                ->where('auditscheduleid', $auditscheduleid)
                ->update([
                    'auditeeresponsedt' => View::shared('get_nowtime'),
                    'auditeeresponse' => 'A',
                    //'entrymeetdate' => View::shared('get_nowtime'),
                    'updatedon' => View::shared('get_nowtime')

                ]);

    }
    
    public static function fetch_Accountaccepteddetails($auditscheduleid)
    {

        $table = self::$Instschedule_Table;

        return self::query()
            ->join(self::$TransaccountDetails_Table . ' as ad', 'ad.auditscheduleid', '=', 'inst_auditschedule.auditscheduleid')
            ->join(self::$AccountParticulars_Table . ' as map', 'map.accountparticularsid', '=', 'ad.accountcode')
            ->join(self::$AuditPlan_Table . ' as ap', 'inst_auditschedule.auditplanid', '=', 'ap.auditplanid')
            ->join(self::$AuditPlanTeam_Table . ' as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            ->join(self::$Institution_Table . ' as mi', 'mi.instid', '=', 'ap.instid')
            ->leftjoin(self::$FileUpload_Table . ' as fu', 'fu.fileuploadid', '=', 'ad.fileuploadid')

            ->select(
                DB::raw("
                        STRING_AGG(
                            CASE
                                WHEN ad.fileuploadid != 0 THEN CONCAT(fu.filename, '-', fu.filepath, '-', fu.filesize, '-', fu.fileuploadid)
                                ELSE '-'
                            END,
                            ',' ORDER BY fu.fileuploadid
                        ) AS filedetails
                    "),
                $table . '.auditscheduleid',
                $table . '.nodalname',
                $table . '.nodalmobile',
                $table . '.nodaldesignation',
                $table . '.nodalemail',
                $table . '.auditeeremarks',
                'ad.accountcode',
                'ad.fileuploadid',
                'ad.remarks',
                'map.accountparticularsename',
                'map.accountparticularstname',
                'map.accountparticularsid'

            )
            ->groupBy(
                $table . '.auditscheduleid',
                $table . '.nodalname',
                $table . '.nodalmobile',
                $table . '.nodaldesignation',
                $table . '.nodalemail',
                $table . '.auditeeremarks',
                'ad.accountcode',
                'ad.fileuploadid',
                'ad.remarks',
                'map.accountparticularsename',
                'map.accountparticularstname',
                'map.accountparticularsid'
            )
            ->where($table . '.statusflag', '=', 'F')
            ->where($table . '.auditscheduleid', '=', $auditscheduleid)
            ->whereNotNull($table . '.auditeeresponse')
            ->orderBy('map.accountparticularsename', 'desc')
            ->get();
    }

    public static function fetch_cfraccepteddetails($auditscheduleid)
    {
        $table = self::$Instschedule_Table;

        return self::query()
            ->join(self::$TransCallforRecords_Table . ' as cfr', 'cfr.auditscheduleid', '=', 'inst_auditschedule.auditscheduleid')
            //->join('audit.mst_subworkallocationtype as msw', 'msw.subworkallocationtypeid', '=', 'cfr.subtypecode')
            // ->join('audit.mst_majorworkallocationtype as mmw', 'mmw.majorworkallocationtypeid', '=', 'msw.majorworkallocationtypeid')
            ->join(self::$CallforRecordsAuditee_Table . ' as cfra', 'cfra.callforrecordsid', '=', 'cfr.subtypecode')

            ->join(self::$AuditPlan_Table . ' as ap', 'inst_auditschedule.auditplanid', '=', 'ap.auditplanid')
            ->join(self::$AuditPlanTeam_Table . ' as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            ->join(self::$Institution_Table . ' as mi', 'mi.instid', '=', 'ap.instid')

            ->select(
                $table . '.auditscheduleid',
                $table . '.nodalname',
                $table . '.nodalmobile',
                $table . '.nodaldesignation',
                $table . '.nodalemail',
                $table . '.auditeeremarks',
                'cfr.subtypecode',
                'cfr.remarks as cfr_remarks',
                'cfr.replystatus',
                'cfra.callforrecordsename',
                'cfra.callforrecordstname',
                'cfra.callforrecordsid',
            )
            ->where($table . '.statusflag', '=', 'F')
            ->where('cfr.auditscheduleid', '=', $auditscheduleid)
            ->whereNotNull('inst_auditschedule.auditeeresponse')
            ->orderBy('cfra.callforrecordsename', 'desc')
            ->get();
    }

    public static function callforrecords($catcode, $deptcode)
    {
       /* return DB::table(self::$MapCallforRecords_Table)
            ->join(self::$CallforRecordsAuditee_Table . ' as cfa', self::$MapCallforRecords_Table . '.callforecordsid', '=', 'cfa.callforrecordsid')
            ->orderBy('cfa.callforrecordsename', 'asc') // Order by callforrecordsid or any other column
            ->where('cfa.statusflag', '=', 'Y') // Filter by 'statusflag' in callforrecords_auditee table
            ->where('map_callforrecord.catcode', '=', $catcode) // Filter by 'statusflag' in callforrecords_auditee table
            ->where('cfa.deptcode', '=', $deptcode) // Filter by 'statusflag' in callforrecords_auditee table
            ->get();*/

            return DB::table('audit.map_allocation_objection as mao')
            ->join('audit.callforrecords_auditee as cfa', 'mao.deptcode', '=', 'cfa.deptcode')
           // ->join('audit.map_allocation_objection as mao', 'mao.mapcallforrecordsid', '=', 'map_callforrecord.mapcallforrecordid')
            ->orderBy('cfa.callforrecordsename', 'asc') // Order by callforrecordsid or any other column
            ->where('cfa.statusflag', '=', 'Y') // Filter by 'statusflag' in callforrecords_auditee table
            ->where('mao.catcode', '=',$catcode) // Filter by 'statusflag' in callforrecords_auditee table
            ->where('cfa.deptcode', '=',$deptcode) // Filter by 'statusflag' in callforrecords_auditee table
            ->get();
    }

    public static function accountparticulars()
    {
        return DB::table(self::$AccountParticulars_Table)
            ->where('statusflag', '=', 'Y')
            ->orderBy('accountparticularsename', 'asc')
            ->get();
    }

    public static function GetAccountParticulars($auditscheduleid, $accountCode)
    {
        return TransAccountDetailModel::where('auditscheduleid', $auditscheduleid)
            ->where('accountcode', $accountCode)
            ->first();
    }

    public static function StoreAccountParticulars($data)
    {
        // Create a record for each account code with the corresponding file upload ID
        return TransAccountDetailModel::create([
            'auditscheduleid' => $data['auditscheduleid'],
            'accountcode' => $data['accountCode'],
            'remarks' => $data['remarks'], // Use the corresponding remarks value
            'statusflag' => 'Y',
            'createdon' => View::shared('get_nowtime'),
            'updatedon' => View::shared('get_nowtime'),
            'fileuploadid' => $data['fileuploadid'],
            // other fields as necessary
        ]);
    }


    public static function GetCallforRecords($auditscheduleid, $cfrSubcode)
    {
        return TransCallforrecordsModel::where('auditscheduleid', $auditscheduleid)
            ->where('subtypecode', $cfrSubcode)
            ->first();
    }

    public static function StoreCallforRecords($data)
    {
        return TransCallforrecordsModel::create([
            'auditscheduleid' => $data['auditscheduleid'],
            'subtypecode' => $data['subtypecode'],
            'remarks' => $data['remarks'],
            'replystatus' => $data['replystatus'],
            'statusflag' => 'Y',
            'createdon' => View::shared('get_nowtime'),
            'updatedon' => View::shared('get_nowtime'),
        ]);
    }

    public static function AuditPeriod()
    {
        return DB::table(self::$AuditPeriod_Table)
            ->where('statusflag', '=', 'Y')
            ->first();
    }

    public static function store_auditeeofficeusers($data)
    {
        $ifexists = AuditeeOfficeUserModel::where('auditscheduleid', $data['auditscheduleid'])
                              ->first();

        if($ifexists)
        {
            AuditeeOfficeUserModel::where('auditscheduleid', $data['auditscheduleid'])->delete();

        }
       
        foreach($data['ofc_userid'] as $key => $val)
        {
            AuditeeOfficeUserModel::create([
                'auditscheduleid' => $data['auditscheduleid'],
                'ofc_username' => $data['ofc_user'][$key],
                'ofc_designation' =>$data['ofc_designation'][$key],
                'service_fromdate' =>$data['ofc_fromdate'][$key],
                'service_todate'=>$data['ofc_enddate'][$key]
            ]);
        }
 	self::query()
            ->where('auditscheduleid',  $data['auditscheduleid'])
            ->update([
                'auditeeresponsedt' => View::shared('get_nowtime'),
                'auditeeresponse' => 'A',
                //'entrymeetdate' => View::shared('get_nowtime'),
                'updatedon' => View::shared('get_nowtime'),

            ]);
        return 'success';

      
    }

    public static function fetch_auditeeofficeusers($auditscheduleid)
    {
        $ifexists = AuditeeOfficeUserModel::where('auditscheduleid', $auditscheduleid)
                              ->first();

        if($ifexists)
        {
            $fetch_auditeeofficeusers = AuditeeOfficeUserModel::where('auditscheduleid', $auditscheduleid)
                              ->get();
            $exists = 1;
            return response()->json([
                'exists' => $exists,
                'fetch_auditeeofficeusers' => $fetch_auditeeofficeusers
            ]);

        }else
        {
            $exists = 0;
            return response()->json([
                'exists' => $exists
            ]);
        }
        
    }
}

class AuditeeOfficeUserModel extends Model
{
    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL


    protected $table = 'audit.auditee_office_users';

    protected $primaryKey = 'ofc_userid'; // No primary key
    // public $timestamps = true;
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon';
    protected $fillable = [
        'auditscheduleid',
        'ofc_username',
        'ofc_designation',
        'service_fromdate',
        'service_todate',

    ];
}


class TransCallforrecordsModel extends Model
{
    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL


    protected $table = BaseModel::TRANSCALLFORRECORDS_TABLE;

    protected $primaryKey = 'callforrecordid'; // No primary key
    // public $timestamps = true;
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon';
    protected $fillable = [
        'subtypecode',
        'auditscheduleid',
        'statusflag',
        'remarks',
        'replystatus',

    ];
}


class TransAccountDetailModel extends Model
{
    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL
    protected $table = BaseModel::TRANSACCOUNTDETAILS_TABLE;
    protected $primaryKey = 'accountdetailid'; // No primary key
    // public $timestamps = true;
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon';
    protected $fillable = [
        'accountcode',
        'auditscheduleid',
        'statusflag',
        'remarks',
        'fileuploadid'
    ];
}
