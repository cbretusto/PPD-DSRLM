@php
    session_start();
    if(isset($_SESSION['ppd_dsrlm_classification'])){
        $userClassifcation = $_SESSION['ppd_dsrlm_classification'];
    }else{
        $userClassifcation = 0;
    }

    $layout = 'layouts.layout';
@endphp
@extends($layout)
@section('title', 'Device')
@section('content_page')
                    <style>
        .col-5-items {
            flex: 0 0 auto;
            width: 20%;
        }

        @media (max-width: 1199.98px) {
            .col-5-items {
                width: 25%;
            }
        }

        @media (max-width: 991.98px) {
            .col-5-items {
                width: 33.333333%;
            }
        }

        @media (max-width: 767.98px) {
            .col-5-items {
                width: 50%;
            }
        }

        @media (max-width: 575.98px) {
            .col-5-items {
                width: 100%;
            }
        }
    </style>

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

        #device-page .card{
            border: none;
            border-radius: 0.9rem;
            box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        #device-page .card-header{
            flex-wrap: wrap;
            row-gap: 0.75rem;
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            padding: 1rem 1.25rem;
        }

        #device-page .card-header > .d-flex:first-child i.fa-microchip{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background-color: rgba(33, 37, 41, 0.08);
            color: #212529;
            font-size: 16px !important;
        }

        #device-page .card-title{
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        #device-page .card-header .gap-5{
            flex-wrap: wrap;
            row-gap: 0.5rem;
            column-gap: 1.25rem !important;
        }

        #device-page .card-header .gap-5 > div{
            padding: 0.35rem 0.85rem 0.35rem 0.6rem;
            border-radius: 2rem;
            background-color: #f8f9fa;
            border: 1px solid rgba(0, 0, 0, 0.06);
            transition: transform 0.15s ease-in-out;
        }

        #device-page .card-header .gap-5 > div:hover{
            transform: translateY(-1px);
        }

        #device-page .card-header .gap-5 i.icon-shadow{
            font-size: 18px !important;
            filter: drop-shadow(0 1px 1px rgba(0, 0, 0, 0.12));
        }

        #device-page .card-header .gap-5 span{
            font-size: 0.85rem;
        }

        #device-page #buttonAddDevice{
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 0.4rem rgba(0, 0, 0, 0.12);
            transition: transform 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        #device-page #buttonAddDevice:hover{
            transform: translateY(-1px);
            box-shadow: 0 0.35rem 0.75rem rgba(0, 0, 0, 0.18);
        }

        #device-page .card-body{
            padding: 1.25rem;
        }

        #device-page table.table thead th{
            background-color: #f8f9fa;
            color: #495057;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border-bottom-width: 1px;
        }

        #device-page table.table tbody tr{
            transition: background-color 0.1s ease-in-out;
        }

        #device-page table.table tbody td{
            font-size: 0.9rem;
        }

        #device-page table.table .btn-sm{
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        #modalDeviceCreateUpdate .modal-content,
        #modalDeviceChangeStatus .modal-content,
        #modalDeviceHistory .modal-content,
        #deviceResetModal .modal-content{
            border-radius: 1rem;
        }

        #modalDeviceCreateUpdate .form-control:focus,
        #modalDeviceChangeStatus .form-control:focus,
        #deviceResetModal .form-control:focus{
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.2);
        }

        #modalDeviceHistory .card.bg-light{
            border-radius: 0.75rem;
            transition: box-shadow 0.15s ease-in-out;
        }

        #modalDeviceHistory .card.bg-light:hover{
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.08);
        }

        #modalDeviceHistory .nav-tabs .nav-link{
            font-weight: 500;
            color: #6c757d;
        }

        #modalDeviceHistory .nav-tabs .nav-link.active{
            color: #212529;
            font-weight: 600;
        }

        @media (max-width: 767.98px){
            #device-page .card-header .gap-5{
                margin-left: 0 !important;
                width: 100%;
            }

            #device-page .card-header .ms-auto{
                margin-left: 0 !important;
                width: 100%;
            }

            #device-page .card-header .ms-auto #buttonAddDevice{
                width: 100%;
            }

            #device-page .ms-4{
                margin-left: 0 !important;
                width: 100%;
            }
        }
    </style>

    <div class="content-wrapper layout-fixed" id="device-page">
        <section class="content p-3">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-microchip me-2" style="font-size: 20px;"></i>

                                    <h3 class="card-title mb-0">
                                        Device
                                    </h3>
                                </div>

                                <div class="ms-4">
                                    <select id="processFilter"class="form-select">
                                        <option value="" selected disabled>-- Select Department --</option>
                                        <option value="0">PPD CN</option>
                                        <option value="1">PPD TS</option>
                                        <option value="2">Stamping</option>
                                    </select>
                                </div>

                                <div class="d-flex align-items-center ms-5 gap-5">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-check-circle icon-shadow" style="font-size:30px; color:gray;"></i>

                                        <span class="ms-1 fw-bold">
                                            Normal
                                        </span>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-exclamation-triangle icon-shadow" style="font-size:30px; color:blue;"></i>

                                        <span class="ms-1 fw-bold">
                                            70%
                                        </span>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-exclamation-triangle icon-shadow" style="font-size:30px; color:orange;"></i>

                                        <span class="ms-1 fw-bold">
                                            80%
                                        </span>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-exclamation-triangle icon-shadow" style="font-size:30px; color:red;"></i>

                                        <span class="ms-1 fw-bold">
                                            100%
                                        </span>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-thumbs-up icon-shadow" style="font-size:30px; color:green;"></i>

                                        <span class="ms-1 fw-bold">
                                            Reset - For Approval
                                        </span>
                                    </div>
                                </div>

                                <div class="ms-auto">
                                    <button type="button" class="btn btn-dark" id="buttonAddDevice" data-bs-toggle="modal" data-bs-target="#modalDeviceCreateUpdate">
                                        <i class="fa fa-plus fa-md me-1"></i>
                                        New Data
                                    </button>
                                </div>
                            </div>

                            <div class="card-body">
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
                                                <th>Percentage</th>
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

    {{-- <!-- Device Modal Start -->
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
                        <div class="mb-4">
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

                        <!-- Tool Life -->
                        <div class="mb-4">
                            <label for="txtToolLife"class="form-label fw-semibold">
                                Tool Life:
                            </label>

                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-scale-balanced text-dark"></i>
                                </span>
                                <input type="text" name="tool_life" id="txtToolLife" class="form-control bg-light border-start-0" autocomplete="off">
                            </div>
                        </div>

                        <!-- YEC Sales Qty -->
                        <div class="mb-4">
                            <label for="txtYecSalesQty" class="form-label fw-semibold">
                                YEC Sales Qty. (pcs):
                            </label>

                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-boxes-stacked text-dark"></i>
                                </span>
                                <input type="number"
                                    name="yec_sales_qty"
                                    id="txtYecSalesQty"
                                    class="form-control bg-light border-start-0"
                                    min="0"
                                    step="1"
                                    autocomplete="off">
                            </div>
                        </div>

                        <!-- PMI Sales Qty -->
                        <div class="mb-4">
                            <label for="txtPmiSalesQty" class="form-label fw-semibold">
                                PMI Sales Qty. (pcs):
                            </label>

                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-boxes-stacked text-dark"></i>
                                </span>
                                <input type="number"
                                    name="pmi_sales_qty"
                                    id="txtPmiSalesQty"
                                    class="form-control bg-light border-start-0"
                                    min="0"
                                    step="1"
                                    autocomplete="off">
                            </div>
                        </div>


                        <!-- Department / Process -->
                        <div class="mb-2">
                            <label class="form-label fw-semibold">
                                Process / Department:
                            </label>

                            <div class="d-flex flex-wrap gap-4">
                                <input type="radio" class="btn-check" name="process_type" id="processPPDCN" value="0" autocomplete="off">
                                <label class="btn btn-outline-dark px-4" for="processPPDCN">
                                    PPD - CN
                                </label>

                                <input type="radio" class="btn-check" name="process_type" id="processPPDTS" value="1" autocomplete="off">
                                <label class="btn btn-outline-dark px-4" for="processPPDTS">
                                    PPD - TS
                                </label>

                                <input type="radio" class="btn-check" name="process_type" id="processStamping" value="2" autocomplete="off">
                                <label class="btn btn-outline-dark px-4" for="processStamping">
                                    Stamping
                                </label>
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
    </div><!-- Device Modal End --> --}}

    <!-- Device Modal Start -->
    <div class="modal fade" id="modalDeviceCreateUpdate" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <form method="post" id="formDevice" autocomplete="off">
                    @csrf
                    <!-- Header -->
                    <div class="bg-dark text-white px-4 py-4">
                        <div class="d-flex align-items-center justify-content-between">

                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-10 rounded-4 d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px;">
                                    <i class="fas fa-microchip fs-4"></i>
                                </div>

                                <div>
                                    <h5 class="mb-1 fw-bold">
                                        Device Setup
                                    </h5>

                                    <small class="text-white-50">
                                        Configure device information and sales quantities
                                    </small>
                                </div>
                            </div>

                            <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal" aria-label="Close">
                            </button>

                        </div>
                    </div>

                    <!-- Body -->
                    <div class="modal-body p-4">

                        <input type="text" class="input_hidden" id="txtDeviceId" name="device_id">

                        <!-- Device Information -->
                        <div class="mb-4">

                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-dark bg-opacity-10 text-dark rounded-3 d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                                    <i class="fas fa-circle-info"></i>
                                </div>

                                <div>
                                    <h6 class="mb-0 fw-bold">
                                        Device Information
                                    </h6>
                                    <small class="text-muted">
                                        Basic device identification
                                    </small>
                                </div>
                            </div>

                            <div class="row g-3">

                                <!-- Device Code -->
                                <div class="col-md-6">
                                    <label for="txtDeviceCode" class="form-label fw-semibold">
                                        Device Code
                                    </label>

                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-end-0 text-muted">
                                            <i class="fas fa-barcode"></i>
                                        </span>

                                        <input type="text" id="txtDeviceCode" name="device_code" class="form-control bg-light border-start-0" autocomplete="off" placeholder="Enter device code">
                                    </div>
                                </div>

                                <!-- Device Name -->
                                <div class="col-md-6">
                                    <label for="txtDeviceName" class="form-label fw-semibold">
                                        Device Name
                                    </label>

                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-end-0 text-muted">
                                            <i class="fas fa-screwdriver-wrench"></i>
                                        </span>

                                        <input type="text" name="device_name" id="txtDeviceName" class="form-control bg-light border-start-0" autocomplete="off" placeholder="Enter device name">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <hr>
                        <!-- Production Information -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-dark bg-opacity-10 text-dark rounded-3 d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                                    <i class="fas fa-gears"></i>
                                </div>

                                <div>
                                    <h6 class="mb-0 fw-bold">
                                        Production Information
                                    </h6>
                                    <small class="text-muted">
                                        Configure tool life and sales quantities
                                    </small>
                                </div>
                            </div>

                            <div class="row g-3">
                                <!-- Tool Life -->
                                <div class="col-md-4">
                                    <label for="txtToolLife" class="form-label fw-semibold">
                                        Tool Life
                                    </label>

                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-end-0 text-muted">
                                            <i class="fas fa-scale-balanced"></i>
                                        </span>

                                        <input type="text" name="tool_life" id="txtToolLife" class="form-control bg-light border-start-0" autocomplete="off" placeholder="Tool life">
                                    </div>
                                </div>

                                <!-- YEC Sales Qty -->
                                <div class="col-md-4">
                                    <label for="txtYecSalesQty" class="form-label fw-semibold">
                                        YEC Sales Qty. <span class="text-muted fw-normal">(pcs)</span>
                                    </label>

                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-end-0 text-muted">
                                            <i class="fas fa-boxes-stacked"></i>
                                        </span>

                                        <input type="number" name="yec_sales_qty" id="txtYecSalesQty" class="form-control bg-light border-start-0" min="0" step="1" autocomplete="off" placeholder="0">
                                    </div>
                                </div>

                                <!-- PMI Sales Qty -->
                                <div class="col-md-4">
                                    <label for="txtPmiSalesQty" class="form-label fw-semibold">
                                        PMI Sales Qty. <span class="text-muted fw-normal">(pcs)</span>
                                    </label>

                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-end-0 text-muted">
                                            <i class="fas fa-boxes-stacked"></i>
                                        </span>
                                        <input type="number" name="pmi_sales_qty" id="txtPmiSalesQty" class="form-control bg-light border-start-0" min="0" step="1" autocomplete="off" placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <!-- Department / Process -->
                        <div class="mb-2">
                            <label class="form-label fw-semibold">
                                Process / Department:
                            </label>

                            <div class="d-flex flex-wrap gap-4">
                                <input type="radio" class="btn-check" name="process_type" id="processPPDCN" value="0" autocomplete="off">
                                <label class="btn btn-outline-dark px-4" for="processPPDCN">
                                    PPD - CN
                                </label>

                                <input type="radio" class="btn-check" name="process_type" id="processPPDTS" value="1" autocomplete="off">
                                <label class="btn btn-outline-dark px-4" for="processPPDTS">
                                    PPD - TS
                                </label>

                                <input type="radio" class="btn-check" name="process_type" id="processStamping" value="2" autocomplete="off">
                                <label class="btn btn-outline-dark px-4" for="processStamping">
                                    Stamping
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer justify-content-between bg-light">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-3" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>
                            Cancel
                        </button>

                        <button type="submit" id="btnDevice" class="btn btn-dark px-4 py-2 rounded-3">
                            <i id="iBtnDeviceIcon" class="fas fa-check me-1"></i>
                            Save Device
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Device Modal End -->


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
                                        <i class="fas fa-screwdriver-wrench text-dark fs-3"></i>
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
                                    {{-- <input type="text" class="input_hidden1" name="get_device_code" placeholder="Device Code" id="txtGetDeviceCode" readonly> --}}
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
                    <div class="row g-2 mb-3">
                        <!-- Device Name -->
                        <div class="col-5-items">
                            <div class="card border-0 shadow-sm h-100 bg-light">
                                <div class="card-body d-flex align-items-center">
                                    <div class="bg-dark bg-opacity-10 text-dark rounded-3 p-3 me-3">
                                        <i class="fa fa-wrench fs-4"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <small class="text-muted d-block fw-semibold">
                                            Device Name:
                                        </small>
                                        <div class="fw-bold text-dark text-truncate" id="historyDeviceName">
                                            —
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Device Code -->
                        <div class="col-5-items">
                            <div class="card border-0 shadow-sm h-100 bg-light">
                                <div class="card-body d-flex align-items-center">
                                    <div class="bg-dark bg-opacity-10 text-dark rounded-3 p-3 me-3">
                                        <i class="fa fa-barcode fs-4"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <small class="text-muted d-block fw-semibold">
                                            Device Code:
                                        </small>
                                        <div class="fw-bold text-dark text-truncate" id="historyDeviceCode">
                                            —
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Drawing Information -->
                        <div class="col-5-items">
                            <div class="card border-0 shadow-sm h-100 bg-light">
                                <div class="card-body d-flex align-items-center">
                                    <div class="bg-dark bg-opacity-10 text-dark rounded-3 p-3 me-3">
                                        <i class="fa fa-file-lines fs-4"></i>
                                    </div>

                                    <div class="min-w-0">
                                        <small class="text-muted d-block fw-semibold">
                                            Drawing No.:
                                        </small>

                                        <div class="fw-bold text-dark text-truncate">
                                            <span id="historyDieSetDrawingNo">—</span>
                                        </div>

                                        <small class="text-muted">
                                            Revision:
                                            <span id="historyDieSetDrawingRevision">—</span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Die Set No. -->
                        <div class="col-5-items">
                            <div class="card border-0 shadow-sm h-100 bg-light">
                                <div class="card-body d-flex align-items-center">
                                    <div class="bg-dark bg-opacity-10 text-dark rounded-3 p-3 me-3">
                                        <i class="fa fa-toolbox fs-4"></i>
                                    </div>

                                    <div class="min-w-0">
                                        <small class="text-muted d-block fw-semibold">
                                            Die Set No.:
                                        </small>

                                        <div class="fw-bold text-dark text-truncate"
                                            id="historyDieSetDieNo">
                                            —
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Quantity -->
                        <div class="col-5-items">
                            <div class="card border-0 shadow-sm h-100 bg-light">
                                <div class="card-body d-flex align-items-center">
                                    <div class="bg-dark bg-opacity-10 text-dark rounded-3 p-3 me-3">
                                        <i class="fa fa-cubes fs-4"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <small class="text-muted d-block fw-semibold">
                                            Total Quantity:
                                        </small>
                                        <div class="fw-bold text-dark">
                                            <span id="historyTotalQuantity">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Die Set Received Date -->
                        <div class="col-5-items">
                            <div class="card border-0 shadow-sm h-100 bg-light">
                                <div class="card-body d-flex align-items-center">
                                    <div class="bg-dark bg-opacity-10 text-dark rounded-3 p-3 me-3">
                                        <i class="fa fa-calendar-check fs-4"></i>
                                    </div>

                                    <div class="min-w-0">
                                        <small class="text-muted d-block fw-semibold">
                                            Die Set Received Date:
                                        </small>

                                        <div class="fw-bold text-dark text-truncate"
                                            id="historyDieSetReceivedDate">
                                            —
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Die Set Age -->
                        <div class="col-5-items">
                            <div class="card border-0 shadow-sm h-100 bg-light">
                                <div class="card-body d-flex align-items-center">
                                    <div class="bg-dark bg-opacity-10 text-dark rounded-3 p-3 me-3">
                                        {{-- <i class="fa fa-hourglass-half fs-4"></i> --}}
                                        <i class="fa-solid fa-clipboard-question fs-4"></i>
                                    </div>

                                    <div class="min-w-0">
                                        <small class="text-muted d-block fw-semibold">
                                            Die Set Age:
                                        </small>

                                        <div class="fw-bold text-dark">
                                            <span id="historyDieSetAge">0</span>
                                            <small class="text-muted fw-normal">years</small>

                                            <span id="historyDieSetAgeMonths">0</span>
                                            <small class="text-muted fw-normal">months</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tool Life -->
                        <div class="col-5-items">
                            <div class="card border-0 shadow-sm h-100 bg-light">
                                <div class="card-body d-flex align-items-center">
                                    <div class="bg-dark bg-opacity-10 text-dark rounded-3 p-3 me-3">
                                        <i class="fas fa-scale-balanced fs-4"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <small class="text-muted d-block fw-semibold">
                                            Tool Life:
                                        </small>
                                        <div class="fw-bold text-dark">
                                            <span id="historyToolLife">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sales Quantity -->
                        <div class="col-5-items">
                            <div class="card border-0 shadow-sm h-100 bg-light">
                                <div class="card-body d-flex align-items-center">
                                    <div class="bg-dark bg-opacity-10 text-dark rounded-3 p-3 me-3">
                                        <i class="fa fa-calculator fs-4"></i>
                                    </div>

                                    <div class="min-w-0">
                                        <small class="text-muted d-block fw-semibold">
                                            Shipment Quantity:
                                        </small>

                                        <div class="fw-bold text-dark">
                                            <small class="text-muted fw-normal">YEC:</small>
                                            <span id="historySalesQuantityYEC">0</span>
                                            <small class="text-muted fw-normal">+</small>
                                            <small class="text-muted fw-normal">PMI:</small>
                                            <span id="historySalesQuantityPMI">0</span>
                                            =
                                            <small class="text-muted fw-normal">Total:</small>
                                            <span id="historySalesTotalQuantity">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tool Life Balance -->
                        <div class="col-5-items">
                            <div class="card border-0 shadow-sm h-100 bg-light">
                                <div class="card-body d-flex align-items-center">
                                    <div class="bg-dark bg-opacity-10 text-dark rounded-3 p-3 me-3">
                                        <i class="fa-solid fa-hourglass-half fs-4"></i>
                                    </div>

                                    <div class="min-w-0">
                                        <small class="text-muted d-block fw-semibold">
                                            Tool Life Balance:
                                        </small>

                                        <div class="fw-bold text-dark text-truncate"
                                            id="historyToolLifeBalance">
                                            —
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <!-- History Tabs -->
                    <ul class="nav nav-tabs mb-3" id="deviceHistoryTabs" role="tablist">
                        <!-- Device History -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="device-history-tab" data-bs-toggle="tab" data-bs-target="#deviceHistoryTab" type="button" role="tab">
                                Device History
                            </button>
                        </li>

                        <!-- Reset History -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reset-history-tab" data-bs-toggle="tab" data-bs-target="#resetHistoryTab" type="button" role="tab">
                                Reset History
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="deviceHistoryTabContent">
                        @if($userClassifcation == 2)
                            <div class="row row align-items-end g-3">
                                <div id="historyResetFilters" class="col-md-7">
                                    <div class="row align-items-end g-3">
                                        <!-- Date From -->
                                        <div class="col-md-6">
                                            <label for="historyDateFrom" class="form-label fw-semibold mb-1">
                                                Date From
                                            </label>
                                            <input type="date" class="form-control reset-value" id="historyDateFrom">
                                        </div>

                                        <!-- Date To -->
                                        <div class="col-md-6">
                                            <label for="historyDateTo" class="form-label fw-semibold mb-1">
                                                Date To
                                            </label>
                                            <input type="date" class="form-control reset-value" id="historyDateTo">
                                        </div>
                                    </div>
                                </div>

                                <div id="historyResetButton" class="col-md-2">
                                    <button type="button" class="btn btn-danger w-100" id="buttonResetHistoryFilter">
                                        <i class="fa fa-refresh me-1"></i>
                                        Reset
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="tab-pane fade show active" id="deviceHistoryTab" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table id="tableDeviceHistory"class="table table-bordered table-hover nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>ActualSO</th>
                                            <th>Total Shipment</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="resetHistoryTab" role="tabpanel">
                            <div class="table-responsive">
                                <table id="tableDeviceResetHistory" class="table table-bordered table-hover nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Status</th>
                                            <th>Date From</th>
                                            <th>Date To</th>
                                            <th>Tool Life<br>Balance</th>
                                            <th>Upload File</th>
                                            <th>Tool Life<br>Reset By</th>
                                            <th>Approver</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- Device History Modal End -->

    <div class="modal fade" id="deviceResetModal" tabindex="-1" aria-labelledby="deviceResetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form method="post" id="deviceResetForm" enctype="multipart/form-data" autocomplete="off" readonly>
                @csrf
                <div class="modal-content border-0 shadow rounded-4">
                    <input type="text" class="input_hidden" name="get_device_code" id="getDeviceCode" readonly>

                    <!-- Header -->
                    <div class="bg-dark bg-gradient text-white px-3 py-3 rounded-top-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center me-2"
                                    style="width: 38px; height: 38px;">
                                    <i class="fa-solid fa-book-bookmark"></i>
                                </div>

                                <div>
                                    <h6 class="modal-title fw-bold mb-0" id="deviceResetModalLabel">
                                        Device Tool Life Reset
                                    </h6>

                                    <div class="text-white-50 small">
                                        Reset the tool life balance using the information below.
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>

                    <div class="modal-body px-3 py-3">
                        <!-- Reset Period -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-2 p-2 me-2">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>

                                <div>
                                    <div class="fw-semibold small">
                                        Reset Period
                                    </div>
                                    <div class="text-muted small">
                                        Applicable dates.
                                    </div>
                                </div>
                            </div>

                            <div class="row g-2">
                                <!-- Date From -->
                                <div class="col-md-6">
                                    <label for="resetDateFrom" class="form-label fw-semibold small mb-1">
                                        Date From
                                    </label>
                                    <input type="date" class="form-control" id="resetDateFrom" name="reset_date_from" readonly>
                                </div>

                                <!-- Date To -->
                                <div class="col-md-6">
                                    <label for="resetDateTo" class="form-label fw-semibold small mb-1">
                                        Date To
                                    </label>
                                    <input type="date" class="form-control" id="resetDateTo" name="reset_date_to" readonly>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        <!-- Current Balance -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-warning bg-opacity-10 text-warning rounded-2 p-2 me-2">
                                    <i class="fas fa-chart-line"></i>
                                </div>

                                <div>
                                    <div class="fw-semibold small">
                                        Current Tool Life
                                    </div>
                                    <div class="text-muted small">
                                        Current available tool life balance.
                                    </div>
                                </div>
                            </div>

                            <input type="number" class="form-control fw-bold" id="resetLastVariance" name="reset_last_variance" readonly>
                        </div>

                        <!-- Upload -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-success bg-opacity-10 text-success rounded-2 p-2 me-2">
                                    <i class="fas fa-paperclip"></i>
                                </div>

                                <div>
                                    <div class="fw-semibold small">
                                        Supporting File
                                    </div>
                                    <div class="text-muted small">
                                        Attach the required document.
                                    </div>
                                </div>
                            </div>

                            <input type="file" class="form-control" id="resetUploadFile" name="reset_upload_file[]" multiple accept=".xlsx,.xls,.csv,.pdf">
                            <div class="form-text mt-1 small">
                                Allowed files:
                                <strong>XLSX, XLS, CSV, PDF</strong>
                            </div>
                        </div>

                        <!-- Approve By -->
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-info bg-opacity-10 text-info rounded-2 p-2 me-2">
                                    <i class="fas fa-user-check"></i>
                                </div>

                                <div>
                                    <div class="fw-semibold small">
                                        Approve By
                                    </div>
                                    <div class="text-muted small">
                                        Select the person responsible for approval.
                                    </div>
                                </div>
                            </div>
                            <select class="form-select get-ppd_dsrlm-user-approve_by bg-light border-start-0" id="resetApproveBy" name="reset_approve_by">
                            </select>
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="modal-footer justify-content-between px-3 py-2 bg-light border-0 rounded-bottom-4">
                        <button type="button" class="btn btn-light btn-sm border px-3" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-dark btn-sm px-3 shadow-sm" id="confirmDeviceReset">
                            <i id="iBtnDeviceResetIcon" class="fas fa-check me-1"></i>
                            Confirm Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalDeviceResetHistory" tabindex="-1" aria-labelledby="modalDeviceResetHistoryLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">

                <!-- Header -->
                <div class="bg-dark bg-gradient text-white px-3 py-3 rounded-top-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center me-2"
                                style="width: 38px; height: 38px;">
                                <i class="fa-solid fa-book-bookmark"></i>
                            </div>

                            <div>
                                <h6 class="modal-title fw-bold mb-0" id="modalDeviceResetHistoryLabel">
                                    Device Reset History
                                </h6>
                            </div>
                        </div>

                        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>

                <div class="modal-body px-3 py-3">
                    <div class="table-responsive mt-3">
                        <table id="tableDeviceHistoryByDate"class="table table-bordered table-hover nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>ActualSO</th>
                                    <th>Variance</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Modal Start -->
    <div class="modal fade" id="modalResetDeviceApproval" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title text-white"><i class="fa fa-file-invoice"></i>&nbsp;&nbsp;Approval of Reset Device by Date</h4>
                    <button type="button" class="btn-close white-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="resetDeviceApprovalForm" autocomplete="off">
                    @csrf
                    <div class="card-body p-3">
                        <input type="text" class="input_hidden" id="txtResetDeviceCode" name="reset_device_code" placeholder="Device Code">
                        <input type="text" class="input_hidden" id="txtResetDeviceApprovalId" name="reset_device_id" placeholder="Device Id">
                        <input type="text" class="input_hidden" id="txtResetDeviceApprovalStatus" name="reset_approval_status" placeholder="Approval Status">

                        <div class="d-flex flex-column">
                            <label for="" class="form-label"><strong>Remark:</strong></label>
                            <textarea class="form-control" id="textResetApprovalRemark" name="reset_approval_remark" autocomplete="off" rows="2" placeholder="N/A"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-success" id="btnCloseResetDeviceApproval" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="btnResetDeviceApproval" class="btn">
                            <label id="iBtnResetDeviceApprovalIcon"></label>
                            <label id="approvalTitle"></label>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Approval Modal End -->
