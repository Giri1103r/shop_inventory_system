/**
 *  Custom Validation method for the Jquery Validation
 *  Version 1.0.0
 *  Created Date : 26-10-2023
 *  Created By  : Gowtham M
 *
 *  1.passwordPolicy
 *  2.allowedAlphabets
 *  3.mobileNumber
 *  4.customEmail
 *  5.customDate
 *  6.imageFormat
 *  7.multiImageFormat
 *  8.maxFileSize
 *  9.multiMaxFileSize
 *  10.customAddress
 *  11.customPincode
 *  12.maxTextareaLength
 *  13.commaSeparatedEmails
 *  14.commaSeparatedPhoneNumbers
 *  15.checkuniquee
 *  16.customPassportID
 *  17.customEmployeeID
 *  18.validate-input-required  // Dynamic Class Based Validation
 *  19.validate-radio-required  // Dynamic Class Based Validation
 *  20.validate-checkbox-required  // Dynamic Class Based Validation
 *  21.validate-textarea-required  // Dynamic Class Based Validation
 *  22.validate-file-required // Dynamic Class Based Validation
 *23.validateFileSize
 */



/**
 * Password Policy Validation
 */

$.validator.addMethod("passwordPolicy", function (value, element) {
    // Check for at least one capital letter
    if (!/[A-Z]/.test(value)) {
        return false;
    }
    // Check for at least one small letter
    if (!/[a-z]/.test(value)) {
        return false;
    }
    // Check for at least one number
    if (!/[0-9]/.test(value)) {
        return false;
    }
    // Check for at least one special character (you can customize this character set)
    if (!/[^a-zA-Z0-9]/.test(value)) {
        return false;
    }
    // Check for length between 8 and 16 characters
    if (value.length < 8 || value.length > 16) {
        return false;
    }
    return true;
}, "Password must meet the specified criteria.");

/**
 * Name Validation
 */
$.validator.addMethod("allowedAlphabets", function (value, element) {
    // Check if the input consists only of capital and small letters
    if (/^[a-zA-Z]+$/.test(value)) {
        return true;
    }
    return false;
}, "Only capital and small letters are allowed.");

$.validator.addMethod("numericOnly", function (value, element) {
    return this.optional(element) || $.isNumeric(value);
}, "Please enter only numbers");

/**
 * Mobile Number Validation
 */
$.validator.addMethod("mobileNumber", function (value, element) {
    // Check if the input consists of exactly 10 digits (0-9)
    return /^\d{10}$/.test(value);
}, "Please enter a valid 10-digit mobile number.");

/**
 * Email Validation
 */
$.validator.addMethod("customEmail", function (value, element) {
    // Check if the input matches a basic email format
    return /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(value);
}, "Please enter a valid email address.");

/**
 * Date Format Validation
 */
$.validator.addMethod("customDate", function (value, element) {
    // Check if the input matches the "dd-mm-yyyy" date format
    return /^\d{2}-\d{2}-\d{4}$/.test(value) && isDateValid(value);
}, "Please enter a valid date in the format dd-mm-yyyy.");

function isDateValid(dateString) {
    var parts = dateString.split("-");
    var day = parseInt(parts[0], 10);
    var month = parseInt(parts[1], 10);
    var year = parseInt(parts[2], 10);

    // Check if the date components are valid
    if (isNaN(day) || isNaN(month) || isNaN(year)) {
        return false;
    }

    // Check if the month is within a valid range (1-12)
    if (month < 1 || month > 12) {
        return false;
    }

    // Check the day based on the month
    var daysInMonth = new Date(year, month, 0).getDate();
    if (day < 1 || day > daysInMonth) {
        return false;
    }

    return true;
}

/**
 * Singele Image Validation
 */
$.validator.addMethod("imageFormat", function (value, element) {
    // Get the file extension
    var extension = value.split('.').pop().toLowerCase();

    // Check if the file extension is one of the allowed formats
    return ['png', 'jpg', 'jpeg'].includes(extension);
}, "Please upload an image in PNG, JPG, or JPEG format.");

/**
 * Multiple Image Validation
 */
$.validator.addMethod("multiImageFormat", function (value, element) {
    // Get the file extensions for all selected files
    var extensions = [];
    for (var i = 0; i < element.files.length; i++) {
        var extension = element.files[i].name.split('.').pop().toLowerCase();
        extensions.push(extension);
    }

    // Check if all file extensions are in the allowed formats
    return extensions.every(function (extension) {
        return ['png', 'jpg', 'jpeg', 'pdf', 'doc', 'mp4'].includes(extension);
    });
}, "Please upload images in PNG, JPG, or JPEG format.");

/**
 * Singele file size Validation
 */
