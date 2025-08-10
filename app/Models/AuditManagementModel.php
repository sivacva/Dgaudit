<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use Illuminate\Support\Facades\View;
use App\Services\SmsService;
use App\Models\MastersModel;
use Carbon\Carbon; 


use App\Services\PHPMailerService;

class YearcodeMapping extends Model
{
    

    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL

    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon'; // Custom column name for `updated_at` (if you have it)

    // Define the table associated with this model
    protected $table = 'audit.yearcode_mapping';

    // Set primary key if it differs from the default 'id'
    protected $primaryKey = 'yearcodemappingid';  // Assuming `yearcodemapping_id` is the primary key

    // Set the primary key type if it's not an auto-incrementing integer
    protected $keyType = 'int';  // If `userid` is an integer

    protected static $regionTable = BaseModel::REGION_TABLE;


    // Disable auto-incrementing if necessary
    public $incrementing = true;  // If true, it will be treated as an auto-incrementing column


    // Set the fillable fields
    protected $fillable = ['auditplanid', 'yearselected', 'statusflag','financestatus', 'createdby', 'createdon', 'updatedon'];

   public static function fetchYearmapById($AuditId, $finance = '')
    {
        return self::where('auditplanid', $AuditId)
            ->where('statusflag', 'Y')
            ->where(function ($query) use ($finance) {
                $query->where('financestatus', $finance)
                      ->orWhereNull('financestatus')
                      ->orWhere('financestatus', '');
            })
            ->get();
    }



}


class StoreCFR extends Model
{
    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL

    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon'; // Custom column name for `updated_at` (if you have it)

    // Define the table associated with this model
    protected $table = 'audit.selected_cfr';

    // Set primary key if it differs from the default 'id'
    protected $primaryKey = 'selected_cfrid';  // Assuming `yearcodemapping_id` is the primary key

    // Set the primary key type if it's not an auto-incrementing integer
    protected $keyType = 'int';  // If `userid` is an integer

    // Disable auto-incrementing if necessary
    public $incrementing = true;  // If true, it will be treated as an auto-incrementing column

    // Set the fillable fields
    protected $fillable = ['auditscheduleid', 'selected_cfr','statusflag'];
}



class AuditManagementModel extends Model
{
    //protected $smsService;
   // protected $mailService;

    // Combine both services in the constructor
    //public function __construct(SmsService $smsService, PHPMailerService $mailService)
  //  {
    //    $this->smsService = $smsService;
      //  $this->mailService = $mailService;
   // }

    protected static $teamassignments_table = BaseModel::TEAMASSIGNMENTS_TABLE;
    protected static $loop_untilfinished_function = BaseModel::LOOP_UNTILFINISHED_FUNCTION;

    protected static $mapinst_table = BaseModel::MAPINST_TABLE;
    protected static $rolemapping_table = BaseModel::ROLEMAPPING_TABLE;
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
    protected static $yearcodemapping_table = BaseModel::MAPYEARCODE_TABLE;
    protected static $transaccountdetails_table = BaseModel::TRANSACCOUNTDETAILS_TABLE;
    protected static $accountparticulars_table = BaseModel::ACCOUNTPARTICULARS_TABLE;
    protected static $fileuploaddetail_table = BaseModel::FILEUPLOAD_TABLE;
    protected static $transcallforrec_table = BaseModel::TRANSCALLFORRECORDS_TABLE;
    protected static $automate_function = BaseModel::AUTOMATE_FUNCTION;
    protected static $finaliseplan_function = BaseModel::FINALISEPLAN_FUNCTION;
    protected static $auditquarter_table = BaseModel::AUDITQUARTER_TABLE;
    protected static $auditperiod_table = BaseModel::AUDITPERIOD_TABLE;
    protected static $callforrec_table = BaseModel::CALLFORRECORDS_AUDITEE_TABLE;
    protected static $mapregiondistrict_table = BaseModel::MAPREGIONDISTRICT_TABLE;
    protected static $dist_table = BaseModel::DIST_Table;
    protected static $auditdistrict_table = BaseModel::AUDITDISTRICT_TABLE;
    protected static $userchargedetail_table = BaseModel::USERCHARGEDETAIL_TABLE;
    protected static $chargedetail_table = BaseModel::CHARGEDETAIL_TABLE;
    protected static $readyforautomate_function = BaseModel::READYFORAUTOMATE_FUNCTION;
    protected static $auditor_instmapping_table = BaseModel::AUDITOR_INSTMAPPING_TABLE;
    protected static $subcategory_table = BaseModel::SUBCATEGORY_TABLE;

protected static $regionTable = BaseModel::REGION_TABLE;


    
    protected $connection = 'pgsql'; // Default is 'mysql', use 'pgsql' for PostgreSQL

    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon'; // Custom column name for `updated_at` (if you have it)

    // Specify that the primary key is `userid` instead of `id`
    protected $primaryKey = 'auditplanid';

    // Specify the table name
    protected $table = 'audit.auditplan';

    // Set the primary key type if it's not an auto-incrementing integer
    protected $keyType = 'int';  // If `userid` is an integer

    // If your primary key is not auto-incrementing, set `incrementing` to false
    public $incrementing = true; // Set to `false` if `userid` is not auto-incrementing

    // Define the fillable fields
    protected $fillable = [
        'instid',
        'auditteamid',
        'typeofauditcode',
        'auditperiodid',
        'auditquartercode',
        'statusflag'
    ];


    /**
     * Create a new user if it doesn't already exist based on email, phone, name, and address.
     * Otherwise, update the user if it already exists, based on email, phone, and name (excluding current id).
     *
     * @param array $data
     * @param int|null $currentUserId (optional: pass the current user's id for updates)
     * @return User|false
     */
    public static function getquarterDet($deptcode)
    {

        return DB::table(self::$deptartment_table . ' as dept')
            ->join(self::$auditquarter_table . ' as quart', 'quart.auditquartercode', '=', 'dept.currentquarter')
            ->where('dept.deptcode', $deptcode)
            ->select('dept.currentquarter', 'quart.auditquarter', 'quart.auditquartercode', 'quart.auditquartertname')
            ->distinct()
            ->get();
    }

    public static function createIfNotExistsOrUpdate(array $data, $currentUserId = null, array $yeararr, $VarDel = null)
    {
        $YearcodeMapArr = [];
        $data['statusflag'] = $data['statusflag'];
        //$data['yearcode'] = '0';


        try {
            // If currentUserId is provided, we are doing an update operation
            if ($currentUserId) {


                //for delete particular record
                if (isset($data['statusflag']) && $VarDel == 'Delete') {
                    // Set the new status flag value
                    $data['statusflag'] = 'N';

                    DB::enableQueryLog();

                    // Search for the record with statusflag = 1 and matching auditplanid
                    $existingRecord = self::where('statusflag', 'Y')
                        ->where('auditplanid', $currentUserId)
                        ->first();


                    // Check if the record exists
                    if ($existingRecord) {
                        // Update the record with the new statusflag value
                        //$existingRecord->update(['statusflag' => $data['statusflag']]);

                        // Manually perform the update using the Query Builder
                        $connection = 'pgsql'; // Name of the database connection
                        $table = 'audit.auditplan'; // Full table name (including schema)

                        $UpdateAuditDelete = DB::connection($connection)
                            ->table($table)
                            ->where('auditplanid', $currentUserId)
                            ->update(['statusflag' => $data['statusflag']]);

                        // Return the updated record
                        return $UpdateAuditDelete;
                    }
                }

                $existingUser = self::query()
                    ->whereIn('statusflag', ['Y', 'F'])
                    //->where('statusflag', '=', 'Y')
                    ->where('auditteamid', '=', $data['auditteamid'])
                    ->where('instid', '=', $data['instid'])
                    ->where('auditplanid', '!=', $currentUserId)
                    ->first();

                if ($existingUser) {
                    // If a user exists, return false or throw an exception (depending on your need)
                    return false;
                }

                // If no such user exists, update the existing record with the provided data
                $existingUser = self::find($currentUserId);
                $existingUser->update($data);

                $existingMappingarr = YearcodeMapping::fetchYearmapById($currentUserId);
                $yearSelectedArr = $existingMappingarr->pluck('yearselected')->toarray();

                $findNewArr = array_diff($yeararr, $yearSelectedArr);
                $RemoveExistArr = array_diff($yearSelectedArr, $yeararr);

                if (sizeof($findNewArr) > 0) {
                    self::updateyearcodemapping($findNewArr, $currentUserId);
                }

                if (sizeof($RemoveExistArr) > 0) {
                    self::updateyearcodemapping($RemoveExistArr, $currentUserId, 'Updatestatusflag');
                }

                return $existingUser;
            } else {
                // Check if a data with the same institute, auditteamcode, year already exists for insertion
                $existingUser = self::query()
                    ->whereIn('statusflag', ['Y', 'F'])
                    //->where('statusflag', '=', 'Y')
                    ->where('auditteamid', '=', $data['auditteamid'])
                    ->where('instid', '=', $data['instid'])
                    ->first();

                if ($existingUser) {
                    // If data exists, return false or handle it as needed
                    return false;
                }
                // Otherwise, create and return the new user

                $CreateAuditPlanid = self::create($data);
                $GetAuditPlanId = $CreateAuditPlanid->auditplanid;
                self::updateyearcodemapping($yeararr, $GetAuditPlanId);
                return $GetAuditPlanId;
            }
        } catch (QueryException $e) {
            // Handle any database-specific exceptions (e.g., duplicate entry)
            Log::error("Database error: " . $e->getMessage());
            throw new Exception("Database error occurred. Please try again later.");
        } catch (Exception $e) {
            // Handle any other general exceptions
            Log::error("General error: " . $e->getMessage());
            throw new Exception("Something went wrong: " . $e->getMessage());
        }
    }

    public static function updateyearcodemapping(array $data, $currentUserId, $statusflagupdate = '',$financestatus='')
    {
        if ($statusflagupdate == 'Updatestatusflag') {
            foreach ($data as $YearVal) {
                // Check if the mapping already exists
                $yearmapping = YearcodeMapping::where('auditplanid', $currentUserId)
                    ->where('yearselected', $YearVal)
                    ->where('statusflag', 'Y')
                    ->first();
                if ($yearmapping) {
                    // If it exists, update the record
                    YearcodeMapping::where('auditplanid', $currentUserId)
                        ->where('yearselected', $YearVal)
                        ->where('statusflag', 'Y')
                        ->update(['statusflag' => 'N','financestatus'=>$financestatus]);
                }
            }
        } else {
            foreach ($data as $YearVal) {
                // Check if the mapping already exists
                $yearmapping = YearcodeMapping::where('auditplanid', $currentUserId)
                    ->where('yearselected', $YearVal)
                    ->where('statusflag', 'Y')
                    ->first();
                if ($yearmapping) {
                    // If it exists, update the record
                    $yearmapping->update(['yearselected' => $YearVal,'financestatus'=>$financestatus]);
                } else {
                    // If it doesn't exist, create a new mapping
                    YearcodeMapping::create([
                        'auditplanid' => $currentUserId,
                        'yearselected' => $YearVal,
                        'createdby' => $currentUserId,
                        'statusflag' => 'Y',
                        'financestatus'=>$financestatus
                    ]);
                }
            }
        }
    }

    public static function fetchAllusers()
    {
        // Fetch all records where statusflag is 1
        //$AllData = self::whereIn('statusflag', ['Y','F'])->get();

        $sessioncharge = session('charge');
        $deptcode = $sessioncharge->deptcode;
        $regioncode = $sessioncharge->regioncode;
        $distcode = $sessioncharge->distcode;

        $AllData = DB::table('audit.auditplan as auditPlan')
            // Join with mst_institution
            ->join('audit.mst_institution as inst', 'auditPlan.instid', '=', 'inst.instid')
            // Join with mst_dept
            ->join('audit.mst_dept as dept', 'inst.deptcode', '=', 'dept.deptcode')
            // Join with mst_region
            ->join('audit.mst_region as region', 'inst.regioncode', '=', 'region.regioncode')
            // Join with mst_district
            ->join('audit.mst_district as dist', 'inst.distcode', '=', 'dist.distcode')
            // Join with mst_auditeeins_category
            ->join('audit.mst_auditeeins_category as instCategory', 'inst.catcode', '=', 'instCategory.catcode')
            // Join with auditplanteam
            ->join('audit.auditplanteam as auditTeam', 'auditPlan.auditteamid', '=', 'auditTeam.auditplanteamid')
            // Join with mst_typeofaudit
            ->join('audit.mst_typeofaudit as typeOfAudit', 'auditPlan.typeofauditcode', '=', 'typeOfAudit.typeofauditcode')
            // Join with mst_auditquarter
            ->join('audit.mst_auditquarter as auditQuarter', function ($join) {
                $join->on('auditQuarter.deptcode', '=', 'inst.deptcode')
                    ->on('auditQuarter.auditquartercode', '=', 'auditPlan.auditquartercode');
            })
            // Add where conditions for filtering the active records
            ->whereIn('auditPlan.statusflag', ['Y', 'F'])  // Only active or flagged records from auditplan
            ->where('inst.statusflag', '=', 'Y')  // Only active institution records
            ->where('dept.statusflag', '=', 'Y')  // Only active department records
            ->where('region.statusflag', '=', 'Y')  // Only active region records
            ->where('dist.statusflag', '=', 'Y')  // Only active district records
            ->where('instCategory.statusflag', '=', 'Y')  // Only active institute category records
            ->where('auditTeam.statusflag', '=', 'F')  // Active audit team (assuming 'F' means active)
            ->where('typeOfAudit.statusflag', '=', 'Y')  // Only active Plan Period records
            ->where('auditQuarter.statusflag', '=', 'Y');  // Only active audit quarter records

        // Conditional WHERE clauses based on deptcode, regioncode, distcode
        if (!empty($deptcode)) {
            $AllData->where('inst.deptcode', '=', $deptcode);
        }

        if (!empty($regioncode)) {
            $AllData->where('inst.regioncode', '=', $regioncode);
        }

        if (!empty($distcode)) {
            $AllData->where('inst.distcode', '=', $distcode);
        }

        $AllData = $AllData->select(
            'auditPlan.*',  // All columns from auditplan
            'inst.deptcode',  // Department code from mst_institution
            'inst.instename',
            'dept.deptesname',  // Department name from mst_dept
            'region.regionename',  // Region name from mst_region
            'dist.distename',  // District name from mst_district
            'instCategory.catename',  // Institute Category name from mst_auditeeins_category
            'auditTeam.teamname',  // Audit Team name from auditplanteam
            'typeOfAudit.typeofauditename',  // Plan Period name from mst_typeofaudit
            'auditQuarter.auditquarter'  // Audit Quarter name from mst_auditquarter
        )->get();


        // Log the result for debugging (better than using print_r)
        \Log::info('Fetched Audit Records:', $AllData->toArray()); // Logs as array for better readability

        // Return the data (can be used in a controller to return the response)
        return $AllData;  // Eloquent collection
    }



    /**
     * Insert the yearcode mapping into the yearcode_mapping table and return the generated yearmapping_id.
     *
     * @param string $yearcodes (comma-separated string of year codes)
     * @return int $yearmappingId (the primary key of the inserted record)
     */

    public static function fetchUserById($userId)
    {
        return self::find($userId);
    }
    
