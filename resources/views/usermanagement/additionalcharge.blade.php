@section('content')
    @extends('index2')
    @include('common.alert')
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">


    @php
        $sessionchargedel = session('charge');
        $roleTypeCode = $sessionchargedel->roletypecode;
        $deptcode = $sessionchargedel->deptcode;
        $regioncode = $sessionchargedel->regioncode;
        $distcode = $sessionchargedel->distcode;

        $dga_roletypecode = $DGA_roletypecode;
        $Dist_roletypecode = $Dist_roletypecode;
        $Re_roletypecode = $Re_roletypecode;
        $Ho_roletypecode = $Ho_roletypecode;
        $Admin_roletypecode = $Admin_roletypecode;

        $make_dept_disable = $deptcode ? 'disabled' : '';
        $make_deptdiv_show = $deptcode ? '' : 'hide_this';
        $make_region_disable = $regioncode ? 'disabled' : '';
        $make_regiondiv_disable = $regioncode ? '' : 'hide_this';
        $make_district_disable = $distcode ? 'disabled' : '';
        $make_districtdiv_disable = $distcode ? '' : 'hide_this';

    @endphp

    <?php
    use Carbon\Carbon;

    // Get today's date in dd/mm/yy format
    $today = Carbon::today()->format('d/m/y');
    ?>


    <style>
        /* Change the border color of the search box */
        .select2-container .select2-search__field {
            border-radius: 4px !important;
            height: 30px !important;
            padding: 5px;


        }



        .select2-container .select2-selection--single {
            font-size: 12px;
        }

        .select2-container .select2-results__option {
            font-size: 12px !important;
        }


        .select2-container .select2-selection--single .select2-selection__arrow {
            top: 50%;
            transform: translateY(-50%);


        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            margin-top: 20px !important;
        }

        body .select2-results__option {
            padding: 4px 10px;
        }

        .select2-selection--single .select2-selection__rendered {
            height: 30px !important;
            line-height: 36px !important;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            padding-right: 30px;
            position: relative;
        }

        /* Remove focus outline and blue border */
        .select2-container--default .select2-selection--single:focus {
            outline: none !important;
            border-color: #ced4da !important;
            /* Match Bootstrap default */
            box-shadow: none !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .select2-container--default .select2-selection--single .select2-selection__clear {
            position: absolute;
            right: 5px;
        }
    </style>


    <div class="row">
        <div class="col-12">

            <div class="card card_border">
                <div class="card-header card_header_color lang" key="additional_charge">Additional Charge</div>

                <div class="card-body">
                    <form id="assignchargeform" name="assignchargeform">
                        <input type="hidden" value="additionalcharge" id="page" name="page">
                        <div class="row">
                            <div class="col-md-4 mb-3" id="deptdiv">
                                <label class="form-label required lang" for="dept" key="dept">Department</label>

                                <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                    onchange="getroletypecode_basedondept('')" <?php echo $make_dept_disable; ?>>
                                    <option value="" data-name-en=" Select Department"
                                        data-name-ta="துறையைத் தேர்ந்தெடுக்கவும்">Select Department</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" for="roletypecode" key="roletype">Role Type</label>
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="roletypecode"
                                    name="roletypecode" onchange="settingform_basedonroletypcode('','','','','')">
                                    <option value=''
                                        data-name-en="Select Role Type"data-name-ta="களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும�">Select
                                        Role Type</option>

                                </select>
                            </div>

                            <div class="col-md-4 mb-3 <?php echo $make_regiondiv_disable; ?>" id="regiondiv">
                                <label class="form-label required lang" for="validationDefault01" key="region">Region
                                </label>
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="regioncode" name="regioncode"
                                    onchange="getvaluebasedon_regionroletype('', '', '', '', '')" <?php echo $make_region_disable; ?>>
                                    <option value="" data-name-en="Select a Region"
                                        data-name-ta="பகுதியை தேர்ந்தெடுக்கவும்">Select Region</option>
                                    @if ($regiondetails)
                                        {
                                        <option value="{{ $regiondetails['regioncode'] }}" selected>
                                            {{ $regiondetails['regionename'] }}
                                        </option>
                                        }
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4 mb-3 <?php echo $make_districtdiv_disable; ?>" id="distdiv">
                                <label class="form-label required lang" for="validationDefault01" key="district">District
                                </label>
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="distcode" name="distcode"
                                    onchange="getdistregioninst_basedondept('', '', '', '', 'institution', 'instmappingcode','')"
                                    <?php echo $make_district_disable; ?>>
                                    <option value="" data-name-en="Select a District"
                                        data-name-ta="மாவட்டத்தை தேர்ந்தெடுக்கவும்">Select District</option>
                                    @if ($distdetails)
                                        {
                                        <option value="{{ $distdetails['distcode'] }}" selected>
                                            {{ $distdetails['distename'] }}
                                        </option>
                                        }
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4 mb-3 hide_this" id="instdiv">
                                <label class="form-label required lang" for="validationDefault01" key="inst">Insitution
                                </label>
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="instmappingcode"
                                    name="instmappingcode" onchange="getdesignation_chargedet()">
                                    <option value="" data-name-en="Select a Institution"
                                        data-name-ta="நிறுவனத்தைத் தேர்ந்தெடுக்கவும்">Select Institution</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" for="validationDefault01"
                                    key="designation">Designation </label>
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="desigcode" name="desigcode"
                                    onchange="getchargedescription()">
                                    <option value='' data-name-en="Select Designation"
                                        data-name-ta="பதவியைத் தேர்ந்தெடுக்கவும்">Select Designation</option>
                                    @if (!empty($designation) && is_iterable($designation))
                                        @foreach ($designation as $department)
                                            <option value="{{ $department->desigcode }}">
                                                {{ $department->desigelname }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" for="validationDefault02" key="charge">Charge
                                </label>
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="chargeid" name="chargeid"
                                    onchange="getuserbasedonroletype()">
                                    <option value='' data-name-en="Select Charge"
                                        data-name-ta="பொறுப்பை தேர்வு செய்யவும்">Select Charge</option>


                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" for="validationDefaultUsername"
                                    key="user">User</label>
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="userid" name="userid">
                                    <option value="" data-name-en="Select User"
                                        data-name-ta="பயனரை தேர்வு செய்யவும்">Select User</option>
                                </select>

                            </div>



                            <div class="col-md-4 mb-3">
                                <label class="form-label lang" for="validationDefaultUsername"
                                    key="chargeFromDate">Charge From Date</label>
                                <input type="text" class="form-control" id ="cod" name="cod"
                                    value="<?php echo $today; ?>" disabled />
                            </div>

                        </div>

                        <label for="include_otherdept">
                            <input type="checkbox" id="include_otherdept" name="include_otherdept" onclick="show_otherdept(this)">
                        Include other deptment user 
                        </label>
                      


                        <div class="row">
                            <div class="col-md-4 mb-3 hide_this" id="otherdeptdiv" >
                                <label class="form-label required lang" for="otherdept" key="otherdept">Department</label>

                                <select class="form-select mr-sm-2 lang-dropdown " id="otherdept" name="otherdept"
                                    <?php echo $make_dept_disable; ?> onchange="getroletypecodeDesignation_basedonotherdept()" >
                                    <option value="" data-name-en=" Select Department"
                                        data-name-ta="துறையைத் தேர்ந்தெடுக்கவும்">Select Department</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3 hide_this" id="otherdeptroletypediv">
                                <label class="form-label required lang" for="roletypecode" key="roletype">Role Type</label>
                                <select class="form-select mr-sm-2 lang-dropdown " id="otherdept_roletypecode"
                                    name="otherdept_roletypecode" >
                                    <option value=''
                                        data-name-en="Select Role Type"data-name-ta="களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும�">Select
                                        Role Type</option>

                                </select>
                            </div>

                            <div class="col-md-4 mb-3 hide_this" id="otherdeptdesignationdiv">
                                <label class="form-label required lang" for="validationDefault01"
                                    key="designation">Designation </label>
                                <select class="form-select mr-sm-2 lang-dropdown " id="otherdept_desigcode" name="otherdept_desigcode"
                                onchange="getuserbasedonroletype()">
                                    <option value='' data-name-en="Select Designation"
                                        data-name-ta="பதவியைத் தேர்ந்தெடுக்கவும்">Select Designation</option>
                                
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 mx-auto text-center">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <button class="btn button_save mt-3" type="submit" action="insert" id="buttonaction"
                                name="buttonaction">Save</button>
                            <button type="button" class="btn btn-danger mt-3" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>



                    </form>

                    <div id='hideform' class='hide_this'>
                        <center>No Data Available</center>
                    </div>

                </div>
            </div>


            <div class="card card_border">
                <div class="card-header card_header_color lang" key="additionalChargedetails">Additional User Charge
                    Details</div>
                <div class="card-body"><br>
                    <div class="datatables">
                        <div class="table-responsive hide_this" id="tableshow">
                            <table id="additionalchargetable"
                                class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                                <thead>
                                    <tr>
                                        <th class="lang text-center" key="s_no" style="width:5%">S.No</th>
                                        <th class="text-center lang" style="width:25%" key="deptDetails">Department
                                            Details</th>
                                        <th class="text-center lang" style="width:9%" key="roletype">Roletype</th>
                                        <!-- <th>Region</th> -->
                                        <!-- <th>District</th>
                                                <th>Institution</th> -->
                                        <th class="text-center lang" style="width:9%" key="roleaction">RoleAction</th>
                                        <th class="text-center lang" style="width:12%" key="designation">Designation</th>
                                        <th class="text-center lang" style="width:12%" key="chargeDescription">Charge
                                            Description</th>
                                        <th class="text-center lang" style="width:25%" key="userdetails">User Details
                                        </th>

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

    <!-- <script src="../assets/js/vendor.min.js"></script>  -->

    <script src="../assets/js/jquery_3.7.1.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>

    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

    <script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <script src="../common/ajaxfn.js"></script>
    <!-- select2 -->
<!-- 
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script> -->

    <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
    <script src="../assets/libs/select2/dist/js/select2.min.js"></script>
    <script src="../assets/js/forms/select2.init.js"></script>
<!-- -------------------------------Download button-------------------- -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>


function show_otherdept(checkbox) {
            if (checkbox.checked) {
                $('#otherdeptdiv').show();
                $('#otherdeptdesignationdiv').show();
                $('#otherdeptroletypediv').show();
              
                $('#otherdeptuser_modal').modal("show");
            } else {
                $('#otherdeptdiv').hide();
                $('#otherdeptdesignationdiv').hide();
                $('#otherdeptroletypediv').hide();

                

            }
        }

        get_assignchargevalue()

        var session_roletypecode = '<?php echo $roleTypeCode; ?>';


        // function get_assignchargevalue() {
        //     $.ajax({
        //         url: '/get_assignchargevalue',
        //         type: 'POST',
        //         data: {
        //             _token: $('meta[name="csrf-token"]').attr('content')
        //         },
        //         success: function(response) {
        //             if (response.success && response.data) {
        //                 const {
        //                     dept,
        //                     roletype
        //                 } = response.data;




        //                 // Check if dept and roletype are arrays
        //                 if (Array.isArray(dept)) {
        //                     // alert(`Dept Count: ${dept.length}, RoleType Count: ${roletype.length}`);

        //                     if (dept.length > 0) {
        //                         // Check Role Type condition
        //                         if (
        //                             session_roletypecode === '<?php echo $dga_roletypecode; ?>' ||
        //                             session_roletypecode === '<?php echo $Admin_roletypecode; ?>'
        //                         ) {
        //                             // Populate dept dropdown
        //                             makedropdownempty('deptcode', 'Select Department');
        //                             let deptOptions =
        //                                 "<option value='' data-name-en='Select Department'  data-name-ta='துறையைத் தேர்ந்தெடுக்கவும்'>Select Department</option>";

        //                             dept.forEach(({
        //                                 deptcode: code,
        //                                 deptelname: name,
        //                                 depttlname: taname
        //                             }) => {
        //                                 if (code && name) {
        //                                     deptOptions +=
        //                                         `<option value="${code}" data-name-en="${name}" data-name-ta="${taname || name}">${name}</option>`;
        //                                 }
        //                             });
        //                             $("#deptcode").html(deptOptions);


        //                         } else {
        //                             session_deptcode = '<?php echo $deptcode; ?>';
        //                             // Populate dept dropdown
        //                             makedropdownempty('deptcode', 'Select Department');
        //                             let deptOptions = "";

        //                             dept.forEach(({
        //                                 deptcode: code,
        //                                 deptelname: name
        //                             }) => {
        //                                 if (code && name) {
        //                                     const isSelected = (code === session_deptcode) ?
        //                                         "selected" : "";
        //                                     deptOptions += `<option value="${code}">${name}</option>`;
        //                                 }
        //                             });

        //                             $("#deptcode").html(deptOptions);


        //                             // Populate roletype dropdown
        //                             makedropdownempty('roletype', 'Select Role Type');
        //                             let roleTypeOptions =
        //                                 "<option value=''  data-name-en='Select Role Type' data-name-ta='களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும�'>Select Role Type</option>";

        //                             roletype.forEach(({
        //                                 roletypecode: code,
        //                                 roletypeelname: name
        //                             }) => {
        //                                 if (code && name) {
        //                                     roleTypeOptions +=
        //                                         `<option value="${code}">${name}</option>`;
        //                                 }
        //                             });
        //                             $("#roletypecode").html(roleTypeOptions);
        //                         }

        //                         // Show or hide forms based on conditions
        //                         $('#hideform').hide();
        //                         $('#assignchargeform').show();
        //                     } else {
        //                         $('#hideform').show();
        //                         $('#assignchargeform').hide();
        //                     }
        //                 } else {
        //                     console.error("Invalid data format: 'dept' or 'roletype' is not an array.");
        //                 }
        //             }
        //         },
        //         error: function(xhr) {
        //             console.error(xhr);
        //             let errorMessage = 'An error occurred. Please try again.';

        //             if (xhr.responseText) {
        //                 try {
        //                     const response = JSON.parse(xhr.responseText);
        //                     errorMessage = response.message || errorMessage;
        //                 } catch (e) {
        //                     console.error("Error parsing error response:", e);
        //                 }
        //             }

        //             passing_alert_value(
        //                 'Alert',
        //                 errorMessage,
        //                 'confirmation_alert',
        //                 'alert_header',
        //                 'alert_body',
        //                 'confirmation_alert'
        //             );
        //         }
        //     });
        // }

        function get_assignchargevalue() {
            $.ajax({
                url: '/get_assignchargevalue',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success && response.data) {
                        const {
                            dept,
                            roletype
                        } = response.data;




                        // Check if dept and roletype are arrays
                        if (Array.isArray(dept)) {
                            // alert(`Dept Count: ${dept.length}, RoleType Count: ${roletype.length}`);

                            if (dept.length > 0) {
                                // Check Role Type condition
                                if (
                                    session_roletypecode === '<?php echo $dga_roletypecode; ?>' ||
                                    session_roletypecode === '<?php echo $Admin_roletypecode; ?>'
                                ) {
                                    // Populate dept dropdown
                                    makedropdownempty('deptcode', 'Select Department');
                                    makedropdownempty('otherdept', 'Select Department');
                                    let deptOptions =
                                        "<option value='' data-name-en='Select Department'  data-name-ta='துறையைத் தேர்ந்தெடுக்கவும்'>Select Department</option>";

                                    dept.forEach(({
                                        deptcode: code,
                                        deptelname: name,
                                        depttlname: taname
                                    }) => {
                                        if (code && name) {
                                            deptOptions +=
                                                `<option value="${code}" data-name-en="${name}" data-name-ta="${taname || name}">${name}</option>`;
                                        }
                                    });
                                    $("#deptcode").html(deptOptions);

                                  
                               
                                    $("#otherdept").html(deptOptions);


                                } else {
                                    session_deptcode = '<?php echo $deptcode; ?>';
                                    // Populate dept dropdown
                                    makedropdownempty('deptcode', 'Select Department');
                                    let deptOptions = "";

                                    dept.forEach(({
                                        deptcode: code,
                                        deptelname: name
                                    }) => {
                                        if (code && name) {
                                            const isSelected = (code === session_deptcode) ?
                                                "selected" : "";
                                            deptOptions += `<option value="${code}">${name}</option>`;
                                        }
                                    });

                                    $("#deptcode").html(deptOptions);


                                    // Populate roletype dropdown
                                    makedropdownempty('roletype', 'Select Role Type');
                                    let roleTypeOptions =
                                        "<option value=''  data-name-en='Select Role Type' data-name-ta='களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும�'>Select Role Type</option>";

                                    roletype.forEach(({
                                        roletypecode: code,
                                        roletypeelname: name
                                    }) => {
                                        if (code && name) {
                                            roleTypeOptions +=
                                                `<option value="${code}">${name}</option>`;
                                        }
                                    });
                                    $("#roletypecode").html(roleTypeOptions);
                                }

                                // Show or hide forms based on conditions
                                $('#hideform').hide();
                                $('#assignchargeform').show();
                            } else {
                                $('#hideform').show();
                                $('#assignchargeform').hide();
                            }
                        } else {
                            console.error("Invalid data format: 'dept' or 'roletype' is not an array.");
                        }
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                    let errorMessage = 'An error occurred. Please try again.';

                    if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch (e) {
                            console.error("Error parsing error response:", e);
                        }
                    }

                    passing_alert_value(
                        'Alert',
                        errorMessage,
                        'confirmation_alert',
                        'alert_header',
                        'alert_body',
                        'confirmation_alert'
                    );
                }
            });
        }


        var session_roletypecode = '<?php echo $roleTypeCode; ?>';



        // function getroletypecode_basedondept(deptcode, roletypecode) {
        //     const defaultOption = "<option value=''>Select Role Type</option>";
        //     const $dropdown = $("#roletypecode");

        //     // Get department code from DOM if not passed
        //     if (!deptcode) deptcode = $('#deptcode').val();

        //     if (deptcode) {
        //         // Clear the dropdown and set the default option
        //         $dropdown.html(defaultOption);



        //     } else {
        //         // Reset to default option if no department code is provided
        //         $dropdown.html(defaultOption);
        //     }
        // }

        function getroletypecodeDesignation_basedonotherdept(deptcode) {
    const lang = getLanguage();

    const defaultOption = `<option value="" data-name-en="Select Role Type" data-name-ta="களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும�">
                        ${lang === 'ta' ? 'களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும�' : 'Select Role Type'}
                    </option>`;

    const $dropdown = $("#otherdept_roletypecode");
    const $designationdropdown = $("#otherdept_desigcode");
    $designationdropdown.empty();

    // Get department code from DOM if not passed
    if (!deptcode) deptcode = $('#otherdept').val();

    if (deptcode) {
        // Clear the dropdown and set the default option
        $dropdown.html(defaultOption);

        $.ajax({
            url: '/getroletypecodeDesignation_basedonotherdept',
            type: 'POST',
            data: {
                deptcode: deptcode,
                'page': 'assigncharge',
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Handle role type options
                if (response.success && Array.isArray(response.roletypedel)) {
                    let options = defaultOption;

                    response.roletypedel.forEach(({ roletypecode: code, roletypeelname: name, roletypetlname: nameTa }) => {
                        if (code && name) {
                            const isSelected = (code === roletypecode) ? "selected" : "";
                            options += `<option value="${code}" ${isSelected} data-name-en="${name}" data-name-ta="${nameTa || name}">${name}</option>`;
                        }
                    });

                    $dropdown.html(options); // Populate role type dropdown

                    // Set the default "Select Designation" option in the designation dropdown
                    const defaultDesigOption = `<option value="" data-name-en="Select Designation" data-name-ta="பதவியை தேர்ந்தெடுக்கவும்">
                        ${lang === 'ta' ? 'பதவியை தேர்ந்தெடுக்கவும்' : 'Select Designation'}
                    </option>`;

                    // Clear previous options in the designation dropdown
                    $designationdropdown.html(defaultDesigOption);

                    // Handle designation options
                    if (response.success && Array.isArray(response.designation)) {
                        let desigOptions = '';
                        response.designation.forEach(item => {
                            // Use the designation code and names in the option tags
                            desigOptions += `<option value="${item.desigcode}" data-name-en="${item.desigelname}" data-name-ta="${item.desigtlname}">${lang === 'ta' ? item.desigtlname : item.desigelname}</option>`;
                        });
                        // Append designation options to the dropdown
                        $designationdropdown.append(desigOptions); 
                    } else {
                        console.error("No designations found in the response.");
                    }

                } else {
                    console.error("Invalid response or data format:", response);
                }
            },
            error: function(xhr) {
                console.log(xhr);
                let errorMessage = xhr.responseText;

                if (xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                    } catch (e) {
                        console.error("Error parsing error response:", e);
                    }
                }

                passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');
            }
        });

    } else {
        // Reset to default option if no department code is provided
        $dropdown.html(defaultOption);
    }
}


        // function getroletypecode_basedondept(deptcode, roletypecode) {
        //     const lang = getLanguage();

        //     const defaultOption = `<option value="" data-name-en="Select Role Type" data-name-ta="களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும�">
        //                 ${lang === 'ta' ? 'களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும�' : 'Select Role Type'}
        //             </option>`;

        //     const $dropdown = $("#roletypecode");

        //     //getDesignationBasedonDept(deptcode, roletypecode,'assigncharge','deptcode','desigcode');

        //     // Get department code from DOM if not passed
        //     if (!deptcode) deptcode = $('#deptcode').val();

        //     if (deptcode) {
        //         // Clear the dropdown and set the default option
        //         $dropdown.html(defaultOption);

        //         $.ajax({
        //             url: '/getroletypecode_basedondept',
        //             type: 'POST',
        //             data: {
        //                 deptcode: deptcode,
        //                 'page': 'assigncharge',
        //                 _token: $('meta[name="csrf-token"]').attr('content')
        //             },
        //             success: function(response) {
        //                 if (response.success && Array.isArray(response.data)) {
        //                     let options = defaultOption;

        //                     // Iterate through the roles and build options
        //                     response.data.forEach(({
        //                         roletypecode: code,
        //                         roletypeelname: name,
        //                         roletypetlname: nameTa

        //                     }) => {
        //                         if (code && name) {
        //                             const isSelected = (code === roletypecode) ? "selected" : "";
        //                             options +=
        //                                 `<option value="${code}" ${isSelected} data-name-en="${name}" data-name-ta="${nameTa || name}">${name}</option>`;
        //                         }
        //                     });

        //                     // Append the options to the dropdown
        //                     $dropdown.html(options);
        //                 } else {
        //                     console.error("Invalid response or data format:", response);
        //                 }
        //             },
        //             error: function(xhr) {
        //                 console.log(xhr);
        //                 let errorMessage = response.error;

        //                 if (xhr.responseText) {
        //                     try {
        //                         const response = JSON.parse(xhr.responseText);
        //                         errorMessage = response.message || errorMessage;
        //                     } catch (e) {
        //                         console.error("Error parsing error response:", e);
        //                     }
        //                 }

        //                 passing_alert_value('Alert', errorMessage, 'confirmation_alert',
        //                     'alert_header', 'alert_body', 'confirmation_alert');
        //             }
        //         });

        //     } else {
        //         // Reset to default option if no department code is provided
        //         $dropdown.html(defaultOption);
        //     }
        // }
        function getroletypecode_basedondept(deptcode, roletypecode) {
            const lang = getLanguage();

            const defaultOption = `<option value="" data-name-en="Select Role Type" data-name-ta="களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும�">
                        ${lang === 'ta' ? 'களத்தணிக்கை பங்கு நிலையை தேர்ந்தெடுக்கவும�': 'Select Role Type'}
                    </option>`;

            const $dropdown = $("#roletypecode");

            //getDesignationBasedonDept(deptcode, roletypecode,'assigncharge','deptcode','desigcode');

            // Get department code from DOM if not passed
            if (!deptcode) deptcode = $('#deptcode').val();

            if (deptcode) 
            {
                // Clear the dropdown and set the default option
                $dropdown.html(defaultOption);

                $.ajax({
                    url: '/getroletypecode_basedondept',
                    type: 'POST',
                    data: {
                        deptcode: deptcode,
                        'page': 'assigncharge',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success && Array.isArray(response.data)) {
                            let options = defaultOption;

                            // Iterate through the roles and build options
                            response.data.forEach(({
                                roletypecode: code,
                                roletypeelname: name,
                                roletypetlname: nameTa

                            }) => {
                                if (code && name) {
                                    const isSelected = (code === roletypecode) ? "selected" : "";
                                    options +=
                                        `<option value="${code}" ${isSelected} data-name-en="${name}" data-name-ta="${nameTa || name}">${name}</option>`;
                                }
                            });

                            // Append the options to the dropdown
                            $dropdown.html(options);


                             // Reset all options to be visible first
                             var selectedValue = $('#deptcode').val().trim(); // Get selected value from the first select box

                              // Reset all options to be visible first
                                $('#otherdept').find('option').each(function() {
                                    $(this).show();  // Make sure all options are visible
                                });

                                // Loop through each option in the second select box
                                $('#otherdept').find('option').each(function() {
                                    var optionValue = $(this).val().trim();  // Ensure no extra spaces

                                    // Skip empty option values
                                    if (!optionValue) {
                                        return; // Skip if optionValue is empty
                                    }

                                    // Extract only the department code part (before the first space or colon)
                                    var deptcodeFromOption = extractDeptCode(optionValue);
                                    var deptcodeFromSelected = extractDeptCode(selectedValue);

                                    if (deptcodeFromOption == deptcodeFromSelected) {

                                        $(this).prop('disabled', true); // Disable the option from the second select box
                                        $(this).hide();
                                    } else {
                                        $(this).prop('disabled', false); // Enable all other options
                                    }
                                });
                            

                            // Function to extract the department code (only the part before the colon or space)
                            function extractDeptCode(value) {
                                // Extract the first part before any space or colon, then trim it
                                return value.split(' ')[0].split(':')[0].trim();
                            }

                        } else {
                            console.error("Invalid response or data format:", response);
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr);
                        let errorMessage = response.error;

                        if (xhr.responseText) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                errorMessage = response.message || errorMessage;
                            } catch (e) {
                                console.error("Error parsing error response:", e);
                            }
                        }

                        passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                            'alert_header', 'alert_body', 'confirmation_alert');
                    }
                });

            } else {
                // Reset to default option if no department code is provided
                $dropdown.html(defaultOption);
            }
        }


        function settingform_basedonroletypcode(roletypecode, deptcode, regioncode, distcode, instmappingcode) {
            if (!roletypecode) roletypecode = $('#roletypecode').val();

            if (!deptcode) deptcode = $('#deptcode').val();

            // if ((roletypecode) && ((roletypecode == '<?php echo $Dist_roletypecode; ?>') || (roletypecode == '<?php echo $Re_roletypecode; ?>')))
            // {
            //     if((session_roletypecode == '<?php echo $dga_roletypecode; ?>')||(session_roletypecode == '<?php echo $Ho_roletypecode; ?>'))
            //     {
            //         makedropdownempty('regioncode', 'Select Region')
            //         getdistregioninst_basedondept(roletypecode, deptcode, regioncode, distcode, 'region', 'regioncode')
            //         $('#regiondiv').show();

            //         makedropdownempty('instmappingcode', 'Select Insittuion')
            //         $('#instdiv').show();

            //         if ((roletypecode == '<?php echo $Dist_roletypecode; ?>')) {
            //             // getdistregioninst_basedondept(roletypecode, deptcode, regioncode, distcode, 'region')
            //             makedropdownempty('distcode', 'Select District')
            //             $('#distdiv').show();
            //         } else {
            //             $('#distdiv').hide();
            //         }
            //     }

            //     if((session_roletypecode == '<?php echo $Dist_roletypecode; ?>')|| (session_roletypecode == '<?php echo $Re_roletypecode; ?>'))
            //     {
            //         makedropdownempty('instmappingcode', 'Select Insittuion')
            //         $('#instdiv').show();

            //         if((session_roletypecode == '<?php echo $Re_roletypecode; ?>'))
            //         {
            //             if(roletypecode == '<?php echo $Re_roletypecode; ?>')
            //             {
            //                 getdistregioninst_basedondept(roletypecode, deptcode, regioncode, '', 'institution', 'instmappingcode','')
            //             }
            //             else
            //             {
            //                 getdistregioninst_basedondept(roletypecode, deptcode, regioncode, '', 'district', 'distcode')
            //             }
            //         }

            //         }
            //         if((session_roletypecode == '<?php echo $Dist_roletypecode; ?>'))
            //         {
            //             makedropdownempty('instmappingcode', 'Select Insittuion')
            //             $('#instdiv').show();
            //             getdistregioninst_basedondept(roletypecode, deptcode, regioncode, '', 'institution', 'instmappingcode','')
            //         }
            // }
            // else {
            //     $('#distdiv').hide();
            //     $('#regiondiv').hide();
            //     $('#instdiv').hide();
            // }

            if (
                roletypecode &&
                (roletypecode == '<?php echo $Dist_roletypecode; ?>' || roletypecode == '<?php echo $Re_roletypecode; ?>')
            ) {

                if (
                    session_roletypecode == '<?php echo $dga_roletypecode; ?>' || session_roletypecode == '<?php echo $Admin_roletypecode; ?>' ||
                    session_roletypecode == '<?php echo $Ho_roletypecode; ?>'
                ) {
                    makedropdownempty('regioncode', 'Select Region');
                    getdistregioninst_basedondept(roletypecode, deptcode, regioncode, distcode, 'region', 'regioncode');
                    $('#regiondiv').show();

                    makedropdownempty('instmappingcode', lang === 'ta' ? 'நிறுவனத்தை தேர்ந்தெடுக்கவும்' :
                        'Select Institution');
                    $('#instdiv').show();

                    if (roletypecode == '<?php echo $Dist_roletypecode; ?>') {
                        makedropdownempty('distcode', lang === 'ta' ? 'மாவட்டத்தைத் தேர்ந்தெடுக்கவும்' : 'Select District');
                        $('#distdiv').show();
                    } else {
                        $('#distdiv').hide();
                    }
                }

                if (
                    session_roletypecode == '<?php echo $Dist_roletypecode; ?>' ||
                    session_roletypecode == '<?php echo $Re_roletypecode; ?>'
                ) {
                    makedropdownempty('instmappingcode', 'Select Institution');
                    $('#instdiv').show();

                    if (session_roletypecode == '<?php echo $Re_roletypecode; ?>') {
                        if (!regioncode) regioncode = $('#regioncode').val();
                        if (roletypecode == '<?php echo $Re_roletypecode; ?>') {
                            getdistregioninst_basedondept(
                                roletypecode, deptcode, regioncode, distcode, 'institution', 'instmappingcode',
                                instmappingcode
                            );
                        } else {
                            makedropdownempty('distcode', 'Select District');
                            $('#distdiv').show();
                            getdistregioninst_basedondept(
                                roletypecode, deptcode, regioncode, distcode, 'district', 'distcode'
                            );
                        }
                    } else if (session_roletypecode == '<?php echo $Dist_roletypecode; ?>') {
                        if (!regioncode) regioncode = $('#regioncode').val();
                        getdistregioninst_basedondept(
                            roletypecode, deptcode, regioncode, '', 'institution', 'instmappingcode', instmappingcode
                        );
                    }
                }
            } else {
                $('#distdiv').hide();
                $('#regiondiv').hide();
                $('#instdiv').hide();
                getdesignation_chargedet();

                makedropdownempty('desigcode', 'Select Designation');
                makedropdownempty('chargeid', 'Select Charge Description');
                makedropdownempty('userid', 'Select User');
            }



        }


        function getvaluebasedon_regionroletype(roletypecode, deptcode, regioncode, distcode) {
            if (!roletypecode) roletypecode = $('#roletypecode').val();
            if (!deptcode) deptcode = $('#deptcode').val();
            if (!regioncode) regioncode = $('#regioncode').val();


            if (roletypecode == '<?php echo $Re_roletypecode; ?>') {
                getdistregioninst_basedondept(roletypecode, deptcode, regioncode, '', 'institution', 'instmappingcode', '')
            }
            if (roletypecode == '<?php echo $Dist_roletypecode; ?>') {
                getdistregioninst_basedondept(roletypecode, deptcode, regioncode, '', 'district', 'distcode')
            }
        }

        // function getdistregioninst_basedondept(roletypecode, deptcode, regioncode, distcode, valuefor, valueforid,instmappingcode)
        // {
        //     if(valuefor == 'institution')
        //     {
        //         if (!roletypecode) roletypecode = $('#roletypecode').val();
        //         if (!deptcode) deptcode = $('#deptcode').val();
        //         if (!regioncode) regioncode = $('#regioncode').val();
        //         if (!distcode) distcode = $('#distcode').val();
        //     }

        //     const $dropdown = $("#" + valueforid);

        //     // Clear existing options and display a "Select" placeholder
        //     $dropdown.html('<option value="">Select</option>');

        //     if(roletypecode && (((valuefor != 'institution') && (roletypecode == '<?php echo $Re_roletypecode; ?>') && (deptcode)) || ((valuefor != 'institution') && (roletypecode == '<?php echo $Dist_roletypecode; ?>') && (deptcode) && (regioncode))
        //         && ((valuefor == 'institution') && (roletypecode == '<?php echo $Re_roletypecode; ?>') && (deptcode) && (regioncode) ) || ((valuefor == 'institution') && (roletypecode == '<?php echo $Re_roletypecode; ?>') && (deptcode) && (regioncode) && (distcode))
        //     ))
        //     {
        //         // Make the AJAX request
        //         $.ajax({
        //         url: '/getRegionDistInstBasedOnDept',
        //         type: 'POST',
        //         data: {
        //             roletypecode,
        //             deptcode,
        //             regioncode,
        //             distcode,
        //             valuefor,
        //             page : 'assigncharge',
        //             _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
        //         },
        //         success: function(response) {
        //             if (response.success && Array.isArray(response.data)) {

        //                 // Map the response data into <option> elements
        //                 const options = response.data.map(item => {
        //                     switch (valuefor) {
        //                         case 'region':
        //                             return `<option value="${item.regioncode}" ${item.regioncode === regioncode ? "selected" : ""}>${item.regionename}</option>`;
        //                         case 'district':
        //                             return `<option value="${item.distcode}" ${item.distcode === distcode ? "selected" : ""}>${item.distename}</option>`;
        //                         case 'institution':
        //                             return `<option value="${item.instmappingcode}" ${item.instmappingcode === instmappingcode ? "selected" : ""}>${item.instename}</option>`;

        //                             // return `<option value="${item.instmappingcode}">${item.instename}</option>`;
        //                         default:
        //                             return '';
        //                     }
        //                 }).join('');

        //                 // Append options to the dropdown
        //                 $dropdown.append(options || '<option value="">No data available</option>');
        //             } else {
        //                 console.error("Invalid response or no data:", response);
        //                 $dropdown.append('<option value="">No data available</option>');
        //             }
        //             updateSelectColorByValue(document.querySelectorAll(".form-select"));
        //         },
        //         error: function(xhr) {
        //             console.error("Error during AJAX request:", xhr);

        //             // Show error message and reset dropdown
        //             $dropdown.html('<option value="">Error loading data</option>');
        //             passing_alert_value(
        //                 'Alert',
        //                 'Error loading data. Please try again.',
        //                 'confirmation_alert',
        //                 'alert_header',
        //                 'alert_body',
        //                 'confirmation_alert'
        //             );
        //         }
        //     });
        // }

        // }


        function getdistregioninst_basedondept(roletypecode, deptcode, regioncode, distcode, valuefor, valueforid,
            instmappingcode) {
            // Default values for institution
            if (valuefor === 'institution') {
                roletypecode = roletypecode || $('#roletypecode').val();
                deptcode = deptcode || $('#deptcode').val();
                regioncode = regioncode || $('#regioncode').val();
                distcode = distcode || $('#distcode').val();
            }

            const $dropdown = $("#" + valueforid);
            let placeholderTextEn = '',
                placeholderTextTa = '';
            switch (valuefor) {
                case 'region':
                    placeholderTextEn = 'Select a Region';
                    placeholderTextTa = 'பகுதியை தேர்ந்தெடுக்கவும்';
                    break;
                case 'district':
                    placeholderTextEn = 'Select a District';
                    placeholderTextTa = 'மாவட்டத்தை தேர்ந்தெடுக்கவும்';
                    break;
                case 'institution':
                    placeholderTextEn = 'Select an Institution';
                    placeholderTextTa = 'நிறுவனத்தை தேர்ந்தெடுக்கவும்';
                    break;
                default:
                    placeholderTextEn = 'Select an Option';
                    placeholderTextTa = 'ஒரு விருப்பத்தை தேர்வு செய்';
            }

            $dropdown.html(`<option value="" data-name-en="${placeholderTextEn}" data-name-ta="${placeholderTextTa}">
                        ${lang === 'ta' ? placeholderTextTa : placeholderTextEn}
                    </option>`);

            // Simplify conditions for clarity
            const isValid = roletypecode && (
                (valuefor === 'region' && deptcode) ||
                (valuefor === 'district' && roletypecode === '<?php echo $Dist_roletypecode; ?>' && deptcode && regioncode) ||
                (valuefor === 'institution' && roletypecode === '<?php echo $Re_roletypecode; ?>' && deptcode && regioncode) ||
                (valuefor === 'institution' && roletypecode === '<?php echo $Dist_roletypecode; ?>' && deptcode && regioncode &&
                    distcode)
            );

            if (isValid) {
                // Make the AJAX request
                $.ajax({
                    url: '/getRegionDistInstBasedOnDept',
                    type: 'POST',
                    data: {
                        roletypecode,
                        deptcode,
                        regioncode,
                        distcode,
                        valuefor,
                        page: 'assigncharge',
                        _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                    },
                    success: function(response) {
                        if (response.success && Array.isArray(response.data)) {
                            // Map response data to options
                            const options = response.data.map(item => {
                                switch (valuefor) {
                                    case 'region':
                                        return `<option value="${item.regioncode}" data-name-en="${item.regionename}" data-name-ta="${item.regiontname}" ${item.regioncode === regioncode ? "selected" : ""}>${item.regionename}</option>`;
                                    case 'district':
                                        return `<option value="${item.distcode}" data-name-en="${item.distename}" data-name-ta="${item.disttname}" ${item.distcode === distcode ? "selected" : ""}>${item.distename}</option>`;
                                    case 'institution':
                                        return `<option value="${item.instmappingcode}"  data-name-en="${item.instename}" data-name-ta="${item.insttname}" ${item.instmappingcode === instmappingcode ? "selected" : ""}>${item.instename}</option>`;
                                    default:
                                        return '';
                                }
                            }).join('');

                            // Append options or show fallback message
                            $dropdown.append(options || '<option value="">No data available</option>');
                        } else {
                            console.error("Invalid response or no data:", response);
                            $dropdown.append('<option value="">No data available</option>');
                        }
                        updateSelectColorByValue(document.querySelectorAll(".form-select"));
                    },
                    error: function(xhr) {
                        console.error("Error during AJAX request:", xhr);
                        $dropdown.html('<option value="">Error loading data</option>');
                        passing_alert_value(
                            'Alert',
                            'Error loading data. Please try again.',
                            'confirmation_alert',
                            'alert_header',
                            'alert_body',
                            'confirmation_alert'
                        );
                    }
                });
            } else {
                // console.warn('Validation failed: Missing required parameters.');
                // $dropdown.append('<option value="">Invalid parameters</option>');
                makedropdownempty('desigcode', 'Select Designation');
                makedropdownempty('chargeid', 'Select Charge Description');
                makedropdownempty('userid', 'Select User');

            }
        }


        function getdesignation_chargedet() {
            const deptcode = $('#deptcode').val();
            const roletypecode = $('#roletypecode').val();
            const regioncode = $('#regioncode').val();
            const distcode = $('#distcode').val();
            const instmappingcode = $('#instmappingcode').val();
            const $dropdown = $('#desigcode'); // Assuming the dropdown ID is `desigcode`

            // Clear dropdown and show loading
            // $dropdown.html('<option value="">Select</option>');


            // Validation conditions
            const isValid = roletypecode && (
                (roletypecode === '<?php echo $Re_roletypecode; ?>' && deptcode && regioncode && instmappingcode) ||
                (roletypecode === '<?php echo $Dist_roletypecode; ?>' && deptcode && regioncode && distcode && instmappingcode) ||
                (roletypecode === '<?php echo $Ho_roletypecode; ?>' && deptcode)
            );

            if (isValid) {
                $.ajax({
                    url: '/getdesignation_fromchargedet',
                    type: 'POST',
                    data: {
                        deptcode: deptcode,
                        roletypecode: roletypecode,
                        regioncode: regioncode,
                        distcode: distcode,
                        instmappingcode: instmappingcode,
                        _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                    },
                    success: function(response) {
                        $dropdown.empty(); // Clear previous options
                        $dropdown.html(
                            `<option value="" data-name-en="Select Designation" data-name-ta="பதவியை தேர்ந்தெடுக்கவும்">${lang === 'ta' ? 'பதவியை தேர்ந்தெடுக்கவும்' : 'Select Designation'}</option>`
                            );


                        if (response.success && Array.isArray(response.data)) {
                            // Map response data to dropdown options
                            const options = response.data.map(item =>
                                `<option value="${item.desigcode}" data-name-en="${item.desigelname}" data-name-ta="${item.desigtlname}">${item.desigelname}</option>`
                            ).join('');

                            $dropdown.append(options || '<option value="">No data available</option>');
                        } else {
                            console.error("Invalid response or no data:", response);
                            $dropdown.append('<option value="">No data available</option>');
                        }

                        // Update select color
                        updateSelectColorByValue(document.querySelectorAll(".form-select"));
                    },
                    error: function(xhr) {
                        console.error("Error during AJAX request:", xhr);

                        $dropdown.html('<option value="">Error loading data</option>');
                        passing_alert_value(
                            'Alert',
                            'Error loading data. Please try again.',
                            'confirmation_alert',
                            'alert_header',
                            'alert_body',
                            'confirmation_alert'
                        );
                    }
                });
            } else {
                // console.warn('Validation failed: Missing required parameters.');
                // $dropdown.html('<option value="">Invalid parameters</option>');
            }
        }



        function getchargedescription() {
            const deptcode = $('#deptcode').val();
            const roletypecode = $('#roletypecode').val();
            const regioncode = $('#regioncode').val();
            const distcode = $('#distcode').val();
            const instmappingcode = $('#instmappingcode').val();
            const desigcode = $('#desigcode').val();
            const $dropdown = $('#chargeid'); // Assuming the dropdown ID is `desigcode`

            // Clear the dropdown before making the AJAX call
            $dropdown.html(
                `<option value='' data-name-en='Loading...' data-name-ta='ஏற்றுகிறது...'>${lang === 'ta' ? 'ஏற்றுகிறது...' : 'Loading...'}</option>`
                );


            if ((roletypecode) && ((roletypecode == '<?php echo $Ho_roletypecode; ?>' && deptcode) ||
                    (roletypecode == '<?php echo $Re_roletypecode; ?>' && deptcode && regioncode && instmappingcode) ||
                    (roletypecode == '<?php echo $Dist_roletypecode; ?>' && deptcode && regioncode && distcode && instmappingcode)) &&
                desigcode) {
                $.ajax({
                    url: '/getchargedescription',
                    type: 'POST',
                    data: {
                        deptcode: deptcode,
                        roletypecode: roletypecode,
                        regioncode: regioncode,
                        distcode: distcode,
                        instmappingcode: instmappingcode,
                        desigcode: desigcode,
                        _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                    },
                    success: function(response) {
                        const lang = getLanguage();
                        $dropdown.empty(); // Clear previous options
                        $dropdown.html(
                            `<option value='' data-name-en='Select Charge' data-name-ta='பொறுப்பை தேர்வு செய்யவும்'>${lang === 'ta' ? 'பொறுப்பை தேர்வு செய்யவும்' : 'Select Charge'}</option>`
                            );

                        if (response.success && Array.isArray(response.data)) {
                            // Map the response data into <option> elements
                            const options = response.data.map(item => {
                                return `<option value="${item.chargeid}">${item.chargedescription}</option>`;
                            }).join('');

                            // Append options to the dropdown
                            $dropdown.append(options || '<option value="">No data available</option>');
                        } else {
                            console.error("Invalid response or no data:", response);
                            $dropdown.append('<option value="">No data available</option>');
                        }

                        // Update select color
                        updateSelectColorByValue(document.querySelectorAll(".form-select"));
                    },
                    error: function(xhr) {
                        console.error("Error during AJAX request:", xhr);

                        // Show error message and reset dropdown
                        $dropdown.html('<option value="">Error loading data</option>');
                        passing_alert_value(
                            'Alert',
                            'Error loading data. Please try again.',
                            'confirmation_alert',
                            'alert_header',
                            'alert_body',
                            'confirmation_alert'
                        );
                    }
                });
            }




        }


        // function getuserbasedonroletype() {
        //     const desigcode = $('#desigcode').val();
        //     const distcode = $('#distcode').val();
        //     const roletypecode = $('#roletypecode').val();
        //     const regioncode = $('#regioncode').val();
        //     const chargeid = $('#chargeid').val();
        //     const $dropdown = $('#userid'); // Assuming the dropdown ID is `desigcode`

        //     if ((roletypecode) && ((roletypecode == '<?php echo $Ho_roletypecode; ?>' && deptcode) ||
        //             (roletypecode == '<?php echo $Re_roletypecode; ?>' && deptcode && regioncode && instmappingcode) ||
        //             (roletypecode == '<?php echo $Dist_roletypecode; ?>' && deptcode && regioncode && distcode && instmappingcode)) &&
        //         desigcode && chargeid)

        //     {
        //         // Clear the dropdown before making the AJAX call
        //         $dropdown.html(
        //             `<option value='' data-name-en='Loading...' data-name-ta='ஏற்றுகிறது...'>${lang === 'ta' ? 'ஏற்றுகிறது...' : 'Loading...'}</option>`
        //             );

        //         $.ajax({
        //             url: '/getuserbasedonroletype',
        //             type: 'POST',
        //             data: {
        //                 desigcode: desigcode,
        //                 distcode: distcode,
        //                 roletypecode: roletypecode,
        //                 regioncode: regioncode,
        //                 chargeid: chargeid,
        //                 'page': 'additionalcharge',
        //                 _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
        //             },
        //             success: function(response) {
        //                 const lang = getLanguage();

        //                 $dropdown.empty(); // Clear previous options
        //                 $dropdown.html(`<option value="" data-name-en="Select User" data-name-ta="பயனரை தேர்ந்தெடுக்கவும்">
        //                     ${lang === "ta" ? "பயனரை தேர்ந்தெடுக்கவும்" : "Select User"}
        //                 </option>`);

        //                 if (response.success && Array.isArray(response.data) && response.data.length > 0) {
        //                     // Map the response data into <option> elements
        //                     const options = response.data.map(item => {
        //                         return `<option value="${item.deptuserid}" data-name-en="${item.username}" data-name-ta="${item.usertamilname}>${item.username}</option>`;
        //                     }).join('');

        //                     // Append options to the dropdown
        //                     //  $dropdown.append(options || '<option value="">No data available</option>');
        //                     $dropdown.append(options);

        //                 } else {
        //                     // console.error("Invalid response or no data:", response);
        //                     // $dropdown.append('<option value="">No data available</option>');
        //                     getLabels_jsonlayout([{
        //                             id: 'AssignChargeDesig',
        //                             key: 'AssignChargeDesig'
        //                         }], 'N').then((text) => {
        //                             passing_alert_value('Confirmation', text.AssignChargeDesig, 'confirmation_alert',
        //                                 'alert_header', 'alert_body',
        //                                 'confirmation_alert');
        //                         });
        //                 }

        //                 // Update select color
        //                 updateSelectColorByValue(document.querySelectorAll(".form-select"));
        //             },
        //             error: function(xhr) {
        //                 console.error("Error during AJAX request:", xhr);

        //                 // Show error message and reset dropdown
        //                 $dropdown.html('<option value="">Error loading data</option>');
        //                 passing_alert_value(
        //                     'Alert',
        //                     'Error loading data. Please try again.',
        //                     'confirmation_alert',
        //                     'alert_header',
        //                     'alert_body',
        //                     'confirmation_alert'
        //                 );
        //             }
        //         });
        //     }


        // }
        function getuserbasedonroletype() {

include_otherdeptment   =   'N';
otherdeptcode   =   '';
otherdept_roletypecode   =   '';
otherdept_desigcode=   '';

// Check if a checkbox is checked using :checked selector
if ($('#include_otherdept').is(':checked')) {
    include_otherdeptment   =   'Y';
    otherdeptcode   =   $('#otherdept').val();
    otherdept_roletypecode   =   $('#otherdept_roletypecode').val();
    otherdept_desigcode   =   $('#otherdept_desigcode').val();
    if(!(otherdeptcode))
    {
        alert('select the department');
        return;
    }

} 

const lang = getLanguage();

const desigcode = $('#desigcode').val();
const deptcode = $('#deptcode').val();
const distcode = $('#distcode').val();
const roletypecode = $('#roletypecode').val();
const regioncode = $('#regioncode').val();
const chargeid = $('#chargeid').val();
const $dropdown = $('#userid'); // Assuming the dropdown ID is `desigcode`

if ((roletypecode) && ((roletypecode == '<?php echo $Ho_roletypecode; ?>' && deptcode) ||
        (roletypecode == '<?php echo $Re_roletypecode; ?>' && deptcode && regioncode && instmappingcode) ||
        (roletypecode == '<?php echo $Dist_roletypecode; ?>' && deptcode && regioncode && distcode && instmappingcode)) &&
    desigcode && chargeid)

{
    // Clear the dropdown before making the AJAX call
    $dropdown.html(
        `<option value='' data-name-en='Loading...' data-name-ta='ஏற்றுகிறது...'>${lang === 'ta' ? 'ஏற்றுகிறது...' : 'Loading...'}</option>`
        );

    $.ajax({
        url: '/getuserbasedonroletype',
        type: 'POST',
        data: {
            deptcode: deptcode,
            desigcode: desigcode,
            distcode: distcode,
            roletypecode: roletypecode,
            regioncode: regioncode,
            chargeid: chargeid,
            'page': 'additionalcharge',
            include_otherdeptment   :  include_otherdeptment,
            otherdeptcode   :   otherdeptcode,
            otherdept_desigcode :   otherdept_desigcode,
            otherdept_roletypecode : otherdept_roletypecode,                        
            _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
        },
        success: function(response) {
            const lang = getLanguage();

            $dropdown.empty(); // Clear previous options
            $dropdown.html(`<option value="" data-name-en="Select User" data-name-ta="பயனரை தேர்ந்தெடுக்கவும்">
                ${lang === "ta" ? "பயனரை தேர்ந்தெடுக்கவும்" : "Select User"}
            </option>`);

            // if (response.success && Array.isArray(response.data) && response.data.length > 0) {
            //     // Map the response data into <option> elements
            //     const options = response.data.map(item => {
            //         return `<option value="${item.deptuserid}" data-name-en="${item.username}" data-name-ta="${item.usertamilname}>${item.username}</option>`;
            //     }).join('');

            //     // Append options to the dropdown
            //     //  $dropdown.append(options || '<option value="">No data available</option>');
            //     $dropdown.append(options);

            // } 
            if (response.success && Array.isArray(response.data)) {
        // Map the response data to <option> elements
        const options = response.data.map(item => {
            return `<option value="${item.deptuserid}">${item.username}</option>`;
        }).join('');

        // Append the options to the dropdown
        $dropdown.append(options || '<option value="">No data available</option>');
    } 
            else {
                // console.error("Invalid response or no data:", response);
                // $dropdown.append('<option value="">No data available</option>');
                getLabels_jsonlayout([{
                        id: 'AssignChargeDesig',
                        key: 'AssignChargeDesig'
                    }], 'N').then((text) => {
                        passing_alert_value('Confirmation', text.AssignChargeDesig, 'confirmation_alert',
                            'alert_header', 'alert_body',
                            'confirmation_alert');
                    });
            }

            // Update select color
            updateSelectColorByValue(document.querySelectorAll(".form-select"));
        },
        error: function(xhr) {
            console.error("Error during AJAX request:", xhr);

            // Show error message and reset dropdown
            $dropdown.html('<option value="">Error loading data</option>');
            passing_alert_value(
                'Alert',
                'Error loading data. Please try again.',
                'confirmation_alert',
                'alert_header',
                'alert_body',
                'confirmation_alert'
            );
        }
    });
}


}
        ///////////////////////////////////////  Fetching Data ///////////////////////////////////////

        $(document).ready(function() {
            $('#assignchargeform')[0].reset();
            updateSelectColorByValue(document.querySelectorAll(".form-select"));

            // Load the initial language and initialize the DataTable
            const lang = window.localStorage.getItem('lang') || 'en'; // Default to 'en' if no language is set
            initializeDataTable(lang);

            // Change event for language selection dropdown
            $('#translate').change(function() {
                var lang = getLanguage('Y');
                changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
                    @json($updatebtn), @json($clearbtn));

                updateTableLanguage(lang);
                updateValidationMessages(getLanguage('Y'), 'assignchargeform');
            });
        });


        function initializeDataTable(language) {
            $.ajax({
                url: "/fetchuserchargeData",
                type: "POST",
                data: {
                    page: 'additionalcharge'
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(json) {
                    if (json.data && json.data.length > 0) {
                        $('#tableshow').show();
                        $('#usertable_wrapper').show();
                        $('#no_data').hide();
                        dataFromServer = json.data;
                        renderTable(language);
                    } else {
                        $('#tableshow').hide();
                        $('#usertable_wrapper').hide();
                        $('#no_data').show();
                    }
                }
            });
        }



        // function renderTable(language) {
        //     const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';
        //     const designationColumn = language === 'ta' ? 'desigtsname' : 'desigesname';

        //     if ($.fn.DataTable.isDataTable('#additionalchargetable')) {
        //         $('#additionalchargetable').DataTable().clear().destroy();
        //     }

        //     table = $('#additionalchargetable').DataTable({
        //         "processing": true,
        //         "serverSide": false,
        //         "lengthChange": false,
        //         "data": dataFromServer,
        //         // "columns": [
        //         //     {
        //         //         "data": null,
        //         //         "render": function(data, type, row, meta) {
        //         //             return meta.row + 1;
        //         //         }
        //         //     },
        //         //     { "data": departmentColumn },
        //         //     { "data": designationColumn },
        //         //     {
        //         //         "data": "username",
        //         //         "render": function(data, type, row) {
        //         //             let dob = row.dob ? new Date(row.dob).toLocaleDateString('en-GB') : "N/A";
        //         //             return `<b>Name</b>: ${data} <br> <small><b>IFHRMS No : </b>${row.ifhrmsno}</small> <br> <small><b>DOB :</b> ${dob}</small>`;
        //         //         }
        //         //     },
        //         //     { "data": "email" },
        //         //     { "data": "mobilenumber" },
        //         //     {
        //         //         "data": "encrypted_userid",
        //         //         "render": function(data, type, row) {
        //         //             return `<center><a class="btn editicon edit_user" id="${data}"><i class="ti ti-edit fs-4"></i></a></center>`;
        //         //         }
        //         //     }
        //         // ]
        //         columns: [{
        //             data: null,
        //             render: (_, __, ___, meta) => meta.row + 1, // Serial number column
        //             className: 'text-end' // Align to the right
        //         },
        //         // {
        //         //     data: "deptesname"
        //         // },
        //         {
        //            "data": "deptesname",
        //             "render": function(data, type, row) {
        //                 // Initialize the text variable
        //                 let text = "";

        //                 // Convert data into a formatted string
        //                 if (data) {
        //                     text += `<b>Department :</b> ${data}`;
        //                 }
        //                 if (row.regionename) {
        //                     text += `<br><b>Region :</b> ${data}`;
        //                 }
        //                 if (row.distename) {
        //                     text += `<br><b>District :</b> ${row.distename}`;
        //                 }
        //                 if (row.instename) {
        //                     text += `<br><b>Institution :</b> ${row.instename}`;
        //                 }

        //                 // Return the constructed HTML
        //                 return text;
        //             }
        //         },
        //         {
        //             data: "roletypeelname"
        //         },
        //         // {
        //         //    "data": "regionename",
        //         //     "render": function(data, type, row) {
        //         //         // Initialize the text variable
        //         //         let text = "";

        //         //         // Convert data into a formatted string
        //         //         if (data) {
        //         //             text = `<b>Region :</b> ${data}`;
        //         //         }
        //         //         if (row.distename) {
        //         //             text += `<br><b>District :</b> ${row.distename}`;
        //         //         }
        //         //         if (row.instename) {
        //         //             text += `<br><b>Institution :</b> ${row.instename}`;
        //         //         }

        //         //         // Return the constructed HTML
        //         //         return text;
        //         //     }


        //         // {
        //         //     data: "distename"
        //         // },
        //         // {
        //         //     data: "instename"
        //         // },
        //         {
        //             data: "roleactionelname"
        //         },
        //         {
        //             data: "desigesname"
        //         },
        //         {
        //             data: "chargedescription"
        //         },
        //         {
        //             "data": "username",
        //             "render": function(data, type, row) {
        //                 // Convert DOB to dd-mm-yyyy format
        //                 return `<b>Name :</b> ${data} <br> <small><b>IFHRMS No : </b>${row.ifhrmsno}</small> <br> <small><b>Email :</b> ${row.email}</small>`;
        //             }
        //         }
        //     ]
        //     });
        // }
        function renderTable(language) {
        const departmentColumn = language === 'ta' ? 'depttsname' : 'deptesname';
        const designationColumn = language === 'ta' ? 'desigtsname' : 'desigesname';
        const regionColumn = language === 'ta' ? 'regiontname' : 'regionename';
        const districtColumn = language === 'ta' ? 'disttname' : 'distename';
        const institutionColumn = language === 'ta' ? 'insttname' : 'instename';
        const roletypeColumn = language === 'ta' ? 'roletypetlname' : 'roletypeelname';
        const roleactionColumn = language === 'ta' ? 'roleactiontlname' : 'roleactionelname';
                // Destroy and reinitialize DataTable if it's already initialized
                if ($.fn.DataTable.isDataTable('#additionalchargetable')) {
                    $('#additionalchargetable').DataTable().clear().destroy();
                }

                // Initialize the DataTable
                table = $('#additionalchargetable').DataTable({
                    processing: true,
                    serverSide: false,
                    lengthChange: false,
                    data: dataFromServer,
                    columns: [
                                {
                                    data: null,
                                    render: function (data, type, row, meta) {
                                        return `<div>
                                                    <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button>${meta.row + 1}
                                                </div>`;
                                    },
                                    className: 'text-end',
                                    type: "num"
                                },
                                {
    data: departmentColumn, // Dynamically chosen column name
    render: function (data, type, row) {
        // ✅ Debugging: Check what keys exist in the row
     

        // ✅ Define translations dynamically
        const translations = {
            en: { 
                department: "Department", 
                region: "Region", 
                district: "District", 
                institution: "Institution" 
            },
            ta: { 
                department: "துறை", 
                region: "மண்டலம்", 
                district: "மாவட்டம்", 
                institution: "நிறுவனம்" 
            }
        };

        // ✅ Ensure the correct language selection
        const lang = language === "ta" ? "ta" : "en"; 

        // ✅ Fix: Check if Tamil column exists, else fallback to English column
        let departmentValue = row[departmentColumn] || row['deptesname'] || "-";
        let regionValue = row[regionColumn] || row['regionename'] || "-";
        let districtValue = row[districtColumn] || row['distename'] || "-";
        let institutionValue = row[institutionColumn] || row['instename'] || "-";

        return `<b>${translations[lang].department}:</b> ${departmentValue} <br> 
                <small><b>${translations[lang].region}:</b> ${regionValue}</small> <br> 
                <small><b>${translations[lang].district}:</b> ${districtValue}</small> <br> 
                <small><b>${translations[lang].institution}:</b> ${institutionValue}</small>`;
    },
    className: 'text-wrap text-start'
},


                {
                    data: roletypeColumn,
                    title: columnLabels?.[roletypeColumn]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function (data, type, row) {
                        return row[roletypeColumn] || '-';
                    }
                },
                {
                    data: roleactionColumn,
                    title: columnLabels?.[roleactionColumn]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function (data, type, row) {
                        return row[roleactionColumn] || '-';
                    }
                },
                {
                    data: designationColumn,
                    title: columnLabels?.[designationColumn]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function (data, type, row) {
                        return row[designationColumn] || '-';
                    }
                },
                {
                    data: "chargedescription",
                    title: columnLabels?.["chargedescription"]?.[language],
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                    render: function (data, type, row) {
                        return row.chargedescription || '-';
                    }
                },
                {
                    data: "username",
                    render: function (data, type, row) {
                        let dob = row.dob ? new Date(row.dob).toLocaleDateString('en-GB') : "N/A";

                        // Define translations based on language selection
                        const translations = {
                            en: { name: "Name", ifhrmsno: "IFHRMS No", email: "Email" },
                            ta: { name: "பெயர்", ifhrmsno: "IFHRMS எண்", email: "மின்னஞ்சல் முகவரி" }
                        };

                        const lang = language === "ta" ? "ta" : "en"; // Ensure fallback to English if not Tamil

                        return `<b>${translations[lang].name}</b>: ${data} <br> 
                                <small><b>${translations[lang].ifhrmsno} : </b>${row.ifhrmsno}</small> <br> 
                                <small><b>${translations[lang].email} :</b> ${row.email}</small>`;
                    },
                    className: "d-none d-md-table-cell lang extra-column text-wrap",
                }
            ],
                "columnDefs": [{
                        render: function(data) {
                            return "<div class='text-wrap width-200'>" + data + "</div>";
                        },
                        targets: 1
                    },
                    {
                        render: function(data) {
                            return "<div class='text-wrap width-100'>" + data + "</div>";
                        },
                        targets: [2, 3] // Set width for columns
                    }
                ],
                });
                const mobileColumns = [
            roletypeColumn,
            roleactionColumn,
            designationColumn,
            "chargedescription",
            "username",
            "ifhrmsno",
            "email"
        ];

        setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "additionalchargetable"); 
            }
          function exportToExcel(tableId, language) {
    let table = $(`#${tableId}`).DataTable(); // Get DataTable instance

    // ✅ Get translated title dynamically
    let titleKey = `${tableId}_title`;
    let translatedTitle = dataTables[language]?.datatable?.[titleKey] || "Default Title";
    let safeSheetName = translatedTitle.substring(0, 31);
    // ✅ Fetch column headers from JSON layout
    let dtText = dataTables[language]?.datatable || dataTables["en"].datatable;

    // ✅ Column Mapping (for language-specific keys)
    const columnMap = {
        department: language === 'ta' ? 'depttsname' : 'deptesname',
        region: language === 'ta' ? 'regiontname' : 'regionename',
        district: language === 'ta' ? 'disttname' : 'distename',
        institution: language === 'ta' ? 'insttname' : 'instename',
        designation: language === 'ta' ? 'desigtsname' : 'desigesname',
        chargedescription: "chargedescription", // Fixed mapping issue
        username: "username",
        ifhrmsno: "ifhrmsno",
        email: "email",
        roletype: language === 'ta' ? 'roletypetlname' : 'roletypeelname',
        roleaction: language === 'ta' ? 'roleactiontlname' : 'roleactionelname'
    };

    // ✅ Define Headers Properly
    let headers = [
        { header: dtText["department"] || "Department", key: "department" },
        { header: dtText["Region"] || "Region", key: "region" },
        { header: dtText["District"] || "District", key: "district" },
        { header: dtText["Institution"] || "Institution", key: "Institution" },
        { header: dtText["designation"] || "Designation", key: "designation" },
        { header: dtText["chargedescription"] || "Charge Description", key: "chargedescription" },
        { header: dtText["username"] || "Name", key: "username" },
        { header: dtText["ifhrmsno"] || "IFHRMS No", key: "ifhrmsno" },
        { header: dtText["Email"] || "Email", key: "email" },
        { header: dtText["roletype"] || "Role Type", key: "roletype" },
        { header: dtText["roleaction"] || "Role Action", key: "roleaction" }
    ];

    // ✅ Extract Data from Table
    let rawData = table.rows({ search: 'applied' }).data().toArray();

    let excelData = rawData.map(row => {
        let button = $(row[0]).find("button.toggle-row");
        let dataRow = button.attr("data-row");
        let rowData = dataRow ? JSON.parse(dataRow.replace(/&quot;/g, '"')) : {};

        return {
            department: rowData[columnMap.department] || "-",
            region: rowData[columnMap.region] || "-",
            district: rowData[columnMap.district] || "-",
            institution: rowData[columnMap.institution] || "-",
            designation: rowData[columnMap.designation] || "-",
            chargedescription: rowData[columnMap.chargedescription] || "-",
            username: rowData[columnMap.username] || "-",
            ifhrmsno: rowData[columnMap.ifhrmsno] || "-",
            email: rowData[columnMap.email] || "-",
            roletype: rowData[columnMap.roletype] || "-",
            roleaction: rowData[columnMap.roleaction] || "-"
        };
    });

    if (excelData.length === 0) {
        alert("No data available for export!");
        return;
    }

    // ✅ Create Workbook and Worksheet
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet([]);

    // ✅ Add Headers in Separate Columns (Avoid Merging Issues)
    XLSX.utils.sheet_add_aoa(ws, [headers.map(h => h.header)], { origin: "A1" });
    
    // ✅ Ensure Headers Align with Data
    XLSX.utils.sheet_add_json(ws, excelData, { skipHeader: true, origin: "A2" });

    XLSX.utils.book_append_sheet(wb, ws, safeSheetName);
    XLSX.writeFile(wb, `${safeSheetName}_${language}.xlsx`);
}


