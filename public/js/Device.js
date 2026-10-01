function DeviceCreateUpdate() {
    const ajaxDeviceCreateUpdate = {
        url: "device_create_update",
        method: "POST",
        data: $('#formDevice').serialize(),
        dataType: "json",
        beforeSendCallback: function(xhr) {
            $("#iBtnDeviceIcon").addClass('spinner-border spinner-border-sm');
            $("#btnDevice").addClass('disabled');
            $("#iBtnDeviceIcon").removeClass('fa fa-check');
        },
        successCallback: (response) => {
            if (response['validationHasError'] == 1) {
                toastr.error('Saving failed!');
            }else if(response['hasError'] == 0) {
                $('#modalDeviceCreateUpdate').modal('hide');
                toastr.success('Successfully saved!');
                dataTableDevice.draw();
            }else{
                toastr.error('Device already exist!');
                $('#modalDeviceCreateUpdate').modal('hide');
            }
            $("#iBtnDeviceIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnDevice").removeClass('disabled');
            $("#iBtnDeviceIcon").addClass('fa fa-check');
        },
        errorCallback: (xhr) => {
            toastr.error('Saving device failed!');
            handleValidatorErrors(xhr.responseJSON.errors);

            $("#iBtnDeviceIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnDevice").removeClass('disabled');
            $("#iBtnDeviceIcon").addClass('fa fa-check');
        }
    };

    ajaxRequest(ajaxDeviceCreateUpdate);
}

function GetDeviceInfoByIdToEdit(deviceId){
    const ajaxGetDeviceByIdToEdit = {
        url: 'get_device_info_by_id',
        method: 'GET',
        data: {
            deviceId: deviceId
        },
        successCallback: (response) => {
            let requestDeviceInfo = response['requestDeviceInfo']
            if(requestDeviceInfo.length > 0){
                $("#txtDeviceCode").val(requestDeviceInfo[0].device_code);
                $("#txtDeviceName").val(requestDeviceInfo[0].device_name);
                $("#txtToolLife").val(requestDeviceInfo[0].tool_life);
                $("#txtYecSalesQty").val(requestDeviceInfo[0].yec_sales_qty);
                $("#txtPmiSalesQty").val(requestDeviceInfo[0].pmi_sales_qty);
                $(`input[name="process_type"][value="${requestDeviceInfo[0].process_type}"]`).prop('checked', true);
            }
        },
        errorCallback: () => {


        }
    };
    ajaxRequest(ajaxGetDeviceByIdToEdit);
}

function DeviceChangeStatus(){
    const ajaxChangeDeviceStatus = {
        url: 'change_device_status',
        method: "POST",
        data: $('#formDeviceChangeStatus').serialize(),
        dataType: "json",
        beforeSendCallback: function(xhr) {
            $("#iBtnDeviceChangeStatusIcon").addClass('spinner-border spinner-border-sm');
            $("#btnDeviceChangeStatus").addClass('disabled');
            $("#iBtnDeviceChangeStatusIcon").removeClass('fa fa-check');
        },
        successCallback: (response) => {
            if(response['hasError'] == 0){
                if($("#txtDeviceChangeStatus").val() == 0){
                    toastr.success('Device activation success!');
                    $("#txtDeviceChangeStatus").val() == 1;
                }
                else{
                    toastr.success('Device deactivation success!');
                    $("#txtDeviceChangeStatus").val() == 0;
                }
                $('#modalDeviceChangeStatus').modal('hide');
                dataTableDevice.draw();
            }
            $("#iBtnDeviceChangeStatusIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnDeviceChangeStatus").removeClass('disabled');
            $("#iBtnDeviceChangeStatusIcon").addClass('fa fa-check');
        },
        errorCallback: () => {
            toastr.error('An error occurred while processing your request.');
            $("#iBtnDeviceChangeStatusIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnDeviceChangeStatus").removeClass('disabled');
            $("#iBtnDeviceChangeStatusIcon").addClass('fa fa-check');
        }
    };
    ajaxRequest(ajaxChangeDeviceStatus);
}

function calculateDieSetAge(receivedDate) {
    if (!receivedDate) {
        $('#historyDieSetAge').text('0');
        $('#historyDieSetAgeMonths').text('0');
        return;
    }

    const received = new Date(receivedDate);
    const today = new Date();

    let years = today.getFullYear() - received.getFullYear();
    let months = today.getMonth() - received.getMonth();

    if (today.getDate() < received.getDate()) {
        months--;
    }

    if (months < 0) {
        years--;
        months += 12;
    }

    $('#historyDieSetAge').text(years);
    $('#historyDieSetAgeMonths').text(months);
}

function toggleHistoryResetFilters() {
    const activeTab = $('#deviceHistoryTabContent .tab-pane.active').attr('id');

    if (activeTab === 'deviceHistoryTab') {
        $('#historyResetFilters').removeClass('d-none');
        $('#historyResetButton').removeClass('d-none');
    } else {
        $('#historyResetFilters').addClass('d-none');
        $('#historyResetButton').addClass('d-none');
    }
}

