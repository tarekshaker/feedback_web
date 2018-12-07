<!-- page content -->
<div class="right_col" role="main">
    <?php if ($this->uri->segment(1) == 'organization') { ?>
        <style media="screen">
            .count_top {
                font-size: 20px !important;
            }

            .poor {
                color: red !important;
            }

            .good {
                color: #1ABB9C !important;
            }

            .excellent {
                color: #4C9ED9 !important;

            }
        </style>
        <!-- top tiles -->

        <div class="row top_tiles">
            <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats excellent">
                    <div class="icon"><i class="fa fa-gratipay"></i></div>
                    <div><span class="count"><?php if (isset($exc) && ($exc > 0)) {
                                echo ($exc / ($exc + $good + $accept)) * 100;
                            } else {
                                echo 0;
                            } ?></span>%
                    </div>
                    <h3>Excellent</h3>
                </div>
            </div>
            <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats good">
                    <div class="icon"><i class="fa fa-thumbs-o-up"></i></div>
                    <div><span class="count"><?php if (isset($good) && ($good > 0)) {
                                echo ($good / ($exc + $good + $accept)) * 100;
                            } else {
                                echo 0;
                            } ?></span>%
                    </div>
                    <h3>Good</h3>
                </div>
            </div>
            <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats poor">
                    <div class="icon"><i class="fa fa fa-thumbs-o-down"></i></div>
                    <div><span class="count"><?php if (isset($accept) && ($accept > 0)) {
                                echo ($accept / ($exc + $good + $accept)) * 100;
                            } else {
                                echo 0;
                            } ?></span>%
                    </div>
                    <h3>Poor</h3>
                </div>
            </div>
            <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-wpforms"></i></div>
                    <div><span class="count"><?php if (isset($total) && ($total > 0)) {
                                echo ($total / ($exc + $good + $accept)) * 100;
                            } else {
                                echo 0;
                            } ?></span>%
                    </div>
                    <h3>Total Ratio</h3>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $('.count').each(function () {
                $(this).prop('Counter', 0).animate({
                    Counter: $(this).text()
                }, {
                    duration: 4000,
                    easing: 'swing',
                    step: function (now) {
                        $(this).text(now.toFixed(0));
                    }
                });
            });
        </script>
        <!-- ******************** Start for searching ******************** -->
        <script src="<?php echo base_url(); ?>assets/new/moment.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/new/daterangepicker.js"></script>
        <script>
            $(document).ready(function () {
                var cb = function (start, end, label) {
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                };

                var optionSet1 = {
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment(),
                    minDate: '01/01/2016',
                    maxDate: '12/31/2099',
                    dateLimit: {
                        days: 36500
                    },
                    showDropdowns: true,
                    showWeekNumbers: true,
                    timePicker: false,
                    timePickerIncrement: 1,
                    timePicker12Hour: true,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    opens: 'left',
                    buttonClasses: ['btn btn-default'],
                    cancelClass: 'btn-small',
                    format: 'YYYY-MM-DD',
                    separator: ' to ',
                    locale: {
                        applyLabel: 'Apply',
                        cancelLabel: 'Clear',
                        fromLabel: 'From',
                        toLabel: 'To',
                        customRangeLabel: 'Custom range',
                        daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                        firstDay: 1
                    }
                };
                $('#reportrange span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
                $('#from').val(moment().subtract(29, 'days').format('YYYY-MM-DD'));
                $('#to').val(moment().format('YYYY-MM-DD'));
                $('#reportrange').daterangepicker(optionSet1, cb);

                $('#reportrange').data('daterangepicker').setStartDate(moment('<?php echo $_POST['from'];?>'));
                $('#reportrange').data('daterangepicker').setEndDate(moment('<?php echo $_POST['to'];?>'));


                $('#reportrange').on('show.daterangepicker', function () {
                });
                $('#reportrange').on('hide.daterangepicker', function () {
                });
                $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
                    console.log("apply event fired, start/end dates are " + picker.startDate.format('MMMM D, YYYY') + " to " + picker.endDate.format('MMMM D, YYYY'));
                    $('#from').val(picker.startDate.format('YYYY-MM-DD'));
                    $('#to').val(picker.endDate.format('YYYY-MM-DD'));
                });
                $('#reportrange').on('cancel.daterangepicker', function (ev, picker) {
                });
                $('#options1').click(function () {
                    $('#reportrange').data('daterangepicker').setOptions(optionSet1, cb);
                });
                $('#options2').click(function () {
                    $('#reportrange').data('daterangepicker').setOptions(optionSet2, cb);
                });
                $('#destroy').click(function () {
                    $('#reportrange').data('daterangepicker').remove();
                });
                $('.range_inputs > button.cancelBtn').remove();


                $('#clear-form').on('click', function () {
                    $('.select2_single').select2("val", " ");
                    $('#reportrange').val('');
                });

            });
        </script>
        <!-- Select2 -->
        <script src="<?php echo base_url(); ?>assets/select2.min.js"></script>
        <link href="<?php echo base_url(); ?>assets/select2.min.css" rel="stylesheet">
        <script>
            $(document).ready(function () {
                $(".select2_single").select2({
                    placeholder: "Select a Sector",
                    allowClear: true
                });
            });
        </script>
        <!-- bootstrap-daterangepicker -->
        <!-- /top tiles -->
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="dashboard_graph">
                    <div class="row x_title">
                        <div class="col-md-3">
                            <h3>Dashboard Filter</h3>
                        </div>
                        <div class="col-md-9">
                            <form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left"
                                  method="post" action="<?php echo base_url(); ?>organization/filteredDash">
                                <div class="col-md-4 col-sm-12 col-xs-12">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <label>Sector: </label>
                                        <select name="sector" class="select2_single form-control" tabindex="-1"
                                            style="width: 350px;"  required>
                                            <?php
                                            if (isset($sectors)) {
                                                foreach ($sectors as $sector) {
//                        echo "<option value='{$sector}'>{$sector}</option>";
                                                    echo "<option value='{$sector}' " . set_select('sector', $sector) . " >" . $sector . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12 col-xs-12">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <label>Date Range: </label>
                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                        <span></span> <b class="caret"></b>
                                        <input id="reportrange" placeholder="Select Date Range"
                                               style="background: #fff; cursor: pointer; padding: 8px 10px; border: 1px solid #ccc">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12 col-xs-12">
                                    <input type="hidden" name="from" id="from" value="">
                                    <input type="hidden" name="to" id="to" value="">
                                    <button class="applyBtn btn btn-default btn-small btn-primary" type="submit">
                                        Search
                                    </button>
                                    <input type="button" class="btn btn-success btn-small" id="clear-form"
                                           value="Clear"/>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <!-- ******************** End for searching ******************** -->

        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="x_panel tile fixed_height_320">
                    <div class="x_title">
                        <h2>All Branches</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
<!--                            <li><a class="close-link"><i class="fa fa-close"></i></a>-->
<!--                            </li>-->
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="sidebar-widget">
                            <h4>Total Feedbacks Counter</h4>
                            <canvas width="400" height="190" id="foo" class=""
                                    style="width: 100%; height: 100%;"></canvas>
                            <div class="goal-wrapper">
                                <span id="gauge-text" class="gauge-value pull-left"><?php if (isset($sectorFeedback)) {
                                        echo $sectorFeedback;
                                    } ?></span>
                                <span id="goal-text" class="goal-value pull-right"><?php if (isset($totalFeedbacks)) {
                                        echo $totalFeedbacks;
                                    } ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- gauge.js -->
            <!-- gauge.js -->
            <script src="<?php echo base_url(); ?>assets/new/gauge.min.js"></script>
            <script>
                var opts = {
                    lines: 12,
                    angle: 0,
                    lineWidth: 0.4,
                    pointer: {
                        length: 0.75,
                        strokeWidth: 0.042,
                        color: '#1D212A'
                    },
                    limitMax: 'false',
                    colorStart: '#1ABC9C',
                    colorStop: '#1ABC9C',
                    strokeColor: '#F0F3F3',
                    generateGradient: true
                };
                var target = document.getElementById('foo'),
                    gauge = new Gauge(target).setOptions(opts);

                gauge.maxValue = <?php if (isset($totalFeedbacks)) {
                    echo $totalFeedbacks;
                }?>;
                gauge.animationSpeed = 32;
                gauge.set(<?php if (isset($sectorFeedback)) {
                    echo $sectorFeedback;
                }?>);
                gauge.setTextField(document.getElementById("gauge-text"));
            </script>
            <!-- /gauge.js -->

            <script src="<?php echo base_url(); ?>assets/echarts.min.js"></script>
            <!-- ******************** Start for donut chart ******************** -->
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Total Branches feedbacks</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
<!--                            <li><a class="close-link"><i class="fa fa-close"></i></a>-->
<!--                            </li>-->
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div id="echart_donut" style="height:238px;"></div>
                    </div>
                </div>
            </div>

            <!-- ******************** Start for Bar chart ******************** -->
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Percent of feedbacks in all Branches</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
<!--                            <li><a class="close-link"><i class="fa fa-close"></i></a>-->
<!--                            </li>-->
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" id="bar">
                        <div id="mainb" style="height:238px;"></div>
                    </div>
                </div>
            </div>
            <!-- ******************** end for Bar chart ******************** -->
        </div>
        <div class="row">
            <!-- ******************** Start for donut chart ******************** -->
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Feedback result for branches</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
<!--                            <li><a class="close-link"><i class="fa fa-close"></i></a>-->
<!--                            </li>-->
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" id="don">
                        <select class="form-control" id="sel2">
                            <?php
                            if (isset($branches)) {
                                foreach ($branches as $branch) {
                                    echo "<option value='{$branch['id']}'>{$branch['bran_name']}</option>";
                                }
                            }
                            ?>
                        </select>
                        <div id="echart_donut_branch" style="height:238px;"></div>
                    </div>
                </div>
            </div>
            <!-- ******************** end for donut chart ******************** -->

            <!-- ******************** Start for Bar chart ******************** -->
            <div class="col-md-8 col-sm-8 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Branches Comparing</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
<!--                            <li><a class="close-link"><i class="fa fa-close"></i></a>-->
<!--                            </li>-->
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" id="barcomparing">
                        <select class="select2_multiple form-control" multiple="multiple" id="sel1">
                            <?php
                            if (isset($branchnames)) {
                                for ($i = 0; $i < sizeof($branchnames); $i++) {
                                    if ($i == 0) {
                                        echo "<option selected value='{$i}'>{$branchnames[$i]}</option>";
                                    } else {
                                        echo "<option value='{$i}'>{$branchnames[$i]}</option>";
                                    }
                                }
                            }
                            ?>
                        </select>
                        <div id="comparebar" style="height:238px;"></div>
                    </div>
                </div>
            </div>
            <!-- ******************** end for Bar chart ******************** -->
        </div>


        <script type="text/javascript">
            var theme = {
                color: [
                    '#26B99A', '#34495E', '#BDC3C7', '#3498DB',
                    '#9B59B6', '#8abb6f', '#759c6a', '#bfd3b7'
                ],

                title: {
                    itemGap: 8,
                    textStyle: {
                        fontWeight: 'normal',
                        color: '#408829'
                    }
                },

                dataRange: {
                    color: ['#1f610a', '#97b58d']
                },

                toolbox: {
                    color: ['#408829', '#408829', '#408829', '#408829']
                },

                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.5)',
                    axisPointer: {
                        type: 'line',
                        lineStyle: {
                            color: '#408829',
                            type: 'dashed'
                        },
                        crossStyle: {
                            color: '#408829'
                        },
                        shadowStyle: {
                            color: 'rgba(200,200,200,0.3)'
                        }
                    }
                },

                dataZoom: {
                    dataBackgroundColor: '#eee',
                    fillerColor: 'rgba(64,136,41,0.2)',
                    handleColor: '#408829'
                },
                grid: {
                    borderWidth: 0
                },

                categoryAxis: {
                    axisLine: {
                        lineStyle: {
                            color: '#408829'
                        }
                    },
                    splitLine: {
                        lineStyle: {
                            color: ['#eee']
                        }
                    }
                },

                valueAxis: {
                    axisLine: {
                        lineStyle: {
                            color: '#408829'
                        }
                    },
                    splitArea: {
                        show: true,
                        areaStyle: {
                            color: ['rgba(250,250,250,0.1)', 'rgba(200,200,200,0.1)']
                        }
                    },
                    splitLine: {
                        lineStyle: {
                            color: ['#eee']
                        }
                    }
                },
                k: {
                    itemStyle: {
                        normal: {
                            color: '#68a54a',
                            color0: '#a9cba2',
                            lineStyle: {
                                width: 1,
                                color: '#408829',
                                color0: '#86b379'
                            }
                        }
                    }
                },
                force: {
                    itemStyle: {
                        normal: {
                            linkStyle: {
                                strokeColor: '#408829'
                            }
                        }
                    }
                },
                textStyle: {
                    fontFamily: 'Arial, Verdana, sans-serif'
                }
            };

            $(document).ready(function () {

                var percentPerBranch = <?php if (isset($branchPercent)) {
                    echo json_encode($branchPercent);
                } else {
                    echo json_encode(array(0 => 0));
                } ?>;
                var echartDonut = echarts.init(document.getElementById('echart_donut'), theme);
                var items =  <?php if (isset($donutBranches)) {
                    echo json_encode($donutBranches);
                } else {
                    echo json_encode(array(0 => 0));
                } ?>;
                var info =  <?php if (isset($donut)) {
                    echo json_encode($donut);
                } else {
                    echo json_encode(array(0 => 0));
                } ?>;
                drawDonut(echartDonut, items, info);

                var info = percentPerBranch[$('#sel2').val()];
                var echartDonut = echarts.init(document.getElementById('echart_donut_branch'), theme);
                drawDonut(echartDonut, ['Excellent', 'Good', 'Poor'], info);
                $('#sel2').on('change', function () {
                    $('#echart_donut_branch').remove();
                    $('#don').append("<div id='echart_donut_branch' style='height:238px;'></div>");
                    var echartDonut = echarts.init(document.getElementById('echart_donut_branch'), theme);
                    var info = percentPerBranch[$('#sel2').val()];
                    drawDonut(echartDonut, ['Excellent', 'Good', 'Poor'], info);
                });

                function drawDonut(eDonut, items, info) {
                    eDonut.setOption({
                        tooltip: {trigger: 'item', formatter: "{a} <br/>{b} : {c} ({d}%)"},
                        calculable: true,
                        legend: {x: 'center', y: 'bottom', data: items},
                        toolbox: {
                            show: true,
                            feature: {
                                magicType: {
                                    show: true, type: ['pie', 'funnel'],
                                    option: {
                                        funnel: {
                                            x: '25%', width: '50%', funnelAlign: 'center', max: 1548
                                        }
                                    }
                                },
                                restore: {show: true, title: "Restore"},
                                saveAsImage: {show: true, title: "Save Image"}
                            }
                        },
                        series: [{
                            name: 'Access to the resource', type: 'pie',
                            radius: ['35%', '55%'], itemStyle: {
                                normal: {
                                    label: {how: true},
                                    labelLine: {show: true}
                                },
                                emphasis: {
                                    label: {
                                        show: true, position: 'center',
                                        textStyle: {
                                            fontSize: '14', fontWeight: 'normal'
                                        }
                                    }
                                }
                            },
                            data: info
                        }]
                    });
                }

                $(".select2_multiple").select2({
                    maximumSelectionLength: 3,
                    placeholder: "With Max Selection limit 3",
                    allowClear: true
                });

                var groupnames =<?php if (isset($groupname)) {
                    echo json_encode($groupname);
                } else {
                    echo json_encode(array(0 => 0));
                }?>;
                var branchnames = <?php if (isset($branchnames)) {
                    echo json_encode($branchnames);
                } else {
                    echo json_encode(array(0 => 0));
                }?>;
                var comparingchartPercent = <?php if (isset($comparingchartPercent)) {
                    echo json_encode($comparingchartPercent);
                } else {
                    echo json_encode(array(0 => 0));
                }?>;
                var echartBar = echarts.init(document.getElementById('comparebar'), theme);

                var privilege = [];
                var select = $('#sel1').val();
                for (var i = 0; i < select.length; i++) {
                    privilege[i] = {};
                    privilege[i].name = branchnames[select[i]];
                    privilege[i].type = 'bar';
                    privilege[i].markPoint = {};
                    privilege[i].data = comparingchartPercent[select[i]];
                    privilege[i].markPoint.data = [];
                    privilege[i].markPoint.data[0] = {};
                    privilege[i].markPoint.data[0].type = 'max';
                    privilege[i].markPoint.data[0].name = 'Max Value';
                    privilege[i].markPoint.data[1] = {};
                    privilege[i].markPoint.data[1].type = 'min';
                    privilege[i].markPoint.data[1].name = 'Min Value';
                    privilege[i].markLine = {};
                    privilege[i].markLine.data = [];
                    privilege[i].markLine.data[0] = {};
                    privilege[i].markLine.data[0].type = 'average';
                    privilege[i].markLine.data[0].name = 'Average';
                }

                drawbar(echartBar, groupnames, branchnames, privilege);

                $('#sel1').on('change', function () {
                    var selected = $(this).val();
                    $('#comparebar').remove();
                    $('#barcomparing').append("<div id='comparebar' style='height:238px;'></div>");
                    var echartBar = echarts.init(document.getElementById('comparebar'), theme);

                    var privilege = [];

                    for (var i = 0; i < selected.length; i++) {
                        privilege[i] = {};
                        privilege[i].name = branchnames[selected[i]];
                        privilege[i].type = 'bar';
                        privilege[i].markPoint = {};
                        privilege[i].data = comparingchartPercent[selected[i]];
                        privilege[i].markPoint.data = [];
                        privilege[i].markPoint.data[0] = {};
                        privilege[i].markPoint.data[0].type = 'max';
                        privilege[i].markPoint.data[0].name = 'Max Value';
                        privilege[i].markPoint.data[1] = {};
                        privilege[i].markPoint.data[1].type = 'min';
                        privilege[i].markPoint.data[1].name = 'Min Value';
                        privilege[i].markLine = {};
                        privilege[i].markLine.data = [];
                        privilege[i].markLine.data[0] = {};
                        privilege[i].markLine.data[0].type = 'average';
                        privilege[i].markLine.data[0].name = 'Average';
                    }

                    drawbar(echartBar, groupnames, branchnames, privilege);
                });

                var brancheNames =<?php echo json_encode(array_column($barchart, 'bran_name'));?>;
                var percentages = [];
                percentages[0] = <?php echo json_encode(array_column($barchart, 'percent'));?>;
                var echartBar = echarts.init(document.getElementById('mainb'), theme);

                var privilege = [];

                for (var i = 0; i < percentages.length; i++) {
                    privilege[i] = {};
                    privilege[i].name = 'Branches';
                    privilege[i].type = 'bar';
                    privilege[i].markPoint = {};
                    privilege[i].data = percentages[i];
                    privilege[i].markPoint.data = [];
                    privilege[i].markPoint.data[0] = {};
                    privilege[i].markPoint.data[0].type = 'max';
                    privilege[i].markPoint.data[0].name = 'Max Value';
                    privilege[i].markPoint.data[1] = {};
                    privilege[i].markPoint.data[1].type = 'min';
                    privilege[i].markPoint.data[1].name = 'Min Value';
                    privilege[i].markLine = {};
                    privilege[i].markLine.data = [];
                    privilege[i].markLine.data[0] = {};
                    privilege[i].markLine.data[0].type = 'average';
                    privilege[i].markLine.data[0].name = 'Average';
                }
                drawbar(echartBar, brancheNames, ['Branches'], privilege);

                function drawbar(echartBar, brancheNames, selects, privilege) {
                    echartBar.setOption({

                        tooltip: {
                            trigger: 'axis'
                        },
                        legend: {
                            data: selects
                        },
                        toolbox: {
                            show: true,
                            feature: {
                                mark: {
                                    show: true,
                                    title: "Mark"
                                },
                                magicType: {
                                    show: true, type: ['line', 'bar'],
                                    title: "Magic Type"
                                },
                                restore: {
                                    show: true,
                                    title: "Restore"
                                },
                                saveAsImage: {
                                    show: true,
                                    title: "Save Image"
                                }
                            }
                        }, calculable: true, xAxis: [{
                            type: 'category', data: brancheNames
                        }], yAxis: [{type: 'value'}],
                        series: privilege
                    });
                }

            });

        </script>
        <!-- ******************** end for donut chart ******************** -->
    <?php } ?>
</div>
