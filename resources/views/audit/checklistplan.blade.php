@extends('index2')
@section('content')
@include('common.alert')
@section('title', 'Check list for Planning')
<style>
    .card-body {
        padding: 15px 10px;
    }

    .card {
        margin-bottom: 10px;
    }

    .userCountTable thead tr th,
    .instTable thead tr th {
        background-color: #707070 !important;
        /* Darker shade for header */
        color: #fff !important;
        /* Ensure text remains white */
        border: 1px solid #5e5c5c !important;
    }

    .userCountTable tbody tr td,
    .instTable tbody tr td {
        border: 1px solid #5e5c5c !important;
    }

    .hide-finish {
        display: none !important;

    }

    .alert_container {
        text-align: center;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        max-width: 400px;
    }

    h1 {
        color: #dc3545;
    }

    p {
        font-size: 18px;
    }
</style>
@php
$sessioncharge = session('charge');
$sessiondeptcode = $sessioncharge->deptcode;
$sessiondistcode = $sessioncharge->distcode;
@endphp
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<form id="user_detailform" name=user_detailform></form>
<div class="col-12">
    <div id="overlay-loader" style="
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.5) url('loader.gif') center center no-repeat;
    z-index: 9999;">
        <span class="ti ti-ban"><span>
    </div>
    <div id="pendinginst"></div>
    <div class="justify-content-center hide_this" id="assignteambtn_div">
        <div class="card card_border  ">
            <div class="card-header card_header_color lang" key="">Checklist </div>
            <div class="col-md-4 mx-auto p-3">
                <button type="button" id="conduct_checklist" key=""
                    class="justify-content-center w-100 btn mb-1 btn-rounded btn-success d-flex align-items-center lang">
                    Conduct Checklist
                </button>
            </div>
        </div>
    </div>
    <div class="col-md-6 justify-content-center hide_this" id="alert_div">
        <div class="card justify-content-center">

            <div class="col-md-8 mx-auto p-3">
                <div class="justify-content-center align-items-center">
                    <h1>Alert</h1>

                    <p>Please Complete the pending task</p>
                </div>



            </div>
        </div>
    </div>




    <div class="hide_this" id="data_teamassigned_div">
        <div class="card card_border">
            <div class="  mt-1 mb-2 p-2" id="count_det">
            </div>
            <div class="card-body" id="desig_countDetails">
                <div class=" table-responsive rounded-2 border ">
                    <table class="table userCountTable">
                        <thead class="">
                            <tr>
                                <th class="lang userCount_head" key="s_no">S No</th>
                                <th class="lang userCount_head" key="designation">Designation Name
                                </th>
                                <th class="lang userCount_head" key="count">Count</th>
                            </tr>
                        </thead>
                        <tbody id="designationTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
            <hr>
            <div class="card-body">
                <div id="example-basic" class="mt-1 ">
                    <h3 class="lang" key="">Team Compositions</h3>
                    <section class="mb-2">
                        <div class="card card-border mb-2" style="max-height: 300px; overflow-y: auto;width:98%;margin:0 auto;">
                            <div class="card-header card_header_color lang" key=""> Team Compositions
                            </div>
                            <div class="card-body">
                                <div class="datatables">
                                    <div class="table-responsive " id="tableshow_teamdetails">
                                        <table id="auditteamdetailstable"
                                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                            <thead>
                                                <tr>
                                                    <th class="lang" key="s_no">S.No</th>
                                                    <th class="lang" key="team">Team Name</th>
                                                    <th class="lang" key="teamhead">Team Head</th>
                                                    <th class="lang" key="">Team Members</th>
                                                    <th class="lang" key="">Team Size</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Dynamically populated rows will go here -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="no_data_teamdetails" class="hide_this">
                                        <center>No Data Available</center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <h3 class="lang" key="">Allocation Details</h3>
                    <section class="mb-2">
                        <div class="card card-border mb-2" style="max-height: 300px; overflow-y: auto;width:98%;margin:0 auto;">
                            <div class="card-header card_header_color lang" key=""> Allocation Details
                            </div>
                            <div class="card-body">
                                <div class="datatables">
                                    <div class="table-responsive " id="tableshow_allocation">
                                        <table id="auditteamtable"
                                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                            <thead>
                                                <tr>
                                                    <th class="lang" key="s_no">S.No</th>
                                                    <th class="lang" key="inst">Institution</th>
                                                    <th class="lang" key="team">Team Name</th>
                                                    <!-- <th class="lang" key="teamhead">Team Head</th>
                                    <th class="lang" key="">Team Members</th> -->
                                                    <th class="lang" key="">Assigned Date</th>
                                                    <th class="lang" key="">Team Size</th>
                                                    <th class="lang" key="">Mandays</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Dynamically populated rows will go here -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="no_data_allocation" class="hide_this">
                                        <center>No Data Available</center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <h3 class="lang" key="">Idle Auditors Details</h3>
                    <section>
                        <div class="card card-border" style="max-height: 300px; overflow-y: auto;width:98%;margin:0 auto;">
                            <div class="card-header card_header_color lang" key="">Idle Auditors Details
                            </div>
                            <div class="card-body">
                                <div class="datatables">
                                    <div class="table-responsive " id="tableshow_idleauditors">
                                        <table id="idleauditorstable"
                                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                            <thead>
                                                <tr>
                                                    <th class="lang" key="s_no">S.No</th>
                                                    <th class="lang" key="">User Name
                                                    <th class="lang" key="">Engagement Period</th>
                                                    <th class="lang" key="">Status</th>
                                                    <th class="lang" key="">Total Audit Days</th>
                                                    <th class="lang" key="">Alloted Days</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Dynamically populated rows will go here -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="no_data_idleauditors" class="hide_this">
                                        <center>No Data Available</center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <h3 class="lang" key="">Idle Institution Details</h3>
                    <section>
                        <div class="card card-border" style="max-height: 300px; overflow-y: auto;width:98%;margin:0 auto;">
                            <div class="card-header card_header_color lang" key="">Idle Institution Details
                            </div>
                            <div class="card-body">
                                <div class="datatables">
                                    <div class="table-responsive " id="tableshow_idleinstitution">
                                        <table id="idleinsttable"
                                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                            <thead>
                                                <tr>
                                                    <th class="lang" key="s_no">S.No</th>
                                                    <th class="lang" key="inst">Institution
                                                    <th class="lang" key="Mandays">Mandays</th>
                                                    <th class="lang" key="">Carry Forward</th>
                                                    <th class="lang" key="teamsize">Teamsize</th>
                                                    <th class="lang" key="">Rank Order</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Dynamically populated rows will go here -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="no_data_idleinstitution" class="hide_this">
                                        <center>No Data Available</center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <div class="row justify-content-center text-center" id="buttonset">
            <div class="col-md-6 ">
                <button class="btn btn-primary mt-3 lang" key="" type="button"
                    id="redo_checklist" name="redo_checklist"> Redo
                </button>
                <button class="btn btn-success mt-3 lang" key="" type="button"
                   id="finalise_btn" name="finalise_btn">Finalise
                </button>
            </div>
        </div>
    </div>
    <div class="hide_this" id="finalised_plandiv">
        <div class="col-12">
            <div class="card card_border mt-2  finalised_plandiv" id="finalised_plandiv">
                <div class="card-header card_header_color lang" key="autoplan_head">Audit Team and Institiute Details
                </div>
                <div class="card-body">
                    <div class="datatables">
                        <div class="table-responsive " id="tableshow">
                            <table id="team_Inst_Details"
                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                <thead>
                                    <th class="lang text-center align-middle" key="s_no">S.No</th>
                                    <th class="lang " key="inst_name">Institute Name </th>
                                    <th class="lang " key="teamhead_label">Team Head</th>
                                    <th class="lang " key="teammember_label">Total Members</th>
                                    <th class="lang " key="teamsize">Total Size</th>
                                    <th class="lang " key="">Spill over</th>
                                    <th class="lang " key="mandays">Total Mandays</th>
                                    <th class="lang " key="">Date</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id='no_data' class='hide_this'>
                        <center>No Data Available</center>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/libs/jquery-steps/build/jquery.steps.min.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="../assets/js/forms/form-wizard.js"></script>
    <script src="../assets/js/jquery_3.7.1.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../assets/js/download-button/buttons.min.js"></script>
    <script src="../assets/js/download-button/jszip.min.js"></script>
    <script src="../assets/js/download-button/buttons.print.min.js"></script>
    <script src="../assets/js/download-button/buttons.html5.min.js"></script>
    <script src="../assets/js/download-button/custom.xl.min.js"></script>
    <script src="../assets/js/datatable/datatable-advanced.init.js"></script>
    <script>
        ////////////////////global variable////////////
        let totalteamDetails = '';
        let teamDetails = '';
        let idleAuditorsDetail = '';
        let idleInstitutionDetails = '';
        let totalInstitutionDetails = '';
        let totalAuditorDetails = '';
        let auditors_det = '';
        let executingquartercode = '';

        $(document).ready(function() {
            $('.actions a[href="#finish"]').closest('li').addClass('hide-finish');
            checkisteamassigned();

        });

        function updateTableLanguage(lang) {
            if ($.fn.DataTable.isDataTable('#team_Inst_Details')) {
                $('#team_Inst_Details').DataTable().clear().destroy();
            }

            fetchAuditorsTable(lang);

        }


        $("#translate").change(function() {
            const lang = getLanguage('Y');
            updateTableLanguage(lang);
            // updateValidationMessages(getLanguage('Y'), 'audit_autoplan');


        });

        /////////////////////////////////////check-teamassigned/////////////////////////

        function checkisteamassigned() {

            var deptcode = '<?php echo $sessiondeptcode; ?>';
            var distcode = '<?php echo $sessiondistcode; ?>';

            const language = getLanguage();

            $.ajax({
                url: "checklistplan/checkisteamassigned", // Your API route to get user details
                method: 'POST',
                data: {
                    deptcode: deptcode,
                    distcode: distcode
                }, // Pass deptuserid in the data object
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {
                    if (response.success) {
                        if (response.planstatus == 'F') {
                            $('#data_teamassigned_div').hide()
                            $('#assignteambtn_div').hide()

                            $('#finalised_plandiv').show();
                            executingquartercode = response.executingquartercode;
                            auditors_det = response.planned_auditors;
                            fetchAuditorsTable(language)

                        } else {
                            //      if(response.pendinginststatus==''||response.pendinginststatus=='N'||response.pendinginststatus=='null')
                            //    {

                            //    }

                            if (response.teamassignedstatus) {
                                $('#data_teamassigned_div').show()
                                $('#assignteambtn_div').hide()
                                $('#finalised_plandiv').hide();

                                if (response.inst_det.length > 0) {
                                    totalInstitutionDetails = response.inst_det;
                                } else {

                                }

                                if (response.users.length > 0) {
                                    totalAuditorDetails = response.users;
                                } else {

                                }
                                if (response.teamdet.length > 0) {

                                    $('#tableshow_teamdetails').show();
                                    $('#no_data_teamdetails').hide();
                                    totalteamDetails = response.teamdet;

                                    renderTotalTeamdetails(language);

                                } else {
                                    $('#tableshow_teamdetails').hide();
                                    // $('#auditteamtable_wrapper').hide();
                                    $('#no_data_teamdetails').show();
                                }

                                if (response.totalteamdetails.length > 0) {

                                    $('#tableshow_allocation').show();
                                    $('#no_data_allocation').hide();
                                    teamDetails = response.totalteamdetails;
                                    renderTeamdetails(language);

                                } else {
                                    $('#tableshow_allocation').hide();
                                    // $('#auditteamtable_wrapper').hide();
                                    $('#no_data_allocation').show();
                                }

                                if (response.idelusers.length > 0) {

                                    $('#tableshow_idleauditors').show();
                                    $('#no_data_idleauditors').hide();
                                    idleAuditorsDetail = response.idelusers;
                                    renderIdleusersdetails(language);

                                } else {
                                    $('#tableshow_idleauditors').hide();
                                    // $('#auditteamtable_wrapper').hide();
                                    $('#no_data_idleauditors').show();
                                }

                                if (!response.idleinst || response.idleinst.length === 0) {
                                    // If idelusers is null, undefined, or an empty array

                                    $('#tableshow_idleinstitution').hide();
                                    $('#no_data_idleinstitution').show();
                                } else {
                                    // If idelusers is not null or empty
                                    $('#tableshow_idleinstitution').show();
                                    $('#no_data_idleinstitution').hide();
                                    idleInstitutionDetails = response.idleinst;
                                    renderIdleInstdetails(language);
                                }



                                populateCountdetails(response);


                            } else {
                                if (response.pendinginststatus == 'Y') {
                                    $('#assignteambtn_div').show()
                                    $('#data_teamassigned_div').hide()

                                } else {
                                    //  alert()
                                    $('#alert_div').show()

                                }


                            }

                        }
                    } else {
                        alert(response.message);
                    }

                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });

        }

        /////////////////////////////////////////////populate details/////////////////////////////////////////////////////////////////////////////////

        function populateCountdetails(data) {
            $('#count_det').empty();
            const lang = getLanguage();
            const fromdate = ChangeDateFormat(data.quarterfromdate);
            const todate = ChangeDateFormat(data.quartertodate);
            $counthtml = ` 
     
            <div class="row text-center">
        <!-- Department -->
        <div class="col-sm-3 border-end">
                    <div class="form-group">
                        <label class="form-label lang" key="">Department:</label>
                        <p>${data.deptname[0].deptelname}</p>
                    </div>
                </div>
   
                <!-- District -->
                <div class="col-sm-3 border-end">
            <div class="form-group">
                <label class="form-label lang" key="">District:</label>
                <p>${data.distname[0].distename}</p>
            </div>
                </div>
   
        <!-- Total Institute Count -->
        <div class="col-sm-3 border-end">
            <div class="form-group">
                <label class="form-label lang" key="">Total Institute Count:</label>
                        <p onclick="showDetails('inst')" style="cursor:pointer">${data.totalinstcount}</p>
                    </div>
                </div>
   
                <!-- Total Auditors Count -->
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="form-label lang" key="">Total Auditors Count:</label>
                        <p onclick="showDetails('auditors')"  style="cursor:pointer">${data.totalauditorscount}</p>
                    </div>
                </div>
   
           </div>
         <!------------Mandays -----------!>
     <div class="row text-center">
        <!-- Department -->
        <div class="col-sm-3 border-end">
            <div class="form-group">
                <label class="form-label lang" key="">Total Working Days</label>
                <p>${data.totalworkingdays}</p>
            </div>
        </div>
   
        <!-- District -->
        <div class="col-sm-3 border-end">
            <div class="form-group">
                <label class="form-label lang" key="">Sum of Mandays:</label>
                <p>${data.sumofinstmandays}</p>
            </div>
        </div>
        <!-- District -->
        <div class="col-sm-3 border-end">
            <div class="form-group">
                <label class="form-label lang" key="">Need Mandays:</label>
                <p>${data.neededmandays}</p>
            </div>
        </div>
         <div class="col-sm-3 border-end">
            <div class="form-group">
                <label class="form-label lang" key="">Allocated Mandays:</label>
                <p>${data.allocatedmandays}</p>
            </div>
        </div>
   
     
        
   </div>
     <div class="p-2 mt-2"><b>Audit Quarter Period:</b>${fromdate +' to ' + todate}</div>
   `;
            $('#count_det').append($counthtml);

            let designationCounts = data.designationDetails;
            // Get the table body reference
            let tableBody = document.getElementById("designationTableBody");



            // Clear previous content
            tableBody.innerHTML = "";

            // Loop through designationCounts and add rows dynamically
            designationCounts.forEach((desig, index) => {
                let row = document.createElement("tr");
                row.innerHTML = `
                        <td>${index + 1}</td>
                        <td class="lang">${lang=='ta'?desig.desigtlname:desig.desigelname}</td>
                        <td>${desig.count}</td>`;
                tableBody.appendChild(row);
            });


        }

        $(document).on('click', '#redo_checklist', function() {
            event.preventDefault();

            $('#process_button').off('click').on('click', function(event) {
                event.preventDefault();
                $('#confirmation_alert').modal('hide');
                assignteams()
            });

            passing_alert_value('Confirmation', 'Are you sure to re-assign the teams for checking',
                'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');
        });
        ////////////////////////////////////////////////////////////////////////////////////////
        $(document).on('click', '#finalise_btn', function() {
            event.preventDefault();

            passing_alert_value('Confirmation', 'Are you sure to allocate plan?',
                'confirmation_alert',
                'alert_header', 'alert_body',
                'forward_alert');

            $('#confirmation_alert .modal-footer').show();
            //    $("#process_button").html("Send OTP");

            $('#process_button').off('click').on('click', function(event) {
                event.preventDefault();
                $('#confirmation_alert').modal('hide');
                checkexitmeetstatus()
                // finaliseplan()
                // $('#process_button').off('click').on('click', function(event) {

            });


        });

        function checkexitmeetstatus() {
            var deptcode = '<?php echo $sessiondeptcode; ?>';
            var distcode = '<?php echo $sessiondistcode; ?>';
            $.ajax({
                url: 'checklistplan/checkexitmeetstatus', // or use `{{ route('send.otp') }}` if blade
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    deptcode: deptcode,
                    distcode: distcode
                },
                beforeSend: function() {
                    $('#overlay-loader').show();
                },
                success: function(response) {
                    if (response.status === 'success') {


                        passing_alert_value('Confirmation', 'Are you sure to sent OTP for finalize',
                            'confirmation_alert',
                            'alert_header', 'alert_body',
                            'forward_alert');

                        $('#process_button').off('click').on('click', function(event) {
                            event.preventDefault();
                           // finaliseplan()
                            //$('#confirmation_alert').modal('hide');
                             sentfinaliseOTP()
                        });

                        $("#process_button").html("Send OTP");
                        //             const otpcontent = `                        <div id="otp_div">
                        //     <h5 class="text-center mb-3"><b>Verify Your OTP</b></h5>
                        //     <span class="text-center mb-3 d-block">Enter 6-Digit verification code that was sent to your mail</span><br>
                        //     <div class="row justify-content-center">
                        //         <div class="col-auto">
                        //             <div class="d-flex justify-content-center gap-2 mb-3" id="otp-box-wrapper">
                        //                 <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                        //                 <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                        //                 <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                        //                 <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                        //                 <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                        //                 <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                        //             </div>
                        //             <button type="button" id="verify_otp_button" class="btn btn-primary w-100" id="verify_otp_btn">Verify OTP</button>
                        //         </div>
                        //     </div><br>

                        //     <small class="text-center mb-3 d-block">Didn't receive the code? <b  id="resend_otp_link" style="color:#4f73d9;cursor:pointer;">Resend OTP</b></small><br>

                        // </div>`;


                        //             $('#otp_div').css({
                        //                 'text-align': 'center'
                        //             }).show();

                        //             $('#confirmation_alert .modal-footer').hide();
                        //             $('#process_button').html("Verify OTP");

                        //             passing_alert_value('Confirmation', otpcontent,
                        //                 'confirmation_alert',
                        //                 'alert_header', 'alert_body',
                        //                 'forward_alert');
                    }
                },
                complete: function() {
                    $('#overlay-loader').hide(); // Always hide loader

                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);

                    passing_alert_value('Alert', response.message,
                        'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                }
            });

        }
        //------------------------------SMS Function ----------------------------------------//

        function sentfinaliseOTP() {
            $('#process_button').attr('disabled', true);
            $.ajax({
                url: 'checklistplan/sendOtp_allocateplan', // or use `{{ route('send.otp') }}` if blade
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('#overlay-loader').show();
                },
                success: function(response) {
                    if (response.status === 'success') {
                        const otpcontent = `                        <div id="otp_div">
                <h5 class="text-center mb-3"><b>Verify Your OTP</b></h5>
                <span class="text-center mb-3 d-block">Enter 6-Digit verification code that was sent to your mail</span><br>
                <div class="row justify-content-center">
                    <div class="col-auto">
                        <div class="d-flex justify-content-center gap-2 mb-3" id="otp-box-wrapper">
                            <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                            <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                            <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                            <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                            <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                            <input type="text" class="form-control text-center otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
                        </div>
                        <button type="button" id="verify_otp_button" class="btn btn-primary w-100" id="verify_otp_btn">Verify OTP</button>
                    </div>
                </div><br>
              
                <small class="text-center mb-3 d-block">Didn't receive the code? <b  id="resend_otp_link" style="color:#4f73d9;cursor:pointer;">Resend OTP</b></small><br>

            </div>`;


                        $('#otp_div').css({
                            'text-align': 'center'
                        }).show();

                        $('#confirmation_alert .modal-footer').hide();
                        $('#process_button').html("Verify OTP");

                        passing_alert_value('Confirmation', otpcontent,
                            'confirmation_alert',
                            'alert_header', 'alert_body',
                            'forward_alert');
 $('#confirmation_alert').modal('show');
                    }
                },

                complete: function() {
                    $('#overlay-loader').hide(); // Always hide loader
                    $('#process_button').removeAttr('disabled');
                },
                error: function() {
                    passing_alert_value('Alert', 'Failed to send OTP. Please try again.',
                        'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');

                }
            });

        }
        $(document).on('click', '#verify_otp_button', function() {
            let otp = '';
            $('.otp-input').each(function() {
                otp += $(this).val();
            });

            if (otp.length !== 6 || !/^\d{6}$/.test(otp)) {
                alert('Please enter a valid 6-digit OTP.');
                return;
            }
            $('#process_button').attr('disabled', true);

            // alert(otp);

            $.ajax({
                url: 'checklistplan/verifyOtp_allocateplan', // Your route to verify OTP
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    otp: otp
                },
                beforeSend: function() {
                    $('#overlay-loader').show();
                },
                success: function(response) {
                    if (response.status === 'success') {
                        alert('OTP verified successfully!');
                        $("#confirmation_alert").modal("hide");
                        finaliseplan()
                        // You can redirect or close modal here
                    } else {
                        alert(response.message || 'Incorrect OTP.');
                    }
                },
                complete: function() {
                    $('#overlay-loader').hide(); // Always hide loader
                    $('#process_button').removeAttr('disabled');
                },
                error: function(xhr) {
                    let errorMsg = 'OTP verification failed. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                }
            });
        });


        $(document).on('click', '#resend_otp_link', function(e) {
            e.preventDefault();
            $('#confirmation_alert').modal('hide');
            $.ajax({
                url: 'checklistplan/sendOtp_allocateplan', // Replace with your actual controller route
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content') // Ensure CSRF token is present
                },
                success: function(response) {
                    alert('OTP has been resent successfully.');
                },
                error: function(xhr) {
                    alert('Failed to resend OTP. Please try again.');
                }
            });
        });

        $(document).on('input', '.otp-input', function() {
            if (this.value.length === 1) {
                $(this).next('.otp-input').focus();
            }
        });

        $(document).on('keydown', '.otp-input', function(e) {
            // Allow: Backspace, Tab, Arrow keys
            if (['Backspace', 'Tab', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
                // Handle auto-focus on backspace
                if (e.key === 'Backspace' && !this.value) {
                    $(this).prev('.otp-input').focus();
                }
                return;
            }

            // Block non-numeric keys
            if (!/^[0-9]$/.test(e.key)) {
                e.preventDefault();
            }
        });

        ////////////////////////////////////////////////////////////////////
        $(document).on('click', '#conduct_checklist', function() {
            event.preventDefault();
            assignteams()
            // $('#process_button').off('click').on('click', function(event) {
            //     event.preventDefault();
            //     $('#confirmation_alert').modal('hide');
            //     assignteams()
            // });

            // passing_alert_value('Confirmation', 'Are you sure to assign the teams for checking',
            //     'confirmation_alert',
            //     'alert_header', 'alert_body',
            //     'forward_alert');
        });


        function populateInstitutionTable(det, param) {
            const lang = getLanguage()

            const tbody = document.getElementById('designationTableBody');
            if (!tbody) return;

            tbody.innerHTML = ''; // Clear any previous rows
            if (param == 'inst') {
                det.forEach((item, index) => {
                    const row = document.createElement('tr');
                    let carryforwardvalue = '-';
                    let carryforwardText = '-'; // Default to "-"
                    let workingDays = '0';



                    if (item.spillover == 'Y') {
                        workingDays = item.remainingmandays;
                        carryforwardvalue = 'Y';
                    } else {
                        workingDays = item.mandays;
                        carryforwardvalue = 'N';
                    }

                    if (carryforwardvalue === 'Y') {
                        carryforwardText_en = 'Yes';
                        carryforwardText_ta = '???'
                    } else if (carryforwardvalue === 'N') {
                        carryforwardText_en = 'No';
                        carryforwardText_ta = '?????';
                    }

                    row.innerHTML = `
                       <td><h6>${index + 1}<h6></td>
                       <td><h6>${lang=='ta'?item.insttname:item.instename}</h6></td>
                       <td><h6>${lang=='ta'?item.cattname:	item.catename}<h6></td> <!-- Default to 0 if count missing -->
                       <td><h6>${item.teamsize}<h6></td>
                        <td><h6>${lang=='ta'?carryforwardText_ta:carryforwardText_en}<h6></td>
                         <td><h6>${workingDays}<h6></td>
   
                   `;
                    tbody.appendChild(row);
                });
            } else {
                det.forEach((item, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
             <td><h6>${index + 1}</h6></td>
             <td><h6>${lang=='ta'?item.usertamilname:item.username}</h6></td>
              <td><h6>${lang=='ta'?item.desigtlname:item.desigelname}</h6></td>
         
         `;
                    tbody.appendChild(row);
                });
            }

        }

        function showDetails(param) {
            const lang = getLanguage('');
            var serialno = lang == 'ta' ? '?.???' : 'S.No';
            if (param == 'inst') {
                var instHead = lang == 'ta' ? '??????? ????????' : 'Auditable Institution';
                var catHead = lang == 'ta' ? '???' : 'Category';
                var teamSize = lang == 'ta' ? '???? ????' : 'Team Size';
                var carryForward = lang == 'ta' ? '?????????? ?????? ???????????' : 'Carry Forward';

                var mandays = lang == 'ta' ? '???????' : 'Mandays';

                var datacontent = `<div class=" table-responsive rounded-2 border " style="max-width: 750px;overflow-x: auto;max-height: 600px; overflow-y: auto;width:98%;margin:0 auto;">
                           <table class="table userCountTable">
                               <thead class="">
                                   <tr>
                                       <th class="lang userCount_head" key="s_no">${serialno}</th>
                                       <th class="lang userCount_head" key="audit_office">${instHead}
                                       </th>
                                       <th class="lang userCount_head" key="">${catHead}</th>
                                       <th class="lang userCount_head" key="">${teamSize}</th>
                                        <th class="lang userCount_head" key="">${carryForward}</th>
                                          <th class="lang userCount_head" key="">${mandays}</th>
                                   </tr>
                               </thead>
                               <tbody id="designationTableBody">
                             
                               </tbody>
                           </table>
                       </div>`;
                //      passing_large_alert('Institution Details', datacontent, 'large_confirmation_alert',
                //                'large_alert_header',
                //                'large_alert_body', 'forward_alert');
                //                 setTimeout(() => {
                //        populateInstitutionTable(totalInstitutionDetails,'inst');
                //    }, 100);


                passing_extra_large_alert('Institution Details', datacontent, 'extra_large_confirmation_alert',
                    'extra_large_alert_header',
                    'extra_large_alert_body', 'forward_alert', 'send_intimation_label');
                setTimeout(() => {
                    populateInstitutionTable(totalInstitutionDetails, 'inst');
                }, 100);

            } else if (param = 'auditors') {

                var user = lang == 'ta' ? '?????' : 'Name';
                var desiglname = lang == 'ta' ? '????' : 'Designation';

                var datacontent = `<div class=" table-responsive rounded-2 border " style="max-height: 750px; overflow-y: auto;width:98%;margin:0 auto;">
                           <table class="table userCountTable">
                               <thead class="">
                                   <tr>
                                       <th class="lang userCount_head" key="s_no">${serialno}</th>
                                       <th class="lang userCount_head" key="">${user}
                                       </th>
                                      <th class="lang userCount_head" key="">${desiglname}</th>
                                   </tr>
                               </thead>
                               <tbody id="designationTableBody">
                             
                               </tbody>
                           </table>
                       </div>`;
                passing_large_alert('Auditor Details', datacontent, 'large_confirmation_alert',
                    'large_alert_header',
                    'large_alert_body', 'forward_alert');
                setTimeout(() => {
                    populateInstitutionTable(totalAuditorDetails, 'auditors');
                }, 100);
            }
        }

        function assignteams() {
            var deptcode = '<?php echo $sessiondeptcode; ?>';
            var distcode = '<?php echo $sessiondistcode; ?>';
            $.ajax({
                url: "checklistplan/assignteams", // Your API route to get user details
                method: 'POST',
                data: {
                    deptcode: deptcode,
                    distcode: distcode
                }, // Pass deptuserid in the data object
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {

                    if (response.success) {
                        const val = response.data['loop_until_finished'];
                        if (val === '' || val === null) {


                            passing_alert_value('Alert', 'Team has been assigned successfully',
                                'confirmation_alert',
                                'alert_header', 'alert_body', 'confirmation_alert');

                            checkisteamassigned()
                        } else {
                            alert(response.message);
                        }

                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);

                    passing_alert_value('Alert', response.message,
                        'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                }
            });
        }

        function finaliseplan() {
            var deptcode = '<?php echo $sessiondeptcode; ?>';
            var distcode = '<?php echo $sessiondistcode; ?>';

            const language = getLanguage('');
            $.ajax({
                url: "checklistplan/finaliseplan", // Your API route to get user details
                method: 'POST',
                data: {
                    deptcode: deptcode,
                    distcode: distcode
                }, // Pass deptuserid in the data object
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {

                    if (response.success) {
                        $('#data_teamassigned_div').hide()
                        $('#assignteambtn_div').hide()

                        $('#finalised_plandiv').show();
                        executingquartercode = response.executingquartercode;
                        auditors_det = response.planned_auditors;
                        fetchAuditorsTable(language)
                        //  checkisteamassigned()

                        // const val = response.data['loop_until_finished'];
                        // if (val === '' || val === null) {


                        //     passing_alert_value('Alert', 'Team has been assigned successfully',
                        //         'confirmation_alert',
                        //         'alert_header', 'alert_body', 'confirmation_alert');

                        //     checkisteamassigned()
                        // } else {
                        //     alert(response.message);
                        // }

                    }
                },
                error: function(xhr, status, error) {

                    var response = JSON.parse(xhr.responseText);

                    passing_alert_value('Alert', response.message,
                        'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                }
            });
        }

        /////////////////////////////////////////Render Tables////////////////////////////
        function renderTotalTeamdetails(language) {



            if ($.fn.DataTable.isDataTable('#auditteamdetailstable')) {
                $('#auditteamdetailstable').DataTable().clear().destroy();
            }

            var table = $('#auditteamdetailstable').DataTable({
                "processing": true,
                "serverSide": false,
                "lengthChange": false,
                "data": totalteamDetails,
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return `<div >
                       <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button>${meta.row + 1}
                   </div>`;
                        },
                        className: 'text-wrap text-end',
                        type: "num"
                    },

                    {
                        data: "team_name",
                        title: columnLabels?.["team_name"]?.[language] || 'Team Name',
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.team_name || '-';
                        }
                    },
                    {
                        data: "team_head",
                        title: columnLabels?.["team_head"]?.[language] || 'Team Head',
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.team_head || '-';
                        }
                    },
                    {
                        data: "members",
                        title: columnLabels?.["members"]?.[language] || 'Team Members',
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            if (!row.members) return '-';
                            return row.members
                                .split(',')
                                .map(member => member.trim())
                                .join('<br>');
                        },

                    },

                    {
                        data: "team_size",
                        title: columnLabels?.["team_size"]?.[language] || 'Team Size',
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.team_size || '-';
                        }
                    },





                ],
                "initComplete": function(settings, json) {
                    $("#auditteamdetailstable").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>");
                },

            });
            updatedatatable(language, "auditteamdetailstable");
        }

        function renderTeamdetails(language) {
            const instColumn = language === 'ta' ? 'insttname' : 'instename';


            if ($.fn.DataTable.isDataTable('#auditteamtable')) {
                $('#auditteamtable').DataTable().clear().destroy();
            }

            var table = $('#auditteamtable').DataTable({
                "processing": true,
                "serverSide": false,
                "lengthChange": false,
                "data": teamDetails,
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return `<div >
                       <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button>${meta.row + 1}
                   </div>`;
                        },
                        className: 'text-wrap text-end',
                        type: "num"
                    },
                    {
                        data: instColumn,
                        title: columnLabels?.[instColumn]?.[language] || "Institution",
                        render: function(data, type, row) {
                            let isSpillover;
                            if (row.spillover == 'Y') {
                                isSpillover = true;
                            } else {
                                isSpillover = false;
                            }
                            return `${row[instColumn]}
                       ${isSpillover?'<small style="color:red"> (spill over) </small>':''}`;
                        },
                        className: 'text-wrap text-start'
                    },
                    {
                        data: "team_name",
                        title: columnLabels?.["team_name"]?.[language] || 'Team Name',
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.team_name || '-';
                        }
                    },
                    //    {
                    //        data: "teamhead_ename	",
                    //        title: columnLabels?.["teamhead_ename	"]?.[language] || 'Team Head',
                    //        className: "d-none d-md-table-cell lang extra-column text-wrap",
                    //        render: function(data, type, row) {
                    //            return row.teamhead_ename	 || '-';
                    //        }
                    //    },
                    //    {
                    //        data: "members",
                    //        title: columnLabels?.["members"]?.[language] || 'Team Members',
                    //        className: "d-none d-md-table-cell lang extra-column text-wrap",
                    //        render: function(data, type, row) {
                    //            if (!row.members) return '-';
                    //             return row.members
                    //                 .split(',')
                    //                 .map(member => member.trim())
                    //                 .join('<br>');
                    //        },

                    //    },
                    {
                        data: 'from_date',
                        title: columnLabels?.['from_date']?.[language] || "Proposed Audit Period",
                        render: function(data, type, row) {
                            const isValidDate = (d) => {
                                const date = new Date(d);
                                return d && !isNaN(date);
                            };

                            const fromDate = isValidDate(row.from_date) ? new Date(row.from_date)
                                .toLocaleDateString('en-GB') : "N/A";
                            const toDate = isValidDate(row.to_date) ? new Date(row.to_date)
                                .toLocaleDateString('en-GB') : "N/A";

                            return `${fromDate} - ${toDate}`;
                        },
                        className: "text-start d-none d-md-table-cell extra-column text-wrap"

                    },
                    {
                        data: "team_size",
                        title: columnLabels?.["team_size"]?.[language] || 'Team Size',
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.team_size || '-';
                        }
                    },
                    {
                        data: "mandays",
                        title: columnLabels?.["mandays"]?.[language] || 'Mandays',
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.mandays || '-';
                        }
                    },




                ],
                "initComplete": function(settings, json) {
                    $("#auditteamtable").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>");
                },

            });
            updatedatatable(language, "auditteamtable");
        }

        function exportToExcel(tableId, language) {
            let table = $(`#${tableId}`).DataTable(); // Get DataTable instance

            let titleKey = `${tableId}_title`;
            let translatedTitle = dataTables[language]?.datatable?.[titleKey] || "Default Title";
            let safeSheetName = translatedTitle.substring(0, 31);
            // ? Fetch column headers from JSON layout
            let dtText = dataTables[language]?.datatable || dataTables["en"].datatable;


            const columnMap = {
                institution: language === 'ta' ? 'insttname' : 'instename',
                team: language === 'ta' ? 'team_name' : 'team_name',
                teamhead: language === 'ta' ? 'teamhead' : 'teamhead',
                members: language === 'ta' ? 'members' : 'members'

            };

            let headers = [{
                    header: dtText["audit_office"] || "Auditable Institution",
                    key: "audit_office"
                },
                {
                    header: dtText["teamname"] || "Team Name",
                    key: "teamname"
                },

                {
                    header: dtText["proposed_date"] || "Proposed Date       ",
                    key: "proposed_date"
                },
                {
                    header: dtText["teamsize"] || "Team Size",
                    key: "teamsize"
                },
                {
                    header: dtText["mandays"] || "Mandays",
                    key: "mandays"
                },


            ];

            let rawData = table.rows({
                search: 'applied'
            }).data().toArray();

            let excelData = rawData.map(row => {
                let button = $(row[0]).find("button.toggle-row");
                let dataRow = button.attr("data-row");
                let rowData = dataRow ? JSON.parse(dataRow.replace(/&quot;/g, '"')) : {};


                const fromDateFormatted = convertDateFormatYmd_ddmmyy(rowData.from_date);
                const toDateFormatted = convertDateFormatYmd_ddmmyy(rowData.to_date);

                const proposed_date = (fromDateFormatted && toDateFormatted) ?
                    `${fromDateFormatted} - ${toDateFormatted}` :
                    "-";
                return {
                    audit_office: rowData[columnMap.institution] || "-",
                    teamname: rowData[columnMap.team] || "-",

                    proposed_date: proposed_date || "-",
                    team_size: rowData.team_size || "-",
                    mandays: rowData.mandays || "-",



                };
            });

            if (excelData.length === 0) {
                alert("No data available for export!");
                return;
            }

            // ? Create Workbook and Worksheet
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.json_to_sheet([]);

            // ? Add Headers in Separate Columns (Avoid Merging Issues)
            XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], {
                origin: "A1"
            });

            // ? Ensure Headers Align with Data
            XLSX.utils.sheet_add_json(ws, excelData, {
                skipHeader: true,
                origin: "A2"
            });

            XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
            XLSX.writeFile(wb, `${safeSheetName}.xlsx`);



        }

        function renderIdleusersdetails(language) {
            //  const instColumn = language === 'ta' ? 'insttname' : 'instename';


            if ($.fn.DataTable.isDataTable('#idleauditorstable')) {
                $('#idleauditorstable').DataTable().clear().destroy();
            }

            var table = $('#idleauditorstable').DataTable({
                "processing": true,
                "serverSide": false,
                "lengthChange": false,
                "data": idleAuditorsDetail,
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return `<div >
                       <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button>${meta.row + 1}
                   </div>`;
                        },
                        className: 'text-wrap text-end',
                        type: "num"
                    },

                    {
                        data: "username",
                        title: columnLabels?.["username"]?.[language] || 'User',
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.username || '-';
                        }
                    },
                    {
                        data: "engagement_period",
                        title: columnLabels?.["engagement_period"]?.[language] || 'Engagement Period',
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.engagement_period || '-';
                        }
                    },
                    {
                        data: "status",
                        title: columnLabels?.["status"]?.[language] || 'Status',
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.status || '-';
                        }
                    },
                    {
                        data: "total_audit_days",
                        title: columnLabels?.["total_audit_days"]?.[language] || 'Total Mandays',
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.total_audit_days || '-';
                        }
                    },
                    {
                        data: "allotted_days",
                        title: columnLabels?.["allotted_days"]?.[language] || 'Alloted Days',
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.allotted_days || '-';
                        }
                    },





                ],
                "initComplete": function(settings, json) {
                    $("#idleauditorstable").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>");
                },

            });
        }

        function renderIdleInstdetails(language) {
            const instColumn = language === 'ta' ? 'insttname' : 'instename';


            if ($.fn.DataTable.isDataTable('#idleinsttable')) {
                $('#idleinsttable').DataTable().clear().destroy();
            }

            var table = $('#idleinsttable').DataTable({
                "processing": true,
                "serverSide": false,
                "lengthChange": false,
                "data": idleInstitutionDetails,
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return `<div >
                       <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button>${meta.row + 1}
                   </div>`;
                        },
                        className: 'text-wrap text-end',
                        type: "num"
                    },

                    {
                        data: instColumn,
                        title: columnLabels?.["instColumn"]?.[language] || 'Institution',
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row?.[instColumn] || '-';
                        }
                    },
                    {
                        data: "mandays",
                        title: columnLabels?.["mandays"]?.[language] || 'Mandays',
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.mandays || '-';
                        }
                    },
                    {
                        data: "carryforward",
                        title: columnLabels?.["carryforward"]?.[language] || 'Carry Forward',
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.carryforward || '-';
                        }
                    },
                    {
                        data: "desigcodes",
                        title: columnLabels?.["total_audit_days"]?.[language] || 'Count of Designation',
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.desigcodes || '-';
                        }
                    },
                    {
                        data: "rankorder",
                        title: columnLabels?.["rankorder"]?.[language] || 'Rank Order',
                        className: "d-none d-md-table-cell lang extra-column text-wrap",
                        render: function(data, type, row) {
                            return row.rankorder || '-';
                        }
                    },





                ],
                "initComplete": function(settings, json) {
                    $("#idleinsttable").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>");
                },

            });
        }

        //////////////////////////////finalise plan data
        function fetchAuditorsTable(lang) {



            if ($.fn.DataTable.isDataTable('#team_Inst_Details')) {
                $('#team_Inst_Details').DataTable().clear().destroy();
            }


            // Group auditors by their institution ID and separate team head and members
            let groupedAuditors = auditors_det.reduce((acc, auditor) => {
                if (!acc[auditor.instid]) {
                    acc[auditor.instid] = {
                        instid: auditor.instid,
                        mandays: auditor.mandays,
                       // fdate: ChangeDateFormat(auditor.fromdate) || '-',
                        //tdate: ChangeDateFormat(auditor.todate) || '-',
                        fdate: auditor.fromdate ? ChangeDateFormat(auditor.fromdate) : '-',
                        tdate: auditor.todate ? ChangeDateFormat(auditor.todate) : '-',
                        teamsize: auditor.teamsize,
                        spillover: auditor.spilloverflag,
                        remainingmandays: auditor.remainingmandays,

                        instename: lang === 'ta' ? auditor.insttname : auditor.instename,
                        teamhead: lang === "ta" ? auditor.team_head_ta : auditor.team_head_en,
                        teammember: lang === "ta" ? auditor.team_members_ta : auditor.team_members_en

                    };
                }


                //  // Check if the current user is the team head (orderid = 1)
                //  if (auditor.teamhead === 'Y' && auditor.auditquartercode == executingquartercode ) {
                //      acc[auditor.instid].teamHead = auditor;
                //  } else if(auditor.teamhead === 'N' && auditor.auditquartercode == executingquartercode ) {
                //      acc[auditor.instid].members.push(auditor);
                //  }

                return acc;
            }, {});

            // Convert grouped data into rows for the table
            let tableData = Object.values(groupedAuditors).map((group, index) => {
                let teamHead = group.teamhead;
                let members = group.teammember;
                let inst = lang == "ta" ? group.instename : group.instename;
                let mandays = group.mandays;
                let fromdate = group.fdate || '-';
                let todate = group.tdate || '-';
                let teamsize = group.teamsize || '-';
		let spillOver = group.spilloverflag;
                let remainingmandays = group.remainingmandays;
                let spilloverlabel = '-';
                let tota_mandays = 0;
                let spillovertext_ta = '-';
                let spillovertext_en = '-';

            let date_range = (fromdate === '-' && todate === '-') ? '-' : fromdate + ' to ' + todate;
                //let date_range = fromdate + ' to ' + todate;
                // if (fromdate && todate && fromdate !== "-" && todate !== "-") {
                //     date_range = fromdate + ' to ' + todate;  // Only concatenate if both dates are valid
                // } else {
                //     date_range='-'
                // }
                //  let fromDate =fromdate 
               // console.log(date_range)


                if (spillOver == 'Y') {
                    spillovertext_ta = '???';
                    spillovertext_en = 'Yes';
                    tota_mandays = remainingmandays; // Use total_mandays if spillover is 'Y'
                } else {
                    spillovertext_ta = '?????';
                    spillovertext_en = 'No';
                    tota_mandays = mandays || 0; // Otherwise, use remainingmandays
                }


                spilloverlabel = lang == 'ta' ? spillovertext_ta : spillovertext_en;
                mandays = mandays || '-';

                inst = inst || "No Institute Name";

                //  // Construct a string for team members
                //  let membersString = members
                //      .map(member =>
                //          `<span>${lang === 'ta'?member.usertamilname:member.username} (${lang === 'ta' ? member.desigtlname:member.desigelname || 'No Designation'})</span>`
                //      )
                //      .join("<br>");
                //  let teamHead_des = teamHead ?
                //      `<span>${lang === 'ta'?teamHead.usertamilname:teamHead.username}(${lang === 'ta' ?teamHead.desigtlname:teamHead.desigelname})</span>` :
                //      "No Team Head";

                return {
                    index: index + 1, // S.No
                    instColumn: inst, // Institute name


                    //  date:fromdate,
                    teamHead: teamHead, // Team head username
                    members: members || "No Members", // Team members
                    teamsize: teamsize,
                    spillOver: spilloverlabel,
                    mandays: tota_mandays,
                    date_range: date_range
                };
            });

            // Display table if data exists



            // Check if DataTable already initialized
            if ($.fn.DataTable.isDataTable("#team_Inst_Details")) {
                // Clear existing table and re-add new data
                $('#team_Inst_Details').DataTable().clear().rows.add(tableData).draw();
            } else {

                //console.log(tableData)
                // Initialize DataTable
                $("#user_detail_table_wrapper").hide();
                $('#schedule_allocatedwork_wrapper').show();

                $('#team_Inst_Details').DataTable({
                    data: tableData,

                    columns: [{
                            data: "index",
                            render: function(data, type, row, meta) {
                                return `<div>
                                 <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>?</button> ${meta.row + 1}
                             </div>`;
                            },
                            className: 'text-end fw-normal',
                            type: "num"
                        },
                        {
                            data: "instColumn",
                            render: function(data) {
                                return data || '-';
                            },
                            className: "d-none d-md-table-cell lang extra-column text-wrap fw-normal"
                        },
                        {
                            data: "teamHead",
                            render: function(data) {
                                return data || '-';
                            },
                            className: "d-none d-md-table-cell lang extra-column text-wrap fw-normal"
                        },
                        {
                            data: "members",
                            render: function(data) {
                                return data || '-';
                            },
                            className: "d-none d-md-table-cell lang extra-column text-wrap fw-normal"
                        },
                        {
                            data: "teamsize",
                            render: function(data) {
                                return data || '-';
                            },
                            className: "d-none d-md-table-cell lang extra-column text-wrap fw-normal"
                        },
                        {
                            data: "spillOver",
                            render: function(data) {
                                return data || '-';
                            },
                            className: "d-none d-md-table-cell lang extra-column text-wrap fw-normal"
                        },
                        {
                            data: "mandays",
                            render: function(data) {
                                return data || '-';
                            },
                            className: "d-none d-md-table-cell lang extra-column text-wrap fw-normal"
                        },
                        {
                            data: "date_range",
                            render: function(data) {
                                return data || '-';
                            },
                            className: "d-none d-md-table-cell lang extra-column text-wrap fw-normal"
                        },

                    ],
                    "initComplete": function(settings, json) {
                        $("#audit_autoplan").wrap(
                            "<div style='overflow:auto; width:100%;position:relative;'></div>");
                    }

                });
                const mobileColumns = ["instename", "teamHead", "members", "mandays"];
                setupMobileRowToggle(mobileColumns);

                updatedatatable(lang, "team_Inst_Details");
                // $(".dt-button").addClass("btn btn-primary lang").text(download);
            }
        }
    </script>
    @endsection