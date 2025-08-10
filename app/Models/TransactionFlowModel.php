<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BaseModel;
use Illuminate\Support\Facades\View;
use InvalidArgumentException;



class TransactionFlowModel extends Model
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
    protected static $rolemapping_table = BaseModel::ROLEMAPPING_TABLE;
    protected static $chargedetail_table = BaseModel::CHARGEDETAIL_TABLE;
    protected static $transactionflow_table = BaseModel::TRANSACTIONFLOW_TABLE;
    protected static $userchargedetail_table = BaseModel::USERCHARGEDETAIL_TABLE;
    protected static $roleaction_table = BaseModel::ROLEACTION_TABLE;
    protected static $leavetype_table = BaseModel::LEAVETYPE_TABLE;

    protected static $transactiondetail_table = BaseModel::TRANSACTIONDETAILTABLE;

    protected static $historytransaction_table = BaseModel::HISTORYTRANSACTION_TABLE;
    protected static $indleavedetail_table = BaseModel::INDLEAVEDETAIL_TABLE;
    protected static $instschedule_table = BaseModel::INSTSCHEDULE_TABLE;
    protected static $instschedulemem_table = BaseModel::INSTSCHEDULEMEM_TABLE;
    protected static $auditplan_table = BaseModel::AUDITPLAN_TABLE;
    protected static $instituiion_table = BaseModel::INSTITUTION_TABLE;


    protected static $dataTransferFromTofun_table = BaseModel::DATATRANSFERFROMTOUSER;
    protected static $migrateallocationslip_table = BaseModel::WORKALLOCATIONDISTRIBUTION;
    protected static $auditor_instmapping_table = BaseModel::AUDITOR_INSTMAPPING_TABLE;
    protected static $nodatachange_fun = BaseModel::NODATACHANGE_FUNCTION;



    public static function getdeptbasedonsession()
    {
        $userData = session('charge');
        $session_deptcode = $userData->deptcode ?? null;

        $query = DB::table(self::$department_table)
            ->where('statusflag', 'Y');

        if ($session_deptcode) {
            $query->where('deptcode', $session_deptcode);
        }

        $query->orderBy('orderid', 'asc');

        $departments = $query->get();

        return $departments;
    }


    public static function getdeptbased_desig(string $deptcode, $instid, $for)
    {

        if (empty($deptcode)) {
            throw new InvalidArgumentException("Invalid arguments provided.");
        }

        $query = DB::table('audit.userchargedetails as uc')
            ->join('audit.chargedetails as ch', 'ch.chargeid', '=', 'uc.chargeid')
            ->join('audit.mst_designation as des', 'des.desigcode', '=', 'ch.desigcode')
            ->select('ch.desigcode', 'des.desigelname', 'des.desigtlname')
            ->where('uc.statusflag', 'Y')
            ->where('ch.deptcode', $deptcode);

        if ($instid) {
            $query->where('ch.instmappingcode', $instid);
        }

        if ($for  == 'desig') {
            $userData = session('charge');
            $session_roletypecode = $userData->roletypecode ?? null;
            if ($session_roletypecode)
                $query->where('des.roletypecode', $session_roletypecode);

            //       $querySql = $query->toSql();
            // $bindings = $query->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            // print_r($finalQuery);
            // exit;

        }

        $query->groupBy('ch.desigcode', 'des.desigelname', 'des.desigtlname');

        $result = $query->get();
        return $result;
    }

    public static function desigbaseduser($table, $paramcheck)
    {
        $distcode = $paramcheck['distcode'] ?? NULL;


        $timestamp = strtotime('first day of last month');
        $CurrYear = date('Y', $timestamp);
        $previousMonth =  date('m', $timestamp);

        $result = DB::table(self::$userdet_table . ' as dp')
            ->join(self::$userchargedetail_table . ' as uc', 'uc.userid', '=', 'dp.deptuserid')
            ->join(self::$chargedetail_table . ' as c', 'c.chargeid', '=', 'uc.chargeid')
            ->join(self::$rolemapping_table . ' as r', 'r.rolemappingid', '=', 'c.rolemappingid')
            ->where('dp.deptcode',  $paramcheck['deptcode'])
            ->where('dp.desigcode', $paramcheck['desigcode'])
            ->when(
                empty($paramcheck['distcode']) || $paramcheck['distcode'] === 'null',
                function ($query) {
                    return $query->whereNull('dp.distcode');
                },
                function ($query) use ($paramcheck) {
                    return $query->where('dp.distcode', $paramcheck['distcode']);
                }
            )
            ->when(
                isset($paramcheck['transtype']) && $paramcheck['transtype'] === 'superannuation',
                function ($query) use ($previousMonth, $CurrYear) {
                    return $query
                        ->whereYear('dp.dor',  $CurrYear)
                        ->whereMonth('dp.dor', $previousMonth);
                }
            )
            ->where('uc.statusflag', 'Y')
            ->where('dp.statusflag', 'Y')
            ->where('dp.reservelist', 'Y')
            ->select('dp.username', 'dp.usertamilname', 'dp.deptuserid', 'dp.dor')
            ->distinct()
            ->get();

        return $result;
    }







    public static function regionbaseddist($table, $regioncode, $deptcode)
    {
        $del =    DB::table($table . ' as dist')
            ->join(self::$auditor_instmapping_table . ' as inst', 'inst.distcode', '=', 'dist.distcode')
            ->where('inst.regioncode', $regioncode)
            ->where('inst.deptcode', $deptcode)
            ->select('dist.distcode', 'dist.distename', 'dist.disttname',)
            ->where('dist.statusflag', 'Y')
            ->distinct()
            ->get();
        return $del;
    }
    public static function deptbasedregion($table, $deptcode)
    {
        $del = DB::table($table . ' as rtm')
            ->join(self::$auditor_instmapping_table . ' as inst', 'inst.deptcode', '=', 'rtm.deptcode')
            ->join(self::$region_table . ' as rt', 'rt.deptcode', '=', 'rtm.parentcode')
            ->join(self::$department_table . ' as md', 'md.deptcode', '=', 'rtm.parentcode')
            ->where('rtm.deptcode', $deptcode)
            ->where('inst.statusflag', 'Y')
            ->where('rt.statusflag', 'Y')
            // ->where('rtm.roletypecode', $request->roletypecode)
            ->select('md.deptcode', 'rt.regionename', 'rt.regiontname', 'rt.regioncode')
            ->distinct()
            ->get();

        return $del;
    }

    public static function deptbaseddesignation($table, $deptcode)
    {
        $del =    DB::table($table . ' as desig')
            ->join(self::$department_table . ' as md', 'md.deptcode', '=', 'desig.deptcode')
            ->where('desig.deptcode', $deptcode)
            ->select('md.deptcode', 'desig.desigelname', 'desig.desigtlname', 'desig.desigcode', 'desig.orderid')
            ->where('desig.statusflag', 'Y')
            ->orderby('desig.orderid', 'ASC')
            ->distinct()
            ->get();
        return $del;
    }

    public static function getdata_regdistinst(string $deptcode, ?string $regioncode = null, ?string $distcode = null, string $getval, string $roletypecode)
    {
        $userData = session('charge');
        $session_regioncode = $userData->regioncode ?? null;
        $session_distcode = $userData->distcode ?? null;


        if (empty($deptcode) || empty($getval)) {
            throw new InvalidArgumentException("Invalid arguments provided.");
        }
        $query = DB::table(self::$auditor_instmapping_table . ' as instm')
            ->where('instm.statusflag', 'Y')
            ->where('instm.deptcode', $deptcode);
        switch ($getval) {
            case 'region':
                $query->join(self::$region_table . ' as re', 're.regioncode', '=', "instm.regioncode")
                    ->select("instm.regioncode", 're.regionename', 're.regiontname')
                    ->distinct();
                if ($session_regioncode) {
                    $query->where("instm.regioncode", $session_regioncode);
                }
                $query->orderBy('re.regionename', 'ASC');
                break;
            case 'district':
                $query->join(self::$region_table . ' as re', 're.regioncode', '=', "instm.regioncode")
                    ->join(self::$district_table . ' as d', 'd.distcode', '=', "instm.distcode")

                    ->select("instm.distcode", 'd.distename', 'd.disttname')
                    ->distinct();
                $query->where("instm.regioncode", $regioncode);
                if ($session_distcode) {
                    $query->where("instm.distcode", $session_distcode);
                }
                $query->orderBy("d.distename", 'ASC');
                break;
            case 'institution':
                $query->join(self::$region_table . ' as re', 're.regioncode', '=', "instm.regioncode")
                    ->select("instm.instmappingid", "instm.instename", "instm.instmappingcode", "instm.insttname");
                $query->where("instm.regioncode", $regioncode);
                if ($roletypecode == '01') {
                    $query->join(self::$district_table . ' as d', 'd.distcode', '=', "instm.distcode")
                        ->where("instm.distcode", $distcode);
                }
                $query->orderBy("instm.instename", 'ASC');
                break;

            default:
                throw new InvalidArgumentException("Invalid 'getval' provided. Allowed values are 'region', 'district', or 'institution'.");
        }
        return $query->get();
    }


    public static function getdataforToInst(string $deptcode, ?string $regioncode = null, ?string $distcode = null, string $getval, ?string $fromdistcode = null, string $roletypecode)
    {
        if (empty($deptcode) || empty($getval)) {
            throw new InvalidArgumentException("Invalid arguments provided.");
        }
        $query = DB::table(self::$auditor_instmapping_table . ' as instm')
            ->where('instm.statusflag', 'Y')
            ->where('instm.deptcode', $deptcode);
        switch ($getval) {
            case 'region':
                $query->join(self::$region_table . ' as re', 're.regioncode', '=', "instm.regioncode")
                    ->select("instm.regioncode", 're.regionename', 're.regiontname')
                    ->distinct();
                $query->orderBy('re.regionename', 'ASC');
                break;
            case 'district':
                $query->join(self::$region_table . ' as re', 're.regioncode', '=', "instm.regioncode")
                    ->join(self::$district_table . ' as d', 'd.distcode', '=', "instm.distcode")

                    ->select("instm.distcode", 'd.distename', 'd.disttname')
                    ->distinct();
                $query->where("instm.regioncode", $regioncode);
                if (!empty($fromdistcode)) {
                    $query->whereNot("d.distcode", $fromdistcode);
                }
                $query->orderBy("d.distename", 'ASC');
                break;

            case 'institution':
                $query->join(self::$region_table . ' as re', 're.regioncode', '=', "instm.regioncode")
                    ->select("instm.instmappingid", "instm.instename", "instm.instmappingcode", "instm.insttname");
                $query->where("instm.regioncode", $regioncode);
                //}
                if ($roletypecode == '01') {
                    $query->join(self::$district_table . ' as d', 'd.distcode', '=', "instm.distcode")
                        ->where("instm.distcode", $distcode);
                }
                $query->orderBy("instm.instename", 'ASC');
                break;

            default:
                throw new InvalidArgumentException("Invalid 'getval' provided. Allowed values are 'region', 'district', or 'institution'.");
        }
        return $query->get();
    }





    /*********************************************************************** Other Transaction Form *************************************************************/

    public static function fetchothertransdel($othertransid)
    {
        $userData = session('user');
        $session_userid = $userData->userid;

        $chargeData = session('charge');
        $session_userchargeid = $chargeData->userchargeid;



        $query = DB::table(self::$othertrans_table . ' as other')
            ->join(self::$transtype_table . ' as transtype', 'transtype.transactiontypecode', '=', 'other.transactiontypecode')
            ->leftJoin(self::$auditor_instmapping_table . ' as instmap', 'instmap.instmappingcode', '=', 'other.toinstmappingcode')
            ->Join(self::$auditor_instmapping_table . ' as frominstmap', 'frominstmap.instmappingcode', '=', 'other.frominstmappingcode')
            ->join(self::$userdet_table . ' as user', 'user.deptuserid', '=', 'other.userid')
            ->join(self::$department_table . ' as dept', 'dept.deptcode', '=', 'frominstmap.deptcode')
            ->join(self::$designation_table . ' as desig', 'desig.desigcode', '=', 'user.desigcode')
            ->join(self::$region_table . ' as region', 'region.regioncode', '=', 'frominstmap.regioncode')
            ->join(self::$district_table . ' as dist', 'dist.distcode', '=', 'frominstmap.distcode')
            ->leftjoin(self::$department_table . ' as todept', 'todept.deptcode', '=', 'instmap.deptcode')
            ->leftJoin(self::$region_table . ' as toregion', 'toregion.regioncode', '=', 'instmap.regioncode')
            ->leftJoin(self::$district_table . ' as todist', 'todist.distcode', '=', 'instmap.distcode')
            ->join(self::$roletype . ' as roletype', 'roletype.roletypecode', '=', 'frominstmap.roletypecode')
            ->join('audit.fileuploaddetail as fu', 'fu.fileuploadid', '=', 'other.uploadid')
            ->select(
                'instmap.instmappingcode',
                'frominstmap.roletypecode',
                'instmap.instename',
                'instmap.insttname',
                'frominstmap.instmappingcode as frominstmapcode',
                'instmap.deptcode as dev_deptcode',
                'instmap.regioncode as div_region',
                'instmap.distcode as div_dist',
                DB::raw("
            CASE
                WHEN other.uploadid != 0 THEN CONCAT(fu.filename, '-', fu.filepath, '-', fu.filesize, '-', fu.fileuploadid)
                ELSE '-'
            END AS filedetails
        "),
                'other.userid',
                'frominstmap.instename as from_instename',
                'frominstmap.insttname as from_insttname',
                'frominstmap.deptcode',
                'frominstmap.regioncode',
                'frominstmap.distcode',
                'desig.desigcode',
                'desig.desigelname',
                'desig.desigtlname',
                'dist.distename',
                'dist.disttname',
                'region.regionename',
                'region.regiontname',
                'todist.distename as to_distename',
                'todist.disttname as to_disttname',
                'toregion.regionename as to_regionename',
                'toregion.regiontname as to_regiontname',
                'other.frominstmappingcode',
                'other.fromdesigcode',
                'other.todesigcode',
                'other.othertransid',
                'other.orderdate',
                'other.userid',
                'other.toinstmappingcode',
                'other.processcode',
                'other.orderno',
                'transtype.transactiontypelname',
                'transtype.transactiontypecode',
                'user.username',
                'user.deptuserid',
                'user.dob',
                'user.dor',
                'user.ifhrmsno',
                'user.usertamilname',
                'user.dor',
                'dept.deptelname',
                'dept.depttlname',
                'dept.depttsname',
                'dept.deptesname',
                'todept.deptelname as div_deptelname',
                'todept.depttlname as div_depttlname',
                'todept.depttsname as div_depttsname',
                'todept.deptesname as div_deptesname'
            )
            ->distinct();

        $query->when($othertransid, function ($query) use ($othertransid) {
            $query->where('other.othertransid', '=', $othertransid);
        });
        $query->where('other.createdbyuserchargeid', '=', $session_userchargeid);
        return $query->get();
    }

    // public static function insertorUpdateOthertrans($table, $data, $sessionuserid, $othertransid)
    // {
    //     if ($othertransid) {

    //         DB::table($table)->where('othertransid', $othertransid)->update($data);
    //         return DB::table($table)->where('othertransid', $othertransid)->first();
    //     } else {

    //         $othertransid = DB::table($table)->insertGetId($data, 'othertransid');
    //         return  $othertransid;
    //     }
    // }

    // public static function insertorUpdateOthertrans($data, $othertransid)
    // {
    //     try {

    //         if ($othertransid) {
    //             $updated = DB::table(self::$othertrans_table)->where('othertransid', $othertransid)->update($data);

    //             if ($updated) {
    //                 $
    //
    //  = DB::table(self::$othertrans_table)->where('othertransid', $othertransid)->first();
    //                 return ['status' => 'updated', 'data' => $record];
    //             } else {
    //                 return ['status' => 'failed', 'message' => 'No rows updated'];
    //             }
    //         } else {
    //             $insertedId = DB::table(self::$othertrans_table)->insertGetId($data, 'othertransid');

    //             if ($insertedId) {
    //                 return ['status' => 'inserted', 'othertransid' => $insertedId];
    //             } else {
    //                 return ['status' => 'failed', 'message' => 'Insertion failed'];
    //             }
    //         }
    //     } catch (\Exception $e) {
    //         return ['status' => 'error', 'message' => $e->getMessage()];
    //     }
    // }


    // public static function insertorUpdateOthertrans($data, $othertransid)
    // {
    //     try {
    //         if ($othertransid) {
    //             // Attempt to update the record
    //             $updated = DB::table(self::$othertrans_table)->where('othertransid', $othertransid)->update($data);

    //             // Check if the update was successful (at least one row was affected)
    //             if ($updated > 0) {
    //                 // Fetch the updated record to return
    //                 $record = DB::table(self::$othertrans_table)->where('othertransid', $othertransid)->first();
    //                 return ['status' => 'updated', 'data' => $record];
    //             } else {
    //                 // No rows were updated (maybe no changes were made)
    //                 return ['status' => 'failed', 'message' => 'No rows updated (data might be the same)'];
    //             }
    //         } else {
    //             // Attempt to insert a new record
    //             $insertedId = DB::table(self::$othertrans_table)->insertGetId($data, 'othertransid');

    //             // Check if the insert was successful
    //             if ($insertedId) {
    //                 return ['status' => 'inserted', 'othertransid' => $insertedId];
    //             } else {
    //                 // Insertion failed
    //                 return ['status' => 'failed', 'message' => 'Insertion failed'];
    //             }
    //         }
    //     } catch (\Exception $e) {
    //         // Handle any exceptions that occur during the process
    //         return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    //     }
    // }

    // Method to insert or update other transactions

    public static function insertorUpdateOthertrans($data, $othertransid, $formname)
    {
        try {

            if ($formname != 'processtable') {
                // Create a copy of $data without 'updatedbyuserchargeid' and 'updatedon'
                $copiedData = $data;
                unset($copiedData['updatedbyuserchargeid'], $copiedData['updatedon'], $copiedData['uploadid'], $copiedData['createdbyuserchargeid'], $copiedData['createdon']);
            }

            if ($othertransid) {

                if ($formname != 'processtable') {
                    $checkexists = DB::table(self::$othertrans_table)
                        ->where('orderno', '=', $copiedData['orderno'])
                        ->where('othertransid', '!=', $othertransid)
                        ->exists();

                    if ($checkexists) {
                        return [
                            'status' => 'failed',
                            'message' => 'A record with the order number already exists.'
                        ];
                    }

                    // Check if a record already exists with the same data (excluding the current record)
                    $checkexists = DB::table(self::$othertrans_table)
                        ->where($copiedData)
                        ->where('othertransid', '!=', $othertransid)
                        ->exists();

                    if ($checkexists) {
                        return [
                            'status' => 'failed',
                            'message' => 'A record with the same data already exists .'
                        ];
                    }
                }

                // Update the record
                $updatedRows = DB::table(self::$othertrans_table)
                    ->where('othertransid', $othertransid)
                    ->update($data);

                if ($updatedRows > 0) {
                    $record = DB::table(self::$othertrans_table)
                        ->where('othertransid', $othertransid)
                        ->first();
                    return [
                        'status' => 'updated',
                        'data' => $record
                    ];
                } else {
                    return [
                        'status' => 'failed',
                        'message' => 'No rows updated. Data might be identical or update failed.'
                    ];
                }
            } else {
                // Additional check for 'Super Annuation' if transactiontypecode = 02
                if (($data['transactiontypecode'] === '02') || ($data['transactiontypecode'] === '03') || ($data['transactiontypecode'] === '04')) {
                    $checkexists = DB::table(self::$othertrans_table)
                        ->where('transactiontypecode', '02')
                        ->where('userid', $data['userid'])
                        ->exists();

                    if ($checkexists) {
                        return [
                            'status' => 'failed',
                            'message' => 'User already exists for Super Annuation.'
                        ];
                    }
                    $checkexists = DB::table(self::$othertrans_table)
                        ->where('transactiontypecode', '03')
                        ->where('userid', $data['userid'])
                        ->exists();

                    if ($checkexists) {
                        return [
                            'status' => 'failed',
                            'message' => 'User already exists for VRS.'
                        ];
                    }
                    $checkexists = DB::table(self::$othertrans_table)
                        ->where('transactiontypecode', '04')
                        ->where('userid', $data['userid'])
                        ->exists();

                    if ($checkexists) {
                        return [
                            'status' => 'failed',
                            'message' => 'User already exists for Death.'
                        ];
                    }
                }

                if ($data['transactiontypecode'] === '02') {
                    $getuserdel = DB::table('audit.deptuserdetails')
                        ->where('deptuserid', $data['userid'])
                        ->first();

                    if ($getuserdel) {
                        $dor = $getuserdel->dor;

                        // Extract the month and year from DOR
                        $dormonth = date('m', strtotime($dor));
                        $doryear = date('Y', strtotime($dor));

                        // Get the current month and year
                        $currentmonth = date('m');
                        $currentyear = date('Y');

                        // Calculate previous month and its year
                        $prevmonth = date('m', strtotime('-1 month'));
                        $prevyear = date('Y', strtotime('-1 month'));

                        // Check if DOR is not in the current or previous month
                        if (!(($dormonth == $currentmonth && $doryear == $currentyear) ||
                            ($dormonth == $prevmonth && $doryear == $prevyear))) {
                            return [
                                'status' => 'failed',
                                'message' => 'DOR is not in the current or previous month.'
                            ];
                        }
                    } else {
                        return [
                            'status' => 'failed',
                            'message' => 'User details not found.'
                        ];
                        // Handle case where no record found

                    }
                }





                $checkexists = DB::table(self::$othertrans_table)
                    ->where('orderno', '=', $copiedData['orderno'])
                    ->exists();

                if ($checkexists) {
                    return [
                        'status' => 'failed',
                        'message' => 'A record with the order number already exists .'
                    ];
                }

                // Check if a record with the same data already exists (for insertion)
                $checkexists = DB::table(self::$othertrans_table)
                    ->where($copiedData)
                    ->exists();

                if ($checkexists) {
                    return [
                        'status' => 'failed',
                        'message' => 'A record with the same data already exists.'
                    ];
                }

                // Insert a new record
                $insertedId = DB::table(self::$othertrans_table)
                    ->insertGetId($data, 'othertransid');

                if ($insertedId) {
                    return [
                        'status' => 'inserted',
                        'othertransid' => $insertedId
                    ];
                } else {
                    return [
                        'status' => 'failed',
                        'message' => 'Insertion failed due to unknown error.'
                    ];
                }
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error in insertorUpdateOthertrans: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }


    /*********************************************************************** Other Transaction Form *************************************************************/




    public static function forwardtonextlevel($transactiontypecode, $userid, $action)
    {
        $userData = session('charge');
        $session_rolemappingid = $userData->rolemappingid ?? null;
        $session_deptcode = $userData->deptcode ?? null;
        $session_regioncode = $userData->regioncode ?? null;
        $session_distcode = $userData->distcode ?? null;
        $leaveTransactionCode = View::shared('Leavetransactiontypecode');





        $data = DB::table('audit.userchargedetails as uc')
            ->join('audit.chargedetails as ch', 'ch.chargeid', '=', 'uc.chargeid')
            ->join('audit.transactionflow as tf', 'tf.torolemappingid', '=', 'ch.rolemappingid')
            ->select('uc.userid', 'uc.chargeid', 'ch.chargedescription', 'tf.flowflag', 'uc.userchargeid')
            ->where('ch.statusflag', 'Y')
            ->where('uc.statusflag', 'Y')
            ->where('ch.deptcode', $session_deptcode)
            ->where('ch.regioncode', $session_regioncode)
            ->where('ch.distcode', $session_distcode)
            ->when(
                $action === 'first',
                function ($query) {
                    $query->where('tf.flowflag', 'S');
                },
                function ($query) {
                    $query->where('tf.flowflag', '<>', 'S');
                }
            )
            ->where('tf.transactiontypecode', $transactiontypecode)
            ->whereExists(function ($query) use ($transactiontypecode, $userid, $session_rolemappingid, $leaveTransactionCode) {
                $query->select(DB::raw(1))
                    ->from('audit.userchargedetails as uc2')
                    ->join('audit.chargedetails as ch2', 'ch2.chargeid', '=', 'uc2.chargeid')
                    ->where('ch2.statusflag', 'Y')
                    ->where('uc2.statusflag', 'Y')
                    ->whereColumn('ch.deptcode', 'ch2.deptcode')
                    ->whereColumn('ch.regioncode', 'ch2.regioncode')
                    ->whereColumn('ch.rolemappingid', 'tf.torolemappingid')
                    ->whereColumn('tf.fromrolemappingid', 'ch2.rolemappingid');

                // Handle nullable distcode conditionally
                $query->where(function ($subQuery) {
                    $subQuery->whereNull('ch2.distcode')
                        ->orWhereColumn('ch.distcode', 'ch2.distcode');
                });

                // User vs role logic depending on transaction type
                if ($transactiontypecode == $leaveTransactionCode) {
                    $query->where('uc2.userid', $userid);
                } else {
                    $query->where('ch2.rolemappingid', $session_rolemappingid);
                }
            })


            ->get();
        // print_r($data);

        //       $querySql = $data->toSql();
        // $bindings = $data->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );
        // print_r($finalQuery);
        // exit;

        return $data;
    }



    // Method to insert or update transaction details
    public static function insertupdate_transdet($data, $where)
    {
        try {


            // Check if a matching record already exists in transactiondetail
            $record = DB::table(self::$transactiondetail_table)->where($where)->first();

            if ($record) {

                // If it exists, try to update in audit.transactiondetail using leaveid
                // if (isset($record->leaveid)) {
                // Update the transaction status to 'I' in the audit.transactiondetail table
                $updatedRows = DB::table('audit.transactiondetail')
                    ->where($where)
                    ->update($data);

                // Check how many rows were updated
                if ($updatedRows > 0) {
                    // echo 'ho';
                    return [
                        'status' => 'updated',
                        'message' => "{$updatedRows} row(s) updated in audit.transactiondetail",
                        // 'leaveid' => $record->processcode
                    ];
                } else {
                    return [
                        'status' => 'no_change',
                        'message' => 'No rows updated in audit.transactiondetail',
                        // 'leaveid' => $record->processcode
                    ];
                }                // } else {
                //     return [
                //         'status' => 'error',
                //         'message' => 'leaveid not found in existing record'
                //     ];
                // }
            } else {

                // If no record found, insert a new one into transactiondetail table
                $inserted = DB::table(self::$transactiondetail_table)->insertGetId($data, 'transactiondetailid');

                if ($inserted) {
                    return [
                        'status' => 'inserted',
                        'message' => 'New transaction detail inserted successfully'
                    ];
                } else {
                    return [
                        'status' => 'failed',
                        'message' => 'Failed to insert new transaction detail'
                    ];
                }
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    // Method to insert or update history transaction details
    // public static function insert_historyTransDetail($data, $where)
    // {
    //     try {
    //         // Check if a matching record already exists in historytransaction
    //         $record = DB::table(self::$historytransaction_table)->where($where)->first();

    //         $updatedRows    =   1;
    //         if ($record) {

    //             // If it exists, try to update in audit.transactiondetail using leaveid
    //             if (isset($record->leaveid)) {
    //                 // Update the transaction status to 'I' in the audit.transactiondetail table
    //                 $updatedRows = DB::table(self::$historytransaction_table)
    //                     ->where($where)
    //                     ->update(['transstatus' => 'I']);

    //                 // Check how many rows were updated

    //             }
    //         }
    //         echo 'if';
    //         if ($updatedRows > 0) {
    //             echo 'hi';

    //             print_r($data);

    //             echo self::$historytransaction_table;

    //             // $inserted = DB::table(self::$historytransaction_table)->insert($data);
    //             $insertedId = DB::table(self::$historytransaction_table)->insertGetId($data);

    //             if ($inserted) {
    //                 echo'else';
    //                 return [
    //                     'status' => 'inserted',
    //                     'message' => 'New transaction detail inserted successfully'
    //                 ];
    //             } else {
    //                 echo'else';
    //                 return [
    //                     'status' => 'failed',
    //                     'message' => 'Failed to insert new transaction detail'
    //                 ];
    //             }
    //         }

    //     } catch (\Exception $e) {
    //         return [
    //             'status' => 'error',
    //             'message' => $e->getMessage()
    //         ];
    //     }
    // }

    public static function insert_historyTransDetail($data, $where)
    {
        try {
            // Check if a matching record already exists in historytransaction
            $record = DB::table(self::$historytransaction_table)->where($where)->first();

            // Default value for $updatedRows
            $updatedRows = 0;

            if ($record) {
                // If a record exists, try to update in the audit.transactiondetail table using leaveid
                if (isset($record->leaveid)) {
                    // Update the transaction status to 'I' in the audit.transactiondetail table
                    $updatedRows = DB::table(self::$historytransaction_table)
                        ->where($where)
                        ->update(['transstatus' => 'I']);
                }
            }

            // If no matching record exists, proceed with inserting a new record
            // Insert the data into the historytransaction table and get the inserted ID
            $insertedId = DB::table(self::$historytransaction_table)->insertGetId($data, 'historytransactionsid');


            if ($insertedId) {
                return [
                    'status' => 'inserted',
                    'message' => 'New transaction detail inserted successfully',
                    'inserted_id' => $insertedId  // Returning the inserted ID
                ];
            } else {
                return [
                    'status' => 'failed',
                    'message' => 'Failed to insert new transaction detail'
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }


    // $results = DB::table('audit.transactiondetail as ht')
    //     ->leftJoin('audit.ind_leavedetail as ld', 'ld.leaveid', '=', 'ht.leaveid')
    //     ->leftJoin('audit.othertransactions as other', 'other.othertransid', '=', 'ht.othertransid')
    //     ->join('audit.deptuserdetails as du', function($join) {
    //         $join->on('du.deptuserid', '=', 'ld.userid')
    //              ->orOn('du.deptuserid', '=', 'other.userid');
    //     })
    //     ->join('audit.mst_designation as md', 'md.desigcode', '=', 'du.desigcode')
    //     ->join('audit.mst_district as dist', 'dist.distcode', '=', 'du.distcode')
    //     ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'du.deptcode')
    //     ->leftJoin('audit.mst_leavetype as mlt', 'mlt.leavetypeid', '=', 'ld.leavetypecode')
    //     ->join('audit.mst_transactiontype as tt', 'tt.transactiontypecode', '=', 'ht.transactiontypecode')
    //     ->join('audit.userchargedetails as uc', function($join) {
    //         $join->on('uc.userchargeid', '=', 'ht.updatedbyuserchargeid')
    //              ->where('uc.statusflag', '=', 'Y');
    //     })
    //     ->join('audit.chargedetails as fbch', 'fbch.chargeid', '=', 'uc.chargeid')
    //     ->join('audit.deptuserdetails as fbdu', 'uc.userid', '=', 'fbdu.deptuserid')
    //     ->join('audit.mst_designation as fbde', 'fbdu.desigcode', '=', 'fbde.desigcode')
    //     ->select(
    //         'other.userid as othertrans_userid',
    //         'other.othertransid',
    //         'other.orderdate as othertrans_date',
    //         'other.inoutstatus',
    //         'ld.fromdate',
    //         'ld.todate',
    //         'ld.leaveid',
    //         'ld.userid as leavedetail_userid',
    //         'ld.reason',
    //         'mlt.leavetypeelname',
    //         DB::raw('COALESCE(other.userid, ld.userid) as final_userid'),
    //         'ht.transactiondetailid',
    //         'ht.transactiontypecode',
    //         'ht.forwardedtouserchargeid as historyfwduc',
    //         'tt.transactiontypelname',
    //         DB::raw('COALESCE(other.processcode, ld.processcode) as processcode'),
    //         DB::raw('CASE
    //                     WHEN ld.leaveid IS NOT NULL THEN (SELECT processelname FROM audit.mst_process WHERE processcode = ld.processcode LIMIT 1)
    //                     WHEN other.othertransid IS NOT NULL THEN (SELECT processelname FROM audit.mst_process WHERE processcode = other.processcode LIMIT 1)
    //                  END as processelname'),
    //         'md.desigesname',
    //         'du.username',
    //         'du.ifhrmsno',
    //         'dist.distename',
    //         'dept.deptesname',
    //         'fbdu.username as fbdu_username',
    //         'fbde.desigesname as fbde_desigesname',
    //         'fbch.chargedescription',
    //         'ht.updatedon'
    //     )
    //     ->where('ht.forwardedtouserchargeid', '=', $userchargeid);
    //     // ->get();


    public static function fetchTransactionFlowData($forwardedToUserChargeId)
    {
        $chargedel = session('charge');
        $userdel = session('user');

        $session_rolemappingid = $chargedel->rolemappingid;

        $session_userid = $userdel->userid;

        // use Illuminate\Support\Facades\DB;

        $query = DB::table('audit.transactiondetail as ht')
            ->select([
                'other.userid as othertrans_userid',
                'other.othertransid',
                'other.orderdate as othertrans_date',
                'other.inoutstatus',
                'ld.fromdate',
                'ld.todate',
                'ld.leaveid',
                'ld.userid as leavedetail_userid',
                'ld.reason',
                'mlt.leavetypeelname',
                DB::raw('COALESCE(other.userid, ld.userid) AS trans_userid'),
                'ht.transactiondetailid',
                'ht.transactiontypecode',
                'ht.forwardedtouserchargeid as historyfwduc',
                'tt.transactiontypelname',
                'md.desigesname',
                'md.desigelname',
                'du.username',
                'du.dob',
                'du.dor',
                'du.ifhrmsno',
                // 'dist.distename',
                // 'dept.deptesname',
                'fbdu.username as fbdu_username',
                'fbde.desigesname as fbde_desigesname',
                'fbch.chargedescription',
                'ht.updatedon',
                'eu.userchargeid as forwardto',
                DB::raw('COALESCE(other.processcode, ld.processcode) AS processcode'),
                DB::raw('COALESCE(proc_ld.processelname, proc_ot.processelname) AS processelname'),
                DB::raw("
                    STRING_AGG(
                        TRIM(BOTH ' - ' FROM
                            CONCAT(
                                dept.deptesname,
                                CASE
                                    WHEN re.regionename IS NOT NULL THEN ' - ' || re.regionename
                                    ELSE ''
                                END,
                                CASE
                                    WHEN dist.distename IS NOT NULL THEN ' - ' || dist.distename
                                    ELSE ''
                                END,
				' (' || ch.chargedescription || ')'
                            )
                        ),
                        ', '
                    ) AS chargedel
                ")



            ])
            ->leftJoin('audit.ind_leavedetail as ld', 'ld.leaveid', '=', 'ht.leaveid')
            ->leftJoin('audit.othertransactions as other', 'other.othertransid', '=', 'ht.othertransid')
            ->leftJoin('audit.mst_process as proc_ot', function ($join) {
                $join->on('proc_ot.processcode', '=', DB::raw('COALESCE(NULLIF(ld.processcode, \'\'), other.processcode)'));
            })
            ->leftJoin('audit.mst_process as proc_ld', 'proc_ld.processcode', '=', 'ld.processcode')
            ->leftJoin('audit.deptuserdetails as du', function ($join) {
                $join->on('du.deptuserid', '=', DB::raw('COALESCE(ld.userid, other.userid)'));
            })
            ->leftJoin('audit.mst_designation as md', 'md.desigcode', '=', 'du.desigcode')
            // ->leftJoin('audit.mst_district as dist', 'dist.distcode', '=', 'du.distcode')
            // ->leftJoin('audit.mst_dept as dept', 'dept.deptcode', '=', 'du.deptcode')
            ->leftJoin('audit.userchargedetails as ucu', 'ucu.userid', '=', 'du.deptuserid')
            ->leftJoin('audit.chargedetails as ch', 'ch.chargeid', '=', 'ucu.chargeid')
            ->leftJoin('audit.mst_dept as dept', 'dept.deptcode', '=', 'ch.deptcode')
            ->leftJoin('audit.mst_region as re', 're.regioncode', '=', 'ch.regioncode')
            ->leftJoin('audit.mst_district as dist', 'dist.distcode', '=', 'ch.distcode')

            ->leftJoin('audit.mst_leavetype as mlt', 'mlt.leavetypeid', '=', 'ld.leavetypecode')
            ->leftJoin('audit.mst_transactiontype as tt', 'tt.transactiontypecode', '=', 'ht.transactiontypecode')
            ->leftJoin('audit.userchargedetails as uc', function ($join) {
                $join->on('uc.userchargeid', '=', 'ht.updatedbyuserchargeid')
                    ->where('uc.statusflag', 'Y');
            })
            ->leftJoin('audit.chargedetails as fbch', 'fbch.chargeid', '=', 'uc.chargeid')
            ->leftJoin('audit.deptuserdetails as fbdu', 'fbdu.deptuserid', '=', 'uc.userid')
            ->leftJoin('audit.mst_designation as fbde', 'fbde.desigcode', '=', 'fbdu.desigcode')
            // ->leftJoin(DB::raw('(SELECT * FROM audit.userchargedetails AS uc
            //                         INNER JOIN audit.chargedetails AS ch ON ch.chargeid = uc.chargeid
            //                         INNER JOIN audit.transactionflow AS tf ON tf.torolemappingid = ch.rolemappingid
            //                         WHERE ch.statusflag = \'Y\' AND uc.statusflag = \'Y\' AND tf.fromteamhead = \'Y\'
            //                         AND EXISTS (SELECT 1 FROM audit.userchargedetails AS uc2
            //                                     INNER JOIN audit.chargedetails AS ch2 ON ch2.chargeid = uc2.chargeid
            //                                     WHERE (tf.transactiontypecode = \'01\' AND uc2.userid = {$session_userid})
            //                                     OR (tf.transactiontypecode <> \'01\' AND ch2.rolemappingid = {$session_rolemappingid})
            //                                     AND ch2.statusflag = \'Y\' AND uc2.statusflag = \'Y\'
            //                                     AND ch.deptcode = ch2.deptcode
            //                                     AND ch.regioncode = ch2.regioncode
            //                                     AND ch.distcode = ch2.distcode
            //                                     AND ch.rolemappingid = tf.torolemappingid
            //                                     AND tf.fromrolemappingid = ch2.rolemappingid)) AS eu'), function($join) {
            //                                         $join->on(DB::raw('TRUE'), '=', DB::raw('TRUE'));

            // })
            ->leftJoin(DB::raw("
                (
                    SELECT * FROM audit.userchargedetails AS uc
                    INNER JOIN audit.chargedetails AS ch ON ch.chargeid = uc.chargeid
                    INNER JOIN audit.transactionflow AS tf ON tf.torolemappingid = ch.rolemappingid
                    WHERE ch.statusflag = 'Y' AND uc.statusflag = 'Y' AND tf.fromteamhead = 'Y'
                    AND EXISTS (
                        SELECT 1 FROM audit.userchargedetails AS uc2
                        INNER JOIN audit.chargedetails AS ch2 ON ch2.chargeid = uc2.chargeid
                        WHERE (
                            (tf.transactiontypecode = '01' AND uc2.userid = {$session_userid})
                            OR (tf.transactiontypecode <> '01' AND ch2.rolemappingid = {$session_rolemappingid})
                        )
                        AND ch2.statusflag = 'Y' AND uc2.statusflag = 'Y'
                        AND ch.deptcode = ch2.deptcode
                        AND ch.regioncode = ch2.regioncode
                        AND ch.distcode = ch2.distcode
                        AND ch.rolemappingid = tf.torolemappingid
                        AND tf.fromrolemappingid = ch2.rolemappingid
                    )
                ) AS eu
            "), function ($join) {
                $join->on(DB::raw('TRUE'), '=', DB::raw('TRUE'));
            })


            ->groupBy(
                'other.userid',
                'other.othertransid',
                'other.orderdate',
                'other.inoutstatus',
                'ld.fromdate',
                'ld.todate',
                'ld.leaveid',
                'ld.userid',
                'ld.reason',
                'mlt.leavetypeelname',
                'ht.transactiondetailid',
                'ht.transactiontypecode',
                'ht.forwardedtouserchargeid',
                'tt.transactiontypelname',
                'md.desigesname',
                'md.desigelname',
                'du.username',
                'du.dob',
                'du.dor',
                'du.ifhrmsno',
                'fbdu.username',
                'fbde.desigesname',
                'fbch.chargedescription',
                'ht.updatedon',
                'eu.userchargeid',
                DB::raw('COALESCE(other.processcode, ld.processcode)'),
                DB::raw('COALESCE(proc_ld.processelname, proc_ot.processelname) ')
            )
            ->where('ht.forwardedtouserchargeid', $forwardedToUserChargeId)
            ->where('ucu.statusflag', 'Y');

        // $querySql = $query->toSql();
        // $bindings = $query->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );
        // print_r($finalQuery);

        // exit;

        $query = $query->get();
        return $query;
    }





    public static function getting_pendingdel($transid, $transtypecode, $userid, $roleactioncode)
    {
        $chargedel = session('charge');
        $deptcode  = $chargedel->deptcode;


        $query = DB::table(self::$department_table)
            ->where('statusflag', 'Y')
            ->where('deptcode', $deptcode)
            ->get();
        $currentquarter =   $query[0]->currentquarter;

        if ($roleactioncode == view::shared('AuditorRoleactioncode')) {
            if ($transtypecode == View::shared('Leavetransactiontypecode')) {
                // $currentscheduledetails = DB::table('audit.auditplanteammember as aptm')
                //     ->join('audit.auditplan as ap', 'ap.auditteamid', '=', 'aptm.auditplanteamid')
                //     ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
                //     ->join('audit.inst_auditschedule as ins', 'ins.auditplanid', '=', 'ap.auditplanid')
                //     ->join('audit.inst_schteammember as insm', function ($join) {
                //         $join->on('ins.auditscheduleid', '=', 'insm.auditscheduleid')
                //             ->on('aptm.userid', '=', 'insm.userid');
                //     })
                //     ->join('audit.ind_leavedetail as ot', 'ot.userid', '=', 'aptm.userid')
                //     ->where('aptm.statusflag', 'Y')
                //     ->whereIn('ins.statusflag', ['Y', 'F'])
                //     ->where('insm.statusflag', 'Y')
                //     ->whereNull('ins.exitmeetdate')
                //     ->where('ot.leaveid', $transid)
                //     ->where('ot.transactiontypecode',  $transtypecode)
                //     ->where(function ($query) {
                //         $query->where(function ($q) {
                //             $q->whereNull('ins.entrymeetdate')
                //                 ->whereColumn('ins.todate', '>=', 'ot.fromdate')
                //                 ->whereColumn('ins.fromdate', '<=', 'ot.todate');
                //         })
                //             ->orWhere(function ($q) {
                //                 $q->whereNotNull('ins.entrymeetdate')
                //                     ->whereColumn('ins.todate', '>=', 'ot.fromdate')
                //                     ->whereColumn('ins.entrymeetdate', '<=', 'ot.todate');
                //             })
                //             ->orWhere(function ($q) {
                //                 $q->whereNotNull('ins.entrymeetdate')
                //                     ->whereNull('ins.exitmeetdate');
                //                 // ->whereColumn('ins.todate', '>=', 'ot.fromdate')
                //                 // ->whereColumn('ins.entrymeetdate', '<=', 'ot.todate');
                //             });
                //     })
                //     ->select([
                //         'aptm.planteammemberid',
                //         'ap.auditplanid',
                //         'inst.instename',
                //         'ins.workallocationflag',
                //         'ins.entrymeetdate',
                //         'ins.exitmeetdate',
                //         'insm.schteammemberid',
                //         'ins.auditscheduleid',
                //         'insm.auditteamhead',

                //         // Subquery for slipcount
                //         DB::raw("(
                //         SELECT COUNT(*)
                //         FROM audit.trans_auditslip
                //         WHERE auditscheduleid = insm.auditscheduleid
                //         AND createdby = insm.userid
                //     ) as slipcount"),

                //         // Subquery for membercount
                //         DB::raw("(
                //         SELECT COUNT(*)
                //         FROM audit.inst_schteammember
                //         WHERE auditscheduleid = insm.auditscheduleid
                //         AND statusflag = 'Y'
                //         AND auditteamhead = 'N'
                //     ) as membercount")
                //     ])
                //     ->get();

                $currentscheduledetails  = collect();

                $planteammberdata = collect();






                // $querySql = $currentscheduledetails->toSql();
                // $bindings = $currentscheduledetails->getBindings();

                // $finalQuery = vsprintf(
                //     str_replace('?', "'%s'", $querySql),
                //     array_map('addslashes', $bindings)
                // );

                // print_r($finalQuery);
                // exit;
            } else {
                $currentscheduledetails = DB::table('audit.auditplanteammember as aptm')
                    ->select([
                        'aptm.planteammemberid',
                        'ap.auditplanid',
                        'inst.instename',
                        'ins.workallocationflag',
                        'ins.entrymeetdate',
                        'ins.exitmeetdate',
                        'insm.schteammemberid',
                        'ins.auditscheduleid',
                        'insm.auditteamhead',
                        DB::raw('(select count(*) from audit.trans_auditslip where auditscheduleid = insm.auditscheduleid and createdby = insm.userid) as slipcount'),
                        DB::raw("(select count(*) from audit.inst_schteammember where auditscheduleid = insm.auditscheduleid and statusflag='Y' and auditteamhead='N') as membercount")
                    ])
                    ->join('audit.auditplan as ap', 'ap.auditteamid', '=', 'aptm.auditplanteamid')
                    ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
                    ->join('audit.inst_auditschedule as ins', 'ins.auditplanid', '=', 'ap.auditplanid')
                    ->Join('audit.othertransactions as ot', 'ot.userid', '=', 'aptm.userid')
                    ->join('audit.inst_schteammember as insm', function ($join) {
                        $join->on('ins.auditscheduleid', '=', 'insm.auditscheduleid')
                            ->on('aptm.userid', '=', 'insm.userid');
                    })
                    // ->where('aptm.userid', $userId)
                    ->where('aptm.statusflag', 'Y')
                    ->where('ap.auditquartercode',  $currentquarter)
                    ->whereIn('ins.statusflag', ['Y', 'F'])
                    ->where('insm.statusflag', 'Y')
                    ->whereNull('ins.exitmeetdate')
                    ->where('ot.othertransid',  $transid)
                    ->where('ot.transactiontypecode', $transtypecode)
                    ->get();



                $planteammberdata = DB::table('audit.auditplanteammember as aptm')
                    ->join('audit.auditplan as ap', 'ap.auditteamid', '=', 'aptm.auditplanteamid')
                    ->join('audit.mst_institution as ins', 'ins.instid', '=', 'ap.instid')
                    ->join('audit.othertransactions as ot', 'ot.userid', '=', 'aptm.userid')
                    ->where('ot.othertransid', $transid)
                    ->where('aptm.statusflag', 'Y')
                    ->where('ot.transactiontypecode', $transtypecode)
                    ->where('ap.auditquartercode',  $currentquarter)
                    ->whereNotIn('ap.auditplanid', function ($query) use ($transid, $transtypecode, $currentquarter) {
                        $query->select('ap.auditplanid')
                            ->from('audit.auditplanteammember as aptm')
                            ->join('audit.auditplan as ap', 'ap.auditteamid', '=', 'aptm.auditplanteamid')
                            ->join('audit.inst_auditschedule as ins', 'ins.auditplanid', '=', 'ap.auditplanid')
                            ->join('audit.othertransactions as ot', 'ot.userid', '=', 'aptm.userid')
                            ->join('audit.inst_schteammember as insm', function ($join) {
                                $join->on('ins.auditscheduleid', '=', 'insm.auditscheduleid')
                                    ->on('aptm.userid', '=', 'insm.userid');
                            })
                            ->where('aptm.statusflag', 'Y')
                            ->where('ap.auditquartercode',  $currentquarter)
                            ->whereIn('ins.statusflag', ['Y', 'F'])
                            ->where('insm.statusflag', 'Y')
                            // ->whereNull('ins.exitmeetdate')
                            ->where('ot.othertransid', $transid)
                            ->where('ot.transactiontypecode', $transtypecode);
                    })
                    ->select('ins.instename', 'ap.auditplanid')
                    ->get();
            }
            return $get_pendingdetails = array(
                'schedulependings' => $currentscheduledetails,
                'planpenings'       =>  $planteammberdata
            );
        } else if ($roleactioncode == view::shared('AdminplanviewRoleactioncode')) {
        } else if ($roleactioncode == view::shared('AdminentryRoleactioncode')) {
        } else {
        }
    }


    public static function fetch_usedrdata_transfer($id, $transtypecode, $inoutstatus, $roleactioncode)
    {

        if ($transtypecode == View::shared('Leavetransactiontypecode')) {

            $othertrans = DB::table('audit.ind_leavedetail as other')
                ->join('audit.deptuserdetails as du', 'du.deptuserid', '=', 'other.userid')
                ->join('audit.userchargedetails as uc', function ($join) {
                    $join->on('uc.userid', '=', 'other.userid')
                        ->where('uc.statusflag', '=', 'Y');
                })
                ->join('audit.chargedetails as cd', 'cd.chargeid', '=', 'uc.chargeid')
                ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'cd.deptcode')
                ->join('audit.mst_region as re', 're.regioncode', '=', 'cd.regioncode')
                ->join('audit.mst_district as dist', 'dist.distcode', '=', 'cd.distcode')
                ->join('audit.mst_transactiontype as tt', 'tt.transactiontypecode', '=', 'other.transactiontypecode')
                ->selectRaw("
                    other.userid,
                    du.username,
                    du.ifhrmsno,
                    du.dob,
                    du.dor,
                    other.fromdate,
                    other.todate,
                     other.reason,
                    tt.transactiontypelname,
                    other.transactiontypecode,
                    'O' as inoutstatus,
                    other.leaveid,
                    STRING_AGG(
                        TRIM(BOTH ' - ' FROM CONCAT(
                            dept.deptesname,
                            CASE WHEN re.regionename IS NOT NULL THEN ' - ' || re.regionename ELSE '' END,
                            CASE WHEN dist.distename IS NOT NULL THEN ' - ' || dist.distename ELSE '' END,
                            ' (' || cd.chargedescription || ')'
                        )), ', '
                    ) AS chargedel,
                    STRING_AGG(DISTINCT cd.regioncode, ',') AS regioncodes,
                    STRING_AGG(DISTINCT cd.deptcode, ',') AS deptcodes
                ")
                ->where('other.leaveid', '=', $id)
                ->where('other.statusflag', '=', 'Y')
                ->groupBy([
                    'other.userid',
                    'du.username',
                    'du.ifhrmsno',
                    'du.dob',
                    'du.dor',
                    'other.fromdate',
                    'other.todate',
                    'other.reason',
                    'tt.transactiontypelname',
                    'other.transactiontypecode',
                    'other.leaveid'
                ])
                ->get();



            // $querySql = $data->toSql();
            // $bindings = $data->getBindings();

            // $finalQuery = vsprintf(
            //     str_replace('?', "'%s'", $querySql),
            //     array_map('addslashes', $bindings)
            // );
            // print_r($finalQuery);

            // exit;



            // print_r( $data);

            // exit;


        } else {
            if ($inoutstatus == View::shared('Outflag')) {
                $othertrans = DB::table('audit.othertransactions as other')
                    ->join('audit.deptuserdetails as du', 'du.deptuserid', '=', 'other.userid')
                    ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'other.userid')
                    ->join('audit.chargedetails as cd', 'cd.chargeid', '=', 'uc.chargeid')
                    ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'cd.deptcode')
                    ->join('audit.mst_region as re', 're.regioncode', '=', 'cd.regioncode')
                    ->join('audit.mst_district as dist', 'dist.distcode', '=', 'cd.distcode')
                    ->join('audit.mst_transactiontype as tt', 'tt.transactiontypecode', '=', 'other.transactiontypecode')
                    ->join('audit.fileuploaddetail as fu', 'fu.fileuploadid', '=', 'other.uploadid')
                    ->leftJoin(self::$auditor_instmapping_table . ' as instmap', 'instmap.instmappingcode', '=', 'other.toinstmappingcode')
                    ->leftJoin(self::$department_table . ' as todept', 'todept.deptcode', '=', 'instmap.deptcode')
                    ->leftJoin(self::$region_table . ' as toregion', 'toregion.regioncode', '=', 'instmap.regioncode')
                    ->leftJoin(self::$district_table . ' as todist', 'todist.distcode', '=', 'instmap.distcode')
                    ->select([
                        'other.userid',
                        'du.username',
                        'du.ifhrmsno',
                        'du.dob',
                        'du.dor',
                        'other.orderdate',
                        'other.orderno',
                        'tt.transactiontypelname',
                        'other.transactiontypecode',
                        'other.inoutstatus',
                        'other.othertransid',
                        'todist.distename as to_distename',
                        'todist.disttname as to_disttname',
                        'toregion.regionename as to_regionename',
                        'toregion.regiontname as to_regiontname',
                        'todept.deptelname as div_deptelname',
                        'todept.depttlname as div_depttlname',
                        'todept.depttsname as div_depttsname',
                        'todept.deptesname as div_deptesname',
                        'instmap.instename',
                        'instmap.insttname',

                        // STRING_AGG for detailed charge info
                        DB::raw("
					STRING_AGG(
						TRIM(BOTH ' - ' FROM
							CONCAT(
								dept.deptesname,
								CASE
									WHEN re.regionename IS NOT NULL THEN ' - ' || re.regionename
									ELSE ''
								END,
								CASE
									WHEN dist.distename IS NOT NULL THEN ' - ' || dist.distename
									ELSE ''
								END,
								' (' || cd.chargedescription || ')'
							)
						),
						', '
					) AS chargedel
				"),

                        // STRING_AGG for region codes
                        DB::raw("STRING_AGG(cd.regioncode, ',') AS regioncodes"),
                        DB::raw("STRING_AGG(cd.deptcode, ',') AS deptcodes"),

                        // Conditional file details
                        DB::raw("
					CASE
						WHEN other.uploadid != 0
						THEN CONCAT(fu.filename, '-', fu.filepath, '-', fu.filesize, '-', fu.fileuploadid)
						ELSE '-'
					END AS filedetails
				")
                    ])
                    ->where('other.othertransid', '=', $id)
                    ->where('other.statusflag', '=', 'Y')
                    ->where('uc.statusflag', '=', 'Y')
                    ->groupBy(
                        'other.userid',
                        'du.username',
                        'du.ifhrmsno',
                        'du.dob',
                        'du.dor',
                        'other.orderdate',
                        'other.orderno',
                        'other.uploadid',
                        'tt.transactiontypelname',
                        'other.transactiontypecode',
                        'other.inoutstatus',
                        'fu.filename',
                        'fu.filepath',
                        'fu.filesize',
                        'fu.fileuploadid',
                        'other.othertransid',
                        'todist.distename',
                        'todist.disttname',
                        'toregion.regionename',
                        'toregion.regiontname',
                        'todept.deptelname',
                        'todept.depttlname',
                        'todept.depttsname',
                        'todept.deptesname',
                        'instmap.instename',
                        'instmap.insttname',
                    )
                    ->get();
            } else {
                $othertrans = DB::table('audit.othertransactions as other')
                    ->join('audit.deptuserdetails as du', 'du.deptuserid', '=', 'other.userid')
                    ->join('audit.userchargedetails as uc', 'uc.userid', '=', 'other.userid')
                    ->join('audit.chargedetails as cd', 'cd.chargeid', '=', 'uc.chargeid')
                    ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'cd.deptcode')
                    ->join('audit.mst_region as re', 're.regioncode', '=', 'cd.regioncode')
                    ->join('audit.mst_district as dist', 'dist.distcode', '=', 'cd.distcode')
                    ->join('audit.mst_transactiontype as tt', 'tt.transactiontypecode', '=', 'other.transactiontypecode')
                    ->join('audit.fileuploaddetail as fu', 'fu.fileuploadid', '=', 'other.uploadid')
                    ->leftJoin(self::$auditor_instmapping_table . ' as frominstmap', 'frominstmap.instmappingcode', '=', 'other.frominstmappingcode')
                    ->leftJoin(self::$department_table . ' as fromdept', 'fromdept.deptcode', '=', 'frominstmap.deptcode')
                    ->leftJoin(self::$region_table . ' as fromregion', 'fromregion.regioncode', '=', 'frominstmap.regioncode')
                    ->leftJoin(self::$district_table . ' as fromdist', 'dist.distcode', '=', 'frominstmap.distcode')

                    ->select([
                        'other.userid',
                        'du.username',
                        'du.ifhrmsno',
                        'du.dob',
                        'du.dor',
                        'other.orderdate',
                        'other.orderno',
                        'tt.transactiontypelname',
                        'other.transactiontypecode',
                        'other.inoutstatus',
                        'other.othertransid',
                        'fromdist.distename',
                        'fromdist.disttname',
                        'fromregion.regionename',
                        'fromregion.regiontname',
                        'fromdept.deptelname',
                        'fromdept.depttlname',
                        'fromdept.depttsname',
                        'fromdept.deptesname',
                        'frominstmap.instename',

                        // STRING_AGG for detailed charge info
                        DB::raw("
					STRING_AGG(
						TRIM(BOTH ' - ' FROM
							CONCAT(
								dept.deptesname,
								CASE
									WHEN re.regionename IS NOT NULL THEN ' - ' || re.regionename
									ELSE ''
								END,
								CASE
									WHEN dist.distename IS NOT NULL THEN ' - ' || dist.distename
									ELSE ''
								END,
								' (' || cd.chargedescription || ')'
							)
						),
						', '
					) AS chargedel
				"),

                        // STRING_AGG for region codes
                        DB::raw("STRING_AGG(cd.regioncode, ',') AS regioncodes"),
                        DB::raw("STRING_AGG(cd.deptcode, ',') AS deptcodes"),

                        // Conditional file details
                        DB::raw("
					CASE
						WHEN other.uploadid != 0
						THEN CONCAT(fu.filename, '-', fu.filepath, '-', fu.filesize, '-', fu.fileuploadid)
						ELSE '-'
					END AS filedetails
				")
                    ])
                    ->where('other.othertransid', '=', $id)
                    ->where('other.statusflag', '=', 'Y')
                    ->where('uc.statusflag', '=', 'Y')
                    ->groupBy(
                        'other.userid',
                        'du.username',
                        'du.ifhrmsno',
                        'du.dob',
                        'du.dor',
                        'other.orderdate',
                        'other.orderno',
                        'other.uploadid',
                        'tt.transactiontypelname',
                        'other.transactiontypecode',
                        'other.inoutstatus',
                        'fu.filename',
                        'fu.filepath',
                        'fu.filesize',
                        'fu.fileuploadid',
                        'other.othertransid',
                        'fromdist.disttname',
                        'fromdist.distename',
                        'fromregion.regionename',
                        'fromregion.regiontname',
                        'fromdept.deptelname',
                        'fromdept.depttlname',
                        'fromdept.depttsname',
                        'fromdept.deptesname',
                        'frominstmap.instename',
                    )
                    ->get();
            }
        }



        if ($roleactioncode == view::shared('auditor_roleactioncode')) {
            $todeptcode =  $othertrans[0]->deptcodes;
            $toregioncode =  $othertrans[0]->regioncodes;

            $touserdata = DB::table(self::$userdet_table . ' as ut')
                ->join(self::$district_table . ' as dt', 'ut.distcode', '=', 'dt.distcode')
                ->join(self::$userchargedetail_table . ' as uc', 'uc.userid', '=', 'ut.deptuserid')
                ->join(self::$chargedetail_table . ' as c', 'c.chargeid', '=', 'uc.chargeid')
                ->join(self::$rolemapping_table . ' as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
                ->join(self::$designation_table . ' as d', 'd.desigcode', '=', 'ut.desigcode')
                ->where('c.regioncode', $toregioncode)
                ->where('ut.deptcode', $todeptcode)
                ->where('ut.reservelist', 'N')
                ->where('uc.statusflag', 'Y')
                ->where('ut.statusflag', 'Y')
                ->where('rm.roleactioncode', View::shared('auditor_roleactioncode'))
                ->whereNotIn('ut.deptuserid', function ($query) {
                    $query->select('userid')
                        ->from('audit.othertransactions')
                        ->where('processcode', 'F')
                        ->where('inoutstatus', 'I');
                })
                ->select('ut.deptuserid', 'd.desigesname', 'ut.username', 'ut.usertamilname', 'dt.distcode', 'dt.distename')
                ->orderBy('d.desigesname', 'asc')
                ->orderBy('dt.distename', 'asc')
                ->orderBy('ut.username', 'asc')
                ->orderBy('ut.usertamilname', 'asc')
                ->get();
        } else if ($roleactioncode == view::shared('AdminplanviewRoleactioncode')) {
            $touserdata = DB::table(self::$userdet_table . ' as ut')
                ->join(self::$district_table . ' as dt', 'ut.distcode', '=', 'dt.distcode')
                ->join(self::$userchargedetail_table . ' as uc', 'uc.userid', '=', 'ut.deptuserid')
                ->join(self::$chargedetail_table . ' as c', 'c.chargeid', '=', 'uc.chargeid')
                ->join(self::$rolemapping_table . ' as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
                ->join(self::$designation_table . ' as d', 'd.desigcode', '=', 'ut.desigcode')
                ->where('uc.statusflag', 'Y')
                ->where('ut.statusflag', 'Y')
                ->where('rm.roleactioncode', View::shared('AdminplanviewRoleactioncode'))
                ->select('ut.deptuserid', 'd.desigesname', 'ut.username', 'ut.usertamilname', 'dt.distcode', 'dt.distename')
                ->orderBy('d.desigesname', 'asc')
                ->orderBy('ut.username', 'asc')
                ->orderBy('ut.usertamilname', 'asc')
                ->get();
        } else if ($roleactioncode == view::shared('AdminentryRoleactioncode')) {
        } else {
        }

        $data = [
            'othertransdet'     => $othertrans,
            'touser'            => $touserdata,
        ];

        return $data;
    }


    public static function fetch_otherteamhead($userid)
    {
        $chargedel = session('charge');
        $deptcode  = $chargedel->deptcode;
        $distcode  = $chargedel->distcode;

        $results = DB::table('audit.inst_auditschedule as ina')
            ->join('audit.inst_schteammember as mem', function ($join) {
                $join->on('ina.auditscheduleid', '=', 'mem.auditscheduleid')
                    ->where('mem.auditteamhead', '=', 'Y')
                    ->where('mem.statusflag', '=', 'Y');
            })
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'ina.auditplanid')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
            ->join('audit.deptuserdetails as dp', 'dp.deptuserid', '=', 'mem.userid')
            ->join('audit.mst_designation as des', 'des.desigcode', '=', 'dp.desigcode')
            ->where('inst.distcode', $distcode)
            ->where('inst.deptcode',  $deptcode)
            ->where('mem.userid', '!=', $userid)
            ->whereNotNull('ina.entrymeetdate')
            ->select('mem.userid', 'dp.username', 'des.desigesname')
            ->distinct()
            ->get();

        return $results;
    }

    public static function getothermembers($scheduleid)
    {
        $query = DB::table('audit.inst_auditschedule as ina')
            ->join('audit.inst_schteammember as mem', function ($join) {
                $join->on('ina.auditscheduleid', '=', 'mem.auditscheduleid')
                    ->where('mem.auditteamhead', '=', 'N')
                    ->where('mem.statusflag', '=', 'Y');
            })
            ->join('audit.deptuserdetails as dp', 'dp.deptuserid', '=', 'mem.userid')
            ->join('audit.mst_designation as des', 'des.desigcode', '=', 'dp.desigcode');

        if (is_array($scheduleid)) {
            $query->whereIn('ina.auditscheduleid', $scheduleid);
        } else {
            $query->where('ina.auditscheduleid', $scheduleid);
        }

        $query = $query->select('mem.userid', 'dp.username', 'des.desigesname')
            ->distinct()
            ->get();

        return $query;
    }











    public static function getworkalloactionbasedonSchedulemember($auditscheduleid, $schememberid)
    {
        try {

            $query1 =  DB::table('audit.trans_workallocation as wa')
                ->join('audit.map_allocation_objection as mao', 'mao.mapallocationobjectionid', '=', 'wa.workallocationtypeid')
                ->join('audit.mst_mainobjection as mo', 'mo.mainobjectionid', '=', 'mao.mainobjectionid')
                ->join('audit.mst_majorworkallocationtype as mw', 'mw.majorworkallocationtypeid', '=', 'mao.majorworkallocationtypeid')
                ->join('audit.inst_schteammember as itm', 'itm.schteammemberid', '=', 'wa.schteammemberid')
                ->join('audit.group as gro', 'gro.groupid', '=', 'mao.groupid')
                ->where('itm.auditscheduleid', $auditscheduleid)
                ->where('itm.schteammemberid', $schememberid)
                ->where('mo.statusflag', '=', 'Y')
                ->select('mw.majorworkallocationtypeename', 'mw.majorworkallocationtypetname', 'gro.groupename', 'gro.grouptname')
                ->distinct()
                ->orderBy('mw.majorworkallocationtypeename', 'asc');






            return $query1->get(); // Returns an array of user IDs
        } catch (\Exception $e) {
            // Throw a custom exception with the message from the model
            throw new \Exception($e->getMessage());
        }
    }



    public static function getslipdetailsbasedon_schedulemember($auditscheduleid, $schememberid)
    {
        try {
            $results = DB::table('audit.trans_auditslip as asl')
                ->join('audit.mst_mainobjection as mo', 'mo.mainobjectionid', '=', 'asl.mainobjectionid')
                ->join('audit.mst_subobjection as sob', 'sob.subobjectionid', '=', 'asl.subobjectionid')
                ->join('audit.inst_schteammember as itm', function ($join) {
                    $join->on('itm.userid', '=', 'asl.createdby')
                        ->on('itm.auditscheduleid', '=', 'asl.auditscheduleid');
                })
                ->join('audit.mst_process as p', 'p.processcode', '=', 'asl.processcode')
                ->join('audit.mst_severity as s', 's.severitycode', '=', 'asl.severitycode')
                ->where('asl.auditscheduleid', '=', $auditscheduleid)
                ->where('itm.schteammemberid', '=', $schememberid)
                ->select(
                    'mo.objectionename',
                    'mo.objectiontname',
                    'mo.mainobjectionid',
                    's.severityelname',
                    's.severitytlname',
                    'p.processelname',
                    'p.processtlname',
                    'sob.subobjectiontname',
                    'sob.subobjectionename',
                    'asl.mainslipnumber'
                ) // You can customize the select statement to return specific columns

                ->get();
            return $results; // Returns an array of user IDs
        } catch (\Exception $e) {
            // Throw a custom exception with the message from the model
            throw new \Exception($e->getMessage());
        }
    }


    // public static function createleave_insertupdate($data, $leave_id, $table, $userid,$for)
    // {

    //     try {
    //         $query = DB::table($table);

    //         if ($leave_id) {
    //             $query->where('leaveid', '!=', $leave_id);
    //         }

    //         if($for == 'form')
    //         {
    //             $leaveexists = (clone $query)
    //             ->where(function ($q) use ($data, $userid) {
    //                 $q->where(function ($subQuery) use ($data) {
    //                     // Overlapping condition
    //                     $subQuery->where('fromdate', '<=', $data['todate'])
    //                         ->where('todate', '>=', $data['fromdate']);
    //                 })
    //                     ->where('userid', '=', $userid)
    //                     ->orWhere(function ($subQuery) use ($data) {
    //                         // Special case where both dates are the same
    //                         $subQuery->where('fromdate', '=', $data['fromdate'])
    //                             ->where('todate', '=', $data['todate']);
    //                     });
    //             })
    //             ->exists();
    //             if ($leaveexists) {
    //                 // return 'excess';
    //                 // return response()->json(['error' => 'Leave for the particular date was already applied.'], 400);
    //                 throw new \Exception('Leave for the particular date was already applied.');
    //             }
    //         }




    //         if ($leave_id) {
    //             DB::table($table)->where('leaveid', $leave_id)->update($data);
    //             return DB::table($table)->where('leaveid', $leave_id)->first();
    //         } else {
    //             $insert_leavedet = DB::table($table)->insertGetId($data, 'leaveid');
    //         }


    //         if ($insert_leavedet) {
    //             return DB::table($table)->where('leaveid', $insert_leavedet)->first();
    //         } else {
    //             return response()->json(['success' => false, 'message' => 'Failed to insert leave details. Please try again.'], 500);
    //         }
    //     } catch (\Exception $e) {
    //         throw new \Exception($e->getMessage());
    //     }
    // }
    public static function createleave_insertupdate($data, $leave_id, $table, $userid, $for)
    {
        try {
            $query = DB::table($table);

            if ($leave_id) {
                $query->where('leaveid', '!=', $leave_id);
            }

            if ($for === 'form') {
                $leaveExists = (clone $query)
                    ->where(function ($q) use ($data, $userid) {
                        $q->where(function ($subQuery) use ($data) {
                            // Overlapping condition
                            $subQuery->where('fromdate', '<=', $data['todate'])
                                ->where('todate', '>=', $data['fromdate']);
                        })
                            ->where('userid', '=', $userid)

                            ->where('processcode', '<>', 'I');
                        // ->orWhere(function ($subQuery) use ($data) {
                        // Special case where both dates are the same
                        //   $subQuery->where('fromdate', '=', $data['fromdate'])
                        //   ->where('todate', '=', $data['todate']);
                        //  });
                    })
                    ->exists();

                if ($leaveExists) {
                    return ['status' => 'failed', 'message' => 'Leave for the particular date was already applied.'];
                }
            }

            if ($leave_id) {
                $updatedRows = DB::table($table)->where('leaveid', $leave_id)->update($data);

                if ($updatedRows > 0) {
                    $record = DB::table($table)->where('leaveid', $leave_id)->first();
                    return ['status' => 'updated', 'data' => $record];
                } else {
                    return ['status' => 'failed', 'message' => 'No rows updated.'];
                }
            } else {
                $insertedId = DB::table($table)->insertGetId($data, 'leaveid');

                if ($insertedId) {
                    $record = DB::table($table)->where('leaveid', $insertedId)->first();
                    return ['status' => 'inserted', 'data' => $record];
                } else {
                    return ['status' => 'failed', 'message' => 'Insertion failed.'];
                }
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }


    public static function getholidaydates()
    {
        $holidaydates = DB::table('audit.mst_holiday')
            ->where('statusflag', 'Y')
            ->select('holiday_date')
            ->orderBy('updatedon', 'desc')
            ->get();
        return  $holidaydates;
    }

    public static function  fetchalldata($userid)
    {
        $all_leavedet = DB::table('audit.ind_leavedetail')
            ->join('audit.mst_leavetype as mlt', 'mlt.leavetypeid',  '=', 'audit.ind_leavedetail.leavetypecode')
            ->where('audit.ind_leavedetail.userid', $userid)
            ->orderBy('audit.ind_leavedetail.updatedon', 'desc')
            ->get();
        return  $all_leavedet;
    }


    public static function fetchsingle_data($leaveid, $table)
    {
        $single_leavedet = DB::table($table)
            ->where('leaveid', $leaveid)
            ->orderBy('audit.ind_leavedetail.updatedon', 'desc')
            ->get();
        return  $single_leavedet;
    }





    public static function getinstitutiondel($auditscheduleid)
    {
        try {
            $query1 =  DB::table('audit.inst_auditschedule as ins')
                ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'ins.auditplanid')
                ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
                ->join('audit.inst_schteammember as insm', 'insm.auditscheduleid', '=', 'ins.auditscheduleid')
                ->join('audit.deptuserdetails as du', 'du.deptuserid', '=', 'insm.userid')
                ->join('audit.mst_designation as desig', 'du.desigcode', '=', 'desig.desigcode')
                ->select(
                    'ins.auditscheduleid',
                    'inst.instename',
                    DB::raw("
                        STRING_AGG(
                            CASE WHEN insm.auditteamhead = 'Y'
                                 THEN du.username || ' (' || desig.desigesname || ')'
                                 ELSE NULL END, ', '
                        ) AS teamhead
                    "),
                    DB::raw("
                        STRING_AGG(
                            CASE WHEN insm.auditteamhead = 'N'
                                 THEN du.username || ' (' || desig.desigesname || ')'
                                 ELSE NULL END, ', '
                        ) AS memberdel
                    "),
                    'ins.entrymeetdate',
                    'ins.exitmeetdate',
                    'inst.mandays',
                    'ins.fromdate',
                    'ins.todate'
                )
                ->where('ins.auditscheduleid', $auditscheduleid)
                ->where('ins.statusflag', 'F')
                ->where('insm.statusflag', 'Y')
                ->groupBy(
                    'ins.auditscheduleid',
                    'inst.instename',
                    'ins.entrymeetdate',
                    'ins.exitmeetdate',
                    'inst.mandays',
                    'ins.fromdate',
                    'ins.todate'
                );






            return $query1->get(); // Returns an array of user IDs
        } catch (\Exception $e) {
            // Throw a custom exception with the message from the model
            throw new \Exception($e->getMessage());
        }
    }






















    // public static function insertupdate_transdet($data, $where)
    // {
    //     try {
    //         // Check if a matching record already exists in transactiondetail
    //         $record = DB::table(self::$transactiondetail_table)->where($where)->first();

    //         if ($record) {
    //             // If it exists, try to update in audit.transactiondetail using leaveid
    //             if (isset($record->leaveid)) {
    //                 $updated = DB::table('audit.transactiondetail')
    //                     ->where('leaveid', $record->leaveid)
    //                     ->update($data);

    //                 if ($updated > 0) {
    //                     return [
    //                         'status' => 'updated',
    //                         'message' => 'Record updated successfully in audit.transactiondetail',
    //                         'leaveid' => $record->leaveid
    //                     ];
    //                 } else {
    //                     return [
    //                         'status' => 'no_change',
    //                         'message' => 'Record exists, but no data was changed',
    //                         'leaveid' => $record->leaveid
    //                     ];
    //                 }
    //             } else {
    //                 return [
    //                     'status' => 'error',
    //                     'message' => 'leaveid not found in existing record'
    //                 ];
    //             }
    //         } else {
    //             // If no record found, insert a new one into transactiondetail table
    //             $inserted = DB::table(self::$transactiondetail_table)->insert($data);

    //             if ($inserted) {
    //                 return [
    //                     'status' => 'inserted',
    //                     'message' => 'New transaction detail inserted successfully'
    //                 ];
    //             } else {
    //                 return [
    //                     'status' => 'failed',
    //                     'message' => 'Failed to insert new transaction detail'
    //                 ];
    //             }
    //         }
    //     } catch (\Exception $e) {
    //         return [
    //             'status' => 'error',
    //             'message' => $e->getMessage()
    //         ];
    //     }
    // }


    // public static function insert_historyTransDetail($data, $where)
    // {
    //     try {
    //         // Check if a matching record already exists in transactiondetail
    //         $record = DB::table(self::$historytransaction_table)->where($where)->first();

    //         if ($record) {
    //             // If it exists, try to update in audit.transactiondetail using leaveid
    //             if (isset($record->leaveid)) {
    //                 $updated = DB::table('audit.transactiondetail')
    //                     ->where('leaveid', $record->leaveid)
    //                     ->update(array('transstatus' => 'I'));

    //         }
    //         $inserted = DB::table(self::$historytransaction_table)->insert($data);

    //         if ($inserted) {
    //             return [
    //                 'status' => 'inserted',
    //                 'message' => 'New transaction detail inserted successfully'
    //             ];
    //         } else {
    //             return [
    //                 'status' => 'failed',
    //                 'message' => 'Failed to insert new transaction detail'
    //             ];
    //         }
    //     } catch (\Exception $e) {
    //         return [
    //             'status' => 'error',
    //             'message' => $e->getMessage()
    //         ];
    //     }
    // }



    // public function insert_datatransfer()
    // {

    //     try {
    //         DB::beginTransaction();


    //         $sessionuserid = 748;
    //         $sessionuserchargeid = 4219;

    //         // Format arrays as PostgreSQL array literals
    //         $auditscheduleids = '{' . implode(',', $request['auditscheduleid']) . '}';
    //         $datatypes = '{"' . implode('","', $request['datatransfercode']) . '"}';

    //         // Ensure NULL is used correctly for missing values
    //         $transuserFormatted = array_map(fn($v) => $v === null || $v === '' ? 'NULL' : $v, $request['transuser']);
    //         $transferusers = '{' . implode(',', $transuserFormatted) . '}';

    //         $plandatatypecode = $request['plandatatransfercode'][0] ?? null;
    //         $plantransuser = $request['plantransuser'][0] ?? null;

    //         DB::statement("CALL audit.process_transfer(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
    //             $request['userid'],           // _changeforuserid
    //             $auditscheduleids,           // auditscheduleids
    //             $datatypes,                  // datatypes
    //             $transferusers,              // transferusers
    //             $plandatatypecode,           // planteammber_datatypecode
    //             $plantransuser,              // planteammber_touser
    //             $sessionuserid,              // _sessionuserid
    //             $sessionuserchargeid,        // _sessionuserchargeid
    //             '01',                         // _transactioncode
    //             $request['othertransid']     // _transid
    //         ]);

    //         DB::commit();
    //         return response()->json(['status' => 'success']);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }

    // }



    // public static function insert_datatransfer($request, $sessionuserid, $sessionuserchargeid)
    // {
    //     try {
    //         // print_r( $request->all());
    //         // exit;
    //         DB::beginTransaction();

    //         $request = $request->all(); // Convert to array
    //         $inoutstatus = $request['inoutstatus'] ?? null;

    //         // Declare all variables
    //         $auditscheduleids = null;
    //         $datatypes = null;
    //         $transferusers = null;
    //         $plandatatypecode = null;
    //         $plantransuser = null;

    //         // Only populate values if status is not 'I' (i.e., not "In")
    //         if ($inoutstatus !== 'I') {
    //             if(isset($request['auditscheduleid']))
    //             {
    //                 $auditscheduleids = '{' . implode(',', $request['auditscheduleid']) . '}';
    //                 $datatypes = '{"' . implode('","', $request['datatransfercode']) . '"}';

    //                 // Format transferusers array for NULLs
    //                 $transuserFormatted = array_map(
    //                     fn($v) => ($v === null || $v === '') ? 'NULL' : $v,
    //                     $request['transuser']
    //                 );
    //                 $transferusers = '{' . implode(',', $transuserFormatted) . '}';

    //             }

    //             if(isset($request['plandatatransfercode'][0]))
    //             {
    //                 $plandatatypecode = $request['plandatatransfercode'][0] ?? null;
    //                 $plantransuser = $request['plantransuser'][0] ?? null;
    //             }




    //         }



    //         if($request['transactiontypecode'] == View::shared('Leavetransactiontypecode'))
    //         {

    //             in leave  $auditscheduleids,
    //             $datatypes,
    //             $transferusers,   some time null

    //             {"success":false,"error":"SQLSTATE[22004]: Null value not allowed: 7 ERROR:  upper bound of FOR loop cannot be null\nCONTEXT:  PL\/pgSQL function audit.leave_management(integer,integer[],text[],integer[],integer,integer,character,integer) line 57 at FOR with integer loop variable (Connection: pgsql, SQL: CALL audit.leave_management(781, ?, ?, ?, 748,  4186, 01, 2))"}
    //             DB::statement("CALL audit.leave_management(?, ?, ?, ?, ?,  ?, ?, ?)", [
    //                 $request['userid'],
    //                 $auditscheduleids,
    //                 $datatypes,
    //                 $transferusers,
    //                 $sessionuserid,
    //                 $sessionuserchargeid,
    //                 $request['transactiontypecode'],
    //                 $request['othertransid']
    //             ]);


    //         }
    //         else
    //         {
    //             // Call the stored procedure
    //             DB::statement("CALL audit.process_transfer(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
    //                 $request['userid'],
    //                 $auditscheduleids,
    //                 $datatypes,
    //                 $transferusers,
    //                 $plandatatypecode,
    //                 $plantransuser,
    //                 $sessionuserid,
    //                 $sessionuserchargeid,
    //                 $request['transactiontypecode'],
    //                 $request['othertransid']
    //             ]);
    //         }



    //         DB::commit();
    //         return ['status' => 'success'];

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return ['status' => 'error', 'message' => $e->getMessage()];
    //     }
    // }


    public static function insert_datatransfer($request, $sessionuserid, $sessionuserchargeid)
    {
        try {
            DB::beginTransaction();

            $request = $request->all(); // Ensure input is an array
            $inoutstatus = $request['inoutstatus'] ?? null;

            // Initialize values
            $auditscheduleids = '{}';
            $datatypes = '{}';
            $transferusers = '{}';
            $plandatatypecode = null;
            $plantransuser = null;

            $auditplanids = '{}';




            // Process array parameters only if not in 'I' status
            if ($inoutstatus !== 'I') {
                if (isset($request['auditscheduleid']) && is_array($request['auditscheduleid']) && count($request['auditscheduleid']) > 0) {
                    $auditscheduleids = '{' . implode(',', $request['auditscheduleid']) . '}';
                }

                if (isset($request['datatransfercode']) && is_array($request['datatransfercode']) && count($request['datatransfercode']) > 0) {
                    $quoted = array_map(fn($v) => '"' . $v . '"', $request['datatransfercode']);
                    $datatypes = '{' . implode(',', $quoted) . '}';
                }

                // if (isset($request['transuser']) && is_array($request['transuser']) && count($request['transuser']) > 0) {
                //     $transuserFormatted = array_map(
                //         fn($v) => ($v === null || $v === '') ? 'NULL' : $v,
                //         $request['transuser']
                //     );
                //     $transferusers = '{' . implode(',', $transuserFormatted) . '}';

                // }

                if (isset($request['selectedUserIds']) && is_array($request['selectedUserIds']) && count($request['selectedUserIds']) > 0) {
                    $transuserFormatted = array_map(
                        fn($v) => ($v === null || $v === '') ? 'NULL' : $v,
                        $request['selectedUserIds']
                    );
                    $transferusers = '{' . implode(',', $transuserFormatted) . '}';
                }



                if (isset($request['plandatatransfercode'][0])) {
                    $plandatatypecode = $request['plandatatransfercode'][0] ?? null;
                }

                if (isset($request['plantransuser'][0])) {
                    $plantransuser = $request['plantransuser'][0] ?? null;
                }


                if (isset($request['auditplanid']) && is_array($request['auditplanid']) && count($request['auditplanid']) > 0) {
                    $auditplanids = '{' . implode(',', $request['auditplanid']) . '}';
                }
            }

            // echo  $request['userid'];
            // echo "<br>";
            // echo  $auditscheduleids;
            // echo "<br>";
            // echo  $datatypes;
            // echo "<br>";
            // echo  $transferusers;
            // echo "<br>";
            // echo  $plandatatypecode;
            // echo "<br>";
            // echo  $plantransuser;
            // echo "<br>";
            // echo  $auditplanids;
            // echo "<br>";
            // echo  $sessionuserid;
            // echo "<br>";
            // echo  $sessionuserchargeid;
            // echo "<br>";
            // echo  $request['transactiontypecode'];
            // echo "<br>";
            // echo  $request['othertransid'];
            // echo "<br>";


            // exit;


            // echo  $request['userid'];
            // echo "<br>";
            // echo  $auditscheduleids;
            // echo "<br>";
            // echo  $datatypes;
            // echo "<br>";
            // echo  $transferusers;
            // echo "<br>";

            // echo  $sessionuserid;
            // echo "<br>";
            // echo  $sessionuserchargeid;
            // echo "<br>";
            // echo  $request['transactiontypecode'];
            // echo "<br>";
            // echo  $request['othertransid'];
            // echo "<br>";
            // exit;









            // Handle transaction type and call respective stored procedure
            if ($request['transactiontypecode'] === View::shared('Leavetransactiontypecode')) {
                DB::statement("CALL audit.leave_management(?, ?, ?, ?, ?, ?, ?, ?)", [
                    $request['userid'],
                    $auditscheduleids,
                    $datatypes,
                    $transferusers,
                    $sessionuserid,
                    $sessionuserchargeid,
                    $request['transactiontypecode'],
                    $request['othertransid']
                ]);
            } else {
                DB::statement("CALL audit.process_transfer(?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)", [
                    $request['userid'],
                    $auditscheduleids,
                    $datatypes,
                    $transferusers,
                    $plandatatypecode,
                    $plantransuser,
                    $auditplanids,
                    $sessionuserid,
                    $sessionuserchargeid,
                    $request['transactiontypecode'],
                    $request['othertransid'],
                    $inoutstatus
                ]);
            }

            DB::commit();
            return ['status' => 'success'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }




    public static function getapproveddetails($deptcode, $regioncode, $distcode)
    {
        // First query
        $firstQuery = DB::table('audit.othertransactions as ot')
            ->select([
                'ot.orderdate',
                'ot.orderno',
                'fu.filename',
                'fu.filepath',
                'fdp.deptesname as fromdeptename',
                'fre.regionename as fromregionename',
                'fdist.distename as fromdistename',
                'finsm.instename as frominstename',
                'tdp.deptesname as todeptename',
                'tre.regionename as toregionename',
                'tdist.distename as todistename',
                'tinsm.instename as toinstename',
                'us.username',
                'us.ifhrmsno',
                'us.dob',
                'us.dor',
                'tt.transactiontypelname',
                DB::raw('NULL as fromdate'),
                DB::raw('NULL as todate'),
                'abdu.username as approvedby_username',
                'abch.chargedescription',
                'abdp.deptesname',
                'abre.regionename',
                'abdist.distename',
                'abdes.desigesname',
                'p.processelname',
                'abdu.desigcode',
                'abuc.userchargeid',
                'tt.transactiontypecode',
                'td.updatedon',
                'ot.othertransid as id'
            ])
            ->join('audit.deptuserdetails as us', 'us.deptuserid', '=', 'ot.userid')
            ->join('audit.transactiondetail as td', 'td.othertransid', '=', 'ot.othertransid')
            ->join('audit.mst_transactiontype as tt', 'tt.transactiontypecode', '=', 'ot.transactiontypecode')
            ->join('audit.auditor_instmapping as finsm', 'finsm.instmappingcode', '=', 'ot.frominstmappingcode')
            ->join('audit.mst_dept as fdp', 'fdp.deptcode', '=', 'finsm.deptcode')
            ->join('audit.mst_region as fre', 'fre.regioncode', '=', 'finsm.regioncode')
            ->join('audit.mst_district as fdist', 'fdist.distcode', '=', 'finsm.distcode')
            ->join('audit.fileuploaddetail as fu', 'fu.fileuploadid', '=', 'ot.uploadid')
            ->leftJoin('audit.auditor_instmapping as tinsm', 'tinsm.instmappingcode', '=', 'ot.toinstmappingcode')
            ->leftJoin('audit.mst_dept as tdp', 'tdp.deptcode', '=', 'tinsm.deptcode')
            ->leftJoin('audit.mst_region as tre', 'tre.regioncode', '=', 'tinsm.regioncode')
            ->leftJoin('audit.mst_district as tdist', 'tdist.distcode', '=', 'tinsm.distcode')
            ->join('audit.userchargedetails as abuc', 'abuc.userchargeid', '=', 'td.updatedbyuserchargeid')
            ->join('audit.chargedetails as abch', 'abch.chargeid', '=', 'abuc.chargeid')
            ->join('audit.deptuserdetails as abdu', 'abdu.deptuserid', '=', 'abuc.userid')
            ->join('audit.mst_dept as abdp', 'abdp.deptcode', '=', 'abch.deptcode')
            ->join('audit.mst_region as abre', 'abre.regioncode', '=', 'abch.regioncode')
            ->join('audit.mst_district as abdist', 'abdist.distcode', '=', 'abch.distcode')
            ->join('audit.mst_designation as abdes', 'abdes.desigcode', '=', 'abdu.desigcode')
            ->join('audit.mst_process as p', 'p.processcode', '=', 'ot.processcode')
            ->where('p.processcode', '=', 'P');

        // Second query
        $secondQuery = DB::table('audit.ind_leavedetail as ot')
            ->select([
                DB::raw('NULL as orderdate'),
                DB::raw('NULL as orderno'),
                DB::raw("'' as filename"),
                DB::raw("'' as filepath"),
                DB::raw("'' as fromdeptename"),
                DB::raw("'' as fromregionename"),
                DB::raw("'' as fromdistename"),
                DB::raw("'' as frominstename"),
                DB::raw("'' as todeptename"),
                DB::raw("'' as toregionename"),
                DB::raw("'' as todistename"),
                DB::raw("'' as toinstename"),
                'us.username',
                'us.ifhrmsno',
                'us.dob',
                'us.dor',
                'tt.transactiontypelname',
                'ot.fromdate',
                'ot.todate',
                'abdu.username as approvedby_username',
                'abch.chargedescription',
                'abdp.deptesname',
                'abre.regionename',
                'abdist.distename',
                'abdes.desigesname',
                'p.processelname',
                'abdu.desigcode',
                'abuc.userchargeid',
                'tt.transactiontypecode',
                'td.updatedon',
                'ot.leaveid as id'
            ])
            ->join('audit.deptuserdetails as us', 'us.deptuserid', '=', 'ot.userid')
            ->join('audit.mst_transactiontype as tt', 'tt.transactiontypecode', '=', 'ot.transactiontypecode')
            ->join('audit.transactiondetail as td', 'td.leaveid', '=', 'ot.leaveid')
            ->join('audit.userchargedetails as abuc', 'abuc.userchargeid', '=', 'td.updatedbyuserchargeid')
            ->join('audit.chargedetails as abch', 'abch.chargeid', '=', 'abuc.chargeid')
            ->join('audit.deptuserdetails as abdu', 'abdu.deptuserid', '=', 'abuc.userid')
            ->join('audit.mst_dept as abdp', 'abdp.deptcode', '=', 'abch.deptcode')
            ->join('audit.mst_region as abre', 'abre.regioncode', '=', 'abch.regioncode')
            ->join('audit.mst_district as abdist', 'abdist.distcode', '=', 'abch.distcode')
            ->join('audit.mst_designation as abdes', 'abdes.desigcode', '=', 'abdu.desigcode')
            ->join('audit.mst_process as p', 'p.processcode', '=', 'ot.processcode')
            ->where('p.processcode', '=', 'P');

        // Union the queries
        $finalQuery = $firstQuery->union($secondQuery);

        // Execute the query and get results
        $results = $finalQuery->get();

        return $results;
    }


    public static function getdatatransferdel($id, $transactiontypecode)
    {
        // Query 1
        $query1 = DB::table('audit.logothertrans_plandel as pldel')
            ->join(DB::raw("LATERAL (
            SELECT jsonb_array_elements_text(pldel.auditplanid -> 'plan_ids')::int AS plan_id
        ) as plan_ids_split"), DB::raw('TRUE'), DB::raw('TRUE'))
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'plan_ids_split.plan_id')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
            ->leftJoin('audit.deptuserdetails as fdp', 'fdp.deptuserid', '=', 'pldel.fromuserid')
            ->leftJoin('audit.deptuserdetails as tdp', 'tdp.deptuserid', '=', 'pldel.touserid')
            ->leftJoin('audit.mst_designation as fdes', 'fdes.desigcode', '=', 'fdp.desigcode')
            ->leftJoin('audit.mst_designation as tdes', 'tdes.desigcode', '=', 'tdp.desigcode')
            ->select(
                'ap.instid',
                'inst.instename',
                'pldel.datatransfertypecode',
                DB::raw("fdp.username || ' ( ' || fdes.desigesname || ' )' as from_user_details"),
                DB::raw("tdp.username || ' ( ' || tdes.desigesname || ' )' as to_user_details")
            )
            ->where('pldel.othertransid', $id)
            // ->where('pldel.datatransfertypecode', $transactiontypecode)
            ->get();

        // $querySql = $query1->toSql();
        // $bindings = $query1->getBindings();

        // $finalQuery = vsprintf(
        //     str_replace('?', "'%s'", $querySql),
        //     array_map('addslashes', $bindings)
        // );

        // print_r($finalQuery);

        // Query 2
        $query2 = DB::table('audit.historytrans_workallocation as wa')
            ->join('audit.logothertrans_scheduledel as lsc', 'lsc.othertransid', '=', 'wa.othertransid')
            // ->leftJoin('audit.logothertrans_scheduledel as lscl', 'lscl.leaveid', '=', 'wa.leaveid')
            ->join('audit.map_allocation_objection as mao', 'mao.mapallocationobjectionid', '=', 'wa.workallocationtypeid')
            ->join('audit.mst_mainobjection as mo', 'mo.mainobjectionid', '=', 'mao.mainobjectionid')
            ->join('audit.mst_majorworkallocationtype as mw', 'mw.majorworkallocationtypeid', '=', 'mao.majorworkallocationtypeid')
            ->join('audit.inst_schteammember as itm', 'itm.schteammemberid', '=', 'wa.schteammemberid')
            ->join('audit.deptuserdetails as fdp', function ($join) {
                $join->on('fdp.deptuserid', '=', 'itm.userid')
                    ->on('wa.auditscheduleid', '=', 'itm.auditscheduleid');
            })
            ->join('audit.inst_schteammember as titm', 'titm.schteammemberid', '=', 'wa.toschteammemberid')
            ->join('audit.deptuserdetails as tdp', function ($join) {
                $join->on('tdp.deptuserid', '=', 'titm.userid')
                    ->on('wa.auditscheduleid', '=', 'titm.auditscheduleid');
            })
            ->join('audit.group as gro', 'gro.groupid', '=', 'mao.groupid')
            ->where('mo.statusflag', '=', 'Y')
            ->where('wa.othertransid', $id)
            ->select(
                'mw.majorworkallocationtypeename',
                'mw.majorworkallocationtypetname',
                'gro.groupename',
                'gro.grouptname',
                'fdp.username as fromuser',
                'tdp.username as touser'
            )
            ->distinct()
            ->orderBy('mw.majorworkallocationtypeename', 'asc')
            ->get();

        // Query 3
        $query3 = DB::table('audit.logothertrans_scheduledel as ls')
            ->join('audit.inst_auditschedule as ina', 'ina.auditscheduleid', '=', 'ls.auditscheduleid')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'ina.auditplanid')
            ->join('audit.mst_institution as ins', 'ins.instid', '=', 'ap.instid')
            ->join('audit.deptuserdetails as fdp', 'fdp.deptuserid', '=', 'ls.fromuserid')
            ->join('audit.deptuserdetails as tdp', 'tdp.deptuserid', '=', 'ls.touserid')
            ->where('ls.othertransid', $id)
            ->select(
                'ins.instename',
                'ins.insttname',
                'ls.datatransfertypecode',
                'ls.workallocationstatus',
                'ls.slipcount',
                'fdp.username as fromuser',
                'tdp.username as touser'
            )
            ->get();

        return [
            'query1' => $query1,
            'query2' => $query2,
            'query3' => $query3
        ];
    }

    public static function getTodept()
    {


        $query = DB::table(self::$department_table)
            ->where('statusflag', 'Y');

        $query->orderBy('orderid', 'asc');

        $departments = $query->get();

        return $departments;
    }
}
