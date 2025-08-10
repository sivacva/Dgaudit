@section('content')
@extends('index2')
@include('common.alert')
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">



<?php
$instdel = json_decode($results, true);
// print_r($instdel);
$session = session('charge');
$usertypecode = $session->usertypecode;
if ($session->usertypecode == 'I') {
    $sessioninstid = $session->instid;
} else {
    $sessioninstid = '';
}

if ($instdel) {
    $datashow = '';
    $nodatashow = 'hide_this';
} else {
    $datashow = 'hide_this';
    $nodatashow = '';
}

?>
<style>
    .disabledanch {
        pointer-events: none;
        /* disables click */
        opacity: 0.6;
        /* visual feedback */
        cursor: not-allowed;
    }

    .form-check-input {
        border: 1.25px solid #2d4e86;
    }
/* From Uiverse.io by abrahamcalsin */
    .spinner-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 100vw;
        background-color: rgba(28, 28, 28, 0.18);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;

    }

    .dot-spinner {
        --uib-size: 2.8rem;
        --uib-speed: .9s;
        --uib-color: #183153;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        height: var(--uib-size);
        width: var(--uib-size);
    }

    .dot-spinner__dot {
        position: absolute;
        top: 0;
        left: 0;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        height: 100%;
        width: 100%;
    }

    .dot-spinner__dot::before {
        content: '';
        height: 20%;
        width: 20%;
        border-radius: 50%;
        background-color: var(--uib-color);
        transform: scale(0);
        opacity: 0.5;
        animation: pulse0112 calc(var(--uib-speed) * 1.111) ease-in-out infinite;
        box-shadow: 0 0 20px rgba(18, 31, 53, 0.3);
    }

    .dot-spinner__dot:nth-child(2) {
        transform: rotate(45deg);
    }

    .dot-spinner__dot:nth-child(2)::before {
        animation-delay: calc(var(--uib-speed) * -0.875);
    }

    .dot-spinner__dot:nth-child(3) {
        transform: rotate(90deg);
    }

    .dot-spinner__dot:nth-child(3)::before {
        animation-delay: calc(var(--uib-speed) * -0.75);
    }

    .dot-spinner__dot:nth-child(4) {
        transform: rotate(135deg);
    }

    .dot-spinner__dot:nth-child(4)::before {
        animation-delay: calc(var(--uib-speed) * -0.625);
    }

    .dot-spinner__dot:nth-child(5) {
        transform: rotate(180deg);
    }

    .dot-spinner__dot:nth-child(5)::before {
        animation-delay: calc(var(--uib-speed) * -0.5);
    }

    .dot-spinner__dot:nth-child(6) {
        transform: rotate(225deg);
    }

    .dot-spinner__dot:nth-child(6)::before {
        animation-delay: calc(var(--uib-speed) * -0.375);
    }

    .dot-spinner__dot:nth-child(7) {
        transform: rotate(270deg);
    }

    .dot-spinner__dot:nth-child(7)::before {
        animation-delay: calc(var(--uib-speed) * -0.25);
    }

    .dot-spinner__dot:nth-child(8) {
        transform: rotate(315deg);
    }

    .dot-spinner__dot:nth-child(8)::before {
        animation-delay: calc(var(--uib-speed) * -0.125);
    }

    @keyframes pulse0112 {

        0%,
        100% {
            transform: scale(0);
            opacity: 0.5;
        }

        50% {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>



<div class="spinner-wrapper d-none" id="loader">
    <div class="dot-spinner">
        <div class="dot-spinner__dot"></div>
        <div class="dot-spinner__dot"></div>
        <div class="dot-spinner__dot"></div>
        <div class="dot-spinner__dot"></div>
        <div class="dot-spinner__dot"></div>
        <div class="dot-spinner__dot"></div>
        <div class="dot-spinner__dot"></div>
        <div class="dot-spinner__dot"></div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card" style="border-color: #7198b9">
            <div class="card-header card_header_color">Download Audit Report</div>
            <div class="card-body"><br>
                <div class="datatables <?php echo $datashow; ?>">
                    <div class="table-responsive" id="tableshow" style="overflow-x: auto;">
                        <table id="usertable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang" key="s_no">S.No</th>
                                    <th class="text-wrap">Institution Name</th>
                                    <th>Team Members</th>
                                    <th>ManDays</th>
                                    <th>Region</th>
                                    <th>District</th>
                                    <!--<th>From date</th>
                                    <th>To date</th>-->
                                    <th>Entry Meeting date</th>
                                    <th>Exit Meeting date</th>
                                    <th class="all">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($instdel as $index => $item)
                                <tr>
                                    <td class="text-end">{{ $index + 1 }}</td> <!-- S.No -->
                                    <td>{{ $item['instename'] }}</td>
                                    <td>
                                        <b>Team Head:</b> {{ $item['team_head_en'] }}<br>
                                        <b>Team Members:</b> {{ $item['team_members_en'] }}
                                    </td>
                                    <td>{{ $item['mandays'] }}</td>
                                    <td>{{ $item['regionename'] }}</td>
                                    <td>{{ $item['distename'] }}</td>
                                    <!-- <td>{{ $item['formatted_fromdate'] }}</td>
                                        <td>{{ $item['formatted_todate'] }}</td>-->
                                    <td>{{ $item['formatted_entrydate'] }}</td>
                                    <td>{{ $item['formatted_exitdate'] }}</td>
                                    <td>
                                        <a class="btn btn-sm btn-primary download-report-btn"
                                            data-id="{{ $item['encrypted_auditscheduleid'] }}">
                                            <i class="ti ti-download fs-4 me-2"></i>
                                            Download Report
                                        </a>

                                        @php

                                        if($usertypecode!='I'){
                                        $isdisabled = $item['issuedflag'] === 'Y';
                                        @endphp
                                        <a class="btn btn-sm btn-primary send-mail-btn {{ $isdisabled ? 'disabledanch' : '' }}" data-id="{{ $item['encrypted_auditscheduleid'] }}">
                                            <i class="fa fa-envelope fs-4 me-2"></i> Issue Report
                                        </a>
                                        @php
                                        }
                                        @endphp
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                <div id='no_data' class='<?php echo $nodatashow; ?>'>
                    <center>No Data Available</center>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- <script src="../assets/js/vendor.min.js"></script>  -->

<script src="../assets/js/jquery_3.7.1.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script>
    $(document).ready(function() {
        $('.download-report-btn').on('click', function() {
	$('#loader').removeClass('d-none');

            var audit_scheduleid = $(this).data('id');
            var lang = getLanguage('Y');
            $.ajax({
                url: '{{ route("download.report") }}',
                method: 'POST',
                data: {
                    audit_scheduleid: audit_scheduleid,
                    lang: lang,
                    _token: '{{ csrf_token() }}'
                },
                xhrFields: {
                    responseType: 'blob' // Handle binary file (PDF)
                },
                success: function(data, status, xhr) {
                    var blob = new Blob([data], {
                        type: 'application/pdf'
                    });
                    var downloadUrl = URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = downloadUrl;

                    // Optional: dynamic filename from content-disposition header
                    var filename = "AuditReport.pdf";
                    var disposition = xhr.getResponseHeader('Content-Disposition');
                    if (disposition && disposition.indexOf('filename=') !== -1) {
                        filename = disposition.split('filename=')[1].replace(/"/g, '');
                    }

                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    URL.revokeObjectURL(downloadUrl);
                    passing_alert_value('Confirmation', 'Audit Report Downloaded Successfully!', 'confirmation_alert',
                        'alert_header', 'alert_body',
                        'confirmation_alert');
                },
		complete: function() {
                    $('#loader').addClass('d-none');
                },
                error: function(xhr) {
		$('#loader').addClass('d-none');
                    if (xhr.status === 404) {
                        passing_alert_value('Confirmation', 'File not found', 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'confirmation_alert');
                    } else {
                        passing_alert_value('Confirmation', 'An error occurred while downloading the report.', 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'confirmation_alert');
                    }
                }
            });
        });
    });

    $(document).on('click', '.send-mail-btn', function() {
        var scheduleId = $(this).data('id');
        content = `<div class="div" id="verify_btn">
                       <div class="col-md-12 mt-4 ">
                         <p id="error_msg" class="hide_this"style="font-weight:normal; color:red"></p>
                           <div class="form-check">
                               <input class="form-check-input" type="checkbox" name="exampleRadios" id="usercheck"
                                   value="option1">


                               <label class="form-check-label lang" for="exampleRadios1">
                                   <b>I hereby downloaded the Audit Report and i verified all the details are correct, and the attachments are properly available
                                       <b>
                                      
                               </label>
                           </div>
                       </div>
                    </div>`;

        passing_alert_value('Confirmation', content, 'confirmation_alert',
            'alert_header', 'alert_body',
            'forward_alert');
        $('#process_button').removeAttr('data-bs-dismiss');
        //  $('#process_button').off('click').on('click', function(event) {
        $('#process_button').on('click', function() {
            event.preventDefault();
            $('#confirmation_alert').modal('show'); // Ensure modal is visible if it was hidden
            $('#process_button').removeAttr('data-bs-dismiss', 'modal');

            var isChecked = $('#usercheck').prop('checked');
            if (isChecked) {
                $('#confirmation_alert').modal('hide'); // Ensure modal is visible if it was hidden
                $('#process_button').attr('data-bs-dismiss', 'modal');
                // $('#confirmation_alert').modal('hide');
                sentauditeemail(scheduleId)
            } else {

                $('#error_msg').show();
                $('#error_msg').html('Please verify the downlaod status');

            }
        });

    });


    function sentauditeemail(scheduleid) {

        // var isChecked = $('#usercheck').prop('checked');
        // if (isChecked) {
        $.ajax({
            url: '/sendauditeemail',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                schedule_id: scheduleid
            },
            success: function(response) {
                if (response.success) {
                    passing_alert_value(
                        'Success',
                        response.message,
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );

                    $('#ok_button').off('click').on('click', function(event) {
                        event.preventDefault();
                        location.reload();
                    });
                }


            },

            error: function(xhr, status, error) {


                var response = JSON.parse(xhr.responseText);
                var errorMessage = response.error || 'Error Occured';

                passing_alert_value('Alert', errorMessage, 'confirmation_alert', 'alert_header',
                    'alert_body', 'confirmation_alert');

                console.error('Error details:', xhr, status, error);
            }
        });
        // } else {

        //     $('#confirmation_alert').modal('show');
        //     passing_alert_value('Confirmation', 'Please verify the downlaod status', 'confirmation_alert',
        //         'alert_header', 'alert_body',
        //         'confirmation_alert');

        // }

    }
    // On confirming send mail
    // $(document).on('click', '#process_button', function() {
    //     sentauditeemail(scheduleId)
    // });
</script>
@endsection