@php
    session_start();
    $layout = 'layouts.layout';
@endphp
@extends($layout)
@section('title', 'Device')
@section('content_page')
    <style type="text/css">
        table.table thead th{
            text-align: center !important;
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
                                <i class="fas fa-microchip me-1"></i>
                                <h3 class="card-title mb-0">Device</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-dark" id="buttonAddDevice" data-bs-toggle="modal" data-bs-target="#modalDeviceCreateUpdate">
                                        <i class="fa fa-plus fa-md"></i> New Data
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table id="tableDevice" class="table table-bordered table-hover nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Status</th>
                                                <th>Device Code</th>
                                                <th>Device Name</th>
                                                <th>Tool Life</th>
                                                <th>Total Qty</th>
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

    <!-- Device Modal Start -->
    <div class="modal fade" id="modalDeviceCreateUpdate" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <form method="post" id="formDevice" autocomplete="off">
                    @csrf
                    <!-- Top Header -->
                    <div class="bg-dark bg-gradient text-white p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-microchip fs-4"></i>
                                </div>

                                <div>
                                    <h5 class="mb-1 fw-bold">
                                        Device Setup
                                    </h5>
                                    <small class="opacity-75">
                                        Create or update device information
                                    </small>
                                </div>
                            </div>

                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="modal-body p-4">
                        <input type="text" class="input_hidden" id="txtDeviceId" name="device_id">
                        <!-- Device Code -->
                        <div class="mb-4">
                            <label for="txtDeviceCode" class="form-label fw-semibold">
                                Device Code:
                            </label>

                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-barcode text-dark"></i>
                                </span>
                                <input type="text" id="txtDeviceCode" name="device_code"  class="form-control bg-light border-start-0" autocomplete="off">
                            </div>
                        </div>

                        <!-- Device Name -->
                        <div class="mb-2">
                            <label for="txtDeviceName"class="form-label fw-semibold">
                                Device Name:
                            </label>

                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-screwdriver-wrench text-dark"></i>
                                </span>
                                <input type="text" name="device_name" id="txtDeviceName" class="form-control bg-light border-start-0" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer justify-content-between bg-light">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>
                            Cancel
                        </button>

                        <button type="submit" id="btnDevice" class="btn btn-dark px-4">
                            <i id="iBtnDeviceIcon" class="fas fa-check me-1"></i>
                            Save Device
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Device Modal End -->

    <!-- Device Status Modal Start -->
    <div class="modal fade" id="modalDeviceChangeStatus" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <form method="post" id="formDeviceChangeStatus" autocomplete="off">
                    @csrf
                    <!-- Top Header -->
                    <div class="bg-dark bg-gradient text-white p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-exchange-alt fs-4"></i>
                                </div>

                                <div>
                                    <h5 class="mb-1 fw-bold" id="h4DeviceChangeStatusTitle">
                                        Change Status
                                    </h5>

                                    <small class="opacity-75">
                                        Update device status
                                    </small>
                                </div>
                            </div>

                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="modal-body p-4">
                        <input type="text" class="input_hidden" name="device_id" placeholder="Device Id" id="txtDeviceChangeStatusId">
                        <input type="text" class="input_hidden" name="status" placeholder="Status" id="txtDeviceChangeStatus">
                        <div class="text-center py-2">
                            <div class="bg-light rounded-4 p-4">
                                <div class="mb-3">
                                    <div class="bg-dark bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-toggle-on text-dark fs-3"></i>
                                    </div>
                                </div>

                                <h6 class="fw-bold mb-2">Change Device Status?</h6>

                                <p class="text-muted small mb-0" id="lblDeviceChangeStatusLabel"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer justify-content-between bg-light">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>
                            Close
                        </button>

                        <button type="submit" id="btnDeviceChangeStatus" class="btn btn-dark px-4">
                            <i id="iBtnDeviceChangeStatusIcon" class="fas fa-check me-1"></i>
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Device History Modal Start -->
    <div class="modal fade" id="modalDeviceHistory" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl-custom">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <!-- Top Header -->
                <div class="bg-dark bg-gradient text-white p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center me-3 p-3">
                                <i class="fa fa-history fs-4"></i>
                            </div>

                            <div>
                                <h5 class="mb-1 fw-bold" id="h4DeviceHistoryTitle">
                                    Device History
                                    <input type="text" class="input_hidden1" name="get_device_code" placeholder="Device Code" id="txtGetDeviceCode" readonly>
                                </h5>

                                <small class="opacity-75">
                                    View device history
                                </small>
                            </div>
                        </div>

                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>

                <!-- Body -->
                <div class="card-body p-4">
                    <!-- Filters -->
                    <div class="row align-items-end g-3 mb-4">
                        <!-- View Type -->
                        <div class="col-md-3">
                            <label for="historyViewType" class="form-label fw-semibold mb-1">View Type</label>

                            <select class="form-select" id="historyViewType">
                                <option value="reset" selected>Reset</option>
                                <option value="summary">Summary</option>
                            </select>
                        </div>

                        <!-- Reset Filters -->
                        <div id="historyResetFilters" class="col-md-7">
                            <div class="row align-items-end g-3">
                                <!-- Date From -->
                                <div class="col-md-4">
                                    <label for="historyDateFrom" class="form-label fw-semibold mb-1">Date From</label>
                                    <input type="date" class="form-control reset-value" id="historyDateFrom">
                                </div>

                                <!-- Date To -->
                                <div class="col-md-4">
                                    <label for="historyDateTo" class="form-label fw-semibold mb-1">Date To</label>
                                    <input type="date" class="form-control reset-value" id="historyDateTo">
                                </div>

                                <!-- Search -->
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-dark w-100" id="buttonSearchHistory">
                                        <i class="fa fa-search me-1"></i>Search
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Filters -->
                        <div id="historySummaryFilters" class="col-md-4 d-none">
                            <label for="historySummaryType"class="form-label fw-semibold mb-1">Summary Type</label>

                            <select class="form-select reset-value" id="historySummaryType">
                                <option value="" selected>Select Summary</option>
                                <option value="year">Year</option>
                                <option value="month">Month</option>
                            </select>
                        </div>


                        <!-- Reset Button -->
                        <div id="historyResetButton" class="col-md-auto ms-md-auto">
                            <button type="button" class="btn btn-danger" id="buttonResetHistoryFilter">
                                <i class="fa fa-refresh me-1"></i>Reset
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table id="tableDeviceHistory" class="table table-bordered table-hover nowrap w-100">
                            <thead>
                                <tr>
                                    <th rowspan="2">Die-No</th>
                                    <th rowspan="2">Die-set <br>Receive Date</th>
                                    <th rowspan="2">Tool Life <br>(pcs)</th>
                                    <th rowspan="2"> YEC <br> Sales Qty. (pcs)</th>
                                    <th rowspan="2"> YEC + PMI <br> Sales Qty. (pcs)</th>
                                    <th colspan="2" class="text-center"> Die-set Age</th>
                                </tr>

                                <tr>
                                    <th>Year</th>
                                    <th>Months</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Device History Modal End -->

    {{-- <!-- Device History Modal Start -->
    <div class="modal fade" id="modalDeviceHistory" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl-custom">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">

                <!-- Top Header -->
                <div class="bg-dark bg-gradient text-white p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center me-3 p-3">
                                <i class="fa fa-history fs-4"></i>
                            </div>

                            <div>
                                <h5 class="mb-1 fw-bold" id="h4DeviceHistoryTitle">
                                    Device History
                                </h5>

                                <small class="opacity-75">
                                    View device history
                                </small>
                            </div>
                        </div>

                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="card-body p-4">
                    <!-- Filters -->
                    <div class="row align-items-end justify-content-between g-3 mb-4">
                        <!-- Date From -->
                        <div class="col-md-3">
                            <label for="historyDateFrom" class="form-label fw-semibold mb-1">
                                Date From
                            </label>

                            <input type="date" class="form-control" id="historyDateFrom">
                        </div>

                        <!-- Date To -->
                        <div class="col-md-3">
                            <label for="historyDateTo" class="form-label fw-semibold mb-1">
                                Date To
                            </label>

                            <input type="date" class="form-control" id="historyDateTo">
                        </div>

                        <!-- Search -->
                        <div class="col-md-2">
                            <button type="button" class="btn btn-dark btn w-100" id="buttonSearchHistory">
                                <i class="fa fa-search me-1"></i>
                                Search
                            </button>
                        </div>

                        <!-- Reset -->
                        <div class="col-md-auto ms-md-auto">
                            <button type="button" class="btn btn-danger" id="buttonResetHistoryFilter">
                                <i class="fa fa-refresh me-1"></i>
                                Reset
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">

                        <table id="tableDeviceHistory" class="table table-bordered table-hover nowrap w-100">
                            <thead>
                                <tr>
                                    <th rowspan="2">Die-No</th>
                                    <th rowspan="2">Die-set <br>Receive Date</th>
                                    <th rowspan="2">Tool Life <br>(pcs)</th>
                                    <th rowspan="2">YEC <br>Sales Qty. (pcs)</th>
                                    <th rowspan="2">YEC + PMI <br>Sales Qty. (pcs)</th>
                                    <th colspan="2" class="text-center">
                                        Die-set Age
                                    </th>
                                </tr>

                                <tr>
                                    <th>Year</th>
                                    <th>Months</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Device History Modal End --> --}}


@endsection

<!-- JS CONTENT --}} -->
@section('js_content')
    <script type="text/javascript">
        let dataTableDevice
        let dataTableDeviceHistory

        $(document).ready(function () {
            resetModalFormValues();

            $('.select2bs5').select2({
                theme: 'bootstrap-5',
                dropdownAutoWidth: true,
                dropdownParent: $(this).closest('.modal')
            })

            dataTableDevice = $("#tableDevice").DataTable({
                "processing" : false,
                "serverSide" : true,
                "responsive": true,
                "order": [[1,'desc'],[3, "asc"]],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ Device Record",
                    "lengthMenu": "Show _MENU_ Device Record",
                },
                "ajax" : {
                    url: "view_device",
                },
                "columns":[
                    { "data" : "action", orderable:false, searchable:false},
                    { "data" : "status"},
                    { "data" : "device_code"},
                    { "data" : "device_name"},
                    { "data" : "tool_life"},
                    { "data" : "total_qty"},
                ],
                "columnDefs": [
                    {
                        "targets": "_all",
                        "className": "text-start"
                    }
                ]
            });

            $("#formDevice").submit(function(event){
                event.preventDefault();
                DeviceCreateUpdate();
            });

            $(document).on('click', '.actionUpdateDevice', function(e){
                e.preventDefault();
                let deviceId = $(this).attr('device-id');
                    $("#txtDeviceId").val(deviceId);
                    GetDeviceInfoByIdToEdit(deviceId);
            });

            $(document).on('click', '.actionDeviceChangeStatus', function(){
                let deviceStatus = $(this).attr('status');
                let deviceId = $(this).attr('device-id');
                $("#txtDeviceChangeStatus").val(deviceStatus);
                $("#txtDeviceChangeStatusId").val(deviceId);

                if(deviceStatus == 0){
                    $("#lblDeviceChangeStatusLabel").text('Are you sure to activate?');
                    $("#h4DeviceChangeStatusTitle").html('Activate Device');
                }
                else{
                    $("#lblDeviceChangeStatusLabel").text('Are you sure to deactivate?');
                    $("#h4DeviceChangeStatusTitle").html('Deactivate Device');
                }
            });

            $("#formDeviceChangeStatus").submit(function(event){
                event.preventDefault();
                DeviceChangeStatus();
            });

            // ==================================================================================================
            // ==================================================================================================
            // ==================================================================================================
            $(document).on('click', '.actionDeviceHistory', function(e){
                e.preventDefault();
                let deviceCode = $(this).attr('device-code');
                    $("#txtGetDeviceCode").val(deviceCode);
            });

            $('#historyViewType').on('change', function () {
                const viewType = $(this).val();

                if(viewType === 'reset'){
                    $('#historyResetFilters').removeClass('d-none');
                    $('#historyResetButton').removeClass('d-none');
                    $('#historySummaryFilters').addClass('d-none');
                    $('.reset-value').val('');
                }else if(viewType === 'summary'){
                    $('#historyResetFilters').addClass('d-none');
                    $('#historyResetButton').addClass('d-none');
                    $('#historySummaryFilters').removeClass('d-none');
                }
            });

            $('#buttonResetHistoryFilter').on('click', function () {
                $('.reset-value').val('');
                $('#historyViewType').val('reset');
                $('#historyResetFilters').removeClass('d-none');
                $('#historyResetButton').removeClass('d-none');
                $('#historySummaryFilters').addClass('d-none');
            });

            $('#buttonSearchHistory').on('click', function () {
                const dateFrom = $('#historyDateFrom').val();
                const dateTo = $('#historyDateTo').val();

                console.log('Date From:', dateFrom);
                console.log('Date To:', dateTo);
            });

            $('#historySummaryType').on('change', function () {
                const summaryType = $(this).val();
                console.log('Summary Type:', summaryType);
            });
        });
    </script>
@endsection
