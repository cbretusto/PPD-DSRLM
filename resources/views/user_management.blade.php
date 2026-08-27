@php
    session_start();
    $layout = 'layouts.layout';
    if(isset($_SESSION['invoice_revision_request_id'])){
        $layout = 'layouts.layout';
    }else{
        $layout = 'layouts.no_access';
    }
@endphp
@extends($layout)
@section('title', 'User Managemnet')
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

        .nav-tabs .nav-link {
            font-weight: 500;
        }

        .nav-tabs .nav-link.active {
            background-color: #343a40;
            color: white;
        }
    </style>

    <div class="content-wrapper layout-fixed">
        <section class="content p-3">
            <div class="container-fluid">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <!-- Tabs Navigation -->
                        <ul class="nav nav-tabs mb-3" id="userTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="userManagementTab" data-bs-toggle="tab" data-bs-target="#userManagement" type="button" role="tab" aria-controls="userManagement" aria-selected="true">
                                    <i class="fas fa-users-cog me-1"></i> User Management
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="userClassificationTab" data-bs-toggle="tab" data-bs-target="#userClassification" type="button" role="tab" aria-controls="userClassification" aria-selected="false">
                                    <i class="fas fa-user-check me-1"></i> Classification
                                </button>
                            </li>
                        </ul>

                        <!-- Tabs Content -->
                        <div class="tab-content" id="userTabContent">
                            <!-- Tab 1: User Management -->
                            <div class="tab-pane fade show active" id="userManagement" role="tabpanel" aria-labelledby="userManagementTab">
                                <div class="d-flex justify-content-end mb-3">
                                    <button type="button" class="btn btn-dark" id="buttonCreateUser" data-bs-toggle="modal" data-bs-target="#modalCreateUpdateUserManagement">
                                        <i class="fas fa-plus me-1"></i> New User
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table id="tableUserManagement" class="table table-bordered table-striped table-hover align-middle nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Status</th>
                                                <th>Employee<br>No.</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Department</th>
                                                <th>Position</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab 2: User Classification -->
                            <div class="tab-pane fade" id="userClassification" role="tabpanel" aria-labelledby="userClassificationTab">
                                <div class="d-flex justify-content-end mb-3">
                                    <button type="button" class="btn btn-dark" id="buttonCreateUserClassification" data-bs-toggle="modal" data-bs-target="#modalCreateUserClassification">
                                        <i class="fas fa-plus me-1"></i> User Classification
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table id="tableUserClassification" class="table table-bordered table-striped table-hover align-middle nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Name</th>
                                                <th>Classification</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                        </div> <!-- /.tab-content -->
                    </div> <!-- /.card-body -->
                </div> <!-- /.card -->
            </div> <!-- /.container-fluid -->
        </section>
    </div>

    <!------------------------------------------------------------------------------------------------------------------------------------->
    <!---------------------------------------------------------- USER MANAGEMENT ---------------------------------------------------------->
    <!------------------------------------------------------------------------------------------------------------------------------------->
    <!-- User Management Modal Start -->
    <div class="modal fade" id="modalCreateUpdateUserManagement" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <form method="post" id="formUserManagement" autocomplete="off">
                    @csrf
                    <!-- Top Header -->
                    <div class="bg-dark bg-gradient text-white p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-user fs-4"></i>
                                </div>

                                <div>
                                    <h5 class="mb-1 fw-bold">
                                        User Setup
                                    </h5>

                                    <small class="opacity-75">
                                        Create or update user information
                                    </small>
                                </div>
                            </div>

                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="modal-body p-4">
                        <input type="text" class="input_hidden" id="textUserId" name="user_id" readonly>
                        <div class="row">
                            <!-- Name -->
                            <div class="col-md-12 mb-4">
                                <label for="slctEmployeeNameWID" class="form-label fw-semibold">Name:</label>

                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-user text-dark"></i>
                                    </span>

                                    <select class="form-select select2bs5 get-rapidx-user bg-light border-start-0" id="slctEmployeeNameWID" name="name_w_id">
                                    </select>
                                </div>
                            </div>

                            <!-- Employee No. -->
                            <div class="col-md-6 mb-4">
                                <label for="txtEmployeeNo" class="form-label fw-semibold"> Employee No.:</label>

                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-id-card text-dark"></i>
                                    </span>

                                    <input type="text" class="form-control class-disabled bg-light border-start-0" id="txtEmployeeNo" name="employee_no" placeholder="Auto generate employee no.">
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-4">
                                <label for="txtEmployeeEmail" class="form-label fw-semibold">Email:</label>

                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-envelope text-dark"></i>
                                    </span>

                                    <input type="text" class="form-control class-disabled bg-light border-start-0" id="txtEmployeeEmail" name="email" placeholder="Auto generate email">
                                </div>
                            </div>

                            <!-- Department -->
                            <div class="col-md-6 mb-2">
                                <label for="slctEmployeeDepartment" class="form-label fw-semibold">Department:</label>

                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-building text-dark"></i>
                                    </span>

                                    <select class="form-select get-systemone-department class-disabled bg-light border-start-0"id="slctEmployeeDepartment"name="department">
                                    </select>
                                </div>
                            </div>

                            <!-- Position -->
                            <div class="col-md-6 mb-2">
                                <label for="slctEmployeePosition" class="form-label fw-semibold">Position:</label>

                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-briefcase text-dark"></i>
                                    </span>
                                    <select class="form-select get-systemone-position class-disabled bg-light border-start-0" id="slctEmployeePosition" name="position">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer justify-content-between bg-light">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>
                            Cancel
                        </button>

                        <button type="submit" id="btnUserManagement" class="btn btn-dark px-4">
                            <i id="iBtnUserManagementIcon" class="fas fa-check me-1"></i>Save User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- User Management Modal End -->

    <!-- User Management Status Modal Start -->
    <div class="modal fade" id="modalUserManagementChangeUserStatus" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <form method="post" id="formUserManagementChangeStatus" autocomplete="off">
                    @csrf
                    <!-- Top Header -->
                    <div class="bg-dark bg-gradient text-white p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-user-check fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold"
                                        id="h4UserManagementChangeStatusTitle">
                                        Change Status
                                    </h5>

                                    <small class="opacity-75">
                                        Update user account status
                                    </small>
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <input type="text" class="input_hidden" name="user_id" id="txtUserManagementChangeStatusId">
                        <input type="text" class="input_hidden" name="status" id="txtUserManagementChangeStatus">
                        <div class="text-center py-2">
                            <div class="bg-light rounded-4 p-4">
                                <div class="mb-3">
                                    <div class="bg-dark bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-user-shield text-dark fs-3"></i>
                                    </div>
                                </div>

                                <label id="lblUserManagementChangeStatusLabel" class="fw-semibold fs-6 mb-0"></label>
                                <p class="text-muted small mb-0 mt-2">
                                    Are you sure you want to change the status
                                    of this user?
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer justify-content-between bg-light">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>

                        <button type="submit" id="btnUserManagementChangeUserStatus" class="btn btn-dark px-4">
                            <i id="iBtnUserManagementChangeUserStatusIcon"class="fas fa-check me-1"></i>Save Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- User Management Status Modal End -->

    <!---------------------------------------------------------------------------------------------------------------------------------------->
    <!------------------------------------------------------------- USER CLASSIFICATION ------------------------------------------------------------>
    <!---------------------------------------------------------------------------------------------------------------------------------------->
    <!-- User Classification Modal Start -->
    <div class="modal fade" id="modalCreateUserClassification" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <form method="post" id="formUserClassification" autocomplete="off">
                    @csrf
                    <!-- Top Header -->
                    <div class="bg-dark bg-gradient text-white p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-user-tag fs-4"></i>
                                </div>

                                <div>
                                    <h5 class="mb-1 fw-bold">
                                        User Classification
                                    </h5>

                                    <small class="opacity-75">
                                        Assign classification to a user
                                    </small>
                                </div>
                            </div>

                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="modal-body p-4">
                        <div class="row">
                            <!-- Name -->
                            <div class="col-md-12 mb-4">
                                <label for="slctEmployeeFromUserManagement" class="form-label fw-semibold">Name:</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-user text-dark"></i>
                                    </span>

                                    <select class="form-select select2bs5 get-ppd_dsrlm-user bg-light border-start-0" id="slctEmployeeFromUserManagement" name="user_id">
                                    </select>
                                </div>
                            </div>

                            <!-- Classification -->
                            <div class="col-md-12 mb-2">
                                <label for="slctUserClassification" class="form-label fw-semibold"> Classification:</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-tags text-dark"></i>
                                    </span>

                                    <select class="form-select bg-light border-start-0" id="slctUserClassification" name="user_classification">
                                        <option value="" selected disabled>Select Classification</option>
                                        <option value="1">Checked By</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer justify-content-between bg-light">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>

                        <button type="submit" id="btnUserClassification" class="btn btn-dark px-4">
                            <i id="iBtnUserClassificationIcon" class="fas fa-check me-1"></i>Save Classification
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- User Classification Modal End -->

    <!-- User Classification Remove Modal Start -->
    <div class="modal fade" id="modalUserManagementRemoveUserClassification" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                <form method="post" id="formUserManagementRemoveUserClassification" autocomplete="off">
                    @csrf

                    <!-- Top Header -->
                    <div class="bg-dark bg-gradient text-white p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-user-minus fs-4"></i>
                                </div>

                                <div>
                                    <h5 class="mb-1 fw-bold">
                                        Remove Classification
                                    </h5>

                                    <small class="opacity-75">
                                        Remove user classification
                                    </small>
                                </div>
                            </div>

                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="modal-body p-4">
                        <input type="text" class="input_hidden" name="user_id" id="txtUserManagementRemoveUserClassificationId">
                        <div class="text-center py-2">
                            <div class="bg-light rounded-4 p-4">
                                <div class="mb-3">
                                    <div class="bg-dark bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-user-tag text-dark fs-3"></i>
                                    </div>
                                </div>

                                <h6 class="fw-bold mb-2">
                                    Remove User Classification?
                                </h6>

                                <p class="text-muted small mb-0">
                                    Are you sure you want to remove the
                                    classification from this user?
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer justify-content-between bg-light">
                        <button type="button"class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>

                        <button type="submit"id="btnUserManagementRemoveUserClassification" class="btn btn-dark px-4">
                            <i id="iBtnUserManagementRemoveUserClassificationIcon"class="fas fa-check me-1"></i>Remove Classification
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- User Classification Remove Modal End -->
@endsection

