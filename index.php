<?php
include 'includes/init.php';
include 'header.php';
$db = DB::getInstance();

$_SESSION['proms-admin']['active_ay_id'] = $db->queryUniqueValue("SELECT ay_id FROM tbl_academic_year WHERE status = 'Active'");

$academic_year = $db->queryUniqueObject('SELECT * FROM tbl_academic_year WHERE ay_id = :ay_id', ['ay_id' => $_SESSION['proms-admin']['active_ay_id']]);
if ($academic_year) {
    $ay_id = encrypt_data($academic_year->ay_id);
    $year  = e($academic_year->year);
    $semester  = e($academic_year->semester);
}else{
    $ay_id = 0;
    $year = "";
    $semester = "";
}

$past_ay_id = $db->queryUniqueValue("SELECT ay_id
    FROM tbl_academic_year
    WHERE (year, semester) < (
        SELECT year, semester
        FROM tbl_academic_year
        WHERE ay_id = :ay_id
    )
    ORDER BY year, semester", ['ay_id' => $_SESSION['proms-admin']['active_ay_id']]);

function getPercentage($new_data, $old_data){
    $value = [];
    $total_difference = $new_data - $old_data;

    if($old_data != 0){
        $value['percent'] = round(((abs($total_difference) / $old_data) * 100), 2);
    }else{
        $value['percent'] = 0;
    }

    if($total_difference > 0){
        $value['text-color'] = 'text-success';
        $value['arrow'] = 'mdi-arrow-up-bold';
    }else{
        $value['text-color'] = 'text-danger';
        $value['arrow'] = 'mdi-arrow-down-bold';
    }

    return $value;
}
?>
 <!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Dashboard for Academic Year <?= $year ?> (<?= $semester ?>)</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-xl-4 col-lg-4">
            <div class="card tilebox-one">
                <div class="card-body">
                    <?php
                    $total_reservations = $db->countOf("tbl_reservation", "ay_id = :ay_id", ["ay_id" => $_SESSION['proms-admin']['active_ay_id']]);
                    $past_total_reservations = $db->countOf("tbl_reservation", "ay_id = :ay_id", ["ay_id" => $past_ay_id]);
                    $total = getPercentage($total_reservations, $past_total_reservations);
                    ?>
                    <h6 class="text-uppercase mt-0" style="font-size: 1.3em; text-align: center;">Total Reservations</h6>
                    <h2 id="active-users-count" style="font-size: 6em; text-align: center; margin-block: 3.7rem;"><?= $total_reservations ?></h2>
                    <p class="mb-0 text-muted">
                        <span class="<?= $total['text-color'] ?> me-2"><span class="mdi <?= $total['arrow'] ?>"></span> <?= $total['percent'] ?>%</span>
                        <span class="text-nowrap">Since last academic term</span>  
                    </p>
                </div> <!-- end card-body-->
            </div>
            <!--end card-->
        </div>
        <div class="col-xl-8 col-lg-8">
            <div class="row">
                <div class="col-xl-6 col-lg-6">
                    <div class="card tilebox-one">
                        <div class="card-body">
                            <?php
                            $pending_reservations = $db->countOf("tbl_reservation", "ay_id = :ay_id AND status = 'Pending'", ["ay_id" => $_SESSION['proms-admin']['active_ay_id']]);
                            $past_pending_reservations = $db->countOf("tbl_reservation", "ay_id = :ay_id AND status = 'Pending'", ["ay_id" => $past_ay_id]);
                            $pending = getPercentage($pending_reservations, $past_pending_reservations);
                            ?>
                            <i class='uil uil-file-question-alt float-end'></i>
                            <h6 class="text-uppercase mt-0">Pending Reservations</h6>
                            <h2 class="my-2" id="active-views-count"><?= $pending_reservations ?></h2>
                            <p class="mb-0 text-muted">
                                <span class="<?= $pending['text-color'] ?> me-2"><span class="mdi <?= $pending['arrow'] ?>"></span> <?= $pending['percent'] ?>%</span>
                                <span class="text-nowrap">Since last academic term</span>
                            </p>
                        </div> <!-- end card-body-->
                    </div>
                    <!--end card-->
                </div>

                <div class="col-xl-6 col-lg-6">
                    <div class="card tilebox-one">
                        <div class="card-body">
                            <?php
                            $enrolled_reservations = $db->countOf("tbl_reservation", "ay_id = :ay_id AND status = 'Enrolled'", ["ay_id" => $_SESSION['proms-admin']['active_ay_id']]);
                            $past_enrolled_reservations = $db->countOf("tbl_reservation", "ay_id = :ay_id AND status = 'Enrolled'", ["ay_id" => $past_ay_id]);
                            $enrolled = getPercentage($enrolled_reservations, $past_enrolled_reservations);
                            ?>
                            <i class='uil uil-file-check-alt float-end'></i>
                            <h6 class="text-uppercase mt-0">Enrolled Reservations</h6>
                            <h2 class="my-2" id="active-views-count"><?= $enrolled_reservations ?></h2>
                            <p class="mb-0 text-muted">
                                <span class="<?= $enrolled['text-color'] ?> me-2"><span class="mdi <?= $enrolled['arrow'] ?>"></span> <?= $enrolled['percent'] ?>%</span>
                                <span class="text-nowrap">Since last academic term</span>
                            </p>
                        </div> <!-- end card-body-->
                    </div>
                    <!--end card-->
                </div>
            </div>


            <div class="row">
                <div class="col-xl-6 col-lg-6">
                    <div class="card tilebox-one">
                        <div class="card-body">
                            <?php
                            $reserved_applications = $db->countOf("tbl_reservation", "ay_id = :ay_id AND status = 'Reserved'", ["ay_id" => $_SESSION['proms-admin']['active_ay_id']]);
                            $past_reserved_applications = $db->countOf("tbl_reservation", "ay_id = :ay_id AND status = 'Reserved'", ["ay_id" => $past_ay_id]);
                            $reserved = getPercentage($reserved_applications, $past_reserved_applications);
                            ?>
                            <i class='uil uil-file-bookmark-alt float-end'></i>
                            <h6 class="text-uppercase mt-0">Reserved Applications</h6>
                            <h2 class="my-2" id="active-views-count"><?= $reserved_applications ?></h2>
                            <p class="mb-0 text-muted">
                                <span class="<?= $reserved['text-color'] ?> me-2"><span class="mdi <?= $reserved['arrow'] ?>"></span> <?= $reserved['percent'] ?>%</span>
                                <span class="text-nowrap">Since last academic term</span>
                            </p>
                        </div> <!-- end card-body-->
                    </div>
                    <!--end card-->
                </div>

                <div class="col-xl-6 col-lg-6">
                    <div class="card tilebox-one">
                        <div class="card-body">
                            <?php
                            $expired_reservations = $db->countOf("tbl_reservation", "ay_id = :ay_id AND status = 'Expired'", ["ay_id" => $_SESSION['proms-admin']['active_ay_id']]);
                            $past_expired_reservations = $db->countOf("tbl_reservation", "ay_id = :ay_id AND status = 'Expired'", ["ay_id" => $past_ay_id]);
                            $expired = getPercentage($expired_reservations, $past_expired_reservations);
                            ?>
                            <i class='uil uil-file-times-alt float-end'></i>
                            <h6 class="text-uppercase mt-0">Expired Reservations</h6>
                            <h2 class="my-2" id="active-views-count"><?= $expired_reservations ?></h2>
                            <p class="mb-0 text-muted">
                                <span class="<?= $expired['text-color'] ?> me-2"><span class="mdi <?= $expired['arrow'] ?>"></span> <?= $expired['percent'] ?>%</span>
                                <span class="text-nowrap">Since last academic term</span>
                            </p>
                        </div> <!-- end card-body-->
                    </div>
                    <!--end card-->
                </div>
            </div>
        </div> <!-- end col -->
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-h-100">
                <div class="card-body" style="padding-bottom: 0;">
                    <div dir="ltr" style="overflow: auto;">
                        <figure class="highcharts-figure">
                            <div id="container"></div>
                        </figure>
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div>
    </div>

    <div class="row">
        <div class="col-xl-7 col-lg-6">
            <div class="card card-h-100">
                <div class="card-body" style="padding-bottom: 0;">
                    <div dir="ltr"  style="overflow: auto;">
                        <figure class="highcharts-figure">
                            <div id="pie-chart"></div>
                        </figure>
                    </div>
                        
                </div> <!-- end card-body-->
            </div> <!-- end card-->

        </div> <!-- end col -->
        <div class="col-xl-5 col-lg-6">

            <div class="row">
                <?php
                    $program_query = $db->query("SELECT * FROM tbl_program WHERE status = 'Active'");
                    $programs = [];

                    while ($line = $db->fetchNextObject($program_query)) {
                        $programs[$line->prog_id]['name'] = $line->prog_name;
                ?>
                <div class="col-sm-6">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <?php
                            $program_total = $db->countOf("tbl_reservation", "ay_id = :ay_id AND prog_id = :prog_id", ["ay_id" => $_SESSION['proms-admin']['active_ay_id'], "prog_id" => $line->prog_id]);
                            $past_program_total = $db->countOf("tbl_reservation", "ay_id = :ay_id AND prog_id = :prog_id", ["ay_id" => $past_ay_id, "prog_id" => $line->prog_id]);
                            $program_data = getPercentage($program_total, $past_program_total);
                            ?>
                            <h5 class="text-muted fw-normal mt-0 prog_title"><?= $line->prog_name ?></h5>
                            <h3 class="mt-3 mb-3"><?= $program_total ?></h3>
                            <div style="display: flex;">
                                <p class="mb-0 text-muted" style="width: 70px;">
                                    <span class="<?= $program_data['text-color'] ?> me-2"><i class="mdi <?= $program_data['arrow'] ?>"></i> <?= $program_data['percent'] ?>%</span> 
                                </p>
                                <p class="mb-0 text-muted">
                                    <span">Since last academic term</span>  
                                </p>
                            </div>
                            
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->
                <?php
                    }
                ?>
            </div> <!-- end row -->

        </div> <!-- end col -->
    </div>
    <!-- end row -->

</div>
<!-- container -->
  

<?php
include 'footer.php';

$year = date("Y");
$statuses = ['Pending', 'Enrolled', 'Reserved', 'Expired'];
$months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

$monthly_data = [];

foreach ($statuses as $status) {
    $monthly_data[$status] = array_fill_keys($months, 0);
}

$monthly_query = $db->query(
    "SELECT 
        MONTH(datetime_reserved) AS month,
        status,
        COUNT(*) AS total_records
    FROM tbl_reservation
    WHERE YEAR(datetime_reserved) = :year_now
      AND status IN ('Pending', 'Enrolled', 'Reserved', 'Expired')
    GROUP BY MONTH(datetime_reserved), status
    ORDER BY month", ['year_now' => $year]
);

while ($line = $db->fetchNextObject($monthly_query)) {
    $monthName = date("M", mktime(0, 0, 0, $line->month, 1));
    $monthly_data[$line->status][$monthName] = $line->total_records;
}

$chartLabels = json_encode($months);

$pendingData  = json_encode(array_values($monthly_data['Pending']));
$enrolledData = json_encode(array_values($monthly_data['Enrolled']));
$reservedData = json_encode(array_values($monthly_data['Reserved']));
$expiredData  = json_encode(array_values($monthly_data['Expired']));
?>
<script>
Highcharts.chart('container', {
    chart: {
        type: 'line',
        height: 500,
        backgroundColor: '#ffffff00',
    },

    title: {
        text: 'NUMBER OF RESERVATIONS PER MONTH',
        style: {
            color: '#444444',
            fontSize: '15px',
            fontFamily: 'Nunito, sans-serif',
        }
    },

    legend: {
        itemStyle: {
            color: '#444444',
            fontWeight: 'bold'
        },
        itemHoverStyle: {
            color: '#000'
        },
        itemHiddenStyle: {
            color: '#999'
        }
    },

    xAxis: {
        labels: {
            style: { color: '#444444', fontFamily: 'Nunito, sans-serif' }
        },
        categories: <?= $chartLabels ?>
    },

    yAxis: {
        type: 'logarithmic',
        title: {
            text: null
        },
        labels: {
            style: { color: '#444444', fontFamily: 'Nunito, sans-serif' }
        },
        gridLineColor: '#cccccc',
    },

    tooltip: {
        style: {
            color: '#ffffff'
        }
    },

    navigation: {
        buttonOptions: {
            theme: {
                fill: '#ffffff00',
                style: {
                    color: '#ffffff'    // icon color (fallback)
                },

                states: {
                    hover: {
                        fill: '#ffffff00',
                    },
                    select: {
                        fill: '#ffffff00'
                    }
                }
            },
            symbolStroke: '#cccccc' // icon lines color
        }
    },

    credits: {
        enabled: false // ← removes the Highcharts watermark
    },

    series: [
        {
            name: 'Pending',
            data: <?= $pendingData ?>,
            color: 'var(--warning-color)'
        },
        {
            name: 'Enrolled',
            data: <?= $enrolledData ?>,
            color: 'var(--success-color)'
        },
        {
            name: 'Reserved',
            data: <?= $reservedData ?>,
            color: 'var(--info-color)'
        },
        {
            name: 'Expired',
            data: <?= $expiredData ?>,
            color: 'var(--danger-color)'
        },
    ]
});


<?php
$statuses = ['Pending', 'Enrolled', 'Reserved', 'Expired'];
$months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

$monthly_data = [];

foreach ($statuses as $status) {
    $monthly_data[$status] = array_fill_keys($months, 0);
}

$every_prog_query = $db->query(
    "SELECT 
        prog_id,
        COUNT(*) AS total_records
    FROM tbl_reservation
    WHERE ay_id = :ay_id
    GROUP BY prog_id", ['ay_id' => $_SESSION['proms-admin']['active_ay_id']]
);

while ($line = $db->fetchNextObject($every_prog_query)) {
    $programs[$line->prog_id]['total'] = $line->total_records;
}

$jsArray = [];
foreach($programs as $item) {
    $jsArray[] = ["name"=>$item['name'], "y"=>$item['total'] ?? 0];
}
?>

Highcharts.chart('pie-chart', {
    chart: {
        type: 'pie',
        panning: {
            enabled: true,
            type: 'xy'
        },
        panKey: 'shift',
        backgroundColor: '#ffffff00',
        height: 540,
    },
    title: {
        text: 'PERCENTAGE DISTRIBUTION OF TOTAL PROGRAM RESERVATIONS',
        style: {
            color: '#444444',
            fontSize: '15px',
            fontFamily: 'Nunito, sans-serif',
        }
    },
    tooltip: {
        pointFormat: '<b>{point.y}</b> ({point.percentage:.1f}%)'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            showInLegend: true,
            dataLabels: [{
                enabled: false,
            }, {
                enabled: true,
                distance: -50,
                format: '{point.percentage:.1f}%',
                style: {
                    fontSize: '1.2em',
                    textOutline: 'none',
                    opacity: 0.7,
                    fontFamily: 'Nunito, sans-serif',
                },
                filter: {
                    operator: '>',
                    property: 'percentage',
                    value: 10
                }
            }]
        }
    },

    legend: {
        enabled: true,
        itemStyle: {
            fontFamily: 'Nunito, sans-serif',
            color: '#444'
        }
    },

    navigation: {
        buttonOptions: {
            theme: {
                fill: '#ffffff00',
                style: {
                    color: '#ffffff'    // icon color (fallback)
                },

                states: {
                    hover: {
                        fill: '#ffffff00',
                    },
                    select: {
                        fill: '#ffffff00'
                    }
                }
            },
            symbolStroke: '#cccccc' // icon lines color
        }
    },

    credits: {
        enabled: false // ← removes the Highcharts watermark
    },

    series: [
        {
            name: 'Percentage',
            colorByPoint: true,
            data: <?= json_encode($jsArray) ?>
        }
    ]
});
</script>