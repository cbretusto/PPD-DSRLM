/**
 * Reusable function for using Ajax Request
 *
 * @param {object} options
 */
const ajaxRequest = (options) => {
    var defaults = {
        url: '',
        method: 'GET',
        data: {},
        headers: {},
        dataType: 'json',
        processData: true,
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        beforeSendCallback: null,
        successCallback: () => {},
        errorCallback: () => {}
    };

    // Merge default options with user-provided options
    options = $.extend({}, defaults, options);

    if (options.data instanceof FormData) {
        options.processData = false;
        options.contentType = false;
    }

    $.ajax({
        url: options.url,
        method: options.method,
        data: options.data,
        headers: options.headers,
        dataType: options.dataType,
        processData: options.processData,
        contentType: options.contentType,
        beforeSend(xhr) {
            if (typeof options.beforeSendCallback === 'function') {
                options.beforeSendCallback(xhr);
            }
        },
        success(response) {
            options.successCallback(response);
        },
        error(xhr, status, error) {
            options.errorCallback(xhr, status, error);
        }
    });
};

/**
 * Set invalid class to elements for each field in the errors object
 *
 * @param {object} errors
 */

function handleValidatorErrors(errors) {
    // Remove all existing error states in the form
    $('input, select, textarea').removeClass('is-invalid').removeAttr('title');
    $('div.invalid-feedback[id$="-error"]').remove(); // remove all dynamic error messages

    // Loop through each field in the errors object
    for (let field in errors) {
        if (errors.hasOwnProperty(field)) {
            let fieldErrorMessage = errors[field];

            // Target any form control with that name
            let $field = $(`[name="${field}"]`);

            if ($field.length) {
                $field.addClass('is-invalid');
                $field.attr('title', fieldErrorMessage);

                // OPTIONAL: Show custom error message using Bootstrap-style feedback
                let $errorDiv = $(`<div class="invalid-feedback" id="${field}-error">${fieldErrorMessage}</div>`);

                // Append the error div if it's not already there
                if (!$(`#${field}-error`).length) {
                    // Place it after the field
                    $field.after($errorDiv);
                }
            }
        }
    }
}

/**
 * Automatically resets all forms inside any modal when it's hidden.
 * Applies to all modals on the page.
 */
const resetModalFormValues = () => {
    // Use a delegated event to listen for any modal hidden event
    $(document).on('hidden.bs.modal', '.modal', function () {
        const $modal = $(this);
        const $form = $modal.find('form');

        if ($form.length) {
            console.log(`Resetting form inside modal: #${$modal.attr('id')}`);

            // Reset the form fields
            $form[0].reset();

            // Clear Select2 fields
            $form.find('select.select2-hidden-accessible')
                .val('')
                .trigger('change');

            // Re-enable selects if disabled
            $form.find('select').prop('disabled', false);

            // Remove validation classes and tooltips
            $form.find('input, select, textarea')
                .removeClass('is-invalid')
                .removeAttr('title');

            // Remove error feedback elements
            $form.find('div.invalid-feedback[id$="-error"]').remove();
        }
    });
};


