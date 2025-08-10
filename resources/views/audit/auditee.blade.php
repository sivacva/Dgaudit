@section('content')
@extends('index2')
@include('common.alert')
<style>
    /* Initially make the tabs visible */
    section {
        display: none;
    }

    .card_dark {
        border: 1px solid #7198b9;
    }
</style>
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<div class="card   .card_dark">
    <div class="card-body wizard-content">

        <div action="#" class="validation-wizard wizard-circle ">
            <input type="hidden" class="form-control" id="auditeereponseget" name="auditeereponseget">
            <input type="hidden" class="form-control" id="auditscheduleid"
                name="auditscheduleid">
            <input type="hidden" id="fromquarter" />
            <input type="hidden" id="toquarter" />
            <!-- Step 1 -->
            <h6><span class="lang" key="intimation_label">Intimation</span></h6>
            <section>
                <div class="card   card_dark">
                    <div class="card-header card_header_color lang" key="intimation_label">
                        Intimation
                    </div>
                    <div class="card-body">
                        <div class="card-header" id="inst_name"></div>
                        <input type="hidden" id="h_deptcode" />
                        <input type="hidden" id="h_catcode" />
                        <div class="row">
                            <div class="col-md-4 ">
                                <label class="form-label lang" key="entrymeeting_date"
                                    for="validationDefault02">Entry Meeting
                                    date</label>
                                <div class="input-group" onclick="datepicker('entry_date','')">
                                    <input type="text" class="form-control datepicker" id="entry_date"
                                        name="entry_date" placeholder="dd/mm/yyyy" disabled />
                                    <span class="input-group-text">
                                        <i class="ti ti-calendar fs-5"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label style="text-align:center;" key="proposed_date" align="center"
                                    class="form-label lang" for="validationDefault02">Proposed Date</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <label class="form-label " for="validationDefault02"><b
                                                    class="lang" key="fromdate_label">From
                                                    Date</b>&nbsp;&nbsp; : &nbsp;&nbsp;</label>
                                            <input type="text" class="form-control" id="start_date"
                                                name="start_date" placeholder="dd/mm/yyyy" disabled />
                                            <span class="input-group-text">
                                                <i class="ti ti-calendar fs-5"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <label class="form-label" for="validationDefault02"><b
                                                    class="lang" key="todate_label">To
                                                    Date</b>&nbsp;&nbsp; : &nbsp;&nbsp;</label>
                                            <input type="text" class="form-control" id="end_date"
                                                name="end_date" placeholder="dd/mm/yyyy" disabled />
                                            <span class="input-group-text">
                                                <i class="ti ti-calendar fs-5"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label lang" key="typeofaudit_label"
                                    for="validationDefault02">Audit Type</label>
                                <input type="text" class="form-control" value="Financial"
                                    id="audit_type" name="audit_type" disabled />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label lang" key="audityear_label"
                                    for="validationDefault02">Audit Year</label>
                                <input type="text" class="form-control" id="financial_year"
                                    name="financial_year" disabled />
                            </div>

                            <div id="annadhanamDiv" class="col-md-4">
                                <label class="form-label lang" key=""
                                    for="validationDefault02">Annadhanam Year</label>
                                <input type="text" class="form-control" id="annadhanam_year"
                                    name="annadhanam_year" disabled />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label lang" key="quarter_label"
                                    for="validationDefault02">Quarter</label>
                                <input type="text" class="form-control"
                                    value="Quarter4 (January 2024- March 2024)" id="audit_period"
                                    name="audit_period" disabled />
                            </div>

                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label lang" key="teamhead_label"
                                    for="validationDefault02">Audit Team
                                    Head</label>
                                <select class="select2 form-control custom-select" multiple="multiple"
                                    id="tm_hid" name="tm_hid" aria-placeholder="Select Member"
                                    disabled>


                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label lang" key="teammember_label"
                                    for="validationDefault02">Audit Team
                                    Member</label>
                                <select class="select2 form-control custom-select" multiple="multiple"
                                    id="tm_uid" name="tm_uid[]" aria-placeholder="Select Member"
                                    disabled>


                                </select>
                            </div>
                        </div>
                        <div class="row hide_this" id="buttonsforacceptance">
                            <div class="col-md-3 mx-auto text-center">
                                <!-- Adding text-center to center the content inside -->
                                <button type="button " class="btn btn-success lang mt-4"
                                    data-bs-toggle="modal" id="accept"
                                    data-bs-target="#success-header-modal" onclick="acceptAndProceed()"
                                    key="accepted_btn">
                                    <span class="ms-2">
                                        Accept</span>

                                </button>
                            </div>
                        </div>
                        <div id="statusmessage" class="row  hide_this">
                            <div class="col-md-8 ms-4 mt-4"><span class="required"></span>
                                <span class="lang" key="successmsg_auditee">Data has been submitted successfully.</span>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
            <!-- Step 2 -->
            <h6><span class="lang" key="records_label">Records/Details</span></h6>
            <section>
                <div class="card   card_dark">
                    <div class="card-header card_header_color lang" key="records_label">
                        Records/Details
                    </div>
                    <div class="card-body">
                        <form id="callforrecords" name="callforrecords">
                            @csrf
                            <input type="hidden" class="form-control" id="audit_scheduleid"
                                name="auditscheduleid">
                            <h5 class="mt-2 lang auditparticularlabel_audit" key="audit_particulars_label">Audit
                                Particulars</h5>
                            <div class="table-responsive rounded-4">
                                <table class="table table-bordered">
                                    <tbody id="part_details"></tbody>
                                </table>
                                <table class="table table-bordered">
                                    <tbody id="part_details_fetch"></tbody>
                                </table>


                            </div>
                            <div id="details_tabletab_buttons" class="row">
                                <div class="col-md-3  mx-auto">
                                    <button class="btn btn-success mt-3 lang" key="submit" type="submit"
                                        action="insert" id="buttonaccept" name="buttonaccept">
                                        Submit
                                    </button>


                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </section>
            <!-- Step 3 -->
            <h6><span class="lang" key="audituserdetails_label">Auditee Officers</span></h6>
            <section>
                <div class="card  card_dark">
                    <div class="card-header card_header_color">
                        Auditee Officer Details
                    </div>
                    <div class="card-body">
                        <form id="auditee_officeusers" name="auditee_officeusers">
                            @csrf
                            <input type="hidden" name="auditscheduleid" id="officeuseraudit_scheduleid" />
                            <input type="hidden" name="auditee_ofcusercount" id="auditee_ofcusercount" />
                            <input type="hidden" class="form-control" id="auditeereponseget" name="auditeereponseget">

                            <div id="appendusers" class="single-note-item">
                                <div id="addrowUsers">
                                    <div class="row">
                                        <div class="col-md-1">

                                        </div>

                                        <div class="col-md-3 ms-2">

                                        </div>

                                        <div class="col-md-2 ms-2">

                                        </div>

                                        <div class="col-md-4 ms-2">

                                            <center><b class="lang" key="serviceperiod">Service Period </b></center>
                                        </div>

                                        <div class="col-md-3">

                                        </div>
                                    </div>
                                    <div class="d-flex mt-2 work-row-insert" id="row0">
                                        <input type="hidden" name="officeuserid[1]" value="UserId_1">

                                        <div class="col-md-1">
                                            <label class="form-label lang" key="s_no" for="validationDefaultUsername">S.No</label>
                                            <input type="text" class="form-control alpha_numeric" value="1" disabled>
                                        </div>

                                        <div class="col-md-3 ms-2">
                                            <label class="form-label lang" key="name" for="validationDefaultUsername">Name</label>
                                            <input type="text" class="form-control name " maxlength="50" id="name0" data-placeholder-key="username" name="officeusername[1]" value="" placeholder="Enter Name">
                                        </div>

                                        <div class="col-md-2 ms-2">
                                            <label class="form-label lang" key="designation" for="validationDefaultUsername">Designation</label>
                                            <input type="text" class="form-control name" maxlength="100" data-placeholder-key="designation_ph" name="officeuserdesignation[1]" id="designation0" value="" placeholder="Enter Designation">
                                        </div>

                                        <div class="col-md-2 ms-2">
                                            <label class="form-label lang" for="validationDefaultUsername">From Date</label>
                                            <div class="input-group" onclick="datepicker('from_date','','1')">
                                                <input type="text" id="from_date1" class="form-control datepicker"
                                                    name="officeuserfromdate[1]" placeholder="dd/mm/yyyy" />
                                                <span class="input-group-text">
                                                    <i class="ti ti-calendar fs-5"></i>
                                                </span>
                                            </div>
                                        </div>


                                        <div class="col-md-2 ms-2">
                                            <label class="form-label lang" key="to_date" for="validationDefaultUsername">To Date</label>
                                            <div class="input-group" onclick="datepicker('to_date','','1')">
                                                <input type="text" class="form-control datepicker" id="to_date1"
                                                    name="officeusertodate[1]" placeholder="dd/mm/yyyy" />
                                                <span class="input-group-text">
                                                    <i class="ti ti-calendar fs-5"></i>
                                                </span>
                                            </div>

                                        </div>

                                        <div class="col-md-3 actionbtns">
                                            <label class="form-label lang" key="action" for="validationDefaultUsername">Action</label><br>
                                            <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn" onclick="addNewWorkRow(event,'insert')">
                                                <i class="ti ti-circle-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="EditrowUsers">

                            </div>


                            <div id="statusmessage_auditeeusers" class="row  hide_this">
                                <div class="col-md-8 ms-4 mt-4"><span class="required"></span>
                                    <span class="lang" key="datasubmitsuccess_auditeeofc">Institution Official Details submitted successfully.</span>
                                </div>
                            </div>
                            <br class="row actionbtns">
                            <hr class="row actionbtns">
                            <!--<span style="color:red;">Note: Have Priviledge to Add/Modify Institution Official Details till acceptance</span>-->
                            <div class="row actionbtns">
                                <div class="col-md-3  mx-auto">
                                    <button class="btn btn-success mt-3 lang" key="submit" type="submit" id="officeuser_submit" onclick="insertAuditOfficersDetail()">Submit</button>

                                </div>

                            </div><br class="row ">
                        </form>
                    </div>



                </div>

            </section>

        </div>
    </div>
