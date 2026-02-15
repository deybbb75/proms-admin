    <!-- bundle -->
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/vendor.min.js"></script>
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/app.min.js"></script>

    <!-- third party js -->
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/vendor/apexcharts.min.js"></script>
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/vendor/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/vendor/jquery-jvectormap-world-mill-en.js"></script>
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/vendor/jquery.dataTables.min.js"></script>
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/vendor/dataTables.bootstrap5.js"></script>
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/vendor/dataTables.responsive.min.js"></script>
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/vendor/responsive.bootstrap5.min.js"></script>
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/vendor/dataTables.buttons.min.js"></script>
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/vendor/buttons.bootstrap5.min.js"></script>
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/vendor/buttons.html5.min.js"></script>
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/vendor/buttons.flash.min.js"></script>
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/vendor/buttons.print.min.js"></script>
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/vendor/dataTables.keyTable.min.js"></script>
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/vendor/dataTables.select.min.js"></script>
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/vendor/fixedColumns.bootstrap5.min.js"></script>
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/vendor/fixedHeader.bootstrap5.min.js"></script>
    <!-- third party js ends -->

    <!-- FilePond Plugins Js -->
    <script src="https://unpkg.com/filepond-plugin-file-encode/dist/filepond-plugin-file-encode.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>

    <!-- demo app -->
    <!-- <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/pages/demo.dashboard.js"></script> -->
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/pages/demo.datatable-init.js"></script>
    <!-- end demo js-->

    <!-- 2️⃣ jQuery Validate -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <!-- (Optional but recommended) Additional methods -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>

    <!-- SweetAlert Plugin Js -->
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/plugins/sweetalert2/dist/sweetalert2.min.js"></script>

    <!-- Custom JS -->
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/custom.js"></script>
    <script src="<?= $GLOBALS['INF_CONFIG']['sitehost'] ?>/assets/js/form-validation.js"></script>
    <!-- end Custom JS -->

    <!-- Highcharts API -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/non-cartesian-zoom.js"></script>
    <script src="https://code.highcharts.com/modules/mouse-wheel-zoom.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://code.highcharts.com/themes/adaptive.js"></script>

    <?php
        Alert::render();
        unset($_SESSION['img_input']);
    ?>
</html>