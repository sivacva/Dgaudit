@section('content')
@extends('index2')
@include('common.alert')



@php

$sessionchargedel = session('charge');

$sessionroletypecode = $sessionchargedel->roletypecode;
$dga_roletypecode = $DGA_roletypecode;
$Dist_roletypecode = $Dist_roletypecode;
$Re_roletypecode = $Re_roletypecode;
$Ho_roletypecode = $Ho_roletypecode;
$Admin_roletypecode = $Admin_roletypecode;

$deptcode = $sessionchargedel->deptcode;
$regioncode = $sessionchargedel->regioncode;
$distcode = $sessionchargedel->distcode;

$make_dept_disable = $deptcode ? 'disabled' : '';
$make_region_disable = $regioncode ? 'disabled' : '';
$make_dist_disable = $distcode ? 'disabled' : '';
$auditteamhead = $sessionchargedel->auditteamhead;



@endphp



<style>

</style>

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">



<script src="../assets/js/jquery_3.7.1.js"></script>

<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

<!-- select2 -->
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>

<script src="../assets/js/download-button/buttons.min.js"></script>
<script src="../assets/js/download-button/jszip.min.js"></script>
<script src="../assets/js/download-button/buttons.print.min.js"></script>
<script src="../assets/js/download-button/buttons.html5.min.js"></script>
<script src="../assets/js/download-button/custom.xl.min.js"></script>
<style>
    .nav-tabs .nav-link {
        border: 2px solid #5a6174;
        color: #5a6174;
        font-weight: bold;
        transition: all 0.3s ease;
        margin: 0 5px;
        /* Add gap between tabs */
    }

    .nav-tabs .nav-link.active {
        border: 2px solid #4973e8;
        color: rgb(245, 245, 245);
        background-color: #4973e8;
    }

    .nav-tabs .nav-link:hover {
        border: 2px solid #4973e8;
    }
</style>
{{-- Pass data to JavaScript --}}
<script>

</script>



@php


$hasPendingY = collect($pendinginstcheck)->contains(function ($item) {
return $item->pendinginststatus === 'Y';
});
$hasOnlyNull = collect($pendinginstcheck)->every(function ($item) {
return is_null($item->pendinginststatus) || $item->pendinginststatus === 'N';
});

$spillData = json_decode($Spilloverdatecheck); // Decode the JSON
$spillDate = isset($spillData[0]->spilloverenddate) ? $spillData[0]->spilloverenddate : null;

if ($spillDate) {
$formattedDate = date('d/m/Y', strtotime($spillDate));
} else {
$formattedDate = 'N/A'; // Or display nothing
}
print_r($hasPendingY);
@endphp

@if (empty($spillous) && sizeof($data) == 0 && $hasPendingY)
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-body text-center">
            <p class="mb-2">
            <h4>All the Spillover and Pending institutions have successfully completed for Q2.<h4>
                    </p>
        </div>
    </div>
</div>

@elseif(empty($spillous) && sizeof($data) == 0 && $hasOnlyNull)
<form id="pendinginst_table" name="pendinginst_table">
    <div class="container mt-4 text-center">
        <div class="form-check d-flex justify-content-center align-items-center gap-2">
            <input type="checkbox" id="confirm_check" class="form-check-input" style="width: 17px; height: 17px; cursor:pointer;" />
            <label class="form-check-label mb-0" style="cursor: default;">
                <h4 class="mb-0">You have No Spillover and Pending Institutions for Q2</h4>

            </label>
        </div>

        <button class="btn w-20 bg-success button_finalise lang mt-3" key="confirm" type="button" id="savebtn" disabled>
            Finalize
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkbox = document.getElementById('confirm_check');
            const confirmBtn = document.getElementById('savebtn');

            checkbox.addEventListener('change', function() {
                confirmBtn.disabled = !this.checked;
            });
        });
    </script>
</form>

@else
<!-- Tabs Section -->
<div class="container mt-4">
    <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="scheduled_plans_tab" data-bs-toggle="tab"
                data-bs-target="#scheduled_plans" type="button" role="tab"
                aria-controls="scheduled_plans" aria-selected="true">
                Spill Over
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="slip_details_tab" data-bs-toggle="tab"
                data-bs-target="#slip_details" type="button" role="tab"
                aria-controls="slip_details" aria-selected="false">
                Pending
            </button>
        </li>
    </ul>
</div>

<br>

