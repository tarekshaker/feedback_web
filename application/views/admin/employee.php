<!-- page content -->
<script src="<?php echo base_url(); ?>assets/select2.min.js"></script>
<link href="<?php echo base_url(); ?>assets/select2.min.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/echarts.min.js"></script>

<script src="<?php echo base_url(); ?>assets/new/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/new/daterangepicker.js"></script>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
            <div class="btn-group">
                <button id="listbutton" class="btn btn-default" type="button">List</button>
                <button id="chartbutton" class="btn btn-default" type="button">Chart</button>
            </div>
        </div>
        <div id="chart" class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Employee Data</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <div id="table" class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Employees Report</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <style media="screen">
                        .select2 {
                            width: 200px !important;
                        }
                    </style>
                    <link href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet">
                    <script src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
                    <form class="form-inline" action="" method="post">
                        <div class="col-md-4 column">
                            <div class="form-group">
                                <label>Branches:</label>

                                <select class=" select_3 form-control input-lg" name="branches[]" multiple="multiple"
                                        id="branches" style="width=400px">
                                    <?php
                                    if (isset($branches)) {
                                        for ($i = 0; $i < sizeof($branches); $i++) {
                                            echo "<option value='{$branches[$i]['id']}'>{$branches[$i]['bran_name']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 column">
                            <div class="form-group">
                                <label>Jobs:</label>
                                <select class=" select_3 form-control input-lg" name="jobs[]" multiple="multiple"
                                        id="jobs" style="width=400px">
                                    <?php
                                    if (isset($jobs)) {
                                        for ($i = 0; $i < sizeof($jobs); $i++) {
                                            echo "<option value='{$jobs[$i]['id']}'>{$jobs[$i]['job_title']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 column">
                            <div class="form-group">
                                <label>Groups:</label>
                                <select class=" select_3 form-control input-lg" name="groups[]" multiple="multiple"
                                        id="groups" style="width=400px">
                                    <?php
                                    if (isset($groups)) {
                                        for ($i = 0; $i < sizeof($groups); $i++) {
                                            echo "<option value='{$groups[$i]['id']}'>{$groups[$i]['group_name']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <br><br>

                        <div class="col-md-4 column">
                            <div class="form-group">
                                <label>Sectors:</label>
                                <select class=" select_3 form-control input-lg" name="sectors[]" multiple="multiple"
                                        id="sectors" style="width=400px">
                                    <?php
                                    if (isset($sectors)) {
                                        for ($i = 0; $i < sizeof($sectors); $i++) {
                                            echo "<option value='{$sectors[$i]['Sector']}'>{$sectors[$i]['Sector']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-4 column">
                            <div class="form-group">
                                <label>Review Time:</label>
                                <input name="daterange" id="reportrange" placeholder="From"
                                       class="date-picker form-control input-sm " type="text">
                            </div>
                        </div>
                        <div class="col-md-4 column">
                            <button type="submit" name="button">Filter</button>
                        </div>


                    </form>

                    <table id="myTable">
                        <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Position</th>
                            <th>Sector</th>
                            <th>Result</th>
                            <th>-</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($result as $row) {
                                ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url() ?>organization/employeeSectionGroups/<?= $row->id ?>"
                                           class="btn">  <?= $row->employeName ?></a>
                                    </td>
                                    <td>
                                        <?= $row->job_title ?>
                                    </td>
                                    <td>
                                        <?= $row->sector ?>
                                    </td>
                                    <td>
                                        <?= $row->result ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url() ?>organization/employee/edit/<?= $row->id ?>"
                                           class="btn">Edit</a>

                                    </td>
                                </tr>
                            <?php }
                            ?>
                            </tbody>
                    </table>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            $('#myTable').DataTable();
                            $(".select_3").select2({
                                allowClear: true
                            });

                            $("#branches").select2({
                                placeholder: "Select branches"
                            });

                            $("#jobs").select2({
                                placeholder: "Select jobs"
                            });

                            $("#groups").select2({
                                placeholder: "Select groups"
                            });

                            $("#sectors").select2({
                                placeholder: "Select sectors"
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
                            $('#reportrange').on('show.daterangepicker', function () {
                            });
                            $('#reportrange').on('hide.daterangepicker', function () {
                            });
                            $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
                                console.log("apply event fired, start/end dates are " + picker.startDate.format('DD/MM/YYYY') + " to " + picker.endDate.format('DD/MM/YYYY'));
                                table.draw();
                            });
                            $('#reportrange').on('cancel.daterangepicker', function (ev, picker) {
                                $(this).val('');
                                table.draw();
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
                </div>
            </div>
        </div>
    </div>
</div>
