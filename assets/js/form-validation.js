function validateForm() {
    // =========================================================================
    // GROUPED RULES (your entire original rules reorganized)
    // =========================================================================
    /**
     * Generate field names with flexible indexing and subkeys
     * @param {string} baseName - the root field name
     * @param {Array} structure - array of objects defining levels
     *   { count?: number, key?: string } 
     *   - count: number of items to index
     *   - key: fixed subkey
     */
    const indexedFields = (baseName, structure = []) => {
        if (!structure.length) return [baseName];

        const [level, ...rest] = structure;
        const indices = level.count != null ? Array.from({ length: level.count }, (_, i) => i) : [null];

        return indices.flatMap(i => {
            const part = i != null ? `[${i}]` : '';
            const keyPart = level.key ? `[${level.key}]` : '';
            const name = `${baseName}${part}${keyPart}`;
            return indexedFields(name, rest);
        });
    };
    
    const groupedRules = [
        {
            fields: ["lpu_no"],
            rules: { required: true, noWhitespace: true, minlength: 6, maxlength: 8, digits: true }
        },
        {
            fields: ["emp_no"],
            rules: { required: true, noWhitespace: true, minlength: 6, maxlength: 6, digits: true }
        },
        {
            fields: ["student_no"],
            rules: { required: true, noWhitespace: true, minlength: 8, maxlength: 8, digits: true }
        },
        {
            fields: [
                "fname",
                "lname",
                "name",
                "developer"
            ],
            rules: { required: true, noWhitespace: true, namePattern: true }
        },
        {
            fields: ["mname"],
            rules: { noWhitespace: true, namePattern: true }
        },
        {
            fields: ["email", "developer_email"],
            rules: { required: true, noWhitespace: true, email: true }
        },
        {
            fields: [
                "start_date_1", 
                "start_date_2",
                ...indexedFields('schedule', [{ count: 3, key: 'day' }]),
                ...indexedFields('schedule', [{ count: 3, key: 'start_time' }]),
                ...indexedFields('schedule', [{ count: 3, key: 'end_time' }]),
                "main_fee",
                "sub_fee",
                "semester",
                ...indexedFields('objective', [{ count: 10 }]),
                ...indexedFields("outline", [{ count: 10 }]),
                "duration",
                ...indexedFields("policy", [{ count: 10 }]),
                ...indexedFields("requirement", [{ count: 10 }]),
                ...indexedFields("offering", [{ count: 10 }]),
                ...indexedFields("level", [{ count: 10 }]),
                ...indexedFields("duration", [{ count: 10 }]),
                ...indexedFields("mode", [{ count: 10 }]),
                ...indexedFields("note", [{ count: 10 }]),
                ...indexedFields("cert", [{ count: 5, key: 'title' }]),
                ...indexedFields('cert', [
                    { count: 5, key: 'ctg' },
                    { count: 5, key: 'title' }
                ]),
                ...indexedFields('cert', [
                    { count: 5, key: 'ctg' },
                    { count: 5, key: 'desc' }
                ]),
                ...indexedFields("associate_cert", [{ count: 10 }]),
                ...indexedFields("expert_cert", [{ count: 10 }]),
                "yt_link",
            ],
            rules: { required: true, noWhitespace: true }
        },
        {
            fields: ["start_year", "end_year"],
            rules: { required: true, noWhitespace: true, digits: true, min: 2025, max: 2100, step: 1 }
        },
        {
            fields: ["dept_id", "div_id", "course_id", "role_id"],
            rules: { required: true, digits: true }
        },
        {
            fields: ["member_order"],
            rules: { required: true, positiveWholeNumber: true }
        },
        {
            fields: ["credit_unit"],
            rules: { digits: true }
        },
        {
            fields: [
                "prog_name", 
                "venue", 
                "title", 
                "news_title", 
                "news_content", 
                "position", 
                "description", 
                "prog_title", 
                "training_title",
                "course_title",
                "objective",
                "class_details",
                "note",
                "content"
            ],
            rules: { required: true, noWhitespace: true, descPattern: true }
        },
        {
            fields: [
                "course_1",
                "course_2",
                "course_3",
            ],
            rules: { noWhitespace: true, descPattern: true }
        },
        {
            fields: ["year_level"],
            rules: { required: true, digits: true, min: 1, max: 6 }
        },
        {
            fields: ["year_level[]"],
            rules: { requireYearLevel: true, min: 1, max: 6 }
        },
        {
            fields: ["status", "role"],
            rules: { required: true, letters: true, noWhitespace: true }
        },
        {
            fields: ["role_points"],
            rules: { required: true, noWhitespace: true, digits: true }
        },
        {
            fields: ["activity-date"],
            rules: { required: true, notFutureDate: true }
        },
        {
            fields: ["activity-description"],
            rules: { required: true, noWhitespace: true, descPattern: true, minlength: 200, maxlength: 5000 }
        },
        {
            fields: ["activity-file"],
            rules: {
                required: function () { return $("#transcript-id").length === 0; },
                filetype: ["pdf", "doc", "docx", "txt", "jpg", "jpeg", "png", "gif", "bmp", "webp", "svg", "xls", "xlsx", "ppt", "pptx"],
                filesize: 25,
                mimetype: [
                    "application/pdf",
                    "application/msword",
                    "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                    "text/plain",
                    "image/jpeg", "image/png", "image/gif", "image/bmp", "image/webp", "image/svg+xml",
                    "application/vnd.ms-excel",
                    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                    "application/vnd.ms-powerpoint",
                    "application/vnd.openxmlformats-officedocument.presentationml.presentation"
                ]
            }
        },
        {
            fields: ["attachment_file"],
            rules: {
                required: true,
                filetype: ["pdf"],
                filesize: 10,
                mimetype: [
                    "application/pdf",
                ]
            }
        },

        // ▌ CSV UPLOAD
        {
            fields: ["file"],
            rules: {
                required: true,
                filetype: ["csv"],
                filesize: 5,
                mimetype: [
                    "text/csv",
                    "application/vnd.ms-excel",
                    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                ]
            }
        }
    ];

    // =========================================================================
    // CONVERT GROUPED RULES → jQuery Validate rules
    // =========================================================================

    let finalRules = {};

    groupedRules.forEach(group => {
        group.fields.forEach(field => {
            finalRules[field] = group.rules;
        });
    });

    // =========================================================================
    // INITIALIZE VALIDATOR
    // =========================================================================

    $("#form_validation").validate({
        ignore: ":hidden:not(.form-control):not(.form-select):not(.form-check-input)",
        rules: finalRules,
        messages: {},

        invalidHandler(event, validator) {
            if (validator.numberOfInvalids()) {
                validator.errorList[0].element.focus();
            }
        },

        highlight(element) {
            $(element)
                .removeClass("is-valid")
                .addClass("is-invalid");

            if ($(element).is("select.select2")) {
                $(element).prev().children().first().children().first().addClass("is-invalid").removeClass("is-valid");
            }
        },

        unhighlight(element) {
            $(element)
                .removeClass("is-invalid")
                .addClass("is-valid");

            if ($(element).is("select.select2")) {
                $(element).prev().children().first().children().first().removeClass("is-invalid").addClass("is-valid");
            }

            // Remove error message when valid
            $(element).next(".invalid-feedback").remove();
        },

        errorPlacement(error, element) {
            // Remove existing message to prevent duplicates
            element.next(".invalid-feedback").remove();

            $('<div class="invalid-feedback"></div>')
                .text(error.text())
                .insertAfter(element);
        }
    });

    $(".select2").on("change", function() {
        $(this).valid();
    });

    // =========================================================================
    // CUSTOM VALIDATION METHODS
    // =========================================================================

    $.validator.addMethod("noWhitespace", function (value, element) {
        return this.optional(element) || value.trim().length > 0;
    }, "This field cannot be empty or contain only spaces.");

    $.validator.addMethod("customdate", function (value, element) {
        return value.match(/^\d{4}-\d{2}-\d{2}$/);
    }, "Please enter a date in the format YYYY-MM-DD.");

    $.validator.addMethod("creditcard", function (value, element) {
        return value.match(/^\d{4}-\d{4}-\d{4}-\d{4}$/);
    }, "Please enter a credit card in the format XXXX-XXXX-XXXX-XXXX.");

    $.validator.addMethod("namePattern", function (value, element) {
        return this.optional(element) || /^[a-zA-ZÑñ\s.\-]+$/.test(value);
    }, "Please enter a valid name (letters, spaces, Ñ/ñ, period, and dash only).");

    $.validator.addMethod("letters", function (value, element) {
        return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
    }, "Please enter a valid text (letters and spaces only).");

    $.validator.addMethod("requireYearLevel", function () {
        return $(".year-level-checkbox:checked").length > 0;
    }, "Please select at least one year level.");

    $.validator.addMethod("descPattern", function (value, element) {
        return this.optional(element) || /^[^<>]*$/.test(value);
    }, "Please do not use '<' or '>' characters.");

    $.validator.addMethod("filetype", function (value, element, params) {
        const type = value.split(".").pop().toLowerCase();
        return this.optional(element) || params.includes(type);
    }, "Please select a valid file type.");

    $.validator.addMethod("filesize", function (value, element, param) {
        if (element.files.length === 0) return true;
        return element.files[0].size <= param * 1024 * 1024;
    }, "File size must be less than or equal to {0} MB.");

    $.validator.addMethod("mimetype", function (value, element, params) {
        if (element.files.length === 0) return true;
        return params.includes(element.files[0].type);
    }, "Please select a file with a valid MIME type.");

    $.validator.addMethod("notFutureDate", function (value) {
        if (!value) return true;
        const selected = new Date(value);
        const today = new Date();
        selected.setHours(0, 0, 0, 0);
        today.setHours(0, 0, 0, 0);
        return selected <= today;
    }, "Date cannot be in the future.");

    $.validator.addMethod("positiveWholeNumber", function (value, element) {
        return this.optional(element) || /^[1-9]\d*$/.test(value);
    }, "Please enter a valid order (positive whole number only).");
}

$(function () {
    validateForm();
});