</div>
<script src="../assets/js/vendor.min.js"></script>
<!-- Import Js Files -->



<!-- solar icons -->
<!-- <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script> -->
<script src="../assets/libs/jquery-steps/build/jquery.steps.min.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>
<script src="../assets/js/apps/notes.js"></script>
<script src="../assets/libs/simplebar/dist/simplebar.min.js"></script>

<!-- <script src="../assets/js/forms/form-wizard.js"></script> -->
<script>
    var form = $(".validation-wizard").show();

    $(".validation-wizard").steps({
        headerTag: "h6",
        bodyTag: "section",
        transitionEffect: "fade",
        titleTemplate: '<span class="step">#index#</span> #title#',
        labels: {
            finish: "Submit",
        },

        onStepChanging: function(event, currentIndex, newIndex) {
            // if (currentIndex > newIndex) return true;

            // Step 0 to Step 1 logic
            if (currentIndex === 0 && newIndex === 1) {

                if (recordStatus === 'Y') {
                    var audit_scheduleidget = $('#auditscheduleid').val();
                    acceptstatus(audit_scheduleidget);
                    return true;
                } else if (recordStatus === 'N') {
                    fetch_audit_particulars_detail();
                    return true;
                } else {
                    alert("No information Found");
                    $('.actions a[href="#next"]').parent('li').addClass('disabled');

                    return false;
                }
            }

            // ? Step 2 to Step 3 (last page)   Add your condition here
            if (currentIndex === 1 && newIndex === 2) {
                // Replace with your condition
                if (recordStatus === 'N' && auditofficerdetailStatus == 'N') {
                    const $callforrecordsForm = $("#callforrecords");

                    event.preventDefault(); // Prevent form submission
                    restrictSpecialChars("#nodalname, #nodaldesignation, #auditee_remarks");
                    validateRadioButtons();
                    if ($callforrecordsForm.valid()) {

                        getLabels_jsonlayout([{
                            id: 'confirmation_submit',
                            key: 'confirmation_submit'
                        }], 'N').then((text) => {
                            passing_alert_value('Confirmation', Object.values(
                                    text)[0], 'confirmation_alert',
                                'alert_header', 'alert_body',
                                'forward_alert');
                        });

                        $("#process_button").addClass("button_confirmation");
                        $('#process_button').removeAttr('data-bs-dismiss');
                        //  $('.button_confirmation').data('auditplanid', auditplanid);
                    }
                    return false;
                } else if (recordStatus === 'Y' && auditofficerdetailStatus == 'N') {
                    $('.actions a[href="#finish"]').parent('li').addClass('disabled');

                    ofcUserDetailsTab();
                    return true;
                } else if (recordStatus === 'Y' && auditofficerdetailStatus == 'Y') {
                    $('.actions a[href="#finish"]').parent('li').addClass('disabled');

                    ofcUserDetailsTab();
                    return true;
                } else {
                    return true;
                }
            }
            ////////////previous//////////////


            if (currentIndex > newIndex) {
                if (currentIndex === 1 && newIndex === 0) {
                    if (recordStatus === 'Y') {
                        return true;
                    } else if (recordStatus === 'N') {
                        passing_alert_value('Confirmation', 'Data is not saved! Please Submit the Records', 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'confirmation_alert');
                        return false;
                    } else {
                        alert("Unexpected status or no data.");
                        return false;
                    }
                }

                if (currentIndex === 2 && newIndex === 1) {
                    if (recordStatus === 'Y' && auditofficerdetailStatus == 'Y') {
                        $('.actions a[href="#finish"]').parent('li').addClass('disabled');


                        ofcUserDetailsTab();
                        return true;
                    } else if (recordStatus === 'Y' && auditofficerdetailStatus == 'N') {
                        passing_alert_value('Confirmation', 'Data is not saved! Please Submit the Records', 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'confirmation_alert');
                        return false;
                    } else {
                        return true;
                    }
                }
            }



        },


        onFinishing: function(event, currentIndex) {

            return $("#auditee_officeusers").valid(); // Validate only on final step
        },

        onFinished: function(event, currentIndex) {
           // insertAuditOfficersDetail()
        }
    });


    function acceptAndProceed() {
        // Your custom logic here if needed


        // Move to the next step of the wizard
        $(".validation-wizard").steps("next");
    }


    ///////////////////////////////////////////////////////////////////////wizard/////////////////////////////////////////////
    function datepicker(fieldType, setdate, rowCount) {
        const fromVal = $(`#from_date${rowCount}`).val();
        const toVal = $(`#to_date${rowCount}`).val();

        let minDate = null;
        let maxDate = new Date();

        // Only apply limits if the paired field has a value
        if (fieldType === 'to_date' && fromVal) {
            const parts = fromVal.split('/');
            const fromDate = new Date(parts[2], parts[1] - 1, parts[0]);
            fromDate.setDate(fromDate.getDate() + 1);
            minDate = new Date(fromDate); // to_date must be after from_date

        }

        if (fieldType === 'from_date' && toVal) {
            const parts = toVal.split('/');
            const toDate = new Date(parts[2], parts[1] - 1, parts[0]);
            toDate.setDate(toDate.getDate() - 1);
            maxDate = new Date(toDate); // from_date must be before to_date
        }

        let inputId = `${fieldType}${rowCount}`;
        let fromvalclr = `from_date${rowCount}`;
        let tovalclr = `to_date${rowCount}`;
        let form = 'cleardateform';

        init_datepicker(inputId, minDate, maxDate, setdate, form, fromvalclr, tovalclr, 'serviceperiod');
    }

    // Global //
    let audit_schedule;
    let recordStatus;
    let auditofficerdetailStatus;

    //////////////////////////////////////////auditee officer details/////////////////////////////////////////
    function insertAuditOfficersDetail() {
        event.preventDefault();
        // ? Copy logic from #officeuser_submit handler here
        var lang = getLanguage('Y');
        var allowedusers = $('#auditee_ofcusercount').val();

        for (var i = 1; i <= allowedusers; i++) {
            applyValidationToNewFields(`officeusername[${i}]`, errorMessages[lang]['username']);
            applyValidationToNewFields(`officeuserdesignation[${i}]`, errorMessages[lang]['designation_ph']);
            applyValidationToNewFields(`officeuserfromdate[${i}]`, errorMessages[lang]['from_date']);
          //  applyValidationToNewFields(`officeusertodate[${i}]`, errorMessages[lang]['to_date']);

        }

        if ($("#auditee_officeusers").valid()) {
            var formData = $('#auditee_officeusers').serializeArray();
            // console.log(formData);
            // return;
            $.ajax({
                url: 'audit/store_auditeeofficeusers',
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.message == 'datasubmitsuccess') {

                        getLabels_jsonlayout([{
                            id: response.message,
                            key: response.message
                        }], 'N').then((text) => {
                            passing_alert_value('Confirmation', Object.values(text)[0], 'confirmation_alert',
                                'alert_header', 'alert_body', 'confirmation_alert');
                        });


                    } else {


                        getLabels_jsonlayout([{
                            id: response.message,
                            key: response.message
                        }], 'N').then((text) => {
                            passing_alert_value('Confirmation', Object.values(text)[0], 'confirmation_alert',
                                'alert_header', 'alert_body', 'confirmation_alert');
                        });
                        auditofficerdetailStatus = 'Y';
                        $('#auditeereponseget').val('A');
                        $('#addrowUsers').hide();
                        ofcUserDetailsTab();
                        $('.actions a[href="#finish"]').parent('li').addClass('disabled');
                        fetchalldata(lang)
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.error == 401) {
                        handleUnauthorizedError();
                    } else {
                        getLabels_jsonlayout([{
                            id: response.message,
                            key: response.message
                        }], 'N').then((text) => {
                            let alertMessage = Object.values(text)[0] || "Error Occurred";
                            passing_alert_value('Confirmation', alertMessage, 'confirmation_alert',
                                'alert_header', 'alert_body', 'confirmation_alert');
                        });
                    }
                }
            });
        }
    }

    function ofcUserDetailsTab(formname = '') {

        var auditscheduleid = $('#officeuseraudit_scheduleid').val();

        $.ajax({
            url: 'audit/fetch_auditeeofficeusers', // Replace with your endpoint
            method: 'POST',
            data: {
                auditscheduleid: auditscheduleid
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // Pass CSRF token in headers
            },
            success: function(response) {
                var auditeereponseget = $('#auditeereponseget').val();
                if (formname == 'intimationform') {

                    if (response.exists == 0) {
                        //passing_alert_value('Confirmation', 'Please add auditee user details before accept', 'confirmation_alert','alert_header', 'alert_body', 'forward_alert');


                        $('#audituserdetailstab a').addClass('active');

                        audituserdetailsdiv.style.display = 'block';
                        audituserdetailsdiv.classList.add('show', 'active'); // Make it "active" for Bootstrap tab



                        //ofcUserDetailsTab();            
                    } else {
                        return 'success';
                    }

                } else {
                    if (response.exists == 1) {

                        $('#addrowUsers').hide();
                        $('#EditrowUsers').empty();
                        $('#EditrowUsers').show();

                        var tablehead = ` <div class="row">
                                                        <div class="col-md-1"></div>

                                                        <div class="col-md-3 ms-2"></div>

                                                        <div class="col-md-2 ms-2"></div>

                                                        <div class="col-md-4 ms-2">
                                                            <center><b class="lang" key="serviceperiod">Service Period</b></center>
                                                        </div>

                                                        <div class="col-md-3"></div>
                                                    </div>

                                                    <div class="d-flex mt-2 work-row" id="row0">
                                                        <div class="col-md-1">
                                                            <label class="form-label lang" key="s_no" for="validationDefaultUsername">S.No</label>
                                                        </div>

                                                        <div class="col-md-3 ms-2">
                                                            <label class="form-label lang" key="name" for="validationDefaultUsername">Name</label>
                                                        </div>

                                                        <div class="col-md-2 ms-2">
                                                            <label class="form-label lang" key="designation" for="validationDefaultUsername">Designation</label>
                                                        </div>

                                                        <div class="col-md-2 ms-2">
                                                            <label class="form-label lang" key="from_date" for="validationDefaultUsername">From Date</label>
                                                        </div>

                                                        <div class="col-md-2 ms-2">
                                                            <label class="form-label lang" key="to_date" for="validationDefaultUsername">To Date</label>
                                                        </div>
                                                    </div>
                                                `;
                        $('#EditrowUsers').append(tablehead);
                        translate();

                        var rowCount = 0; // Initialize row count

                        // Loop through the data and append to the HTML
                        $.each(response.fetch_auditeeofficeusers, function(index, item) {
                            rowCount++; // Increment rowCount for each iteration


                            // Create HTML template for a new row
                            var appendHTML = `
                                        <div class="d-flex mt-2 work-row-edit" id="row${rowCount}">
                                            <input type="hidden" name="officeuserid[${rowCount}]" value="UserId_${rowCount}">

                                            <div class="col-md-1">
                                                <input type="text" class="form-control alpha_numeric" value="${rowCount}" disabled>
                                            </div>

                                            <div class="col-md-3 ms-2">
                                                <input type="text" class="form-control name" maxlength="50" name="officeusername[${rowCount}]" value="${item.ofc_username || ''}" placeholder="Enter Name">
                                            </div>

                                            <div class="col-md-2 ms-2">
                                                <input type="text" class="form-control name"  maxlength="100" name="officeuserdesignation[${rowCount}]" id="designation${rowCount}" value="${item.ofc_designation || ''}" placeholder="Enter Designation">
                                            </div>

                                            <div class="col-md-2 ms-2">
                                                <div class="input-group" onclick="datepicker('from_date','','1${rowCount}')">
                                                    <input type="text"  id="from_date1${rowCount}"  value="${item.converted_service_fromdate || ''}" class="form-control datepicker" 
                                                               name="officeuserfromdate[${rowCount}]" placeholder="dd/mm/yyyy" />
                                                    <span class="input-group-text"><i class="ti ti-calendar fs-5"></i></span>
                                                </div>
                                            </div>

                                            <div class="col-md-2 ms-2">
                                                <div class="input-group" onclick="datepicker('to_date','','1${rowCount}')">
                                                    <input type="text"  value="${item.converted_service_todate || ''}" class="form-control datepicker" id="to_date1${rowCount}"
                                                               name="officeusertodate[${rowCount}]" placeholder="dd/mm/yyyy" />
                                                    <span class="input-group-text"><i class="ti ti-calendar fs-5"></i></span>
                                                </div>
                                            </div>

                                            <div class="col-md-3 actionbtns">
                                                <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn" onclick="addNewWorkRow(event,'edit')">
                                                    <i class="ti ti-circle-plus"></i> 
                                                </button>
                                                <button type="button" class="btn btn-danger fw-medium ms-2 removeRowBtn" onclick="removeRow(this,'edit')">
                                                    <i class="ti ti-circle-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    `;



                            $('#EditrowUsers').append(appendHTML);
                        });
                        if (auditeereponseget == 'A') {
                            $('#EditrowUsers input').attr('disabled', true);
                            $('.actionbtns').hide();
                            $('#statusmessage_auditeeusers').show();


                        }


                    } else {

                        $('#addrowUsers').show();
                        $('#EditrowUsers').hide();

                        if (auditeereponseget == 'A') {
                            $('#addrowUsers input').attr('disabled', true);
                            $('.actionbtns').hide();

                        }

                    }

                }


            },
            error: function(xhr, status, error) {
                var response = JSON.parse(xhr.responseText);
                if (response.error == 401) {
                    handleUnauthorizedError();
                } else {

                    getLabels_jsonlayout([{
                        id: response.message,
                        key: response.message
                    }], 'N').then((text) => {
                        let alertMessage = Object.values(text)[0] ||
                            "Error Occured";
                        passing_alert_value('Confirmation', alertMessage,
                            'confirmation_alert', 'alert_header',
                            'alert_body', 'confirmation_alert');
                    });
                }
            }
        });
    }

    // Function to add a new row using jQuery
    function addNewWorkRow(event, action = '') {
        // Get the next row index based on existing rows
        const rowCount = $('.work-row-' + action + '').length + 1; // Start from 2, hence +1
        //var allowedusers ='<?php //echo $allowedusers; 
                                ?>';
        var allowedusers = $('#auditee_ofcusercount').val();
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
                    <input type="hidden" name="officeuserid[${rowCount}]" value="UserId_${rowCount}">

                    <div class="col-md-1">
                        <input type="text" class="form-control alpha_numeric" value="${rowCount}" disabled>
                    </div>

                    <div class="col-md-3 ms-2">
                        <input type="text" class="form-control name"  data-placeholder-key="username" name="officeusername[${rowCount}]"  value="" placeholder="Enter Name">
                    </div>

                    <div class="col-md-2 ms-2">
                        <input type="text" class="form-control name" data-placeholder-key="designation_ph" name="officeuserdesignation[${rowCount}]" id="designation${rowCount}" value="" placeholder="Enter Designation">
                    </div>

                    <div class="col-md-2 ms-2">
                        <div class="input-group" onclick="datepicker('from_date','','${rowCount}')">
                            <input type="text"  id="from_date${rowCount}" class="form-control datepicker"  name="officeuserfromdate[${rowCount}]" placeholder="dd/mm/yyyy" />
                            <span class="input-group-text"><i class="ti ti-calendar fs-5"></i></span>
                        </div>
                    </div>

                    <div class="col-md-2 ms-2">
                        <div class="input-group" onclick="datepicker('to_date','','${rowCount}')">
                            <input type="text"  id="to_date${rowCount}"  class="form-control datepicker"  name="officeusertodate[${rowCount}]" placeholder="dd/mm/yyyy" />
                            <span class="input-group-text"><i class="ti ti-calendar fs-5"></i></span>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <button type="button" class="btn btn-success fw-medium ms-2 addRowBtn" onclick="addNewWorkRow(event,'${action}')">
                            <i class="ti ti-circle-plus"></i> 
                        </button>
                        <button type="button" class="btn btn-danger fw-medium ms-2 removeRowBtn" onclick="removeRow(this,'${action}')">
                            <i class="ti ti-circle-minus"></i>
                        </button>
                    </div>
                </div>
            `;

        if (action == 'insert') {
            $('#addrowUsers').append(newRowHtml);

        } else {
            $('#EditrowUsers').append(newRowHtml);

        }
        var lang = getLanguage('Y');

        updatePlaceholders(lang);



        // Append the new row using jQuery to the container
    }



    function applyValidationToNewFields(inputName, message) {
        let $input = $("[name='" + inputName + "']"); // Select input by name
        // alert(inputName)
        if ($input.length) {

            let validator = $("#auditee_officeusers").data("validator"); // Get validator instance

            if (!validator) {

                $("#auditee_officeusers").validate({ // Initialize validation if not already done
                    errorPlacement: function(error, element) {
                        // Check if the element has the 'datepicker' class
                        if (element.hasClass('datepicker')) {

                            // Insert the error message after the input-group, so it appears below the input and icon
                            //  error.insertAfter('.form-control');
                            element.closest('.input-group').parent().append(error);
                        } else {

                            // Default behavior: insert the error message after the input field
                            error.insertAfter(element);
                        }
                    }
                });
                validator = $("#auditee_officeusers").data("validator");
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

            // ? Ensure validation runs on change without removing existing messages
            $input.on("change", function() {
                $(this).valid(); // Validate when the input changes
            });
        } else {
            //  console.error("? Element not found:", inputName); // Handle case if element is not found
        }
    }


    // Function to remove a row
    function removeRow(button, action) {
        // Find the closest row and remove it
        const rowToRemove = $(button).closest('.work-row-' + action + '');
        rowToRemove.remove();

        // Recalculate and update the S.NO values after removing a row
        //updateSerialNumbers();
    }


    /////////////////////////////////////////auditee officer details-END ////////////////////////////////////
    function fetchalldata(lang) {
        $.ajax({
            url: 'audit/audit_scheduledetails', // Replace with your endpoint
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // Pass CSRF token in headers
            },
            success: function(response) {

                $('#fromquarter').val(response.Quarter['fromquarter']);
                $('#toquarter').val(response.Quarter['toquarter']);

                audit_schedule = response.data;
                $('#h_deptcode').val(audit_schedule[0].deptcode);
                $('#h_catcode').val(audit_schedule[0].catcode);

                const audit_period = response.auditperiod;

                var concat = audit_period.from + ' - ' + audit_period.to;
                // $('#financial_year').val(concat);
                const audit_year = audit_schedule[0].yearname;
                $('#financial_year').val(audit_year);



                if (audit_schedule[0].deptcode == '01' && audit_schedule[0].annadhanam_only == 'Y') {
                    $('#annadhanamDiv').show();
                    const annadhanam_year = audit_schedule[0].annadhanamyear;
                    $('#annadhanam_year').val(annadhanam_year);
                } else {
                    $('#annadhanamDiv').hide();
                }






                if (audit_schedule && audit_schedule.length > 0) {
                    var auditeeresponse = audit_schedule[0].auditeeresponse;

                    if (auditeeresponse == 'A') {
                        $('#buttonsforacceptance').hide();
                        $('#statusmessage').show();
                        recordStatus = 'Y'
                        auditofficerdetailStatus = 'Y'


                    } else if (auditeeresponse == 'R') {
                        passing_alert_value('Confirmation', 'Auditee Officer Detail is not Submitted! Please fill the form.', 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'confirmation_alert');
                        $('#buttonsforacceptance').show();
                        // $('#statusmessage').show();
                        recordStatus = 'Y'
                        auditofficerdetailStatus = 'N'


                    } else {
                        $('#buttonsforacceptance').show();
                        $('#statusmessage').hide();
                        recordStatus = 'N'
                        auditofficerdetailStatus = 'N'
                    }



                    if (lang == 'ta') {
                        var instname = audit_schedule[0].insttname;
                        var typeofaudit = audit_schedule[0].typeofaudittname;
                        var auditquarter = audit_schedule[0].auditquartertamil;

                    } else {
                        var instname = audit_schedule[0].instename;
                        var typeofaudit = audit_schedule[0].typeofauditename;
                        var auditquarter = audit_schedule[0].auditquarter;

                    }

                    $('#inst_name').text(instname);
                    $('#audit_type').val(typeofaudit);
                    $('#audit_period').val(auditquarter);

                    $('#officeuseraudit_scheduleid').val(audit_schedule[0].encrypted_auditscheduleid);
                    $('#auditee_ofcusercount').val(audit_schedule[0].auditee_ofcusercount);

                    $('#audit_scheduleid').val(audit_schedule[0].encrypted_auditscheduleid);
                    $('#auditscheduleid').val(audit_schedule[0].encrypted_auditscheduleid);

                    $('#auditscheduleidNewforGet').val(audit_schedule[0].encrypted_auditscheduleid);

                    $('#auditeeresponsehidden').val(auditeeresponse);
                    $('#auditeereponseget').val(auditeeresponse);



                    $('#entry_date').val(convertDateFormatYmd_ddmmyy(audit_schedule[0]
                        .fromdate));
                    $('#start_date').val(convertDateFormatYmd_ddmmyy(audit_schedule[0]
                        .fromdate));
                    $('#end_date').val(convertDateFormatYmd_ddmmyy(audit_schedule[0]
                        .todate));


 	            $('#tm_hid').next('.select2').remove(); // Removes any extra Select2 container
                    $('#tm_uid').next('.select2').remove(); 
                    // Clear existing options in both dropdowns
                    $('#tm_uid, #tm_hid').empty();

                    // Get selected team members' user IDs
                    const selectedTeamMembers = audit_schedule.map(member => member.userid);

                    // If there are any selected team members
                    if (selectedTeamMembers.length > 0) {

                        // Iterate over the response data to append options dynamically
                        audit_schedule.forEach(member => {
                            // Check if the member is in the selected list
                            const isSelected = selectedTeamMembers.includes(member.userid);
                            // Check if the member is a team member or a team head based on 'teamtype'
                            if (member.auditteamhead === 'N') {
                                // Create a new option element for team members
                                let newOption = new Option(
                                    `${lang === 'ta' ?member.usertamilname +' - '+ member.desigtlname : member.username +'-'+member.desigelname}`, // Display text
                                    member.userid, // Option value
                                    isSelected, // Set as selected in the dropdown if it's in selectedTeamMembers
                                    isSelected // Mark as selected for Select2
                                );

                                // Append the new option to the Team Member dropdown
                                $('#tm_uid').append(newOption);
                            } else if (member.auditteamhead === 'Y') {
                                // Create a new option element for team heads
                                let newOption = new Option(
                                    `${lang === 'ta' ?member.usertamilname +' - '+ member.desigtlname : member.username +'-'+member.desigelname}`, // Display text
                                    member.userid, // Option value
                                    isSelected, // Set as selected in the dropdown if it's in selectedTeamMembers
                                    isSelected // Mark as selected for Select2
                                );

                                // Append the new option to the Team Head dropdown
                                $('#tm_hid').append(newOption);
                            }
                        });

                        // Re-initialize Select2 for both dropdowns
                        $('#tm_uid').select2({
                            placeholder: "Select Team Member",
                            allowClear: true
                        });

                        $('#tm_hid').select2({
                            placeholder: "Select Team Head",
                            allowClear: true
                        });

                        // Set selected values for both dropdowns
                        $('#tm_uid').val(selectedTeamMembers).trigger('change');
                        $('#tm_hid').val(selectedTeamMembers).trigger('change');
                    }


                }




            },
            error: function(xhr, status, error) {
                var response = JSON.parse(xhr.responseText);
                if (response.error == 401) {
                    handleUnauthorizedError();
                } else {
                    $('.actions a[href="#next"]').parent('li').addClass('disabled');
                    getLabels_jsonlayout([{
                        id: response.message,
                        key: response.message
                    }], 'N').then((text) => {
                        let alertMessage = Object.values(text)[0] ||
                            "Error Occured";
                        passing_alert_value('Confirmation', alertMessage,
                            'confirmation_alert', 'alert_header',
                            'alert_body', 'confirmation_alert');
                    });
                }
            }
        });
    }
    //////////////////////////////Record////////////////////////////////////////////////
    function validateRadioButtons() {

        $("input[type='radio'][name$='-radio']").each(function() {
            let groupName = $(this).attr("name");
            $(`input[name="${groupName}"]`).rules("add", {
                radioRequired: true
            });
        });
    }

    function getlanguagelc() {
        // Example: Retrieve from localStorage or set to 'en' if not available
        return window.localStorage.getItem('lang') || 'en';
    }
    loadJsonData(); // Ensure this function loads the necessary data correctly

    // Custom validation method for special characters
    $.validator.addMethod("noSpecialChars", function(value, element) {
        return this.optional(element) || /^[^{}\[\]:;",|~`'"#^]+$/.test(value);
    }, function() {
        // Get the current language and return the localized error message
        const language = getlanguagelc(); // Fetch current language (like 'en' or 'ta')
        return errorMessages[language]['specialChars']; // Return the localized message for special characters
    });

    // Custom validation for radio button selection
    $.validator.addMethod("radioRequired", function(value, element) {
        let radioName = $(element).attr("name");

        // Check if any radio button is selected
        return $(`input[name="${radioName}"]:checked`).length > 0;
    }, function() {
        // Get the current language and return the localized message for radio selection
        const language = getlanguagelc();
        return errorMessages[language]['yesorno']; // Return the localized message for Yes/No selection
    });

    // Custom validation method to check if file is required when 'Y' is selected
    $.validator.addMethod("fileRequiredIfYes", function(value, element) {
        let accountId = $(element).attr("id").split("-")[0]; // Extracts the ID prefix
        let selectedRadio = $(`input[name="${accountId}-radio"]:checked`).val();
        return selectedRadio === "Y" ? value !== "" : true; // File is required if 'Y' is selected
    }, function() {
        // Get the current language and return the localized error message
        const language = getlanguagelc();
        return errorMessages[language]['fileRequiredIfYes']; // Return localized message for file required
    });

    // Custom validation for file size limit (3MB)
    $.validator.addMethod("fileSizeLimit", function(value, element) {
        if (element.files.length > 0) {
            return element.files[0].size <= 1 * 1024 * 1024; // 1MB limit
        }
        return true;
    }, function() {
        // Get the current language and return the localized error message for file size
        const language = getlanguagelc();
        return errorMessages[language]['fileSizeLimit']; // Return localized message for file size limit
    });

    // Custom validation for valid file types (PNG, JPEG, PDF, Excel)
    $.validator.addMethod("validFileType", function(value, element) {
        if (value) {
            let allowedTypes = ["pdf", "png", "jpeg", "jpg", "xls", "xlsx"];
            let fileExtension = value.split(".").pop().toLowerCase();
            return allowedTypes.includes(fileExtension);
        }
        return true;
    }, function() {
        // Get the current language and return the localized error message for valid file type
        const language = getlanguagelc();
        return errorMessages[language]['validFileType']; // Return localized message for file type validation
    });
    $(document).on('click', '#buttonaccept', function(event) {

        event.preventDefault(); // Prevent form submission
        restrictSpecialChars("#nodalname, #nodaldesignation, #auditee_remarks");
        validateRadioButtons();
        if ($callforrecordsForm.valid()) {
            getLabels_jsonlayout([{
                id: 'confirmation_submit',
                key: 'confirmation_submit'
            }], 'N').then((text) => {
                passing_alert_value('Confirmation', Object.values(
                        text)[0], 'confirmation_alert',
                    'alert_header', 'alert_body',
                    'forward_alert');
            });
            $("#process_button").addClass("button_confirmation");
            $('#process_button').removeAttr('data-bs-dismiss');
            // $('.button_confirmation').data('auditplanid', auditplanid);
        }
        //  else {
        //     alert("Please fill out all required fields correctly before submitting.");
        // }

    });
    $(document).on('click', '.button_confirmation', function() {
        $("#process_button").prop("disabled", true);

        // if ($callforrecordsForm.valid()) {
        $('#callforrecords').append(
            `<input type="hidden" name="auditscheduleid" value="${$('#audit_scheduleid').val()}">`);

        // Create the FormData object
        var formData = new FormData($('#callforrecords')[0]);

        $.ajax({
            url: 'audit/auditee_accept', // Replace with your endpoint
            method: 'POST',
            data: formData,
            processData: false, // Disable automatic data processing
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // Pass CSRF token in headers
            },
            success: function(response) {
                $("#process_button").prop("disabled", false);

                // var validator = $("#audit_schedule").validate();
                // validator.resetForm();
                if (response.success) {
                    recordStatus = 'Y';
                    if (response.message == 'movetonexttab') {

                        getLabels_jsonlayout([{
                            id: 'add_audituserdetails',
                            key: 'add_audituserdetails'
                        }], 'N').then((text) => {
                            passing_alert_value('Confirmation', Object.values(
                                    text)[0], 'confirmation_alert',
                                'alert_header', 'alert_body',
                                'confirmation_alert');
                        });
                        var auditscheduleid = $('#auditscheduleid').val();

                        acceptstatus(auditscheduleid)
                        $(".validation-wizard").steps("next");
                        // var movetonexttab = ofcUserDetailsTab('intimationform');
                        // //$('#details-tab').addClass('disabled');

                    } else {


                        getLabels_jsonlayout([{
                            id: response.message,
                            key: response.message
                        }], 'N').then((text) => {
                            passing_alert_value('Confirmation', Object.values(
                                    text)[0], 'confirmation_alert',
                                'alert_header', 'alert_body',
                                'confirmation_alert');
                        });
                        $('#auditeereponseget').val('A');
                        ofcUserDetailsTab();
                        // $('#details-tab').removeClass('disabled');
                        // $('#part_details').hide();

                        // const detailsTab = document.getElementById('details-tab');
                        // const detailsContent = document.getElementById('details-section');

                        // Disable the "Details" tab
                        /*detailsTab.classList.add('disabled');
                        detailsTab.removeAttribute('href');
                        detailsTab.removeAttribute('data-bs-toggle');*/

                        // Hide the content of the "Details" tab
                        // if (detailsContent) {

                        //     detailsContent.style.display = 'none';
                        // }
                        // $('.date_change').hide();
                        // $('.nav-link').removeClass('active');

                        // // Add 'active' class to #all-category
                        // $('#all-category').addClass('active');
                        // $('.all-category').show();
                        $('#buttonsforacceptance').hide();
                        $('#statusmessage').show();


                    }


                }



            },
            error: function(xhr, status, error) {
                $("#process_button").prop("disabled", false);

                var response = JSON.parse(xhr.responseText);
                if (response.error == 401) {
                    handleUnauthorizedError();
                } else {

                    getLabels_jsonlayout([{
                        id: response.message,
                        key: response.message
                    }], 'N').then((text) => {
                        let alertMessage = Object.values(text)[0] ||
                            "Error Occured";
                        passing_alert_value('Confirmation', alertMessage,
                            'confirmation_alert', 'alert_header',
                            'alert_body', 'confirmation_alert');
                    });
                }
            }
        });

    });


    $("#callforrecords").validate({
        rules: {
            "textarea[name$='-cfrvalues'], textarea[name$='-accountvalues']": {
                required: true,
                minlength: 10,
                noSpecialChars: true,
            },

            "input[type='radio'][name$='-radio']": {
                radioRequired: true,
            },
            // Validate file upload only if 'Y' is selected
            "input[type='file'][name$='-accountfile']": {
                fileRequiredIfYes: true,
                fileSizeLimit: true, // File size limit
                validFileType: true, // File type validation
            },
            nodalname: {
                required: true,
                noSpecialChars: true,

            },
            nodalmobile: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 10,
                noSpecialChars: true,
            },
            nodalemail: {
                required: true,
                email: true,
                noSpecialChars: true,
            },
            nodaldesignation: {
                required: true,
                noSpecialChars: true,
            },
            auditee_remarks: {
                required: true,
                minlength: 10,
                noSpecialChars: true,
            },
        },
        messages: {
            "input[type='radio'][name$='-radio']": {
                radioRequired: "Please select Yes or No",
            },
            "input[type='file'][name$='-accountfile']": {
                fileRequiredIfYes: "File is required ",
                fileSizeLimit: "File size must be less than 1MB",
                validFileType: "Allowed file types: PNG, JPEG, PDF, Excel",
            },

            "textarea[name$='-cfrvalues'], textarea[name$='-accountvalues']": {
                required: "Remarks are required",
                minlength: "Remarks must be at least 10 characters long",
                noSpecialChars: "Special characters are not allowed",
            },
            nodalname: {
                required: "Name is required",
                noSpecialChars: "Enter the valid Remarks"

            },
            nodalmobile: {
                required: "Mobile number is required",
                digits: "Enter a valid mobile number",
                minlength: "Must be 10 digits",
                maxlength: "Must be 10 digits",
                noSpecialChars: "Enter the valid Remarks"
            },
            nodalemail: {
                required: "Email is required",
                email: "Enter a valid email address",
                noSpecialChars: "Enter the valid Remarks"
            },
            nodaldesignation: {
                required: "Designation is required",
                noSpecialChars: "Enter the valid Remarks"
            },
            auditee_remarks: {
                required: "Remarks are required",
                minlength: "Remarks must be at least 10 characters long",
                noSpecialChars: "Enter the valid Remarks"
            },
        },
        errorPlacement: function(error, element) {
            // For datepicker fields inside input-group, place error below the input group
            if (element.hasClass('datepicker')) {
                // Insert the error message after the input-group, so it appears below the input and icon
                error.insertAfter(element.closest('.input-group'));
            } 
           else if (element.attr("type") === "radio") {
                // Insert error message after the entire .col-md-12 container of radio buttons
                error.insertAfter(element.closest('.col-md-12'));
            } 
           else {
                // For other elements, insert the error after the element itself
                error.insertAfter(element);
            }
       
        },
        invalidHandler: function(event, validator) {
            scrollToFirstError();
        }
    });



    const $callforrecordsForm = $("#callforrecords");

    // Scroll to the first error field (for better UX)
    function scrollToFirstError() {
        const firstError = $callforrecordsForm.find('.error:first');
        if (firstError.length) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
        }
    }
    // Event listener for file validation when 'Yes' is selected

    $(document).on("change", "input[type='radio'][name$='-radio']", function() {
        let accountId = $(this).attr("name").split("-")[0];
        $(`#${accountId}-attachment input[type="file"]`).valid();
    });


    function toggleAttachment(accountId, isRequired) {
        let fileInputContainer = $(`#${accountId}-attachment`);

        let fileInput = $(`#${accountId}-attachment input[type="file"]`);
        if (isRequired) {
            fileInputContainer.show();

            fileInput.rules("add", {
                // required: true,
                fileRequiredIfYes: true,
                fileSizeLimit: true,
                validFileType: true,
                messages: {
                    // required: "File is required ",
                    fileSizeLimit: "File size must be less than 1MB",
                    validFileType: "Allowed file types: PNG, JPEG, PDF, Excel",
                }
            });
        } else {
            fileInputContainer.hide();
            fileInput.val(""); 

            fileInput.rules("remove", "required fileRequiredIfYes fileSizeLimit validFileType");
        }
        fileInput.valid();
    }

    function preventPasteSpecialChars(event) {
        setTimeout(() => {
            let inputVal = $(this).val();
            let sanitizedVal = inputVal.replace(/["'|\#^`~]/g, ""); // Remove special characters
            if (inputVal !== sanitizedVal) {
                alert("Pasting special characters is not allowed!");
                $(this).val(sanitizedVal); // Set the sanitized value back
            }
        }, 100); // Timeout to ensure paste operation is complete
    }

    function restrictSpecialChars(selector) {

        $(selector)
            .on("keypress", function(event) {
                let char = String.fromCharCode(event.which);
                if (/["'|\#^`~]/.test(char)) {
                    event.preventDefault(); // Block special characters on keypress
                }
            })
            .on("paste", preventPasteSpecialChars); // Block special characters on paste
    }

    // Apply restrictions to specific input fields
    restrictSpecialChars("#nodalname, #nodaldesignation, #auditee_remarks");


    function acceptstatus(auditscheduleid) {

        $.ajax({
            url: 'audit/auditee_acceptdetails', // The route to call your controller method
            method: 'POST',
            data: {
                auditscheduleid: auditscheduleid // Passing the auditplanid from the button's id
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {

                populateTableFetch(response)

            },
            error: function(xhr, status, error) {
                // Handle error
                console.log("AJAX error: " + error);
            }
        });
    }

    function fetch_audit_particulars_detail() {

        var catcode = $('#h_catcode').val();
        var deptcode = $('#h_deptcode').val();
        var scheduleid = $('#auditscheduleid').val();

        $.ajax({
            url: 'audit/audit_particulars', // Replace with your endpoint
            method: 'GET',
            data: {
                catcode: catcode, // Pass the 'catcode' to the controller
                deptcode: deptcode, // Pass the 'catcode' to the controller
                scheduleid: scheduleid
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // Pass CSRF token in headers
            },
            success: function(response) {
                if ((response.data && response.data.length > 0) &&
                    (response.account_particulars && response.account_particulars.length > 0)) {

                    populateTable(response);
                    //  $(".validation-wizard").steps("next");

                } else {
                    passing_alert_value('Confirmation', 'No Data Found',
                        'confirmation_alert', 'alert_header',
                        'alert_body', 'confirmation_alert');
                    // $(".validation-wizard").steps("previous");

                }

            },
            error: function(xhr, status, error) {
                var response = JSON.parse(xhr.responseText);
                if (response.error == 401) {
                    handleUnauthorizedError();
                } else {

                    getLabels_jsonlayout([{
                        id: response.message,
                        key: response.message
                    }], 'N').then((text) => {
                        let alertMessage = Object.values(text)[0] ||
                            "Error Occured";
                        passing_alert_value('Confirmation', alertMessage,
                            'confirmation_alert', 'alert_header',
                            'alert_body', 'confirmation_alert');
                    });
                }
            }
        });
    }

    function populateTable(response) {
        $('#part_details').show();
        const tableBody = $('#part_details'); // Select the table's tbody
        tableBody.empty(); // Clear existing rows


        const accountParticulars = response.account_particulars.reduce((acc, item) => {
            if (!acc[item.accountparticularsename]) {
                acc[item.accountparticularsename] = [];
            }
            acc[item.accountparticularsename].push(item);
            return acc;
        }, {});

        // Calculate the total number of rows in the table
        //const totalRows = Object.values(groupedData).reduce((sum, group) => sum + group.length, 0);
        const accountTotalRows = Object.values(accountParticulars).reduce((sum, group) => sum + group.length, 0);

        // Start the table with "Call for Records" and "Account Particulars" as row headers
        let tableHTML = `<tr>
                    <th rowspan="${accountTotalRows + 2}" class="lang availaccount" key="account_particulars_label">Availability of  Account Particulars</th>
                </tr>
                <tr>
                    <th class="callforrecords_th lang" key="type">Type</th>
                    <th class="callforrecords_th ressts lang" key="avail_of_records">Availability Of Records</th>
                    <th class="callforrecords_th lang">
                        <div>
                            <label class="form-label required lang" key="file_upload" for="validationDefault01">File Upload&nbsp;&nbsp;<Label>
                            <span style="color:red;font-weight:300;">(&nbsp;&nbsp;File size must not exceed 1 MB&nbsp;&nbsp;)</span>
                        </div>
                    </th>
                    <th class="callforrecords_th">
                        <div>
                            <label class="form-label required lang" key="remarks" >Remarks</label>
                        </div>
                    </th>
                </tr>`;
        var lang = getLanguage('Y');


        // Iterate over account particulars and create rows
        for (const [accountParticularsName, accountParts] of Object.entries(accountParticulars)) {
            const accountRowSpan = accountParts.length; // Number of subcategories under the account category

            accountParts.forEach((accountParticular, index) => {

                if (lang == 'ta') {
                    var accountparticularsname = accountParticular.accountparticularstname;
                } else {
                    var accountparticularsname = accountParticular.accountparticularsename;
                }
                tableHTML += `
                                <tr>

                                <td class="lang">${accountparticularsname}
                                <input type="hidden" id="${accountParticular.accountparticularsid}-accountcode" name="${accountParticular.accountparticularsid}-accountcode" value="${accountParticular.accountparticularsid}"></td> <!-- Account Item -->
                                <td>
                                <div class="col-md-12">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="${accountParticular.accountparticularsid}-radio"
                                            id="${accountParticular.accountparticularsid}-radio" value="Y"
                                            onclick="toggleAttachment('${accountParticular.accountparticularsid}', true)" />
                                        <label class="form-check-label lang"  key="avail_label" for="account-${accountParticular.accountparticularsid}-yes">Available</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="${accountParticular.accountparticularsid}-radio"
                                            id="${accountParticular.accountparticularsid}-radio" value="N"
                                            onclick="toggleAttachment('${accountParticular.accountparticularsid}', false)" />
                                        <label class="form-check-label lang"  key="notavail_label" for="${accountParticular.accountparticularsid}-no">Not Available</label>
                                    </div>
                                </div>
                                </td>
                                    <td>
                                        <div id="${accountParticular.accountparticularsid}-attachment" name="${accountParticular.accountparticularsid}-file " style="padding:10px;">

                                            <input type="file" data-placeholder-key="remarks_ph" class="form-control"
                                                id="${accountParticular.accountparticularsid}-attachment" name="${accountParticular.accountparticularsid}-accountfile">

                                        </div>

                                    </td>

                                <td style="padding:10px;">
                                <textarea id="account-${accountParticular.accountparticularsid}" data-placeholder-key="remarks_ph" name="${accountParticular.accountparticularsid}-accountvalues" class="form-control" placeholder="Enter remarks" style="height: 20px;"></textarea>
                                </td>
                                </tr>

                                `;
            });
        }

        // Call for Records Section
        tableHTML += `<tr style="height:30px;"></tr><tr>
                 <th rowspan="${response.data.length+1}" class="lang" key="callforrecords_label">Call For Records</th>

                 <th colspan="2" class="callforrecords_th lang" key="type">Type</th>
                 <th class="callforrecords_th ressts lang" key="avail_of_records">Availability Of Records</th>
                 <th class="callforrecords_th lang" key="remarks">Remarks</th>
              </tr>`;

        $.each(response.data, function(index, record) {
            // Determine the value to display based on the language
            var callForRecordsName = (lang === 'en') ?
                record.callforrecordsename :
                record.callforrecordstname;

            tableHTML += `
                        <tr>
                            <td colspan="2" >${callForRecordsName}</td>
                            <td>
                                <input type="hidden" id="${record.callforrecordsid}-cfrcode" name="${record.callforrecordsid}-cfrcode" value="${record.callforrecordsid}">

                                <div class="col-md-12">
                                    <div class="form-check form-check-inline">
                                        <input  checked class="form-check-input" type="radio" name="${record.callforrecordsid}-cfrradio"
                                            id="${record.callforrecordsid}-cfrradio" value="Y"
                                        />
                                        <label class="form-check-label lang"  key="avail_label" for="${record.callforrecordsid}-cfrradio">Available</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="${record.callforrecordsid}-cfrradio"
                                            id="${record.callforrecordsid}-cfrradio" value="N"
                                        />
                                        <label class="form-check-label lang"  key="notavail_label" for="${record.callforrecordsid}-radio-no">Not Available</label>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:10px;">
                                <textarea id="${record.callforrecordsid}" data-placeholder-key="remarks_ph"
                                    name="${record.callforrecordsid}-cfrvalues"
                                    class="form-control"
                                    placeholder="Enter remarks"
                                    style="height: 20px;"></textarea>
                            </td>
                        </tr>`;
        });




        // Add Nodal Person and Remarks
        tableHTML += `
<tr style="height:30px;"></tr><tr>
<th class="lang" key="nodal_person">Nodal Person</th>
<td colspan="4">
 <div class="row">
    <div class="col-md-6">
       <label class="form-label required lang"  key="name" for="nodal_name">Name</label>
       <input type="text" class="form-control name"  maxlength='50' id="nodalname" name="nodalname"  data-placeholder-key="username"  placeholder="Enter Name" value="${audit_schedule[0]?.nodalperson_ename ? audit_schedule[0].nodalperson_ename : ""}" />
    </div>
    <div class="col-md-6">
       <label class="form-label required lang"  key="mobile" for="mobile">Mobile Number</label>
       <input type="text" class= "form-control only_numbers"  maxlength='10' data-placeholder-key="mobile"   id="nodalmobile" name="nodalmobile" value="${audit_schedule[0]?.mobile  ? audit_schedule[0].mobile : ""}"  placeholder="Enter Mobile Number" maxlength = 10  />
    </div>
</div><br>
<div class="row">
    <div class="col-md-6">
        <label class="form-label required lang"  key="Email" for="mobile">Email</label>
        <input type="text" class="form-control"  maxlength='50' id="nodalemail"  data-placeholder-key="email"  name="nodalemail" placeholder="Enter Email" value="${audit_schedule[0]?.email  ? audit_schedule[0].email : ""}"  />
    </div>
    <div class="col-md-6">
        <label class="form-label required lang"  key="designation" for="mobile">Designation</label>
        <input type="text" class="form-control alpha_numeric"  maxlength='50' id="nodaldesignation" data-placeholder-key="designation_ph"  name="nodaldesignation"value="${audit_schedule[0]?.nodalperson_desigcode ? audit_schedule[0].nodalperson_desigcode : ""}"   placeholder="Enter Designation"  />
    </div>
</div>
<br>
</td>
</tr>
<tr>
<th class="lang" key="remarks">Remarks</th>
<td colspan="4">
<div class="col-md-12">
    <label class="form-label required lang" key="remarks" for="remarks">Remarks</label>
    <textarea id="auditee_remarks"  maxlength='200' data-placeholder-key="remarks_ph" name="auditee_remarks"  class="form-control" placeholder="Enter remarks" style="height: 20px;"></textarea>
    <div id="error-message" style="font-size: 12px; color: red; display: none;">Word limit reached! Maximum 250 words allowed.</div>
</div><br>
</td>
</tr>
`;

        // Append the generated table HTML to the table body
        tableBody.html(tableHTML);
        translate();
        updatePlaceholders(lang);

        $(document).on("keypress", ".only_numbers", function(event) {
            if (event.charCode >= 48 && event.charCode <= 57)
                return true;
            else return false;
        });

        $(".name").on("keypress", function(event) {
            if (
                (event.charCode > 64 && event.charCode < 91) ||
                (event.charCode > 96 && event.charCode < 123) ||
                event.charCode == 32
            )
                return true;
            else return false;
        });

        // Allow Alphabets and Numbers
        $(".alpha_numeric").on("keypress", function(event) {
            if (
                (event.charCode > 64 && event.charCode < 91) ||
                (event.charCode > 96 && event.charCode < 123) ||
                (event.charCode >= 48 && event.charCode <= 57) ||
                event.charCode == 32
            )
                return true; // let it happen, don't do anything
            else return false;
        });
        restrictSpecialChars("#nodalname, #nodaldesignation, #auditee_remarks");



    }

    function populateTableFetch(response) {
        $('#part_details').hide();

        const tableBody = $('#part_details_fetch'); // Select the table's tbody
        tableBody.empty(); // Clear existing rows

        const data = response.data;
        const cfr = response.cfr;


        // Grouping data for Account Particulars
        const accountParticulars = data.reduce((acc, item) => {
            if (!acc[item.accountparticularsename]) {
                acc[item.accountparticularsename] = [];
            }
            acc[item.accountparticularsename].push(item);
            return acc;
        }, {});

        // Grouping data for Call for Records
        /*const callForRecords = cfr.reduce((acc, item) => {
            if (!acc[item.majorworkallocationtypeename]) {
                acc[item.majorworkallocationtypeename] = [];
            }
            acc[item.majorworkallocationtypeename].push(item);
            return acc;
        }, {});
        const totalRows = Object.values(callForRecords).reduce((sum, group) => sum + group.length, 0);*/
        const accountTotalRows = Object.values(accountParticulars).reduce((sum, group) => sum + group.length, 0);
        // Start building the table HTML
        let tableHTML = '';
        tableHTML += `<tr>
                            <th rowspan="${accountTotalRows + 2}" class="lang" key="account_particulars_label">Account Particulars</th>
                        </tr>
                        <tr>
                            <th class="callforrecords_th lang" key="type">Type</th>
                            <th class="callforrecords_th ressts lang" key="avail_of_records">Availability Of Records</th>
                            <th class="callforrecords_th">
                                <div>
                                    <label class="form-label lang" for="validationDefault01" key="file_upload">File Upload&nbsp;&nbsp;<Label>
                                </div>
                            </th>
                            <th class="callforrecords_th">
                                <div>
                                    <label class="form-label lang" key="remarks">Remarks</label>
                                </div>
                            </th>
                        </tr>`;

        // Account Particulars Section
        tableHTML += `
                `;

        //var lang=$('#translate').val();
        var lang = getLanguage('Y');

        for (const [particularName, particulars] of Object.entries(accountParticulars)) {
            particulars.forEach((particular) => {
                const isFileUploaded = particular.fileuploadid !== 0;
                const fileDetailsString = particular.filedetails;
                const fileDetailsArray = fileDetailsString.split(
                    ',');

                const fileCardsHTML = fileDetailsArray.map((fileDetail, index) => {
                    const [name, path, size, fileuploadid] = fileDetail.split('-'); // Split by hyphen

                    const file = {
                        id: index + 1, // Use index+1 as unique ID for the file
                        name: name,
                        path: path,
                        size: size,
                        fileuploadid: fileuploadid,
                    };
		const encodedPath = encodeURIComponent(file.path);
                    const fileUrl = `/download-file?filepath=${encodedPath}`;

                    const extension = file.name.split('.').pop().toLowerCase();

                    return isFileUploaded ?
                        `<div class="card overflow-hidden" id="file-card-${file.id}">
                    <input type="hidden" id="fileuploadid_${file.id}" name="fileuploadid_${file.id}" value="${file.fileuploadid}">
                    <div class="d-flex flex-row">
                        <div class="align-items-center">
                            <h3 class="text-danger box mb-0 round-56 p-2">
                                <i class="ti ti-file-text"></i>
                            </h3>
                        </div>
                        <div class="p-3">
                            <h3 class="text-dark mb-0 fs-3">
                                <a style="color:black" href="javascript:void(0);"  onclick="downloadAndPreview('${fileUrl}')">${file.name}</a>
                            </h3>

                        </div>
                    </div>
                </div>` :
                        `<div class=""></div>`;
                }).join('');

                if (lang == 'ta') {
                    var accountparticularsname = particular.accountparticularstname;
                } else {
                    var accountparticularsname = particular.accountparticularsename;
                }
                tableHTML += `
            <tr>
                <td>${accountparticularsname}
                    <input type="hidden" id="${particular.accountparticularsid}-cfrcode" name="${particular.accountparticularsid}-cfrcode" value="${particular.accountparticularsid}">
                </td>
                <td>${isFileUploaded ? '<span class="lang" key="avail_label"></span>' : '<span class="lang" key="notavail_label"></span>'}</td>


                  <td>${fileCardsHTML}</td>
                <td>
                    <textarea id="${particular.accountparticularsid}" name="${particular.accountparticularsid}-cfrvalues" class="form-control" data-placeholder-key="remarks_ph" placeholder="Enter remarks" disabled style="height: 20px;">${particular.remarks || ''}</textarea>
                </td>
            </tr>`;
            });
        }


        // Call for Records Section
        tableHTML += `<tr style="height:30px;"></tr><tr>
                             <th rowspan="${cfr.length+ 2}" class="lang" key="callforrecords_label">Call For Records</th>
                          </tr>
                          <tr>
                             <th colspan="2" class="callforrecords_th lang" key="type">Type</th>
                             <th class="callforrecords_th ressts lang" key="avail_of_records">Availability Of Records </th>
                             <th class="callforrecords_th lang" key="remarks">Remarks</th>
                          </tr>`;

        $.each(cfr, function(index, record) {
            // Determine the value to display based on the language
            var callForRecordsName = (lang === 'en') ?
                record.callforrecordsename :
                record.callforrecordstname;

            const isReplyPending = record.replystatus !== 'Y';

            tableHTML += `
                        <tr>
                            <td colspan="2" >${callForRecordsName}</td>
                            <td>
                               ${isReplyPending ? '<span class="lang" key="notavail_label"></span>' : '<span class="lang" key="avail_label"></span>'}
                            </td>
                            <td style="padding:10px;">
                                <textarea id="${record.callforrecordsid}" name="${record.callforrecordsid}-cfrvalues" class="form-control" data-placeholder-key="remarks_ph" placeholder="Enter remarks" disabled style="height: 20px;">${record.cfr_remarks || ''}</textarea>

                            </td>
                        </tr>`;
        });



        // Nodal Person Section
        tableHTML += `
        <tr style="height:30px;"></tr><tr>
        <th class="lang" key="nodal_person">Nodal Person</th>
        <td colspan="4">
            <div class="row">
                <div class="col-md-6">
                   <label class="form-label lang" key="name" for="nodal_name">Name</label>
                   <input type="text" class="form-control " id="nodalname" data-placeholder-key="username" name="nodalname" value="${data[0].nodalname || ''}" disabled placeholder="Enter Name"  />
                </div>
                <div class="col-md-6">
                   <label class="form-label lang" key="mobile" for="mobile">Mobile Number</label>
                   <input type="text" class= "form-control only_numbers" data-placeholder-key="mobile" id="nodalmobile" value="${data[0].nodalmobile || ''}" disabled name="nodalmobile" placeholder="Enter Mobile Number" maxlength = 10 />
                </div>
            </div><br>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label lang" key="Email"  for="mobile">Email</label>
                    <input type="text" class="form-control" id="nodalemail"  data-placeholder-key="email" value="${data[0].nodalemail || ''}" disabled name="nodalemail" placeholder="Enter Email"  />
                </div>
                <div class="col-md-6">
                    <label class="form-label lang" key="designation"  for="mobile">Designation</label>
                    <input type="text" class="form-control" id="nodaldesignation" data-placeholder-key="designation_ph" value="${data[0].nodaldesignation || ''}" disabled  name="nodaldesignation" placeholder="Enter Designation"  />
                </div>
            </div><br>
        </td>
    </tr>
    <tr>
        <th class="lang" key="remarks">Remarks</th>
        <td colspan="4">
            <label class="form-label lang" key="remarks" for="auditee_remarks">Remarks</label>
            <textarea id="auditee_remarks" name="auditee_remarks" data-placeholder-key="remarks_ph" class="form-control" disabled style="height: 20px;">${data[0].auditeeremarks || ''}</textarea><br>
        </td>
    </tr>`;

        // Append the HTML to the table body
        tableBody.append(tableHTML);
        translate();
        $('#details_tabletab_buttons').hide();
        updatePlaceholders(lang);

    }

    $(document).on('change', '#translate', function() {
        var lang = getLanguage('Y');
        updateValidationMessages(getLanguage('Y'), 'callforrecords');







        fetchalldata(lang);


    });

    $(document).ready(function() {
        var lang = getLanguage('')
        fetchalldata(lang);




    });
</script>

@endsection