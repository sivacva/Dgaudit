<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\TransactionFlowModel;
use Illuminate\Support\Facades\View;
use App\Models\BaseModel;
use App\Services\FileUploadService;

use App\Models\UserManagementModel;

use Illuminate\Http\Request;
use DataTables;


class TransactionflowController extends Controller
{
    protected static $roletype = BaseModel::ROLETYPE;
    protected static $roletypemapping_table = BaseModel::ROLETYPEMAPPING_TABLE;
    protected static $department_table = BaseModel::DEPARTMENT_TABLE;
    protected static $region_table = BaseModel::REGION_TABLE;
    protected static $transtype_table = BaseModel::TRANSACTIONTYPE_TABLE;
    protected static $district_table = BaseModel::DIST_Table;
    protected static $designation_table = BaseModel::DESIGNATION_TABLE;
    protected static $userdet_table = BaseModel::USERDETAIL_TABLE;
    protected static $othertrans_table = BaseModel::OTHERTRANS_TABLE;
    protected static $leavetype_table = BaseModel::LEAVETYPE_TABLE;
    protected static $transactionflow_table = BaseModel::TRANSACTIONFLOW_TABLE;
    protected $fileUploadService;


    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }



    public function usertrans_dropdown()
    {
        $dept = TransactionFlowModel::getdeptbasedonsession();
        $todept = TransactionFlowModel::getTodept();
        $trans_type = DB::table(self::$transtype_table)
            ->whereNotIn('transactiontypecode', ['01'])
            ->where('statusflag', 'Y')
            ->get();

        $userdata = session('user');
        $sessionuserid = $userdata->userid;
        $ensessionuserid = Crypt::encryptString($sessionuserid);

        return view('transactionflow.othertransaction', compact('dept', 'trans_type', 'ensessionuserid', 'todept'));
    }

    public function getroletypecode_basedondept_othertrans(Request $request)
    {
        $deptcode   =   $_REQUEST['deptcode'];
        $page       =   $_REQUEST['page'];

        $userData = session('charge');
        $session_roletypecode = $userData->roletypecode ?? '';

        // echo $session_roletypecode;
        // exit;

        $request->validate([
            'deptcode'  =>  ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex'     =>  'The :attribute field must be a valid number.',
        ]);

        // Fetch user data based on deptuserid
        $roletypedel = UserManagementModel::roletypebasedon_sessionroletype($deptcode, $session_roletypecode, $page); // Adjust query as needed


        if ($roletypedel) {
            return response()->json(['success' => true, 'data' => $roletypedel]);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }



    public function getdeptbaseddesig(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode'      => ['required', 'string', 'regex:/^\d+$/'],
            // 'valuefor'      => ['required', 'string', 'in:desig,userdetail'], // Include "region"
        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid number.',
            // 'in'       => 'The :attribute field must be one of: desig, userdetail',
        ]);



        // Extract validated data
        $deptcode = $validatedData['deptcode'];
        $instid = $request->input('instid');
        $for    =    $request->input('for');



        // echo $instid;

        // exit;

        // Additional validation for 'region'
        if (!$deptcode) {
            return response()->json(['success' => false, 'message' => 'Department code is required for region.'], 422);
        }
        try {

            $getdata =  TransactionFlowModel::getdeptbased_desig(
                $deptcode,
                $instid,
                $for
            );

            if ($getdata) {
                return response()->json(['success' => true, 'data' => $getdata]);
            }

            return response()->json(['success' => false, 'message' => 'Data not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function fetchRegDistInstbasedondept(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode'      => ['required', 'string', 'regex:/^\d+$/'],
            'roletypecode'  => ['required', 'string', 'regex:/^\d+$/'],
            'regioncode'    => ['nullable', 'string', 'regex:/^\d+$/'],
            'distcode'      => ['nullable', 'string', 'regex:/^\d+$/'],
            'valuefor'      => ['required', 'string', 'in:region,district,institution'], // Include "region"
        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid number.',
            'in'       => 'The :attribute field must be one of: region, district, institution.',
        ]);

        // Extract validated data
        $deptcode = $validatedData['deptcode'];
        $regioncode = $validatedData['regioncode'] ?? null;
        $distcode = $validatedData['distcode'] ?? null;
        $roletypecode = $validatedData['roletypecode'];
        $valuefor = $validatedData['valuefor'];

        // Additional validation for 'region'
        if ($valuefor === 'region' && !$deptcode) {
            return response()->json(['success' => false, 'message' => 'Department code is required for region.'], 422);
        }

        // Additional validation for 'district'
        if ($valuefor === 'district' && !$regioncode) {
            return response()->json(['success' => false, 'message' => 'Region code is required for district.'], 422);
        }

        // Additional validation for 'institution'
        if ($valuefor === 'institution' && in_array($roletypecode, [View::shared('Re_roletypecode'), View::shared('Dist_roletypecode')])) {
            if (!$regioncode) {
                return response()->json(['success' => false, 'message' => 'Region code is required for institution.'], 422);
            }
            if ($roletypecode === View::shared('Dist_roletypecode') && !$distcode) {
                return response()->json(['success' => false, 'message' => 'District code is required for this role type.'], 422);
            }
        }
        try {
            $getdata =  TransactionFlowModel::getdata_regdistinst($deptcode, $regioncode, $distcode, $valuefor, $roletypecode);

            if ($getdata) {
                return response()->json(['success' => true, 'data' => $getdata]);
            }

            return response()->json(['success' => false, 'message' => 'Data not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function instdataforothers(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode'      => ['required', 'string', 'regex:/^\d+$/'],
            'roletypecode'  => ['required', 'string', 'regex:/^\d+$/'],
            'regioncode'    => ['nullable', 'string', 'regex:/^\d+$/'],
            'distcode'      => ['nullable', 'string', 'regex:/^\d+$/'],
            'fromdistcode'  => ['nullable', 'string', 'regex:/^\d+$/'],
            'valuefor'      => ['required', 'string', 'in:region,district,institution'], // Include "region"
        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid number.',
            'in'       => 'The :attribute field must be one of: region, district, institution.',
        ]);

        // Extract validated data
        $deptcode = $validatedData['deptcode'];
        $regioncode = $validatedData['regioncode'] ?? null;
        $distcode = $validatedData['distcode'] ?? null;
        $fromdistcode = $validatedData['fromdistcode'] ?? null;
        $roletypecode = $validatedData['roletypecode'];
        $valuefor = $validatedData['valuefor'];



        // Additional validation for 'region'
        if ($valuefor === 'region' && !$deptcode) {
            return response()->json(['success' => false, 'message' => 'Department code is required for region.'], 422);
        }

        // Additional validation for 'district'
        if ($valuefor === 'district' && !$regioncode) {
            return response()->json(['success' => false, 'message' => 'Region code is required for district.'], 422);
        }

        // Additional validation for 'institution'
        if ($valuefor === 'institution' && in_array($roletypecode, [View::shared('Re_roletypecode'), View::shared('Dist_roletypecode')])) {
            if (!$regioncode) {
                return response()->json(['success' => false, 'message' => 'Region code is required for institution.'], 422);
            }
            if ($roletypecode === View::shared('Dist_roletypecode') && !$distcode) {
                return response()->json(['success' => false, 'message' => 'District code is required for this role type.'], 422);
            }
        }
        try {

            $getdata =  TransactionFlowModel::getdataforToInst($deptcode, $regioncode, $distcode, $valuefor, $fromdistcode, $roletypecode);

            if ($getdata) {
                return response()->json(['success' => true, 'data' => $getdata]);
            }

            return response()->json(['success' => false, 'message' => 'Data not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function filterforusertrans(Request $request)
    {

        if ($request->desigcode && $request->deptcode) {
            $paramCheck = [
                'deptcode' => $request->deptcode,
                'desigcode' => $request->desigcode,
                'distcode' => $request->distcode ?? null,
                'transtype' => $request->transtype ?? null

            ];
            $userdet = TransactionFlowModel::desigbaseduser(self::$userdet_table, $paramCheck);

            return response()->json(['users' => $userdet]);
        }
        if ($request->regioncode && $request->deptcode) {
            $districtdata = TransactionFlowModel::regionbaseddist(self::$district_table, $request->regioncode, $request->deptcode);

            return response()->json(['districtdata' => $districtdata]);
        }
        if ($request->deptcode) {

            $regionDet = TransactionFlowModel::deptbasedregion(self::$roletypemapping_table, $request->deptcode);
            $designationDet = TransactionFlowModel::deptbaseddesignation(self::$designation_table, $request->deptcode);

            return response()->json(['designation' => $designationDet, 'region' => $regionDet]);
        }
    }








    // public function othertransction_insertupdate(Request $request)
    // {

    //     try {
    //         $data = $request->all();
    //         // return $request;

    //         $userSessionData = session('user');
    //         $userid = $userSessionData->userid;
    //         // $auditscheduleid = Crypt::decryptString($request->auditscheduleid);
    //         $order_date = Carbon::createFromFormat('d/m/Y', $request->input('order_date'))->format('Y-m-d');
    //         // return $request->deptuserid;
    //         $request->merge(['order_date' => $order_date]);
    //         $action = $request->input('action');
    //         // $scheduleid  = TransactionFlowModel::getScheduleid($request->deptuserid);
    //         // $auditscheduleid = $scheduleid[0]->auditscheduleid ?? null;
    //         $request->validate([
    //             'deptuserid'            => 'required|integer',
    //             'roletypecode'           => 'required|string|regex:/^\d+$/',
    //             'deptcode'              => 'required|string|regex:/^\d+$/',
    //             'regioncode'              => 'nullable|string|regex:/^\d+$/',
    //             'distcode'              => 'nullable|string|regex:/^\d+$/',
    //             'frominstmapcode'        => 'required|string|regex:/^\d+$/',
    //             'desigcode'              => 'required|string|regex:/^\d+$/',
    //             'transtypecode'         =>  'required|string|regex:/^\d+$/',         // Only alphabets (no numbers or symbols)
    //             'order_date'             =>  'required|date|date_format:Y-m-d|',             // Alphanumeric (letters and numbers)
    //             'orderno'               =>  'required|integer',



    //         ]);
    //         $data = [
    //             'userid'        => $request->deptuserid,
    //             'frominstmappingcode' => $request->frominstmapcode,

    //             'transactiontypecode' => $request->transtypecode,
    //             'orderdate'     => $request->order_date,
    //             'orderno'       => $request->orderno,
    //             'fromdesigcode' => $request->desigcode,
    //             'statusflag'    => 'Y',
    //             'processcode'   => View::shared('Insert'),
    //             'inoutstatus'   =>  View::shared('Outflag'),
    //             'updatedby'     =>  $sessionuserid,
    //             'updatedon'     =>  View::shared('get_nowtime')
    //         ];



    //         if (($request->transtypecode == View::shared('diversionTransactiontypecode')) ||
    //             ($request->transtypecode == View::shared('transfercode')) ||
    //             ($request->transtypecode == View::shared('transferwithpromocode'))
    //         ) {
    //             $data['toinstmappingcode'] = $request->audit_inst;
    //             if (($request->transtypecode == View::shared('transferwithpromocode')) ||
    //                 ($request->transtypecode == View::shared('transfercode'))
    //             ) {
    //                 $data['todesigcode'] = $request->to_desig;
    //             }

    //             // $data['instmappingid'] = 13;
    //         }

    //         // print_r($data);
    //         // exit;

    //         $uploadid = $request->input('uploadid');

    //         if ((($action === 'insert') || ($action === 'update')) && ($request->hasFile('file'))) {

    //             $destinationPath = 'uploads/othertransaction';
    //             $destinationarray = [
    //                 $request->deptcode,
    //                 $request->regioncode,
    //                 $request->distcode,
    //                 $request->frominstmapcode,
    //                 View::shared('othertransactionfilepath'),

    //             ];
    //             if ($uploadid) {
    //                 $uploadResult = $this->fileUploadService->uploadFile($request->file('file'), $destinationPath, $uploadid,  $destinationarray);
    //             } else {
    //                 $uploadResult = $this->fileUploadService->uploadFile($request->file('file'), $destinationPath, '',  $destinationarray);
    //             }

    //             $fileuploadId = $uploadResult->getData()->fileupload_id ?? null;
    //             $data['uploadid'] = $fileuploadId;

    //             $data['createdby'] = $sessionuserid;
    //             $data['createdon'] = View::shared('get_nowtime');


    //         }
    //         if ($request->action == 'update') {
    //             $othertransid = $request->filled('othertransid') ? Crypt::decryptString($request->othertransid) : null;

    //         } else
    //             $othertransid =   null;
    //         $othertrandet = TransactionFlowModel::insertorUpdateOthertrans(self::$othertrans_table, $data, $userid, $othertransid);
    //         return response()->json(['success' => 'Apllication was created/updated successfully', 'othertrans' => $othertrandet]);
    //     } catch (ValidationException $e) {
    //         return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
    //     }
    // }

    public function othertransction_insertupdate(Request $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $userSessionData = session('user');
            $userid = $userSessionData->userid ?? null;
            $charge = session('charge');
            $userchargeid = $charge->userchargeid ?? null;

            // Validate session and user
            if (!$userid) {
                throw new \Exception('User session is invalid or user not logged in.');
            }

            if (!$userchargeid) {
                throw new \Exception('User charge session is missing. Please login again.');
            }

            $enuserid = Crypt::decryptString($request->enuserid);
            // echo $enuserid;
            // echo $userid;
            // exit;

            if ($enuserid != $userid) {

                throw new \Exception('Session mismatch detected. Unauthorized access.');
            }


            $action = $request->input('action');
            $othertransid = ($action === 'update' && $request->filled('othertransid'))
                ? Crypt::decryptString($request->othertransid)
                : null;

            if ($othertransid && $action === 'update' && $enuserid != $userid) {
                throw new \Exception('Update action detected with session mismatch. Aborting.');
            }

            // Validate order_date
            $request->validate([
                'order_date' => 'required|date_format:d/m/Y'
            ], [
                'order_date.required' => 'The Order Date field is required.',
                'order_date.date_format' => 'Order Date must be in the format DD/MM/YYYY.',
            ]);

            // Format date
            $order_date = Carbon::createFromFormat('d/m/Y', $request->input('order_date'))->format('Y-m-d');
            $request->merge(['order_date' => $order_date]);

            // Main validation
            $request->validate([
                'deptuserid' => 'required|integer',
                'roletypecode' => 'required|string|regex:/^\d+$/',
                'deptcode' => 'required|string|regex:/^\d+$/',
                'regioncode' => 'required|string|regex:/^\d+$/',
                'distcode' => 'required|string|regex:/^\d+$/',
                'frominstmapcode' => 'required|string|regex:/^\d+$/',
                'desigcode' => 'required|string|regex:/^\d+$/',
                'transtypecode' => 'required|string|regex:/^\d+$/',
                'order_date' => 'required|date|date_format:Y-m-d',
                'orderno' => 'required|integer',
                'action' => 'required'
            ], [
                'required' => 'The :attribute field is mandatory.',
                'regex' => 'The :attribute field must contain only numbers.',
                'date_format' => 'Invalid date format for :attribute. Expected Y-m-d.'
            ]);

            // Prepare data
            $data = [
                'userid' => $request->deptuserid,
                'frominstmappingcode' => $request->frominstmapcode,
                'transactiontypecode' => $request->transtypecode,
                'orderdate' => $request->order_date,
                'orderno' => $request->orderno,
                'fromdesigcode' => $request->desigcode,
                'statusflag' => 'Y',
                'processcode' => View::shared('Insert'),
                'inoutstatus' => View::shared('Outflag'),
                'updatedbyuserchargeid' => $userchargeid,
                'updatedon' => View::shared('get_nowtime'),
            ];

            // Conditional validations
            if (in_array($request->transtypecode, [
                View::shared('diversionTransactiontypecode'),
                View::shared('transfercode'),
                View::shared('transferwithpromocode')
            ])) {
                $request->validate([
                    'audit_inst' => 'required|string|regex:/^\d+$/'
                ], [
                    'toinstmappingcode.required' => 'The To Institution Mapping Code field is required.',
                    'toinstmappingcode.regex' => 'The To Institution Mapping Code must be numeric.'
                ]);

                $data['toinstmappingcode'] = $request->audit_inst;

                if (in_array($request->transtypecode, [
                    // View::shared('transfercode'),
                    View::shared('transferwithpromocode')
                ])) {
                    $request->validate([
                        'todesigcode' => 'required|string|regex:/^\d+$/'
                    ], [
                        'todesigcode.required' => 'The To Designation Code field is required for this transaction.',
                        'todesigcode.regex' => 'The To Designation Code must be numeric.'
                    ]);

                    $data['todesigcode'] = $request->to_desig;
                }
            } else {
                $data['toinstmappingcode'] =    null;
            }

            // Handle file upload
            $uploadid = $request->input('uploadid');
            if (($action === 'insert' || $action === 'update') && $request->hasFile('file')) {
                $destinationPath = 'uploads/othertransaction';
                $destinationarray = [
                    $request->deptcode,
                    $request->regioncode,
                    $request->distcode,
                    $request->frominstmapcode,
                    View::shared('othertransactionfilepath'),
                ];

                $uploadResult = $this->fileUploadService->uploadFile(
                    $request->file('file'),
                    $destinationPath,
                    $uploadid ?? '',
                    $destinationarray
                );

                $fileuploadId = $uploadResult->getData()->fileupload_id ?? null;
                if (!$fileuploadId) {
                    throw new \Exception('File upload failed. Please try again.');
                }

                $data['uploadid'] = $fileuploadId;
                $data['createdbyuserchargeid'] = $userchargeid;
                $data['createdon'] = View::shared('get_nowtime');
            }

            // Insert or update data
            $othertrandet = TransactionFlowModel::insertorUpdateOthertrans($data, $othertransid, 'maintable');

            if (!$othertrandet || !isset($othertrandet['status'])) {
                throw new \Exception('Unexpected response from transaction model.');
            }

            DB::commit();

            if ($othertrandet['status'] === 'inserted') {
                return response()->json([
                    'success' => 'Application created successfully.',
                    'othertransid' => $othertrandet['othertransid']
                ], 201);
            } elseif ($othertrandet['status'] === 'updated') {
                return response()->json([
                    'success' => 'Application updated successfully.',
                    'othertrans' => $othertrandet['data']
                ], 200);
            } else {
                throw new \Exception($othertrandet['message'] ?? 'Unknown error occurred.');
            }
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['error' => 'Validation failed: ' . $e->getMessage()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }





    public function fetchOtherTranDel(Request $request)
    {
        try {

            $othertransid = $request->filled('othertransid') ? Crypt::decryptString($request->othertransid) : null;
            $othertransDet = TransactionFlowModel::fetchothertransdel($othertransid);

            foreach ($othertransDet as $all) {
                $all->encrypted_othertransid = Crypt::encryptString($all->othertransid);
            }

            if ($othertransid) {
                if ($othertransDet->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Mapping Details not found',
                        'data' => null
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'message' => '',
                    'data' => $othertransDet
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $othertransDet->isEmpty() ? null : $othertransDet
            ], 200);
        } catch (QueryException $e) {
            \Log::error('Database Query Error: ' . $e->getMessage());  // Log the error for debugging

            return response()->json([
                'success' => false,
                'message' => 'There was an issue with the database query. Please try again.'
            ], 400);  // Return a custom error message without the generic server error
        } catch (Exception $e) {
            \Log::error('General Error: ' . $e->getMessage());  // Log the error for debugging

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 400);  // Return a more user-friendly error message
        }
    }

    // public function forward_application(Request $request)
    // {
    //     DB::beginTransaction(); // Begin a new transaction

    //     try {
    //         // Get session data
    //         $userSessionData = session('user');
    //         $userSessionChargeData = session('charge');

    //         // Validate session data
    //         if (!$userSessionData || !isset($userSessionData->userid)) {
    //             return redirect()->back()->withErrors(['Session expired or data missing.']);
    //         }

    //         $sessionchargeid = $userSessionChargeData->userchargeid;
    //         $userid = $userSessionData->userid;

    //         // Retrieve request data
    //         $detailuserid = $request->userid;
    //         $transtypecode = $request->transactiontypecode;
    //         $action = $request->action;
    //         $id = $request->id;

    //         // Forward the application to the next level based on transaction type code
    //         $forwarddel = TransactionFlowModel::forwardtonextlevel($transtypecode, $forwardedtouser, $action);

    //         print_r($forwarddel);
    //         exit;

    //         if (count($forwarddel) == 1) {
    //             $forwardtouserchargeid = $forwarddel[0]->userchargeid;

    //             // Determine process code based on action (Approve or Forward)
    //             $trans_action = $request->action;
    //             $process = $trans_action == 'Approve' ? View::shared('Approve') : View::shared('Forward');

    //             // Prepare the transaction detail data
    //             $transactiondel_data = [
    //                 'forwardedtouserchargeid' => $forwardtouserchargeid,
    //                 'updatedbyuserchargeid' => $userid,
    //                 'updatedon' => View::shared('get_nowtime'),
    //             ];

    //             if ($action == 'first') {
    //                 $transactiondel_data['userid'] = $detailuserid;
    //                 $transactiondel_data['transactiontypecode'] = $transtypecode;
    //                 $transactiondel_data['createdbyuserchargeid'] = $userid;
    //                 $transactiondel_data['createdon'] = View::shared('get_nowtime');
    //                 $transactiondel_data['statusflag'] = 'Y';

    //                 // Set where condition based on transaction type
    //                 if ($transtypecode == View::shared('Leavetransactiontypecode')) {
    //                     $transactiondel_data['leaveid'] = $id;
    //                     $where = ['leaveid' => $id];
    //                 } else {
    //                     $transactiondel_data['othertransid'] = $id;
    //                     $where = ['othertransid' => $id];
    //                 }
    //             }

    //             // Prepare main table update data
    //             $maintableUpdate = [
    //                 'processcode' => $process,
    //                 'updatedby' => $userid,
    //                 'updatedon' => View::shared('get_nowtime'),
    //             ];

    //             // Prepare history transaction data
    //             $historytransaction_data = [
    //                 'userid' => $detailuserid,
    //                 'transactiontypecode' => $transtypecode,
    //                 'processcode' => $process,
    //                 'forwardedtouserchargeid' => $forwardtouserchargeid,
    //                 'forwardedbyuserchargeid' => $userid,
    //                 'forwardedon' => View::shared('get_nowtime'),
    //                 'statusflag' => 'Y',
    //                 'transstatus' => 'A',
    //             ];

    //             // Insert/update history transaction record
    //             $historytransid = TransactionFlowModel::insert_historyTransDetail($historytransaction_data, $where);

    //             // Check if the history transaction was inserted
    //             if ($historytransid && $historytransid['status'] == 'inserted') {
    //                 // Insert or update the transaction detail
    //                 $transdetailid = TransactionFlowModel::insertupdate_transdet($transactiondel_data, $where);

    //                 if ($transdetailid && $transdetailid['status'] == 'updated') {
    //                     // Update the main transaction table
    //                     if ($transtypecode == View::shared('Leavetransactiontypecode')) {
    //                         $leavetableUpdateStatus = TransactionFlowModel::leavetableupdation($maintableUpdate, $id);
    //                     } else {
    //                         $othertransUpdateStatus = TransactionFlowModel::insertorUpdateOthertrans(self::$othertrans_table, $maintableUpdate, $id);
    //                     }

    //                     // Check if the main table update was successful
    //                     if (isset($leavetableUpdateStatus) && $leavetableUpdateStatus['status'] == 'updated' ||
    //                         isset($othertransUpdateStatus) && $othertransUpdateStatus['status'] == 'updated') {
    //                         DB::commit(); // Commit the transaction if all operations are successful
    //                         return response()->json([
    //                             'status' => 'success',
    //                             'message' => 'Application forwarded successfully and record updated.',
    //                         ]);
    //                     } else {
    //                         DB::rollBack(); // Rollback the transaction if the update failed
    //                         return response()->json([
    //                             'status' => 'error',
    //                             'message' => 'Failed to update main transaction table.',
    //                         ]);
    //                     }
    //                 } else {
    //                     DB::rollBack(); // Rollback the transaction if the transaction detail update failed
    //                     return response()->json([
    //                         'status' => 'error',
    //                         'message' => 'Failed to insert/update transaction detail.',
    //                     ]);
    //                 }
    //             } else {
    //                 DB::rollBack(); // Rollback the transaction if history transaction insertion failed
    //                 return response()->json([
    //                     'status' => 'error',
    //                     'message' => 'Failed to insert history transaction.',
    //                 ]);
    //             }
    //         } elseif (count($forwarddel) == 0) {
    //             DB::rollBack(); // Rollback the transaction if no forwarding user found
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'No forwarding user found for the application.',
    //             ]);
    //         } else {
    //             DB::rollBack(); // Rollback the transaction in case of unexpected behavior
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'Multiple users found for forwarding, check your request.',
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         DB::rollBack(); // Rollback the transaction on any unexpected error
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'An error occurred: ' . $e->getMessage(),
    //         ]);
    //     }
    // }


    // public function forward_application(Request $request)
    // {
    //  $userSessionData = session('user');
    //     $userSessionChargeData = session('charge');

    //     // Validate session data
    //     if (!$userSessionData || !isset($userSessionData->userid)) {
    //         return redirect()->back()->withErrors(['Session expired or data missing.']);
    //     }

    //     $sessionchargeid = $userSessionChargeData->userchargeid;
    //     $userid = $userSessionData->userid;

    //     // Retrieve request data
    //     $detailuserid = $request->userid;
    //     $transtypecode = $request->transactiontypecode;
    //     $action = $request->action;
    //     $id = $request->id;

    //     // Forward the application to the next level based on transaction type code
    //     $forwarddel = TransactionFlowModel::forwardtonextlevel($transtypecode, $detailuserid, $action);

    //     $id = Crypt::decryptString($request->id);

    //     // Print debugging info (optional)
    //     // print_r($forwarddel);
    //     // exit;

    //     if (count($forwarddel) == 1) {
    //         $forwardtouserchargeid = $forwarddel[0]->userchargeid;

    //         // Determine process code based on action (Approve or Forward)
    //         $trans_action = $request->action;
    //         $process = $trans_action == 'Approve' ? View::shared('Approve') : View::shared('Forward');

    //         // Prepare the transaction detail data
    //         $transactiondel_data = [
    //             'forwardedtouserchargeid' => $forwardtouserchargeid,
    //             'updatedbyuserchargeid' => $userid,
    //             'updatedon' => View::shared('get_nowtime'),
    //         ];

    //         if ($action == 'first') {
    //             $transactiondel_data['userid'] = $detailuserid;
    //             $transactiondel_data['transactiontypecode'] = $transtypecode;
    //             $transactiondel_data['createdbyuserchargeid'] = $userid;
    //             $transactiondel_data['createdon'] = View::shared('get_nowtime');
    //             $transactiondel_data['statusflag'] = 'Y';

    //             // Set where condition based on transaction type
    //             if ($transtypecode == View::shared('Leavetransactiontypecode')) {
    //                 $transactiondel_data['leaveid'] = $id;
    //                 $where = ['leaveid' => $id];
    //             } else {
    //                 $transactiondel_data['othertransid'] = $id;
    //                 $where = ['othertransid' => $id];
    //             }
    //         }

    //         // Prepare main table update data
    //         $maintableUpdate = [
    //             'processcode' => $process,
    //             'updatedby' => $userid,
    //             'updatedon' => View::shared('get_nowtime'),
    //         ];

    //         // Prepare history transaction data
    //         $historytransaction_data = [
    //             'userid' => $detailuserid,
    //             'transactiontypecode' => $transtypecode,
    //             'processcode' => $process,
    //             'forwardedtouserchargeid' => $forwardtouserchargeid,
    //             'forwardedbyuserchargeid' => $userid,
    //             'forwardedon' => View::shared('get_nowtime'),
    //             'statusflag' => 'Y',
    //             'transstatus' => 'A',
    //         ];

    //         if ($transtypecode == View::shared('Leavetransactiontypecode')) {
    //             $historytransaction_data['leaveid'] = $id;
    //         } else {
    //             $historytransaction_data['othertransid'] = $id;
    //         }

    //         // print_r($historytransaction_data);
    //         // print_r($where);


    //         // Insert/update history transaction record
    //         $historytransid = TransactionFlowModel::insert_historyTransDetail($historytransaction_data, $where);

    //     }
    // }




    // correctone

    public function forward_application(Request $request)
    {
        DB::beginTransaction(); // Begin a new transaction

        try {
            // Get session data
            $userSessionData = session('user');
            $userSessionChargeData = session('charge');

            // Validate session data
            if (!$userSessionData || !isset($userSessionData->userid)) {
                return redirect()->back()->withErrors(['Session expired or data missing.']);
            }

            $sessionchargeid = $userSessionChargeData->userchargeid;
            $userid = $userSessionData->userid;

            $transtypecode = $request->transactiontypecode;


            if ($transtypecode == View::shared('Leavetransactiontypecode')) {
                $detailuserid = $userid;
            } else {
                $detailuserid = $request->userid;
            }
            // Retrieve request data

            $transtypecode = $request->transactiontypecode;
            $action = $request->action;
            $id = $request->id;

            // Forward the application to the next level based on transaction type code
            $forwarddel = TransactionFlowModel::forwardtonextlevel($transtypecode, $detailuserid, $action);

            $id = Crypt::decryptString($request->id);

            // Print debugging info (optional)
            // print_r($forwarddel);
            // exit;

            if (count($forwarddel) == 1) {
                $forwardtouserchargeid = $forwarddel[0]->userchargeid;

                // Determine process code based on action (Approve or Forward)
                $trans_action = $request->action;
                $process = $trans_action == 'Approve' ? View::shared('Approve') : View::shared('Forward');

                // Prepare the transaction detail data
                $transactiondel_data = [
                    'forwardedtouserchargeid' => $forwardtouserchargeid,
                    'updatedbyuserchargeid' => $sessionchargeid,
                    'updatedon' => View::shared('get_nowtime'),
                ];

                if ($action == 'first') {
                    $transactiondel_data['userid'] = $detailuserid;
                    $transactiondel_data['transactiontypecode'] = $transtypecode;
                    $transactiondel_data['createdbyuserchargeid'] = $sessionchargeid;
                    $transactiondel_data['createdon'] = View::shared('get_nowtime');
                    $transactiondel_data['statusflag'] = 'Y';

                    // Set where condition based on transaction type
                    if ($transtypecode == View::shared('Leavetransactiontypecode')) {
                        $transactiondel_data['leaveid'] = $id;
                        $where = ['leaveid' => $id];
                    } else {
                        $transactiondel_data['othertransid'] = $id;
                        $where = ['othertransid' => $id];
                    }
                }

                // Prepare main table update data
                $maintableUpdate = [
                    'processcode' => $process,

                    'updatedon' => View::shared('get_nowtime'),
                ];

                if ($transtypecode != View::shared('Leavetransactiontypecode')) {
                    $maintableUpdate['updatedbyuserchargeid'] = $sessionchargeid;
                } else {
                    $maintableUpdate['updatedby'] = $userid;
                }   // Prepare history transaction data

                $historytransaction_data = [
                    'userid' => $detailuserid,
                    'transactiontypecode' => $transtypecode,
                    'processcode' => $process,
                    'forwardedtouserchargeid' => $forwardtouserchargeid,
                    'forwardedbyuserchargeid' => $sessionchargeid,
                    'forwardedon' => View::shared('get_nowtime'),
                    'statusflag' => 'Y',
                    'transstatus' => 'A',
                ];

                if ($transtypecode == View::shared('Leavetransactiontypecode')) {
                    $historytransaction_data['leaveid'] = $id;
                } else {
                    $historytransaction_data['othertransid'] = $id;
                }

                // print_r($historytransaction_data);
                // print_r($where);


                // Insert/update history transaction record
                $historytransid = TransactionFlowModel::insert_historyTransDetail($historytransaction_data, $where);

                // Check if the history transaction was inserted
                if ($historytransid && $historytransid['status'] == 'inserted') {
                    // Insert or update the transaction detail
                    $transdetailid = TransactionFlowModel::insertupdate_transdet($transactiondel_data, $where);

                    if ($transdetailid && (($transdetailid['status'] == 'updated') || ($transdetailid['status'] == 'inserted'))) {
                        // Update the main transaction table
                        if ($transtypecode == View::shared('Leavetransactiontypecode')) {
                            $leavetableUpdateStatus = TransactionFlowModel::createleave_insertupdate($maintableUpdate, $id, 'audit.ind_leavedetail', $detailuserid, 'transaction');
                        } else {
                            $othertransUpdateStatus = TransactionFlowModel::insertorUpdateOthertrans($maintableUpdate, $id, 'processtable');
                        }

                        // Check if the main table update was successful
                        if (
                            isset($leavetableUpdateStatus) && $leavetableUpdateStatus['status'] == 'updated' ||
                            isset($othertransUpdateStatus) && $othertransUpdateStatus['status'] == 'updated'
                        ) {
                            DB::commit(); // Commit the transaction if all operations are successful
                            return response()->json([
                                'status' => 'success',
                                'message' => 'Application forwarded successfully',
                            ]);
                        } else {
                            DB::rollBack(); // Rollback the transaction if the update failed
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Failed to update main transaction table.',
                            ]);
                        }
                    } else {
                        DB::rollBack(); // Rollback the transaction if the transaction detail update failed
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Failed to insert/update transaction detail.',
                        ]);
                    }
                } else {
                    DB::rollBack(); // Rollback the transaction if history transaction insertion failed
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to insert history transaction.',
                    ]);
                }
            } elseif (count($forwarddel) == 0) {
                DB::rollBack(); // Rollback the transaction if no forwarding user found
                return response()->json([
                    'status' => 'error',
                    'message' => 'No forwarding user found for the application.',
                ]);
            } else {
                DB::rollBack(); // Rollback the transaction in case of unexpected behavior
                return response()->json([
                    'status' => 'error',
                    'message' => 'Multiple users found for forwarding, check your request.',
                ]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack(); // Rollback the transaction on a database-related error
            return response()->json([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on any unexpected error
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ]);
        }
    }


    public function reject_application(Request $request)
    {
        DB::beginTransaction(); // Begin a new transaction

        try {

            $leaveid = Crypt::decryptString($request->leaveid);

            $userSessionChargeData  =   session('charge');
            $userSessionData  =   session('user');

            $sessionchargeid = $userSessionChargeData->userchargeid;
            $userid = $userSessionData->userid;

            $transtypecode = $request->transactiontypecode;
            $detailuserid = $userid;

            $transactiondel_data = [
                'forwardedtouserchargeid' => null,
                'updatedbyuserchargeid' => $sessionchargeid,
                'updatedon' => View::shared('get_nowtime'),
            ];

            // Prepare main table update data
            $maintableUpdate = [
                'processcode' => View::shared('Reject'),
                'updatedby' => $userid,
                'updatedon' => View::shared('get_nowtime'),
            ];

            // Prepare history transaction data
            $historytransaction_data = [
                'userid' => $detailuserid,
                'transactiontypecode' => View::shared('Leavetransactiontypecode'),
                'processcode' => View::shared('Reject'),
                'forwardedbyuserchargeid' => $sessionchargeid,
                'forwardedon' => View::shared('get_nowtime'),
                'statusflag' => 'Y',
                'transstatus' => 'A',
                'leaveid' => $leaveid
            ];

            $where = ['leaveid' => $leaveid];


            // print_r( $where);




            // Insert/update history transaction record
            $historytransid = TransactionFlowModel::insert_historyTransDetail($historytransaction_data, $where);

            // Check if the history transaction was inserted
            if ($historytransid && $historytransid['status'] == 'inserted') {



                // Insert or update the transaction detail
                $transdetailid = TransactionFlowModel::insertupdate_transdet($transactiondel_data, $where);
                //   print_r($transdetailid);
                //   exit;

                if ($transdetailid && (($transdetailid['status'] == 'updated') || ($transdetailid['status'] == 'inserted'))) {
                    // Update the main transaction table
                    $leavetableUpdateStatus = TransactionFlowModel::createleave_insertupdate($maintableUpdate, $leaveid, 'audit.ind_leavedetail', $detailuserid, 'transaction');


                    //   $leavetableUpdateStatus['status']
                    // Check if the main table update was successful
                    if (isset($leavetableUpdateStatus) && $leavetableUpdateStatus['status'] == 'updated') {
                        DB::commit(); // Commit the transaction if all operations are successful
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Application Rejected successfully.',
                        ]);
                    } else {
                        DB::rollBack(); // Rollback the transaction if the update failed
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Failed to update main transaction table.',
                        ]);
                    }
                } else {
                    DB::rollBack(); // Rollback the transaction if the transaction detail update failed
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to insert/update transaction detail.',
                    ]);
                }
            } else {
                DB::rollBack(); // Rollback the transaction if history transaction insertion failed
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to insert history transaction.',
                ]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack(); // Rollback the transaction on a database-related error
            return response()->json([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on any unexpected error
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ]);
        }
    }



    public function fetchall_transflowdata()
    {
        $userSessionData = session('user');
        $userSessionChargeData = session('charge');
        $userSessionChargeData = session('charge');
        $userchargeid = $userSessionChargeData->userchargeid;
        $userid = $userSessionData->userid;

        $forwarded_det = TransactionFlowModel::fetchTransactionFlowData($userchargeid, $userid);

        foreach ($forwarded_det as $item) {
            if ($item->leaveid)  $item->leaveid = Crypt::encryptString($item->leaveid);
            $item->othertransid = Crypt::encryptString($item->othertransid);
        }

        $userSessionChargeData = session('charge');
        $userrolemappingid =  $userSessionChargeData->rolemappingid;
        $roleactioncode = DB::table('audit.rolemapping as rm')
            ->join('audit.mst_roleaction as mr', 'mr.roleactioncode', '=', 'rm.roleactioncode')
            ->where('rolemappingid',  $userrolemappingid)
            ->get(['mr.roleactioncode']);
        // return view('transactionmaster.transaction', compact('roleactioncode'));
        return response()->json(['success' => true, 'data' => $forwarded_det, 'role' => $roleactioncode]);
        // return $forwarded_det;
    }


    public function datatrans_dropdown(Request $request)
    {


        $data = ($request->id); // Get 'id' from URL
        $id =   Crypt::decryptString($request->id);
        $inoutstatus =  $request->inoutstatus;
        $userid =  $request->userid;
        $transtypecode = $request->transtype;

        $roleActionCodes = DB::table('audit.userchargedetails as uc')
            ->join('audit.chargedetails as ch', 'ch.chargeid', '=', 'uc.chargeid')
            ->join('audit.rolemapping as ro', 'ro.rolemappingid', '=', 'ch.rolemappingid')
            ->join('audit.deptuserdetails as du', 'du.deptuserid', '=', 'uc.userid')
            ->where('uc.userid', $userid)
            ->where('uc.statusflag', 'Y')
            ->groupBy('roleactioncode')
            ->select('roleactioncode')
            ->get();



        if (count($roleActionCodes) > 1) {
            echo 'user has 2 roles';
            exit;
        } elseif (count($roleActionCodes) == 1) {
            $roleactioncode =   $roleActionCodes[0]->roleactioncode;
            if (($inoutstatus == 'O') || ($transtypecode == View::shared('Leavetransactiontypecode'))) {
                $pendingdel =  TransactionFlowModel::getting_pendingdel($id, $transtypecode, $userid, $roleactioncode);
            } else    $pendingdel = '';

            $auditscheduleids = collect(); // empty collection by default

            if (!empty($pendingdel['schedulependings']) && $pendingdel['schedulependings']->isNotEmpty()) {
                $auditscheduleids = $pendingdel['schedulependings']->pluck('auditscheduleid');
                $schedule = [];
                foreach ($auditscheduleids as $a) {
                    // echo  $a;
                    $othermembers = $this->getothermembers($a);
                    // print_r($othermembers);

                    $schedule[$a]   =   $othermembers;
                }

                // print_r($schedule);
                // exit;

                $othermembers   =   $schedule;
            } else {
                // No data found or empty collection
                $auditscheduleids = collect(); // or handle empty case as you want
                $othermembers = collect();
            }








            // if($roleactioncode == view::shared('AuditorRoleactioncode'))
            // {

            // }
            // else if($roleactioncode == view::shared('AdminplanviewRoleactioncode'))
            // {

            // }
            // else if($roleactioncode == view::shared('AdminentryRoleactioncode'))
            // {

            // }
            // else{

            // }
        } else {
        }






        // $pendingdel =  TransactionFlowModel::getting_pendingdel($othertransid, $transtypecode,$userid);

        $data =  TransactionFlowModel::fetch_usedrdata_transfer($id, $transtypecode, $inoutstatus, $roleactioncode);
        $otherteamhead =  TransactionFlowModel::fetch_otherteamhead($userid);









        // print_r($data['othertransdet']);
        // foreach ($data as $all) {
        //     $all->a = Crypt::encryptString($all->auditscheduleid);
        // }
        // return $data;
        $othertrans = $data['othertransdet'];
        // $dept       = $data['dept'];
        // $region     = $data['region'];
        // $district   = $data['district'];
        // $user       = $data['user'];
        $touserdata  = $data['touser'];
        // $transtype  = $data['transtype'];

        $othertransid = $id;

        // print_r($othertrans);
        // print_r($touserdata);
        // print_r($othertransid);

        // print_r($inoutstatus);
        // print_r($pendingdel);

        // exit;



        return view('transactionflow.datatransfer', compact('othertrans', 'touserdata',  'othertransid', 'inoutstatus', 'pendingdel', 'othermembers', 'otherteamhead'));
    }



    public function getothermembers($auditscheduleids)
    {
        $scheduleid = $auditscheduleids;

        // Call your model method to get the data
        $getdata = TransactionFlowModel::getothermembers($scheduleid);
        return $getdata;
    }


    public function leavetype_dropdownvalues(Request $request)
    {
        $leavetype_det = DB::table(self::$leavetype_table)
            ->where('statusflag', 'Y')
            ->get();
        $userSessionChargeData = session('charge');
        $userrolemappingid =  $userSessionChargeData->rolemappingid;
        // $roleactioncode =   TransactionFlowModel::getroleactioncode($userrolemappingid);

        $session_user = session('user');
        $encryptedUserId = Crypt::encrypt($session_user->userid);

        $holidayDates = TransactionFlowModel::getholidaydates()
            ->pluck('holiday_date')
            ->map(fn($date) => \Carbon\Carbon::parse($date)->format('Y-m-d'))
            ->toArray();

        return view('transactionflow.leaveform', compact('leavetype_det', 'encryptedUserId', 'holidayDates'));
    }

    // public function storeOrUpdateLeave(Request $request)
    // {

    //     $userSessionData = session('user');
    //     $userid = $userSessionData->userid;

    //     $from_date = Carbon::createFromFormat('d/m/Y', $request->input('from_date'))->format('Y-m-d');
    //     $to_date = Carbon::createFromFormat('d/m/Y', $request->input('to_date'))->format('Y-m-d');


    //     $request->merge(['from_date' => $from_date]);
    //     $request->merge(['to_date'   => $to_date]);

    //     $request->validate([
    //         'from_date'     =>  'required|date|date_format:Y-m-d|',
    //         'to_date'       =>  'required|date|date_format:Y-m-d|',
    //         'leave_type'    =>  'required',
    //         'reason'        =>  'required',
    //     ], [
    //         'required' => 'The :attribute field is required.',
    //         'from_date.date' => 'The from date must be a date',
    //         'to_date.date' => 'The to date must be a date',
    //         'from_date.date_format:Y-m-d' => 'The from date must be in the format of Y-m-d',
    //         'to_date.date_format:Y-m-d' => 'The to date must be in the format of Y-m-d',
    //     ]);
    //     // $scheduleid  = TransactionFlowModel::getScheduleid($userid);
    //     // $auditscheduleid = $scheduleid[0]->auditscheduleid ?? null;

    //     // return $auditscheduleid;
    //     $data = [
    //         'userid'            => $userid,
    //         'fromdate'          => $request->input('from_date'),
    //         'todate'            => $request->input('to_date'),
    //         'leavetypecode'     => $request->input('leave_type'),
    //         'reason'            => $request->input('reason'),
    //         'statusflag'        => $request->input('finaliseflag'),
    //         'createdon'         => View::shared('get_nowtime'),
    //         'updatedon'         => View::shared('get_nowtime'),
    //         'createdby'         => $userid,
    //         'updatedby'         => $userid,
    //         'processcode'       => View::shared('Insert'),
    //         'transtypecode'     => View::shared('Leavetransactiontypecode'),
    //         // 'auditscheduleid'   =>  $auditscheduleid

    //     ];

    //     if ($request->action == 'update') {

    //         $leave_id = Crypt::decryptString($request->input('leave_id'));
    //         unset($all->leave_id);
    //     } else
    //         $leave_id =   null;

    //     try {

    //         $leavedetail = TransactionFlowModel::createleave_insertupdate($data, $leave_id, 'audit.ind_leavedetail', $userid,'form');

    //         return response()->json(['success' => 'Leave application detail was created/updated successfully', 'Leave Detail' => $leavedetail]);
    //     } catch (\Exception $e) {
    //         // Catch the exception thrown by the model and return the error message
    //         return response()->json(['error' => $e->getMessage()], 400);
    //     }
    // }
    public function storeOrUpdateLeave(Request $request)
    {
        try {
            $userSessionData = session('user');
            $userid = $userSessionData->userid;

            $chargedel = session('charge');
            $userchargeid = $chargedel->userchargeid;

            $from_date = Carbon::createFromFormat('d/m/Y', $request->input('from_date'))->format('Y-m-d');
            $to_date = Carbon::createFromFormat('d/m/Y', $request->input('to_date'))->format('Y-m-d');

            $request->merge(['from_date' => $from_date, 'to_date' => $to_date]);

            $request->validate([
                'from_date' => 'required|date|date_format:Y-m-d',
                'to_date' => 'required|date|date_format:Y-m-d',
                'leave_type' => 'required',
                'reason' => 'required',
            ], [
                'required' => 'The :attribute field is required.',
                'from_date.date' => 'The from date must be a valid date.',
                'to_date.date' => 'The to date must be a valid date.',
                'from_date.date_format' => 'The from date must be in the format Y-m-d.',
                'to_date.date_format' => 'The to date must be in the format Y-m-d.',
            ]);

            $data = [
                'userid' => $userid,
                'fromdate' => $request->input('from_date'),
                'todate' => $request->input('to_date'),
                'leavetypecode' => $request->input('leave_type'),
                'reason' => $request->input('reason'),
                'statusflag' => $request->input('finaliseflag'),

                'updatedon' => View::shared('get_nowtime'),

                'updatedby' => $userid,
                'updatedbyuserchargeid' => $userchargeid,
                'processcode' => View::shared('Insert'),
                'transactiontypecode' => View::shared('Leavetransactiontypecode'),
            ];

            if ($request->action == 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdbyuserchargeid'] = $userchargeid;
                $data['createdby'] = $userid;
            }




            $leave_id = $request->action === 'update'
                ? Crypt::decryptString($request->input('leave_id'))
                : null;

            // Call the model function
            $result = TransactionFlowModel::createleave_insertupdate($data, $leave_id, 'audit.ind_leavedetail', $userid, 'form');

            // Handle result
            if (in_array($result['status'], ['inserted', 'updated'])) {
                return response()->json([
                    // 'success' => true,
                    'status' => 'success',
                    'message' => 'Leave application was ' . $result['status'] . ' successfully.',
                    'data' => $result['data']
                ]);
            } elseif ($result['status'] === 'failed') {
                return response()->json([
                    // 'success' => false,
                    'status' => 'error',
                    'message' => $result['message']
                ], 400);
            } else {
                return response()->json([
                    // 'success' => false,
                    'status' => 'error',
                    'message' => $result['message'] ?? 'Unexpected error occurred.'
                ], 500);
            }
        } catch (\Exception $e) {
            // Log the error if needed (optional)
            // Log::error('Leave Store/Update Error: ' . $e->getMessage());

            return response()->json([
                // 'success' => false,
                'status' => 'error',
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchall_leavedata()
    {
        $userSessionData = session('user');
        if (!$userSessionData || !isset($userSessionData->userid)) {
            return redirect()->back()->withErrors(['Session expired or data missing.']);
        }
        $userid = $userSessionData->userid;
        $leavedetail = TransactionFlowModel::fetchalldata($userid);


        foreach ($leavedetail as $item) {
            $item->encrypted_leaveid = Crypt::encryptString($item->leaveid);
            $item->transactiontypecode = View::shared('Leavetransactiontypecode');
            unset($item->leaveid);
        }


        if ($leavedetail) {
            return response()->json(['success' => true, 'data' => $leavedetail]);
        } else {
            return response()->json(['success' => false, 'message' => 'Leave Details was not found'], 404);
        }
    }



    public function fetchsingle_data(Request $request)
    {
        $leaveid = Crypt::decryptString($request->leaveid);

        try {
            // Call the model method for create or update
            $single_leavedetail = TransactionFlowModel::fetchsingle_data($leaveid, 'audit.ind_leavedetail');

            foreach ($single_leavedetail as $item) {
                $item->encrypted_leaveid = Crypt::encryptString($item->leaveid);
            }


            if ($single_leavedetail) {
                return response()->json(['success' => true, 'data' => $single_leavedetail]);
            } else {
                return response()->json(['success' => false, 'message' => 'Leave Detail was not found'], 404);
            }
        } catch (\Exception $e) {
            // Catch the exception thrown by the model and return the error message
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


    public function getinstitutiondel(Request $request)
    {
        $auditscheduleid =  $request->auditscheduleid;
        $data =  TransactionFlowModel::getinstitutiondel($auditscheduleid);
        // print_r($data);
        // exit;
        return response()->json(['success' => true, 'data' => $data]);
    }



    // public function insert_datatransfer(Request $request)
    // {
    //     print_r($_REQUEST);
    //     $sessionchargedel = session('charge');
    //     $sessionuserdel = session('user');
    //     $userchargeid = $sessionchargedel->userchargeid;
    //     $userid = $sessionuserdel->userid;



    //     // $request = [
    //     //     'othertransid' => $othertransid,
    //     //     'userid' => $userid,
    //     //     'auditscheduleid' => $auditscheduleid,
    //     //     'datatransfercode' => $datatransfercode,
    //     //     'transuser' =>$transuser,
    //     //     'plandatatransfercode' => $plandatatransfercode,
    //     //     'plantransuser' => $plantransuser,
    //     //     'action' => 'insert'
    //     // ];

    //     $data =  TransactionFlowModel::insert_datatransfer($request, $userid,$userchargeid);

    //     return response()->json(['success' => true, 'data' => $data]);





    //     // $auditscheduleid =  $request->auditscheduleid;
    //     // $schememberid =  $request->schememberid;
    //     // $data =  TransactionFlowModel::getworkalloactionbasedonSchedulemember($auditscheduleid, $schememberid);

    //     // return response()->json(['success' => true, 'data' => $data]);

    // }


    public function insert_datatransfer(Request $request)
    {
        try {
            $sessionchargedel = session('charge');
            $sessionuserdel = session('user');

            if (!$sessionchargedel || !$sessionuserdel) {
                throw new \Exception("Session expired. Please log in again.");
            }

            $userchargeid = $sessionchargedel->userchargeid;
            $userid = $sessionuserdel->userid;

            $data = TransactionFlowModel::insert_datatransfer($request, $userid, $userchargeid);

            if ($data['status'] === 'success') {
                return response()->json(['success' => true, 'message' => 'Transfer completed successfully']);
            } else {
                return response()->json(['success' => false, 'error' => $data['message']], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }




    public function getworkalloactionbasedonSchedulemember(Request $request)
    {
        $auditscheduleid =  $request->auditscheduleid;
        $schememberid =  $request->schememberid;
        $data =  TransactionFlowModel::getworkalloactionbasedonSchedulemember($auditscheduleid, $schememberid);

        return response()->json(['success' => true, 'data' => $data]);
    }

    public  function getslipdetailsbasedon_schedulemember(Request $request)
    {

        $auditscheduleid =  $request->auditscheduleid;
        $schememberid =  $request->schememberid;
        $data =  TransactionFlowModel::getslipdetailsbasedon_schedulemember($auditscheduleid, $schememberid);

        return response()->json(['success' => true, 'data' => $data]);
    }











    // public function forward_application(Request $request)
    // {
    //     $userSessionData = session('user');
    //     $userSessionChargeData = session('charge');
    //     if (!$userSessionData || !isset($userSessionData->userid)) {
    //         return redirect()->back()->withErrors(['Session expired or data missing.']);
    //     }
    //     $sessionchargeid = $userSessionChargeData->userchargeid;
    //     $userid = $userSessionData->userid;


    //     $detailuserid               = $request->userid;
    //     $transtypecode              = $request->transactiontypecode;
    //     $action                     = $request->action;
    //     $id                         =   $request->id;

    //     $forwarddel =   TransactionFlowModel::forwardtonextlevel( $transtypecode ,$forwardedtouser, $action );

    //     if(count($forwarddel) == 1)
    //     {
    //         $forwardtouserchargeid = $forwarddel[0]->userchargeid;

    //         // $leaveid = ($request->leaveid);
    //         $trans_action = $request->action;

    //         // return  $transtypecode;
    //         if ($trans_action == 'Approve') {
    //             $process = View::shared('Approve');
    //         } else {
    //             $process = View::shared('Forward');
    //         }




    //         // if ($request->remarks) {

    //         //     $remarks = $request->remarks;
    //         // } else {
    //         //     $remarks = '';
    //         // }




    //         $transactiondel_data = [
    //             'forwardedtouserchargeid' =>  $forwardedtouserchargeid,
    //             'updatedbyuserchargeid' => $userid,
    //             'updatedon' => View::shared('get_nowtime'),
    //         ];

    //         if($action == 'first')
    //         {
    //             $transactiondel_data['userid'] =   $detailuserid;
    //             $transactiondel_data['transactiontypecode'] =   $transtypecode;
    //             $transactiondel_data['createdbyuserchargeid'] =   $userid;
    //             $transactiondel_data['createdon'] =   $detacreatedoniluserid;
    //             $transactiondel_data['statusflag'] =   'Y';

    //             if ($transtypecode == View::shared('Leavetransactiontypecode')) {
    //                 $transactiondel_data['leaveid'] = $id ;
    //                 $where = ['leaveid' => $id ];
    //             }
    //             else
    //             {
    //                 $transactiondel_data['othertransid'] = $id ;
    //                 $where = ['othertransid' => $id ];

    //             }
    //         }




    //         $udpate_maintable = [
    //             'processcode'   => $process,
    //             'updatedby'     =>  $userid,
    //             'updatedon'     =>  View::shared('get_nowtime'),
    //         ];


    //         $historytransaction_data = [
    //             'userid'    =>   $detailuserid,
    //             'transactiontypecode' => $transtypecode,
    //             'processcode'   => $process,
    //             'forwardedtouserchargeid' =>  $forwardedtouserchargeid,
    //             'forwardedbyuserchargeid' => $userid,
    //             'forwardedon' => View::shared('get_nowtime'),
    //             'statusflag' => 'Y',
    //             'transstatus'   =>  'A'
    //         ];


    //         $historytransid = $this->insert_historyTransDetail($data,$where);
    //         if($historytransid)
    //         {
    //             $transdetailid = TransactionFlowModel::insertupdate_transdet($data,$where);
    //             if($transdetailid)
    //             {
    //                 if ($transtypecode == View::shared('Leavetransactiontypecode')) {
    //                     TransactionFlowModel::leavetableupdation($udpate_maintable,$id);
    //                 }
    //                 else
    //                 {
    //                     TransactionFlowModel::insertorUpdateOthertrans($udpate_maintable,$id);
    //                 }
    //             }
    //         }

    //     }
    //     elseif(count($forwarddel) == 0)
    //     {

    //     }
    //     else
    //     {

    //     }




    // }





    // public function insert_historyTransDetail($data)
    // {

    //     $transtypecode = $data['transactiontypecode'];

    //     if ($transtypecode == View::shared('Leavetransactiontypecode')) {

    //         $leaveid = $data['leaveid'];
    //         if ($leaveid) {
    //             $exists = DB::table('audit.historytransactions')->where('leaveid', $leaveid)->exists();
    //             if ($exists) {
    //                 $transstatus = ['transstatus' => 'I'];
    //                 DB::table('audit.historytransactions')->where('leaveid', $leaveid)->update($transstatus);
    //             }
    //             DB::table('audit.historytransactions')->insert($data);
    //             if ($data['processcode'] == View::shared('Reject')) {
    //                 return response()->json(['success' => ' Application was rejected ']);
    //             } else if ($data['processcode'] == View::shared('Approve')) {
    //                 return response()->json(['success' => 'Application was Approved']);
    //             } else {
    //                 return response()->json(['success' => 'Application was forwarded successfully  ']);
    //             }
    //         }
    //     } else {

    //         $userdetail_transid = $data['othertransid'];
    //         if ($userdetail_transid) {
    //             $exists = DB::table('audit.historytransactions')->where('othertransid', $userdetail_transid)->exists();

    //             if ($exists) {

    //                 $transstatus = ['transstatus' => View::shared('inactive')];
    //                 DB::table('audit.historytransactions')->where('othertransid', $userdetail_transid)->update($transstatus);
    //             }

    //             DB::table('audit.historytransactions')->insert($data);
    //             if ($data['processcode'] == View::shared('Reject')) {
    //                 return response()->json(['success' => 'Leave application was rejected ']);
    //             } else if ($data['processcode'] == View::shared('Approve')) {
    //                 return response()->json(['success' => 'Leave application was Approved']);
    //             } else {
    //                 return response()->json(['success' => 'Record has been forwarded successfully  ']);
    //             }
    //         }
    //     }
    // }


    public function transactionapproveddetails(Request $request)
    {

        $session_user = session('user');
        $session_charge = session('charge');
        $deptcode = $session_charge->deptcode;
        $regioncode = $session_charge->regioncode;
        $distcode = $session_charge->distcode;


        $getapproveddetails = TransactionFlowModel::getapproveddetails($deptcode, $regioncode, $distcode);
        // print_r($getapproveddetails);
        // exit;

        return view('transactionflow.approveddetails', compact('getapproveddetails'));
    }


    public function viewdatatransferdel(Request $request)
    {
        $othertransid = $request->othertransid;
        $transactiontypecode = $request->transactiontypecode;

        $data = TransactionFlowModel::getdatatransferdel($othertransid, $transactiontypecode);

        return response()->json([
            'result1' => $data['query1'],
            'result2' => $data['query2'],
            'result3' => $data['query3']
        ]);
    }
}
