function addItem({
    fetch_name: fetch_name,
    item_id: item_id = [],
    custom_function: custom_function = () => {},
}) {
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
            custom_function();
		},
	});
}

function editItem({
    fetch_name: fetch_name,
    item_id: item_id,
    custom_function: custom_function = () => {},
}) {
	// Reset the form and set the button to "Save"
	$(".fetched-data").html("");
	$("#save_changes").attr("name", "Edit");
	$.ajax({
		type: "post",
		data: {
			id: item_id,
		},
		url: `fetch/${fetch_name}.php`,
		success: function (data) {
			let $fetch = $(".fetched-data").html(data);
			reInitUI($fetch);
            $("#form_validation").valid();
            custom_function();
		},
	});
}

function deleteItem(item_id) {
	Swal.fire({
		title: "Are you sure you want to delete this?",
		text: "You will not be able to recover this data!",
		icon: "warning", // use "icon" instead of "type"
		showCancelButton: true,
		confirmButtonColor: "#FF2121",
		confirmButtonText: "Yes, delete it!",
		cancelButtonText: "Cancel",
	}).then((result) => {
		if (result.isConfirmed) {
            $(".fetched-data").html("");
            $("#action").attr("name", "Delete");
            $("#action").val(item_id);
            $("#form_validation").submit();
		}
	});
}

