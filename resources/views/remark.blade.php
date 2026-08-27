@php
    session_start();
    $layout = 'layouts.layout';
@endphp
@extends($layout)
@section('title', 'Remark')
@section('content_page')
    <style type="text/css">
        table.table thead th{
            text-align: center;
            vertical-align: middle;
        }

        table.table tbody td{
            vertical-align: middle;
        }

        .input_hidden {
            position: absolute;
            opacity: 0;
        }

        .class-disabled{
            pointer-events: none;
        }
    </style>

    <div class="content-wrapper layout-fixed">
        <section class="content p-3">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <i class="fas fa-comment-dots me-1"></i>
                                <h3 class="card-title mb-0">Remark</h3>
                            </div>

                            <div class="card-body">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-dark" id="buttonAddRemark" data-bs-toggle="modal" data-bs-target="#modalRemarkCreateUpdate">
                                        <i class="fa fa-plus fa-md"></i> New Data
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table id="tableRemark" class="table table-bordered table-hover nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Status</th>
                                                <th>Remark</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Remark Modal Start -->
    <div class="modal fade" id="modalRemarkCreateUpdate" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <form method="post" id="formRemark" autocomplete="off">
                    @csrf
                    <!-- Top Header -->
                    <div class="bg-dark bg-gradient text-white p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-comment-dots fs-4"></i>
                                </div>

                                <div>
                                    <h5 class="mb-1 fw-bold">
                                        Remark Setup
                                    </h5>

                                    <small class="opacity-75">
                                        Create or update remark information
                                    </small>
                                </div>
                            </div>

                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="modal-body p-4">
                        <input type="text" class="input_hidden" id="textRemarkId" name="remark_id" readonly>
                        <!-- Remark -->
                        <div class="mb-2">
                            <label for="textRemark" class="form-label fw-semibold">Remark:</label>

                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0 align-items-start pt-3">
                                    <i class="fas fa-comment-alt text-dark"></i>
                                </span>

                                <textarea class="form-control bg-light border-start-0" id="textRemark" name="remark" autocomplete="off" rows="3" placeholder="Enter remark..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer justify-content-between bg-light">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>

                        <button type="submit" id="btnRemark" class="btn btn-dark px-4">

                            <i id="iBtnRemarkIcon" class="fas fa-check me-1"></i> Save Remark
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Remark Modal End -->

    <!-- Remark Status Modal Start -->
    <div class="modal fade" id="modalRemarkChangeStatus" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <form method="post" id="formRemarkChangeStatus" autocomplete="off">
                    @csrf
                    <!-- Top Header -->
                    <div class="bg-dark bg-gradient text-white p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-toggle-on fs-4"></i>
                                </div>

                                <div>
                                    <h5 class="mb-1 fw-bold"
                                        id="h4RemarkChangeStatusTitle">
                                        Change Status
                                    </h5>

                                    <small class="opacity-75">
                                        Update remark status
                                    </small>
                                </div>
                            </div>

                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="modal-body p-4">
                        <input type="text" class="input_hidden" name="remark_id" id="txtRemarkChangeStatusId">
                        <input type="text" class="input_hidden" name="status" id="txtRemarkChangeStatus">
                        <!-- Status Information -->
                        <div class="text-center py-2">
                            <div class="bg-light rounded-4 p-4">
                                <div class="mb-3">
                                    <div class="bg-dark bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">

                                        <i class="fas fa-info-circle text-dark fs-3"></i>
                                    </div>
                                </div>

                                <label id="lblRemarkChangeStatusLabel"class="fw-semibold fs-6 mb-0"></label>

                                <p class="text-muted small mb-0 mt-2">
                                    Are you sure you want to change the status
                                    of this remark?
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer justify-content-between bg-light">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>

                        <button type="submit" id="btnRemarkChangeStatus" class="btn btn-dark px-4">
                            <i id="iBtnRemarkChangeStatusIcon" class="fas fa-check me-1"></i>Save Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Remark Status Modal End -->
@endsection

<!-- JS CONTENT --}} -->
@section('js_content')
    <script type="text/javascript">
        let dataTableMarkup

        $(document).ready(function () {
            resetModalFormValues();

            $('.select2bs5').select2({
                theme: 'bootstrap-5',
                dropdownAutoWidth: true,
                dropdownParent: $(this).closest('.modal')
            })

            dataTableMarkup = $("#tableRemark").DataTable({
                "processing" : false,
                "serverSide" : true,
                "responsive": true,
                "order": [[1,'desc'],[3, "asc"]],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ Markup Record",
                    "lengthMenu": "Show _MENU_ Remark Record",
                },
                "ajax" : {
                    url: "view_remark",
                },
                "columns":[
                    { "data" : "action", orderable:false, searchable:false},
                    { "data" : "status"},
                    { "data" : "remark"}
                ],
            });

            $("#formRemark").submit(function(event){
                event.preventDefault();
                RemarkCreateUpdate();
            });

            $(document).on('click', '.actionRemarkEdit', function(e){
                e.preventDefault();
                let remarkId = $(this).attr('remark-id');
                    $("#textRemarkId").val(remarkId);
                    GetRemarkInfoByIdToEdit(remarkId);
            });

            $(document).on('click', '.actionRemarkChangeStatus', function(){
                let remarkStatus = $(this).attr('status');
                let remarkId = $(this).attr('remark-id');
                $("#txtRemarkChangeStatus").val(remarkStatus);
                $("#txtRemarkChangeStatusId").val(remarkId);

                if(remarkStatus == 0){
                    $("#lblRemarkChangeStatusLabel").text('Are you sure to activate?');
                    $("#h4RemarkChangeStatusTitle").html('Activate Remark');
                    // $("#h4RemarkChangeStatusTitle").html('<i class="fa-solid fa-book-bookmark"></i> Activate Remark');
                }
                else{
                    $("#lblRemarkChangeStatusLabel").text('Are you sure to deactivate?');
                    $("#h4RemarkChangeStatusTitle").html('Deactivate Remark');
                    // $("#h4RemarkChangeStatusTitle").html('<i class="fa-solid fa-book-bookmark"></i> Deactivate Remark');
                }
            });

            $("#formRemarkChangeStatus").submit(function(event){
                event.preventDefault();
                RemarkChangeStatus();
            });
        });
    </script>
@endsection
