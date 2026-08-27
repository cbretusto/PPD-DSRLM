function RemarkCreateUpdate() {
    const ajaxRemarkCreateUpdate = {
        url: "remark_create_update",
        method: "POST",
        data: $('#formRemark').serialize(),
        dataType: "json",
        beforeSendCallback: function(xhr) {
            $("#iBtnRemarkIcon").addClass('spinner-border spinner-border-sm');
            $("#btnRemark").addClass('disabled');
            $("#iBtnRemarkIcon").removeClass('fa fa-check');
        },
        successCallback: (response) => {
            if (response['validationHasError'] == 1) {
                toastr.error('Saving failed!');
            }else if(response['hasError'] == 0) {
                $('#modalRemarkCreateUpdate').modal('hide');
                toastr.success('Successfully saved!!!');
                dataTableMarkup.draw();
            }else{
                toastr.error('Remark already exist!');
                $('#modalRemarkCreateUpdate').modal('hide');
            }
            $("#iBtnRemarkIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnRemark").removeClass('disabled');
            $("#iBtnRemarkIcon").addClass('fa fa-check');
        },
        errorCallback: (xhr) => {
            toastr.error('Saving user failed!');
            handleValidatorErrors(xhr.responseJSON.errors);

            $("#iBtnRemarkIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnRemark").removeClass('disabled');
            $("#iBtnRemarkIcon").addClass('fa fa-check');
        }
    };

    ajaxRequest(ajaxRemarkCreateUpdate);
}

function GetRemarkInfoByIdToEdit(RemarkId){
    const ajaxGetRemarkByIdToEdit = {
        url: 'get_remark_info_by_id',
        method: 'GET',
        data: {
            RemarkId: RemarkId
        },
        successCallback: (response) => {
            let requestRemarkInfo = response['requestRemarkInfo']
            if(requestRemarkInfo.length > 0){
                $("#textRemark").val(requestRemarkInfo[0].remark);
            }
        },
        errorCallback: () => {

        }
    };
    ajaxRequest(ajaxGetRemarkByIdToEdit);
}

function RemarkChangeStatus(){
    const ajaxChangeRemarkStatus = {
        url: 'change_remark_status',
        method: "POST",
        data: $('#formRemarkChangeStatus').serialize(),
        dataType: "json",
        beforeSendCallback: function(xhr) {
            $("#iBtnRemarkChangeStatusIcon").addClass('spinner-border spinner-border-sm');
            $("#btnRemarkChangeStatus").addClass('disabled');
            $("#iBtnRemarkChangeStatusIcon").removeClass('fa fa-check');
        },
        successCallback: (response) => {
            if(response['hasError'] == 0){
                if($("#txtRemarkChangeStatus").val() == 0){
                    toastr.success('Remark activation success!');
                    $("#txtRemarkChangeStatus").val() == 1;
                }
                else{
                    toastr.success('Remark deactivation success!');
                    $("#txtRemarkChangeStatus").val() == 0;
                }
                $('#modalRemarkChangeStatus').modal('hide');
                dataTableMarkup.draw();
            }
            $("#iBtnRemarkChangeStatusIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnRemarkChangeStatus").removeClass('disabled');
            $("#iBtnRemarkChangeStatusIcon").addClass('fa fa-check');
        },
        errorCallback: () => {
            toastr.error('An error occurred while processing your request.');
            $("#iBtnRemarkChangeStatusIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnRemarkChangeStatus").removeClass('disabled');
            $("#iBtnRemarkChangeStatusIcon").addClass('fa fa-check');
        }
    };
    ajaxRequest(ajaxChangeRemarkStatus);
}
