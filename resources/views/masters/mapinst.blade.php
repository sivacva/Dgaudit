@section('content')
@extends('index2')
@include('common.alert')
@section('title', 'Institution')

@php

$sessionchargedel = session('charge');

$deptcode = $sessionchargedel->deptcode;
$regioncode = $sessionchargedel->regioncode;
$distcode = $sessionchargedel->distcode;

$make_dept_disable = $deptcode ? 'disabled' : '';
$make_region_disable = $regioncode ? 'disabled' : '';
$make_dist_disable = $distcode ? 'disabled' : '';

$distroletype =View::shared('Dist_roletypecode');
$sessionroletypecode = $sessionchargedel->roletypecode;

$dga_roletypecode = $DGA_roletypecode;
$Dist_roletypecode = $Dist_roletypecode;
$Re_roletypecode = $Re_roletypecode;
$Ho_roletypecode = $Ho_roletypecode;
$Admin_roletypecode = $Admin_roletypecode;

$currentquarter= $currentquarter;
$currentauditmodecode = 'N';


$quarterDetails =$quarterdetails[0];

if($sessionroletypecode == $dga_roletypecode)
{
$showFilter = true;

}
else
{
$showFilter = false;
}

@endphp

<link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="institute_detail">
                Institute Details
            </div>
            <div class="card-body collapse show">
                <form id="mapinst_form" name="mapinst_form">
                    <div class="alert alert-danger alert-dismissible fade show hide_this" role="alert"
                        id="display_error">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @csrf
                    <input type="hidden" class="form-control" id="instid" name="instid" />
                    <input type="hidden" class="form-control" id="auditeeuserid" name="auditeeuserid" />


                    <div class="row">
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label lang required" key="roletype" for="validationDefault01">Role
                                Type </label>
                            <input type="hidden" id="" name="" value="">
                            <select class="form-select mr-sm-2 lang-dropdown " id="roletypecode" name="roletypecode"
                                disabled>
                                <option value="" data-name-en="---Select Role Type---"
                                    data-name-ta="---செயல்பாட்டைத் தேர்ந்தெடுக்கவும் ---">---Select Role Type---
                                </option>

                                @foreach ($roletype as $roletypename)
                                <option value="{{ $roletypename->roletypecode }}"
                                    @if ($distroletype==$roletypename->roletypecode) selected @endif

                                    data-name-en="{{ $roletypename->roletypeelname }}"
                                    data-name-ta="{{ $roletypename->roletypetlname }}">
                                    {{ $roletypename->roletypeelname }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label lang required" key="department"
                                for="validationDefault01">Department </label>
                            <input type="hidden" id="" name="" value="">
                            <select class="form-select mr-sm-2 select2 lang-dropdown" id="deptcode" name="deptcode"
                                onchange="onchange_deptcode('','','')" <?php echo $make_dept_disable; ?>>
                                <option value="" data-name-en="---Select Department---"
                                    data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">---Select Department---
                                </option>

                                @if (!empty($dept) && count($dept) > 0)
                                @foreach ($dept as $department)
                                <option value="{{ $department->deptcode }}"
                                    @if (old('dept', $deptcode)==$department->deptcode) selected @endif
                                    data-name-en="{{ $department->deptelname }}"
                                    data-name-ta="{{ $department->depttlname }}">
                                    {{ $department->deptelname }}
                                </option>
                                @endforeach
                                @else
                                <option disabled data-name-en="No Department Available"
                                    data-name-ta="No Department Available">No Departments
                                    Available
                                </option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label lang required" key="instcat_label"
                                for="validationDefault01">Category </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="cat_code" name="cat_code"
                                onchange="onchange_catcode('','','')">
                                <option value="" data-name-en="---Select Category---"
                                    data-name-ta="--- வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>


                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 hide_this" id="subcat_div">
                            <label class="form-label lang required " key="sub_head" for="validationDefault01">
                                Sub
                                Category </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="subcatid" name="subcatid">
                                <option value="" data-name-en="---Select Sub Category---"
                                    data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">---Select Sub Category---
                                </option>


                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 " id="region_div">
                            <label class="form-label lang required" key="region" for="validationDefault01">Region
                            </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" onchange="onchange_region('','')"
                                id="regioncode" name="regioncode" <?php echo $make_region_disable; ?>>
                                <option value="" data-name-en="---Select Region---"
                                    data-name-ta="---பகுதியைத் தேர்ந்தெடுக்கவும்---">---Select Region---</option>
                                @if($regioncode)
                                @if (!empty($region) && count($region) > 0)
                                @foreach ($region as $regiondet)
                                <option value="{{ $regiondet->regioncode }}"
                                    @if (old('region', $regioncode)==$regiondet->regioncode) selected @endif
                                    data-name-en="{{ $regiondet->regionename }}"
                                    data-name-ta="{{ $regiondet->regiontname }}">
                                    {{ $regiondet->regionename }}
                                </option>
                                @endforeach
                                @else
                                <option disabled data-name-en="No Department Available"
                                    data-name-ta="No Department Available">No Departments
                                    Available
                                </option>
                                @endif
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 " id="dist_div">
                            <label class="form-label lang required" key="district" for="validationDefault01">District
                            </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="distcode" name="distcode"
                                onchange="onchange_distcode()" <?php echo $make_dist_disable; ?>>
                                <option value="" data-name-en="---Select District---"
                                    data-name-ta="--- மாவட்டத்தைத் தேர்ந்தெடுக்கவும்---">---Select District---</option>
                                @if($distcode)
                                @if (!empty($district) && count($district) > 0)
                                @foreach ($district as $dist)
                                <option value="{{ $dist->distcode }}"
                                    @if (old('dist', $distcode)==$dist->distcode) selected @endif
                                    data-name-en="{{ $dist->distename }}"
                                    data-name-ta="{{ $dist->disttname }}">
                                    {{ $dist->distename }}
                                </option>
                                @endforeach
                                @else
                                <option disabled data-name-en="No Department Available"
                                    data-name-ta="No Department Available">No Departments
                                    Available
                                </option>
                                @endif
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 ">
                            <label class="form-label lang required" key="audit_office_label" for="validationDefault01">Audit
                                Office Name
                            </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="auditoffice"
                                name="auditoffice" onchange="onchange_auditoffice()">
                                <option value="" data-name-en="---Select Audit Office---"
                                    data-name-ta="--- தணிக்கை அலுவலகத்தைத் தேர்ந்தெடுக்கவும்---">---Select Audit
                                    Office---
                                </option>

                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 ">
                            <label class="form-label lang required" key="audit_office_desig_label"
                                for="validationDefault01">Audit
                                Officer Designation
                            </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="audit_office_desig"
                                name="audit_office_desig" onchange="onchange_auditofficedesig()">
                                <option value="" data-name-en="---Select Audit Office Designation---"
                                    data-name-ta="---தணிக்கை அலுவலக பதவியைத் தேர்ந்தெடுக்கவும்---">---Select Audit
                                    Office
                                    Designation---
                                </option>

                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 ">
                            <label class="form-label lang required" key="audit_ad_label" for="validationDefault01">AAD /
                                AAO Name
                            </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="audit_ad" name="audit_ad">
                                <option value="" data-name-en="---Select Audit AAO/AD---"
                                    data-name-ta="--- AAO/AD தேர்ந்தெடுக்கவும்---">---Select AD/AAO---
                                </option>

                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 ">
                            <label class="form-label lang required" key="auditee_dept_label" for="validationDefault01">Auditee
                                Department
                            </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="auditee_dept"
                                name="auditee_dept">
                                <option value="" data-name-en="---Select Auditee Department---"
                                    data-name-ta="--- தணிக்கை துறையைத் தேர்ந்தெடுக்கவும்---">---Select Auditee
                                    Department---
                                </option>

                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 ">
                            <label class="form-label lang required" key="revenuedistrict"
                                for="validationDefault01">Revenue
                                District
                            </label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="revenuedistcode"
                                name="revenuedistcode">
                                <option value="" data-name-en="---Select Revenue District---"
                                    data-name-ta="---வருவாய் மாவட்டத்தைத் தேர்ந்தெடுக்கவும்---">---Select Revenue
                                    District---
                                </option>

                                @foreach ($revenuedistrict as $revenuedistrictdata)
                                <option value="{{ $revenuedistrictdata->revenuedistcode }}"
                                    data-name-en="{{ $revenuedistrictdata->revenuedistename }}"
                                    data-name-ta="{{ $revenuedistrictdata->revenuedisttname }}">
                                    {{ $revenuedistrictdata->revenuedistename }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label lang" for="validationDefault02" key="erpno_label" id="itmsno_label">ITMS/Reg
                                NO/Reference No
                            </label>
                            <input class="form-control alpha_numeric" id="itms_no" name="itms_no" maxlength="20"
                                data-placeholder-key="" placeholder='Enter the ITMS Number' />
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang " for="validationDefault02"
                                key="institution_eng_name">Institutte English Name
                            </label>
                            <input class="form-control name_special " id="instename" name="instename" maxlength="200"
                                data-placeholder-key="instename" />
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang " for="validationDefault02"
                                key="institution_tam_name">Institute Tamil Name
                            </label>
                            <input class="form-control name_special" id="insttname" name="insttname" maxlength="200"
                                data-placeholder-key="insttname" />
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang" for="validationDefault02" key="fees">Fees
                            </label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="fees"
                                        id="Y" value="Y">
                                    <label class="form-check-label lang" for="all" key="yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="fees"
                                        id="N" value="N" checked>
                                    <label class="form-check-label lang" for="district" key="no">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label lang required" key="categorization_label"
                                for="validationDefault01">Categorization based Income Turnover</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="categorization"
                                name="categorization">
                                <option value="" data-name-en="---Select Income Turnover---"
                                    data-name-ta="---வருமான வருவாயைத் தேர்ந்தெடுக்கவும்---">---Select Income
                                    Turnover---
                                </option>
                                <option value="I">I</option>
                                <option value="II">II
                                </option>
                                <option value="III">III</option>
                                <option value="IV">
                                    IV
                                </option>
                                <option value="V">
                                    V
                                </option>


                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label lang required" key="audit_mode_label"
                                for="validationDefault01">Audit Mode</label>

                            <select class="form-select mr-sm-2 lang-dropdown select2" id="audit_mode"
                                name="audit_mode" onchange="onchange_mode('')">
                                <option value="" data-name-en="---Select Mode---"
                                    data-name-ta="---பயன்முறையைத் தேர்ந்தெடுக்கவும்---">---Select Mode
                                </option>
                                @foreach ($auditmode as $mode)
                                <option value="{{ $mode->auditmodecode }}"


                                    data-name-en="{{ $mode->auditmodeename }}"
                                    data-name-ta="{{ $mode->auditmodetname }}">
                                    {{ $mode->auditmodeename }}
                                </option>
                                @endforeach



                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="row py-2">
                                <div class="col-md-4">
                                    <label class="form-label required lang" for="validationDefault02"
                                        key="applicable_for_label">Applicable
                                    </label>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-9">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input success" name="applicablefor[]" type="checkbox" id="Q1" value="Q1" onchange="onchange_applicablefor()" disabled>
                                            <label class="form-check-label" for="Q1">Quarter 1</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input success" name="applicablefor[]" type="checkbox" id="Q2" value="Q2" onchange="onchange_applicablefor()" disabled>
                                            <label class="form-check-label" for="Q2">Quarter 2</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input success" name="applicablefor[]" type="checkbox" id="Q3" value="Q3" onchange="onchange_applicablefor()" disabled>
                                            <label class="form-check-label" for="Q3">Quarter 3</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input success" name="applicablefor[]" type="checkbox" id="Q4" value="Q4" onchange="onchange_applicablefor()" disabled>
                                            <label class="form-check-label" for="Q4">Quarter 4</label>
                                        </div>

                                    </div>

                                </div>

                            </div>
                        </div>



                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang" for="validationDefault02"
                                key="total_mandays">Total
                                Mandays
                            </label>
                            <input class="form-control only_numbers" id="mandays" name="mandays" maxlength="4"
                                data-placeholder-key="mandays" />
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang" for="validationDefault02"
                                key="teamsize">Team Size
                            </label>
                            <input class="form-control only_numbers" id="team_size" name="team_size" maxlength="2"
                                data-placeholder-key="team_size" />
                        </div>
                        <div class="col-md-3 mb-1 mt-2 ">
                            <label class="form-label lang required" key="inst_type" for="validationDefault01">Institution Type</label>

                            <select class="form-select mr-sm-2 lang-dropdown " id="inst_type" name="inst_type" onchange="onchange_insttype('')">
                                <option value="" data-name-en="---Select Institution Type---"
                                    data-name-ta="--- நிறுவன வகையைத் தேர்ந்தெடுக்கவும்---">---Select Institution Type---
                                </option>


                                <option value="H" data-name-en="Hub" data-name-ta="நோடல்">Hub</option>
                                <option value="S" data-name-en="Spoke" data-name-ta="துணை">Spoke
                                </option>

                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2 hide_this" id="parentinst_div">
                            <label class="form-label  lang " for="validationDefault02" id="parentinstid_label"
                                key="parent_inst">Parent Institution Name
                            </label>
                            <select class="form-select mr-sm-2 lang-dropdown select2" id="parentinstid" name="parentinstid">
                                <option value="" data-name-en="---Select Parent  Institution ---"
                                    data-name-ta="--- நிறுவன வகையைத் தேர்ந்தெடுக்கவும்---">---Select
                                    Parent Institution ---
                                </option>
                            </select>

                        </div>

                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label lang required" key="risk_type" for="validationDefault01">Risk
                                Type</label>

                            <select class="form-select mr-sm-2 lang-dropdown " id="risktype" name="risktype">
                                <option value="" data-name-en="---Select Risk Type---"
                                    data-name-ta="--- ஆபத்து வகை தேர்ந்தெடுக்கவும்---">---Select Risk Type---
                                </option>


                                <option value="H" data-name-en="High" data-name-ta="உயர்">High</option>
                                <option value="M" data-name-en="Medium" data-name-ta="நடுத்தரம்">Medium
                                </option>
                                <option value="L" data-name-en="Low" data-name-ta="குறைந்த">Low</option>

                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang" for="validationDefault02" key="rank_order">Rank
                                Order
                            </label>
                            <input class="form-control only_number" id="rankorder" name="rankorder"
                                data-placeholder-key="rankorder" maxlength="4" />
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label lang required" key="audit_qaurter"
                                for="validationDefault01">Audit
                                Quarter </label>

                            <select class="form-select mr-sm-2 lang-dropdown" id="audit_quarter"
                                name="audit_quarter" disabled>
                                <option value="" data-name-en="---Select Audit Quarter---"
                                    data-name-ta="--- தணிக்கை காலாண்டைத் தேர்ந்தெடுக்கவும்---">---Select Audit
                                    Quarter---
                                </option>



                            </select>
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label lang required" key="audit_sensitive"
                                for="validationDefault01">Audit
                                Sensitive </label>

                            <select class="form-select mr-sm-2 lang-dropdown " id="typeofaudit" name="typeofaudit">
                                <option value="" data-name-en="---Select Audit Sensitive---"
                                    data-name-ta="---தணிக்கை உணர்திறனைத் தேர்ந்தெடுக்கவும்---">---Select Audit
                                    Sensitive---
                                </option>

                            </select>
                        </div>
                        <!-- <div class="col-md-3 mb-1 mt-2">
                                <label class="form-label required lang" for="validationDefault02"
                                    key="max_leave_label">Maximum
                                    Leaves
                                </label>
                                <input class="form-control only_numbers" id="max_leave" name="max_leave" maxlength="3"
                                    data-placeholder-key="mandays" placeholder='Enter the maximum number of days' />
                            </div> -->
                        <!-- <div class="col-md-3 mb-1 mt-2">
                                <label class="form-label required lang" for="validationDefault02"
                                    key="temp_audit_label">Template
                                    Audit
                                </label>
                                <div class="d-flex align-items-center">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input success" type="radio" name="template_audit"
                                            id="Y" value="Y">
                                        <label class="form-check-label lang" for="all" key="yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input success" type="radio" name="template_audit"
                                            id="N" value="N" checked>
                                        <label class="form-check-label lang" for="district" key="no">No</label>
                                    </div>
                                </div>
                            </div> -->
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang" for="validationDefault02"
                                key="carry_forward_label">Carry
                                Forward
                            </label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="cary_fwd"
                                        id="Y" value="Y">
                                    <label class="form-check-label lang" for="all" key="yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="cary_fwd"
                                        id="N" value="N" checked>
                                    <label class="form-check-label lang" for="district" key="no">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang" for="validationDefault02"
                                key="consolidated_label">Consolidated Report
                            </label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="cons_report"
                                        id="Y" value="Y">
                                    <label class="form-check-label lang" for="all" key="yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="cons_report"
                                        id="N" value="N" checked>
                                    <label class="form-check-label lang" for="district" key="no">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang" for="validationDefault02"
                                key="assemble_report">Assembly
                                Report
                            </label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="assemble_report"
                                        id="Y" value="Y">
                                    <label class="form-check-label lang" for="all" key="yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="assemble_report"
                                        id="N" value="N" checked>
                                    <label class="form-check-label lang" for="district" key="no">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang" for="validationDefault02"
                                key="nodal_eng_name">Nodal
                                Person English Name
                            </label>
                            <input class="form-control name" id="nodalperson_ename" name="nodalperson_ename"
                                maxlength="75" data-placeholder-key="nodalperson_ename" />
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang" for="validationDefault02"
                                key="nodal_tam_name">Nodal
                                Person Tamil name
                            </label>
                            <input class="form-control name" id="nodalperson_tname" name="nodalperson_tname"
                                maxlength="75" data-placeholder-key="nodalperson_tname" />
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang" for="validationDefault02" key="email">Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email" maxlength="100"
                                data-placeholder-key="email" />
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang" for="validationDefault02" key="mobile">Mobile
                                Number
                            </label>
                            <input class="form-control only_numbers mobile_number" id="mobile" name="mobile" maxlength="10"
                                data-placeholder-key="mobile" />
                        </div>

                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang " for="validationDefault02"
                                key="nodal_desig">Nodal
                                Person Designation
                            </label>
                            <input class="form-control name" id="nodalperson_desigcode" name="nodalperson_desigcode"
                                maxlength="100" data-placeholder-key="nodalperson_desigcode" />
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang " for="validationDefault02" key="auditee_offc_addr_label">Auditee
                                Office Address
                            </label>
                            <input class="form-control alpha_numeric" id="auditee_ofaddr" name="auditee_ofaddr"
                                maxlength="250" data-placeholder-key="nodalperson_desigcode"
                                placeholder="Enter Auditee Office Address" />
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang" for="validationDefault02"
                                key="audit_certificate">Audit
                                Certificate
                            </label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="auditcertificate"
                                        id="Y" value="Y">
                                    <label class="form-check-label lang" for="all" key="yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="auditcertificate"
                                        id="N" value="N" checked>
                                    <label class="form-check-label lang" for="district" key="no">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang" for="validationDefault02" key="annadhanam_label">
                                Is Annadhanam required?
                            </label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="annadhanam_only"
                                        id="Y" value="Y">
                                    <label class="form-check-label lang" for="all" key="yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input success" type="radio" name="annadhanam_only"
                                        id="N" value="N" checked>
                                    <label class="form-check-label lang" for="district" key="no">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-1 mt-2">
                            <label class="form-label required lang " for="validationDefault02" key="turn_over_label">Turn
                                Over
                            </label>
                            <input class="form-control decimal_numbers" maxlength="15" id="turnover" name="turnover" maxlength="100"
                                data-placeholder-key="" placeholder="Enter Turnover (Amount Ltr)" />
                        </div>


                        <hr class="p-2 mt-2">
                        <div class="col-12 ">
                            <div class="card ">
                                <div class="card-header">
                                    <h5>Audit Reporting Officer</h5>
                                </div>
                                <div class="card-body">
                                    <div id="appendusers" class="single-note-item">
                                        <div id="addrowUsers">
                                            <div class="row">
                                                <div class="col-md-1">

                                                </div>

                                                <div class="col-md-3 ms-2">

                                                </div>


                                                <div class="col-md-3 ms-2">


                                                </div>

                                                <div class="col-md-3">

                                                </div>
                                            </div>
                                            <div class="d-flex mt-2 work-row-insert" id="row0">
                                                <input type="hidden" name="auditee_report[1]" id="auditee_report0"
                                                    value="UserId_1">

                                                <div class="col-md-1">
                                                    <label class="form-label lang" key="s_no"
                                                        for="validationDefaultUsername">S.No</label>
                                                    <input type="text" class="form-control alpha_numeric"
                                                        value="1" disabled>
                                                </div>



                                                <div class="col-md-3 ms-2">
                                                    <label class="form-label lang" key="designation"
                                                        for="validationDefaultUsername">Designation</label>
                                                    <input type="text" class="form-control name"
                                                        data-placeholder-key="designation_ph"
                                                        name="reportdesignation[1]" id="reportdesignation0"
                                                        value="" placeholder="Enter Designation">
                                                </div>

                                                <div class="col-md-3 ms-2">
                                                    <label class="form-label lang"
                                                        for="validationDefaultUsername">Email</label>
                                                    <input type="email" class="form-control "
                                                        data-placeholder-key="" name="reportemail[1]"
                                                        id="reportemail0" value="" placeholder="Enter Email" />
                                                </div>


                                                <div class="col-md-3 ms-2">
                                                    <label class="form-label lang" key="mobile"
                                                        for="validationDefaultUsername">Mobile Number</label>
                                                    <input type="text" class="form-control only_numbers"
                                                        data-placeholder-key="" name="reportmobile[1]"
                                                        id="reportmobile0" value="" maxlength="13"
                                                        placeholder="Enter Mobie" />
                                                </div>

                                                <div class="col-md-3 actionbtns">
                                                    <label class="form-label lang" key="action"
                                                        for="validationDefaultUsername">Action</label><br>
                                                    <button type="button"
                                                        class="btn btn-success fw-medium ms-2 addRowBtn"
                                                        onclick="addNewWorkRow(event,'insert')">
                                                        <i class="ti ti-circle-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div id="EditrowUsers">

                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>


                    </div>
            </div>


            <div class="row justify-content-center">
                <div class="col-md-2 mx-auto mb-2">
                    <input type="hidden" name="action" id="action" value="insert" />
                    <button class="btn button_save" type="submit" action="insert" id="buttonaction"
                        name="buttonaction">Save</button>
                    <button type="button" class="btn btn-danger" id="reset_button"
                        onclick="reset_form()">Clear</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
</div>

<div class="card card_border mt-2">
    <div class="card-header card_header_color lang" key="institute_detail">Institution Details</div>
    <div class="card-body">
        <div class="datatables">

       <div class="row">
			 @php
            if ($showFilter){
            @endphp
                <div class="col-md-3 mb-1 mt-2 text-center">
                    <label class="form-label lang required" key="department" for="deptcode1">Department</label>
                    <select class="form-select lang-dropdown" id="deptcode1" name="deptcode1" onchange="fetchAlldata()" style="max-width: 100%;">
                        @if (!empty($dept) && count($dept) > 0)
                        @foreach ($dept as $department)
                        <option value="{{ $department->deptcode }}"
                            @if (old('dept', $deptcode)==$department->deptcode) selected @endif
                            data-name-en="{{ $department->deptelname }}"
                            data-name-ta="{{ $department->depttlname }}">
                            {{ $department->deptelname }}
                        </option>
                        @endforeach
                        @else
                        <option disabled data-name-en="No Department Available" data-name-ta="No Department Available">
                            No Departments Available
                        </option>
                        @endif
                    </select>
                </div>
			@php
            }
            @endphp
                <div class="col-md-3 mb-1 mt-2 text-center">
                    <label class="form-label lang required" key="quarter" for="quarter">Quarter</label>
                    <select class="form-select lang-dropdown" id="quarter" name="quarter" onchange="fetchAlldata()" style="max-width: 100%;">
                        <option value="Q1">Q1</option>
                        <option value="Q2">Q2</option>
                        <option value="Q3">Q3</option>
                        <option value="Q4">Q4</option>
                    </select>
                </div>
            </div>

			

              <div class="table-responsive hide_this" id="tableshow">
  

             

                <table id="mapinst_table"
                    class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                    <thead>
                        <tr>
                            <th class="lang" key="s_no">S.No</th>
                            <th class="lang" key="department">Department Name</th>
                            <th class="lang" key="instname_label">Institute Name</th>
                            <th class="lang" key="category">Category</th>

                            <th class="lang" key="region">Region</th>
                            <th class="lang" key="district">District</th>
                            <th class="lang" key="rankorder">Rank Order</th>
                            {{-- <th class="lang" key="">Fees</th>
                                <th class="lang" key="">Mandays</th>
                                <th class="lang" key="">Risk Type</th> --}}

                            <th class="lang" key="nodal_details">Nodal Person Details</th>

                            <th class="all lang" key="action">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div id='no_data' class='hide_this lang text-center' key="no_data">
            <center class="lang" key="no_data">No Data Available</center>

        </div>
    </div>
</div>
</div>
<script src="{{ asset('assets/libs/jquery-validation/dist/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<!-- Download Button Start -->

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<!-- select2 -->
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>

<script>
    let designationCount = 1; // Start count
    let designationdata = [];
    // Keypress: allow digits, one dot, and restrict input based on the rules
    $(document).on("keypress", ".decimal_numbers", function(e) {
        const char = String.fromCharCode(e.which);
        const val = $(this).val();

        // Allow digits (0-9) only
        if (/\d/.test(char)) {
            // If no decimal point, allow up to 10 digits
            if (!val.includes('.') && val.length < 12) {
                return true;
            }
            // If decimal point is present, allow only two digits after the decimal
            if (val.includes('.') && val.split('.')[1]?.length < 2) {
                return true;
            }
        }

        // Allow dot only if there's no dot already
        if (char === '.' && !val.includes('.')) {
            return true;
        }

        // Block other characters
        e.preventDefault();
    });

    // Paste: allow only numbers with optional one dot and up to two decimals, and a max of 10 digits before decimal
    $(document).on("paste", ".decimal_numbers", function(e) {
        const pasted = (e.originalEvent || e).clipboardData.getData('text').trim();

        // Valid if digits with optional single decimal point and up to 10 digits before decimal
        if (!/^\d{1,10}(\.\d{0,2})?$/.test(pasted)) {
            e.preventDefault();
        }
    });

    // Blur: format to 2 decimal places (if there's a decimal point, and two digits after it)
    $(document).on("blur", ".decimal_numbers", function() {
        let val = $(this).val().trim();

        // If valid number, format to 2 decimal places
        if (/^\d+(\.\d{1,2})?$/.test(val)) {
            const num = parseFloat(val);
            if (!isNaN(num)) {
                $(this).val(num.toFixed(2)); // Example: 34 → 34.00 or 34.4 → 34.40
            }
        }
    });



    $(".name_special").on("keypress", function(event) {
        var charCode = event.which || event.keyCode;
        var charStr = String.fromCharCode(charCode);

        // Allow English letters, Tamil letters (U+0B80–U+0BFF), digits, and hyphen
        if (/^[a-zA-Z஀-௿0-9\-\/.,() ]$/.test(charStr)) {
            return true;
        } else {
            event.preventDefault(); // Block invalid characters
            return false;
        }
    });
    // Regex pattern to allow English letters, Tamil letters (U+0B80 - U+0BFF), digits, space, and special characters - / . ,
    const allowedPattern = /^[a-zA-Z஀-௿0-9\-\/., ]+$/;

    // Apply validation on keypress and clean invalid characters on paste
    function applyInputRestrictions(selector) {
        $(document).on("keypress", selector, function(event) {
            const charCode = event.which || event.keyCode;
            const charStr = String.fromCharCode(charCode);

            if (!allowedPattern.test(charStr)) {
                event.preventDefault();
                return false;
            }
            return true;
        });

        $(document).on("paste", selector, function(event) {
            const clipboardData = (event.originalEvent || event).clipboardData.getData("text");
            const cleaned = clipboardData.split('').filter(char => allowedPattern.test(char)).join('');

            event.preventDefault();

            const input = event.target;
            const start = input.selectionStart;
            const end = input.selectionEnd;
            const value = input.value;

            input.value = value.slice(0, start) + cleaned + value.slice(end);
            input.setSelectionRange(start + cleaned.length, start + cleaned.length);
        });
    }

    // Apply on multiple fields
    applyInputRestrictions("#instename, #insttname, .text_special");


    function removedeptRow(button, action) {
        // Find the closest row and remove it
        const rowToRemove = $(button).closest('.work-row-' + action + '');
        rowToRemove.remove();

        // Recalculate and update the S.NO values after removing a row
        //updateSerialNumbers();
    }
    // Allowed characters during typing
    const emailCharRegex = /^[a-zA-Z0-9@._\-+]+$/;
    // Full email format validation (e.g. abc@gmail.com)
    const fullEmailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    // Keypress: block disallowed characters
    $(document).on('keypress', 'input[type="email"]', function(e) {
        const char = String.fromCharCode(e.which);
        if (!emailCharRegex.test(char)) {
            e.preventDefault();
        }
    });

    // Paste: prevent pasting invalid characters
    $(document).on('paste', 'input[type="email"]', function(e) {
        const pastedText = (e.originalEvent || e).clipboardData.getData('text');
        if (!emailCharRegex.test(pastedText)) {
            e.preventDefault();
        }
    });

    // Blur: validate full email format
    $(document).on('blur', 'input[type="email"]', function() {
        const email = $(this).val().trim();
        if (email && !fullEmailRegex.test(email)) {

            $(this).addClass('is-invalid'); // Add error class (optional)
        } else {
            $(this).removeClass('is-invalid'); // Remove error if valid
        }
    });

    $(document).on("keypress", ".only_numbers", function(event) {
        const charCode = event.which ? event.which : event.keyCode;
        if (charCode >= 48 && charCode <= 57) {
            return true;
        } else {
            event.preventDefault(); // prevent invalid character input
        }
    });
    const mobileRegex = /^(?!([6-9])\1{9}$)[6-9][0-9]{9}$/; // Validates 10-digit starting with 6-9 and not all same digits

    // Keypress: allow only digits (0-9)
    $(document).on('keypress', 'input.mobile_number', function(event) {
        const charCode = event.which ? event.which : event.keyCode;
        if (charCode < 48 || charCode > 57) {
            event.preventDefault();
        }
    });

    // Paste: prevent if not digits or invalid format
    $(document).on('paste', 'input.mobile_number', function(e) {
        const pasted = (e.originalEvent || e).clipboardData.getData('text');
        if (!/^\d+$/.test(pasted) || pasted.length > 10) {
            e.preventDefault();
        }
    });

    // Blur: full mobile format validation
    $(document).on('blur', 'input.mobile_number', function() {
        const mobile = $(this).val().trim();
        if (!mobileRegex.test(mobile)) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    function addNewWorkRow(event, action = '') {
        // Get the next row index based on existing rows
        const rowCount = $('.work-row-' + action + '').length + 1; // Start from 2, hence +1

        var allowedusers = 3;
        if (rowCount > allowedusers) {
            getLabels_jsonlayout([{
                id: 'alloweduserlimit',
                key: 'alloweduserlimit'
            }], 'N').then((text) => {
                passing_alert_value('Confirmation', Object.values(
                        text)[0], 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'confirmation_alert');
            });
            return; // Prevent adding a new row
        }

        // HTML for the new row
        const newRowHtml = `
                <div class="d-flex mt-2 work-row-${action}" id="row${rowCount}">
                    <input type="hidden" name="auditee_report[${rowCount}]" value="UserId_${rowCount}">

                    <div class="col-md-1">
                        <input type="text" class="form-control alpha_numeric" value="${rowCount}" disabled>
                    </div>

                    <div class="col-md-3 ms-2">
                        <input type="text" class="form-control name"  data-placeholder-key="username" name="reportdesignation[${rowCount}]"  value="" placeholder="Enter designation">
                    </div>


                    <div class="col-md-3 ms-2">
                     <input type="email" class="form-control "  data-placeholder-key="username" name="reportemail[${rowCount}]"  value="" placeholder="Enter Email">

                    </div>

                    <div class="col-md-3 ms-2">
                        <input type="text" class="form-control only_numbers"  maxlength = "13" data-placeholder-key="username" name="reportmobile[${rowCount}]"  value="" placeholder="Enter Mobile">

                    </div>

                    <div class="col-md-3">
                        <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn" onclick="addNewWorkRow(event,'${action}')">
                            <i class="ti ti-circle-plus"></i>
                        </button>
                        <button type="button" class="btn btn-danger fw-medium ms-2 removeRowBtn" onclick="removedeptRow(this,'${action}')">
                            <i class="ti ti-circle-minus"></i>
                        </button>
                    </div>
                </div>
            `;
        $(".only_numbers").on("keypress", function(event) {
            if (event.charCode >= 48 && event.charCode <= 57)
                return true; // let it happen, don't do anything
            else return false;
        });
        if (action == 'insert') {
            $('#addrowUsers').append(newRowHtml);

        } else {
            $('#EditrowUsers').append(newRowHtml);

        }
        var lang = getLanguage('Y');


        applyValidationToNewFields(`reportdesignation[${rowCount}]`, 'Enter Designation');
        applyValidationToNewFields(`reportemail[${rowCount}]`, 'Enter Email');
        applyValidationToNewFields(`reportmobile[${rowCount}]`, 'Enter Mobile');


        // Append the new row using jQuery to the container
    }

    // function populateTeamMembersDropdown(teammembers) {

    //     let memberDesignationDiv = document.getElementById("member_designation");
    //     //  memberDesignationDiv.innerHTML = ""; // Clear existing dropdowns
    //     //Array.isArray(teammembers)); // Should be true
    //     //teammembers);
    //     // Reset designation count
    //     designationCount = 1;

    //     teammembers.forEach((member, index) => {
    //         designationCount++;
    //         let prevAddBtn = memberDesignationDiv.querySelector(".addRowBtn");
    //         if (prevAddBtn) {
    //             prevAddBtn.outerHTML = `<button type="button" class="btn btn-danger fw-medium ms-2 mt-4" onclick="removeRow(this)">
    //                         <i class="ti ti-trash"></i>
    //                     </button>`;
    //         }
    //         let newSelectId = `member_desig_${designationCount}`;

    //         let newRow = document.createElement("div");
    //         newRow.className = "col-md-3 mb-1 mt-2 designation-item d-flex align-items-center";

    //         newRow.innerHTML = `
    //              <div class="w-100">
    //                  <label class="form-label lang required" key="">Team Member Designation ${designationCount}</label>
    //                  <select class="form-select mr-sm-2 lang-dropdown select2" id="${newSelectId}" name="${newSelectId}">
    //                      <option value="">---Select Designation---</option>
    //                  </select>
    //              </div>
    //          <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn mt-4" onclick="addNewRow()">
    //         <i class="ti ti-circle-plus"></i>
    //        </button>`


    //         memberDesignationDiv.appendChild(newRow);
    //         $(`#${newSelectId}`).select2();
    //         // Populate the dropdown with the correct value
    //         populateDesignationDropdown(newSelectId, member);

    //     });

    //     // designationCount = teammembers.length;

    // }

    function populateDesignationDropdown(selectId, selectedValue = "") {

        let dropdown = document.getElementById(selectId);
        if (!dropdown) return;

        dropdown.innerHTML = `<option value="">---Select Designation---</option>`;
        //'log' + selectedValue)
        if (window.designationdata) {
            window.designationdata.forEach(designation => {

                let option = document.createElement("option");
                option.value = designation.desigcode;
                option.textContent = designation.desigelname;
                if (designation.desigcode == selectedValue.trim()) {

                    option.selected = true;
                }
                dropdown.appendChild(option);
            });
        }
        // Re-initialize Select2 (if used)
        //   $(`#${selectId}`).select2();
    }




    // ✅ Add Row Function
    function addNewRow() {

        if (designationCount >= 4) {
            alert("Maximum 4 designations allowed!");
            return;
        }

        designationCount++;
        let memberDesignationDiv = document.getElementById("member_designation");

        // Remove previous Add button, replace with Delete button
        let prevAddBtn = memberDesignationDiv.querySelector(".addRowBtn");
        if (prevAddBtn) {
            prevAddBtn.outerHTML = `<button type="button" class="btn btn-danger fw-medium ms-2 mt-4" onclick="removeRow(this)">
                                <i class="ti ti-trash"></i>
                            </button>`;
        }

        // Create new designation row
        let newRow = document.createElement("div");
        newRow.className = "col-md-3 mb-1 mt-2 designation-item d-flex align-items-center";
        let newSelectId = `member_desig_${designationCount}`;

        newRow.innerHTML = `
                 <div class="w-100">
                     <label class="form-label lang required">Team Member Designation ${designationCount}</label>
                     <select class="form-select mr-sm-2 lang-dropdown select2" id="${newSelectId}" name="${newSelectId}">
                         <option value="">---Select Designation---</option>
                     </select>
                 </div>
                 <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn mt-4" onclick="addNewRow()">
                     <i class="ti ti-circle-plus"></i>
                 </button>
             `;

        // Append new row inside member_designation div
        memberDesignationDiv.appendChild(newRow);
        $(`#${newSelectId}`).select2();
        // ✅ Populate the new dropdown
        populateDesignationDropdown(newSelectId, '');
    }
    // Function to remove a row
    function removeRow(button) {
        let row = button.parentElement;
        row.remove(); // Remove row
        designationCount--;

        // Get remaining rows
        let rows = document.querySelectorAll("#member_designation .designation-item");

        rows.forEach((row, index) => {
            let label = row.querySelector("label");
            let select = row.querySelector("select");

            let selectedValue = select.value; // Store selected value before updating ID

            let newIndex = index + 1;
            label.innerText = `Team Member Designation ${newIndex}`;
            select.id = `member_desig_${newIndex}`;
            select.name = `member_desig_${newIndex}`;

            select.value = selectedValue; // Restore the selected value
        });

        // Remove all Add buttons and ensure only last row has one
        document.querySelectorAll(".addRowBtn").forEach(btn => btn.remove());

        if (rows.length > 0) {
            let lastRow = rows[rows.length - 1]; // Last row
            let addButton = document.createElement("button");
            addButton.type = "button";
            addButton.className = "btn btn-success fw-medium ms-2 addRowBtn mt-4";
            addButton.innerHTML = `<i class="ti ti-circle-plus"></i>`;
            addButton.onclick = addNewRow;

            lastRow.appendChild(addButton);
        }
    }



    let dataFromServer;
    let auditeedept_reportdata;


    var sessiondeptcode = ' <?php echo $deptcode; ?>';
    var sessionregioncode = ' <?php echo $regioncode; ?>';
    var sessiondistcode = ' <?php echo $distcode; ?>';



    // function updateSelect2Language(lang) {
    //     $('.select2 option').each(function() {
    //         var text = (lang === "en") ? $(this).attr('data-name-en') : $(this).attr('data-name-ta');
    //         $(this).text(text);
    //     });
    //     $('.select2').select2();
    // }

    // function changeLanguage(lang) {
    //     window.localStorage.setItem('lang', lang);
    //     updateSelect2Language(lang);
    // }

    function fetchAlldata() {

        let lang = $('html').attr('lang') || 'en';
        let deptcode = $('#deptcode1').val();
        let quarter = $('#quarter').val();
        $.ajax({
            url: '/masters/fetch_mapInstDet', // For creating a new user or updating an existing one
            type: 'POST',
            data: {
                deptcode: deptcode,
               quarter: quarter,
		form: 'fetch',
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    // alert('adds');
                    $('#tableshow').show();
                    $('#usertable_wrapper').show();
                    $('#no_data').hide();
                    dataFromServer = response.data;
                    // auditeedept_reportdata = response.audit_reportdata;
                    // alert(dataFromServer);
                    renderTable(lang);
                } else {

                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                }
            },
            error: function() {
                $('#tableshow').hide();
                $('#no_data').show(); // Show "No Data Available" on error
            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);

                var errorMessage = response.error ||
                    'An unknown error occurred';

                passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');


                // Optionally, log the error to console for debugging
                console.error('Error details:', xhr, status, error);
            }
        });
    }

    $(document).on('click', '.edit_btn', function() {
        // Add more logic here

        var id = $(this).attr('id'); //Getting id of user clicked edit button.

        if (id) {
            // reset_form();
            fetchsinglemap_instdata(id);

        }
    });

    function fetchsinglemap_instdata(instid) {
	let deptcode = $('#deptcode1').val();
  	let quarter = $('#quarter').val();
        $.ajax({
            url: '/masters/fetch_mapInstDet', // Your API route to get user details
            method: 'POST',
            data: {
		deptcode: deptcode,
                quarter: quarter,
                instid: instid,
		form: 'edit'
            }, // Pass deptuserid in the data object
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                if (response.success) {
                    $('#display_error').hide();
                    reset_form();
                    // change_button_as_update('map_allocation_objection', 'action', 'buttonaction',
                    //     'display_error', '', '', 'update');

                    changeButtonAction('mapinst_form', 'action', 'buttonaction',
                        'reset_button',
                        'display_error', @json($updatebtn),
                        @json($clearbtn),
                        @json($update));

                    const detail = response.data[0];

                    var auditreportdata = response.audit_reportdata;


                    // designationArray = response.desigdata || [];
                    designationdata = response.desigdata;
                    // designationdata = designationArray.map(item => item.desigcode);
                    // //designationdata);

                    // alert(detail.nodalperson_ename);
                    $('#instid').val(detail.encrypted_instid);
                    $('#auditeeuserid').val(detail.encrypted_auditeeuserid);
                    $('#insttname').val(detail.insttname);
                    $('#instename').val(detail.instename);
                    $('#risktype').val(detail.risktype);
                    $('#cat_code').val(detail.cat_code);
                    $('#categorization').val(detail.categorization);
                    //$('#auditmode').val(detail.audit_mode);
                    $('#team_size').val(detail.teamsize);
                    $('#parent_instename').val(detail.parentinstename);
                    $('#nodalperson_ename').val(detail.nodalperson_ename);
                    $('#nodalperson_tname').val(detail.nodalperson_tname);
                    $('#email').val(detail.email);
                    $('#mobile').val(detail.mobile);
                    $('#nodalperson_desigcode').val(detail.nodalperson_desigcode);
                    $('#rankorder').val(detail.rankorder);
                    $('#mandays').val(detail.mandays);
                    $('#rankorder').val(detail.rankorder);
                    $('#rankorder').val(detail.rankorder);
                    $('#auditee_dept').val(detail.auditeedeptcode);
                    $('#itms_no').val(detail.erpno);
                    $('#categorization').val(detail.categorization);
                    $('#max_leave').val(detail.leavedays);
                    $('#turnover').val(detail.turnover);
                    $('#auditee_ofaddr').val(detail.auditeeofficeaddress);
                    // $('#audit_mode').val(detail.auditmode);
                    $('#inst_type').val(detail.insttype);

                    onchange_insttype(detail.insttype);


                    let selectedQuarters = [];
                    ['Q1', 'Q2', 'Q3', 'Q4'].forEach(q => {
                        const checkbox = document.getElementById(q);
                        if (checkbox) {
                            checkbox.checked = detail[q] === 'Y';
                            if (checkbox.checked) {
                                selectedQuarters.push(q);
                            }

                        }
                    });

                    $('#categorization').select2('destroy');
                    $('#categorization').val(detail.categorization);
                    $('#categorization').select2();

                    $('#audit_mode').select2('destroy');
                    $('#audit_mode').val(detail.auditmode);
                    $('#audit_mode').select2();

                    //  onchange_applicablefor(detail.auditmode,'edit')
                    onchange_mode(detail.auditmode, 'edit', selectedQuarters);

                    $('#deptcode').select2('destroy');
                    $('#deptcode').val(detail.deptcode);
                    $('#deptcode').select2();

                    $('#deptcode').select2('destroy');
                    $('#deptcode').val(detail.deptcode);
                    $('#deptcode').select2();

                    $('#distcode').select2('destroy');
                    $('#distcode').val(detail.distcode);
                    $('#distcode').select2();

                    $('#revenuedistcode').select2('destroy');
                    $('#revenuedistcode').val(detail.revenuedistcode);
                    $('#revenuedistcode').select2();


                    // $('input[name="template_audit"][value="' + detail.templateaudit + '"]')
                    //     .prop('checked', true);
                    $('input[name="cary_fwd"][value="' + detail.carryforward + '"]')
                        .prop('checked', true);
                    $('input[name="cons_report"][value="' + detail.consreport + '"]')
                        .prop('checked', true);
                    $('input[name="assemble_report"][value="' + detail.assemblereport + '"]')
                        .prop('checked', true);
                    // $('#head_isntdesigid').val(detail.teamhead_mapid);

                    $('input[name="fees"][value="' + detail.fees + '"]')
                        .prop('checked', true);
                    $('input[name="annadhanam_only"][value="' + detail.annadhanam_only + '"]')
                        .prop('checked', true);
                    $('input[name="auditcertificate"][value="' + detail.auditcertificate + '"]')
                        .prop('checked', true);

                    // var memberdesig = detail.teammembers_desigcode;
                    // var memberdesigArray = memberdesig.split(",");
                    var instid = detail.teammembers_mapid;
                    var instdesigidArray = '';

                    onchange_deptcode(detail.catcode, detail.regioncode, detail.teamhead_designation, detail
                        .audit_quarter,
                        instdesigidArray, designationdata, detail
                        .auditeedeptcode, detail.typeofauditcode);

                    onchange_catcode(detail.catcode, detail.if_subcategory, detail
                        .auditeeins_subcategoryid);

                    // onchange_region(detail.regioncode, detail.distcode, detail.auditoffice)
                    // onchange_distcode(detail.deptcode, detail.regioncode, detail.distcode, detail
                    //     .auditoffice)

                    // if (detail.auditoffice != '' || detail.auditoffice != null) {
                    //     onchange_auditoffice(detail.deptcode, detail.regioncode, detail.distcode, detail
                    //         .auditoffice, detail
                    //         .auditaduserid, detail.auditofficedesignation)
                    // }
                    async function executeSequentially(detail) {
                        await onchange_region(detail.regioncode, detail.distcode, detail.auditoffice);
                        await onchange_distcode(detail.deptcode, detail.regioncode, detail.distcode,
                            detail.auditoffice, detail.parentinstid);

                        if (detail.auditoffice !== '' && detail.auditoffice !== null) {
                            await onchange_auditoffice(detail.deptcode, detail.regioncode, detail
                                .distcode, detail.auditoffice, detail.auditofficedesignation);
                            await onchange_auditofficedesig(detail.auditoffice, detail
                                .auditofficedesignation, detail.auditaduserid, detail.regioncode, detail.distcode)
                        }
                    }
                    executeSequentially(detail);


                    appendEditRow(auditreportdata);
                    // populateTeamMembersDropdown(memberdesigArray);




                } else {
                    alert('Institute Details not found');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    function applyValidationToNewFields(inputName, message) {
        let $input = $("[name='" + inputName + "']"); // Select input by name
        if ($input.length) {

            let validator = $("#mapinst_form").data("validator"); // Get validator instance

            if (!validator) {

                $("#mapinst_form").validate({ // Initialize validation if not already done
                    errorPlacement: function(error, element) {
                        // Check if the element has the 'datepicker' class
                        if (element.hasClass('datepicker')) {
                            // Insert the error message after the input-group to display it below the input and icon
                            error.insertAfter(element.closest('.input-group'));
                        } else {
                            // Default behavior: insert the error message after the input field
                            error.insertAfter(element);
                        }
                    }
                });
                validator = $("#mapinst_form").data("validator");
            }

            $input.rules("remove"); // Remove any existing validation rules

            // Ensure rules are applied only once
            $input.rules("add", {
                required: true,
                messages: {
                    required: message // Custom error message for the required rule
                }
            });

            validator.element($input); // Validate the element

            // ✅ Ensure validation runs on change without removing existing messages
            $input.on("change", function() {
                $(this).valid(); // Validate when the input changes
            });
        } else {
            console.error("❌ Element not found:", inputName); // Handle case if element is not found
        }
    }

    function appendEditRow(auditreportdata) {
        $('.work-row-insert').hide();
        $('#EditrowUsers').html('');

        $('#row0').removeClass('d-flex');
        var tablehead = ` <div class="row">
                                                        <div class="col-md-1"></div>

                                                        <div class="col-md-3 ms-2"></div>

                                                        <div class="col-md-3ms-2"></div>



                                                        <div class="col-md-3"></div>
                                                    </div>

                                                    <div class="d-flex mt-2 work-row" id="row0">
                                                        <div class="col-md-1">
                                                            <label class="form-label lang" key="s_no" for="validationDefaultUsername">S.No</label>
                                                        </div>



                                                        <div class="col-md-3 ms-2">
                                                            <label class="form-label lang" key="designation" for="validationDefaultUsername">Designation</label>
                                                        </div>

                                                        <div class="col-md-3 ms-2">
                                                            <label class="form-label lang" key="email="validationDefaultUsername">Email</label>
                                                        </div>


                                                        <div class="col-md-3  ms-2">
                                                            <label class="form-label lang" key="mobile"validationDefaultUsername">Mobile Number</label>
                                                        </div>
                                                    </div>
                                                `;
        $('#EditrowUsers').append(tablehead);


        var rowCount = 0; // Initialize row count

   
           if (auditreportdata.length > 0) {
            $.each(auditreportdata, function(index, item) {
                rowCount++; // Increment rowCount for each iteration


                // Create HTML template for a new row
                var appendHTML = `
                <div class="d-flex mt-2 work-row-edit" id="row${rowCount}">
                    <input type="hidden" name="officeuserid[${rowCount}]" value="UserId_${rowCount}">

                    <div class="col-md-1">
                        <input type="text" class="form-control alpha_numeric" value="${rowCount}" disabled>
                    </div>

                    <div class="col-md-3 ms-2">
                        <input type="text" class="form-control name" name="reportdesignation[${rowCount}]" value="${item.designation || ''}" placeholder="Enter Name">
                    </div>

                    <div class="col-md-3 ms-2">
                        <input type="email" class="form-control " name="reportemail[${rowCount}]" id="reportemail${rowCount}" value="${item.email || ''}" placeholder="Enter Email">
                    </div>
                      <div class="col-md-3 ms-2">
                        <input type="text" class="form-control only_numbers" maxlength = "13" name="reportmobile[${rowCount}]" id="reportmobile${rowCount}" value="${item.mobilenumber || ''}" placeholder="Enter Email">
                    </div>



                    <div class="col-md-3 actionbtns">
                        <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn" onclick="addNewWorkRow(event,'edit')">
                            <i class="ti ti-circle-plus"></i>
                        </button>
                         ${rowCount != 1 ? `
                                        <button type="button" class="btn btn-danger fw-medium ms-2 removeRowBtn" onclick="removedeptRow(this,'edit')">
                                            <i class="ti ti-circle-minus"></i>
                                        </button>` : ``}
                    </div>
                </div>
            `;
                $(".only_numbers").on("keypress", function(event) {
                    if (event.charCode >= 48 && event.charCode <= 57)
                        return true; // let it happen, don't do anything
                    else return false;
                });


                $('#EditrowUsers').append(appendHTML);
            });
        } else {



            // Create HTML template for a new row
            var appendHTML = `
                <div class="d-flex mt-2 work-row-edit" id="row1">
                    <input type="hidden" name="officeuserid[1]" value="UserId_1">

                    <div class="col-md-1">
                        <input type="text" class="form-control alpha_numeric" value="1" disabled>
                    </div>

                    <div class="col-md-3 ms-2">
                        <input type="text" class="form-control name" name="reportdesignation[1]" value="" placeholder="Enter Name">
                    </div>

                    <div class="col-md-3 ms-2">
                        <input type="email" class="form-control " name="reportemail[1]" id="reportemail1}" value="" placeholder="Enter Email">
                    </div>
                      <div class="col-md-3 ms-2">
                        <input type="text" class="form-control only_numbers" maxlength = "13" name="reportmobile[1]" id="reportmobile1" value="" placeholder="Enter Mobile Number">
                    </div>



                    <div class="col-md-3 actionbtns">
                        <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn" onclick="addNewWorkRow(event,'edit')">
                            <i class="ti ti-circle-plus"></i>
                        </button>
                        
                    </div>
                </div>
            `;
            $(".only_numbers").on("keypress", function(event) {
                if (event.charCode >= 48 && event.charCode <= 57)
                    return true; // let it happen, don't do anything
                else return false;
            });


            $('#EditrowUsers').append(appendHTML);

        }
    }

    function updateTableLanguage(lang) {
        if ($.fn.DataTable.isDataTable('#mapinst_table')) {
            $('#mapinst_table').DataTable().clear().destroy();
        }
        renderTable(lang);
    }

    function renderTable(lang) {


        let tableData = Object.values(dataFromServer).map((group, index) => {
            let departmentColumn = lang === 'ta' ? group.depttsname : group.deptesname;
            let categoryColumn = lang === 'ta' ? group.cattname : group.catename;
            let subcategoryColumn = lang === 'ta' ? group.subcattname : group.subcatename;
	        //let isdisable = group.q1spillover === 'Y' ? true : false;
		let isdisable =  false;

            let categoryDetails =
                `<b>${lang === 'ta' ? 'Category Name : ' : 'Category Name :'}</b> ${lang=='ta'?group.cattname:group.catename} <br>
            <b>${lang === 'ta' ? 'Sub Category Name : ' : 'Sub Category Name : '}</b> ${lang=='ta'?group.subcattname:group.subcatename} `;
            let regionColumn = lang === 'ta' ? group.regiontname : group.regionename;
            let districtColumn = lang === 'ta' ? group.disttname : group.distename;

            // ✅ Institution Details as a String (Only for Desktop View)
            let instituteDetail =
                `<b>${lang === 'ta' ? 'நிறுவனத்தின் ஆங்கில பெயர்' : 'Institution English Name'}</b>: ${group.instename} <br>
                               <small><b>${lang === 'ta' ? 'நிறுவனத்தின் தமிழ் பெயர்' : 'Institution Tamil Name'}:</b> ${group.insttname}</small>`;

            // ✅ Nodal Person Details as a String (Only for Desktop View)
            let nodalPersonDetail =
                `<b>${lang === 'ta' ? 'நோடல் நபர் ஆங்கில பெயர்' : 'Nodal Person English Name'}:</b> ${group.nodalperson_ename} <br>
                                 <small><b>${lang === 'ta' ? 'நோடல் நபர் தமிழ் பெயர்' : 'Nodal Person Tamil Name'}:</b> ${group.nodalperson_tname}</small><br>
                                 <small><b>${lang === 'ta' ? 'மின்னஞ்சல்' : 'Email'}:</b> ${group.email}</small><br>
                                 <small><b>${lang === 'ta' ? 'மொபைல் எண்' : 'Mobile Number'}:</b> ${group.mobile}</small><br>
                                 <small><b>${lang === 'ta' ? 'பதவி' : 'Designation'}:</b> ${group.nodalperson_desigcode}</small>`;

            let action = isdisable ? `<i class="ti ti-ban fs-4"></i>` : `<center><a class="btn editicon edit_btn" id="${group.encrypted_instid}" >
                        <i class="ti ti-edit fs-4"></i></a></center>`;

            return {
                serialNo: index + 1,
                department: departmentColumn,
                instename: group.instename, // ✅ Institution English Name
                insttname: group.insttname, // ✅ Institution Tamil Name
                institute: instituteDetail, // 🔹 Full String for Desktop View
                category: categoryColumn,
                subcategory: subcategoryColumn,
                categorydet: categoryDetails,
                region: regionColumn,
                district: districtColumn,
                rankorder: group.rankorder,
                nodelename: group.nodalperson_ename, // ✅ Nodal Person English Name
                nodeltname: group.nodalperson_tname, // ✅ Nodal Person Tamil Name
                email: group.email, // ✅ Email
                mobile: group.mobile, // ✅ Mobile
                designation: group.nodalperson_desigcode, // ✅ Designation
                nodalPerson: nodalPersonDetail, // 🔹 Full String for Desktop View
                action: action
            };
        });

        if ($.fn.DataTable.isDataTable('#mapinst_table')) {
            $('#mapinst_table').DataTable().clear().destroy();
        }

        $('#mapinst_table').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            data: tableData,
            order: [
                [0, 'asc']
            ],
            columns: [{
                    data: "serialNo",
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        // ✅ Mobile View: Pass Only Institution & Nodal Person Sub-Columns Separately
                        let rowDataForMobile = {
                            instename: row.instename, // ✅ Institution English Name
                            insttname: row.insttname, // ✅ Institution Tamil Name
                            category: row.category,
                            subcategory: row.subcategory,
                            region: row.region,
                            district: row.district,
                            rankorder: rankorder,
                            nodelename: row.nodelename, // ✅ Nodal Person English Name
                            nodeltname: row.nodeltname, // ✅ Nodal Person Tamil Name
                            Email: row.email, // ✅ Email
                            mobile: row.mobile, // ✅ Mobile Number
                            designation: row.designation // ✅ Designation
                        };

                        return `<div>
                                <button class="toggle-row d-md-none" data-row='${JSON.stringify(rowDataForMobile)}'>▶</button>
                                ${meta.row + 1}
                            </div>`;
                    },
                    className: 'text-end',
                    type: "num"
                },
                {
                    data: "department",
                    title: columnLabels?.["department"]?.[lang],
                    className: 'text-start text-wrap'
                },
                {
                    data: "institute",
                    title: columnLabels?.["institute"]?.[lang],
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: "categorydet",
                    title: columnLabels?.["categorydet"]?.[lang],
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: "region",
                    title: columnLabels?.["region"]?.[lang],
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: "district",
                    title: columnLabels?.["district"]?.[lang],
                    className: "text-center d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: "rankorder",
                    title: columnLabels?.["rankorder"]?.[lang],
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: "nodalPerson",
                    title: columnLabels?.["nodalPerson"]?.[lang],
                    className: "text-left d-none d-md-table-cell extra-column text-wrap"
                },
                {
                    data: "action",
                    title: columnLabels?.["actions"]?.[lang],
                    className: "text-center text-wrap"
                }
            ],
            columnDefs: [{
                targets: "_all",
                className: ""
            }],
            initComplete: function() {
                $("#mapinst_table").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            }
        });

        // ✅ Ensure Mobile View Works with Institution & Nodal Person Sub-Columns
        const mobileColumns = ["instename", "insttname", "category", "subcategory", "region", "district", "nodelename",
            "nodeltname", "Email", "mobile", "designation"
        ];
        setupMobileRowToggle(mobileColumns);

        updatedatatable(lang, "mapinst_table");
    }


    function exportToExcel(tableId, language) {
        let table = $(`#${tableId}`).DataTable();
        let titleKey = `${tableId}_title`;
        let translatedTitle = dataTables[language]?.datatable?.[titleKey] || "Default Title";
        let safeSheetName = translatedTitle.substring(0, 31);
        let dtText = dataTables[language]["datatable"] || dataTables["en"]["datatable"];

        const riskTypeMap = {
            en: {
                H: "High",
                M: "Medium",
                L: "Low"
            },
            ta: {
                H: "????",
                M: "?????",
                L: "??????"
            }
        };
        const yesNoMap = {
            en: {
                Y: "Yes",
                N: "No"
            },
            ta: {
                Y: "???",
                N: "?????"
            }
        };

        const insttype = {
            en: {
                H: "Hub",
                S: "Spoke"
            },
            ta: {
                H: "ஹப்",
                S: "ஸ்போக்"
            }
        };

        const columnMap = {
            departmentColumn: language === 'ta' ? 'depttsname' : 'deptesname',
            categoryColumn: language === 'ta' ? 'cattname' : 'catename',
            regionColumn: language === 'ta' ? 'regiontname' : 'regionename',
            districtColumn: language === 'ta' ? 'disttname' : 'distename',
            auditeedep: language === 'ta' ? 'auditeedepttname' : 'auditeedeptename',
            typeofaudit: language === 'ta' ? 'typeofaudittname' : 'typeofauditename',
            auditoffice: language === 'ta' ? 'auditinsttname' : 'auditinstename',

            aao: language === 'ta' ? 'usertamilname' : 'username',
            designation: 'designation',
            auditquarter: 'auditquarter',
            erpno: 'erpno',
            mandays: 'mandays',
            nodalPersonEnglish: 'nodalperson_ename',
            nodelPersonTamil: 'nodalperson_tname',
            instenameColumn: 'instename',
            insttnameColumn: 'insttname',
            emailColumn: 'email',
            mobileColumn: 'mobile',
            revenuedist: language === 'ta' ? 'revenuedistesname' : 'revenuedistename',
            subcategory: language === 'ta' ? 'subcattname' : 'subcatename',
            rankorder: 'rankorder',
            risktype: 'risktype',
            designationColumn: 'nodalperson_desigcode',
            auditeeofficeaddress: 'auditeeofficeaddress',
            mobilenumber: 'mobilenumber',
            teammember: language === 'ta' ? 'teammembers_desigelname' : "teammembers_desigelname",

            teamhead: language === 'ta' ? 'teamhead_desigelname' : "teamhead_desigelname",

            auditmode: language === 'ta' ? 'auditmodetname' : 'auditmodeename',
            teamsize: 'teamsize',

            fees: 'fees',
            categorization: 'categorization',
            maxleave: 'leavedays',
            carryforward: 'carryforward',
            templateaudit: 'templateaudit',
            consreport: 'consreport',
            assemblereport: 'assemblereport',
            turnover: 'turnover',

        };

        let headers = [{
                header: dtText["department"] || "Department",
                key: columnMap.departmentColumn
            },
            {
                header: dtText["region"] || "Region",
                key: columnMap.regionColumn
            },
            {
                header: dtText["district"] || "District",
                key: columnMap.districtColumn
            },
            {
                header: dtText["revenuedist"] || "Revenue District",
                key: columnMap.revenuedist
            },
            {
                header: dtText["instename"] || "Institution English Name",
                key: columnMap.instenameColumn
            },
            {
                header: dtText["institution_tam_name"] || "Institution Tamil Name",
                key: columnMap.insttnameColumn
            },
            {
                header: dtText["category"] || "Category",
                key: columnMap.categoryColumn
            },
            {
                header: dtText["subcat"] || "Sub Category",
                key: columnMap.subcategory
            },
            {
                header: dtText["mandays"] || "Total Mandays ",
                key: columnMap.mandays
            },
            {
                header: dtText["auditquarter"] || "Auditquarter",
                key: columnMap.auditquarter
            },
            {
                header: dtText["rankorder"] || "Rank Order",
                key: columnMap.rankorder
            },
            {
                header: dtText["typeofaudit"] || "Type of Audit",
                key: columnMap.typeofaudit
            },
            {
                header: dtText["risktype"] || "Risk Type",
                key: columnMap.risktype
            },
            {
                header: dtText["auditeeofficeaddress"] || "Auditee Office Address",
                key: columnMap.auditeeofficeaddress
            },
            {
                header: dtText["nodalperson_ename"] || "Nodal Person English Name",
                key: columnMap.nodalPersonEnglish
            },
            {
                header: dtText["nodalPerson"] || "Nodal Person Tamil Name",
                key: columnMap.nodelPersonTamil
            },
            {
                header: dtText["designation"] || "Designation",
                key: columnMap.designationColumn
            },
            {
                header: dtText["Email"] || "Email",
                key: columnMap.emailColumn
            },
            {
                header: dtText["mobilenumber"] || "Mobile Number",
                key: columnMap.mobileColumn
            },
            {
                header: dtText["auditoffice"] || "Audit Office Name",
                key: columnMap.auditoffice
            },
            {
                header: dtText["designation"] || "Auditee Designation",
                key: columnMap.designation
            },
            {
                header: dtText["aao"] || "AAO Name",
                key: columnMap.aao
            },
            {
                header: dtText["erpno"] || "ITMS/Reg NO/Reference No",
                key: columnMap.erpno
            },
            {
                header: dtText["teammember"] || "Team Head",
                key: columnMap.teammember
            },
            {
                header: dtText["teamhead"] || "Team Members",
                key: columnMap.teamhead
            },

            {
                header: dtText["audit_mode"] || "Audit Mode",
                key: columnMap.auditmode
            },
            {
                header: dtText["teamsize"] || "Team Size",
                key: columnMap.teamsize
            },
            {
                header: dtText["fees"] || "Fees",
                key: columnMap.fees
            }, {
                header: dtText["categorization"] || "Categorization based Income Turnover",
                key: columnMap.categorization
            }, {
                header: dtText["maxleave"] || "Maximum Leavedays",
                key: columnMap.maxleave
            }, {
                header: dtText["carryforward"] || "Carry Forward",
                key: columnMap.carryforward
            }, {
                header: dtText["templateaudit"] || "Template Audit",
                key: columnMap.templateaudit
            }, {
                header: dtText["consreport"] || "Consolidated Report",
                key: columnMap.consreport
            }, {
                header: dtText["assemblereport"] || "Assembly Report",
                key: columnMap.assemblereport
            }, {
                header: dtText["turnover"] || "Turn Over",
                key: columnMap.turnover
            },

            {
                header: dtText["auditeedep"] || "Auditee Department",
                key: columnMap.auditeedep
            },

        ];

        // Add dynamic headers for up to 10 matched contacts
        for (let i = 1; i <= 10; i++) {
            headers.push({
                header: `Designation ${i}`,
                key: `designation${i}`
            });
            headers.push({
                header: `Email ${i}`,
                key: `email${i}`
            });
            headers.push({
                header: `Mobile ${i}`,
                key: `mobile${i}`
            });
        }

        let excelData = dataFromServer.map(row => {
            let instid = row.instid;
            // let matchingRows = auditeedept_reportdata.filter(d => d.instid === instid);
            let dynamicData = {};

           
            return {
                [columnMap.departmentColumn]: row[columnMap.departmentColumn] || "-",
                [columnMap.regionColumn]: row[columnMap.regionColumn] || "-",
                [columnMap.districtColumn]: row[columnMap.districtColumn] || "-",
                [columnMap.revenuedist]: row[columnMap.revenuedist] || "-",
                [columnMap.instenameColumn]: row[columnMap.instenameColumn] || "-",
                [columnMap.insttnameColumn]: row[columnMap.insttnameColumn] || "-",
                [columnMap.categoryColumn]: row[columnMap.categoryColumn] || "-",
                [columnMap.subcategory]: row[columnMap.subcategory] || "-",
                [columnMap.mandays]: row[columnMap.mandays] || "-",
                [columnMap.auditquarter]: row[columnMap.auditquarter] || "-",
                [columnMap.rankorder]: row[columnMap.rankorder] || "-",
                [columnMap.typeofaudit]: row[columnMap.typeofaudit] || "-",
                [columnMap.risktype]: riskTypeMap[language]?.[row[columnMap.risktype]] || row[columnMap
                    .risktype] || "-",
                [columnMap.auditeeofficeaddress]: row[columnMap.auditeeofficeaddress] || "-",
                [columnMap.nodalPersonEnglish]: row[columnMap.nodalPersonEnglish] || "-",
                [columnMap.nodelPersonTamil]: row[columnMap.nodelPersonTamil] || "-",
                [columnMap.designationColumn]: row[columnMap.designationColumn] || "-",
                [columnMap.emailColumn]: row[columnMap.emailColumn] || "-",
                [columnMap.mobileColumn]: row[columnMap.mobileColumn] || "-",
                [columnMap.auditoffice]: row[columnMap.auditoffice] || "-",
                [columnMap.designation]: row[columnMap.designation] || "-",
                [columnMap.aao]: row[columnMap.aao] || "-",
                [columnMap.erpno]: row[columnMap.erpno] || "-",
                [columnMap.teamhead]: row[columnMap.teamhead] || "-",
                [columnMap.teammember]: row[columnMap.teammember] || "-",
                [columnMap.auditmode]: row[columnMap.auditmode] || "-",
                [columnMap.teamsize]: row[columnMap.teamsize] || "-",
                [columnMap.fees]: yesNoMap[language]?.[row[columnMap.fees]] || row[columnMap.fees] || "-",
                [columnMap.categorization]: row[columnMap.categorisation] || "-",
                [columnMap.maxleave]: row[columnMap.maxleave] || "-",
                [columnMap.carryforward]: yesNoMap[language]?.[row[columnMap.carryforward]] || row[columnMap
                    .carryforward] || "-",
                [columnMap.templateaudit]: yesNoMap[language]?.[row[columnMap.templateaudit]] || row[columnMap
                    .templateaudit] || "-",
                [columnMap.consreport]: yesNoMap[language]?.[row[columnMap.consreport]] || row[columnMap
                    .consreport] || "-",
                [columnMap.assemblereport]: yesNoMap[language]?.[row[columnMap.assemblereport]] || row[columnMap
                    .assemblereport] || "-",
                [columnMap.turnover]: row[columnMap.turnover] || "-",


                [columnMap.auditeedep]: row[columnMap.auditeedep] || "-",
                ...dynamicData
            };
        });

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.json_to_sheet([]);
        XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], {
            origin: "A1"
        });
        XLSX.utils.sheet_add_json(ws, excelData, {
            skipHeader: true,
            origin: "A2"
        });
        XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
        XLSX.writeFile(wb, `${safeSheetName}.xlsx`);
    }


    let dropdownCount = 1;
    let maxDropdowns;
    var lang;




    // Add custom email validation rule
    jQuery.validator.addMethod("customEmail", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value);
    }, "Please enter a valid email address");
    jQuery.validator.addMethod("customMobile", function(value, element) {
        return this.optional(element) || /^(?!([6-9])\1{9}$)[6-9][0-9]{9}$/.test(value);
    }, "Please enter a valid 10-digit mobile number");
    jQuery.validator.addMethod("customDecimal", function(value, element) {
        // This regex allows up to 12 digits before the decimal, and up to 2 digits after it
        return this.optional(element) || /^\d{1,12}(\.\d{1,2})?$/.test(value);
    }, "Please enter a valid decimal number");



    // Load i18n JSON and apply validation
    jsonLoadedPromise.then(() => {
        const language = getLanguage('Y');

        // Make email lowercase as user types
        //$("#email").on("input", function() {
          //  this.value = this.value.toLowerCase();
        //});

        // Trigger validation on blur
        $("#email").on("blur", function() {
            $(this).valid();
        });

        // Apply validation to the form
        const validator = $("#mapinst_form").validate({
            rules: {
                subcatid: {
                    required: true
                },
                roletypecode: {
                    required: true
                },
                deptcode: {
                    required: true
                },
                cat_code: {
                    required: true
                },
                regioncode: {
                    required: true
                },
                distcode: {
                    required: true
                },
                revenuedistcode: {
                    required: true
                },
                instename: {
                    required: true
                },
                insttname: {
                    required: true
                },
                mandays: {
                    required: true
                },
                risktype: {
                    required: true
                },
                applicablefor: {
                    required: true
                },
                rankorder: {
                    required: true
                },
                audit_quarter: {
                    required: true
                },
                typeofaudit: {
                    required: true
                },
                nodalperson_ename: {
                    required: true
                },
                nodalperson_tname: {
                    required: true
                },
                email: {
                    required: true,
                    email: true,
                    customEmail: true
                },
                mobile: {
                    required: true,
                    minlength: 10,
                    customMobile: true
                },
                nodalperson_desigcode: {
                    required: true
                },
                head_desig: {
                    required: true
                },
                auditoffice: {
                    required: true
                },
                audit_office_desig: {
                    required: true
                },
                audit_ad: {
                    required: true
                },
                inst_type: {
                    required: true
                },
                pareninstid: {
                    required: true
                },
                categorization: {
                    required: true
                },
                audit_mode: {
                    required: true
                },
                team_size: {
                    required: true
                },
                max_leave: {
                    required: true
                },
                cary_fwd: {
                    required: true
                },
                cons_report: {
                    required: true
                },
                turnover: {
                    customDecimal: true,
                    required: true
                }

            },

            messages: {
                ...errorMessages[language], // Merge language-based messages
                email: {
                    required: "Email is required",
                    email: "Enter a valid email address",
                    customEmail: "Email format is incorrect"
                },
                mobile: {
                    required: "Mobile number is required",
                    minlength: "Enter at least 10 digits",
                    customMobile: "Please enter a valid 10-digit mobile number"
                },
                turnover: {
                    required: "Turn over is required",
                    customDecimal: "Enter Valid turn over value",

                }
            },

            errorPlacement: function(error, element) {
                if (element.hasClass('select2')) {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            }
        });

    }).catch(error => {
        console.error("Failed to load JSON data:", error);
    });

    // Scroll to the first error field (for better UX)
    function scrollToFirstError() {
        var $mapinst_form = $('#mapinst_form');
        const firstError = $mapinst_form.find('.error:first');
        if (firstError.length) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
        }
    }
    /***********************************jquery Validation**********************************************/
    //
    $(document).ready(function() {
        $('#mapinst_form')[0].reset();
        updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
        fetchAlldata(lang);

        //   if (sessiondeptcode && sessiondeptcode.trim() !== '') {

        //     onchange_deptcode(sessiondeptcode, '', '', '', '', '');
        // }

        lang = getLanguage('');
        var designationdata = [];

        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'


        var lang = getLanguage();

        if (sessionrole == distrole) {

            onchange_deptcode('', sessionregioncode, '', '', '', '', '', '');
            onchange_distcode(sessiondeptcode, sessionregioncode, sessiondistcode, '')
        } else if (sessionrole == regionrole) {
            onchange_deptcode(sessiondeptcode, sessionregioncode, '', '', '', '', '', '');
            onchange_region(sessionregioncode, '')
        } else if (sessionrole == headofficerole) {
            onchange_deptcode(sessiondeptcode, '', '', '', '', '');
        }
        // else if (sessionrole == dgarole) {
        //     getInstData(lang);
        // } else if (sessionrole == adminrole) {
        //     getInstData(lang);
        // }


    });

    $('#translate').change(function() {
        const lang = $('#translate').val();
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        updateTableLanguage(lang);
        updateValidationMessages(getLanguage('Y'), 'mapinst_form');
    });


    // $(document).on('click', '.btn-delete', function() {
    //     const dropClass = $(this).data(
    //         'drop'); // Get the dropdown number from the button's data attribute

    //     // Remove all elements with the corresponding drop class
    //     $(`.drop_${dropClass}`).remove();

    //     dropdownCount--;

    //     // Show the add button if dropdown count is less than max
    //     if (dropdownCount < maxDropdowns) {
    //         $('#btn-add').show();
    //     }

    //     // Hide delete buttons if only one dropdown remains
    //     // if (dropdownCount === 1) {
    //     //     $('.btn-delete').addClass('d-none');
    //     // }

    //     // Reassign IDs, names, and classes to maintain sequential order
    //     $('deignation-drop .designation-row').each(function(index) {
    //         const newIndex = index + 1;

    //         // Update the dropdown ID and name
    //         // $(this)
    //         //     .find('select')
    //         //     .attr('id', `member_${newIndex}`)
    //         //     .attr('name', `member_${newIndex}`);

    //         // Update the class and button data-drop attribute
    //         $(this)
    //             .attr('class', `col-md-4 designation-row drop_${newIndex}`)
    //             .next('.col-md-1')
    //             .attr('class', `col-md-1 ms-4 drop_${newIndex}`)
    //             .find('.btn-delete')
    //             .attr('data-drop', newIndex);
    //     });
    // });






    // $("#translate").change(function() {
    //     lang = getLanguage('Y');
    //     updateTableLanguage(lang);
    //     updateValidationMessages(getLanguage('Y'), 'mapinst_form');

    // });

    function onchange_insttype(insttype) {

        var insttype = insttype || $('#inst_type').val();

        if (insttype == "H") {
            $('#parentinst_div').hide();
        } else if (insttype == 'S') {
            $('#parentinst_div').show();
        } else {
            $('#parentinst_div').hide();
        }
    }

    function onchange_applicablefor(auditmodeCode) {

        var auditmodeCode = auditmodeCode || $('#audit_mode').val();
        let checkedCount = 0;
        var checkboxes = document.querySelectorAll('input[name="applicablefor[]"]:checked');
        if (auditmodeCode == 'N' || auditmodeCode == 'T') {


            if (checkboxes.length > 1) {
                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        checkedCount++;
                        if (checkbox === event.target) {
                            lastChecked = checkbox;
                        }
                    }

                });
                if (checkedCount > 1 && lastChecked) {
                    getLabels_jsonlayout([{
                        id: 'selectonequarter',
                        key: 'selectonequarter'
                    }], 'N').then((text) => {
                        passing_alert_value('Confirmation', text.selectonequarter,
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');
                    });
                    lastChecked.checked = false;
                }
            }




        } else if (auditmodeCode == 'Q') {
            $(this).prop('disabled', true);
            if (checkboxes.length > 3) {
                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        checkedCount++;
                        if (checkbox === event.target) {
                            lastChecked = checkbox;
                        }
                    }

                });
                if (checkedCount > 3 && lastChecked) {
                    getLabels_jsonlayout([{
                        id: 'select_threequarter',
                        key: 'select_threequarter'
                    }], 'N').then((text) => {
                        passing_alert_value('Confirmation', text.select_threequarter,
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');
                    });
                    lastChecked.checked = false;
                }
            }
        }

    }

    function onchange_mode(auditmode, form, selectedQuarters) {

        var auditmodeCode = auditmode || $('#audit_mode').val();
        if (form == 'edit' && auditmodeCode == 'Q') {
            $('input[name="applicablefor[]"]').prop('disabled', false);
            return;
        } else {
            $('input[name="applicablefor[]"]').prop('disabled', true);
            // return
        }
        if (auditmodeCode) {
            if (auditmodeCode == 'C') {

                $('input[name="applicablefor[]"]').prop('checked', true);
                $('input[name="applicablefor[]"]').prop('disabled', true);
                $('#parentinstid_label').removeClass('required');
            } else if (auditmodeCode == 'N') {

                var currentquartercode = '<?php echo $quarterDetails->currentquarter ?>';

                $('input[name="applicablefor[]"]').prop('disabled', true);

                $('input[name="applicablefor[]"]').each(function() {
                    const quarterVal = $(this).val();

                    if (form === 'edit') {
                        if (quarterVal === currentquartercode) {
                            // Always disable current quarter, check only if it's selected
                            const shouldCheck = selectedQuarters.includes(currentquartercode);
                            $(this).prop('checked', shouldCheck).prop('disabled', true);


                        } else if (selectedQuarters.includes(quarterVal)) {
                            $(this).prop('checked', true).prop('disabled', false);

                        } else {
                            $(this).prop('checked', false).prop('disabled', false);
                        }
                    } else {

                        if (quarterVal == currentquartercode) {
                            $(this).prop('checked', false).prop('disabled', true);
                        } else {
                            $(this).prop('checked', false).prop('disabled', false);
                        }
                    }
                });

            } else if (auditmodeCode == 'T') {
                $('input[name="applicablefor[]"]').prop('disabled', false);
                $('#parentinstid_label').addClass('required');

            } else if (auditmodeCode == 'Q') {
                var currentquartercode = '<?php echo $quarterDetails->currentquarter ?>';
                $('input[name="applicablefor[]"]').prop('disabled', false);
                $('input[name="applicablefor[]"]').prop('checked', false);


            }

        } else {
            $('input[name="applicablefor[]"]').prop('checked', false);
            $('input[name="applicablefor[]"]').prop('disabled', false);
        }
    }

    function onchange_deptcode(catcode = '', regioncode = '', desigcode = '', quartercode = '',
        instdesigIDArr, designationdata, auditeedeptcode = '',
        typeofauditcode = '') {

        // reset_auditor_datas();

        var deptcode = $('#deptcode').val();
        var roletypecode = $('#roletypecode').val();
        const selectedOption = document.getElementById('deptcode').selectedOptions[0];
        //     maxDropdowns = parseInt(selectedOption.getAttribute('data-membercount')) || 0;
        //     designationCount = 1;

        //     // ✅ Remove all dynamically added designations
        //     $("#member_designation .designation-item").not(":first").remove();

        //     $("#member_designation .designation-item:last button").replaceWith(`
        //       <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn mt-4" onclick="addNewRow()">
        //           <i class="ti ti-circle-plus"></i>
        //       </button>
        //   `);

        $('#subcat_div').hide();

        const lang = getLanguage('');
        if (deptcode) {

            if (deptcode == '01') {

                $('#itmsno_label').addClass('required');
            } else {
                $('#itmsno_label').removeClass('required');
            }
            $.ajax({
                url: '/masters/FilterInst', // Your API route to get user details
                method: 'POST',
                data: {
                    deptcode: deptcode,
                    roletypecode: roletypecode
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {

                    var data = response.category;
                    var regiondata = response.region;
                    var quarterdata = response.auditquarter;
                    var audieedeptdata = response.audieedept;
                    var typeofauditdata = response.typeofaudit;

                    window.designationdata = response.designation; // ✅ Assign to global scope
                    // $('#designationDrop').empty();
                    $('#typeofaudit').empty();


                    $('#typeofaudit').append(
                        '<option value="" data-name-en="---Select Type Of Audit---"data-name-ta="---தணிக்கை வகையைத் தேர்ந்தெடுக்கவும்--">---Select Type Of Audit---</option>'
                    );
                    $.each(typeofauditdata, function(index, audittype) {
                        var isSelected = audittype.typeofauditcode === typeofauditcode ? 'selected' :
                            '';
                        $('#typeofaudit').append(
                            '<option value="' + audittype.typeofauditcode + '"' +

                            ' data-name-en="' + audittype.typeofauditename + '"' +
                            ' data-name-ta="' + audittype.typeofaudittname + '" ' + isSelected +
                            '>' +
                            (lang == "en" ? audittype.typeofauditename : audittype
                                .typeofaudittname) +
                            '</option>'
                        );
                    });



                    $('#audit_quarter').empty();


                    $('#audit_quarter').append(
                        '<option value="" data-name-en="---Select Audit Quarter---"data-name-ta="--- தணிக்கை காலாண்டைத் தேர்ந்தெடுக்கவும்---">---Select Audit Quarter---</option>'
                    );
                    var currentquarter = <?= json_encode($currentquarter) ?>;

                    $.each(quarterdata, function(index, quarter) {
                        var isSelected = quarter.auditquartercode === (currentquarter) ? ' selected' : '';

                        $('#audit_quarter').append(
                            '<option value="' + quarter.auditquartercode + '"' +
                            ' data-name-en="' + quarter.auditquarter + '"' +
                            ' data-name-ta="' + quarter.auditquartertname + '"' +
                            isSelected + '>' +
                            (lang == "en" ? quarter.auditquarter : quarter.auditquartertname) +
                            '</option>'
                        );
                    });

                    $('#cat_code').empty();
                    $('#cat_code').append(
                        '<option value="" data-name-en="---Select Category---"data-name-ta="--- வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>'
                    );
                    $.each(data, function(index, category) {
                        var isSelected = category.catcode === catcode ? 'selected' : '';
                        $('#cat_code').append(
                            '<option value="' + category.catcode + '"' +
                            'if_subcategory="' + category.if_subcategory + '"' +
                            ' data-name-en="' + category.catename + '"' +
                            ' data-name-ta="' + category.cattname + '" ' + isSelected +
                            '>' +
                            (lang == "en" ? category.catename : category.cattname) +
                            '</option>'
                        );
                    });
                    $('#regioncode').empty();
                    $('#regioncode').append(
                        '<option value="" data-name-en="---Select Region---"data-name-ta="--- பகுதியைத் தேர்ந்தெடுக்கவும்---">---Select Region---</option>'
                    );

                    $.each(regiondata, function(index, region) {

                        var isSelected = region.regioncode === regioncode.trim() ? 'selected' : '';

                        $('#regioncode').append(
                            '<option value="' + region.regioncode + '"' +
                            ' data-name-en="' + region.regionename + '"' +
                            ' data-name-ta="' + region.regiontname + '" ' + isSelected +
                            '>' +
                            (lang === "en" ? region.regionename : region.regiontname) +
                            '</option>'
                        );
                    });
                    $('#auditee_dept').empty();
                    $('#auditee_dept').append(
                        '<option value="" data-name-en="---Select Auditee Office---"data-name-ta="---தணிக்கையாளர் அலுவலகத்தைத் தேர்ந்தெடுக்கவும்---">---Select Auditee Office---</option>'
                    );

                    $.each(audieedeptdata, function(index, auditeedepartment) {
                        var isSelected = auditeedepartment.auditeedeptcode === auditeedeptcode ?
                            'selected' : '';
                        $('#auditee_dept').append(
                            '<option value="' + auditeedepartment.auditeedeptcode + '"' +
                            ' data-name-en="' + auditeedepartment.auditeedeptename + '"' +
                            ' data-name-ta="' + auditeedepartment.auditeedepttname + '" ' +
                            isSelected +
                            '>' +
                            (lang === "en" ? auditeedepartment.auditeedeptename : auditeedepartment
                                .auditeedepttname) +
                            '</option>'
                        );
                    });






                },

                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        } else {
            $('#itmsno_label').removeClass('required');
            //   type of audit
            $('#typeofaudit').empty();
            $('#typeofaudit').append(
                '<option value="" data-name-en="---Select Type Of Audit---"data-name-ta="---தணிக்கை வகையைத் தேர்ந்தெடுக்கவும்--">---Select Type Of Audit---</option>'
            );
            //   auditee dept
            $('#auditee_dept').empty();
            $('#auditee_dept').append(
                '<option value="" data-name-en="---Select Auditee Office---"data-name-ta="---தணிக்கையாளர் அலுவலகத்தைத் தேர்ந்தெடுக்கவும்---">---Select Auditee Office---</option>'
            );
            //region
            $('#regioncode').empty();
            $('#regioncode').append(
                '<option value="" data-name-en="---Select Region---"data-name-ta="--- பகுதியைத் தேர்ந்தெடுக்கவும்---">---Select Region---</option>'
            );
            //Category
            $('#cat_code').empty();
            $('#cat_code').append(
                '<option value="" data-name-en="---Select Category---"data-name-ta="--- வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>'
            );
            //audit quarter
            $('#audit_quarter').empty();
            $('#audit_quarter').append(
                '<option value="" data-name-en="---Select Audit Quarter---"data-name-ta="--- தணிக்கை காலாண்டைத் தேர்ந்தெடுக்கவும்---">---Select Audit Quarter---</option>'
            );


        }
    }

    function onchange_catcode(catcode = '', ifSubcategoryVal = '', subcatid = '') {


        const catCodeElement = document.getElementById('cat_code');
        const selectedOption = catCodeElement.selectedOptions[0];
        const ifSubcategory = ifSubcategoryVal || selectedOption.getAttribute('if_subcategory');
        const lang = getLanguage('');
        if (ifSubcategory == 'Y') {
            $('#subcat_div').show();
            var catcode = catcode || $('#cat_code').val();
            $.ajax({
                url: '/masters/FilterInst', // Your API route to get user details
                method: 'POST',
                data: {
                    catcode: catcode,
                    // roletypecode: roletypecode
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {
                    var data = response.subcategory;

                    $('#subcatid').empty();
                    $('#subcatid').append(
                        '<option value="" data-name-en="---Select Sub Category---"data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">Select Sub Category</option>'
                    );
                    $.each(data, function(index, subcategory) {
                        var isSelected = subcategory.auditeeins_subcategoryid === subcatid ?
                            'selected' : '';
                        $('#subcatid').append(
                            '<option value="' + subcategory.auditeeins_subcategoryid +
                            '"' +

                            ' data-name-en="' + subcategory.subcatename + '"' +
                            ' data-name-ta="' + subcategory.subcattname + '" ' +
                            isSelected +
                            '>' +
                            (lang === "en" ? subcategory.subcatename : subcategory
                                .subcattname) +
                            '</option>'
                        );
                    });


                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        } else {
            $('#subcat_div').hide();
        }
    }

    function onchange_region(regioncode, distcode = '', instmappingcode = '') {

        var regioncode = regioncode || $('#regioncode').val();
        var deptcode = $('#deptcode').val();

        const lang = getLanguage('');

        $.ajax({
            url: '/masters/FilterInst', // Your API route to get user details
            method: 'POST',
            data: {
                regioncode: regioncode,
                deptcode: deptcode
                // roletypecode: roletypecode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                var districtdata = response.districtdet;

                $('#distcode').empty();
                $('#distcode').append(
                    '<option value="" data-name-en="---Select District---"data-name-ta="--- பகுதியைத் தேர்ந்தெடுக்கவும்---">---Select District---</option>'
                );


                $.each(districtdata, function(index, distdata) {
                    var isSelected = distdata.distcode === distcode ? 'selected' : '';
                    $('#distcode').append(
                        '<option value="' + distdata.distcode + '"' +
                        ' data-name-en="' + distdata.distename + '"' +
                        ' data-name-ta="' + distdata.disttname + '" ' + isSelected +
                        '>' +
                        (lang === "en" ? distdata.distename : distdata.disttname) +
                        '</option>'
                    );
                });

                // if (instmappingcode != '' || instmappingcode != null) {
                //     onchange_distcode(deptcode, regioncode, distcode, instmappingcode)
                // }
                // }
            },

            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });

    }

    function onchange_distcode(deptcode = '', regioncode = '', distcode = '', instmappingcode = '', parentinstid = '') {

        var deptcode = deptcode || $('#deptcode').val() || '<?php echo $deptcode; ?>';
        var regioncode = regioncode || $('#regioncode').val() || '<?php echo $regioncode; ?>';
        var distcode = distcode || $('#distcode').val() || '<?php echo $distcode; ?>';
        var instid = $('#instid').val();

        const lang = getLanguage('');
        //  $('#parentinstid').empty();
        //         $('#parentinstid').append(
        //             '<option value="" data-name-en="---Select Parent Institution---"data-name-ta="--- பெற்றோர் நிறுவனத்தைத் தேர்ந்தெடுக்கவும்---">---Select Parent Institution---</option>'
        //         );
        $.ajax({
            url: '/masters/FilterAuditInst', // Your API route to get user details
            method: 'POST',
            data: {
                deptcode: deptcode,
                regioncode: regioncode,
                distcode: distcode,
                instid: instid
                // roletypecode: roletypecode
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                var auditofficedet = response.auditofficedet;
                var parentinstdet = response.parentinstdet;

                $('#auditoffice').empty();
                $('#auditoffice').append(
                    '<option value="" data-name-en="---Select Audit Office---"data-name-ta="--- தணிக்கை அலுவலகத்தைத் தேர்ந்தெடுக்கவும்---">---Select Audit Office---</option>'
                );


                $.each(auditofficedet, function(index, auditoff) {
                    var isSelected = auditoff.instmappingcode === instmappingcode ? 'selected' : '';
                    $('#auditoffice').append(
                        '<option value="' + auditoff.instmappingcode + '"' +
                        ' data-name-en="' + auditoff.instename + '"' +
                        ' data-name-ta="' + auditoff.insttname + '" ' + isSelected +
                        '>' +
                        (lang === "en" ? auditoff.instename : auditoff.insttname) +
                        '</option>'
                    );
                });
                $('#parentinstid').empty();
                $('#parentinstid').append(
                    '<option value="" data-name-en="---Select Parent Institution---"data-name-ta="--- பெற்றோர் நிறுவனத்தைத் தேர்ந்தெடுக்கவும்---">---Select Parent Institution---</option>'
                );
                $.each(parentinstdet, function(index, parentinst) {

                    var isSelected = parentinst.instid === parentinstid ? 'selected' : '';


                    $('#parentinstid').append(
                        '<option value="' + parentinst.instid + '"' +
                        ' data-name-en="' + parentinst.instename + '"' +
                        ' data-name-ta="' + parentinst.insttname + '" ' + isSelected +
                        '>' +
                        (lang === "en" ? parentinst.instename : parentinst.insttname) +
                        '</option>'
                    );
                });


            },

            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    function onchange_auditoffice(deptcode = '', regioncode = '', distcode = '', instmappingcode = '',
        nodalperson_desigcode = '') {
        var instmappingcode = instmappingcode || $('#auditoffice').val();
        var deptcode = deptcode || $('#deptcode').val() || '<?php echo $deptcode; ?>';
        var regioncode = regioncode || $('#regioncode').val() || '<?php echo $regioncode; ?>';
        var distcode = distcode || $('#distcode').val() || '<?php echo $distcode; ?>';

        const lang = getLanguage('');
        $('#audit_office_desig').empty();
        $('#audit_office_desig').append(
            '<option value="" data-name-en="---Select Audit Office Designation---"data-name-ta="--- தணிக்கை அலுவலக பதவியைத் தேர்ந்தெடுக்கவும்---">---Select Audit Office Designation---</option>'
        );
        if (instmappingcode) {
            $.ajax({
                url: '/masters/FilterAuditInst', // Your API route to get user details
                method: 'POST',
                data: {
                    instmappingcode: instmappingcode,
                    deptcode: deptcode,
                    regioncode: regioncode,
                    distcode: distcode
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {
                    var auditordet = response.auditordet;
                    var auditordesigdet = auditordet[0];
                    //auditordesigdet)
                    $('#audit_office_desig').empty();
                    $('#audit_office_desig').append(
                        '<option value="" data-name-en="---Select Audit Office Designation---"data-name-ta="--- தணிக்கை அலுவலக பதவியைத் தேர்ந்தெடுக்கவும்---">---Select Audit Office Designation---</option>'
                    );



                    var isSelected = auditordesigdet.nodalperson_desigcode === nodalperson_desigcode ?
                        'selected' :
                        '';
                    $('#audit_office_desig').append(
                        '<option value="' + auditordesigdet.nodalperson_desigcode + '"' +
                        ' data-name-en="' + auditordesigdet.desigelname + '"' +
                        ' data-name-ta="' + auditordesigdet.desigtlname + '" ' + isSelected +
                        '>' +
                        (lang === "en" ? auditordesigdet.desigelname : auditorsdesig
                            .desigtlname) +
                        '</option>'
                    );


                    // $('#audit_ad').empty();
                    // $('#audit_ad').append(
                    //     '<option value="" data-name-en="---Select Audit Office---"data-name-ta="--- தணிக்கை அலுவலகத்தைத் தேர்ந்தெடுக்கவும்---">---Select Audit Office---</option>'
                    // );


                    // $.each(auditordet, function(index, auditors) {
                    //     var isSelected = auditors.deptuserid === deptuserid ? 'selected' :
                    //         '';
                    //     $('#audit_ad').append(
                    //         '<option value="' + auditors.deptuserid + '"' +
                    //         ' data-name-en="' + auditors.username + '"' +
                    //         ' data-name-ta="' + auditors.usertamilname + '" ' + isSelected +
                    //         '>' +
                    //         (lang === "en" ? auditors.username : auditors.usertamilname) +
                    //         '</option>'
                    //     );
                    // });

                },

                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        } else {
            $('#audit_office_desig').empty();
            $('#audit_office_desig').append(
                '<option value="" data-name-en="---Select Audit Office Designation---"data-name-ta="--- தணிக்கை அலுவலக பதவியைத் தேர்ந்தெடுக்கவும்---">---Select Audit Office Designation---</option>'
            );
        }

    }

    function onchange_auditofficedesig(instmappingcode, desigcode = '', deptuserid = '', regioncode, distcode) {
        var instmappingcode = instmappingcode || $('#auditoffice').val();
        var desigcode = desigcode || $('#audit_office_desig').val();
        var deptcode = deptcode || $('#deptcode').val() || '<?php echo $deptcode; ?>';
        var regioncode = regioncode || $('#regioncode').val() || '<?php echo $regioncode; ?>';
        var distcode = distcode || $('#distcode').val() || '<?php echo $distcode; ?>';

        const lang = getLanguage('');

        $('#audit_ad').empty();
        $('#audit_ad').append(
            '<option value="" data-name-en="---Select Audit Office---"data-name-ta="--- தணிக்கை அலுவலகத்தைத் தேர்ந்தெடுக்கவும்---">---Select Audit Office---</option>'
        );
        if (desigcode) {
            $.ajax({
                url: '/masters/FilterAuditInst', // Your API route to get user details
                method: 'POST',
                data: {
                    instmappingcode: instmappingcode,
                    desigcode: desigcode,
                    regioncode: regioncode,
                    deptcode: deptcode,
                    distcode: distcode

                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {
                    var auditorsdet = response.auditorsuserdet;



                    $('#audit_ad').empty();
                    $('#audit_ad').append(
                        '<option value="" data-name-en="---Select Audit Office---"data-name-ta="--- தணிக்கை அலுவலகத்தைத் தேர்ந்தெடுக்கவும்---">---Select Audit Office---</option>'
                    );


                    $.each(auditorsdet, function(index, auditors) {
                        var isSelected = auditors.deptuserid === deptuserid ? 'selected' :
                            '';
                        $('#audit_ad').append(
                            '<option value="' + auditors.deptuserid + '"' +
                            ' data-name-en="' + auditors.username + '"' +
                            ' data-name-ta="' + auditors.usertamilname + '" ' + isSelected +
                            '>' +
                            (lang === "en" ? auditors.username : auditors.usertamilname) +
                            '</option>'
                        );
                    });

                },

                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        } else {
            $('#audit_ad').empty();
            $('#audit_ad').append(
                '<option value="" data-name-en="---Select Audit Office---"data-name-ta="--- தணிக்கை அலுவலகத்தைத் தேர்ந்தெடுக்கவும்---">---Select Audit Office---</option>'
            );
        }

    }


    $(document).on('click', '#buttonaction', function(event) {

        event.preventDefault(); // Prevent form submission
        // insertorUpdate_instdata('insert');
        var allowedusers = '3'; // Get the number of users
        var teamsize = $('#team_size').val()
        if (teamsize > 10) {
            getLabels_jsonlayout([{
                id: 'teamsizelimit_msg',
                key: 'teamsizelimit_msg'
            }], 'N').then((text) => {
                passing_alert_value('Confirmation', text
                    .teamsizelimit_msg, 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');
            });
            return;
        } else if (teamsize < 1) {
            passing_alert_value('Confirmation', 'Team size cannot be zero', 'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');
            return;
        }

        var mandays = $('#mandays').val();

        if (mandays < 1) {
            passing_alert_value('Confirmation', 'Mandays can not be zero', 'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');
            return;
        }
        applyValidationToNewFields(`reportdesignation[1]`, 'Enter Designation');
        applyValidationToNewFields(`reportemail[1]`, 'Enter Email');
        applyValidationToNewFields(`reportmobile[1]`, 'Enter Mobile Number');

        var $mapinst_form = $('#mapinst_form');

        if ($mapinst_form.valid()) {
            var checkboxes = document.querySelectorAll('input[name="applicablefor[]"]:checked');
            if (checkboxes.length === 0) {
                getLabels_jsonlayout([{
                    id: 'selectquarter',
                    key: 'selectquarter'
                }], 'N').then((text) => {
                    passing_alert_value('Confirmation', text
                        .selectquarter, 'confirmation_alert',
                        'alert_header', 'alert_body',
                        'forward_alert');
                });

                return;

            }

            var auditmodeCode = $('#audit_mode').val();

            if (auditmodeCode == 'N' || auditmodeCode == 'T') {

                if (checkboxes.length > 1) {
                    getLabels_jsonlayout([{
                        id: 'selectonequarter',
                        key: 'selectonequarter'
                    }], 'N').then((text) => {
                        passing_alert_value('Confirmation', text
                            .selectonequarter, 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'forward_alert');
                    });

                    return;

                }
            } else if (auditmodeCode == 'Q') {
                if (checkboxes.length > 3) {
                    getLabels_jsonlayout([{
                        id: 'select_threequarter',
                        key: 'select_threequarter'
                    }], 'N').then((text) => {
                        passing_alert_value('Confirmation', text
                            .select_threequarter, 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'forward_alert');
                    });

                    return;

                }
            }
            var insttype = $('#inst_type').val();
            if (insttype == 'S' && (!$('[name="parentinstid"]').val() || $('[name="parentinstid"]').val().trim() === '')) {

                applyValidationToNewFields('parentinstid', 'Select Parent Institution');
                scrollToFirstError();
                return;
            }

            var mandays = $('#mandays').val();
            if (mandays > 1000) {
                passing_alert_value('Confirmation', 'Mandays should not exceed 1000', 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');
                return
            }

            var deptcode = $('#deptcode').val();
            if (deptcode == '01' && (!$('[name="itms_no"]').val() || $('[name="itms_no"]').val().trim() === '')) {

                applyValidationToNewFields('itms_no', 'Enter ITMS/Reg No/Reference No');
                scrollToFirstError();
                return;
            }


            $('#buttonaction').attr('disabled', true);
            insertorUpdate_instdata('insert');

        } else {

            scrollToFirstError();
        }
    });

    function insertorUpdate_instdata(action) {

        $('#buttonaction').attr('disabled', true);

        var formData = $('#mapinst_form').serializeArray();
        var deptcode = $('#deptcode').val();
        var distcode = $('#distcode').val();
        var regioncode = $('#regioncode').val();
        var audit_quarter = $('#audit_quarter').val();
        var audit_mode = $('#audit_mode').val();


        if ($('#deptcode').prop('disabled')) {

            formData.push({
                name: 'deptcode',
                value: deptcode
            });
        }
        if ($('#audit_mode').prop('disabled')) {

            formData.push({
                name: 'audit_mode',
                value: audit_mode
            });
        }
        const checkboxes = $('input[name="applicablefor[]"]'); // Select all checkboxes


        // Loop through checkboxes to check if they are disabled and selected
        checkboxes.each(function() {
            if ($(this).prop('disabled') && $(this).prop('checked')) {
                formData.push({
                    name: 'applicablefor[]',
                    value: $(this).val() // Push selected disabled checkbox value
                });
            }
        });
        if ($('#regioncode').prop('disabled')) {

            formData.push({
                name: 'regioncode',
                value: regioncode
            });
        }
        if ($('#audit_quarter').prop('disabled')) {

            formData.push({
                name: 'audit_quarter',
                value: audit_quarter
            });
        }
        if ($('#distcode').prop('disabled')) {

            formData.push({
                name: 'distcode',
                value: distcode
            });
        }


        $.ajax({
            url: '/masters/insertorupdate_mapInst', // For creating a new user or updating an existing one
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {

                    reset_form()

                    getLabels_jsonlayout([{
                        id: response.message,
                        key: response.message
                    }], 'N').then((text) => {
                        passing_alert_value('Confirmation', Object.values(text)[0],
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');
                    });
                    // fetchAlldata();
                    // reset_form();
                    // table.ajax.reload(); // Reload the table
                    fetchAlldata(lang);

                } else if (response.error) {}
            },
            complete: function() {
                // Optionally, you can re-enable the button here if desired

                $('#buttonaction').removeAttr('disabled');
            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);

                var errorMessage = response.message ||
                    'An unknown error occurred';

                passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');


                // Optionally, log the error to console for debugging
                console.error('Error details:', xhr, status, error);
            }
        });
    }


    function reset_form() {
        changeButtonAction('mapinst_form', 'action', 'buttonaction', 'reset_button',
            'display_error', @json($savebtn), @json($clearbtn),
            @json($insert));
        $('#mapinst_form')[0].reset();




        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'


        if (sessionrole == distrole) {

            $("#parentinstid,#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad").select2('destroy');
            $("#parentinstid,#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad").val(null);
            $("#parentinstid,#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad").select2();
            onchange_deptcode('', sessionregioncode, '', '', '', '', '', '');
            onchange_distcode(sessiondeptcode, sessionregioncode, sessiondistcode, '', '')
        } else if (sessionrole == regionrole) {
            $("#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad").select2('destroy');
            $("#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad").val(null);
            $("#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad").select2();
            onchange_deptcode(sessiondeptcode, sessionregioncode, '', '', '', '', '', '');

            onchange_region('district', 'distcode')
        } else if (sessionrole == headofficerole) {

            $('#distcode,#regioncode,#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad').select2('destroy');
            $('#distcode,#regioncode,#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad').select2(null);
            $('#distcode,#regioncode,#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad').select2();
            $('#distcode').empty();
            $('#distcode').append(
                '<option value="" data-name-en="---Select District---"data-name-ta="--- பகுதியைத் தேர்ந்தெடுக்கவும்---">---Select District---</option>'
            );
            onchange_deptcode(sessiondeptcode, '', '', '', '', '', '', '');

        } else {
            $("#deptcode,#regioncode,#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad,#categorization,#audit_mode").select2('destroy');
            $("#deptcode,#regioncode,#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad,#categorization,#audit_mode").val(null);
            $("#deptcode,#regioncode,#revenuedistcode,#audit_office_desig,#auditoffice,#audit_ad,#categorization,#audit_mode").select2();
            onchange_deptcode('', '', '', '', '', '', '')
            onchange_region('', '')
            $('#parentinstid').empty();
            $('#parentinstid').append(
                '<option value="" data-name-en="---Select Parent Institution---"data-name-ta="--- பெற்றோர் நிறுவனத்தைத் தேர்ந்தெடுக்கவும்---">---Select Parent Institution---</option>'
            );
        }


        $("#auditoffice,#audit_office_desig,#audit_ad,#auditee_dept,categorization").select2('');
        //   $("#auditoffice,#audit_office_desig,#audit_ad,#auditee_dept").val(null);
        //   $("#auditoffice,#audit_office_desig,#audit_ad,#auditee_dept").select2();
        $('#work-row-insert').show();
        $('#parentinst_div').hide();

        $('#EditrowUsers').html('');

        $('#row0').addClass('d-flex');
        var rowcount = '0'
        // onchange_deptcode('', '', '', '', '', '', '')
        // onchange_region('', '')


        $('#mapinst_form').validate().resetForm();
        $('#designationDrop').empty();
        dropdownCount = 1;
        //   designationdata = [];

    }

    $('#translate').change(function() {
        lang = getLanguage('Y');
        updateTableLanguage(lang);
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        // updateSelect2Language(lang); // Update Select2 dropdown
        // changeButtonText('action', 'buttonaction', 'reset_button', '', @json($savebtn),
        //     @json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'mapinst_form');

    });
</script>
@endsection