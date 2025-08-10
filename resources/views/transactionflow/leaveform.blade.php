@section('content')
    @extends('index2')
    @include('common.alert')
    <style>
        .card_seperator {
            height: 10px;
            border: 0;
            box-shadow: 0 10px 10px -10px #8c8b8b inset;
        }

        .card-title {
            font-size: 15px;
        }

        .title-part-padding {
            background-color: #e3efff;
        }

        .card-body {
            padding: 15px 10px;
        }

        .card {
            margin-bottom: 10px;
        }

        .dataTables_info {
            margin-bottom: 1rem !important;
        }
    </style>
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

    <?php $sessionj_detail = session('charge');
    // $roleaction = json_decode($roleactioncode, true);
    // $userroleactioncode = $roleaction[0]['roleactionesname'];
    //  print_r($userroleactioncode);
    // print_r($sessionj_detail);
    ?>
    <div class="row">
        <div class="col-12">
            <div class="card card_border">
                <div class="card-header card_header_color lang" key="leave_app">
                    Leave Application
                </div>
                <div class="card-body collapse show">
                    <form id="leave_form" name="leave_form">
                        <div class="alert alert-danger alert-dismissible fade show hide_this" role="alert"
                            id="display_error">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @csrf
                        <input type="hidden" class="form-control" id="leave_id" name="leave_id" />
			<input type="hidden" name="enuserid" value="{{ $encryptedUserId }}">
                        <div class="row mb-2">
                            <div class="col-md-3 mb-1">
                                <label class="form-label required lang" for="validationDefault02" key="from_date">From
                                    date</label>
                                <div class="input-group" onclick="datepicker('from_date','')">
                                    <input type="text" class="form-control datepicker" id="from_date" name="from_date"
                                        placeholder="dd/mm/yyyy" />
                                    <span class="input-group-text">
                                        <i class="ti ti-calendar fs-5"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3 mb-1">
                                <label class="form-label required lang" for="validationDefault02" key="to_date"> To
                                    date</label>
                                <div class="input-group" onclick="datepicker('to_date','')">
                                    <input type="text" class="form-control datepicker" id="to_date" name="to_date"
                                        placeholder="dd/mm/yyyy" />
                                    <span class="input-group-text">
                                        <i class="ti ti-calendar fs-5"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label required lang" key="leave_type" for="validationDefault01">Leave
                                    Type</label>
                                <select class="form-select mr-sm-2" id="leave_type" name="leave_type">
                                    <option value="">---Select Leave Type---</option>
                                    @foreach ($leavetype_det as $leavetype)
                                        <option value="{{ $leavetype->leavetypeid }}">
                                            {{ $leavetype->leavetypeelname }}

                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label required lang" key="reason" for="validationDefault01">Reason
                                </label>
                                <textarea class="form-control" id="reason" name="reason" placeholder="Enter reason for leave"></textarea>
                            </div>
                        </div>


                        <div class="row justify-content-center">
                            <div class="col-md-2 mx-auto">
                                <input type="hidden" name="action" id="action" value="insert" />
                                <button class="btn button_save mt-3" type="submit" action="insert" id="buttonaction"
                                    name="buttonaction">Save </button>
                                <button type="button" class="btn btn-danger mt-3" id="reset_button">Clear</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card card_border mt-2">
        <div class="card-header card_header_color lang" key="leave_app_det">Leave Application Details</div>
        <div class="card-body">
            <div class="datatables">
                <div class="table-responsive hide_this" id="tableshow">
                    <table id="leavedetTable"
                        class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                        <thead>
                            <tr>
                                <th class="lang" key="s_no">S.No</th>
                                <th>Leave Period</th>
                                <th>Leave Type</th>

                                <th>Reason</th>

                                <th class="all">Action</th>
                            </tr>
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

    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/js/jquery_3.7.1.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    {{-- data table --}}
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

    <script src="../assets/js/datatable/datatable-advanced.init.js"></script>


    <script>
  $(document).ready(function() {
            fetchAlldata();
            initialize_date();

        });

        function initialize_date() {
            // Initialize 'from_date' datepicker
            $('#from_date').datepicker({
                format: 'dd/mm/yyyy',
                daysOfWeekDisabled: [0, 6], // Disable Sundays (0) and Saturdays (6)
                startDate: (() => {
                    const startDate = new Date();
                    startDate.setMonth(startDate.getMonth() - 1); // Subtract one month
                    return startDate;
                })(), // Minimum date is one month before the current date
                autoclose: true
            });

            // Initialize 'to_date' datepicker with the default start date as today + 11 days
            $('#to_date').datepicker({
                format: 'dd/mm/yyyy',
                daysOfWeekDisabled: [0, 6], // Disable Sundays (0) and Saturdays (6)
                startDate: (() => {
                    const startDate = new Date();
                    startDate.setMonth(startDate.getMonth() - 1); // Subtract one month
                    return startDate;
                })(),
                autoclose: true
            });

            // Update 'to_date' minDate when 'from_date' changes
            $('#from_date').on('changeDate', function() {
                var fromDate = $('#from_date').datepicker('getDate');
                if (fromDate) {
                    // Add 11 days to the selected 'from_date'
                    // fromDate.setDate(fromDate.getDate() + 1);

                    // Update 'to_date' minDate
                    $('#to_date').datepicker('setStartDate', fromDate);

                    // If the selected from_date is before the required minDate for to_date, clear to_date
                    var toDate = $('#to_date').datepicker('getDate');
                    if (toDate && toDate < fromDate) {
                        $('#to_date').datepicker('clearDates');
                    }
                }
            });

            // Update 'from_date' minDate when 'to_date' changes
            $('#to_date').on('changeDate', function() {
                var toDate = $('#to_date').datepicker('getDate');
                if (toDate) {
                    $('#from_date').datepicker('setEndDate', toDate);

                    // If the selected to_date is before the required minDate for from_date, clear from_date
                    var fromDate = $('#from_date').datepicker('getDate');
                    if (fromDate && fromDate > toDate) {
                        $('#from_date').datepicker('clearDates');
                    }
                }
            }); // Add 11 days to the selected 'to_date'
            // toDate.setDate(toDate.getDate() - 1);
            const startDate = new Date();
            startDate.setMonth(startDate.getMonth() - 1);
            // Update 'from_date' maxDate

        }

        let holidaysget = [];

        // Fetch holidays (assumes Laravel returns array of objects with 'date' in 'dd/mm/yyyy' format)
        $.ajax({
            url: '/fetch-holidays',
            method: 'GET',
            async: false,
            success: function(data) {
                holidaysget = data.map(item => {
                    const [dd, mm, yyyy] = item.date.split('/');
                    return `${yyyy}-${mm}-${dd}`; // convert to 'yyyy-mm-dd'
                });
                console.log("Formatted Holidays:", holidaysget);
            },
            error: function() {
                console.error("Failed to fetch holidays.");
            }
        });

        // Format a Date object to 'yyyy-mm-dd' string
        function formatISODate(date) {
            const yyyy = date.getFullYear();
            const mm = ('0' + (date.getMonth() + 1)).slice(-2);
            const dd = ('0' + date.getDate()).slice(-2);
            return `${yyyy}-${mm}-${dd}`;
        }

        // Check if a date is a business day (not weekend and not holiday)
        function isBusinessDay(date) {
            const day = date.getDay();
            const formattedDate = formatISODate(date);
            return day !== 0  && day !== 6 && !holidaysget.includes(formattedDate);
        }

    

        // Add business days
        function addBusinessDays(startDate, daysToAdd) {
            let result = new Date(startDate);
            while (daysToAdd > 0) {
                result.setDate(result.getDate() + 1);
                if (isBusinessDay(result)) {
                    daysToAdd--;
                }
            }
            return result;
        }

        function datepicker(value, setdate) {
            var today = new Date();

            if (value == 'from_date') {
                // Calculate the minimum date (18 years ago)
                /*var maxDate = new Date(today);
                maxDate.setMonth(today.getMonth() + 4);*/

                // Calculate the maximum date (60 years ago)
                var minDate = today;
                var maxDate = today;

            }
            if (value == 'to_date') {
               /* var maxDate = new Date(today);
                maxDate.setMonth(today.getMonth() + 4);*/

                // Calculate the maximum date (60 years ago)
                var minDate = today;
                var maxDate = addBusinessDays(minDate,2);
                

            }

            // Format the dates to dd/mm/yyyy format
            var minDateString = formatDate(minDate); // Format date to dd/mm/yyyy
            var maxDateString = formatDate(maxDate); // Format date to dd/mm/yyyy

            init_datepicker(value, minDateString, maxDateString, setdate)
        }




        /***********************************Jquery Form Validation **********************************************/

        const $leave_form = $("#leave_form");

        // Validation rules and messages
        $leave_form.validate({
            rules: {

                from_date: {
                    required: true,
                },
                to_date: {
                    required: true,
                },

                leave_type: {
                    required: true,
                },

                reason: {
                    required: true,
                },
            },
            errorPlacement: function(error, element) {
                // For datepicker fields inside input-group, place error below the input group
                if (element.hasClass('datepicker')) {
                    // Insert the error message after the input-group, so it appears below the input and icon
                    error.insertAfter(element.closest('.input-group'));
                } else {
                    // For other elements, insert the error after the element itself
                    error.insertAfter(element);
                }

            },

            messages: {

                from_date: {
                    required: "Select From Date ",
                },
                to_date: {
                    required: "Select  To Date",
                },
                leave_type: {
                    required: "select Leave Type",
                },
                reason: {
                    required: "Enter Reason",
                },


            }
        });

        // Scroll to the first error field (for better UX)
        function scrollToFirstError() {
            const firstError = $leave_form.find('.error:first');
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
        }
        /***********************************Jquery Form Validation **********************************************/
        function reset_form() {

            $('#display_error').hide();
            var validator = $("#leave_form").validate();
            validator.resetForm();
            initialize_date();
           // fetchAlldata();
	    changeButtonAction('leave_form','action','buttonaction', 'reset_button','display_error',  @json($savebtn), @json($clearbtn), @json($insert))
            updateSelectColorByValue(document.querySelectorAll(".form-select"));
        }
        /***********************************Submission Button Function**********************************************/
        $(document).on('click', '#buttonaction', function(event) {
            event.preventDefault(); // Prevent form submission

            if ($leave_form.valid()) {

                get_insertdata('insert')
            } else {
                scrollToFirstError();
            }
        });
        //reset the form
        $('#reset_button').on('click', function() {

            reset_form(); // Call the reset_form function
        });

        $(document).on('click', '.edit_btn', function() {
            // Add more logic here
            // alert();
            var id = $(this).attr('id'); //Getting id of user clicked edit button.

            if (id) {
                reset_form();
                fetchsingle_data(id)

            }
        });
        $(document).on('click', '.fwd_btn', function() {

            // alert();
            var id = $(this).attr('id');
            var transtypecode = $(this).attr('transtypecode');

            if (id) {
                var confirmation = 'Are you sure to forward the leave application?';
                document.getElementById("process_button").onclick = function() {
                    // getForwardTo_data(id, transtypecode);
                    forward_application( id, transtypecode)
                };
                passing_alert_value('Confirmation', confirmation, 'confirmation_alert', 'alert_header',
                    'alert_body', 'forward_alert');
                // reset_form();
                // getTeamhead_det(id);

            }
        });

        /***********************************Submission Button Function**********************************************/

        /***********************************Insert, Update, Edit Leave**********************************************/
      function get_insertdata(action) {

            var requestSent = false;

            if (!requestSent) {

                $('#buttonaction').attr('disabled', true);
                var formData = $('#leave_form').serializeArray();



                if (action === 'finalise') {
                    finaliseflag = 'F';
                } else if (action === 'insert') {
                    finaliseflag = 'Y';
                }

                // Push the finaliseflag to the formData array
                formData.push({
                    name: 'finaliseflag',
                    value: finaliseflag
                });


                $.ajax({
                    url: '/storeOrUpdateLeave',
                    type: 'POST',
                    data: formData,
                    success: function(response) {

                        if (response.status == 'success') {
                            passing_alert_value('Confirmation', response.message,
                                'confirmation_alert', 'alert_header', 'alert_body',
                                'confirmation_alert');

                            reset_form();
                            fetchAlldata();
                            table.ajax.reload();

                        } else {
                            alert(response.message);
                            
                        }

                    },
                    error: function(xhr, status, error) {

                        var response = JSON.parse(xhr.responseText);

                        var errorMessage = response.message ||
                            'An unknown error occurred';
                        // $('#display_error').show();
                        // $('#display_error').text(errorMessage);
                        passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                            'alert_header', 'alert_body', 'confirmation_alert');


                        // Optionally, log the error to console for debugging
                        console.error('Error details:', xhr, status, error);
                    },
                    complete: function() {
                            // Optionally, you can re-enable the button here if desired
                        $('#buttonaction').removeAttr('disabled');
                        }
                });
            }
        }

        function fetchsingle_data(leaveid) {
            $.ajax({
                url: 'fetchsingle_data', // Your API route to get user details
                method: 'POST',
                data: {
                    leaveid: leaveid
                }, // Pass deptuserid in the data object
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {
                    if (response.success) {
                        $('#display_error').hide();
                     changeButtonAction('leave_form','action','buttonaction', 'reset_button','display_error', @json($updatebtn), @json($clearbtn), @json($update))
                        // validator.resetForm();

                        const leave_det = response.data; // The array of schedule data

  datepicker('from_date', convertDateFormatYmd_ddmmyy(leave_det[0].fromdate));
                        datepicker('to_date', convertDateFormatYmd_ddmmyy(leave_det[0].todate));


                        $('#leave_id').val(leave_det[0].encrypted_leaveid);
                        $('#leave_type').val(leave_det[0].leavetypecode);
                        $('#reason').val(leave_det[0].reason);


                    } else {
                        alert('Schedule Details not found');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
        /***********************************Insert, Update, Edit Leave**********************************************/



        function fetchAlldata() {

            if ($.fn.dataTable.isDataTable('#leavedetTable')) {
                $('#leavedetTable').DataTable().clear().destroy();
            }

            var table = $('#leavedetTable').DataTable({
                "processing": true,
                "serverSide": false,
                "lengthChange": false,
                "ajax": {
                    "url": "/fetchall_leavedata", // Your API route for fetching data
                    "type": "POST",
                    "headers": {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // Pass CSRF token in headers
                    },
                    "dataSrc": function(json) {

                        if (json.data && json.data.length > 0) {
                            $('#tableshow').show();
                            $('#leavedetTable_wrapper').show();
                            $('#no_data').hide(); // Hide custom "No Data" message
                            return json.data;
                        } else {
                            $('#tableshow').hide();
                            $('#leavedetTable_wrapper').hide();
                            $('#no_data').show(); // Show custom "No Data" message
                            return [];
                        }
                    }
                },
                "columns": [{
                        "data": null, // Serial number column
                        "render": function(data, type, row, meta) {
                            return meta.row + 1; // Serial number starts from 1
                        }
                    },
                    {
                        "data": "null",
                        "render": function(data, type, row) {
                            // Convert DOB to dd-mm-yyyy format
                            let fromdate = row.fromdate ? new Date(row.fromdate).toLocaleDateString(
                                    'en-GB') :
                                "N/A";
                            let todate = row.todate ? new Date(row.todate).toLocaleDateString(
                                    'en-GB') :
                                "N/A";
                            if (fromdate === todate) {
                                return ` ${fromdate}`;
                            } else {
                                return ` ${fromdate} - ${todate}`;
                            }

                        }
                    },
                    {
                        "data": "leavetypeelname"
                    },


                    {
                        "data": "reason"
                    },


                    {
                        "data": "encrypted_leaveid", // Use the encrypted deptuserid
                        "render": function(data, type, row) {
                            if (row.processcode === 'S') {
                                // Check if statusflag is 'N'
                                return `<center>
                        <a class="btn editicon edit_btn" id="${data}">
                            <i class="ti ti-edit fs-4"></i>
                        </a>
                         <a class="btn editicon fwd_btn" id="${data}" transtypecode="${row.transactiontypecode}">
                            <i class="ti ti-corner-up-right-double fs-4"></i>
                        </a>
                    </center>`;
                            } else if (row.processcode === 'F') {
                                // Otherwise, show the Finalize button
                                return `<center>
                        <button class="btn btn-primary finalize_btn" id="${data}">
                            Forwarded
                        </button>
                    </center>`;
                            } else if (row.processcode === 'I') {
                                // Otherwise, show the Finalize button
                                return `<center>
                        <button class="btn btn-danger finalize_btn" id="${data}">
                            Rejected
                        </button>
                    </center>`;
                            } else if (row.processcode === 'P') {
                                // Otherwise, show the Finalize button
                                return `<center>
                        <button class="btn btn-success finalize_btn" id="${data}">
                            Approved
                        </button>
                    </center>`;
                            }
                        }
                    }
                ]
            });
        }

        // function getForwardTo_data(leaveid, transtypecode) {
        //     var leaveid = leaveid;
        //     var transtypecode = transtypecode;
        //     $.ajax({
        //         url: 'fetchforwardto_data', // Your API route to get user details
        //         method: 'POST',
        //         data: {
        //            // leaveid: leaveid
        //            transtypecode : transtypecode
        //         },
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
        //                 'content') // CSRF token for security
        //         },
        //         success: function(response) {
        //             if (response.success) {
        //                 $('#display_error').hide();
        //                 change_button_as_update('leave_form', 'action', 'buttonaction',
        //                     'display_error', '', '');
        //                 // validator.resetForm();

        //                 // const teamdet = response.data[0]; // The array of schedule data
        //                 // var forwardto_userid = teamdet.userid;
        //                 // var forwardto_userchargeid = teamdet.userchargeid;

        //                 if (teamdet) {

        //                     forward_application(forwardto_userid, forwardto_userchargeid, leaveid,
        //                         transtypecode);
        //                         forward_application( leaveid, transtypecode,userid)
        //                 }


        //             } else {
        //                 alert(' Details not found');
        //             }
        //         },
        //         error: function(xhr, status, error) {
        //             console.error('Error:', error);
        //         }
        //     });
        // }

        function forward_application( id, transtypecode) {


$.ajax({
    url: '/transaction/forward_application', // Your API route to get user details
    method: 'POST',
    data: {
        // userid: userid,
        // userchargeid: forwardto_userchargeid,
        id: id,
        transactiontypecode: transtypecode,
        action : 'first'


    }, // Pass deptuserid in the data object
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
            'content') // CSRF token for security
    },
    success: function(response) {
        if (response.status == 'success') {
            // $('#display_error').hide();
            // change_button_as_update('othertrans_form', 'action', 'buttonaction',
            //     'display_error', '', '');
            // // validator.resetForm();

            // passing_alert_value('Confirmation', response.message,
            //     'confirmation_alert', 'alert_header', 'alert_body',
            //     'confirmation_alert');

            // reset_form();
            // fetchAlldata(lang);
            passing_alert_value('Confirmation', response.message,
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');

                        reset_form();
                         fetchAlldata();
                        table.ajax.reload();


        } else {
            alert(response.message);
            
        }
    },
    error: function(xhr, status, error) {
        console.error('Error:', error);
    }
});
}

   
    </script>
@endsection
