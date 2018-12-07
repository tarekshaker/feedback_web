<?php
foreach ($crud_output->css_files as $file): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>"/>
<?php endforeach; ?>
<?php foreach ($crud_output->js_files as $file): ?>
    <script src="<?php echo $file; ?>"></script>
<?php endforeach; ?>

<!-- Page Content -->
<div class="right_col" role="main">
    <div class="row">


        <?php if (($this->uri->segment(2) == "employeeSection") || (($this->uri->segment(2) == 'customerBehaviour') && !$this->uri->segment(4) && $this->uri->segment(3) != '')) { ?>
            <!-- Select2 -->
            <script src="<?php echo base_url(); ?>assets/select2.min.js"></script>
            <link href="<?php echo base_url(); ?>assets/select2.min.css" rel="stylesheet">
            <script src="<?php echo base_url(); ?>assets/echarts.min.js"></script>

            <script>
                $(document).ready(function () {
                    var theme = {
                        color: [
                            '#26B99A', '#34495E', '#BDC3C7', '#3498DB',
                            '#9B59B6', '#8abb6f', '#759c6a', '#bfd3b7'],
                        title: {itemGap: 8, textStyle: {fontWeight: 'normal', color: '#408829'}},
                        dataRange: {color: ['#1f610a', '#97b58d']},
                        toolbox: {color: ['#408829', '#408829', '#408829', '#408829']},
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.5)',
                            axisPointer: {
                                type: 'line', lineStyle: {color: '#408829', type: 'dashed'},
                                crossStyle: {color: '#408829'}, shadowStyle: {color: 'rgba(200,200,200,0.3)'}
                            }
                        },
                        dataZoom: {
                            dataBackgroundColor: '#eee',
                            fillerColor: 'rgba(64,136,41,0.2)',
                            handleColor: '#408829'
                        },
                        grid: {borderWidth: 0},
                        categoryAxis: {
                            axisLine: {lineStyle: {color: '#408829'}},
                            splitLine: {lineStyle: {color: ['#eee']}}
                        },
                        valueAxis: {
                            axisLine: {lineStyle: {color: '#408829'}},
                            splitArea: {
                                show: true, areaStyle: {
                                    color: ['rgba(250,250,250,0.1)', 'rgba(200,200,200,0.1)']
                                }
                            },
                            splitLine: {lineStyle: {color: ['#eee']}}
                        },
                        k: {
                            itemStyle: {
                                normal: {
                                    color: '#68a54a', color0: '#a9cba2',
                                    lineStyle: {width: 1, color: '#408829', color0: '#86b379'}
                                }
                            }
                        }, force: {
                            itemStyle: {normal: {linkStyle: {strokeColor: '#408829'}}}
                        }, textStyle: {fontFamily: 'Arial, Verdana, sans-serif'}
                    };
                    var questions = <?php if (isset($questions)) {
                        echo json_encode($questions);
                    } else {
                        echo json_encode(array(0 => 0));
                    }?>;
                    var empNames = <?php if (isset($empNames)) {
                        echo json_encode($empNames);
                    } else {
                        echo json_encode(array(0 => 0));
                    }?>;
                    var comparingchartPercent = <?php if (isset($comparingchartPercent)) {
                        echo json_encode($comparingchartPercent);
                    } else {
                        echo json_encode(array(0 => 0));
                    }?>;

                    <?php if($this->uri->segment(2) == "employeeSection"){ ?>
                    $(".select2_multiple").select2({
                        maximumSelectionLength: 4,
                        placeholder: "With Max Selection limit 4",
                        allowClear: true
                    });



                    <?php }else{ ?>
                    $(".select2_multiple").select2({
                        maximumSelectionLength: 1,
                        placeholder: "With Max Selection limit 1",
                        allowClear: true
                    });

                    <?php  } ?>
                    $('#sel1').on('change', function () {
                        var selected = $(this).val();
                        $('#comparebar').remove();
                        $('#barcomparing').append("<div id='comparebar' style='height:238px;'></div>");
                        var echartBar = echarts.init(document.getElementById('comparebar'), theme);

                        var privilege = [];

                        for (var i = 0; i < selected.length; i++) {
                            privilege[i] = {};
                            privilege[i].name = empNames[selected[i]];
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
                        <?php if($this->uri->segment(2) == "employeeSection"){ ?>
                        drawbar(echartBar, questions, empNames, privilege);
                        <?php }else{ ?>
                        drawbar(echartBar, questions[selected[0]], empNames, privilege);
                        <?php  } ?>
                    });

                    function drawbar(echartBar, brancheNames, selects, privilege) {
                        echartBar.setOption({

                            tooltip: {trigger: 'axis'},
                            legend: {data: selects},
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

                    $('#chart').css("display", "none");
                    $('#listbutton').on('click', function () {
                        $('#table').css("display", "block");
                        $('#chart').css("display", "none");
                    });
                    $('#chartbutton').on('click', function () {
                        $('#table').css("display", "none");
                        $('#chart').css("display", "block");
                    });
                });
            </script>
            <!-- Select2 -->
            <div class="col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                <div class="btn-group">
                    <button id="listbutton" class="btn btn-default" type="button">List</button>
                    <button id="chartbutton" class="btn btn-default" type="button">Chart</button>
                </div>
            </div>
            <div id="chart" class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <?php if($this->uri->segment(2) == "employeeSection"){ ?>
                        <h2>Comparing Employees</h2>
                        <?php }else{ ?>
                        <h2>Comparing Branches</h2>
                        <?php } ?>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" id="barcomparing">
                        <select class="select2_multiple form-control" multiple="multiple" id="sel1" style="width=100%">
                            <?php
                            if (isset($empNames)) {
                                for ($i = 0; $i < sizeof($empNames); $i++) {
                                    if ($i == 0) {
                                        echo "<option  value='{$i}'>{$empNames[$i]}</option>";
                                    } else {
                                        echo "<option value='{$i}'>{$empNames[$i]}</option>";
                                    }
                                }
                            }
                            ?>
                        </select>

                        <div id="comparebar" style="height:238px;"></div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div id="table" class="col-md-12 col-sm-12 col-xs-12">
            <h1 class="page-header"><?php echo $page_title; ?></h1>
            <?php
            if ($this->uri->segment(2) == 'reportSection') {
                ?>
                <!-- Select2 -->
                <script src="<?php echo base_url(); ?>assets/select2.min.js"></script>
                <link href="<?php echo base_url(); ?>assets/select2.min.css" rel="stylesheet">
                <script src="<?php echo base_url(); ?>assets/new/moment.min.js"></script>
                <script src="<?php echo base_url(); ?>assets/new/daterangepicker.js"></script>
                <script type="text/javascript">

                    $.fn.dataTable.ext.search.push(
                        function (settings, data, dataIndex) {
                            var min = parseInt($('#min').val(), 10);
                            var max = parseInt($('#max').val(), 10);
                            var age = parseFloat(data[4]) || 0; // use data for the age column

                            if ((isNaN(min) && isNaN(max)) ||
                                (isNaN(min) && age <= max) ||
                                (min <= age && isNaN(max)) ||
                                (min <= age && age <= max)) {
                                return true;
                            }
                            return false;
                        }
                    );
                    $(document).ready(function () {
                        var table = $('table').DataTable();
                        // Event listener to the two range filtering inputs to redraw on input
                        $('#min, #max').keyup(function () {
                            table.draw();
                        });
                    });
                    $(document).ready(function () {
                        $(".select_3").select2({
                            allowClear: true
                        });

                        $("#sectors").select2({
                            placeholder: "Select sectors"
                        });

                        $("#branches").select2({
                            placeholder: "Select branches"
                        });

                        $("#genders").select2({
                            placeholder: "Select gender"
                        });

                        $("#groups").select2({
                            placeholder: "Select group"
                        });


                        var cb = function (start, end, label) {
                            $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                            $('#reportrange').val('');
                        };

                        var optionSet1 = {
                            startDate: moment().subtract(29, 'days'),
                            endDate: moment(),
                            minDate: '01/01/2016',
                            maxDate: '31/12/2099',
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
                            format: 'DD/MM/YYYY',
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
                        $('#reportrange').daterangepicker(optionSet1, cb);
                        $('#from').val(moment().subtract(29, 'days').format('YYYY-MM-DD'));
                        $('#to').val(moment().format('YYYY-MM-DD'));

                        <?php if (isset($_POST['from']) && isset($_POST['to'])){ ?>
                        $('#reportrange').data('daterangepicker').setStartDate(moment('<?php echo $_POST['from'];?>'));
                        $('#reportrange').data('daterangepicker').setEndDate(moment('<?php echo $_POST['to'];?>'));
                        <?php } ?>
                        $('#reportrange').on('show.daterangepicker', function () {
                        });
                        $('#reportrange').on('hide.daterangepicker', function () {
                        });
                        $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
                            console.log("apply event fired, start/end dates are " + picker.startDate.format('DD/MM/YYYY') + " to " + picker.endDate.format('DD/MM/YYYY'));
                        });
                        $('#reportrange').on('cancel.daterangepicker', function (ev, picker) {
                            $(this).val('');
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

                        $('#clear-form').on('click', function () {
                            $('.select_3').select2("val", " ");
                            $('#reportrange').val('');
                            $('#min').val('');
                            $('#max').val('');
                        });

                    });
                </script>
                <form class="form-inline" action="" method="post">
                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Gender:</label>
                            <style media="screen">
                                .select2 {
                                    width: 290px !important;
                                }
                            </style>
                            <select class="select_3 form-control input-lg" name="genders" id="genders"
                                    style="width=200px">
                                <?php
                                echo "<option value=''></option>";
                                if (isset($genders)) {
                                    for ($i = 0; $i < sizeof($genders); $i++) {
//                                        echo "<option value='{$genders[$i]}'>{$genders[$i]}</option>";
                                        echo "<option value='{$genders[$i]}' " . set_select('genders', $genders[$i]) . " >" . $genders[$i] . "</option>";
                                    }
                                }
                                ?>
                            </select>

                        </div>
                    </div>

                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Group:</label>
                            <style media="screen">
                                .select2 {
                                    width: 290px !important;
                                }
                            </style>
                            <select class="select_3 form-control input-lg" name="groups" id="groups"
                                    style="width=200px">
                                <?php
                                echo "<option value=''></option>";
                                if (isset($groups)) {
                                    for ($i = 0; $i < sizeof($groups); $i++) {
//                                        echo "<option value='{$groups[$i]['group_name']}'>{$groups[$i]['group_name']}</option>";
                                        echo "<option value='{$groups[$i]['id']}' " . set_select('groups', $groups[$i]['id']) . " >" . $groups[$i]['group_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>

                        </div>
                    </div>

                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Age:</label>
                            <input id="min" placeholder="Min" name="min" class="form-control input-sm "
                                   style="width: 70px;" type="number" min="1" max="99"
                                   value="<?php
                                   if (isset($_POST['min'])) {
                                       echo $_POST['min'];
                                   }
                                   ?>">
                            <input id="max" placeholder="Max" name="max" class="form-control input-sm "
                                   style="width: 70px;" type="number" min="1" max="99"
                                   value="<?php
                                   if (isset($_POST['max'])) {
                                       echo $_POST['max'];
                                   }
                                   ?>">
                        </div>
                    </div>

                    <br><br>

                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Sectors:</label>
                            <style media="screen">
                                .select2 {
                                    width: 290px !important;
                                }
                            </style>
                            <select class="select_3 form-control input-lg" name="sectors[]" multiple="multiple"
                                    id="sectors" style="width:400px">
                                <?php
                                if (isset($sectors)) {
                                    for ($i = 0; $i < sizeof($sectors); $i++) {
//                                        echo "<option value='{$sectors[$i]['Sector']}'>{$sectors[$i]['Sector']}</option>";
                                        echo "<option value='{$sectors[$i]['Sector']}' " . set_select('sectors', $sectors[$i]['Sector']) . " >" . $sectors[$i]['Sector'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Branches:</label>
                            <style media="screen">
                                .select2 {
                                    width: 290px !important;
                                }
                            </style>
                            <select class=" select_3 form-control input-lg" name="branches[]" multiple="multiple"
                                    id="branches" style="width:400px">
                                <?php
                                if (isset($branches)) {
                                    for ($i = 0; $i < sizeof($branches); $i++) {
//                                        echo "<option value='{$branches[$i]['bran_name']}'>{$branches[$i]['bran_name']}</option>";
                                        echo "<option value='{$branches[$i]['bran_name']}' " . set_select('branches', $branches[$i]['bran_name']) . " >" . $branches[$i]['bran_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 column">
                        <div class="form-group">
                            <label>Review Time:</label>
                            <input name="daterange" id="reportrange" placeholder="Select Date Range"
                                   class="date-picker form-control input-sm " type="text">
                        </div>
                    </div>

                    <br><br>


                    <div class="col-md-12 column" style="text-align: center;">
                        <input type="hidden" name="from" id="from" value="">
                        <input type="hidden" name="to" id="to" value="">
                        <button type="submit" class="btn btn-default btn-primary" name="button">Filter</button>
                        <input type="button" class="btn btn-success btn-small" id="clear-form"
                               value="Clear"/>
                    </div>


                </form>
            <?php } ?>
            <?php
            if ($this->uri->segment(2) == 'customerBehaviour') { ?>
                <!-- Select2 -->
                <script src="<?php echo base_url(); ?>assets/select2.min.js"></script>
                <link href="<?php echo base_url(); ?>assets/select2.min.css" rel="stylesheet">

            <?php if ($this->uri->segment(3) && !$this->uri->segment(4)) { ?>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Age</th>
                        <th>All Reviews Result</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?= $client[0]['name'] ?></td>
                        <td><?= $client[0]['gender'] ?></td>
                        <td><?= $client[0]['email'] ?></td>
                        <td><?= $client[0]['phone'] ?></td>
                        <td><?= $client[0]['age'] ?></td>
                        <td><?= $client[0]['all_reviews'] ?></td>
                    </tr>
                    </tbody>
                </table>
            <?php } ?>
            <?php if ($this->uri->segment(4) && $this->uri->segment(5)) { ?>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Age</th>
                        <th>Review Result</th>
                        <th>Branch</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?= $client[0]['name'] ?></td>
                        <td><?= $client[0]['gender'] ?></td>
                        <td><?= $client[0]['email'] ?></td>
                        <td><?= $client[0]['phone'] ?></td>
                        <td><?= $client[0]['age'] ?></td>
                        <td><?= $review[0]['percent'] ?></td>
                        <td><?= $review[0]['bran_name'] ?></td>
                    </tr>
                    </tbody>
                </table>
            <?php } ?>
            <?php } ?>

            <?php
            if (($this->uri->segment(2) == 'customerBehaviour') && !$this->uri->segment(3)) {
                ?>
                <script src="<?php echo base_url(); ?>assets/new/moment.min.js"></script>
                <script src="<?php echo base_url(); ?>assets/new/daterangepicker.js"></script>
                <script type="text/javascript">

                    $.fn.dataTable.ext.search.push(
                        function (settings, data, dataIndex) {
                            var min = parseInt($('#min').val(), 10);
                            var max = parseInt($('#max').val(), 10);
                            var age = parseFloat(data[4]) || 0; // use data for the age column

                            if ((isNaN(min) && isNaN(max)) ||
                                (isNaN(min) && age <= max) ||
                                (min <= age && isNaN(max)) ||
                                (min <= age && age <= max)) {
                                return true;
                            }
                            return false;
                        }
                    );
                    // $(document).ready(function () {
                    //     var table = $('table').DataTable();
                    //     // Event listener to the two range filtering inputs to redraw on input
                    //     $('#min, #max').keyup(function () {
                    //         table.draw();
                    //     });
                    // });
                    $(document).ready(function () {
                        $(".select_3").select2({
                            allowClear: true
                        });

                        $("#sectors").select2({
                            placeholder: "Select sectors"
                        });

                        $("#branches").select2({
                            placeholder: "Select branches"
                        });

                        $("#genders").select2({
                            placeholder: "Select gender"
                        });


                        var cb = function (start, end, label) {
                            $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                            $('#reportrange').val('');
                        };

                        var optionSet1 = {
                            startDate: moment().subtract(29, 'days'),
                            endDate: moment(),
                            minDate: '01/01/2016',
                            maxDate: '31/12/2099',
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
                            format: 'DD/MM/YYYY',
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
                        $('#reportrange').daterangepicker(optionSet1, cb);
                        $('#from').val(moment().subtract(29, 'days').format('YYYY-MM-DD'));
                        $('#to').val(moment().format('YYYY-MM-DD'));

                        <?php if (isset($_POST['from']) && isset($_POST['to'])){ ?>
                        $('#reportrange').data('daterangepicker').setStartDate(moment('<?php echo $_POST['from'];?>'));
                        $('#reportrange').data('daterangepicker').setEndDate(moment('<?php echo $_POST['to'];?>'));
                        <?php } ?>

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
                            $(this).val('');
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

                        $('#clear-form').on('click', function () {
                            $('.select_3').select2("val", " ");
                            $('#reportrange').val('');
                            $('#min').val('');
                            $('#max').val('');
                        });

                    });
                </script>
                <form class="form-inline" action="" method="post">

                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Branches:</label>
                            <style media="screen">
                                .select2 {
                                    width: 290px !important;
                                }
                            </style>
                            <select class=" select_3 form-control input-lg" name="branches[]" multiple="multiple"
                                    id="branches" style="width:400px">
                                <?php
                                if (isset($branches)) {
                                    for ($i = 0; $i < sizeof($branches); $i++) {
//                                        echo "<option value='{$branches[$i]['id']}'>{$branches[$i]['bran_name']}</option>";
                                        echo "<option value='{$branches[$i]['id']}' " . set_select('branches', $branches[$i]['id']) . " >" . $branches[$i]['bran_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>


                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Gender:</label>
                            <style media="screen">
                                .select2 {
                                    width: 290px !important;
                                }
                            </style>
                            <select class=" select_3 form-control input-lg" name="genders" id="genders"
                                    style="width=200px">
                                <?php
                                echo "<option value = ''></option>";
                                if (isset($genders)) {
                                    for ($i = 0; $i < sizeof($genders); $i++) {
//                                        echo "<option value='{$genders[$i]}'>{$genders[$i]}</option>";
                                        echo "<option value='{$genders[$i]}' " . set_select('genders', $genders[$i]) . " >" . $genders[$i] . "</option>";
                                    }
                                }
                                ?>
                            </select>

                        </div>

                    </div>

                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Sectors:</label>
                            <style media="screen">
                                .select2 {
                                    width: 290px !important;
                                }
                            </style>
                            <select class=" select_3 form-control input-lg" name="sectors[]" multiple="multiple"
                                    id="sectors" style="width:400px">
                                <?php
                                if (isset($sectors)) {
                                    for ($i = 0; $i < sizeof($sectors); $i++) {
//                                        echo "<option value='{$sectors[$i]['Sector']}'>{$sectors[$i]['Sector']}</option>";
                                        echo "<option value='{$sectors[$i]['Sector']}' " . set_select('sectors', $sectors[$i]['Sector']) . " >" . $sectors[$i]['Sector'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <br><br>


                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Age:</label>
                            <input id="min" placeholder="Min" name="min" class="form-control input-sm "
                                   style="width: 70px;" type="number" min="1" max="99"
                                   value="<?php
                                   if (isset($_POST['min'])) {
                                       echo $_POST['min'];
                                   }
                                   ?>">
                            <input id="max" placeholder="Max" name="max" class="form-control input-sm "
                                   style="width: 70px;" type="number" min="1" max="99"
                                   value="<?php
                                   if (isset($_POST['max'])) {
                                       echo $_POST['max'];
                                   }
                                   ?>">
                        </div>
                    </div>


                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Review Time:</label>
                            <input name="daterange" id="reportrange" placeholder="Select Date Range"
                                   class="date-picker form-control input-sm " type="text">
                        </div>
                    </div>


                    <div class="col-md-4 column">
                        <input type="hidden" name="from" id="from" value="">
                        <input type="hidden" name="to" id="to" value="">
                        <button type="submit" class="btn btn-default btn-primary" name="button">Filter</button>
                        <input type="button" class="btn btn-success btn-small" id="clear-form"
                               value="Clear"/>
                    </div>


                </form>
            <?php } ?>

            <?php if (($this->uri->segment(2) == 'suggestedBranches')) { ?>
                <!-- Select2 -->
                <script src="<?php echo base_url(); ?>assets/select2.min.js"></script>
                <link href="<?php echo base_url(); ?>assets/select2.min.css" rel="stylesheet">
                <script src="<?php echo base_url(); ?>assets/new/moment.min.js"></script>
                <script src="<?php echo base_url(); ?>assets/new/daterangepicker.js"></script>
                <script type="text/javascript">


                    $(document).ready(function () {
                        var table = $('table').DataTable();
                        // Event listener to the two range filtering inputs to redraw on input
                        $('#min, #max').keyup(function () {
                            table.draw();
                        });
                    });
                    $(document).ready(function () {
                        $(".select_3").select2({
                            allowClear: true
                        });

                        $("#sectors").select2({
                            placeholder: "Select sectors"
                        });

                        $("#branches").select2({
                            placeholder: "Select branches"
                        });

                        $("#genders").select2({
                            placeholder: "Select gender"
                        });

                        var cb = function (start, end, label) {
                            $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                            $('#reportrange').val('');
                        };

                        var optionSet1 = {
                            startDate: moment().subtract(29, 'days'),
                            endDate: moment(),
                            minDate: '01/01/2016',
                            maxDate: '31/12/2099',
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
                            format: 'DD/MM/YYYY',
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
                        $('#reportrange').daterangepicker(optionSet1, cb);
                        $('#from').val(moment().subtract(29, 'days').format('YYYY-MM-DD'));
                        $('#to').val(moment().format('YYYY-MM-DD'));

                        <?php if (isset($_POST['from']) && isset($_POST['to'])){ ?>
                        $('#reportrange').data('daterangepicker').setStartDate(moment('<?php echo $_POST['from'];?>'));
                        $('#reportrange').data('daterangepicker').setEndDate(moment('<?php echo $_POST['to'];?>'));
                        <?php } ?>



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
                            $(this).val('');
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

                        $('#clear-form').on('click', function () {
                            $('.select_3').select2("val", " ");
                            $('#reportrange').val('');
                        });
                    });
                </script>
                <form class="form-inline" action="" method="post">
                    <div class="col-md-3 column">
                        <div class="form-group">
                            <label>Branches:</label>
                            <style media="screen">
                                .select2 {
                                    width: 290px !important;
                                }
                            </style>
                            <select class="select_3 form-control input-lg" name="branches[]" multiple="multiple"
                                    id="branches" style="width:400px">
                                <?php
                                if (isset($branches)) {
                                    for ($i = 0; $i < sizeof($branches); $i++) {
//                                        echo "<option value='{$branches[$i]['id']}'>{$branches[$i]['bran_name']}</option>";
                                        echo "<option value='{$branches[$i]['id']}' " . set_select('branches', $branches[$i]['id']) . " >" . $branches[$i]['bran_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>


                    <div class="col-md-3 column">
                        <div class="form-group">
                            <label>Sectors:</label>
                            <style media="screen">
                                .select2 {
                                    width: 290px !important;
                                }
                            </style>
                            <select class="select_3 form-control input-lg" name="sectors[]" multiple="multiple"
                                    id="sectors" style="width:400px">
                                <?php
                                if (isset($sectors)) {
                                    for ($i = 0; $i < sizeof($sectors); $i++) {
//                                        echo "<option value='{$sectors[$i]['Sector']}'>{$sectors[$i]['Sector']}</option>";
                                        echo "<option value='{$sectors[$i]['Sector']}' " . set_select('sectors', $sectors[$i]['Sector']) . " >" . $sectors[$i]['Sector'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 column">
                        <div class="form-group">
                            <label>Gender:</label>
                            <style media="screen">
                                .select2 {
                                    width: 290px !important;
                                }
                            </style>
                            <select class="select_3 form-control input-lg" name="genders" id="genders"
                                    style="width=200px">

                                <?php
                                echo "<option value=''></option>";
                                if (isset($genders)) {
                                    for ($i = 0; $i < sizeof($genders); $i++) {
//                                        echo "<option value='{$genders[$i]}'>{$genders[$i]}</option>";
                                        echo "<option value='{$genders[$i]}' " . set_select('genders', $genders[$i]) . " >" . $genders[$i] . "</option>";
                                    }
                                }
                                ?>
                            </select>

                        </div>

                    </div>
                    <div class="col-md-3 column">
                        <div class="form-group">
                            <label>Review Time:</label>
                            <input name="daterange" id="reportrange" placeholder="Select Date Range" required
                                   class="date-picker form-control input-sm " type="text">
                        </div>
                    </div>

                    <br><br><br>

                    <div class="col-md-12 column" style="text-align: center;">
                        <input type="hidden" name="from" id="from" value="">
                        <input type="hidden" name="to" id="to" value="">
                        <button type="submit" class="btn btn-default btn-primary" name="button">Filter</button>
                        <input type="button" class="btn btn-success btn-small" id="clear-form" value="Clear"/>
                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal">Summary
                        </button>
                    </div>


                    <!-- Trigger the modal with a button -->


                    <!-- Modal -->
                    <div class="modal fade" id="myModal" role="dialog">
                        <div class="modal-dialog modal-lg">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header" style="text-align: center;">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h3 class="modal-title">Summary of Suggested Branches</h3>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>Branch Name</th>
                                            <th>Number of suggesstions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($summary as $item) { ?>
                                            <tr>
                                                <td><?php echo $item['Branch Name']; ?></td>
                                                <td><?php echo $item['Count']; ?></td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                    <br>
                                    <br>

                                </div>
                                <div class="modal-footer" style="text-align: center;">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>

                        </div>
                    </div>


                </form>


            <?php } ?>

            <?php if (($this->uri->segment(2) == 'reviewsComments')) { ?>
                <!-- Select2 -->
                <script src="<?php echo base_url(); ?>assets/select2.min.js"></script>
                <link href="<?php echo base_url(); ?>assets/select2.min.css" rel="stylesheet">
                <script src="<?php echo base_url(); ?>assets/new/moment.min.js"></script>
                <script src="<?php echo base_url(); ?>assets/new/daterangepicker.js"></script>
                <script type="text/javascript">


                    $(document).ready(function () {
                        var table = $('table').DataTable();
                        // Event listener to the two range filtering inputs to redraw on input
                        $('#min, #max').keyup(function () {
                            table.draw();
                        });
                    });
                    $(document).ready(function () {
                        $(".select_3").select2({
                            allowClear: true
                        });

                        $("#sectors").select2({
                            placeholder: "Select sectors"
                        });

                        $("#branches").select2({
                            placeholder: "Select branches"
                        });

                        $("#genders").select2({
                            placeholder: "Select gender"
                        });

                        $("#groups").select2({
                            placeholder: "Select groups"
                        });


                        var cb = function (start, end, label) {
                            $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                            $('#reportrange').val('');
                        };

                        var optionSet1 = {
                            startDate: moment().subtract(29, 'days'),
                            endDate: moment(),
                            minDate: '01/01/2016',
                            maxDate: '31/12/2099',
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
                            format: 'DD/MM/YYYY',
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
                        $('#reportrange').daterangepicker(optionSet1, cb);
                        $('#from').val(moment().subtract(29, 'days').format('YYYY-MM-DD'));
                        $('#to').val(moment().format('YYYY-MM-DD'));

                        <?php if (isset($_POST['from']) && isset($_POST['to'])){ ?>
                        $('#reportrange').data('daterangepicker').setStartDate(moment('<?php echo $_POST['from'];?>'));
                        $('#reportrange').data('daterangepicker').setEndDate(moment('<?php echo $_POST['to'];?>'));
                        <?php } ?>

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
                            $(this).val('');
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

                        $('#clear-form').on('click', function () {
                            $('.select_3').select2("val", " ");
                            $('#reportrange').val('');
                            $('#min').val('');
                            $('#max').val('');
                        });
                    });
                </script>
                <form class="form-inline" action="" method="post">


                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Branches:</label>
                            <style media="screen">
                                .select2 {
                                    width: 290px !important;
                                }
                            </style>
                            <select class=" select_3 form-control input-lg" name="branches[]" multiple="multiple"
                                    id="branches" style="width:400px">
                                <?php
                                if (isset($branches)) {
                                    for ($i = 0; $i < sizeof($branches); $i++) {
//                                        echo "<option value='{$branches[$i]['id']}'>{$branches[$i]['bran_name']}</option>";
                                        echo "<option value='{$branches[$i]['id']}' " . set_select('branches', $branches[$i]['id']) . " >" . $branches[$i]['bran_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>


                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Sectors:</label>
                            <style media="screen">
                                .select2 {
                                    width: 290px !important;
                                }
                            </style>
                            <select class=" select_3 form-control input-lg" name="sectors[]" multiple="multiple"
                                    id="sectors" style="width:400px">
                                <?php
                                if (isset($sectors)) {
                                    for ($i = 0; $i < sizeof($sectors); $i++) {
//                                        echo "<option value='{$sectors[$i]['Sector']}'>{$sectors[$i]['Sector']}</option>";
                                        echo "<option value='{$sectors[$i]['Sector']}' " . set_select('sectors', $sectors[$i]['Sector']) . " >" . $sectors[$i]['Sector'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Review Time:</label>
                            <input name="daterange" id="reportrange" placeholder="Select Date Range" required
                                   class="date-picker form-control input-sm " type="text">
                        </div>
                    </div>


                    <br><br><br>

                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Gender:</label>
                            <style media="screen">
                                .select2 {
                                    width: 290px !important;
                                }
                            </style>
                            <select class=" select_3 form-control input-lg" name="genders" id="genders"
                                    style="width=200px">

                                <?php
                                echo "<option value=''></option>";
                                if (isset($genders)) {
                                    for ($i = 0; $i < sizeof($genders); $i++) {
//                                        echo "<option value='{$genders[$i]}'>{$genders[$i]}</option>";
                                        echo "<option value='{$genders[$i]}' " . set_select('genders', $genders[$i]) . " >" . $genders[$i] . "</option>";
                                    }
                                }
                                ?>
                            </select>

                        </div>

                    </div>

                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Groups:</label>
                            <style media="screen">
                                .select2 {
                                    width: 290px !important;
                                }
                            </style>
                            <select class=" select_3 form-control input-lg" name="groups[]" multiple="multiple"
                                    id="groups" style="width:400px">
                                <?php
                                if (isset($groups)) {
                                    for ($i = 0; $i < sizeof($groups); $i++) {
//                                        echo "<option value='{$groups[$i]['group_name']}'>{$groups[$i]['group_name']}</option>";
                                        echo "<option value='{$groups[$i]['id']}' " . set_select('groups', $groups[$i]['id']) . " >" . $groups[$i]['group_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>

                        </div>

                    </div>


                    <div class="col-md-4 column">
                        <input type="hidden" name="from" id="from" value="">
                        <input type="hidden" name="to" id="to" value="">
                        <button type="submit" class="btn btn-default btn-primary" name="button">Filter</button>
                        <input type="button" class="btn btn-success btn-small" id="clear-form"
                               value="Clear"/>
                    </div>


                </form>
            <?php } ?>

            <?php if (($this->uri->segment(2) == 'employeeSection')) { ?>
                <!-- Select2 -->
                <script src="<?php echo base_url(); ?>assets/select2.min.js"></script>
                <link href="<?php echo base_url(); ?>assets/select2.min.css" rel="stylesheet">
                <script src="<?php echo base_url(); ?>assets/new/moment.min.js"></script>
                <script src="<?php echo base_url(); ?>assets/new/daterangepicker.js"></script>
                <script type="text/javascript">

                    $(document).ready(function () {
                        $(".select_4").select2({
                            allowClear: true
                        });

                        $("#sectors").select2({
                            placeholder: "Select sectors"
                        });

                        $("#branches").select2({
                            placeholder: "Select branches"
                        });

                        $("#genders").select2({
                            placeholder: "Select gender"
                        });

                        $("#groups").select2({
                            placeholder: "Select groups"
                        });

                        $("#jobs").select2({
                            placeholder: "Select jobs"
                        });


                        var cb = function (start, end, label) {
                            $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                            $('#reportrange').val('');
                        };

                        var optionSet1 = {
                            startDate: moment().subtract(29, 'days'),
                            endDate: moment(),
                            minDate: '01/01/2016',
                            maxDate: '31/12/2099',
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
                            opens: 'right',
                            buttonClasses: ['btn btn-default'],
                            cancelClass: 'btn-small',
                            format: 'DD/MM/YYYY',
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
                        $('#reportrange').daterangepicker(optionSet1, cb);
                        $('#from').val(moment().subtract(29, 'days').format('YYYY-MM-DD'));
                        $('#to').val(moment().format('YYYY-MM-DD'));


                        <?php if (isset($_POST['from']) && isset($_POST['to'])){ ?>
                        $('#reportrange').data('daterangepicker').setStartDate(moment('<?php echo $_POST['from'];?>'));
                        $('#reportrange').data('daterangepicker').setEndDate(moment('<?php echo $_POST['to'];?>'));
                        <?php } ?>

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
                            $(this).val('');
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

                        $('#clear-form').on('click', function () {
                            $('.select_4').select2("val", " ");
                            $('#reportrange').val('');
                            $('#min').val('');
                            $('#max').val('');
                        });


                    });
                </script>
                <form class="form-inline" action="" method="post">
                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Branches:</label>

                            <select class="select_4 form-control" name="branches[]" multiple="multiple"
                                    id="branches" style="width:400px">
                                <?php
                                if (isset($branches)) {
                                    for ($i = 0; $i < sizeof($branches); $i++) {
//                                        echo "<option value='{$branches[$i]['id']}'>{$branches[$i]['bran_name']}</option>";
                                        echo "<option value='{$branches[$i]['id']}' " . set_select('branches', $branches[$i]['id']) . " >" . $branches[$i]['bran_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Jobs:</label>
                            <select class="select_4 form-control input-lg" name="jobs[]" multiple="multiple"
                                    id="jobs" style="width:400px">
                                <?php
                                if (isset($jobs)) {
                                    for ($i = 0; $i < sizeof($jobs); $i++) {
//                                        echo "<option value='{$jobs[$i]['id']}'>{$jobs[$i]['job_title']}</option>";
                                        echo "<option value='{$jobs[$i]['id']}' " . set_select('jobs', $jobs[$i]['id']) . " >" . $jobs[$i]['job_title'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>


                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Groups:</label>
                            <select class="select_4 form-control input-lg" name="groups[]" multiple="multiple"
                                    id="groups" style="width:400px">
                                <?php
                                if (isset($groups)) {
                                    for ($i = 0; $i < sizeof($groups); $i++) {
//                                        echo "<option value='{$groups[$i]['id']}'>{$groups[$i]['group_name']}</option>";
                                        echo "<option value='{$groups[$i]['id']}' " . set_select('groups', $groups[$i]['id']) . " >" . $groups[$i]['group_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <br><br><br>

                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Sectors:</label>
                            <select class="select_4 form-control input-lg" name="sectors[]" multiple="multiple"
                                    id="sectors" style="width:400px">
                                <?php
                                if (isset($sectors)) {
                                    for ($i = 0; $i < sizeof($sectors); $i++) {
//                                        echo "<option value='{$sectors[$i]['Sector']}'>{$sectors[$i]['Sector']}</option>";
                                        echo "<option value='{$sectors[$i]['Sector']}' " . set_select('sectors', $sectors[$i]['Sector']) . " >" . $sectors[$i]['Sector'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>


                    <div class="col-md-4 column">
                        <div class="form-group">
                            <label>Review Time:</label>
                            <input name="daterange" id="reportrange" placeholder="Select Date Range"
                                   class="date-picker form-control input-sm " type="text">
                        </div>
                    </div>
                    <div class="col-md-4 column">
                        <input type="hidden" name="from" id="from" value="">
                        <input type="hidden" name="to" id="to" value="">
                        <button type="submit" class="btn btn-default btn-primary" name="button">Filter</button>
                        <input type="button" class="btn btn-success btn-small" id="clear-form"
                               value="Clear"/>
                    </div>


                </form>
            <?php } ?>


            <div id="crud_output"><?php echo $crud_output->output; ?></div>

        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->


<!-- ************************ Branches Module ************************-->
<?php if ($this->uri->segment(2) == 'branches') { ?>
    <script type="text/javascript">
        $(document).ready(function () {
            <?php if ($this->uri->segment(3) == 'add') {?>
            $('#field-online,#field-inhouse').html('');
            $('#field-online').trigger("chosen:updated");
            $('#field-inhouse').trigger("chosen:updated");
            <?php } ?>
            <?php if ($this->uri->segment(3) == 'edit') { ?>
            var selectedonline = $("#field-online").val();
            var selectedinhouse = $("#field-inhouse").val();

            $.post("<?=base_url()?>admin/getOrgServicesAjax",
                {
                    orgd: $('#field-organization_id').val()
                },
                function (data, status) {
                    data = JSON.parse(data);
                    $('#field-online').html('');
                    for (var i = 0; i < data['online'].length; i++) {
                        $('#field-online').append('<option value="' + data['online'][i]['id'] + '">' + data['online'][i]['service'] + '</option>');
                    }
                    $('#field-online').trigger("chosen:updated");
                    $('#field-inhouse').html('');
                    for (var i = 0; i < data['inhouse'].length; i++) {
                        $('#field-inhouse').append('<option value="' + data['inhouse'][i]['id'] + '">' + data['inhouse'][i]['service'] + '</option>');
                    }
                    $('#field-inhouse').trigger("chosen:updated");
                    $('#field-online').val(selectedonline).trigger("chosen:updated");
                    $('#field-inhouse').val(selectedinhouse).trigger("chosen:updated");
                });
            <?php  } ?>

            $('#field-organization_id').on('change', function () {
                $('#field-online,#field-inhouse').html('');
                $('#field-online').trigger("chosen:updated");
                $('#field-inhouse').trigger("chosen:updated");

                //alert( this.value ); // or $(this).val()
                $.post("<?=base_url()?>admin/getOrgServicesAjax",
                    {
                        orgd: this.value
                    },
                    function (data, status) {
                        data = JSON.parse(data);
                        for (var i = 0; i < data['online'].length; i++) {
                            $('#field-online').append('<option value="' + data['online'][i]['id'] + '">' + data['online'][i]['service'] + '</option>');
                        }
                        $('#field-online').trigger("chosen:updated");

                        for (var i = 0; i < data['inhouse'].length; i++) {
                            $('#field-inhouse').append('<option value="' + data['inhouse'][i]['id'] + '">' + data['inhouse'][i]['service'] + '</option>');
                        }
                        $('#field-inhouse').trigger("chosen:updated");

                    });
            });
        });
    </script>
<?php } ?>




<?php if ($this->uri->segment(2) == 'organizations' && ($this->uri->segment(3) == 'add' || $this->uri->segment(3) == 'edit')) {
  ?>

<!--    <script src="--><?php //echo base_url(); ?><!--assets/new/moment.min.js"></script>-->

    <script>

        $('#field-DayToExpire').attr('readonly', 'readonly');
        $('#field-DayToExpire').attr('min', 'minDate');


        $('#field-DayToExpire').datetimepicker({
            timeFormat: 'HH:mm:ss',
            dateFormat: js_date_format,
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            minDate: 0
        });
    </script>

<?php } ?>


<!-- ************************ Branches Module ************************-->

<!-- ************************ Employee Module ************************-->
<?php if ($this->uri->segment(2) == 'employee') { ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $('input:radio').parent().parent().css('display', 'none');
            $('#field-type').on('change', function () {
                if (this.value == "employee") {
                    $('input:radio').parent().parent().css('display', 'inherit');
                } else {
                    $('input:radio').parent().parent().css('display', 'none');
                }
            });

            <?php if (($this->uri->segment(1) == 'organization') && ($this->uri->segment(3) == 'add')) { ?>
            $('#field-branches').html('');
            $('#field-branches').trigger("chosen:updated");
            $.post("<?=base_url()?>organization/getBranchesAjax",
                {},
                function (data, status) {
                    data = JSON.parse(data);
                    console.log(data);
                    for (var i = 0; i < data['branches'].length; i++) {
                        $('#field-branches').append('<option value="' + data['branches'][i]['id'] + '">' + data['branches'][i]['bran_name'] + '</option>');
                    }
                    $('#field-branches').trigger("chosen:updated");
                });
            <?php }  ?>
            // <!-- ////////////////////////// add Page /////////////////////////////-->
            <?php if (($this->uri->segment(3) == 'add') && ($this->uri->segment(1) == 'admin')) {  ?>
            $('#field-branches').html('');
            $('#field-branches').trigger("chosen:updated");
            <?php   } ?>
            // <-- ////////////////////////// add Page /////////////////////////////-->

            // < !-- ////////////////////////// update Page /////////////////////////////-->
            <?php if ($this->uri->segment(3) == 'edit') { ?>
            var selectedChoises = $("#field-branches").val();
            var select = $('#field-branches'), selectContainer = $('#branches_input_box');
            var type = $('#field-type').val();

            if (type == 'employee') {
                $('input:radio').parent().parent().css('display', 'inherit');
            }

            <?php if ($this->uri->segment(1) == 'organization') {?>
            $.post("<?=base_url()?>organization/checkjob",
                <?php } else {?>
                $.post("<?=base_url()?>admin/checkjob",
                    <?php }?>
                    {
                        jobi: $('#field-jobID').val()
                    },
                    function (data, status) {
                        data = JSON.parse(data);
                        var selectHTML = selectContainer.find('#field-branches').html();
                        selectContainer.html("");
                        select = $('<select id="field-branches" name="branches[]" style="width: 510px;">' + selectHTML + '</select>');
                        if (data['type'] == 'multi') {
                            select.attr('multiple', 'true');
                        }
                        selectContainer.append(select);
                        select.chosen();
                    });


            <?php if ($this->uri->segment(1) == 'organization') {?>
            $.post("<?=base_url()?>organization/getBranchesAjax",
                <?php } else {?>
                $.post("<?=base_url()?>admin/getBranchesAjax",
                    <?php }?>
                    {
                        <?php if ($this->uri->segment(1) == 'admin') {?>
                        orgd: $('#field-organization_id').val()
                        <?php }?>
                    },
                    function (data, status) {
                        data = JSON.parse(data);
                        $('#field-branches').html('');
                        for (var i = 0; i < data['branches'].length; i++) {
                            $('#field-branches').append('<option value="' + data['branches'][i]['id'] + '">' + data['branches'][i]['bran_name'] + '</option>');
                        }
                        $('#field-branches').trigger("chosen:updated");
                        $('#field-branches').val(selectedChoises).trigger("chosen:updated");
                    });

            <?php } ?>
            // <!-- ////////////////////////// update Page /////////////////////////////-->


            //////////////////////////Ajax to Update branches////////////////////////////
            <?php if ($this->uri->segment(1) == 'admin') { ?>
            $('#field-organization_id').on('change', function () {
                $('#field-branches').html('');
                $('#field-branches').trigger("chosen:updated");
                $.post("<?=base_url()?>admin/getBranchesAjax",
                    {
                        orgd: this.value
                    },
                    function (data, status) {
                        data = JSON.parse(data);
                        console.log(data);
                        for (var i = 0; i < data['branches'].length; i++) {
                            $('#field-branches').append('<option value="' + data['branches'][i]['id'] + '">' + data['branches'][i]['bran_name'] + '</option>');
                        }
                        $('#field-branches').trigger("chosen:updated");
                    });
            });
            <?php } ?>
///////////////////////////Ajax to check Job type///////////////////////////////
            $('#field-jobID').on('change', function () {
                var select = $('#field-branches'), selectContainer = $('#branches_input_box');
                <?php if ($this->uri->segment(1) == 'organization') {?>
                $.post("<?=base_url()?>organization/checkjob",
                    <?php } else {?>
                    $.post("<?=base_url()?>admin/checkjob",
                        <?php }?>
                        {
                            jobi: this.value
                        },
                        function (data, status) {
                            data = JSON.parse(data);
                            var selectHTML = selectContainer.find('#field-branches').html();
                            selectContainer.html("");
                            select = $('<select id="field-branches" name="branches[]" style="width: 510px;">' + selectHTML + '</select>');
                            if (data['type'] == 'multi') {
                                select.attr('multiple', 'true');
                            }
                            selectContainer.append(select);
                            select.chosen();
                        });
            });
        });
    </script>

<?php } ?>
<!-- ************************ Employee Module ************************-->


<!-- ************************ Group Module ************************-->
<?php if (($this->uri->segment(2) == 'groups')) { ?>
    <script type="text/javascript">
        $(document).ready(function () {
            <?php if(($this->uri->segment(3) == 'add')){ ?>
            $("#cateringGuestFlag_field_box").css('display', 'none');
            $('#jobs_field_box').removeClass('even');
            $('#jobs_field_box').addClass('odd');
            <?php  }else{?>
            if ($('#field-sector').val() == 2) {
                $("#cateringGuestFlag_field_box").css('display', 'inherit');
                $('#jobs_field_box').removeClass('odd');
                $('#jobs_field_box').addClass('even');
            } else {
                $("#cateringGuestFlag_field_box").css('display', 'none');
                $('#jobs_field_box').removeClass('even');
                $('#jobs_field_box').addClass('odd');
            }
            <?php  }?>
            $('#field-sector').on('change', function () {
                if ($(this).val() == 2) {
                    $("#cateringGuestFlag_field_box").css('display', 'inherit');
                    $('#jobs_field_box').removeClass('odd');
                    $('#jobs_field_box').addClass('even');
                } else {
                    $("#cateringGuestFlag_field_box").css('display', 'none');
                    $('#jobs_field_box').removeClass('even');
                    $('#jobs_field_box').addClass('odd');
                }
            });
        });
    </script>
<?php } ?>
<!-- ************************ Group Module ************************-->


<?php if ($this->uri->segment(2) == 'screenSaverSection'){

    ?>
    <style>
        tfoot {display: none;}
    </style>

<?php } ?>