<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\BaseModel;


use App\Models\LagacyModel;

use Illuminate\Http\Request;
// use DB;
use App\Services\FileUploadService;

class LagacyController extends Controller
{
    protected static $mstauditeeinscategory_table = BaseModel::MSTAUDITEEINSCATEGORY_TABLE;

    protected $fileUploadService;

    // Inject the FileUploadService
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }
    public function followup_dropdown(Request $request)
    {
        $instid = $request->query('inst');
        $catcode = $request->query('catcode');
        $subcatid = $request->query('subcatid');

        $data = LagacyModel::followup_dropdown($instid, $catcode, $subcatid);

        $instData         = $data['inst']->first();
        $catData          = $data['catDet']->first();
        $typeofauditData  = $data['typeofaudit'];
        $objectionData    = $data['objection'];

        $severities = [
            'L' => ['en' => 'Low', 'ta' => 'குறைந்த'],
            'M' => ['en' => 'Medium', 'ta' => 'மாதிரி'],
            'H' => ['en' => 'High', 'ta' => 'உயர்ந்த'],
        ];

        return view('lagacy.followup', compact('instData', 'catData', 'typeofauditData', 'objectionData', 'severities'));
    }

    public function getminordet(Request $request)
    {
        $request->validate([
            'mainobjectionid'       => 'required',
        ], [
            'required' => 'The :attribute field is required.',
        ]);
        if ($request->mainobjectionid) {
            $minorobjectionData = LagacyModel::getminorobjection($request->mainobjectionid);
            return response()->json(['minorobjectionData' => $minorobjectionData]);
        }
    }


    public function followup_insert(Request $request)
    {
        $data = $request->all();
        $userSessionData = session('user');
        $userid = $userSessionData->userid;
        $action = $request->input('action');

        $request->validate([
            'typeofauditcode'          => 'required|string',
            'mainobjectionid'          => 'required|integer',
            'subobjectionid'           => 'required|integer',
            'amount_involved'          => 'required|integer',
            'severityid'               => 'required|max:1|string',
        ]);
        $content = json_encode(['content' => $request->input('remarks')]);

        $data = [
            'instid'              => $request->instid,
            'catcode'             => $request->catcode,
            'typeofauditcode'     => $request->typeofauditcode,
            'mainobjectionid'     => $request->mainobjectionid,
            'subobjectionid'      => $request->subobjectionid,
            'amtinvolved'         => $request->amount_involved,
            'slipdetails'         => $request->slipdetails,
            'severity'            => $request->severityid,
            'liability'           => $request->severityid,
            'remarks'             => $content,

        ];

        $uploadid = $request->input('uploadid');


        if ((($action === 'insert') || ($action === 'update')) && ($request->hasFile('file_upload'))) {
            $destinationPath = 'uploads/lagacy';

            if ($uploadid) {

                $uploadResult = $this->fileUploadService->uploadFile($request->file('file_upload'), $destinationPath, $uploadid);
            } else {

                $uploadResult = $this->fileUploadService->uploadFile($request->file('file_upload'), $destinationPath, '');
            }

            $fileuploadId = $uploadResult->getData()->fileupload_id ?? null;
            $data['uploadid'] = $fileuploadId;
        }

        $lagacyid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('lagacyid')) : null;


        if ($request->input('action') === 'insert') {
            $data['createdon'] =  View::shared('get_nowtime');
            $data['createdby'] =  $userid;
        }

        if ($request->input('action') === 'update') {
            $data['updatedon'] =  View::shared('get_nowtime');
            $data['updatedby'] =  $userid;
        }


        $lagacydet = LagacyModel::createorinsertLagacydet($data, $lagacyid);
        return $data;
    }


    public static function fetch_lagacydata(Request $request)
    {
        try {
            $lagacyid = $request->filled('lagacyid') ? Crypt::decryptString($request->lagacyid) : null;

            $lagacyDet = LagacyModel::fetch_lagacydata($lagacyid);
            // return  $mapInstDet;
            foreach ($lagacyDet as $all) {
                $all->encrypted_lagacyid = Crypt::encryptString($all->lagacyid);
                unset($all->lagacyid);
            }
            if ($lagacyid) {
                if ($lagacyDet->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Mapping Details not found not found',
                        'data' => null
                    ], 404);
                }

                // Encrypt user IDs in results
                $lagacyDet->transform(function ($all) {
                    $all->encrypted_instid = Crypt::encryptString($all->instid);
                    unset($all->lagacyid);
                    return $all;
                });

                return response()->json([
                    'success' => true,
                    'message' => '',
                    'data' => $lagacyDet
                ], 200);
            }


            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $lagacyDet->isEmpty() ? null : $lagacyDet
            ], 200);



            // Return data in JSON format
            // return response()->json($allMapallocationobjectionDet);
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid  ID provided'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching user data'
            ], 500);
        }
    }
}
