<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;



class FormatModel extends Model
{
    use HasFactory;

    protected $connection = 'pgsql'; // PostgreSQL connection
    protected $table = BaseModel::REPORTCONTENTS_table;
    protected static $regionTable = BaseModel::REGION_TABLE;
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
    protected static $SlipHistroyDetails_Table      =  BaseModel::SLIPHISTORYDETAILS_TABLE;

    protected static $TransWorkAllocation_Table     =  BaseModel::TRANSWORKALLOCATION_TABLE;
    protected static $MapWorkAllocation_Table       =  BaseModel::MAPWORKALLOCATION_TABLE;
    protected static $MajWorkAllocation_Table       =  BaseModel::MAJWORKALLOCATION_TABLE;
    protected static $mapallocationobjection_table  =  BaseModel::MAPALLOCATIONOBJECTION_TABLE;

    protected static $auditeedeptreport_table = BaseModel::AUDITEEDEPTREPORT_TABLE;


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
        'report_contents_ta',
        'statusflag'
    ];

    // Cast JSON field to array when retrieved
    protected $casts = [
        'report_contents' => 'array'
    ];

   

    public static function FetchAnnextures($scheduleid)
    {
        $FetchAnnextures = DB::table('audit.report_annextures as ann')
                             ->join('audit.fileuploaddetail as fup', 'fup.fileuploadid', '=', 'ann.fileupload_id')
                            ->select('fup.filepath','fup.filename', 'ann.annexture_type')
                             ->where('ann.auditscheduleid',$scheduleid)
                              ->where('ann.statusflag', '!=', 'N')
                             ->get();

       // Initialize arrays
        $pdfFiles = [];
        $xlsxFiles = [];

        // Split based on file extension
        foreach ($FetchAnnextures as $file) {
            $extension = strtolower(pathinfo($file->filename, PATHINFO_EXTENSION));

            if ($extension === 'pdf') {
                $pdfFiles[] = $file;
            } elseif (in_array($extension, ['xlsx', 'xls'])) {
                $xlsxFiles[] = $file;
            }
        }
        
       return [
            'pdfFiles' => $pdfFiles,
            'xlsxFiles' => $xlsxFiles
        ];

                             
    }

   public static function DeleteAnnexture($auditscheduleid, $annexturetype)
    {
        $existingRecord = DB::table('audit.report_annextures')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('annexture_type', $annexturetype)
            ->where('statusflag', 'Y')
            ->first(); 

        if ($existingRecord) {
            $updated = DB::table('audit.report_annextures')
                ->where('auditscheduleid', $auditscheduleid)
                ->where('annexture_type', $annexturetype)
                ->where('statusflag', 'Y')
                ->update(['statusflag' => 'N']);

            return $updated; // true if rows were updated
        }

        return false; // No record found
    }


    public static function annexturestore(array $data)
    {
        // Add any additional fields like timestamps, statusflag, uploadedby
        $insertData = [
            'auditscheduleid' =>$data['auditscheduleid'],
            'annexture_type' => $data['annexture_type'] ?? null,
            'fileupload_id' => $data['fileupload_id'] ?? null,
            'filetype' => $data['filetype'] ?? null,
            'uploadedon' => now(),
            'statusflag' => 'Y',  // example status
            'uploadedby' => 1,  // current user ID if available
        ];
        $existingRecord = DB::table('audit.report_annextures')
                    ->where('auditscheduleid', $data['auditscheduleid'])
                    ->where('annexture_type', $data['annexture_type'])
                    ->first(); 

        if($existingRecord)
        {
            $annextureId =  DB::table('audit.report_annextures')
               ->where('auditscheduleid', $data['auditscheduleid'])
                ->where('annexture_type', $data['annexture_type'])
                ->update($insertData);

        }else
        {
             $annextureId = DB::table('audit.report_annextures')->insertGetId($insertData, 'annexture_id');

        }


        return $annextureId;
    }

    public static function StoreSlipOrdering($data)
    {
         // Check if record exists
        $existingRecord = DB::table('audit.report_storesliporder')
            ->where('auditscheduleid', $data['auditscheduleid'])
            ->first();



        $StoreData = [
            'auditscheduleid' => $data['auditscheduleid'],
            'statusflag' => 'Y',
            'updatedon' => now(),
        ];

        if ($data['type'] === 'serious') {
            $StoreData['ser_ordered_slips'] = $data['order_auditslip'];
        } else if ($data['type'] === 'nonserious') {
            $StoreData['nonser_ordered_slips'] = $data['order_auditslip'];
        }


        
        if ($existingRecord) {
            // Update existing record
            DB::table('audit.report_storesliporder')
                 ->where('auditscheduleid', $data['auditscheduleid'])
                ->update($StoreData);

            return $existingRecord->auditscheduleid;
        } else {
            // Insert new record, add createdon timestamp
            $StoreData['createdon'] = now();

            return DB::table('audit.report_storesliporder')->insertGetId($StoreData, 'auditscheduleid');
        }


    }

     public static function FinalizeReport($data)
    {
        try {
            $partno = $data['partno'];
            $scheduleId = $data['auditscheduleid'];
            $instId = $data['instid'] ?? null;

            // Standard record for most updates
            $record = [
                'updatedon'   => now(),
                'statusflag'  => 'F',
            ];

            // Optional use of DB transaction if you want rollback safety
            DB::beginTransaction();

            switch ($partno) {
                case 'part_a':
                    DB::table('audit.report_auditcertificate')
                        ->where('scheduleid', $scheduleId)
                        ->where('instid', $instId)
                        ->update($record);

                    DB::table('audit.report_authorityofaudit')
                        ->where('scheduleid', $scheduleId)
                        ->where('instid', $instId)
                        ->update($record);

                    DB::table('audit.report_accountdetails')
                        ->where('auditscheduleid', $scheduleId)
                        ->where('instid', $instId)
                        ->update($record);

                    DB::table('audit.report_insitutegenesis')
                        ->where('scheduleid', $scheduleId)
                        ->where('instid', $instId)
                        ->update($record);

                    DB::table('audit.report_pantan')
                        ->where('auditscheduleid', $scheduleId)
                        ->where('instid', $instId)
                        ->update($record);
                    break;

                case 'part_b_seriousirregularities':
                    DB::table('audit.report_paradetails')
                        ->where('auditscheduleid', $scheduleId)
                        ->where('irregularitycode', '01')
                        ->update($record);
                    break;

                case 'part_b_nonseriousirregularities':
                    DB::table('audit.report_paradetails')
                        ->where('auditscheduleid', $scheduleId)
                        ->where('irregularitycode', '02')
                        ->update($record);
                    break;

                case 'part_c':
                    DB::table('audit.report_annextures')
                        ->where('auditscheduleid', $scheduleId)
                        ->where('statusflag', 'Y')
                        ->update(['statusflag' => 'F']);
                    break;

                case 'part_b_others':
                    DB::table('audit.report_auditlevycertificate')
                        ->where('scheduleid', $scheduleId)
                        ->where('statusflag', 'Y')
                        ->update(['statusflag' => 'F']);

                    DB::table('audit.report_pendingparadetails')
                        ->where('scheduleid', $scheduleId)
                        ->where('statusflag', 'Y')
                        ->update(['statusflag' => 'F']);
                    break;

                case 'pdfreport':
                    DB::table('audit.inst_auditschedule')
                        ->where('auditscheduleid', $scheduleId)
                        ->update([
                            'updatedon'       => now(),
                            'sendintimation'  => 'F'
                        ]);
                    break;

                default:
                    DB::rollBack();
                    return 'invalid_partno';
            }

            DB::commit();
            return 'success';

        } catch (\Exception $e) {
            DB::rollBack();
            return 'error: ' . $e->getMessage();
        }
    }

   public static function StoreParaDetails($data)
    {
        // Check if record exists
        $existingRecord = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $data['auditscheduleid'])
            ->where('inst_id', $data['instid'])
             ->where('slip_id', $data['parnohidden'])
            ->first();

        $fileUploadIds = $data['file_upload_ids'];

        if (is_array($fileUploadIds)) {
            // Convert array to JSON
            $fileUploadIds = json_encode($fileUploadIds);
        } elseif (is_string($fileUploadIds)) {
            // Validate JSON string (optional safety)
            json_decode($fileUploadIds);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Handle error or convert manually
                $fileUploadIds = json_encode([$fileUploadIds]); // fallback
            }
        }

        $StoreData = [
            'auditscheduleid' => $data['auditscheduleid'],
            'inst_id' => $data['instid'],
            'slip_id' => $data['parnohidden'],
            'remarks' => json_encode(['content' => $data['slip_ckeditor']]),
            'deptcode' => $data['deptcode'],
            'orderid'=>$data['ordernohidden'],
            'slip_attachments'=>$fileUploadIds,
            'irregularitycode'=>$data['irregularitycode'],
            'slipdetails'=>$data['slipdetails_data'],
            'updatedon' => now(),
            'statusflag'=>'Y'
        ];

         DB::table('audit.trans_auditslip')
                ->where('auditscheduleid', $data['auditscheduleid'])
                ->where('auditslipid', $data['parnohidden'])
                ->update(['slipdetails'=>$data['slipdetails_data'],'remarks' => json_encode(['content' => $data['slip_ckeditor']])]);

        if ($existingRecord) {
            // Update existing record
            DB::table('audit.report_paradetails')
                ->where('para_id', $existingRecord->para_id)
                ->update($StoreData);

            return $existingRecord->para_id;
        } else {
            // Insert new record, add createdon timestamp
            $StoreData['createdon'] = now();

            return DB::table('audit.report_paradetails')->insertGetId($StoreData, 'para_id');
        }

    }


    public static function StorePanTan($data,$dynlabel)
    {
        try {
            // Prepare main record for report_pantan

            if($dynlabel == 'filabledata')
            {
                $record = [
                            'itfiling_issue'   => json_encode(['content' => $data['itfiling_issue']]),
                            'legal_complaince' => json_encode(['content' => $data['legal_complaince']]),
                            'financial_review' => json_encode(['content' => $data['financial_review']]),
                            'updatedon'        => now()
                        ];

                $exists = DB::table('audit.report_pantan')
                    ->where('auditscheduleid', $data['auditscheduleid'])
                    ->where('instid', $data['instid'])
                    ->first();

                if ($exists) {
                    DB::table('audit.report_pantan')
                        ->where('auditscheduleid', $data['auditscheduleid'])
                        ->where('instid', $data['instid'])
                        ->update($record);
                } else {
                    $record['auditscheduleid'] = $data['auditscheduleid'];
                    $record['instid'] = $data['instid'];
                    $record['createdon'] = now();

                    DB::table('audit.report_pantan')->insert($record);
                }

            }else if($dynlabel == 'lwf_data')
            {
                 // Prepare LWF record for report_labourwelfarefund
                $lwfrecord = [
                    'lwfq1_remarks' =>  $data['lwfdata']['lwf_1'],
                    'lwfq2_remarks' =>  $data['lwfdata']['lwf_2'],
                    'lwfq3_remarks' =>  $data['lwfdata']['lwf_3'],
                    'lwfq4_remarks' =>  $data['lwfdata']['lwf_4'],
                    'updatedon'     => now()
                ];

                $lwfexists = DB::table('audit.report_labourwelfarefund')
                    ->where('auditscheduleid', $data['auditscheduleid'])
                    ->where('instid', $data['instid'])
                    ->first();

                if ($lwfexists) {
                    DB::table('audit.report_labourwelfarefund') // 🔧 fixed incorrect table name
                        ->where('auditscheduleid', $data['auditscheduleid'])
                        ->where('instid', $data['instid'])
                        ->update($lwfrecord);
                } else {
                    $lwfrecord['auditscheduleid'] = $data['auditscheduleid'];
                    $lwfrecord['instid'] = $data['instid'];
                    $lwfrecord['createdon'] = now();

                    DB::table('audit.report_labourwelfarefund')->insert($lwfrecord);
                }

            }else if($dynlabel == 'gst_data')
            {
                
                // Prepare LWF record for report_labourwelfarefund
                $gstdata = [
                    'det_q1' =>  json_encode($data['gstdata']['q1']),
                    'det_q2' =>  json_encode($data['gstdata']['q2']),
                    'det_q3' =>  json_encode($data['gstdata']['q3']),
                    'det_q4' =>  json_encode($data['gstdata']['q4']),
                    'updatedon'     => now()
                ];

                $gstdataexists = DB::table('audit.report_gstreturn_details')
                    ->where('auditscheduleid', $data['auditscheduleid'])
                    ->where('instid', $data['instid'])
                    ->first();

                if ($gstdataexists) {
                    DB::table('audit.report_gstreturn_details') // 🔧 fixed incorrect table name
                        ->where('auditscheduleid', $data['auditscheduleid'])
                        ->where('instid', $data['instid'])
                        ->update($gstdata);
                } else {
                    $gstdata['auditscheduleid'] = $data['auditscheduleid'];
                    $gstdata['instid'] = $data['instid'];
                    $gstdata['createdon'] = now();

                    DB::table('audit.report_gstreturn_details')->insert($gstdata);
                }

            }


            DB::table('audit.report_pantan')
                        ->where('auditscheduleid', $data['auditscheduleid'])
                        ->where('instid', $data['instid'])
                        ->update(['statusflag'=>'Y']);
            

        } catch (\Exception $e) {
            // Optional: Log the error
           // \Log::error('Error storing PanTan data: ' . $e->getMessage(), ['data' => $data]);

            // Optionally rethrow or return a response
            throw new \Exception('Failed to store PanTan data.');
        }
    }


    public static function StorePendingPara($data)
    {
         // Check if record exists
        $existingRecord = DB::table('audit.report_pendingparadetails')
            ->where('scheduleid', $data['auditscheduleid'])
            ->where('instid', $data['instid'])
            ->first();

        $StoreData = [
            'scheduleid' => $data['auditscheduleid'],
            'instid' => $data['instid'],
            'pendingpara_remarks' => json_encode(['content' => $data['pendingparadet_ckeditor']]),
            'statusflag' => $data['finaliseflag'],
            'updatedon' => now(),
        ];

        if ($existingRecord) {
            // Update existing record
            DB::table('audit.report_pendingparadetails')
                ->where('pendingpara_id', $existingRecord->pendingpara_id)
                ->update($StoreData);

            return $existingRecord->pendingpara_id;
        } else {
            // Insert new record, add createdon timestamp
            $StoreData['createdon'] = now();

            return DB::table('audit.report_pendingparadetails')->insertGetId($StoreData, 'pendingpara_id');
        }

    }

    public static function StoreLevyCertificate($data)
    {
        // Check if record exists
        $existingRecord = DB::table('audit.report_auditlevycertificate')
            ->where('scheduleid', $data['auditscheduleid'])
            ->where('instid', $data['instid'])
            ->first();

        $StoreData = [
            'scheduleid' => $data['auditscheduleid'],
            'instid' => $data['instid'],
            'auditlevy_remarks' => json_encode(['content' => $data['levycertificate_ckeditor']]),
            'statusflag' => $data['finaliseflag'],
            'updatedon' => now(),
        ];

        if ($existingRecord) {
            // Update existing record
            DB::table('audit.report_auditlevycertificate')
                ->where('auditlevy_id', $existingRecord->auditlevy_id)
                ->update($StoreData);

            return $existingRecord->auditlevy_id;
        } else {
            // Insert new record, add createdon timestamp
            $StoreData['createdon'] = now();

            return DB::table('audit.report_auditlevycertificate')->insertGetId($StoreData, 'auditlevy_id');
        }

    }

    public static function StoreInstituteGenesis($data)
    {
        // Check if record exists
        $existingRecord = DB::table('audit.report_insitutegenesis')
            ->where('scheduleid', $data['auditscheduleid'])
            ->where('instid', $data['instid'])
            ->first();

        $StoreData = [
            'scheduleid' => $data['auditscheduleid'],
            'instid' => $data['instid'],
            'genesis_remarks' => json_encode(['content' => $data['genesis_ckeditor']]),
            'statusflag' => $data['finaliseflag'],
            'updatedon' => now(),
        ];

        if ($existingRecord) {
            // Update existing record
            DB::table('audit.report_insitutegenesis')
                ->where('inst_genesis_id', $existingRecord->inst_genesis_id)
                ->update($StoreData);

            return $existingRecord->inst_genesis_id;
        } else {
            // Insert new record, add createdon timestamp
            $StoreData['createdon'] = now();

            return DB::table('audit.report_insitutegenesis')->insertGetId($StoreData, 'inst_genesis_id');
        }

    }

   
    public static function StoreAccountDetails($data)
    {
        $existing = DB::table('audit.report_accountdetails')
            ->where('auditscheduleid', $data['auditscheduleid'])
            ->where('instid', $data['instid'])
            ->first();

        $record = [
            'statusflag' => $data['finaliseflag'],
            //'account_remarks' => json_encode(['content' => $data['accountdetails_ckeditor']]),
            'account_details' => $data['account_details'],
            'bank_account_number' => $data['bank_account_number'],
            'ob' => $data['ob'],
            'receipts' => $data['receipts'],
            'total' => $data['total'],
            'expenditure' => $data['expenditure'],
            'cb_cashbook' => $data['cb_cashbook'],
            'add' => $data['add'],
            'less' => $data['less'],
            'cb_passbook' => $data['cb_passbook'],
            'scheme' => $data['scheme'],
            'branch' => $data['branch'],
        ];

        if ($existing) {
            // Update the existing record
            $record['updatedon'] = now();

            DB::table('audit.report_accountdetails')
                ->where('auditscheduleid', $data['auditscheduleid'])
                ->where('instid', $data['instid'])
                ->update($record);
        } else {
            // Insert new record
            $record['auditscheduleid'] = $data['auditscheduleid'];
            $record['instid'] = $data['instid'];
            $record['createdon'] = now();

            DB::table('audit.report_accountdetails')->insert($record);
        }
    }

   
    public static function StoreAuthorityOfAudit($data)
    {
        // Check if record exists
        $existingRecord = DB::table('audit.report_authorityofaudit')
            ->where('scheduleid', $data['auditscheduleid'])
            ->where('instid', $data['instid'])
            ->first();

        $StoreData = [
            'scheduleid' => $data['auditscheduleid'],
            'instid' => $data['instid'],
            //'remarks' => json_encode(['content' => $data['authorityofaudit']]),
            'statusflag' => $data['finaliseflag'],
            'updatedon' => now(),
        ];

        if ($existingRecord) {
            // Update existing record
            DB::table('audit.report_authorityofaudit')
                ->where('authorityofaudit_id', $existingRecord->authorityofaudit_id)
                ->update($StoreData);

            return $existingRecord->authorityofaudit_id;
        } else {
            // Insert new record, add createdon timestamp
            $StoreData['createdon'] = now();

            return DB::table('audit.report_authorityofaudit')->insertGetId($StoreData, 'authorityofaudit_id');
        }
    }

    public static function StoreAuditCertificate($data)
    {
        // Check if record exists
        $existingRecord = DB::table('audit.report_auditcertificate')
            ->where('scheduleid', $data['auditscheduleid'])
            ->where('instid', $data['instid'])
            ->first();

        $StoreData = [
            'scheduleid' => $data['auditscheduleid'],
            'instid' => $data['instid'],
            'cer_type_code' => $data['cer_typecode'],
            'cer_remarks' => json_encode(['content' => $data['cer_remarks']]),
            'statusflag' => $data['finaliseflag'],
            'updatedon' => now(),
        ];

        if ($existingRecord) {
            // Update existing record
            DB::table('audit.report_auditcertificate')
                ->where('cer_id', $existingRecord->cer_id)
                ->update($StoreData);

            return $existingRecord->cer_id;
        } else {
            // Insert new record, add createdon timestamp
            $StoreData['createdon'] = now();

            return DB::table('audit.report_auditcertificate')->insertGetId($StoreData, 'cer_id');
        }
    }

 public static function fetch_listinstitutes($sessionuserid, $finalizeflag, $instid, $whom)
    {

        $userChargeData = session('charge');

        $usertypecode = $userChargeData->usertypecode;
        if ($usertypecode == 'I') {
            $sessioninstid = $userChargeData->instid;
        }
        $deptcode = $userChargeData->deptcode ?? null;
        $distcode = $userChargeData->distcode ?? null;
        $auditteamhead = $userChargeData->auditteamhead ?? null;
        $query = DB::table(self::$InstscheduleMem_Table . ' as scm')
            ->join(self::$Instschedule_Table . ' as sc', 'sc.auditscheduleid', '=', 'scm.auditscheduleid')
            ->join(self::$AuditPlan_Table . ' as ap', 'ap.auditplanid', '=', 'sc.auditplanid')
            ->join(self::$Institution_Table . ' as mi', 'mi.instid', '=', 'ap.instid')
            ->join('audit.mst_district as dist', 'mi.distcode', '=', 'dist.distcode')
            ->join('audit.mst_region as reg', 'reg.regioncode', '=', 'mi.regioncode')
            ->where('auditeeresponse', 'A')
            ->where('ap.auditquartercode', '=', 'Q1');

        if ($finalizeflag) {
            $query->where('sc.sendintimation', '=', 'F');
        }
        if ($usertypecode == 'I') {
            $query->where('mi.instid', '=', $sessioninstid)
                ->where('sc.issuedflag', 'Y');
        }
        // if($whom =='AH')
        //{
        //   $query->where('scm.auditteamhead', '=', 'Y');
        // $query->where('scm.userid', '=', $sessionuserid);

        // }else if($whom == 'AU')
        // {
        //  $query->where('scm.userid', '=', $sessionuserid);

        // }else if($whom == 'AI')
        // {
        //    $query->where('mi.instid', '=',$instid);
        // }

        if (empty($auditteamhead)) {
            if ($whom === 'AI') {
                $query->where('mi.instid', '=', $instid);
            } else {
                $query->where('mi.deptcode', '=', $deptcode)
                    ->where('mi.distcode', '=', $distcode);
            }
        } else {
            if ($whom === 'AH') {
                $query->where('scm.auditteamhead', '=', 'Y')
                    ->where('scm.userid', '=', $sessionuserid);
            } elseif ($whom === 'AU') {
                $query->where('scm.userid', '=', $sessionuserid);
            } elseif ($whom === 'AI') {
                $query->where('mi.instid', '=', $instid);
            }
        }


        return $query->groupBy(
            'sc.auditscheduleid',
            'ap.auditplanid',
            'ap.instid',
            'mi.instename',
            'mi.mandays',
            'sc.fromdate',
            'sc.todate',
            'sc.entrymeetdate',
            'sc.exitmeetdate',
            'dist.distename',
            'reg.regionename'
        )
            ->select(
                'sc.auditscheduleid',
                'sc.fromdate',
                'sc.todate',
                'ap.auditplanid',
                'ap.instid',
                'mi.instename',
                'mi.mandays',
                'sc.entrymeetdate',
                'sc.exitmeetdate',
                'dist.distename',
                'reg.regionename',
                'sc.issuedflag',
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
                ) AS team_members_en")
            )
            ->get();
    }

  public static function getreportingofcrdetails($instid)
    {
        try {
            if (empty($instid)) {
                throw new \Exception("schedule Details not found");
            }
            $query = DB::table(self::$auditeedeptreport_table)
                ->where('instid', $instid)
                ->where('statusflag', 'Y')
                ->select('email')
                ->get();

            return $query;
        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public static function fetchissuingSchdet($scheduleid)
    {

        try {
            if (empty($scheduleid)) {
                throw new \Exception("schedule Details not found");
            }
            $query = DB::table(self::$Instschedule_Table . ' as schd')
                ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'schd.auditplanid')
                ->join(self::$Institution_Table . '  as inst', 'ap.instid', '=', 'inst.instid')
                ->join(self::$TransAuditSlip_Table . '  as trans', 'trans.auditscheduleid', '=', 'schd.auditscheduleid')
                ->join(self::$MapYearcode_Table . '  as yrmap', 'yrmap.auditplanid', '=', 'schd.auditplanid')
                ->join('audit.mst_auditperiod as d', DB::raw('CAST(yrmap.yearselected AS INTEGER)'), '=', 'd.auditperiodid')

                ->select(
                    'schd.auditscheduleid',
                    'schd.rcno',
                    'inst.instename as institutionname',
                    DB::raw('COUNT(trans.auditslipid) as para_count'),
		    //DB::raw('COUNT(DISTINCT trans.auditslipid) AS para_count'),
DB::raw("COUNT(DISTINCT CASE WHEN trans.processcode = 'X' THEN trans.auditslipid END) AS para_count"),
                    DB::raw('STRING_AGG(DISTINCT d.fromyear || \'-\' || d.toyear, \', \') as yearselected')

                )
                ->where('schd.auditscheduleid', $scheduleid)
                //->where('trans.processcode', 'X')
                ->where('trans.auditscheduleid', $scheduleid)
                ->where('schd.statusflag', 'F')
                ->groupBy(

                    'schd.auditscheduleid',
                    'schd.rcno',
                    'inst.instename',
                )
                ->get();

            return $query;
        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

      
     public static function FetchInstituteDetails($session_userid, $auditscheduleid)
    {
        return DB::table(self::$InstscheduleMem_Table . ' as sm')
            ->join(self::$Instschedule_Table . ' as is', 'is.auditscheduleid', '=', 'sm.auditscheduleid')
            ->join(self::$AuditPlan_Table . ' as ap', 'ap.auditplanid', '=', 'is.auditplanid')
            ->join(self::$Institution_Table . ' as in', 'in.instid', '=', 'ap.instid')
            ->join(self::$MstAuditeeInsCategory_Table . ' as incat', 'incat.catcode', '=', 'in.catcode')
            ->join(self::$TypeofAudit_Table . ' as ta', 'ta.typeofauditcode', '=', 'ap.typeofauditcode')
            ->join(self::$Dept_Table . ' as dept', 'in.deptcode', '=', 'dept.deptcode')
            //  ->join('audit.mst_auditperiod as d', 'd.auditperiodid', '=', 'ap.auditperiodid')
            ->join(self::$MapYearcode_Table . ' as yrmap', 'yrmap.auditplanid', '=', 'ap.auditplanid')
            ->join(
                self::$AuditPeriod_Table . ' as d',
                DB::raw('CAST(yrmap.yearselected AS INTEGER)'),
                '=',
                'd.auditperiodid'
            )
            ->where('is.auditscheduleid', $auditscheduleid)
            // Apply STRING_AGG to aggregate years

            ->select(
                'is.auditscheduleid',
                'sm.auditscheduleid',
                'sm.auditteamhead',
                'is.auditplanid',
                'is.fromdate',
                'is.todate',
                'is.entrymeetdate',
                'is.exitmeetdate',
                'ap.instid',
                'dept.deptelname',
                'dept.depttlname',
                'dept.deptcode',
                'in.instename',
                'in.insttname',
                'incat.catename',
                'incat.cattname',
                'incat.catcode',
                'incat.if_subcategory',
                'in.mandays',
                'sm.auditteamhead',
                'ta.typeofauditename',
                'ta.typeofaudittname',
                'sm.schteammemberid',
                DB::raw("
                    STRING_AGG(
                        DISTINCT 
                        CASE 
                            WHEN d.toyear IS NULL THEN d.fromyear::text
                            ELSE d.fromyear::text || '-' || d.toyear::text
                        END,
                        ', '
                    ) 
                    FILTER (WHERE yrmap.financestatus = 'Y' AND yrmap.statusflag ='Y') AS annadhanamyearname
            "),
                DB::raw("
                STRING_AGG(
                    DISTINCT 
                    CASE 
                        WHEN d.toyear IS NULL THEN d.fromyear::text
                        ELSE d.fromyear::text || '-' || d.toyear::text
                    END,
                    ', '
                ) 
                FILTER (WHERE yrmap.financestatus = 'N' AND yrmap.statusflag ='Y') AS yearname
            "),
            )
            ->groupby(
                'is.entrymeetdate',
                'is.exitmeetdate',
                'is.auditscheduleid',
                'sm.auditscheduleid',
                'sm.auditteamhead',
                'is.auditplanid',
                'is.fromdate',
                'is.todate',
                'ap.instid',
                'dept.deptcode',
                'dept.deptelname',
                'dept.depttlname',
                'in.instename',
                'in.insttname',
                'incat.catename',
                'incat.cattname',
                'in.mandays',
                'sm.auditteamhead',
                'ta.typeofauditename',
                'sm.schteammemberid',
                'ta.typeofaudittname',
                'incat.catcode'
            )

            ->get();
    }

    /**
     * Store Report Content as JSON
     */
    public static function storeReport($data)
    {
        $explode =explode("_",$data['activeStep']);

        // Prepare JSON data
        $iframeContent = mb_convert_encoding($data['iframeContent'], 'UTF-8', 'auto');

        $jsonData = json_encode(["content" => $iframeContent, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG]);

        // Check if the record exists
        $existingReport = self::where('report_type', $data['activeStepNo'])->first();

        if ($existingReport) {
            // If the record exists, update it

            if($explode[1] == 'en')
            {
                $existingReport->update([
                    'report_name'    => $explode[0],
                    'report_contents' => $jsonData,
                    'statusflag'      => 'Y'
                ]);
            }else
            {
                $existingReport->update([
                    'report_name'    => $explode[0],
                    'report_contents_ta' => $jsonData,
                    'statusflag'      => 'Y'
                ]);

            }
           


            return $existingReport; // Return the updated report

        } else {

            if($explode[1] == 'en')
            {
                $newReport = self::create(['report_type'    => $data['activeStepNo'],
                                          'report_name'    => $explode[0],
                                          'report_contents' => $jsonData,
                                          'statusflag'      => 'Y'
                                        ]);
            }else
            {
                $newReport = self::create(['report_type'    => $data['activeStepNo'],
                                           'report_name'    => $explode[0],
                                           'report_contents_ta' => $jsonData,
                                           'statusflag'      => 'Y'
                                        ]);

            }
           
            return $newReport; // Return the newly created report
        }
    }

    /**
     * Retrieve the latest iframe content.
     */
    public static function getLatestIframeContent()
    {
        return self::latest()->first();
    }

    public static function getTeamMembers($auditeamid)
    {
        $table = self::$auditplanteammemberTable;

        return DB::table($table . '  as apt')
            ->join(self::$UserDetails_Table . ' as dud', 'apt.userid', '=', 'dud.deptuserid')
            ->join(self::$Designation_Table . ' as msd', 'dud.desigcode', '=', 'msd.desigcode')
            ->where('apt.auditplanteamid', $auditeamid)
            ->select('apt.userid', 'dud.username', 'dud.usertamilname','msd.desigelname','msd.desigtlname')
            ->orderBy('dud.desigcode', 'asc')
            ->orderBy('dud.deptuserid', 'asc')
            ->get();
    }


    public static function GetSchedultedEventDetails($scheduleid)
    {
        $table = self::$Instschedule_Table;
        $instScheduleMemTable = self::$InstscheduleMem_Table;
        $userDetailsTable = self::$UserDetails_Table;
        $desigtable=self::$Designation_Table;
    
        return DB::table($table)
            // Joins
            ->join(self::$InstscheduleMem_Table . ' as inm', 'inm.auditscheduleid', '=', "$table.auditscheduleid")
            ->join(self::$AuditPlan_Table . ' as ap', 'ap.auditplanid', '=', "$table.auditplanid")
            ->join(self::$AuditPlanTeam_Table . ' as at', 'ap.auditteamid', '=', 'at.auditplanteamid')
            ->join(self::$Institution_Table . ' as ai', 'ai.instid', '=', 'ap.instid')
            ->join(self::$TypeofAudit_Table . ' as mst', 'mst.typeofauditcode', '=', 'ap.typeofauditcode')
            ->join(self::$Dept_Table . ' as msd', 'msd.deptcode', '=', 'ai.deptcode')
            ->join(self::$District_Table . ' as msi', 'msi.distcode', '=', 'ai.distcode')
            ->join(self::$MstAuditeeInsCategory_Table . ' as mac', 'mac.catcode', '=', 'ai.catcode')
            ->join(self::$AuditQuarter_Table . ' as maq', 'maq.auditquartercode', '=', 'ap.auditquartercode')
            ->join(self::$UserChargeDetails_Table . ' as uc', 'uc.userid', '=', 'inm.userid')
            ->join(self::$UserDetails_Table . ' as du', 'uc.userid', '=', 'du.deptuserid')
            ->join(self::$ChargeDetails_Table . ' as cd', 'uc.chargeid', '=', 'cd.chargeid')
            ->join(self::$Designation_Table . ' as de', 'de.desigcode', '=', 'du.desigcode')
            ->join(self::$MapYearcode_Table . ' as yrmap', 'yrmap.auditplanid', '=', 'ap.auditplanid')
            ->join(self::$AuditPeriod_Table . ' as period', DB::raw('CAST(yrmap.yearselected AS INTEGER)'), '=', 'period.auditperiodid')
    
            // Where conditions
            ->where("$table.auditscheduleid", '=', $scheduleid)
            ->where('inm.statusflag', '=', 'Y')
    
            // Select columns
            ->select(
                "$table.auditscheduleid",
                "$table.fromdate",
                "$table.todate",
                "$table.auditeeresponse",
                "$table.entrymeetdate",
                "$table.exitmeetdate",
                "$table.rcno",
                "$table.nodalname",
                "$table.nodalmobile",
                "$table.nodalemail",
                "$table.nodaldesignation",
                'ai.subcatid',
                'inm.userid',
                'du.username',
                'du.usertamilname',
                'ai.instename',
                'ai.insttname',
                'ai.mandays',
                'ai.deptcode',
                'ai.instid',
                'ai.auditeeofficeaddress',
                'ap.auditteamid',
                'ap.auditplanid',
                'at.teamname',
                'mst.typeofauditename',
                'mst.typeofaudittname',
                'msd.deptesname',
                'msd.deptelname',
                'msd.depttlname',
                'msi.distename',
                'msi.disttname',
                'mac.catename',
                'mac.cattname',
                'maq.auditquarter',
                'ap.statusflag',
                'cd.chargedescription',
                'de.desigelname',
                'inm.auditteamhead',
                'ai.catcode',
                DB::raw("
                STRING_AGG(
                    DISTINCT 
                    CASE 
                        WHEN period.toyear IS NULL THEN period.fromyear::text
                        ELSE period.fromyear::text || '-' || period.toyear::text
                    END,
                    ', '
                ) 
                FILTER (WHERE yrmap.financestatus = 'N' AND yrmap.statusflag ='Y') AS yearname
            "),
            DB::raw("
                STRING_AGG(
                    DISTINCT 
                    CASE 
                        WHEN period.toyear IS NULL THEN period.fromyear::text
                        ELSE period.fromyear::text || '-' || period.toyear::text
                    END,
                    ', '
                ) 
                FILTER (WHERE yrmap.financestatus = 'Y' AND yrmap.statusflag ='Y') AS annadhanamyearname
            "),
    
                // Fetch team head's username (English)
                DB::raw("(SELECT STRING_AGG(CONCAT(dud.username, ' (', desig.desigelname, ')'), ', ') 
                          FROM $instScheduleMemTable AS schteam
                          JOIN $userDetailsTable AS dud ON dud.deptuserid = schteam.userid
                          JOIN $desigtable AS desig ON dud.desigcode = desig.desigcode
                          WHERE schteam.auditscheduleid = $table.auditscheduleid
                          AND schteam.auditteamhead = 'Y'
                         ) AS teamhead_en"),
    
                // Fetch team head's username (Tamil)
                DB::raw("(SELECT STRING_AGG(CONCAT(dud.usertamilname, ' (', desig.desigtlname, ')'), ', ') 
                          FROM $instScheduleMemTable AS schteam
                          JOIN $userDetailsTable AS dud ON dud.deptuserid = schteam.userid
                          JOIN $desigtable AS desig ON dud.desigcode = desig.desigcode
                          WHERE schteam.auditscheduleid = $table.auditscheduleid
                          AND schteam.auditteamhead = 'Y'
                         ) AS teamhead_ta"),


                DB::raw("(SELECT STRING_AGG(CONCAT(dud.username, ' (', desig.desigelname, ')'), ', ') 
                         FROM $instScheduleMemTable AS schteam
                         JOIN $userDetailsTable AS dud ON dud.deptuserid = schteam.userid
                         JOIN $desigtable AS desig ON dud.desigcode = desig.desigcode
                         WHERE schteam.auditscheduleid = $table.auditscheduleid
                         AND schteam.statusflag = 'Y' AND   schteam.auditteamhead = 'N'
                        ) AS teammembers_en"),
                
                DB::raw("(SELECT STRING_AGG(CONCAT(dud.usertamilname, ' (', desig.desigtlname, ')'), ', ') 
                         FROM $instScheduleMemTable AS schteam
                         JOIN $userDetailsTable AS dud ON dud.deptuserid = schteam.userid
                         JOIN $desigtable AS desig ON dud.desigcode = desig.desigcode
                         WHERE schteam.auditscheduleid = $table.auditscheduleid
                         AND schteam.statusflag = 'Y' AND   schteam.auditteamhead = 'N'
                        ) AS teammembers_ta")
               
              
            )
    
            // Group by clause
            ->groupBy(
                "$table.auditscheduleid",
                "$table.fromdate",
                "$table.todate",
                "$table.auditeeresponse",
                "$table.entrymeetdate",
                "$table.exitmeetdate",
                "$table.rcno",
                'ai.subcatid',
                'inm.userid',
                'du.username',
                'du.usertamilname',
                'ai.instename',
                'ai.insttname',
                'ai.mandays',
                'ai.deptcode',
                'ai.instid',
                'ap.auditteamid',
                'ap.auditplanid',
                'at.teamname',
                'mst.typeofauditename',
                'mst.typeofaudittname',
                'msd.deptesname',
                'msd.deptelname',
                'msd.depttlname',
                'msi.distename',
                'msi.disttname',
                'mac.catename',
                'mac.cattname',
                'maq.auditquarter',
                'ap.statusflag',
                'cd.chargedescription',
                'de.desigelname',
                'inm.auditteamhead'
            )
            ->first(); // Execute the query
    }
    
    


    public static function fetch_allocatedwork($auditscheduleid)
    {
        $table = self::$TransWorkAllocation_Table;

        return DB::table($table)
            ->join(self::$InstscheduleMem_Table . ' as inm', 'inm.schteammemberid', '=', $table . '.schteammemberid')
            ->join(self::$Instschedule_Table . ' as asch', 'asch.auditscheduleid', '=', $table . '.auditscheduleid')
            ->join(self::$mapallocationobjection_table . ' as map', 'map.mapallocationobjectionid', '=', $table . '.workallocationtypeid')
            ->join(self::$MajWorkAllocation_Table . ' as major', 'major.majorworkallocationtypeid', '=', 'map.majorworkallocationtypeid')
          
            //->join(self::$MapWorkAllocation_Table . ' as map', 'map.workallocationtypeid', '=', $table . '.workallocationtypeid')
            //->join(self::$MajWorkAllocation_Table . ' as major', 'major.majorworkallocationtypeid', '=', 'map.majorworkallocationtypeid')
            ->join(self::$UserDetails_Table . ' as dept', 'dept.deptuserid', '=', 'inm.userid')
            ->join(self::$Designation_Table . ' as desig', 'desig.desigcode', '=', 'dept.desigcode')
            ->where($table . '.auditscheduleid', $auditscheduleid)
            ->select(
                'dept.deptuserid',
                'dept.username',
                'desig.desigelname',
                'desig.desigtlname',
                DB::raw("string_agg(DISTINCT major.majorworkallocationtypeename, '<br>' ORDER BY major.majorworkallocationtypeename ASC) as worktypes_first"),
                DB::raw('string_agg(DISTINCT major.majorworkallocationtypeename, \',\' ORDER BY major.majorworkallocationtypeename ASC) as worktypes')
            )
            ->groupBy(
                'dept.deptuserid',
                'dept.username',
                'desig.desigelname',
                'desig.desigtlname',
            )
            ->orderBy('dept.desigcode', 'asc')
            ->orderBy('dept.deptuserid', 'asc') // Order by deptuserid
            ->get();
    }

    public static function AuditeeUsers($auditscheduleid)
    {

        $fetch_auditeeofficeusers = DB::table('audit.auditee_office_users')
                                    ->where('auditscheduleid', $auditscheduleid)
                                    ->get();


        return $fetch_auditeeofficeusers;

    }

    public static function FetchGistObjections($auditscheduleid)
    {
        $table = self::$TransAuditSlip_Table;

        $auditSlips =  DB::table($table.' as auditslip')
                        ->join(self::$ProcessFlag_Table . ' as p', 'p.processcode', '=', 'auditslip.processcode')
                        ->join(self::$MajorObj_Table . ' as m', 'm.mainobjectionid', '=', 'auditslip.mainobjectionid')
                        ->join(self::$SubObj_Table . ' as s', 's.subobjectionid', '=', 'auditslip.subobjectionid')
                        //->join(self::$UserDetails_Table . ' as dud', 'dud.deptuserid', '=', 'auditslip.createdby')
                        ->join(self::$AuditPlan_Table . ' as ap', 'ap.auditplanid', '=', 'auditslip.auditplanid')
                        //->join(self::$AuditeeUserDetail_Table . ' as auditee', 'ap.instid', '=', 'auditee.instid')
                        //->join('audit.slipfileupload as fileupload', 'fileupload.auditslipid', '=', 'auditslip.auditslipid')
                        ->select('m.objectionename', 'm.objectiontname', 's.subobjectionename', 's.subobjectiontname', 'auditslip.amtinvolved', 'auditslip.slipdetails', 'p.processelname', 'p.processcode','auditslip.auditslipid','auditslip.mainslipnumber')                        
                        ->where('auditslip.processcode', 'X')
                        ->where('auditslip.auditscheduleid', $auditscheduleid)
                        ->orderBy('auditslip.irregularitiescode', 'asc')
                        ->orderBy('auditslip.irregularitiescatcode', 'asc')
                        ->orderBy('auditslip.irregularitiessubcatcode', 'asc')
                        ->orderBy('auditslip.mainslipnumber', 'asc')
                        ->get();

        return response()->json([
                            'status' => 'success',
                            'data' => $auditSlips
                        ]);

    }

    public static function FetchAuditSlips($auditscheduleid, $irregularity)
    {
       // Step 1: Get custom slip order JSON
        $orderRow = DB::table('audit.report_storesliporder')
            ->where('auditscheduleid', $auditscheduleid)
            ->first();

        if($irregularity == '01')
        {
            $customOrderJson = $orderRow?->ser_ordered_slips;
        }else if($irregularity == '02')
        {
           $customOrderJson = $orderRow?->nonser_ordered_slips;

        }

        // Step 2: Decode and prepare ordering (if available)
        $customOrder = [];
        $orderCase = null;

        if ($customOrderJson) {
            $customOrder = json_decode($customOrderJson, true);
            $orderValues = array_map('intval', array_values($customOrder)); // Ensure numeric

            if (!empty($orderValues)) {
                $orderCase = 'CASE auditslip.auditslipid ';
                foreach ($orderValues as $index => $id) {
                    $orderCase .= "WHEN $id THEN $index ";
                }
                $orderCase .= 'ELSE ' . (count($orderValues) + 1) . ' END';
            }
        }

        // Step 3: First slip history subquery
        $firstHistorySubquery = DB::table(self::$SlipHistroyDetails_Table)
            ->select(DB::raw('MIN(transhistoryid) as transhistoryid'))
            ->groupBy('auditslipid');

        // Step 4: Main query
        $auditSlipsQuery = DB::table(self::$SlipHistroyDetails_Table . ' as sh')
            ->joinSub($firstHistorySubquery, 'firstsh', function ($join) {
                $join->on('sh.transhistoryid', '=', 'firstsh.transhistoryid');
            })
            ->join(self::$TransAuditSlip_Table . ' as auditslip', 'auditslip.auditslipid', '=', 'sh.auditslipid')
            ->join(self::$ProcessFlag_Table . ' as p', 'p.processcode', '=', 'sh.processcode')
            ->join(self::$MajorObj_Table . ' as m', 'm.mainobjectionid', '=', 'sh.mainobjectionid')
            ->join(self::$SubObj_Table . ' as s', 's.subobjectionid', '=', 'sh.subobjectionid')
            ->join(self::$AuditPlan_Table . ' as ap', 'ap.auditplanid', '=', 'sh.auditplanid')
            ->join(self::$AuditeeUserDetail_Table . ' as auditee', 'ap.instid', '=', 'auditee.instid')
            ->join('audit.mst_irregularitiescategory as irrcat', 'irrcat.irregularitiescatcode', '=', 'auditslip.irregularitiescatcode')
            ->join('audit.mst_irregularitiessubcategory as irrsubcat', 'irrsubcat.irregularitiessubcatcode', '=', 'auditslip.irregularitiessubcatcode')
            ->select(
                'auditee.username',
                'm.objectionename', 'm.objectiontname',
                's.subobjectionename', 's.subobjectiontname',
                'auditslip.amtinvolved',
                'sh.slipdetails',
                'p.processelname', 'p.processcode',
                'sh.liability', 'sh.remarks as sliphistory_remarks','auditslip.remarks as slip_remarks','auditslip.auditslipid', 'sh.severityid',
                'auditslip.mainslipnumber',
                'irrcat.irregularitiescatelname','irrcat.irregularitiescatcode',
                'irrsubcat.irregularitiessubcatelname','irrsubcat.irregularitiessubcatcode'
            )
            ->where('auditslip.processcode', 'X')
            ->where('auditslip.irregularitiescode', $irregularity)
            ->where('auditslip.auditscheduleid', $auditscheduleid);

        // ✅ Apply custom order only if available
        if ($orderCase) {
            $auditSlipsQuery->orderByRaw($orderCase);
        }else{
             $auditSlipsQuery->orderBy('auditslip.irregularitiescode', 'asc');
             $auditSlipsQuery->orderBy('auditslip.irregularitiescatcode', 'asc');
             $auditSlipsQuery->orderBy('auditslip.irregularitiessubcatcode', 'asc');
             $auditSlipsQuery->orderBy('auditslip.mainslipnumber', 'asc');
        }

        $auditSlips = $auditSlipsQuery->get();

        $paratable ='audit.report_paradetails';

        $SlipHistroyDetails_Table = self::$SlipHistroyDetails_Table;

        foreach ($auditSlips as $slip) 
        {

            $slipno = $slip->auditslipid;   

            $ParaExists = DB::table($paratable)
                                ->where('auditscheduleid', $auditscheduleid)
                                ->exists();


            if ($ParaExists) {
                $remarks = DB::table($paratable)
                    ->where($paratable . '.slip_id', $slipno)
                    ->where($paratable . '.auditscheduleid', $auditscheduleid)
                    ->value('remarks'); // get only remarks

                $slipdetails = DB::table($paratable)
                    ->where($paratable . '.slip_id', $slipno)
                    ->where($paratable . '.auditscheduleid', $auditscheduleid)
                    ->value('slipdetails'); // get only remarks
            } else {
                $remarks = $slip->sliphistory_remarks;   
                
                $slipdetails = $slip->slipdetails;  
            }

            // Attach to slip object
            $slip->final_remarks = $remarks;
            $slip->final_slipdetails = $slipdetails;

        }

        // Step 5: Fetch liability
        $LiabilityDetails = DB::table('audit.liability as liability')
            ->join(self::$TransAuditSlip_Table . ' as auditslip', 'liability.auditslipid', '=', 'auditslip.auditslipid')
            ->select(
                'auditslip.mainslipnumber',
                'liability.liabilityname',
                'liability.liabilitygpfno',
                'liability.liabilitydesignation',
                'liability.liabilityamount',
                'liability.notype'
            )
            ->where('auditslip.auditscheduleid', $auditscheduleid)
            ->get();

        // Final response
        return response()->json([
            'status' => 'success',
            'data' => $auditSlips,
            'liability' => $LiabilityDetails
        ]);

    }


    public static function FetchAuditSlipsbyID($auditscheduleid,$slipno)
    {
        // Fetch data from the database where processcode = 'X'
        $table  = self::$SlipHistroyDetails_Table;
        $table1 = self::$TransAuditSlip_Table;

        $paratable ='audit.report_paradetails';


        $ParaExists =  DB::table($paratable)
                        ->where($paratable . '.slip_id', $slipno)
                        ->where($paratable . '.auditscheduleid', $auditscheduleid)
                        ->exists();

      //  echo 'hiihloo';exit;

        $minTransId = DB::table($table)
                        ->where('auditslipid', $slipno)
                        ->min('transhistoryid');

        $auditSlips = DB::table($table)
                ->join($table1 . ' as auditslip', 'auditslip.auditslipid', '=', $table . '.auditslipid')
                ->join(self::$ProcessFlag_Table . ' as p', 'p.processcode', '=', $table . '.processcode')
                ->join(self::$MajorObj_Table . ' as m', 'm.mainobjectionid', '=', $table . '.mainobjectionid')
                ->join(self::$SubObj_Table . ' as s', 's.subobjectionid', '=', $table . '.subobjectionid')
                ->join(self::$AuditPlan_Table . ' as ap', 'ap.auditplanid', '=', $table . '.auditplanid')
                ->join(self::$AuditeeUserDetail_Table . ' as auditee', 'ap.instid', '=', 'auditee.instid')
                ->select(
                    'auditee.username',
                    'm.objectionename', 'm.objectiontname',
                    's.subobjectionename', 's.subobjectiontname',
                    'auditslip.amtinvolved',
                    'p.processelname', 'p.processcode',
                    $table . '.liability',
                    $table . '.remarks',
                    $table . '.severityid',
                    'auditslip.mainslipnumber'
                )
                ->where($table . '.transhistoryid', $minTransId)
                ->first();


       if ($ParaExists) {
            $remarks = DB::table($paratable)
                ->where($paratable . '.slip_id', $slipno)
                ->where($paratable . '.auditscheduleid', $auditscheduleid)
                ->value('remarks'); // get only remarks

            $slipdetails = DB::table($paratable)
                ->where($paratable . '.slip_id', $slipno)
                ->where($paratable . '.auditscheduleid', $auditscheduleid)
                ->value('slipdetails'); // get only remarks
        } else {
            $remarks = DB::table($table)
                ->where($table . '.auditslipid', $slipno)
                ->where($table . '.transhistoryid', $minTransId)
                ->value('remarks');
            
            $slipdetails = DB::table($table)
                ->where($table . '.auditslipid', $slipno)
                ->where($table . '.transhistoryid', $minTransId)
                ->value('slipdetails'); // get only remarks
        }

        // Attach remarks manually to result
        if ($auditSlips) {
            $auditSlips->remarks = $remarks;
            $auditSlips->slipdetails = $slipdetails;
        }

        $slipattachments = DB::table($paratable)
            ->where($paratable . '.slip_id', $slipno)
            ->where($paratable . '.auditscheduleid', $auditscheduleid)
            ->value('slip_attachments');

        // Check and parse if para exists
        $attachmentIds = [];

        if (!empty($slipattachments)) {
            // Handle input like "[2715, 2716]" or "[2715]"
            $slipattachments = trim($slipattachments, "[]"); // Remove square brackets
            $attachmentIds = array_filter(array_map('intval', explode(',', $slipattachments))); // Convert to array of integers
        }

        $fileUploadsQuery = DB::table('audit.slipfileupload as sf')
            ->leftJoin('audit.fileuploaddetail as fu', 'fu.fileuploadid', '=', 'sf.fileuploadid')
            ->select(
                'fu.filename',
                'fu.filepath',
                'fu.filesize',
                'fu.fileuploadid'
            )
            ->where('sf.auditslipid', $slipno);

        // Apply filter if IDs are present
        if (!empty($attachmentIds) && $ParaExists) {
            $fileUploadsQuery->whereIn('sf.fileuploadid', $attachmentIds);
        }

        $fileUploads = $fileUploadsQuery->get();

        $Seriousreport_storesliporder = DB::table('audit.report_paradetails')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->where('irregularitycode','01')
                            ->first();
        $Serstatusflag_storeslip = $Seriousreport_storesliporder->statusflag ?? '';

        $NonSeriousreport_storesliporder = DB::table('audit.report_paradetails')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->where('irregularitycode','02')
                            ->first();
        $NonSerstatusflag_storeslip = $NonSeriousreport_storesliporder->statusflag ?? '';
                              
        return response()->json([
                                'status' => 'success',
                                'slipdata' => $auditSlips,
                                'fileuploads' => $fileUploads,
                                'ser_statusflag_storeslip'=>$Serstatusflag_storeslip,
                                'nonser_statusflag_storeslip'=>$NonSerstatusflag_storeslip

                            ]);

    }


    public static function AllAuditSlips($auditscheduleid,$irregularity)
    {
        //$irregularity ='02';
        $table =self::$TransAuditSlip_Table;
        return  DB::table($table)
                ->join(self::$MajorObj_Table . ' as m', 'm.mainobjectionid', '=', $table .'.mainobjectionid')
                ->join(self::$SubObj_Table . ' as s', 's.subobjectionid', '=', $table .'.subobjectionid')
                ->join('audit.mst_irregularitiescategory as irrcat', 'irrcat.irregularitiescatcode', '=', $table .'.irregularitiescatcode')
                ->join('audit.mst_irregularitiessubcategory as irrsubcat', 'irrsubcat.irregularitiessubcatcode', '=', $table .'.irregularitiessubcatcode')
                ->select('auditslipid','mainslipnumber','m.objectionename', 'm.objectiontname','s.subobjectionename','s.subobjectiontname','irrcat.irregularitiescatelname','irrsubcat.irregularitiessubcatelname')
                ->whereIn('processcode',['X'])
                ->where($table.'.auditscheduleid', $auditscheduleid)
                ->where($table.'.irregularitiescode', $irregularity)
                ->orderBy($table . '.irregularitiescatcode', 'asc')
                ->orderBy($table . '.irregularitiessubcatcode', 'asc')
                ->orderBy($table . '.mainslipnumber', 'asc')
                ->get();

    }


    public static function Paracount($auditscheduleid,$paratype)
    {
        $table = self::$TransAuditSlip_Table;

        return DB::table($table)
                 ->selectRaw(
                    $paratype == 'pendingpara' 
                    ? "COUNT(DISTINCT CASE WHEN processcode NOT IN ('A', 'X') THEN auditslipid END) as pendingslips" 
                    : "COUNT(DISTINCT CASE WHEN processcode IS NOT NULL THEN auditslipid END) as totalslips"
                 )
                ->where($table . '.auditscheduleid', $auditscheduleid)
                ->first();
        
    }

        public static function getauditeedetails($scheduleid)
    {

        $query =  DB::table('audit.inst_auditschedule as ins')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'ins.auditplanid')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
            ->join('audit.audtieeuserdetails as auditee', 'inst.instid', '=', 'auditee.instid')
            ->where('ins.auditscheduleid', $scheduleid)
            ->select('auditee.email as auditeeemail', 'auditee.username as auditeeusername', 'inst.instid')
            ->get();

        return $query;
    }

    public static function accountparticulars($auditscheduleid,$accountcode)
    {
       $select = DB::table(self::$TransaccountDetails_Table .' as accdet')
                  ->join(self::$FileUpload_Table .' as fileupload', 'fileupload.fileuploadid', '=', 'accdet.fileuploadid')
                  ->select('filepath')
                  ->where('accdet.auditscheduleid', $auditscheduleid)
                  ->where('accdet.accountcode', $accountcode)
                  ->first();
        if($select)
        {
            $res = $select;
        }else
        {
            $res = 'nodata';
        }
        return  $res;

    }

  public static function commondeptfetch()
    {
        return DB::table(self::$Dept_Table . ' as dept')
            ->select('dept.deptelname', 'dept.deptcode', 'dept.depttlname') // Select required columns
            ->where('dept.statusflag', '=', 'Y') // Use the correct table alias for `statusflag`
            ->orderBy('dept.deptcode', 'asc')
            ->get();
    }


   public static function getinstitutionBydistrictchange($district, $regioncode, $deptcode)
    {
        $table = self::$Institution_Table;

        $query =  DB::table($table . ' as ins')
            ->join('audit.auditplan' . ' as plan', 'ins.instid', '=', 'plan.instid')
            ->join('audit.inst_auditschedule' . ' as sch', 'sch.auditplanid', '=', 'plan.auditplanid')
            ->select('ins.instename', 'ins.instid', 'ins.insttname', 'sch.auditscheduleid')
            ->distinct()
            ->where('ins.distcode', $district)
            ->where('ins.deptcode', $deptcode)
            ->where('ins.regioncode', $regioncode)
            ->where('plan.statusflag', 'F')
            ->where('plan.auditquartercode', 'Q1')
            ->where('sch.workallocationflag', 'Y')
            ->where('sch.statusflag', 'F')
 	    ->where('sch.sendintimation', 'F')
            ->whereNotNull('sch.exitmeetdate');

        $query->where(function ($q) {
            $q->where('sch.issuedflag', '!=', 'Y')
                ->orWhereNull('sch.issuedflag');
        });

        $result =  $query->get();

        return $result;
    }



    public static function getRegionsByDept($deptcode)
    {
        $table = self::$Institution_Table;

        return DB::table($table . ' as ins')
            ->join(self::$regionTable . ' as reg', 'ins.regioncode', '=', 'reg.regioncode')
            ->select('reg.regioncode', 'reg.regionename', 'reg.regiontname')
            ->distinct()
            ->where('ins.deptcode', $deptcode)
            ->where('ins.statusflag', 'Y')
            ->orderBy('reg.regionename', 'Asc')
            ->get();
    }

    public static function getdistrictByregion($regioncode, $deptcode)
    {
        $table = self::$Institution_Table;

        return DB::table($table . ' as ins')
            ->join('audit.auditplan as plan', 'ins.instid', '=', 'plan.instid')
            ->join('audit.inst_auditschedule as sch', 'sch.auditplanid', '=', 'plan.auditplanid')
            ->join(self::$District_Table . ' as dis', 'ins.distcode', '=', 'dis.distcode')
            ->select('dis.distename', 'dis.distcode', 'dis.disttname')
            ->distinct()
            ->where('ins.deptcode', $deptcode)
            ->where('ins.regioncode', $regioncode)
            ->where('ins.statusflag', 'Y')
            ->get();
    }


    public static function getUsernameBasedOnInstitution($instMappingCode)
    {

        $table = self::$Institution_Table;

        return DB::table($table . ' as ins')
            ->join('audit.auditplan as plan', 'ins.instid', '=', 'plan.instid')
            // ->join('audit.mst_dept as dept', 'dept.currentquarter', '=', 'plan.auditquartercode')
            ->join('audit.inst_auditschedule as sch', 'sch.auditplanid', '=', 'plan.auditplanid')
            ->join('audit.inst_schteammember as schmem', 'schmem.auditscheduleid', '=', 'sch.auditscheduleid')
            ->join('audit.deptuserdetails as user', 'user.deptuserid', '=', 'schmem.userid')
            ->select('user.username', 'schmem.schteammemberid', 'schmem.auditteamhead', 'user.deptuserid')
            ->where('user.statusflag', 'Y')

            ->where('schmem.statusflag', 'Y')
            ->groupBy('user.username', 'schmem.schteammemberid', 'schmem.auditteamhead', 'user.deptuserid')
            ->where('ins.instid', $instMappingCode)
            ->where('ins.statusflag', 'Y')
            ->get();
    }



    public static function reportfetchData($table = null)
    {
        $table = 'audit.auditplan';

        $query = DB::table($table . ' as plan')
            ->join(self::$Institution_Table . ' as ins', 'plan.instid', '=', 'ins.instid')
            ->join(self::$Dept_Table . ' as dept_actual', 'ins.deptcode', '=', 'dept_actual.deptcode')
            ->join('audit.inst_auditschedule as sch', 'sch.auditplanid', '=', 'plan.auditplanid')
            ->join(self::$regionTable . ' as reg', 'ins.regioncode', '=', 'reg.regioncode')
            ->join(self::$District_Table . ' as dis', 'ins.distcode', '=', 'dis.distcode')
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
                'dept_actual.deptcode',
                'plan.auditquartercode',
                'dept_actual.deptesname',
                'dept_actual.deptelname',
                'dept_actual.depttsname',
                'dept_actual.depttlname',
                'sch.auditscheduleid',
                'plan.auditplanid',

            )
	

            ->where('plan.statusflag', 'F')
            ->where('sch.statusflag', 'F')
            ->where('ins.statusflag', 'Y')
            ->where('plan.auditquartercode', 'Q1')
            ->where('sch.workallocationflag', 'Y')
            ->whereNotNull('sch.exitmeetdate');
	$query->where(function ($q) {
            $q->where('sch.issuedflag', '!=', 'Y')
                ->orWhereNull('sch.issuedflag');
        });

        return $query->get();
    }




    public static function auditreportrevoke_insertupdate(array $data, $table = null, $userchargeid)
    {
        $auditscheduleid = $data['auditscheduleid'] ?? null;
        $instid = $data['instid'] ?? null;
        $userid = $data['userid'] ?? null;
        $schteammemberid = $data['schteammemberid'] ?? null;

        $errors = [];

        $reportTables = [
            'audit.report_accountdetails' => ['schedule' => 'auditscheduleid', 'inst' => 'instid'],
            'audit.report_annextures' => ['schedule' => 'auditscheduleid'],
            'audit.report_auditcertificate' => ['schedule' => 'scheduleid', 'inst' => 'instid'],
            'audit.report_auditlevycertificate' => ['schedule' => 'scheduleid', 'inst' => 'instid'],
            'audit.report_authorityofaudit' => ['schedule' => 'scheduleid', 'inst' => 'instid'],
            'audit.report_gstreturn_details' => ['schedule' => 'auditscheduleid', 'inst' => 'instid'],
            'audit.report_insitutegenesis' => ['schedule' => 'scheduleid', 'inst' => 'instid'],
            'audit.report_labourwelfarefund' => ['schedule' => 'auditscheduleid', 'inst' => 'instid'],
            'audit.report_pantan' => ['schedule' => 'auditscheduleid', 'inst' => 'instid'],
            'audit.report_paradetails' => ['schedule' => 'auditscheduleid', 'inst' => 'inst_id'],
            'audit.report_pendingparadetails' => ['schedule' => 'scheduleid', 'inst' => 'instid'],
            'audit.report_storesliporder' => ['schedule' => 'auditscheduleid'],
            'audit.report_tds_filed_details' => ['schedule' => 'auditscheduleid', 'inst' => 'instid'],
            'audit.inst_auditschedule' => ['schedule' => 'auditscheduleid']
        ];

        $now = View::shared('get_nowtime');

        DB::beginTransaction(); // Start transaction

        try {
            foreach ($reportTables as $tbl => $columns) {
                $query = DB::table($tbl);

                if (isset($columns['schedule']) && $auditscheduleid) {
                    $query->where($columns['schedule'], $auditscheduleid);
                }

                if (isset($columns['inst']) && $instid) {
                    $query->where($columns['inst'], $instid);
                }

                $updateFields = [
                    'statusflag' => 'Y',
                    'createdon' => $now,
                    'createdby' => $userchargeid,
                    'updatedon' => $now,
                    'updatedby' => $userchargeid,
                ];

                // Table-specific field overrides
                if ($tbl === 'audit.report_annextures') {
                    $updateFields = [
                        'statusflag' => 'Y',
                        'uploadedon' => $now,
                        'uploadedby' => $userchargeid,
                    ];
                }

                if ($tbl === 'audit.report_tds_filed_details') {
                    $updateFields = [
                        'statusflag' => 'Y',
                        'created_on' => $now,
                        'created_by' => $userchargeid,
                        'updated_on' => $now,
                        'updated_by' => $userchargeid,
                    ];
                }

                if ($tbl === 'audit.inst_auditschedule') {
                    $updateFields = [
                        'sendintimation' => 'Y',
                        'updatedon' => $now,
                        'updatedby' => $userchargeid,
                    ];
                }

                $query->update($updateFields);
            }

            DB::commit(); // Commit only if all updates succeed
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback if any failure
            throw new \Exception("Transaction failed:\n" . $e->getMessage());
        }

    }

public static function fetchQuarterData($quarterCode = 'Q1')
{
    $query = DB::table('audit.trans_auditslip as ta')
        ->join('audit.inst_auditschedule as inas', 'inas.auditscheduleid', '=', 'ta.auditscheduleid')
        ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'inas.auditplanid')
        ->join('audit.mst_institution as inst', 'ap.instid', '=', 'inst.instid')
        ->join('audit.mst_dept as dp', 'dp.deptcode', '=', 'inst.deptcode')
        ->join('audit.mst_auditeeins_category as auc', 'auc.catcode', '=', 'inst.catcode')
        ->join('audit.mst_auditeeins_subcategory as aucs', 'aucs.auditeeins_subcategoryid', '=', 'inst.subcatid')
        ->join('audit.liability as li', 'ta.auditslipid', '=', 'li.auditslipid')
        ->where('quartercode', $quarterCode)
        ->where('li.statusflag', 'Y')
        ->where('ta.processcode', 'X')
                        ->select(
                    'dp.deptesname',
                    'auc.catename',
                    'aucs.subcatename',
                    'inst.instename',
                    'ta.slipdetails',
                    DB::raw("
                        STRING_AGG(
                            li.liabilityname || ' - ' 
                            || CASE 
                                WHEN li.notype = '01' THEN 'EPF NO'
                                WHEN li.notype = '02' THEN 'CPS NO'
                                ELSE 'IFHRMS NO'
                            END
                            || ' - '
                            || li.liabilitygpfno || ' - ' 
                            || li.liabilitydesignation || ' - '
                            || li.liabilityamount,
                            ', '
                        ) AS liabilities
                    ")
                )
                ->groupBy(
                    'dp.deptesname',
                    'auc.catename',
                    'aucs.subcatename',
                    'inst.instename',
                    'ta.slipdetails'
                );
                    return $query->get(); // Retrieve all results
                }

public static function Fetchparadetails($scheduleId)
{
    $reportTable = 'audit.report_storesliporder as re';
    $auditSlipTable = self::$TransAuditSlip_Table . ' as auditslip'; 

    $query = DB::table($reportTable)
        ->join($auditSlipTable, 'auditslip.auditscheduleid', '=', 're.auditscheduleid')
        ->select([
            'auditslip.auditslipid',
            'auditslip.irregularitiescode',
            'auditslip.irregularitiescatcode',
            'auditslip.irregularitiessubcatcode',
            'auditslip.amtinvolved',
            DB::raw("(SELECT key::int FROM jsonb_each_text(re.ser_ordered_slips) AS ser(key, val) WHERE val::int = auditslip.auditslipid LIMIT 1) AS ser_key"),
            DB::raw("(SELECT key::int FROM jsonb_each_text(re.nonser_ordered_slips) AS nonser(key, val) WHERE val::int = auditslip.auditslipid LIMIT 1) AS nonser_key"),
        ])
        ->where('auditslip.processcode', 'X')
        ->where('auditslip.auditscheduleid', $scheduleId)
        ->where('re.statusflag', 'Y')
        ->distinct()
        ->orderByRaw('ser_key ASC NULLS LAST')
        ->orderByRaw('nonser_key ASC NULLS LAST')
        ->orderBy('auditslip.irregularitiescode', 'asc')
        ->orderBy('auditslip.irregularitiescatcode', 'asc')
        ->orderBy('auditslip.irregularitiessubcatcode', 'asc');

    $PendingParaDetails = $query->get();

    return response()->json([
        'status' => 'success',
        'data' => $PendingParaDetails,
    ]);
}
  

}