$.validator.addMethod("maxFileSize", function (value, element) {
    // Get the maximum file size in bytes (2 MB = 2 * 1024 * 1024 bytes)
    var maxSize = 2 * 1024 * 1024;

    // Check the file size of the selected file
    return element.files[0].size <= maxSize;
}, "File size must not exceed 2 MB.");

$.validator.addMethod('filesize', function (value, element, param) {
    return this.optional(element) || (element.files[0].size <= param);
}, 'File size must be less than {0}');

/**
 * Multiple file size Validation
 */
$.validator.addMethod("multiMaxFileSize", function (value, element) {
    // Get the maximum file size in bytes (2 MB = 2 * 1024 * 1024 bytes)
    var maxSize = 2 * 1024 * 1024;

    // Check the file size of each selected file
    for (var i = 0; i < element.files.length; i++) {
        if (element.files[i].size > maxSize) {
            return false;
        }
    }

    return true;
}, "File size must not exceed 2 MB.");

/**
 * Address Validation
 */
$.validator.addMethod("customAddress", function (value, element) {
    // Check for allowed characters: letters (caps and small), numbers, and specific special characters
    if (/^[a-zA-Z0-9 ,.#/]*$/.test(value)) {
        // Check for consecutive special characters or consecutive spaces
        if (/(,|#|\/| )\1/.test(value)) {
            return false;
        }
        return true;
    }
    return false;
}, "Please enter a valid address.");

/**
 *  Pincode 6-Digit
 */
$.validator.addMethod("customPincode", function (value, element) {
    // Check if the input consists of exactly 6 digits (0-9)
    return /^[0-9]{6}$/.test(value);
}, "Please enter a 6-digit PIN code with numbers only.");

/**
* Maximum 1000 Characters
*/
$.validator.addMethod("maxTextareaLength", function (value, element) {
    // Check if the length of the input does not exceed 1000 characters
    return value.length <= 1000;
}, "The text cannot exceed 1000 characters.");

/**
* Comma Separated Email Id Validation
*/
$.validator.addMethod("commaSeparatedEmails", function (value, element) {
    // Split the input by commas to separate email addresses
    var emails = value.split(',');

    // Regular expression pattern for validating individual email addresses
    var emailPattern = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;

    // Check each email address in the list
    for (var i = 0; i < emails.length; i++) {
        var email = emails[i].trim();
        if (!email.match(emailPattern)) {
            return false;
        }
    }

    return true;
}, "Please enter a valid list of comma-separated email addresses.");

/**
* Comma Separated Phone number with 10 Digit
*/
$.validator.addMethod("commaSeparatedPhoneNumbers", function (value, element) {
    // Split the input by commas to separate phone numbers
    var phoneNumbers = value.split(',');

    // Regular expression pattern for validating individual phone numbers (exactly 10 digits)
    var phoneNumberPattern = /^\d{10}$/;

    // Check each phone number in the list
    for (var i = 0; i < phoneNumbers.length; i++) {
        var phoneNumber = phoneNumbers[i].trim();
        if (!phoneNumber.match(phoneNumberPattern)) {
            return false;
        }
    }

    return true;
}, "Please enter a valid list of comma-separated phone numbers with exactly 10 digits.");

/**
 * Uniquee Value check
 *
 * checkuniquee: {
    url: "http://mywebsite.com",
    id: "exampleParam"
    }
 */

// Extend jQuery Validation Plugin with a custom method
$.validator.addMethod("checkuniquee", function (value, element, params) {
    // Extract the URL and custom parameter from the params object
    var url = params.url;
    var id = params.id;

    // Perform an Ajax request to the specified URL, passing the custom parameter
    return $.ajax({
        type: "POST",
        url: url,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            id: id,
            customparam: params
        },
        dataType: "json"
    }).then(function (response) {
        return response.valid === true;
    });
}, "This value is not unique.");

/**
 * Passport Number Validation
 */

$.validator.addMethod("customPassportID", function (value, element) {
    // Check if the input consists of at most 16 characters, with numbers and hyphens only
    return /^[0-9-]{1,16}$/.test(value);
}, "Please enter a valid passport ID with a maximum of 16 characters, containing only numbers and hyphens.");

/**
 * Employee Id
 */
$.validator.addMethod("customEmployeeID", function (value, element) {
    // Check if the input consists of only alphanumeric characters
    return /^[a-zA-Z0-9]*$/.test(value);
}, "Please enter a valid employee ID with alphanumeric characters (letters and numbers only).");


/**
 *
 *
 * Class based Required Validation
 *
 */

/**
 * Class based range Required Validation
 */
$.validator.addClassRules("validate-range-required", {
    iRange: true,
    iRequired: true// Use the custom range validation
});
$.validator.addMethod("iRange", function (value, element) {
    return this.optional(element) || (value >= 0 && value <= 100 && !isNaN(value));
}, function (value, element) {
    var customErrorMessage = $(element).data('error');

    if (customErrorMessage) {
        return customErrorMessage;
    } else {
        return "Please enter a number between 0 and 100.";
    }
});



/**
 * Class based Input Required Validation
 */

$.validator.addClassRules("validate-input-required", {
    iRequired: true // Requires the field to be non-empty
});

$.validator.addMethod("iRequired", function (value, element) {

    return value.trim() !== '';

}, function (value, element) {

    var customErrorMessage = $(element).data('error');

    if (customErrorMessage) {
        return customErrorMessage;
    } else {

        return "Please enter the value.";
    }

});


/**
 * Class based Radio Required Validation
 */

$.validator.addClassRules("validate-radio-required", {
    rRequired: true,
});

$.validator.addMethod("rRequired", function (value, element) {

    var radios = $('input[type="radio"][name="' + element.name + '"]');

    return radios.is(":checked");
}, function (value, element) {

    var customErrorMessage = $(element).data('error');

    if (customErrorMessage) {
        return customErrorMessage;
    } else {

        return "Please select an option.";
    }

});


/**
 * Class based Checkbox Required Validation
 */

$.validator.addClassRules("validate-checkbox-required", {
    cRequired: true,
});


$.validator.addMethod("cRequired", function (value, element) {

    var checkboxes = $('input[type="checkbox"][name="' + element.name + '"]');
    return checkboxes.is(":checked");

}, function (value, element) {

    var customErrorMessage = $(element).data('error');

    if (customErrorMessage) {
        return customErrorMessage;
    } else {

        return "Please select an option.";
    }

});


/**
 * Class based Select Required Validation
 */

$.validator.addClassRules("validate-select-required", {
    sRequired: true,
});

$.validator.addMethod("sRequired", function (value, element) {

    var selects = $('select[name="' + element.name + '"]');

    if (selects.is('[multiple]')) {
        var selectedOptions = selects.find('option:selected');
        console.log(selectedOptions);
        return selectedOptions.length > 0;

    } else {
        return selects.val() !== "";
    }
}, function (value, element) {

    var customErrorMessage = $(element).data('error');

    if (customErrorMessage) {
        return customErrorMessage;
    } else {

        return "Please select an option.";
    }

});


/**
 * Class based Textarea Required Validation
 */

// Add the custom rule to the 'validate-textarea-required' class
$.validator.addClassRules("validate-textarea-required", {
    tRequired: true,
});

$.validator.addMethod("tRequired", function (value, element) {
    // Check if the textarea is not empty
    return value.trim() !== '';
}, function (value, element) {
    var customErrorMessage = $(element).data('error');

    if (customErrorMessage) {
        return customErrorMessage;
    } else {
        return "Please enter a value.";
    }
});


/**
 * Class based File Required Validation
 */

$.validator.addClassRules("validate-file-required", {
    fRequired: true,
});

// Add the custom method for file input required validation
$.validator.addMethod("fRequired", function (value, element) {
    // Check if a file is selected
    return element.files.length > 0;
}, function (value, element) {
    var customErrorMessage = $(element).data('error');
    console.log(customErrorMessage, "data-error")
    if (customErrorMessage) {
        return customErrorMessage;
    } else {
        return "Please select a file.";
    }
});


$.validator.addClassRules("validate-text-alphanumeric", {
    talpnum: true,
});

// alpha numeric validation
$.validator.addMethod("talpnum", function (value, element) {

    return /^[a-zA-Z0-9]*$/.test(value);
}, function (value, element) {

    return "Please enter a valid input.";

});


$.validator.addMethod("validateFileType", function (value, element) {
    if (element.files.length === 0) {
        return false;
    }

    let acceptedTypes = ["jpeg", "jpg", "png", "pdf", "doc", "docx", "mp4"];
    let isValid = true;

    $.each(element.files, function (i, file) {
        let fileExtension = file.name.split('.').pop().toLowerCase();
        if ($.inArray(fileExtension, acceptedTypes) === -1) {
            isValid = false;
            return false;
        }
    });

    return isValid;
}, "Please select a valid file type: jpeg, jpg, png, pdf, doc, docx, mp4");

$.validator.addMethod("validateFileSize", function (value, element) {
    let maxSize = 5 * 1024 * 1024;
    let isValid = true;

    $.each(element.files, function (i, file) {
        if (file.size > maxSize) {
            isValid = false;
            return false;
        }
    });

    return isValid;
}, "Each file must be less than or equal to 5 MB");



$(".validate-file-accept").each(function () {
    $(this).rules("add", {
        validateFileType: true,
        validateFileSize: true,
        required: true
    });
});




