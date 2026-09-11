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
                toastr.success('Successfully saved!!!');
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