// Utility functions to extract text & clean up data
function extractText(html) {
    return $("<div>").html(html).text().trim();
}

function extractIfhrms(html) {
    let match = html.match(/<small><b>IFHRMS No : <\/b>(\d+)<\/small>/);
    return match ? match[1] : "-";
}

        function updateTableLanguage(language) {
            if ($.fn.DataTable.isDataTable('#usertable')) {
                $('#additionalchargetable').DataTable().clear().destroy();
            }
            renderTable(language);
        }

        ///////////////////////////////////////  Fetching Data ///////////////////////////////////////

        jsonLoadedPromise.then(() => {
            const language = window.localStorage.getItem('lang') || 'en';
            var validator = $("#assignchargeform").validate({
                rules: {
                    deptcode: {
                        required: true,
                    },
                    roletypecode: {
                        required: true
                    },
                    desigcode: {
                        required: true
                    },
                    chargeid: {
                        required: true
                    },
                    roleactioncode: {
                        required: true
                    },
                    regioncode: {
                        required: true
                    },
                    instmappingcode: {
                        required: true
                    },
                    distcode: {
                        required: true
                    },
                    userid: {
                        required: true
                    }
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
                // messages: {
                //     deptcode: {
                //         required: "Select a department",
                //     },
                //     roletypecode: {
                //         required: "Select a roletype",
                //     },
                //     desigcode: {
                //         required: "Select a designation",
                //     },
                //     chargeid: {
                //         required: "Enter a charge description",
                //     },
                //     roleactioncode: {
                //         required: "Select a role action",
                //     },
                //     regioncode: {
                //         required: "select a region",
                //     },
                //     distcode: {
                //         required: "Select a District",
                //     },
                //     instmappingcode: {
                //         required: "Select a institution",
                //     },
                //     userid:{ required : "Select a user"}
                // }
            });






            $("#buttonaction").on("click", function(event) {
                // Prevent form submission (this stops the page from refreshing)
                event.preventDefault();

                //Trigger the form validation
                if ($("#assignchargeform").valid()) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    var formData = $('#assignchargeform').serializeArray();




                    $.ajax({
                        url: '/assigncharge_insertupdate', // URL where the form data will be posted
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                reset_form(); // Reset the form after successful submission
                                getLabels_jsonlayout([{
                                    id: response.message,
                                    key: response.message
                                }], 'N').then((text) => {
                                    passing_alert_value('Confirmation', Object.values(
                                            text)[0], 'confirmation_alert',
                                        'alert_header', 'alert_body',
                                        'confirmation_alert');
                                });
                                get_assignchargevalue();
                                const lang = window.localStorage.getItem('lang') ||
                                'en'; // Default to 'en' if no language is set
                                initializeDataTable(lang);


                            } else if (response.error) {
                                // Handle errors if needed
                                console.log(response.error);
                            }
                        },
                        error: function(xhr, status, error) {

                            var response = JSON.parse(xhr.responseText);
                            if(response.error == 401)
                            {
                                handleUnauthorizedError(); 
                            }
                            else
                            {

                                getLabels_jsonlayout([{ id: response.message, key: response.message }], 'N').then((text) => {
                                    let alertMessage = Object.values(text)[0] || "Error Occured";
                                    passing_alert_value('Confirmation', alertMessage, 'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
                                });
                            }
                        }
                    });

                } else {

                }


            });
            reset_form();

        }).catch(error => {
            console.error("Failed to load JSON data:", error);
        });




        function reset_form() {
            if (
                session_roletypecode == '<?php echo $Ho_roletypecode; ?>' ||
                session_roletypecode == '<?php echo $Re_roletypecode; ?>' ||
                session_roletypecode == '<?php echo $Dist_roletypecode; ?>'
            ) {
                makedropdownempty('instmappingcode', 'Select Institution');

                if (session_roletypecode == '<?php echo $Ho_roletypecode; ?>') {
                    makedropdownempty('regioncode', 'Select Region');
                    makedropdownempty('distcode', 'Select District');
                } else if (session_roletypecode == '<?php echo $Re_roletypecode; ?>') {
                    makedropdownempty('distcode', 'Select District');
                }
            } else {
                $('#assignchargeform')[0].reset();
            }


            $('#deptcode').val('').trigger('change');
            $('#roletypecode').val('');
            $('#chargeid').val('');
            $('#desigcode').val('').trigger('change');
            $('#userid').val('').trigger('change');

            $('#chargeid').val('').html(`
                <option value="" data-name-en="Select a Region" data-name-ta="பகுதியை தேர்ந்தெடுக்கவும்">
                    ${lang === 'ta' ? 'பகுதியை தேர்ந்தெடுக்கவும்' : 'Select Charge'}
                </option>
            `);

            $('#display_error').hide();

            $('#assignchargeform').validate().resetForm();
            changeButtonAction('assignchargeform', 'action', 'buttonaction', 'reset_button', 'display_error',
                @json($savebtn), @json($clearbtn), @json($insert));
            updateSelectColorByValue(document.querySelectorAll(".form-select"));

            if ($('#roletypecode').val() == "") {
                $('#distdiv').hide();
                $('#regiondiv').hide();
                $('#instdiv').hide();
            }
        }

        $(document).ready(function() {
            // Reset form and update select box colors on page load
            $('#assignchargeform')[0].reset();
            updateSelectColorByValue(document.querySelectorAll(".form-select"));




        });
    </script>


@endsection
