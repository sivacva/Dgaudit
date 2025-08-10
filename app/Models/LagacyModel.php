<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\View;
use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class LagacyModel extends Model
{

    protected static $deptartment_table = BaseModel::DEPARTMENT_TABLE;
    protected static $institution_table = BaseModel::INSTITUTION_TABLE;
    protected static $auditplan_table = BaseModel::AUDITPLAN_TABLE;
    protected static $temprankusers_table = BaseModel::TEMPRANKUSERS_TABLE;
    protected static $designation_table = BaseModel::DESIGNATION_TABLE;
    protected static $userdetail_table = BaseModel::USERDETAIL_TABLE;
    protected static $auditplanteam_table = BaseModel::AUDITPLANTEAM_TABLE;
    protected static $auditplanteammem_table = BaseModel::AUDITPLANTEAMMEM_TABLE;
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
    protected static $subcategory_table = BaseModel::SUBCATEGORY_TABLE;
    protected static $typeofaudit_table = BaseModel::TYPEOFAUDIT_TABLE;
    protected static $mapallocationobjection_table = BaseModel::MAPALLOCATIONOBJECTION_TABLE;
    protected static $mainobjection_table = BaseModel::MAINOBJECTION_TABLE;
    protected static $subobjection_table = BaseModel::SUBOBJ_TABLE;
    protected static $lagacy_table = BaseModel::LAGACY_TABLE;




    protected static $auditperiod_table = BaseModel::AUDITPERIOD_TABLE;
    protected static $callforrec_table = BaseModel::CALLFORRECORDS_AUDITEE_TABLE;

    public static function followup_dropdown($instid, $catcode, $subcatid)
    {
        $inst = DB::table(self::$institution_table)
            ->where('statusflag', 'Y')
            ->where('instid', $instid)
            ->get();
        // return 'asd';
        $catDet = DB::table(self::$subcategory_table . ' as sub')
            ->join(self::$mstauditeeinscategory_table . ' as cat', 'cat.catcode', '=', 'sub.catcode')

            ->where('sub.statusflag', 'Y')
            ->where('sub.catcode', $catcode)
            ->where('sub.auditeeins_subcategoryid', $subcatid)
            ->get();
        $typeofaudit = DB::table(self::$typeofaudit_table)
            ->where('statusflag', 'Y')
            ->get();
        $objection  = DB::table(self::$mapallocationobjection_table . ' as map')
            ->join(self::$mainobjection_table . ' as mainobj', 'map.mainobjectionid', '=', 'mainobj.mainobjectionid')
            // ->join(self::$subobjection_table . ' as subobj', 'map.subobjectionid', '=', 'subobj.subobjectionid')
            ->where('map.statusflag', 'Y')
            ->where('map.catcode', $catcode)
            ->where('map.auditeeins_subcategoryid', $subcatid)
            ->distinct()
            ->get();
        $data = [
            'inst'          => $inst,
            'catDet'        => $catDet,
            'typeofaudit'   => $typeofaudit,
            'objection'     => $objection,
        ];

        return $data;
    }

    public static function getminorobjection($mainobjectionid)
    {
        return DB::table(self::$subobjection_table . ' as subobj')
            ->join(self::$mapallocationobjection_table . ' as map', 'subobj.mainobjectionid', '=', 'map.mainobjectionid')
            // ->join(self::$mainobjection_table . ' as mainobj', 'map.mainobjectionid', '=', 'mainobj.mainobjectionid')

            ->where('subobj.mainobjectionid', $mainobjectionid)
            ->where('subobj.statusflag', 'Y')
            ->get();
    }

    public static function createorinsertLagacydet($data, $lagacyid)
    {
        $table = self::$lagacy_table;
        if ($lagacyid) {
            $affectedRows = DB::table($table)
                ->where('lagacyid', $lagacyid)
                ->update($data);

            if ($affectedRows === 0) {
                throw new \Exception('Failed to update the record.');
            }
            return $lagacyid;
        } else {
            $newRecordId = DB::table($table)->insertGetId($data, 'lagacyid');

            if (!$newRecordId) {
                throw new \Exception('Failed to insert the new record.');
            }
            return $newRecordId; // Return the ID of the newly inserted record
        }
    }


    public static function fetch_lagacydata($lagacyid)

    {
        $table = self::$lagacy_table;

        $query = DB::table($table . ' as lagacy')
            ->join(self::$typeofaudit_table . ' as type', 'type.typeofauditcode', '=', 'lagacy.typeofauditcode')
            ->join(self::$mainobjection_table . ' as main', 'main.mainobjectionid', '=', 'lagacy.mainobjectionid')
            ->join(self::$subobjection_table .  ' as sub', 'sub.subobjectionid', '=', 'lagacy.subobjectionid')
            // ->join(self::$auditquarter_table . ' as quarter', 'quarter.auditquartercode', '=', 'inst.audit_quarter')
            // ->join(self::$distTable . ' as dist', 'dist.distcode', '=', 'inst.distcode')
            ->join(self::$fileuploaddetail_table . ' as fu', 'fu.fileuploadid', '=', 'lagacy.uploadid')
            ->select(
                'lagacy.lagacyid',
                'lagacy.amtinvolved',
                'lagacy.slipdetails',
                'lagacy.severity',
                'lagacy.liability',
                'lagacy.liabilityname',
                'type.typeofauditcode',
                'type.typeofauditename',
                'type.typeofaudittname',
                'main.mainobjectionid',
                'main.objectionename',
                'main.objectiontname',
                'sub.subobjectionid',
                'sub.subobjectionename',
                'sub.subobjectiontname',
                DB::raw("COALESCE(remarks::json->>'content', '') AS remarks"),
                DB::raw("
                    CASE
                        WHEN lagacy.uploadid != 0 THEN CONCAT(fu.filename, '-', fu.filepath, '-', fu.filesize, '-', fu.fileuploadid)
                        ELSE '-'
                    END AS filedetails
                "),
            )
            ->distinct();

        // ->where('inst.statusflag', 'Y');
        // $query->when($id, function ($query) use ($id) {
        //     $query->where('inst.instid', '=', $id);
        // });
        // $querySql = $query->toSql();
        // return $querySql;
        // print_r($querySql);
        return $query->get();
    }
}
