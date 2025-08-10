@section('content')
    @extends('index2')
    @include('common.alert')
    <?php

    ?>




    <div class="row">
        <div class="col-12">
            <div class="card card_border">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-3 mb-3"> <label class="form-label required" for="validationDefault01">Institution
                                Name</label> <input type="text" class="form-control" id="instid" name="instid"
                                value="304" disabled>
                        </div>
                        <div class="col-md-2 mb-3"> <label class="form-label required" for="validationDefault01">Institution
                                Category</label> <input type="text" class="form-control" id="catid" name="catid"
                                value="12" disabled>
                        </div>
                        <div class="col-md-2 mb-3"> <label class="form-label required" for="validationDefault01">Sub
                                Category</label> <input type="text" class="form-control" id="auditeeins_subcategoryid"
                                name="auditeeins_subcategoryid" value="2">
                        </div>
                        <div class="col-md-2 mt-6"> <button class="followup_btn btn bg-primary text-white">Follow
                                Up</button>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>
    </div>
    </div>
    </script>
    <script src="../assets/js/vendor.min.js"></script>
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
    <script src="../assets/libs/jquery-steps/build/jquery.steps.min.js"></script>
    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <!-- <script src="../assets/js/forms/form-wizard.js"></script> -->
    <script src="../assets/libs/simplebar/dist/simplebar.min.js"></script>


    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).on('click', '.followup_btn', function() {
            var id = $(this).attr('id'); // Getting id of the clicked button (which is auditplanid)
            var userid = $(this).attr('data-userid');

            window.location.href = '/followup?inst=' + '306' + '&catcode=' + '12' + '&subcatid=' + '2';

        });
    </script>
@endsection
