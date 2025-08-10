<?php

namespace App\Http\Controllers;
//require_once app_path('../vendor/setasign/fpdf/fpdf.php');
//require_once app_path('../vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/IOFactory.php');
//require_once app_path('../vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Shared/File.php');
//require_once app_path('../vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Reader/Xlsx.php');
//require_once app_path('../vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Reader/BaseReader.php');

//require_once __DIR__.'/../vendor/autoload.php';

ini_set("pcre.backtrack_limit", "100000000"); // Increase further if needed
ini_set("memory_limit", "12000M");             // Give more memory to PHP

use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Imagick;

//use App\Models\AuditDiaryModel;
//use App\Models\InstAuditscheduleModel;

use Illuminate\Support\Facades\Crypt;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use DOMDocument;
use Illuminate\Support\Facades\File;
use setasign\Fpdi\Fpdi;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Html; // optional, if rendering HTML
use Illuminate\Support\Facades\View;

use Carbon\Carbon;
//use App\Http\Controllers\AuditSlipController;
//use App\Models\TransWorkAllocationModel;
use App\Models\FormatModel;
use App\Models\AuditManagementModel;
use App\Services\FileUploadService;

use Illuminate\Support\Facades\DB;

use App\Models\SmsmailModel;
use App\Services\SmsService;
use App\Services\PHPMailerService;

class FormatController extends Controller
{

    // Define variables at the class level
    private $tamilfontfile = 'NotoSansTamil-Regular.ttf';
    private $tamilfontname = 'noto';

    
    protected $fileUploadService;


    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

   
   public function DownloadReport(Request $request)
    {
       
        try {
            $auditscheduleid = Crypt::decryptString($request->audit_scheduleid);

            $lang = $request->input('lang');

            $fileName = $this->DownloadAuditReport($auditscheduleid,$lang);
          

            $filePath = public_path('files/' . $fileName);

             

            if (!file_exists($filePath)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            // Stream the file to the user WITHOUT deleting it
            return response()->streamDownload(function () use ($filePath) {
                readfile($filePath);
                unlink($filePath);
            }, $fileName, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['error' => 'Invalid audit schedule ID'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function deleteAnnexureFile(Request $request)
    {
        $auditscheduleid =$request->input('auditscheduleid');
        $annexturetype =$request->input('key');

        $deleteannexture = FormatModel::DeleteAnnexture($auditscheduleid,$annexturetype);

        if($deleteannexture)
        {
            return response()->json([
            'success' => true,
            'data' => 'annexture_deleted']);
        }

    }

 public function getUploadedAnnexures($auditscheduleid)
    {
        // Example structure
        $files = DB::table('audit.report_annextures as ann')
                             ->join('audit.fileuploaddetail as fup', 'fup.fileuploadid', '=', 'ann.fileupload_id')
                            ->select('fup.filepath','fup.filename', 'ann.statusflag','ann.annexture_type')
                             ->where('ann.auditscheduleid',$auditscheduleid)
                            ->where('ann.statusflag', '!=', 'N')
                            ->orderby('ann.annexture_id','asc')
                             ->get();
        $response = [];
        foreach ($files as $file) {
            $response[$file->annexture_type] = [
                'filename' => $file->filename,
                'filepath' => $file->filepath,
                'statusflag' => $file->statusflag,

            ];
        }
        return response()->json($response);
    }
   public function getAnnexureData(Request $request)
{
    $auditscheduleid = $request->input('auditscheduleid');

    // Step 1: Get serious slip count once (only needed for non-serious slips)
    $seriousCount = 0;
    $serSlipRow = DB::table('audit.report_storesliporder')
        ->where('auditscheduleid', $auditscheduleid)
        ->select('ser_ordered_slips')
        ->first();

    if ($serSlipRow && $serSlipRow->ser_ordered_slips) {
        $seriousArray = json_decode($serSlipRow->ser_ordered_slips, true);
        if (is_array($seriousArray)) {
            $seriousCount = count($seriousArray);
        }
    }

    // Step 2: Fetch Annexure slips
    $ParaDetails = DB::table('audit.slipfileupload as fileup')
        ->join('audit.trans_auditslip as slip', 'slip.auditslipid', '=', 'fileup.auditslipid')
        ->join('audit.fileuploaddetail as filedet', 'filedet.fileuploadid', '=', 'fileup.fileuploadid')
        ->where('slip.auditscheduleid', $auditscheduleid)
        //->where('slip.processcode', 'X')
        ->whereIn('slip.processcode',['X'])

        ->select(
            'slip.auditslipid',
            'slip.irregularitiescode',
            'slip.slipdetails',
            'fileup.fileuploadid',
            'filedet.filepath',
            'filedet.filename'
        )
        ->get();

    // Step 3: Attach ParaNo for each record
    $response = [];

    foreach ($ParaDetails as $para) {
        $code = $para->irregularitiescode;
        $slipId = $para->auditslipid;
        $slipOrderColumn = ($code === '01') ? 'ser_ordered_slips' : 'nonser_ordered_slips';

        $slipOrderRow = DB::table('audit.report_storesliporder')
            ->where('auditscheduleid', $auditscheduleid)
            ->select($slipOrderColumn)
            ->first();

        $paraNo = null;

        if ($slipOrderRow && $slipOrderRow->$slipOrderColumn) {
            $orderedArray = json_decode($slipOrderRow->$slipOrderColumn, true);
            if (is_array($orderedArray)) {
                foreach ($orderedArray as $key => $id) {
                    if ((int)$id === (int)$slipId) {
                        $paraNo = ($code === '01') ? (int)$key : $seriousCount + (int)$key;
                        break;
                    }
                }
            }
        }

        $response[] = [
            'auditslipid' => $slipId,
            'slipdetails' => $para->slipdetails,
            'filename' => $para->filename,
            'filepath' => $para->filepath,
            'parano' => $paraNo ? str_pad($paraNo, 4, '0', STR_PAD_LEFT) : '-',
        ];
    }

    return response()->json($response);
}


  public function getstatusflagfromDB(Request $request)
    {
        $auditscheduleid =$request->input('auditscheduleid');

        $auditCertificate = DB::table('audit.report_auditcertificate')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();

                            

        $AuthorityofAudit = DB::table('audit.report_authorityofaudit')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();

        $GenesisofAudit = DB::table('audit.report_insitutegenesis')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();
       
        $Report_PanTan = DB::table('audit.report_pantan')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->first();
        $Report_lWF = DB::table('audit.report_labourwelfarefund')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->first();
                           

        $AccountDetails = DB::table('audit.report_accountdetails')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->first();

        $serious_report_storesliporder = DB::table('audit.report_paradetails')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->where('irregularitycode','01')
                            ->first();

        $Nonserious_report_storesliporder = DB::table('audit.report_paradetails')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->where('irregularitycode','02')
                            ->first();

        $annexture_finalize = DB::table('audit.report_annextures')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->where('statusflag', '!=', 'N')
                            ->first();

       
        $levy_status = DB::table('audit.report_auditlevycertificate')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();

        $pendingpara = DB::table('audit.report_pendingparadetails')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();

        $pendingpara = DB::table('audit.report_pendingparadetails')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();

        $reportfinalizeflag = DB::table('audit.inst_auditschedule')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->select('sendintimation')
                            ->first();


                                 $serious_report_storesliporderCount = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '01')
            ->count();
        $serious_trans_auditslipCount = DB::table('audit.trans_auditslip')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitiescode', '01')
            ->whereIn('processcode', ['X'])
            ->count();
        $seriousSlipFinalizable = $serious_report_storesliporderCount === $serious_trans_auditslipCount;
         $nonserious_report_storesliporderCount = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '02')
	    ->where('statusflag', 'Y')
            ->count();
        $nonserious_trans_auditslipCount = DB::table('audit.trans_auditslip')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitiescode', '02')
            ->whereIn('processcode', ['X'])
            ->count();
// dd( $seriousSlipFinalizable);
        // Compare and store the result as a boolean
        $nonseriousSlipFinalizable = $nonserious_report_storesliporderCount === $nonserious_trans_auditslipCount;


        $statusflag_auditcertificate = $auditCertificate->statusflag ?? '';
        $statusflag_authorityofaudit = $AuthorityofAudit->statusflag ?? '';
        $statusflag_GenesisofAudit = $GenesisofAudit->statusflag ?? '';
        $statusflag_AccountDetails = $AccountDetails->statusflag ?? '';
        $statusflag_pantan = $Report_PanTan->statusflag ?? '';
        $statusflag_storeslip_serious = $serious_report_storesliporder->statusflag ?? '';
        $statusflag_storeslip_nonserious = $Nonserious_report_storesliporder->statusflag ?? '';
        $statusflag_annexture = $annexture_finalize->statusflag ?? '';
        $statusflag_levystatus = $levy_status->statusflag ?? '';
        $statusflag_pendingpara = $pendingpara->statusflag ?? '';
        $statusflag_pdfreport = $reportfinalizeflag->sendintimation ?? '';
        return response()->json([
            'success' => true,
            'statusflags' => [
                'auditcertificate' => $statusflag_auditcertificate,
                'authorityofaudit' => $statusflag_authorityofaudit,
                'genesisofaudit' => $statusflag_GenesisofAudit,
                'accountdetails' => $statusflag_AccountDetails,
                'pantan' => $statusflag_pantan,
                'serious_storeslip' => $statusflag_storeslip_serious,
                'nonserious_storeslip' => $statusflag_storeslip_nonserious,
                'annextures'=>$statusflag_annexture,
                'levystatus'=>$statusflag_levystatus,
                'pendingpara'=>$statusflag_pendingpara,
                'pdfreport'=>$statusflag_pdfreport,
                 'seriouscountdata' => $seriousSlipFinalizable,
                'nonseriouscountdata' => $nonseriousSlipFinalizable
            ],
        ]);
    }


    public function AnnextureUpload(Request $request)
    {
        // Validate if files are uploaded
        if (!$request->hasFile('annexure_files')) {
            return response()->json(['status' => false, 'message' => 'nofiles_choosen'], 400);
        }

        $files = $request->file('annexure_files');
        $destinationPath = 'uploads/report_annexures';

        $storedAnnexures = [];
     	$session = session('charge');

          $designationArray = [
                    $session->deptcode,
                    $session->regioncode,
                    $session->distcode,
                    $request->input('auditscheduleid'),
                    View::shared('annexturepath'),

            ];

        foreach ($files as $annextureType => $file) {
            if ($file && $file->isValid()) {
                //$uploadResult = $this->fileUploadService->uploadFileAnnexture($file, $destinationPath, '');
                $uploadResult = $this->fileUploadService->uploadFile($file, $destinationPath, '', $designationArray);

                $responseData = $uploadResult->getData();

                $fileuploadId = $responseData->fileupload_id ?? null;
                $filetype = $file->getClientOriginalExtension();

                $data = [
                    'annexture_type' => $annextureType,  // from input name key
                    'fileupload_id' => $fileuploadId,
                    'filetype' => $filetype,
                    'auditscheduleid'=>$request->input('auditscheduleid')
                ];

                $storedAnnexure = FormatModel::annexturestore($data);
                $storedAnnexures[] = $storedAnnexure;
            }
        }

        $auditscheduleid =$request->input('auditscheduleid');

        $auditCertificate = DB::table('audit.report_auditcertificate')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();

        $AuthorityofAudit = DB::table('audit.report_authorityofaudit')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();

        $GenesisofAudit = DB::table('audit.report_insitutegenesis')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();
        
        $Report_PanTan = DB::table('audit.report_pantan')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->first();
        $Report_lWF = DB::table('audit.report_labourwelfarefund')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->first();
                            

        $AccountDetails = DB::table('audit.report_accountdetails')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->first();

        $serious_report_storesliporder = DB::table('audit.report_paradetails')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->where('irregularitycode','01')
                            ->first();

        $Nonserious_report_storesliporder = DB::table('audit.report_paradetails')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->where('irregularitycode','02')
                            ->first();

        $annexture_finalize = DB::table('audit.report_annextures')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->where('statusflag', '!=', 'N')
                            ->first();

        $levy_status = DB::table('audit.report_auditlevycertificate')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();

        $pendingpara = DB::table('audit.report_pendingparadetails')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();



                            $serious_report_storesliporderCount = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '01')
            ->count();
        $serious_trans_auditslipCount = DB::table('audit.trans_auditslip')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitiescode', '01')
            ->whereIn('processcode', ['X'])
            ->count();
        $seriousSlipFinalizable = $serious_report_storesliporderCount === $serious_trans_auditslipCount;
            $nonserious_report_storesliporderCount = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '02')
            ->where('statusflag', 'Y')
            ->count();
        $nonserious_trans_auditslipCount = DB::table('audit.trans_auditslip')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitiescode', '02')
            ->whereIn('processcode', ['X'])
            ->count();
        // Compare and store the result as a boolean
        $nonseriousSlipFinalizable = $nonserious_report_storesliporderCount === $nonserious_trans_auditslipCount;


        $statusflag_auditcertificate = $auditCertificate->statusflag ?? '';
        $statusflag_authorityofaudit = $AuthorityofAudit->statusflag ?? '';
        $statusflag_GenesisofAudit = $GenesisofAudit->statusflag ?? '';
        $statusflag_AccountDetails = $AccountDetails->statusflag ?? '';
        $statusflag_pantan = $Report_PanTan->statusflag ?? '';
        $statusflag_storeslip_serious = $serious_report_storesliporder->statusflag ?? '';
        $statusflag_storeslip_nonserious = $Nonserious_report_storesliporder->statusflag ?? '';
        $statusflag_annexture = $annexture_finalize->statusflag ?? '';
        $statusflag_levystatus = $levy_status->statusflag ?? '';
        $statusflag_pendingpara = $pendingpara->statusflag ?? '';

        return response()->json([
            'success' => true,
            'statusflags' => [
                'auditcertificate' => $statusflag_auditcertificate,
                'authorityofaudit' => $statusflag_authorityofaudit,
                'genesisofaudit' => $statusflag_GenesisofAudit,
                'accountdetails' => $statusflag_AccountDetails,
                'pantan' => $statusflag_pantan,
                'serious_storeslip' => $statusflag_storeslip_serious,
                'nonserious_storeslip' => $statusflag_storeslip_nonserious,
                'annextures'=>$statusflag_annexture,
                'levystatus'=>$statusflag_levystatus,
                'pendingpara'=>$statusflag_pendingpara,
                 'seriouscountdata' => $seriousSlipFinalizable,
                'nonseriouscountdata' => $nonseriousSlipFinalizable
            ],
        ]);

    }

    public function saveSlipOrder(Request $request)
    {
        $data['order_auditslip'] = $request->input('slip_order_json');
        $data['auditscheduleid'] =$request->input('auditschid');
        $data['type'] =$request->input('type');
        $result = FormatModel::StoreSlipOrdering($data);
        return response()->json(['status' => 'success','data' => $result,]);
    }


    public function Finalize_PartA(Request $request)
    {
        $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    'partno'
                ]);
        $result = FormatModel::FinalizeReport($data);


        
        $auditscheduleid =$request->input('auditscheduleid');

        $auditCertificate = DB::table('audit.report_auditcertificate')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();

        $AuthorityofAudit = DB::table('audit.report_authorityofaudit')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();

        $GenesisofAudit = DB::table('audit.report_insitutegenesis')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();
        
        $Report_PanTan = DB::table('audit.report_pantan')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->first();

        $AccountDetails = DB::table('audit.report_accountdetails')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->first();

        $serious_report_storesliporder = DB::table('audit.report_paradetails')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->where('irregularitycode','01')
                            ->first();

        $Nonserious_report_storesliporder = DB::table('audit.report_paradetails')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->where('irregularitycode','02')
                            ->first();

        $annexture_finalize = DB::table('audit.report_annextures')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->where('statusflag', '!=', 'N')
                            ->first();


        $levy_status = DB::table('audit.report_auditlevycertificate')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();

        $pendingpara = DB::table('audit.report_pendingparadetails')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();


                             $serious_report_storesliporderCount = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '01')
            ->count();
        $serious_trans_auditslipCount = DB::table('audit.trans_auditslip')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitiescode', '01')
            ->whereIn('processcode', ['X'])
            ->count();
        $seriousSlipFinalizable = $serious_report_storesliporderCount === $serious_trans_auditslipCount;
            $nonserious_report_storesliporderCount = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '02')
	    ->where('statusflag', 'Y')
            ->count();
        $nonserious_trans_auditslipCount = DB::table('audit.trans_auditslip')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitiescode', '02')
            ->whereIn('processcode', ['X'])
            ->count();
        // Compare and store the result as a boolean
        $nonseriousSlipFinalizable = $nonserious_report_storesliporderCount === $nonserious_trans_auditslipCount;



        $statusflag_auditcertificate = $auditCertificate->statusflag ?? '';
        $statusflag_authorityofaudit = $AuthorityofAudit->statusflag ?? '';
        $statusflag_GenesisofAudit = $GenesisofAudit->statusflag ?? '';
        $statusflag_AccountDetails = $AccountDetails->statusflag ?? '';
        $statusflag_pantan = $Report_PanTan->statusflag ?? '';
        $statusflag_storeslip_serious = $serious_report_storesliporder->statusflag ?? '';
        $statusflag_storeslip_nonserious = $Nonserious_report_storesliporder->statusflag ?? '';
        $statusflag_annexture = $annexture_finalize->statusflag ?? '';
        $statusflag_levystatus = $levy_status->statusflag ?? '';
        $statusflag_pendingpara = $pendingpara->statusflag ?? '';

        return response()->json([
            'success' => true,
            'data' => $result,
            'statusflags' => [
                'auditcertificate' => $statusflag_auditcertificate,
                'authorityofaudit' => $statusflag_authorityofaudit,
                'genesisofaudit' => $statusflag_GenesisofAudit,
                'accountdetails' => $statusflag_AccountDetails,
                'pantan' => $statusflag_pantan,
                'serious_storeslip' => $statusflag_storeslip_serious,
                'nonserious_storeslip' => $statusflag_storeslip_nonserious,
                'annextures'=>$statusflag_annexture,
                'levystatus'=>$statusflag_levystatus,
                'pendingpara'=>$statusflag_pendingpara,
                 'seriouscountdata' => $seriousSlipFinalizable,
                'nonseriouscountdata' => $nonseriousSlipFinalizable
            ],
        ]);

    }


public function Report_Prefil(Request $request)
    {

        $formType = $request->input('whichtypeofform');
        $result = null;

        switch ($formType) {
            case 'audit_certificate':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    'cer_typecode',
                    'cer_remarks',
                    'finaliseflag'
                ]);
                $result = FormatModel::StoreAuditCertificate($data);
                break;

            case 'authorityofaudit':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    //'authorityofaudit',
                    'finaliseflag' // replace with actual fields
                ]);
                $result = FormatModel::StoreAuthorityOfAudit($data);
                break;

            case 'institutedetailsentry':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    'genesis_ckeditor',
                    'finaliseflag' // replace with actual fields
                ]);
                $result = FormatModel::StoreInstituteGenesis($data);
                break;

            case 'accountdet':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    //'accountdetails_ckeditor',
                    'finaliseflag' // replace with actual fields
                ]);
               
                // Collect and encode each field separately
                $data['account_details'] = json_encode($request->input('account_details', []));
                $data['bank_account_number'] = json_encode($request->input('bank_account_number', []));
                $data['ob'] = json_encode($request->input('ob', []));
                $data['receipts'] = json_encode($request->input('receipts', []));
                $data['total'] = json_encode($request->input('total', []));
                $data['expenditure'] = json_encode($request->input('expenditure', []));
                $data['cb_cashbook'] = json_encode($request->input('cb_cashbook', []));
                $data['add'] = json_encode($request->input('add', []));
                $data['less'] = json_encode($request->input('less', []));
                $data['cb_passbook'] = json_encode($request->input('cb_passbook', []));
                $data['scheme'] = json_encode($request->input('scheme', []));
                $data['branch'] = json_encode($request->input('branch', []));
                $result = FormatModel::StoreAccountDetails($data);
                break;

            case 'pan_tan':

            
               // print_r($request->all());exit;
                /*$data = $request->only([
                    'auditscheduleid',
                    'instid',
                    'itfiling_issue',
                    'legal_complaince',
                    'financial_review',
                    //lwf
                    'lwf_1',
                    'lwf_2',
                    'lwf_3',
                    'lwf_4',
                ]);*/
                
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                ]);

                $itfilings_issue  = trim($request->input('itfiling_issue'));
                $legal_complaince = trim($request->input('legal_complaince'));
                $financial_review = trim($request->input('financial_review'));


               

                $auditscheduleid = $request->auditscheduleid;
                $instid = $request->instid;
                $formData = $request->input('tdsdata', []);

                $tds_avail = $request->input('tds_avail');

                if($tds_avail == 02)
                {
                    DB::table('audit.report_tds_filed_details')
                    ->where('auditscheduleid', $auditscheduleid)
                    ->where('instid', $instid)
                    ->update([
                                'statusflag' => 'N',
                                'updated_on' => now(),
                            ]);

                }else
                {
                    // Step 1: Get all existing records from DB for this audit/inst
                    $existingRecords = DB::table('audit.report_tds_filed_details')
                        ->where('auditscheduleid', $auditscheduleid)
                        ->where('instid', $instid)
                        ->get();

                    // Step 2: Build a quick lookup of existing entries
                    $existingMap = [];
                    foreach ($existingRecords as $record) {
                        $key = $record->audityear . '|' . $record->filing_status . '|' . $record->auditquarter;
                        $existingMap[$key] = $record;
                    }

                    // Step 3: Track keys weâ€™ve processed
                    $processedKeys = [];

                    foreach ($formData as $row) {
                        $year = $row['year'] ?? '';
                        $status = $row['status'] ?? '';
                        $period = $row['period'] ?? '';
                        $remit = $row['remit'] ?? null;
                        $filed = $row['filed'] ?? null;

                        if (!$year || !$status || !$period) continue; // Skip incomplete rows

                        $key = $year . '|' . $status . '|' . $period;
                        $processedKeys[] = $key;

                        if (isset($existingMap[$key])) {
                            // Update if exists
                            DB::table('audit.report_tds_filed_details')
                                ->where('tds_id', $existingMap[$key]->tds_id)
                                ->update([
                                    'remit_on_time' => $remit,
                                    'returns_filed' => $filed,
                                    'updated_on' => now(),
                                    'statusflag' => 'Y'
                                    
                                ]);
                        } else {
                            // Insert new
                            DB::table('audit.report_tds_filed_details')->insert([
                                'auditscheduleid' => $auditscheduleid,
                                'instid' => $instid,
                                'audityear' => $year,
                                'filing_status' => $status,
                                'auditquarter' => $period,
                                'remit_on_time' => $remit,
                                'returns_filed' => $filed,
                                'created_on' => now(),
                                'updated_on' => now(),
                                'statusflag' => 'Y'

                            ]);
                        }
                    }

                    // Optional Step 4: Delete rows not present in form (if cleanup required)
                    $keysToKeep = collect($processedKeys)->flip();

                    foreach ($existingMap as $key => $record) {
                        if (!$keysToKeep->has($key)) {
                            DB::table('audit.report_tds_filed_details')
                                ->where('tds_id', $record->tds_id)
                                ->update([
                                    'statusflag' => 'N',
                                    'updated_on' => now(),
                                ]);
                        }
                    }



                }

                
                

                // Process GST data
                if ($request->filled('gstdata')) {

                    $gstData = [
                        'auditscheduleid' => $data['auditscheduleid'],
                        'instid' => $data['instid'],
                        'gstdata' => $request->input('gstdata'),
                    ];
                   $gstdata = FormatModel::StorePanTan($gstData, 'gst_data');
                   // print_r($gstdata);
                }

                // Process LWF data
                if ($request->filled('lwfdata')) {
                    $lwfData = [
                        'auditscheduleid' => $data['auditscheduleid'],
                        'instid' => $data['instid'],
                        'lwfdata' => $request->input('lwfdata'),
                    ];
                    FormatModel::StorePanTan($lwfData, 'lwf_data');
                }

                // Process TDS data (only if applicable)
                if ($request->filled('tdsdata')) {
                    $tdsData = [
                        'auditscheduleid' => $data['auditscheduleid'],
                        'instid' => $data['instid'],
                        'tdsdata' => $request->input('tdsdata'),
                    ];
                    FormatModel::StorePanTan($tdsData, 'tds_data');
                }

                // Process CKEditor fields only if not empty
                if (
                    !empty(trim(strip_tags($itfilings_issue))) ||
                    !empty(trim(strip_tags($legal_complaince))) ||
                    !empty(trim(strip_tags($financial_review)))
                ) {
                    $ckeditorData = [
                        'auditscheduleid' => $data['auditscheduleid'],
                        'instid' => $data['instid'],
                        'itfiling_issue' => $itfilings_issue,
                        'legal_complaince' => $legal_complaince,
                        'financial_review' => $financial_review,
                    ];
                    FormatModel::StorePanTan($ckeditorData, 'filabledata');
                }


                break;

            case 'audit_levy_form':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    'levycertificate_ckeditor',
                    'finaliseflag' // replace with actual fields
                ]);
                $result = FormatModel::StoreLevyCertificate($data);
                break;

            case 'pending_para_form':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    'pendingparadet_ckeditor',
                    'finaliseflag' // replace with actual fields
                ]);
                $result = FormatModel::StorePendingPara($data);
                break;

            case 'slipform':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    'parnohidden',
                    'deptcode',
                    'slip_ckeditor',
                    'slipdetails_data',
                    'ordernohidden',
                    'file_upload_ids',
                    'irregularitycode'
                    
                ]);

                $result = FormatModel::StoreParaDetails($data);
                break;

            default:
                return response()->json(['error' => 'Invalid form type'], 400);
        }

        $auditscheduleid =$request->input('auditscheduleid');

        $auditCertificate = DB::table('audit.report_auditcertificate')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();

        $AuthorityofAudit = DB::table('audit.report_authorityofaudit')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();

        $GenesisofAudit = DB::table('audit.report_insitutegenesis')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();
        
        $Report_PanTan = DB::table('audit.report_pantan')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->first();

        $AccountDetails = DB::table('audit.report_accountdetails')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->first();

        $serious_report_storesliporder = DB::table('audit.report_paradetails')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->where('irregularitycode','01')
                            ->first();

        $Nonserious_report_storesliporder = DB::table('audit.report_paradetails')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->where('irregularitycode','02')
                            ->first();

        $annexture_finalize = DB::table('audit.report_annextures')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->where('statusflag', '!=', 'N')
                            ->first();

        $levy_status = DB::table('audit.report_auditlevycertificate')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();

        $pendingpara = DB::table('audit.report_pendingparadetails')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();


                             $serious_report_storesliporderCount = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '01')
            ->count();
        $serious_trans_auditslipCount = DB::table('audit.trans_auditslip')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitiescode', '01')
            ->whereIn('processcode', ['X'])
            ->count();
        $seriousSlipFinalizable = $serious_report_storesliporderCount === $serious_trans_auditslipCount;
            $nonserious_report_storesliporderCount = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '02')
	    ->where('statusflag', 'Y')
            ->count();
        $nonserious_trans_auditslipCount = DB::table('audit.trans_auditslip')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitiescode', '02')
            ->whereIn('processcode', ['X'])
            ->count();
        // Compare and store the result as a boolean
        $nonseriousSlipFinalizable = $nonserious_report_storesliporderCount === $nonserious_trans_auditslipCount;

        $statusflag_auditcertificate = $auditCertificate->statusflag ?? '';
        $statusflag_authorityofaudit = $AuthorityofAudit->statusflag ?? '';
        $statusflag_GenesisofAudit = $GenesisofAudit->statusflag ?? '';
        $statusflag_AccountDetails = $AccountDetails->statusflag ?? '';
        $statusflag_pantan = $Report_PanTan->statusflag ?? '';
        $statusflag_storeslip_serious = $serious_report_storesliporder->statusflag ?? '';
        $statusflag_storeslip_nonserious = $Nonserious_report_storesliporder->statusflag ?? '';
        $statusflag_annexture = $annexture_finalize->statusflag ?? '';
        $statusflag_levystatus = $levy_status->statusflag ?? '';
        $statusflag_pendingpara = $pendingpara->statusflag ?? '';


        return response()->json([
            'success' => true,
            'message' => 'saved_popup',
            'data' => $result,
            'statusflags' => [
                'auditcertificate' => $statusflag_auditcertificate,
                'authorityofaudit' => $statusflag_authorityofaudit,
                'genesisofaudit' => $statusflag_GenesisofAudit,
                'accountdetails' => $statusflag_AccountDetails,
                'pantan' => $statusflag_pantan,
                'serious_storeslip' => $statusflag_storeslip_serious,
                'nonserious_storeslip' => $statusflag_storeslip_nonserious,
                'annextures'=>$statusflag_annexture,
                 'levystatus'=>$statusflag_levystatus,
                'pendingpara'=>$statusflag_pendingpara,
                  'seriouscountdata' => $seriousSlipFinalizable,
                'nonseriouscountdata' => $nonseriousSlipFinalizable
            ],
        ]);

    }



    public function view_fieldaudit()
    {
        $userData = session('user');
        $session_userid = $userData->userid;
 	$results  = FormatModel::fetch_listinstitutes($session_userid,'','','AH');
        $resultsNew=[];
        foreach ($results as $all)
        {
            if ($all->exitmeetdate)
            {
                // Convert exitmeetdate to timestamp and add 6 days
                $exitmeetdate = strtotime($all->exitmeetdate);
                $dateAfter6Days = strtotime('+6 days', $exitmeetdate);

                // Get current date (only date part)
                $currdate = strtotime(date('Y-m-d'));

                // Check if current date is more than 6 days after exitmeetdate
                if ($currdate > $dateAfter6Days)
                {
                    $all->encrypted_auditscheduleid = Crypt::encryptString($all->auditscheduleid);
                    $all->formatted_fromdate = Controller::ChangeDateFormat($all->fromdate);
                    $all->formatted_todate = Controller::ChangeDateFormat($all->todate);
                    $all->formatted_entrydate = Controller::ChangeDateFormat($all->entrymeetdate);
                    $all->formatted_exitdate = Controller::ChangeDateFormat($all->exitmeetdate);
                    $resultsNew[] = $all;
                }
            }
        }



        $results =json_encode($resultsNew);


        return view('audit.listinstitute', compact('results'));
    }

    public function finalizedinstitutesforreport()
    {
        $userData = session('user');
	 $userChargeData = session('charge');

   	if (!empty($userChargeData->instid)) {
            $instid = $userChargeData->instid;
            $whom = 'AI';
     	 } else {
            $instid = $userChargeData->instid ?? null;
           // $whom = 'AU';
$whom = 'AH';
      	}
        $session_userid = $userData->userid;
 	$results  =   FormatModel::fetch_listinstitutes($session_userid,'finalized',$instid,$whom);
        $resultsNew=[];
        foreach ($results as $all)
        {
            if ($all->exitmeetdate)
            {
                // Convert exitmeetdate to timestamp and add 6 days
                $exitmeetdate = strtotime($all->exitmeetdate);
                $dateAfter6Days = strtotime('+6 days', $exitmeetdate);

                // Get current date (only date part)
                $currdate = strtotime(date('Y-m-d'));

                // Check if current date is more than 6 days after exitmeetdate
                if ($currdate > $dateAfter6Days)
                {
                    $all->encrypted_auditscheduleid = Crypt::encryptString($all->auditscheduleid);
                    $all->formatted_fromdate = Controller::ChangeDateFormat($all->fromdate);
                    $all->formatted_todate = Controller::ChangeDateFormat($all->todate);
                    $all->formatted_entrydate = Controller::ChangeDateFormat($all->entrymeetdate);
                    $all->formatted_exitdate = Controller::ChangeDateFormat($all->exitmeetdate);
                    $resultsNew[] = $all;
                }
            }
        }



        $results =json_encode($resultsNew);


        return view('audit.reportdownload', compact('results'));
    }


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
        $inst_details = FormatModel::FetchInstituteDetails($session_userid,$auditscheduleid);



        $FetchAuditslips =FormatModel::AllAuditSlips($auditscheduleid,'01');

        $FetchAuditslips_NonIrrReg =FormatModel::AllAuditSlips($auditscheduleid,'02');

        $auditCertificate = DB::table('audit.report_auditcertificate')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();

        $AuthorityofAudit = DB::table('audit.report_authorityofaudit')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();

        $GenesisofAudit = DB::table('audit.report_insitutegenesis')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();
        
        $Report_PanTan = DB::table('audit.report_pantan')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->first();

        $AccountDetails = DB::table('audit.report_accountdetails')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->first();

        if($inst_details[0]->deptcode == '01')
        {
              $auditEndDate ='30/06/2025';
 
        }else
        {    
                // Split the years
                $years = explode(',', $inst_details[0]->yearname);

                // Get the last year range
                $lastYearRange = trim(end($years)); // e.g. "2023-2024"
               
                // Split the range into start and end years
                $yearParts = explode('-', $lastYearRange);

                // Use the second part as the end year
                $endYear = isset($yearParts[1]) ? trim($yearParts[1]) : null;

                if ($endYear) {
                    $auditEndDate = "31/03/$endYear";
                   // echo "Audit End Date: $auditEndDate";
                } 

        }
        $Master_Auditcertificate = DB::table('audit.mst_auditcertificatetype')
                            ->select('cer_type_code', 'cer_content')
                            ->get();
                    //dd($Master_Auditcertificate);
                        foreach ($Master_Auditcertificate as $cert) {
                            $decodedContent = json_decode($cert->cer_content);
                            // Check if decoding and content are valid
                            if ($decodedContent && isset($decodedContent->content)) {
                                $replacedContent = str_replace('[audityear]', $auditEndDate, $decodedContent->content);

                                // Replace original cer_content with updated JSON
                                $cert->cer_content = json_encode([
                                    'content' => $replacedContent
                                ]);
                            }
                        }




        $serious_report_storesliporder = DB::table('audit.report_paradetails')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->where('irregularitycode','01')
                            ->first();

        $Nonserious_report_storesliporder = DB::table('audit.report_paradetails')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->where('irregularitycode','02')
                            ->first();


       /* $ParaDetails = DB::table('audit.report_paradetails as para')
            ->join('audit.trans_auditslip as slip', 'slip.auditslipid', '=', 'para.slip_id')
            ->where('para.auditscheduleid', $auditscheduleid)
            ->select('para.para_id', 'para.slip_id','para.orderid', 'slip.slipdetails', 'para.slip_attachments')
             ->orderBy('para.orderid', 'asc')
            ->get();

        foreach ($ParaDetails as $para) {
            $fileIds = json_decode($para->slip_attachments ?? '[]', true); // decode attachment IDs

            $files = DB::table('audit.fileuploaddetail')
                ->whereIn('fileuploadid', $fileIds)
                ->pluck('filename') // get just the names
                ->toArray();

            $para->filenames = implode(', ', $files); // attach comma-separated filenames
        }*/

        $ParaDetails = DB::table('audit.slipfileupload as fileup')
            ->join('audit.trans_auditslip as slip', 'slip.auditslipid', '=', 'fileup.auditslipid')
            ->join('audit.fileuploaddetail as filedet', 'filedet.fileuploadid', '=', 'fileup.fileuploadid')
            ->where('slip.auditscheduleid', $auditscheduleid)
            //->where('slip.processcode', 'X')
            ->whereIn('slip.processcode',['X'])
            ->select('slip.slipdetails','fileup.fileuploadid','filedet.filepath','filedet.filename')
            ->get();


        $SerOrderingSlips = DB::table('audit.report_storesliporder')
                            ->where('auditscheduleid', $auditscheduleid)
                             ->select('ser_ordered_slips')
                            ->first();
    
        $NonSerOrderingSlips = DB::table('audit.report_storesliporder')
                            ->where('auditscheduleid', $auditscheduleid)
                             ->select('nonser_ordered_slips')
                            ->first();


        $annexture_finalize = DB::table('audit.report_annextures')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->where('statusflag', '!=', 'N')
                            ->first();
               $instdel = json_decode($inst_details, true);
 
        $deptcode = $instdel[0]['deptcode'];
        $catcode  =  $instdel[0]['catcode'];

        $subcatcode = null;
