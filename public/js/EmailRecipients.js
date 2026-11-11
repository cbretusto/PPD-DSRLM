
function EmailRecipientsCreateUpdate() {
    const ajaxEmailRecipientsCreateUpdate = {
        url: "email_recipients_create_update",
        method: "POST",
        data: $('#formEmailRecipients').serialize(),
        dataType: "json",
        beforeSendCallback: function(xhr) {
            $("#iBtnEmailRecipientIcon").addClass('spinner-border spinner-border-sm');
            $("#btnEmailRecipient").addClass('disabled');
            $("#iBtnEmailRecipientIcon").removeClass('fa fa-check');
        },
        successCallback: (response) => {
            if (response['validationHasError'] == 1) {
                toastr.error('Saving failed!');
            }else if(response['hasError'] == 0) {
                $('#modalEmailCreateUpdate').modal('hide');
                toastr.success('Successfully saved!!!');
                dataTableEmailRecipients.draw();
            }else{
                toastr.error('Email recipient already exist!');
                $('#modalEmailCreateUpdate').modal('hide');
            }

            $("#iBtnEmailRecipientIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnEmailRecipient").removeClass('disabled');
            $("#iBtnEmailRecipientIcon").addClass('fa fa-check');
        },
        errorCallback: (xhr) => {
            toastr.error('Saving email recipient failed!');
            handleValidatorErrors(xhr.responseJSON.errors);

            $("#iBtnEmailRecipientIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnEmailRecipient").removeClass('disabled');
            $("#iBtnEmailRecipientIcon").addClass('fa fa-check');
        }
    };

    ajaxRequest(ajaxEmailRecipientsCreateUpdate);
}


function GetEmailRecipientInfoByIdToEdit(EmailRecipientId){
    const ajaxGetEmailRecipientByIdToEdit = {
        url: 'get_email_recipients_info_by_id',
        method: 'GET',
        data: {
            EmailRecipientId: EmailRecipientId
        },
        successCallback: (response) => {
            let requestEmailRecipientInfo = response['requestEmailRecipientInfo'];

            if(requestEmailRecipientInfo.length > 0){
                $("#textEmailRecipient").val(requestEmailRecipientInfo[0].email);
            }
        },
        errorCallback: () => {

        }
    };

    ajaxRequest(ajaxGetEmailRecipientByIdToEdit);
}


function EmailRecipientChangeStatus(){
    const ajaxChangeEmailRecipientStatus = {
        url: 'change_email_recipients_status',
        method: "POST",
        data: $('#formEmailRecipientChangeStatus').serialize(),
        dataType: "json",
        beforeSendCallback: function(xhr) {
            $("#iBtnEmailRecipientChangeStatusIcon").addClass('spinner-border spinner-border-sm');
            $("#btnEmailRecipientChangeStatus").addClass('disabled');
            $("#iBtnEmailRecipientChangeStatusIcon").removeClass('fa fa-check');
        },
        successCallback: (response) => {
            if(response['hasError'] == 0){
                if($("#txtEmailRecipientChangeStatus").val() == 0){
                    toastr.success('Email recipient activation success!');
                    $("#txtEmailRecipientChangeStatus").val(1);
                }
                else{
                    toastr.success('Email recipient deactivation success!');
                    $("#txtEmailRecipientChangeStatus").val(0);
                }

                $('#modalEmailRecipientChangeStatus').modal('hide');
                dataTableEmailRecipients.draw();
            }

            $("#iBtnEmailRecipientChangeStatusIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnEmailRecipientChangeStatus").removeClass('disabled');
            $("#iBtnEmailRecipientChangeStatusIcon").addClass('fa fa-check');
        },
        errorCallback: () => {
            toastr.error('An error occurred while processing your request.');

            $("#iBtnEmailRecipientChangeStatusIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnEmailRecipientChangeStatus").removeClass('disabled');
            $("#iBtnEmailRecipientChangeStatusIcon").addClass('fa fa-check');
        }
    };

    ajaxRequest(ajaxChangeEmailRecipientStatus);
}
