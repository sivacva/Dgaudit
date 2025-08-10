<?php

// app/Models/BaseModel.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    const DEPT_TABLE = 'audit.mst_dept';
    const REGION_TABLE      =    'audit.mst_region';
    const DIST_Table = 'audit.mst_district';
    const ROLETYPE                    = 'audit.mst_roletype';
    const DEPARTMENT_TABLE            = 'audit.mst_dept';
    const SUBCATEGORY_TABLE           = 'audit.mst_auditeeins_subcategory';
    const TRANSACTIONTYPE_TABLE      = 'audit.mst_transactiontype';
    const OTHERTRANS_TABLE      = 'audit.othertransactions';

    const STATE_TABLE                    = 'audit.mst_state';
    const MAPREGIONDISTRICT_TABLE       = 'audit.map_regiondistrict';
    const supercheck_TABLE      =   'audit.super_check';

    const AUDITEESCHEME_TABLE      =   'audit.auditeescheme';

    const AUDITEEDEPARTMENT_TABLE         =     "audit.mst_auditeedept";

    const IRREGULARITIES_TABLE = 'audit.mst_irregularities';

    const IRREGULARITIESCATEGORY_TABLE = 'audit.mst_irregularitiescategory';

    const IRREGULARITIESSUBCATEGORY_TABLE = 'audit.mst_irregularitiessubcategory';

    //---------------------------------MAP CALL FOR RECORDS TABLE-----------------------------------------------------

    const CALLFORRECORDS_AUDITEE_TABLE = 'audit.callforrecords_auditee';
    const MAPCALLFORRECORDS_TABLE = 'audit.map_callforrecord';
    const MSTAUDITEEINSCATEGORY_TABLE = 'audit.mst_auditeeins_category';
    const TRANSCALLFORRECORDS_TABLE = 'audit.trans_callforrecords';

    const LEAVETYPE_TABLE = 'audit.mst_leavetype';
    const TEMPRANKUSERS_TABLE      = 'audit.temp_ranked_users';
    const AUTOMATE_FUNCTION      =    'audit.fnauditteamplanautomation';
    const FINALISEPLAN_FUNCTION      =    'audit.distributeauditteamplan';
    const WORKALLOCATION_FUNCTION      =    'audit.workallocationAutomation';
    const GROUP_TABLE       =    'audit.group';

    const DESIGNATION_TABLE = 'audit.mst_designation';

    const TYPEOFAUDIT_TABLE = 'audit.mst_typeofaudit';
    const MAINOBJECTION_TABLE = 'audit.mst_mainobjection';
    //------------------------------------SUB WORK ALLOCATION TABLES------------------------------------------------------

    const MAPINST_TABLE    = 'audit.map_instdesig';

    //-----------------------------------------------END-----------------------------------------------------------------

    //--------------------------------------------END----------------------------------------------------------------
    //-------------------------------------------------Auditor institution mapping------------------------------------------------------------------------------------

    const AUDITOR_INSTMAPPING_TABLE = 'audit.auditor_instmapping';
    const DESIGNATION_Table = 'audit.mst_designation';

    //------------------------------------WORK ALLOCATION TABLES------------------------------------------------------

    const MAJORWORKALLOCATION_TABLE = 'audit.mst_majorworkallocationtype';

    //-----------------------------------------------END-----------------------------------------------------------------

    //------------------------------------SUB WORK ALLOCATION TABLES------------------------------------------------------

    const SUBWORKALLOCATION_TABLE = 'audit.mst_subworkallocationtype';
    const SLIPHISTORYTRANSACTION_TABLE      =    'audit.sliphistorytransactions';
    //-----------------------------------------------END-----------------------------------------------------------------
    //------------------------------------AUDITEE USER DETAILS TABLES------------------------------------------------------

    const INSTITUTION_TABLE = 'audit.mst_institution';
    const AUDITEEUSERDETAIL_TABLE = 'audit.audtieeuserdetails';
    const AUDITEEDEPT_TABLE = 'audit.mst_auditeedept';
    const AUDITEEDEPTREPORT_TABLE = 'audit.auditee_dept_reporting';



    //-----------------------------------------------END-----------------------------------------------------------------

    // Define common table names as constants or static properties
    //  const DEPT_TABLE = 'audit.mst_dept';
    // const DIST_Table = 'audit.mst_district';



    const AUDITDISTRICT_TABLE = 'audit.mst_auditdistrict';
    const REVENUEDISTRICT_TABLE = 'audit.mst_revenuedistrict';

    const ROLETYPE_TABLE = 'audit.mst_roletype';
    const ROLETYPEMAPPING_TABLE = 'audit.roletypemapping';
    const ROLEACTION_TABLE = 'audit.mst_roleaction';
    const ROLEMAPPING_TABLE = 'audit.rolemapping';

    const USERDETAIL_TABLE = 'audit.deptuserdetails';
    const CHARGEDETAIL_TABLE = 'audit.chargedetails';
    const USERCHARGEDETAIL_TABLE = 'audit.userchargedetails';
    //const REGION_TABLE      =    'audit.mst_region';
    const AUDITORINSTMAPPING_TABLE      =    'audit.auditor_instmapping';

    const REPORTCONTENTS_table    = 'audit.report_contents';

    const AUDITQUARTER_TABLE      = 'audit.mst_auditquarter';
    const MAPYEARCODE_TABLE      = 'audit.yearcode_mapping';
    const AUDITPERIOD_TABLE      = 'audit.mst_auditperiod';


    const AUDITPLAN_TABLE           =    'audit.auditplan';
    const AUDITPLANTEAM_TABLE       =    'audit.auditplanteam';
    const AUDITPLANTEAMMEM_TABLE    =    'audit.auditplanteammember';
    const INSTSCHEDULE_TABLE     =    'audit.inst_auditschedule';
    const INSTSCHEDULEMEM_TABLE     =    'audit.inst_schteammember';

    const TRANSACCOUNTDETAILS_TABLE     =    'audit.trans_accountdetails';
    const ACCOUNTPARTICULARS_TABLE     =    'audit.mst_accountparticulars';
    const FILEUPLOAD_TABLE     =    'audit.fileuploaddetail';

    const TEAMMEMBER_Table    = 'audit.auditplanteammember';
    // const TYPEOFAUDIT_TABLE = 'audit.mst_typeofaudit';


    const PROCESSFLAG_TABLE  = 'audit.mst_process';
    const MAINOBJ_TABLE  = 'audit.mst_mainobjection';
    const SUBOBJ_TABLE  = 'audit.mst_subobjection';
    const TRANSAUDITSLIP_TABLE  = 'audit.trans_auditslip';
    const TRANSWORKALLOCATION_TABLE = 'audit.trans_workallocation';
    const MAPWORKALLOCATION_TABLE = 'audit.map_workallocation';
    const MAJWORKALLOCATION_TABLE = 'audit.mst_majorworkallocationtype';
    const SLIPHISTORYDETAILS_TABLE = 'audit.sliphistorytransactions';
    const HOLIDAY_TABLE   = 'audit.mst_holiday';
    // ////////////////////////followup//////////
    const MAPALLOCATIONOBJECTION_TABLE = 'audit.map_allocation_objection';
    const LAGACY_TABLE = 'audit.lagacy';



    //------------------------------------Leave Management------------------------------------------------------

    const   TRANSACTIONFLOW_TABLE = 'audit.transactionflow';
    const   HISTORYTRANSACTION_TABLE = 'audit.historytransactions';
    const   INDLEAVEDETAIL_TABLE = 'audit.ind_leavedetail';
    const   TRANSACTIONDETAILTABLE = 'audit.transactiondetail'; 
    //-----------------------------------------------END-----------------------------------------------------------------



    //--------------------------------------Functions--------------------------------------------------------------------
    const DATATRANSFERFROMTOUSER = 'audit.dataTransferFromToUser';
    const WORKALLOCATIONDISTRIBUTION = 'audit.workAllocationDistribution';
    const MIGRATEALLOCATIONSLIP_FUNC = 'audit.migrate_work_allocation_and_audit_slip';
    const READYFORAUTOMATE_FUNCTION   = 'audit.readyforAutomatePlan';

    const NODATACHANGE_FUNCTION   = 'audit.nodatachange';

     //--------------------------------------Inspection--------------------------------------------------------------------

    const AUDITINSPECTION_TABLE          =   'audit.mst_auditinspection';
    const TRANSAUDITINSPECTION_TABLE     =   'audit.trans_auditinspection';
    const INSPECTIONHISTORY_TABLE        =   'audit.inspectionhistorytrans';

//---------------------------Audit Mode------------------------------------------------------------------------------
    const AUDITMODE_TABLE        =   'audit.mst_auditmode';

//--------------------------------------checklistplan--------------------------------------------------------------------
    const TEAMASSIGNMENTS_TABLE       =   'audit.team_assignments';
    const LOOP_UNTILFINISHED_FUNCTION       =   'audit.loop_until_finished';
    const CHECKLISTDETAIL_FUNCTION        =   'audit.checklistdel';

    //--------------------------------------OTP --------------------------------------------------------------------
    const OTPVERIFY_TABLE        =   'audit.otp_verify';

}