function viewItem(item_id) {
    $(".fetched-data").html("");
    $("#action").attr("name", "View");
    $("#action").val(item_id);
    $("#form_validation").submit();
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

        var value = $select.val();
        if (value !== null && value !== '' && value.length !== 0) {
            $select.valid();
        }
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

// Initialize UI components on page load
$(function () {
	setTimeout(function () {
		$("#main-preloader").fadeOut();
	}, 50);
});

// Initialize FilePond for the attachment input
function initFilePond(inputId, acceptedFileType, errorMsg, buttons) {
    // Register all FilePond plugins that will be used
    FilePond.registerPlugin(
        FilePondPluginFileEncode,              // Allows encoding files
        FilePondPluginFileValidateSize,        // Allows file size validation
        FilePondPluginImageExifOrientation,    // Fixes image rotation (not used for DOCX, but ok)
        FilePondPluginImagePreview,            // Shows preview (not used for DOCX, but ok)
        FilePondPluginFileValidateType         // Allows file type validation
    );

    // Create the FilePond instance for the attachment input
    remarksFile = FilePond.create(
        document.getElementById(inputId),
        {
            // Only allow DOCX files
            acceptedFileTypes: acceptedFileType,

            // Backend endpoints used by FilePond
            server: {
                process: 'controller/ctr-upload.php',   // Called when file is uploaded
                revert: 'controller/ctr-revert.php'     // Called when file is removed
            }
        }
    );

    // Customize FilePond error message for invalid file types
    FilePond.setOptions({
        labelFileTypeNotAllowed: errorMsg
    });


    /* =====================================================
    FILE UPLOAD STATUS TRACKING
    These events tell us when FilePond is uploading,
    finished uploading, or when a new file is added.
    ===================================================== */


    // Fires when ONE file finishes uploading to the server
    remarksFile.on('processfile', (error, file) => {

        // If the server returned an error
        if (error) {
            console.log('Upload failed:', file.filename);
            return;
        }

        // If upload was successful
        console.log('Upload finished:', file.filename);
    });


    // Fires when ALL files have finished uploading
    remarksFile.on('processfiles', () => {
        console.log('All files uploaded');

        // Enable the Approve and Return buttons
        // (Now the form is safe to submit)
        buttons.forEach(button => {
            $(button).prop('disabled', false);
        });
    });


    // Fires when a new file is added (upload starts again)
    remarksFile.on('addfile', () => {

        // Disable the buttons while upload is in progress
        // to prevent submitting before upload finishes
        buttons.forEach(button => {
            $(button).prop('disabled', true);
        });
    });
}

window.addEventListener("load", () => {
  const menu = document.getElementById("side-nav");
  const activeItem = menu.querySelector(".menuitem-active");

  if (activeItem) {
    activeItem.scrollIntoView({
      behavior: "auto",
      block: "center"
    });
  }
});

// Highlight the active menu item based on the current URL path
document.addEventListener('DOMContentLoaded', () => {
    const menuItems = document.querySelectorAll('.simplebar-content .side-nav li');

    // Get the current page's pathname
    const currentPath = window.location.pathname;

    // Get the current path saved in the local storage if the menu has subpage
    let menuLink = localStorage.getItem('menu_link');
    let localStoragePath = false;

    menuItems.forEach(item => {
        const link = item.querySelector('a');
        
        if (link) {
            if (menuLink) {
                localStoragePath = link.getAttribute('href').includes(menuLink);
            }
            
            if (localStoragePath) {
                item.classList.add('menuitem-active');

                if (item.parentElement.classList.contains("side-nav-second-level")) {
                    item.closest('li').classList.add('menuitem-active');
                }
            }
        }
    });

    localStorage.removeItem('menu_link');
});

// Match the height of the left sidebar to the wrapper content
function matchHeight() {
    const el1 = document.getElementById('wrapper');
    const el2 = document.querySelector(
    'body[data-leftbar-compact-mode="condensed"]:not(.authentication-bg) .wrapper .leftside-menu'
    );

    if(el1 && el2) {
        if (el1.offsetHeight > el2.offsetHeight) {
            el2.style.height = el1.offsetHeight + "px";
        }else{
            el2.style.height = "fit-content";
        }
    }
}

window.addEventListener('resize', matchHeight);
window.addEventListener('load', matchHeight);

// Match the height of a group of elements (e.g. cards) to the tallest one
function matchGroupHeight(selector) {
    const elements = document.querySelectorAll(selector);
    if (!elements.length) return;

    // Reset heights first so we measure natural height
    elements.forEach(el => el.style.height = "auto");

    // Find tallest height
    let maxHeight = 0;
    elements.forEach(el => {
        if (el.offsetHeight > maxHeight) {
            maxHeight = el.offsetHeight;
        }
    });

    // Apply tallest height to all
    elements.forEach(el => {
        el.style.height = maxHeight + "px";
    });
}

// Example usage
function runMatchHeight() {
    matchGroupHeight(".prog_title");
}

window.addEventListener("load", runMatchHeight);
window.addEventListener("resize", runMatchHeight);

// A reusable function to create dynamic lists (e.g. objectives, activities) with add/remove functionality
// function createDynamicList(config) {

//     const {
//         templateId,
//         containerId,
//         addBtnId,
//         addBtnContainerId,
//         itemSelector,
//         fieldMap,
//         maxItems = Infinity,
//         confirmTitle = "Delete item?",
//         confirmText = "This action cannot be undone!",
//         afterAdd = () => {},
//         afterRemove = () => {}
//     } = config;

//     const template = document.getElementById(templateId);
//     const container = document.getElementById(containerId);
//     const addBtn = document.getElementById(addBtnId);
//     const addBtnContainer = document.getElementById(addBtnContainerId);

//     if (!template || !container || !addBtn) {
//         console.warn("DynamicList init failed — missing elements");
//         return;
//     }

//     // ---------- indexing ----------
//     function reindex() {

//         const rows = container.querySelectorAll(itemSelector);

//         rows.forEach((row, index) => {

//             Object.entries(fieldMap).forEach(([selector, name]) => {

//                 const field = row.querySelector(selector);
//                 if (field) {
//                     if (/\[\d+\]/.test(name)) {
//                         // Replace the first numeric index
//                         field.name = name.replace(/\[\d+\]/, `[${index}]`);
//                     } else {
//                         // No numeric index, append one
//                         field.name = `${name}[${index}]`;
//                     }
//                 }

//             });

//         });

//         return rows.length;
//     }

//     // ---------- add button visibility ----------
//     function updateAddVisibility(count) {

//         if (!addBtnContainer) return;

//         addBtnContainer.style.display =
//             count >= maxItems ? "none" : "block";
//     }

//     // ---------- add item ----------
//     function addItem() {

//         const count = reindex();

//         if (count >= maxItems) return;

//         const clone = template.content.cloneNode(true);

//         container.appendChild(clone);

//         const newCount = reindex();
//         updateAddVisibility(newCount);

//         afterAdd(container);
//     }

//     // ---------- remove item ----------
//     function removeItem(trigger) {

//         Swal.fire({
//             title: confirmTitle,
//             text: confirmText,
//             icon: "warning",
//             showCancelButton: true,
//             confirmButtonColor: "#FF2121"
//         }).then(result => {

//             if (!result.isConfirmed) return;

//             const row = trigger.closest(itemSelector);
//             if (!row) return;

//             row.remove();

//             const count = reindex();
//             updateAddVisibility(count);

//             afterRemove(container);

//         });

//     }

//     // ---------- bind add ----------
//     addBtn.addEventListener("click", () => addItem());

//     // ---------- delegated remove ----------
//     container.addEventListener("click", e => {

//         const removeBtn = e.target.closest("[data-remove-item]");

//         if (removeBtn) {
//             removeItem(removeBtn);
//         }

//     });

//     // ---------- initial state ----------
//     updateAddVisibility(reindex());

//     return {
//         addItem,
//         removeItem,
//         reindex
//     };
// }

// ---------- indexing ----------
function reindex(fieldMap, container, itemSelector) {

    const rows = container.querySelectorAll(itemSelector);

    rows.forEach((row, index) => {
        Object.entries(fieldMap).forEach(([selector, name]) => {

            const field = row.querySelector(selector);
            if (field) {
                if(itemSelector == ".sub-item"){
                    const parent = field.closest(".main-item").children[0];
                    let parentName = Array.from(parent.children).find(child => child.hasAttribute('name'));
                    if(parentName){
                        parentName = parentName.getAttribute('name').match(/^[^\]]+\]/)[0]
                        if (/\[\d+\]/.test(name)) {
                            // Replace the first numeric index
                            field.name = parentName + name.replace(/\[\d+\]/, `[${index}]`);
                        } else {
                            // No numeric index, append one
                            field.name = parentName + `${name}[${index}]`;
                        }
                    }
                }else{
                    if (/\[\d+\]/.test(name)) {
                        // Replace the first numeric index
                        field.name = name.replace(/\[\d+\]/, `[${index}]`);
                    } else {
                        // No numeric index, append one
                        field.name = `${name}[${index}]`;
                    }
                }
            }

        });

    });

    return rows.length;
}

