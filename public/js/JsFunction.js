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
    $('input, select, textarea')
        .removeClass('is-invalid')
        .removeAttr('title');

    $('label')
        .removeClass('border-danger text-danger');

    $('div.invalid-feedback[id$="-error"]').remove();

    for (let field in errors) {
        if (!errors.hasOwnProperty(field)) {
            continue;
        }

        let fieldErrorMessage = errors[field];

        // Laravel can return an array of messages
        if (Array.isArray(fieldErrorMessage)) {
            fieldErrorMessage = fieldErrorMessage[0];
        }

        // First try normal input name
        let $field = $(`[name="${field}"]`);

        // If not found, try array input name: field[]
        if (!$field.length) {
            $field = $(`[name="${field}[]"]`);
        }

        if (!$field.length) {
            continue;
        }

        /*
         * Radio / Checkbox
         */
        if ($field.is(':radio') || $field.is(':checkbox')) {
            $field.addClass('is-invalid');
            $field.attr('title', fieldErrorMessage);

            $field.each(function () {
                let inputId = $(this).attr('id');

                $(`label[for="${inputId}"]`)
                    .addClass('border-danger text-danger');
            });

            let $container = $field.first().closest('.d-flex');

            if ($container.length) {
                $container.after(`
                    <div class="invalid-feedback d-block" id="${field}-error">
                        ${fieldErrorMessage}
                    </div>
                `);
            }

            continue;
        }

        /*
         * Normal input / select / textarea / file input
         */
        $field.first()
            .addClass('is-invalid')
            .attr('title', fieldErrorMessage);

        let $errorDiv = $(`
            <div class="invalid-feedback" id="${field}-error">
                ${fieldErrorMessage}
            </div>
        `);

        $field.last().after($errorDiv);
    }
}

// function handleValidatorErrors(errors) {
//     $('input, select, textarea').removeClass('is-invalid').removeAttr('title');

//     $('label').removeClass('border-danger text-danger');

//     $('div.invalid-feedback[id$="-error"]').remove();

//     for (let field in errors) {
//         if (!errors.hasOwnProperty(field)) {
//             continue;
//         }

//         let fieldErrorMessage = errors[field];
//         let $field = $(`[name="${field}"]`);
//         if (!$field.length) {
//             continue;
//         }

//         if ($field.is(':radio') || $field.is(':checkbox')) {
//             $field.addClass('is-invalid');
//             $field.attr('title', fieldErrorMessage);
//             $field.each(function () {
//                 let inputId = $(this).attr('id');

//                 $(`label[for="${inputId}"]`)
//                     .addClass('border-danger text-danger');
//             });

//             let $container = $field.first().closest('.d-flex');
//             if ($container.length) {
//                 $container.after(`
//                     <div class="invalid-feedback d-block" id="${field}-error">
//                         ${fieldErrorMessage}
//                     </div>
//                 `);
//             }
//             continue;
//         }

//         $field.first().addClass('is-invalid');
//         $field.first().attr('title', fieldErrorMessage);
//         let $errorDiv = $(
//             `<div class="invalid-feedback" id="${field}-error">
//                 ${fieldErrorMessage}
//             </div>`
//         );
//         $field.last().after($errorDiv);
//     }
// }


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

            // Uncheck all radio buttons and checkboxes
            $form.find('input[type="radio"], input[type="checkbox"]')
                .removeAttr('checked')
                .prop('checked', false);

            // Remove error feedback elements
            $form.find('div.invalid-feedback[id$="-error"]').remove();
        }
    });
};


