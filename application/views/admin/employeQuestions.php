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
          <h2>Employee Report</h2>
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
          <h2>Employees Data</h2>
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
          .select2{
            width: 200px !important;
          }
        </style>
              <link href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet">
              <script src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>

              <table id="myTable">
                <thead>
                  <tr>
                      <th>Employee</th>
                      <th>position</th>
                      <th>Result</th>
                      <th>Branch Name</th>
                  </tr>
                </thead>
                <tboby>
                  <?php
                  foreach($result as $row){?>
                    <tr>
                      <td>
                        <a href="<?=base_url()?>organization/employeeSectionGroups/<?= $row->id?>" class="btn">  <?= $row->employeName?></a>
                      </td>
                      <td>
                        <?= $row->job_title?>
                      </td>
                      <td>
                        <?= $row->result?>
                      </td>
                      <td>
                        <?= $row->bran_name?>

                      </td>
                    </tr>
                  <?php  }
                  ?>
                </tbody>
              </table>
              <script type="text/javascript">
              $(document).ready(function(){
                $('#myTable').DataTable();

              });
              </script>
        </div>
      </div>
    </div>

    <div id="table" class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <table id="myTable2">
            <thead>
              <tr>
                <th>Group Name</th>
                  <th>Question</th>
                  <th>Result</th>
              </tr>
            </thead>
            <tboby>
              <?php
              $i =0;
              foreach($question as $row){?>
                <tr>
                  <?php if($i == 0){?>
                  <td rowspan="<?= sizeof($question)?>">
                      <?= $data[0]->group_name?>
                  </td>
                  <?php }?>
                  <td>
                    <?= $row->quesition?>
                  </td>
                  <td>
                    <?= $row->all_reviews?>
                  </td>

                </tr>
              <?php $i++; }
              ?>
            </tbody>
          </table>
          <script type="text/javascript">
          $(document).ready(function(){
            $('#myTable2').DataTable();
          });
          </script>
        </div>
      </div>
    </div>
  </div>
</div>
