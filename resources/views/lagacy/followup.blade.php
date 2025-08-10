@section('content')
    @extends('index2') @include('common.alert')
    <?php
    // $instdel = json_decode($inst_details, true);
    // $getmajorobjection = json_decode($get_majorobjection, true);

    // // $teamhead = $instdel[0]['auditteamhead'];
    ?>
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>


    <?php

    // print_r($instData);
    // if (is_string($data) && json_decode($data) !== null) {
    //     $instdata = json_decode($data, true);
    //     print_r($instdata);
    // } else {
    //     echo 'Invalid JSON format';
    // }
    ?>


    <div class="row">
        <form id="lagacy_form" name="lagacy_form">
            @csrf
            <div class="col-12">
                <div class="card card_border">
                    <div class="card-body">

                        <div class="row">

                            <input type="hidden" name="instid" id="instid" value="{{ $instData->instid ?? '' }}">
                            <input type="hidden" name="catcode" id="catcode" value="{{ $catData->catcode ?? '' }}">
                            <input type="hidden" name="auditeeins_subcategoryid" id="auditeeins_subcategoryid"
                                value="{{ $catData->auditeeins_subcategoryid ?? '' }}">
                            <div class="col-md-4 mb-3">
                                <label class="form-label required" for="validationDefault01">Audit Office
                                </label>
                                <input type="text" class="form-control" id="total_mandays" name="total_mandays"
                                    value="{{ $instData->instename ?? '' }}" disabled>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label required" for="validationDefault01">Institution Category
                                </label>
                                <input type="text" class="form-control" id="catname" name="catname"
                                    value="{{ $catData->catename ?? '' }}" disabled>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label required" for="validationDefault01">Institution Sub
                                    Category</label>
                                <input type="text" class="form-control" id="subcatname" name="subcatname"
                                    value="{{ $catData->subcatename ?? '' }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label lang required" key="" for="validationDefault01">Type of
                                    Audit</label>
                                <input type="hidden" id="" name="" value="">
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="typeofauditcode"
                                    name="typeofauditcode">
                                    <option value="" data-name-en="---Select Department---"
                                        data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">---Select Plan Period---</option>

                                    @foreach ($typeofauditData as $typeofaudit)
                                        <option value="{{ $typeofaudit->typeofauditcode }}"
                                            data-name-en="{{ $typeofaudit->typeofauditename }}"
                                            data-name-ta="{{ $typeofaudit->typeofaudittname }}">
                                            {{ $typeofaudit->typeofauditename }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label lang required" key="" for="validationDefault01">Year Of
                                    Audit</label>
                                <input type="hidden" id="" name="" value="">
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode">
                                    <option value="" data-name-en="---Select Department---"
                                        data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">---Select Year Of Audit---</option>

                                    {{-- @foreach ($typeofauditData as $typeofaudit)
                                        <option value="{{ $typeofaudit->typeofauditcode }}" data-name-en="{{ $typeofaudit->typeofauditename }}" data-name-ta="{{ $typeofaudit->typeofaudittname }}">
                                            {{ $typeofaudit->typeofauditename }}
                                        </option>
                                        @endforeach --}}
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card card_border">
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <b><span id="forwardedby" class="text-end"></span></b>
                                    </div>
                                    <div class="col-md-2">
                                        <!-- Optional: You can place another item here if needed -->
                                    </div>
                                    <div class="col-md-4">
                                        <b><span id="approvedby" class="text-end"></span></b>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label required lang" for="validationDefaultUsername"
                                            key="major_obj">Title/Heading</label>
                                        <select class="select form-control custom-select" id="mainobjectionid"
                                            name="mainobjectionid" onchange="getminorobjection('','')">
                                            <option value="" data-name-en="---Select Title---"
                                                data-name-ta="---தலைப்பைத் தேர்ந்தெடுக்கவும்---">---Select Title---
                                            </option>
                                            @foreach ($objectionData as $mainobjection)
                                                <option value="{{ $mainobjection->mainobjectionid }}"
                                                    data-name-en="{{ $mainobjection->objectionename }}"
                                                    data-name-ta="{{ $mainobjection->objectiontname }}">
                                                    {{ $mainobjection->objectionename }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="col-md-4 ">
                                        <label class="form-label required lang" for="validationDefaultUsername"
                                            key="minor_obj">Categorization of Paras
                                        </label>
                                        <select class="select form-control custom-select" id="subobjectionid"
                                            name="subobjectionid">

                                        </select>
                                    </div>
                                    <div class="col-md-4 ">
                                        <label class="form-label lang" for="validationDefaultUsername"
                                            key="amount_involved">Amount Involved
                                        </label>
                                        <input type="text" class="form-control only_numbers" id="amount_involved"
                                            name="amount_involved" placeholder="50,000" maxlength="9">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-4 ">
                                        <label class="form-label required lang" for="validationDefaultUsername"
                                            key="severity">Severity</label>
                                        <select class="select form-control custom-select" id="severityid"
                                            name="severityid">
                                            <!-- <option value="">@lang('Select Severity')</option> -->
                                            {{--
                                                    <option value="" data-en="Select Severity" data-ta="தெரிவு கடைசியாக"></option> --}}
                                            <option value="" data-name-en="---Select Severity---"
                                                data-name-ta="---தெரிவு கடைசியாக தேர்ந்தெடுக்கவும்---">---Select
                                                Severity---</option>

                                            @foreach ($severities as $key => $severity)
                                                <option value="{{ $key }}" data-en="{{ $severity['en'] }}"
                                                    data-ta="{{ $severity['ta'] }}">
                                                    {{ $severity['en'] }}
                                                    <!-- Default language text, can be changed dynamically -->
                                                </option>
                                            @endforeach

                                        </select>

                                    </div>
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-6">
                                                <label class="form-label required lang" for="validationDefaultUsername"
                                                    key="liability">
                                                    Liablility</label>
                                                <br>
                                                <div class="d-flex align-items-center">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input success" type="radio"
                                                            name="liability" id="Y" value="Y"
                                                            onchange="enable_liability('Y','liabilityname_div','liabilitygpfno_div')">
                                                        <label class="form-check-label lang" for="all"
                                                            key="yes">Yes</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input success" type="radio"
                                                            name="liability" id="N" value="N" checked
                                                            onchange="enable_liability('N','liabilityname_div','liabilitygpfno_div')">
                                                        <label class="form-check-label lang" for="district"
                                                            key="no">No</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-6 hide_this" id="liabilityname_div">
                                                <label class="form-label required" for="validationDefaultUsername">
                                                    Name</label>
                                                <input type="text" id="liabilityname" name="liabilityname"
                                                    class="form-control" placeholder="Enter Liability name">
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-md-4 hide_this" id="liabilitygpfno_div">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-6 ">
                                                <label class="form-label required" for="validationDefaultUsername">
                                                    GPF / CPF No</label>
                                                <input type="text" id="liabilitygpfno" name="liabilitygpfno"
                                                    class="form-control" placeholder="Enter Liability GPFno">
                                            </div>
                                            <div class="col-sm-12 col-md-6 ">
                                                <label class="form-label required" for="validationDefaultUsername">
                                                    Designation</label>
                                                <input type="text" id="liabilitydesig" name="liabilitydesig"
                                                    class="form-control" placeholder="Enter Liability Designation">
                                            </div>
                                        </div>
                                    </div>




                                    <div class="col-md-4">
                                        <label class="form-label required lang" for="validationDefaultUsername"
                                            key="slip_details">Slip Details
                                        </label>
                                        <textarea id="slipdetails" name="slipdetails" class="form-control" placeholder="Enter remarks"></textarea>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">


                                    <div class="col-md-12 ">
                                        <label class="form-label required lang" for="validationDefaultUsername"
                                            key="observation">
                                            Auditor Observation/Remarks</label>
                                        <textarea id="remarks" class="form-control" placeholder="Enter remarks" name="remarks"></textarea>
                                    </div>
                                    <div class="col-md-4 mt-4">
                                        <label class="form-label required lang" for="validationDefaultUsername"
                                            key="">Attachments
                                        </label>
                                        <span style="color:#ff0000; font-size:10px;">(Max Size : 3
                                            MB & File Format : Pdf,Excel,Jpg & Png )</span>
                                        <input type="hidden" class="form-control" id="uploadid" name="uploadid">

                                        <input type="file" class="form-control" id="file_upload" name="file_upload">
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-md-6" style="margin-left: 39% !important;">
                                            <div class="d-flex align-items-center gap-6">
                                                <input type="hidden" id="action" name="action" value="insert">
                                                <button class="btn button_save" id="buttonaction"
                                                    name="buttonaction">Save Draft
                                                </button>
                                                <button class="btn button_finalise" id="approvebtn">Approve</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>


                        </div>
                        <hr>
                        <div class="col-12">
                            <div class="card card_border">
                                <div class="card-header card_header_color lang" key="work_obj_map_table">Work Allocation
                                    and Objection Mapping
                                    Details</div>
                                <div class="card-body">
                                    <div class="datatables">
                                        <div class="table-responsive hide_this" id="tableshow">
                                            <table id="lagacy_table"
                                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                                <thead>
                                                    <tr>
                                                        <th class="lang" key="s_no">S.No</th>
                                                        <th class="lang" key="">Main Objection</th>
                                                        <th class="lang" key="instcat_label">Sub Objection</th>
                                                        {{-- <th class="lang" key="">Subcategory</th> --}}
                                                        <th class="lang" key="allocatedtowhom">Amount Invovlved</th>
                                                        {{-- <th class="lang" key="callforrecords_label">Call For Rcords</th>
                                                        <th class="lang" key="majorworkallocationtype">Main Work
                                                            Allocation</th>
                                                        <th class="lang" key="minorworkallocationtype">Minor Work
                                                            Allocation</th>

                                                        <th class="lang" key="main_obj_label">Objection</th> --}}
                                                        <th class="all lang" key="action">Action</th>
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
                    </div>
                </div>
            </div>
        </form>
    </div>

    </script>
    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <!-- <script src="../assets/js/extra-libs/moment/moment.min.js"></script> -->
    <!-- <script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script> -->
    <!-- <script src="../assets/js/forms/daterangepicker-init.js"></script> -->
    <!--select 2 -->
    <!-- <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script> -->
    <!-- <script src="../assets/libs/select2/dist/js/select2.min.js"></script> -->
    <!-- <script src="../assets/js/forms/select2.init.js"></script> -->
    <!--chat-app-->
    <script src="../assets/js/apps/chat.js"></script>
    <!-- Form Wizard -->

    <!-- <script src="../assets/js/forms/form-wizard.js"></script> -->
    <script src="../assets/libs/simplebar/dist/simplebar.min.js"></script>


    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>





    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           <script src="../assets/js/datatable/datatable-advanced.init.js"></script> -->
    <script>
        let editor;

        CKEDITOR.ClassicEditor.create(document.getElementById("remarks"), {
                toolbar: {
                    items: [
                        'findAndReplace', 'selectAll', '|',
                        'heading', '|',
                        'bold', 'italic', 'underline', '|',
                        'numberedList', '|',
                        'outdent', 'indent', '|',
                        'undo', 'redo',
                        'fontSize', 'fontFamily', '|',
                        'alignment', '|',
                        'uploadImage', 'insertTable',
                        '|',

                    ],
                    shouldNotGroupWhenFull: true
                },
                placeholder: 'Welcome to CAMS... ',
                fontFamily: {
                    options: [
                        'default', 'Marutham', 'Arial, Helvetica, sans-serif', 'Courier New, Courier, monospace',
                        'Georgia, serif', 'Lucida Sans Unicode, Lucida Grande, sans-serif',
                        'Tahoma, Geneva, sans-serif', 'Times New Roman, Times, serif',
                        'Trebuchet MS, Helvetica, sans-serif', 'Verdana, Geneva, sans-serif'
                    ],
                    supportAllValues: true
                },
                fontSize: {
                    options: [10, 12, 14, 'default', 18, 20, 22],
                    supportAllValues: true
                },
                htmlSupport: {
                    allow: [{
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }]
                },
                link: {
                    decorators: {
                        addTargetToExternalLinks: true,
                        defaultProtocol: 'https://',
                        toggleDownloadable: {
                            mode: 'manual',
                            label: 'Downloadable',
                            attributes: {
                                download: 'file'
                            }
                        }
                    }
                },
                removePlugins: [
                    'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'Base64UploadAdapter', 'MultiLevelList',
                    'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                    'RealTimeCollaborativeRevisionHistory',
                    'PresenceList', 'Comments', 'TrackChanges', 'TrackChangesData', 'RevisionHistory', 'Pagination',
                    'WProofreader',
                    'MathType', 'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter', 'TableOfContents',
                    'PasteFromOfficeEnhanced', 'CaseChange'
                ]
            })
            .then(e => {
                editor = e;
            })
            .catch(error => {
                console.error(error);
            });


        function enable_liability(selectedOption, liablilitynamedivid, liablilitygpfdivid) {
            if (selectedOption === 'Y') {
                // Show the textbox when "Yes" is selected
                $("#" + liablilitynamedivid).show(); //liabilityname_div
                $("#" + liablilitygpfdivid).show(); // liabilitygpfno_div

            } else {
                // Hide the textbox when "No" is selected
                $("#" + liablilitynamedivid).hide(); //liabilityname_div
                $("#" + liablilitygpfdivid).hide(); //liabilitygpfno_div
            }
        }


        var lang;

        $(document).ready(function() {
            lang = getLanguage();
            fetch_lagacydata(lang);
        });
        $("#translate").change(function() {
            lang = getLanguage('Y');
            // updateTableLanguage(lang);


        });
        let dataFromServer;

        function fetch_lagacydata(lang) {
            $.ajax({
                url: '/lagacy/fetch_lagacydata', // For creating a new user or updating an existing one
                type: 'POST',
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

                        renderTable(lang);
                    } else {

                        $('#tableshow').hide();
                        $('#usertable_wrapper').hide();
                        $('#no_data').show();
                    }
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

        function renderTable(language) {
            const objectioncolumn = language === 'ta' ? 'objectiontname' : 'objectionename';
            const subobjectioncolumn = language === 'ta' ? 'subobjectiontname' : 'subobjectionename';
            // const subcategoryColumn = language === 'ta' ? 'subcattname' : 'subcatename';
            // const membername = language === 'ta' ? 'குழு உறுப்பினர்' : 'Team Member';
            // const leadername = language === 'ta' ? 'குழு தலைவர்' : 'Team Leader';

            if ($.fn.DataTable.isDataTable('#lagacy_table')) {
                $('#lagacy_table').DataTable().clear().destroy();
            }


            table = $('#lagacy_table').DataTable({
                "processing": true,
                "serverSide": false,
                "lengthChange": false,
                "autoWidth": false,
                "data": dataFromServer,

                "initComplete": function(settings, json) {
                    $("#lagacy_table").wrap(
                        "<div style='overflow:auto; width:100%;position:relative;'></div>");
                },
                // "scrollX": true,
                "columns": [{
                        "data": null,
                        "render": function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        "data": objectioncolumn
                    },
                    {
                        // "data": categoryColumn
                        "data": subobjectioncolumn


                    },


                    {
                        "data": "amtinvolved"
                    },

                    {
                        "data": "encrypted_lagacyid",
                        "render": function(data, type, row) {
                            return `<center><a class="btn editicon edit_btn" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`;
                        }
                    }
                ],
                "columnDefs": [{
                        "targets": [0], // Targeting dynamically rendered columns
                        "width": "1%",
                        "className": "text-wrap"
                    }, {
                        "targets": [1, 2], // Targeting dynamically rendered columns
                        "width": "15%",
                        "className": "text-wrap"
                    },
                    {
                        "targets": [3, 4], // Targeting dynamically rendered columns
                        "width": "10%",
                        "className": "text-wrap"
                    },

                ],

                "dom": '<"row d-flex justify-content-between align-items-center"<"col-auto"B><"col-auto"f>>rtip',
                "buttons": [{
                    extend: "excelHtml5",
                    text: window.innerWidth > 768 ? '<i class="fas fa-download"></i>&nbsp;&nbsp;Download' :
                        '<i class="fas fa-download"></i>',
                    title: 'Mapping of Work ALllocation and Objection',
                    exportOptions: {
                        columns: ':not(:last-child)' // Excluding the last column (Action column)
                    },
                    className: window.innerWidth > 768 ? 'btn btn-info' :
                        'btn btn-info btn-sm' // Full button on desktop, smaller on mobile
                }],
                "pagingType": "simple_numbers",
                "responsive": true,
                "pageLength": 10,
                "lengthMenu": [
                    [10, 50, -1], // Full options for Desktop
                    [10, 25, 50, -1] // Compressed options for Mobile
                ],
                "fnDrawCallback": function() {
                    let $pagination = $('.dataTables_paginate');
                    let $pages = $pagination.find('.paginate_button:not(.next, .previous)');

                    // Function to adjust pagination and info text based on window width
                    function adjustView() {
                        if ($(window).width() <= 768) {
                            // Mobile View Adjustments
                            $(".dataTables_filter input").css({
                                "width": "100px",
                                "font-size": "12px",
                                "padding": "4px"
                            }); // Smaller search box
                            $(".dt-buttons .btn").addClass("btn-sm"); // Smaller download button

                            // Compress pagination display to show only first & last buttons
                            let totalPages = $pages.length;
                            $pages.each(function(index) {
                                if (index !== 0 && index !== totalPages - 1) {
                                    $(this).hide();
                                }
                            });

                            // Display "Showing x to y of z entries" on a separate row in mobile view
                            $(".dataTables_info").css("display", "block");
                            $(".dataTables_info").css("text-align", "center");
                            $(".dataTables_info").css("margin-bottom",
                                "20px"); // Optional: Align the text to center
                        } else {
                            // Desktop View Adjustments
                            $(".dataTables_info").css("display", "inline-block");
                            $(".dataTables_filter input").css({
                                "width": "auto",
                                "font-size": "14px",
                                "padding": "8px"
                            }); // Reset search box style
                            $(".dt-buttons .btn").removeClass("btn-sm"); // Reset download button size
                            // Show all pagination buttons
                            $pages.show();
                        }
                    }

                    // Call the function initially
                    adjustView();

                    // Call the function when the window is resized
                    $(window).resize(function() {
                        adjustView();
                    });
                }
            });

            $(window).resize(function() {
                table.buttons(0).text(
                    window.innerWidth > 768 ?
                    '<i class="fas fa-download"></i>&nbsp;&nbsp;Download' :
                    '<i class="fas fa-download"></i>'
                );
            });
        }
        $(document).on('click', '.edit_btn', function() {
            // Add more logic here
            // alert();

            var id = $(this).attr('id'); //Getting id of user clicked edit button.
            //  alert(id);

            if (id) {
                // reset_form();
                fetchlagacysingle_data(id);

            }
        });

        function fetchlagacysingle_data() {
            $.ajax({
                url: '/lagacy/fetch_lagacydata', // For creating a new user or updating an existing one
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        alert(data);
                        editor.setdata(data.remarks);

                    } else {
                        alert('Schedule Details not found');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }

            });
        }

        function getminorobjection(mainobjectionid = '', subobjectionid = '') {

            var mainobjectionid = mainobjectionid || $('#mainobjectionid').val();



            $.ajax({
                url: '/followup/getminordet', // Your API route to get user details
                method: 'POST',
                data: {
                    mainobjectionid: mainobjectionid,

                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF token for security
                },
                success: function(response) {
                    var data = response.minorobjectionData;


                    $('#subobjectionid').empty();
                    $('#subobjectionid').append(
                        '<option value="" data-name-en="---Select Category---"data-name-ta="--- வகையைத் தேர்ந்தெடுக்கவும்---">Select Category</option>'
                    );
                    $.each(data, function(index, minorobjection) {
                        var isSelected = minorobjection.catcode === subobjectionid ? 'selected' :
                            '';
                        $('#subobjectionid').append(
                            '<option value="' + minorobjection.subobjectionid + '"' +

                            ' data-name-en="' + minorobjection.subobjectionename + '"' +
                            ' data-name-ta="' + minorobjection.subobjectiontname + '" ' +
                            isSelected + '>' +
                            (lang == "en" ? minorobjection.subobjectionename : minorobjection
                                .subobjectiontname) +
                            '</option>'
                        );
                    });



                },

                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }

        /***********************************jquery Validation**********************************************/
        const $lagacy_form = $("#lagacy_form");


        $("#lagacy_form").validate({
            rules: {
                typeofauditcode: {
                    required: true,
                },
                mainobjectionid: {
                    required: true,
                },
                subobjectionid: {
                    required: true,
                },

                amount_involved: {
                    required: true,
                },

                severityid: {
                    required: true,
                },
                liability: {
                    required: true,
                },

                slipdetails: {
                    required: true,
                },


            },

            errorPlacement: function(error, element) {
                if (element.hasClass('select2')) {
                    // Insert the error message below the select2 dropdown container
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    // For other fields, insert the error message after the element itself
                    error.insertAfter(element);
                }
            },

            messages: {
                typeofauditcode: {
                    required: "Select the Plan Period",
                },
                mainobjectionid: {
                    required: "Select Main Objection",
                },
                subobjectionid: {
                    required: "Select Sub Objection",
                },

                amount_involved: {
                    required: "Enter the amount involved",
                },

                severityid: {
                    required: "Select severity",
                },
                liability: {
                    required: "Choose the liability",
                },

                slipdetails: {
                    required: "Enter the slipdetails",
                },
                // highlight: function(element, errorClass) {
                //     $(element).removeClass(errorClass); //prevent class to be added to selects
                // },

            }


        });


        // Scroll to the first error field (for better UX)
        function scrollToFirstError() {
            const firstError = $lagacy_form.find('.error:first');
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
            }
        }
        /***********************************jquery Validation**********************************************/
        $(document).on('click', '#buttonaction', function(event) {

            event.preventDefault(); // Prevent form submission
            // Check if the error message is visible
            // if ($('#display_error').is(':visible')) {
            //     return; // Exit the function to prevent form submission
            // }
            // if ($("#lagacy_form").valid()) {


            get_insertLagacydata('insert');

            // } else {
            //     scrollToFirstError();
            // }
        });

        function get_insertLagacydata(action) {


            var formData = new FormData($('#lagacy_form')[0]);
            formData.append('remarks', editor.getData());

            $.ajax({
                url: '/lagacy/followup_insert', // For creating a new user or updating an existing one
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // reset_form();
                        alert();

                        // passing_alert_value('Confirmation', response.success,
                        //     'confirmation_alert', 'alert_header', 'alert_body',
                        //     'confirmation_alert');
                        //fetchAlldata();
                        // table.ajax.reload(); // Reload the table



                    } else if (response.error) {}
                },
                error: function(xhr, status, error) {

                    var response = JSON.parse(xhr.responseText);

                    var errorMessage = response.message ||
                        'An unknown error occurred';
                    $('#display_error').show();
                    $('#display_error').text(errorMessage);
                    // passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                    //     'alert_header', 'alert_body', 'confirmation_alert');


                    // Optionally, log the error to console for debugging
                    console.error('Error details:', xhr, status, error);
                }
            });
        }
    </script>
@endsection