<div class="tab-content">
    <!-- Spill Over Tab -->
    <div class="tab-pane fade show active" id="scheduled_plans" role="tabpanel" aria-labelledby="scheduled_plans_tab">
        <div>
            <h3 class="text-center">List of Institutions To be Carry Forward to Q2</h3>
        </div>
        <div>
            <h4 class="text-center">Spill Over Closing Date <?php echo $formattedDate ?></h4>
        </div>

        <div class="card card_border">
            <div class="card-header card_header_color lang">List of Institutions</div>
            <div class="card-body">
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow_spillous">
                        <table id="spillous_table" class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle">S.No</th>
                                    <th class="text-center align-middle">Name of the Institution</th>
                                    <th class="text-center align-middle">Team Members</th>
                                    <th class="lang text-center align-middle">Proposed Audit Date</th>
                                    <th class="text-center align-middle">Entry Meet</th>
                                    <th class="text-center align-middle">Team Size</th>
                                    <th class="text-center align-middle">Mandays</th>
                                    <th class="text-center align-middle">Completed Mandays</th>
                                    <th class="text-center align-middle">Remaining Mandays</th>
                                    <th class="text-center align-middle">Remaining Working Days</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div id="no_data_spillous" class="hide_this">
                        <center>No Data Available</center>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Tab -->
    <div class="tab-pane fade" id="slip_details" role="tabpanel" aria-labelledby="slip_details_tab">
        <div>
            <h3 class="text-center">List of Pending Institutions in Q1</h3>
        </div>

        <div class="card card_border">
            <form id="pendinginst_table" name="pendinginst_table">
                <div class="card-header card_header_color lang">List of Institutions Pending in Q1</div>
                <div class="card-body">
                   
                    <div class="datatables">
                        <div class="table-responsive" id="tableshow_pending">
 <div class="custom-length mb-2" style="float:center;">
                        <label>
                            Show
                            <select id="customLengthChange" class="form-select d-inline-block w-auto form-select-sm mx-1">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            entries
                        </label>
                    </div>
                            <table id="pending_table" class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                <thead>
                                    <tr>
                                        <th class="lang text-center align-middle">S.No</th>
                                        <th class="text-center align-middle">Name of the Institution</th>
                                        <th class="lang text-center align-middle">Quarter</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div id="no_data_pending" class="hide_this">
                            <center>No Data Available</center>
                        </div>
                    </div>
                </div>

                <center>
                    <button class="btn button_save mt-3 lang" key="sav_draft" type="button" id="buttonaction" name="buttonaction">
                        Save Draft
                    </button>
                </center>
            </form>
        </div>
    </div>
</div>

<!-- Finalize Button -->
<div class="container mt-4">
    <div class="row">
        <div class="col-md-5 mx-auto text-center">
            <input type="hidden" name="action" id="action" value="insert" />
            <input type="hidden" name="tempid" id="tempid" value="" />

           @if (($countinst == $penidnCount )  || !empty($spillous) && empty($data))
            <button class="btn w-50 bg-success button_finalise lang mt-5" key="final_btn" type="button" id="finalisebtn">
                Finalize
            </button>
            @endif
        </div>
    </div>
</div>
@endif