function GetDeviceHistoryLastVariance(deviceCode, dateFrom, dateTo){
    const ajaxGetDeviceHistoryLastVariance = {
        url: 'device_history_last_variance',
        method: 'GET',
        data: {
            deviceCode: deviceCode,
            dateFrom: dateFrom,
            dateTo: dateTo
        },
        successCallback: (response) => {
            Swal.close();
            console.log('response:', response);

            if (response.hasError == 1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Reset Date Already Exists',
                    text: response.message || 'The selected reset date range already exists or overlaps with an existing reset period.'
                });
                return;
            }

            if (!response.success) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No History Found',
                    text: response.message || 'No device history found for the selected date range.'
                });
                return;
            }

            $('#getDeviceCode').val(deviceCode);
            $('#resetDateFrom').val(dateFrom);
            $('#resetDateTo').val(dateTo);
            $('#resetLastVariance').val(response.lastVariance);

            $('#deviceResetModal').modal('show');
        },
        errorCallback: (xhr) => {
            Swal.close();
            console.error(xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to get last variance.'
            });
        }
    };
    ajaxRequest(ajaxGetDeviceHistoryLastVariance);
}

function DeviceToolLifeReset() {
    const form = $('#deviceResetForm')[0];
    const formData = new FormData(form);

    const ajaxDeviceResetToolLife = {
        url: "device_reset_tool_life",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        beforeSendCallback: function(xhr) {
            $("#iBtnDeviceResetIcon").addClass('spinner-border spinner-border-sm');
            $("#btnDeviceReset").addClass('disabled');
            $("#iBtnDeviceResetIcon").removeClass('fa fa-check');
        },
        successCallback: (response) => {
            if (response['validationHasError'] == 1) {
                toastr.error('Saving failed!');
            }else if(response['hasError'] == 0) {
                $('#deviceResetModal').modal('hide');
                toastr.success('Successfully saved!');
                dataTableDeviceResetHistory.draw();
            }else{
                toastr.error('Device already exist!');
                $('#deviceResetModal').modal('hide');
            }
            $("#iBtnDeviceResetIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnDeviceReset").removeClass('disabled');
            $("#iBtnDeviceResetIcon").addClass('fa fa-check');
        },
        errorCallback: (xhr) => {
            toastr.error('Saving device failed!');
            handleValidatorErrors(xhr.responseJSON.errors);

            $("#iBtnDeviceResetIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnDeviceReset").removeClass('disabled');
            $("#iBtnDeviceResetIcon").addClass('fa fa-check');
        }
    };

    ajaxRequest(ajaxDeviceResetToolLife);
}

const UserManagementGetApproveBy = (element) => {
    let result = '';
    const ajaxGetSystemOneDepartment = {
        url: 'get_ppd_dsrlm_user_approve_by',
        method: 'GET',
        successCallback: (response) => {
            let userApproveBy = response['userApproveBy']
            if(userApproveBy.length > 0){
                result += '<option value="" disabled selected>-- Select User --</option>';
                for(let index = 0; index < userApproveBy.length; index++){
                    result += '<option value="' + userApproveBy[index].rapidx_user_id + '">' + userApproveBy[index].user_management_rapidx_user_info.name + '</option>';
                }
            }
            else{
                result += '<option value="" disabled>Not found</option>';
            }
            element.html(result);
        },
        errorCallback: () => {
            result = '<option value="" disabled>Reload Again</option>';
        }
    };
    ajaxRequest(ajaxGetSystemOneDepartment);
}

function ResetDeviceApproval(){
    const ajaxResetDeviceApproval = {
        url: 'reset_device_approval',
        method: "POST",
        data: $('#resetDeviceApprovalForm').serialize(),
        dataType: "json",
        beforeSendCallback: function(xhr) {
            $("#iBtnResetDeviceApprovalIcon").addClass('spinner-border spinner-border-sm');
            $("#btnResetDeviceApproval").addClass('disabled');
            $("#iBtnResetDeviceApprovalIcon").removeClass('');
        },
        successCallback: (response) => {
            if(response['hasError'] == 0){
                $('#modalResetDeviceApproval').modal('hide');
                dataTableDeviceResetHistory.draw();
            }
            $("#iBtnResetDeviceApprovalIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnResetDeviceApproval").removeClass('disabled');
            $("#iBtnResetDeviceApprovalIcon").addClass('');
        },
        errorCallback: (xhr) => {
            $("#iBtnResetDeviceApprovalIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnResetDeviceApproval").removeClass('disabled');
            $("#iBtnResetDeviceApprovalIcon").addClass('');
            handleValidatorErrors(xhr.responseJSON.errors);
            toastr.error('An error occurred while processing your request.');
        }
    };
    ajaxRequest(ajaxResetDeviceApproval);
}