$ifcategory = $instdel[0]['if_subcategory'];

 if ($ifcategory == 'Y') {
            $Subcatget = DB::table('audit.mst_auditeeins_subcategory')
                ->where('catcode', '=', $instdel[0]['catcode'])
                ->select('auditeeins_subcategoryid', 'subcatename', 'subcattname')
                ->first();

            if ($Subcatget) {
                $subcatcode = $Subcatget->auditeeins_subcategoryid; // assuming subcatcode exists in the table
            }
        }
     $Contentauthorityofaudit = DB::table('audit.map_authorityofaudit as auth')
                                    ->select('auth.auth_content_en')
                                    ->where('auth.deptcode', $deptcode)
                                    ->when(in_array($deptcode, [03, 02]), function ($query) use ($catcode, $subcatcode) {
                                            $query->where('auth.catcode', $catcode);

                                            if (!empty($subcatcode)) {
                                                $query->where('auth.subcatid', $subcatcode);
                                            }

                                            return $query;
                                        })
                                        ->first();
       /* $account_particulars = DB::table('audit.mst_accountparticulars_details as map')
                                ->join('audit.report_accountparticulars as rp', 'map.accpar_id', '=', 'rp.accpar_id')
                                ->select('map.accpar_ename','map.accpar_key')
                                ->where('rp.deptcode', $deptcode)
                                ->where('rp.catcode', $catcode)
                                ->get();*/

        $account_particulars = DB::table('audit.mst_accountparticulars_details as map')
                                ->select('map.accpar_ename','map.accpar_key','map.accpar_tname')
                                ->where('map.statusflag', 'Y')
                                ->orderBy('map.orderid', 'asc')
                                ->get();


      /*  if($deptcode == 03)
        {
            $Contentauthorityofaudit = DB::table('audit.map_authorityofaudit as auth')
                                ->select('auth.auth_content_en')
                                ->where('auth.deptcode', $deptcode)
                                //->where('auth.catcode', $catcode)
                                ->get();

        }else
        {
            $Contentauthorityofaudit = DB::table('audit.map_authorityofaudit as auth')
                                ->select('auth.auth_content_en')
                                ->where('auth.deptcode', $deptcode)
                                ->get();

        }*/
        

        $Report_lWF = DB::table('audit.report_labourwelfarefund')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->first();

        $Report_GST = DB::table('audit.report_gstreturn_details')
                            ->where('auditscheduleid', $auditscheduleid)
                            ->first();

        $Report_Levy = DB::table('audit.report_auditlevycertificate')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();

        $PendingparaDet = DB::table('audit.report_pendingparadetails')
                            ->where('scheduleid', $auditscheduleid)
                            ->first();

        $tdsFiledData = DB::table('audit.report_tds_filed_details')
                        ->where('auditscheduleid', $auditscheduleid)
                        ->where('statusflag','Y')
                        ->get();


        
        return view('audit.transauditslip', compact('Contentauthorityofaudit','account_particulars','annexture_finalize','Nonserious_report_storesliporder','serious_report_storesliporder','NonSerOrderingSlips','SerOrderingSlips','ParaDetails','Master_Auditcertificate','inst_details','FetchAuditslips_NonIrrReg','FetchAuditslips','auditCertificate','AuthorityofAudit','GenesisofAudit','Report_PanTan','AccountDetails','Report_lWF','Report_GST','Report_Levy','tdsFiledData','PendingparaDet'));


        // You can also add logic to handle the ID if needed
    }



    public static function codeofethics()
    {
        $chargeData = session('charge');
        $userData = session('user');
        $session_userName = $userData->username;
        $session_DesigName=$chargeData->desigelname;

        $mpdf = new Mpdf();

        // Path to the HTML file
        $htmlFilePath = resource_path('views/pdf/codeofethics.html'); // Adjust path as needed

        // Set up the page (optional)
        $mpdf->AddPage();

        // Set the border properties (e.g., color, width)
        $mpdf->SetLineWidth(1); // Set the border width
        $mpdf->SetDrawColor(0, 0, 0); // Set the border color (Black)

        // Draw a border around the page (Rect(x, y, width, height))
        // You can adjust the dimensions as needed to control where the border appears.
        $mpdf->Rect(10, 10, 190, 277); // (X, Y, Width, Height)

        $htmlContent = file_get_contents($htmlFilePath);

        $controller = new Controller();
        $currentDate = $controller->ChangeDateFormat(date('d-m-Y'));

        $dynamicData = [
            'name'        =>  $session_userName,
            'designation' =>  $session_DesigName,
            'currentdate' =>  $currentDate
        ];

        // Replace placeholders with dynamic values
        foreach ($dynamicData as $key => $value) {
            // Replace {{key}} with actual values
            $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
        }

        // Write the HTML content to the PDF
        $mpdf->WriteHTML($htmlContent);
       
        $filename = 'codeofethics.pdf'; // Change this to your desired file name

        // Output the PDF to browser with the specified filename for download
        return response($mpdf->Output($filename, 'D'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function entrymeeting(Request $request)
    {
       
        $language = $request->lang === 'ta' ? 'ta' : 'en'; //en or ta



        $auditscheduleid = $request->auditscheduleid; //en or ta
        $auditscheduleid = Crypt::decryptString($auditscheduleid);

       // echo $auditscheduleid;
        //exit;
        $WorkingOfficeGet=FormatModel::GetSchedultedEventDetails($auditscheduleid);
       // print_r($WorkingOfficeGet);exit;

       $ifSubcategory = DB::table('audit.mst_auditeeins_category')
                        ->where('catcode', '=', $WorkingOfficeGet->catcode)
                        ->value('if_subcategory');

       if ($ifSubcategory === 'Y') 
       {
        
            $Subcatget = DB::table('audit.mst_auditeeins_subcategory')
            ->where('catcode', '=', $WorkingOfficeGet->catcode)
            ->value('subcatename','subcattname');

       }else
       {
           $Subcatget ='-';
       }

        if($language == 'en')
        {
            $InstituteName =$WorkingOfficeGet->instename;
            $InstCategory =$WorkingOfficeGet->catename;
            $teamhead   =$WorkingOfficeGet->teamhead_en;
            $teammembers=$WorkingOfficeGet->teammembers_en;
            $teamHead_Label='Team Head';
            $teamMem_Label='Team Members';
            $nodalpersondetails ='<span style="font-family:Times New Roman;">'.$WorkingOfficeGet->nodalname .'<br>'.$WorkingOfficeGet->nodaldesignation.'</span>';


        }else
        {
            $InstituteName =$WorkingOfficeGet->insttname;
            $InstCategory =$WorkingOfficeGet->cattname;
            $teamhead   =$WorkingOfficeGet->teamhead_ta;
            $teammembers=$WorkingOfficeGet->teammembers_ta;
            $teamHead_Label='????  ??????';
            $teamMem_Label='???? ?????????????';
            $nodalpersondetails ='<span style="font-family:arial;">'.$WorkingOfficeGet->nodalname .'<br>'.$WorkingOfficeGet->nodaldesignation.'</span>';

        }
        //print_r($InstituteName);exit;
        $TypeofAudit =$WorkingOfficeGet->typeofauditename;
        $FinancialYear =$WorkingOfficeGet->yearname;


       // Extract Team Head name and designation
        if (preg_match('/^(.*?)\s*\((.*?)\)$/', trim($teamhead), $matches)) {
            $headName = trim($matches[1]);
            $headDesignation = trim($matches[2]);
        } else {
            $headName = trim($WorkingOfficeGet->teamhead_en);
            $headDesignation = '';
        }

       $TeamDetails = '<b>'.$teamHead_Label.'</b><br>' . $headName . '<br>(' . $headDesignation . ')<br>';


        $TeamDetails .= '<br><b>'.$teamMem_Label.'</b><br>';

        $teamMembers = explode(',', $teammembers);
        
        foreach ($teamMembers as $index => $member) {
            $member = trim($member);
        
            // Extract name and designation
            if (preg_match('/^(.*?)\s*\((.*?)\)$/', $member, $matches)) {
                $name = trim($matches[1]);
                $designation = trim($matches[2]);
            } else {
                // Fallback if format doesn't match
                $name = $member;
                $designation = '';
            }
        
            $TeamDetails .= ($index + 1) . '. ' . $name . '<br>(' . $designation . ')<br><br>';
        }

        $entrymeetdate = Controller::ChangeDateFormat(date('d-m-Y',strtotime($WorkingOfficeGet->entrymeetdate)));
        $proposedtodate = Controller::ChangeDateFormat(date('d-m-Y',strtotime($WorkingOfficeGet->todate)));


        $ValuesEcho = array($InstituteName, $FinancialYear,'',$entrymeetdate, $WorkingOfficeGet->teamname, '',$WorkingOfficeGet->mandays, '', '', '', '', '', '', '');

        $entrymeetdate = Controller::ChangeDateFormat(date('d-m-Y',strtotime($entrymeetdate)));
        $proposedtodate = Controller::ChangeDateFormat(date('d-m-Y',strtotime($proposedtodate)));

        $dynamicData = [
                        'Enter Institute Name'=>$InstituteName,
                        'Enter Institute Category'=>$InstCategory,
                        'Enter Institute SubCategory'=>$Subcatget,
                        'Enter Audit Year'=>$FinancialYear,
                        'Enter Entry Meet Date'=>$entrymeetdate,
                        'Enter Audit Team Details'=>$TeamDetails,
                        'Enter Man Days Allocated'=>$WorkingOfficeGet->mandays,
                        'Enter Proposed End Date'=>$proposedtodate,
                        'Enter Nodal officer name and details'=>$nodalpersondetails
                      
                        ];
   
       

			$htmlFilePath = base_path('resources/views/pdf/entryorexitmeeting.html');
			$htmlContent = file_get_contents($htmlFilePath);

            $htmlContent = $this->loadreportcontents(2, $language);


        // Replace placeholders with dynamic values
        foreach ($dynamicData as $key => $value)
        {
            // Replace [key] with actual values
            $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
        }
 
       

        // Create mPDF instance
        if($language == 'ta')
        {
            $mpdf = new Mpdf([
                'mode' => 'utf-8',

                'fontDir' => [public_path('fonts/Tamil')],  // Point mPDF to the directory of custom fonts
                'fontdata' => [
                    $this->tamilfontname => [
                        'R' => $this->tamilfontfile,  // Regular font
                    ],
                    'arial' => [
                        'R' => 'arial.ttf',  // Make sure to add Arial if you plan to use it for English content
                    ]
                ]
            ]);
           
            
            $fontfamily = $this->tamilfontname;

        }else
        {
            $mpdf = new Mpdf();
            $fontfamily = 'arial';
        }

        // Add a page
        $mpdf->AddPage();

        // Set the border properties (e.g., color, width)
       // $mpdf->SetLineWidth(1); // Set the border width
        $mpdf->SetDrawColor(0, 0, 0); // Set the border color (Black)

        // Draw a border around the page (Rect(x, y, width, height))
       // $mpdf->Rect(10, 10, 190, 277); // (X, Y, Width, Height)

     
        // Write HTML content to the PDF
        $mpdf->WriteHTML($htmlContent);

        // Output the PDF to the browser
        return $mpdf->Output('entrymeeting.pdf', 'D');
    }

    public function exitmeeting(Request $request)
    {
        
        // $language = $request->lang;

            $language = $request->lang === 'ta' ? 'ta' : 'en'; //en or ta


            $auditscheduleid = $request->auditscheduleid; //en or ta
            $auditscheduleid = Crypt::decryptString($auditscheduleid);
            
            $WorkingOfficeGet=FormatModel::GetSchedultedEventDetails($auditscheduleid);
            if($language == 'en')
            {
                $InstituteName =$WorkingOfficeGet->instename;

            }else
            {
                $InstituteName =$WorkingOfficeGet->insttname;

            }


            $TypeofAudit   = $WorkingOfficeGet->typeofauditename;
            $FinancialYear = $WorkingOfficeGet->yearname;

        
            $entrymeetdate = Controller::ChangeDateFormat(date('d-m-Y',strtotime($WorkingOfficeGet->entrymeetdate)));

            $ToDate = Controller::ChangeDateFormat(date('d-m-Y',strtotime($WorkingOfficeGet->todate)));

            $exitmeetdate = Controller::ChangeDateFormat(date('d-m-Y',strtotime($WorkingOfficeGet->exitmeetdate)));

            //$ValuesEcho = array($InstituteName, $FinancialYear,$fromDate,$ToDate, $WorkingOfficeGet->mandays, '',$WorkingOfficeGet->teamname, '', '', '', '', '', '', '');

            
            $entrymeetdate = Controller::ChangeDateFormat(date('d-m-Y',strtotime($entrymeetdate)));

            $currdate =date('d-m-Y');

            if($language == 'en')
            {
                $InstituteName =$WorkingOfficeGet->instename;
                $InstCategory =$WorkingOfficeGet->catename;
                $teamhead   =$WorkingOfficeGet->teamhead_en;
                $teammembers=$WorkingOfficeGet->teammembers_en;
                $teamHead_Label='Team Head';
                $teamMem_Label='Team Members';
                $nodalpersondetails ='<span style="font-family:Times New Roman;">'.$WorkingOfficeGet->nodalname .'<br>'.$WorkingOfficeGet->nodaldesignation.'</span>';


            }else
            {
                $InstituteName =$WorkingOfficeGet->insttname;
                $InstCategory =$WorkingOfficeGet->cattname;
                $teamhead   =$WorkingOfficeGet->teamhead_ta;
                $teammembers=$WorkingOfficeGet->teammembers_ta;
                $teamHead_Label='????  ??????';
                $teamMem_Label='???? ?????????????';
                $nodalpersondetails ='<span style="font-family:arial;">'.$WorkingOfficeGet->nodalname .'<br>'.$WorkingOfficeGet->nodaldesignation.'</span>';

            }

            // Extract Team Head name and designation
        if (preg_match('/^(.*?)\s*\((.*?)\)$/', trim($teamhead), $matches)) {
            $headName = trim($matches[1]);
            $headDesignation = trim($matches[2]);
        } else {
            $headName = trim($WorkingOfficeGet->teamhead_en);
            $headDesignation = '';
        }

        $TeamDetails = '<b>'.$teamHead_Label.'</b><br>' . $headName . '<br>(' . $headDesignation . ')<br>';


        $TeamDetails .= '<br><b>'.$teamMem_Label.'</b><br>';

        $teamMembers = explode(',', $teammembers);
        
        foreach ($teamMembers as $index => $member) {
            $member = trim($member);
        
            // Extract name and designation
            if (preg_match('/^(.*?)\s*\((.*?)\)$/', $member, $matches)) {
                $name = trim($matches[1]);
                $designation = trim($matches[2]);
            } else {
                // Fallback if format doesn't match
                $name = $member;
                $designation = '';
            }
        
            $TeamDetails .= ($index + 1) . '. ' . $name . '<br>(' . $designation . ')<br><br>';
        }

            $dynamicData = [
                'Enter Institute Name'=>$InstituteName,
                'Enter Audit Year'=>$FinancialYear,
                'Enter Audit Start Date'=>$entrymeetdate,
                'Enter Proposed Exit Meeting Date'=>$ToDate,
                'Enter Audit Team Details'=>$TeamDetails,
                'Enter Allocated Man Days'=>$WorkingOfficeGet->mandays,
                'Enter Exit Meeting Date'=>$exitmeetdate,
                'Enter Exact Man Days'=>$WorkingOfficeGet->mandays,
                'Enter Conference Date'=>'',
                'Enter officer Details'=>$nodalpersondetails,
                'Enter Data'=>''
                ];


            
        $htmlFilePath = base_path('resources/views/pdf/exitmeeting.html');
        $htmlContent = file_get_contents($htmlFilePath);
            // Replace placeholders with dynamic values
            foreach ($dynamicData as $key => $value)
            {
                // Replace [key] with actual values
                $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
            }


            

            // Create mPDF instance
            if($language == 'ta')
            {
                $mpdf = new Mpdf([
                    'fontDir' => [public_path('fonts/Tamil')],  // Point mPDF to the directory of custom fonts
                    'fontdata' => [
                        $this->tamilfontname => [
                            'R' => $this->tamilfontfile,  // Regular font
                        ],
                        'arial' => [
                            'R' => 'arial.ttf',  // Make sure to add Arial if you plan to use it for English content
                        ]
                    ]
                ]);
                $fontfamily = $this->tamilfontname;

            }else
            {
                $mpdf = new Mpdf();
                $fontfamily = 'arial';
            }

            // Add a page
            $mpdf->AddPage();

            // Set the border properties (e.g., color, width)
            //$mpdf->SetLineWidth(1); // Set the border width
            $mpdf->SetDrawColor(0, 0, 0); // Set the border color (Black)

            // Draw a border around the page (Rect(x, y, width, height))
            //$mpdf->Rect(10, 10, 190, 277); // (X, Y, Width, Height)
            
            // Write HTML content to the PDF
            $mpdf->WriteHTML($htmlContent);

            // Output the PDF to the browser
            return $mpdf->Output('exitmeeting.pdf', 'D');
    }

    // Function to detect if a string contains any English characters
    public function containsEnglish($string)
    {
        return preg_match('/[a-zA-Z]/', $string);  // Check for English letters
    }

    public function previewgeneratepdf(Request $request)
    {
          // Initialize mPDF
          $mpdf = new \Mpdf\Mpdf();

           // Add a page
        $mpdf->AddPage();

        // Set the border properties (e.g., color, width)
        $mpdf->SetLineWidth(1); // Set the border width
        $mpdf->SetDrawColor(0, 0, 0); // Set the border color (Black)

        // Draw a border around the page (Rect(x, y, width, height))
        $mpdf->Rect(10, 10, 190, 277); // (X, Y, Width, Height)

          // HTML content for the PDF
          $html = '
              <html>
              <head>
                  <style>

                  </style>
              </head>
              <body>
                  <div class="container">
                      <div class="header">
                         ArulMigu Kapaleeshwarar temple, Mylapore, Chennai
                      </div>
                      <div class="content">
                          <p>Financial Year of 2024 - 2025</p>
                      </div>
                  </div>
                  <div class="part-a">
                    <h3>PART A</h3>
                    <ol>
                        <li><i class="fa fa-calendar text-warning me-2"></i>Intimation Letter</li>
                        <li><i class="fa fa-calendar text-warning me-2"></i>Entry Meeting</li>
                        <li><i class="fa fa-file text-info me-2"></i>Code Of Ethics</li>
                        <li><i class="fa fa-file text-info me-2"></i>Minute of Meeting</li>
                        <li><i class="fa fa-file text-info me-2"></i>Work Allocation</li>
                        <li><i class="fa fa-file text-info me-2"></i>Exit Meeting</li>
                    </ol>
                </div>

              </body>
              </html>
          ';

          // Write the HTML content to mPDF
          $mpdf->WriteHTML($html);

        $pdfContent = $mpdf->Output('', 'S');


        // Output the PDF to the browser
        //return $mpdf->Output('tamil_pdf_example.pdf', 'I');

         // Return Base64 encoded PDF
         return response()->json([
            'status' => 'success',
            'pdf' => base64_encode($pdfContent),
        ]);

    }


    public function auditcertificate(Request $request)
    {

        $mpdf = new \Mpdf\Mpdf();

        // Path to the HTML file
        $htmlFilePath = resource_path('views/pdf/auditcertificate.html'); // Adjust path as needed

        // Set up the page (optional)
        $mpdf->AddPage();

        // Set the border properties (e.g., color, width)
        $mpdf->SetLineWidth(0.5); // Set the border width
        $mpdf->SetDrawColor(0, 0, 0); // Set the border color (Black)

        // Draw a border around the page (Rect(x, y, width, height))
        // You can adjust the dimensions as needed to control where the border appears.
        $mpdf->Rect(10, 10, 190, 277); // (X, Y, Width, Height)

        $htmlContent = file_get_contents($htmlFilePath);

        // Write the HTML content to the PDF
        $mpdf->WriteHTML($htmlContent);

        $filename = 'auditcertificate.pdf'; // Change this to your desired file name

        // Output the PDF to browser with the specified filename for download
        return response($mpdf->Output($filename, 'I'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

    }

    public function finalize_auditreport(Request $request)
    {
        try {
            $data['iframeContent'] = trim($request->input('iframeContent'));
            $data['activeStep'] = $request->input('activeStep');
            $data['activeStepNo'] = $request->input('activeStepNo');


            if (empty($data)) {
                return response()->json(['error' => 'No content provided'], 400);
            }

            // Call the model function to store content
            FormatModel::storeReport($data);

           // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'Content saved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }


    }

    public function exitmeeting_editreport()
    {

        try {
            $fontName = 'Times New Roman';
            $defaultsize = 13;

            // Step 1: Load the HTML content from an HTML file
            $htmlFilePath2 = base_path('resources/views/pdf/exitmeeting.html');
            $fileFromTemplate = true; // Flag to track if content is from file

            if (!File::exists($htmlFilePath2)) {
                return response()->json(['error' => 'HTML file not found at ' . $htmlFilePath2], 404);
            }

            // Step 2: Check if report exists
            $report = FormatModel::where('report_type', '5')->latest()->first();
            $htmlContent ='';


            if ($report && !empty($report->report_contents)) {
                $steptype=$_GET['step'];
                $explode=explode('exitmeeting_',$steptype);
                if($explode[1] == 'en')
                {
                    $reportContent = json_decode($report->report_contents, true);

                }else
                {
                    $reportContent = json_decode($report->report_contents_ta, true);

                }                
                if (isset($reportContent['content']))
                {
                     $htmlContent .= ($reportContent['content']);

                    $fileFromTemplate = false;
                }
            }else
            {
                //$htmlContent .= '<h4>5. EXIT MEETING</h4>';

            }

             // If no report found, use template file content
             if ($fileFromTemplate) {
                $htmlContent .= File::get($htmlFilePath2);
            }


              // Load content from the JSON file
              $jsonFilePath = public_path('json/pdfcontent.json');
              $jsonContent = file_get_contents($jsonFilePath);
              $data = json_decode($jsonContent, true);
              $data = mb_convert_encoding($data, 'UTF-8', 'auto');


              $language = 'en'; // en or ta
              $title = $data['exitpdfword_' . $language]['title'];
              $tablecontents = $data['exitpdfword_' . $language];

              unset($tablecontents['title']);

              $tabledata = '';
              $sno = 1;
              $x = 0;

              $ValuesEcho = array('', '','','', '', '','', '', '', '', '', '', '', '');
              foreach ($tablecontents as $tablekey => $tableval)
              {
                  $tabledata .= '<tr><td class="lang">' . $sno . '</td><td class="lang">' . $tableval . '</td><td class="lang">:</td><td class="fillupfield englishcontent">' . (isset($ValuesEcho[$x]) ? $ValuesEcho[$x] : '') . '</td></tr>';
                  $sno++;
                  $x++;

              }

              $dynamicData = [
                                'heading_title' => $title,
                                'fontFamily' => $fontName,
                                'tabledata' => $tabledata
                                ];

              foreach ($dynamicData as $key => $value) {
              $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
              }



             // Step 4: Make content editable & apply necessary formatting
             //$htmlContent = $this->makeEditable($htmlContent);

             if ($fileFromTemplate)
             {

                 $htmlContent = $this->addBordersToHtml($htmlContent);

             }



            return response()->json([
                'res' => 'success',
                'html' => $htmlContent
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in intimationletter: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }

    }



    public function codeofethics_editreport()
    {
        try {
            $fontName = 'Times New Roman';
            $defaultsize = 13;

            // Step 1: Load the HTML content from an HTML file
            $htmlFilePath2 = base_path('resources/views/pdf/codeofethics.html');

            if (!File::exists($htmlFilePath2)) {
                return response()->json(['error' => 'HTML file not found at ' . $htmlFilePath2], 404);
            }

            // Step 2: Check if report exists
            $report = FormatModel::where('report_type', '3')->latest()->first();
            $htmlContent = '';
            $fileFromTemplate = true; // Flag to track if content is from file

            if ($report && !empty($report->report_contents))
            {
                $steptype=$_GET['step'];
                $explode=explode('codeofethics_',$steptype);
                if($explode[1] == 'en')
                {
                    $reportContent = json_decode($report->report_contents, true);

                }else
                {
                    $reportContent = json_decode($report->report_contents_ta, true);

                }
                if (isset($reportContent['content']))
                {
                     $htmlContent = ($reportContent['content']);

                    $fileFromTemplate = false;
                }
            }

            // If no report found, use template file content
            if ($fileFromTemplate) {
                $htmlContent = File::get($htmlFilePath2);
            }


            $dynamicData = [
                'name'=>'[Name]',
                'designation'=>'[Designation]',
                'currentdate' =>'[Current Date]'
            ];

            // Replace placeholders with dynamic values
            foreach ($dynamicData as $key => $value)
            {
                // Replace {{key}} with actual values
                $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);

            }

             // Step 4: Make content editable & apply necessary formatting
             $htmlContent = $this->makeEditable($htmlContent);

            if ($fileFromTemplate)
            {

                $htmlContent = $this->addBordersToHtml($htmlContent);

            }
            header('Content-Type: text/html; charset=UTF-8');

            return response()->json([
                'res' => 'success',
                'html' => $htmlContent
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in intimationletter: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }

    }

    public function entrymeeting_editreport()
    {

        try {
            $fontName = 'Times New Roman';
            $defaultsize = 13;
        
            // Step 1: Load the HTML content from an HTML file
            $htmlFilePath2 = base_path('resources/views/pdf/entryorexitmeeting.html');
            $fileFromTemplate = true; // Flag to track if content is from file
        
            if (!File::exists($htmlFilePath2)) {
                return response()->json(['error' => 'HTML file not found at ' . $htmlFilePath2], 404);
            }
        
            // Step 2: Check if report exists
            $report = FormatModel::where('report_type', '2')->latest()->first();
            $htmlContent = '';
        
            if ($report && !empty($report->report_contents)) {
                $steptype = $_GET['step'] ?? '';
                $explode = explode('entrymeeting_', $steptype);
        
                // Ensure index exists before accessing it
                $languageKey = isset($explode[1]) && $explode[1] === 'ta' ? 'report_contents_ta' : 'report_contents';
        
                $reportContent = json_decode($report->$languageKey, true, 512, JSON_UNESCAPED_UNICODE);
        
                if (!empty($reportContent['content'])) {
                    $htmlContent .= $reportContent['content'];
                    $fileFromTemplate = false;
                }
            } else {
                $htmlContent .= '<h4>2. ENTRY MEETING</h4>';
            }
        
            // If no report found, use template file content
            if ($fileFromTemplate) {
                $htmlContent .= File::get($htmlFilePath2);
            }
        
            // Step 3: Load content from the JSON file
            $jsonFilePath = public_path('json/pdfcontent.json');
        
            if (File::exists($jsonFilePath)) {
                $jsonContent = file_get_contents($jsonFilePath);
                $data = json_decode($jsonContent, true, 512, JSON_UNESCAPED_UNICODE);
            } else {
                return response()->json(['error' => 'JSON file not found'], 404);
            }
        
            $language = 'en'; // Change to 'ta' dynamically if needed
            $title = $data['entrypdfword_' . $language]['title'] ?? 'Entry Meeting';
        
            $tablecontents = $data['entrypdfword_' . $language] ?? [];
            unset($tablecontents['title']); // Remove title from contents
        
            $tabledata = '';
            $sno = 1;
            $ValuesEcho = ['', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        
            foreach ($tablecontents as $tablekey => $tableval) {
                $tabledata .= '<tr><td class="lang">' . $sno . '</td><td class="lang">' . $tableval . '</td><td class="lang">:</td><td class="fillupfield englishcontent">' . ($ValuesEcho[$sno - 1] ?? '') . '</td></tr>';
                $sno++;
            }
        
            // Step 4: Replace placeholders with dynamic data
            $dynamicData = [
                'heading_title' => $title,
                'fontFamily' => $fontName,
                'tabledata' => $tabledata
            ];
        
            foreach ($dynamicData as $key => $value) {
                $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
            }
        
            // Make content editable
           $htmlContent = $this->makeEditable($htmlContent);
        
            // Add borders if content is from the template file
            if ($fileFromTemplate) {
                $htmlContent = $this->addBordersToHtml($htmlContent);
            }
        
            return response()->json([
                'res' => 'success',
                'html' => $htmlContent
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in entry/exit meeting: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
        

    }

    public function intimationletter()
    {
        try {
            // Step 1: Load the HTML content from the template file
            $htmlFilePath = base_path('resources/views/pdf/intimationletter.html');

            if (!File::exists($htmlFilePath)) {
                return response()->json(['error' => 'HTML file not found at ' . $htmlFilePath], 404);
            }

            // Step 2: Check if report exists
            $report = FormatModel::where('report_type', '1')->latest()->first();
            $htmlContent = '';
            $fileFromTemplate = true; // Flag to track if content is from file

            if ($report && !empty($report->report_contents)) {
                $steptype=$_GET['step'];
                $explode=explode('intimationletter_',$steptype);
                if($explode[1] == 'en')
                {
                    $reportContent = json_decode($report->report_contents, true);

                }else
                {
                    $reportContent = json_decode($report->report_contents_ta, true);

                }
                if (isset($reportContent['content'])) {
                $htmlContent = ($reportContent['content']);
                    //$htmlContent = html_entity_decode($reportContent['content'], ENT_QUOTES, 'UTF-8');

                // $htmlContent = mb_convert_encoding($htmlContent, 'UTF-8', 'auto');

                    $fileFromTemplate = false;
                }
            }

            // If no report found, use template file content
            if ($fileFromTemplate) {
                $htmlContent = File::get($htmlFilePath);
            }

            // Step 3: Define placeholders & replace them with default values
            $placeholders = [
                'from_name' => '[From Name]',
                'from_desig' => '[From Designation]',
                'from_location' => '[From Location]',
                'audit_fromdate' => '[Audit Start Date]',
                'currentdate' => '[Current Date]',
                'to_name'=>'[To Name]',
                'to_desig'=>'[To Designation]',
                'to_location'=>'[To Location]'

            ];

            foreach ($placeholders as $key => $value) {
                $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
            }

            // Step 4: Make content editable & apply necessary formatting
          //  $htmlContent = $this->makeEditable($htmlContent);

            if ($fileFromTemplate)
            {
               $htmlContent = $this->makeEditable($htmlContent);

                $htmlContent = $this->addBordersToHtml($htmlContent);

            }
         
            // Set the default internal encoding to UTF-8
            
            // Ensure PHP outputs UTF-8 encoded content
            header('Content-Type: text/html; charset=UTF-8');
         
            

            // Step 5: Return formatted HTML response
            return response()->json([
                'res' => 'success',
                'html' => $htmlContent
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in intimationletter: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function PartC_Contents()
    {
        $StepForm =$_REQUEST['stepform'];
        $partc_other =$_REQUEST['partc_other'];
        $scheduleId = $_GET['scheduleid'];
        $auditscheduleid = $scheduleId;
        $flagset=false;

        if($StepForm == 'receipts_charges')
        {
                $accountparticulars =FormatModel::accountparticulars($auditscheduleid,1);
                $formHeading ='Receipts & Charges';
                $flagset =true;

        }

        if($StepForm == 'income_expendiature')
        {
                $accountparticulars =FormatModel::accountparticulars($auditscheduleid,2);
                $formHeading ='Income & Expendiature';
                $flagset =true;

        }

        if($StepForm == 'account_investments')
        {
                $accountparticulars =FormatModel::accountparticulars($auditscheduleid,4);            
                $formHeading ='Account Investments';
                $flagset =true;

        }

        if($flagset == true)
        {
            if($accountparticulars == 'nodata')
            {
                return response()->json(['res'=>'success',
                                         'formheading' =>$formHeading ]);
            }else
            {
                $filepath=$accountparticulars->filepath;
            
                $rootpath = url('/'); // This will generate the correct URL based on your current domain
                $AccountParticularsFilepath = $rootpath.'/'.$filepath;
                $extension = pathinfo($filepath, PATHINFO_EXTENSION);
                $url = Storage::url($filepath);

                if ($url) 
                {
                    if($extension == 'pdf')
                    {
                        return response()->json(['res'=>'success',
                                                 'pdf_url' => $AccountParticularsFilepath,   // Send the HTML content
                                                 'formheading' =>$formHeading,
                                                 'fileurl'=>$url,
                                                 'extension'=>'pdf' ]);
        
                    }else if($extension == 'xlsx')
                    {
                        // Load the Excel file
                        $AccountParticularsFilepath = public_path($filepath);
        
        
        
                        $spreadsheet = SpreadsheetIOFactory::load($AccountParticularsFilepath);
                        $sheetNames = $spreadsheet->getSheetNames();
                        $htmlOutput = "";
        
                        foreach ($spreadsheet->getAllSheets() as $index => $sheet) {
                            $sheetTitle = $sheetNames[$index];
                            $htmlOutput .= "<h3>Sheet: $sheetTitle</h3>";
                            $htmlOutput .= "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
        
                            $rows = $sheet->toArray(); // Convert sheet to array
        
                            foreach ($rows as $rowIndex => $row) {
                                $htmlOutput .= "<tr>";
                                foreach ($row as $cell) {
                                    $tag = $rowIndex == 0 ? "th" : "td"; // First row as header
                                    $htmlOutput .= "<$tag style='padding: 8px; border: 1px solid #ddd;'>".htmlspecialchars($cell)."</$tag>";
                                }
                                $htmlOutput .= "</tr>";
                            }
        
                            $htmlOutput .= "</table><br>";
                        }
        
                        return response()->json(['res'=>'success',
                                                'filedata' => $htmlOutput,   // Send the HTML contentphp artisan storage:link
        
                                                'formheading' =>$formHeading,
                                                'fileurl'=>$url,
                                                'extension'=>$extension ]);
        
                    }else if($extension == 'png' || $extension == 'jpeg')
                    {
                        return response()->json(['res'=>'success',
                                                 'filepath' => $AccountParticularsFilepath,   // Send the HTML content
                                                 'formheading' =>$formHeading,
                                                 'fileurl'=>$url,
                                                 'extension'=>'img']);
        
                    }
                }else
                {
                    echo 'elsee';
                    return response()->json(['res'=>'success',
                                             'formheading' =>$formHeading ]);
                }

            }
        }
       

        if($partc_other == 'partc_other')
        {
            $AccountParticularsFilepath = '';
            $formHeading =$StepForm;
        }

        return response()->json(['res'=>'success',
                                'formheading' =>$formHeading ]);


    }

    public function singleSlipDetails()
    {
         $scheduleId = $_GET['scheduleid'];
        $slipno = $_REQUEST['slipno'] ?? null;

         $GetauditSlips = FormatModel::FetchAuditSlipsbyID($scheduleId, $slipno);


         return $GetauditSlips;

    }

    public function previewWordforSingleFile()
    {
        try {
            $scheduleId = $_GET['scheduleid'];
            $lang = $_GET['lang'];
            $stepForm = $_REQUEST['stepform'];
            $slipno = $_REQUEST['slipno'] ?? null;

            $auditscheduleid = $scheduleId;
            $loaddata = $this->loadAllValues($auditscheduleid, $lang);
            $labels = $this->loadlabels();
            $nodata_avail = $labels[$lang]['nodata_avail'];

            $fontName = $loaddata['fontName'];
            $defaultSize = $loaddata['defaultSize'];

            $commonStyle = '
            <style>
                body {
                    font-family: Times New Roman;
                    font-size: 18pt !important;
                    line-height: 1.6;
                    margin: 0;
                    padding: 0;
                    color: #000;
                }
        
                .pdf-wrapper {
                    border: 2px solid black;
                    padding: 40px;
                    margin: 10mm;
                    height: calc(100% - 30mm); /* Adjust height for margins */
                    box-sizing: border-box;
                }
        
                h1, h2, h3 {
                    text-align: center;
                    color: #333;
                }
        
                .section-header {
                    text-align: center;
                    margin-bottom: 15px;
                    border-bottom: 1px solid #ccc;
                    padding-bottom: 5px;
                    font-size: 16pt;
                    font-weight: bold;
                }
        
                .section-content {
                    margin-top: 15px;
                }
        
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                }
        
                table, th, td {
                    border: 1px solid #999;
                }
        
                th, td {
                    padding: 13px;
                    text-align: left;
                    vertical-align: top;
                }
        
                .letter-footer {
                    margin-top: 40px;
                    font-size: 10pt;
                    text-align: right;
                }
        
                .signature {
                    margin-top: 60px;
                }
        
                .page-break {
                    page-break-before: always;
                }
        
                .center {
                    text-align: center;
                    margin-top: 20px;
                    font-weight: bold;
                }
            </style>
        ';
        

                        // You can add multiple sections like this dynamically

            $formHeading = '';
            $htmlContent = '<html><head><style>' . $commonStyle . '</style></head><body><div class="pdf-wrapper">';

            // HEADINGS & CONTENTS
            if ($stepForm == 'intimation') {
                $formHeading = '1. ????????? ??????';
                $htmlContent .= $this->loadintimationletter($scheduleId, $lang);

            } elseif ($stepForm == 'entrymeeting') {
                $formHeading = '2. Entry Meeting';
                $htmlContent .= $this->loadentrymeeting($scheduleId, $lang, $fontName);

            } elseif ($stepForm == 'codeofethics') {
                $formHeading = '3. Code of Ethics';
                $htmlContent .= $this->load_codeofethicscontents($scheduleId, $lang);

            } elseif ($stepForm == 'minutesofmeeting') {
                $formHeading = '4. Minutes of Meeting';
                $htmlContent .= "<div class='center'><strong>{$nodata_avail}</strong></div>";

            } elseif ($stepForm == 'workallocation') {
                $formHeading = '5. Work Allocation';
                $htmlContent .= $this->workallocationpdf($scheduleId, $lang);

            // $htmlContent .= $this->generateWorkAllocationHTML($auditscheduleid, $fontName, $defaultSize, 'singlereport', $lang);

            } elseif ($stepForm == 'exitmeeting') {
                $formHeading = '6. Exit Meeting';
                $htmlContent .= $this->loadexitmeeting($scheduleId, $lang, $fontName);

            } elseif ($stepForm == 'auditslips') {
                $formHeading = 'Audit Slip #' . $slipno;
                if ($slipno) {
                    $GetauditSlips = FormatModel::FetchAuditSlipsbyID($auditscheduleid, $slipno);
                    //$htmlContent .= $this->generateAuditSlipsHtml($GetauditSlips, $lang, $fontName, $defaultSize);
                    $htmlContent .= $this->AuditSlipLoadPDF($auditscheduleid, $GetauditSlips, $lang,'','','');

                } else {
                    $htmlContent .= "<div class='center'><strong>{$nodata_avail}</strong></div>";
                }

            } elseif ($stepForm == 'pendingpara') {
                $formHeading = 'Pending Para';
                $pendingparacount = FormatModel::Paracount($auditscheduleid, 'pendingpara')->pendingslips;
                $label = $labels[$lang]['pendingparacount'];
                $htmlContent .= "<div class='center'><strong>{$label} - {$pendingparacount}</strong></div>";

            } elseif ($stepForm == 'currentpara') {
                $formHeading = 'Current Para';
                $currentparacount = FormatModel::Paracount($auditscheduleid, 'currentpara')->totalslips;
                $label = $labels[$lang]['currentparacount'];
                $htmlContent .= "<div class='center'><strong>{$label} - {$currentparacount}</strong></div>";
            }

            $htmlContent .= '</div></body></html>';

            $mpdfContentFinal = self::applyFontByLanguage($htmlContent);  // Adds font and auto-break


            // Create and render the PDF with mPDF
            $mpdf = new Mpdf([
                'default_font' => $fontName,
                'format' => 'A4',
                'margin_top' => 15,
                'margin_bottom' => 15,
                'margin_left' => 15,
                'margin_right' => 15,
            ]);

            $mpdf->WriteHTML($htmlContent);

            // Save the PDF file
            $fileName = 'AuditReport_' . now()->format('Y_m_d_H_i_s') . '.pdf';
            $filePath = public_path('files/' . $fileName);
            $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

            return response()->json([
                'res' => 'success',
                'filename' => $fileName,
                'formheading' => $formHeading,
                'html' => $htmlContent, // optional preview HTML
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in previewPDFforSingleFile: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }


    private function getPdfStyles($fontName, $defaultSize)
    {
        return "
            body { font-family: '{$fontName}'; font-size: {$defaultSize}px; }
            .center { text-align: center; margin: 10px 0; }
            table { width: 100%; border-collapse: collapse; margin: 10px 0; }
            th, td { border: 1px solid #000; padding: 6px; text-align: left; }
            h4 { font-size: 16px; margin-top: 20px; }
        ";
    }


    private function loadAllValues($scheduleId, $lang)
    {
        /** First Page Content **/
        $chargeData = session('charge');
        $userData = session('user');
    
        $auditscheduleid = $scheduleId;
        $workAllocation = FormatModel::fetch_allocatedwork($auditscheduleid);
        $data = (array) FormatModel::GetSchedultedEventDetails($auditscheduleid); // Convert to array for merging


        $ifSubcategory = DB::table('audit.mst_auditeeins_category')
                        ->where('catcode', '=', $data['catcode'])
                        ->value('if_subcategory');

       if ($ifSubcategory === 'Y') 
       {
        
            $Subcatget = DB::table('audit.mst_auditeeins_subcategory')
            ->where('catcode', '=', $data['catcode'])
            ->value('subcatename','subcattname');

       }else
       {
           $Subcatget ='-';
       }
       if($lang === 'en'){
       $teamHead_Label='Team Head';
       $teamMem_Label='Team Members';

         $teamhead   =$data['teamhead_en'];
       $teammembers=$data['teammembers_en'];

       }else{
         $teamHead_Label='குழு தலைவர்';
       $teamMem_Label='குழு உறுப்பினர்கள்';

         $teamhead   =$data['teamhead_ta'];
       $teammembers=$data['teammembers_ta'];

       }

     

       if (preg_match('/^(.*?)\s*\((.*?)\)$/', trim($teamhead), $matches)) {
            $headName = trim($matches[1]);
            $headDesignation = trim($matches[2]);
        } else {
            $headName = trim($data['teamhead_en']);
            $headDesignation = '';
        }

        $TeamDetails = '<b>'.$teamHead_Label.'</b><br>' . $headName . '<br>(' . $headDesignation . ')<br>';


        $TeamDetails .= '<br><b>'.$teamMem_Label.'</b><br>';

        $teamMembers = explode(',', $teammembers);
        
        foreach ($teamMembers as $index => $member) {
            $member = trim($member);
        
            // Extract name and designation
            if (preg_match('/^(.*?)\s*\((.*?)\)$/', $member, $matches)) {
                $name = trim($matches[1]);
                $designation = trim($matches[2]);
            } else {
                // Fallback if format doesn't match
                $name = $member;
                $designation = '';
            }
        
            $TeamDetails .= ($index + 1) . '. ' . $name . '<br>(' . $designation . ')<br><br>';
        }
        
        // Assign language-specific values
        $langData = ($lang == 'ta') ? [
            'DeptName'      => $data['depttlname'] ?? '',
            'InstituteName' => $data['insttname'] ?? '',
            'InstCategory'  => $data['cattname'] ?? '',
            'InstSubcat'    => $Subcatget   ,   
            'TypeofAudit'   => $data['typeofaudittname'] ?? '',
            'DistName'      => ($data['disttname'] ?? '') . ' ????????',
            'designName'    => $userData->desigtsname ?? '',
            'UserName'      => $userData->usertname ?? '',
            'TeamHead'      => $data['teamhead_ta'] ?? '',
            'TeamMembers'   => $data['teammembers_ta'] ?? '',
            'fontName'      => 'Tau_Marutham, sans-serif',
            'defaultSize'   => 11,
            'Teamdetails'   => $TeamDetails
        ] : [
            'DeptName'      => $data['deptelname'] ?? '',
            'InstituteName' => $data['instename'] ?? '',
            'InstCategory'  => $data['catename'] ?? '',
            'InstSubcat'    => $Subcatget   ,   
            'TypeofAudit'   => $data['typeofauditename'] ?? '',
            'DistName'      => ($data['distename'] ?? '') . ' District',
            'designName'    => $chargeData->desigelname ?? '',
            'UserName'      => $userData->username ?? '',
            'TeamHead'      => $data['teamhead_en'] ?? '',
            'TeamMembers'   => $data['teammembers_en'] ?? '',
            'fontName'      => 'Times New Roman',
            'defaultSize'   => 13,
            'Teamdetails'=>$TeamDetails
        ];
    
        // Merge original data with language-specific data
        return array_merge($data, $langData);
    }

    private function loadreportcontents($reportype,$lang)
    {
        $report = FormatModel::where('report_type', $reportype)->latest()->first();
        $fileFromTemplate = true; // Flag to track if content is from file
    
        if ($report && !empty($report->report_contents)) 
        {
            if ($lang == 'ta') {
                $reportContent = json_decode($report->report_contents_ta, true);
            } else {
                $reportContent = json_decode($report->report_contents, true);
            }
    
            if (isset($reportContent['content'])) {
                $htmlContent = $reportContent['content'];
            } else {
                $htmlContent = "<p>Content not found</p>";
            }

        }
        return $htmlContent;

    }

    private function loadintimationletterss($scheduleId, $lang)
    {
        $loaddata = $this->loadAllValues($scheduleId, $lang);
        $htmlContent = $this->loadreportcontents(1, $lang);

        // Convert HTML entities (ensure Tamil text is preserved)
        $htmlContent = html_entity_decode($htmlContent, ENT_QUOTES, 'UTF-8');

        $fromDate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($loaddata['fromdate'])));
        $toDate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($loaddata['todate'])));
        $entymeetdate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($loaddata['entrymeetdate'])));

        // TeamMembers
        $dynamicData = [
            'Team Head Details' => $loaddata['TeamHead'],
            'Institution Details' => $loaddata['InstituteName'],
            'Department Name' => $loaddata['DeptName'],
            'RC No' => $loaddata['rcno'],
            'Audit Year' => $loaddata['yearname'],
            'From Date' => $fromDate,
            'To Date' => $toDate,
            'Entry Meeting Date' => $entymeetdate,
            'Current Date' => Controller::ChangeDateFormat(date('d-m-Y'))
        ];

        // Replace placeholders with dynamic values
        foreach ($dynamicData as $key => $value) {
            $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
        }

        // Handle Team Members and Designations
        $serialNumber = 1;
        $tbodyContent = '';

        // Convert comma-separated values into an array
        $teamMembersArray = explode(',', $loaddata['TeamMembers']);
        $teamDesignationsArray = isset($loaddata['TeamDesignations']) ? explode(',', $loaddata['TeamDesignations']) : [];

        foreach ($teamMembersArray as $index => $member) {
            // Assign designation if available, otherwise set as '-'
            $designation = isset($teamDesignationsArray[$index]) ? trim($teamDesignationsArray[$index]) : '-';

            $tbodyContent .= "<tr>
                                <td>" . $serialNumber . "</td>
                                <td>" . htmlspecialchars(trim($member)) . "</td>
                                <td>" . htmlspecialchars($designation) . "</td>
                            </tr>";
            $serialNumber++;
        }

        // Ensure placeholder replacement works even if formatting differs
        $htmlContent = preg_replace('/<tbody>.*?\[S.No\].*?\[Name\].*?\[Designation\].*?<\/tbody>/is', "<tbody>$tbodyContent</tbody>", $htmlContent);

        // Remove <style> and <script> tags (PHPWord doesn't support them)
        $htmlContent = preg_replace('/<style.*?<\/style>/is', '', $htmlContent);
        $htmlContent = preg_replace('/<script.*?<\/script>/is', '', $htmlContent);

        return $htmlContent;
    }


    private function loadintimationletter($scheduleId, $lang)
    {
        $loaddata = $this->loadAllValues($scheduleId, $lang);
        $htmlContent = $this->loadreportcontents(1, $lang);

        // Convert HTML entities (ensure Tamil text is preserved)
        $htmlContent = html_entity_decode($htmlContent, ENT_QUOTES, 'UTF-8');

        $htmlContent = preg_replace('/<tbody>.*?\[S.No\].*?\[Name\].*?\[Designation\].*?<\/tbody>/is', "<tbody>[Table Content]</tbody>", $htmlContent);


        $fromDate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($loaddata['fromdate'])));
        $toDate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($loaddata['todate'])));
        $entymeetdate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($loaddata['entrymeetdate'])));

        // Generate table content first
        $serialNumber = 1;
        $tbodyContent = '';

        // Convert comma-separated values into an array
        $teamMembersArray = explode(',', $loaddata['TeamMembers']);

        foreach ($teamMembersArray as $member) {

            $parts = explode('(', $member);

            $name = trim($parts[0]);

            $designation = trim(str_replace(')', '', $parts[1]));

            $tbodyContent .= "<tr><td>" . $serialNumber . "</td><td>" . htmlspecialchars(trim($name)) . "</td><td>" . htmlspecialchars(trim($designation)) . "</td></tr>";
            $serialNumber++;
        }

    
        $audit_particulars = AuditManagementModel::Selected_CFR($scheduleId);
        $CallforRecords = array();

        $x=1;

        $x = 0; // Initialize a counter

        foreach ($audit_particulars as $record) 
        {        
            // Check the language and add the appropriate field to the result array
            if ($lang == 'ta') {
                // If the language is Tamil, use the Tamil field
                $CallforRecords[] = $record['callforrecordstname']; // Access as an array, not as an object
            } else {
                // Otherwise, use the English field
                $CallforRecords[] = $record['callforrecordsename']; // Access as an array, not as an object
            }
        
            // Optional: Break after processing 12 records (uncomment to stop after 12 iterations)
            if ($x == 12) {
                //break; // Stops the loop after 12 iterations
            }
        
            $x++; // Increment the counter
        }
        
       
        
        $CallforRecords = implode(", ", $CallforRecords);
        $CallforRecords = str_replace(['&', '<', '>'], ['&amp;', '&lt;', '&gt;'], $CallforRecords);
        $CallforRecords = htmlspecialchars($CallforRecords, ENT_XML1, 'UTF-8');


        // Store dynamic data
        $dynamicData = [
            'Team Head Details' => $loaddata['TeamHead'],
            'Institution Details' => $loaddata['InstituteName'],
            'Department Name' => $loaddata['DeptName'],
            'RC No' => $loaddata['rcno'],
            'Audit Year' => $loaddata['yearname'],
            'From Date' => $fromDate,
            'To Date' => $toDate,
            'Entry Meeting Date' => $entymeetdate,
            'Current Date' => Controller::ChangeDateFormat(date('d-m-Y')),
            'table content' => $tbodyContent ,// Store table content for replacement
            'CallforRecords'=>$CallforRecords
        ];

        // Replace the table placeholder separately

        // Replace other placeholders
        foreach ($dynamicData as $key => $value) {
            if ($key !== "tablecontent_replace11") {
                // Ensure the correct placeholder format
                $htmlContent = str_replace("[$key]", $value, $htmlContent);
            }
        }

        // Remove <style> and <script> tags (PHPWord doesn't support them)
        $htmlContent = preg_replace('/<style.*?<\/style>/is', '', $htmlContent);
        $htmlContent = preg_replace('/<script.*?<\/script>/is', '', $htmlContent);

        return $htmlContent;
    }


    private function loadentrymeeting($scheduleId, $lang,$fontName)
    {
        $loaddata = $this->loadAllValues($scheduleId, $lang);

       //print_r($loaddata);exit;
        $htmlContent = $this->loadreportcontents(2, $lang);
        
        // Convert HTML entities properly
       // $htmlContent = mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8');

       $fromDate = Controller::ChangeDateFormat(date('d-m-Y',strtotime($loaddata['entrymeetdate'])));

       $todate = Controller::ChangeDateFormat(date('d-m-Y',strtotime($loaddata['todate'])));

       $nodalpersondetails ='<span style="font-family:Times New Roman;">'.$loaddata['nodalname'] .'<br>'.$loaddata['nodaldesignation'].'</span>';


       $dynamicData = [
                       'Enter Institute Name'=>$loaddata['InstituteName'],
                       'Enter Institute Category'=>$loaddata['InstCategory'],
                       'Enter Institute SubCategory'=>$loaddata['InstSubcat'],
                       'Enter Audit Year'=>$loaddata['yearname'],
                       'Enter Entry Meet Date'=>Controller::ChangeDateFormat($loaddata['entrymeetdate']),
                       'Enter Proposed End Date'=>$todate,
                       'Enter Man Days Allocated'=>$loaddata['mandays'],
                       'Enter Audit Team Details'=>$loaddata['Teamdetails'],
                       'Enter Nodal officer name and details'=>$nodalpersondetails

                       ];

       // Replace placeholders with dynamic values
       foreach ($dynamicData as $key => $value)
       {
           // Replace [key] with actual values
           $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
       }

    

        
        // Remove <style> and <script> tags
        $htmlContent = preg_replace('/<style.*?<\/style>/is', '', $htmlContent);
        $htmlContent = preg_replace('/<script.*?<\/script>/is', '', $htmlContent);

       // $htmlContent = str_replace(['&', '<', '>'], ['&amp;', '&lt;', '&gt;'], $htmlContent);

       /// $htmlContent = htmlspecialchars($htmlContent, ENT_XML1, 'UTF-8');
        
        // Decode special characters for PHPWord compatibility
        //$htmlContent = htmlspecialchars_decode($htmlContent, ENT_QUOTES);
        
        return $htmlContent;
        
    }
    
    private function load_codeofethicscontents($scheduleId, $lang,$ifModel='',$Name='',$Desig='')
    {
        $loaddata=$this->loadAllValues($scheduleId, $lang);
        $htmlContent =$this->loadreportcontents(3,$lang);

        if($ifModel)
        {
           $Name =$Name;
           $Desig =$Desig;

        }else
        {
            $Name =$loaddata['UserName'];
            $Desig =$loaddata['designName'];

        }
                    
        $dynamicData = ['Name'=>$Name,
                        'Designation'=>$Desig,
                        'Current Date' =>Controller::ChangeDateFormat(date('d-m-Y'))
                       ];

        // Replace placeholders with dynamic values
        foreach ($dynamicData as $key => $value) 
        {
            // Replace [key] with actual values
            $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
        }
        $htmlContent = preg_replace('/<style.*?<\/style>/is', '', $htmlContent);

         return $htmlContent;
    }

    private function generateWorkAllocationTable($section, $auditscheduleid, $fontName, $defaultsize,$reportype,$lang)
    {
        // Fetch Work Allocation Data
        $workAllocation = FormatModel::fetch_allocatedwork($auditscheduleid);


        $labels =$this->loadlabels();

       $nodata_avail=$labels[$lang]['nodata_avail'];

        if (!$workAllocation->isEmpty()) {
            $results = [];
            foreach ($workAllocation->all() as $item) {
                $results[] = [
                    'username' => $item->username,
                    'worktypes' => $item->worktypes,
                ];
            }

            if (!empty($results)) {
                $section->addText(
                    '5. WORK ALLOCATION',
                    ['name' => $fontName, 'size' => 14, 'bold' => true],
                    ['align' => 'center', 'lineHeight' => 1.5, 'spaceBefore' => 70]
                );

                $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);

                // Add table headers with bold text
                $table->addRow();
                $table->addCell(1000)->addText("S.No.", ['bold' => true, 'name' => $fontName, 'size' => $defaultsize]);
                $table->addCell(5000)->addText("Team Member Name", ['bold' => true, 'name' => $fontName, 'size' => $defaultsize]);
                $table->addCell(5000)->addText("Work Allocation", ['bold' => true, 'name' => $fontName, 'size' => $defaultsize]);

                $serialNumber = 1;

                foreach ($results as $entry) {
                    $table->addRow();
                    $table->addCell(1000)->addText($serialNumber++, ['name' => $fontName, 'size' => $defaultsize]);
                    $table->addCell(5000)->addText($entry['username'], ['name' => $fontName, 'size' => $defaultsize]);
                    $worktypes = htmlspecialchars($entry['worktypes'], ENT_QUOTES, 'UTF-8');
                    $table->addCell(5000)->addText($worktypes, ['name' => $fontName, 'size' => $defaultsize]);
                }
            }
        } else {
            
            if($reportype=='singlereport')
            {
                $section->addText($nodata_avail,
                    ['name' => $fontName, 'size' => $defaultsize, 'bold' => true],
                    ['alignment' => 'center']
                );

            }
           
        }
    }


    private function loadexitmeeting($scheduleId, $lang,$fontName)
    {
        $loaddata = $this->loadAllValues($scheduleId, $lang);
        $htmlContent = $this->loadreportcontents(5, $lang);
        
        // Convert HTML entities properly
       // $htmlContent = mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8');
       $nodalpersondetails ='<span style="font-family:Times New Roman;">'.$loaddata['nodalname'] .'<br>'.$loaddata['nodaldesignation'].'</span>';

       $fromDate = Controller::ChangeDateFormat(date('d-m-Y',strtotime($loaddata['fromdate'])));
       $dynamicData = [
                       'Enter Institute Name'=>$loaddata['InstituteName'],
                       'Enter Audit Year'=>$loaddata['yearname'],
                       'Enter Audit Start Date'=>Controller::ChangeDateFormat($loaddata['fromdate']),
                       'Enter Audit Team Details'=>$loaddata['Teamdetails'],
                       'Enter Proposed Exit Meeting Date'=>Controller::ChangeDateFormat($loaddata['todate']),
                       'Enter Exit Meeting Date'=>Controller::ChangeDateFormat($loaddata['exitmeetdate']),
                       'Enter Allocated Man Days'=>$loaddata['mandays'],
                       'Enter Exact Man Days'=>$loaddata['mandays'],
                       'Enter Conference Date'=>'',
                       'Enter officer Details'=>$nodalpersondetails,
                       'Enter Data'=>''
                       ];

       // Replace placeholders with dynamic values
       foreach ($dynamicData as $key => $value)
       {
           // Replace [key] with actual values
           $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
       }

        
        
        // Remove <style> and <script> tags
        $htmlContent = preg_replace('/<style.*?<\/style>/is', '', $htmlContent);
        $htmlContent = preg_replace('/<script.*?<\/script>/is', '', $htmlContent);
        
        // Decode special characters for PHPWord compatibility
        //$htmlContent = htmlspecialchars_decode($htmlContent, ENT_QUOTES);
        
        return $htmlContent;
    }

    private function loadlabels()
    {
        $jsonFilePath = public_path('json/layout.json');
        $jsonContent = file_get_contents($jsonFilePath);
        $labels = json_decode($jsonContent, true);
        $labels = mb_convert_encoding($labels, 'UTF-8', 'auto');
        return  $labels; 
    }

    private function AuditSlipLoad($section, $auditscheduleid, $fontName, $defaultsize,$GetauditSlips,$lang)
    {
      
       $tableContent = '';
        
       $labels =$this->loadlabels();

       $amountinvolved_label=$labels[$lang]['amount_involved'];
       $severity_label=$labels[$lang]['severity'];
       $liability_label=$labels[$lang]['liability'];
       $slip_details_label=$labels[$lang]['slip_details'];
       $slip_details_headinglabel=$labels[$lang]['slipdetails_label'];

       $auditordetails=$labels[$lang]['auditordetails_heading'];
      

       $severitylow=$labels[$lang]['severity_low'];
       $severitymedium=$labels[$lang]['severity_medium'];
       $severityhigh=$labels[$lang]['severity_high'];


       $SeverityArr=['L' => $severitylow,'M' => $severitymedium,'H' => $severityhigh];

       $liabilityarr=['Y'=>$labels[$lang]['yes'],'N'=>$labels[$lang]['no']];

       //$GetauditSlips =json_decode($GetauditSlips,true);
       $liability = $GetauditSlips->getData()->liability; // Get 'data' key from response
       $GetauditSlips = $GetauditSlips->getData()->data; // Get 'data' key from response

       $groupedByMainslip = [];
       $groupedByLiability1 = [];
       $mainslipnumbers    = [];

        // Iterate over each item and group by 'mainslipnumber'
        foreach ($GetauditSlips as $item) 
        {
            $groupedByMainslip[$item->mainslipnumber][] = $item;
            $mainslipnumbers[] =$item->mainslipnumber;
        }
        $mainslipnumbers = array_unique($mainslipnumbers);

        foreach ($liability as $LiabilityItem) 
        {
            $groupedByLiability1[$LiabilityItem->mainslipnumber][] = $LiabilityItem;
        }

        $groupedByLiability = [];

        foreach ($mainslipnumbers as $val) {
            if (array_key_exists($val, $groupedByLiability1)) {
                // If the mainslip number exists in the first grouped array, add it to the final grouped array
                $groupedByLiability[$val] = $groupedByLiability1[$val];
            } else {
                // If the mainslip number doesn't exist, assign an empty array
                $groupedByLiability[$val] = 'nodata';
            }
        }


      // $groupedByMainslip = $GetauditSlips->groupBy('mainslipnumber');


        foreach ($groupedByMainslip as $mainslipNumber => $items) 
        {
            $textRun = $section->addTextRun(['align' => 'center']);

            if($lang == 'ta')
            {
                $textRun->addText('#'.$mainslipNumber.'-'.$slip_details_headinglabel, ['name' => $fontName,'bold' => true, 'size' => 14]);   
               

            }else
            {              
                $textRun->addText($slip_details_headinglabel.'#'.$mainslipNumber.'', ['name' => $fontName,'bold' => true, 'size' => 14]);
                
            }

            $X = 1;
            foreach ($items as $auditSlip) 
            {

                if($lang == 'ta')
                {
                    $objectionname = $auditSlip->objectiontname;
                    $subobjectionname = $auditSlip->subobjectiontname;

                }else
                {
                    $objectionname = $auditSlip->objectionename;
                    $subobjectionname = $auditSlip->subobjectionename;
                }


                $textRun = $section->addTextRun(['align' => 'center']);
                $textRun->addText(''.$X.') ' . $objectionname . ' => ', ['name' => $fontName, 'bold' => false, 'size' => 14]);
                $textRun->addText($subobjectionname , ['name' => $fontName, 'bold' => false, 'size' => $defaultsize]);

                // Add remaining content
                /*$textRun = $section->addTextRun(['align' => 'left']);
                $textRun->addText($amountinvolved_label.': ', ['name' => $fontName, 'size' => $defaultsize, 'bold' => true]);
                $textRun->addText($auditSlip->amtinvolved, ['name' => $fontName, 'size' => $defaultsize]);*/

                $textRun = $section->addTextRun(['align' => 'left']);
                $textRun->addText($severity_label .'   : ', ['name' => $fontName, 'size' => $defaultsize, 'bold' => true]);
                $textRun->addText($SeverityArr[$auditSlip->severityid], ['name' => $fontName, 'size' => $defaultsize]);

                /*$textRun = $section->addTextRun(['align' => 'left']);
                $textRun->addText($liability_label .'               : ', ['name' => $fontName, 'size' => $defaultsize, 'bold' => true]);
                $textRun->addText($liabilityarr[$auditSlip->liability], ['name' => $fontName, 'size' => $defaultsize]);

                if($auditSlip->liability == 'Y')
                {
                    $textRun = $section->addTextRun(['align' => 'left']);
                    $textRun->addText('Liability Name: ', ['name' => $fontName, 'size' => $defaultsize, 'bold' => true]);
                    $textRun->addText($auditSlip->liabilityname, ['name' => $fontName, 'size' => $defaultsize]);
                }*/

                $textRun = $section->addTextRun();
                $textRun->addText($slip_details_label .': ', ['name' => $fontName,'size' => $defaultsize,'bold' => true]);
                $textRun->addText($auditSlip->slipdetails, ['name' => $fontName,'size' => $defaultsize]);

                $textRun = $section->addTextRun(['align' => 'left']);
                $textRun->addText('Status : ', ['name' => $fontName, 'size' => $defaultsize, 'bold' => true]);
                $textRun->addText($auditSlip->processelname, ['name' => $fontName, 'size' => $defaultsize]);


                $textRun = $section->addTextRun(['align' => 'left']);

                

                $textRun->addText(''.$X.'.1.Remarks' , ['name' => $fontName,'bold' => true, 'size' => $defaultsize]);

                $textRun = $section->addTextRun(['align' => 'left']);

                if (!empty($auditSlip->remarks))
                {

                    $auditorRemarks = json_decode($auditSlip->remarks);
                    $auditorContent = isset($auditorRemarks->content) ? $auditorRemarks->content : 'No Remarks Available';

                    // Decode HTML entities
                    $auditorContent = html_entity_decode($auditorContent, ENT_QUOTES, 'UTF-8');
                        
                    // Convert double quotes to single quotes
                    $auditorContent = str_replace('"', "'", $auditorContent);
                        
                    // Remove entire style="" attributes
                    $auditorContent = preg_replace("/font-family:'([^']+)'/i", "font-family:$1", $auditorContent);


                    // Convert encoding to UTF-8 (ensure proper character handling)
                    $auditorContent = mb_convert_encoding($auditorContent, 'UTF-8', 'auto');
                        
                    // Add clean HTML content to PHPWord
                    Html::addHtml($section, $auditorContent, false, false);
                        

                } else
                {
                    $section->addText('No Remarks Available', ['size' => $defaultsize,'name' => $fontName]);
                }

                //$section->addLine(['weight' => 1, 'width' => 430, 'height' => 0, 'color' => '000000']);
                // Add a line break at the end of each loop iteration
                $section->addText('', ['name' => $fontName, 'size' => $defaultsize]); // Blank line after each loop iteration

                $X++;

            }
            $Liability = $groupedByLiability[$mainslipNumber];

            if($Liability != 'nodata')
            {
                $textRun = $section->addTextRun(['align' => 'center']);
                $textRun->addText('Liability Details', ['name' => $fontName,'bold' => true, 'size' => 14]);

                $tableStyle = array(
                    'borderSize' => 6,
                    'borderColor' => '000000',
                    'cellMargin' => 80,
                );
                
                // Add the table to the section
                $table = $section->addTable('tableStyle');
                
                // Add the header row
                $table->addRow();
                $table->addCell(2000)->addText('Liability Name', array('bold' => true, 'name' => $fontName, 'size' =>12));
                $table->addCell(2000)->addText('Details', array('bold' => true, 'name' => $fontName, 'size' =>12));
                $table->addCell(2000)->addText('Designation', array('bold' => true, 'name' => $fontName, 'size' =>12));
                $table->addCell(2000)->addText('Amount Involved', array('bold' => true, 'name' => $fontName, 'size' => 12));
                
                // Assuming $Liability is your array of objects
                foreach ($Liability as $LiabilityKey => $LiabilityVal) {
                    $table->addRow();
                    $table->addCell(2000)->addText($LiabilityVal->liabilityname, array('name' => $fontName, 'size' => 12));
                
                    // Conditional logic for Identification Number with prefix
                    $identificationNumber = $LiabilityVal->liabilitygpfno;
                    $prefix = ''; // Initialize prefix variable
                    
                    if ($LiabilityVal->notype == 1) {
                        $prefix = 'GPF No: ';
                    } elseif ($LiabilityVal->notype == 2) {
                        $prefix = 'CF No: ';
                    } elseif ($LiabilityVal->notype == 3) {
                        $prefix = 'IHRMS No: ';
                    }
                
                    // Add the prefix and the identification number together
                    $table->addCell(2000)->addText($prefix . $identificationNumber, array('name' => $fontName, 'size' => 12));
                    $table->addCell(2000)->addText($LiabilityVal->liabilitydesignation, array('name' => $fontName, 'size' => 12));
                    $table->addCell(2000)->addText($LiabilityVal->liabilityamount, array('name' => $fontName, 'size' => 12));
                }
                

            }


          
         }
         
        // Html::addHtml($textRun, $htmlContent, false, false);

 
    }


    private function OverviewContentLoad($auditscheduleid)
    {
        $html = '<h3>OVERVIEW</h3>';

        $html .= 'This Report contains four chapters. The first and second chapters contain an Executive
                Summary and overview of Annual Accounts respectively. The third chapter contains details
                of audit procedure and Auditable Institutions. The fourth chapter contain the Introduction and
                audit observations pertaining to City Municipal Corporations, Municipalities and Town
                Panchayats in Urban Local Bodies respectively.1. Executive Summary:
                An Overview of Financial Position of Urban Local Bodies (viz., Municipal Corporations,
                Municipalities and Town Panchayats).IL Overview of Annual Accounts:
                Comparative analysis of Income and Expenditure under various sub-heads of Urban Local
                Bodies.
                III Audit procedure and Auditable Institutions:
                A short introduction on the Tamil Nadu Local Fund Audit Department and the Tamil Nadu
                Local Fund Audit Act, 2014. Audit procedure, Number of Auditable Institutions and the Gist
                of major Audit Observations are given in this chapter.
                IV. Introduction to Urban Local Bodies and Major Audit Observations:
                For the Year ended March 2022, 41 Major Audit Observations pertaining to Urban Local
                Bodies are discussed in this chapter.';

        return $html;       
    }

    private function currentparadetails($GetauditSlips,$lang)
    {
         $labelsJson_layout = json_decode(file_get_contents(public_path('json/layout.json')), true);
        $label_layout = $labelsJson_layout[$lang];

        $GetauditSlips = $GetauditSlips->getData()->data;
    
        /*$groupedByMainslip = [];
        $groupedByLiability1 = [];
        $mainslipnumbers = [];
    
        foreach ($GetauditSlips as $item) {
            $groupedByMainslip[$item->mainslipnumber][] = $item;
            $mainslipnumbers[] = $item->mainslipnumber;
        }
    
        $mainslipnumbers = array_unique($mainslipnumbers);*/

       $currentparacontent = '<div><h3 style="text-align:center;"><u>' . $label_layout['currectyearpata'] . '</u></h3>';

       $currentparacontent .= ' <table style="width: 100%; border: none; border-collapse: collapse;">
        <tr>
            <th style="width:10%; font-weight: bold;text-align:center;">' . $label_layout['s_no'] . '</th>
            <th style="width:25%; font-weight: bold;text-align:center;">' . $label_layout['reportpara'] . '</th>
            <th style="width:20%; font-weight: bold;text-align:center;">' . $label_layout['amount'] . '</th>
        </tr>';

        $counter = 1; // Initialize the counter for S.No

        // Loop through the $mainslipnumbers array
       /* foreach ($mainslipnumbers as $slipkey => $slipval) {
            $currentparacontent .= '<tr>';
            $currentparacontent .= '<td style="width:10%;">' . $counter . '</td>'; // Insert S.No
            $currentparacontent .= '<td style="width:25%;">' . $counter . '</td>';  // Insert Audit Report Para No
            $currentparacontent .= '<td style="width:25%;">' . $slipval . '</td>'; // Insert Audit Notes Para No
            $currentparacontent .= '<td style="width:20%;">' . (isset($slipval['amtinvolved']) ? $slipval['notes_amount'] : '6770') . '</td>'; // Insert Amount for Audit Notes
            $currentparacontent .= '</tr>';

            $counter++;
        }*/
        $totalAmount = 0;

          foreach ($GetauditSlips as $item) {

            $currentparacontent .= '<tr>';
            $currentparacontent .= '<td style="width:10%;text-align:center;">' . $counter . '</td>'; // Insert S.No
            $currentparacontent .= '<td style="width:25%;text-align:center;">' . str_pad($counter, 4, '0', STR_PAD_LEFT) . '</td>';  // Insert Audit Report Para No
        
            $currentparacontent .=  '<td style="text-align:right;">' . htmlspecialchars($this->formatIndianCurrency($item->amtinvolved)) . '</td>'; // Insert Amount for Audit Notes
            $currentparacontent .= '</tr>';
            $counter++;

             $totalAmount += $item->amtinvolved; // Add to total
        }

          // Add total row
        $currentparacontent .= '<tr>';
        $currentparacontent .= '<td colspan="2" style="text-align:right;font-weight:bold;">' . $label_layout['total_amt'] . '</td>';
        $currentparacontent .= '<td style="font-weight:bold;text-align:right;">' . htmlspecialchars($this->formatIndianCurrency($totalAmount)) . '</td>';
        $currentparacontent .= '</tr>';

        $currentparacontent .= '</tbody>';
        $currentparacontent .= '</table>';

        $currentparacontent .= '</table></div>';

        return $currentparacontent;
    }
   
private function generatePartAContent($scheduleId, $lang,$handlestatusflag)
    {
        $labelsJson = json_decode(file_get_contents(public_path('json/report.json')), true);
        $label = $labelsJson[$lang];


        $labelsJson_layout = json_decode(file_get_contents(public_path('json/layout.json')), true);
        $label_layout = $labelsJson_layout[$lang];


        $loaddata = $this->loadAllValues($scheduleId, $lang);


        $AuditeeInstituteDetails = FormatModel::AuditeeUsers($scheduleId);

       // b) Period of Audit Conducted
        $yearLabel = ($loaddata['deptcode'] == '01') 
                            ? 'Falsi Year'
                            : 'Financial Year';

        $yearName =$loaddata['yearname'];

        $entrydate =date('d-m-Y',strtotime($loaddata['entrymeetdate']));
        $exitmeet =date('d-m-Y',strtotime($loaddata['exitmeetdate']));


       if ($lang === 'en') {
                $intname = htmlspecialchars($loaddata['instename']);
                $yearLabel = ($loaddata['deptcode'] == '01') 
                    ? 'Fasli Year'
                    : 'Financial Year';
                    $extra = "for";
            } else {
                $intname = htmlspecialchars($loaddata['insttname']);
                $yearLabel = ($loaddata['deptcode'] == '01') 
                    ? 'ஃபஸ்லி ஆண்டு'
                    : 'நிதி ஆண்டு';
                    $extra = '-';
            }



        $officialRows = '';
        $count = 1;
        foreach ($AuditeeInstituteDetails as $official) 
        {

              $fromDate = !empty($official->service_fromdate) ? date('d-m-Y', strtotime($official->service_fromdate)) : '-';
            $toDate = !empty($official->service_todate) ? date('d-m-Y', strtotime($official->service_todate)) : 'Till Date';


            $officialRows .= '<tr>';
            $officialRows .= '<td>' . $count++ . '</td>';
            $officialRows .= '<td>' . htmlspecialchars($official->ofc_username) . ' , ' . htmlspecialchars($official->ofc_designation) . '</td>';
            $officialRows .= '<td>' . date('d-m-Y', strtotime($official->service_fromdate)) . ' to ' . date('d-m-Y', strtotime($official->service_todate)) . '</td>';
            $officialRows .= '</tr>';
        }

        $parta_content = '
        <div style="border: 2px solid black; padding: 20px; text-align: center; margin-bottom: 20px;">
<h3 style="margin: 0;">' . $label['audit_report_title'] . ' ' . $intname . ' ' . $extra . ' ' . $yearLabel . ' ' . htmlspecialchars($loaddata['yearname']) . '</h3>
        </div>
        
        <!-- a) Name of the Auditors -->
        <div style="margin-bottom: 10px;">
            <table style="width: 100%; border: none; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; font-weight: bold; border: none;">' . $label['name_of_auditors'] . '</td>
                    <td style="width: 2%; border: none;"><b>:</b></td>
                    <td style="width: 48%; border: none;">' . $loaddata['Teamdetails'] . '</td>
                </tr>';
        
        $parta_content .= '</table></div>';
        
       
        $parta_content .= '
            <div style="margin-bottom: 10px;">
                <table style="width: 100%; border: none; border-collapse: collapse;">
                    <tr>
                        <td style="width: 50%; font-weight: bold; border: none;">' . $label['period_of_audit'] . '</td>
                        <td style="width: 2%; border: none;"><b>:</b></td>
                        <td style="width: 48%; border: none;">'.$entrydate.' to '.$exitmeet.'</td>
                    </tr>
                </table>
            </div>';

        
        $parta_content .= '
        <div style="margin-bottom: 10px;">
            <p><strong>' . $label['officials_of_institution'] . '</strong></p>
            <table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">
                <thead>
                    <tr>
                        <th>' . $label['s_no'] . '</th>
                        <th>' . $label['name_desig'] . '</th>
                        <th>' . $label['service_period'] . '</th>
                    </tr>
                </thead>
                <tbody>' . $officialRows . '</tbody>
            </table>
        </div>';

      
        

      /* if($loaddata['deptcode'] == 03)
        {
            $Contentauthorityofaudit = DB::table('audit.map_authorityofaudit as auth')
                                ->select('auth.auth_content_en')
                                ->where('auth.deptcode', $loaddata['deptcode'])
                                ->where('auth.catcode', $loaddata['catcode'])
                                ->first();

        }else
        {
            $Contentauthorityofaudit = DB::table('audit.map_authorityofaudit as auth')
                                ->select('auth.auth_content_en')
                                ->where('auth.deptcode', $loaddata['deptcode'])
                                ->first();

        }*/

       $Contentauthorityofaudit = DB::table('audit.map_authorityofaudit as auth')
                                        ->select('auth.auth_content_en','auth.auth_content_ta')
                                        ->where('auth.deptcode', $loaddata['deptcode'])
                                        ->when(in_array($loaddata['deptcode'], [03, 02]), function ($query) use ($loaddata) {
                                            $query->where('auth.catcode', $loaddata['catcode']);

                                            if (!empty($loaddata['subcatid'])) {
                                                $query->where('auth.subcatid', $loaddata['subcatid']);
                                            }

                                            return $query;
                                        })
                                        ->first();


$authorityofaudit_remarks = '';

        if ($Contentauthorityofaudit && !empty($Contentauthorityofaudit->auth_content_en)) {
            if($lang === 'en'){
            $decoded = json_decode($Contentauthorityofaudit->auth_content_en);
            }else{
                 $decoded = json_decode($Contentauthorityofaudit->auth_content_ta);
            }
            if (isset($decoded->content)) {
                $authorityofaudit_remarks = $decoded->content;
            }
        }
                   
        $parta_content .= '<div class="page-break"></div><div style="margin-bottom: 10px;">
            <table style="width: 100%; border: none; border-collapse: collapse;">
                <tr>
                    <td style="width: 100%; font-weight: bold; border: none;">' . $label['authority_of_audit'] . '</td>
                </tr>
                 <tr>
                    <td style="width: 100%; border: none;">'.$authorityofaudit_remarks.'</td>
                </tr>
            </table>
        </div>';

         $GenesisofAudit = DB::table('audit.report_insitutegenesis')
                            ->where('scheduleid', $scheduleId)
                            ->where('statusflag', $handlestatusflag)
                            ->first();

    
        $genesis_remarks= json_decode($GenesisofAudit->genesis_remarks)->content;

        $parta_content .= '<div ></div><div style="margin-bottom: 10px;">
            <table style="width: 100%; border: none; border-collapse: collapse;">
                <tr>
                    <td style="width: 100%; font-weight: bold; border: none;">' . $label['genesis_title'] . '</td>
                </tr>
                 <tr>
                    <td style="width: 100%; border: none;">'.$genesis_remarks.'</td>
                </tr>
            </table>
        </div>';

        $AccountDetails = DB::table('audit.report_accountdetails')
                            ->where('auditscheduleid', $scheduleId)
                            ->where('statusflag', $handlestatusflag)
                            ->first();


        $parta_content .= '<div style="margin-bottom: 10px;">
        <br>
        <p><b>' . $label['accounts_section_title'] . '</b></p>

       

        <p><b>' . $label['accounts_intro'] . '</b></p>

        <p>' . $label['accounts_para'] . '</p>

        <table style="width: 100%; border: none; border-collapse: collapse;">
            <tr>
                <th style="width:8%; font-weight: bold;text-align:center;">' . $label_layout['s_no'] . '</th>
                <th style="width:12%; font-weight: bold;text-align:center;">' . $label_layout['namescheme'] . '</th>
                <th style="width:12%; font-weight: bold;text-align:center;">' . $label_layout['bank'] . '</th>
                <th style="width:12%; font-weight: bold;text-align:center;">' . $label_layout['branch'] . '</th>
                <th style="width:12%; font-weight: bold;text-align:center;">' . $label_layout['bank_acc_no'] . '</th>
                <th style="width:5%; font-weight: bold;text-align:center;">' . $label_layout['cashbook'] . '<br>OB<br>(1)</th>
                <th style="width:10%; font-weight: bold;text-align:center;">' . $label_layout['receipts'] . '<br>(2)</th>
                <th style="width:8%; font-weight: bold;text-align:center;">' . $label_layout['total'] . '<br>(3)<br>(1+2)</th>
                <th style="width:10%; font-weight: bold;text-align:center;">' . $label_layout['expenditure'] . '<br>(4)</th>
                <th style="width:10%; font-weight: bold;text-align:center;">' . $label_layout['cb_cashbook'] . '<br>(5)<br>(3 - 4)</th>
                <th style="width:8%; font-weight: bold;text-align:center;">' . $label_layout['cheq'] . '<br>' . $label_layout['Add'] . '<br>(6)</th>
                <th style="width:8%; font-weight: bold;text-align:center;">' . $label_layout['discheq'] . '<br>' . $label_layout['less'] . '<br>(7)</th>
                <th style="width:10%; font-weight: bold;text-align:center;">' . $label_layout['cb_passbook'] . '<br>(8)<br>(5+6-7)</th>
            </tr>';

        // Decode JSON fields
        $account_details = json_decode($AccountDetails->account_details ?? '{}', true);
        $bank_account_number = json_decode($AccountDetails->bank_account_number ?? '{}', true);
        $ob = json_decode($AccountDetails->ob ?? '{}', true);
        $receipts = json_decode($AccountDetails->receipts ?? '{}', true);
        $total = json_decode($AccountDetails->total ?? '{}', true);
        $expenditure = json_decode($AccountDetails->expenditure ?? '{}', true);
        $cb_cashbook = json_decode($AccountDetails->cb_cashbook ?? '{}', true);
        $add = json_decode($AccountDetails->add ?? '{}', true);
        $less = json_decode($AccountDetails->less ?? '{}', true);
        $cb_passbook = json_decode($AccountDetails->cb_passbook ?? '{}', true);
        $scheme = json_decode($AccountDetails->scheme ?? '{}', true);
        $branch = json_decode($AccountDetails->branch ?? '{}', true);

        // Determine keys
        $entryKeys = array_keys($account_details);
        if (empty($entryKeys)) {
            $entryKeys = [1]; // fallback default
        }


        foreach ($entryKeys as $index => $key) 
        {
            // Collect all values for the current row
            $values = [
                $account_details[$key] ?? null,
                $bank_account_number[$key] ?? null,
                $ob[$key] ?? null,
                $receipts[$key] ?? null,
                $total[$key] ?? null,
                $expenditure[$key] ?? null,
                $cb_cashbook[$key] ?? null,
                $add[$key] ?? null,
                $less[$key] ?? null,
                $cb_passbook[$key] ?? null,
                $scheme[$key]  ?? null,
                $branch[$key]  ?? null,

            ];

            // Check if all values are null or empty
            $allEmpty = true;
            foreach ($values as $value) {
                if (!is_null($value) && $value !== '') {
                    $allEmpty = false;
                    break;
                }
            }

            if ($allEmpty) {
                continue; // Skip this row
            }

            // Add the row if not all values are empty
            $parta_content .= '
                <tr>
                    <td style="width:8%;">' . ($index + 1) . '</td>
                    <td style="width:12%;">' . ($scheme[$key] ?? '') . '</td>
                    <td style="width:12%;">' . ($account_details[$key] ?? '') . '</td>
                    <td style="width:12%;">' . ($branch[$key] ?? '') . '</td>
                    <td style="width:12%;">' . ($bank_account_number[$key] ?? '') . '</td>
                    <td style="width:5%;">' . ($ob[$key] ?? '') . '</td>
                    <td style="width:10%;">' . ($receipts[$key] ?? '') . '</td>
                    <td style="width:8%;">' . ($total[$key] ?? '') . '</td>
                    <td style="width:10%;">' . ($expenditure[$key] ?? '') . '</td>
                    <td style="width:10%;">' . ($cb_cashbook[$key] ?? '') . '</td>
                    <td style="width:8%;">' . ($add[$key] ?? '') . '</td>
                    <td style="width:8%;">' . ($less[$key] ?? '') . '</td>
                    <td style="width:10%;">' . ($cb_passbook[$key] ?? '') . '</td>
                </tr>';
        }

        
        $PANTAN_Details = DB::table('audit.report_pantan')
                            ->where('auditscheduleid', $scheduleId)
                            ->where('statusflag', $handlestatusflag)
                            ->first();

        // $itfiling_remaks= json_decode($PANTAN_Details->itfiling_issue)->content;

         $itfiling_remaks = '';
        if (!empty($PANTAN_Details->itfiling_issue)) {
            $raw = $PANTAN_Details->itfiling_issue;
        $itfiling_remaks= json_decode($PANTAN_Details->itfiling_issue)->content;
            $decoded = json_decode($raw);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (is_object($decoded) && isset($decoded->content)) {
                    $itfiling_remaks = $decoded->content;
                } elseif (is_string($decoded)) {
                    // This means it was a quoted string in JSON, like: "<p>text</p>"
                    $itfiling_remaks = $decoded;
                }
            } else {
                // Fallback in case it's not even valid JSON
                $itfiling_remaks = $raw;
            }
        }


        $legalcomplaince_remaks= json_decode($PANTAN_Details->legal_complaince)->content;
        $financialreview_remaks= json_decode($PANTAN_Details->financial_review)->content;
        $parta_content .= '</table>

            <p><center><b >' . $label['filing_status_title'] . '</b></center></p>
             <p><b>' . $label_layout['tdsdetails'] . '</b></p>';
            $tdsFiledData = DB::table('audit.report_tds_filed_details')
                ->where('auditscheduleid', $scheduleId)
                ->where('statusflag', 'Y')
                ->get();
            if (!$tdsFiledData->isEmpty()) 
            {
                $tableHtml = '
                    <table border="1" style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr>
                                <th style="width:19%;text-align:center;" class="lang">' . $label_layout['financeyr'] . '</th>
                                <th style="width:19%;text-align:center;" class="lang">' . $label_layout['section'] . '</th>
                                <th style="width:19%;text-align:center;" class="lang">' . $label_layout['period_label'] . '</th>
                                <th style="width:19%;text-align:center;" class="lang1">' . $label_layout['remittance'] . '</th>
                                <th style="width:19%;text-align:center;" class="lang1">' . $label_layout['returnfiled'] . '</th>
                            </tr>
                        </thead>
                        <tbody>';

                foreach ($tdsFiledData as $row) {
                    $tableHtml .= '
                        <tr>
                            <td style="text-align:center;">' . htmlspecialchars($row->audityear) . '</td>
                            <td style="text-align:center;">' . htmlspecialchars($row->filing_status) . '</td>
                            <td style="text-align:center;">' . htmlspecialchars($row->auditquarter) . '</td>
                            <td style="text-align:center;">' . htmlspecialchars($row->remit_on_time) . '</td>
                            <td style="text-align:center;">' . htmlspecialchars($row->returns_filed) . '</td>
                        </tr>';
                }

                $tableHtml .= '
                        </tbody>
                    </table>';
                
            }else
            {
                 $tableHtml ='<p>' . $label['no_tds'] . '</p>';
            }
          


            $parta_content .=$tableHtml .'<p><b>' . $label_layout['itfill'] . '</b></p>
             <p>'.$itfiling_remaks.'</p>

             <p><b>' . $label_layout['gstreturn'] . '</b></p>';
            $Report_GST = DB::table('audit.report_gstreturn_details')
                                        ->where('auditscheduleid', $scheduleId)
                                        ->first();
                        
            $gstdata =  [
                                'audit_year' => '2025-2026',
                                'q1' => json_decode($Report_GST->det_q1,true),
                                'q2' => json_decode($Report_GST->det_q2,true),
                                'q3' => json_decode($Report_GST->det_q3,true),
                                'q4' => json_decode($Report_GST->det_q4,true),
                            ];

        $quarters = ['q1', 'q2', 'q3', 'q4'];

            $tableHtml = '
            <b>' . $label_layout['financeyr'] . '  - 2024 -2025</b>
            <table border="1" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:center;">' . $label_layout['period_label'] . '</th>
                        <th style="text-align:center;">' . $label_layout['remittance_ontime'] . '</th>
                        <th style="text-align:center;">' . $label_layout['duedatebefore'] . '</th>
                    </tr>
                </thead>
                <tbody>';
$yesNoMap = [
    'en' => ['yes' => 'Yes', 'no' => 'No'],
    'ta' => ['yes' => 'ஆம்', 'no' => 'இல்லை']
];
            foreach ($quarters as $q) {
                $qUpper = strtoupper($q);
                // $remit = $gstdata[$q]['remit'] ?? '';
                // $filed = $gstdata[$q]['filed'] ?? '';

                // Normalize inputs
    $remitRaw = strtolower(trim($gstdata[$q]['remit'] ?? ''));
    $filedRaw = strtolower(trim($gstdata[$q]['filed'] ?? ''));

    // Fallback if value not recognized
    $translatedRemit = $yesNoMap[$lang][$remitRaw] ?? $gstdata[$q]['remit'] ?? '';
    $translatedFiled = $yesNoMap[$lang][$filedRaw] ?? $gstdata[$q]['filed'] ?? '';

                // print_r($translatedRemit);
                // exit;
                $tableHtml .= '
                    <tr>
                        <td style="text-align:center;">' . $qUpper . '</td>
                        <td style="text-align:center;">' . $translatedRemit . '</td>
                        <td style="text-align:center;">' . $translatedFiled . '</td>
                    </tr>';
            }

            $tableHtml .= '
                </tbody>
            </table>';

            $parta_content .=$tableHtml;

            
                    $Report_lWF = DB::table('audit.report_labourwelfarefund')
                                    ->where('auditscheduleid', $scheduleId)
                                    ->first();

                    if($Report_lWF)
                    {
                        

                            $tableHtml = '
                        <br><div style="margin-bottom: 10px;">
                            <b>' . $label_layout['labour_heading'] . '</b>
                        </div>
                        <table border="1" cellpadding="5" style="width:100%; border-collapse: collapse; font-size: 12px;">
                            <thead>
                                <tr style="background-color: #f2f2f2;">
                                    <th style="text-align:center;">' . $label_layout['s_no'] . '</th>
                                    <th style="text-align:center;">' . $label_layout['details'] . '</th>
                                    <th style="text-align:center;">' . $label_layout['remarks'] . '</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="text-align:center;">1</td>
                                    <td>' . $label_layout['estimate_deduct'] . '</td>
                                    <td style="text-align:center;">'.$Report_lWF->lwfq1_remarks.'</td>
                                </tr>
                                <tr>
                                    <td style="text-align:center;">2</td>
                                    <td>' . $label_layout['nodeduct'] . '</td>
                                    <td>'.$Report_lWF->lwfq2_remarks.'</td>
                                </tr>
                                <tr>
                                    <td style="text-align:center;">3</td>
                                    <td>' . $label_layout['lwf_collect'] . '</td>
                                    <td style="text-align:center;">'.$Report_lWF->lwfq3_remarks.'</td>
                                </tr>
                                <tr>
                                    <td style="text-align:center;">4</td>
                                    <td>' . $label_layout['shortfall'] . '</td>
                                    <td style="text-align:center;">'.$Report_lWF->lwfq4_remarks.'</td>
                                </tr>
                            </tbody>
                        </table>';

                    $parta_content .=$tableHtml;

                    }
             

         $parta_content .='<p><b>' . $label_layout['legal'] . '</b></p>
             <p>'.$legalcomplaince_remaks.'</p>

             <p><b>' . $label_layout['financial_review'] . '</b></p>
             <p>'.$financialreview_remaks.'</p>
            
            
            
            ' ;


        return $parta_content;
    }

    private function generatePartCContent($scheduleId, $lang)
    {
        $partc_content = '<h1 style="text-align:center;">PART - III</h1>';

        $partc_content .= '<p><b>1) List of Annexures</b></p>';

        return $partc_content;

    }


  
    private function GistAuditObjections($auditscheduleid, $GetauditSlips, $lang)
    {
        $labels = $this->loadlabels();
        $GetauditSlips = $GetauditSlips->getData()->data;
        if($lang === 'en'){
        $html = '<h3>GIST OF AUDIT OBJECTIONS</h3>';

        $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>S.No</th>';
        $html .= '<th>Para No</th>';
        $html .= '<th>Details of Observation</th>';
        $html .= '<th>Amount</th>';
        //$html .= '<th>Page No</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        }else{
            $html = '<h3>தணிக்கை ஆட்சேபனைகளின் சுருக்கம்</h3>';

        $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>வ.எண்</th>';
        $html .= '<th>பாரா எண்</th>';
        $html .= '<th>கவனிப்பு விவரங்கள்</th>';
        $html .= '<th>தொகை</th>';
        //$html .= '<th>பக்க எண்</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        }
        $count = 1;

        $OrderingSlips = DB::table('audit.report_storesliporder')
                ->where('auditscheduleid', $auditscheduleid)
                ->select('ser_ordered_slips', 'nonser_ordered_slips')
                ->first();

              
        // Step 1: Decode both ordered slip JSONs
        $seriousOrdered = isset($OrderingSlips->ser_ordered_slips)
                ? json_decode($OrderingSlips->ser_ordered_slips, true)
                : [];

        $nonSeriousOrdered = isset($OrderingSlips->nonser_ordered_slips)
                ? json_decode($OrderingSlips->nonser_ordered_slips, true)
                : [];

        // Step 2: Merge serious first, then non-serious
        $orderedArray = array_merge($seriousOrdered, $nonSeriousOrdered);

        // Step 2: Reorder the $GetauditSlips collection
        $orderedSlips = [];

        if (!empty($orderedArray)) {
            $lookup = collect($GetauditSlips)->keyBy('auditslipid');

            foreach ($orderedArray as $pos => $slipId) {
                if ($lookup->has($slipId)) {
                    $orderedSlips[] = $lookup[$slipId];
                }
            }

            // Optional: Append any slips not in the order
            $remaining = collect($GetauditSlips)->whereNotIn('auditslipid', $orderedArray);
            foreach ($remaining as $item) {
                $orderedSlips[] = $item;
            }
        } else {
            $orderedSlips = $GetauditSlips;
        }
        


        $totalAmount = 0; // Initialize total

        // Step 3: Generate the HTML table rows
        foreach ($orderedSlips as $item) {
            $html .= '<tr>';
            $html .= '<td>' . $count . '</td>';
            $html .= '<td>' . str_pad($count, 4, '0', STR_PAD_LEFT) . '</td>';
            $html .= '<td>' . 
         htmlspecialchars($lang === 'ta' ? $item->objectiontname : $item->objectionename) . 
         ' - ' . htmlspecialchars($item->slipdetails) . 
         '</td>';
            $html .= '<td style="text-align:right;">' . htmlspecialchars($this->formatIndianCurrency($item->amtinvolved)) . '</td>';
            $html .= '</tr>';

            $totalAmount += $item->amtinvolved; // Add to total
            $count++;
        }

        // Add total row
        $html .= '<tr>';
$html .= '<td colspan="3" style="text-align:right;font-weight:bold;">' . 
         ($lang === 'ta' ? 'மொத்தத் தொகை' : 'Total Amount Involved') . 
         '</td>';
        $html .= '<td style="font-weight:bold;text-align:right;">' . htmlspecialchars($this->formatIndianCurrency($totalAmount)) . '</td>';
        $html .= '</tr>';

        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }

    private function formatIndianCurrency($amount)
    {
        $intPart = (int) round($amount); // Remove decimals
        $lastThree = substr($intPart, -3);
        $restUnits = substr($intPart, 0, -3);
        
        if ($restUnits != '') {
            $restUnits = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits);
            return $restUnits . "," . $lastThree;
        } else {
            return $lastThree;
        }
    }


    
  
  private function AuditSlipLoadPDF($auditscheduleid, $GetauditSlips, $lang, $mpdf,$seriousLastSlipNO,$sertype)
    {
        $labels = $this->loadlabels();
        //$slipPageMap = [];

        $amountinvolved_label = $labels[$lang]['amount_involved'];
        $severity_label = $labels[$lang]['severity'];
        $liability_label = $labels[$lang]['liability'];
        $slip_details_label = $labels[$lang]['slip_details'];
        $slip_details_headinglabel = $labels[$lang]['slipdetails_label'];
        $auditordetails = $labels[$lang]['auditordetails_heading'];

        $severitylow = $labels[$lang]['severity_low'];
        $severitymedium = $labels[$lang]['severity_medium'];
        $severityhigh = $labels[$lang]['severity_high'];

        $SeverityArr = ['L' => $severitylow, 'M' => $severitymedium, 'H' => $severityhigh];
        $liabilityarr = ['Y' => $labels[$lang]['yes'], 'N' => $labels[$lang]['no']];

        $liability = $GetauditSlips->getData()->liability;
        $GetauditSlips = $GetauditSlips->getData()->data;

        // Group slips by irregularity category and subcategory
        $groupedByIrregularity = [];
        $groupedByLiability = [];

        foreach ($GetauditSlips as $item) {
            $cat = $item->irregularitiescatelname;
            $subcat = $item->irregularitiessubcatelname;
            $groupedByIrregularity[$cat][$subcat][$item->mainslipnumber][] = $item;
        }

        // Group liabilities by mainslip
        foreach ($liability as $LiabilityItem) {
            $groupedByLiability[$LiabilityItem->mainslipnumber][] = $LiabilityItem;
        }

        $html = '';

        $X = 1; // Start serial number outside all loops
        $currentSlip = 1; // Track current slip number

        if($sertype == 'nonser')
        {
        $X = $seriousLastSlipNO;
        }

        foreach ($groupedByIrregularity as $catCode => $subCats) {
            foreach ($subCats as $subCatCode => $slipGroups) {
                foreach ($slipGroups as $mainslipNumber => $items) {
                    $title = ($lang == 'ta') 
                        ? '#' . $mainslipNumber . '-' . $slip_details_headinglabel 
                        : $slip_details_headinglabel . ' #' . $mainslipNumber;

                    $html .= "<a name='slip-{$mainslipNumber}'></a>"; // Anchor for referencing later

                    // Capture the current page for TOC
                   // $slipPageMap[$mainslipNumber] = $mpdf->PageNo();

                     foreach ($items as $auditSlip) 
                    {
                            // Add page break *before* second slip onwards
                            if($currentSlip > 1)
                            {
                                $html .= '<div style="page-break-before:after;">';
                            }

                            $auditslipdetails = !empty($auditSlip->final_slipdetails) ? $auditSlip->final_slipdetails : $auditSlip->slipdetails;


                            $objectionname = ($lang == 'ta') ? $auditSlip->objectiontname : $auditSlip->objectionename;

                            $html .= "<a name='slip-{$mainslipNumber}'></a>";
                            $html .= "<p style='margin-bottom: 4px;'><strong>$X) $objectionname</strong></p>";
                            $html .= "<div style=\"font-family: 'nototamil', 'DejaVu Sans', sans-serif; border: 1px solid #222; padding: 8px; background-color: #e4e4e4; margin-bottom: 4px;\">{$auditslipdetails}</div>";

                            $auditslipremarks = !empty($auditSlip->final_remarks) ? $auditSlip->final_remarks : $auditSlip->sliphistory_remarks;

                            if (!empty($auditslipremarks)) {
                                $auditorRemarks = json_decode($auditslipremarks);

                                if (
                                    json_last_error() === JSON_ERROR_NONE &&
                                    is_object($auditorRemarks) &&
                                    isset($auditorRemarks->content)
                                ) {
                                    $auditorContent = $auditorRemarks->content;
                                } else {
                                    $auditorContent = $auditslipremarks;
                                }

                                $auditorContent = is_string($auditorContent) ? $auditorContent : '';
                                $auditorContent = $this->cleanForMPDF($auditorContent);

                                if (trim($auditorContent) !== '') {
                                    $html .= "<div style='margin-bottom: 6px; font-size: 12pt;'>$auditorContent</div>";
                                } else {
                                    $html .= "<p style='margin-bottom: 6px;'>No Remarks Available</p>";
                                }
                            } else {
                                $html .= "<p style='margin-bottom: 6px;'>No Remarks Available</p>";
                            }

                            // ✅ Keep liability section as is, since it comes immediately after remarks
                            $Liability = $groupedByLiability[$mainslipNumber] ?? null;
                            if ($Liability) {
                                $html .= "<h4 style='text-align: center;'>Liability Details</h4>";
                                $html .= "<table border='1' cellpadding='5' cellspacing='0' width='100%' style='border-collapse: collapse;'>";
                                $html .= "<thead>
                                            <tr>
                                                <th>Liability Name</th>
                                                <th>GPF/CPS/Other Number</th>
                                                <th>Designation</th>
                                                <th>Amount Involved</th>
                                            </tr>
                                        </thead><tbody>";

                                foreach ($Liability as $LiabilityVal) {
                                    $identificationNumber = $LiabilityVal->liabilitygpfno;
                                    $prefix = match ($LiabilityVal->notype) {
                                        1 => 'GPF No: ',
                                        2 => 'CF No: ',
                                        3 => 'IHRMS No: ',
                                        default => '',
                                    };

                                    $html .= "<tr>
                                                <td>{$LiabilityVal->liabilityname}</td>
                                                <td>{$prefix}{$identificationNumber}</td>
                                                <td>{$LiabilityVal->liabilitydesignation}</td>
                                                <td>{$LiabilityVal->liabilityamount}</td>
                                            </tr>";
                                }

                                $html .= "</tbody></table>";
                            }

                            if($currentSlip > 1)
                            {
                                $html .= '</div>';
                            }

                            $currentSlip++;
                            $X++; // Next serial number
                        }


                }
            }
        }


        return [
            'html' => $html,
            //'slipPages' => $slipPageMap,
            'seriousno'=>$X
        ];
    }

    

    public function previewWordFileTest()
    {
        try {
            $phpWord = new \PhpOffice\PhpWord\PhpWord();


            $phpWord->addFontStyle('TamilStyle', [
                'name' => 'Nirmala UI', // Use a Tamil Unicode font
                'size' => 16,
                'bold' => true
            ]);
    
            // Add a new section
            $section = $phpWord->addSection();
    
            // Add Tamil text with a Unicode-compatible font
            $section->addText('???????, ??? ??? ??????????? ???????????!', 'TamilStyle');

    
            // Save the Word file
           // $fileName = 'TamilPreview.docx';
            $fileName = 'AuditReport_' . Carbon::now()->format('Y_m_d_H_i_s') . '.docx';

            $filePath = public_path('files/' . $fileName);
            $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($filePath);
    
            // Generate HTML Preview
            $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
            ob_start();
            $htmlWriter->save('php://output');
            $htmlContent = ob_get_clean();
    
            return response()->json([
                'res' => 'success',
                'html' => $htmlContent, // Send the HTML content
                'filename' => $fileName // Send the generated filename for download
            ]);
    
        } catch (\Exception $e) {
            // Log the error and return a response
            \Log::error('Error in previewWordFile: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

   public function previewWordFile()
    {    $lang = $_GET['lang'];

        $labelsJson_layout = json_decode(file_get_contents(public_path('json/layout.json')), true);
        $label_layout = $labelsJson_layout[$lang];
            ob_end_clean();
            ob_start();
       
            $scheduleId = $_GET['scheduleid'];
          
            $whichpart = $_GET['whichpart'];

            $handlestatusflag = 'Y';

            if($whichpart == 'all')
            {
                $handlestatusflag = 'F';
            }
            $chargeData = session('charge');
            $userData = session('user');
            $session_userid = $userData->userid;
       
        
            if (!$scheduleId) {
                return response()->json(['res' => 'nodata']);
            }

            $WorkingOfficeGet = FormatModel::GetSchedultedEventDetails($scheduleId);
            $auditeamid = $WorkingOfficeGet->auditteamid;
              
            // Language setup
            if ($lang == 'ta')
            {
                $DeptName = $WorkingOfficeGet->depttlname;
                $InstituteName = $WorkingOfficeGet->insttname;
                $TypeofAudit = $WorkingOfficeGet->typeofaudittname;
                $DistName = $WorkingOfficeGet->disttname . ' மாவட்டம்';
                $fontName = 'Latha';
                $defaultsize = 8;
                $AuditReport_Text = 'தணிக்கை அறிக்கை';
                $AuditReport_Year = 'ஆண்டு';
                $deptsize = '16';
                $InstNameSize = '22';

            } else {
                   
                $DeptName = $WorkingOfficeGet->deptelname;
                $InstituteName = $WorkingOfficeGet->instename;
                $TypeofAudit = $WorkingOfficeGet->typeofauditename;
                $DistName = $WorkingOfficeGet->distename . ' District';
                $fontName = 'Latha';
                $defaultsize = 13;
                $AuditReport_Text = 'Audit Report';
                $AuditReport_Year = 'Year of';
                $deptsize = '24';
                $InstNameSize = '26';
            }

            $FinancialYear = $WorkingOfficeGet->yearname;

            $deptcode = $WorkingOfficeGet->deptcode;


            if($deptcode == '01')
            {
                $auditEndDate ='30/06/2025';
    
            }else
            {        
                    // Split the years
                    $years = explode(',', $FinancialYear);

                    // Get the last year range
                    $lastYearRange = trim(end($years)); // e.g. "2023-2024"

                    // Split the range into start and end years
                    $yearParts = explode('-', $lastYearRange);

                    // Use the second part as the end year
                    $endYear = isset($yearParts[1]) ? trim($yearParts[1]) : null;

                    if ($endYear) {
                        $auditEndDate = "31/03/$endYear";
                        //echo "Audit End Date: $auditEndDate";
                    }

            }

           

            if($whichpart == 'part_a' || $whichpart == 'all')
            {

                $auditCertificate = DB::table('audit.report_auditcertificate')
                                    ->where('scheduleid', $scheduleId)
                                    ->where('statusflag',$handlestatusflag)
                                    ->first();


                
                $cer_type_code = $auditCertificate->cer_type_code;

                
                $MasterauditCertificate = DB::table('audit.mst_auditcertificatetype')
                                        ->where('cer_type_code', $cer_type_code)
                                        ->first();         

                $Master_cer_content = json_decode($MasterauditCertificate->cer_content)->content;
                $Master_cer_content = str_replace('[audityear]', $auditEndDate, $Master_cer_content);
                $certypetext =$MasterauditCertificate->cer_ename;

            }

            if($whichpart == 'all')
            {

                $imagePath = public_path('site/image/tn__logo.png');
                $imageData = base64_encode(file_get_contents($imagePath));
                $imageType = pathinfo($imagePath, PATHINFO_EXTENSION);
                $base64Image = 'data:image/' . $imageType . ';base64,' . $imageData;
            
                $htmlContent = $this->generateFirstPageHtml($deptsize,$DeptName,$base64Image,$DistName,$InstituteName,$FinancialYear,$AuditReport_Year,$AuditReport_Text);
                $mpdfContent = $htmlContent;

                $coveringletter =$this->coveringletter($scheduleId,$lang,$certypetext,$auditEndDate);
                $loadcoveringletter = '<div class="page-break"></div><div class="section-content">' . $coveringletter . '</div>';
                $mpdfContent .= $loadcoveringletter;

            }else
            {
                 // Load CSS file
                $templatePath = resource_path('views/pdf/style.css');
                $stylesheet = File::get($templatePath);

                // Inline CSS in <style> tag
                $css = "<style>$stylesheet</style>";

                $mpdfContent  =$css;
                
            }

            if($whichpart == 'part_a' || $whichpart == 'all')
            {
                $auditcertificate_remarks = json_decode($auditCertificate->cer_remarks)->content;

                $partacontents = '
                <div>
                    <h1 style="text-align:center; font-size: 36px;">PART - I</h1>
                </div>
                ';

                // Page break to move to next page
                $partacontents .= '<pagebreak />';

                // Page 2: Audit Certificate content (normal alignment)
                $partacontents .= '
                <div >
                    <h3 style="text-align:center;">AUDIT CERTIFICATE</h3><p>' . $Master_cer_content . '</p>
                </div> ';

                if($cer_type_code !== '01')
                {
                //$partacontents .='<h3 style="text-align:center;">Remarks of Audit Certificate with '.$MasterauditCertificate->cer_ename.'</h3>';
                $partacontents .=$auditcertificate_remarks;


                }


                $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $partacontents . '</div>';


            }


            $GetauditSlips = FormatModel::FetchAuditSlips($scheduleId, '01');
            $slipResultSerious = $this->AuditSlipLoadPDF($scheduleId, $GetauditSlips, $lang, '','','ser');
            //$slipPageMapSerious = $slipResultSerious['slipPages'];

            $seriousLastSlipNO =$slipResultSerious['seriousno'];

            $GetauditSlips = FormatModel::FetchAuditSlips($scheduleId, '02');
            $slipResultNonSerious = $this->AuditSlipLoadPDF($scheduleId, $GetauditSlips, $lang, '',$seriousLastSlipNO,'nonser');
            // $slipPageMapNonSerious = $slipResultNonSerious['slipPages'];


            $FetchGistObjections = FormatModel::FetchGistObjections($scheduleId);

            if($whichpart == 'all')
            {

                $GistAuditObjections = $this->GistAuditObjections($scheduleId, $FetchGistObjections, $lang);
                $mpdfContent .= ' <div class="page-break"></div><div class="section-content">' . $GistAuditObjections . '</div>';
           
            }
            
           
            if($whichpart == 'part_a' || $whichpart == 'all')
            {
                $partacontents = $this->generatePartAContent($scheduleId,$lang,$handlestatusflag);
          
                $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $partacontents . '</div>';
                
            }

           
            if($whichpart == 'part_b_seriousirregularities' || $whichpart == 'all')
            {
               

                if($slipResultSerious['html'])
                {
                    // $SeriousSectionHeading = '<div>
                    //                         <h1 style="text-align:center; font-size: 36px;">PART - II</h1>
                    //                         <h2 style="text-align:center;">Serious Irregularities</h2>
                    //                     </div>';
                                         $SeriousSectionHeading = '<div>
                                            <h1 style="text-align:center; font-size: 36px;">' . $label_layout['part_b_prefill'] . '</h1>
                                            <h2 style="text-align:center;">' . $label_layout['serious_reg'] . '</h2>
                                        </div>';
//  $SeriousSectionHeading = '<div style="height: 100vh; display: flex; justify-content: center; align-items: center;">
//                                             <h1 style="text-align:center; font-size: 36px;margin-top: 380px;">' . $label_layout['part_b_prefill'] . '</h1>
//                                             <h2 style="text-align:center;">' . $label_layout['serious_reg'] . '</h2>
//                                         </div>';

                    $mpdfContent .= '<div style="page-break-before: always;">' . $SeriousSectionHeading . '</div>';

                    $SeriousSection = $slipResultSerious['html'];

                    $mpdfContent .= '<div style="page-break-before: always;">' . $SeriousSection . '</div>';


                }

            }
               

            if($whichpart == 'part_b_nonseriousirregularities' || $whichpart == 'all')
            {

                // Now for non-serious irregularities
                if($slipResultNonSerious['html'])
                {
                    // $nonSeriousSectionHeading = '<div>
                    //                         <h1 style="text-align:center; font-size: 36px;">PART - II</h1>
                    //                         <h2 style="text-align:center;">Non Serious Irregularities</h2>
                    //                     </div>';
                                          $nonSeriousSectionHeading = '<div>
                                            <h1 style="text-align:center; font-size: 36px;">' . $label_layout['part_b_prefill'] . '</h1>
                                            <h2 style="text-align:center;">' . $label_layout['nonserious_reg'] . '</h2>
                                        </div>';
                    // $nonSeriousSectionHeading = '<div style="height: 100vh; display: flex; justify-content: center; align-items: center;">
                    //                         <h1 style="text-align:center; font-size: 36px;margin-top: 380px;">' . $label_layout['part_b_prefill'] . '</h1>
                    //                         <h2 style="text-align:center;">' . $label_layout['nonserious_reg'] . '</h2>
                    //                     </div>';
                    $mpdfContent .= '<div style="page-break-before: always;">' . $nonSeriousSectionHeading . '</div>';

                    $nonSeriousSection = $slipResultNonSerious['html'];

                    $mpdfContent .= '<div style="page-break-before: always;">' . $nonSeriousSection . '</div>';

                }

            }


            if($whichpart == 'part_b_others' || $whichpart == 'all')
            {

                $auditfees_content ='<h3 style="text-align:center;">AUDIT FEES / AUDIT LEVY CERTIFICATE</h3>';

               
                $auditfeesDetails = DB::table('audit.report_auditlevycertificate')
                                ->where('scheduleid', $scheduleId)
                                ->where('statusflag', $handlestatusflag)
                                ->first();

                $auditlevy_remarks = json_decode($auditfeesDetails->auditlevy_remarks)->content;

                $auditfees_content .=$auditlevy_remarks;
                $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $auditfees_content . '</div>';

               /* $conclusion_content ='<h3 style="text-align:center;">CONCLUSION OF AUDIT</h3>';
                $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $conclusion_content . '</div>';*/

                $pendingparacontent ='<div><h3 style="text-align:center;"><u>Pending Paras Details</u></h3>';

                $PendingParaDetails = DB::table('audit.report_pendingparadetails')
                                ->where('scheduleid', $scheduleId)
                                ->where('statusflag', $handlestatusflag)
                                ->first();

                $pendingpara_remarks = json_decode($PendingParaDetails->pendingpara_remarks)->content;

                $pendingparacontent .=$pendingpara_remarks;

             
                //$GetauditSlips = FormatModel::FetchGistObjections($scheduleId);
$GetauditSlips = FormatModel::Fetchparadetails($scheduleId);

                $currentparacontent =$this->currentparadetails($GetauditSlips,$lang);

                $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $pendingparacontent . ' '.$currentparacontent.'</div>';


            }


            if($whichpart == 'part_c' || $whichpart == 'all')
            {
                $partCHeading = '<div>
                                            <h1 style="text-align:center; font-size: 36px;">PART - III</h1>
                                        </div>';

                $mpdfContent .= '<div class="page-break"></div><div class="section-content">' .$partCHeading.'</div>';

                   

                $slipAnnexureIframes = '';

                $ParaDetails = DB::table('audit.slipfileupload as fileup')
                    ->join('audit.trans_auditslip as slip', 'slip.auditslipid', '=', 'fileup.auditslipid')
                    ->join('audit.fileuploaddetail as filedet', 'filedet.fileuploadid', '=', 'fileup.fileuploadid')
                    ->where('slip.auditscheduleid', $scheduleId)
                    //->where('slip.processcode', 'X')
                   ->whereIn('slip.processcode',['X'])

                    ->select('slip.auditslipid','slip.irregularitiescode','slip.slipdetails','fileup.fileuploadid','filedet.filepath','filedet.filename')
                    ->get();


                $seriousCount = 0;

                $serSlipRow = DB::table('audit.report_storesliporder')
                    ->where('auditscheduleid', $scheduleId)
                    ->select('ser_ordered_slips')
                    ->first();

                if ($serSlipRow && $serSlipRow->ser_ordered_slips) {
                    $seriousArray = json_decode($serSlipRow->ser_ordered_slips, true);
                    if (is_array($seriousArray)) {
                        $seriousCount = count($seriousArray);
                    }
                }


               
                if (count($ParaDetails) > 0)
                {
                     $partccontents ='<p ><b>1)List of Annexures</b></p>';

                     $partccontents .= '<table style="width: 100%; border: none; border-collapse: collapse;">
                                    <tr>
                                        <th style="width:5%; font-weight: bold;text-align:center;">Annexure No</th>
                                        <th style="width:40%; font-weight: bold;text-align:center;">Subject</th>
                                        <th style="width:40%; font-weight: bold;text-align:center;">Para No</th>
                                        <th style="width:5%; font-weight: bold;text-align:center;">Attachment</th>
                                    </tr>';

                    $groupedParas = [];

                    $slipAnnextureFiles = [];

                    foreach ($ParaDetails as $para) {
                        $slipId = $para->auditslipid;
                        $code = $para->irregularitiescode;

                        // Calculate para number and setup structure
                        if (!isset($groupedParas[$slipId])) {
                            $paraNo = '-';
                            $slipOrderColumn = ($code === '01') ? 'ser_ordered_slips' : 'nonser_ordered_slips';

                            $slipOrderRow = DB::table('audit.report_storesliporder')
                                ->where('auditscheduleid', $scheduleId)
                                ->select($slipOrderColumn)
                                ->first();

                            if ($slipOrderRow && $slipOrderRow->$slipOrderColumn) {
                                $orderedArray = json_decode($slipOrderRow->$slipOrderColumn, true);
                                if (is_array($orderedArray)) {
                                    foreach ($orderedArray as $key => $id) {
                                        if ((int)$id === (int)$slipId) {
                                            $paraNo = ($code === '01') ? (int)$key : $seriousCount + (int)$key;
                                            break;
                                        }
                                    }
                                }
                            }

                            $groupedParas[$slipId] = [
                                'slipdetails' => $para->slipdetails,
                                'parano' => str_pad($paraNo, 4, '0', STR_PAD_LEFT),
                                'attachments' => []
                            ];
                        }

                        // Add attachments
                        if (!empty($para->filename)) {
                            $files = explode(',', $para->filename);
                            $filepaths = explode(',', $para->filepath);

                            foreach ($files as $key => $file) {
                                $file = trim($file);
                                $filepath = isset($filepaths[$key]) ? trim($filepaths[$key]) : '';

                                $groupedParas[$slipId]['attachments'][] = [
                                    'filename' => $file,
                                    'filepath' => $filepath
                                ];
                               $paraNoLabel = 'Annexure of Para No: ' . str_pad($groupedParas[$slipId]['parano'], 4, '0', STR_PAD_LEFT);

                                // ✅ Collect for processing later
                                $slipAnnextureFiles[] = (object)[
                                    'filename' => $file,
                                    'filepath' => $filepath,
                                    'annexture_type' => 'slip_related'
                                ];
                            }
                        }
                    }

                    $serial = 1;

                    if (!empty($groupedParas)) {
                        foreach ($groupedParas as $slipId => $data) {
                            $attachmentLinks = '-';
                            if (!empty($data['attachments'])) {
                                $attachmentLinks = '';
                                foreach ($data['attachments'] as $i => $att) {
                                    $attachmentLinks .= ($i + 1) . ') ' . basename($att['filename']) . '<br>';

                                    // Optional: generate iframes
                                    $filePath = storage_path('app/public/' . $att['filepath']);
                                    if (File::exists($filePath)) {
                                        $publicPath = str_replace(storage_path('app/public/'), '', $filePath);
                                        $url = asset('/' . $publicPath);
                                        $slipAnnexureIframes .='<p ><b>Annexture of Para No :'.$data['parano'].'</b></p>';

                                        $slipAnnexureIframes .= "
                                            <div style='margin-top: 40px; page-break-before: always;'>
                                                <iframe
                                                    src='{$url}#toolbar=0&navpanes=0&scrollbar=0'
                                                    width='100%'
                                                    height='800px'
                                                    style='border:1px solid #ccc; overflow:hidden;'></iframe>
                                            </div>
                                        ";
                                    }
                                }
                            }

                            $partccontents .= '<tr>
                                <td style="width:5%; text-align:center;">' . str_pad($serial++, 4, '0', STR_PAD_LEFT) . '</td>
                                <td style="width:40%; text-align:center;">' . ($data['slipdetails'] ?? '-') . '</td>
                                <td style="width:5%; text-align:center;">' . $data['parano'] . '</td>
                                <td style="width:40%; text-align:left;">' . $attachmentLinks . '</td>
                            </tr>';
                        }
                    } else {
                        $partccontents .= '<tr>
                            <td colspan="4" style="text-align:center;">No annexure found</td>
                        </tr>';
                    }


                   
                }else {
                      
                     $partccontents ='<p ><b>1)List of Annexures : Nil</b></p>';

                }

                $partccontents .= '</table>';

                 $annexturefiles = DB::table('audit.report_annextures as ann')
                                    ->join('audit.fileuploaddetail as fup', 'fup.fileuploadid', '=', 'ann.fileupload_id')
                                    ->select('fup.filepath','fup.filename', 'ann.annexture_type')
                                    ->where('ann.auditscheduleid',$scheduleId)
                                    ->where('ann.statusflag', '!=', 'N')
                                    ->orderby('ann.annexture_id','asc')
                                    ->get();
                $response = [];
                foreach ($annexturefiles as $file) {
                    $response[$file->annexture_type] = [
                        'filename' => $file->filename,
                        'filepath' => $file->filepath,
                        'annexture_type' => $file->annexture_type,
                        'subject' => $file->subject ?? '', // fallback if 'subject' is missing
                    ];
                }


                $annexureLabels = DB::table('audit.mst_accountparticulars_details')
                        ->where('statusflag', 'Y')
                        ->pluck('accpar_ename', 'accpar_key') // value, key
                        ->toArray();



                $partccontents .= '<p><b>2) List of Accounts and Statements</b></p>';
                $partccontents .= '<table width="100%" border="1" cellspacing="0" cellpadding="5">
                                    <tr>
                                        <th width="20%" align="center">Annexure No</th>
                                        <th width="80%" align="center">Subject</th>
                                    </tr>';
                $annexureNo = 1;
                foreach ($response as $type => $data) {
                    $subject = htmlspecialchars($annexureLabels[$data['annexture_type']] ?? 'Unknown');
                    $partccontents .= '<tr>
                        <td align="center">' . $annexureNo++ . '</td>
                        <td>' . $subject . '</td>
                    </tr>';
                }
                $partccontents .= '</table>';

                $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $partccontents . '</div>';

                $listofannextures ='';
                if (count($ParaDetails) > 0)
                {
                    $listofannextures .= '<div>
                                                <h3 style="text-align:center;">List of Annexures</h3>
                                            </div>'.$slipAnnexureIframes;

                }

                $mpdfContent =  $mpdfContent.''.$listofannextures;

           
                    $annexurePaths =[];
                    $GetAnnextureFiles = FormatModel::FetchAnnextures($scheduleId);
                    $annexureIframes ='';
                    if($GetAnnextureFiles['pdfFiles'])
                    {
                            foreach ($GetAnnextureFiles['pdfFiles'] ?? [] as $pdfFile) {
                                $filePath = storage_path('app/public/' . $pdfFile->filepath);
                                if (File::exists($filePath)) {
                                    $annexurePaths[] = [
                                        'path' => $filePath,
                                        'type' => $pdfFile->annexture_type ?? 'Annexure'
                                    ];
                                }
                            }

                            $pdffiles = $annexurePaths;


                            foreach ($pdffiles as $i => $annexure)
                            {
                                $path = $annexure['path'];
                                $type = $annexure['type'];
                                // Convert full storage path to public asset path
                                $publicPath = str_replace(storage_path('app/public/'), '', $path);
                                $url = asset('/' . $publicPath); // assuming you're serving from storage:link
                                $annexureTitle = $annexureLabels[$type];
                                $annexureIframes .= "
                                                            <div style='margin-top: 40px;'>
                                                                <h4 style='font-weight:bold; color:#2c3e50;text-align:center;font-size:16px;'>{$annexureTitle}</h4>
                                                                <iframe
                                                                    src='{$url}#toolbar=0&navpanes=0&scrollbar=0'
                                                                    width='100%'
                                                                    height='800px'
                                                                    style='border:1px solid #ccc; overflow:hidden;'></iframe>
                                                            </div>
                                                        ";
                            }


                            // Combine the main content and annexure previews
                            $mpdfContent = $mpdfContent . $annexureIframes;

                        }
        

                        
                    if (!empty($GetAnnextureFiles['xlsxFiles'])) {
                        foreach ($GetAnnextureFiles['xlsxFiles'] as $xlsxFile) {
                        // print_r($xlsxFile);
                            $excelPath = storage_path('app/public/' . $xlsxFile->filepath);

                            if (file_exists($excelPath)) {
                                try {
                                    $spreadsheet = IOFactory::load($excelPath);
                                    $sheets = $spreadsheet->getAllSheets(); // ✅ get all sheets
                                    $fileTitle = $annexureLabels[$xlsxFile->annexture_type] ?? $xlsxFile->annexture_type ?? 'Excel Annexure';
                                // $fileTitle ='';
                                    $excelHtml = "<div style='page-break-before: always; margin-top:20px; font-family:latha;'>"; // 👈 for Tamil support
                                    $excelHtml .= "<h3 style='font-weight:bold; text-align:center; color:#2c3e50;'>{$fileTitle}</h3>";
                                    
                                    foreach ($sheets as $sheetIndex => $sheet) {
                                       $data = $sheet->toArray(false);
					//$data = $sheet->toArray(null, false, false, true);
                                        $sheetTitle = $sheet->getTitle();

                                    $excelHtml .= "<h4 style='font-weight:bold; text-align:center; color:#2c3e50;'>{$sheetTitle}</h4>";
                                        $excelHtml .= "<table border='1' cellpadding='5' cellspacing='0' width='100%' style='border-collapse:collapse; font-size:12px;'>";

                                        foreach ($data as $row) {
                                            $excelHtml .= '<tr>';
                                            foreach ($row as $cell) {
                                                $excelHtml .= '<td style=\"font-family:latha;\">' . htmlspecialchars($cell) . '</td>'; // 👈 force font here too
                                            }
                                            $excelHtml .= '</tr>';
                                        }

                                        $excelHtml .= '</table>';

                                        // Append each sheet's HTML to the final preview content
                                    }

                                    $excelHtml .= '</div>';
                                    $mpdfContent .= $excelHtml;



                                } catch (\Exception $e) {
                                    $mpdfContent .= "<p style='color:red;'>Failed to load Excel file: {$xlsxFile['filename']}</p>";
                                }
                            } else {
                                $mpdfContent .= "<p style='color:red;'>Excel file not found: {$xlsxFile['filename']}</p>";
                            }
                        }
                    }


                


                }

                // Return
                return response()->json([
                    'res' => 'success',
                    'html' => $mpdfContent
                ]);


             

                       

       
    }

    public function DownloadAuditReport($scheduleId,$lang)
    {  
          $labelsJson_layout = json_decode(file_get_contents(public_path('json/layout.json')), true);
            $label_layout = $labelsJson_layout[$lang];

            ob_end_clean();
            ob_start();
                
            $chargeData = session('charge');
            $userData = session('user');
            $session_userid = $userData->userid;
        
            $workAllocation = FormatModel::fetch_allocatedwork($scheduleId);
            $WorkingOfficeGet = FormatModel::GetSchedultedEventDetails($scheduleId);

           
            $auditeamid = $WorkingOfficeGet->auditteamid;
        
            if (!$scheduleId) {
                return response()->json(['res' => 'nodata']);
            }
        
            $TeammemberGet = FormatModel::getTeamMembers($auditeamid);
        
            // Language setup
            if ($lang == 'ta') 
            {
                $DeptName = $WorkingOfficeGet->depttlname;
                $InstituteName = $WorkingOfficeGet->insttname;
                $TypeofAudit = $WorkingOfficeGet->typeofaudittname;
                $DistName = $WorkingOfficeGet->disttname . ' மாவட்டம்';
                $fontName = 'Latha';
                $defaultsize = 8;
                $AuditReport_Text = 'தணிக்கை அறிக்கை';
                $AuditReport_Year = 'ஆண்டு';
                $deptsize = '16';
                $InstNameSize = '22';

            } else {
                    
                $DeptName = $WorkingOfficeGet->deptelname;
                $InstituteName = $WorkingOfficeGet->instename;
                $TypeofAudit = $WorkingOfficeGet->typeofauditename;
                $DistName = $WorkingOfficeGet->distename . ' District';
                $fontName = 'Latha';
                $defaultsize = 13;
                $AuditReport_Text = 'Audit Report';
                $AuditReport_Year = 'Year of';
                $deptsize = '24';
                $InstNameSize = '26';
            }

            //print_r($WorkingOfficeGet);
           $FinancialYear = $WorkingOfficeGet->yearname;


           
           $deptcode = $WorkingOfficeGet->deptcode;


           if($deptcode == '01')
           {
              $auditEndDate ='30/06/2025';
 
           }else
           {         
                // Split the years
                $years = explode(',', $FinancialYear);

                // Get the last year range
                $lastYearRange = trim(end($years)); // e.g. "2023-2024"

                // Split the range into start and end years
                $yearParts = explode('-', $lastYearRange);

                // Use the second part as the end year
                $endYear = isset($yearParts[1]) ? trim($yearParts[1]) : null;

                if ($endYear) {
                    $auditEndDate = "31/03/$endYear";
                    //echo "Audit End Date: $auditEndDate";
                } 

           }


           /* if($WorkingOfficeGet->deptcode == '01')
            {
                $FinancialYear = $WorkingOfficeGet->annadhanamyearname;

                
            }*/
        

            // mPDF instance
            $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8',
                                    'fontDir' => array_merge(
                                        (new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'],
                                        [public_path('fonts/tamil')]
                                    ),
                                    'fontdata' => (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'] + [
                                        'noto1' => [
                                            'R' => 'Latha.ttf',
                                            'useOTL' => 0xFF,
                                            //'useKashida' => 75,
                                        ],
                                        'noto' => [
                                            'R' => 'times.ttf',
                                            'useOTL' => 0xFF,
                                        // 'useKashida' => 75,
                                        ],
                                        'times' => [
                                            'R' => 'times.ttf',
                                            'useOTL' => 0xFF,
                                            //'useKashida' => 75,
                                        ],
                                        'arial' => [
                                            'R' => 'arial.ttf',
                                            'useOTL' => 0xFF,
                                        // 'useKashida' => 75,
                                        ],
                                    ],
                                    'default_font' => 'noto1',
                                    'format' => 'A4',
                                    'orientation' => 'P',
                                    'margin_top' => 13,    // Top margin
                                    'margin_bottom' => 13, // Bottom margin
                                    'margin_left' => 10,   // Left margin
                                    'margin_right' => 10   // Right margin
                                ]);
                            
            // Border size: inside the margins (15mm all sides)
            // Use a div in header that covers whole page with border
            $borderHtml = '<div style="
                                position: fixed;
                                top: -10;
                                left: -20;
                                right: -20;
                                bottom: 20;
                                width: 100%;
                                height: 100%;
                                border: 2px solid black;
                                box-sizing: border-box;
                                padding:20px;
                                padding-bottom:none;
                                text-align: justify; 
                            "></div>';
                                
            // Set the same border as header and footer for all pages
            $mpdf->SetHTMLHeader($borderHtml);
            $mpdf->SetHTMLFooter($borderHtml);
 

            $imagePath = public_path('site/image/tn__logo.png');
            $imageData = base64_encode(file_get_contents($imagePath));
            $imageType = pathinfo($imagePath, PATHINFO_EXTENSION);
            $base64Image = 'data:image/' . $imageType . ';base64,' . $imageData; 
            
            $htmlContent = $this->generateFirstPageHtml($deptsize,$DeptName,$base64Image,$DistName,$InstituteName,$FinancialYear,$AuditReport_Year,$AuditReport_Text);
            // print_r($htmlContent);
            // exit;
            $mpdfContent = $htmlContent;

             $auditCertificate = DB::table('audit.report_auditcertificate')
                                ->where('scheduleid', $scheduleId)
                                ->where('statusflag', 'F')
                                ->first();

            $auditcertificate_remarks = json_decode($auditCertificate->cer_remarks)->content;

           

            $cer_type_code = $auditCertificate->cer_type_code;


            
            $MasterauditCertificate = DB::table('audit.mst_auditcertificatetype')
                                ->where('cer_type_code', $cer_type_code)
                                ->first();
              

            $Master_cer_content = json_decode($MasterauditCertificate->cer_content)->content;

         
               
            $Master_cer_content = str_replace('[audityear]', $auditEndDate, $Master_cer_content);


           //$certypetext =$MasterauditCertificate->cer_ename;
            if($lang === 'en'){
                $certypetext =$MasterauditCertificate->cer_ename;
            }else{
                $certypetext =$MasterauditCertificate->cer_tname;
            }


            $coveringletter =$this->coveringletter($scheduleId,$lang,$certypetext,$auditEndDate);

         // print_r($coveringletter);
// print_r($coveringletter);
//            exit;
            $loadcoveringletter = '<div class="page-break"></div><div class="section-content">' . $coveringletter . '</div>';


            $mpdfContent .= $loadcoveringletter;

            // Add other sections
            //$mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $this->loadintimationletter($scheduleId, $lang) . '</div>';
            //$mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $this->loadentrymeeting($scheduleId, $lang, $fontName) . '</div>';

            /*$overview_content = $this->OverviewContentLoad($scheduleId);
            $mpdfContent .= ' <div class="page-break"></div><div class="section-content">' . $overview_content . '</div>';*/

           
          
            //$partacontents = $this->GeneratePartAContentsLoad($scheduleId,$lang);

           // $mpdfContent .= $partacontents;

          
          if ($lang === 'en') {
                    $part1 = 'PART - I';
                    $certifiacte = 'AUDIT CERTIFICATE';
                } else {
                    $part1 = 'பகுதி - I';
                    $certifiacte = 'தணிக்கைச் சான்றிதழ்';
                }
                    
            // Page 1: PART - I centered in the middle
            $partacontents = '
            <div style="height: 100vh; display: flex; justify-content: center; align-items: center;">
                <h1 style="text-align:center; font-size: 36px;margin-top: 380px;">'.$part1.'</h1>
            </div>
            ';

            // Page break to move to next page
            $partacontents .= '<pagebreak />';

            // Page 2: Audit Certificate content (normal alignment)
            $partacontents .= '
            <div >
                <h3 style="text-align:center;">'.$certifiacte.'</h3><p>' . $Master_cer_content . '</p>
            </div> ';

            if($cer_type_code !== '01')
            {
              //$partacontents .='<h3 style="text-align:center;">Remarks of Audit Certificate with '.$MasterauditCertificate->cer_ename.'</h3>';
               $partacontents .=$auditcertificate_remarks;


            }


            $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $partacontents . '</div>';

            $GetauditSlips = FormatModel::FetchAuditSlips($scheduleId, '01');
            $slipResultSerious = $this->AuditSlipLoadPDF($scheduleId, $GetauditSlips, $lang, $mpdf,'','ser');
            //$slipPageMapSerious = $slipResultSerious['slipPages'];

            $seriousLastSlipNO =$slipResultSerious['seriousno'];

            $GetauditSlips = FormatModel::FetchAuditSlips($scheduleId, '02');
            $slipResultNonSerious = $this->AuditSlipLoadPDF($scheduleId, $GetauditSlips, $lang, $mpdf,$seriousLastSlipNO,'nonser');
            //$slipPageMapNonSerious = $slipResultNonSerious['slipPages'];

            $mpdf->TOC_Entry("Chapter 1",0);

            $FetchGistObjections = FormatModel::FetchGistObjections($scheduleId);
            $GistAuditObjections = $this->GistAuditObjections($scheduleId, $FetchGistObjections, $lang);
            $mpdfContent .= ' <div class="page-break"></div><div class="section-content">' . $GistAuditObjections . '</div>';
            

           $partacontents = $this->generatePartAContent($scheduleId,$lang,'F');

                    
            $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $partacontents . '</div>';

                /* foreach ($TeammemberGet as $Teammembers) {
                        $Name = $lang == 'ta' ? $Teammembers->usertamilname : $Teammembers->username;
                        $Desig = $lang == 'ta' ? $Teammembers->desigtlname : $Teammembers->desigelname;
                        $codeOfEthics = $this->load_codeofethicscontents($scheduleId, $lang, true, $Name, $Desig);
                        $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $codeOfEthics . '</div>';
                    }

                    $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $this->workallocationpdf($scheduleId, $lang) . '</div>';
                    $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $this->loadexitmeeting($scheduleId, $lang, $fontName) . '</div>';*/

               

                if($slipResultSerious['html'])
                {
                    $SeriousSectionHeading = '<div style="height: 100vh; display: flex; justify-content: center; align-items: center;">
                                            <h1 style="text-align:center; font-size: 36px;margin-top: 380px;">' . $label_layout['part_b_prefill'] . '</h1>
                                            <h2 style="text-align:center;">' . $label_layout['serious_reg'] . '</h2>
                                        </div>';

                    $mpdfContent .= '<div style="page-break-before: always;">' . $SeriousSectionHeading . '</div>';

                    $SeriousSection = $slipResultSerious['html'];

                    $mpdfContent .= '<div style="page-break-before: always;">' . $SeriousSection . '</div>';


                }
                


                // Now for non-serious irregularities
                if($slipResultNonSerious['html'])
                {
                    $nonSeriousSectionHeading = '<div style="height: 100vh; display: flex; justify-content: center; align-items: center;">
                                            <h1 style="text-align:center; font-size: 36px;margin-top: 380px;">' . $label_layout['part_b_prefill'] . '</h1>
                                            <h2 style="text-align:center;">' . $label_layout['nonserious_reg'] . '</h2>
                                        </div>';
                    $mpdfContent .= '<div style="page-break-before: always;">' . $nonSeriousSectionHeading . '</div>';

                    $nonSeriousSection = $slipResultNonSerious['html'];

                    $mpdfContent .= '<div style="page-break-before: always;">' . $nonSeriousSection . '</div>';

                }

                //$slipPageMap = array_merge($slipPageMapSerious, $slipPageMapNonSerious);


                //print_r($slipPageMapSerious);




                $auditfees_content ='<h3 style="text-align:center;">' . $label_layout['auditefees'] . '</h3>';

                
                $auditfeesDetails = DB::table('audit.report_auditlevycertificate')
                                ->where('scheduleid', $scheduleId)
                                ->where('statusflag', 'F')
                                ->first();

                $auditlevy_remarks = json_decode($auditfeesDetails->auditlevy_remarks)->content;

                $auditfees_content .=$auditlevy_remarks;
                $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $auditfees_content . '</div>';

               /* $conclusion_content ='<h3 style="text-align:center;">CONCLUSION OF AUDIT</h3>';
                $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $conclusion_content . '</div>';*/

                $pendingparacontent ='<div><h3 style="text-align:center;"><u>' . $label_layout['pendingpara'] . '</u></h3>';

                $PendingParaDetails = DB::table('audit.report_pendingparadetails')
                                ->where('scheduleid', $scheduleId)
                                ->where('statusflag', 'F')
                                ->first();

                $pendingpara_remarks = json_decode($PendingParaDetails->pendingpara_remarks)->content;

                $pendingparacontent .=$pendingpara_remarks;

             
              //  $GetauditSlips = FormatModel::FetchGistObjections($scheduleId);

		$GetauditSlips = FormatModel::Fetchparadetails($scheduleId);


                $currentparacontent =$this->currentparadetails($GetauditSlips,$lang);

                $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $pendingparacontent . ' '.$currentparacontent.'</div>';


                $partCHeading = '<div style="height: 100vh; display: flex; justify-content: center; align-items: center;">
                                            <h1 style="text-align:center; font-size: 36px;margin-top: 380px;">' . $label_layout['part_c_prefill'] . '</h1>
                                        </div>';

                $mpdfContent .= '<div class="page-break"></div><div class="section-content">' .$partCHeading.'</div>';

                         // Initialize the annexure page map

               /* $mpdfContentFinal = self::applyFontByLanguage($mpdfContent);

                $footerHTML = '
                        <sethtmlpagefooter name="myFooter" value="on" />
                        <htmlpagefooter name="myFooter">
                            <div style="text-align:center; font-size: 15pt; font-family:Times New Roman;">
                                {PAGENO}
                            </div>
                        </htmlpagefooter>';

                $mpdfContentFinal = $footerHTML . $mpdfContentFinal;

                $mpdf->WriteHTML('<div style="padding: 20px; text-align: justify;">' . $mpdfContentFinal . '</div>');*/


                /*$ParaDetails = DB::table('audit.report_paradetails as para')
                                ->join('audit.trans_auditslip as slip', 'slip.auditslipid', '=', 'para.slip_id')
                                ->where('para.auditscheduleid', $scheduleId)
                                ->whereNotNull('para.slip_attachments') // ensure not null\
                                ->select('para.para_id', 'para.slip_id','para.orderid', 'slip.slipdetails', 'para.slip_attachments')
                                ->orderBy('para.orderid', 'asc')
                                ->get();

                foreach ($ParaDetails as $para) {
                                
                    $fileIds = json_decode($para->slip_attachments ?? '[]', true); // decode attachment IDs

                    $files = DB::table('audit.fileuploaddetail')
                                ->whereIn('fileuploadid', $fileIds)
                                ->pluck('filepath') // get just the names
                                ->toArray();
                                
                    $filesNames = DB::table('audit.fileuploaddetail')
                                    ->whereIn('fileuploadid', $fileIds)
                                    ->pluck('filename') // get just the names
                                    ->toArray();

                    $para->filepaths = implode(', ', $files); // attach comma-separated filenames
                    $para->filenames = implode(', ', $filesNames);
                }*/
                $partccontents ='<p ><b>' . $label_layout['list_annexure'] . '</b></p>';

                $slipAnnexureIframes = '';

                $ParaDetails = DB::table('audit.slipfileupload as fileup')
                    ->join('audit.trans_auditslip as slip', 'slip.auditslipid', '=', 'fileup.auditslipid')
                    ->join('audit.fileuploaddetail as filedet', 'filedet.fileuploadid', '=', 'fileup.fileuploadid')
                    ->where('slip.auditscheduleid', $scheduleId)
                    //->where('slip.processcode', 'X')
                   ->whereIn('slip.processcode',['X'])

                    ->select('slip.auditslipid','slip.irregularitiescode','slip.slipdetails','fileup.fileuploadid','filedet.filepath','filedet.filename')
                    ->get();


                $seriousCount = 0;

                $serSlipRow = DB::table('audit.report_storesliporder')
                    ->where('auditscheduleid', $scheduleId)
                    ->select('ser_ordered_slips')
                    ->first();

                if ($serSlipRow && $serSlipRow->ser_ordered_slips) {
                    $seriousArray = json_decode($serSlipRow->ser_ordered_slips, true);
                    if (is_array($seriousArray)) {
                        $seriousCount = count($seriousArray);
                    }
                }


                $partccontents .= '<table style="width: 100%; border: none; border-collapse: collapse;">
                                    <tr>
                                        <th style="width:5%; font-weight: bold;text-align:center;">' . $label_layout['annexureno'] . '</th>
                                        <th style="width:40%; font-weight: bold;text-align:center;">' . $label_layout['subject'] . '</th>
                                        <th style="width:40%; font-weight: bold;text-align:center;">' . $label_layout['parano'] . '</th>
                                        <th style="width:5%; font-weight: bold;text-align:center;">' . $label_layout['attachments'] . '</th>
                                    </tr>';
                if (count($ParaDetails) > 0) 
                {
                    $groupedParas = [];

                    $slipAnnextureFiles = [];

                    foreach ($ParaDetails as $para) {
                        $slipId = $para->auditslipid;
                        $code = $para->irregularitiescode;

                        // Calculate para number and setup structure
                        if (!isset($groupedParas[$slipId])) {
                            $paraNo = '-';
                            $slipOrderColumn = ($code === '01') ? 'ser_ordered_slips' : 'nonser_ordered_slips';

                            $slipOrderRow = DB::table('audit.report_storesliporder')
                                ->where('auditscheduleid', $scheduleId)
                                ->select($slipOrderColumn)
                                ->first();

                            if ($slipOrderRow && $slipOrderRow->$slipOrderColumn) {
                                $orderedArray = json_decode($slipOrderRow->$slipOrderColumn, true);
                                if (is_array($orderedArray)) {
                                    foreach ($orderedArray as $key => $id) {
                                        if ((int)$id === (int)$slipId) {
                                            $paraNo = ($code === '01') ? (int)$key : $seriousCount + (int)$key;
                                            break;
                                        }
                                    }
                                }
                            }

                            $groupedParas[$slipId] = [
                                'slipdetails' => $para->slipdetails,
                                'parano' => str_pad($paraNo, 4, '0', STR_PAD_LEFT),
                                'attachments' => []
                            ];
                        }

                        // Add attachments
                        if (!empty($para->filename)) {
                            $files = explode(',', $para->filename);
                            $filepaths = explode(',', $para->filepath);

                            foreach ($files as $key => $file) {
                                $file = trim($file);
                                $filepath = isset($filepaths[$key]) ? trim($filepaths[$key]) : '';

                                $groupedParas[$slipId]['attachments'][] = [
                                    'filename' => $file,
                                    'filepath' => $filepath
                                ];
                               $paraNoLabel = 'Annexure of Para No: ' . str_pad($groupedParas[$slipId]['parano'], 4, '0', STR_PAD_LEFT);

                                // ✅ Collect for processing later
                                $slipAnnextureFiles[] = (object)[
                                    'filename' => $file,
                                    'filepath' => $filepath,
                                    'annexture_type' => 'slip_related'
                                ];
                            }
                        }
                    }

             


              

              


            



                    $serial = 1;

                    if (!empty($groupedParas)) {
                        foreach ($groupedParas as $slipId => $data) {
                            $attachmentLinks = '-';
                            if (!empty($data['attachments'])) {
                                $attachmentLinks = '';
                                foreach ($data['attachments'] as $i => $att) {
                                    $attachmentLinks .= ($i + 1) . ') ' . basename($att['filename']) . '<br>';

                                    // Optional: generate iframes
                                    $filePath = storage_path('app/public/' . $att['filepath']);
                                    if (File::exists($filePath)) {
                                        $publicPath = str_replace(storage_path('app/public/'), '', $filePath);
                                        $url = asset($publicPath);

                                        $slipAnnexureIframes .= "
                                            <div style='margin-top: 40px; page-break-before: always;'>
                                                <iframe 
                                                    src='{$url}#toolbar=0&navpanes=0&scrollbar=0' 
                                                    width='100%' 
                                                    height='800px' 
                                                    style='border:1px solid #ccc; overflow:hidden;'></iframe>
                                            </div>
                                        ";
                                    }
                                }
                            }

                            $partccontents .= '<tr>
                                <td style="width:5%; text-align:center;">' . str_pad($serial++, 4, '0', STR_PAD_LEFT) . '</td>
                                <td style="width:40%; text-align:center;">' . ($data['slipdetails'] ?? '-') . '</td>
                                <td style="width:5%; text-align:center;">' . $data['parano'] . '</td>
                                <td style="width:40%; text-align:left;">' . $attachmentLinks . '</td>
                            </tr>';
                        }
                    } else {
                        $partccontents .= '<tr>
                            <td colspan="4" style="text-align:center;">' . $label_layout['noannexure'] . '</td>
                        </tr>';
                    }


                   
                }else {
                        // Show fallback message when no annexures exist
                        $partccontents .= '<tr>
                                        <td colspan="4" style="text-align:center;">' . $label_layout['noannexure'] . '</td>
                                    </tr>';
                }

                $partccontents .= '</table>';

                 $annexturefiles = DB::table('audit.report_annextures as ann')
                                    ->join('audit.fileuploaddetail as fup', 'fup.fileuploadid', '=', 'ann.fileupload_id')
                                    ->select('fup.filepath','fup.filename', 'ann.annexture_type')
                                    ->where('ann.auditscheduleid',$scheduleId)
                                    ->where('ann.statusflag', '!=', 'N')
                                    ->orderby('ann.annexture_id','asc')
                                    ->get();

                // print_r($annexturefiles);
                // exit;
                $response = [];
                foreach ($annexturefiles as $file) {
                    $response[$file->annexture_type] = [
                        'filename' => $file->filename,
                        'filepath' => $file->filepath,
                        'annexture_type' => $file->annexture_type,
                        'subject' => $file->subject ?? '', // fallback if 'subject' is missing
                    ];
                }


                // $annexureLabels = DB::table('audit.mst_accountparticulars_details')
                //         ->where('statusflag', 'Y')
                //         ->pluck('accpar_ename','accpar_tname', 'accpar_key') // value, key
                //         ->toArray();
                $annexureRaw = DB::table('audit.mst_accountparticulars_details')
                        ->where('statusflag', 'Y')
                        ->select('accpar_key', 'accpar_ename', 'accpar_tname')
                        ->get();

                    $annexureLabels = [];

                    foreach ($annexureRaw as $row) {
                        $annexureLabels[$row->accpar_key] = $lang === 'ta' ? $row->accpar_ename : $row->accpar_ename;
                    }



                $partccontents .= '<p><b>' . $label_layout['list_acc'] . '</b></p>';
                $partccontents .= '<table width="100%" border="1" cellspacing="0" cellpadding="5">
                                    <tr>
                                        <th width="20%" align="center">' . $label_layout['annexureno'] . '</th>
                                        <th width="80%" align="center">' . $label_layout['subject'] . '</th>
                                    </tr>';
                $annexureNo = 1;
               foreach ($response as $type => $data) {
                        $subject = htmlspecialchars($annexureLabels[$data['annexture_type']] ?? 'Unknown');
                        
                        $partccontents .= '<tr>
                            <td align="center">' . $annexureNo++ . '</td>
                            <td>' . $subject . '</td>
                        </tr>';
                    }

                $partccontents .= '</table>';

                $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $partccontents . '</div>';


               // $partccontents .=  $slipAnnexureIframes;

                $Listofannexture = '';

if (count($ParaDetails) > 0) {
$Listofannexture = '<div style="height: 100vh; display: flex; justify-content: center; align-items: center;">
    <h1 style="text-align:center; font-size: 36px;margin-top: 380px;">' . $label_layout['listofannexture'] . '</h1>
</div>';

$mpdfContentPdfContent = $MdfPreviewContent = '';

$mpdfContentPdfContent .= $mpdfContent . '<div class="page-break"></div>
<div class="section-content"> ' . $Listofannexture . '</div>';


$MdfPreviewContent .= $mpdfContent . '<div>
    <h3 style="text-align:center;">' . $label_layout['listofannexture'] . '</h3>
</div>' . $slipAnnexureIframes;
$mpdfContentFinal = self::applyFontByLanguage($mpdfContentPdfContent);
} else {
$mpdfContentPdfContent = $MdfPreviewContent = '';
$mpdfContentPdfContent .= $mpdfContent . '';
$mpdfContentFinal = self::applyFontByLanguage($mpdfContentPdfContent);
}

                $footerHTML = '
                        <sethtmlpagefooter name="myFooter" value="on" />
                        <htmlpagefooter name="myFooter">
                            <div style="text-align:center; font-size: 15pt; font-family:Times New Roman;">
                                {PAGENO}
                            </div>
                        </htmlpagefooter>';

                  

                $mpdfContentFinal = $footerHTML . $mpdfContentFinal;
                @$mpdf->WriteHTML('<div style="padding: 20px; text-align: justify;">' . $mpdfContentFinal . '</div>');
                //   print_r($footerHTML);
                //     exit;

                $tempMainPdfPath = tempnam(sys_get_temp_dir(), 'mainpdf_') . '.pdf';
                @$mpdf->Output($tempMainPdfPath, \Mpdf\Output\Destination::FILE);

                // DEBUG: Ensure temp file exists
                if (!file_exists($tempMainPdfPath) || filesize($tempMainPdfPath) < 1000) 
                {
                    
                    throw new \Exception("Invalid mPDF output: $tempMainPdfPath");
                }

                // FPDI (with TCPDF or FPDF base)
                // use setasign\Fpdi\Tcpdf\Fpdi; // If using TCPDF
                $pdf = new Fpdi();

                $pageCount = $pdf->setSourceFile($tempMainPdfPath);
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) 
                {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                }

                // Annexures
                if (!empty($slipAnnextureFiles)) 
                {
                    $slipAnnexurePaths = $this->getConvertedAnnexurePaths($slipAnnextureFiles, $paraNoLabel);
                    $this->loadAnnexuresToFpdi($pdf, $slipAnnexurePaths, $paraNoLabel,'slipbased',$lang);
                }

               
            $GetAnnextureFiles = FormatModel::FetchAnnextures($scheduleId);
            $annexureIframes ='';
        

           
            $converted = [];

            foreach ($GetAnnextureFiles['pdfFiles'] as $file) {
                $converted[] = [
                    'path' => storage_path('app/public/' . str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $file->filepath)),
                    'type' => $annexureLabels[$file->annexture_type] ?? 'xlsx',
                    'title' => $annexureLabels[$file->annexture_type] ?? 'annexure',
                ];
            }

            // If you want to handle xlsxFiles the same way (skip if not needed)
            foreach ($GetAnnextureFiles['xlsxFiles'] as $file) {
                $converted[] = [
                    'path' => storage_path('app/public/' . str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $file->filepath)),
                    'type' => $annexureLabels[$file->annexture_type] ?? 'xlsx',
                    'title' => $annexureLabels[$file->annexture_type] ?? 'annexure',
                ];
            }

              // $slipAnnexurePaths = $this->getConvertedAnnexurePaths($GetAnnextureFiles, $annexureLabels);
              $this->loadAnnexuresToFpdi($pdf, $converted, $annexureLabels,'fileupbased',$lang);



                if (file_exists($tempMainPdfPath)) {
          unlink($tempMainPdfPath);
   }


    // Output final PDF
       $fileName = 'AuditReport_'.$scheduleId.'.pdf';
           $finalFilePath = public_path('files/' . $fileName);
                $pdf->Output($finalFilePath, 'F');
                return $fileName;   
                
    }

    private function getConvertedAnnexurePaths($annexureFiles, $labels)
    {
        $pdfPaths = [];
        $excelPaths = [];

        foreach ($annexureFiles as $file) {
            $path = storage_path('app/public/' . $file->filepath);
            $type = $file->annexture_type ?? 'unknown';

            if (!File::exists($path)) continue;

            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            if ($extension === 'pdf') {
                $pdfPaths[] = [
                    'path' => $path,
                    'type' => $type,
                    'title' => $labels[$type] ?? 'Annexure',
                ];
            } elseif ($extension === 'xlsx') {
                // Convert XLSX to PDF
                $spreadsheet = IOFactory::load($path);
                $htmlWriter = new Html($spreadsheet);
                ob_start();
                $htmlWriter->save('php://output');
                $excelHtml = ob_get_clean();

                $tempExcelPdfPath = tempnam(sys_get_temp_dir(), 'xlsx_pdf_') . '.pdf';

                $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
                $style = '<style>table, td, th { border: 1px solid #000; border-collapse: collapse; }</style>';
                $mpdf->WriteHTML($style . $excelHtml);
                $mpdf->Output($tempExcelPdfPath, 'F');

                $excelPaths[] = [
                    'path' => $tempExcelPdfPath,
                    'type' => $type,
                    'title' => $labels[$type] ?? 'Excel Annexure',
                ];
            }
        }

        return array_merge($pdfPaths, $excelPaths);
    }

    
private function loadAnnexuresToFpdi(Fpdi $fpdi, array $annexureFiles, $labels, $typeofann, $lang)
{
    $labelsJson_layout = json_decode(file_get_contents(public_path('json/layout.json')), true);
    $label_layout = $labelsJson_layout[$lang] ?? [];

    foreach ($annexureFiles as $file) {
        $path = $file['path'] ?? '';
        $label = $file['title'] ?? 'Annexure';

        if (!File::exists($path)) continue;
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($typeofann === 'fileupbased') {
            $mpdfTitle = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'fontDir' => array_merge(
                    (new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'],
                    [public_path('fonts/tamil')]
                ),
                'fontdata' => (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'] + [
                    'noto1' => ['R' => 'Latha.ttf', 'useOTL' => 0xFF],
                    'noto'  => ['R' => 'times.ttf', 'useOTL' => 0xFF],
                ],
                'default_font' => 'noto1',
                'format' => 'A4',
                // 'margin_top' => 0,
                // 'margin_bottom' => 0,
                'margin_left' => 0,
                'margin_right' => 0,
            ]);

            $titlePageHtml = '
                 <div style="border:.5mm solid black;margin:10px 20px; height:100%;  box-sizing:border-box; display:flex; ">
                    <div style="width:100%; text-align:center;">
                        <div style="font-size:24pt; margin-top:70%; font-weight:bold; text-align:center;">' . htmlspecialchars($label) . '</div>
                    </div>
                </div>';

            $mpdfTitle->WriteHTML($titlePageHtml);
            $tempTitlePdf = tempnam(sys_get_temp_dir(), 'title_') . '.pdf';
            $mpdfTitle->Output($tempTitlePdf, 'F');

            $pageCount = $fpdi->setSourceFile($tempTitlePdf);
            for ($p = 1; $p <= $pageCount; $p++) {
                $tpl = $fpdi->importPage($p);
                $size = $fpdi->getTemplateSize($tpl);
                $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $fpdi->useTemplate($tpl);
            }

            @unlink($tempTitlePdf);
        }

        if ($extension === 'pdf') {
            $outputFile = tempnam(sys_get_temp_dir(), 'uncompress_') . '.pdf';
            $this->convertPdfTo14($path, $outputFile);
            $pageCount = $fpdi->setSourceFile($outputFile);

            for ($p = 1; $p <= $pageCount; $p++) {
                $tpl = $fpdi->importPage($p);
                $size = $fpdi->getTemplateSize($tpl);
                $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $fpdi->useTemplate($tpl);
            }

            @unlink($outputFile);
        } elseif ($extension === 'xlsx') {
            try {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($path);
                $sheets = $spreadsheet->getAllSheets();

                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'fontDir' => array_merge(
                        (new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'],
                        [public_path('fonts/tamil')]
                    ),
                    'fontdata' => (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'] + [
                        'noto1' => ['R' => 'Latha.ttf', 'useOTL' => 0xFF],
                        'noto'  => ['R' => 'times.ttf', 'useOTL' => 0xFF],
                        'arial' => ['R' => 'arial.ttf', 'useOTL' => 0xFF],
                    ],
                    'default_font' => 'noto1',
                    'format' => 'A4',
                    'orientation' => 'P',
                    'margin_top' => 13,
                    'margin_bottom' => 13,
                    'margin_left' => 10,
                    'margin_right' => 10,
                ]);

                $style = '<style>table, td, th { border: 1px solid #000; border-collapse: collapse; font-size: 10pt; font-family: latha; }</style>';

                foreach ($sheets as $sheetIndex => $sheet) {
                    $data = $sheet->toArray(null, false, false, true);
                    $isSheetEmpty = array_reduce($data, fn($carry, $row) =>
                        $carry && !array_filter($row, fn($cell) => trim((string)$cell) !== ''), true);

                    if ($isSheetEmpty) continue;
                    if ($sheetIndex > 0) $mpdf->AddPage();

                    $sheetTitle = $sheet->getTitle();
                    $nonEmptyColumns = [];
                    foreach ($data as $row) {
                        foreach ($row as $colIndex => $cell) {
                            if (trim((string)$cell) !== '') {
                                $nonEmptyColumns[$colIndex] = true;
                            }
                        }
                    }

                    $excelHtml = $style;
                    $excelHtml .= "<h4 style='font-weight:bold; text-align:center; color:#2c3e50;'>{$sheetTitle}</h4>";
                    $excelHtml .= "<table width='100%' cellpadding='5'>";

                    foreach ($data as $row) {
                        if (!array_filter($row, fn($cell) => trim((string)$cell) !== '')) continue;

                        $excelHtml .= "<tr>";
                        foreach ($row as $colIndex => $cell) {
                            if (!isset($nonEmptyColumns[$colIndex])) continue;
                            $excelHtml .= "<td>" . htmlspecialchars($cell ?? '') . "</td>";
                        }
                        $excelHtml .= "</tr>";
                    }

                    $excelHtml .= "</table><br><br>";
                    $mpdf->WriteHTML($excelHtml);
                }

                $tempPdf = tempnam(sys_get_temp_dir(), 'xlsx_') . '.pdf';
                $mpdf->Output($tempPdf, 'F');

                $pageCount = $fpdi->setSourceFile($tempPdf);
                for ($p = 1; $p <= $pageCount; $p++) {
                    $tpl = $fpdi->importPage($p);
                    $size = $fpdi->getTemplateSize($tpl);
                    $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $fpdi->useTemplate($tpl);
                }

                @unlink($tempPdf);
            } catch (\Exception $e) {
                $fpdi->AddPage();
                $fpdi->SetFont('Times', '', 12);
                $fpdi->SetXY(10, 50);
                $fpdi->MultiCell(190, 10, "Error loading Excel file '{$file['title']}'\n" . $e->getMessage(), 0);
            }
        }
    }

    // Final End Page (*** End of Report ***)
    if ($typeofann === 'fileupbased') {
        $mpdfEnd = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'fontDir' => array_merge(
                (new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'],
                [public_path('fonts/tamil')]
            ),
            'fontdata' => (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'] + [
                'noto1' => ['R' => 'Latha.ttf', 'useOTL' => 0xFF],
                'noto'  => ['R' => 'times.ttf', 'useOTL' => 0xFF],
            ],
            'default_font' => 'noto1',
            'format' => 'A4',
            //'margin_top' => 0,
            //'margin_bottom' => 0,
           'margin_left' => 0,
           'margin_right' => 0,
        ]);

       $endPageHtml = '
                 <div style="border:.5mm solid black;margin:10px 20px; height:100%;  box-sizing:border-box; display:flex;justify-content:center; align-items:center; ">   
        <div style="font-size:24pt; margin-top:70%; font-weight:bold; text-align:center;">' . 
            htmlspecialchars($label_layout['endreport'] ?? '*** End of Report ***') . 
        '</div>
    </div>';



        $mpdfEnd->WriteHTML($endPageHtml);
        $tempEndPdf = tempnam(sys_get_temp_dir(), 'end_') . '.pdf';
        $mpdfEnd->Output($tempEndPdf, 'F');

        $pageCount = $fpdi->setSourceFile($tempEndPdf);
        for ($p = 1; $p <= $pageCount; $p++) {
            $tpl = $fpdi->importPage($p);
            $size = $fpdi->getTemplateSize($tpl);
            $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $fpdi->useTemplate($tpl);
        }

        @unlink($tempEndPdf);
    }
}







// private function loadAnnexuresToFpdi(Fpdi $fpdi, array $annexureFiles, $labels, $typeofann, $lang)
// {
//     $label_layout = json_decode(file_get_contents(public_path('json/layout.json')), true)[$lang];

//     foreach ($annexureFiles as $file) {
//         $path = $file['path'] ?? '';
//         $label = $file['title'] ?? 'Annexure';

//         if (!File::exists($path)) continue;

//         $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

//         if ($typeofann == 'fileupbased') {
//             // Generate title page with mPDF and Tamil font
//             $mpdf = new \Mpdf\Mpdf([
//                 'mode' => 'utf-8',
//                 'format' => 'A4',
//                 'orientation' => 'P',
//                 'margin_top' => 20,
//                 'margin_bottom' => 20,
//                 'margin_left' => 10,
//                 'margin_right' => 10,
//                 'default_font' => 'latha',
//                 'fontDir' => array_merge(
//                     (new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'],
//                     [public_path('fonts/tamil')]
//                 ),
//                 'fontdata' => (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'] + [
//                     'latha' => [
//                         'R' => 'Latha.ttf',
//                         'useOTL' => 0xFF,
//                     ]
//                 ]
//             ]);

//             $html = '
//                 <div <style>table, td, th { border: 1px solid #000; border-collapse: collapse; font-size: 10pt; }</style>
//                     <b>' . htmlspecialchars($label) . '</b>
//                 </div>
//             ';
//             $titlePdf = tempnam(sys_get_temp_dir(), 'title_') . '.pdf';
//             $mpdf->WriteHTML($html);
//             $mpdf->Output($titlePdf, 'F');

//             $pageCount = $fpdi->setSourceFile($titlePdf);
//             for ($p = 1; $p <= $pageCount; $p++) {
//                 $tpl = $fpdi->importPage($p);
//                 $size = $fpdi->getTemplateSize($tpl);
//                 $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
//                 $fpdi->useTemplate($tpl);
//             }
//             @unlink($titlePdf);
//         }

//         // Merge PDF
//         if ($extension === 'pdf') {
//             $pageCount = $fpdi->setSourceFile($path);
//             for ($p = 1; $p <= $pageCount; $p++) {
//                 $tpl = $fpdi->importPage($p);
//                 $size = $fpdi->getTemplateSize($tpl);
//                 $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
//                 $fpdi->useTemplate($tpl);
//             }
//         }

//         // Convert Excel to PDF with mPDF
//         elseif ($extension === 'xlsx') {
//             $spreadsheet = IOFactory::load($path);
//             $htmlWriter = new Html($spreadsheet);
//             ob_start();
//             $htmlWriter->save('php://output');
//             $excelHtml = ob_get_clean();

//             $tempPdf = tempnam(sys_get_temp_dir(), 'xlsx_') . '.pdf';

//             $mpdf = new \Mpdf\Mpdf([
//                 'mode' => 'utf-8',
//                 'format' => 'A4',
//                 'default_font' => 'latha',
//                 'fontDir' => array_merge(
//                     (new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'],
//                     [public_path('fonts/tamil')]
//                 ),
//                 'fontdata' => (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'] + [
//                     'latha' => [
//                         'R' => 'Latha.ttf',
//                         'useOTL' => 0xFF,
//                     ]
//                 ]
//             ]);

//             $style = '<style>table, td, th { border: 1px solid #000; border-collapse: collapse; font-size: 10pt; }</style>';
//             $mpdf->WriteHTML($style . $excelHtml);
//             $mpdf->Output($tempPdf, 'F');

//             $pageCount = $fpdi->setSourceFile($tempPdf);
//             for ($p = 1; $p <= $pageCount; $p++) {
//                 $tpl = $fpdi->importPage($p);
//                 $size = $fpdi->getTemplateSize($tpl);
//                 $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
//                 $fpdi->useTemplate($tpl);
//             }

//             @unlink($tempPdf);
//         }
//     }

//     // Final End of Report Page using mPDF
//     if ($typeofann === 'fileupbased') {
//         $endText = htmlspecialchars($label_layout['endreport'] ?? '*** End of Report ***');

//         $mpdf = new \Mpdf\Mpdf([
//             'mode' => 'utf-8',
//             'format' => 'A4',
//             'orientation' => 'P',
//             'default_font' => 'latha',
           
//                 'margin_top' => '13',
//                 'margin_bottom' => '13',
//                 'margin_left' => '10',
//                 'margin_right' => '10',
//             'fontDir' => array_merge(
//                 (new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'],
//                 [public_path('fonts/tamil')]
//             ),
//             'fontdata' => (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'] + [
//                 'latha' => [
//                     'R' => 'Latha.ttf',
//                     'useOTL' => 0xFF,
//                 ]
//             ]
//         ]);

//    $html = '
// <div style="
//     width: 100%;
//     height: 100%;
//     border: 3px solid #000;
//     font-size: 15pt;
//     display: flex;
//     justify-content: center;
//     align-items: center;
//     text-align: center;
//     box-sizing: border-box;
//     margin-top : 13;
// ">
//     <b>' . htmlspecialchars($endText) . '</b>
// </div>
// ';




//         $endPdf = tempnam(sys_get_temp_dir(), 'end_') . '.pdf';
//         $mpdf->WriteHTML($html);
//         $mpdf->Output($endPdf, 'F');

//         $pageCount = $fpdi->setSourceFile($endPdf);
//         for ($p = 1; $p <= $pageCount; $p++) {
//             $tpl = $fpdi->importPage($p);
//             $size = $fpdi->getTemplateSize($tpl);
//             $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
//             $fpdi->useTemplate($tpl);
//         }

//         @unlink($endPdf);
//     }
// }

    function applyFontByLanguage(string $html, bool $pageBreak = true): string
    {
        // Load and clean HTML
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        // Remove existing font-family styles
        $xpath = new \DOMXPath($dom);
        foreach ($xpath->query('//*[@style]') as $el) {
            $style = $el->getAttribute('style');
            $rules = array_filter(array_map('trim', explode(';', $style)), function ($rule) {
                return $rule !== '' && stripos($rule, 'font-family') !== 0;
            });
            if (count($rules)) {
                $el->setAttribute('style', implode('; ', $rules));
            } else {
                $el->removeAttribute('style');
            }
        }

        $body = $dom->getElementsByTagName('body')->item(0);
        $cleanHtml = '';
        foreach ($body->childNodes as $child) {
            $cleanHtml .= $dom->saveHTML($child);
        }

        // Regex to split by language/script (Tamil vs Non-Tamil)
        $segments = preg_split('/((?:[\x{0B80}-\x{0BFF}]+))/u', $cleanHtml, -1, PREG_SPLIT_DELIM_CAPTURE);

        // Initialize output
        $output = '';

        // No @page style or border here   just font styling

        // Optional page break div if needed
        if ($pageBreak && !empty(trim($cleanHtml))) {
            $output .= '<div ></div>';
        }

        // Process segments and apply font styles
        foreach ($segments as $segment) {
            if (preg_match('/[\x{0B80}-\x{0BFF}]/u', $segment)) {
                // Tamil font
                $output .= '<span style="font-family: noto1; font-size: 12pt;">' . $segment . '</span>';
            } elseif (trim(strip_tags($segment)) !== '') {
                // Non-empty English or others
                
                $output .= '<span style="font-family: times; font-size: 13pt;">' . $segment . '</span>';
            } else {
                // Keep spacing/markup if needed
                $output .= $segment;
            }
        }

        return $output;
    }

    private function GeneratePartAContentsLoad()
    {
        $templatePath = resource_path('views/pdf/first_page_template.html');
        $htmlTemplate = File::get($templatePath);

        return $htmlTemplate;

        
    }

    private function coveringletter($auditscheduleid, $lang,$certypetxt,$auditEndDate)
    {

        $loaddata = $this->loadAllValues($auditscheduleid, $lang);
        //    print_r($loaddata);
        // exit;
        $financialYear = $loaddata['yearname'];

       
        if($lang === 'en'){
             $yeartype = 'Financial Year';
        }else{
             $yeartype = 'நிதி ஆண்டு';
        }


        if($loaddata['deptcode'] == '01' )
        {
if($lang === 'en')
            $yeartype = 'Fasli Year';
else $yeartype = 'ஃபஸ்லி ஆண்டு';

        }

        $certificate_opinion =  $certypetxt .' Opinion';

        // print_r($certypetxt);
        // exit;

        $AuditeeInstDet = DB::table('audit.auditee_dept_reporting')
                                ->where('instid',$loaddata['instid'])
                                ->select('designation')
                                ->orderBy('auditeedesigid', 'asc')
                                ->first();


         if(!$AuditeeInstDet)
         {


                        $auditeedesig = 'to be filled';


         }else
         {
                        $auditeedesig = $AuditeeInstDet->designation;

         }
   $templateJson = json_decode(file_get_contents(public_path('json/report.json')), true);

$template = $templateJson[$lang]['coverletter_' . $lang];


    $htmlTemplate = $template;


    
        // $htmlTemplate = File::get($templatePath);
//  print_r($templatePath);
//         exit;
        // Break address by comma
        $auditaddress = explode(',', $loaddata['auditeeofficeaddress']);
        //   print_r($templatePath);
        // exit;
        // Convert to <br> separated string
        $formattedAddress = implode('<br>', array_map('trim', $auditaddress));

        // Prepare final HTML
        $auditeeofficeaddress = '<p style="font-weight:normal !important;">'.$auditeedesig . '<br>' . $formattedAddress .'</p>';

        // print_r($auditeeofficeaddress);
        // exit;

         if($lang === 'en'){
        $replacements = [
            '[audityear]'           => $financialYear,
            '[AuditeeInstitution]'  => $loaddata['instename'],
            '[TeamDet]'             => $loaddata['Teamdetails'],
            '[yeartype]'            => $yeartype,
            '[certificateopinion]'  => $certificate_opinion,
            '[auditeeInstDetails]'  => $auditeeofficeaddress,
            '[endateofaudit]'       => $auditEndDate,
            '[Date of Signing]'     => date('d/m/Y')
          
        ];
    }else{
         $replacements = [
            '[audityear]'           => $financialYear,
            '[AuditeeInstitution]'  => $loaddata['insttname'],
            '[TeamDet]'             => $loaddata['Teamdetails'],
            '[yeartype]'            => $yeartype,
            '[certificateopinion]'  => $certificate_opinion,
            '[auditeeInstDetails]'  => $auditeeofficeaddress,
            '[endateofaudit]'       => $auditEndDate,
            '[Date of Signing]'     => date('d/m/Y')
          
        ];
    }

        // Replace all placeholders
        $htmlContent = str_replace(array_keys($replacements), array_values($replacements), $htmlTemplate);

        return '<div class="section-content">' . $htmlContent . '</div>';

    }
//  private function coveringletter($auditscheduleid, $lang, $certypetxt, $auditEndDate)
// {
//     $loaddata = $this->loadAllValues($auditscheduleid, $lang);
//     $financialYear = $loaddata['yearname'];

//     // Set year type based on language and deptcode
//     if ($loaddata['deptcode'] == '01') {
//         $yeartype = $lang === 'en' ? 'Fasli Year' : 'ஃபஸ்லி ஆண்டு';
//     } else {
//         $yeartype = $lang === 'en' ? 'Financial Year' : 'நிதி ஆண்டு';
//     }

//     $certificate_opinion = $certypetxt . ' Opinion';

//     $AuditeeInstDet = DB::table('audit.auditee_dept_reporting')
//         ->where('instid', $loaddata['instid'])
//         ->select('designation')
//         ->orderBy('auditeedesigid', 'asc')
//         ->first();

//     $auditeedesig = $AuditeeInstDet ? $AuditeeInstDet->designation : 'to be filled';

//     // Load the template JSON file
//     $templateJson = json_decode(file_get_contents(public_path('json/report.json')), true);
//     $template = $templateJson[$lang === 'ta' ? 'coverletter_ta' : 'coverletter_en'];

//     // This is your template (HTML string), no need to use File::get
//     $htmlTemplate = $template;

//     // Format address
//     $auditaddress = explode(',', $loaddata['auditeeofficeaddress']);
//     $formattedAddress = implode('<br>', array_map('trim', $auditaddress));
//     $auditeeofficeaddress = '<p style="font-weight:normal !important;">' . $auditeedesig . '<br>' . $formattedAddress . '</p>';

//     // Define replacements
//     $replacements = [
//         '[audityear]'           => $financialYear,
//         '[AuditeeInstitution]'  => $lang === 'en' ? $loaddata['instename'] : $loaddata['insttname'],
//         '[TeamDet]'             => $loaddata['Teamdetails'],
//         '[yeartype]'            => $yeartype,
//         '[certificateopinion]'  => $certificate_opinion,
//         '[auditeeInstDetails]'  => $auditeeofficeaddress,
//         '[endateofaudit]'       => $auditEndDate,
//         '[Date of Signing]'     => date('d/m/Y')
//     ];

//     // Replace placeholders in HTML
//     $htmlContent = str_replace(array_keys($replacements), array_values($replacements), $htmlTemplate);

//     return '<div class="section-content">' . $htmlContent . '</div>';
// }
    private function generateFirstPageHtml($deptsize, $DeptName, $base64Image, $DistName, $InstituteName, $FinancialYear, $AuditReport_Year, $AuditReport_Text)
    {
        // Load the HTML template
        $templatePath = resource_path('views/pdf/first_page_template.html');
        $htmlTemplate = File::get($templatePath);

        // Define replacements
        $replacements = [
            '{{deptsize}}'         => $deptsize,
            '{{DeptName}}'         => htmlspecialchars($DeptName),
            '{{base64Image}}'      => $base64Image,
            '{{DistName}}'         => htmlspecialchars($DistName),
            '{{InstituteName}}'    => htmlspecialchars($InstituteName),
            '{{FinancialYear}}'    => htmlspecialchars($FinancialYear),
            '{{AuditReport_Year}}' => htmlspecialchars($AuditReport_Year),
            '{{AuditReport_Text}}' => htmlspecialchars($AuditReport_Text),
        ];

        // Replace all placeholders
        $htmlContent = str_replace(array_keys($replacements), array_values($replacements), $htmlTemplate);

        return '<div class="section-content">' . $htmlContent . '</div>';
    }


    private function workallocationpdf($auditscheduleid, $lang)
    {
        $workAllocation = FormatModel::fetch_allocatedwork($auditscheduleid);
        $labels = $this->loadlabels();
        $nodata_avail = $labels[$lang]['nodata_avail'];
    
        $html = '';
    
        if (!$workAllocation->isEmpty()) {
            $results = [];
            foreach ($workAllocation->all() as $item) {
                $results[] = [
                    'username' => $item->username,
                    'worktypes_first' => $item->worktypes_first,
                ];
            }
    
            if (!empty($results)) {
                $html .= '<h3 style="text-align: center;">5. WORK ALLOCATION</h3>';
                $html .= '<div style="page-break-inside: avoid;">'; // Prevent breaking inside table
                $html .= '<table border="1" cellpadding="8" cellspacing="0" width="100%" style="border-collapse: collapse;">';
                $html .= '<thead>';
                $html .= '<tr>';
                $html .= '<th style="width: 10%; font-weight: bold;">S.No.</th>';
                $html .= '<th style="width: 45%; font-weight: bold;">Team Member Name</th>';
                $html .= '<th style="width: 45%; font-weight: bold;">Work Allocation</th>';
                $html .= '</tr>';
                $html .= '</thead><tbody>';
    
                $serialNumber = 1;
                foreach ($results as $entry) {
                    $html .= '<tr>';
                    $html .= '<td>' . $serialNumber++ . '</td>';
                    $html .= '<td>' . htmlspecialchars($entry['username']) . '</td>';
                    $html .= '<td>' . $entry['worktypes_first'] . '</td>';
                    $html .= '</tr>';
                }
    
                $html .= '</tbody></table>';
                $html .= '</div>';
            }
        }
    
        return $html;
    }
    
        /**
     * Make the HTML content editable by adding contenteditable="true" to all text elements,
     * but exclude images from being editable.
     */
    private function makeEditablenew($htmlContent)
    {
        // Use DOMDocument to manipulate the HTML and make text fields editable
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); // Disable warnings for invalid HTML structure

        // Load the HTML content
        $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Create an XPath object to search for elements
        $xpath = new \DOMXPath($dom);

        // Find all text nodes (excluding images) and set them to editable
        foreach ($xpath->query('//body') as $element) {
            // Check if the element is not an image
            if ($element->tagName !== 'img') {
                $element->setAttribute('contenteditable', 'true');
            }
        }

        // Return the modified HTML content
        return $dom->saveHTML();
    }


      /**
     * Make the HTML content editable by adding contenteditable="true" to all elements.
     */
    private function makeEditable($htmlContent)
    {
        // Use DOMDocument to manipulate the HTML and make text fields editable
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); // Disable warnings for invalid HTML structure

        $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Find all text nodes and set them to editable
        $xpath = new \DOMXPath($dom);
       // Make only the .content-wrapper div editable
        foreach ($xpath->query('//div[contains(@class, "content-wrapper")]|//body') as $contentWrapper) {
            $contentWrapper->setAttribute('contenteditable', 'true');
        }

        // Ensure individual child elements inside .content-wrapper are NOT editable
        foreach ($xpath->query('//div[contains(@class, "content-wrapper")]//*') as $childElement) {
            $childElement->removeAttribute('contenteditable');
        }


        // Return the modified HTML content
        return $dom->saveHTML();
    }


    private function addEmptySpaceForPreview($htmlContent)
    {
        // Calculate the total height of content (you can use JS to dynamically get the height in the front-end, but for simplicity, let's assume a fixed height here)
        $contentHeight = 5000;  // For example, assume content height is 5000px (adjust dynamically as needed)
        $pageHeight = 8000;     // Assume page height is 8000px

        // Calculate the empty space to be added
        $emptySpaceHeight = $pageHeight - $contentHeight;

        // If content is smaller than the page, add empty space
        if ($emptySpaceHeight > 0) {
            $htmlContent .= '<div style="height:' . $emptySpaceHeight . 'px;"></div>';
        }

        return $htmlContent;
    }


    private function addBordersToHtml($htmlContent)
    {
        // Add a style for the border of the content and page breaks
        $htmlContent = '
        <style>
           
            .content-wrapper {
                border: 2px solid #000;
                padding: 20px;
                margin: 20px auto;
                width: 90%;
                box-sizing: border-box;
            }
            .page-content {
                padding: 10px;
            }
            .highlight {
                background-color: yellow;
            }
            table {
                border-collapse: collapse;  /* Ensures single line borders for the table */
                width: 100%;
            }
            th, td {
                border: 1px solid black;  /* Single line border for table cells */
                padding: 5px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }




            img
            {
               width:200px !important;
               height:200px !important;
            }




        </style>
        <div class="content-wrapper">
            <div class="page-content">
                ' . $htmlContent . '
            </div>
        </div>';

        return $htmlContent;
    }

    private function parseHtmlToWord($section, $htmlContent, $lang )
{
    // Set default font and size based on language
    $fontSettings = $this->getFontSettings($lang);
    $fontName = $fontSettings['name'];
    $defaultSize = $fontSettings['size'];

    // Keep only allowed HTML tags
    $htmlContent = strip_tags($htmlContent, '<b><h1><h2><h4><h5><h6><p><ul><ol><li><table><tr><td><pre>');

    $paragraphStyle = [
        'lineHeight' => 0.5, // 1.5x line spacing
        'spaceAfter' => 200, // Adds spacing after paragraphs
    ];

    $dom = new \DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML(mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();

    $body = $dom->getElementsByTagName('body')->item(0);
    if (!$body) return;

    foreach ($body->childNodes as $node) {
        $this->processNode($section, $node, $fontName, $defaultSize,$lang,null);
    }
}

// Function to get font settings based on language
private function getFontSettings($lang)
{
    $fonts = [
        'ta' => ['name' => 'Latha', 'size' => 10], // Tamil
        'en' => ['name' => 'Times New Roman', 'size' => 13], // English
    ];

    return $fonts[$lang] ?? $fonts['en']; // Default to English settings
}

    

// Function to get default line height based on language
private function getLineHeightSettings($lang)
{
    $lineHeights = [
        'ta' => 0.8, // Tamil (example: higher line height for readability)
        'en' => 1.4, // English (default)
    ];

    return $lineHeights[$lang] ?? $lineHeights['en']; // Default to English if not found
}

private function processNode($section, $node, $fontName, $defaultSize, $lang,$tableCell = null)
{
    $text = trim($node->textContent);
    if (empty($text)) return;

    $styles = ['name' => $fontName, 'size' => $defaultSize];
    $alignment = [];
    
    // Get base line height for language
    $baseLineHeight = $this->getLineHeightSettings($lang);
    $lineHeight = $baseLineHeight; // Default line height for general text

    switch ($node->nodeName) {
        case 'h1': 
            $styles['bold'] = true; 
            $styles['size'] = 16; 
            $alignment = ['alignment' => 'center']; 
            $lineHeight = $baseLineHeight * 1.5; 
            break;

        case 'h2': 
            $styles['bold'] = true; 
            $styles['size'] = 14; 
            $alignment = ['alignment' => 'center']; 
            $lineHeight = $baseLineHeight * 1.4; 
            break;

        case 'h4': 
            $styles['bold'] = true; 
            $styles['size'] = 13; 
            $alignment = ['alignment' => 'center']; 
            $lineHeight = $baseLineHeight * 1.3; 
            break;

        case 'h5': case 'h6': 
            $styles['bold'] = true; 
            $lineHeight = $baseLineHeight * 1.2; 
            break;

        case 'b': 
            $styles['bold'] = true; 
            break;

        case 'p': 
            $alignment = ['lineHeight' => $baseLineHeight]; // Adjust paragraph spacing
            $lineHeight = $baseLineHeight ; 
            break;

        case 'pre': 
            $styles['underline'] = true; 
            $lineHeight = $baseLineHeight * 1.6; 
            break;

        case 'ul': case 'ol':
            $isOrdered = ($node->nodeName === 'ol');
            foreach ($node->getElementsByTagName('li') as $li) {
                $section->addListItem(trim($li->textContent), 0, $styles, 
                    $isOrdered ? \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER : 
                                 \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET);
            }
            return;

        case 'table':
            $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 100]);
            foreach ($node->getElementsByTagName('tr') as $tr) {
                $tableRow = $table->addRow();
                foreach ($tr->getElementsByTagName('td') as $td) {
                    $colspan = $td->getAttribute('colspan') ? intval($td->getAttribute('colspan')) : 1;
                    $cellWidth = 3000 * $colspan;

                    $tableCell = $tableRow->addCell($cellWidth, ['gridSpan' => $colspan, 'borderSize' => 6]);

                    foreach ($td->childNodes as $childNode) {
                        $this->processNode($section, $childNode, $fontName, $defaultSize, $lang, $tableCell);
                    }
                }
            }
            return;
    }

    // Apply dynamic line height based on language
    $alignment['lineHeight'] = $lineHeight;

    if ($tableCell) {
        $tableCell->addText($text, $styles, $alignment);
    } else {
        $section->addText($text, $styles, $alignment);
    }
}


    // Function to download the generated Word file
    public function downloadWordFile($fileName)
    {
        try {
            // Define the file path for download
            $filePath = public_path('files/' . $fileName);

            // Check if the file exists
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            // Ensure the content type is correctly set for Word files
            return response()->download($filePath, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
        } catch (\Exception $e) {
            // Log the error and return a response
            \Log::error('Error in downloadWordFile: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function deleteFile(Request $request)
    {
        $fileName = $request->input('fileName'); // Get the file name from the request

        // Define the file path for deletion
        $filePath = public_path('files/' . $fileName);

        // Check if the file exists and delete it
        if (file_exists($filePath)) {
            unlink($filePath); // Delete the file
            return response()->json(['success' => 'File deleted successfully']);
        }

        return response()->json(['error' => 'File not found'], 404);
    }

private function cleanForMPDF($html)
    {
        if (!is_string($html) || empty($html)) {
            return '';
        }

        // 1. Remove unsupported HTML5 semantic tags
        $html = preg_replace('/<\/?(section|article|nav|aside|header|footer)[^>]*>/i', '', $html);

        // 2. Remove scripts, styles, iframes, etc.
        $html = preg_replace('/<(script|style|iframe|object|embed)[^>]*>.*?<\/\1>/is', '', $html);

        // 3. Remove class and id attributes
        $html = preg_replace('/\s(class|id)="[^"]*"/i', '', $html);

        // 4. Sanitize inline styles
        $html = preg_replace_callback('/style="([^"]*)"/i', function ($matches) {
            $styles = explode(';', $matches[1]);
            $safeStyles = [];

            foreach ($styles as $style) {
                $style = trim($style);

                // Skip empty or malformed
                if (strpos($style, ':') === false) continue;

                // Skip Word-generated styles like windowtext, pt, etc.
                if (stripos($style, 'windowtext') !== false || stripos($style, 'pt') !== false) {
                    continue;
                }

                $safeStyles[] = $style;
            }

            return count($safeStyles) ? 'style="' . implode('; ', $safeStyles) . '"' : '';
        }, $html);

        return $html;
    }

      public function sendAuditeeMail(Request $request)
    {

        try {
            


            $scheduleId = Crypt::decryptString($request->schedule_id);
            if (empty($scheduleId)) {
                throw new \Exception("schedule Details not found");
            }

            // print_r($scheduleId);
            // exit;

            $user = session('user');
            $userId = $user->userid ?? null;
            $username = $user->username ?? null;

            // return $request->all();




            $auditeedetails = FormatModel::getauditeedetails($scheduleId);



            if (!$auditeedetails) {
                return response()->json(['error' => 'Auditee not found for this schedule.'], 404);
            }

            $instid = $auditeedetails[0]->instid;



            $data = [
                'sendername' => $username,
                'userid' => $userId,
                  'email' => $auditeedetails[0]->auditeeemail,
               // 'email' => 'nijisa18@gmail.com',
                'auditeeusername' => $auditeedetails[0]->auditeeusername,
                'issuedby' => $username,
                'issuedon' =>  View::shared('get_nowtime'),
                // 'ccEmails' => $ccEmails
            ];

    
            $Lang = 'en';
            $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
            $sentsms = $auditModel->sendauditeerportmail($data, $Lang, $scheduleId, $instid);

            //  return $sentsms;


            if ($sentsms) {
                return response()->json(['success' => true, 'message' => 'Audit Report issued successfully']);
            } else {
                return response()->json(['error' => 'Failed to sent Mail'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
            //  return response()->json(['error' => 'Failed to sent Mail'], 500);
        }
    }

   public static function reportdeptfetch()
    {
        $dept = FormatModel::commondeptfetch();

        return view('audit.reportrevoke', compact('dept'));
    }





public function getregionbasedondeptforreportdept(Request $request)
{
    $request->validate([
        'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
    ], [
        'required' => 'The :attribute field is required.',
        'regex'    => 'The :attribute field must be a valid number.',
    ]);

    $deptcode = $request->input('deptcode');


    $regions = FormatModel::getRegionsByDept($deptcode);


    return response()->json([
        'success' => true,
        'data' => $regions
    ]);
}




public function getdistrictbasedonregionreport(Request $request)
{
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

    $regioncode = $request->input('region');
    $deptcode = $request->input('deptcode');


    $district = FormatModel::getdistrictByregion($regioncode, $deptcode);

    if ($district->isNotEmpty()) {
        return response()->json(['success' => true, 'data' => $district]);
    } else {
        return response()->json(['success' => false, 'message' => 'No regions found'], 404);
    }
}


public function getinstitutionbasedondistreport(Request $request)
{
    // Validate the input
    $request->validate([
        'region'   => ['required', 'string', 'regex:/^\d+$/'],
        'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        'district' => ['required', 'string', 'regex:/^\d+$/'],
    ], [
        'region.required' => 'The :attribute field is required.',
        'region.regex'    => 'The :attribute field must be a valid number.',
        'deptcode.required' => 'The deptcode field is required.',
        'deptcode.regex'    => 'The deptcode field must be a valid number.',
        'district.required' => 'The district field is required.',
        'district.regex'    => 'The district field must be a valid number.',
    ]);

    $regioncode = $request->input('region');
    $deptcode = $request->input('deptcode');
    $district = $request->input('district');

	$institution = FormatModel::getinstitutionBydistrictchange($district, $regioncode, $deptcode);

    if ($institution->isNotEmpty()) {
        return response()->json(['success' => true, 'data' => $institution]);
    } else {
        return response()->json(['success' => false, 'message' => 'No Institutions found'], 200);
    }
}






public function report_fetchData(Request $request)
{

    $auditscheduleid = $request->has('auditscheduleid') ? Crypt::decryptString($request->auditscheduleid) : null;
    $auditschedule = FormatModel::reportfetchData('audit.inst_schteammember');
    if (is_iterable($auditschedule)) {
        foreach ($auditschedule as $all) {
            $all->encrypted_sauditscheduleid= Crypt::encryptString($all->auditscheduleid);
            unset($all->auditscheduleid);
        }
    }

    return response()->json([
        'success' => true,
        'message' => empty($auditschedule) ? 'No Details found' : '',
        'data' => $auditschedule ?? []
    ], 200);
}




public function report_insertupdate(Request  $request)
{
    // print_r($_REQUEST);

   try {


    $rules = [
        'deptcode' => 'required|string|regex:/^\d+$/',
        "regioncode" => 'required|string|regex:/^\d+$/',
        'distcode' => 'required|string|regex:/^\d+$/',
        'instmappingcode' => 'required|string|regex:/^\d+$/',
        // 'usernamefield' => 'integer',
        'revoke' => 'integer'

    ];



    $auditplan = session('user');
    if (!$auditplan || !isset($auditplan->userid)) {
        return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
    }
    $userchargeid = $auditplan->userid;


   $data = [
        'auditscheduleid' => $request->auditscheduleid ?? null,
         'instmappingcode' => $request->instmappingcode ?? null,
        'revoke' =>  $request->revoke ?? null,

    ];

   
    $result = FormatModel::auditreportrevoke_insertupdate($data, 'audit.inst_schteammember',$userchargeid);
      return response()->json(['success' => true, 'message' => 'auditdiaryupdated']);

   } 
   catch (ValidationException $e) {
     return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
   }
    catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
    }
}

private  function convertPdfTo14($inputPath, $outputPath)
    {
        // Escape file paths
        $input = escapeshellarg($inputPath);
        $output = escapeshellarg($outputPath);

        // Use gswin64c or gswin32c depending on your OS
    $command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$output $input";
        exec($command, $outputLines, $resultCode);

        return $resultCode === 0;
    }

public function callcodecheck(Request $request)
{
    // Use your model method to fetch data
    $data = FormatModel::fetchQuarterData('Q1');  

    //print_r($data);exit;
    return response()->json($data);
}


}