@endsection

{{-- JS CONTENT --}}
@section('js_content')
    <script type="text/javascript">
        let dataTableDevice
        let dataTableDeviceHistory
        let dataTableDeviceResetHistory
        let dataTableDeviceHistoryByDate
        let deviceCode
        let deviceName
        let toolLife
        let deviceDateFrom
        let deviceDateTo
        let processType = ""

        $(document).ready(function () {
            resetModalFormValues();
            UserManagementGetApproveBy($('.get-ppd_dsrlm-user-approve_by'));

            $('.select2bs5').select2({
                theme: 'bootstrap-5',
                dropdownAutoWidth: true,
                dropdownParent: $(this).closest('.modal')
            })

            $('#processFilter').change(function (e) {
                e.preventDefault();
                processType = $(this).val();
                dataTableDevice.draw();
            });

            $('#buttonAddDevice').click(function (e) {
                e.preventDefault();
                let selectedProcess = $('#processFilter').val();

                $('input[name="process_type"]').prop('checked', false);

                if (selectedProcess) {
                    $(`input[name="process_type"][value="${selectedProcess}"]`).prop('checked', true);
                }
            });

            // ==================================================================================================
            // ========================================= DEVICE RECORDS =========================================
            // ==================================================================================================
            dataTableDevice = $("#tableDevice").DataTable({
                "processing" : false,
                "serverSide" : true,
                "responsive": true,
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ Device Record",
                    "lengthMenu": "Show _MENU_ Device Record",
                },
                "ajax" : {
                    url: "view_device",
                    data: function (d) {
                        d.deviceCode = deviceCode;
                        d.processType = processType;
                    }
                },
                "columns":[
                    { "data" : "action", orderable:false, searchable:false},
                    { "data" : "status"},
                    { "data" : "device_code"},
                    { "data" : "device_name"},
                    { "data" : "tool_life"},
                    { "data" : "total_actual_so"},
                    { "data" : "percentage"},
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

            $('#modalDeviceCreateUpdate').on('hidden.bs.modal', '.modal', function () {
                $('.reset').removeClass('btn-outline-dark');
            });

            // ==================================================================================================
            // ========================================= DEVICE HISTORY =========================================
            // ==================================================================================================
            $('button[data-bs-toggle="tab"], a[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
                toggleHistoryResetFilters();
            });

            dataTableDeviceHistory = $("#tableDeviceHistory").DataTable({
                processing: false,
                serverSide: true,
                responsive: true,
                // pageLength: 500,
                ordering: false,

                language: {
                    info: "Showing _START_ to _END_ of _TOTAL_ Device Record",
                    lengthMenu: "Show _MENU_ Device Record",
                },

                ajax: {
                    url: "view_device_history",
                    data: function (d) {
                        d.deviceCode = deviceCode;
                    }
                },

                columns: [
                    {data: "DeliveryUpdateRecordDate"},
                    {data: "ActualSO"},
                    {data: "Variance"}
                ],

                columnDefs: [
                    {
                        targets: "_all",
                        className: "text-start"
                    }
                ]
            });

            $(document).on('click', '.actionDeviceHistory', function(e){
                e.preventDefault();
                toolLife                = $(this).attr('tool-life');
                diesetDate              = $(this).attr('dieset-date');
                deviceCode              = $(this).attr('device-code');
                deviceName              = $(this).attr('device-name');
                diesetDieNo             = $(this).attr('dieset-die_no');
                deviceTotalQty          = $(this).attr('device-total_qty');
                diesetDrawingNo         = $(this).attr('dieset-drawing_no');
                diesetDrawingRevision   = $(this).attr('dieset-drawing_revision');
                historySalesQuantityYEC   = $(this).attr('yec-sales-quantity');
                historySalesQuantityPMI   = $(this).attr('pmi-sales-quantity');

                let totalSalesQuantity = Number(historySalesQuantityYEC) + Number(historySalesQuantityPMI);
                $("#historyDeviceName").text(deviceName);
                $("#historyDeviceCode").text(deviceCode);
                $("#historyToolLife").text(Number(toolLife || 0).toLocaleString());
                $("#historyTotalQuantity").text(Number(deviceTotalQty || 0).toLocaleString());
                $('#historyDieSetDrawingNo').text(diesetDrawingNo ?? '—');
                $('#historyDieSetDrawingRevision').text(diesetDrawingRevision ?? '—');
                $('#historyDieSetDieNo').text(diesetDieNo ?? '—');
                $('#historyDieSetReceivedDate').text(diesetDate ?? '—');
                $('#historySalesQuantityYEC').text(Number(historySalesQuantityYEC || 0).toLocaleString());
                $('#historySalesQuantityPMI').text(Number(historySalesQuantityPMI || 0).toLocaleString());
                $('#historySalesTotalQuantity').text(totalSalesQuantity);
                $('#historyToolLifeBalance').text((Number(toolLife) - Number(totalSalesQuantity)).toLocaleString());
                calculateDieSetAge(diesetDate);

                dataTableDeviceHistory.draw();
                dataTableDeviceResetHistory.draw();

                setTimeout(() => {
                    const rowValue = $("#tableDeviceHistory tbody tr").eq(0).find("td").eq(2).text().trim();
                    $('#historyTotalQuantity').text(rowValue);
                }, 1000);
            });

            $('#buttonResetHistoryFilter').click(function (e) {
                e.preventDefault();

                const dateFrom = $('#historyDateFrom').val();
                const dateTo = $('#historyDateTo').val();
                deviceCode = $('#historyDeviceCode').text();

                if (!dateFrom || !dateTo) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Date Required',
                        text: 'Please select Date From and Date To.'
                    });
                    return;
                }

                if (dateFrom > dateTo) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Date Range',
                        text: 'Date From cannot be later than Date To.'
                    });
                    return;
                }

                // Swal.fire({
                //     title: 'Loading...',
                //     text: 'Getting current tool life balance.',
                //     allowOutsideClick: false,
                //     didOpen: () => {
                //         Swal.showLoading();
                //     }
                // });

                GetDeviceHistoryLastVariance(deviceCode, dateFrom, dateTo);
            });

            $("#deviceResetForm").submit(function(event){
                event.preventDefault();
                DeviceToolLifeReset();
            });

            // ==================================================================================================
            // ====================================== DEVICE RESET HISTORY ======================================
            // ==================================================================================================
            dataTableDeviceResetHistory = $("#tableDeviceResetHistory").DataTable({
                processing: false,
                serverSide: true,
                responsive: true,
                // pageLength: 500,
                ordering: false,

                language: {
                    info: "Showing _START_ to _END_ of _TOTAL_ Device Reset History Records",
                    lengthMenu: "Show _MENU_ Device Reset History Records",
                },

                ajax: {
                    url: "view_device_reset_history",
                    data: function (d) {
                        d.deviceCode = deviceCode;
                    }
                },

                columns: [
                    {data: "action"},
                    {data: "status"},
                    {data: "from"},
                    {data: "to"},
                    {data: "variance"},
                    {data: "uploaded_file"},
                    {data: "reset_by"},
                    {data: "approve_by"}
                ],

                columnDefs: [
                    {
                        targets: "_all",
                        className: "text-start"
                    }
                ]
            });

            $(document).on('click', '.actionDeviceResetHistory', function(e){
                e.preventDefault();
                deviceCode              = $(this).attr('device-code');
                deviceDateFrom              = $(this).attr('date-from');
                deviceDateTo              = $(this).attr('date-to');

                dataTableDeviceHistoryByDate.draw()
                $('#modalDeviceResetHistory').modal('show');
            });

            dataTableDeviceHistoryByDate = $("#tableDeviceHistoryByDate").DataTable({
                processing: false,
                serverSide: true,
                responsive: true,
                // pageLength: 500,
                ordering: false,

                language: {
                    info: "Showing _START_ to _END_ of _TOTAL_ Device Record By Date",
                    lengthMenu: "Show _MENU_ Device Record By Date",
                },

                ajax: {
                    url: "view_device_history_by_date",
                    data: function (d) {
                        d.deviceCode = deviceCode;
                        d.deviceDateFrom = deviceDateFrom;
                        d.deviceDateTo = deviceDateTo;
                    }
                },

                columns: [
                    {data: "DeliveryUpdateRecordDate"},
                    {data: "ActualSO"},
                    {data: "Variance"}
                ],

                columnDefs: [
                    {
                        targets: "_all",
                        className: "text-start"
                    }
                ]
            });

            $(document).on('click', '.actionResetDeviceApproval', function(e){
                let deviceId = $(this).attr('device-id');
                let approvalStatus = $(this).attr('approval-status');
                let deviceCode = $(this).attr('device-code');

                $('#txtResetDeviceApprovalId').val(deviceId);
                $('#txtResetDeviceApprovalStatus').val(approvalStatus);
                $('#txtResetDeviceCode').val(deviceCode);

                if(approvalStatus == '1'){
                    $("#btnResetDeviceApproval").addClass('btn-success').removeClass('btn-danger')
                    $('#approvalTitle').text(' Approve')
                    $("#iBtnResetDeviceApprovalIcon").html('<i class="fa-solid fa-thumbs-up"></i>')
                    $('#btnCloseResetDeviceApproval').addClass('btn-success').removeClass('btn-danger')
                }
                else{
                    $("#btnResetDeviceApproval").addClass('btn-danger').removeClass('btn-success')
                    $("#iBtnResetDeviceApprovalIcon").html('<i class="fa-solid fa-thumbs-down"></i>')
                    $('#btnCloseResetDeviceApproval').addClass('btn-danger').removeClass('btn-success')
                    $('#approvalTitle').text(' Disapprove')
                }

                $('#modalResetDeviceApproval').modal('show');
            })

            $("#resetDeviceApprovalForm").submit(function(event){
                event.preventDefault();
                ResetDeviceApproval();
            });
        });
    </script>
@endsection
