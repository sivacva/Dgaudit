@section('content')

@section('title', 'Work Allocation Report')
@extends('index2')
@include('common.alert')
@php
    $sessionchargedel = session('charge');
    //print_r($sessionchargedel);
    $deptcode = $sessionchargedel->deptcode;
    $make_dept_disable = $deptcode ? 'disabled' : '';

@endphp
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

<div class="row">
    <div class="col-12">


        <div class="card card_border">
            <div class="card-header card_header_color lang" key="">Liability Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive" id="tableshow">
                        <table id="LiabilityDetails"
                            class="table w-100 table-striped table-bordered display align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th>
                                    <th class="lang" key="">Category</th>
                                    <th class="lang" key="">SubCategory</th>
                                    <th class="lang" key="">Institution</th>
                                    <th class="lang" key="">Gist of Objection</th> 
                                    <th class="lang" key="">Liability Details</th>

                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div id='no_data' class='hide_this lang text-center' key="no_data">
                    <center class="lang" key="no_data">No Data Available</center>

                </div>
            </div>
        </div>

    </div>
</div>
<!-- Include jQuery and Bootstrap -->


    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <!-- Download Button Start -->

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <!-- select2 -->
    <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
    <script src="../assets/libs/select2/dist/js/select2.min.js"></script>
    <script src="../assets/js/forms/select2.init.js"></script>

<!-- Download Button End -->

<script>
    let table;
    let dataFromServer = [];

    var sessiondeptcode = ' <?php echo $deptcode; ?>';

    $(document).ready(function() {
        // $('#workallocationform')[0].reset();
        updateSelectColorByValue(document.querySelectorAll(".form-select"));

        var lang = getLanguage();
        initializeDataTable(lang);





    });


    $('#translate').change(function() {
        var lang = getLanguage('Y');
        // change_lang_for_page(lang);
        updateTableLanguage(lang);
        changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
            @json($updatebtn), @json($clearbtn));
        updateValidationMessages(getLanguage('Y'), 'workallocationform');
    });

    function initializeDataTable(language) {
        $.ajax({
            url: "{{ route('callcodecheck') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataSrc: "json",
            success: function(json) {
                            console.log("Success Response:", json);
                            if (json && json.length > 0) {
                                dataFromServer = json;
                                renderTable(language);
                                $('#tableshow').show();
                                $('#usertable_wrapper').show();
                                $('#no_data').hide();
                            } else {
                                $('#tableshow').hide();
                                $('#usertable_wrapper').hide();
                                $('#no_data').show();
                            }
                        },

            error: function() {
                $('#tableshow').hide();
                $('#no_data').show(); // Show "No Data Available" on error
            }
        });
    }



    function renderTable(language) {
        const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';

        if ($.fn.DataTable.isDataTable('#LiabilityDetails')) {
            $('#LiabilityDetails').DataTable().clear().destroy();
        }

        table = $('#LiabilityDetails').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": dataFromServer,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                            <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button>${meta.row + 1}
                        </div>`;
                    },
                    className: 'text-end',
                    type: "num"
                },
                 {
                    data: "deptesname",
                    title: columnLabels?.["deptesname"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.deptesname || '-';
                    }
                },
                {
                    data: "catename",
                    title: columnLabels?.["catename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.catename || '-';
                    }
                },
                {
                    data: "subcatename",
                    title: columnLabels?.["subcatename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.subcatename || '-';
                    }
                },
                 {
                    data: "instename",
                    title: columnLabels?.["instename"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.instename || '-';
                    }
                },
                {
                    data: "slipdetails",
                    title: columnLabels?.["slipdetails"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.slipdetails || '-';
                    }
                },
                {
                    data: "liabilities",
                    title: columnLabels?.["liabilities"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function(data, type, row) {
                        return row.liabilities || '-';
                    }
                }
            ],

            "initComplete": function(settings, json) {
                $("#LiabilityDetails").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        // const mobileColumns = ["majorworkallocationtypeename", "majorworkallocationtypetname", "statusflag"];
        // setupMobileRowToggle(mobileColumns);

        //    updatedatatable("en", "callforrecordstable", "Call for Records");
        updatedatatable(language, "LiabilityDetails"); // Title: "Call for Records"
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#LiabilityDetails')) {
            $('#LiabilityDetails').DataTable().clear().destroy();
        }
        renderTable(language);
    }
</script>


@endsection