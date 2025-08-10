<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

use App\Models\ReportModel;
use App\Models\FieldAuditModel;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function pendingparra()
    {
        $sessionuserdel    =    session('user');
        $sessionuserid    =    $sessionuserdel->userid;
        $results         =   ReportModel::fetchpendingparas($sessionuserid);

        foreach ($results as $all) {
            $all->encrypted_auditscheduleid = Crypt::encryptString($all->auditscheduleid);
            $all->formatted_fromdate = Controller::ChangeDateFormat($all->fromdate);
            $all->formatted_todate = Controller::ChangeDateFormat($all->todate);
        }

        return view('fieldaudit.pendingpara', compact('results'));
    }

    public function getpendingparadetails(Request $request)
    {
        $auditscheduleid = Crypt::decryptString($request->auditscheduleid);
        $quartercode = $request->quartercode;
        $slipsts = $request->slipsts;
        $filterapply = $request->filterapply;
	$quarter = $request->input('quarter'); 
        // Fetch details
        $alldetails = ReportModel::getpendingparadetails($auditscheduleid, $quartercode, $slipsts, $filterapply,$quarter);
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

    public function getSlipDetailsHistory($slipId)
    {
        $slipId = Crypt::decryptString($slipId);
        return ReportModel::getSlipDetailsHistory($slipId);
    }

    public function getSlipHistoryDetails($slipId)
    {
        $slipId = Crypt::decryptString($slipId);
        return ReportModel::getSlipHistoryDetails($slipId);
    }
}