// ---------- add button visibility ----------
function updateAddVisibility(count, maxItems, addBtnContainer) {

    if (!addBtnContainer) return;

    addBtnContainer.style.display =
        count >= maxItems ? "none" : "block";
}

// ---------- add item ----------
function addNewItem(trigger, { fieldMap = "Guest", maxItems = 0, templateId = '', itemSelector = '.row' } = {}) {
    const addBtnContainer = trigger.parentElement;
    const container = addBtnContainer.previousElementSibling;
    const template = document.getElementById(templateId)

    const count = reindex(fieldMap, container, itemSelector);

    if (count >= maxItems) return;

    const clone = template.content.cloneNode(true);

    container.appendChild(clone);

    const newCount = reindex(fieldMap, container, itemSelector);
    updateAddVisibility(newCount, maxItems, addBtnContainer);

    reInitUI($(container));
}

// ---------- remove item ----------
function removeOldItem(trigger, { fieldMap = "Guest", maxItems = 0, confirmTitle = "Delete item?", itemSelector = '.row' }) {
    const addBtnContainer = trigger.parentElement.parentElement.parentElement.nextElementSibling;
    console.log(addBtnContainer);
    const container = addBtnContainer.previousElementSibling;

    Swal.fire({
        title: confirmTitle,
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#FF2121"
    }).then(result => {

        if (!result.isConfirmed) return;

        const row = trigger.closest(".row");
        if (!row) return;

        row.remove();

        const count = reindex(fieldMap, container, itemSelector);
        updateAddVisibility(count, maxItems, addBtnContainer);

        reInitUI($(container));
    });

}
