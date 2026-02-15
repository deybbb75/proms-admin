<?php
include 'includes/init.php';
include 'header.php';
$db = DB::getInstance();
?>
 <!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Dashboard for Academic Year 2025 - 2026 (1st Semester)</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-xl-3 col-lg-4">
            <div class="card tilebox-one">
                <div class="card-body">
                    <i class='uil uil-users-alt float-end'></i>
                    <h6 class="text-uppercase mt-0">Total Reservations</h6>
                    <h2 class="my-2" id="active-users-count">121</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-success me-2"><span class="mdi mdi-arrow-up-bold"></span> 5.27%</span>
                        <span class="text-nowrap">Since last month</span>  
                    </p>
                </div> <!-- end card-body-->
            </div>
            <!--end card-->

            <div class="card tilebox-one">
                <div class="card-body">
                    <i class='uil uil-window-restore float-end'></i>
                    <h6 class="text-uppercase mt-0">Pending Reservations</h6>
                    <h2 class="my-2" id="active-views-count">560</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-danger me-2"><span class="mdi mdi-arrow-down-bold"></span> 1.08%</span>
                        <span class="text-nowrap">Since previous week</span>
                    </p>
                </div> <!-- end card-body-->
            </div>
            <!--end card-->

            <div class="card tilebox-one">
                <div class="card-body">
                    <i class='uil uil-window-restore float-end'></i>
                    <h6 class="text-uppercase mt-0">Enrolled Reservations</h6>
                    <h2 class="my-2" id="active-views-count">560</h2>
                    <p class="mb-0 text-muted">
                        <span class="text-danger me-2"><span class="mdi mdi-arrow-down-bold"></span> 1.08%</span>
                        <span class="text-nowrap">Since previous week</span>
                    </p>
                </div> <!-- end card-body-->
            </div>
            <!--end card-->
        </div> <!-- end col -->

        <div class="col-xl-9 col-lg-8">
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
                <div class="col-sm-6">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <h5 class="text-muted fw-normal mt-0 prog_title">Assessment and Certification</h5>
                            <h3 class="mt-3 mb-3">36,254</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-success me-2"><i class="mdi mdi-arrow-up-bold"></i> 5.27%</span>
                                <span class="text-nowrap">Since last month</span>  
                            </p>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->

                <div class="col-sm-6">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <h5 class="text-muted fw-normal mt-0 prog_title">Foreign Language Program</h5>
                            <h3 class="mt-3 mb-3">5,543</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-danger me-2"><i class="mdi mdi-arrow-down-bold"></i> 1.08%</span>
                                <span class="text-nowrap">Since last month</span>
                            </p>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->
            </div> <!-- end row -->

            <div class="row">
                <div class="col-sm-6">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <h5 class="text-muted fw-normal mt-0 prog_title">Certificate Programs</h5>
                            <h3 class="mt-3 mb-3">$6,254</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-danger me-2"><i class="mdi mdi-arrow-down-bold"></i> 7.00%</span>
                                <span class="text-nowrap">Since last month</span>
                            </p>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->

                <div class="col-sm-6">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <h5 class="text-muted fw-normal mt-0 prog_title">Short-Term Programs/Courses</h5>
                            <h3 class="mt-3 mb-3">+ 30.56%</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-success me-2"><i class="mdi mdi-arrow-up-bold"></i> 4.87%</span>
                                <span class="text-nowrap">Since last month</span>
                            </p>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->
            </div> <!-- end row -->

            <div class="row">
                <div class="col-sm-6">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <h5 class="text-muted fw-normal mt-0 prog_title">Micro-credentials Program</h5>
                            <h3 class="mt-3 mb-3">$6,254</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-danger me-2"><i class="mdi mdi-arrow-down-bold"></i> 7.00%</span>
                                <span class="text-nowrap">Since last month</span>
                            </p>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->

                <div class="col-sm-6">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <h5 class="text-muted fw-normal mt-0 prog_title">Microsoft Office Specialist</h5>
                            <h3 class="mt-3 mb-3">+ 30.56%</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-success me-2"><i class="mdi mdi-arrow-up-bold"></i> 4.87%</span>
                                <span class="text-nowrap">Since last month</span>
                            </p>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->
            </div> <!-- end row -->

        </div> <!-- end col -->
    </div>
    <!-- end row -->

</div>
<!-- container -->
  

<?php
include 'footer.php';
?>
<script>
Highcharts.chart('container', {
    chart: {
        type: 'line',
        height: 415,
        backgroundColor: '#ffffff00',
    },

    title: {
        text: 'TOTAL NUMBER OF RESERVATIONS PER MONTH',
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
        categories: [
            'Jan', 
            'Feb', 
            'Mar', 
            'Apr', 
            'May', 
            'Jun', 
            'Jul', 
            'Aug', 
            'Sep', 
            'Oct', 
            'Nov', 
            'Dec'
        ]
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

    series: [{
        name: 'Internet Users',
        data: [16, 361, 1018, 2025, 3192, 4673, 5200, 16, 361, 1018, 2025, 3192],
        color: '#2caffe'
    }]
});


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
            data: [
                {
                    name: 'Assessment and Certification',
                    y: 55.02
                },
                {
                    name: 'Foreign Language Program',
                    y: 26.71
                },
                {
                    name: 'Certificate Programs',
                    y: 1.09
                },
                {
                    name: 'Short-Term Programs/Courses',
                    y: 15.5
                },
                {
                    name: 'Micro-credentials Program',
                    y: 1.68
                },
                {
                    name: 'Microsoft Office Specialist',
                    y: 1.68
                }
            ]
        }
    ]
});
</script>