   public static function auditplandet($auditplanid, $userid)
    {
        $table = self::$auditplan_table;
        return  DB::table($table)
            ->join('audit.mst_institution as ai', 'ai.instid', '=', 'auditplan.instid')
            ->join('audit.auditplanteam as at', 'at.auditplanteamid', '=', 'auditplan.auditteamid')
            ->join('audit.auditplanteammember as atm', 'atm.auditplanteamid', '=', 'auditplan.auditteamid')
            ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'atm.userid')
            ->join('audit.deptuserdetails as du', 'atm.userid', '=', 'du.deptuserid')
            ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
            ->select(
                'ai.teamsize',
                'ai.instename',
                'ai.insttname',
                'ai.mandays',
                'ai.instid',
                'ai.catcode',
                'ai.subcatid',
                'ai.deptcode',
                'ai.annadhanam_only',
                'de.desigelname',
                'de.desigtlname',
                'auditplan.auditteamid',
                'auditplan.auditplanid',
                'at.auditplanteamid',
                'atm.userid',
                'uc.userchargeid',
                'du.username',
                'du.usertamilname',
                'cd.chargedescription',
                'ai.carryforward',
                'auditplan.auditquartercode',
                DB::raw('(
                SELECT COUNT(*)
                FROM audit.auditplanteammember AS sub_atm
                WHERE sub_atm.auditplanteamid = auditplan.auditteamid
		        and sub_atm.statusflag = \'Y\'
                ) AS team_member_count'),
                'auditplan.auditquartercode',

            )
            ->where('auditplan.auditplanid', '=', $auditplanid) // Use the decrypted or plain auditplanid
            ->where('atm.userid', '=', $userid)
            ->where('atm.teamhead', '=', 'Y')
            ->where('atm.statusflag', '=', 'Y')
            ->where('auditplan.statusflag', '=', 'F')
            ->get();
    }
    // public static function  fetch_auditplandetails($userid)
    // {

    //     return self::query()
    //         ->join('audit.mst_institution as ai', 'ai.instid', '=', 'auditplan.instid')
    //         ->join('audit.auditplanteam as at', 'at.auditplanteamid', '=', 'auditplan.auditteamid')
    //         ->join('audit.auditplanteammember as atm', 'atm.auditplanteamid', '=', 'auditplan.auditteamid')
    //         ->join('audit.mst_typeofaudit as mst', 'mst.typeofauditcode', '=', 'auditplan.typeofauditcode')
    //         // ->join('audit.mst_auditperiod as map', 'map.auditperiodid', '=', 'auditplan.auditperiodid')
    //         ->join('audit.mst_dept as msd', 'msd.deptcode', '=', 'ai.deptcode')
    //         ->join('audit.mst_auditeeins_category as mac', 'mac.catcode', '=', 'ai.catcode')
    //         ->join(
    //             DB::raw('(SELECT DISTINCT ON (auditquartercode) * FROM audit.mst_auditquarter) AS maq'),
    //             'maq.auditquartercode',
    //             '=',
    //             'auditplan.auditquartercode'
    //         )

    //         ->select(
    //             'ai.instename',
    //             'ai.insttname',
    //             'ai.deptcode',
    //             'ai.instid',
    //             'auditplan.auditteamid',
    //             'auditplan.auditplanid',
    //             'at.auditplanteamid',
    //             'atm.userid',
    //             'at.teamname',
    //             'mst.typeofauditename',
    //             'mst.typeofaudittname',
    //             // 'map.fromyear',
    //             // 'map.toyear',
    //             'msd.deptesname',
    //             'msd.depttsname',
    //             'mac.catename',
    //             'mac.cattname',
    //             'maq.auditquarter',
    //             'auditplan.statusflag',
    //             DB::raw('(
    //     SELECT COUNT(*)
    //     FROM audit.auditplanteammember AS sub_atm
    //     WHERE sub_atm.auditplanteamid = auditplan.auditteamid
    //     AND sub_atm.teamhead = \'N\'
    // ) AS team_member_count')
    //         )
    //         ->where('atm.userid', '=', $userid)
    //         ->where('atm.statusflag', '=', 'Y')
    //         ->where('atm.teamhead', '=', 'Y')
    //         ->where('auditplan.statusflag', '=', 'F')
    //         ->get();
    // }

    // public static function fetch_plandetail()
    // {
    //     return self::query()
    //         ->join('audit.mst_institution as ai', 'ai.instid', '=', 'auditplan.instid')
    //         ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'ai.deptcode')
    //         ->select(
    //             'dept.deptelname',
    //             // Subquery for total_team_count
    //             DB::raw('(SELECT COUNT( auditplanid) FROM audit.auditplan WHERE statusflag = \'F\') AS total_plan_count'),
    //             // DB::raw('(SELECT COUNT( auditplanid) FROM audit.auditplan WHERE statusflag = \'F\' and ) AS dept_plan_count'),
    //             // Subquery for team_member_count
    //             // DB::raw('(SELECT COUNT(DISTINCT at.planteammemberid) FROM audit.auditplanteammember as at WHERE at.statusflag = \'Y\' AND at.auditplanteamid = auditplanteam.auditplanteamid) AS team_member_count')
    //         )
    //         ->where('auditplan.statusflag', 'F')
    //         ->get();
    // }



    public static function fetch_plandetail()
    {
        return DB::table('audit.mst_dept AS dept')
            ->leftJoin('audit.mst_institution AS ai', 'ai.deptcode', '=', 'dept.deptcode')
            ->leftJoin('audit.auditplan AS auditplan', function ($join) {
                $join->on('auditplan.instid', '=', 'ai.instid')
                    ->where('auditplan.statusflag', '=', 'F');
            })
            ->select(
                'dept.deptelname',
                DB::raw('(SELECT COUNT(DISTINCT auditplanid) FROM audit.auditplan WHERE statusflag =  \'F\'  ) AS total_auditplan_count'),
                DB::raw('COUNT(DISTINCT auditplan.auditplanid) AS dept_plan_count')
            )
            ->groupBy('dept.deptelname')
            ->get();
    }

    public static  function automate_plan($deptcode, $distcode, $auditquartercode)
    {

        return DB::select('SELECT * FROM  ' . self::$automate_function . '(:distcode, :quartercode, :deptcode)', [
            'distcode' => $distcode,
            'quartercode' => $auditquartercode,
            'deptcode' => $deptcode,
        ]);
    }

    public static function checkfordetails($data)
    {
        return DB::select('SELECT * FROM  ' . self::$readyforautomate_function . '(:distcode, :quartercode, :deptcode)', [
            // 'distcode'       => $data['distcode'],
            'quartercode'    => $data['auditquartercode'],
            // 'deptcode'       => $data['deptcode'],
            'distcode'       => $data['distcode'],
            // 'quartercode'    => 'Q1',
            'deptcode'       => $data['deptcode'],
        ]);
    }

    public static function getUser_planStatus($data)
    {

       return DB::table(self::$auditor_instmapping_table)
        
            ->select('autoplanstatus', 'userverified')
             ->where('deptcode', $data['deptcode'])
            ->where('distcode', $data['distcode'])
            // ->where('regioncode', $checkdata['regioncode'])
              ->get();


//      $querySql = $query->toSql();
// $bindings = $query->getBindings();

// $finalQuery = vsprintf(
//     str_replace('?', "'%s'", $querySql),
//     array_map('addslashes', $bindings)
// );
//      print_r($finalQuery);
    }
    public static function finalize_plan($deptcode, $distcode, $auditquartercode)
    {
        // return  DB::select('SELECT * FROM ' . self::$finaliseplan_function . '(:distcode, :quartercode, :deptcode)', [
        //     'distcode' => $distcode,
        //     'quartercode' => $auditquartercode,
        //     'deptcode' => $deptcode,
        // ]);

        $query = DB::select(
            'SELECT * FROM ' . self::$finaliseplan_function . '(:distcode, :quartercode, :deptcode)',
            [
                'distcode' => $distcode,
                'quartercode' => $auditquartercode,
                'deptcode' => $deptcode,
            ]
        );
        return $query;
        // Replacing bindings for debugging purposes
        // foreach ($bindings as $key => $value) {
        //     $query = str_replace(':' . $key, "'" . addslashes($value) . "'", $query);
        // }

        // dd($query);
    }

    // public static function getAuditPlan($deptcode, $distcode)
    // {
    //     // return self::$auditplan_table;
    //     return DB::table(self::$auditplan_table . ' as tmr')
    //         ->join(self::$institution_table . ' as inst', 'inst.instid', '=', 'tmr.instid')
    //         ->where('inst.deptcode', $deptcode)
    //         ->where('inst.distcode', $distcode)
    //         ->get();
    // }
    public static function getAuditPlanStatus($checkdata)
    {

        return DB::table(self::$auditdistrict_table)
            // if( $checkdata['deptcode'])
            ->select('planflag', 'userflag')
            ->where('auditdeptcode', $checkdata['deptcode'])
            ->where('auditdistcode', $checkdata['distcode'])
            // ->where('regioncode', $checkdata['regioncode'])
            ->get();


        // dd($query->toSql());
    }

    public static function updateuserStatus($checkdata)
    {
        DB::table(self::$auditdistrict_table)
            ->where('auditdeptcode', $checkdata['deptcode'])
            ->where('auditdistcode', $checkdata['distcode'])
            ->update(['userflag' => 'Y']);
    }
    public static function getAuditors($deptcode, $distcode)
    {
        return DB::table(self::$temprankusers_table . ' as tmr')
            ->join(self::$institution_table . ' as inst', 'inst.instid', '=', 'tmr.instid')
            ->join(self::$userdetail_table . ' as du', 'tmr.deptuserid', '=', 'du.deptuserid')
            ->join(self::$designation_table . ' as dd', 'dd.desigcode', '=', 'du.desigcode')
            ->where('tmr.deptcode', $deptcode)
            ->where('tmr.distcode', $distcode)
            ->orderBy('du.desigcode', 'asc')
            ->orderBy('du.deptuserid', 'asc')
            ->get();
    }

    public static function getAuditorUser($checkdata)
    {
        // Query to fetch all user details
        $users = DB::table(self::$userdetail_table . ' as du')
            ->join(self::$userchargedetail_table . ' as uc', 'uc.userid', '=', 'du.deptuserid')
            ->join(self::$chargedetail_table . ' as cd', 'cd.chargeid', '=', 'uc.chargeid')
            ->join(self::$rolemapping_table . ' as rm', 'rm.rolemappingid', '=', 'cd.rolemappingid')
            ->join(self::$designation_table . ' as dd', 'dd.desigcode', '=', 'du.desigcode')
            ->join(self::$deptartment_table . ' as dept', 'dept.deptcode', '=', 'du.deptcode')

            ->where('du.deptcode', $checkdata['deptcode'])
            ->where('du.distcode', $checkdata['distcode'])
            ->where('du.statusflag', 'Y')
            ->where('uc.statusflag', 'Y')
            ->where('du.reservelist', 'Y')
            ->where('rm.roleactioncode', View::shared('auditor_roleactioncode'))
            // ->where('du.chargeassigned', 'Y')
            // ->where('du.auditorflag', 'Y')
            ->orderBy('dd.desigcode', 'asc')
            ->groupBy('dd.desigcode', 'dd.desigelname', 'dd.desigtlname', 'du.deptuserid', 'du.username', 'du.usertamilname', 'dept.deptesname', 'dept.depttsname')
            ->select('du.deptuserid', 'du.username', 'du.usertamilname', 'dd.desigelname', 'dd.desigtlname', 'dept.deptesname', 'dept.depttsname')
            ->get();

        // Query to get distinct designations with count
        $designationCounts = DB::table(self::$userdetail_table . ' as du')
            ->join(self::$userchargedetail_table . ' as uc', 'uc.userid', '=', 'du.deptuserid')
            ->join(self::$chargedetail_table . ' as cd', 'cd.chargeid', '=', 'uc.chargeid')
            ->join(self::$rolemapping_table . ' as rm', 'rm.rolemappingid', '=', 'cd.rolemappingid')
            ->join(self::$designation_table . ' as dd', 'dd.desigcode', '=', 'du.desigcode')
            ->join(self::$deptartment_table . ' as dept', 'dept.deptcode', '=', 'du.deptcode')

            ->where('du.deptcode', $checkdata['deptcode'])
            ->where('du.distcode', $checkdata['distcode'])
            ->where('du.statusflag', 'Y')
            // ->where('du.chargeassigned', 'Y')
            // ->where('du.auditorflag', 'Y')
            ->where('uc.statusflag', 'Y')
            ->where('du.reservelist', 'Y')
            ->where('rm.roleactioncode', View::shared('auditor_roleactioncode'))

            ->groupBy('dd.desigcode', 'dd.desigelname', 'dd.desigtlname')
            ->orderBy('dd.desigcode', 'asc')
            ->select('dd.desigcode', 'dd.desigelname', 'dd.desigtlname', DB::raw('COUNT(du.deptuserid) as count'))
            ->get();

        $inst_det = DB::table(self::$institution_table . ' as inst')
            ->join(self::$deptartment_table . ' as dept', 'dept.deptcode', '=', 'inst.deptcode')
            ->join(self::$mstauditeeinscategory_table . ' as cat', 'cat.catcode', '=', 'inst.catcode')

            ->where('inst.deptcode', $checkdata['deptcode'])
            ->where('inst.distcode', $checkdata['distcode'])
            ->where('inst.statusflag', 'Y')
            ->whereColumn('inst.audit_quarter', 'dept.currentquarter')
            ->select(
                'inst.instid',
                'inst.instename',
                'inst.insttname',
                'cat.catename',
                'cat.cattname',

            )
            ->get();



        // Return both results
        return [
            'users' => $users,
            'designation_counts' => $designationCounts,
            'inst_det' => $inst_det
        ];
    }


    public static function createauditschedule_dropdown($userid, $auditplanid)
    {
        $table = self::$auditplan_table;

        return   DB::table($table)
            ->join('audit.mst_institution as ai', 'ai.instid', '=', 'auditplan.instid')
            ->join('audit.auditplanteam as at', 'at.auditplanteamid', '=', 'auditplan.auditteamid')
            ->join('audit.auditplanteammember as atm', 'atm.auditplanteamid', '=', 'auditplan.auditteamid')
            ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'atm.userid')
            ->join('audit.deptuserdetails as du', 'atm.userid', '=', 'du.deptuserid')
            ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
            ->select(
                'ai.instename',
                'ai.insttname',
                'ai.instid',
                'ai.mandays',
                'ai.instid',
                'ai.catcode',
                'ai.deptcode',
                'de.desigelname',
                'de.desigtlname',
                'auditplan.auditteamid',
                'auditplan.auditplanid',
                'at.auditplanteamid',
                'atm.userid',
                'uc.userchargeid',
                'du.username',
                'cd.chargedescription',
                'auditplan.auditquartercode',
                DB::raw('(
            SELECT COUNT(*)
            FROM audit.auditplanteammember AS sub_atm
            WHERE sub_atm.auditplanteamid = auditplan.auditteamid
        ) AS team_member_count')
            )
            ->where('auditplan.auditplanid', '=', $auditplanid) // Use the decrypted or plain auditplanid
            ->where('atm.userid', '=', $userid)
            ->where('auditplan.statusflag', '=', 'F')
            ->get();
    }
    public static function audit_members($planid)
    {
        $table = self::$auditplan_table;
        return DB::table($table)
            ->join(self::$auditplanteam_table . ' as at', 'at.auditplanteamid', '=', $table . '.auditteamid')
            ->join(self::$auditplanteammem_table . ' as atm', 'atm.auditplanteamid', '=',  $table . '.auditteamid')
            ->join(self::$userdetail_table . ' as du', function ($join) {
                $join->on('du.deptuserid', '=', 'atm.userid')
                    ->where('atm.teamhead', '=', 'N') // Filter for team members
                    ->where('atm.statusflag', '=', 'Y'); // Filter for team members

            })
            // ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid')
            // ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join(self::$designation_table . ' as de', 'de.desigcode', '=', 'du.desigcode')
            ->where($table . '.statusflag', '=', 'F')
            ->where($table . '.auditplanid', '=', $planid)
            /*->where('auditplan.auditteamid', function ($query) {
        $query->select('auditteamid')
            ->from('audit.auditplan')
            ->whereColumn('auditteamid', 'auditplan.auditteamid')
            ->where('statusflag', 'F')
            ->limit(1); // Ensure only one value is returned
    })*/
            ->select(
                $table . '.auditteamid',
                // 'uc.userchargeid',
                $table . '.auditplanid',
                // 'cd.chargedescription',
                'de.desigelname',
                'de.desigtlname',
                'du.username',
                'du.usertamilname',
                'du.deptuserid',
                'atm.teamhead',
                'atm.userid'
            )
            ->orderBy('du.desigcode', 'asc') // Order by desigcode
            ->orderBy('du.deptuserid', 'asc') // Order by userid in descending order
            ->get();
    }

    public static function fetchAllScheduleData($deptcode)
    {
        $table = self::$instauditschedule_table;

        $user = session('user');
        $userId = $user->userid ?? null;

        $yearSelectedQuery = DB::table('audit.yearcode_mapping as yrmap')
                                ->join('audit.mst_auditperiod as d', DB::raw('CAST(yrmap.yearselected AS INTEGER)'), '=', 'd.auditperiodid')
                                ->select(
                                    'yrmap.auditplanid',
                                    DB::raw('STRING_AGG(DISTINCT d.fromyear || \'-\' || d.toyear, \', \') as yearselected')
                                )
                                ->where('yrmap.statusflag', 'Y')
                                ->where('yrmap.financestatus', 'N')
                                ->groupBy('yrmap.auditplanid')
                                ->get();

        $auditscheduleIdsSubquery = DB::table(self::$instauditschedulemem_table)
                                ->select('auditscheduleid')
                                ->where('userid', $userId)
                                ->whereIn('statusflag', ['Y', 'C', 'R', 'S']);

        $mainQuery = DB::table($table)
            ->join('audit.auditplan as ap',  $table . '.auditplanid', '=', 'ap.auditplanid')
            ->join(self::$institution_table . ' as mi', 'mi.instid', '=', 'ap.instid')
  		->join(self::$deptartment_table . ' as dept', 'dept.deptcode', '=', 'mi.deptcode')
            ->join(self::$instauditschedulemem_table . ' as at', function ($join) use ($table) {
                $join->on('at.auditscheduleid', '=', $table . '.auditscheduleid')
                    ->whereIn('at.statusflag', ['Y', 'C', 'R', 'S']); // Apply the status flag condition here
            })
            // ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'at.userid')
            ->join(self::$userdetail_table . ' as du', 'at.userid', '=', 'du.deptuserid')
            // ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join(self::$designation_table . ' as de', 'de.desigcode', '=', 'du.desigcode')
            ->select(
                $table . '.auditscheduleid',
                $table . '.fromdate',
                $table . '.todate',
                $table . '.rcno',
                $table . '.statusflag',
                'mi.instename',
                'mi.insttname',
                'mi.mandays',
                'ap.auditplanid',
                DB::raw("
                    MIN(
                        CASE
                            WHEN at.auditteamhead = 'Y' AND at.statusflag IN ('Y', 'C','R','S') THEN du.username::text
                            ELSE NULL
                        END
                    ) AS teamhead
                "),
                DB::raw("
                    MIN(
                        CASE
                            WHEN at.auditteamhead = 'Y' AND at.statusflag IN ('Y', 'C','R','S') THEN du.usertamilname::text
                            ELSE NULL
                        END
                    ) AS teamheadtamil
                "),
                        DB::raw("
                    STRING_AGG(
                        CASE
                            WHEN at.auditteamhead = 'N' AND at.statusflag IN ('Y', 'C','R','S') THEN du.username::text
                            ELSE NULL
                        END, ', ' ORDER BY du.desigcode ASC, du.deptuserid ASC
                    ) AS teammembers
                "),
                
                DB::raw("
                STRING_AGG(
                    CASE
                        WHEN at.auditteamhead = 'N' AND at.statusflag IN ('Y', 'C','R','S') THEN du.usertamilname::text
                        ELSE NULL
                    END, ', ' ORDER BY du.desigcode ASC, du.deptuserid ASC
                ) AS teammemberstamil
                "),
                    DB::raw('
                COUNT(
                    CASE
                        WHEN at.statusflag IN (\'Y\', \'C\',\'R\',\'S\') THEN 1
                        ELSE NULL
                    END
                ) AS team_count
            ')
                )
            ->where(function ($query) use ($table) {
               $query->where($table . '.statusflag', '=', 'Y')
                    ->orWhere($table . '.statusflag', '=', 'F')
                    ->orWhere($table . '.statusflag', '=', 'C')
                    ->orWhere($table . '.statusflag', '=', 'S')
                    ->orWhere($table . '.statusflag', '=', 'R');
            })
            ->where('mi.deptcode', $deptcode)
 		->whereColumn('ap.auditquartercode', 'dept.currentquarter')
            ->whereIn($table . '.auditscheduleid', $auditscheduleIdsSubquery)
            ->groupBy(
                $table . '.auditscheduleid',
                $table . '.fromdate',
                $table . '.todate',
                $table . '.rcno',
                $table . '.statusflag',
                'mi.instename',
                'mi.insttname',
                'mi.mandays',
                'ap.auditplanid'
            )
            ->orderByRaw("CASE {$table}.statusflag WHEN 'Y' THEN 0 ELSE 1 END")
            ->get();


       // Get the main query results
        $mainResults = $mainQuery;

        // Get the yearselected values separately
        $yearSelectedResults = $yearSelectedQuery->keyBy('auditplanid'); // Use auditplanid as key

        // Merge the yearselected values into the main query results
        foreach ($mainResults as $result) {
            // Check if yearselected exists for this auditplanid
            if (isset($yearSelectedResults[$result->auditplanid])) {
                $result->yearselected = $yearSelectedResults[$result->auditplanid]->yearselected;
            } else {
                $result->yearselected = null; // Or some default value if not found
            }
        }

        // Now $mainResults contains both the main data and the merged yearselected values
        return $mainResults;

   
   
    }

    public static function createIfNotExistsOrUpdateAuditSchedule(array $data, $currentScheduleid = null, $userid)
    {

        try {

            // If no conflicts, proceed with create or update
            if (!empty($currentScheduleid)) {
		$AlreadyExists = DB::table(self::$instauditschedule_table)
                    ->where('auditplanid', $data['auditplanid'])
                    ->where('auditscheduleid', '!=', $currentScheduleid)
                    ->whereNotIn('statusflag', ['C', 'R', 'S', 'N'])  // Exclude rows where status is either 'C' or 'R'
                    ->exists();
                if ($AlreadyExists) {
                    throw new \Exception('Audit already scheduled');
                }
                $existingUser = DB::table(self::$instauditschedule_table)
                    ->where('auditscheduleid', $currentScheduleid)
                    ->first();

                if ($existingUser) {
                    $data['updatedby'] = $userid;
                    $data['updatedon'] = View::shared('get_nowtime');

                    $yearsselected =$data['yearselected'];

                    $annadhanam_yearselected =$data['annadhanam_yearselected'];


                    unset($data['yearselected']);

                    unset($data['annadhanam_yearselected']);

                    DB::table(self::$instauditschedule_table)
                        ->where('auditscheduleid', $currentScheduleid)
                        ->update($data);

                    if($yearsselected)
                    {
                        $auditplanid =$existingUser->auditplanid;
                        $existingMappingarr = YearcodeMapping::fetchYearmapById($auditplanid,'N');
                        $yearSelectedArr = $existingMappingarr->pluck('yearselected')->toarray();
                        $findNewArr=array_diff($yearsselected,$yearSelectedArr);
                        $RemoveExistArr=array_diff($yearSelectedArr,$yearsselected);
    
        
                        if(sizeof($findNewArr) > 0)
                        {
                            self::updateyearcodemapping($findNewArr,$auditplanid,'','N');
        
                        }
        
                        if(sizeof($RemoveExistArr) > 0)
                        {
                            self::updateyearcodemapping($RemoveExistArr,$auditplanid,'Updatestatusflag','N');
        
                        }

                    }

                    if($annadhanam_yearselected)
                    {
                        $auditplanid =$existingUser->auditplanid;
                        $existingMappingarr = YearcodeMapping::fetchYearmapById($auditplanid,'Y');
                        $yearSelectedArr = $existingMappingarr->pluck('yearselected')->toarray();
                        $findNewArr=array_diff($annadhanam_yearselected,$yearSelectedArr);
                        $RemoveExistArr=array_diff($yearSelectedArr,$annadhanam_yearselected);
    
        
                        if(sizeof($findNewArr) > 0)
                        {
                            self::updateyearcodemapping($findNewArr,$auditplanid,'','Y');
        
                        }
        
                        if(sizeof($RemoveExistArr) > 0)
                        {
                            self::updateyearcodemapping($RemoveExistArr,$auditplanid,'Updatestatusflag','Y');
        
                        }

                    }
                   
                    
                    // Optionally fetch the updated record if needed
                    $updatedUser = DB::table(self::$instauditschedule_table)->where('auditscheduleid', $currentScheduleid)->first();
                   
                   
                    return $updatedUser;
                } else {
                    throw new \Exception("Record not found with ID: $currentScheduleid");
                }
            } else {
                $data['createdby'] = $userid;

                $data['createdon'] = View::shared('get_nowtime');

                $auditplanid=$data['auditplanid'];
                $yeararr = $data['yearselected'];
                $annadhanam_yearselected =$data['annadhanam_yearselected'];


                unset($data['yearselected']);
                unset($data['annadhanam_yearselected']);
                self::updateyearcodemapping($yeararr,$auditplanid,'','N');
                self::updateyearcodemapping($annadhanam_yearselected,$auditplanid,'','Y');

                $AlreadyExists = DB::table(self::$instauditschedule_table)
                                    ->where('auditplanid',$data['auditplanid'])
                                   //->whereNotIn('statusflag', ['C', 'R', 'S'])  // Exclude rows where status is either 'C' or 'R'
                                    ->exists();

                if ($AlreadyExists)
                {
                    throw new \Exception('Audit already scheduled');
                }else
                {
                    return DB::table(self::$instauditschedule_table)->insertGetId($data, 'auditscheduleid');
                }

            }
        } catch (\Exception $e) {
            // Throwing a custom exception with the message from the model
            throw new \Exception($e->getMessage());
        }
    }

    public static function DatecheckAuditschedule($userId, $fromDate, $toDate)
    {
      	 $session = session('charge');
	 $deptcode = $session->deptcode;
 	 $quarterdetails = self::getquarterdetails($deptcode);
	 $currentquarter = $quarterdetails[0]->auditquartercode;

        //    $fetchexitmeet = DB::table(self::$instauditschedule_table . ' as ist')
        //                        ->join(self::$instauditschedulemem_table . ' as ism', 'ism.auditscheduleid', '=', 'ist.auditscheduleid')
        //                        ->where('ism.userid', $userId)
        //                        ->select('ist.exitmeetdate')
        //                        ->first();

        //updated on 06-06-2025 by krishnaveni

        $fetchexitmeet = DB::table(self::$instauditschedule_table . ' as ist')
            ->join(self::$instauditschedulemem_table . ' as ism', 'ism.auditscheduleid', '=', 'ist.auditscheduleid')
	    ->join(self::$auditplan_table . ' as plan', 'ist.auditplanid', '=', 'plan.auditplanid')
            ->where('ism.userid', $userId)
            ->where('ism.statusflag', 'Y')
  	    ->where('plan.auditquartercode', $currentquarter)
            ->whereNotNull('ist.exitmeetdate')
            ->select('ist.exitmeetdate')
            ->orderby('ist.exitmeetdate', 'desc')
            ->first();

        $hasExitMeetDate = $fetchexitmeet && !empty($fetchexitmeet->exitmeetdate);

        $table = $hasExitMeetDate
            ? DB::table(self::$instauditschedulemem_table . ' as ism')
            ->join(self::$instauditschedule_table . ' as ist', 'ism.auditscheduleid', '=', 'ist.auditscheduleid')
            ->where('ism.userid', $userId)
            ->where('ism.statusflag', 'Y')
            : DB::table(self::$instauditschedulemem_table . ' as ism')
            ->where('ism.userid', $userId)
            ->where('ism.statusflag', 'Y');

        $table->where(function ($query) use ($hasExitMeetDate, $fromDate, $toDate) {
            if ($hasExitMeetDate) {
                $query->whereBetween('ism.auditfromdate', [$fromDate, $toDate])
                    ->orWhereBetween('ist.exitmeetdate', [$fromDate, $toDate])
                    ->orWhere(function ($sub) use ($fromDate, $toDate) {
                        $sub->where('ism.auditfromdate', '<=', $fromDate)
                            ->where('ist.exitmeetdate', '>=', $toDate);
                    });
            } else {
                $query->whereBetween('ism.auditfromdate', [$fromDate, $toDate])
                    ->orWhereBetween('ism.audittodate', [$fromDate, $toDate])
                    ->orWhere(function ($sub) use ($fromDate, $toDate) {
                        $sub->where('ism.auditfromdate', '<=', $fromDate)
                            ->where('ism.audittodate', '>=', $toDate);
                    });
            }
        });

        return $table;
    }

    public static function fetchsingle_scheduledata($auditscheduleid)
    {
        // print_r($auditscheduleid);
        $table = self::$instauditschedule_table;
        return DB::table($table)

            ->join(self::$auditplan_table . ' as ap', $table . '.auditplanid', '=', 'ap.auditplanid')
            ->join(self::$institution_table . ' as mi', 'mi.instid', '=', 'ap.instid')
            ->join(self::$instauditschedulemem_table . ' as at', function ($join) use ($table) {
                $join->on('at.auditscheduleid', '=', $table . '.auditscheduleid')
                    ->where('at.auditteamhead', '=', 'Y');
            })
            ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'at.userid')
            ->join('audit.deptuserdetails as du', 'at.userid', '=', 'du.deptuserid')
            ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
            ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->leftJoin('audit.inst_schteammember as sub_atm', function ($join) use ($table) {
                $join->on('sub_atm.auditscheduleid', '=', $table . '.auditscheduleid')
                    ->where('sub_atm.auditteamhead', '=', 'N');
            })
            ->leftJoin('audit.yearcode_mapping as yrmap', function ($join) {
                $join->on('yrmap.auditplanid', '=', 'inst_auditschedule.auditplanid')
                    ->where('yrmap.statusflag', '=', 'Y');
            })
            ->leftJoin('audit.deptuserdetails as sub_du', 'sub_atm.userid', '=', 'sub_du.deptuserid')
            ->select(
                $table . '.auditscheduleid',
                $table . '.fromdate',
                $table . '.todate',
                $table . '.rcno',
                'mi.instename',
                'mi.insttname',
                'mi.instid',
                'mi.mandays',
                'at.auditscheduleid',
                'at.userid as team_head_userid',
                'du.username as team_head_name_en',
                'du.username as team_head_name_ta',
                'cd.chargedescription',
                'teammembers.userid as team_member_userid',
                'teammembers.username as team_member_name',
                'cd.chargedescription',
                'de.desigelname',
                DB::raw('STRING_AGG(yrmap.yearselected::text, \',\' ORDER BY yrmap.yearselected) 
                FILTER (WHERE yrmap.financestatus = \'N\') as yearselected'),
                DB::raw('STRING_AGG(yrmap.yearselected::text, \',\' ORDER BY yrmap.yearselected) 
                FILTER (WHERE yrmap.financestatus = \'Y\') as annadhanam_yearselected'),
                DB::raw('(
                SELECT COUNT(*)
                FROM audit.auditplanteammember as sub_atm
                WHERE sub_atm.auditplanteamid = ap.auditteamid AND sub_atm.statusflag =\'Y\'

            ) as total_team_count')
            )
            ->leftJoin(
                DB::raw('(SELECT sub_atm.auditscheduleid, sub_atm.userid, sub_du.username
                            FROM audit.inst_schteammember as sub_atm
                            JOIN audit.deptuserdetails as sub_du
                                ON sub_atm.userid = sub_du.deptuserid
                            WHERE sub_atm.auditteamhead =\'N\'
                             AND (sub_atm.statusflag =  \'Y\' )) as teammembers'),
                'teammembers.auditscheduleid',
                '=',
                $table . '.auditscheduleid'
            )
            ->where(function ($query) use ($table) {
                $query->whereIn($table . '.statusflag', ['Y', 'C', 'R', 'S']);
                // ->orWhere('inst_auditschedule.statusflag', '=', 'N');
            })
            ->where($table . '.auditscheduleid', '=', $auditscheduleid)
            ->groupBy(
                $table . '.fromdate',
                $table . '.todate',
                $table . '.rcno',
            )
            ->groupBy('inst_auditschedule.auditscheduleid')
            ->groupBy('mi.instename')
            ->groupBy('mi.insttname')
            ->groupBy('mi.instid')
            ->groupBy('at.auditscheduleid')
            ->groupBy('at.userid')
            ->groupBy('du.username')
            ->groupBy('cd.chargedescription')
            ->groupBy('teammembers.userid')
            ->groupBy('teammembers.username')
            ->groupBy('de.desigelname')
            ->groupBy('ap.auditteamid')
            ->get();
    }


    public static function fetchteamMembers($auditscheduleid)
    {
        $teammember = DB::table(self::$instauditschedulemem_table)->where(self::$instauditschedulemem_table . '.auditscheduleid', '=', $auditscheduleid)
            ->get(); // Use get() to return all matching records

        // Return the team members
        return $teammember;
    }

    public static function updateAuditScheduleMem($membersToRemove, $audit_scheduleid, $memberId, $teamhead, $status, $userid)
    {
        return DB::table(self::$instauditschedulemem_table)->whereIn('userid', $membersToRemove)
            ->where('auditscheduleid', $audit_scheduleid)
            ->where('userid', $memberId)
            ->where('auditteamhead', $teamhead)
            ->update(['statusflag' => $status, 'updatedby' => $userid, 'updatedon' => View::shared('get_nowtime')]);
    }

    public static function insertAuditScheduleMem($audit_scheduleid, $memberId, $fromdate, $todate, $teamhead, $userid,)
    {
        return DB::table(self::$instauditschedulemem_table)->insert([
            'auditscheduleid'      => $audit_scheduleid,
            'userid'               => $memberId,
            'auditteamhead'        => $teamhead,
            'auditfromdate'        => $fromdate,
            'audittodate'          => $todate,
	    'diarystatus'  => 'N',
            'statusflag'           => 'Y',
            'createdby'            => $userid,
            'updatedby'            => $userid,
            'createdon'            => View::shared('get_nowtime'),
            'updatedon'            => View::shared('get_nowtime'),
            // other fields as necessary
        ]);
    }
   public static function update_teamstatus($statusflag, $auditscheduleid, $fromdate, $todate)
    {
        $sessiondet = session('user');
        $sessionuserid = $sessiondet->userid;

        $query = DB::table(self::$instauditschedulemem_table)
            ->where('auditscheduleid', $auditscheduleid)
            ->whereNot('statusflag', 'N')
            ->update([
                'statusflag' => $statusflag,
                'auditfromdate' => $fromdate,
                'audittodate'  => $todate,
		'diarystatus'  => 'N',
                'updatedon' => View::shared('get_nowtime'),
                'updatedby' =>  $sessionuserid
            ]);

        return $query;
    }

    public static function updateRcno($deptcode, $incrementedRcno)
    {

        return DB::table(self::$deptartment_table)
            ->where('deptcode', $deptcode)
            ->update(['rcno' => $incrementedRcno]);
    }
    public static function getLastinsertedId($data)
    {

        $existingRecord = DB::table(self::$instauditschedule_table)
            ->where('statusflag', 'Y')
            ->orWhere('statusflag', 'F')
            ->first();


        if ($existingRecord) {
            $id = DB::table(self::$instauditschedule_table)
                ->insertGetId($data, 'auditscheduleid');
        } else {
            $id = null;
        }

        return $id;
    }

    public static function  fetch_auditscheduledetails($userid, $quartercode)
    {
        $table = self::$instauditschedule_table;
        return DB::table($table)
            ->join(self::$auditplan_table . ' as ap', $table . '.auditplanid', '=', 'ap.auditplanid')
            ->join(self::$institution_table . ' as mi', 'mi.instid', '=', 'ap.instid')
            ->join(self::$instauditschedulemem_table . ' as at', function ($join) use ($table) {
                $join->on('at.auditscheduleid', '=', $table . '.auditscheduleid')
                    ->where('at.auditteamhead', '=', 'Y');
            })

            ->join(self::$auditplanteam_table . ' as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            // ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'at.userid')
            ->join(self::$userdetail_table . ' as du', 'at.userid', '=', 'du.deptuserid')
            // ->join('audit.chargedetails as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join(self::$yearcodemapping_table . ' as yrmap', 'yrmap.auditplanid', '=', 'ap.auditplanid')
            ->join(
                self::$auditperiod_table . ' as period',
                DB::raw('CAST(yrmap.yearselected AS INTEGER)'),
                '=',
                'period.auditperiodid'
            )
            ->select(
                $table . '.auditscheduleid',
                $table . '.fromdate',
                $table . '.todate',
                $table . '.rcno',
                $table . '.statusflag',
                $table . '.auditeeresponse',
                $table . '.auditeeresponsedt',
                $table . '.auditeeproposeddate',
                $table . '.auditeeremarks',
                $table . '.nodalname',
                $table . '.nodalmobile',
                $table . '.nodalemail',
                $table . '.nodaldesignation',
                $table . '.workallocationflag',
                'ap.auditplanid',
                'apt.teamname',
                'mi.instename',
                'mi.insttname',
                'mi.mandays',
                'at.auditscheduleid',
                'at.userid',
                'du.username',
                // 'cd.chargedescription',
                DB::raw("STRING_AGG(DISTINCT period.fromyear || '-' || period.toyear, ', ') 
                FILTER (WHERE period.financestatus = 'N') as yearname"),
                //DB::raw('STRING_AGG(DISTINCT period.fromyear || \'-\' || period.toyear, \', \') as yearname'),
                DB::raw('(
    SELECT COUNT(*)
    FROM ' . self::$instauditschedulemem_table . ' as sub_atm
    WHERE sub_atm.auditscheduleid = ' . $table . '.auditscheduleid
    AND (sub_atm.statusflag =  \'Y\' )
    AND sub_atm.auditteamhead = \'N\'
) AS team_member_count')
            )
            ->groupBy(
                $table . '.auditscheduleid',
                $table . '.fromdate',
                $table . '.todate',
                $table . '.rcno',
                $table . '.statusflag',
                $table . '.auditeeresponse',
                $table . '.auditeeresponsedt',
                $table . '.auditeeproposeddate',
                $table . '.auditeeremarks',
                $table . '.nodalname',
                $table . '.nodalmobile',
                $table . '.nodalemail',
                $table . '.nodaldesignation',
                $table . '.workallocationflag',
                'ap.auditplanid',
                'apt.teamname',
                'mi.instename',
                'mi.insttname',
                'mi.mandays',
                'at.auditscheduleid',
                'at.userid',
                'du.username',
                // 'cd.chargedescription',
            )
            // ->where(function ($query) {

            //     $query->where('inst_auditschedule.statusflag', '=', 'F')
            //         ->whereNotNull('inst_auditschedule.auditeeresponse') // Check auditeeresponse is not null
            //         ->where('inst_auditschedule.auditeeresponse', '!=', '');
            // })
            ->where('at.userid', $userid)
            ->where('inst_auditschedule.statusflag', '=', 'F')
	    ->when($quartercode, function ($query) use ($quartercode) {
                $query->where('ap.auditquartercode', $quartercode);
            })
          //  ->whereNotNull('inst_auditschedule.auditeeresponse')
           // ->where('inst_auditschedule.auditeeresponse', '!=', '')
            ->where('yrmap.statusflag','Y')
            ->get();
    }

    public static function fetch_Accountaccepteddetails($auditscheduleid)
    {
        $table = self::$instauditschedule_table;
        return DB::table($table)


            ->join(self::$transaccountdetails_table . ' as ad', 'ad.auditscheduleid', '=', $table . '.auditscheduleid')
            ->join(self::$accountparticulars_table . ' as map', 'map.accountparticularsid', '=', 'ad.accountcode')
            ->join(self::$auditplan_table . ' as ap', $table . '.auditplanid', '=', 'ap.auditplanid')
            ->join(self::$auditplanteam_table . ' as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            ->join(self::$institution_table . ' as mi', 'mi.instid', '=', 'ap.instid')
            ->leftjoin(self::$fileuploaddetail_table . ' as fu', 'fu.fileuploadid', '=', 'ad.fileuploadid')

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
        $table = self::$instauditschedule_table;
        return DB::table($table)

            ->join(self::$transcallforrec_table . ' as cfr', 'cfr.auditscheduleid', '=',  $table . '.auditscheduleid')
            //->join('audit.mst_subworkallocationtype as msw', 'msw.subworkallocationtypeid', '=', 'cfr.subtypecode')
            // ->join('audit.mst_majorworkallocationtype as mmw', 'mmw.majorworkallocationtypeid', '=', 'msw.majorworkallocationtypeid')
            ->join(self::$callforrec_table . ' as cfra', 'cfra.callforrecordsid', '=', 'cfr.subtypecode')

            ->join(self::$auditplan_table . ' as ap',  $table . '.auditplanid', '=', 'ap.auditplanid')
            ->join(self::$auditplanteam_table . ' as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            ->join(self::$institution_table . ' as mi', 'mi.instid', '=', 'ap.instid')

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
            ->whereNotNull($table . '.auditeeresponse')
            ->orderBy('cfra.callforrecordsename', 'desc')
            ->get();
    }

   
    public static function CFRStoreData($audit_scheduleid, $JsonData)
    {
        StoreCFR::create([
            'auditscheduleid' => $audit_scheduleid,
            'selected_cfr' => $JsonData,
            'statusflag'=> 'Y'
        ]);
    }

 public static function getCurrentQuarter($deptcode, $quartercode)
    {
        // return DB::table(self::$deptartment_table . ' as msd')
        //     ->join(self::$auditquarter_table . ' as maq', 'maq.auditquartercode', '=', 'msd.currentquarter')
        //     ->select('maq.quarterfrom', 'maq.quarterto')
        //     ->where('maq.deptcode', $deptcode)
        //     /// ->where('maq.auditquartercode',$quartercode)
        //     ->first();
        return DB::table(self::$auditquarter_table . ' as maq')
            ->select('maq.quarterfrom', 'maq.quarterto')
            ->where('maq.auditquartercode', $quartercode)
            ->where('maq.deptcode', $deptcode)
            ->first();
    }

    public static function Selected_CFR($audit_scheduleid)
    {
        // Fetch the record for the given audit_scheduleid
        $Selected_CFR = StoreCFR::where('auditscheduleid', $audit_scheduleid)
                                      ->where('statusflag','Y')
                                      ->first();

        // Check if the record exists
        if (!$Selected_CFR) {
            // Return or handle error if no record is found
            return response()->json(['error' => 'No record found'], 404);
        }

        // Decode the selected_cfr JSON field
        $JsonDecode_CFR = json_decode($Selected_CFR->selected_cfr, true);

        // Check if JSON is valid
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Handle invalid JSON error
            return response()->json(['error' => 'Invalid JSON data'], 500);
        }

        // If there are no CFR values in the decoded JSON
        if (empty($JsonDecode_CFR)) {
            return response()->json(['error' => 'No CFR values found in JSON'], 404);
        }

        // Fetch all records for the selected CFR values in a single query using whereIn
        $records = DB::table('audit.callforrecords_auditee as cfra')
            ->select('callforrecordsid', 'callforrecordsename', 'callforrecordstname')
            ->whereIn('cfra.callforrecordsid', $JsonDecode_CFR) // Use whereIn to fetch multiple records
            ->get(); // Fetch all matching records in a single query

        // Initialize an empty array to hold the final result
        $CallforRecords = [];
        // Loop through all fetched records and add them to the final array
        foreach ($records as $record) {

            $CallforRecords[] = [
                'callforrecordsid'    => $record->callforrecordsid,
                'callforrecordsename' => $record->callforrecordsename,
                'callforrecordstname' => $record->callforrecordstname
            ];
        }

        return $CallforRecords;
    }

    /*  public static function CancelSchedule($scheduleid, $remarksdata)
    {
        // Update the 'statusflag' column to 'C' for the specific schedule
        $Instschedule = DB::table(self::$instauditschedule_table)
                           ->where('auditscheduleid', $scheduleid)
                           ->update(['statusflag' => 'C', 'cancel_remarks' => $remarksdata]);

        $InstscheduleMem = DB::table(self::$instauditschedulemem_table)
                             ->where('auditscheduleid', $scheduleid)
                             ->update(['statusflag' => 'C']);

        return $Instschedule;

        // You can also include other fields that need to be updated.
    }*/

    public static function CancelorReSchedule($data)
    {
        if ($data['statusflag'] == 'R') {
            $Instschedule = DB::table(self::$instauditschedule_table)
                ->where('auditscheduleid', $data['auditscheduleid'])
                ->first(); // Use first() to get a single row of data

            DB::table('audit.auditschedule_history')->insert([
                'auditscheduleid' => $Instschedule->auditscheduleid,
                'auditplanid' => $Instschedule->auditplanid,
                'fromdate' => $Instschedule->fromdate,
                'todate' => $Instschedule->todate,
                'entrymeetdate' => $Instschedule->entrymeetdate,
                'exitmeetdate' => $Instschedule->exitmeetdate,
                'auditeeresponse' => $Instschedule->auditeeresponse,
                'auditeeresponsedt' => $Instschedule->auditeeresponsedt,
                'auditeeremarks' => $Instschedule->auditeeremarks,
                'auditeeproposeddate' => $Instschedule->auditeeproposeddate,
                'auditorresponse' => $Instschedule->auditorresponse,
                'rcno' => $Instschedule->rcno,
                'statusflag' => $Instschedule->statusflag,
                'createdon' => $Instschedule->createdon,
                'createdby' => $Instschedule->createdby,
                'updatedby' => $Instschedule->updatedby,
                'updatedon' => $Instschedule->updatedon,
                'nodalname' => $Instschedule->nodalname,
                'nodalmobile' => $Instschedule->nodalmobile,
                'nodalemail' => $Instschedule->nodalemail,
                'nodaldesignation' => $Instschedule->nodaldesignation,
                'workallocationflag' => $Instschedule->workallocationflag,
                'remarks' => $Instschedule->remarks,
                'history_createdon' =>  $data['updatedon'],
                'history_createdby' =>  $data['updatedby'], // Timestamp when the record was created in history
            ]);
        }

        $InstscheduleUpdate = DB::table(self::$instauditschedule_table)
            ->where('auditscheduleid', $data['auditscheduleid'])
            ->update([
                'statusflag' => $data['statusflag'],
                'remarks' => $data['remarks'],
                'updatedby' => $data['updatedby'],
                'updatedon' => $data['updatedon']
            ]);

        $InstscheduleMemUpdate = DB::table(self::$instauditschedulemem_table)
            ->where('auditscheduleid', $data['auditscheduleid'])
            ->update([
                'statusflag' => $data['statusflag'],
                'updatedby' => $data['updatedby'],
                'updatedon' => $data['updatedon']
            ]);

        if ($InstscheduleUpdate && $InstscheduleMemUpdate) {
            return response()->json([
                'message' => 'Audit schedule updated successfully, and data stored in history.',
                'status' => true
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to update audit schedule.',
                'status' => false
            ]);
        }
    }

    public static function checkrandomizedWA($auditscheduleid)
    {
        return DB::table(self::$instauditschedule_table)
            ->where('auditscheduleid', $auditscheduleid)
            ->where('statusflag', 'F')
            ->where('auditeeresponse', 'A')
            ->select('workallocationflag')
            ->get();
    }

    public static function sent_intimation($audit_scheduleid)
    {
        echo $audit_scheduleid;
        $institutions = DB::table('audit.inst_auditschedule as ins')
        ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'ins.auditplanid')
        ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
        ->where('ins.auditscheduleid',$audit_scheduleid)
        ->select('inst.instename', 'inst.mobile','ins.fromdate')
        ->get();

        $instename = $institutions[0]->instename;
        $mobileNumber = $institutions[0]->mobile;
        $fromdate = $institutions[0]->fromdate;

        $data = ['instename' => $instename,
        'mobileNumber'  =>  $mobileNumber,
        'fromdate'  =>  $fromdate];

        //$otp    =   rand(1000,9999);

        $mobileNumber = "8148958988";
        $response = $this->smsService->sendSms($mobileNumber,'','sent_initimation',$data);

      //  print_r($response);

                            

    }

    public static function UserMatchCheck($auditplanid)
    {

        $InstidGet = DB::table('audit.auditplan as ap')
            ->select('ap.instid', 'ap.modifiedplan', 'ap.auditplanid')
            ->where('ap.auditplanid', $auditplanid)
            ->where('ap.statusflag', 'F')
            ->first();
        // return $InstidGet;
        if ($InstidGet->modifiedplan == 'Y') {

            return 'success';
        } else {
            /**Team Member Check */
            $MapinstUsercountGet = DB::table(self::$institution_table . ' as inst')
                ->where('inst.instid', $InstidGet->instid)
                ->select('inst.teamsize')
                ->get();
            $MapinstUsercountGet = $MapinstUsercountGet[0]->teamsize;


            $auditplan = DB::table('audit.auditplan as ap')
                ->join(self::$auditplanteam_table . ' as at', 'at.auditplanteamid', '=', 'ap.auditteamid')
                ->join(self::$auditplanteammem_table . ' as atm', 'atm.auditplanteamid', '=', 'ap.auditteamid')
                // ->where('atm.teamhead', '=', 'N')
                ->where('atm.statusflag', '=', 'Y')
                ->where('ap.auditplanid', $auditplanid)
                ->count();

            if ($MapinstUsercountGet != $auditplan) {
                $response = 'insufficient_user_count';
                return $response;
            }

            // /**Team Head Check */
            // $MapinstHeadcountGet = DB::table('audit.map_instdesig as mid')
            //     ->where('mid.instid', $InstidGet->instid)
            //     ->where('mid.teamhead', 'Y')
            //     ->count();

            // $auditplan_Head = DB::table('audit.auditplan as ap')
            //     ->join(self::$auditplanteam_table . ' as at', 'at.auditplanteamid', '=', 'ap.auditteamid')
            //     ->join(self::$auditplanteammem_table . ' as atm', 'atm.auditplanteamid', '=', 'ap.auditteamid')
            //     ->where('atm.teamhead', '=', 'Y')
            //     ->where('atm.statusflag', '=', 'Y')
            //     ->where('ap.auditplanid', $auditplanid)
            //     ->count(); // Filter for team members

            // if ($MapinstHeadcountGet !== $auditplan_Head) {
            //     $response = 'insufficient_head_count';
            //     return $response;
            // }
        }
    }


public static function fetchInstitutionData($deptcode = null, $regioncode = null, $distcode = null)
    {
        $auditplanTable = self::$auditplan_table;

        $query = DB::table("$auditplanTable as ap")
            ->join('audit.auditplanteam as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            ->join('audit.mst_institution as ins', 'ins.instid', '=', 'ap.instid')
            ->join('audit.auditplanteammember as aptm', 'aptm.auditplanteamid', '=', 'ap.auditteamid')
            ->join('audit.deptuserdetails as dp', 'dp.deptuserid', '=', 'aptm.userid')
            ->leftJoin('audit.inst_auditschedule as ias', function ($join) {
                $join->on('ias.auditplanid', '=', 'ap.auditplanid')
                    ->whereNull('ias.entrymeetdate')
                    ->WhereIn('ias.workallocationflag', ['N', null])
                    // ->WhereIn('ias.workallocationflag',['N',null])
                    ->whereIn('ias.statusflag', ['Y', 'F']);
                //  ->whereNull('ias.auditeeresponse');
            });
        //->whereNull('ias.auditeeresponse');

        if (!empty($deptcode)) {
            $query->where('ins.deptcode', $deptcode);
        }
        if (!empty($regioncode)) {
            $query->where('ins.regioncode', $regioncode);
        }
        if (!empty($distcode)) {
            $query->where('ins.distcode', $distcode);
        }

        $query->whereNotExists(function ($subquery) {
            $subquery->select(DB::raw(1))
                ->from('audit.inst_auditschedule as ias2')
                ->whereRaw('ias2.auditplanid = ap.auditplanid')
                ->where(function ($cond) {
                    $cond->whereNotNull('ias2.entrymeetdate')
                        ->orWhereNotIn('ias2.workallocationflag', ['N', ''])
                        ->orWhereNotIn('ias2.statusflag', ['Y', 'F']);
                });
        });
        $query
            ->select(
                'ap.auditplanid',
                'ins.instename',
                'ins.insttname',
            )
            ->groupBy(
                'ap.auditplanid',
                // 'ins.instid',
                'ins.instename',
                'ins.insttname'
            );

      

        return
            $query->get();
    }


public static function fetchTeamData($deptcode = null, $regioncode = null, $distcode = null, $auditteamid = null, $instid = null, $teamHeadNames = [], $teamMemberNames = [])
    {
        $auditplanTable = self::$auditplan_table;

        $scheduledauditors = DB::table("$auditplanTable as ap")
            ->join('audit.auditplanteam as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            // ->join('audit.mst_institution as ins', 'ins.instid', '=', 'ap.instid')
            // ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'ins.regioncode')
            // ->join('audit.mst_district as dist', 'dist.distcode', '=', 'ins.distcode')
            // ->join('audit.mst_dept as d', 'd.deptcode', '=', 'ins.deptcode')
            ->join('audit.auditplanteammember as aptm', 'aptm.auditplanteamid', '=', 'ap.auditteamid')
            ->join('audit.deptuserdetails as dp', 'dp.deptuserid', '=', 'aptm.userid')
            ->join(self::$designation_table . ' as desig', 'dp.desigcode', '=', 'desig.desigcode')
            ->select(
                'ap.auditteamid',
                'dp.deptuserid',
                DB::raw("STRING_AGG(dp.username || ' - ' || desig.desigelname, ', ') FILTER (WHERE aptm.teamhead = 'Y' and aptm.statusflag = 'Y') AS teamhead"),
                DB::raw("STRING_AGG(dp.username || ' - ' || desig.desigelname, ', ') FILTER (WHERE aptm.teamhead = 'N' and aptm.statusflag = 'Y') AS members")
            )
            ->where('ap.auditplanid', $auditteamid)
            ->groupBy('ap.auditteamid', 'dp.deptuserid')
            ->get();
        // return $query;

        // // // Start the base query
        // $query = DB::table("$auditplanTable as ap")
        //     ->join('audit.auditplanteam as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
        //     ->join('audit.mst_institution as ins', 'ins.instid', '=', 'ap.instid')
        //     ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'ins.regioncode')
        //     ->join('audit.mst_district as dist', 'dist.distcode', '=', 'ins.distcode')
        //     ->join('audit.mst_dept as d', 'd.deptcode', '=', 'ins.deptcode')
        //     ->join('audit.auditplanteammember as aptm', 'aptm.auditplanteamid', '=', 'ap.auditteamid')
        //     ->join('audit.deptuserdetails as dp', 'dp.deptuserid', '=', 'aptm.userid')

        //     ->whereNotExists(function ($subQuery) {
        //         $subQuery->select(DB::raw(1))
        //             ->from('audit.inst_auditschedule as ias')
        //             ->whereColumn('ias.auditplanid', 'ap.auditplanid');
        //     });

        // // Apply the filters if provided (DeptCode, RegionCode, DistCode, AuditTeamID, InstID)
        // if (!empty($deptcode)) {
        //     $query->where('ins.deptcode', $deptcode);
        // }
        // if (!empty($regioncode)) {
        //     $query->where('ins.regioncode', $regioncode);
        // }
        // if (!empty($distcode)) {
        //     $query->where('ins.distcode', $distcode);
        // }
        // if (!empty($auditteamid)) {
        //     $query->where('ap.auditteamid', $auditteamid);
        // }
        // // if (!empty($instid)) {
        // //     $query->where('ins.auditplanid', $auditteamid);
        // // }

        // // If team head names are provided, filter them
        // if (!empty($teamHeadNames)) {
        //     $query->whereIn('dp.username', $teamHeadNames)
        //         ->where('aptm.teamhead', 'Y'); // Only team heads
        // }

        // // If team member names are provided, filter them
        // if (!empty($teamMemberNames)) {
        //     $query->whereIn('dp.username', $teamMemberNames)
        //         ->where('aptm.teamhead', 'N'); // Only team members
        // }

        // // Return the query with selected fields
        // $scheduledauditors =  $query
        //     ->select(
        //         'ap.auditteamid',
        //         'reg.regionename',
        //         'dist.distename',
        //         'd.deptelname',
        //         'dp.deptuserid',
        //         'ins.instename',
        //         DB::raw("STRING_AGG(dp.username, ', ') FILTER (WHERE aptm.teamhead = 'Y') AS teamhead"),
        //         DB::raw("STRING_AGG(dp.username, ', ') FILTER (WHERE aptm.teamhead = 'N') AS members")
        //     )
        //     ->groupBy(
        //         'ap.auditteamid',
        //         'reg.regionename',
        //         'dist.distename',
        //         'd.deptelname',
        //         'ins.instename',
        //         'dp.deptuserid',
        //     )
        //       ->get();
        // $auditplanid = 2117;

        // $scheduledauditors = DB::select("
        //     SELECT
        //         ap.auditteamid,
        //         STRING_AGG(dp.username, ', ') FILTER (WHERE aptm.teamhead = 'Y') AS teamhead,
        //         STRING_AGG(dp.username || ' - ' || des.desigelname, ', ') FILTER (WHERE aptm.teamhead = 'N') AS members,
        //         dp.deptuserid
        //     FROM audit.auditplan ap
        //     INNER JOIN audit.auditplanteam apt ON apt.auditplanteamid = ap.auditteamid
        //     INNER JOIN audit.auditplanteammember aptm ON aptm.auditplanteamid = ap.auditteamid
        //     INNER JOIN audit.deptuserdetails dp ON dp.deptuserid = aptm.userid
        //     INNER JOIN audit.mst_designation des ON des.desigcode = dp.desigcode
        //     WHERE ap.auditplanid = ?
        //     GROUP BY ap.auditteamid
        // ", [$auditteamid]);

        // print_r($scheduledauditors);


        // // Manually replace the `?` with the bound value
        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $sql),
        //     array_map('addslashes', $bindings)
        // );

        // echo $finalQuery;
        // exit;


        $membercount = DB::table('audit.map_instdesig as mid')
            ->join('audit.auditplan as ap', 'ap.instid', '=', 'mid.instid')
            ->where('ap.auditplanid', $auditteamid)
            ->where('mid.teamhead', 'N')
            ->count();

        //     $querySql = $membercount->toSql();
        // $bindings = $membercount->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );

        // print_r($finalQuery);
        // exit;



        $data['scheduledauditors']  =    $scheduledauditors;
        $data['membercount']    =  $membercount;


        return  $data;
    }


public static function updateauditplanuser(array $data, $auditteamid = null)
    {


        // print_r($data);
        // exit;

        DB::beginTransaction();

        try {
            $session = session('user');
            $session_userId = $session->userid;

            $auditplanid = $data['auditplanid'];
            $finaliseflag = $data['statusflag'];

            $udpatedauditteamid = DB::table('audit.auditplan')
                ->where('auditplanid', $auditplanid)
                ->value('auditteamid');

            $query = DB::table('audit.audit_teams_draft')->where('auditplanid', $auditplanid);

            if ($auditteamid) {
                $query->where('auditteamsdraftid', '<>', $auditteamid);
            }

            if ($query->exists()) {
                throw new \Exception('Institution already exists for this audit plan.');
            }

            $procceed_forward = false;

            if ($finaliseflag === 'S') {
                DB::table('audit.auditplan')
                    ->where('auditplanid', $auditplanid)
                    ->update(['statusflag' => 'S']);
            }

            if ($auditteamid) {
                $updated = DB::table('audit.audit_teams_draft')
                    ->where('auditteamsdraftid', $auditteamid)
                    ->update($data);

                if ($updated === 0) {
                    throw new \Exception('No records updated.');
                }
                $procceed_forward = true;
            } else {
                $auditteamid = DB::table('audit.audit_teams_draft')
                    ->insertGetId($data, 'auditteamsdraftid');

                if (!$auditteamid) {
                    throw new \Exception('Insert failed for audit team draft.');
                }
                $procceed_forward = true;
            }

            if ($procceed_forward && $finaliseflag === 'F') {
                DB::table('audit.auditplanteammember')
                    ->where('auditplanteamid', $udpatedauditteamid)
                    ->update(['statusflag' => 'N']);

                // Insert team head
                if (!empty($data['newteamhead'])) {
                    DB::table('audit.auditplanteammember')->insert([
                        'auditplanteamid' => $udpatedauditteamid,
                        'userid'          => $data['newteamhead'],
                        'teamhead'        => 'Y',
                        'statusflag'      => 'Y',
                        'createdon'       => View::shared('get_nowtime'),
                        'createdby'       => $session_userId,
                        'updatedby'       => $session_userId,
                        'updatedon'       => View::shared('get_nowtime'),
                    ]);
                }

                // Insert team members
                $members = is_array($data['newteammembers'])
                    ? $data['newteammembers']
                    : json_decode($data['newteammembers'], true);

                if (!empty($members) && is_array($members)) {
                    $rows = [];
                    foreach ($members as $userId) {
                        $rows[] = [
                            'auditplanteamid' => $udpatedauditteamid,
                            'userid'          => (int) $userId,
                            'teamhead'        => 'N',
                            'statusflag'      => 'Y',
                            'createdon'       => View::shared('get_nowtime'),
                            'createdby'       => $session_userId,
                            'updatedby'       => $session_userId,
                            'updatedon'       => View::shared('get_nowtime'),
                        ];
                    }
                    DB::table('audit.auditplanteammember')->insert($rows);
                }

                DB::table('audit.auditplan')
                    ->where('auditplanid', $auditplanid)
	            ->update(['statusflag' => 'F', 'modifiedplan' => 'Y']);

$schdExists = DB::table(self::$instauditschedule_table)
                    ->where('auditplanid', $auditplanid)
                    ->whereIn('statusflag', ['Y', 'F'])
                    ->first();

                if ($schdExists) {
                    $auditscheduleid = $schdExists->auditscheduleid;
                    $fromdate = $schdExists->fromdate;
                    $todate = $schdExists->todate;

                    DB::table(self::$instauditschedulemem_table)
                        ->where('auditscheduleid', $auditscheduleid)
                        ->update(['statusflag' => 'N']);

                    // Insert team head
                    if (!empty($data['newteamhead'])) {
                        DB::table(self::$instauditschedulemem_table)->insert([
                            'auditscheduleid' => $auditscheduleid,
                            'userid'          => $data['newteamhead'],
                            'auditfromdate'   => $fromdate,
                            'audittodate'     => $todate,
                            'auditteamhead'   => 'Y',
                            'statusflag'      => 'Y',
                            'createdon'       => View::shared('get_nowtime'),
                            'createdby'       => $session_userId,
                            'updatedby'       => $session_userId,
                            'updatedon'       => View::shared('get_nowtime'),
                        ]);
                    }

                    // Insert team members
                    $members = is_array($data['newteammembers'])
                        ? $data['newteammembers']
                        : json_decode($data['newteammembers'], true);

                    if (!empty($members) && is_array($members)) {
                        $rows = [];
                        foreach ($members as $userId) {
                            $rows[] = [
                                'auditscheduleid' => $auditscheduleid,
                                'userid'          => (int) $userId,
                                'auditfromdate'   => $fromdate,
                                'audittodate'     => $todate,
                                'auditteamhead'   => 'N',
                                'statusflag'      => 'Y',
                                'createdon'       => View::shared('get_nowtime'),
                                'createdby'       => $session_userId,
                                'updatedby'       => $session_userId,
                                'updatedon'       => View::shared('get_nowtime'),
                            ];
                        }
                        DB::table(self::$instauditschedulemem_table)->insert($rows);
                    }

                    DB::table(self::$instauditschedule_table)
                        ->where('auditplanid', $auditplanid)
                        ->update(['statusflag' => 'Y']);
                }





                DB::commit(); // ? COMMIT HERE

                return [
                    'status' => true,
                    'type' => 'finalised',
                    'message' => 'Audit Team finalised successfully.'
                ];
            } elseif ($procceed_forward && $finaliseflag === 'S') {
                DB::commit(); // ? COMMIT HERE

                return [
                    'status' => true,
                    'type' => 'saved',
                    'message' => 'Audit Team draft saved successfully.'
                ];
            } else {
                DB::rollBack(); // ? ROLLBACK if nothing done

                return [
                    'status' => false,
                    'type' => 'error',
                    'message' => 'No operation was completed.'
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack(); // ? ROLLBACK on exception

            return [
                'status' => false,
                'type' => 'error',
                'message' => 'Update failed: ' . $e->getMessage()
            ];
        }
    }

  
    public static function getauditors_updateplanuser($deptcode,$regioncode,$distcode,$auditteamid)
    {
        if ($auditteamid) {


            // echo $auditteamid;

            // Step 1: First, retrieve the teamhead_userid and teammember_userid
            // $excludedUserIds = AuditTeamModel::query()
            //     ->join('audit.auditplanteammember as t', 't.auditplanteamid', '=', 'auditplanteam.auditplanteamid')  // Join with the teammember table
            //     ->where('auditplanteam.auditplanteamid', '=', $auditteamid)  // Filter by the specified teamcode
            //     ->where('t.statusflag', '=', 'Y') // Filter by teamcode
            //     ->select('t.userid')  // Select the user IDs
            //     ->get()
            //     ->pluck('userid')  // Get all teamhead_userid values
            //     ->merge(
            //         AuditTeamModel::query()
            //             ->join('audit.auditplanteammember as t', 't.auditplanteamid', '=', 'auditplanteam.auditplanteamid')  // Join again with the teammember table
            //             ->where('auditplanteam.auditplanteamid', '=', $auditteamid)  // Filter by the specified teamcode
            //             ->where('t.statusflag', '=', 'Y') // Filter by teamcode
            //             ->select('t.userid')  // Select the user IDs
            //             ->get()
            //             ->pluck('userid')  // Get all teammember_userid values
            //     );
            // // Step 2: Then, use the excluded user IDs in your original query to filter out them
            // $auditors = UserChargeDetailsModel::query()
            //     ->join('audit.deptuserdetails as du', 'userchargedetails.userid', '=', 'du.deptuserid')
            //     ->join('audit.chargedetails as cd', 'userchargedetails.chargeid', '=', 'cd.chargeid')
            //     ->join('audit.mst_district as md', 'md.distcode', '=', 'cd.distcode')
            //     ->join('audit.mst_designation as de', 'de.desigcode', '=', 'du.desigcode')
            //     ->select(
            //         'du.deptuserid',
            //         'du.username',
            //         'cd.chargedescription',
            //         'md.distename',
            //         'md.disttname',
            //         'userchargedetails.userid',
            //         'de.desigelname',
            //         'de.desigtlname'

            //     )
            //     ->where('userchargedetails.statusflag', '=', 'Y')
            //     ->when($distcode !== 'A', function ($query) use ($distcode) {
            //         return $query->where('md.distcode', '=', $distcode);
            //     })
            //     ->whereNotIn('userchargedetails.userid', $excludedUserIds)  // Exclude the userids
            //     ->get();
            //use Illuminate\Support\Facades\DB;

            $excludedIds = DB::table('audit.audit_teams_draft')
            ->where('auditteamsdraftid', $auditteamid)
            ->value('newteammembers'); // This returns a JSON string like '["1261","1434"]'
        
            $excludedArray = json_decode($excludedIds, true) ?? []; // Ensure it's an array even if null
            
            $newTeamHead = DB::table('audit.audit_teams_draft')
                ->where('auditteamsdraftid', $auditteamid)
                ->value('newteamhead'); // Integer
            
            // Add newteamhead to the array if it's not null
            if (!is_null($newTeamHead)) {
                $excludedArray[] = (string)$newTeamHead; // Ensure it's a string for consistency
            }
            
            // Optional: remove duplicates just in case
            $excludedArray = array_unique($excludedArray);
            
            // Debug
            // print_r($excludedArray);
            
            $data = DB::table('audit.deptuserdetails as dp')
                ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'dp.deptuserid')
                ->join('audit.chargedetails as cd', 'cd.chargeid', '=', 'uc.chargeid')
                ->join('audit.rolemapping as rol', 'rol.rolemappingid', '=', 'cd.rolemappingid')
                ->join('audit.mst_district as md', 'md.distcode', '=', 'dp.distcode')
                ->join('audit.mst_designation as de', 'de.desigcode', '=', 'dp.desigcode')
                ->where('uc.statusflag', '=', 'Y')
                ->where('dp.statusflag', '=', 'Y')
                ->where('dp.deptcode', $deptcode)
                ->where('cd.regioncode', $regioncode)
                ->where('dp.distcode', $distcode)
                ->where('rol.roleactioncode', '04')
                ->when(!empty($excludedArray), function ($query) use ($excludedArray) {
                    $query->whereNotIn('dp.deptuserid', $excludedArray);
                })
                ->select( 'dp.deptuserid',
                'dp.username',
                'cd.chargedescription',
                'md.distename',
                'md.disttname',
                'uc.userid',
                'de.desigelname',
                'de.desigtlname')
                ->get();
            

                return $data;

                // print_r($data);

                // exit;

        } else {
            $data = DB::table('audit.deptuserdetails as dp')
            ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'dp.deptuserid')
            ->join('audit.chargedetails as cd', 'cd.chargeid', '=', 'uc.chargeid')
            ->join('audit.rolemapping as rol', 'rol.rolemappingid', '=', 'cd.rolemappingid')
            ->join('audit.mst_designation as de', 'de.desigcode', '=', 'dp.desigcode')
            ->join('audit.mst_district as md', 'md.distcode', '=', 'dp.distcode')
            ->where('uc.statusflag', '=', 'Y')
            ->where('dp.statusflag', '=', 'Y')
            ->where('dp.deptcode',  $deptcode)
            ->where('cd.regioncode', $regioncode)
            ->where('dp.distcode',  $distcode)
            ->where('rol.roleactioncode', '04')
            ->select(
                'dp.deptuserid',
                'dp.username',
                'cd.chargedescription',
                'md.distename',
                'md.disttname',
                'uc.userid',
                'de.desigelname',
                'de.desigtlname'

            )
            ->get();

            return $data;

               
        }
    }

     public function fetchUpdateuserplanData($auditteamid = null)
    {
        $data = DB::table('audit.audit_teams_draft as atd')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'atd.auditplanid')
            ->join('audit.auditplanteam as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            ->join('audit.mst_institution as inst', 'ap.instid', '=', 'inst.instid')
            ->join('audit.mst_dept as dept', 'inst.deptcode', '=', 'dept.deptcode')
            ->join('audit.mst_region as region', 'inst.regioncode', '=', 'region.regioncode')
            ->join('audit.mst_district as dist', 'inst.distcode', '=', 'dist.distcode')
            ->join('audit.deptuserdetails as oldhead', 'oldhead.deptuserid', '=', 'atd.oldteamhead')
            ->join('audit.mst_designation as olddes', 'olddes.desigcode', '=', 'oldhead.desigcode')
            ->join('audit.deptuserdetails as newhead', 'newhead.deptuserid', '=', 'atd.newteamhead')
            ->join('audit.mst_designation as newdes', 'newdes.desigcode', '=', 'newhead.desigcode')
            ->leftJoin('audit.fileuploaddetail as fu', 'fu.fileuploadid', '=', 'atd.fileuploadid')
            ->leftJoin(DB::raw("(SELECT mid.instid, COUNT(*) AS membercount
            FROM audit.map_instdesig AS mid
            JOIN audit.auditplan AS ap ON ap.instid = mid.instid
            WHERE mid.teamhead = 'N'
            GROUP BY mid.instid) AS member_counts"), 'member_counts.instid', '=', 'ap.instid')
            ->where('inst.statusflag', 'Y')
            ->where('dept.statusflag', 'Y')
            ->where('region.statusflag', 'Y')
            ->where('dist.statusflag', 'Y');

        if (!empty($auditteamid)) {
            $data->where('atd.auditteamsdraftid', $auditteamid);
        }

        $data = $data->select(
            DB::raw("CASE
            WHEN atd.fileuploadid != 0 THEN CONCAT(fu.filename, ' - ', fu.filepath, ' - ', fu.filesize, ' - ', fu.fileuploadid)
            ELSE ' - '
        END AS filedetails"),
            'atd.*',
            'inst.deptcode',
            'inst.instename',
            'dept.deptelname',
            'region.regionename',
            'inst.regioncode',
            'inst.distcode',
            'ap.auditplanid',
            'auditteamsdraftid',
            'dist.distename',
            DB::raw("oldhead.username || ' - ' || olddes.desigelname || ' - ' || oldhead.deptuserid AS oldteamheadname"),
            DB::raw("newhead.username || ' - ' || newdes.desigelname || ' - ' || newhead.deptuserid AS newteamheadname"),
            'member_counts.membercount',

            // Safely handle the JSONB fields using a CASE WHEN for valid JSONB arrays
            DB::raw("(SELECT string_agg(dud.username || ' - ' || des.desigelname || ' - ' || dud.deptuserid, ', ')
                  FROM jsonb_array_elements_text(CASE
                      WHEN jsonb_typeof(atd.oldteammembers) = 'array' THEN atd.oldteammembers
                      ELSE '[]'::jsonb
                  END) AS memberid
                  JOIN audit.deptuserdetails AS dud ON dud.deptuserid = memberid::int
                  JOIN audit.mst_designation AS des ON des.desigcode = dud.desigcode) AS oldteammembernames"),

            DB::raw("(SELECT string_agg(dud.username || ' - ' || des.desigelname || ' - ' || dud.deptuserid, ', ')
                  FROM jsonb_array_elements_text(CASE
                      WHEN jsonb_typeof(atd.newteammembers) = 'array' THEN atd.newteammembers
                      ELSE '[]'::jsonb
                  END) AS memberid
                  JOIN audit.deptuserdetails AS dud ON dud.deptuserid = memberid::int
                  JOIN audit.mst_designation AS des ON des.desigcode = dud.desigcode) AS newteammembernames"))
                  ->orderBy('dept.deptelname', 'asc')
        	  ->get();

        return $data;
    }

    public static function getAuditorsfromplan($deptcode, $distcode, $quartercode)
    {

        // return $quartercode;
        try {
            $column = 'inst.' . $quartercode;


            if (empty($deptcode)) {
                throw new Exception("Deptcode is not available");
            }
            if (empty($distcode)) {
                throw new Exception("distcode is not available");
            }
            $query = DB::table(self::$auditplan_table . ' as ap')
                ->join(self::$institution_table . ' as inst', 'inst.instid', '=', 'ap.instid')
                ->join(self::$auditplanteam_table . ' as at', 'at.auditplanteamid', '=', 'ap.auditteamid')
                ->join(self::$auditplanteammem_table . ' as mem', 'mem.auditplanteamid', '=', 'ap.auditteamid')
                ->join(self::$userdetail_table . ' as du', 'mem.userid', '=', 'du.deptuserid')
                ->join(self::$designation_table . ' as dd', 'dd.desigcode', '=', 'du.desigcode')
                ->select(
                    'ap.fromdate',
                    'ap.todate',
                    'ap.instid',
                    'inst.instename',
                    'inst.insttname',
                    'inst.mandays',
                    'ap.auditplanid',
                    'ap.auditquartercode',
                    'inst.spillover',
                    'inst.teamsize',
                    'inst.remainingmandays',
		    'ap.spilloverflag',

                    DB::raw("(
            SELECT head.usertamilname || ' - ' || desig.desigtlname
            FROM audit.auditplanteammember AS head_tm
            JOIN audit.deptuserdetails AS head ON head.deptuserid = head_tm.userid
            JOIN audit.mst_designation AS desig ON desig.desigcode = head.desigcode
            WHERE head_tm.auditplanteamid = ap.auditteamid AND head_tm.teamhead = 'Y' AND head_tm.statusflag='Y'
            LIMIT 1
        ) AS team_head_ta"),
                    DB::raw("(
            SELECT COALESCE(STRING_AGG(member.usertamilname || ' - ' || desig2.desigtlname, ', '), '')
            FROM audit.auditplanteammember AS member_tm
            JOIN audit.deptuserdetails AS member ON member.deptuserid = member_tm.userid
            JOIN audit.mst_designation AS desig2 ON desig2.desigcode = member.desigcode
            WHERE member_tm.auditplanteamid = ap.auditteamid AND member_tm.teamhead != 'Y' AND member_tm.statusflag='Y'
        ) AS team_members_ta"),
                    DB::raw("(
            SELECT head.username || ' - ' || desig.desigelname
            FROM audit.auditplanteammember AS head_tm
            JOIN audit.deptuserdetails AS head ON head.deptuserid = head_tm.userid
            JOIN audit.mst_designation AS desig ON desig.desigcode = head.desigcode
            WHERE head_tm.auditplanteamid = ap.auditteamid AND head_tm.teamhead = 'Y' AND head_tm.statusflag='Y'
            LIMIT 1
        ) AS team_head_en"),
                    DB::raw("(
            SELECT COALESCE(STRING_AGG(member.username || ' - ' || desig2.desigelname, ', '), '')
            FROM audit.auditplanteammember AS member_tm
            JOIN audit.deptuserdetails AS member ON member.deptuserid = member_tm.userid
            JOIN audit.mst_designation AS desig2 ON desig2.desigcode = member.desigcode
            WHERE member_tm.auditplanteamid = ap.auditteamid AND member_tm.teamhead != 'Y' AND member_tm.statusflag='Y'
        ) AS team_members_en")
                )
                ->where('inst.deptcode', $deptcode)
                ->where('inst.distcode', $distcode)
                ->where('ap.auditquartercode', $quartercode)
                ->where('mem.statusflag', 'Y')
                ->where('ap.statusflag', 'F')
                ->orderBy('ap.auditplanid', 'asc')

                ->get();


            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            \Log::error('SQL Error: ' . $customMessage);
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function checkexitmeetstatus($deptcode, $distcode)
    {
        try {
            if (empty($deptcode)) {
                throw new Exception("Deptcode is not available");
            }
            if (empty($distcode)) {
                throw new Exception("distcode is not available");
            }

            $quarterdet = MastersModel::getoldandnewquarter($deptcode);

            $currentquarter = $quarterdet[0]->currentquarter;



            $query = DB::table(self::$institution_table . ' as inst')
                ->join(self::$auditplan_table . ' as plan', 'inst.instid', '=', 'plan.instid')
                ->join(self::$instauditschedule_table . ' as schd', 'plan.auditplanid', '=', 'schd.auditplanid')
                ->where('inst.deptcode', $deptcode)
                ->where('inst.distcode', $distcode)
                ->where('plan.auditquartercode', $currentquarter)
                ->whereNull('schd.exitmeetdate')
 		->whereNotNull('schd.entrymeetdate')
                ->exists();
            // ->where('inst.' . $currentquarter, 'Y')
            //->join(self::$auditplan_table . ' as plan', 'inst.instid', '=', 'plan.instid')
            //   ->get();
            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while finalising. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {


            // Optionally, you can log the error or handle it accordingly
            Log::error("Error in executing finaliseplan_function: " . $e->getMessage());
            throw new \Exception($e->getMessage(), 409);
        }
    }

public static function backupOldYearMapping($auditplanid)
{
    $oldData = DB::table('audit.yearcode_mapping')
                ->where('auditplanid', $auditplanid)
                ->where('statusflag', 'Y')
                ->get();

    if ($oldData->isEmpty()) {
        return; 
    }

    $yearselectedCombined = [];

    foreach ($oldData as $item) {
        $years = array_map('strval', explode(',', trim($item->yearselected, '{}')));
        $yearselectedCombined = array_merge($yearselectedCombined, $years);
    }

    $yearselectedCombined = array_values(array_unique($yearselectedCombined));

    $first = $oldData->first();

    DB::table('audit.yearcode_mappinghistory')->insert([
        'auditplanid'     => $auditplanid,
        'createdby'       => $first->createdby,
        'createdon'       => $first->createdon,
        'updatedon'       => $first->updatedon,
        'yearselected'    => json_encode($yearselectedCombined), 
        'statusflag'      => $first->statusflag,
        'financestatus'   => $first->financestatus,
    ]);
}

    

   
    public static function changerequests_insertupdate(array $data, $auditplanid, $table)
    {
        try {



        self::backupOldYearMapping($auditplanid);

        $yeararr = array_unique($data['yearselected'] ?? []);  

        $existingMappingarr = YearcodeMapping::fetchYearmapById($auditplanid,'N');
        $yearSelectedArr = $existingMappingarr->pluck('yearselected')->toArray();

        $findNewArr = array_diff($yeararr, $yearSelectedArr);
        $removeExistArr = array_diff($yearSelectedArr, $yeararr);



        if ($data['updatefield'] === '02') {
            $record = DB::table('audit.inst_auditschedule')
            ->where('auditplanid', $auditplanid)
            ->select('workallocationflag')
            ->first();

            if ($record && $record->workallocationflag === 'Y') {
                throw new \Exception("Workflagvalidation");
            }

            DB::table('audit.inst_auditschedule')
                ->where('auditplanid', $auditplanid)
                ->update([
                    'fromdate' => null,
                    'todate' => null,
		    'statusflag'   => 'Y',
                    'updatedon' => View::shared('get_nowtime'),
                    'updatedby' => $data['updatedby']
            ]);


            DB::table('audit.yearcode_mapping')
                    ->where('auditplanid',   $auditplanid)
                ->update([
                'yearselected' => null             
            ]);

            $auditschedule = DB::table('audit.inst_auditschedule')
            ->where('auditplanid', $auditplanid)
            ->select('auditscheduleid')
            ->first();
            
            if ($auditschedule) {
                DB::table('audit.selected_cfr')
                    ->where('auditscheduleid', $auditschedule->auditscheduleid)
                    ->update([
                        'statusflag' => 'N',
                        'updatedon' => View::shared('get_nowtime'),
                        'updatedby' => $data['updatedby']
                ]);
            }
        }

        $message = null;
        $hasUpdated = false;


        if (sizeof($findNewArr) > 0) {
           self::updateyearcodemappings($findNewArr, $auditplanid,'','N');
           $hasUpdated = true;

        }
        
        if (sizeof($removeExistArr) > 0) {
           self::updateyearcodemappings($removeExistArr, $auditplanid, 'Updatestatusflag','N');
           $hasUpdated = true;

        }
        if (!$message && $data['updatefield'] === '02') {
            $message = 'Schedule Period has been reset to null. Assign new dates.';
        } elseif (!$message && $data['updatefield'] === '01') {
            $message = 'Audit year has been updated successfully.';
        } 
        
        return $message;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
	


    public static function updateyearcodemappings(array $data, $currentUserId, $statusflagupdate = '',$financestatus='')
    {
      //  return $statusflagupdate;
      
        if ($statusflagupdate == 'Updatestatusflag') {
         //   return 'entered';
            foreach ($data as $YearVal) {
                $yearmapping = YearcodeMapping::where('auditplanid', $currentUserId)
                    ->where('yearselected', $YearVal)
                    ->where('statusflag', 'Y')
                    ->first();
                if ($yearmapping) {
                
                    YearcodeMapping::where('auditplanid', $currentUserId)
                        ->where('yearselected', $YearVal)
                        ->where('statusflag', 'Y')
                        ->update(['statusflag' => 'N','financestatus'=>$financestatus]);

                        DB::table('audit.inst_auditschedule')
                        ->where('auditplanid', $currentUserId)
                        ->update(['updatedon' => now()]);
                }
            }
        } else {
          //  return 'add';
            foreach ($data as $YearVal) {
                //return 'asd';
                $yearmapping = YearcodeMapping::where('auditplanid', $currentUserId)
                    ->where('yearselected', $YearVal)
                    ->where('statusflag', 'Y')
                    ->first();
                if ($yearmapping) {
                    $yearmapping->update(['yearselected' => $YearVal,'financestatus'=>$financestatus]);
                } else {
                    YearcodeMapping::create([
                        'auditplanid' => $currentUserId,
                        'yearselected' => $YearVal,
                        'createdby' => $currentUserId,
                        'statusflag' => 'Y',
                        'financestatus'=>$financestatus
                    ]);
                }
                DB::table('audit.inst_auditschedule')
                ->where('auditplanid', $currentUserId)
                ->update(['updatedon' => now()]);
            }
        }
    }





      


     public static function changerequestfetchData($auditplanid = null, $table = null)
     {

        
         $table = 'audit.auditplan';
     
         $auditPeriodSubquery = DB::table('audit.yearcode_mapping as ycm')
         ->join('audit.mst_auditperiod as ap', 'ap.auditperiodid', '=', 'ycm.yearselected')
         ->select(
             'ycm.auditplanid',
             DB::raw("string_agg(CONCAT(fromyear, ' - ', toyear), ', ' ORDER BY fromyear) as audit_period"),
             DB::raw("string_agg(yearselected::text, ', ') as yearkeys")
         )
         ->where('ap.financestatus', 'N')
         ->where('ycm.statusflag', 'Y')
         ->where('ap.statusflag', 'Y')
         ->groupBy('ycm.auditplanid');


     
     
     
     $query = DB::table($table . ' as plan')  
         ->join('audit.inst_auditschedule as sch', 'sch.auditplanid', '=', 'plan.auditplanid')
         ->join(self::$institution_table . ' as ins', 'plan.instid', '=', 'ins.instid')
         ->join(self::$deptartment_table . ' as dept', 'ins.deptcode', '=', 'dept.deptcode')
         //->join('audit.mst_auditquarter' . ' as qua', 'ins.audit_quarter', '=', 'qua.auditquartercode')  
         ->join(self::$regionTable . ' as reg', 'ins.regioncode', '=', 'reg.regioncode')
         ->join(self::$dist_table . ' as dis', 'ins.distcode', '=', 'dis.distcode')
         ->join('audit.mst_auditquarter as qua', function ($join) {
            $join->on('qua.auditquartercode', '=', 'ins.audit_quarter')
                ->on('qua.deptcode', '=', 'ins.deptcode');
        })
         ->leftJoinSub($auditPeriodSubquery, 'aps', function ($join) {
             $join->on('aps.auditplanid', '=', 'plan.auditplanid');
         })
         ->select(
             'ins.instid',
             'ins.instename',
             'ins.insttname',
             'reg.regioncode',
             'reg.regionename',
             'reg.regiontname',
             'dis.distename',
             'dis.disttname',
             'dis.distcode',
             'dept.deptcode',
             'dept.deptesname',
             'dept.deptelname',
             'sch.fromdate',
             'sch.todate',
             'dept.depttsname',
             'dept.depttlname',
             'qua.auditquartercode',
             'qua.auditquarter',
             'plan.auditplanid',
             'aps.audit_period',
             'aps.yearkeys'
         )
         ->where('plan.statusflag', 'F')
         ->where('sch.statusflag', 'F')
        ->where(function($query) {
            $query->whereNull('sch.auditeeresponse')
                  ->orWhere('sch.auditeeresponse', '');
        }) 
         ->when($auditplanid, function ($query) use ($auditplanid) {
             $query->where('sch.auditplanid', $auditplanid);
         })
        ->orderBy('sch.updatedon', 'desc');


     
         $mainResults = $query->get();
     
         return $mainResults;
     }
     
  



    

    public static function commonquarterfetch($deptcode,$instmappingcode){
        return DB::table('audit.auditplan as plan')
            ->join('audit.mst_auditquarter' . ' as qua', 'plan.auditquartercode', '=', 'qua.auditquartercode')
            ->join(self::$deptartment_table . ' as dept', 'qua.deptcode', '=', 'dept.deptcode') 
             ->select('qua.auditquarter', 'qua.auditquartercode')
             ->where('dept.deptcode', $deptcode) 
             ->where('plan.instid', $instmappingcode) 
             ->where('qua.statusflag', '=', 'Y') 
             ->orderBy('qua.auditquarter', 'asc')
             ->get();
    }


    public static function commondeptfetch()
     {
         return DB::table(self::$deptartment_table . ' as dept')
             ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname') // Select required columns
             ->where('dept.statusflag', '=', 'Y') // Use the correct table alias for `statusflag`
             ->orderBy('dept.deptcode', 'asc')
             ->get();
     }


     public static function Auditperiodcompactfetch($deptcode)
     {
         return DB::table('audit.mst_auditperiod')
             ->select('auditperiodid', DB::raw("CONCAT(fromyear, ' - ', toyear) AS audit_period"))
             ->where('deptcode', $deptcode)
             ->where('statusflag', 'Y')
             ->where('financestatus', 'N')
             ->orderBy('fromyear', 'desc')
             ->get();
     }




   

     public static function Auditperiodfetch($instmappingcode)
     {
        $table = 'audit.auditplan';
    
        $auditPeriodSubquery = DB::table('audit.yearcode_mapping as ycm')
        ->join('audit.mst_auditperiod as ap', 'ap.auditperiodid', '=', 'ycm.yearselected')
        ->select(
            'ycm.auditplanid',
            'ycm.yearselected',
            DB::raw("CONCAT(fromyear, ' - ', toyear) as audit_period")
        )
        ->where('ap.financestatus', 'N')
        ->where('ycm.statusflag', 'Y')
        ->where('ap.statusflag', 'Y');
    
    
    
        $query = DB::table($table . ' as plan')  
        ->join('audit.inst_auditschedule as sch', 'sch.auditplanid', '=', 'plan.auditplanid')
        ->leftJoinSub($auditPeriodSubquery, 'aps', function ($join) {
            $join->on('aps.auditplanid', '=', 'plan.auditplanid');
        })
        ->select(
            'plan.auditplanid',
            'aps.audit_period',
            'aps.yearselected',
            'sch.fromdate',
            'sch.todate'
        )
         ->where('plan.instid', $instmappingcode)
        ->where('plan.statusflag', 'F')
        ->where('sch.statusflag', 'F');
    
      
            //->orderBy('ycm.updatedon', 'desc');
    
        $mainResults = $query->get();
    
    
        return $mainResults;
     }
     

public static function getinstitutionBydistrictchange($district, $regioncode, $deptcode,$updatefield = null)
{
    $table = self::$institution_table;

    return DB::table($table . ' as ins')
        ->join('audit.auditplan' . ' as plan', 'ins.instid', '=', 'plan.instid')
        ->join('audit.inst_auditschedule' . ' as sch', 'sch.auditplanid', '=', 'plan.auditplanid')
        ->select('ins.instename', 'ins.instid','ins.insttname')
        ->distinct()
        ->where('ins.distcode', $district)
        ->where('ins.deptcode', $deptcode)
        ->where('ins.regioncode', $regioncode)
        ->when($updatefield === '01'|| $updatefield === '02', function ($query) {
            return $query->where('sch.auditeeresponse', 'A');
        }, function ($query) {
            return $query->where(function ($q) {
                $q->whereNull('sch.auditeeresponse')
                  ->orWhere('sch.auditeeresponse', '');
            });
        })      
         ->where('plan.statusflag', 'F')
        ->get();
}

public static function getRegionsByDept($deptcode)
{
    $table = self::$institution_table;

    return DB::table($table . ' as ins')
        ->join(self::$regionTable . ' as reg', 'ins.regioncode', '=', 'reg.regioncode')
        ->select('reg.regioncode', 'reg.regionename','reg.regiontname')
        ->distinct()
        ->where('ins.deptcode', $deptcode)
        ->where('ins.statusflag', 'Y')
        ->orderBy('reg.regionename', 'Asc')
        ->get();
}

public static function getdistrictByregion($regioncode, $deptcode)
{
    $table = self::$institution_table;

    return DB::table($table . ' as ins')
        ->join(self::$dist_table . ' as dis', 'ins.distcode', '=', 'dis.distcode')
        ->select('dis.distename', 'dis.distcode','dis.disttname')
        ->distinct()
        ->where('ins.deptcode', $deptcode)
        ->where('ins.regioncode', $regioncode)
        ->where('ins.statusflag', 'Y')
        ->get();
}

    //-------------------------------------------Manual Plan Start----------------------------

 
    public static function fetchUpdatepManualplan($auditteamid = null)
    {
        $query = DB::table(self::$auditplan_table . ' as plan')
            ->join(self::$auditplanteam_table . ' as team', 'team.auditplanteamid', '=', 'plan.auditteamid')
            ->join(self::$auditplanteammem_table . ' as mem', 'team.auditplanteamid', '=', 'mem.auditplanteamid')
            ->join(self::$institution_table . ' as inst', 'inst.instid', '=', 'plan.instid')
            ->join(self::$deptartment_table . ' as dept', 'inst.deptcode', '=', 'dept.deptcode')
            ->join(self::$regionTable . ' as region', 'inst.regioncode', '=', 'region.regioncode')
            ->join(self::$dist_table . ' as dist', 'inst.distcode', '=', 'dist.distcode')
            ->join(self::$userdetail_table . ' as du', 'du.deptuserid', '=', 'mem.userid')
            ->join(self::$designation_table . ' as desig', 'desig.desigcode', '=', 'du.desigcode')
            ->where('inst.statusflag', 'Y')
            ->where('dept.statusflag', 'Y')
            ->where('region.statusflag', 'Y')
            ->where('dist.statusflag', 'Y')
            ->where('plan.manualplan', 'Y')
            ->whereColumn('plan.auditquartercode', 'dept.currentquarter')
            ->select(
                'inst.deptcode',
                'inst.instid',
                'inst.instename',
                'inst.mandays',
                'inst.teamsize',
                'dept.deptelname',
                'region.regionename',
                'inst.regioncode',
                'inst.distcode',
                'plan.auditplanid',
                'plan.fromdate',
                'plan.todate',
                'team.auditplanteamid',
                'dist.distename',
                'plan.statusflag',
                DB::raw("(
                    SELECT head.username || ' - ' || head_desig.desigelname|| ' - ' || head.deptuserid  || ' - ' || head_desig.desigcode
                    FROM audit.auditplanteammember AS head_tm
                    JOIN audit.deptuserdetails AS head ON head.deptuserid = head_tm.userid
                    JOIN audit.mst_designation AS head_desig ON head_desig.desigcode = head.desigcode
                    WHERE head_tm.auditplanteamid = team.auditplanteamid 
                      AND head_tm.teamhead = 'Y' 
                      AND head_tm.statusflag = 'Y'
                    LIMIT 1
                ) AS newteamheadname"),
                DB::raw("(
                    SELECT json_agg(DISTINCT tm.userid)
                    FROM audit.auditplanteammember AS tm
                    WHERE tm.auditplanteamid = team.auditplanteamid
                      AND tm.statusflag = 'Y'
                      AND tm.teamhead = 'N'
                ) AS newteammembers"),
                DB::raw("(
                    SELECT json_agg(DISTINCT tm.userid)
                    FROM audit.auditplanteammember AS tm
                    WHERE tm.auditplanteamid = team.auditplanteamid
                      AND tm.statusflag = 'Y'
                      AND tm.teamhead = 'Y'
                ) AS newteamhead"),

                DB::raw("(
                    SELECT STRING_AGG(DISTINCT member.username || ' - ' || member_desig.desigelname || ' - ' || member.deptuserid  || ' - ' ||member_desig.desigcode, ', ')
                    FROM audit.auditplanteammember AS tm
                    JOIN audit.deptuserdetails AS member ON member.deptuserid = tm.userid
                    JOIN audit.mst_designation AS member_desig ON member_desig.desigcode = member.desigcode
                    WHERE tm.auditplanteamid = team.auditplanteamid
                      AND tm.statusflag = 'Y'
                      AND tm.teamhead = 'N'
                ) AS newteammembernames")




            );


        if (!empty($auditteamid)) {
            $query->where('team.auditplanteamid', $auditteamid);
        }

        $query->groupBy(
            'inst.deptcode',
            'inst.instename',
            'dept.deptelname',
            'region.regionename',
            'inst.regioncode',
            'inst.distcode',
            'plan.auditplanid',
            'team.auditplanteamid',
            'dist.distename',
            'team.statusflag',
            'inst.instid',
        )
            ->orderBy('plan.updatedon', 'desc')
            ->orderBy('dept.deptelname', 'asc');


        // $querySql = $query->toSql();
        // //  return $querySql;
        // $querySql = $query->toSql();
        // $bindings = $query->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );
        // print_r($finalQuery);

        return $query->get();
    }

     public static function fetchExcessinstitution(?string $deptcode = null, ?string $regioncode = null, ?string $distcode = null, $instid)
    {
        try {
            $quarterdetails = self::getquarterdetails($deptcode);
            //    return $quarterdetails;
            $currentquarter = $quarterdetails[0]->auditquartercode;

            $query = DB::table(self::$institution_table . " as inst")
                ->leftJoin(self::$auditplan_table . ' as plan', 'plan.instid', '=', 'inst.instid')
                ->join(self::$deptartment_table . ' as dept', 'dept.deptcode', '=', 'inst.deptcode')
                ->where(function ($q) use ($currentquarter, $instid) {
                    $q->whereNotIn('inst.instid', function ($subquery) use ($currentquarter) {
                        $subquery->select('instid')
                            ->from('audit.auditplan')
                            ->where('auditquartercode', $currentquarter);
                    });

                    if ($instid) {
                        $q->orWhere('inst.instid', $instid);
                    }
                })

                // ->whereNotIn('inst.instid', function ($query)  use ($currentquarter) {
                //     $query->select('instid')
                //         ->from('audit.auditplan')
                //         ->where('auditquartercode', $currentquarter);
                // })
                // ->where(function ($q) use ($instid) {
                //     $q->orWhere(function ($or) use ($instid) {
                //         $or->where('plan.instid', '=', $instid);
                //         //  ->whereNotIn('plan.statusflag', ['S', 'F']);
                //     });
                // })
                ->where("inst.$currentquarter", '=', 'Y')



                //  ->join(self::$mapinst_table . ' as map', 'map.instid', '=', 'inst.instid')
                ->select('inst.instename', 'inst.insttname', 'inst.instid', 'inst.mandays', 'inst.teamsize', 'plan.auditplanid')
                //  ->where('inst.quartercode', view::shared('current_quarter'))
                ->when($deptcode, function ($query) use ($deptcode) {
                    $query->where('inst.deptcode', $deptcode);
                })
                ->when($regioncode, function ($query) use ($regioncode) {
                    $query->where('inst.regioncode', $regioncode);
                })
                ->when($distcode, function ($query) use ($distcode) {
                    $query->where('inst.distcode', $distcode);
                })
                ->distinct();
            // $querySql = $query->toSql();
            // $bindings = $query->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );

            // print_r($finalQuery);
            // exit;


            return $query->get();
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function getAuditors_manualplan($deptcode, $regioncode, $distcode, $auditteamid, $isreservedauditors)
    {
        try {

            if ($auditteamid) {


                $excludedIds = DB::table('audit.auditplanteammember')
                    ->where('auditplanteamid', $auditteamid)
                    ->pluck('userid')
                    ->map(fn($id) => (string)$id)
                    ->toArray();

                $newTeamHead = DB::table('audit.auditplanteammember')
                    ->where('auditplanteamid', $auditteamid)
                    ->where('teamhead', 'Y')
                    ->value('userid');

                if (!is_null($newTeamHead)) {
                    $excludedIds[] = (string)$newTeamHead;
                }

                $excludedIds = array_unique($excludedIds); // deduplicate


                $data = DB::table('audit.auditplan as ap')
                    ->join('audit.auditplanteammember as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
                    ->join('audit.deptuserdetails as dept', 'dept.deptuserid', '=', 'apt.userid')
                    ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'dept.desigcode')
                    ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'dept.deptuserid')
                    ->join('audit.chargedetails as ch', 'ch.chargeid', '=', 'uc.chargeid')
                    ->join('audit.rolemapping as rol', 'rol.rolemappingid', '=', 'ch.rolemappingid')
                    ->join('audit.mst_dept as de', 'de.deptcode', '=', 'dept.deptcode')

                    ->whereColumn('ap.auditquartercode', 'de.currentquarter')
                    ->where('dept.deptcode', $deptcode)
                    ->where('dept.distcode', $distcode)
                    ->where('dept.reservelist', 'Y')
                    ->where('dept.statusflag', 'Y')
                    ->where('desig.belowaddesig', 'Y')
                    ->when(!empty($excludedIds), function ($query) use ($excludedIds) {
                        $query->whereNotIn('apt.userid', $excludedIds);
                    })
                    ->where('rol.roleactioncode', '04')
                    ->groupBy(
                        'apt.userid',
                        'dept.deptuserid',
                        'dept.username',
                        'desig.desigelname',
                        'desig.desigcode',

                        'de.currentquartertodatewithcoolingperiod'
                    )
                    ->havingRaw('MAX(ap.todate) < de.currentquartertodatewithcoolingperiod')
                    ->selectRaw('
                   DISTINCT on (apt.userid) apt.userid,
                    dept.username,
                    desig.desigcode,
                     dept.deptuserid,
                    desig.desigelname
   
                        ')
                    ->orderBy('apt.userid')
                    ->orderBy('dept.deptuserid')
                    ->orderBy('dept.username', 'ASC')

                    ->get();


                return $data;

                // print_r($data);

                // exit;

            } else {
                $data = DB::table('audit.auditplan as ap')
                    ->join('audit.auditplanteammember as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
                    ->join('audit.deptuserdetails as dept', 'dept.deptuserid', '=', 'apt.userid')
                    ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'dept.desigcode')
                    ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'dept.deptuserid')
                    ->join('audit.chargedetails as ch', 'ch.chargeid', '=', 'uc.chargeid')
                    ->join('audit.rolemapping as rol', 'rol.rolemappingid', '=', 'ch.rolemappingid')
                    ->join('audit.mst_dept as de', 'de.deptcode', '=', 'dept.deptcode')
                    ->whereColumn('ap.auditquartercode', 'de.currentquarter')
                    ->where('dept.deptcode', $deptcode)
                    ->where('dept.distcode', $distcode)
                    ->where('dept.reservelist', 'Y')
                    ->where('dept.statusflag', 'Y')
                    ->where('desig.belowaddesig', 'Y')

                    ->where('rol.roleactioncode', '04')
                    ->groupBy(
                        'apt.userid',
                        'dept.username',
                        'desig.desigelname',
                        'desig.desigcode',
                        'dept.deptuserid',
                        'de.currentquartertodatewithcoolingperiod'
                    )
                    ->havingRaw('MAX(ap.todate) < de.currentquartertodatewithcoolingperiod')
                    ->selectRaw('
                     DISTINCT on (apt.userid)
                     apt.userid,
                     dept.deptuserid,
                    dept.username,
                     desig.desigcode,
                    desig.desigelname
   
                        ');

                // $querySql = $data->toSql();
                // $bindings = $data->getBindings();

                // $finalQuery = vsprintf(
                //     str_replace('?', "'%s'", $querySql),
                //     array_map('addslashes', $bindings)
                // );

                // print_r($finalQuery);
                // exit;


                $dataquery = $data->get();

                if ($isreservedauditors == 'Y') {

                    $reserveData =  DB::table('audit.deptuserdetails as dp')
                        ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'dp.deptuserid')
                        ->join('audit.chargedetails as cd', 'cd.chargeid', '=', 'uc.chargeid')
                        ->join('audit.rolemapping as rol', 'rol.rolemappingid', '=', 'cd.rolemappingid')
                        ->join('audit.mst_designation as de', 'de.desigcode', '=', 'cd.desigcode')
                        ->join('audit.mst_district as md', 'md.distcode', '=', 'dp.distcode')
                        ->where('uc.statusflag', '=', 'Y')
                        ->where('dp.statusflag', '=', 'Y')
                        ->where('dp.deptcode',  $deptcode)
                        ->where('de.deptcode',  $deptcode)
                        ->where('cd.regioncode', $regioncode)
                        ->where('dp.distcode',  $distcode)
                        ->where('dp.reservelist',  'N')
                        ->where('rol.roleactioncode', '04')
                        ->select(
                            'dp.deptuserid',
                            'dp.username',
                            'cd.chargedescription',
                            'md.distename',
                            'md.disttname',
                            'uc.userid',
                            'de.desigelname',
                            'de.desigcode',
                            'de.desigtlname'

                        )
                        ->orderBy('de.orderid')
                        ->orderBy('dp.username')
                        ->orderBy('dp.username')
                        ->get();
                    $merged = $dataquery->merge($reserveData);
                    return $merged->values();
                }
                return $dataquery;
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetchings. Please contact the administrator.';


            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public function fetchupdatemanualplan($auditteamid = null)
    {
        $data = DB::table('audit.audit_teams_draft as atd')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'atd.auditplanid')
            ->join('audit.auditplanteam as apt', 'apt.auditplanteamid', '=', 'ap.auditteamid')
            ->join('audit.mst_institution as inst', 'ap.instid', '=', 'inst.instid')
            ->join('audit.mst_dept as dept', 'inst.deptcode', '=', 'dept.deptcode')
            ->join('audit.mst_region as region', 'inst.regioncode', '=', 'region.regioncode')
            ->join('audit.mst_district as dist', 'inst.distcode', '=', 'dist.distcode')
            ->join('audit.deptuserdetails as oldhead', 'oldhead.deptuserid', '=', 'atd.oldteamhead')
            ->join('audit.mst_designation as olddes', 'olddes.desigcode', '=', 'oldhead.desigcode')
            ->join('audit.deptuserdetails as newhead', 'newhead.deptuserid', '=', 'atd.newteamhead')
            ->join('audit.mst_designation as newdes', 'newdes.desigcode', '=', 'newhead.desigcode')
            ->leftJoin('audit.fileuploaddetail as fu', 'fu.fileuploadid', '=', 'atd.fileuploadid')
            ->leftJoin(DB::raw("(SELECT mid.instid, COUNT(*) AS membercount
            FROM audit.map_instdesig AS mid
            JOIN audit.auditplan AS ap ON ap.instid = mid.instid
            WHERE mid.teamhead = 'N'
            GROUP BY mid.instid) AS member_counts"), 'member_counts.instid', '=', 'ap.instid')
            ->where('inst.statusflag', 'Y')
            ->where('dept.statusflag', 'Y')
            ->where('region.statusflag', 'Y')
            ->where('dist.statusflag', 'Y');

        if (!empty($auditteamid)) {
            $data->where('atd.auditteamsdraftid', $auditteamid);
        }

        $data = $data->select(
            DB::raw("CASE
            WHEN atd.fileuploadid != 0 THEN CONCAT(fu.filename, ' - ', fu.filepath, ' - ', fu.filesize, ' - ', fu.fileuploadid)
            ELSE ' - '
        END AS filedetails"),
            'atd.*',
            'inst.deptcode',
            'inst.instename',
            'dept.deptelname',
            'region.regionename',
            'inst.regioncode',
            'inst.distcode',
            'ap.auditplanid',
            'auditteamsdraftid',
            'dist.distename',
            DB::raw("oldhead.username || ' - ' || olddes.desigelname || ' - ' || oldhead.deptuserid AS oldteamheadname"),
            DB::raw("newhead.username || ' - ' || newdes.desigelname || ' - ' || newhead.deptuserid AS newteamheadname"),
            'member_counts.membercount',

            // Safely handle the JSONB fields using a CASE WHEN for valid JSONB arrays
            DB::raw("(SELECT string_agg(dud.username || ' - ' || des.desigelname || ' - ' || dud.deptuserid || ' - '|| des.desigcode  , ', ')
                  FROM jsonb_array_elements_text(CASE
                      WHEN jsonb_typeof(atd.oldteammembers) = 'array' THEN atd.oldteammembers
                      ELSE '[]'::jsonb
                  END) AS memberid
                  JOIN audit.deptuserdetails AS dud ON dud.deptuserid = memberid::int
                  JOIN audit.mst_designation AS des ON des.desigcode = dud.desigcode) AS oldteammembernames"),

            DB::raw("(SELECT string_agg(dud.username || ' - ' || des.desigelname || ' - ' || dud.deptuserid || ' - '|| des.desigcode , ', ')
                  FROM jsonb_array_elements_text(CASE
                      WHEN jsonb_typeof(atd.newteammembers) = 'array' THEN atd.newteammembers
                      ELSE '[]'::jsonb
                  END) AS memberid
                  JOIN audit.deptuserdetails AS dud ON dud.deptuserid = memberid::int
                  JOIN audit.mst_designation AS des ON des.desigcode = dud.desigcode) AS newteammembernames")
        )
            ->orderBy('dept.deptelname', 'asc')
            ->get();

        return $data;
    }

    public static function updatemanualplan(array $data, $auditteamId = null, $auditplanid)
    {
        try {
            $session = session('user');
            $userid = $session->userid;

            //  $auditplanid = $data['auditplanid'];
            $finaliseflag = $data['statusflag'];


            //  return $data;
            // $quoted = array_map(fn($v) => '"' . $v . '"', $data['newteammembers']);
            // $datatypes = '{' . implode(',', $quoted) . '}';
            //return $data['newteammembers'];
            $manualplanstatus = $query = DB::select(
                'SELECT * FROM audit.manualplan' . '(:deptcode,:regioncode, :distcode, :instid,:newteamhead,:newteammembers,:planid,:userid,:formparam)',
                [

                    'deptcode'       => $data['deptcode'],
                    'regioncode'       => $data['regioncode'],
                    'distcode'       => $data['distcode'],
                    'instid'       => $data['instid'],
                    'newteamhead'       => $data['newteamhead'],
                    'newteammembers'       => $data['newteammembers'],
                    'planid'        => $auditplanid,
                    'userid'   => $userid,
                    'formparam'       => $data['formparam'],
                ]
            );
            return  $manualplanstatus;
        } catch (\Exception $e) {
            DB::rollBack(); // ? ROLLBACK on exception


            return [
                'status' => false,
                'type' => 'error',
                'message' => 'Update failed: ' . $e->getMessage()
            ];
        }
    }


    //-------------------------------------------Manual Plan End----------------------------


    //-------------------------------------------Quarter Transaction Start----------------------------


    public static function getnotscheduleinstData()
    {
        $sessionchargedel = session('charge');
        $deptcode   = $sessionchargedel->deptcode ?? null;
        $regioncode = $sessionchargedel->regioncode ?? null;
        $distcode   = $sessionchargedel->distcode ?? null;

        $currecntQuarterdel = DB::table('audit.mst_dept')
            ->where('deptcode', $deptcode)
            ->select('currentquarter', 'nextquarter')
            ->get();



        $currecntQuarterFromDept = $currecntQuarterdel[0]->currentquarter;
        $nextQuarterFromDept = $currecntQuarterdel[0]->nextquarter;






        $query = DB::table('audit.auditplan as ap')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
            ->whereNull("inst.$nextQuarterFromDept")
            ->leftJoin('audit.inst_auditschedule as asch', 'asch.auditplanid', '=', 'ap.auditplanid')
            ->join('audit.mst_dept as de', 'de.deptcode', '=', 'inst.deptcode') // ✅ Fixed join
            ->select(
                'ap.*',
                'de.currentquarter',
                'inst.instename',
                'inst.mandays',
                DB::raw("(SELECT newquartercode FROM audit.temp_inst_q1_pending WHERE instid = inst.instid) as newquartercode")
            )
            ->where(function ($q) use ($deptcode, $regioncode, $distcode, $currecntQuarterFromDept) {
                $q->whereNotIn('ap.auditplanid', function ($sub) use ($deptcode, $regioncode, $distcode) {
                    $sub->select('ap.auditplanid')
                        ->from('audit.inst_auditschedule as asch')
                        ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'asch.auditplanid')
                        ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
                        ->where('inst.deptcode', $deptcode)
                        ->where('inst.regioncode', $regioncode)
                        ->where('inst.distcode', $distcode);
                })
                    ->where('inst.deptcode', $deptcode)
                    ->where('inst.regioncode', $regioncode)
                    ->where('inst.distcode', $distcode)
                    ->where("inst.$currecntQuarterFromDept", 'Y');
            })
             ->orWhere(function ($q) use ($deptcode, $regioncode, $distcode, $currecntQuarterFromDept) {
                $q->where(function ($inner) {
                    $inner->where('asch.statusflag', 'Y')
                        ->orWhere(function ($inner2) {
                            $inner2->where('asch.statusflag', 'F')
                                ->where('workallocationflag', 'N');
                        })
                         ->orWhere(function ($inner2) {
                            $inner2->where('asch.statusflag', 'F')
                                ->where('workallocationflag', 'Y')
                                ->whereNull('entrymeetdate'); // fixed here

                        });
                })
                    ->where('inst.deptcode', $deptcode)
                    ->where('inst.regioncode', $regioncode)
                    ->where('inst.distcode', $distcode)
                    ->where("inst.$currecntQuarterFromDept", 'Y');
            });
        // ->get();
        // $querySql = $query->toSql();
        // $bindings = $query->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );

        // print_r($finalQuery);
        // exit;
        return $query->get();
    }

    public static function getnotscheduleinstCount()
    {
        $sessionchargedel = session('charge');

        $deptcode   = $sessionchargedel->deptcode ?? null;
        $regioncode = $sessionchargedel->regioncode ?? null;
        $distcode   = $sessionchargedel->distcode ?? null;

        if (!$deptcode || !$regioncode || !$distcode) {
            return 0;
        }
        $currecntQuarterdel = DB::table('audit.mst_dept')
            ->where('deptcode', $deptcode)
            ->select('currentquarter', 'nextquarter')
            ->get();



        $currecntQuarterFromDept = $currecntQuarterdel[0]->currentquarter;
        $nextQuarterFromDept = $currecntQuarterdel[0]->nextquarter;

        $query = DB::table('audit.auditplan as ap')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
            ->whereNull("inst.$nextQuarterFromDept")
            ->leftJoin('audit.inst_auditschedule as asch', 'asch.auditplanid', '=', 'ap.auditplanid')
            ->where(function ($query) use ($deptcode, $regioncode, $distcode, $currecntQuarterFromDept) {
                $subQuery = DB::table('audit.inst_auditschedule as asch_sub')
                    ->join('audit.auditplan as ap_sub', 'ap_sub.auditplanid', '=', 'asch_sub.auditplanid')
                    ->join('audit.mst_institution as inst_sub', 'inst_sub.instid', '=', 'ap_sub.instid')
                    ->where('inst_sub.deptcode', $deptcode)
                    ->where('inst_sub.regioncode', $regioncode)
                    ->where('inst_sub.distcode', $distcode)
                    ->select('ap_sub.auditplanid');



                $query->whereNotIn('ap.auditplanid', $subQuery)
                    ->where('inst.deptcode', $deptcode)
                    ->where('inst.regioncode', $regioncode)
                    ->where('inst.distcode', $distcode)
                    ->where("inst.$currecntQuarterFromDept", 'Y');
            })
             ->orWhere(function ($q) use ($deptcode, $regioncode, $distcode, $currecntQuarterFromDept) {
                $q->where(function ($inner) {
                    $inner->where('asch.statusflag', 'Y')
                        ->orWhere(function ($inner2) {
                            $inner2->where('asch.statusflag', 'F')
                                ->where('workallocationflag', 'N');
                        })
                         ->orWhere(function ($inner2) {
                            $inner2->where('asch.statusflag', 'F')
                                ->where('workallocationflag', 'Y')
                                ->whereNull('entrymeetdate'); // fixed here

                        });
                })
                    ->where('inst.deptcode', $deptcode)
                    ->where('inst.regioncode', $regioncode)
                    ->where('inst.distcode', $distcode)
                    ->where("inst.$currecntQuarterFromDept", 'Y');
            });

        return $query->distinct('inst.instid')->count('inst.instid');
    }




    public static function getSpilloverInstitutions()
    {
        $sessionchargedel = session('charge');
        $deptcode = $sessionchargedel->deptcode ?? null;
        $distcode = $sessionchargedel->distcode ?? null;

        // Get pendinginststatus from auditor_instmapping table
        $pendingStatus = DB::table('audit.auditor_instmapping')
            ->where('deptcode', $deptcode)
            ->where('distcode', $distcode)
            ->value('pendinginststatus');

        if (is_null($pendingStatus) || $pendingStatus == 'N' || $pendingStatus == '') {
            // If pendinginststatus is NULL, call the stored procedure
            $result = DB::select("SELECT audit.getspilloverinst(?, ?) AS data", [$deptcode, $distcode]);

            if (!empty($result)) {
                return json_decode($result[0]->data);
            }

            return []; // In case result is empty
        } else {
            // If pendinginststatus is not NULL, return existing data from the table
            $record = DB::table('audit.auditor_instmapping')
                ->where('deptcode', $deptcode)
                ->where('distcode', $distcode);


            if ($record && isset($record->data)) {
                return json_decode($record->data);
            }

            return []; // Fallback
        }
    }


    public static function pendinginstcheck()
    {
        $sessionchargedel = session('charge');
        $deptcode = $sessionchargedel->deptcode ?? null;
        $distcode = $sessionchargedel->distcode ?? null;

        $result = DB::table('audit.auditor_instmapping')
            ->where('deptcode', $deptcode)
            ->where('distcode', $distcode)
            ->get(); // <-- added execution

        return $result;
    }



    public static function penidninst_fetchData($tempid = null, $table = null)
{
    if (!$table) {
        throw new \Exception("Table name is required");
    }

    // Get session charge details
    $sessionchargedel = session('charge');
    $deptcode   = $sessionchargedel->deptcode ?? null;
    $regioncode = $sessionchargedel->regioncode ?? null;
    $distcode   = $sessionchargedel->distcode ?? null;

    // 1. Fetch Data
    $dataQuery = DB::table($table . ' as t')
        ->join('audit.mst_institution as ins', 'ins.instid', '=', 't.instid')
        ->select('t.*', 'ins.instename', 'ins.insttname')
        ->orderBy('t.tempid', 'desc');

    if ($tempid !== null) {
        $dataQuery->where('t.tempid', $tempid);
    }

    $data = $dataQuery->get();

    // 2. Get current quarter for the department
    $currecntQuarterFromDept = DB::table('audit.mst_dept')
        ->where('deptcode', $deptcode)
        ->value('currentquarter');

    // 3. Count with properly grouped OR conditions
    $countQuery = DB::table($table . ' as t')
        ->join('audit.mst_institution as ins', 'ins.instid', '=', 't.instid')
       
        ->where('t.spilloverflag', 'N')
        ->where('t.quartercode', $currecntQuarterFromDept)
        ->where('ins.deptcode', $deptcode)
        ->where('ins.regioncode', $regioncode)
        ->where('ins.distcode', $distcode)
        ->where("ins.$currecntQuarterFromDept", 'Y')
       ->whereIn('pendingflag', ['F', 'D'])
         ->distinct();

    $count = $countQuery->count('t.instid');

    return [
        'count' => $count,
        'data'  => $data,
    ];
}


    public static function spilloverdateCheck()
    {
        $sessionchargedel = session('charge');
        $deptcode = $sessionchargedel->deptcode ?? null;
        $distcode = $sessionchargedel->distcode ?? null;

        $result = DB::table('audit.mst_dept')
            ->where('deptcode', $deptcode)
            ->select('spilloverenddate')
            ->get(); // <-- added execution

        return $result;
    }




    public static function getQuarterValue($deptcode, $column = 'currentquarter')
    {
        return DB::table('audit.mst_dept')
            ->where('deptcode', $deptcode)
            ->value($column);
    }


   public static function indexSpillousByInstid($spillous)
    {
        $indexed = [];
        if (is_array($spillous)) {
            foreach ($spillous as $item) {
                if (isset($item['instid'])) {
                    $indexed[(string)$item['instid']] = $item;
                }
            }
        }
        return $indexed;
    }


      public static function updateInstitutionQuarter($instid, $new_quarter, $current_quarter, $userid, $now)
    {
        DB::table('audit.mst_institution')
            ->where('instid', $instid)
            ->update([
                $new_quarter => 'Y',
                $current_quarter => 'N',
                'priority'   => 'B',
                'updatedon'  => $now,
                'updatedby'  => $userid,
            ]);
    }

  public static function deactivateAuditDataByInstid($instid, $userid, $now)
    {
        $scheduleIds = DB::table('audit.inst_auditschedule as asch')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'asch.auditplanid')
            ->where('ap.instid', $instid)
            ->pluck('asch.auditscheduleid');

        if ($scheduleIds->isNotEmpty()) {
            foreach (['selected_cfr', 'trans_accountdetails', 'auditee_office_users', 'trans_callforrecords'] as $table) {
                DB::table("audit.$table")
                    ->whereIn('auditscheduleid', $scheduleIds)
                    ->update([
                        'statusflag' => 'N',
                        'updatedon'  => $now,
                        'updatedby'  => $userid,
                    ]);
            }
        }

        $planIds = DB::table('audit.auditplan as p')
            ->join('audit.inst_auditschedule as s', 'p.auditplanid', '=', 's.auditplanid')
            ->where('p.instid', $instid)
            ->pluck('p.auditplanid');

        if ($planIds->isNotEmpty()) {
            DB::table('audit.yearcode_mapping')
                ->whereIn('auditplanid', $planIds)
                ->update([
                    'statusflag' => 'N',
                    'updatedon'  => $now
                ]);
        }
    }

    public static function updateSpilloverInstitutions($indexed, $newQuarter, $userid, $now)
    {
        foreach ($indexed as $instid => $item) {

            $update = [
                'priority' => 'Y',
                'remainingmandays' => $item['remainingmandays'] ?? null,
                'spillover' => 'Y',
                'updatedon' => $now,
                'updatedby' => $userid,
            ];

            if (in_array($newQuarter, ['Q1', 'Q2', 'Q3', 'Q4'])) {
                $update[$newQuarter] = 'Y';
            }

            DB::table('audit.mst_institution')
                ->where('instid', $instid)
                ->update($update);

            $deptcode = DB::table('audit.mst_institution')
                ->where('instid', $instid)
                ->value('deptcode');

            $currentcode = DB::table('audit.mst_dept')
                ->where('deptcode', $deptcode)
                ->value('currentquarter');

            DB::table('audit.temp_inst_q1_pending')->insert([
                'instid'          => $instid,
                'mandays'         => $item['mandays'] ?? null,
                'remainingmandays' => $item['remainingmandays'] ?? null,
                'teamsize'        => $item['teamsize'] ?? null,
                'quartercode'  => $currentcode,
                'newquartercode'  => $newQuarter,
                'spilloverflag'  => 'Y',
                'pendingflag'      => 'F',
                'yearofaudit'  => '2025',
                'createdon'       => $now,
                'createdby'       => $userid,
                'updatedon'       => $now,
                'updatedby'       => $userid,
            ]);
        }
    }

       public static function finaliseInstitutionStatus($deptcode, $distcode, $userid, $now)
    {
        DB::table('audit.auditor_instmapping')
            ->where('deptcode', $deptcode)
            ->where('distcode', $distcode)
            ->update([
                'pendinginststatus' => 'Y',
                'updatedon' => $now,
                'updatedby' => $userid,
            ]);
    }

    public static function UpdateNotscheduledData($data, $tempid, $table)
    {
        try {
            if ($tempid) {
                DB::table($table)
                    ->where('instid', $data['instid'])
                    ->where('quartercode', $data['quartercode'])
                    ->update($data);
            } else {
                DB::table($table)->updateOrInsert([
                    'instid' => $data['instid'],
                    'quartercode' => $data['quartercode']
                ], $data);
            }
            return ['status' => true];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

public static function checkisteamassigned($deptcode, $distcode)
    {
        try {
            if (empty($deptcode)) {
                throw new Exception("Deptcode is not available");
            }
            if (empty($distcode)) {
                throw new Exception("distcode is not available");
            }

            $query = DB::table(self::$teamassignments_table . ' as ta')
                ->join(self::$institution_table . ' as inst', 'inst.instid', '=', 'ta.instid')
                ->where('inst.distcode', $distcode)
                ->where('inst.deptcode', $deptcode)
                ->exists();
            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function assignteams($deptcode, $distcode, $quartercode)
    {
        try {
            if (empty($deptcode)) {
                throw new Exception("Deptcode is not available");
            }
            if (empty($distcode)) {
                throw new Exception("Distcode is not available");
            }
            if (empty($quartercode)) {
                throw new Exception("Quartercode is not available");
            }
          DB::beginTransaction();

        // Execute the query
        $result = DB::select("SELECT * FROM audit.loop_until_finished(?, ?, ?)", [$deptcode, $distcode, $quartercode]);

        // Commit the transaction if everything goes fine
        DB::commit();
          

            return $result;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();

    // Optionally, you can log the error or handle it accordingly
            Log::error("Error in executing loop_until_finished: " . $e->getMessage());

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function getchecklistteamdet($deptcode, $distcode, $quartercode)
    {
       
        try {
            if (empty($deptcode)) {
                throw new Exception("Deptcode is not available");
            }
            if (empty($distcode)) {
                throw new Exception("Distcode is not available");
            }
            if (empty($quartercode)) {
                throw new Exception("Quartercode is not available");
            }


            $teamdetails = DB::table('audit.team_assignments as ta')
                ->selectRaw("
                    DISTINCT ON (ta.team_name) 
                    ta.team_name,
                    th.username || ' (' || deth.desigesname || ')' AS teamhead,
                    ta.team_size,
                    members_list.members
                ")
                ->join('audit.deptuserdetails as th', 'th.deptuserid', '=', 'ta.team_head')
                ->join('audit.mst_designation as deth', 'deth.desigcode', '=', 'th.desigcode')
                ->leftJoin(DB::raw("LATERAL (
                    SELECT string_agg(member, ', ' ORDER BY member) AS members
                    FROM (
                        SELECT DISTINCT u.username || ' (' || de.desigesname || ')' AS member
                        FROM unnest(ta.team_users) AS uid(deptuserid)
                        JOIN audit.deptuserdetails u ON u.deptuserid = uid.deptuserid
                        JOIN audit.mst_designation de ON de.desigcode = u.desigcode
                    ) AS sub
                ) members_list"), DB::raw('true'), '=', DB::raw('true'))
                ->where('th.deptcode', $deptcode)
                ->where('th.distcode', $distcode)
                ->orderBy('ta.team_name')
                ->orderBy('th.username')
                ->orderBy('deth.desigesname')
                ->get();

  


            $teamdet = DB::table(self::$teamassignments_table . ' as ta')
                ->join(DB::raw('unnest(ta.team_users) AS uid(deptuserid)'), DB::raw('true'), DB::raw('true'), DB::raw('true'))
                ->join(self::$userdetail_table . ' as u', 'u.deptuserid', '=', 'uid.deptuserid')
                ->join(self::$designation_table . ' as de', 'de.desigcode', '=', 'u.desigcode')
                ->join(self::$userdetail_table . ' as th', 'th.deptuserid', '=', 'ta.team_head')
                ->join(self::$designation_table . ' as deth', 'deth.desigcode', '=', 'th.desigcode')
                ->join(self::$institution_table . ' as ins', 'ins.instid', '=', 'ta.instid')
                ->where('ins.distcode', $distcode)
                ->where('ins.deptcode', $deptcode)
                ->selectRaw("
                    ins.instename,
                     ins.insttname,
                    ta.team_name,
                    th.username || ' (' || deth.desigesname || ')' AS teamhead,
                    string_agg(u.username || ' (' || de.desigesname || ')', ', ' ORDER BY u.username) AS members,
                    ta.from_date,
                    ta.to_date,
                    team_size,
                    ta.mandays
                ")
                ->groupByRaw('ins.instename, ins.insttname,ta.from_date, ta.to_date, ta.mandays, ta.assign_id, th.username, deth.desigesname')
                ->orderBy('ta.assign_id')
                ->get();


    // 👥 Step 1: Calculate total audit days (excluding weekends and holidays)
    $totalAuditDays = DB::table(DB::raw("generate_series('2025-04-15'::date, '2025-06-23'::date, interval '1 day') as d"))
    ->whereRaw("EXTRACT(DOW FROM d) NOT IN (0,6)") // Exclude weekends (Sunday = 0, Saturday = 6)
    ->whereNotIn('d', function ($q) {
        $q->select('holiday_date')->from('audit.mst_holiday'); // Exclude holidays
    })
    ->count();

    // 👥 Step 2: Get all assigned users (team_head and team_users)
    $assignments = DB::table('audit.team_assignments')
    ->select('team_head as person_id', 'from_date', 'to_date')
    ->unionAll(
        DB::table('audit.team_assignments')
            ->select(DB::raw('unnest(team_users) as person_id'), 'from_date', 'to_date')
    );

    // 📊 Step 3: Person-wise period and allotted audit days (merge with assignments)
    $personPeriods = DB::table(DB::raw("({$assignments->toSql()}) as a"))
    ->mergeBindings($assignments)
    ->select(
        'person_id',
        DB::raw('MIN(from_date) as period_start'),
        DB::raw('MAX(to_date) as period_end'),
        DB::raw("SUM(
            (
                SELECT COUNT(*) 
                FROM generate_series(from_date, to_date, interval '1 day') AS d
                WHERE EXTRACT(DOW FROM d) NOT IN (0,6)
                AND d::date NOT IN (SELECT holiday_date FROM audit.mst_holiday)
            )
        ) as allotted_days")
    )
    ->groupBy('person_id');

    // 👤 Step 4: Filter users from specific dist and dept
    $allUsers = DB::table('audit.deptuserdetails as u')
    ->leftJoin(DB::raw("(
        SELECT team_head AS person_id FROM audit.team_assignments
        UNION
        SELECT unnest(team_users) AS person_id FROM audit.team_assignments
    ) as a"), 'a.person_id', '=', 'u.deptuserid')
    ->join('audit.mst_designation as de', 'de.desigcode', '=', 'u.desigcode')
    ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'u.deptuserid')
    ->join('audit.chargedetails as ch', 'ch.chargeid', '=', 'uc.chargeid')
    ->join('audit.rolemapping as ro', 'ro.rolemappingid', '=', 'ch.rolemappingid')
    ->where([
        ['u.distcode', '=', $distcode],
        ['u.deptcode', '=', $deptcode],
        ['ro.roleactioncode', '=', '04'],
        ['uc.statusflag', '=', 'Y'],
        ['u.statusflag', '=', 'Y'],
        ['u.reservelist', '=', 'Y']
    ])
    ->whereIn('u.desigcode', function ($query) use ($deptcode) {
        $query->select('desigcode')
              ->from('audit.mst_designation')
              ->where('deptcode',  $deptcode)
              ->where('belowaddesig', 'Y');
    })
    ->select(
        'u.deptuserid as person_id',
        DB::raw("u.username || ' (' || de.desigesname || ')' as username_with_designation"),
        'de.desigesname'
    );

    // 🧾 Step 5: Join users with periods and calculate status
    $finalList = DB::table(DB::raw("({$allUsers->toSql()}) as u"))
    ->mergeBindings($allUsers)
    ->leftJoinSub($personPeriods, 'p', 'p.person_id', '=', 'u.person_id')
    ->select(
        'u.username_with_designation as username',
        DB::raw("COALESCE(TO_CHAR(p.period_start, 'DD/MM/YYYY') || ' - ' || TO_CHAR(p.period_end, 'DD/MM/YYYY'), 'NIL') as engagement_period"),
        DB::raw("
            CASE
                WHEN p.allotted_days = {$totalAuditDays} THEN 'Fully Engaged'
                WHEN p.allotted_days IS NULL THEN 'Idle'
                ELSE 'Partially engaged'
            END as status
        "),
        DB::raw("{$totalAuditDays} as total_audit_days"),
        DB::raw("COALESCE(p.allotted_days, 0) as allotted_days"),
        'u.desigesname'
    );

    // 🧑‍💼 Step 6: Custom ordering of statuses (Fully Engaged -> Partially Engaged -> Idle)
    $result = $finalList
    ->orderByRaw("
        CASE
            WHEN
                (CASE
                    WHEN p.allotted_days = {$totalAuditDays} THEN 'Fully Engaged'
                    WHEN p.allotted_days IS NULL THEN 'Idle'
                    ELSE 'Partially engaged'
                END) = 'Fully Engaged' THEN 1
            WHEN
                (CASE
                    WHEN p.allotted_days = {$totalAuditDays} THEN 'Fully Engaged'
                    WHEN p.allotted_days IS NULL THEN 'Idle'
                    ELSE 'Partially engaged'
                END) = 'Partially engaged' THEN 2
            WHEN
                (CASE
                    WHEN p.allotted_days = {$totalAuditDays} THEN 'Fully Engaged'
                    WHEN p.allotted_days IS NULL THEN 'Idle'
                    ELSE 'Partially engaged'
                END) = 'Idle' THEN 3
            ELSE 4
        END
    ")
    ->get();



    $idleinst=DB::table(self::$institution_table . ' as inst')
                    ->join(self::$mapinst_table . ' as map', 'inst.instid', '=', 'map.instid')
                    //   ->join(self::$designation_table . ' as desig', 'desig.desigcode', '=', 'map.desigcode')
                    ->selectRaw('inst.instid,inst.instename,inst.insttname,inst.mandays,inst.carryforward,
              
                     count( map.desigcode ORDER BY map.desigcode) AS desigcodes, inst.rankorder')
                    ->where('inst.deptcode',$deptcode)
                    ->where('inst.distcode',$distcode)
                    ->where( 'inst.allocatedflag' , 'N')
                    ->groupBy(
                    'inst.instid', 
                    'inst.mandays', 
                    'inst.rankorder',
                    // 'desig.desigelname',
                    // 'desig.desigtlname',
                    )
                     ->get();

                $totalinstcount=DB::table(self::$institution_table . ' as inst')
                
                                ->where('inst.deptcode',$deptcode)
                                ->where('inst.distcode',$distcode)
                                ->where('inst.audit_quarter',$quartercode)
                                ->count();
                   

                $totalauditorscount =DB::table(self::$userdetail_table . ' as du')
                    ->join(self::$designation_table . ' as desig', 'desig.desigcode', '=', 'du.desigcode')
                    ->join(self::$userchargedetail_table . ' as uc', 'uc.userid', '=', 'du.deptuserid')
                    ->join(self::$chargedetail_table . ' as c', 'uc.chargeid', '=', 'c.chargeid')
                    ->join(self::$rolemapping_table . ' as ro', 'ro.rolemappingid', '=', 'c.rolemappingid')
                    ->where('du.deptcode',$deptcode)
                    ->where('du.distcode',$distcode)
                    ->where('ro.roleactioncode','04')
                    ->where('uc.statusflag','Y')
                    ->where('du.reservelist','Y')
                    ->where('du.statusflag','Y')
                    ->count();


                    $designationDetails=DB::table(self::$userdetail_table . ' as du')
                     ->join(self::$designation_table . ' as desig', 'desig.desigcode', '=', 'du.desigcode')
                    ->join(self::$userchargedetail_table . ' as uc', 'uc.userid', '=', 'du.deptuserid')
                    ->join(self::$chargedetail_table . ' as c', 'uc.chargeid', '=', 'c.chargeid')
                    ->selectRaw('desig.desigelname,desig.desigtlname,count(du.desigcode)')
                     ->where('du.deptcode',$deptcode)
                    ->where('du.distcode',$distcode)
                    ->where('uc.statusflag','Y')
                    ->where('du.reservelist','Y')
                    ->where('du.statusflag','Y')
                    ->groupBy(
                         'du.desigcode','desig.desigelname','desig.desigtlname'
                    )
                    ->get();

            $sessiondept=DB::table(self::$deptartment_table . ' as dept')
            ->select('dept.deptelname','dept.depttlname')
            ->where('dept.deptcode',$deptcode)
            ->get();
                                
            $sessiondist=DB::table(self::$dist_table . ' as dist')
            ->select('dist.distename','dist.disttname')
            ->where('dist.distcode',$distcode)
            ->get();


        return 
        [
            'totalteamdetails'          =>$teamdetails,
        'idelusers'         =>$result,
        'teamdetails'        =>$teamdet,
        'idleinst'           =>$idleinst,
        'totalinstcount'     =>$totalinstcount,
        'totalauditorscount' =>$totalauditorscount,
        'designationDetails' =>$designationDetails,
        'deptname'           =>$sessiondept,
        'distname'           =>$sessiondist

    ];
   
            //return $results;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
}
    public static function getAuditorsInstdet($deptcode, $distcode, $quartercode)
    {
        try {
            if (empty($deptcode)) {
                throw new Exception("Deptcode is not available");
            }
            if (empty($distcode)) {
                throw new Exception("Distcode is not available");
            }
            if (empty($quartercode)) {
                throw new Exception("Quartercode is not available");
            }

            $users = DB::table(self::$userdetail_table . ' as du')
                ->join(self::$userchargedetail_table . ' as uc', 'uc.userid', '=', 'du.deptuserid')
                ->join(self::$chargedetail_table . ' as cd', 'cd.chargeid', '=', 'uc.chargeid')
                ->join(self::$rolemapping_table . ' as rm', 'rm.rolemappingid', '=', 'cd.rolemappingid')
                ->join(self::$designation_table . ' as dd', 'dd.desigcode', '=', 'du.desigcode')
                ->join(self::$deptartment_table . ' as dept', 'dept.deptcode', '=', 'du.deptcode')

                ->where('du.deptcode', $deptcode)
                ->where('du.distcode', $distcode)
                ->where('du.statusflag', 'Y')
                ->where('uc.statusflag', 'Y')
                ->where('du.reservelist', 'Y')
                ->where('rm.roleactioncode', View::shared('auditor_roleactioncode'))
                // ->where('du.chargeassigned', 'Y')
                // ->where('du.auditorflag', 'Y')
                ->orderBy('dd.desigcode', 'asc')
                ->groupBy('dd.desigcode', 'dd.desigelname', 'dd.desigtlname', 'du.deptuserid', 'du.username', 'du.usertamilname', 'dept.deptesname', 'dept.depttsname')
                ->select('du.deptuserid', 'du.username', 'du.usertamilname', 'dd.desigelname', 'dd.desigtlname', 'dept.deptesname', 'dept.depttsname')
                ->get();


            $column = 'inst.' . $quartercode;


            $inst_det = DB::table(self::$institution_table . ' as inst')
                ->join(self::$deptartment_table . ' as dept', 'dept.deptcode', '=', 'inst.deptcode')
                ->join(self::$mstauditeeinscategory_table . ' as cat', 'cat.catcode', '=', 'inst.catcode')
                // ->join(self::$mapinst_table . ' as map', 'map.instid', '=', 'inst.instid')

                ->where('inst.deptcode',  $deptcode)
                ->where('inst.distcode',  $distcode)
                ->where('inst.statusflag', 'Y')
                ->where($column, 'Y')
                ->select(

                    'inst.mandays',
                    'inst.carryforward',
                    'inst.checkcarryforward',
                    'inst.instid',
                    'inst.instename',
                    'inst.insttname',
                    'inst.spillover',
                    'inst.remainingmandays',
                    'cat.catename',
                    'cat.cattname',
                    'inst.teamsize'


                )
                ->orderByRaw("inst.spillover = 'Y' ASC")
                ->orderBy('cat.catename', 'asc')
                ->orderBy('inst.mandays', 'asc')
                ->orderBy('inst.instename', 'asc')
                // ->groupBy(
                //     'inst.instid',
                //     'inst.mandays',
                //     'inst.carryforward',
                //     'inst.instename',
                //     'inst.insttname',
                //     'cat.catename',
                //     'cat.cattname',)
                ->get();


            return [
                'users' => $users,
                'inst_det' => $inst_det
            ];
            //return $results;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching Auditors and Institution Details. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }
    public static function getmandaysDetais($deptcode, $distcode, $quartercode)
    {
         try {
            if (empty($deptcode)) {
                throw new Exception("Deptcode is not available");
            }
            if (empty($distcode)) {
                throw new Exception("Distcode is not available");
            }
            if (empty($quartercode)) {
                throw new Exception("Quartercode is not available");
            }

            $totalworkingdays=DB::select("WITH all_days AS (
    SELECT generate_series('2025-04-15', '2025-06-30', interval '1 day') AS day
  ),
  working_days AS (
    SELECT day
    FROM all_days
    WHERE EXTRACT(DOW FROM day) NOT IN (0, 6)  -- Exclude Sunday(0) and Saturday(6)
      AND day NOT IN (SELECT holiday_date FROM audit.mst_holiday)
  ),
  cutoff AS (
    SELECT day AS cutoff_date
    FROM working_days
    WHERE day < '2025-06-30'
    ORDER BY day DESC
    OFFSET 4 LIMIT 1
  ),
  final_days AS (
    SELECT day
    FROM working_days, cutoff
    WHERE day >= '2025-04-01' AND day <= cutoff.cutoff_date
  ),
  final_result AS (
    SELECT
      ARRAY_AGG(day ORDER BY day) AS assigned_users,
      MAX(cutoff.cutoff_date) AS last_date,
      COUNT(*) AS total_days
    FROM final_days, cutoff
  )
  SELECT
    fr.total_days
  FROM final_result fr;
 ");




     $totalusers=DB::table(self::$userdetail_table . ' as du')
             ->join(self::$designation_table . ' as de', 'de.desigcode', '=', 'du.desigcode')
             ->join(self::$userchargedetail_table . ' as uc', 'uc.userid', '=', 'du.deptuserid')
             ->join(self::$chargedetail_table . ' as c', 'uc.chargeid', '=', 'c.chargeid')
             ->join(self::$rolemapping_table . ' as ro', 'ro.rolemappingid', '=', 'c.rolemappingid')
             ->where('ro.roleactioncode','04')
             ->where('uc.statusflag','Y')
             ->where('du.statusflag','Y')
             ->where('reservelist','Y')
             ->where('du.distcode',$distcode)
             ->where('du.deptcode',$deptcode)
             ->where('uc.statusflag','Y')
            ->count();

    $sumMandays =DB::select("  select sum(mandays) from audit.mst_institution where distcode = '029' AND deptcode = '03'
    ");
    return [
    'totalworkingdays' =>$totalworkingdays,
     'sumMandays' =>$sumMandays,
      'totalusers' =>$totalusers,
    ];
            } catch (\Illuminate\Database\QueryException $e) {
                $customMessage = 'A database error occurred while fetching Auditors and Institution Details. Please contact the administrator.';
            
                \Log::error('SQL Error: ' . $e->getMessage());
                throw new \Exception($e->getMessage(), 500);
            } catch (\Exception $e) {
            
                throw new \Exception($e->getMessage(), 409);
            }
        }

    public static   function getalldetails($deptcode, $distcode, $quartercode)
    
    {
        try {
            if (empty($deptcode)) {
                throw new Exception("Deptcode is not available");
            }
            if (empty($distcode)) {
                throw new Exception("Distcode is not available");
            }
            if (empty($quartercode)) {
                throw new Exception("Quartercode is not available");
            }
            $result = DB::select("SELECT * FROM audit.checklistdel(?, ?)", [$deptcode, $distcode]);

            return $result;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }

    public static function checkisPlanfinalized($deptcode, $distcode)
    {
         try {
            if (empty($deptcode)) {
                throw new Exception("Deptcode is not available");
            }
            if (empty($distcode)) {
                throw new Exception("distcode is not available");
            }

            $query = DB::table(self::$auditor_instmapping_table . ' as map')
            ->select('map.autoplanstatus','map.pendinginststatus')
                // ->join(self::$institution_table . ' as inst', 'inst.instid', '=', 'ta.instid')
                ->where('map.distcode', $distcode)
                ->where('map.deptcode', $deptcode)
              
                ->get();
            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while saving the remarks. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
        }
    }

       public static function finaliseplan($deptcode, $distcode)
    {
        try {
            if (empty($deptcode)) {
                throw new Exception("Deptcode is not available");
            }
            if (empty($distcode)) {
                throw new Exception("distcode is not available");
            }


            $session = session('charge');
            $userchargeid = $session->userchargeid;



            DB::beginTransaction();

            //$isexitdone = self::checkexitmeetstatus($deptcode, $distcode);

            // Execute the query
            $query = DB::select(
                'SELECT * FROM audit.distributeauditteamplan' . '(:distcode, :deptcode, :userchargeid)',
                [
                    'distcode'       => $distcode,
                    'deptcode'       => $deptcode,
                    'userchargeid'   => $userchargeid,
                ]
            );

            // Commit the transaction if everything goes fine
            DB::commit();
            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while finalising. Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();

            // Optionally, you can log the error or handle it accordingly
            Log::error("Error in executing finaliseplan_function: " . $e->getMessage());
            throw new \Exception($e->getMessage(), 409);
        }
    }

    //don't forget to get the use///
    public static function getauditquarter($deptcode)
    {
        try
        {
        if (empty($deptcode)) {
                throw new Exception("Deptcode is not available");
            }
             $deptData = DB::table(self::$deptartment_table )
                ->select('nextquarter', 'nextquarterfromdate', 'nextquartertodate','currentquarter', 
                'currentquarterfromdate', 'currentquartertodate','autoplandate')
                ->where('deptcode' , $deptcode)
                ->get();
                //return $deptData;
               $autoplandate= $deptData[0]->autoplandate;

         $currentDate = Carbon::today();

         // Check if autoplandate is available and valid
            if (is_null($autoplandate)) {
                throw new \Exception('Autoplandate is null for department ' . $deptcode);
            }

            // Compare current date with autoplandate
            if ($currentDate->gte($autoplandate)) {
                // If current date is greater than or equal to autoplandate, use next quarter
                $planQuarter = $deptData[0]->nextquarter;
                $planQuarterFromDate = $deptData[0]->nextquarterfromdate;
                $planQuarterToDate = $deptData[0]->nextquartertodate;
            } else {
                // If current date is less than autoplandate, use current quarter
                $planQuarter = $deptData[0]->currentquarter;
                $planQuarterFromDate = $deptData[0]->currentquarterfromdate;
                $planQuarterToDate = $deptData[0]->currentquartertodate;
            }

          return $planQuarter;
         } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), 409);
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
                    // Now safely access properties on the first item
                    $rcnocount = $firstItem->rcno;
                    $deptsname = $firstItem->deptesname;
                    $deptfirstcharacter = substr($deptsname, 0, 1);  // Corrected the typo

                    // Increment the count, and ensure it's padded with leading zeros
                    $incrementcount = $rcnocount ? $rcnocount + 1 : 1;

                    // Pad the increment count with leading zeros to make it 4 digits
                    $incrementcount = str_pad($incrementcount, 4, '0', STR_PAD_LEFT);

                    // Concatenate the values
                    $rcno = $deptfirstcharacter . '25' . $quartercode . $incrementcount;
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

    public static function getquarterdetails($deptcode)
    {


        try {

            if (empty($deptcode)) {
                throw new Exception("Deptcode is not available");
            }

            $deptData = DB::table(self::$deptartment_table . ' as dept')

                ->select(
                    'dept.nextquarter',
                    'dept.nextquarterfromdate',
                    'dept.nextquartertodate',
                    'dept.currentquarter',
                    'dept.currentquarterfromdate',
                    'dept.currentquartertodate',
                    'dept.autoplandate',
                )
                ->where('dept.deptcode', $deptcode)
                ->get();



            $autoplandate = $deptData[0]->autoplandate;

            $currentDate = Carbon::today();
            //  $currentDate = '2025-07-22';


            // Check if autoplandate is available and valid
            if (is_null($autoplandate)) {
                throw new \Exception('Date is null for department ' . $deptcode);
            }

            $quarterDets = [];

            if (
                $currentDate > $deptData[0]->autoplandate &&
                $currentDate < $deptData[0]->nextquarterfromdate
            ) {

                $quarterDets = DB::table(self::$auditquarter_table . ' as aq')
                    ->select('aq.auditquarter', 'aq.auditquartercode')
                    ->distinct()
                    ->where('deptcode', $deptcode)
                    ->whereIN('auditquartercode', [$deptData[0]->nextquarter, $deptData[0]->currentquarter])
                    ->orderBy('auditquartercode', 'desc')
                    ->get();
            } elseif (
                $currentDate > $deptData[0]->autoplandate &&
                $currentDate >= $deptData[0]->currentquarterfromdate
            ) {

                $quarterDets = DB::table(self::$auditquarter_table . ' as aq')
                    ->select('aq.auditquarter', 'aq.auditquartercode')
                    ->distinct()
                    ->where('deptcode', $deptcode)
                    ->whereIN('auditquartercode', [$deptData[0]->currentquarter])
                    ->get();
            } elseif ($currentDate < $deptData[0]->autoplandate) {
                $quarterDets = DB::table(self::$auditquarter_table . ' as aq')
                    ->select('aq.auditquarter', 'aq.auditquartercode')
                    ->distinct()
                    ->where('deptcode', $deptcode)
                    ->whereIN('auditquartercode', [$deptData[0]->currentquarter])
                    ->get();
            }
            return  $quarterDets;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching . Please contact the administrator.';

            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($e->getMessage(), 500);
        } catch (\Exception $e) {


            // Optionally, you can log the error or handle it accordingly

            throw new \Exception($e->getMessage(), 409);
        }
    }

 public static function  fetch_auditplandetails($userid, $quartercode)
    {

        $query = self::query()
            ->join(self::$institution_table . ' as ai', 'ai.instid', '=', 'auditplan.instid')
            ->join(self::$auditplanteam_table . ' as at', 'at.auditplanteamid', '=', 'auditplan.auditteamid')
            ->join(self::$auditplanteammem_table . ' as atm', 'atm.auditplanteamid', '=', 'auditplan.auditteamid')
            ->join(self::$typeofaudit_table . ' as mst', 'mst.typeofauditcode', '=', 'auditplan.typeofauditcode')
            ->join(self::$userdetail_table . ' as du', 'du.deptuserid', '=', 'atm.userid')
            ->join(self::$designation_table . ' as desig', 'desig.desigcode', '=', 'du.desigcode')
            ->join(self::$deptartment_table . ' as msd', 'msd.deptcode', '=', 'ai.deptcode')
            ->join(self::$mstauditeeinscategory_table . ' as mac', 'mac.catcode', '=', 'ai.catcode')
            ->LeftJoin(self::$subcategory_table . ' as sub', 'sub.auditeeins_subcategoryid', '=', 'ai.subcatid')
            ->leftJoin('audit.inst_auditschedule as ins', function ($join) {
                $join->on('ins.auditplanid', '=', 'auditplan.auditplanid')
                    ->whereIn('ins.statusflag', ['Y', 'F']);
            })
            ->join(
                DB::raw('(SELECT DISTINCT ON (auditquartercode) * FROM audit.mst_auditquarter) AS maq'),
                'maq.auditquartercode',
                '=',
                'auditplan.auditquartercode'
            )
            ->select(
                DB::raw("CASE WHEN CURRENT_DATE >= msd.autoplandate THEN 'next' ELSE 'current' END AS quartertype"),
                DB::raw("CASE WHEN CURRENT_DATE >= msd.autoplandate THEN msd.nextquarter ELSE msd.currentquarter END AS activequartercode"),
                'auditplan.fromdate',
                'auditplan.todate',
                'auditplan.auditquartercode',
                'msd.freezeschedule',
                'msd.autoplandate',
                'msd.currentquarter',
                'msd.nextquarter',
                'ai.instename',
                'ai.insttname',
                'ai.deptcode',
                'ai.instid',
                'ai.mandays',
                'ai.remainingmandays',
                'ai.spillover',
                'auditplan.auditteamid',
                'auditplan.auditplanid',
                'at.auditplanteamid',
                'at.teamname',
                'mst.typeofauditename',
                'mst.typeofaudittname',
                'msd.deptesname',
                'msd.depttsname',
                'mac.catename',
                'mac.cattname',
                'du.deptuserid',
                'maq.auditquarter',
                'maq.auditquartertname',
                'auditplan.statusflag',
                'sub.subcatename',
                'sub.subcattname',
                'ins.statusflag as schedule_status',
                'ins.exitmeetdate',
                'ins.auditscheduleid',
		'auditplan.spilloverflag',

                // Count of team members who are NOT team heads
                DB::raw('(
                SELECT COUNT(*)
                FROM audit.auditplanteammember AS sub_atm
                WHERE sub_atm.auditplanteamid = auditplan.auditteamid
                   AND sub_atm.statusflag = \'Y\'
                AND sub_atm.teamhead = \'N\'
            ) AS team_member_count'),

                // Aggregating all team members' names and designations
                DB::raw('(
                SELECT STRING_AGG(du2.username || \' - \' || desig2.desigelname, \', \')
                FROM audit.auditplanteammember AS sub_atm
                JOIN audit.deptuserdetails AS du2 ON du2.deptuserid = sub_atm.userid
                JOIN audit.mst_designation AS desig2 ON desig2.desigcode = du2.desigcode
                WHERE sub_atm.auditplanteamid = auditplan.auditteamid
                AND sub_atm.statusflag=\'Y\'
                AND sub_atm.teamhead = \'N\'
            ) AS team_members_en'),

                // Getting team head s name and designation separately
                DB::raw('(
                SELECT du3.username || \' - \' || desig3.desigelname
                FROM audit.auditplanteammember AS sub_atm
                JOIN audit.deptuserdetails AS du3 ON du3.deptuserid = sub_atm.userid
                JOIN audit.mst_designation AS desig3 ON desig3.desigcode = du3.desigcode
                WHERE sub_atm.auditplanteamid = auditplan.auditteamid
                AND sub_atm.teamhead = \'Y\'
                AND sub_atm.statusflag=\'Y\'
                LIMIT 1
            ) AS team_head_en'),
                DB::raw('(
                SELECT STRING_AGG(du2.usertamilname || \' - \' || desig2.desigtlname, \', \')
                FROM audit.auditplanteammember AS sub_atm
                JOIN audit.deptuserdetails AS du2 ON du2.deptuserid = sub_atm.userid
                JOIN audit.mst_designation AS desig2 ON desig2.desigcode = du2.desigcode
                WHERE sub_atm.auditplanteamid = auditplan.auditteamid
                 AND sub_atm.statusflag = \'Y\'
                AND sub_atm.teamhead = \'N\'
            ) AS team_members_ta'),

                // Getting team head s name and designation separately
                DB::raw('(
                SELECT du3.usertamilname || \' - \' || desig3.desigtlname
                FROM audit.auditplanteammember AS sub_atm
                JOIN audit.deptuserdetails AS du3 ON du3.deptuserid = sub_atm.userid
                JOIN audit.mst_designation AS desig3 ON desig3.desigcode = du3.desigcode
                WHERE sub_atm.auditplanteamid = auditplan.auditteamid
                    AND sub_atm.statusflag = \'Y\'
                AND sub_atm.teamhead = \'Y\'
                LIMIT 1
            ) AS team_head_ta'),
            )
            ->where('atm.userid', '=', $userid)
            ->where('auditplan.auditquartercode',  $quartercode)
            ->where('atm.statusflag', '=', 'Y')
            ->where('atm.teamhead', '=', 'Y')
            ->where('auditplan.statusflag', '=', 'F')
            ->groupBy(
                'msd.freezeschedule',
                'auditplan.auditquartercode',
                'msd.autoplandate',
                'msd.currentquarter',
                'msd.nextquarter',
                'ai.instename',
                'ai.insttname',
                'ai.deptcode',
                'ai.instid',
                'ai.mandays',
                'maq.auditquartertname',
                'du.deptuserid',
                'auditplan.auditteamid',
                'auditplan.auditplanid',
                'at.auditplanteamid',
                'at.teamname',
                'mst.typeofauditename',
                'mst.typeofaudittname',
                'msd.deptesname',
                'msd.depttsname',
                'mac.catename',
                'mac.cattname',
                'maq.auditquarter',
                'auditplan.statusflag',
                'sub.subcatename',
                'sub.subcattname',
                'ins.statusflag',
                'ins.exitmeetdate',
                'ins.auditscheduleid',
                'auditplan.fromdate',
                'auditplan.todate',
            )
            ->get();

        //    $currentDate = Carbon::today();
        //    $autoplandate=  $query[0]->autoplandate; 


        // if (is_null($autoplandate)) {
        //     throw new \Exception('Autoplandate is null for department ' . $deptcode);
        // }
        //  $quartertype = '';
        //   $activequartercode = '';
        // // Compare current date with autoplandate
        // if ($currentDate->gte($autoplandate)) {
        //     // If current date is greater than or equal to autoplandate, use next quarter
        //    $quartertype = 'next';
        //    $activequartercode =  $query[0]->nextquarter; 
        // } else {
        //     $quartertype = 'current';
        //    $activequartercode =  $query[0]->currentquarter; 
        // }
        return $query;

        //  return [
        //     'details'          => $query,
        //     'quartertype'       =>$quartertype,
        //     'activequartercode' =>$activequartercode,
        //  ];




    }


public static function fetch_particularscheduleDetails($auditscheduleid)
    {

        try {

            if (empty($auditscheduleid)) {
                throw new \Exception("No Schedule details");
            }

            $query = DB::table(self::$instauditschedule_table . ' as schd')
                ->join(self::$auditplan_table . ' as plan', 'plan.auditplanid', '=', 'schd.auditplanid')
                ->join(self::$institution_table . ' as inst', 'plan.instid', '=', 'inst.instid')
                ->join(self::$deptartment_table . ' as dept', 'dept.deptcode', '=', 'inst.deptcode')
                ->select('inst.spillover', 'schd.entrymeetdate', 'schd.workallocationflag', 'plan.auditquartercode', 'dept.financialyear', 'plan.spilloverflag')
                ->where('schd.auditscheduleid', $auditscheduleid)
                ->get();

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching . Please contact the administrator.';
            \Log::error('SQL Error: ' . $e->getMessage());
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }

public static function getspilloverplandetails($instid, $planid)
    {
        try {
            $query = DB::table(self::$institution_table . ' as inst')
                ->join(self::$auditplan_table . ' as plan', 'plan.instid', '=', 'inst.instid')

                ->join(self::$auditplanteammem_table . ' as team', 'plan.auditteamid', '=', 'team.auditplanteamid')
                ->join(self::$userdetail_table . ' as uc', 'uc.deptuserid', '=', 'team.userid')
                ->join(self::$designation_table . ' as desig', 'desig.desigcode', '=', 'uc.desigcode')
                ->join(self::$auditquarter_table . ' as q', 'q.auditquartercode', '=', 'plan.auditquartercode')
                ->select(
                    DB::raw("CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM audit.inst_auditschedule schd 
			WHERE schd.auditplanid = plan.auditplanid AND schd.statusflag='F'
                    ) THEN 'Y' ELSE 'N' 
                 END AS scheduledflag"),
                    'plan.todate',
                    'plan.auditquartercode',
                    'plan.fromdate',
                    'plan.todate',
                    'inst.instename',
                    'inst.insttname',
                    'inst.instid',
                    'plan.auditplanid',
                    'q.auditquarter',

                    DB::raw("TO_CHAR(plan.fromdate, 'DD-MM-YYYY') AS fromdate"),
                    DB::raw("TO_CHAR(plan.todate, 'DD-MM-YYYY') AS todate"),
                    DB::raw('(
                SELECT COUNT(*)
                FROM audit.auditplanteammember AS sub_atm
                WHERE sub_atm.auditplanteamid = plan.auditteamid
                   AND sub_atm.statusflag = \'Y\'
                  ) AS team_member_count'),

                    // Aggregating all team members' names and designations
                    DB::raw('(
                SELECT STRING_AGG(du2.username || \' - \' || desig2.desigelname, \', \')
                FROM audit.auditplanteammember AS sub_atm
                JOIN audit.deptuserdetails AS du2 ON du2.deptuserid = sub_atm.userid
                JOIN audit.mst_designation AS desig2 ON desig2.desigcode = du2.desigcode
                WHERE sub_atm.auditplanteamid = plan.auditteamid
                AND sub_atm.statusflag=\'Y\'
                AND sub_atm.teamhead = \'N\'
            ) AS team_members_en'),

                    // Getting team head s name and designation separately
                    DB::raw('(
                SELECT du3.username || \' - \' || desig3.desigelname
                FROM audit.auditplanteammember AS sub_atm
                JOIN audit.deptuserdetails AS du3 ON du3.deptuserid = sub_atm.userid
                JOIN audit.mst_designation AS desig3 ON desig3.desigcode = du3.desigcode
                WHERE sub_atm.auditplanteamid = plan.auditteamid
                AND sub_atm.teamhead = \'Y\'
                AND sub_atm.statusflag=\'Y\'
                LIMIT 1
            ) AS team_head_en'),
                    DB::raw('(
                SELECT STRING_AGG(du2.usertamilname || \' - \' || desig2.desigtlname, \', \')
                FROM audit.auditplanteammember AS sub_atm
                JOIN audit.deptuserdetails AS du2 ON du2.deptuserid = sub_atm.userid
                JOIN audit.mst_designation AS desig2 ON desig2.desigcode = du2.desigcode
                WHERE sub_atm.auditplanteamid = plan.auditteamid
                 AND sub_atm.statusflag = \'Y\'
                AND sub_atm.teamhead = \'N\'
            ) AS team_members_ta'),

                    // Getting team head s name and designation separately
                    DB::raw('(
                SELECT du3.usertamilname || \' - \' || desig3.desigtlname
                FROM audit.auditplanteammember AS sub_atm
                JOIN audit.deptuserdetails AS du3 ON du3.deptuserid = sub_atm.userid
                JOIN audit.mst_designation AS desig3 ON desig3.desigcode = du3.desigcode
                WHERE sub_atm.auditplanteamid = plan.auditteamid
                    AND sub_atm.statusflag = \'Y\'
                AND sub_atm.teamhead = \'Y\'
                LIMIT 1
            ) AS team_head_ta'),
                )
                ->where('inst.instid', $instid)
                ->where('plan.auditplanid', $planid)
                ->where('plan.statusflag', 'F')
                ->groupBy(
                    'plan.mandays',
                    'plan.auditquartercode',
                    'plan.fromdate',
                    'plan.todate',
                    'inst.instename',
                    'inst.insttname',
                    'inst.instid',
                    'plan.auditplanid',
                    'q.auditquarter',
                    'team.auditplanteamid',
                    'plan.auditteamid',
                    'desig.desigelname',
                    'desig.desigtlname'
                )
                ->get();

            return $query;
        } catch (\Illuminate\Database\QueryException $e) {
            $customMessage = 'A database error occurred while fetching . Please contact the administrator.';

            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }


    public static function chargetakingover($instid, $userid)
    {
        DB::beginTransaction();

        try {
            if (empty($instid)) {
                throw new \Exception("Institution Details not found");
            }
            if (empty($userid)) {
                throw new \Exception("Session Details not found");
            }

            $result = DB::select("SELECT audit.chargetakingover(?, ?) AS response", [$instid, $userid]);

            DB::commit();

            return $result;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $customMessage = 'A database error occurred while fetching. Please contact the administrator.';
            throw new \Exception($customMessage, 500);
        } catch (\Exception $e) {
            DB::rollBack();

            throw new \Exception($e->getMessage(), 409);
        }
    }


}