<!-- JS CONTENT --}} -->
@section('js_content')
    <script type="text/javascript">
        let dataTableUserManagement
        let dataTableUserResult

        $(document).ready(function () {
            resetModalFormValues();

            $('.select2bs5').each(function () {
                $(this).select2({
                    theme: 'bootstrap-5',
                    dropdownAutoWidth: true,
                    dropdownParent: $(this).closest('.modal')
                });

            });

            // -------------------------------------------------------------------------------------------------------------------------------------
            // ---------------------------------------------------------- USER MANAGEMENT ----------------------------------------------------------
            // -------------------------------------------------------------------------------------------------------------------------------------
            UserManagementGetRapidxUserActiveInSystemOne($('.get-rapidx-user'));
            UserManagementGetSystemOneDepartment($('.get-systemone-department'));
            UserManagementGetSystemOnePosition($('.get-systemone-position'));

            dataTableUserManagement = $("#tableUserManagement").DataTable({
                "processing" : false,
                "serverSide" : true,
                "responsive": true,
                "order": [[1,'asc'],[4, "asc"]],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ User Record",
                    "lengthMenu": "Show _MENU_ User Record",
                },
                "ajax" : {
                    url: "view_user",
                },
                "columns":[
                    { "data" : "action", orderable:false, searchable:false},
                    { "data" : "status",
                        "defaultContent": 'N/A',
                        "name": 'status',
                        "orderable": true,
                        "searchable": true,
                        "render": function (data, type, row) {
                            let status
                            let badgeClass
                            switch (row.status) {
                                case 0:
                                    status = 'Active';
                                    badgeClass = 'success';
                                    break;
                                case 1:
                                    status = 'Inactive';
                                    badgeClass = 'danger';
                                    break;
                                case 2:
                                    status = 'Resigned';
                                    badgeClass = 'warning shadow';
                                    break;
                                default:
                                    status = 'Unknown';
                                    badgeClass = 'secondary';
                                    break;
                            }
                            return '<center><span class="badge bg-' + badgeClass + '">' + status + '</span></center>';
                        },
                    },
                    { "data" : "user_management_rapidx_user_info.employee_number"},
                    { "data" : "user_management_rapidx_user_info.name"},
                    { "data" : "user_management_rapidx_user_info.email"},
                    { "data" : "user_management_systemone_department_info.Department"},
                    { "data" : "user_management_systemone_position_info.Position"},
                ],
            });

            $('#slctEmployeeNameWID').change(function (e) {
                e.preventDefault();
                console.log('1');
                const ajaxSelectName = {
                    url: 'get_rapidx_user_active_in_systemone',
                    method: 'GET',
                    successCallback: (response) => {
                        let getDataById     = $(this).val()
                        let getDataByName   = response['rapidxNameActiveInSystemone']
                        let department      = ''
                        let position        = ''

                        if(getDataByName.length > 0){
                            for(let index = 0; index < getDataByName.length; index++){
                                if(getDataByName[index].id == getDataById){
                                    $('#txtEmployeeNo').val(getDataByName[index].employee_number)
                                    if(getDataByName[index].email != null){
                                        $('#txtEmployeeEmail').val(getDataByName[index].email)
                                    }else{
                                        $('#txtEmployeeEmail').val('N/A')
                                    }

                                    if(getDataByName[index].rapidx_systemone_employee_info != null){
                                        department  = getDataByName[index].rapidx_systemone_employee_info.fkDepartment
                                        position    = getDataByName[index].rapidx_systemone_employee_info.fkPosition
                                    }

                                    if(getDataByName[index].rapidx_systemone_subcon_info != null){
                                        department  = getDataByName[index].rapidx_systemone_subcon_info.fkDepartment
                                        position    = getDataByName[index].rapidx_systemone_subcon_info.fkPosition
                                    }

                                    $('#slctEmployeeDepartment').val(department).trigger('change')
                                    $('#slctEmployeePosition').val(position).trigger('change')

                                    let departmentText = $('#slctEmployeeDepartment option:selected').text().split("-");
                                    let departmentPerInvoice = departmentText[0].replace(/\s+/g, '');
                                    console.log('departmentText: ', departmentPerInvoice);

                                    $('.invoice-department').prop('checked', false);

                                    $('.invoice-department').each(function () {
                                        let value = $(this).val().replace(/\s+/g, '');
                                        if (value.toLowerCase() === departmentPerInvoice.toLowerCase()) {
                                            $(this).prop('checked', true);
                                        }
                                    });

                                }
                            }
                        }
                    },
                    errorCallback: () => {
                    }
                };
                ajaxRequest(ajaxSelectName);
            });

            $("#formUserManagement").submit(function(event){
                event.preventDefault();
                UserManagementUserCreateUpdate();
            });

            $(document).on('click', '.actionUpdateUserManagement', function(e){
                e.preventDefault();
                let UserId = $(this).attr('user-id');
                    $("#textUserId").val(UserId);
                    UserManagementGetUserInfoByIdToEdit(UserId);
            });

            $(document).on('click', '.actionUserManagementChangeStatus', function(){
                let userStatus = $(this).attr('status');
                let userId = $(this).attr('user-id');
                $("#txtUserManagementChangeStatus").val(userStatus);
                $("#txtUserManagementChangeStatusId").val(userId);

                if(userStatus == 0){
                    $("#lblUserManagementChangeStatusLabel").text('Are you sure to activate?');
                    $("#h4UserManagementChangeStatusTitle").html('Activate User');
                }
                else{
                    $("#lblUserManagementChangeStatusLabel").text('Are you sure to deactivate?');
                    $("#h4UserManagementChangeStatusTitle").html('Deactivate User');
                }
            });

            $("#formUserManagementChangeStatus").submit(function(event){
                event.preventDefault();
                UserManagementChangeStatus();
            });

            // -------------------------------------------------------------------------------------------------------------------------------------
            // ----------------------------------------------------------- USER CLASSIFICATION -----------------------------------------------------------
            // -------------------------------------------------------------------------------------------------------------------------------------
            GetInfoFromUserManagement($('.get-ppd_dsrlm-user'));

            $('#userClassification').click(function (e) {
                e.preventDefault();
                GetInfoFromUserManagement($('.get-ppd_dsrlm-user'));
            });

            dataTableUserClassification = $("#tableUserClassification").DataTable({
                "processing" : false,
                "serverSide" : true,
                "responsive": true,
                "order": [[3,'asc']],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ User Classification Record",
                    "lengthMenu": "Show _MENU_ User Classification Record",
                },
                "ajax" : {
                    url: "view_user_classification",
                },
                "columns":[
                    { "data" : "action", orderable:false, searchable:false},
                    { "data" : "user_management_rapidx_user_info.name"},
                    { "data" : "classification",
                        "defaultContent": 'N/A',
                        "name": 'classification',
                        "orderable": true,
                        "searchable": true,
                        "render": function (data, type, row) {
                            switch (row.classification) {
                                case '1':
                                    return "Checked By";
                                case '2':
                                    return "Noted By";
                                case '3':
                                    return "Approved By";
                                case '4':
                                    return "Conformed By";
                                default:
                                    return "Unknown";
                            }
                        },
                    }
                ],
            });

            $("#formUserClassification").submit(function(event){
                event.preventDefault();
                CreateUserClassification();
            });

            $(document).on('click', '.actionUserManagementRemoveUserClassification', function(){
                let userId = $(this).attr('user-id');
                $("#txtUserManagementRemoveUserClassificationId").val(userId);
            });

            $("#formUserManagementRemoveUserClassification").submit(function(event){
                event.preventDefault();
                UserManagementRemoveUserClassification();
            });
        });
    </script>
@endsection