<script>
    lengthChangeInitialized = false;

    const spillousData = @json($spillous ?? []);
    const pendingData = @json($data ?? []);

    $(document).ready(function() {
        const language = window.localStorage.getItem('lang') || 'en';

        // Initialize SPILLOUS Table
        const spillousTable = $('#spillous_table').DataTable({
            processing: true,
            serverSide: false,
            lengthChange: false,
            autoWidth: false,
            // scrollX: true, // ✅ Enables horizontal scroll
            // scrollY: '400px', // ✅ Optional: enables vertical scroll with fixed height
            // scrollCollapse: true, // ✅ Collapse scroll area if content is less
            data: spillousData,
            order: [
                [1, 'asc']
            ],
            columnDefs: [{
                targets: 0,
                type: 'num'
            }],
            order: [
                [0, 'asc']
            ],
            initComplete: function() {
                $("#spillous_table").wrap("<div class='table-responsive'></div>");
                if (spillousData.length > 0) {
                    $('#tableshow_spillous').fadeIn(200);
                    $('#no_data_spillous').hide();
                } else {
                    $('#tableshow_spillous').hide();
                    $('#no_data_spillous').removeClass('hide_this').show();
                }

            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                                        <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button>${meta.row + 1}
                                    </div>`;
                    },
                    className: 'text-wrap text-end',
                    type: "num"
                },
                {
                    data: 'instename',
                    render: function(data) {
                        return data || '-';
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        let teamHead = row.team_head_en || '-';
                        let teamMembers = row.team_members_en || '-';
                        return `
                    <div>
                        <strong>Team Head:</strong> ${teamHead} <br/>
                        <strong>Team Members:</strong> ${teamMembers}
                    </div>
                `;
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        function formatDate(dateStr) {
                            if (!dateStr) return '-';
                            const date = new Date(dateStr);
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const year = date.getFullYear();
                            return `${day}/${month}/${year}`;
                        }
                        let fromdate = formatDate(row.fromdate);
                        let todate = formatDate(row.todate);
                        return `<div>${fromdate} - ${todate}</div>`;
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap'
                },
                {
                    data: 'entrymeetdate',
                    render: function(data) {
                        function formatDate(dateStr) {
                            if (!dateStr) return '-';
                            const date = new Date(dateStr);
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const year = date.getFullYear();
                            return `${day}/${month}/${year}`;
                        }
                        return formatDate(data);
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap'
                },
                {
                    data: 'team_member_count',
                    render: function(data) {
                        return data || '-';
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap'
                },
                {
                    data: 'mandays',
                    render: function(data) {
                        return data || '-';
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap'
                },
                {
                    data: 'completed_mandays',
                    render: function(data) {
                        return data || '-';
                    },
                    className: 'd-none d-md-table-cell lang extra-column text-wrap'
                },
                {
                    data: 'remaining_mandays',
                    render: function(data) {
                        return data || '-';
                    },
                    className: 'text-wrap'
                },
                {
                    data: 'remaining_working_days',
                    render: function(data) {
                        return data || '-';
                    },
                    className: 'text-wrap'
                }
            ]



        });
        //   Mobile column handling
        const mobileColumns = [
            'instename', 'team_head_en', 'team_members_en',
            'fromdate', 'todate',
            'entrymeetdate', 'mandays', 'totalworkingmandays'
        ];
        setupMobileRowToggle(mobileColumns);
        updatedatatable(language, "spillous_table");



        const pendingTable = $('#pending_table').DataTable({
            processing: true,
            serverSide: false,

            autoWidth: false,
            data: pendingData,
            columnDefs: [{
                targets: 0,
                type: 'num'
            }],

            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        const rowIndex = meta.row + 1 + meta.settings._iDisplayStart;
                        return `
                                    <div>
                                        <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button>
                                        ${rowIndex}
                                    </div>
                                `;
                    },
                    className: 'text-wrap align-middle text-end',
                    type: "num"
                },
                {
                    data: 'instename',
                    render: function(data, type, row, meta) {
                        return `
                                    ${data}
                                    <input type="hidden" name="rows[${meta.row}][instid]" value="${row.instid}" />
                                    <input type="hidden" name="rows[${meta.row}][audit_quarter]" value="${row.currentquarter}" />
                                `;
                    },
                    className: 'text-start align-middle'
                },
                {
                    data: null,
                    render: function(data, type, row, meta) {

                        const selectedQuarter = row.newquartercode || '';

                        const options = ['Q2', 'Q3', 'Q4'].map(q => {
                            const selected = selectedQuarter === q ? 'selected' : '';
                            return `<option value="${q}" ${selected}>Quarter ${q.slice(-1)}</option>`;
                        }).join('');

                        const bgColor = selectedQuarter ? 'border:2px solid rgb(13, 133, 231)' : '';

                        return `
                                    <select 
                                        name="rows[${meta.row}][quarter]" 
                                        class="form-select quarter-select" 
                                        data-instid="${row.instid}"
                                        style="${bgColor}"
                                    >
                                       
                                        ${options}
                                    </select>
                                `;
                    },
                    className: 'text-start align-middle noExport'
                }
            ],
            initComplete: function() {
                if (pendingData.length === 0) {
                    $('#buttonaction').hide();
                   // $('#finalisebtn').hide();
                    $('#no_data_pending').removeClass('hide_this').show();
                    $('#tableshow_pending').hide();
                } else {
                    $('#buttonaction').show();
                   // $('#finalisebtn').show();
                    $('#no_data_pending').hide();
                    $('#tableshow_pending').show();
                }

                if (!lengthChangeInitialized) {
                    $('#customLengthChange').off('change').on('change', function() {
                        const length = parseInt($(this).val());
                        pendingTable.page.len(length).draw(); // ✅ CORRECT USAGE

                        if (pendingTable.responsive) {
                            pendingTable.responsive.recalc();
                        }
                    });
                    lengthChangeInitialized = true;
                }
            }
        });

        updatedatatable(language, "pending_table");

        $('#customLengthChange').off('change').on('change', function() {
            const length = parseInt($(this).val());

            pendingTable.page.len(length).draw();

            if (pendingTable.responsive) {
                pendingTable.responsive.recalc();
            }
        });

    });


    // let isSubmitting = false;

    function submitPendingInstForm(actionType) {

        $('#buttonaction').attr('disabled', true);

        $('#finalisebtn').attr('disabled', true);

        $('#savebtn').attr('disabled', true);

        //    if (isSubmitting) 
        //    return; // Prevent duplicate submissions
        // isSubmitting = true;
        if ($("#pendinginst_table").valid()) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const table = $('#pending_table').DataTable();
            const allRows = table.rows().nodes();

            let filteredRows = [];

            $(allRows).each(function(index, rowNode) {
                const instid = $(rowNode).find(`input[name="rows[${index}][instid]"]`).val();
                const audit_quarter = $(rowNode).find(`input[name="rows[${index}][audit_quarter]"]`).val();
                const quarter = $(rowNode).find(`select[name="rows[${index}][quarter]"]`).val();

                if (quarter && quarter.trim() !== '') {
                    let rowData = {
                        instid,
                        audit_quarter,
                        quarter
                    };

                    // ✅ Optionally add remainingmandays if available (based on your logic)
                    if (actionType === 'finalise') {
                        const remaining = $(rowNode).find(`input[name="rows[${index}][remainingmandays]"]`).val();
                        if (remaining !== undefined) {
                            rowData.remainingmandays = remaining;
                        }
                    }

                    filteredRows.push(rowData);
                }
            });

            // ✅ Prepare final data object
            let dataToSend = {
                rows: filteredRows,
                action: actionType
            };

            // ✅ Also pass SPILLOUS table data if finalizing
            const spillousFiltered = [];
            $('#spillous_table tbody tr').each(function() {
                const button = $(this).find('button.toggle-row');
                if (button.length) {
                    const dataRowJson = button.attr('data-row');
                    if (dataRowJson) {
                        // The JSON is HTML entity encoded, decode it first
                        const decodedJson = $('<textarea/>').html(dataRowJson).text();
                        const dataObj = JSON.parse(decodedJson);
                        spillousFiltered.push({
                            instid: dataObj.instid,
                            mandays: dataObj.mandays,
                            remainingmandays: dataObj.remaining_mandays
                        });
                    }
                }
            });
            dataToSend.spillous = spillousFiltered;



            console.log("Data sent to server:", dataToSend);

            $.ajax({
                url: "{{ route('/instchange/penidninstUpdation') }}",
                type: 'POST',
                data: dataToSend,
                success: async function(response) {
                    if (response.success) {
                        getLabels_jsonlayout([{
                            id: response.message,
                            key: response.message
                        }], 'N').then((text) => {


                            passing_alert_value(
                                'Confirmation',
                                Object.values(text)[0],
                                'confirmation_alert',
                                'alert_header',
                                'alert_body',
                                'confirmation_alert'
                            );
                            $("#ok_button").off('click').on('click', function() {
                                setTimeout(() => {
                                    location.reload();
                                }, 200); // Adjust delay if needed
                            });
                        });
                    }
                },
                error: function(xhr) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.error == 401) {
                        handleUnauthorizedError();
                    } else {
                        getLabels_jsonlayout([{
                            id: response.message,
                            key: response.message
                        }], 'N').then((text) => {
                            let alertMessage = Object.values(text)[0] || "Error Occurred";
                            passing_alert_value('Confirmation', alertMessage, 'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                        });
                    }
                },
                complete: function() {
                    // Optionally, you can re-enable the button here if desired
                    $('#buttonaction').removeAttr('disabled');
                    $('#finalisebtn').removeAttr('disabled');
                    $('#savebtn').removeAttr('disabled');

                }
            });

        }
    }

    // Save Draft button click
    $("#buttonaction").on("click", function(event) {
        event.preventDefault();
        submitPendingInstForm('insert');
    });

    $("#finalisebtn").on("click", function(event) {
        event.preventDefault();
        $('#process_button').off('click').on('click', function(event) {
            event.preventDefault();
            $('#confirmation_alert').modal('hide');
            submitPendingInstForm('finalise');
        });

        passing_alert_value('Confirmation', "Are you sure you want to finalize?",
            'confirmation_alert',
            'alert_header', 'alert_body',
            'forward_alert');

    });
    $("#savebtn").on("click", function(event) {
        event.preventDefault();
        $('#process_button').off('click').on('click', function(event) {
            event.preventDefault();
            $('#confirmation_alert').modal('hide');
            submitPendingInstForm('finalise');
        });

        passing_alert_value('Confirmation', "Are you sure you want to finalize?",
            'confirmation_alert',
            'alert_header', 'alert_body',
            'forward_alert');

    });
</script>



@endsection