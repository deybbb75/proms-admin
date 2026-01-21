function addItem(fetch_name, item_id = []) {
	// Reset the form and set the button to "Save"
	$(".fetched-data").html("");
	$("#save_changes").attr("name", "Save");
	$.ajax({
		type: "post",
		data: {
			add_id: item_id,
		},
		url: `fetch/${fetch_name}.php`,
		success: function (data) {
			let $fetch = $(".fetched-data").html(data);
			reInitUI($fetch);
		},
	});
}

function reInitUI(container) {
    var $scope = container ? $(container) : $(document);

    // Select2
    $scope.find('[data-toggle="select2"]').each(function () {
		var $select = $(this);

		// Destroy old instance
		if ($select.hasClass('select2-hidden-accessible')) {
			$select.select2('destroy');
		}

		var showSearch = $select.data('search') === true || $select.data('search') === 'true';
		var minResultsForSearch = showSearch ? 0 : Infinity;

		// Initialize Select2
		$select.select2({
			width: '100%',
			minimumResultsForSearch: minResultsForSearch,
			placeholder: $select.data('placeholder') || '',
			allowClear: $select.data('allow-clear') || false,
			dropdownParent: $select.closest('form, .modal, body')
		});

		// 🔥 Move Select2 container BEFORE the select
		var $select2Container = $select.next('.select2');
		$select2Container.insertBefore($select);
	});

	$(".select2").on("change", function() {
		$(this).valid();
	});

    // Input masks
    $scope.find('[data-toggle="input-mask"]').each(function () {
        var format = $(this).data('maskFormat');
        var reverse = $(this).data('reverse');
        reverse != null ? $(this).mask(format, { reverse: reverse }) : $(this).mask(format);
    });

    // Date picker
    $scope.find('[data-toggle="date-picker"]').each(function () {
        $(this).daterangepicker($.extend({
            cancelClass: "btn-light",
            applyButtonClasses: "btn-success"
        }, $(this).data()));
    });

    // Date range picker
    $scope.find('[data-toggle="date-picker-range"]').each(function () {
        var opts = $.extend({
            startDate: moment().subtract(29, "days"),
            endDate: moment(),
            ranges: {
                Today: [moment(), moment()],
                Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "Last 7 Days": [moment().subtract(6, "days"), moment()],
                "Last 30 Days": [moment().subtract(29, "days"), moment()],
                "This Month": [moment().startOf("month"), moment().endOf("month")],
                "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
            }
        }, $(this).data());

        $(this).daterangepicker(opts);
    });

    // Time picker
    $scope.find('[data-toggle="timepicker"]').each(function () {
        $(this).timepicker($.extend({
            showSeconds: true,
            icons: {
                up: "mdi mdi-chevron-up",
                down: "mdi mdi-chevron-down"
            }
        }, $(this).data()));
    });

    // TouchSpin
    $scope.find('[data-toggle="touchspin"]').each(function () {
        $(this).TouchSpin($.extend({}, $(this).data()));
    });

    // Maxlength
    $scope.find('[data-toggle="maxlength"]').each(function () {
        $(this).maxlength($.extend({
            warningClass: "badge bg-success",
            limitReachedClass: "badge bg-danger",
            separator: " out of ",
            preText: "You typed ",
            postText: " chars available.",
            placement: "bottom"
        }, $(this).data()));
    });

    /* ========================
       Components
    ======================== */

    // Tooltips
    $scope.find('[data-bs-toggle="tooltip"]').each(function () {
        bootstrap.Tooltip.getOrCreateInstance(this);
    });

    // Popovers
    $scope.find('[data-bs-toggle="popover"]').each(function () {
        bootstrap.Popover.getOrCreateInstance(this);
    });

    // Toasts
    $scope.find('[data-toggle="toast"]').toast();

    // Show / hide password
    $scope.find('[data-password]').off('click').on('click', function () {
        var $input = $(this).siblings('input');
        var visible = $(this).attr('data-password') === 'true';

        $input.attr('type', visible ? 'password' : 'text');
        $(this).attr('data-password', !visible).toggleClass('show-password');
    });

    // Multi dropdown
    $scope.find('.dropdown-menu a.dropdown-toggle').off('click').on('click', function () {
        $(this).next('.dropdown-menu').toggleClass('show');
        return false;
    });

    // Form validation
    $scope.find('.needs-validation').off('submit').on('submit', function (e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        $(this).addClass('was-validated');
    });
}

$(function () {
	setTimeout(function () {
		$("#main-preloader").fadeOut();
	}, 50);
});